<?php
App::uses('AppController', 'Controller');
/**
 * DropOffAreaRates Controller
 *
 * @property DropOffAreaRate $DropOffAreaRate
 */
class DropOffAreaRatesController extends AppController {

/**
 *  Layout
 *
 * @var string
 */

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array();
/**
 * Components
 *
 * @var array
 */
	public $components = array('Session');

	public $uses = array('DropOffAreaRate','DropOffArea');

	// 乗捨料金パターン
	public $dropOffPricePatternList = array(
		1 => '料金1',
		2 => '料金2',
		3 => '料金3',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		/**
		 * 編集・削除対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit','delete'),$this->action)) {
			//編集・削除対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集・削除対象IDとクライアントIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->DropOffAreaRate->clientCheck($this->passedArgs[0],$this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}

		// 乗捨料金パターンの項目を絞る
		$maxPattern = $this->clientData['Client']['required_drop_off_price_pattern'];
		$dropOffPricePatternList = array();
		foreach ($this->dropOffPricePatternList as $k => $v) {
			if ($k > $maxPattern) {
				break;
			}
			$dropOffPricePatternList[$k] = $v;
		}

		$this->set(compact('dropOffPricePatternList'));
		$this->set('is_check_user', true);
	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$params = array();
		try{

			$conditions = array(
				'DropOffAreaRate.client_id'=>$this->clientData['Client']['id'],
				'DropOffAreaRate.delete_flg'=>array(0,1),
				'RentDropOffArea.delete_flg'=>0,
				'ReturnDropOffArea.delete_flg'=>0,
			);

			if(!empty($this->request->query)){
				if(!empty($this->request->query['rent_drop_off_area_id'])){
					$conditions['DropOffAreaRate.rent_drop_off_area_id'] = $this->request->query['rent_drop_off_area_id'];
					$this->request->data['DropOffAreaRate']['rent_drop_off_area_id'] = $this->request->query['rent_drop_off_area_id'];
					$params['rent_drop_off_area_id'] = $this->request->query['rent_drop_off_area_id'];
				}
				if(!empty($this->request->query['return_drop_off_area_id'])){
					$conditions['DropOffAreaRate.return_drop_off_area_id'] = $this->request->query['return_drop_off_area_id'];
					$this->request->data['DropOffAreaRate']['return_drop_off_area_id'] = $this->request->query['return_drop_off_area_id'];
					$params['return_drop_off_area_id'] = $this->request->query['return_drop_off_area_id'];
				}
			}

			$this->DropOffAreaRate->recursive = -1;
			$this->paginate = array(
				'conditions'=> $conditions,
				'joins'=>array(
					array(
						'type'=>'INNER',
						'alias'=>'RentDropOffArea',
						'table'=>'drop_off_areas',
						'conditions'=>'RentDropOffArea.id = DropOffAreaRate.rent_drop_off_area_id'
					),
					array(
						'type'=>'INNER',
						'alias'=>'ReturnDropOffArea',
						'table'=>'drop_off_areas',
						'conditions'=>'ReturnDropOffArea.id = DropOffAreaRate.return_drop_off_area_id'
					),
				),
				'recursive'=>-1
			);
			$this->set('dropOffAreaRates', $this->paginate());
		}
		catch (NotFoundException $e)
		{
			$this->set('dropOffAreaRates', null);
			$redirectUrl = array('action' => 'index');
			if(!empty($params)){
				$redirectUrl['?'] = $params;
			}
			return $this->redirect($redirectUrl);
		}

		$dropOfAreaList = $this->DropOffArea->getDropOffAreaList($this->clientData['Client']['id']);
		$this->set('dropOfAreaList',$dropOfAreaList);
	}


/**
 * エリア区間ごとの乗捨料金を登録する。
 * 出発エリア、返却エリアの登録が成功した場合、逆方向のデータも登録。
 *
 * @return void
 */
	public function add() {

		$dropOfAreaList = $this->DropOffArea->getDropOffAreaList($this->clientData['Client']['id']);
		$this->set('dropOfAreaList',$dropOfAreaList);

		if ($this->request->is('post')) {
			$this->DropOffAreaRate->create();

			$postData = $this->request->data['DropOffAreaRate'];
			$postData['client_id'] = $this->clientData['Client']['id'];
			$postData['staff_id'] = $this->clientData['id'];

			$withReverse = $postData['with_reverse'];
			unset($postData['with_reverse']);

			//既にデータが存在しないかチェックする
			$checkData = $this->DropOffAreaRate->uniqueAreaCheck(
							$postData['rent_drop_off_area_id'],
							$postData['return_drop_off_area_id'],
							$postData['client_id']
			);

			//重複データがなかった場合
			if($checkData) {
				if ($this->DropOffAreaRate->save($postData)) {

					if ($withReverse) {
						/**
						 * 逆方向のエリアも自動で登録
						 */
						$checkData = $this->DropOffAreaRate->uniqueAreaCheck(
							$postData['return_drop_off_area_id'],
							$postData['rent_drop_off_area_id'],
							$postData['client_id']
						);

						//逆方向のデータがなかった場合
						if ($checkData) {
							$reverseData = $postData;
							$reverseData['rent_drop_off_area_id'] = $postData['return_drop_off_area_id'];
							$reverseData['return_drop_off_area_id'] = $postData['rent_drop_off_area_id'];
							$this->DropOffAreaRate->create();
							if ($this->DropOffAreaRate->save($reverseData)) {
								$this->Session->setFlash('エリア区間の乗捨料金を登録しました','default',array('class'=>'alert alert-success'));
							}
						} else {
							$this->Session->setFlash('エリア区間の乗捨料金を登録しました。逆方向の乗捨料金は既に登録されています。','default',array('class'=>'alert alert-success'));
						}
					} else {
						$this->Session->setFlash('エリア区間の乗捨料金を登録しました','default',array('class'=>'alert alert-success'));
					}

					if (!empty($this->request->data['Custom']['referer'])) {
						$this->redirect($this->request->data['Custom']['referer']);
					} else {
						$this->redirect(array('action' => 'index'));
					}
				} else {
					$this->Session->setFlash('エリア区間の乗捨料金の登録に失敗しました','default',array('class'=>'alert alert-error'));
				}
			} else {
				$this->Session->setFlash($dropOfAreaList[$postData['rent_drop_off_area_id']] .'から' . $dropOfAreaList[$postData['return_drop_off_area_id']]. 'の区間は既に登録されています。','default',array('class'=>'alert alert-error'));
			}
		}

	}

/**
 * エリア区間ごとの乗捨両機を編集する
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		//乗捨てエリアリスト取得
		$dropOfAreaList = $this->DropOffArea->getDropOffAreaList($this->clientData['Client']['id']);
		$this->set('dropOfAreaList',$dropOfAreaList);

		if ($this->request->is('post')) {
			$this->DropOffAreaRate->create();

			$postData = $this->request->data['DropOffAreaRate'];
			$postData['id'] = $id;
			$postData['client_id'] = $this->clientData['Client']['id'];
			$postData['staff_id'] = $this->clientData['id'];
			$postData['deleted'] = ($this->request->data['DropOffAreaRate']['delete_flg'] == '2') ? date('Y-m-d H:i:s') : null;

			if ($this->DropOffAreaRate->save($postData)) {
				$this->Session->setFlash('エリア区間の乗捨料金の編集に成功しました','default',array('class'=>'alert alert-success'));
				if (!empty($this->request->data['Custom']['referer'])) {
					$this->redirect($this->request->data['Custom']['referer']);
				} else {
					$this->redirect(array('action' => 'index'));
				}
			} else {
				$this->Session->setFlash('エリア区間の乗捨料金の編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$this->request->data = $this->DropOffAreaRate->find('first',array('conditions'=>array('id'=>$id)));
		}

	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {

		//クライアントチェック
		if(!$this->DropOffAreaRate->clientCheck($id,$this->clientData['Client']['id'])) {
			$this->redirect('/DropOffAreaRates/');
		}

		//$this->request->onlyAllow('post', 'delete');

		//論理削除
		$saveData = array(
			'id' => $id,
			'staff_id' => $this->clientData['id'],
			'delete_flg' => 2,
			'deleted' => date('Y-m-d H:i:s'),
		);

		if ($this->DropOffAreaRate->save($saveData)) {
			$this->Session->setFlash('エリア区間の乗捨料金を削除しました','default',array('class'=>'alert alert-success'));
			$this->redirect('/DropOffAreaRates/');
		}

		$this->Session->setFlash('エリア区間の乗捨料金の削除に失敗しました。','default',array('class'=>'alert alert-success'));
		$this->redirect('/DropOffAreaRates/');
	}
}

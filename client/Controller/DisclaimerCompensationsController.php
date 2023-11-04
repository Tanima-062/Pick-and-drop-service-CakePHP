<?php
App::uses('AppController', 'Controller');
/**
 * DisclaimerCompensations Controller
 *
 * @property DisclaimerCompensation $DisclaimerCompensation
 */
class DisclaimerCompensationsController extends AppController {

	public $uses = array('DisclaimerCompensation', 'CarClass', 'CommodityItem', 'CommodityPrivilege', 'Commodity');

	public function beforeFilter() {
		parent::beforeFilter();

		/**
		 * 編集・削除対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit', 'delete'), $this->action)) {
			//編集・削除対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集・削除対象IDとクライアントIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->DisclaimerCompensation->clientCheck($this->passedArgs[0], $this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}

		$periodList = array(
			0 => '1日ごとに加算',
			1 => '24時間ごとに加算',
		);
		$this->set(compact('periodList'));
		$this->set('is_check_user', true);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$clientId = $this->clientData['Client']['id'];
		$staffId = $this->clientData['id'];
		$isClientAdmin = $this->clientData['is_client_admin'];

		$carClasses = $this->CarClass->getCarClassLists($clientId);

		$this->set(compact('carClasses'));

		$settings = array(
			'conditions' => array(
				'DisclaimerCompensation.client_id' => $clientId,
				'DisclaimerCompensation.delete_flg' => 0,
			),
			'order' => array(
				'DisclaimerCompensation.car_class_id' => 'ASC',
				'DisclaimerCompensation.start_date' => 'ASC',
			),
			'limit' => 100,
			'recursive' => 0,
		);
		if (!$isClientAdmin) {
			$settings['conditions']['OR'] = array(
				array('CarClass.scope' => 0),
				array('CarClass.scope' => $staffId)
			);
		}
		$this->Paginator->settings = $settings;
		if (!empty($this->request->query['car_class_id'])) {
			$this->Paginator->settings['conditions']['DisclaimerCompensation.car_class_id'] = $this->request->query['car_class_id'];
			$this->request->data['car_class_id'] = $this->request->query['car_class_id'];
		}
		$data = $this->Paginator->paginate('DisclaimerCompensation');
		$this->set('disclaimerCompensations', $data);

	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$clientId = $this->clientData['Client']['id'];
		$carClasses = $this->CarClass->getCarClassLists($clientId);

		if ($this->request->is('post')) {
			//設定した料金の影響で合計マイナスになるかチェック
			$minusCheckFlg = $this->__checkMinusPrice($this->request->data['DisclaimerCompensation']);

			$this->request->data['DisclaimerCompensation']['client_id'] = $clientId;
			$this->request->data['DisclaimerCompensation']['staff_id'] = $this->clientData['id'];
			$this->request->data['DisclaimerCompensation']['period_limit'] = intval($this->request->data['DisclaimerCompensation']['period_limit']);
			$check = $this->DisclaimerCompensation->dateDuplicateCheck($this->data);

			$this->DisclaimerCompensation->create();
			if (empty($check) && $minusCheckFlg && $this->DisclaimerCompensation->save($this->request->data)) {
				$this->Session->setFlash(__('保存しました。'), 'default', array('class' => 'alert alert-success'));
				$this->redirect('/DisclaimerCompensations?car_class_id='.$this->data['DisclaimerCompensation']['car_class_id']);
			} else {
				if(!empty($check)) {
					$this->Session->setFlash(__('日付が重複しています。'), 'default', array('class' => 'alert alert-error'));
				} elseif(!$minusCheckFlg) {
					$this->Session->setFlash(__('料金設定がマイナスになる可能性があります。商品やオプションの料金と併せて見直してください。'), 'default', array('class' => 'alert alert-error'));
				} else {
					$this->Session->setFlash(__('保存に失敗しました。もう一度登録をお願いします。'), 'default', array('class' => 'alert alert-error'));
				}
			}
		}

		$this->set(compact('carClasses'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->DisclaimerCompensation->id = $id;
		$this->request->data['DisclaimerCompensation']['id'] = $id;
		if (!$this->DisclaimerCompensation->exists()) {
			throw new NotFoundException(__('Invalid disclaimer compensation'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			//設定した料金の影響で合計マイナスになるかチェック
			$minusCheckFlg = $this->__checkMinusPrice($this->request->data['DisclaimerCompensation']);

			$this->request->data['DisclaimerCompensation']['staff_id'] = $this->clientData['id'];
			$this->request->data['DisclaimerCompensation']['period_limit'] = intval($this->request->data['DisclaimerCompensation']['period_limit']);
			$check = $this->DisclaimerCompensation->dateDuplicateCheck($this->data);

			if (empty($check) && $minusCheckFlg && $this->DisclaimerCompensation->save($this->request->data)) {
				$this->Session->setFlash(__('保存しました。'), 'default', array('class' => 'alert alert-success'));
				$this->redirect('/DisclaimerCompensations?car_class_id='.$this->data['DisclaimerCompensation']['car_class_id']);
			} else {
				if(!empty($check)) {
					$this->Session->setFlash(__('日付が重複しています。'), 'default', array('class' => 'alert alert-error'));
				} elseif(!$minusCheckFlg) {
					$this->Session->setFlash(__('料金設定がマイナスになる可能性があります。商品やオプションの料金と併せて見直してください。'), 'default', array('class' => 'alert alert-error'));
				} else {
					$this->Session->setFlash(__('保存に失敗しました。もう一度登録をお願いします。'), 'default', array('class' => 'alert alert-error'));
				}
			}
		} else {
			$this->request->data = $this->DisclaimerCompensation->read(null, $id);
		}
		$clientId = $this->clientData['Client']['id'];
		$carClasses = $this->CarClass->getCarClassLists($clientId);
		$this->set(compact('carClasses'));
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->DisclaimerCompensation->id = $id;
		if (!$this->DisclaimerCompensation->exists()) {
			throw new NotFoundException(__('Invalid disclaimer compensation'));
		}
		$saveData= array(
			'id' => $id,
			'staff_id' => $this->clientData['id'],
			'delete_flg' => 2,
			'deleted' => date("Y-m-d H:i:s", time()),
		);
		if ($this->DisclaimerCompensation->save($saveData)) {
			$this->Session->setFlash(__('削除しました。'), 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('削除できませんでした。'), 'default', array('class' => 'alert alert-error'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * 新規/更新時の合計料金マイナス判定
 * @param data->request
 * @return boolean
**/
	private function __checkMinusPrice($checkData) {
		//この免責事項を使っている商品を抽出する
		$car_class_id = $checkData['car_class_id'];
		$commodityIds = $this->CommodityItem->getCommodityId($car_class_id);
		//各商品で設定できるマイナス料金オプションを抽出する
		if(!empty($commodityIds)){
			//抽出した商品に紐づいているマイナス金額のオプションを抽出
			$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($commodityIds);
			//各商品の金額を出し、マイナスオプションを合算
			if(!empty($commodityPrivilegeData)){
				//商品IDごとにマイナスオプションを合算する
				foreach($commodityPrivilegeData as $ck => $cv){
					$commodityId = $cv['CommodityPrivilege']['commodity_id'];
					$commodityOptionPrice[$commodityId] += $cv['PrivilegePrice']['price'] * $cv['Privilege']['maximum'];
				}
			}
			//基本料金
			$commodityItemPrice = $this->Commodity->getPriceDataMulti(array_keys($commodityOptionPrice));
			//商品ごとにマイナスチェック
			foreach($commodityItemPrice as $ck => $cv){
				$price = $cv + $checkData['price'] + $commodityOptionPrice[$ck];
				if($price < 0){
					//料金がマイナスになる可能性がある場合NG
					return false;
				}
			}
		}

		return true;
	}
}

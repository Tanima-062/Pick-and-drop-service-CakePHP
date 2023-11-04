<?php
App::uses('AppController', 'Controller');
/**
 * CommodityGroups Controller
 *
 * @property CommodityGroup $CommodityGroup
 */
class CommodityGroupsController extends AppController {

	public $uses = array(
		'Commodity','CommodityGroup','CommodityTerm','Staff'
	);

	function beforeFilter() {
		parent::beforeFilter();
		$staffId = $this->clientData['id'];
		$isClientAdmin = $this->clientData['is_client_admin'];
		/**
		 * 編集対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit'), $this->action)) {
			// 編集対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集対象ID、クライアントID、スタッフIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->CommodityGroup->clientCheck($this->passedArgs[0], $this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}
		// 公開範囲
		$scopeList = array(0 => '共通');
		if ($isClientAdmin) {
			$scopeList += $this->Staff->getStaffList($this->clientData['client_id']);
		} else {
			$scopeList[$staffId] = $this->clientData['name'];
		}
		$this->set(compact('isClientAdmin', 'scopeList', 'staffId'));
		$this->set('is_check_user', true);
	}

	public function index() {

		//並び順を保存するを押されたとき
		if($this->request->is('post')) {
			if(!empty($this->request->data['Client']['sort'])) {
				$order = $this->request->data['Client']['sort'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {
					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->CommodityGroup->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}

		$this->set('commodityGroups', $this->CommodityGroup->getAll($this->clientData['Client']['id']));
	}

	public function edit($id) {

		// 商品の一覧を取得する
		$this->set('commodities', $this->Commodity->getNotGroupCommodityLists($this->clientData['Client']['id'], $id));

		if ($this->request->is('post') || $this->request->is('put')) {
			$saveFlg = true;
			$this->request->data['CommodityGroup']['client_id'] = $this->clientData['Client']['id'];
			$this->request->data['CommodityGroup']['staff_id'] = $this->clientData['id'];
			if ($this->request->data['CommodityGroup']['delete_flg'] == 1) {
				$this->request->data['CommodityGroup']['deleted'] = date("Y-m-d H:i:s", time());
			}
			$this->CommodityGroup->begin();
			$commodityGroup = $this->CommodityGroup->save($this->data['CommodityGroup']);
			if (empty($commodityGroup)) {
				$saveFlg = false;
			}

			if ($saveFlg) {
				$saveCommodity = array();
				if(!empty($this->data['Commodity'])) {
					// 一度グループに紐づくプランのグループIDを初期化
					$fields = array('commodity_group_id' => NULL, 'staff_id' => $this->clientData['id'], 'modified' => date("'Y-m-d H:i:s'", time()));
					$conditions = array('Commodity.commodity_group_id' => $id, 'Commodity.id' => array_keys($this->data['Commodity']));
					$this->Commodity->updateAll($fields, $conditions);

					foreach ($this->data['Commodity'] as $commodity) {

						// チェックされていなければ保存しない
						if ($commodity['id'] == 0) {
							continue;
						}
						array_push($saveCommodity,array(
							'id' => $commodity['id'],
							'commodity_group_id' => $commodityGroup['CommodityGroup']['id'],
							'staff_id' => $this->clientData['id'],
							'not_update_table' => true,
						));
					}
					if (!$this->Commodity->saveMany($saveCommodity)) {
						$saveFlg = false;
					}
				}
			}

			if ($saveFlg) {
				$this->CommodityGroup->commit();
				$this->Session->setFlash('商品グループを保存しました。','default',array('class'=> 'alert alert-success'));
			} else {
				$this->Session->setFlash('商品グループの保存に失敗しました。','default',array('class'=> 'alert alert-error'));
			}

			$this->redirect(array('action'=>'index'));

		} else {

			// フォーム用にデータセット
			$this->request->data = $this->CommodityGroup->getFirst($id);
			$this->request->data = array_merge($this->data,$this->Commodity->getBelongCommodityGroup($id));
		}
	}

	public function detail_edit($id) {

		// 商品の一覧を取得する
		$this->set('commodities',$this->Commodity->getCommodityLists($this->clientData['Client']['id']));

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['CommodityGroup']['client_id'] = $this->clientData['Client']['id'];
			$this->request->data['CommodityGroup']['staff_id'] = $this->clientData['id'];
			$this->request->data['CommodityGroup']['available_from'] .= ' '.$this->data['CommodityGroup']['from_hour']['hour'].':'.
			$this->data['CommodityGroup']['from_min']['min'];
			$this->request->data['CommodityGroup']['available_to'] .=  ' '.$this->data['CommodityGroup']['to_hour']['hour'].':'.
			$this->data['CommodityGroup']['to_min']['min'];

			$this->Commodity->begin();
			$commodityGroup = $this->CommodityGroup->save($this->data['CommodityGroup']);

			$commodityIds = $this->Commodity->getBelongCommodityGroup($id);

			$commodityTerm = array();
			foreach ($commodityIds['Commodity'] as $val) {
				// Commodity表を更新するコードも書かれていたが削除した
				// 唯一の更新項目is_publishedもコメント化されており、modifiedだけ更新されてしまうので
				array_push($commodityTerm,array(
						'id'=>$val['term_id'],
						'available_from'=>$commodityGroup['CommodityGroup']['available_from'],
						'available_to'=>$commodityGroup['CommodityGroup']['available_to'],
						'staff_id'=>$this->clientData['id'],
						'not_update_table' => true,));
			}

			if (!empty($commodityGroup) && $this->CommodityTerm->saveMany($commodityTerm)) {

				$this->Commodity->commit();
				$this->Session->setFlash(__('該当商品の情報を更新しました'));
				$this->redirect(array('controller'=>'commodities','action'=>'index'));
			} else {

				$this->Commodity->rollback();
				$this->Session->setFlash(__('内容の編集に失敗しました'));
				$this->redirect(array('controller'=>'commodities','action'=>'index'));
			}

		} else {

			// フォーム用にデータセット
			$this->request->data = $this->CommodityGroup->getFirst($id);
			$this->request->data = array_merge($this->data,$this->Commodity->getBelongCommodityGroup($id));

			// フォーム用に時間の配列整形
			if (!empty($this->data['CommodityGroup']['available_from'])) {

				$this->request->data['CommodityGroup']['from_hour']['hour'] =
						date('G',strtotime($this->data['CommodityGroup']['available_from']));
				$this->request->data['CommodityGroup']['from_min']['min'] =
						date('i',strtotime($this->data['CommodityGroup']['available_from']));

				$this->request->data['CommodityGroup']['to_hour']['hour'] =
						date('G',strtotime($this->data['CommodityGroup']['available_to']));
				$this->request->data['CommodityGroup']['to_min']['min'] =
						date('i',strtotime($this->data['CommodityGroup']['available_to']));

			} else {
				$this->request->data['CommodityGroup']['available_from'] = date('Y-m-d');
				$this->request->data['CommodityGroup']['available_to'] = date('Y-m-d');

			}

		}

		//時間フォームオプション
		$this->set('timeOption',array(
			'empty'=>false,'required'
		));

	}

	public function add() {

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['CommodityGroup']['client_id'] = $this->clientData['Client']['id'];
			$this->request->data['CommodityGroup']['staff_id'] = $this->clientData['id'];
			$commodityGroup = $this->CommodityGroup->save($this->data['CommodityGroup']);

			if (!empty($commodityGroup)) {
				$this->redirect(array('action'=>'index'));
			}
		}

	}
}

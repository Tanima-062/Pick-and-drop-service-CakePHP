<?php

App::uses('AppController', 'Controller');

/**
 * StockGroups Controller
 *
 * @property StockGroup $StockGroup
 */
class StockGroupsController extends AppController {

	public $uses = array('StockGroup', 'Commodity', 'OfficeStockGroup', 'Prefecture');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('is_check_user', true);
	}
	
	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {

		//並び順を保存するを押されたとき
		if ($this->request->is('post')) {
			if (!empty($this->request->data['StockGroups']['sort'])) {
				$order = $this->request->data['StockGroups']['sort'];
				$orderArray = explode(',', $order);
				$saveData = array();
				$i = 1;
				foreach ($orderArray as $val) {
					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if ($this->StockGroup->saveAll($saveData)) {
					$this->Session->setFlash('並び順を保存しました。', 'default', array('class' => 'alert alert-success'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。', 'default', array('class' => 'alert alert-success'));
				}
			}
		}

		$conditions['conditions']['StockGroup.delete_flg'] = 0;
		$conditions['conditions']['StockGroup.client_id'] = $this->clientData['client_id'];
		$conditions['order']['StockGroup.sort'] = 'asc';
		$conditions['order'] = array('StockGroup.sort' => 'ASC', 'StockGroup.id' => 'ASC');
		$conditions['limit'] = 1000;
		$conditions['maxLimit'] = 1000;
		$this->paginate = $conditions;

		$this->StockGroup->recursive = -1;
		$this->set('stockGroups', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->redirect(array('controller' => 'StockGroups', 'action' => 'index'));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {

		$clientData = $this->clientData;

		if ($this->request->is('post')) {

			$this->request->data['StockGroup']['sort'] = 1000;
			$this->request->data['StockGroup']['staff_id'] = $clientData['id'];

			$this->StockGroup->create();
			if ($this->StockGroup->save($this->request->data)) {
				$this->Session->setFlash('在庫管理地域を追加しました。', 'default', array('class' => 'alert alert-success'));

				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('在庫管理地域を追加に失敗しました。', 'default', array('class' => 'alert alert-error'));
			}
		}
		$clients = $this->StockGroup->Client->find('list');
		$staffs = $this->StockGroup->Staff->find('list');
		$prefectureList = $this->Prefecture->getPrefectureList();

		$this->set(compact('clients', 'staffs', 'clientData', 'prefectureList'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {

		if (!$this->StockGroup->exists($id)) {
			throw new NotFoundException(__('Invalid stock group'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['StockGroup']['staff_id'] = $this->clientData['id'];

			//削除の場合、すでに在庫管理地域に紐づいている商品があるかチェック
			if (!empty($this->request->data['StockGroup']['delete_flg'])) {

				$stockGroupCount = $this->OfficeStockGroup->getOfficeStockGroupCount($id);
				if ($stockGroupCount == 0) {
					$this->request->data['StockGroup']['deleted'] = date("Y-m-d H:i:s", time());
					if ($this->StockGroup->save($this->request->data)) {
						$this->Session->setFlash('編集しました。', 'default', array('class' => 'alert alert-success'));
						$this->redirect(array('action' => 'index'));
					} else {
						$this->set('error', '1');
						$this->Session->setFlash('編集に失敗しました。', 'default', array('class' => 'alert alert-error'));
					}
				} else {
					$this->set('error', '1');
					$this->Session->setFlash('営業所に該当の在庫管理地域が登録されているため削除できません。', 'default', array('class' => 'alert alert-error'));
				}
			} else {
				if ($this->StockGroup->save($this->request->data)) {
					$this->Session->setFlash('編集しました。', 'default', array('class' => 'alert alert-success'));
					$this->redirect(array('action' => 'index'));
				} else {

					$this->set('error', '1');
					$this->Session->setFlash('編集に失敗しました。', 'default', array('class' => 'alert alert-error'));
				}
			}
		} else {
			$options = array('conditions' => array('StockGroup.' . $this->StockGroup->primaryKey => $id));
			$this->StockGroup->recursive = -1;
			$this->request->data = $this->StockGroup->find('first', $options);
			if ($this->request->data['StockGroup']['client_id'] !== $this->clientData['Client']['id']) {
				$this->redirect(array("controller" => "Users", "action" => "logout"));
			}
		}
		$clients = $this->StockGroup->Client->find('list');
		$staffs = $this->StockGroup->Staff->find('list');
		$prefectureList = $this->Prefecture->getPrefectureList();

		$this->set(compact('clients', 'staffs', 'prefectureList'));
	}

	public function getStockGroupByPrefecture($prefectureId) {
		$this->StockGroup->recursive = -1;
		if ($this->request->is('ajax')) {
			$this->autoLayout = false;
			$clientId = $this->clientData['client_id'];
			if (empty($this->clientData['is_system_admin'])) {
				$staffId = $this->clientData['id'];
			} else {
				$staffId = '';
			}
			$stockGroups = $this->StockGroup->getStockGroupList($clientId, $prefectureId, $staffId);
			$this->set('stockGroups', $stockGroups);
		}
	}

}

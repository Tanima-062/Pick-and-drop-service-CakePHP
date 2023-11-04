<?php
App::uses('AppController', 'Controller');
/**
 * Clients Controller
 *
 * @property Client $CarClassStock
 */
class ClientCarModelsController extends AppController {

	public $uses = array('ClientCarModel', 'CarClass', 'Automaker', 'CarModel');

    public function beforeFilter() {
		parent::beforeFilter();
		$this->set('is_check_user', true);
	}

	public function index() {
        if ($this->request->is('post') || $this->request->is('put')) {
			if (!empty($this->request->data['ClientCarModel'])) {
				$deleteData = array();
				foreach ($this->request->data['ClientCarModel'] as $val) {
					if ($val['delete_flg'] == 1) {
						$carClassCount = $this->ClientCarModel->getCarClassCount($this->clientData['client_id'], $val['car_model_id']);
						if ($carClassCount > 0) {
							$this->Session->setFlash(__('登録済み車両クラスに該当の車種が登録されているため削除できません。'), 'default', array('class' => 'alert alert-error'), 'error');
							$deleteData = array();
							break;
						} else {
							// 削除対象レコードは表示中の1レコードのみならず（車種が同じ）全てのレコード
							$deleteIds = $this->ClientCarModel->getIds($this->clientData['client_id'], $val['car_model_id']);
							foreach ($deleteIds as $v) {
								$deleteData[] = array(
									'id' => $v['ClientCarModel']['id'],
									'delete_flg' => 1,
									'staff_id' => $this->clientData['id'],
									'deleted' => date("Y-m-d H:i:s", time()),
								);
							}
						}
					}
				}
				if (!empty($deleteData)) {
					$this->ClientCarModel->saveall($deleteData);
				}
			}
			$this->redirect(array('action' => 'index'));
		}

		$clientCarModelLists = $this->ClientCarModel->getClientCarModelList($this->clientData['client_id']);
		$this->set(compact(array('clientCarModelLists')));
	}

	public function add() {
		if (!empty($this->request->data['ClientCarModel'])) {
			foreach ($this->request->data['ClientCarModel'] as $key => $val) {
				if ($val['add_flg'] == 1) {

					unset($val['add_flg']);
					$saveData[$key] = $val;
					$saveData[$key]['client_id']  = $this->clientData['client_id'];

					$saveData[$key]['delete_flg'] = 0;
					$saveData[$key]['staff_id']   = $this->clientData['id'];

				} else {
					unset($this->request->data['ClientCarModel'][$key]);
				}
			}


			if (!empty($saveData)) {
				$this->ClientCarModel->recursive = -1;
				$this->ClientCarModel->saveall($saveData);
			}
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->request->data['CarModel']['automaker_id'])) {
			$carModelLists = $this->CarModel->getCarModel($this->request->data['CarModel']['automaker_id']);
			$this->set(array(
				'automaker_id'  => $this->request->data['CarModel']['automaker_id'],
				'carModelLists' => $carModelLists,
			));
		}


		$automakerLists = $this->Automaker->getAutomaker();
		$this->set(compact(array('automakerLists')));
	}
}
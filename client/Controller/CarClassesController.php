<?php
App::uses('AppController', 'Controller');
/**
 * Clients Controller
 *
 * @property Client $CarClassStock
 * @property CarClass $CarClass
 * @property StockGroup $StockGroup
 */
class CarClassesController extends AppController {

	public $uses = array('CarClass', 'CarType', 'Commodity','StockGroup','ClientCarModel',
			'CarClassStockGroup','CarModel','Automaker','ClientCarModel','Staff');

	// 乗捨料金パターン
	public $dropOffPricePatternList = array(
		1 => '料金＃1',
		2 => '料金＃2',
		3 => '料金＃3',
	);

	function beforeFilter() {
		parent::beforeFilter();
		$staffId = $this->clientData['id'];
		$isClientAdmin = $this->clientData['is_client_admin'];
		/**
		 * 編集対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit'),$this->action)) {
			// 編集対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集対象ID、クライアントID、スタッフIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->CarClass->clientCheck($this->passedArgs[0], $this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}
		$carTypeLists = $this->CarType->getCarType();
		$carModelLists = $this->CarModel->getAllList();
		$autoMaker = $this->Automaker->getAutomaker();
		$stockGroupList = $this->StockGroup->getStockGroupListWithUnassociated($this->clientData['Client']['id'], '', $staffId);

		$clientCarModelLists  = $this->ClientCarModel->getClientModel($this->clientData['Client']['id']);
		// 公開範囲
		$scopeList = array(0 => '共通');
		if ($isClientAdmin) {
			$scopeList += $this->Staff->getStaffList($this->clientData['client_id']);
		} else {
			$scopeList[$staffId] = $this->clientData['name'];
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

		$this->set(compact(array('carTypeLists','stockGroupList','carModelLists','autoMaker','clientCarModelLists','scopeList', 'isClientAdmin', 'staffId', 'dropOffPricePatternList')));
		$this->set('is_check_user', true);
	}

	public function index() {

		//並び順を保存するを押されたとき
		if($this->request->is('post')) {
			if(!empty($this->request->data['Client']['order'])) {
				$order = $this->request->data['Client']['order'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {
					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->CarClass->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}

		//$carClassLists = $this->CarClass->getCarClassList($this->clientData['client_id']);

		$options = array(
				'recursive' => -1,
				'conditions' => array(
						'CarClass.delete_flg' => 0,
						'CarClass.client_id'=> $this->clientData['client_id'],
				),
				'order' => array('CarClass.sort' => 'ASC', 'CarClass.id' => 'ASC'),
		);
		if (!$this->clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('CarClass.scope' => 0),
				array('CarClass.scope' => $this->clientData['id'])
			);
		}

		$carClassLists = $this->CarClass->find('all', $options);

		$carClassId = array();
		foreach($carClassLists as $carClass) {
			$carClassId[] = $carClass['CarClass']['id'];
		}

		$clientCarModel = $this->ClientCarModel->getClientCarModelsByCarClassId($carClassId,$this->clientData['client_id']);

		$this->set(compact(array('carClassLists','clientCarModel')));
	}

	public function edit($id) {

		if ($this->request->is('post') || $this->request->is('put')) {

			if(!empty($this->request->data['CarClass']['delete_flg'])) {
				$carClassId = $this->request->data['CarClass']['id'];
				$count = $this->Commodity->getCarClassCount($carClassId,$this->clientData['client_id']);

				if($count == 0) {
					$this->_saveCarClass();
					$this->Session->setFlash('クラスを削除しました。','default',array('class'=>'alert alert-error'));
				} else {

					$this->set('error','1');
					$this->Session->setFlash(__('登録済み商品に該当のクラスが登録されているため削除できません。'),'',array(),'auth');
				}
			} else {

                $carModelSave = false;
                // カーモデルが一つでもチェックされていれば保存
                foreach ($this->data['ClientCarModel']['car_model_id'] as $val) {
                  if (!empty($val)) {
                    $carModelSave = true;
                    break;
                  }
                }

                $carClassStockSave = false;
                // 在庫管理地域が一つでもチェックされていれば保存
                foreach ($this->data['CarClassStockGroup']['stock_group_id'] as $val) {
                  if (!empty($val)) {
                    $carClassStockSave = true;
                    break;
                  }
                }

                $error = '';
                if(!$carClassStockSave) {
                  $error .= "※在庫管理地域を設定して下さい<br />";
                }

                if(!$carModelSave) {
                  $error .= "※車種を設定して下さい<br />";
                }

                if (!empty($error)) {
                  $this->Session->setFlash($error,'default',array('class'=>'alert alert-error'));
                } else {
                  $this->_saveCarClass();
                }
			}
		}

		$carClassDetail = $this->CarClass->getCarClassAndCarClassDetail($this->clientData['client_id'], $id);
		$clientCarModel = $this->ClientCarModel->getClientCarModel($id);
		$mergeCarModel['car_model_id'] = array();
		foreach ($clientCarModel as $val) {
			$mergeCarModel['car_model_id'][$val['ClientCarModel']['car_model_id']] = $val['ClientCarModel']['car_model_id'];
			$mergeCarModel['id'][$val['ClientCarModel']['car_model_id']] = $val['ClientCarModel']['id'];
		}
		$carClassDetail['CarModel'] = $mergeCarModel;

		$stockGroupArray = array();
		if(!empty($carClassDetail['CarClassStockGroup'])) {
			foreach($carClassDetail['CarClassStockGroup'] as $key => $val) {
				$stockGroupArray[] = $val['stock_group_id'];
			}
		}

		$this->set(compact(array('carClassDetail', 'stockGroupArray')));

	}

	public function add() {

		if ($this->request->is('post') || $this->request->is('put')) {

			$carModelSave = false;
			// カーモデルが一つでもチェックされていれば保存
			foreach ($this->data['ClientCarModel']['car_model_id'] as $val) {
				if (!empty($val)) {
					$carModelSave = true;
					break;
				}
			}

			$carClassStockSave = false;
			// 在庫管理地域が一つでもチェックされていれば保存
			foreach ($this->data['CarClassStockGroup']['stock_group_id'] as $val) {
				if (!empty($val)) {
					$carClassStockSave = true;
					break;
				}
			}

			$error = '';
			if(!$carClassStockSave) {
				$error .= "※在庫管理地域を設定して下さい<br />";
			}

			if(!$carModelSave) {
				$error .= "※車種を設定して下さい<br />";
			}


			if (!empty($error)) {
				$this->Session->setFlash($error,'default',array('class'=>'alert alert-error'));
			} else {
				$this->_saveCarClass();
			}
		}
	}

	protected function _saveCarClass() {

		$this->request->data['CarClass']['client_id'] = $this->clientData['client_id'];
		$this->request->data['CarClass']['staff_id']  = $this->clientData['id'];
		if ($this->request->params['action'] == 'add') {
			$this->request->data['CarClass']['sort'] = 1000;
		}
		if (!empty($this->request->data['CarClass']['delete_flg'])) {
			$this->request->data['CarClass']['deleted']  = date("Y-m-d H:i:s", time());
		}
		$saved = $this->CarClass->save($this->request->data['CarClass']);

		if (!empty($this->data['CarClassStockGroup']['stock_group_id'])) {
			$this->_saveCarClassStockGroup($saved);
		}
		if (isset($this->data['ClientCarModel']['car_model_id'])) {
			$this->_saveClientCarModel($saved);
		}

		$this->redirect(array('action' => 'index'));

	}

	protected function _saveCarClassStockGroup($carClass = false) {

		$stockGroupList = $this->StockGroup->getStockGroupListWithUnassociated($this->clientData['Client']['id'], '', $this->clientData['id']);
		if (!empty($carClass)) {
			$carClassId = $carClass['CarClass']['id'];
		} else {
			$carClassId = $this->data['CarClass']['id'];
		}
		$conditions = array(
			'car_class_id' => $carClassId,
			'stock_group_id' => array_keys($stockGroupList)
		);
		$this->CarClassStockGroup->deleteAll($conditions);

		$saveData = array();
		if(!empty($this->data['CarClassStockGroup']['stock_group_id'])) {
			$alertCountArray = $this->data['CarClassStockGroup']['alert_count'];

			foreach ($this->data['CarClassStockGroup']['stock_group_id'] as $key => $stockGroupId) {
				$key = $stockGroupId;
				$saveData[$key]['stock_group_id'] = $stockGroupId;
				$saveData[$key]['car_class_id'] = $carClassId;

				if(!empty($alertCountArray[$key])) {
					$saveData[$key]['stock_alert_count'] = $alertCountArray[$key];
				} else {
					$saveData[$key]['stock_alert_count'] = 0;
				}

				$saveData[$key]['staff_id'] = $this->clientData['id'];
			}

			$this->CarClassStockGroup->saveMany($saveData);
		}

	}

	protected function _saveClientCarModel($carClass) {

		$saveData = array();
		$deleteClientCarModel = array();
		foreach ($this->data['ClientCarModel']['car_model_id'] as $key => $carModelId) {

			if (is_array($carModelId)) {
				$carModelId = array_shift($carModelId);
			}

			if (!empty($carModelId) && isset($this->data['ClientCarModel']['id'][$carModelId])) {
				array_push($saveData,array(
						'id' => $this->data['ClientCarModel']['id'][$carModelId],
						'car_model_id' => $carModelId,
						'client_id' => $carClass['CarClass']['client_id'],
						'car_class_id' => $carClass['CarClass']['id'],
						'staff_id' => $this->clientData['id'],
						'not_update_table' => true,
					)
				);
			} else if (!empty($carModelId) && !isset($this->data['ClientCarModel']['id'][$carModelId])) {
				array_push($saveData,array(
						'car_model_id' => $carModelId,
						'client_id' => $carClass['CarClass']['client_id'],
						'car_class_id' => $carClass['CarClass']['id'],
						'staff_id' => $this->clientData['id'],
						'not_update_table' => true,
					)
				);
			} else {
				array_push($deleteClientCarModel,$key);
			}
		}
		$conditions = array(
			'client_id' => $carClass['CarClass']['client_id'],
			'car_class_id' => $carClass['CarClass']['id'],
			'car_model_id' => $deleteClientCarModel,
		);
		$fields = array(
			'staff_id' => $this->clientData['id'],
			'delete_flg' => 1,
			'deleted' => date("'Y-m-d H:i:s'", time()),
		);
		$this->ClientCarModel->unbindModel(
			array(
				'belongsTo' => array_keys($this->ClientCarModel->belongsTo),
				'hasMany' => array_keys($this->ClientCarModel->hasMany)
			)
		);
		$this->ClientCarModel->updateAll($fields,$conditions,false);

		$this->ClientCarModel->saveMany($saveData);
	}

}
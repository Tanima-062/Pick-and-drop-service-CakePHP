<?php
App::uses('AppController', 'Controller');
/**
 * ClientCarModelSorts Controller
 *
 * @property ClientCarModelSort $ClientCarModelSort
 */
class ClientCarModelSortsController extends AppController {

	public $uses = array('CarModel','ClientCarModelSort');

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
		if($this->request->is('post')) {
			if(!empty($this->request->data['Client']['order'])) {
				$order = $this->request->data['Client']['order'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {

					$saveData[$i]['car_model_id'] = $val;
					$saveData[$i]['client_id'] = $this->clientData['Client']['id'];

					$data = $this->ClientCarModelSort->find('first',array('conditions'=>array(
								'car_model_id'=> $saveData[$i]['car_model_id'],
								'client_id'=>$saveData[$i]['client_id']
							)
						)
					);

					if(!empty($data['ClientCarModelSort']['id'])) {
						$saveData[$i]['id'] = $data['ClientCarModelSort']['id'];
					}

					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->ClientCarModelSort->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}



		$this->CarModel->recursive = 0;

		$carModelList = $this->CarModel->find('all',array(
																'conditions'=>array(
																		'CarModel.delete_flg'=>0,
																		'ClientCarModel.delete_flg'=>0,
																		'ClientCarModel.client_id'=>$this->clientData['Client']['id']
																),
																'joins'=>array(
																		array(
																				'type'=>'INNER',
																				'alias'=>'ClientCarModel',
																				'table'=>'client_car_models',
																				'conditions'=>'ClientCarModel.car_model_id = CarModel.id',
																		),
																		array(
																				'type'=>'LEFT',
																				'alias'=>'ClientCarModelSort',
																				'table'=>'client_car_model_sorts',
																				'conditions'=>"ClientCarModelSort.client_id={$this->clientData['Client']['id']} and ClientCarModelSort.car_model_id = CarModel.id"
																			)
																),
																'fields'=>'CarModel.*,ClientCarModelSort.*,min(ClientCarModelSort.sort) as sort',
																'group'=>'CarModel.name,CarModel.displacement',
																'order'=>'sort asc',
																'recursive'=>-1
																)
														);

		$this->set('clientCarModelSorts', $carModelList);
	}

}

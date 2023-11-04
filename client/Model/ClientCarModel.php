<?php
App::uses('AppModel', 'Model');
/**
 * ClientCarModel Model
 *
 * @property Client $Client
 * @property CarClass $CarClass
 * @property CarModel $CarModel
 * @property Staff $Staff
 */
class ClientCarModel extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'car_class_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'car_model_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CarClass' => array(
			'className' => 'CarClass',
			'foreignKey' => 'car_class_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CarModel' => array(
			'className' => 'CarModel',
			'foreignKey' => 'car_model_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getClientCarModelList($clientId) {

		$options = array(
				'fields' => array(
					'ClientCarModel.id',
					'ClientCarModel.car_model_id',
					'CarModel.name',
					'CarModel.trunk_space',
					'CarModel.golf_bag',
					'CarModel.displacement',
					'CarModel.capacity',
					'CarModel.mileage',
					'CarModel.automaker_id',
					'Automaker.name',
					'CarClass.name',
					'CarType.name',
				),
				'conditions' => array(
					'ClientCarModel.delete_flg' => 0,
					'ClientCarModel.client_id'  => $clientId
				),
				'recursive' => -1,
				'joins' => array (
					array(
						'type'  => 'left',
						'table' => 'car_models',
						'alias' => 'CarModel',
						'conditions' => array (
							'ClientCarModel.car_model_id = CarModel.id',
							'CarModel.delete_flg' => 0,
						),
					),
					array(
						'type'  => 'left',
						'table' => 'automakers',
						'alias' => 'Automaker',
						'conditions' => array (
							'CarModel.automaker_id = Automaker.id',
							'Automaker.delete_flg' => 0,
						),
					),
					array(
						'type'  => 'left',
						'table' => 'car_classes',
						'alias' => 'CarClass',
						'conditions' => array (
							'ClientCarModel.car_class_id = CarClass.id',
							'CarClass.delete_flg' => 0,
							'CarClass.client_id' => $clientId,
						),
					),
					array(
						'type'  => 'left',
						'table' => 'car_types',
						'alias' => 'CarType',
						'conditions' => array (
							'CarClass.car_type_id = CarType.id',
							'CarType.delete_flg' => 0,
						),
					),
				),
				'group'=>'CarModel.name,CarModel.displacement',
				'order'=>'CarModel.automaker_id,ClientCarModel.created asc'
		);

		return $this->find('all', $options);
	}

	public function getClientCarModelDetail($id, $clientId) {

		$options = array(
				'fields' => array(
					'ClientCarModel.id',
					'ClientCarModel.car_class_id',
					'CarModel.name',
					'CarModel.displacement',
					'Automaker.name',
				),
				'conditions' => array(
					'ClientCarModel.delete_flg' => 0,
					'ClientCarModel.client_id'  => $clientId,
					'ClientCarModel.id'         => $id,
				),
				'recursive' => -1,
				'joins' => array (
					array(
						'type'  => 'left',
						'table' => 'car_models',
						'alias' => 'CarModel',
						'conditions' => array (
							'ClientCarModel.car_model_id = CarModel.id',
							'CarModel.delete_flg' => 0,
						),
					),
					array(
						'type'  => 'left',
						'table' => 'automakers',
						'alias' => 'Automaker',
						'conditions' => array (
							'CarModel.automaker_id = Automaker.id',
							'Automaker.delete_flg' => 0,
						),
					),
					array(
						'type'  => 'left',
						'table' => 'car_classes',
						'alias' => 'CarClass',
						'conditions' => array (
							'ClientCarModel.car_class_id = CarClass.id',
							'CarClass.delete_flg' => 0,
						),
					),
				),
		);

		return $this->find('first', $options);
	}

	public function getClientCarModel($carClassId) {

		$options = array(
				'conditions' => array(
						'car_class_id' => $carClassId,
						'delete_flg' => 0,
				),
				'recursive' => -1
		);

		return $this->find('all',$options);
	}

	public function getClientModel($clientId) {
		$options = array(
				'fields' => array(
						'ClientCarModel.id',
						'ClientCarModel.car_class_id',
						'CarModel.id',
						'CarModel.name',
						'CarModel.trunk_space',
						'CarModel.displacement',
						'CarModel.capacity',
						'CarModel.mileage',
						'CarModel.automaker_id',
						'Automaker.name',
						'CarClass.name',
						'CarType.name',
				),
				'conditions' => array(
						'ClientCarModel.delete_flg' => 0,
						'ClientCarModel.client_id'  => $clientId
				),
				'recursive' => -1,
				'joins' => array (
						array(
								'type'  => 'left',
								'table' => 'car_models',
								'alias' => 'CarModel',
								'conditions' => array (
										'ClientCarModel.car_model_id = CarModel.id',
										'CarModel.delete_flg' => 0,
								),
						),
						array(
								'type'  => 'left',
								'table' => 'automakers',
								'alias' => 'Automaker',
								'conditions' => array (
										'CarModel.automaker_id = Automaker.id',
										'Automaker.delete_flg' => 0,
								),
						),
						array(
								'type'  => 'left',
								'table' => 'car_classes',
								'alias' => 'CarClass',
								'conditions' => array (
										'ClientCarModel.car_class_id = CarClass.id',
										'CarClass.delete_flg' => 0,
										'CarClass.client_id' => $clientId,
								),
						),
						array(
								'type'  => 'left',
								'table' => 'car_types',
								'alias' => 'CarType',
								'conditions' => array (
										'CarClass.car_type_id = CarType.id',
										'CarType.delete_flg' => 0,
								),
						),
				),
				'group'=>'CarModel.id',
				'order'=>'CarModel.automaker_id,ClientCarModel.created asc'
		);

		return $this->find('all', $options);

	}

	public function getClientCarModelsByCarClassId($carClassId,$clientId) {
		$clientCarModels = $this->find('all',array(
				'conditions'=>array(
						'ClientCarModel.car_class_id'=>$carClassId,
						'ClientCarModel.client_id'=>$clientId,
						'ClientCarModel.delete_flg'=>0,
						'CarModel.delete_flg'=>0
				),
				'joins'=>array(
						array(
								'type'=>'INNER',
								'alias'=>'CarModel',
								'table'=>'car_models',
								'conditions'=>'CarModel.id = ClientCarModel.car_model_id'
						),
						array(
								'type'=>'INNER',
								'alias'=>'Automaker',
								'table'=>'automakers',
								'conditions'=>'Automaker.id = CarModel.automaker_id'
						)
				),
				'fields'=>'CarModel.*,ClientCarModel.*,Automaker.*,min(Automaker.id) as automaker_id',
				'group'=>'ClientCarModel.car_class_id,CarModel.id',
				'order'=>'automaker_id',
				'recursive'=>-1
			)
		);

		$carModelArray = array();
		foreach($clientCarModels as $val) {
			$carClassId = $val['ClientCarModel']['car_class_id'];

			if(empty($carModelArray[$carClassId]['name'])) {
				$carModelArray[$carClassId]['name'] = '';
			}
			$carModelArray[$carClassId]['name'] .= $val['Automaker']['name'] .'・' . $val['CarModel']['name'] . "<br />";
		}

		return $carModelArray;

	}

	public function getCarClassCount($clientId, $carModelId) {
		$options = array(
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions' => array(
						'CarClass.id = ClientCarModel.car_class_id',
						'CarClass.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'ClientCarModel.client_id' => $clientId,
				'ClientCarModel.car_model_id' => $carModelId,
				'ClientCarModel.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->find('count', $options);
	}

	public function getIds($clientId, $carModelId) {
		$options = array(
			'fields' => array(
				'ClientCarModel.id',
			),
			'conditions' => array(
				'ClientCarModel.client_id' => $clientId,
				'ClientCarModel.car_model_id' => $carModelId,
				'ClientCarModel.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}
}

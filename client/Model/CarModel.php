<?php
App::uses('AppModel', 'Model');
/**
 * CarModel Model
 *
 * @property Automaker $Automaker
 * @property Staff $Staff
 */
class CarModel extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'automaker_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'trunk_space' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'displacement' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'image_relative_url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
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
		'seo' => array(
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
		'Automaker' => array(
			'className' => 'Automaker',
			'foreignKey' => 'automaker_id',
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

	public function getCarModel($automakerId) {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'automaker_id' => $automakerId,
			),
			'order'=>'name asc',
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getList() {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
			),
			'order' => 'displacement ASC',
			'recursive' => -1,
		);

		return $this->find('list', $options);
	}

	public function getAllList() {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
			),
			'order' => 'automaker_id ASC, displacement ASC',
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	public function getCarModelListByCarClassId($carClassId) {
		$carModelList = $this->find('list',
			array(
				'joins' => array(
					array(
						'type' => 'INNER',
						'alias' => 'ClientCarModel',
						'table' => 'client_car_models',
						'conditions' => array(
							'ClientCarModel.car_class_id' => $carClassId,
							'ClientCarModel.delete_flg' => 0,
							'ClientCarModel.car_model_id = CarModel.id'
						)
					)
				),
				'conditions' => array('CarModel.delete_flg' => 0)
			)
		);
		return $carModelList;
	}

	public function getCarModelForSippCode($carClassId, $carModelId = null) {
		// 車両クラスに紐づく車種情報と車両タイプを取得
		$options = array(
			'fields' => array(
				'CarModel.id',
				'CarModel.name',
				'CarModel.capacity',
				'CarModel.door',
				'CarType.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'ClientCarModel',
					'table' => 'client_car_models',
					'conditions' => array(
						'ClientCarModel.car_model_id = CarModel.id',
						'ClientCarModel.delete_flg' => 0,
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarClass',
					'table' => 'car_classes',
					'conditions' => array(
						'CarClass.id = ClientCarModel.car_class_id',
						'CarClass.delete_flg' => 0,
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarType',
					'table' => 'car_types',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id',
						'CarType.delete_flg' => 0,
					)
				),
			),
			'conditions' => array(
				'CarClass.id' => $carClassId,
			),
			'recursive' => -1,
		);
		
		// 車種の指定がある時
		if (!empty($carModelId)) {
			$options['conditions']['CarModel.id'] = $carModelId;
		}
		
		// 最初にマッチングしたもののみ返す
		return $this->find('first', $options);
	}

	public function getCarModelListByClientIdAndCarClassId($clientId, $carClassId) {
		$options = array(
			'fields' => array(
				'CarModel.name',
				'ClientCarModelSort.sort',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'client_car_models',
					'alias' => 'ClientCarModel',
					'conditions' => array(
						'ClientCarModel.car_model_id = CarModel.id',
						'ClientCarModel.car_class_id' => $carClassId,
						'ClientCarModel.client_id' => $clientId,
						'ClientCarModel.delete_flg' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'client_car_model_sorts',
					'alias' => 'ClientCarModelSort',
					'conditions' => array(
						'ClientCarModelSort.car_model_id = CarModel.id',
						'ClientCarModelSort.client_id' => $clientId,
						'ClientCarModelSort.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'CarModel.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		$result = $this->find('all', $options);
		if (!empty($result)) {
			$result = Hash::sort($result, '{n}.ClientCarModelSort.sort', 'asc');

			$tmp = array();
			$unique = array();
			foreach ($result as $r) {
				if (!in_array($r['CarModel']['name'], $tmp)) {
					$tmp[] = $r['CarModel']['name'];
					$unique[] = $r;
				}
			}
			$result = $unique;
		}

		return $result;
	}
}

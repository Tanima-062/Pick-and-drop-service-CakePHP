<?php
App::uses('AppModel', 'Model');
/**
 * CarClass Model
 *
 * @property Client $Client
 * @property CarType $CarType
 * @property Staff $Staff
 * @property CarClassReservation $CarClassReservation
 * @property CarClassStock $CarClassStock
 * @property ClientCarModel $ClientCarModel
 * @property CommodityItem $CommodityItem
 */
class CarClass extends AppModel {

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
		'car_type_id' => array(
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
		'CarType' => array(
			'className' => 'CarType',
			'foreignKey' => 'car_type_id',
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

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CarClassReservation' => array(
			'className' => 'CarClassReservation',
			'foreignKey' => 'car_class_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CarClassStock' => array(
			'className' => 'CarClassStock',
			'foreignKey' => 'car_class_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ClientCarModel' => array(
			'className' => 'ClientCarModel',
			'foreignKey' => 'car_class_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CommodityItem' => array(
			'className' => 'CommodityItem',
			'foreignKey' => 'car_class_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function getCarClassFirst($id,$carTypeId = null) {

		$options = array(
				'conditions' => array(
						'id' => $id,
						'delete_flg' => 0
				),
				'recursive' => -1
		);

		if(!empty($carTypeId)) {
			$options['conditions']['car_type_id'] =  $carTypeId;
		}

		return $this->find('first', $options);
	}

	public function getCarClassDataFirst($carClassId) {
		$this->Behaviors->attach('Containable');
		$this->contain(array(
				'CarType' => array(
						'fields' => array('CarType.id', 'CarType.name', 'CarType.lower_limit'),
						'conditions' => array('CarType.delete_flg' => 0,),
				),
		));

		$options = array(
				'conditions' => array(
						'CarClass.id' => $carClassId,
						'CarClass.delete_flg' => 0
				),
				'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getCarClassByClientId($clientId) {

		$options = array(
				'conditions' => array(
						'delete_flg' => 0,
						'client_id' => $clientId
				),
				'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getCarClassAndStockGroup($clientId,$commodityGroupId = false,$carClassId = null,$carTypeId = null,$options = array()) {

	  if(!empty($options['stock_group_id'])) {
	      $carClassStockGroup = "(
				SELECT
					car_class_stock_groups.car_class_id,
					car_class_stock_groups.stock_group_id,
					stock_groups.name
				FROM
					car_class_stock_groups,
					stock_groups
				WHERE
					car_class_stock_groups.stock_group_id = stock_groups.id
					AND car_class_stock_groups.delete_flg = 0
					AND stock_groups.id = {$options['stock_group_id']}
					AND stock_groups.client_id = {$clientId}
	          )";
	    } else {
	      $carClassStockGroup = "(
				SELECT
					car_class_stock_groups.car_class_id,
					car_class_stock_groups.stock_group_id,
					stock_groups.name
				FROM
					car_class_stock_groups,
					stock_groups
				WHERE
					car_class_stock_groups.stock_group_id = stock_groups.id
					AND car_class_stock_groups.delete_flg = 0
					AND stock_groups.client_id = {$clientId}
	          )";
	  }

		$options = array(
				'fields' => array(
						'CarClass.*',
						'GROUP_CONCAT(CarClassStockGroup.stock_group_id ORDER BY CarClassStockGroup.stock_group_id ASC,"") as stock_group_id'
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'CarClassStockGroup',
								'table' => "{$carClassStockGroup}",
								'conditions' => 'CarClassStockGroup.car_class_id = CarClass.id'
						)
				),
				'conditions' => array(
						'CarClass.delete_flg' => 0,
						'CarClass.client_id' => $clientId
				),
				'group' => 'CarClass.id',
				'order' => 'CarClass.sort ASC,CarClass.id ASC',
				'recursive' => -1
		);

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('CarClass.scope' => 0),
				array('CarClass.scope' => $clientData['id'])
			);
		}

		if(!empty($carTypeId)) {
			$options['conditions']['car_type_id'] = $carTypeId;
		}

		if (!empty($carClassId) && !empty($commodityGroupId)) {
			$Commodity = ClassRegistry::init('Commodity');
			$commodityCarClass = $Commodity->getCarClassIds($commodityGroupId);
			if (in_array($carClassId, $commodityCarClass)) {
				$options['conditions']['CarClass.id'] = $carClassId;
			} else {
				$options['conditions']['CarClass.id'] = 0;
			}
		} elseif (!empty($commodityGroupId)) {
			$Commodity = ClassRegistry::init('Commodity');
			$options['conditions']['CarClass.id'] = $Commodity->getCarClassIds($commodityGroupId);
		} elseif (!empty($carClassId)) {
			$options['conditions']['CarClass.id'] = $carClassId;
		}

		$result =  $this->find('all', $options);
		foreach ($result as $key => $val) {
			$result[$key][0]['stock_group_id'] = explode(',',$val[0]['stock_group_id']);
		}

		return $result;
	}


	public function getCarClassLists($clientId) {

		$options = array(
				'conditions' => array(
						'delete_flg' => 0,
						'client_id' => $clientId
				),
				'order' => 'CarClass.sort ASC,CarClass.id ASC',
				'recursive' => -1
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('CarClass.scope' => 0),
				array('CarClass.scope' => $clientData['id'])
			);
		}

		return $this->find('list', $options);
	}

	public function getCarClass($clientId) {

		$options = array(
				'conditions' => array(
						'delete_flg' => 0,
						'client_id' => $clientId
				),
				'recursive' => -1
		);

		return $this->find('list', $options);
	}

	/**
	 * csv出力に必要な車両クラスをを取得
	 * 過去の営業所のデータも取得しないといけないので、delete_flgは条件に加えない。
	 */
	public function getCsvCarClassList($clientId) {

		$options = array(
				'conditions' => array(
						'client_id' => $clientId
				),
				'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function getCarClassList($clientId) {

		$carClassStockGroup = "(
				SELECT
					car_class_stock_groups.car_class_id,
					car_class_stock_groups.stock_group_id,
					stock_groups.name
				FROM
					car_class_stock_groups,
					stock_groups
				WHERE
					car_class_stock_groups.stock_group_id = stock_groups.id
					AND car_class_stock_groups.delete_flg = 0)";

		$options = array(
				'fields' => array(
						'CarClass.*',
						'GROUP_CONCAT(CarClassStockGroup.name ORDER BY CarClassStockGroup.stock_group_id ASC,"") as name'
				),
				'joins' => array(
						array(
								'type' => 'LEFT',
								'alias' => 'CarClassStockGroup',
								'table' => "{$carClassStockGroup}",
								'conditions' => 'CarClassStockGroup.car_class_id = CarClass.id'
						)
				),
				'conditions' => array(
						'CarClass.delete_flg' => 0,
						'CarClass.client_id' => $clientId
				),
				'group' => 'CarClass.id',
				'order' => 'CarClass.sort ASC,CarClass.id ASC',
				'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getCarClassDetail($clientId, $id) {

		$options = array(
				'fields' => array(
					'CarClass.id',
					'CarClass.car_type_id',
					'CarClass.name',
					'GROUP_CONCAT(CarClassStockGroup.stock_group_id,"") as stock_group_id',
				),
				'joins' => array(
						array(
								'type' => 'LEFT',
								'alias' => 'CarClassStockGroup',
								'table' => "car_class_stock_groups",
								'conditions' => 'CarClassStockGroup.car_class_id = CarClass.id'
						)
				),
				'conditions' => array(
						'CarClass.delete_flg' => 0,
						'CarClass.client_id'  => $clientId,
						'CarClass.id'         => $id,
				),
				'recursive' => -1
		);
		$result = $this->find('first', $options);
		$stockGroupId = explode(',',$result[0]['stock_group_id']);
		$result['CarClassStockGroup']['stock_group_id'] = array();
		foreach ($stockGroupId as $val) {
			if ($val) {
				array_push($result['CarClassStockGroup']['stock_group_id'],$val);
			}
		}

		return $result;
	}

	public function getCarClassAndCarClassDetail($clientId, $id) {

		$options = array(
				'fields' => array(
						'CarClass.id',
						'CarClass.car_type_id',
						'CarClass.name',
						'CarClass.drop_off_price_pattern',
						'CarClass.scope',
						'CarClassStockGroup.*'
						//'GROUP_CONCAT(CarClassStockGroup.stock_group_id,"") as stock_group_id',
				),
				'joins' => array(
						array(
								'type' => 'LEFT',
								'alias' => 'CarClassStockGroup',
								'table' => "car_class_stock_groups",
								'conditions' => 'CarClassStockGroup.car_class_id = CarClass.id'
						)
				),
				'conditions' => array(
						'CarClass.delete_flg' => 0,
						'CarClass.client_id'  => $clientId,
						'CarClass.id'         => $id,
				),
				'recursive' => -1
		);
		$results = $this->find('all', $options);

		$result = array();
		foreach($results as $key => $val) {
			$result['CarClass'] = $val['CarClass'];
			$result['CarClassStockGroup'][] = $val['CarClassStockGroup'];
		}

		return $result;

	}

/**
 * アラートリスト取得
 */
	public function getStockAlertCountList($clientId) {

		$stockAlertCounts = $this->find('all',array(
				'conditions'=>array(
						'CarClass.client_id'=>$clientId
				),
				'joins'=>array(
						array(
								'type'=>'INNER',
								'table'=>'car_class_stock_groups',
								'alias'=>'CarClassStockGroup',
								'conditions'=>'CarClass.id = CarClassStockGroup.car_class_id'
						)
				),
				'fields'=>array('CarClass.*','CarClassStockGroup.*'),
				'recursive'=>'-1'
		)
		);

		$stockAlertCountArray = array();
		foreach($stockAlertCounts as $stockAlertCount) {
			$stockGroupId = $stockAlertCount['CarClassStockGroup']['stock_group_id'];
			$carClassId = $stockAlertCount['CarClassStockGroup']['car_class_id'];
		}


		return $stockAlertCountArray;
	}

	public function getCarTypeByClientID($clientId) {

		$options = array(
				'conditions' => array(
						'CarClass.client_id'=>$clientId,
						'CarClass.delete_flg' => 0,
						'CarType.delete_flg' => 0,
				),
				'joins'=>array(
						array(
							'type'=>'LEFT',
							'alias'=>'CarType',
							'table'=>'car_types',
							'conditions'=>'CarType.id = CarClass.car_type_id'
						)
				),
				'fields'=>array(
							'CarType.id',
							'CarType.name',
						)

		);

		return $this->find('list', $options);
	}

	public function getAllCarClassList() {

		$options = array(
				'fields' => array(
						'id',
						'name',
				),
				'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function getCarTypeList() {

		return $this->find('list',array(
				'joins'=>array(
						array(
								'type'=>'INNER',
								'table'=>'car_types',
								'alias'=>'CarType',
								'conditions'=>'CarType.id = CarClass.car_type_id'
						)
				),
				'fields'=>array(
						'CarClass.id',
						'CarType.name'
				),
				'recursive'=>-1
		)
				);
	}

	/**
	 * 編集ID、クライアントID、スタッフIDで不正アクセスを判定
	 * @param unknown $id
	 * @param unknown $clientId
	 */
	public function clientCheck($id, $clientId) {
		$options = array(
			'conditions' => array(
				'CarClass.id' => $id,
				'CarClass.client_id' => $clientId,
			),
			'recursive' => -1,
		);

		$clientData = $this->_getCurrentUser();

		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('CarClass.scope' => 0),
				array('CarClass.scope' => $clientData['id'])
			);
		}

		$result = $this->find('first', $options);

		if (!empty($result)) {
			return true;
		} else {
			return false;
		}
	}
}

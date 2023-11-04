<?php
App::uses('AppModel', 'Model');
/**
 * CarModel Model
 *
 * @property Automaker $Automaker
 * @property Staff $Staff
 */
class CarModel extends AppModel {

	protected $cacheConfig = '1day';

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

	public function getListByClientAndPrefectureId($clientIds, $prefectureId) {

		$options = array(
			'fields'=>array(
				'Client.id',
				'CarType.id',
				'CarType.name',
				'CarType.description',
				'Automaker.id',
				'Automaker.name',
				'CarModel.id',
				'CarModel.name'
			),
			'conditions' => array(
				'CarModel.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>array(
						'Client.id' => $clientIds,
						'Client.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'Commodity',
					'table'=>'commodities',
					'conditions'=>array(
						'Commodity.client_id = Client.id',
						'Commodity.is_published' => 1,
						'Commodity.delete_flg' => 0,
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CommodityItem',
					'table'=>'commodity_items',
					'conditions'=>array(
						'CommodityItem.commodity_id = Commodity.id',
						'CommodityItem.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CarClass',
					'table'=>'car_classes',
					'conditions'=>array(
						'CarClass.id = CommodityItem.car_class_id',
						'CarClass.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CarType',
					'table'=>'car_types',
					'conditions'=>array(
						'CarType.id = CarClass.car_type_id',
						'CarType.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'ClientCarModel',
					'table'=>'client_car_models',
					'conditions'=>array(
						'ClientCarModel.car_class_id = CarClass.id',
						'ClientCarModel.client_id = Client.id',
						'ClientCarModel.car_model_id = CarModel.id',
						'ClientCarModel.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'Automaker',
					'table'=>'automakers',
					'conditions'=>array(
						'Automaker.id = CarModel.automaker_id',
						'Automaker.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CommodityRentOffice',
					'table'=>'commodity_rent_offices',
					'conditions'=>array(
						'CommodityRentOffice.commodity_id = Commodity.id',
						'CommodityRentOffice.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>array(
						'Office.id = CommodityRentOffice.office_id',
						'Office.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'Area',
					'table'=>'areas',
					'conditions'=>array(
						'Area.id = Office.area_id',
						'Area.prefecture_id' => $prefectureId,
						'Area.delete_flg' => 0
					)
				),
			),
			'group' => array(
				'Client.id',
				'CarType.id',
				'CarType.name',
				'Automaker.id',
				'Automaker.name',
				'CarModel.id',
				'CarModel.name'
			),
			'recursive' => -1
		);

		return $this->findC('all', $options);
	}

	public function getListByClientAndOfficeId($clientIds, $officeIds) {

		$options = array(
			'fields'=>array(
				'Client.id',
				'Office.id',
				'CarType.id',
				'CarType.name',
				'CarType.description',
				'Automaker.id',
				'Automaker.name',
				'CarModel.id',
				'CarModel.name'
			),
			'conditions' => array(
				'CarModel.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>array(
						'Client.id' => $clientIds,
						'Client.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'Commodity',
					'table'=>'commodities',
					'conditions'=>array(
						'Commodity.client_id = Client.id',
						'Commodity.is_published' => 1,
						'Commodity.delete_flg' => 0,
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CommodityItem',
					'table'=>'commodity_items',
					'conditions'=>array(
						'CommodityItem.commodity_id = Commodity.id',
						'CommodityItem.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CarClass',
					'table'=>'car_classes',
					'conditions'=>array(
						'CarClass.id = CommodityItem.car_class_id',
						'CarClass.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CarType',
					'table'=>'car_types',
					'conditions'=>array(
						'CarType.id = CarClass.car_type_id',
						'CarType.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'ClientCarModel',
					'table'=>'client_car_models',
					'conditions'=>array(
						'ClientCarModel.car_class_id = CarClass.id',
						'ClientCarModel.client_id = Client.id',
						'ClientCarModel.car_model_id = CarModel.id',
						'ClientCarModel.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'Automaker',
					'table'=>'automakers',
					'conditions'=>array(
						'Automaker.id = CarModel.automaker_id',
						'Automaker.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'CommodityRentOffice',
					'table'=>'commodity_rent_offices',
					'conditions'=>array(
						'CommodityRentOffice.commodity_id = Commodity.id',
						'CommodityRentOffice.delete_flg' => 0
					)
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>array(
						'Office.id' => $officeIds,
						'Office.id = CommodityRentOffice.office_id',
						'Office.delete_flg' => 0
					)
				),
			),
			'group' => array(
				'Client.id',
				'Office.id',
				'CarType.id',
				'CarType.name',
				'Automaker.id',
				'Automaker.name',
				'CarModel.id',
				'CarModel.name'
			),
			'recursive' => -1
		);

		return $this->findC('all', $options);
	}

	public function countByOfficeId($officeIds) {
		if (empty($officeIds)) {
			return 0;
		}

		$options = array(
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'ClientCarModel',
					'table' => 'client_car_models',
					'conditions' => 'CarModel.id = ClientCarModel.car_model_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarClass',
					'table' => 'car_classes',
					'conditions' => 'CarClass.id = ClientCarModel.car_class_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => 'CarClass.id = CommodityItem.car_class_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Commodity',
					'table' => 'commodities',
					'conditions' => 'Commodity.id = CommodityItem.commodity_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityRentOffice',
					'table' => 'commodity_rent_offices',
					'conditions' => 'Commodity.id = CommodityRentOffice.commodity_id',
				),
			),
			'conditions' => array(
				'CommodityRentOffice.office_id' => $officeIds,
				'Commodity.is_published' => 1,
				'CarModel.delete_flg' => 0,
				'ClientCarModel.delete_flg' => 0,
				'CarClass.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
				'Commodity.delete_flg' => 0,
				'CommodityRentOffice.delete_flg' => 0,
			),
			'group' => array(
				'CarModel.id',
			),
			'recursive' => -1
		);

		$ret = $this->findC('count', $options);

		return $ret;
	}

	public function getCarModelListWithAutomaker($clientId) {
		$options = array(
			'fields' => array(
				'DISTINCT Automaker.id',
				'Automaker.name',
				'CarModel.id',
				'CarModel.name',
			),
			'joins' => array(
				array(
					'table' => 'client_car_models',
					'alias' => 'ClientCarModel',
					'type' => 'INNER',
					'conditions' => array(
						'ClientCarModel.car_model_id = CarModel.id',
						'ClientCarModel.client_id' => $clientId,
						'ClientCarModel.car_class_id' => 0,
						'ClientCarModel.delete_flg' => 0,
					),
				),
				array(
					'table' => 'automakers',
					'alias' => 'Automaker',
					'type' => 'INNER',
					'conditions' => array(
						'CarModel.automaker_id = Automaker.id',
						'Automaker.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'CarModel.delete_flg' => 0,
			),
			'order' => array(
				'Automaker.id',
				'ClientCarModel.created',
			),
			'recursive' => -1,
		);

		return $this->findC('all', $options);
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

		$result = $this->findC('all', $options);
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

<?php

App::uses('AppModel', 'Model');

/**
 * Equipment Model
 *
 * @property Staff $Staff
 * @property Commodity $Commodity
 */
class Equipment extends AppModel {

	protected $cacheConfig = '1day';

	/**
	 * Use table
	 *
	 * @var mixed False or table name
	 */
	public $useTable = 'equipments';

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'image_relative_url' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'seo' => array(
			'notempty' => array(
				'rule' => array('notblank'),
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasAndBelongsToMany associations
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'Commodity' => array(
			'className' => 'Commodity',
			'joinTable' => 'commodity_equipments',
			'foreignKey' => 'equipment_id',
			'associationForeignKey' => 'commodity_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	public function getEquipmentList() {

		$options = array(
			'fields' => array('id', 'name'),
			'conditions' => array(
				'is_published' => 1,
				'delete_flg' => 0,
			),
			'order' => 'sort asc',
			'recursive' => -1
		);

		return $this->findC('list', $options);
	}

	public function getEquipmentListWithoutCondition() {

		$options = array(
			'fields' => array('id', 'name'),
			'order' => 'sort asc',
			'recursive' => -1
		);

		return $this->findC('list', $options);
	}

	public function getEquipment() {

		$options = array(
			'fields' => array(
				'id',
				'option_category_id',
				'name',
				'description',
			),
			'conditions' => array(
				'is_published' => 1,
				'delete_flg' => 0,
			),
			'order' => 'sort asc',
			'recursive' => -1
		);

		return $this->findC('all', $options);
	}

	public function getEquipmentListByClientAndPrefectureId($clientId, $prefectureId) {

		$options = array(
			'fields' => array('id', 'name'),
			'conditions' => array(
				'Equipment.is_published' => 1,
				'Equipment.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_equipments',
					'alias' => 'CommodityEquipment',
					'conditions' => array(
						'CommodityEquipment.equipment_id = Equipment.id'
					)
				),
				array(
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => array(
						'Commodity.id = CommodityEquipment.commodity_id',
						'Commodity.client_id' => $clientId,
						'Commodity.is_published' => 1,
						'Commodity.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'table' => 'commodity_rent_offices',
					'alias' => 'CommodityRentOffice',
					'conditions' => array(
						'CommodityRentOffice.commodity_id = Commodity.id'
					)
				),
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => array(
						'Office.id = CommodityRentOffice.office_id',
						'Office.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'table' => 'areas',
					'alias' => 'Area',
					'conditions' => array(
						'Area.id = Office.area_id',
						'Area.prefecture_id' => $prefectureId,
						'Area.delete_flg' => 0
					)
				)
			),
			'order' => 'Equipment.sort asc',
			'recursive' => -1
		);

		return $this->findC('list', $options);
	}

	public function getCommodityEquipmentList($commodityId) {

		$options = array(
			'fields' => array('Equipment.id', 'Equipment.name'),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_equipments',
					'alias' => 'CommodityEquipment',
					'conditions' => array(
						'CommodityEquipment.commodity_id' => $commodityId,
						'CommodityEquipment.delete_flg' => 0,
						'CommodityEquipment.equipment_id = Equipment.id',
					),
				),
			),
			'conditions' => array(
				'Equipment.is_published' => 1,
				'Equipment.delete_flg' => 0,
			),
			'order' => 'Equipment.sort asc',
			'recursive' => -1
		);

		return $this->findC('list', $options);
	}
}

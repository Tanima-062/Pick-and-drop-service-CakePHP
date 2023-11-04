<?php
App::uses('AppModel', 'Model');
/**
 * Equipment Model
 *
 * @property Staff $Staff
 */
class Equipment extends AppModel {

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
				'rule' => array('notempty'),
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getEquipmentList() {

		$options = array(
				'fields' => array('id', 'name'),
				'conditions' => array('delete_flg' => 0),
				'order'=>'sort asc',
				'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function getEquipmentApiPostData($clientId) {
		$options = array(
			'fields' => array(
				'Reservation.id',
				'Equipment.id',
				'Equipment.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_equipments',
					'alias' => 'CommodityEquipment',
					'conditions' => 'CommodityEquipment.equipment_id = Equipment.id',
				),
				array(
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => 'Commodity.id = CommodityEquipment.commodity_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => 'CommodityItem.commodity_id = Commodity.id',
				),
				array(
					'type' => 'INNER',
					'table' => 'reservations',
					'alias' => 'Reservation',
					'conditions' => 'Reservation.commodity_item_id = CommodityItem.id',
				),
			),
			'conditions' => array(
				'Reservation.client_id' => $clientId,
				'Reservation.api_status_id' => Constant::apiTargetStatus(),
			),
			'order' => 'Equipment.sort',
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	public function getCommodityEquipmentList($commodityId)
	{

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

		return $this->find('list', $options);
	}

	public function getEquipment() {

		$options = array(
			'fields' => array(
				'id',
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

		return $this->find('all', $options);
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * Privilege Model
 *
 * @property Client $Client
 * @property Staff $Staff
 * @property PrivilegePrice $PrivilegePrice
 * @property Commodity $Commodity
 * @property Reservation $Reservation
 */
class Privilege extends AppModel {

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
		'PrivilegePrice' => array(
			'className' => 'PrivilegePrice',
			'foreignKey' => 'privilege_id',
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


/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Commodity' => array(
			'className' => 'Commodity',
			'joinTable' => 'commodity_privileges',
			'foreignKey' => 'privilege_id',
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
		),
		'Reservation' => array(
			'className' => 'Reservation',
			'joinTable' => 'reservation_privileges',
			'foreignKey' => 'privilege_id',
			'associationForeignKey' => 'reservation_id',
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



	public function maxLimitCheck($privilege = array()) {
		$returnFlg = true;
		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.maximum'
			),
			'conditions' => array(
				'Privilege.id' => array_keys($privilege),
				'Privilege.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$result = $this->find('list', $options);
		foreach ($privilege as $key => $value) {
			if ($result[$key] < $value) {
				$returnFlg = false;
			}
		}
		return $returnFlg;
	}


	public function getClientPrivilegeList($clientId = null) {
		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.name',
			),
			'conditions' => array(
				'Privilege.client_id' => $clientId,
			),
			'recursive' => -1,
		);
		$result = $this->find('list', $options);
		return $result;
	}

	public function getOptionListByClientAndPrefectureId($clientId, $prefectureId) {

		$options = array(
			'fields' => array('id', 'name'),
			'conditions' => array(
				'Privilege.option_flg' => 0,
				'Privilege.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_privileges',
					'alias' => 'CommodityPrivilege',
					'conditions' => array(
						'CommodityPrivilege.privilege_id = Privilege.id'
					)
				),
				array(
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => array(
						'Commodity.id = CommodityPrivilege.commodity_id',
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
			'order' => 'Privilege.id asc',
			'recursive' => -1
		);

		return $this->findC('list', $options);
	}

	public function getPrivilegeApiPostData($reservationId, $optionFlg) {
		if ($optionFlg == 0) {
			$table = 'reservation_privileges';
			$alias = 'ReservationPrivilege';
			$id = 'privilege_id';
		} else if ($optionFlg == 1) {
			$table = 'reservation_child_sheets';
			$alias = 'ReservationChildSheet';
			$id = 'child_sheet_id';
		} else {
			return array();
		}

		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.name',
				"{$alias}.count",
				"{$alias}.price",
				"'{$alias}' AS alias",
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => "{$table}",
					'alias' => "{$alias}",
					'conditions' => "{$alias}.{$id} = Privilege.id",
				),
			),
			'conditions' => array(
				"{$alias}.reservation_id" => $reservationId,
			),
			'order' => "{$alias}.id",
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}
}

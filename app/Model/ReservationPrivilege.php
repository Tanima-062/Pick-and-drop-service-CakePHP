<?php

App::uses('AppModel', 'Model');

/**
 * ReservationPrivilege Model
 *
 * @property Reservation $Reservation
 * @property Privilege $Privilege
 * @property PrivilegePrice $PrivilegePrice
 * @property Staff $Staff
 */
class ReservationPrivilege extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'reservation_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'privilege_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'privilege_price_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
		'Reservation' => array(
			'className' => 'Reservation',
			'foreignKey' => 'reservation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Privilege' => array(
			'className' => 'Privilege',
			'foreignKey' => 'privilege_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PrivilegePrice' => array(
			'className' => 'PrivilegePrice',
			'foreignKey' => 'privilege_price_id',
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

	public function getReservationPrivilegeData($reservationId) {
		$options = array(
			'fields' => array(
				'Privilege.name',
				'ReservationPrivilege.count',
				'ReservationPrivilege.price',
			),
			'joins' => array(
				array(
					'table' => 'privileges',
					'alias' => 'Privilege',
					'type' => 'INNER',
					'conditions' => array(
						'Privilege.id = ReservationPrivilege.privilege_id'
					),
				),
			),
			'conditions' => array(
				'ReservationPrivilege.reservation_id' => $reservationId,
				'ReservationPrivilege.count >' => 0,
				'ReservationPrivilege.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);
		return $result;
	}

	public function getReservationPrivilegeByReservationIds($reservationIds) {
		$options = array(
			'fields' => array(
				'ReservationPrivilege.reservation_id',
				'ReservationPrivilege.privilege_id',
				'ReservationPrivilege.count',
				'ReservationPrivilege.price',
			),
			'conditions' => array(
				'ReservationPrivilege.reservation_id' => $reservationIds,
				'ReservationPrivilege.count >' => 0,
				'ReservationPrivilege.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->find('all', $options);
	}
}

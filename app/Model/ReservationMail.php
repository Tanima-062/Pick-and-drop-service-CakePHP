<?php

App::uses('AppModel', 'Model');

/**
 * ReservationMail Model
 *
 * @property Reservation $Reservation
 * @property ReservationMail $ReservationMail
 * @property Staff $Staff
 * @property ReservationMail $ReservationMail
 */
class ReservationMail extends AppModel {

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
		'mail_datetime' => array(
			'datetime' => array(
				'rule' => array('datetime'),
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
		'contents' => array(
			'notempty' => array(
				'rule' => array('notempty'),
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
		'ReservationMail' => array(
			'className' => 'ReservationMail',
			'foreignKey' => 'reservation_mail_id',
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
		'ReservationMail' => array(
			'className' => 'ReservationMail',
			'foreignKey' => 'reservation_mail_id',
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

	public function getMailById($reservationId) {

		$options = array(
			'fields' => array(
				'ReservationMail.*',
				'Staff.name'
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'Staff',
					'table' => 'staffs',
					'conditions' => array(
						'ReservationMail.staff_id = Staff.id'
					)
				)
			),
			'conditions' => array(
				'ReservationMail.reservation_id' => $reservationId,
				'ReservationMail.delete_flg' => 0
			),
			'order' => 'ReservationMail.created DESC',
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function readingMail($reservationId) {

		$this->query("
				UPDATE
					rentacar.reservation_mails
				SET
					reservation_mails.read_flg = 1
				WHERE
					reservation_mails.staff_id > 0
					AND reservation_mails.reservation_id = :reservationId", array('reservationId' => $reservationId), false);
	}

}

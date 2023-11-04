<?php
App::uses('AppModel', 'Model');
/**
 * ReservationDetail Model
 *
 * @property Reservation $Reservation
 * @property Staff $Staff
 */
class ReservationDetail extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'detail_type_id' => array(
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
		'amount' => array(
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getDetailApiPostData($reservationId) {
		$options = array(
			'fields' => array(
				'ReservationDetail.detail_type_id',
				'ReservationDetail.amount',
			),
			'conditions' => array(
				'ReservationDetail.reservation_id' => $reservationId,
			),
			'order' => array(
				'ReservationDetail.id',
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	public function getDetailsByReservationIds($reservationIds) {
		$options = array(
			'fields' => array(
				'ReservationDetail.reservation_id',
				'ReservationDetail.detail_type_id',
				'ReservationDetail.amount',
			),
			'conditions' => array(
				'ReservationDetail.reservation_id' => $reservationIds,
			),
			'order' => array(
				'ReservationDetail.id',
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	public function getDropOffNightFee($reservationId) {
		$options = array(
			'fields' => array(
				'ReservationDetail.detail_type_id',
				'ReservationDetail.count',
				'ReservationDetail.amount',
				'DetailType.name',
			),
			'joins' => array(
				array(
					'table' => 'detail_types',
					'alias' => 'DetailType',
					'type' => 'INNER',
					'conditions' => array(
						'DetailType.id = ReservationDetail.detail_type_id'
					),
				),
			),
			'conditions' => array(
				'ReservationDetail.reservation_id' => $reservationId,
				'ReservationDetail.detail_type_id' => array(Constant::DETAIL_TYPE_DROPOFFPRICE, Constant::DETAIL_TYPE_NIGHTFEE),
				'ReservationDetail.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}
}

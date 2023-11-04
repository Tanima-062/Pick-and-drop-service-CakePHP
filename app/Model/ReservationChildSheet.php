<?php

App::uses('AppModel', 'Model');

/**
 * ReservationChildSheet Model
 *
 * @property Reservation $Reservation
 * @property ChildSheet $ChildSheet
 * @property ChildSheetPrice $ChildSheetPrice
 * @property Staff $Staff
 */
class ReservationChildSheet extends AppModel {

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
		'child_sheet_id' => array(
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
		'ChildSheet' => array(
			'className' => 'ChildSheet',
			'foreignKey' => 'child_sheet_id',
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
	 * 予約者のチャイルドシート情報を取得
	 */
	public function getReservationChildSheet($reservationId = null) {

		$options = array(
			'fields' => array(
				'ReservationChildSheet.reservation_id',
				'ReservationChildSheet.child_sheet_id',
				'ReservationChildSheet.count',
				'ReservationChildSheet.price',
			),
			'conditions' => array(
				'ReservationChildSheet.reservation_id' => $reservationId,
				'ReservationChildSheet.count >' => 0,
				'ReservationChildSheet.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->find('all', $options);
	}

	public function getReservationChildSheetData($reservationId) {
		$options = array(
			'fields' => array(
				'Privilege.name',
				'ReservationChildSheet.count',
				'ReservationChildSheet.price',
			),
			'joins' => array(
				array(
					'table' => 'privileges',
					'alias' => 'Privilege',
					'type' => 'INNER',
					'conditions' => array(
						'Privilege.id = ReservationChildSheet.child_sheet_id'
					),
				),
			),
			'conditions' => array(
				'ReservationChildSheet.reservation_id' => $reservationId,
				'ReservationChildSheet.count >' => 0,
				'ReservationChildSheet.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$results = $this->find('all', $options);
		return $results;
	}

}

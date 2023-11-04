<?php
App::uses('AppModel', 'Model');
/**
 * ReservationDetail Model
 *
 * @property Reservation $Reservation
 * @property DetailType $DetailType
 * @property DetailItem $DetailItem
 * @property PriceRank $PriceRank
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
		'price' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'tax' => array(
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
		'total_tax' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'total_price' => array(
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
		'DetailType' => array(
			'className' => 'DetailType',
			'foreignKey' => 'detail_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PriceRank' => array(
			'className' => 'PriceRank',
			'foreignKey' => 'price_rank_id',
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

	public function saveDetail($data, $reservationId) {

		$conditions = array(
				'reservation_id' => $reservationId
		);
		$this->deleteAll($conditions,false);

		if (!empty($data)) {
			$this->saveMany($data);
		}
	}

/**
 * 内訳データ取得
 * @param unknown $reservationId
 */
	public function getBreakDownData($reservationId) {

		$options = array(
				'conditions' => array(
						'ReservationDetail.reservation_id' => $reservationId,
						'ReservationDetail.delete_flg' => 0,
				),
				'joins'=>array(
						array(
								'type'=>'LEFT',
								'alias'=>'DetailType',
								'table'=>'detail_types',
								'conditions'=>'ReservationDetail.detail_type_id = DetailType.id'
						)
				),
				'fields'=>array(
						'ReservationDetail.*',
						'DetailType.*'
				),
				'order' => array('ReservationDetail.detail_type_id ASC'),
				'recursive' => -1,
		);
		return $this->find('all', $options);
	}

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
}

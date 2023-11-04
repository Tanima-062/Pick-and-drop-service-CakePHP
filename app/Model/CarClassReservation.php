<?php
App::uses('AppModel', 'Model');
/**
 * CarClassReservation Model
 *
 * @property Client $Client
 * @property StockGroup $StockGroup
 * @property CarClass $CarClass
 * @property Reservation $Reservation
 * @property Staff $Staff
 */
class CarClassReservation extends AppModel {

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
		'stock_group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'car_class_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'stock_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
		'reservation_count' => array(
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
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'StockGroup' => array(
			'className' => 'StockGroup',
			'foreignKey' => 'stock_group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CarClass' => array(
			'className' => 'CarClass',
			'foreignKey' => 'car_class_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
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


	public function getCarClassReservation($clientId, $carClassId, $stockGroupIds, $from) {
		$options = array(
				'conditions' => array(
						'CarClassReservation.client_id' => $clientId,
						'CarClassReservation.car_class_id' => $carClassId,
						'CarClassReservation.stock_group_id' => $stockGroupIds,
						'CarClassReservation.stock_date' => $from,
						'CarClassReservation.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		return $this->find('all', $options);
	}


	/**
	 * プラン詳細で使用
	 * @param unknown $commodityItemData
	 * @param unknown $from
	 * @param unknown $to
	 */
	public function getCarClassReservationData($commodityItemData, $stockGroupIds, $from, $to) {
		$options = array(
				'conditions' => array(
						'CarClassReservation.client_id' => $commodityItemData['CommodityItem']['client_id'],
						'CarClassReservation.stock_group_id' => $stockGroupIds,
						'CarClassReservation.car_class_id' => $commodityItemData['CommodityItem']['car_class_id'],
						'CarClassReservation.stock_date >=' => $from,
						'CarClassReservation.stock_date <=' => $to,
						'CarClassReservation.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('all', $options);
		return $result;
	}


	public function getCarClassReservationCount($stockGroupId, $carClassId, $year, $month, $day = 0) {
		$options = array(
			'fields' => array(
				"DATE_FORMAT(CarClassReservation.stock_date, '%e') AS day",
				'SUM(CarClassReservation.reservation_count) AS count',
			),
			'conditions' => array(
				'CarClassReservation.stock_group_id' => $stockGroupId,
				'CarClassReservation.car_class_id' => $carClassId,
				'CarClassReservation.delete_flg' => 0,
			),
			'group' => 'CarClassReservation.stock_date',
			'recursive' => -1,
		);

		if (empty($day)) {
			$targetMonth = sprintf('%04d-%02d-', $year, $month);
			$options['conditions']['CarClassReservation.stock_date BETWEEN ? AND ?'] = array($targetMonth.'01', $targetMonth.'31');
		} else {
			$options['conditions']['CarClassReservation.stock_date'] = sprintf('%04d-%02d-%02d', $year, $month, $day);
		}

		$ret = $this->find('all', $options);
		if (!empty($ret)) {
			$ret = Hash::combine($ret, '{n}.0.day', '{n}.0.count');
		}

		return $ret;
	}
}

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

	public function getCarClassReservationCount($data, $clientId) {

		$this->virtualFields['reservation_count'] = 0;
		$options = array(
				'fields' => array(
						'CarClassReservation.stock_date',
						'SUM(CarClassReservation.reservation_count) AS "CarClassReservation__reservation_count"'
				),
				'conditions' => array(
						'delete_flg' => 0,
						'client_id' => $clientId,
						'stock_group_id' => $data['stock_group_id'],
						'car_class_id' => $data['car_class_id'],
						'stock_date BETWEEN ? AND ?' => array($data['year'].'-'.$data['month'].'-01',$data['year'].'-'.$data['month'].'-31')
				),
				'group' => 'stock_date',
				'order' => array('stock_date' => 'ASC'),
				'recursive' => -1
		);

		$carClassReservation =  $this->find('all', $options);
		$result = array();
		foreach ($carClassReservation as $val) {
			$index = substr($val['CarClassReservation']['stock_date'], 8, 2);
			$result[$index] = $val['CarClassReservation']['reservation_count'];
		}

		return $result;
	}

	//上の関数の日付部位を改造した関数
	public function getCarClassReservationByDateTimeCount($data, $clientId) {

		$this->virtualFields['reservation_count'] = 0;
		$options = array(
				'fields' => array(
						'CarClassReservation.stock_date',
						'SUM(CarClassReservation.reservation_count) AS "CarClassReservation__reservation_count"'
				),
				'conditions' => array(
						'delete_flg' => 0,
						'client_id' => $clientId,
						'stock_group_id' => $data['stock_group_id'],
						'car_class_id' => $data['car_class_id'],
						'stock_date BETWEEN ? AND ?' => array($data['min_date'],$data['max_date'])
				),
				'group' => 'stock_date',
				'order' => array('stock_date' => 'ASC'),
				'recursive' => -1
		);

		$carClassReservation =  $this->find('all', $options);

		$result = array();
		foreach ($carClassReservation as $val) {
			$index = $val['CarClassReservation']['stock_date'];
			$result[$index] = $val['CarClassReservation']['reservation_count'];
		}

		return $result;
	}

	public function getCarClassReservationCountAll($data,$clientId) {

		$this->virtualFields['reservation_count'] = 0;
		$options = array(
				'fields' => array(
						'CarClassReservation.stock_date',
						'CarClassReservation.stock_group_id',
						'CarClassReservation.car_class_id',
						'SUM(CarClassReservation.reservation_count) AS "CarClassReservation__reservation_count"'
				),
				'conditions' => array(
						'delete_flg' => 0,
						'client_id' => $clientId,
						/*
						'stock_group_id' => $data['stock_group_id'],
						'car_class_id' => $data['car_class_id'],
						'stock_date BETWEEN ? AND ?' => array($data['year'].'-'.$data['month'].'-01',$data['year'].'-'.$data['month'].'-31')
						*/
						'stock_date BETWEEN ? AND ?' => array($data['from_date'],$data['to_date'])
				),
				'group' => array('stock_date','stock_group_id','car_class_id'),
				'order' => array('stock_date' => 'ASC'),
				'recursive' => -1
		);

		$carClassReservation =  $this->find('all', $options);
		$result = array();
		foreach ($carClassReservation as $val) {
			$year = substr($val['CarClassReservation']['stock_date'], 0,4);
			$month = substr($val['CarClassReservation']['stock_date'], 5,2);
			$day = substr($val['CarClassReservation']['stock_date'], 8, 2);
			$stockGroupId = $val['CarClassReservation']['stock_group_id'];
			$carClassId = $val['CarClassReservation']['car_class_id'];
			$result[$year][$month][$day][$stockGroupId][$carClassId] = $val['CarClassReservation']['reservation_count'];
		}

		return $result;

	}
}

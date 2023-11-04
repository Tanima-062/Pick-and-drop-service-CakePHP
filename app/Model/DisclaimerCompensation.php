<?php
App::uses('AppModel', 'Model');
/**
 * DisclaimerCompensation Model
 *
 * @property Client $Client
 * @property CarClass $CarClass
 * @property Staff $Staff
 */
class DisclaimerCompensation extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
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
		'start_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'end_date' => array(
			'date' => array(
				'rule' => array('date'),
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
			'numeric' => array(
				'rule' => array('numeric'),
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
		'CarClass' => array(
			'className' => 'CarClass',
			'foreignKey' => 'car_class_id',
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
	 * 免責補償料金取得
	 *
	 * @param int $carClassId
	 * @param string $from
	 * @param string $to
	 * @param int $period 日数(暦日制)
	 * @param int $period24 日数(24時間制)
	 * @return int
	 */
	public function getFee($carClassId, $from, $to, $period, $period24) {
		$options = array(
			'fields' => array(
				'DisclaimerCompensation.price',
				'DisclaimerCompensation.period_flg',
				'DisclaimerCompensation.period_limit',
			),
			'conditions' => array(
				'DisclaimerCompensation.car_class_id' => $carClassId,
				'DisclaimerCompensation.delete_flg' => 0,
				'OR' => array(
					'OR' => array(
						array(
							'DisclaimerCompensation.start_date <=' => $from,
							'DisclaimerCompensation.end_date >=' => $from,
						),
						array(
							'DisclaimerCompensation.start_date <=' => $to,
							'DisclaimerCompensation.end_date >=' => $to,
						)
					),
					array(
						array(
							'DisclaimerCompensation.start_date >=' => $from,
							'DisclaimerCompensation.start_date <=' => $to,
						),
						array(
							'DisclaimerCompensation.end_date >=' => $from,
							'DisclaimerCompensation.end_date <=' => $to,
						)
					)
				),
			),
			'recursive' => -1
		);

		$result = $this->find('first', $options);

		if (empty($result)) {
			return 0;
		}

		$result = $result['DisclaimerCompensation'];

		// 加算方法によって日数を変える
		$disclaimerPeriod = ($result['period_flg'] == 1) ? $period24 : $period;

		// 上限設定ありで上限を超えた場合
		if (!empty($result['period_limit']) && $disclaimerPeriod > $result['period_limit']) {
			$disclaimerPeriod = $result['period_limit'];
		}

		return  $result['price'] * $disclaimerPeriod;
	}
}

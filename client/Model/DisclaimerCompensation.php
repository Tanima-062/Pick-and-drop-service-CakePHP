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
		'period_flg' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'period_limit' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => true,
				'required' => false,
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

	public function clientCheck($id, $clientId) {
		$options = array(
			'conditions' => array(
				'DisclaimerCompensation.id'=>$id,
				'DisclaimerCompensation.client_id' => $clientId
			),
			'recursive' => -1
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['joins'] = array(
				array(
					'type' => 'INNER',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions' => array(
						'CarClass.id = DisclaimerCompensation.car_class_id',
						'CarClass.delete_flg' => 0,
						'OR' => array(
							array('CarClass.scope' => 0),
							array('CarClass.scope' => $clientData['id'])
						)
					)
				)
			);
		}
		$count = $this->find('count', $options);
		if(empty($count)) {
			return false;
		}
		return true;
	}

	// 日付重複チェック
	public function dateDuplicateCheck($data) {
		$options = array(
				'conditions' => array(
						'DisclaimerCompensation.car_class_id' => $data['DisclaimerCompensation']['car_class_id'],
						'DisclaimerCompensation.delete_flg' => 0,
						'OR' => array(
								'OR' => array(
										array(
												'DisclaimerCompensation.start_date <=' => $data['DisclaimerCompensation']['start_date'],
												'DisclaimerCompensation.end_date >=' => $data['DisclaimerCompensation']['start_date']
										),
										array(
												'DisclaimerCompensation.start_date <=' => $data['DisclaimerCompensation']['end_date'],
												'DisclaimerCompensation.end_date >=' => $data['DisclaimerCompensation']['end_date']
										)
								),
								array(
										array(
												'DisclaimerCompensation.start_date >=' => $data['DisclaimerCompensation']['start_date'],
												'DisclaimerCompensation.start_date <=' => $data['DisclaimerCompensation']['end_date']
										),
										array(
												'DisclaimerCompensation.end_date >=' => $data['DisclaimerCompensation']['start_date'],
												'DisclaimerCompensation.end_date <=' => $data['DisclaimerCompensation']['end_date']
										)
								)
						),
				),
				'recursive' => -1
		);
		if (!empty($data['DisclaimerCompensation']['id'])) {
			$options['conditions']['DisclaimerCompensation.id  !='] += $data['DisclaimerCompensation']['id'];
		}
		$result = $this->find('all', $options);
		return $result;
	}

	public function getAllByCarClassId($carClassId) {
		$options = array(
			'fields' => array(
				'id',
				'start_date',
				'end_date',
				'price',
				'period_flg',
			),
			'conditions' => array(
				'car_class_id' => $carClassId,
				'delete_flg' => 0
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	// 免責補償取得
	public function getDisclaimerCompensation($data) {
		$options = array(
			'conditions' => array(
				'DisclaimerCompensation.car_class_id' => $data['car_class_id'],
				'DisclaimerCompensation.delete_flg' => 0,
				'OR' => array(
					'OR' => array(
						array(
							'DisclaimerCompensation.start_date <=' => $data['from'],
							'DisclaimerCompensation.end_date >=' => $data['from']
						),
						array(
							'DisclaimerCompensation.start_date <=' => $data['to'],
							'DisclaimerCompensation.end_date >=' => $data['to']
						)
					),
					array(
						array(
							'DisclaimerCompensation.start_date >=' => $data['from'],
							'DisclaimerCompensation.start_date <=' => $data['to']
						),
						array(
							'DisclaimerCompensation.end_date >=' => $data['from'],
							'DisclaimerCompensation.end_date <=' => $data['to']
						)
					)
				),
			),
			'recursive' => -1
		);
		$result = $this->find('first', $options);
		return $result['DisclaimerCompensation'];
	}
}

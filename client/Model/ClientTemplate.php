<?php
App::uses('AppModel', 'Model');
/**
 * ClientTemplate Model
 *
 * @property Client $Client
 * @property CreditCard $CreditCard
 * @property Staff $Staff
 */
class ClientTemplate extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'テンプレート名は必須です',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'template' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '内容は必須です',
				'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
		'CreditCard' => array(
			'className' => 'CreditCard',
			'foreignKey' => 'credit_card_id',
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

	public function getClientTemplateContentList($clientId, $loginStaffId = null) {

		$options = array(
				'fields' => array(
						'id',
						'template'
				),
				'conditions' => array(
						'client_id' => $clientId,
						'delete_flg' => 0
				),
				'order' => 'sort ASC',
				'recursive' => -1
		);
		if (!empty($loginStaffId)) {
			$options['conditions']['login_staff_id'] = $loginStaffId;
		}

		return $this->find('list',$options);
	}

	public function getClientTemplateList($clientId, $loginStaffId = null) {

		$options = array(
				'fields' => array(
						'id',
						'name'
				),
				'conditions' => array(
						'client_id' => $clientId,
						'delete_flg' => 0
				),
				'order' => 'sort ASC',
				'recursive' => -1
		);
		if (!empty($loginStaffId)) {
			$options['conditions']['login_staff_id'] = $loginStaffId;
		}

		return $this->find('list',$options);
	}

	public function clientCheck($id,$clientId, $loginStaffId = null) {

		$options = array(
				'conditions' => array(
						'id'=>$id,
						'client_id'=>$clientId,
				),
				'recursive' => -1,
		);
		if (!empty($loginStaffId)) {
			$options['conditions']['login_staff_id'] = $loginStaffId;
		}
		$count = $this->find('count', $options);
		if(empty($count)) {
			return false;
		}

		return true;
	}
}

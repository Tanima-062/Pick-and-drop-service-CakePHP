<?php
App::uses('AppModel', 'Model');
/**
 * PrivilegePrice Model
 *
 * @property Client $Client
 * @property Privilege $Privilege
 * @property Staff $Staff
 */
class PrivilegePrice extends AppModel {

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
		'price' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'span_count' => array(
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
		'created' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'modified' => array(
			'datetime' => array(
				'rule' => array('datetime'),
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
		'Privilege' => array(
			'className' => 'Privilege',
			'foreignKey' => 'privilege_id',
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
	 * オプションIDに対して料金を取得
	 * @param unknown $privilegeId
	 */
	public function getPrivilegePriceData($privilegeId = null) {
		$options = array(
				'conditions' => array(
						'PrivilegePrice.privilege_id' => $privilegeId,
						'PrivilegePrice.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('all', $options);
		return $result;
	}

	/**
	 * オプションIDに対して1日目の料金を1件取得
	 * @param unknown $privilegeId
	 */
	public function getPrivilegePriceFirstDay($privilegeId = null) {
		$options = array(
				'conditions' => array(
						'PrivilegePrice.privilege_id' => $privilegeId,
						'PrivilegePrice.span_count' => 1,
						'PrivilegePrice.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('first', $options);
		return $result;
	}

	/**
	 * オプションIDに対して1日目の料金を複数件取得(リスト)
	 * @param unknown $privilegeId
	 */
	public function getPrivilegePriceFirstDayList($privilegeId = null) {
		$options = array(
				'fields' => array(
						'PrivilegePrice.privilege_id',
						'PrivilegePrice.price',
				),
				'conditions' => array(
						'PrivilegePrice.privilege_id' => $privilegeId,
						'PrivilegePrice.span_count' => 1,
						'PrivilegePrice.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('list', $options);
		return $result;
	}
}

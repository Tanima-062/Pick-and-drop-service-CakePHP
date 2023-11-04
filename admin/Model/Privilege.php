<?php

App::uses('AppModel', 'Model');

/**
 * Privilege Model
 *
 * @property Client $Client
 * @property Staff $Staff
 */
class Privilege extends AppModel {

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
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
// 		'image_relative_url' => array(
// 			'notempty' => array(
// 				'rule' => array('notempty'),
// 				//'message' => 'Your custom message here',
// 				//'allowEmpty' => false,
// 				//'required' => false,
// 				//'last' => false, // Stop validation after this rule
// 				//'on' => 'create', // Limit validation to 'create' or 'update' operations
// 			),
// 		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'excluding_tax' => array(
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
		'maximum' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'unit_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getPrivilege($clientId) {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'client_id' => $clientId
			),
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function privilegeIdKeyList() {

		$options = array(
			'recursive' => -1
		);

		$privileges = $this->find('all', $options);
		$result = array();
		foreach ($privileges as $privilege) {
			$result[$privilege['Privilege']['id']] = array();
			$result[$privilege['Privilege']['id']] = $privilege['Privilege'];
		}

		return $result;
	}

	public function getPrivilegeList($clientId) {
		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.name',
				'Privilege.delete_flg'
			),
			'conditions' => array(
				'Privilege.client_id' => $clientId,
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);
		return Hash::combine($result, '{n}.Privilege.id', '{n}.Privilege');
	}

	public function getPrivilegeApiPostData($reservationId, $optionFlg) {
		if ($optionFlg == 0) {
			$table = 'reservation_privileges';
			$alias = 'ReservationPrivilege';
			$id = 'privilege_id';
		} else if ($optionFlg == 1) {
			$table = 'reservation_child_sheets';
			$alias = 'ReservationChildSheet';
			$id = 'child_sheet_id';
		} else {
			return array();
		}

		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.name',
				"{$alias}.count",
				"{$alias}.price",
				"'{$alias}' AS alias",
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => "{$table}",
					'alias' => "{$alias}",
					'conditions' => "{$alias}.{$id} = Privilege.id",
				),
			),
			'conditions' => array(
				"{$alias}.reservation_id" => $reservationId,
			),
			'order' => "{$alias}.id",
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	public function getClientPrivilegeList($clientId = null)
	{
		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.name',
			),
			'conditions' => array(
				'Privilege.client_id' => $clientId,
			),
			'recursive' => -1,
		);
		$result = $this->find('list', $options);
		return $result;
	}
}

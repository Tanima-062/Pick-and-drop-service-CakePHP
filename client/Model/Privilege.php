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
				'message' => 'シート名は必須です',
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
				'message' => '整数値を入力してください',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'maximum' => array(
			'numeric' => array(
				'rule' => array('naturalNumber'),
				'message' => '1以上の整数値を入力してください',
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

		$privileges = $this->find('all',$options);
		$result = array();
		foreach ($privileges as $privilege) {
			$result[$privilege['Privilege']['id']] = array();
			$result[$privilege['Privilege']['id']] = $privilege['Privilege'];
		}

		return $result;

	}


	/**
	 * 編集ID、クライアントID、スタッフIDで不正アクセスを判定
	 * @param unknown $id
	 * @param unknown $clientId
	 */
	public function clientCheck($id, $clientId) {
		$options = array(
				'conditions' => array(
						'Privilege.id' => $id,
						'Privilege.client_id' => $clientId,
				),
				'recursive' => -1,
		);

		$clientData = $this->_getCurrentUser();

		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('Privilege.scope' => 0),
				array('Privilege.scope' => $clientData['id'])
			);
		}

		$result = $this->find('first', $options);

		if (!empty($result)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * クライアントに対応したオプション一覧をリストで取得
	 * @param $optionFlg
	 * @param unknown $clientId
	 */
	public function getPrivilegeDataList($clientId = null, $optionFlg = array(0,1)) {
		$options = array(
				'fields' => array(
						'Privilege.id',
						'Privilege.name'
				),
				'conditions' => array(
						'Privilege.client_id' => $clientId,
						'Privilege.option_flg' => $optionFlg,
						'Privilege.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('Privilege.scope' => 0),
				array('Privilege.scope' => $clientData['id'])
			);
		}
		$result = $this->find('list', $options);
		return $result;
	}

	/**
	 * オプションIDに対応するデータ取得
	 * @param unknown $privilegeId
	 * @param array $optionFlg
	 */
	public function getPrivilegeData($privilegeId = null, $optionFlg = array(0,1)) {
		$options = array(
				'conditions' => array(
						'Privilege.id' => $privilegeId,
						'Privilege.option_flg' => $optionFlg,
						'Privilege.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('all', $options);
		return $result;
	}

	/**
	 * オプションIDに対応するデータを1件取得
	 * @param unknown $privilegeId
	 */
	public function getPrivilegeFirstData($privilegeId) {
		$options = array(
				'conditions' => array(
						'Privilege.id' => $privilegeId,
						'Privilege.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('first', $options);
		return $result;
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

	/**
	 * オプションIDに対応する料金、最大個数取得
	 * @param unknown $privilegeId
	 * @param array $optionFlg
	 */
	public function getPrivilegeMaxPriceData($privilegeIds) {

		$options = array(
			'fields' => array(
				'Privilege.maximum',
				'PrivilegePrice.price',
			),
			'joins' => array(
				array(
					'table' => 'privilege_prices',
					'alias' => 'PrivilegePrice',
					'type' => 'LEFT',
					'conditions' => array(
						'PrivilegePrice.privilege_id = Privilege.id'
					),
				),
			),
			'conditions' => array(
				'Privilege.id' => $privilegeIds,
				'Privilege.option_flg' => 0,
				'Privilege.delete_flg' => 0,
				'PrivilegePrice.span_count' => 1,
				'PrivilegePrice.delete_flg' => 0,
			),
			'order' => array(
				'Privilege.id ASC',
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);

		return $result;

	}
}

<?php

App::uses('AppModel', 'Model');

class Client extends AppModel {

	public $hasMany = array(
		'SettlementCompany' => array(
			'className' => 'SettlementCompany',
		)
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notblank'),
				'message' => 'クライアント名は必須です',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_admin' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'url' => array(
			'notempty' => array(
				'rule' => array('notblank'),
				'message' => 'リンク用URLは必須です',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isunique' => array(
				'rule' => array('isunique'),
				'message' => '登録済みのリンク用URLです',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'need_remark' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'accept_cash' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'accept_card' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'reserve_tag' => array(
			'notempty' => array(
				'rule' => array('notblank'),
				'message' => '予約タグは必須です',
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'この予約タグは既に登録されています'
			),
			'between' => array(
				'rule' => array('between', 2, 2),
				'message' => '2文字入力して下さい',
			),
			'string' => array(
				'rule' => array('custom', '/^[A-Z]+$/'),
				'message' => '大文字英字のみ登録可能です',
			),
		),
		'commission_rate' => array(
			'notempty' => array(
				'rule' => array('notblank'),
				'message' => '手数料率は必須です',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'required_drop_off_price_pattern' => array(
			'notempty' => array(
				'rule' => array('range', 0, 4),
				'message' => '乗捨料金パターン数は1～3の間で入力してください',
			),
		),
		'is_managed_package' => [
			'boolean' => [
				'rule'     => 'boolean',
				'message'  => '包括販売商品のON/OFFを正しく指定してください',
				'required' => true
			],
		],
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public function getClientData() {
		return $this->find('all');
	}

	public function getClientByConclusionContractCriteria() {

		$clients = $this->find('all');

		$clientArray = array();
		foreach ($clients as $client) {
			if ($client['Client']['conclusion_contract_criteria'] == 0) {
				$clientArray['rent'][] = $client['Client']['id'];
			} else if ($client['Client']['conclusion_contract_criteria'] == 1) {
				$clientArray['return'][] = $client['Client']['id'];
			}
		}

		return $clientArray;
	}

	public function getClientListByIata($iataCd) {
		return $this->find('list', array(
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => array(
						'Office.client_id = Client.id',
						'Office.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'table' => 'landmarks',
					'alias' => 'Landmark',
					'conditions' => array(
						'Landmark.id = Office.airport_id',
						'Landmark.iata_cd' => $iataCd,
						'Landmark.delete_flg' => 0
					)
				)
			),
			'conditions' => array(
				'Client.delete_flg' => 0
			)
		));
	}
}

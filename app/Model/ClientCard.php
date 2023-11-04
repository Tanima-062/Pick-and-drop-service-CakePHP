<?php
App::uses('AppModel', 'Model');
/**
 * ClientCard Model
 *
 * @property Client $Client
 * @property CreditCard $CreditCard
 * @property Staff $Staff
 */
class ClientCard extends AppModel {

	protected $cacheConfig = '1day';
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
		'credit_card_id' => array(
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

	public function getCardByClientId($clientIds) {

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$options = array(
				'fields' => array(
						'ClientCard.client_id',
						'CreditCard.name',
						'CreditCard.image_relative_url'
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'alias' => 'CreditCard',
						'table' => 'credit_cards',
						'conditions' => array(
							'CreditCard.id = ClientCard.credit_card_id'
						)
					),
				),
				'conditions' => array(
						'ClientCard.delete_flg' => 0,
						'ClientCard.client_id' => $clientIds
				),
				'recursive' => -1
		);

		$cards = $this->find('all', $options);

		if (!empty($cards)) {

			$card = array();
			$credit = array('name' => array(), 'url' => array());
			foreach ($cards as $key => $val) {
				if (!empty($tmpId) && $tmpId != $val['ClientCard']['client_id']) {
					array_push($card[$tmpId], $credit);
					$credit = array();
				}
				$tmpId = $val['ClientCard']['client_id'];
				array_push($credit['name'], $val['CreditCard']['name']);
				array_push($credit['url'], $val['CreditCard']['image_relative_url']);
			}
			if (empty($card)) {
				$card[$tmpId] = $credit;
			}

			// 返り値をキャッシュに設定 ※取り扱い注意
			$this->writeCache($cache_name, $card);

			return $card;
		}

		return false;
	}
}

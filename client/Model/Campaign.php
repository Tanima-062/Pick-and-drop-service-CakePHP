<?php
App::uses('AppModel', 'Model');
/**
 * Campaign Model
 *
 * @property Client $Client
 * @property CarClass $CarClass
 * @property Staff $Staff
 */
class Campaign extends AppModel {

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
		'day_time_flg' => array(
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CampaignTerm' => array(
			'className' => 'CampaignTerm',
			'foreignKey' => 'campaign_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function getCampaignAndTermsByCampaignId($campaignId) {
		$options = array(
			'fields' => array(
				'Campaign.id',
				'Campaign.name',
				'Campaign.scope',
				'CampaignTerm.id',
				'CampaignTerm.start_date',
				'CampaignTerm.end_date',
				'CampaignTerm.mon',
				'CampaignTerm.tue',
				'CampaignTerm.wed',
				'CampaignTerm.thu',
				'CampaignTerm.fri',
				'CampaignTerm.sat',
				'CampaignTerm.sun',
				'CampaignTerm.hol',
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'campaign_terms',
					'alias' => 'CampaignTerm',
					'conditions' => array(
						'Campaign.id = CampaignTerm.campaign_id',
						'CampaignTerm.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'Campaign.id' => $campaignId,
				'Campaign.delete_flg' => 0,
			),
			'order' => array(
				'Campaign.id',
				'CampaignTerm.start_date',
				'CampaignTerm.end_date',
			),
			'recursive' => -1,
		);

		$ret = $this->find('all', $options);

		if (empty($ret)) {
			return $ret;
		}

		return array('Campaign' => $ret[0]['Campaign'], 'CampaignTerm' => Hash::extract($ret, '{n}.CampaignTerm'));
	}

	public function getAllCampaignAndTerms($clientId, $name = '', $date = '', $week = '') {
		$options = array(
			'fields' => array(
				'Campaign.id',
				'Campaign.name',
				'Campaign.scope',
				'CampaignTerm.id',
				'CampaignTerm.start_date',
				'CampaignTerm.end_date',
				'CampaignTerm.mon',
				'CampaignTerm.tue',
				'CampaignTerm.wed',
				'CampaignTerm.thu',
				'CampaignTerm.fri',
				'CampaignTerm.sat',
				'CampaignTerm.sun',
				'CampaignTerm.hol',
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'campaign_terms',
					'alias' => 'CampaignTerm',
					'conditions' => array(
						'Campaign.id = CampaignTerm.campaign_id',
						'CampaignTerm.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'Campaign.client_id' => $clientId,
				'Campaign.delete_flg' => 0,
			),
			'order' => array(
				'Campaign.id',
				'CampaignTerm.start_date',
				'CampaignTerm.end_date',
			),
			'recursive' => -1,
		);

		if (!empty($name)) {
			$options['conditions']['Campaign.name LIKE'] = $name . '%';
		}

		$weekEn = Constant::weekEn();
		if (!empty($week)) {
			foreach ($week as $val) {
				if (!empty($weekEn[$val])) {
					$options['conditions']['CampaignTerm.'.$weekEn[$val]] = 1;
				}
			}
		}

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('Campaign.scope' => 0),
				array('Campaign.scope' => $clientData['id'])
			);
		}

		if (!empty($date)) {
			$date = strtotime($date);
		}

		$ret = $this->find('all', $options);
		
		if (empty($ret)) {
			return array();
		}
		
		$campaigns = Hash::combine($ret, '{n}.Campaign.id', '{n}.Campaign');
		$campaignTerms = Hash::combine($ret, '{n}.CampaignTerm.id', '{n}.CampaignTerm', '{n}.Campaign.id');
		
		// 結果を組み立てる
		$ret = array();
		foreach ($campaignTerms as $campaign_id => $terms) {
			$add = false;
			if (empty($date)) {
				$add = true;
			} else {
				foreach ($terms as $term) {
					if ((strtotime($term['start_date']) <= $date && $date <= strtotime($term['end_date']))) {
						// 指定した期間が存在する場合はキャンペーンを追加する
						$add = true;
						break;
					}
				}
			}

			if ($add) {
				$ret[$campaign_id] = $campaigns[$campaign_id];
				$ret[$campaign_id]['terms'] = $terms;
			}
		}

		return $ret;
	}

	public function clientCheck($id, $clientId) {
		$options = array(
			'conditions' => array(
				'id' => $id,
				'client_id' => $clientId
			),
			'recursive' => -1,
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('scope' => 0),
				array('scope' => $clientData['id'])
			);
		}

		$count = $this->find('count', $options);
		if(empty($count)) {
			return false;
		}
		return true;
	}

	public function getCampaignList($clientId) {
		$options = array(
			'conditions' => array(
				'Campaign.client_id' => $clientId,
				'Campaign.delete_flg' => 0,
			),
			'order' => array(
				'Campaign.id',
			),
			'recursive' => -1,
		);

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('Campaign.scope' => 0),
				array('Campaign.scope' => $clientData['id'])
			);
		}

		return $this->find('list', $options);
	}

	public function getCommodityCampaignPrice($commodityItemId) {
		$options = array(
			'fields' => array(
				'CommodityCampaignPrice.id',
				'CommodityCampaignPrice.campaign_id',
				'CommodityCampaignPrice.span_count',
				'CommodityCampaignPrice.price',
				'CommodityCampaignPrice.delete_flg',
			),
			'conditions' => array(
				'Campaign.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_campaign_prices',
					'alias' => 'CommodityCampaignPrice',
					'conditions' => array(
						'CommodityCampaignPrice.campaign_id = Campaign.id',
						'CommodityCampaignPrice.commodity_item_id' => $commodityItemId,
					),
				),
			),
			'order' => array(
				'CommodityCampaignPrice.id',
			),
			'recursive' => -1,
		);

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('Campaign.scope' => 0),
				array('Campaign.scope' => $clientData['id'])
			);
		}

		$data = $this->find('all', $options);
		if (empty($data)) {
			return array();
		}

		$data = Hash::combine($data, '{n}.CommodityCampaignPrice.id', '{n}.CommodityCampaignPrice', '{n}.CommodityCampaignPrice.campaign_id');

		return $data;
	}

	public function getCommodityCampaignIdOutOfScope($commodityItemId) {
		$clientData = $this->_getCurrentUser();
		if ($clientData['is_client_admin']) {
			// 管理者に見えないキャンペーンはない
			return array();
		}

		$options = array(
			'fields' => array(
				'CommodityCampaignPrice.campaign_id',
			),
			'conditions' => array(
				'Campaign.delete_flg' => 0,
				'Campaign.scope <> 0',
				'Campaign.scope <>' => $clientData['id'],
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_campaign_prices',
					'alias' => 'CommodityCampaignPrice',
					'conditions' => array(
						'CommodityCampaignPrice.campaign_id = Campaign.id',
						'CommodityCampaignPrice.commodity_item_id' => $commodityItemId,
					),
				),
			),
			'group' => array(
				'CommodityCampaignPrice.campaign_id',
			),
			'recursive' => -1,
		);

		$data = $this->find('all', $options);
		if (empty($data)) {
			return array();
		}

		return Hash::combine($data, '{n}.CommodityCampaignPrice.campaign_id', '{n}.CommodityCampaignPrice.campaign_id');
	}

	// 日付重複チェック
	public function dateDuplicateCheck($data) {
		$options = array(
			'conditions' => array(
				'Campaign.car_class_id' => $data['Campaign']['car_class_id'],
				'Campaign.delete_flg' => 0,
				'OR' => array(
					'OR' => array(
						array(
							'Campaign.start_date <=' => $data['Campaign']['start_date'],
							'Campaign.end_date >=' => $data['Campaign']['start_date']
						),
						array(
							'Campaign.start_date <=' => $data['Campaign']['end_date'],
							'Campaign.end_date >=' => $data['Campaign']['end_date']
						)
					),
					array(
						array(
							'Campaign.start_date >=' => $data['Campaign']['start_date'],
							'Campaign.start_date <=' => $data['Campaign']['end_date']
						),
						array(
							'Campaign.end_date >=' => $data['Campaign']['start_date'],
							'Campaign.end_date <=' => $data['Campaign']['end_date']
						)
					)
				),
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'table'=>'campaign_stock_groups',
					'alias'=> 'CampaignStockGroup',
					'conditions'=> 'CampaignStockGroup.campaign_id = Campaign.id'
				),
			),
			'fields' => array(
				'CampaignStockGroup.stock_group_id'
			),
			'groups' => array(
				'CampaignStockGroup.stock_group_id'
			),
			'recursive' => -1
		);
		if (!empty($data['Campaign']['id'])) {
			$options['conditions']['Campaign.id  !='] += $data['Campaign']['id'];
		}
		$campaignStockGroups = $this->find('all', $options);

		$stockGroupIds = Hash::extract($campaignStockGroups, '{n}.CampaignStockGroup.stock_group_id');

		$duplicated = array_intersect($stockGroupIds, $data['CampaignStockGroup']['stock_group_id']);

		return $duplicated;
	}
}

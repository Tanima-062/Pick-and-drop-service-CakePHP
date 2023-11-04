<?php
App::uses('AppModel', 'Model');
/**
 * CommodityCampaignPrice Model
 *
 * @property Client $Client
 * @property CommodityItem $CommodityItem
 * @property Staff $Staff
 */
class CommodityCampaignPrice extends AppModel {

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
		'campaign_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
		'commodity_item_id' => array(
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
/*		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CommodityItem' => array(
			'className' => 'CommodityItem',
			'foreignKey' => 'commodity_item_id',
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
		)*/
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
/*		'Contract' => array(
			'className' => 'Contract',
			'foreignKey' => 'commodity_price_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)*/
	);


	public function getUpdateDate($commodityItemId) {
		$ret = $this->find('first', array(
			'fields' => array(
				'MAX(CommodityCampaignPrice.modified) AS modified',
			),
			'conditions' => array(
				'CommodityCampaignPrice.commodity_item_id' => $commodityItemId,
			),
			'recursive' => -1,
		));
		if (empty($ret)) {
			return '';
		} else {
			return $ret[0]['modified'];
		}
	}

    /**
     * キャンペーンIDとクライアントIDから、紐付いている商品IDを取得する
     *
     * @param string $campaignId
     * @param string $clientId
     * @return string $commodityItemId
     */
    public function getCommodityItemId($campaignId, $clientId) {

        $options = array(
            'fields' => array(
                'DISTINCT commodity_item_id',
            ),
            'conditions' => array(
                'delete_flg' => 0,
                'campaign_id' => $campaignId,
                'client_id' => $clientId
            ),
            'recursive' => -1
        );

        return $this->find('all', $options);
    }
}

<?php
App::uses('AppModel', 'Model');
/**
 * CommodityPrice Model
 *
 * @property Client $Client
 * @property PriceRank $PriceRank
 * @property PriceSpan $PriceSpan
 * @property CommodityItem $CommodityItem
 * @property Staff $Staff
 * @property Contract $Contract
 */
class CommodityPrice extends AppModel {

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
		'price_rank_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'price_span_id' => array(
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
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PriceRank' => array(
			'className' => 'PriceRank',
			'foreignKey' => 'price_rank_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PriceSpan' => array(
			'className' => 'PriceSpan',
			'foreignKey' => 'price_span_id',
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
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Contract' => array(
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
		)
	);

	/**
	 *
	 * @param unknown_type $commodityItemId
	 */
	public function checkHavePrice($commodityItemId) {

		$options = array(
				'conditions' => array(
						'commodity_item_id' => $commodityItemId
				),
				'recursive' => -1
		);

		$commodityPrice = $this->find('first',$options);

		if (!empty($commodityPrice)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param unknown $clientId
	 * @param unknown $spanCount
	 * @param unknown $commodityItemId
	 */
	public function getSpanCountPrice($clientId, $spanCount, $commodityItemId) {
		$options = array(
				'fields' => array(
						'CommodityPrice.id',
						'CommodityPrice.client_id',
						'CommodityPrice.span_count',
						'CommodityPrice.price',
						'CommodityPrice.commodity_item_id',
				),
				'conditions' => array(
						'CommodityPrice.client_id' => $clientId,
						'CommodityPrice.span_count' => $spanCount,
						'CommodityPrice.commodity_item_id' => $commodityItemId,
						'CommodityPrice.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('first', $options);
		return $result;
	}
}

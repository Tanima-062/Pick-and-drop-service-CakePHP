<?php
App::uses('AppModel', 'Model');
/**
 * CommodityPrivilege Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property Privilege $Privilege
 * @property Staff $Staff
 */
class CommodityPrivilege extends AppModel {

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
		'commodity_id' => array(
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
		'Commodity' => array(
			'className' => 'Commodity',
			'foreignKey' => 'commodity_id',
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

	public function getCommodityPrivilege($commodityItemId) {

		$options = array(
				'fields' => array(
						'Privilege.*',
						'CommodityPrivilege.*'
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'CommodityItem',
								'table' => 'commodity_items',
								'conditions' => array(
										'CommodityItem.id = '.$commodityItemId,
										'CommodityPrivilege.commodity_id = CommodityItem.commodity_id'
								)
						),
						array(
								'type' => 'INNER',
								'alias' => 'Privilege',
								'table' => 'privileges',
								'conditions' => array(
										'CommodityPrivilege.privilege_id = Privilege.id'
								)
						)),
				'conditions' => array(
						'CommodityPrivilege.delete_flg' => 0
				),
				'recursive' => -1
		);

		return $this->find('all', $options);
	}

	/*
	商品ごとの初日のマイナスオプション料金取得
	*/
	public function getCommodityPrivilegeData($commodityId, $privilegeId = null) {

		$options = array(
			'fields' => array(
				'CommodityPrivilege.commodity_id',
				'CommodityPrivilege.privilege_id',
				'Privilege.maximum',
				'PrivilegePrice.price',
			),
			'joins' => array(
				array(
					'table' => 'privileges',
					'alias' => 'Privilege',
					'type' => 'INNER',
					'conditions' => array(
						'Privilege.id = CommodityPrivilege.privilege_id'
					),
				),
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
				'CommodityPrivilege.commodity_id' => $commodityId,
				'CommodityPrivilege.delete_flg' => 0,
				'Privilege.option_flg' => 0,
				'Privilege.delete_flg' => 0,
				'PrivilegePrice.price < ' => 0,
				'PrivilegePrice.delete_flg' => 0,
				'OR' => array(
					array(
						'Privilege.shape_flg' => 0,
						'PrivilegePrice.span_count' => 1,
					),
					array(
						'Privilege.shape_flg' => 1,
						'Privilege.period_flg' => 0,
						'PrivilegePrice.span_count' => 1,
					),
					array(
						'Privilege.shape_flg' => 1,
						'Privilege.period_flg' => 1,
						'PrivilegePrice.span_count' => 1,
					),
				),
			),
			'group' => array(
				'CommodityPrivilege.commodity_id',
				'Privilege.id'
			),
			'order' => array(
				'Privilege.option_flg DESC',
				'Privilege.id ASC',
			),
			'recursive' => -1,
		);
		if(!empty($privilegeId)){
			$options['conditions']['NOT']['CommodityPrivilege.privilege_id'] = $privilegeId;
		}
		$result = $this->find('all', $options);

		return $result;
	}
}

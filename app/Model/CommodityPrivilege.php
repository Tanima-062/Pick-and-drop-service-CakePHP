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

	public function getCommodityPrivilegeData($commodityId, $spanCount, $spanCount24) {

		// レンタル期間が31日以降
		$diffCount = $diffCount24 = 0;
		if ($spanCount > 31) {
			$diffCount = $spanCount - 31;
			$spanCount = array(31, 0);
		}
		if ($spanCount24 > 31) {
			$diffCount24 = $spanCount24 - 31;
			$spanCount24 = array(31, 0);
		}
		
		$diffCount = filter_var($diffCount, FILTER_VALIDATE_FLOAT, array('options' => array('default' => 0)));
		$diffCount24 = filter_var($diffCount24, FILTER_VALIDATE_FLOAT, array('options' => array('default' => 0)));

		$options = array(
			'fields' => array(
				'CommodityPrivilege.id',
				'CommodityPrivilege.client_id',
				'CommodityPrivilege.commodity_id',
				'CommodityPrivilege.privilege_id',
				'Privilege.id',
				'Privilege.option_category_id',
				'Privilege.name',
				'Privilege.maximum',
				'Privilege.unit_name',
				'Privilege.option_flg',
				'Privilege.shape_flg',
				'Privilege.period_flg',
// 						'PrivilegePrice.id',
// 						'PrivilegePrice.price',
// 						'PrivilegePrice.span_count',
// 						'PrivilegePrice.privilege_id',
				'SUM(CASE' .
					' WHEN PrivilegePrice.span_count = 0 AND Privilege.period_flg = 0 THEN PrivilegePrice.price * ' . $diffCount .
					' WHEN PrivilegePrice.span_count = 0 AND Privilege.period_flg = 1 THEN PrivilegePrice.price * ' . $diffCount24 .
					' ELSE PrivilegePrice.price END) AS Sum',
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
				'Privilege.delete_flg' => 0,
				'PrivilegePrice.delete_flg' => 0,
				'OR' => array(
					array(
						'Privilege.shape_flg' => 0,
						'PrivilegePrice.span_count' => 1,
					),
					array(
						'Privilege.shape_flg' => 1,
						'Privilege.period_flg' => 0,
						'PrivilegePrice.span_count' => $spanCount,
					),
					array(
						'Privilege.shape_flg' => 1,
						'Privilege.period_flg' => 1,
						'PrivilegePrice.span_count' => $spanCount24,
					),
				),
			),
			'group' => array(
				'Privilege.id'
			),
			'order' => array(
				'Privilege.option_flg DESC',
				'Privilege.id ASC',
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);

		return $result;
	}

	/**
	 * オプションデータ
	 * @param array $optionParams
	 * @return array
	 */
	public function getPrivilegeData($optionParams) {
		// オプション計算
		$commodityPrivilegeData = $this->getCommodityPrivilegeData($optionParams['commodityId'], $optionParams['period'], $optionParams['period24']);
		$commodityPrivilege = array();
		$optionArray = $optionParams['sheet'] + $optionParams['privilege'];
		foreach ($commodityPrivilegeData as $value) {
			if (!empty($optionArray[$value['Privilege']['id']])) {
				$calculation = $value[0]['Sum'] * $optionArray[$value['Privilege']['id']];
				$commodityPrivilege[$value['Privilege']['id']]['id'] = $value['Privilege']['id'];
				$commodityPrivilege[$value['Privilege']['id']]['name'] = $value['Privilege']['name'];
				$commodityPrivilege[$value['Privilege']['id']]['count'] = $optionArray[$value['Privilege']['id']];
				$commodityPrivilege[$value['Privilege']['id']]['amount'] = $calculation;
				$commodityPrivilege[$value['Privilege']['id']]['option_flg'] = $value['Privilege']['option_flg'];
			}
		}
		return $commodityPrivilege;
	}

	public function getOptionCategoryIdList($commodityId) {
		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.option_category_id',
				'CommodityPrivilege.commodity_id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Privilege',
					'table' => 'privileges',
					'conditions' => array('Privilege.id = CommodityPrivilege.privilege_id')
				),
			),
			'conditions' => array(
				'CommodityPrivilege.commodity_id' => $commodityId,
				'CommodityPrivilege.delete_flg' => 0,
				'Privilege.option_category_id !=' => 999,
				'Privilege.delete_flg' => 0,
			),
			'order' => array(
				'Privilege.option_flg',
				'Privilege.id',
			),
			'recursive' => -1,
		);
		
		return $this->findC('list', $options);
	}

	public function getPrivileges($commodityId) {
		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.option_category_id',
				'Privilege.name',
				'Privilege.option_flg',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'privileges',
					'alias' => 'Privilege',
					'conditions' => array(
						'Privilege.id = CommodityPrivilege.privilege_id',
					),
				)
			),
			'conditions' => array(
				'CommodityPrivilege.commodity_id' => $commodityId,
				'CommodityPrivilege.delete_flg' => 0,
				'Privilege.delete_flg' => 0,
			),
			// orderは仮
			'order' => array(
				'Privilege.option_flg' => 'desc',
				'Privilege.id',
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	/**
	 * @param array      $commodityIds 商品ID一覧
	 * @param array|null $fields       取得フィールド一覧
	 * @return array|null
	 */
	public function getPrivilegeListByCommodityId($commodityIds, $fields = NULL) {

		if (is_null($fields)) {
			$fields = array(
				'CommodityPrivilege.*',
				'Privilege.*',
			);
		}

		$options = array(
			'fields' => $fields,
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'privileges',
					'alias' => 'Privilege',
					'conditions' => array(
						'CommodityPrivilege.privilege_id = Privilege.id',
						'Privilege.delete_flg' => 0,
					),
				)
			),
			'conditions' => array(
				'CommodityPrivilege.commodity_id' => $commodityIds,
				'CommodityPrivilege.delete_flg' => 0,
			),
			'order' => array(
				'Privilege.option_flg' => 'desc',
				'Privilege.id',
			),
			'recursive' => -1,
		);

		return  $this->find('all', $options);
	}

}

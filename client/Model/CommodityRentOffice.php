<?php
App::uses('AppModel', 'Model');
/**
 * CommodityRentOffice Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property Office $Office
 * @property Staff $Staff
 */
class CommodityRentOffice extends AppModel {

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
		'office_id' => array(
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
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
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

	public function getStockGroupByCommodityId($commodityId) {

		$subquery = "(
				SELECT
					stock_groups.id,
					stock_groups.name,
					office_stock_groups.office_id
				FROM
					stock_groups,
					office_stock_groups
				WHERE
					office_stock_groups.stock_group_id = stock_groups.id
					AND office_stock_groups.delete_flg = 0)";

		$options = array(
				'fields' => array(
						'CommodityRentOffice.*',
						'OfficeStockGroup.*'
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'OfficeStockGroup',
								'table' => "{$subquery}",
								'conditions' => array(
										'OfficeStockGroup.office_id = CommodityRentOffice.office_id'
								)
						)),
				'conditions' => array(
						'CommodityRentOffice.commodity_id' => $commodityId,
						'CommodityRentOffice.delete_flg' => 0
				),
				'group' => 'OfficeStockGroup.id',
				'recursive' => -1
		);

		return $this->find('all', $options);

	}

	public function getRentOffice($commondityId, $clientId) {

		$officeListArray = $this->find('all',
				array(
						'conditions'=>
						array(
								'commodity_id'=>$commondityId,
								'CommodityRentOffice.client_id' => $clientId,
								'Office.id <>'=>0
						),
						'fields'=>array(
								'CommodityRentOffice.office_id',
								'Office.name',
								'Office.office_code'
						),
						'callbacks' => 'before'
				)
		);

		foreach($officeListArray as $key => $val) {
			$id = $val['CommodityRentOffice']['office_id'];
			$officeList[$id] = $val;
		}

		return $officeList;

	}
}

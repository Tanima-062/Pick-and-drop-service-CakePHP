<?php
App::uses('AppModel', 'Model');
/**
 * OfficeStockGroup Model
 *
 * @property Client $Client
 * @property StockGroup $StockGroup
 * @property Office $Office
 * @property Staff $Staff
 */
class OfficeStockGroup extends AppModel {

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
		'stock_group_id' => array(
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
		'StockGroup' => array(
			'className' => 'StockGroup',
			'foreignKey' => 'stock_group_id',
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


	public function getOfficeByStockGroupId($stockGroupId) {

		$options = array(
				'fields' => array(
						'Office.name'
				),
				'conditions' => array(
						'OfficeStockGroup.stock_group_id' => $stockGroupId,
						'OfficeStockGroup.delete_flg' => 0
				)
		);

		$offices = $this->find('all', $options);

		$result = array();
		foreach ($offices as $val) {
			array_push($result, $val['Office']['name']);
		}

		return $result;
	}

	public function getOfficeStockGroupCount($stockGroupId) {

		$options = array(
				'conditions' => array(
						'OfficeStockGroup.delete_flg' => 0,
						'OfficeStockGroup.stock_group_id' => $stockGroupId
				),
				'joins' => array(
						array(
								'type' => 'RIGHT',
								'alias' => 'Office',
								'table' => 'offices',
								'conditions' => 'OfficeStockGroup.office_id = Office.id AND Office.delete_flg = 0',
						)
				),
				'recursive' => -1
		);

		return $this->find('count',$options);
	}

	public function getStockGroups($officeIds = null) {

		$options = array(
				'conditions' => array(
						'OfficeStockGroup.delete_flg' => 0
				),
				'recursive' => -1,
		);
		if (!empty($officeIds)) {
			$options['conditions']['OfficeStockGroup.office_id'] = $officeIds;
		}

		return $this->find('all',$options);

	}
}

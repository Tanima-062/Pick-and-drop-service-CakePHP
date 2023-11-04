<?php
App::uses('AppModel', 'Model');
/**
 * StockGroup Model
 *
 * @property Client $Client
 * @property Staff $Staff
 * @property CarClassReservation $CarClassReservation
 * @property CarClassStock $CarClassStock
 * @property CommodityItemReservation $CommodityItemReservation
 * @property CommodityItemStock $CommodityItemStock
 */
class StockGroup extends AppModel {

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

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CarClassReservation' => array(
			'className' => 'CarClassReservation',
			'foreignKey' => 'stock_group_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CarClassStock' => array(
			'className' => 'CarClassStock',
			'foreignKey' => 'stock_group_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CommodityItemReservation' => array(
			'className' => 'CommodityItemReservation',
			'foreignKey' => 'stock_group_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CommodityItemStock' => array(
			'className' => 'CommodityItemStock',
			'foreignKey' => 'stock_group_id',
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

	public function getStockGroup($id) {

		$options = array(
				'conditions' => array(
						'id' => $id,
						'delete_flg' => 0
				),
				'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getStockGroupByClientId($clientId, $commodityGroupId = false) {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'client_id' => $clientId
			),
			'recursive' => -1
		);

		$Commodity = ClassRegistry::init('Commodity');
		$options['conditions']['id'] = $Commodity->getCommodityGroupStockGroupIds($clientId, $commodityGroupId);

		return $this->find('all', $options);
	}

	public function getStockGroupList($clientId, $prefectureId = '', $staffId = '') {

		$options = array(
				'conditions' => array(
						'delete_flg' => 0,
						'client_id' => $clientId
				),
				'order'=>'sort,id asc',
				'recursive' => -1,
		);

		if(!empty($prefectureId)) {
		  $options['conditions']['prefecture_id'] = $prefectureId;
		}

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_system_admin'] && !empty($staffId)) {
			// 権限営業所取得（ログインユーザー）
			$this->OfficeSelectionPermission = ClassRegistry::init('OfficeSelectionPermission');
			$permissionOfficeList = $this->OfficeSelectionPermission->getPermissionOfficeList($staffId);
			// 営業在庫地域の取得（権限営業所対応）
			$this->OfficeStockGroup = ClassRegistry::init('OfficeStockGroup');
			$officeStockGroupData = $this->OfficeStockGroup->getStockGroups($permissionOfficeList);
			// 在庫地域の取得
			$stockGroupIdList = Hash::extract($officeStockGroupData, '{n}.OfficeStockGroup.stock_group_id');
			$stockGroupIds = array_unique($stockGroupIdList);
			$options['conditions']['id'] = $stockGroupIds;
		}

		return $this->find('list', $options);
	}

	public function getStockGroupListWithUnassociated($clientId, $prefectureId = '', $staffId = '') {
		$stockGroupList = $this->getStockGroupList($clientId, $prefectureId, $staffId);

		$options = array(
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'office_stock_groups',
					'alias' => 'OfficeStockGroup',
					'conditions' => array(
						'StockGroup.id = OfficeStockGroup.stock_group_id',
					)
				)
			),
			'conditions' => array(
				'StockGroup.delete_flg' => 0,
				'StockGroup.client_id' => $clientId,
				'OfficeStockGroup.office_id' => NULL,
			),
			'order'=>'StockGroup.sort,StockGroup.id asc',
			'recursive' => -1,
		);

		if(!empty($prefectureId)) {
			$options['conditions']['StockGroup.prefecture_id'] = $prefectureId;
		}

		$strayStockGroupList = $this->find('list', $options);

		return array_unique($stockGroupList + $strayStockGroupList);
	}

	public function getAllFindStockGroup($stockGroupId, $prefectureId = null) {
		$options = array(
				'conditions' => array(
						'StockGroup.id' => $stockGroupId,
						'StockGroup.delete_flg' => 0,
				),
				'order' => array(
						'StockGroup.sort' => 'asc',
				),
				'recursive' => -1,
		);
		if (!empty($prefectureId)) {
			$options['conditions']['StockGroup.prefecture_id'] = $prefectureId;
		}
		return $this->find('all', $options);
	}

}

<?php

App::uses('AppModel', 'Model');
App::import('Vendor', 'imageResizeUpLoad');

/**
 * Commodity Model
 *
 * @property Client $Client
 */
class Commodity extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		/*
		  'commodity_key' => array(
		  'notempty' => array(
		  'rule' => array('notempty'),
		  'message' => '商品管理番号が空です',
		  //'allowEmpty' => false,
		  //'required' => false,
		  //'last' => false, // Stop validation after this rule
		  //'on' => 'create', // Limit validation to 'create' or 'update' operations
		  ),
		  ),
		 */
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'クライアントIDを数値で選択してください',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'language_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => '使用言語を数値選択してください',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '名前を入力してください',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '説明を入力してください',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*
		  'remark' => array(
		  'notempty' => array(
		  'rule' => array('notempty'),
		  'message' => '備考を入力してください',
		  //'allowEmpty' => false,
		  //'equired' => false,
		  //'last' => false, // Stop validation after this rule
		  //'on' => 'create', // Limit validation to 'create' or 'update' operations
		  ),
		  ),
		 */
		'is_published' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'sales_type' => array(
			'enum' => array(
				'rule' => array('custom', '/^(ARRANGED|AGENT-ORGANIZED)$/'),
				'message' => '販売方法の値が不正です。',
			)
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
		'Language' => array(
			'className' => 'Language',
			'foreignKey' => 'language_id',
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
		),
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 *
	 */
	/*
	  public $hasMany = array(

	  );
	 */
	public $hasMany = array(
		'CommodityEquipment' => array(
			'className' => 'CommodityEquipment',
			'foreignKey' => 'commodity_id',
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
		'CommodityImage' => array(
			'className' => 'CommodityImage',
			'foreignKey' => 'commodity_id',
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
		'CommodityItem' => array(
			'className' => 'CommodityItem',
			'foreignKey' => 'commodity_id',
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
		'CommodityPrivilege' => array(
			'className' => 'CommodityPrivilege',
			'foreignKey' => 'commodity_id',
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
		'CommodityRentOffice' => array(
			'className' => 'CommodityRentOffice',
			'foreignKey' => 'commodity_id',
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
		'CommodityReturnOffice' => array(
			'className' => 'CommodityReturnOffice',
			'foreignKey' => 'commodity_id',
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
		'CommodityTerm' => array(
			'className' => 'CommodityTerm',
			'foreignKey' => 'commodity_id',
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
		'CommodityPrices' => array(
			'className' => 'CommodityPrices',
			'foreignKey' => 'client_id',
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
	);

	//検索条件をページャーに渡す
	public function getCommonditiesConditions($searchConditions = '', $clientId) {

		$sql['paramType'] = 'querystring';

		$sql["joins"] = array();

		//デフォルト値
		$sql["conditions"] = array();
		$sql["conditions"] = array(
			'Commodity.client_id' => $clientId,
			'Commodity.delete_flg' => 0
		);

		//検索条件に名前があった場合
		if (!empty($searchConditions["name"])) {
			$sql["conditions"] += array("Commodity.name like " => "%{$searchConditions["name"]}%");
		}

		//検索条件に日付があった場合
		if (isset($searchConditions["datetimeFrom"]) && !empty($searchConditions["datetimeFrom"])) {
			$dateTime = $searchConditions["datetimeFrom"];
		}

		if (!empty($dateTime['year'])) {

			$fromDate = $this->formatDateTime($dateTime);

			if (empty($dateTime['month'])) {
				$fromDate = substr($fromDate, 0, 4);
				$sql["conditions"] += array(
					"DATE_FORMAT(CommodityTerm.available_from, '%Y') <=" => $fromDate,
					"DATE_FORMAT(CommodityTerm.available_to, '%Y') >=" => $fromDate,
				);
			} else if (empty($dateTime['day'])) {
				$fromDate = substr($fromDate, 0, 7);
				$sql["conditions"] += array(
					"DATE_FORMAT(CommodityTerm.available_from, '%Y-%m') <=" => $fromDate,
					"DATE_FORMAT(CommodityTerm.available_to, '%Y-%m') >=" => $fromDate,
				);
			} else {
				$sql["conditions"] += array(
					"DATE_FORMAT(CommodityTerm.available_from, '%Y-%m-%d') <=" => $fromDate,
					"DATE_FORMAT(CommodityTerm.available_to, '%Y-%m-%d') >=" => $fromDate,
				);
			}
		} else {
			//$today = date("Y-m-d");;
			//$sql["conditions"] += array("CommodityTerm.available_from <= " => "{$today}","CommodityTerm.available_to >= " => "{$today}");
		}

		//検索条件に車種クラスがあった場合
		if (!empty($searchConditions['car_class_id'])) {
			$sql['conditions'] += array('CarClasses.id' => $searchConditions['car_class_id']);
		}

		//検索条件に在庫管理地域があった場合
		if (!empty($searchConditions['stock_group_id'])) {
			$sql['conditions'] += array('OfficeStockGroup.stock_group_id' => $searchConditions['stock_group_id']);
		}

		//検索条件に公開/非公開があった場合
		if (isset($searchConditions['is_published']) && strlen($searchConditions['is_published']) > 0) {
			if ($searchConditions['is_published'] == 2) {
				$sql['conditions'] += array('Commodity.public_request' => 1);
			} else if ($searchConditions['is_published'] == 0) {
				$sql['conditions'] += array(
					'Commodity.is_published' => $searchConditions['is_published'],
				);
			} else {
				$sql['conditions'] += array('Commodity.is_published' => $searchConditions['is_published']);
			}
		}

		//検索条件に商品グループがあった場合
		if (!empty($searchConditions['commodity_group_id'])) {
			$sql['conditions'] += array('Commodity.commodity_group_id' => $searchConditions['commodity_group_id']);
		}

		$officeStockGroup = "(
				SELECT
					`OfficeStockGroup`.*,
					`StockGroup`.name
				FROM
					`rentalcars`.`office_stock_groups` AS `OfficeStockGroup`,
					`rentalcars`.`stock_groups` AS `StockGroup`
				WHERE
					`OfficeStockGroup`.`stock_group_id` = `StockGroup`.`id`
			)";

		$sql["joins"] = array(
			array(
				"type" => "LEFT",
				"table" => "commodity_rent_offices",
				"alias" => "CommodityRentOffice",
				"conditions" => "Commodity.id = CommodityRentOffice.commodity_id"
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_return_offices",
				"alias" => "CommodityReturnOffice",
				"conditions" => "Commodity.id = CommodityReturnOffice.commodity_id"
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_items",
				"alias" => "CommodityItem",
				"conditions" => array(
					"CommodityItem.delete_flg = 0",
					"Commodity.id = CommodityItem.commodity_id"
				)
			),
			array(
				"type" => "LEFT",
				"table" => "car_classes",
				"alias" => "CarClasses",
				"conditions" => array(
					"CarClasses.id = CommodityItem.car_class_id",
					"CarClasses.delete_flg = 0",
				),
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_terms",
				"alias" => "CommodityTerm",
				"conditions" => " Commodity.id = CommodityTerm.commodity_id"
			),
			array(
				"type" => "Left",
				"table" => "offices",
				"alias" => "Office",
				"conditions" => " Office.id = CommodityRentOffice.office_id"
			),
			array(
				"type" => "Left",
				"table" => $officeStockGroup,
				"alias" => "OfficeStockGroup",
				"conditions" => " OfficeStockGroup.office_id = Office.id"
			),
		);

		$bindModel = array(
			'hasMany' => array(
				'CommodityGroup',
			),
		);
		$this->unbindFully($bindModel);

		$sql['fields'] = array(
			'Commodity.id',
			'Commodity.client_id',
			'Commodity.name',
			'Commodity.public_request',
			'Commodity.is_published',
			'CarClasses.id',
			'CarClasses.client_id',
			'CarClasses.car_type_id',
			'CarClasses.name',
			'CommodityTerm.id',
			'CommodityTerm.client_id',
			'CommodityTerm.commodity_id',
			'CommodityTerm.available_from',
			'CommodityTerm.available_to',
			'CommodityTerm.deadline_hours',
			'CommodityTerm.deadline_days',
			'CommodityTerm.deadline_time',
			'CommodityItem.id',
			'OfficeStockGroup.id',
			'OfficeStockGroup.name',
		);

		$sql['group'] = array('Commodity.id', 'CarClasses.id');
		$sql['order'] = array(
			'Commodity.is_published' => 'DESC',
			'Commodity.created' => 'DESC');
		$sql['recursive'] = 1;

		return $sql;
	}

	//上の関数の速度重視
	public function getCommonditiesConditions2($searchConditions = '', $clientId) {


		$sql['paramType'] = 'querystring';

		$sql["joins"] = array();

		//デフォルト値
		$sql["conditions"] = array();
		$sql["conditions"] = array(
			'Commodity.client_id' => $clientId,
			'Commodity.delete_flg' => 0
		);

		//検索条件に名前があった場合
		if (!empty($searchConditions["name"])) {
			$sql["conditions"] += array("Commodity.name like " => "%{$searchConditions["name"]}%");
		}

		// 検索条件に販売方法があった場合
		if (!empty($searchConditions['sales_type'])) {
			$sql['conditions'] += ['Commodity.sales_type' => $searchConditions['sales_type']];
		}

		//検索条件に日付があった場合
		if (isset($searchConditions["datetimeFrom"]) && !empty($searchConditions["datetimeFrom"])) {
			$dateTime = $searchConditions["datetimeFrom"];
		}

		if (!empty($dateTime['year'])) {

			$fromDate = $this->formatDateTime($dateTime);

			if (empty($dateTime['month'])) {
				$fromDate = substr($fromDate, 0, 4);
				$sql["conditions"] += array('OR' => array(
						array(
							"DATE_FORMAT(CommodityTerm.available_from, '%Y') <=" => $fromDate,
							"DATE_FORMAT(CommodityTerm.available_to, '%Y') >=" => $fromDate,
						),
						array(
							"DATE_FORMAT(AgentOrganizedPrice.start_date, '%Y') <=" => $fromDate,
							"DATE_FORMAT(AgentOrganizedPrice.end_date, '%Y') >=" => $fromDate,
						)
					)
				);
			} else if (empty($dateTime['day'])) {
				$fromDate = substr($fromDate, 0, 7);
				$sql["conditions"] += array('OR' => array(
						array(
							"DATE_FORMAT(CommodityTerm.available_from, '%Y-%m') <=" => $fromDate,
							"DATE_FORMAT(CommodityTerm.available_to, '%Y-%m') >=" => $fromDate,
						),
						array(
							"DATE_FORMAT(AgentOrganizedPrice.start_date, '%Y-%m') <=" => $fromDate,
							"DATE_FORMAT(AgentOrganizedPrice.end_date, '%Y-%m') >=" => $fromDate,
						)
					)
				);
			} else {
				$sql["conditions"] += array('OR' => array(
						array(
							"DATE_FORMAT(CommodityTerm.available_from, '%Y-%m-%d') <=" => $fromDate,
							"DATE_FORMAT(CommodityTerm.available_to, '%Y-%m-%d') >=" => $fromDate,
						),
						array(
							"DATE_FORMAT(AgentOrganizedPrice.start_date, '%Y-%m-%d') <=" => $fromDate,
							"DATE_FORMAT(AgentOrganizedPrice.end_date, '%Y-%m-%d') >=" => $fromDate,
						)
					)
				);
			}
		} else {
			//$today = date("Y-m-d");;
			//$sql["conditions"] += array("CommodityTerm.available_from <= " => "{$today}","CommodityTerm.available_to >= " => "{$today}");
		}

		//検索条件に車種クラスがあった場合
		if (!empty($searchConditions['car_class_id'])) {
			$sql['conditions'] += array('CarClasses.id' => $searchConditions['car_class_id']);
		}

		//検索条件に在庫管理地域があった場合
		if (!empty($searchConditions['stock_group_id'])) {
			$sql['conditions'] += array('OfficeStockGroup.stock_group_id' => $searchConditions['stock_group_id']);
		}

		//検索条件に公開/非公開があった場合
		if (isset($searchConditions['is_published']) && strlen($searchConditions['is_published']) > 0) {
			if ($searchConditions['is_published'] == 2) {
				$sql['conditions'] += array('Commodity.public_request' => 1);
			} else if ($searchConditions['is_published'] == 0) {
				$sql['conditions'] += array(
					'Commodity.is_published' => $searchConditions['is_published'],
				);
			} else {
				$sql['conditions'] += array('Commodity.is_published' => $searchConditions['is_published']);
			}
		}

		//検索条件に商品グループがあった場合
		if (!empty($searchConditions['commodity_group_id'])) {
			$sql['conditions'] += array('Commodity.commodity_group_id' => $searchConditions['commodity_group_id']);
		}

		//検索条件に車両タイプがあった場合
		if (!empty($searchConditions['car_type_id'])) {
			$sql['conditions'] += array('CarClasses.car_type_id' => $searchConditions['car_type_id']);
		}

		$sql["joins"] = array(
			array(
				"type" => "LEFT",
				"table" => "commodity_rent_offices",
				"alias" => "CommodityRentOffice",
				"conditions" => array(
					"Commodity.id = CommodityRentOffice.commodity_id",
					"CommodityRentOffice.client_id = '{$clientId}'",
					"CommodityRentOffice.delete_flg = 0"
				)
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_items",
				"alias" => "CommodityItem",
				"conditions" => array(
					"CommodityItem.delete_flg = 0",
					"Commodity.id = CommodityItem.commodity_id",
					"CommodityItem.client_id =  '{$clientId}'"
				)
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_terms",
				"alias" => "CommodityTerm",
				"conditions" => array(
					"Commodity.id = CommodityTerm.commodity_id",
					"CommodityTerm.client_id = '{$clientId}'",
					"CommodityTerm.delete_flg = 0"
				)
			),
			array(
				"type" => "Left",
				"table" => "offices",
				"alias" => "Office",
				"conditions" => array(
					"Office.id = CommodityRentOffice.office_id",
					"Office.client_id = '{$clientId}'",
					"Office.delete_flg = 0"
				)
			),
			array(
				"type" => "Left",
				"table" => 'office_stock_groups',
				"alias" => "OfficeStockGroup",
				"conditions" => array(
					"OfficeStockGroup.office_id = Office.id",
					"OfficeStockGroup.client_id = '{$clientId}'",
					"OfficeStockGroup.delete_flg = 0"
				)
			),
			array(
				"type" => "Left",
				"table" => 'stock_groups',
				"alias" => "StockGroup",
				"conditions" => array(
					"OfficeStockGroup.stock_group_id = StockGroup.id",
					"StockGroup.client_id = '{$clientId}'",
					"OfficeStockGroup.delete_flg = 0"
				)
			),
			array(
				"type" => 'Left',
				"table" => 'agent_organized_prices',
				"alias" => "AgentOrganizedPrice",
				"conditions" => array(
					"AgentOrganizedPrice.commodity_item_id = CommodityItem.id",
					"AgentOrganizedPrice.delete_flg" => 0
				)
			)
		);

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_system_admin']) {
			$sql['joins'][] = array(
				'type' => 'LEFT',
				'table' => 'office_selection_permissions',
				'alias' => 'OfficeSelectionPermission',
				'conditions' => array(
					'OfficeSelectionPermission.staff_id' => $clientData['id'],
					'OfficeSelectionPermission.office_id = CommodityRentOffice.office_id'
				)
			);
			$sql['conditions'][] = array('OR' => array(
					array('CommodityRentOffice.id IS NOT NULL', 'OfficeSelectionPermission.id IS NOT NULL'),
					array('CommodityRentOffice.id IS NULL', 'OfficeSelectionPermission.id IS NULL')
				)
			);
		}

		if (!$clientData['is_client_admin']) {
			$sql['joins'] = array_merge($sql['joins'], array(
				array(
					"type" => "LEFT",
					"table" => "car_classes",
					"alias" => "CarClasses",
					"conditions" => array(
						"CarClasses.id = CommodityItem.car_class_id",
						"CarClasses.delete_flg = 0",
						"CarClasses.client_id = '{$clientId}'",
						"OR" => array(
							array("CarClasses.scope" => 0),
							array("CarClasses.scope" => $clientData['id'])
						)
					)
				),
				array(
					"type" => "LEFT",
					"table" => "commodity_groups",
					"alias" => "CommodityGroup",
					"conditions" => array(
						"CommodityGroup.id = Commodity.commodity_group_id",
						"CommodityGroup.delete_flg = 0",
						"OR" => array(
							array("CommodityGroup.scope" => 0),
							array("CommodityGroup.scope" => $clientData['id'])
						)
					)
				)
			));
			$sql['conditions'] = array_merge($sql['conditions'], array(
				array('OR' => array(
						array('CommodityItem.id IS NOT NULL', 'CarClasses.id IS NOT NULL'),
						array('CommodityItem.id IS NULL', 'CarClasses.id IS NULL')
					)
				),
				array('OR' => array(
						array('Commodity.commodity_group_id IS NOT NULL', 'CommodityGroup.id IS NOT NULL'),
						array('Commodity.commodity_group_id IS NULL', 'CommodityGroup.id IS NULL')
					)
				)
			));
		} else {
			$sql['joins'][] = array(
				"type" => "LEFT",
				"table" => "car_classes",
				"alias" => "CarClasses",
				"conditions" => array(
					"CarClasses.id = CommodityItem.car_class_id",
					"CarClasses.delete_flg = 0",
					"CarClasses.client_id = '{$clientId}'"
				)
			);
		}

		$bindModel = array(
			'hasMany' => array(
				'CommodityGroup',
			),
		);
		$this->unbindFully($bindModel);

		$sql['fields'] = array(
			'Commodity.id',
			'Commodity.client_id',
			'Commodity.name',
			'Commodity.public_request',
			'Commodity.is_published',
			'Commodity.image_relative_url',
			'Commodity.day_time_flg',
			'Commodity.sales_type',
			'CarClasses.id',
			'CarClasses.client_id',
			'CarClasses.car_type_id',
			'CarClasses.name',
			'CommodityTerm.id',
			'CommodityTerm.client_id',
			'CommodityTerm.commodity_id',
			'CommodityTerm.available_from',
			'CommodityTerm.available_to',
			'CommodityTerm.deadline_hours',
			'CommodityTerm.deadline_days',
			'CommodityTerm.deadline_time',
			'CommodityItem.id',
			'CommodityItem.sipp_code',
			'StockGroup.name',
		);

		$sql['group'] = array('Commodity.id', 'CarClasses.id');
		$sql['order'] = array(
			'Commodity.is_published' => 'DESC',
			'Commodity.created' => 'DESC');
		$sql['recursive'] = -1;

		return $sql;
	}

	// 指定されたスタッフが編集可能な商品か判定して返す
	public function isEditableByThisStaff($commodityId, $clientId) {

		$conditions = array(
			'Commodity.id' => $commodityId,
			'Commodity.client_id' => $clientId,
			'Commodity.delete_flg' => 0,
		);
		$joins = array();

		$clientData = $this->_getCurrentUser();

		if (!$clientData['is_system_admin']) {
			$joins = array_merge($joins, array(
				array(
					'type' => 'LEFT',
					'table' => 'commodity_rent_offices',
					'alias' => 'CommodityRentOffice',
					'conditions' => array(
						'CommodityRentOffice.commodity_id = Commodity.id',
						'CommodityRentOffice.client_id = Commodity.client_id',
						'CommodityRentOffice.delete_flg' => 0
					)
				),
				array(
					'type' => 'LEFT',
					'table' => 'office_selection_permissions',
					'alias' => 'OfficeSelectionPermission',
					'conditions' => array(
						'OfficeSelectionPermission.staff_id' => $clientData['id'],
						'OfficeSelectionPermission.office_id = CommodityRentOffice.office_id'
					)
				)
			));
			$conditions[] = array('OR' => array(
					array('CommodityRentOffice.id IS NOT NULL', 'OfficeSelectionPermission.id IS NOT NULL'),
					array('CommodityRentOffice.id IS NULL', 'OfficeSelectionPermission.id IS NULL')
				)
			);
		}

		if (!$clientData['is_client_admin']) {
			$joins = array_merge($joins, array(
				array(
					'type' => 'LEFT',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => array(
						'CommodityItem.delete_flg' => 0,
						'Commodity.id = CommodityItem.commodity_id',
						'CommodityItem.client_id ' => $clientId
					)
				),
				array(
					'type' => 'LEFT',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id',
						'CarClass.delete_flg' => 0,
						'CarClass.client_id' => $clientId,
						'OR' => array(
							array('CarClass.scope' => 0),
							array('CarClass.scope' => $clientData['id'])
						)
					)
				),
				array(
					"type" => "LEFT",
					"table" => "commodity_groups",
					"alias" => "CommodityGroup",
					"conditions" => array(
						"CommodityGroup.id = Commodity.commodity_group_id",
						"CommodityGroup.delete_flg = 0",
						"OR" => array(
							array("CommodityGroup.scope" => 0),
							array("CommodityGroup.scope" => $clientData['id'])
						)
					)
				)
			));
			$conditions = array_merge($conditions, array(
				array('OR' => array(
						array('CommodityItem.id IS NOT NULL', 'CarClass.id IS NOT NULL'),
						array('CommodityItem.id IS NULL', 'CarClass.id IS NULL')
					)
				),
				array('OR' => array(
						array('Commodity.commodity_group_id IS NOT NULL', 'CommodityGroup.id IS NOT NULL'),
						array('Commodity.commodity_group_id IS NULL', 'CommodityGroup.id IS NULL')
					)
				)
			));
		}

		$count = $this->find('count', array(
				'conditions' => $conditions,
				'joins' => $joins,
				'recursive' => -1,
			)
		);

		return $count > 0;
	}

	//検索条件をページャーに渡す
	public function getCommondityGroupConditions($searchConditions = '', $clientId) {

		$sql["joins"] = array();

		//デフォルト値
		$sql["conditions"] = array();
		$sql["conditions"] = array(
			'Client.id' => $clientId,
			'Commodity.client_id' => $clientId,
			'Commodity.delete_flg' => 0,
			'CommodityGroup.id IS NOT NULL',
		);

		//検索条件に名前があった場合
		if (!empty($searchConditions["name"])) {
			$sql["conditions"] += array("Commodity.name like " => "%{$searchConditions["name"]}%");
		}

		//検索条件に日付があった場合
		if (isset($searchConditions["datetimeFrom"]) && !empty($searchConditions["datetimeFrom"])) {
			$dateTime = $searchConditions["datetimeFrom"];
		}

		if (!empty($dateTime['year'])) {

			$fromDate = $this->formatDateTime($dateTime);

			if (empty($dateTime['month'])) {
				$fromDate = substr($fromDate, 0, 4);
				$sql["conditions"] += array(
					"DATE_FORMAT(CommodityTerm.available_from, '%Y') <=" => $fromDate,
					"DATE_FORMAT(CommodityTerm.available_to, '%Y') >=" => $fromDate,
				);
			} else if (empty($dateTime['day'])) {
				$fromDate = substr($fromDate, 0, 7);
				$sql["conditions"] += array(
					"DATE_FORMAT(CommodityTerm.available_from, '%Y-%m') <=" => $fromDate,
					"DATE_FORMAT(CommodityTerm.available_to, '%Y-%m') >=" => $fromDate,
				);
			} else {
				$sql["conditions"] += array(
					"DATE_FORMAT(CommodityTerm.available_from, '%Y-%m-%d') <=" => $fromDate,
					"DATE_FORMAT(CommodityTerm.available_to, '%Y-%m-%d') >=" => $fromDate,
				);
			}
		} else {
			//$today = date("Y-m-d");;
			//$sql["conditions"] += array("CommodityTerm.available_from <= " => "{$today}","CommodityTerm.available_to >= " => "{$today}");
		}

		//検索条件に車種クラスがあった場合
		if (!empty($searchConditions['car_class_id'])) {
			$sql['conditions'] += array('CarClasses.id' => $searchConditions['car_class_id']);
		}

		//検索条件に在庫管理地域があった場合
		if (!empty($searchConditions['stock_group_id'])) {
			$sql['conditions'] += array('OfficeStockGroup.stock_group_id' => $searchConditions['stock_group_id']);
		}

		//検索条件に公開/非公開があった場合
		if (isset($searchConditions['is_published']) && strlen($searchConditions['is_published']) > 0) {
			if ($searchConditions['is_published'] == 2) {
				$sql['conditions'] += array('Commodity.public_request' => 1);
			} else if ($searchConditions['is_published'] == 0) {
				$sql['conditions'] += array(
					'Commodity.is_published' => $searchConditions['is_published'],
					'Commodity.public_request' => 0
				);
			} else {
				$sql['conditions'] += array('Commodity.is_published' => $searchConditions['is_published']);
			}
		}

		$sql["joins"] = array(
			array(
				"type" => "LEFT",
				"table" => "commodity_groups",
				"alias" => "CommodityGroup",
				"conditions" => "Commodity.commodity_group_id = CommodityGroup.id"
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_rent_offices",
				"alias" => "CommodityRentOffice",
				"conditions" => "Commodity.id = CommodityRentOffice.commodity_id"
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_return_offices",
				"alias" => "CommodityReturnOffice",
				"conditions" => "Commodity.id = CommodityReturnOffice.commodity_id"
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_items",
				"alias" => "CommodityItem",
				"conditions" => array(
					"CommodityItem.delete_flg = 0",
					"Commodity.id = CommodityItem.commodity_id"
				)
			),
			array(
				"type" => "LEFT",
				"table" => "car_classes",
				"alias" => "CarClasses",
				"conditions" => array(
					"CarClasses.id = CommodityItem.car_class_id",
					"CarClasses.delete_flg = 0",
				),
			),
			array(
				"type" => "LEFT",
				"table" => "commodity_terms",
				"alias" => "CommodityTerm",
				"conditions" => " Commodity.id = CommodityTerm.commodity_id"
			),
			array(
				"type" => "Left",
				"table" => "offices",
				"alias" => "Office",
				"conditions" => " Office.id = CommodityRentOffice.office_id"
			),
			array(
				"type" => "Left",
				"table" => "office_stock_groups",
				"alias" => "OfficeStockGroup",
				"conditions" => " OfficeStockGroup.office_id = Office.id"
			),
			array(
				"type" => "Left",
				"table" => "stock_groups",
				"alias" => "StockGroup",
				"conditions" => "StockGroup.delete_flg = 0 AND OfficeStockGroup.stock_group_id = StockGroup.id"
			)
		);


		$sql['fields'] = array(
			'Commodity.id',
			'Commodity.client_id',
			'Commodity.name',
			'Commodity.commodity_group_id',
			'Commodity.public_request',
			'Commodity.is_published',
			'CarClasses.id',
			'CarClasses.client_id',
			'CarClasses.car_type_id',
			'CarClasses.name',
			'Client.id',
			'Client.name',
			'CommodityTerm.id',
			'CommodityTerm.client_id',
			'CommodityTerm.commodity_id',
			'CommodityTerm.available_from',
			'CommodityTerm.available_to',
			'CommodityTerm.deadline_hours',
			'CommodityTerm.deadline_days',
			'CommodityTerm.deadline_time',
			'CommodityItem.id',
			'OfficeStockGroup.id',
			'StockGroup.name',
			'CommodityGroup.id',
			'CommodityGroup.name',
		);

		$sql['group'] = array('Commodity.commodity_group_id');
		$sql['order'] = array(
			'Commodity.commodity_group_id' => 'ASC',
			'Commodity.public_request' => 'DESC',
			'Commodity.is_published' => 'DESC',
			'Commodity.created' => 'DESC');

		return $sql;
	}

	//ステータス毎のカウント
	public function getCommonditiesCount($sql) {

		$sql['joins'] = array_merge($sql['joins'], array(
			array(
				"type" => "LEFT",
				"table" => "clients",
				"alias" => "Client",
				"conditions" => "Commodity.client_id = Client.id"
			),
		));
		$sql['fields'] = array(
			'Commodity.id',
			'Commodity.public_request',
			'Commodity.is_published',
		);

		$dbo = $this->getDataSource();
		$subQuery = $dbo->buildStatement(
				array(
			'fields' => $sql['fields'],
			'table' => $dbo->fullTableName($this),
			'alias' => 'Commodity',
			'limit' => null,
			'offset' => null,
			'joins' => $sql['joins'],
			'conditions' => $sql['conditions'],
			'order' => $sql['order'],
			'group' => $sql['group'],
				), $this
		);

		$options = array(
			'fields' => array(
				"COUNT(C.is_published = 1 OR null) as public_count",
				"COUNT(C.is_published = 0 OR null) as private_count",
				"COUNT(C.public_request = 1 OR null) as request_count"
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'C',
					'table' => "({$subQuery})",
					'conditions' => 'C.id = Commodity.id'
				)
			),
			'conditions' => array(
				'client_id' => $sql['conditions']['Commodity.client_id'],
			),
		);

		return $this->find('first', $options);
	}

	public function getPreviewData($id) {

		$conditions = array(
			'conditions' =>
			array(
				'Commodity.id' => $id, 'Commodity.delete_flg <>' => 1
			),
			'joins' => array(
				array(
					"type" => "LEFT",
					"table" => "commodity_rent_offices",
					"alias" => "CommodityRentOffice",
					"conditions" => "Commodity.id = CommodityRentOffice.commodity_id"
				),
				array(
					"type" => "LEFT",
					"table" => "offices",
					"alias" => "Office",
					"conditions" => "Office.id = CommodityRentOffice.office_id"
				),
				array(
					"type" => "LEFT",
					"table" => "office_areas",
					"alias" => "OfficeArea",
					"conditions" => "Office.id = OfficeArea.office_id"
				),
				array(
					"type" => "LEFT",
					"table" => "areas",
					"alias" => "Area",
					"conditions" => "OfficeArea.area_id = Area.id"
				),
				array(
					"type" => "LEFT",
					"table" => "commodity_items",
					"alias" => "CommodityItem",
					"conditions" => "CommodityItem.commodity_id = Commodity.id"
				),
				array(
					"type" => "LEFT",
					"table" => "car_classes",
					"alias" => "CarClasse",
					"conditions" => "CommodityItem.car_class_id = CarClasse.id"
				),
				array(
					"type" => "LEFT",
					"table" => "client_car_models",
					"alias" => "ClientCarModel",
					"conditions" => "CarClasse.id = ClientCarModel.car_class_id"
				),
				array(
					"type" => "LEFT",
					"table" => "car_models",
					"alias" => "CarModel",
					"conditions" => "ClientCarModel.car_model_id = CarModel.id"
				),
			),
			'fields' => array(
				"CommodityRentOffice.*",
				"Office.*",
				"OfficeArea.*",
				"Area.*",
				"CommodityItem.*",
				"CarClasse.*",
				"ClientCarModel.*",
				"CarModel.*",
			)
		);

		return $this->find('all', $conditions);
	}

	//商品マスタへのデータ挿入
	public function saveMethod($commodityData, $clientData) {

		//画像のアップロード
		/*if (!empty($commodityData['file']['tmp_name'])) {

			// 画像リサイズアップロード
			$this->ImageResize = new ImageResizeUpLoad();
			$upLoadDir = 'commodity_main' . DS . $clientData['Client']['id'];
			if (!empty($commodityData['id'])) {
				$commodityDataId = $commodityData['id'];
			} else {
				$commodityDataId = '';
			}
			$commodityData['image_relative_url'] = $this->ImageResize->resizeUpLoad($commodityData['file'], $upLoadDir, $commodityDataId, array(98, 69));
		} else if (isset($commodityData['default_image'])) {
			$commodityData['image_relative_url'] = $commodityData['default_image'];
		}*/
		if(isset($commodityData['id'])){
			$CommodityImage = ClassRegistry::init('CommodityImage');
			$commodityImages = $CommodityImage->getFirstImageByCommodityIds($commodityData['id']);
			$commodityData['image_relative_url'] = $commodityImages[$commodityData['id']];
		} else {
			$commodityData['image_relative_url'] = null;
		}

		//受付可能時間form
		if (!empty($commodityData['rent_time_from']['hour']) && !empty($commodityData['rent_time_from']['min'])) {
			$commodityData['rent_time_from'] = $this->formatDateTime($commodityData['rent_time_from']);
		} else {
			$commodityData['rent_time_from'] = '8:00';
		}

		//受付可能時間to
		if (!empty($commodityData['rent_time_to']['hour']) && !empty($commodityData['rent_time_to']['min'])) {
			$commodityData['rent_time_to'] = $this->formatDateTime($commodityData['rent_time_to']);
		} else {
			$commodityData['rent_time_to'] = '20:00';
		}

		//返却可能時間from
		if (!empty($commodityData['return_time_from']['hour']) && !empty($commodityData['return_time_from']['min'])) {
			$commodityData['return_time_from'] = $this->formatDateTime($commodityData['return_time_from']);
		} else {
			$commodityData['return_time_from'] = '8:00';
		}

		//返却可能時間to
		if (!empty($commodityData['return_time_to']['hour']) && !empty($commodityData['return_time_to']['min'])) {
			$commodityData['return_time_to'] = $this->formatDateTime($commodityData['return_time_to']);
		} else {
			$commodityData['return_time_to'] = '20:00';
		}

		$commodityData['staff_id'] = $clientData['id'];
		$commodityData['client_id'] = $clientData['client_id'];

		unset($commodityData['file']);
		return $this->save($commodityData, true, array_keys($commodityData));
	}

	//商品IDから登録されている価格を取得
	public function getPlace($commondityId, $clientId, $carClassId = '') {

		$sql = array(
			'conditions' => array(
				'Commodity.id' => $commondityId,
				'Client.id' => $clientId,
				'Commodity.client_id' => $clientId,
				'CarClasses.client_id' => $clientId,
				'CommodityPrice.delete_flg' => 0,
			),
			'joins' => array(
				array(
					"type" => "LEFT",
					"table" => "clients",
					"alias" => "Client",
					"conditions" => array(
						"Client.id = Commodity.client_id"
					)
				),
				array(
					"type" => "LEFT",
					"table" => "commodity_items",
					"alias" => "CommodityItem",
					"conditions" => array(
						"Commodity.delete_flg = 0",
						"Commodity.id = CommodityItem.commodity_id"
					)
				),
				array(
					"type" => "LEFT",
					"table" => "commodity_prices",
					"alias" => "CommodityPrice",
					"conditions" => array(
						"CommodityItem.delete_flg = 0",
						"CommodityItem.id = CommodityPrice.commodity_item_id"
					)
				),
				array(
					"type" => "LEFT",
					"table" => "car_classes",
					"alias" => "CarClasses",
					"conditions" => "CommodityItem.car_class_id = CarClasses.id"
				),
				array(
					"type" => "LEFT",
					"table" => "commodity_terms",
					"alias" => "CommodityTerm",
					"conditions" => " Commodity.id = CommodityTerm.commodity_id"
				),
			),
			'fields' => array(
				'Commodity.id',
				'Commodity.client_id',
				'Commodity.name',
				'Commodity.day_time_flg',
				'CommodityItem.*',
				'CarClasses.id',
				'CarClasses.client_id',
				'CarClasses.car_type_id',
				'CarClasses.name',
				'Client.id',
				'Client.name',
				/*
				 */
				'CommodityTerm.id',
				'CommodityTerm.client_id',
				'CommodityTerm.commodity_id',
				'CommodityTerm.available_from',
				'CommodityTerm.available_to',
				'CommodityTerm.deadline_hours',
				'CommodityTerm.deadline_days',
				'CommodityTerm.deadline_time',
				'CommodityPrice.id',
				'CommodityPrice.client_id',
				'CommodityPrice.span_count',
				'CommodityPrice.price',
				'CommodityPrice.commodity_item_id',
				'CommodityPrice.delete_flg',
			),
			'order' => array('Commodity.name desc', 'CarClasses.id'),
			'recursive' => -1
		);

		if (!empty($carClassId)) {
			$sql['conditions'] += array('CommodityItem.car_class_id' => $carClassId);
		} else {
			$sql['group'] = array('Client.id', 'Commodity.name', 'CarClasses.id');
		}

		return $this->find('all', $sql);
	}

	//商品マスタ関連テーブルへのデータインサート
	public function relativeModelSaveMethod($id, $data, $clientData) {

		try {
			$dataSource = $this->getDataSource();
			$dataSource->begin();

			/**
			 * 削除処理　先に登録データを削除
			 */
			$CommodityRentOffice = ClassRegistry::init('CommodityRentOffice');
			$CommodityRentOffice->deleteAll(array('commodity_id' => $id));

			$CommodityReturnOffice = ClassRegistry::init('CommodityReturnOffice');
			$CommodityReturnOffice->deleteAll(array('commodity_id' => $id));

			$CommodityTerm = ClassRegistry::init('CommodityTerm');
			$CommodityTerm->deleteAll(array('commodity_id' => $id));

			$CommodityEquipment = ClassRegistry::init('CommodityEquipment');
			$CommodityEquipment->deleteAll(array('commodity_id' => $id));

			$CommodityPrivilege = ClassRegistry::init('CommodityPrivilege');
			$CommodityPrivilege->deleteAll(array('commodity_id' => $id));

			/**
			 * saveAllするためにデータをまとめる
			 */
			$saveData['Commodity']['id'] = $id;

			//商品受取営業所情報
			$i = 0;
			if (is_array($data['CommodityRentOffice']['commodity_id'])) {
				foreach ($data['CommodityRentOffice']['commodity_id'] as $key => $val) {
					$saveData['CommodityRentOffice'][$i]['commodity_id'] = $id;
					$saveData['CommodityRentOffice'][$i]['office_id'] = $val;
					$saveData['CommodityRentOffice'][$i]['client_id'] = $clientData['client_id'];
					$saveData['CommodityRentOffice'][$i]['staff_id'] = $clientData['id'];
					$i++;
				}
			}

			//商品返却営業所情報
			$i = 0;
			if (is_array($data['CommodityReturnOffice']['commodity_id'])) {
				foreach ($data['CommodityReturnOffice']['commodity_id'] as $key => $val) {
					$saveData['CommodityReturnOffice'][$i]['commodity_id'] = $id;
					$saveData['CommodityReturnOffice'][$i]['office_id'] = $val;
					$saveData['CommodityReturnOffice'][$i]['client_id'] = $clientData['client_id'];
					$saveData['CommodityReturnOffice'][$i]['staff_id'] = $clientData['id'];
					$i++;
				}
			}


			//商品対象期間情報
			if (!empty($data['CommodityTerm']['available_from']['year'])) {

				$saveData['CommodityTerm'][0]['commodity_id'] = $id;
				$saveData['CommodityTerm'][0]['available_from'] = $this->formatDateTime($data['CommodityTerm']['available_from']);

				$saveData['CommodityTerm'][0]['client_id'] = $clientData['client_id'];
				$saveData['CommodityTerm'][0]['staff_id'] = $clientData['id'];
			}

			if (!empty($data['CommodityTerm']['available_to']['year'])) {
				$saveData['CommodityTerm'][0]['available_to'] = $this->formatDateTime($data['CommodityTerm']['available_to']);
			}

			if (isset($data['CommodityTerm']['deadline_hours'])) {
				$saveData['CommodityTerm'][0]['deadline_hours'] = $data['CommodityTerm']['deadline_hours'];
			}
			$saveData['CommodityTerm'][0]['consider_opening_hours'] = $data['CommodityTerm']['consider_opening_hours'];
			if (isset($data['CommodityTerm']['deadline_days'])) {
				$saveData['CommodityTerm'][0]['deadline_days'] = $data['CommodityTerm']['deadline_days'];
			}

			if (!empty($data['CommodityTerm']['deadline_time']['hour']) && !empty($data['CommodityTerm']['deadline_time']['min'])) {
				$saveData['CommodityTerm'][0]['deadline_time'] = $data['CommodityTerm']['deadline_time']['hour'] . ':' . $data['CommodityTerm']['deadline_time']['min'];
			}

			if (isset($data['CommodityTerm']['bookable_days'])) {
				if ($data['CommodityTerm']['bookable_days'] == 0) {
					$data['CommodityTerm']['bookable_days'] = '';
				}
				$saveData['CommodityTerm'][0]['bookable_days'] = $data['CommodityTerm']['bookable_days'];
			}

			//商品イメージ
			if (is_array($data['CommodityImage']) && !empty($data['CommodityImage'])) {
				$i = 0;
				foreach ($data['CommodityImage'] as $key => $val) {
					$newUpload = false;
					if (!empty($val['image_relative_url']['tmp_name'])) {
						// 画像リサイズアップロード
						$this->ImageResize = new ImageResizeUpLoad();
						$upLoadDir = 'commodity_reference' . DS . $clientData['Client']['id'];
						$imgName = $this->ImageResize->resizeUpLoad($val['image_relative_url'], $upLoadDir, $data['Commodity']['id']);
						if ($imgName) {
							$saveData['CommodityImage'][$i]['image_relative_url'] = $imgName;
							$newUpload = true;
						}
					} else if (isset($val['default_image'])) {
						$saveData['CommodityImage'][$i]['image_relative_url'] = $val['default_image'];
						$newUpload = true;
					}

					if(($i == 0) && $newUpload){
						$saveData['Commodity']['image_relative_url'] = $saveData['CommodityImage'][$i]['image_relative_url'];
					}

					if (!empty($val['remark'])) {
						$saveData['CommodityImage'][$i]['remark'] = $val['remark'];
					} else if (isset($val['default_remark'])) {
						$saveData['CommodityImage'][$i]['remark'] = $val['default_remark'];
					}

					if (!empty($val['image_id'])) {
						$saveData['CommodityImage'][$i]['id'] = $val['image_id'];
					}

					$saveData['CommodityImage'][$i]['commodity_id'] = $id;
					$saveData['CommodityImage'][$i]['client_id'] = $clientData['client_id'];
					$saveData['CommodityImage'][$i]['staff_id'] = $clientData['id'];

					$i++;
				}
			}

			//商品装備情報
			$i = 0;
			if (is_array($data['CommodityEquipment']['equipment_id'])) {
				foreach ($data['CommodityEquipment']['equipment_id'] as $key => $val) {

					$saveData['CommodityEquipment'][$i]['commodity_id'] = $id;
					$saveData['CommodityEquipment'][$i]['equipment_id'] = $val;
					$saveData['CommodityEquipment'][$i]['client_id'] = $clientData['client_id'];
					$saveData['CommodityEquipment'][$i]['staff_id'] = $clientData['id'];
					$i++;
				}
			}

			//商品サービス情報
			$i = 0;
			if (!empty($data['CommodityService']['service_id']) && is_array($data['CommodityService']['service_id'])) {
				foreach ($data['CommodityService']['service_id'] as $key => $val) {
					$saveData['CommodityService'][$i]['commodity_id'] = $id;
					$saveData['CommodityService'][$i]['service_id'] = $val;
					$saveData['CommodityService'][$i]['client_id'] = $clientData['client_id'];
					$saveData['CommodityService'][$i]['staff_id'] = $clientData['id'];
					$i++;
				}
			}

			//商品こだわり情報
			$i = 0;
			if (!empty($data['CommoditySpecial']['special_id']) && is_array($data['CommoditySpecial']['special_id'])) {
				foreach ($data['CommoditySpecial']['special_id'] as $key => $val) {
					$saveData['CommoditySpecial'][$i]['commodity_id'] = $id;
					$saveData['CommoditySpecial'][$i]['special_id'] = $val;
					$saveData['CommoditySpecial'][$i]['client_id'] = $clientData['client_id'];
					$saveData['CommoditySpecial'][$i]['staff_id'] = $clientData['id'];
					$i++;
				}
			}

			//商品特典情報
			if (!empty($data['CommodityPrivilege']['privilege_id']) && is_array($data['CommodityPrivilege']['privilege_id'])) {
				foreach ($data['CommodityPrivilege']['privilege_id'] as $key => $val) {
					$saveData['CommodityPrivilege'][$i]['commodity_id'] = $id;
					$saveData['CommodityPrivilege'][$i]['privilege_id'] = $val;
					$saveData['CommodityPrivilege'][$i]['client_id'] = $clientData['client_id'];
					$saveData['CommodityPrivilege'][$i]['staff_id'] = $clientData['id'];
					$i++;
				}
			}

			//チャイルドシート
			if (!empty($data['CommodityFreeChildSheet']['maximum']) && is_array($data['CommodityFreeChildSheet']['maximum']) && !empty($data['CommodityFreeChildSheet']['maximum'][1])) {
				$i = 1;
				foreach ($data['CommodityFreeChildSheet']['maximum'] as $key => $val) {

					if (!empty($val)) {
						$saveData['CommodityFreeChildSheet'][$i]['commodity_id'] = $id;
						$saveData['CommodityFreeChildSheet'][$i]['child_sheet_id'] = $i;
						$saveData['CommodityFreeChildSheet'][$i]['maximum'] = $val;
						$saveData['CommodityFreeChildSheet'][$i]['client_id'] = $clientData['client_id'];
						$saveData['CommodityFreeChildSheet'][$i]['staff_id'] = $clientData['staff_id'];
					}
					$i++;
				}
			}

			// キャンペーン期間
			if (isset($data['Campaign']) && is_array($data['Campaign']) && count($data['Campaign']) > 0) {

				foreach ($data['Campaign'] as $key => $val) {
					if (empty($val['rank_date_from']) || empty($val['rank_date_to'])) {
						unset($data['Campaign'][$key]);
					}
				}

				if (count($data['Campaign']) > 0) {
					$saveData['Campaign'] = $data['Campaign'];
				}
			}

			if ($clientData['id'] != 1 && $clientData['client_id'] == $clientData['Client']['id']) {
				$saveData['Commodity']['modified'] = false;
			}

			$saveResult = $this->saveAll($saveData, array('atomic' => false, 'validate' => 'first'));
		} catch (Exception $e) {

			$dataSource->rollback();

			return false;
		}

		if ($saveResult) {
			$dataSource->commit();
			return true;
		} else {
			$dataSource->rollback();
			return false;
		}
	}

	//日付をフォーマットする関数
	public function formatDateTime($dateData) {

		$formatDate = '';
		foreach ($dateData as $key => $val) {

			switch ($key) {
				case 'year' :
					$formatDate .= $val;
					break;
				case 'month' :
					$formatDate .= '-' . $val;
					break;
				case 'day' :
					$formatDate .= '-' . $val;
					break;
				case 'hour':
					$formatDate .= ' ' . $val;
					break;
				case 'min':
					$formatDate .= ':' . $val . ':00';
					break;
			}
		}

		return $formatDate;
	}

	//商品IDから商品管理番号を取得する関数
	public function getCommodityKey($id) {

		$commodityKey = $this->find('first', array('conditions' => array('Commodity.id' => $id), 'fields' => array('commodity_key')));

		return $commodityKey['Commodity']['commodity_key'];
	}

	//カークラスに紐づく商品の数を取得
	public function getCarClassCount($carClassId, $clientId) {

		$today = date("Y-m-d");


		return $this->find('count', array(
				'conditions' =>
				array(
					'Commodity.client_id' => $clientId,
					'Commodity.delete_flg' => 0,
					'CommodityItem.delete_flg' => 0,
					'CarClasses.id' => $carClassId,
					"CommodityTerm.available_from <= " => "{$today}"
				),
				'joins' => array(
					array(
						"type" => "LEFT",
						"table" => "commodity_items",
						"alias" => "CommodityItem",
						"conditions" => "Commodity.id = CommodityItem.commodity_id"
					),
					array(
						"type" => "LEFT",
						"table" => "car_classes",
						"alias" => "CarClasses",
						"conditions" => "CommodityItem.car_class_id = CarClasses.id"
					),
					array(
						"type" => "LEFT",
						"table" => "commodity_terms",
						"alias" => "CommodityTerm",
						"conditions" => " Commodity.id = CommodityTerm.commodity_id"
					),
				)
			)
		);
	}

	//在庫管理地域マスタに紐づく商品の数を取得
	public function getStockGroupCount($stockGroupId, $clientId) {

		$today = date("Y-m-d");

		return $this->find('count', array(
					'conditions' =>
					array(
						'or' => array(
							'RentOfficeOfficeStockGroup.stock_group_id' => $stockGroupId,
							'ReturnOfficeStockGroup.stock_group_id' => $stockGroupId,
						),
						'Client.id' => $clientId,
						'Commodity.client_id' => $clientId,
						'Commodity.delete_flg' => 0,
						"CommodityTerm.available_from <= " => "{$today}"
					),
					'joins' =>
					array(
						array(
							"type" => "LEFT",
							"table" => "commodity_rent_offices",
							"alias" => "CommodityRentOffice",
							"conditions" => "Commodity.id = CommodityRentOffice.commodity_id"
						),
						array(
							"type" => "LEFT",
							"table" => "commodity_return_offices",
							"alias" => "CommodityReturnOffice",
							"conditions" => "Commodity.id = CommodityReturnOffice.commodity_id"
						),
						array(
							"type" => "LEFT",
							"table" => "commodity_terms",
							"alias" => "CommodityTerm",
							"conditions" => " Commodity.id = CommodityTerm.commodity_id"
						),
						array(
							"type" => "Left",
							"table" => "offices",
							"alias" => "RentOffice",
							"conditions" => " RentOffice.id = CommodityRentOffice.office_id"
						),
						array(
							"type" => "Left",
							"table" => "office_stock_groups",
							"alias" => "RentOfficeOfficeStockGroup",
							"conditions" => " RentOfficeOfficeStockGroup.office_id = RentOffice.id"
						),
						array(
							"type" => "Left",
							"table" => "stock_groups",
							"alias" => "RentOfficeStockGroup",
							"conditions" => "RentOfficeOfficeStockGroup.stock_group_id = RentOfficeStockGroup.id"
						),
						array(
							"type" => "Left",
							"table" => "offices",
							"alias" => "ReturnOffice",
							"conditions" => "ReturnOffice.id = CommodityReturnOffice.office_id"
						),
						array(
							"type" => "Left",
							"table" => "office_stock_groups",
							"alias" => "ReturnOfficeStockGroup",
							"conditions" => " ReturnOfficeStockGroup.office_id = ReturnOffice.id"
						),
						array(
							"type" => "Left",
							"table" => "stock_groups",
							"alias" => "ReturnStockGroup",
							"conditions" => "ReturnOfficeStockGroup.stock_group_id = ReturnStockGroup.id"
						),
					)
						)
		);
	}

	public function getNotGroupCommodityLists($clientId, $commodityGroupId = false) {

		if (!$commodityGroupId) {
			$options = array(
				'conditions' => array(
					'Commodity.delete_flg' => 0,
					'Commodity.client_id' => $clientId,
					'Commodity.commodity_group_id IS NULL',
				),
				'recursive' => -1
			);
		} else {
			$options = array(
				'conditions' => array(
					'Commodity.delete_flg' => 0,
					'Commodity.client_id' => $clientId,
					array('OR' => array(
							'Commodity.commodity_group_id' => $commodityGroupId,
							'Commodity.commodity_group_id IS NULL',
						)
					),
				),
				'recursive' => -1
			);
		}
		$joins = array();

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_system_admin']) {
			$joins = array_merge($joins, array(
					array(
						'type' => 'LEFT',
						'table' => 'commodity_rent_offices',
						'alias' => 'CommodityRentOffice',
						'conditions' => array(
							'CommodityRentOffice.client_id = Commodity.client_id',
							'CommodityRentOffice.commodity_id = Commodity.id'
						)
					),
					array(
						'type' => 'LEFT',
						'table' => 'office_selection_permissions',
						'alias' => 'OfficeSelectionPermission',
						'conditions' => array(
							'OfficeSelectionPermission.staff_id' => $clientData['id'],
							'OfficeSelectionPermission.office_id = CommodityRentOffice.office_id'
						)
					)
				)
			);
			$options['conditions'][] = array('OR' => array(
					array('CommodityRentOffice.id IS NOT NULL', 'OfficeSelectionPermission.id IS NOT NULL'),
					array('CommodityRentOffice.id IS NULL', 'OfficeSelectionPermission.id IS NULL')
				)
			);
		}
		if (!$clientData['is_client_admin']) {
			$joins = array_merge($joins, array(
					array(
						'type' => 'LEFT',
						'table' => 'commodity_items',
						'alias' => 'CommodityItem',
						'conditions' => array(
							'CommodityItem.commodity_id = Commodity.id'
						)
					),
					array(
						'type' => 'LEFT',
						'table' => 'car_classes',
						'alias' => 'CarClass',
						'conditions' => array(
							'CarClass.id = CommodityItem.car_class_id',
							'OR' => array(
								array('CarClass.scope' => 0),
								array('CarClass.scope' => $clientData['id'])
							)
						)
					)
				)
			);
			$options['conditions'][] = array('OR' => array(
					array('CommodityItem.id IS NOT NULL', 'CarClass.id IS NOT NULL'),
					array('CommodityItem.id IS NULL', 'CarClass.id IS NULL')
				)
			);
		}
		$options['joins'] = $joins;

		return $this->find('list', $options);
	}

	public function getCommodityLists($clientId) {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'client_id' => $clientId
			),
			'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function getCommodity($id) {

		$options = array(
			'conditions' => array(
				'id' => $id
			),
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getBelongCommodityGroup($commodityGroupId) {

		$options = array(
			'fields' => array(
				'Commodity.*',
				'CommodityTerm.*'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'CommodityTerm',
					'table' => 'commodity_terms',
					'conditions' => 'CommodityTerm.commodity_id = Commodity.id'
				)
			),
			'conditions' => array(
				'Commodity.commodity_group_id' => $commodityGroupId,
				'Commodity.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		$commodities = $this->find('all', $options);

		$result = array('Commodity' => array());
		foreach ($commodities as $commodity) {
			$result['Commodity'][$commodity['Commodity']['id']] = array(
				'id' => $commodity['Commodity']['id'],
				'term_id' => $commodity['CommodityTerm']['id']);
		}

		return $result;
	}

	public function getCarClassIds($commodityGroupId) {

		$options = array(
			'fields' => array(
				'Commodity.*',
				'CommodityItem.*',
				'CommodityItem.id as nyan'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => 'CommodityItem.commodity_id = Commodity.id'
				)
			),
			'conditions' => array(
				'Commodity.commodity_group_id' => $commodityGroupId,
				'Commodity.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$commodities = $this->find('all', $options);

		$result = array();
		foreach ($commodities as $commodity) {
			array_push($result, $commodity['CommodityItem']['car_class_id']);
		}

		return $result;
	}

	public function getCommodityGroupStockGroupIds($clientId, $commodityGroupId = false) {

		$options = array(
			'fields' => array(
				'CommodityRentOffice.office_id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'CommodityRentOffice',
					'table' => 'commodity_rent_offices',
					'conditions' => 'CommodityRentOffice.commodity_id = Commodity.id',
				),
				array(
					'type' => 'LEFT',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => 'CommodityItem.commodity_id = Commodity.id',
				),
			),
			'conditions' => array(
				'Commodity.client_id' => $clientId,
				'Commodity.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
			),
			'group' => 'CommodityRentOffice.office_id',
			'recursive' => -1
		);
		if (!empty($commodityGroupId)) {
			$conditions['Commodity.commodity_group_id'] = $commodityGroupId;
		}
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_system_admin']) {
			$options['joins'][] = array(
				'type' => 'INNER',
				'alias' => 'OfficeSelectionPermission',
				'table' => 'office_selection_permissions',
				'conditions' => array(
					'OfficeSelectionPermission.staff_id' => $clientData['id'],
					'OfficeSelectionPermission.office_id = CommodityRentOffice.office_id'
				)
			);
		}
		$commodities = $this->find('all', $options);

		$officeIds = array();
		if (!empty($commodities)) {
			$officeIds = Hash::extract($commodities, '{n}.CommodityRentOffice.office_id');
		}

		$OfficeStockGroup = ClassRegistry::init('OfficeStockGroup');
		$officeStockGroups = $OfficeStockGroup->getStockGroups($officeIds);

		$stockGroupIds = array();
		foreach ($officeStockGroups as $officeStockGroup) {
			array_push($stockGroupIds, $officeStockGroup['OfficeStockGroup']['stock_group_id']);
		}

		return array_unique($stockGroupIds);
	}

	/**
	 * 追加
	 */
	public function getCommodityReservation($clientId) {

		$this->unbindModel(array(
			'belongsTo' => array(
				'Client',
				'Language',
				'Staff',
			),
			'hasMany' => array(
				'CommodityImage',
				'CommodityPrivilege',
				'CommodityRentOffice',
				'CommodityReturnOffice',
				'CommodityPrices',
				'Campaign',
			),
		));
		$options = array(
			'fields' => array(
// 						'Commodity.*',
// 						'CommodityRentOffice.*',
// 						'CommodityReturnOffice.*',
// 						'CommoditySpecial.*',
			),
			'conditions' => array(
				'Commodity.client_id' => $clientId,
				'Commodity.delete_flg' => 0,
			),
		);
		return $this->find('all', $options);
	}

	public function getCommodityForSippCode($commodityId) {
		// オプションカテゴリ＝4WDのレコード取得用サブクエリ生成
		$db = $this->getDataSource();

		$subQuery = $db->buildStatement(
				array(
			'fields' => array('DISTINCT CommodityPrivilege.commodity_id'),
			'table' => $db->fullTableName('commodity_privileges'),
			'alias' => 'CommodityPrivilege',
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Privilege',
					'table' => 'privileges',
					'conditions' => array(
						'Privilege.id = CommodityPrivilege.privilege_id',
						'Privilege.delete_flg' => 0,
						'Privilege.option_category_id' => 8,
					)
				),
			),
			'conditions' => array(
				'CommodityPrivilege.commodity_id' => $commodityId,
			),
				), $this
		);

		// 商品情報取得クエリ
		$options = array(
			'fields' => array(
				'Commodity.name',
				'Commodity.transmission_flg',
				'CommodityPrivilege.commodity_id'
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'CommodityPrivilege',
					'table' => "({$subQuery})",
					'conditions' => array(
						'Commodity.id = CommodityPrivilege.commodity_id',
					)
				),
			),
			'conditions' => array(
				'Commodity.id' => $commodityId,
			),
			'recursive' => -1,
		);

		return $this->find('first', $options);
	}

	// キャンペーンに紐づく商品の数を取得
	public function getCampaignCount($campaignId, $clientId) {

		$today = date("Y-m-d");

		return $this->find('count', array(
				'conditions' => array(
					'Commodity.client_id' => $clientId,
					'Commodity.delete_flg' => 0,
					'CommodityItem.delete_flg' => 0,
					'CommodityCampaignPrice.campaign_id' => $campaignId,
					'CommodityCampaignPrice.delete_flg' => 0,
					"CommodityTerm.available_from <= " => "{$today}"
				),
				'joins' => array(
					array(
						"type" => "LEFT",
						"table" => "commodity_items",
						"alias" => "CommodityItem",
						"conditions" => "Commodity.id = CommodityItem.commodity_id"
					),
					array(
						"type" => "LEFT",
						"table" => "commodity_campaign_prices",
						"alias" => "CommodityCampaignPrice",
						"conditions" => "CommodityItem.id = CommodityCampaignPrice.commodity_item_id"
					),
					array(
						"type" => "LEFT",
						"table" => "commodity_terms",
						"alias" => "CommodityTerm",
						"conditions" => " Commodity.id = CommodityTerm.commodity_id"
					),
				)
			)
		);
	}

	public function getCommodityInfoByCommodityItemId($commodityItemId) {
		return $this->find('first', array(
			'fields' => array(
				'Commodity.id',
				'Commodity.client_id',
				'Commodity.name',
				'Commodity.description',
				'Commodity.remark',
				'CommodityItem.car_class_id',
				'CommodityItem.car_model_id',
				'CarType.name', 
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => array(
						'CommodityItem.commodity_id = Commodity.id',
						'CommodityItem.id' => $commodityItemId,
						'CommodityItem.delete_flg' => 0,
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id',
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'car_types',
					'alias' => 'CarType',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id',
					),
				),
			),
			'conditions' => array(
				'Commodity.is_published' => 1,
				'Commodity.delete_flg' => 0,
			),
			'recursive' => -1,
		));
	}

	/**
	 * 商品IDから、募集型企画の価格情報が登録されている商品アイテムデータを取得する
	 *
	 * @param string $commondityId
	 * @param string $clientId
	 * @param string $carClassId
	 * @return array
	 */
	public function getAgentOrganizedCommodityItemData($commondityId, $clientId, $carClassId = '')
	{
		$options = [];
		$options['joins'] = [
			[
				'table'      => 'commodity_items',
				'alias'      => 'CommodityItem',
				'type'       => 'INNER',
				'conditions' => [
					'Commodity.id = CommodityItem.commodity_id',
					'CommodityItem.delete_flg = 0'
				]
			],
			[
				'table'      => 'car_classes',
				'alias'      => 'CarClasses',
				'type'       => 'INNER',
				'conditions' => [
					'CarClasses.id = CommodityItem.car_class_id',
					'CarClasses.delete_flg = 0'
				]
			],
			[
				'table'      => 'clients',
				'alias'      => 'Client',
				'type'       => 'INNER',
				'conditions' => [
					'Client.id = Commodity.client_id',
					'Client.delete_flg = 0'
				]
			],
			[
				'table'      => 'agent_organized_prices',
				'alias'      => 'AgentOrganizedPrice',
				'type'       => 'INNER',
				'conditions' => [
					'CommodityItem.id = AgentOrganizedPrice.commodity_item_id',
					'AgentOrganizedPrice.delete_flg = 0'
				]
			],
		];
		$options['fields'] = [
			'Commodity.id',
			'Commodity.client_id',
			'Commodity.name',
			'Commodity.day_time_flg',
			'CommodityItem.*',
			'CarClasses.id',
			'CarClasses.name'
		];
		$options['conditions'] = [
			'Commodity.id'         => $commondityId,
			'Commodity.client_id'  => $clientId,
			'Commodity.delete_flg' => 0,
		];
		$options['order'] = [
			'Commodity.name desc',
			'CarClasses.id'
		];
		$options['recursive'] = -1;

		if ($carClassId !== '') {
			$options['conditions'] += ['CommodityItem.car_class_id' => $carClassId];
		} else {
			$options['group'] = ['Client.id', 'Commodity.name', 'CarClasses.id'];
		}

		return $this->find('all', $options);
	}

	/**
	 * 商品アイテムIDからsales_typeを取得する
	 *
	 * @param string $commondityItemId
	 * @return string
	 */
	public function getSalesTypeByCommodityItemId($commodityItemId)
	{
		$options = [
			'fields' => [
				'Commodity.sales_type'
			],
			'joins' => [
				[
					'table'      => 'commodity_items',
					'alias'      => 'CommodityItem',
					'type'       => 'INNER',
					'conditions' => [
						'Commodity.id = CommodityItem.commodity_id',
						'Commodity.delete_flg = 0'
					]
				]
			],
			'conditions' => [
				'CommodityItem.id' => $commodityItemId,
				'CommodityItem.delete_flg' => 0
			],
			'recursive' => -1
		];
		$result = $this->find('first', $options);

		return ($result === []) ? '' : $result['Commodity']['sales_type'];
	}

	/**
	 * 商品ごとのキャンペーン,通常全ての1日分または6時間分の最小料金を取得する
	 * @param unknown $commodityIds
	 * return array $[商品ID] = 最小料金
	 */
	public function getPriceDataMulti($commodityIds) {
		// 通常料金を取得する
		$options = array(
			'fields' => array(
				'CommodityPrice.price',
				'Commodity.id',
			),
			'joins' => array(
				array(
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'type' => 'LEFT',
					'conditions' => array(
						'CommodityItem.delete_flg = 0',
						'Commodity.id = CommodityItem.commodity_id'
					),
				),
				array(
					'table' => 'commodity_prices',
					'alias' => 'CommodityPrice',
					'type' => 'INNER',
					'conditions' => array(
						'CommodityPrice.commodity_item_id = CommodityItem.id',
						'CommodityPrice.span_count' => array('1','6'),
					),
				),
			),
			'conditions' => array(
				'Commodity.id' => $commodityIds,
				'CommodityItem.delete_flg' => 0,
				'CommodityPrice.delete_flg' => 0,
				'OR' => array(
					array(
						'Commodity.day_time_flg' => '1',
						'CommodityPrice.span_count' => '6',
					),
					array(
						'Commodity.day_time_flg' => '0',
						'CommodityPrice.span_count' => '1',
					),
				),
			),
			'recursive' => -1,
		);
		$results = $this->find('all', $options);
		if(!empty($results)){
			foreach($results as $rk => $rv){
				//時間か暦日か判定し6時間/日帰りの料金を抽出
				$result[$rv['Commodity']['id']] = $rv['CommodityPrice']['price'];
			}
		}

		// キャンペーンが設定されている商品があれば料金を取得する
		$options = array(
			'fields' => array(
				'CommodityCampaignPrice.price',
				'Commodity.id',
			),
			'joins' => array(
				array(
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'type' => 'LEFT',
					'conditions' => array(
						'CommodityItem.delete_flg = 0',
						'Commodity.id = CommodityItem.commodity_id'
					),
				),
				array(
					'table' => 'commodity_campaign_prices',
					'alias' => 'CommodityCampaignPrice',
					'type' => 'INNER',
					'conditions' => array(
						'CommodityCampaignPrice.commodity_item_id = CommodityItem.id',
						'CommodityCampaignPrice.span_count' => array('1','6'),
					),
				),
			),
			'conditions' => array(
				'Commodity.id' => $commodityIds,
				'CommodityItem.delete_flg' => 0,
				'CommodityCampaignPrice.delete_flg' => 0,
				'OR' => array(
					array(
						'Commodity.day_time_flg' => '1',
						'CommodityCampaignPrice.span_count' => '6',
					),
					array(
						'Commodity.day_time_flg' => '0',
						'CommodityCampaignPrice.span_count' => '1',
					),
				),
			),
			'recursive' => -1,
		);
		$campaign_results = $this->find('all', $options);

		//キャンペーンデータがあれば通常料金と比較し低い方を保持する
		if(!empty($campaign_results)){
			foreach($campaign_results as $ck => $cv){
				//時間か暦日か判定し6時間/日帰りの料金を抽出
				//商品の通常料金、設定されているキャンペーンの中から、金額が低いものを採用する
				if($result[$cv['Commodity']['id']] > $cv['CommodityCampaignPrice']['price']){
					$result[$cv['Commodity']['id']] = $cv['CommodityCampaignPrice']['price'];
				}
			}
		}

		return $result;
	}
}

<?php
App::uses('AppModel', 'Model');
App::uses('Client', 'Model');
App::uses('ReservationDetail', 'Model');
App::uses('ReservationChildSheet', 'Model');
App::uses('ReservationPrivilege', 'Model');
require_once("encrypt_class.php");

class Reservation extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $Statistic;
	public $Office;

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'client_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'reservation_datetime' => array(
			'datetime' => array(
				'rule' => array(
					'datetime'
				)
			)
		),
		'reservation_status_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'commodity_item_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'rent_datetime' => array(
			'datetime' => array(
				'rule' => array(
					'datetime'
				)
			)
		),
		'rent_office_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'return_datetime' => array(
			'datetime' => array(
				'rule' => array(
					'datetime'
				)
			)
		),
		'return_office_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'adults_count' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'children_count' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'infants_count' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'span_count' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'cars_count' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'total_price' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'total_tax' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'commodity_json' => array(
			'notempty' => array(
				'rule' => array(
					'notempty'
				)
			)
		),
		'last_name' => array(
			'notempty' => array(
				'rule' => array(
					'notempty'
				)
			)
		),
		'first_name' => array(
			'notempty' => array(
				'rule' => array(
					'notempty'
				)
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array(
					'email'
				)
			)
		),
		'tel' => array(
			'notempty' => array(
				'rule' => array(
					'notempty'
				)
			)
		),
		'prefecture_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'need_pickup' => array(
			'boolean' => array(
				'rule' => array(
					'boolean'
				)
			)
		),
		'arrival_airline_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'sent_to_enduser' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'sent_to_client' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'cancel_datetime' => array(
			'datetime' => array(
				'rule' => array(
					'datetime'
				)
			)
		),
		'cancel_contact_method_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'cancel_staff_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'cancel_remark' => array(
			'notempty' => array(
				'rule' => array(
					'notempty'
				)
			)
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array(
					'boolean'
				)
			)
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				),
				'message' => '金額は数字で指定してください。'
			)
		)
	);

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
		'UserSession' => array(
			'className' => 'UserSession',
			'foreignKey' => 'user_session_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ReservationStatus' => array(
			'className' => 'ReservationStatus',
			'foreignKey' => 'reservation_status_id',
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
		'CommodityRentOffice' => array(
			'className' => 'CommodityRentOffice',
			'foreignKey' => 'rent_office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CommodityReturnOffice' => array(
			'className' => 'CommodityReturnOffice',
			'foreignKey' => 'return_office_id',
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
			'foreignKey' => 'reservation_id',
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
		'ReservationChildSheet' => array(
			'className' => 'ReservationChildSheet',
			'foreignKey' => 'reservation_id',
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
		'ReservationDetail' => array(
			'className' => 'ReservationDetail',
			'foreignKey' => 'reservation_id',
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
		'ReservationMail' => array(
			'className' => 'ReservationMail',
			'foreignKey' => 'reservation_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => array(
				'id',
				'reservation_id',
				'reservation_mail_id',
				'mail_datetime',
				'staff_id',
				'contents'
			),
			'order' => 'mail_datetime desc',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ReservationPrivilege' => array(
			'className' => 'ReservationPrivilege',
			'foreignKey' => 'reservation_id',
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
	 * 貸出日ごとの予約・成約・キャンセル数
	 */
	function getProceeds($year = '', $month = '', $officeIds = array(), $isSettlement = false) {
		$this->Client = new Client();
		$clients = $this->Client->getClientByConclusionContractCriteria();

		// 貸出日基準のクライアント
		$array1 = array();
		if (!empty($clients['rent'])) {
			if(empty($month)) {
				$rentDateTime = "%" . $year . "%";
				$before_rentDateTime = "%" . ($year -1) . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%c') as date,DATE_FORMAT(rent_datetime, '%Y%c') as Ydate";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m')";

				$conditions = array(
					'reservation_status_id <>' => 0,
					'OR' => array(
						array('rent_datetime like ' => $rentDateTime),
						array('rent_datetime like ' => $before_rentDateTime)
					),
					'Reservation.client_id' => $clients['rent']
				);
			} else {
				$rentDateTime = "%" . $year . '-' . $month . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%e') as date";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m%d')";

				$conditions = array(
					'reservation_status_id <>' => 0,
					'rent_datetime like' => $rentDateTime,
					'Reservation.client_id' => $clients['rent']
				);
			}

			if (!empty($officeIds)) {
				$conditions['Reservation.rent_office_id'] = $officeIds;
			}

			$array1 = $this->getProceedsExec($conditions, $fields, $group, $isSettlement);
		}

		// 返却日基準のクライアント
		$array2 = array();
		if (!empty($clients['return'])) {
			if (empty($month)) {
				$returnDateTime = "%" .$year."%";
				$before_returnDateTime = "%" . ($year -1) . "%";
				$fields = "DATE_FORMAT(return_datetime, '%c') as date,DATE_FORMAT(return_datetime, '%Y%c') as Ydate";
				$group = "DATE_FORMAT(return_datetime, '%Y%m')";

				$conditions = array(
					'reservation_status_id <>' => 3,
					'OR' => array(
						array('return_datetime like ' => $returnDateTime),
						array('return_datetime like ' => $before_returnDateTime)
					),
					'Reservation.client_id' => $clients['return']
				);
			} else {
				$returnDateTime = "%".$year.'-'.$month."%";
				$fields = "DATE_FORMAT(return_datetime, '%e') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m%d')";

				$conditions = array(
					'reservation_status_id <>' => 3,
					'return_datetime like' => $returnDateTime,
					'Reservation.client_id' => $clients['return']
				);
			}

			if (!empty($officeIds)) {
				$conditions['Reservation.rent_office_id'] = $officeIds;
			}

			$returnData = $this->getProceedsExec($conditions, $fields, $group, $isSettlement);

			// 返却日基準のクライアントのキャンセルデータ
			if (empty($month)) {
				$rentDateTime = "%" .$year."%";
				$before_rentDateTime = "%" . ($year -1) . "%";
				$fields = "DATE_FORMAT(return_datetime, '%c') as date,DATE_FORMAT(return_datetime, '%Y%c') as Ydate";
				$group = "DATE_FORMAT(return_datetime, '%Y%m')";

				$conditions = array(
					'reservation_status_id' => 3,
					'OR' => array(
						array('return_datetime like ' => $rentDateTime),
						array('return_datetime like ' => $before_rentDateTime)
					),
					'Reservation.client_id' =>$clients['return']
				);
			} else {
				$rentDateTime = "%".$year.'-'.$month."%";
				$fields = "DATE_FORMAT(return_datetime, '%e') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m%d')";

				$conditions = array(
					'reservation_status_id' => 3,
					'return_datetime like' => $rentDateTime,
					'Reservation.client_id' =>$clients['return']
				);
			}
			
			if (!empty($officeIds)) {
				$conditions['Reservation.rent_office_id'] = $officeIds;
			}

			$returnDataCancel = $this->getProceedsExec($conditions, $fields, $group, $isSettlement);

			$array2 = array_merge($returnData, $returnDataCancel);
		}

		return array_merge($array1,$array2);
	}

	/**
	 * 貸出日ごとの予約・成約・キャンセル数(精算額集計用)
	 */
	function getProceedsSettlement($year = '', $month = '', $officeIds = array(), $isSettlement = false) {
		$this->Client = new Client();
		$clients = $this->Client->getClientByConclusionContractCriteria();

		// 貸出日基準のクライアント
		$array1 = array();
		if (!empty($clients['rent'])) {
			if(empty($month)) {
				$rentDateTime = "%" . $year . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%c') as date";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m')";
			} else {
				$rentDateTime = "%" . $year . '-' . $month . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%e') as date";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m%d')";
			}

			$conditions = array(
				'reservation_status_id <>' => 0,
				'rent_datetime like' => $rentDateTime,
				'Reservation.client_id' => $clients['rent']
			);

			if (!empty($officeIds)) {
				$conditions['Reservation.rent_office_id'] = $officeIds;
			}

			$array1 = $this->getProceedsExec($conditions, $fields, $group, $isSettlement);
		}

		// 返却日基準のクライアント
		$array2 = array();
		if (!empty($clients['return'])) {
			if (empty($month)) {
				$returnDateTime = "%" .$year."%";
				$fields = "DATE_FORMAT(return_datetime, '%c') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m')";
			} else {
				$returnDateTime = "%".$year.'-'.$month."%";
				$fields = "DATE_FORMAT(return_datetime, '%e') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m%d')";
			}

			$conditions = array(
				'reservation_status_id <>' => 3,
				'return_datetime like' => $returnDateTime,
				'Reservation.client_id' => $clients['return']
			);

			if (!empty($officeIds)) {
				$conditions['Reservation.rent_office_id'] = $officeIds;
			}

			$returnData = $this->getProceedsExec($conditions, $fields, $group, $isSettlement);

			// 返却日基準のクライアントのキャンセルデータ
			if (empty($month)) {
				$rentDateTime = "%" .$year."%";
				$fields = "DATE_FORMAT(return_datetime, '%c') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m')";
			} else {
				$rentDateTime = "%".$year.'-'.$month."%";
				$fields = "DATE_FORMAT(return_datetime, '%e') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m%d')";
			}

			$conditions = array(
				'reservation_status_id' => 3,
				'return_datetime like' => $rentDateTime,
				'Reservation.client_id' =>$clients['return']
			);
			
			if (!empty($officeIds)) {
				$conditions['Reservation.rent_office_id'] = $officeIds;
			}

			$returnDataCancel = $this->getProceedsExec($conditions, $fields, $group, $isSettlement);

			$array2 = array_merge($returnData, $returnDataCancel);
		}

		return array_merge($array1,$array2);
	}

	// 実行
	function getProceedsExec($conditions, $fields, $group, $isSettlement) {

		$conditions['Commodity.sales_type'] = Constant::SALES_TYPE_ARRANGED;
		$query = array(
			"conditions" => $conditions,
			"fields" => array(
				"group_concat(Reservation.id separator ',') as reservation_ids",
				"SUM(Reservation.amount) as price",
				"COUNT(Reservation.id) as count",
				"Reservation.reservation_status_id",
				"Reservation.client_id",
				"COUNT(CASE WHEN Reservation.payment_status IS NULL THEN 1 ELSE NULL END) as local_pay",
				"SUM(CASE WHEN Reservation.payment_status IS NULL THEN Reservation.amount ELSE 0 END) as local_amount",
				//"Client.commission_rate",
				//"truncate(SUM(Reservation.amount) * (Client.commission_rate) / 100,0) as commission",
				$fields
			),
			"joins" => array(
				array(
					'type' => "INNER",
					'alias' => "CommodityItem",
					'table' => "commodity_items",
					'conditions' => "CommodityItem.id = Reservation.commodity_item_id"
				),
				array(
					'type' => "INNER",
					'alias' => "Commodity",
					'table' => "commodities",
					'conditions' => "Commodity.id = CommodityItem.commodity_id"
				)
			),
			"group" => array(
				"reservation_status_id",
				$group
			)
		);

		if ($isSettlement) {
			$query["fields"][] = "Office.settlement_company_id";
			$query["joins"][] = array(
				'type' => "INNER",
				'alias' => "Office",
				'table' => "offices",
				'conditions' => "Office.id = Reservation.rent_office_id"
			);
			$query["group"][] = "settlement_company_id";
		} else {
			$query["group"][] = "client_id";
		}

		return $this->find("all", $query);
	}

	/**
	 * 予約日ごとの予約・成約・キャンセル数
	 */
	function getReservedOperand($year = '', $month = '', $day = '', $officeIds = array()) {
		if (!empty($day)) {
			$rentDateTime = "%" . $year . '-' . $month . '-' . $day . "%";
			$fields = "DATE_FORMAT(Reservation.created, '%k') as date";
			$group = "DATE_FORMAT(Reservation.created, '%Y%m%d%H')";
		} else if(! empty($month)) {
			$rentDateTime = "%" . $year . '-' . $month . "%";
			$fields = "DATE_FORMAT(Reservation.created, '%e') as date";
			$group = "DATE_FORMAT(Reservation.created, '%Y%m%d')";
		} else {
			$rentDateTime = "%" . $year . "%";
			$before_rentDateTime = "%" . ($year -1) . "%";
			$fields = "DATE_FORMAT(Reservation.created, '%c') as date,DATE_FORMAT(Reservation.created, '%Y%c') as Ydate";
			$group = "DATE_FORMAT(Reservation.created, '%Y%m')";

			$conditions = array(
				'Reservation.reservation_status_id <>' => 0,
				'OR' => array(
					array('Reservation.created like' => $rentDateTime),
					array('Reservation.created like' => $before_rentDateTime)
				),
				'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
			);
		}

		if(!isset($conditions)){
			$conditions = array(
				'Reservation.reservation_status_id <>' => 0,
				'Reservation.created like' => $rentDateTime,
				'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
			);
		}

		if (!empty($officeIds)) {
			$conditions['Reservation.rent_office_id'] = $officeIds;
		}

		return $this->find('all', array(
			'conditions'=> $conditions,
			'fields'=>array(
				'SUM(Reservation.amount) as price',
				'COUNT(Reservation.id) as count',
				'Reservation.reservation_status_id',
				'Reservation.client_id',
				'Office.settlement_company_id',
				//'Client.commission_rate',
				//'truncate(SUM(Reservation.amount) * (Client.commission_rate / 100),0) as commission',
				$fields
			),
			"joins" => array(
				array(
					'type' => "INNER",
					'alias' => "Office",
					'table' => "offices",
					'conditions' => "Office.id = Reservation.rent_office_id"
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Commodity',
					'table' => 'commodities',
					'conditions' => 'Commodity.id = CommodityItem.commodity_id'
				)
			),
			'group' => array(
				'Office.settlement_company_id',
				$group
			)
		));
	}

	/**
	 * 日付範囲内の成約数取得
	 * 引数1 YYYY-mm-dd 検索開始日
	 * 引数2 YYYY-mm-dd 検索終了日
	 */
	function getNumberOfConclusion($fromDate, $toDate) {
		return $this->find('all', array(
			'conditions' => array(
				'reservation_status_id' => 2,
				'created >=' => $fromDate,
				'created <=' => $toDate
			),
			'fields' => array(
				'reservation_status_id',
				"client_id",
				"created as date",
				"user_session_id"
			),
			'recursive' => -1
		));
	}

	// Test Method
	public function getNumberOfConclusionIP($fromDate, $toDate) {
		$sql = "
				select
					user_session_id,
					created as appointment_date
				from
					reservations
				where
					created >= '$fromDate'
				and
					created <= '$toDate'
				and
					reservation_status_id = 2
			";

		return $this->query($sql);
	}

	public function getReservationCount($conditions) {
		$db = $this->getDataSource();

		$conditionsSub = array_merge($conditions, array(
			'fields' => array(
				'Reservation.amount',
				'Reservation.cancel_reason_id',
				'Reservation.user_agent',
				'PaymentLog.other_payment_econ_credit_log_id',
			),
			'table' => $db->fullTableName($this),
			'alias' => 'Reservation',
			'joins' => array(
				array(
					"type" => "LEFT",
					"table" => "clients",
					"alias" => "Client",
					"conditions"=>"Client.id = Reservation.client_id"
				),
				array(
					"type" => "LEFT",
					"table" => "commodity_items",
					"alias" => "CommodityItem",
					"conditions" => "CommodityItem.id = Reservation.commodity_item_id"
				),
				array(
					"type" => "LEFT",
					"table" => "commodities",
					"alias" => "Commodity",
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
					"table" => "car_types",
					"alias" => "CarType",
					"conditions" => "CarType.id = CarClasses.car_type_id"
				),
				array(
					'type' => 'LEFT',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'CmThApplicationDetail',
					'conditions' => array(
						'CmThApplicationDetail.application_id = Reservation.id',
						'CmThApplicationDetail.service_cd' => 'rc',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'common.cm_th_other_payment_econ_credit_log',
					'alias' => 'PaymentLog',
					'conditions' => array(
						'PaymentLog.cm_application_id = CmThApplicationDetail.cm_application_id'
					),
				),
			),
			'group' => 'Reservation.id',
		));
		$subQuery = $db->buildStatement($conditionsSub, $this);

		$conditionsMain = array(
			'fields' => array(
				"SUM(amount) as total_price",
				"COUNT(cancel_reason_id = 0 OR null) as cancel0_count",
				"COUNT(cancel_reason_id = 1 OR null) as cancel1_count",
				"COUNT(cancel_reason_id = 2 OR null) as cancel2_count",
				"COUNT(cancel_reason_id = 3 OR null) as cancel3_count",
				"COUNT(cancel_reason_id = 4 OR null) as cancel4_count",
				"COUNT(cancel_reason_id = 5 OR null) as cancel5_count",
				"COUNT(cancel_reason_id = 6 OR null) as cancel6_count",
				"COUNT(cancel_reason_id = 7 OR null) as cancel7_count",
				"COUNT(user_agent) as total_count",
				"COUNT((user_agent NOT LIKE '%iPhone%' AND user_agent NOT LIKE '%Android%' AND user_agent NOT LIKE '%Windows Phone%' AND user_agent NOT LIKE '%BlackBerry%') OR null) as pc_count",
				"COUNT((user_agent LIKE '%iPhone%' OR user_agent LIKE '%Android%' OR user_agent LIKE '%Windows Phone%' OR user_agent LIKE '%BlackBerry%') OR null) as sp_count",
			),
			'table' => "({$subQuery})",
			'alias' => 'Sub',
		);
		$query = $db->buildStatement($conditionsMain, $this);

		$result = $this->query($query);

		return $result[0][0];
	}

	public function getReservationData($conditions) {
		return $sql = array_merge($conditions, array(
			'joins' => array(
				array(
					"type" => "LEFT",
					"table" => "commodity_items",
					"alias" => "CommodityItem",
					"conditions" => "CommodityItem.id = Reservation.commodity_item_id"
				),
				array(
					"type" => "LEFT",
					"table" => "commodities",
					"alias" => "Commodity",
					"conditions" => "CommodityItem.commodity_id = Commodity.id"
				),
				array(
					"type" => "LEFT",
					"table" => "car_classes",
					"alias" => "CarClasses",
					"conditions" => "CommodityItem.car_class_id = CarClasses.id"
				),
				array(
					"type" => "LEFT",
					"table" => "car_types",
					"alias" => "CarType",
					"conditions" => "CarType.id = CarClasses.car_type_id"
				),
				array(
					"type" => "LEFT",
					"table" => "offices",
					"alias" => "RentOffices",
					"conditions" => "RentOffices.id = Reservation.rent_office_id"
				),
				array(
					"type" => "LEFT",
					"table" => "offices",
					"alias" => "ReturnOffices",
					"conditions" => "ReturnOffices.id = Reservation.return_office_id"
				),
				array(
					"type" => "LEFT",
					"table" => "cancel_reasons",
					"alias" => "CancelReason",
					"conditions" => "CancelReason.id = Reservation.cancel_reason_id"
				),
				array(
					"type" => "LEFT",
					"table" => "settlement_companies",
					"alias" => "SettlementCompany",
					"conditions" => "SettlementCompany.id = RentOffices.settlement_company_id"
				),
				array(
					'type' => 'LEFT',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'CmThApplicationDetail',
					'conditions' => array(
						'CmThApplicationDetail.application_id = Reservation.id',
						'CmThApplicationDetail.service_cd' => 'rc',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'common.cm_th_other_payment_econ_credit_log',
					'alias' => 'PaymentLog',
					'conditions' => array(
						'PaymentLog.cm_application_id = CmThApplicationDetail.cm_application_id'
					),
				),
			),
			'fields' => array(
				'Reservation.id',
				'Reservation.client_id',
				'Reservation.user_session_id',
				'Reservation.user_agent',
				'Reservation.reservation_status_id',
				'Reservation.reservation_key',
				'Reservation.recommend_id',
				'Reservation.rent_datetime',
				'Reservation.rent_office_id',
				'Reservation.return_datetime',
				'Reservation.return_office_id',
				'Reservation.adults_count',
				'Reservation.children_count',
				'Reservation.infants_count',
				'Reservation.cars_count',
				'Reservation.last_name',
				'Reservation.first_name',
				'Reservation.email',
				'Reservation.mail_status',
				'Reservation.amount',
				'Reservation.cancel_flg',
				'Reservation.cancel_datetime',
				'Reservation.cancel_remark',
				'Reservation.cancel_reason_id',
				'Reservation.advertising_cd',
				'Reservation.created',
				'Reservation.payment_status',
				'Reservation.sales_price',
				'Client.name',
				'Commodity.name',
				'Commodity.sales_type',
				'ReservationStatus.name',
				'CarClasses.name',
				'CarType.name',
				'RentOffices.name',
				'ReturnOffices.name',
				'CancelReason.reason',
				'SettlementCompany.id',
				'SettlementCompany.name',
				'SettlementCompany.commission_rate',
				'SettlementCompany.fee_rate',
				'SettlementCompany.is_internal_tax',
				'CmThApplicationDetail.cm_application_id',
				'PaymentLog.other_payment_econ_credit_log_id',
				"DATE_FORMAT(CASE Client.conclusion_contract_criteria WHEN 0 THEN Reservation.rent_datetime ELSE Reservation.return_datetime END, '%Y%m') AS contract_ym"
			),
			'group' => 'Reservation.id',
			'order' => 'Reservation.id desc'
		));
	}

	/**
	 * クライアントからもらったCSVデータと予約者のデータを比較するためのデータを全件取得（クライアント・返却日）
	 */
	public function getReservationComparison($clientId, $returnDate) {
		$options = array(
			'fields' => array(
				'Reservation.*',
				'ReservationStatus.*',
				'CommodityItem.*',
				'Commodity.*',
				'CarClass.*',
				'ReservationChildSheet.*'
			),
			'conditions' => array(
				'Reservation.client_id' => $clientId,
				'Reservation.delete_flg' => 0,
				'Reservation.reservation_status_id' => 2,
				'Reservation.return_datetime LIKE' => '%' . $returnDate . '%'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'ReservationStatus',
					'table' => 'reservation_statuses',
					'conditions' => 'ReservationStatus.id = Reservation.reservation_status_id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => array(
						'CommodityItem.id = Reservation.commodity_item_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Commodity',
					'table' => 'commodities',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarClass',
					'table' => 'car_classes',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id'
					)
				),
				array(
					'type' => 'LEFT',
					'alias' => 'ReservationChildSheet',
					'table' => 'reservation_child_sheets',
					'conditions' => array(
						'ReservationChildSheet.reservation_id = Reservation.id'
					)
				)
			),
			'order' => array(
				'Reservation.return_datetime' => 'ASC'
			),
			'recursive' => - 1
		);

		return $this->find('all', $options);
	}

	// 電話番号からユーザーの利用回数を取得
	public function getReserveCountList($telList) {
		$reserveCountList = $this->find('all', array(
			'conditions' => array(
				'tel' => $telList
			),
			'fields' => array(
				'tel',
				'count(Reservation.id) as count'
			),
			'group' => array(
				'tel having count(*) >= 1'
			),
			'recursive' => - 1
		));

		return Set::Combine($reserveCountList, '{n}.Reservation.tel', '{n}.0.count');
	}

	public function canEditReservation($reservationId, $staffId) {
		$reservation = $this->find('first', [
			'conditions' => array(
				'Reservation.id' => $reservationId
			),
		]);

		$canEdit = true;

		$currentTime = strtotime('now');

		App::import('Model', 'Client');
		$this->Client = new Client;
		$this->Client->recursive = -1;
		$client = $this->Client->findById($reservation['Reservation']['client_id']);
		if ($client['Client']['conclusion_contract_criteria']) {
			// 締め日は返却日
			$conclusion = $reservation['Reservation']['return_datetime'];
		} else {
			// 締め日は貸出日
			$conclusion = $reservation['Reservation']['rent_datetime'];
		}

		$conclusionYear = date('Y', strtotime($conclusion));
		$conclusionMonth = date('n', strtotime($conclusion));
		$currentYear = date('Y', $currentTime);
		$currentMonth = date('n', $currentTime);

		// 締め日の翌々月以降は編集不可
		if ($currentMonth + ($currentYear - $conclusionYear) * 12 > $conclusionMonth + 1) {
			$canEdit = false;
		// 締め日の翌月
		} else if ($currentMonth + ($currentYear - $conclusionYear) * 12 == $conclusionMonth + 1) {
			$today = date('j', $currentTime);
			if ($today >= 3) {
				App::import('Model', 'PublicHoliday');
				$this->PublicHoliday = new PublicHoliday;
				$holidays = $this->PublicHoliday->getHolidaysByMonth(date('Y-m', $currentTime));
				if ($currentMonth == 1) {
					// 1/2 & 1/3 が休日になるが、祝日マスタには登録できない
					// （祝日とすると、店舗の営業時間も祝日仕様になってしまう）
					$holidays[2] = $currentYear.'-01-02'; // emptyでなければ何でも良い
					$holidays[3] = $currentYear.'-01-03';
				}

				$businessDayCount = 0;
				for ($i = 1; $i <= $today; $i++) {
					$timeStamp = mktime(0, 0, 0, $currentMonth, $i, $currentYear);
					$dayOfWeek = date('w', $timeStamp);
					if ($dayOfWeek == 0 || $dayOfWeek == 6) {
						continue;
					} else if (!empty($holidays[$i])) {
						continue;
					}
					// 第三営業日以降は編集不可
					if (++$businessDayCount >= 3) {
						$canEdit = false;
						break;
					}
				}
			}
		}

		if (!$canEdit) {
			App::import('Model', 'UnlockClientEdit');
			$this->UnlockClientEdit = new UnlockClientEdit;
			$unlockClientEdit = $this->UnlockClientEdit->findBy($staffId, $reservation['Reservation']['id']);
			if ($unlockClientEdit && $unlockClientEdit['UnlockClientEdit']['end_datetime'] >= date('Y-m-d H:i:s')) { // ロック解除中
				$canEdit = true;
			}
		}

		return $canEdit;
	}

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()){
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();
		if (!empty($this->data['Reservation']['last_name'])){
			$this->data['Reservation']['last_name'] = $encrypt->encrypt($this->data['Reservation']['last_name']);
		}
		if (!empty($this->data['Reservation']['first_name'])){
			$this->data['Reservation']['first_name'] = $encrypt->encrypt($this->data['Reservation']['first_name']);
		}
		if (!empty($this->data['Reservation']['email'])){
			$this->data['Reservation']['email'] = $encrypt->encrypt($this->data['Reservation']['email']);
		}
		if (!empty($this->data['Reservation']['tel'])){
			$this->data['Reservation']['tel'] = $encrypt->encrypt($this->data['Reservation']['tel']);
		}

		return true;
	}

	/**
	 * 検索前処理
	 */
	public function beforeFind($queryData) {
		// 対象検索条件を暗号化
		$encrypt = new Encrypt();
		if (!empty($queryData['conditions']['Reservation.last_name'])) {
			$queryData['conditions']['Reservation.last_name'] = $encrypt->encrypt($queryData['conditions']['Reservation.last_name']);
		}
		if (!empty($queryData['conditions']['Reservation.first_name'])) {
			$queryData['conditions']['Reservation.first_name'] = $encrypt->encrypt($queryData['conditions']['Reservation.first_name']);
		}
		if (!empty($queryData['conditions']['Reservation.email'])) {
			$queryData['conditions']['Reservation.email'] = $encrypt->encrypt($queryData['conditions']['Reservation.email']);
		}
		if (!empty($queryData['conditions']['Reservation.tel'])) {
			$queryData['conditions']['Reservation.tel'] = $encrypt->encrypt($queryData['conditions']['Reservation.tel']);
		}

		if (!empty($queryData['conditions']['Reservation.last_name like'])) {
			$val = trim($queryData['conditions']['Reservation.last_name like'], '%');
			$queryData['conditions']['Reservation.last_name like'] = '%' . $encrypt->encrypt($val) . '%';
		}
		if (!empty($queryData['conditions']['Reservation.first_name like'])) {
			$val = trim($queryData['conditions']['Reservation.first_name like'], '%');
			$queryData['conditions']['Reservation.first_name like'] = '%' . $encrypt->encrypt($val) . '%';
		}
		if (!empty($queryData['conditions']['Reservation.email like'])) {
			$val = trim($queryData['conditions']['Reservation.email like'], '%');
			$queryData['conditions']['Reservation.email like'] = '%' . $encrypt->encrypt($val) . '%';
		}
		if (!empty($queryData['conditions']['Reservation.tel like'])) {
			$val = trim($queryData['conditions']['Reservation.tel like'], '%');
			$queryData['conditions']['Reservation.tel like'] = '%' . $encrypt->encrypt($val) . '%';
		}

		return $queryData;
	}

	/**
	 * 検索後処理
	 */
	public function afterFind($results, $primary = false) {
		// 対象フィールドを複合化
		$encrypt = new Encrypt();
		foreach ($results as $key => $val) {
			if (isset($val['Reservation']['last_name'])) {
				$results[$key]['Reservation']['last_name'] = $encrypt->decrypt($val['Reservation']['last_name']);
			}
			if (isset($val['Reservation']['first_name'])) {
				$results[$key]['Reservation']['first_name'] = $encrypt->decrypt($val['Reservation']['first_name']);
			}
			if (isset($val['Reservation']['email'])) {
				$results[$key]['Reservation']['email'] = $encrypt->decrypt($val['Reservation']['email']);
			}
			if (isset($val['Reservation']['tel'])) {
				$results[$key]['Reservation']['tel'] = $encrypt->decrypt($val['Reservation']['tel']);
			}
		}
		return $results;
	}

	public function getCmApplicationId($reservation_id) {
		return $this->find('first', array(
			'conditions' => array(
				'Reservation.id' => $reservation_id
			),
			'fields' => array(
				'CmThApplicationDetail.cm_application_id'
			),
			"joins" => array(
				array(
					'type' => 'INNER',
					'alias' => 'CmThApplicationDetail',
					'table' => 'skyticket.cm_th_application_detail',
					'conditions' => array(
						'CmThApplicationDetail.application_id = Reservation.id',
						'CmThApplicationDetail.service_cd' => 'rc',
					),
				)
			)
		));
	}

	public function makePRConditions($params) {
		$settings = [
			'fields' => [
				'Reservation.*',
				'ReservationStatus.*',
				'Client.name',
				'CancelReason.id',
				'CancelReason.reason',
				'CmThApplicationDetail.cm_application_id'
			],
			'joins' => [
				[
					'type' => 'LEFT',
					'table' => 'rentacar.cancel_reasons',
					'alias' => 'CancelReason',
					'conditions' => 'CancelReason.id = Reservation.cancel_reason_id',
				],
				[
					'type' => 'INNER',
					'alias' => 'CmThApplicationDetail',
					'table' => 'skyticket.cm_th_application_detail',
					'conditions' => [
						'CmThApplicationDetail.application_id = Reservation.id',
						'CmThApplicationDetail.service_cd' => 'rc',
					],
				],
			]
		];

		if (isset($params['reservation_id']) && $params['reservation_id'] != '') { // 予約ID
			$settings['conditions']['Reservation.id'] = $params['reservation_id'];
		}

		if (isset($params['cm_application_ids'])) {
			$settings['conditions']['CmThApplicationDetail.cm_application_id'] = $params['cm_application_ids'];
		}

		return $this->find('all', $settings);
	}

	public function getContractStatusData($yearAndMonth) {

		$subQuery ="
			SELECT
				rr.id,
				SUM(rcd.amount * rcd.count) as cancel_fee,
				SUM(
					CASE rcd.account_code
						WHEN 'ADVENTURE_FEE' THEN rcd.amount * rcd.count
						ELSE 0
					END
				) as adventure_fee
			FROM
				rentacar.reservations rr
			INNER JOIN
				rentacar.clients rc ON rc.id = rr.client_id
			LEFT JOIN
				rentacar.cancel_details rcd
			ON 
				rcd.reservation_id = rr.id
			WHERE
				DATE_FORMAT(CASE rc.conclusion_contract_criteria WHEN 0 THEN rr.rent_datetime ELSE rr.return_datetime END, '%Y%m') = $yearAndMonth
			GROUP BY
				rr.id
			";

		$options = [
			'fields' => array(
				'Reservation.last_name',
				'Reservation.first_name',
				'Reservation.client_id',
				'Reservation.payment_status',
				'Reservation.reservation_key',
				'Reservation.reservation_status_id',
				'Reservation.rent_datetime',
				'Reservation.return_datetime',
				'Reservation.amount',
				'Client.name',
				'Client.conclusion_contract_criteria',
				'Commodity.sales_type',
				'SettlementCompany.id',
				'SettlementCompany.name',
				'CancelDetail.cancel_fee',
				'CancelDetail.adventure_fee',
			),
			'joins' => [
				[
					'type' => 'inner',
					'table' => 'clients',
					'alias' => 'Client',
					'conditions' => 'Client.id = Reservation.client_id'
				],
				[
					'type' => 'inner',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
				],
				[
					'type' => 'inner',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => 'Commodity.id = CommodityItem.commodity_id'
				],
				[
					'type' => 'inner',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => 'Office.id = Reservation.rent_office_id'
				],
				[
					'type' => 'left',
					'table' => 'settlement_companies',
					'alias' => 'SettlementCompany',
					'conditions' => 'SettlementCompany.id = Office.settlement_company_id'
				],
				[
					'type' => 'left',
					'table' => "({$subQuery})",
					'alias' => 'CancelDetail',
					'conditions' => 'CancelDetail.id = Reservation.id'
				],
			],
			'conditions' => [
				"DATE_FORMAT(CASE Client.conclusion_contract_criteria WHEN 0 THEN Reservation.rent_datetime ELSE Reservation.return_datetime END, '%Y%m') " => $yearAndMonth,
			],
			'recursive' => - 1
		];

		$result = $this->find('all', $options);
		return $result;

	}

	public function getRecommendedData($recommendIds, $joins = null, $conditions = null) {
		$condition = array(
			'fields' => array(
				'SettlementCompany.id',
				'Reservation.recommend_id',
				'COUNT(Reservation.id) as count',
				'SUM(Reservation.amount) as sum',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RentOffice',
					'table' => 'offices',
					'conditions' => 'RentOffice.id = Reservation.rent_office_id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'SettlementCompany',
					'table' => 'settlement_companies',
					'conditions' => 'SettlementCompany.id = RentOffice.settlement_company_id'
				)
			),
			'conditions' => array(
				'Reservation.recommend_id' => $recommendIds,
			),
			'group' => array(
				'SettlementCompany.id',
				'Reservation.recommend_id',
			),
			'recursive' => -1
		);
		if (!empty($joins)) {
			$condition['joins'] = array_merge($condition['joins'], $joins);
		}
		if (!empty($conditions)) {
			$condition['conditions'] += $conditions;
		}
		$result = $this->find('all', $condition);
		if (!empty($result)) {
			$result = Hash::combine($result, '{n}.SettlementCompany.id', '{n}.0', '{n}.Reservation.recommend_id');
		}
		return $result;
	}

	public function getReservationApiPostData($reservationId) {
		$options = array(
			'fields' => array(
				'Reservation.reservation_key',
				"DATE_FORMAT(Reservation.rent_datetime, '%Y/%m/%d %H:%i:%s') AS rent_datetime",
				'RentOffice.office_code',
				'RentOffice.name',
				"DATE_FORMAT(Reservation.return_datetime, '%Y/%m/%d %H:%i:%s') AS return_datetime",
				'ReturnOffice.office_code',
				'ReturnOffice.name',
				'CarClass.id',
				'CarClass.name',
				'CarModel.id',
				'CarModel.name',
				'CommodityItem.id',
				'Commodity.id',
				'Commodity.name',
				'Commodity.sales_type',
				'Reservation.last_name',
				'Reservation.first_name',
				'Reservation.tel',
				'Reservation.email',
				'Reservation.arrival_flight_number',
				'Reservation.departure_flight_number',
				'Reservation.adults_count',
				'Reservation.children_count',
				'Reservation.infants_count',
				'Reservation.amount',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => 'CommodityItem.id = Reservation.commodity_item_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => 'Commodity.id = CommodityItem.commodity_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'RentOffice',
					'conditions' => 'RentOffice.id = Reservation.rent_office_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'ReturnOffice',
					'conditions' => 'ReturnOffice.id = Reservation.return_office_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions' => 'CarClass.id = CommodityItem.car_class_id',
				),
				array(
					'type' => 'LEFT',
					'table' => 'car_models',
					'alias' => 'CarModel',
					'conditions' => 'CarModel.id = CommodityItem.car_model_id'
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationId,
			),
			'recursive' => -1,
		);

		return $this->find('first', $options);
	}

	// 予約IDから精算対象となるクライアント、営業所を抽出する。
	function getOfficesByReservationId($reservationIds) {

		$query = array(
			"conditions" => array(
				"Reservation.id" => $reservationIds
			),
			"fields" => array(
				"Client.id",
				"Client.name",
				"Office.id",
				"Office.name"
			),
			"joins" => array(
				array(
					'type' => "INNER",
					'alias' => "Office",
					'table' => "offices",
					'conditions' => "Office.id = Reservation.rent_office_id"
				),
				array(
					'type' => "INNER",
					'alias' => "Client",
					'table' => "clients",
					'conditions' => "Client.id = Reservation.client_id"
				),
			),
			"group" => array(
				"Reservation.client_id",
				"Reservation.rent_office_id"
			),
			'recursive' => - 1
		);

		return $this->find("all", $query);
	}
  
    /**
     * client_id取得 
     */
    public function getClientId($reservation_id) {
        $result =  $this->find('first', array(
            'conditions' => array(
                'Reservation.id' => $reservation_id
            ),
            'fields' => array(
                'Reservation.client_id'
            ),
            "joins" => array(
                array(
                    'type' => 'INNER',
                    'alias' => 'CmThApplicationDetail',
                    'table' => 'skyticket.cm_th_application_detail',
                    'conditions' => array(
                        'CmThApplicationDetail.application_id = Reservation.id',
                        'CmThApplicationDetail.service_cd' => 'rc',
                    ),
                )
            )
        ));

        if (empty($result)) {
            return null;
        }
        return $result['Reservation']['client_id'];
    }

	public function getReservationDataForMail($reservationId)
	{
		$db = $this->getDataSource();

		$conditionsSub = array(
			'fields' => array(
				'ReservationMails.*',
			),
			'table' => $db->fullTableName('reservation_mails'),
			'alias' => 'ReservationMails',
			'conditions' => array(
				'ReservationMails.reservation_id' => $reservationId,
			),
			'order' => 'ReservationMails.mail_datetime desc',
		);
		$subQuery = $db->buildStatement($conditionsSub, $this);

		$options = array(
			'fields' => array(
				'Reservation.*',
				'Client.*',
				'RentOffice.*',
				'ReturnOffice.*',
				'CommodityItem.*',
				'Commodity.*',
				'CarClass.*',
				'CarType.*',
				'ReservationMail.contents',
			),
			'joins' => array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'INNER',
					'conditions' => array(
						'Client.id = Reservation.client_id'
					),
				),
				array(
					'table' => 'offices',
					'alias' => 'RentOffice',
					'type' => 'INNER',
					'conditions' => array(
						'RentOffice.id = Reservation.rent_office_id'
					),
				),
				array(
					'table' => 'offices',
					'alias' => 'ReturnOffice',
					'type' => 'INNER',
					'conditions' => array(
						'ReturnOffice.id = Reservation.return_office_id'
					),
				),
				array(
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'type' => 'INNER',
					'conditions' => array(
						'CommodityItem.id = Reservation.commodity_item_id',
					),
				),
				array(
					'table' => 'commodities',
					'alias' => 'Commodity',
					'type' => 'INNER',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id',
					),
				),
				array(
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'type' => 'INNER',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id',
					),
				),
				array(
					'table' => 'car_types',
					'alias' => 'CarType',
					'type' => 'INNER',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id',
					),
				),
				array(
					'table' => "({$subQuery})",
					'alias' => 'ReservationMail',
					'type' => 'LEFT',
					'conditions' => array(
						'ReservationMail.reservation_id = Reservation.id',
					),
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationId,
				'Reservation.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$result = $this->find('first', $options);

		$this->Office = ClassRegistry::init('Office');
		// 出発営業所営業時間取得
		$rentOfficeTime = $this->Office->getOfficeBusinessHours($result['Reservation']['rent_office_id'], date('Y-m-d', strtotime($result['Reservation']['rent_datetime'])));

		$result['RentOffice']['office_hours_from'] = $rentOfficeTime['start_time'];
		$result['RentOffice']['office_hours_to'] = $rentOfficeTime['end_time'];
		$result['RentOffice']['start_day'] = $rentOfficeTime['start_day'];
		$result['RentOffice']['end_day'] = $rentOfficeTime['end_day'];
		// 返却営業所営業時間取得
		$returnOfficeTime = $this->Office->getOfficeBusinessHours($result['Reservation']['return_office_id'], date('Y-m-d', strtotime($result['Reservation']['return_datetime'])));
		$result['ReturnOffice']['office_hours_from'] = $returnOfficeTime['start_time'];
		$result['ReturnOffice']['office_hours_to'] = $returnOfficeTime['end_time'];
		$result['ReturnOffice']['start_day'] = $returnOfficeTime['start_day'];
		$result['ReturnOffice']['end_day'] = $returnOfficeTime['end_day'];

		$params = array(
			'reservationId' => $reservationId,
		);

		// 基本料金・免責補償料金・乗り捨て料金・深夜手数料
		$reservationDetailData = $this->query("
				SELECT
					reservation_details.*
				FROM
					rentacar.reservation_details
				WHERE
					reservation_details.reservation_id = :reservationId
				AND
					reservation_details.detail_type_id
				IN (1,4,5,6)
				", $params);
		$reservationDetailArray = array();
		if (!empty($reservationDetailData)) {
			foreach ($reservationDetailData as $reservationDetail) {
				$reservationDetailArray['ReservationDetail'][] = $reservationDetail['reservation_details'];
			}
		}
		if (!empty($reservationDetailArray)) {
			$result = array_merge_recursive($result, $reservationDetailArray);
		}

		// チャイルドシート
		$reservationChildSheetData = $this->query("
				SELECT
					reservation_child_sheets.*
				FROM
					rentacar.reservation_child_sheets
				WHERE
					reservation_child_sheets.reservation_id = :reservationId
				", $params);
		$reservationChildSheetArray = array();
		if (!empty($reservationChildSheetData)) {
			foreach ($reservationChildSheetData as $reservationChildSheet) {
				$reservationChildSheetArray['ReservationChildSheet'][] = $reservationChildSheet['reservation_child_sheets'];
			}
		}
		if (!empty($reservationChildSheetArray)) {
			$result = array_merge_recursive($result, $reservationChildSheetArray);
		}

		// オプション（特典）
		$reservationPrivilegeData = $this->query("
				SELECT
					reservation_privileges.*
				FROM
					rentacar.reservation_privileges
				WHERE
					reservation_privileges.reservation_id = :reservationId
				", $params);
		$reservationPrivilegeArray = array();
		if (!empty($reservationPrivilegeData)) {
			foreach ($reservationPrivilegeData as $reservationPrivilege) {
				$reservationPrivilegeArray['ReservationPrivilege'][] = $reservationPrivilege['reservation_privileges'];
			}
		}
		if (!empty($reservationPrivilegeArray)) {
			$result = array_merge_recursive($result, $reservationPrivilegeArray);
		}

		return $result;
	}

	// 事前決済かどうか
	public function getWebFlg($reservation_id)
	{
		$params = array(
			'reservationId' => $reservation_id,
		);

		$data = $this->query("
				SELECT
					count(*) as count
				FROM
					rentacar.reservations as Reservation
				WHERE
					Reservation.id = :reservationId
				AND
					Reservation.payment_status IS NOT NULL
				", $params);

		return $data[0][0]['count'] ? true : false;
	}

	/**
	 * 貸出日ごとの予約・成約・キャンセル数(精算書用)
	 */
	function getProceedsSettlementSummary($year = '', $month = '', $settlementCompanyIds = array()) {
		$this->Client = new Client();
		$clients = $this->Client->getClientByConclusionContractCriteria();

		// 貸出日基準のクライアント
		$array1 = array();
		if (!empty($clients['rent'])) {
			if(empty($month)) {
				$rentDateTime = "%" . $year . "%";
			} else {
				$rentDateTime = "%" . $year . '-' . $month . "%";
			}

			$conditions = array(
				'reservation_status_id <>' => 0,
				'rent_datetime like' => $rentDateTime,
				'Reservation.client_id' => $clients['rent']
			);

			if (!empty($settlementCompanyIds)) {
				$conditions['Office.settlement_company_id'] = $settlementCompanyIds;
			} else {
				$conditions['Office.settlement_company_id'] = null;
			}

			$array1 = $this->getProceedsSettlementSummaryExec($conditions);
		}

		// 返却日基準のクライアント
		$array2 = array();
		if (!empty($clients['return'])) {
			if (empty($month)) {
				$returnDateTime = "%" .$year."%";
			} else {
				$returnDateTime = "%".$year.'-'.$month."%";
			}

			$conditions = array(
				'reservation_status_id <>' => 3,
				'return_datetime like' => $returnDateTime,
				'Reservation.client_id' => $clients['return']
			);

			if (!empty($settlementCompanyIds)) {
				$conditions['Office.settlement_company_id'] = $settlementCompanyIds;
			} else {
				$conditions['Office.settlement_company_id'] = null;
			}

			$returnData = $this->getProceedsSettlementSummaryExec($conditions);

			// 返却日基準のクライアントのキャンセルデータ
			if (empty($month)) {
				$rentDateTime = "%" .$year."%";
			} else {
				$rentDateTime = "%".$year.'-'.$month."%";
			}

			$conditions = array(
				'reservation_status_id' => 3,
				'return_datetime like' => $rentDateTime,
				'Reservation.client_id' =>$clients['return']
			);
			
			if (!empty($settlementCompanyIds)) {
				$conditions['Office.settlement_company_id'] = $settlementCompanyIds;
			} else {
				$conditions['Office.settlement_company_id'] = null;
			}

			$returnDataCancel = $this->getProceedsSettlementSummaryExec($conditions);

			$array2 = array_merge($returnData, $returnDataCancel);
		}

		return array_merge($array1,$array2);
	}

	// 実行
	function getProceedsSettlementSummaryExec($conditions) {

		$conditions['Commodity.sales_type'] = Constant::SALES_TYPE_ARRANGED;
		$query = array(
			"conditions" => $conditions,
			"fields" => array(
				"Reservation.id",
				"Reservation.reservation_key",
				"Reservation.last_name",
				"Reservation.first_name",
				"Reservation.amount",
				"Reservation.payment_status",
				"Reservation.sales_price",
				"Reservation.reservation_status_id",
				"Office.settlement_company_id",
				"Client.name",
				"ReturnOffices.name",
			),
			"joins" => array(
				array(
					'type' => "INNER",
					'alias' => "CommodityItem",
					'table' => "commodity_items",
					'conditions' => "CommodityItem.id = Reservation.commodity_item_id"
				),
				array(
					'type' => "INNER",
					'alias' => "Commodity",
					'table' => "commodities",
					'conditions' => "Commodity.id = CommodityItem.commodity_id"
				),
				array(
					'type' => "INNER",
					'alias' => "Office",
					'table' => "offices",
					'conditions' => "Office.id = Reservation.rent_office_id"
				),
				array(
					'type' => "LEFT",
					'table' => "clients",
					'alias' => "Client",
					'conditions' => "Client.id = Reservation.client_id"
				),
				array(
					'type' => "LEFT",
					'table' => "offices",
					'alias' => "ReturnOffices",
					'conditions' => "ReturnOffices.id = Reservation.return_office_id"
				)
			),
			"order" => array("Reservation.id"),
		);

		return $this->find("all", $query);
	}

	// 予約完了メールで使っているデータの取得(一斉メール置換用)
	public function getReservationDataForMailMulti($reservationIds) {
		$db = $this->getDataSource();

		$conditionsSub = array(
			'fields' => array(
				'ReservationMails.*',
			),
			'table' => $db->fullTableName('reservation_mails'),
			'alias' => 'ReservationMails',
			'conditions' => array(
				'ReservationMails.reservation_id' => $reservationIds,
			),
			'order' => 'ReservationMails.mail_datetime desc',
		);
		$subQuery = $db->buildStatement($conditionsSub, $this);

		// 本来はReservation.delete_flg=0であるべきだがadmin検索画面ではその条件で検索されてないため含めない
		$options = array(
			'fields' => array(
				'Reservation.*',
				'Client.*',
				'RentOffice.*',
				'ReturnOffice.*',
				'CommodityItem.*',
				'Commodity.*',
				'CarClass.*',
				'CarType.*',
				'ReservationMail.contents',
			),
			'joins' => array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'INNER',
					'conditions' => array(
						'Client.id = Reservation.client_id'
					),
				),
				array(
					'table' => 'offices',
					'alias' => 'RentOffice',
					'type' => 'INNER',
					'conditions' => array(
						'RentOffice.id = Reservation.rent_office_id'
					),
				),
				array(
					'table' => 'offices',
					'alias' => 'ReturnOffice',
					'type' => 'INNER',
					'conditions' => array(
						'ReturnOffice.id = Reservation.return_office_id'
					),
				),
				array(
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'type' => 'INNER',
					'conditions' => array(
						'CommodityItem.id = Reservation.commodity_item_id',
					),
				),
				array(
					'table' => 'commodities',
					'alias' => 'Commodity',
					'type' => 'INNER',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id',
					),
				),
				array(
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'type' => 'INNER',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id',
					),
				),
				array(
					'table' => 'car_types',
					'alias' => 'CarType',
					'type' => 'INNER',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id',
					),
				),
				array(
					'table' => "({$subQuery})",
					'alias' => 'ReservationMail',
					'type' => 'LEFT',
					'conditions' => array(
						'ReservationMail.reservation_id = Reservation.id',
					),
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationIds,
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);
		if (empty($result)) {
			return array();
		}
		$result = Hash::combine($result, '{n}.Reservation.id', '{n}');

		$targetDatetime['rent'] = Hash::combine($result, '{n}.Reservation.rent_office_id', '{n}.Reservation.rent_datetime', '{n}.Reservation.id');
		$targetDatetime['return'] = Hash::combine($result, '{n}.Reservation.return_office_id', '{n}.Reservation.return_datetime', '{n}.Reservation.id');
		// 曜日、祝日判定用配列
		$dates = array_merge(Hash::extract($result, '{n}.Reservation.rent_datetime'), Hash::extract($result, '{n}.Reservation.return_datetime'));
		$dates = preg_replace('/ .+$/', '',$dates);
		$dates = array_unique($dates);
		// 営業時間取得
		$this->Office = ClassRegistry::init('Office');
		$officeBusinessDatetime = $this->Office->getOfficeBusinessHoursMulti($targetDatetime, $dates);

		if (!empty($officeBusinessDatetime)) {
			foreach ($result as $reservationId => $reservationData) {
				// 出発営業所営業時間取得
				$date = date('Y-m-d',strtotime($reservationData['Reservation']['rent_datetime']));
				$result[$reservationId]['RentOffice'] = array_merge($result[$reservationId]['RentOffice'], $officeBusinessDatetime[$reservationData['RentOffice']['id']][$date]);
				// 返却営業所営業時間取得
				$date = date('Y-m-d',strtotime($reservationData['Reservation']['return_datetime']));
				$result[$reservationId]['ReturnOffice'] = array_merge($result[$reservationId]['ReturnOffice'], $officeBusinessDatetime[$reservationData['ReturnOffice']['id']][$date]);
			}
		}

		// 基本料金・免責補償料金・乗り捨て料金・深夜手数料
		$options = array(
			'conditions' => array(
				'reservation_id' => $reservationIds,
				'detail_type_id' => array('1','4','5','6'),
			),
			'recursive' => -1
		);
		$this->ReservationDetail = new ReservationDetail;
		$reservationDetailData = $this->ReservationDetail->find('all', $options);
		if (!empty($reservationDetailData)) {
			foreach ($reservationDetailData as $reservationId => $reservationDetail) {
				$reservationId = $reservationDetail['ReservationDetail']['reservation_id'];
				$result[$reservationId]['ReservationDetail'][] = $reservationDetail['ReservationDetail'];
			}
		}

		// チャイルドシート
		$options = array(
			'conditions' => array(
				'reservation_id' => $reservationIds,
			),
			'recursive' => -1
		);
		$this->ReservationChildSheet = new ReservationChildSheet;
		$reservationChildSheetData = $this->ReservationChildSheet->find('all', $options);
		if (!empty($reservationChildSheetData)) {
			foreach ($reservationChildSheetData as $reservationId => $reservationChildSheet) {
				$reservationId = $reservationChildSheet['ReservationChildSheet']['reservation_id'];
				$result[$reservationId]['ReservationChildSheet'][] = $reservationChildSheet['ReservationChildSheet'];
			}
		}

		// オプション（特典）
		$options = array(
			'conditions' => array(
				'reservation_id' => $reservationIds,
			),
			'recursive' => -1
		);
		$this->ReservationPrivilege = new ReservationPrivilege;
		$reservationPrivilegeData = $this->ReservationPrivilege->find('all', $options);
		if (!empty($reservationPrivilegeData)) {
			foreach ($reservationPrivilegeData as $key => $reservationPrivilege) {
				$reservationId = $reservationPrivilege['ReservationPrivilege']['reservation_id'];
				$result[$reservationId]['ReservationPrivilege'][] = $reservationPrivilege['ReservationPrivilege'];
			}
		}

		return $result;
	}

	public function getClientPrivilegeListMulti($reservationIds=array()) {
		$options = array(
			'fields' => array(
				'Privilege.id',
				'Privilege.name',
				'Privilege.client_id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'privileges',
					'alias' => 'Privilege',
					'conditions' => 'Reservation.client_id = Privilege.client_id',
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationIds,
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);
		if (!empty($result)) {
			$result = Hash::combine($result, '{n}.Privilege.id', '{n}.Privilege.name', '{n}.Privilege.client_id');
		}

		return $result;
	}

	// getCommodityEquipmentListのreservation_idでの抽出版
	public function getCommodityEquipmentListMulti($reservationIds=array()) {
		$options = array(
			'fields' => array(
				'Reservation.id',
				'Equipment.id',
				'Equipment.name'
			),
			'joins' => array(
				array(
					'type' => "INNER",
					'alias' => "CommodityItem",
					'table' => "commodity_items",
					'conditions' => "CommodityItem.id = Reservation.commodity_item_id"
				),
				array(
					'type' => "INNER",
					'alias' => "Commodity",
					'table' => "commodities",
					'conditions' => "Commodity.id = CommodityItem.commodity_id"
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityEquipment',
					'table' => 'commodity_equipments',
					'conditions' => 'CommodityEquipment.commodity_id = Commodity.id',
				),
				array(
					'type' => 'INNER',
					'table' => 'equipments',
					'alias' => 'Equipment',
					'conditions' => array(
						'Equipment.id = CommodityEquipment.equipment_id',
					),
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationIds,
			),
			'order' => 'Equipment.sort asc',
			'recursive' => -1
		);
		$result = $this->find('all', $options);
		if (!empty($result)) {
			$result = Hash::combine($result, '{n}.Equipment.id', '{n}.Equipment.name', '{n}.Reservation.id');
		}
		return $result;
	}


}

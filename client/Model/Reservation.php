<?php
App::uses('AppModel','Model');
App::import('Model','Statistic');
App::import('Model','Office');
App::import('Model','Client');
App::import('Model','TourReservation');
require_once("encrypt_class.php");

/**
 * Reservation Model
 */
class Reservation extends AppModel {
	public $Statistic;
	public $Office;

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			'client_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'reservation_datetime'=>array(
					'datetime'=>array(
							'rule'=>array(
									'datetime'
							)
					)
			),
			'reservation_status_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'commodity_item_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'rent_datetime'=>array(
					'datetime'=>array(
							'rule'=>array(
									'datetime'
							)
					)
			),
			'rent_office_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'return_datetime'=>array(
					'datetime'=>array(
							'rule'=>array(
									'datetime'
							)
					)
			),
			'return_office_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'adults_count'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'children_count'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'infants_count'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'price_span_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'span_count'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'cars_count'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'total_price'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'total_tax'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'amount'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'commodity_json'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
			),
			'last_name'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
			),
			'first_name'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
			),
			'email'=>array(
					'email'=>array(
							'rule'=>array(
									'email'
							)
					)
			),
			'tel'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
			),
			'prefecture_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'need_pickup'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'arrival_airline_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'sent_to_enduser'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'sent_to_client'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'cancel_datetime'=>array(
					'datetime'=>array(
							'rule'=>array(
									'datetime'
							)
					)
			),
			'cancel_contact_method_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'cancel_staff_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'cancel_remark'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
			),
			'staff_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
			),
			'delete_flg'=>array(
					'boolean'=>array(
							'rule'=>array(
									'boolean'
							)
					)
			),
			'sales_price'=>array(
				'numeric'=>array(
						'rule'=>array(
								'numeric'
						)
				)
			),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			'Client'=>array(
					'className'=>'Client',
					'foreignKey'=>'client_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
			'UserSession'=>array(
					'className'=>'UserSession',
					'foreignKey'=>'user_session_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
			'ReservationStatus'=>array(
					'className'=>'ReservationStatus',
					'foreignKey'=>'reservation_status_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
			'CommodityItem'=>array(
					'className'=>'CommodityItem',
					'foreignKey'=>'commodity_item_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
			'Staff'=>array(
					'className'=>'Staff',
					'foreignKey'=>'staff_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			'Contract'=>array(
					'className'=>'Contract',
					'foreignKey'=>'reservation_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			),
			'ReservationChildSheet'=>array(
					'className'=>'ReservationChildSheet',
					'foreignKey'=>'reservation_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			),
			'ReservationDetail'=>array(
					'className'=>'ReservationDetail',
					'foreignKey'=>'reservation_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			),
			'ReservationMail'=>array(
					'className'=>'ReservationMail',
					'foreignKey'=>'reservation_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>array(
							'id',
							'reservation_id',
							'reservation_mail_id',
							'mail_datetime',
							'staff_id',
							'contents'
					),
					'order'=>'mail_datetime desc',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			),
			'ReservationPrivilege'=>array(
					'className'=>'ReservationPrivilege',
					'foreignKey'=>'reservation_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			)
	);

	public function checkClientId($id, $clientId) {
		$options = array(
				'fields'=>'client_id',
				'conditions'=>array(
						'id'=>$id
				),
				'recursive'=>- 1
		);

		$reservation = $this->find('first',$options);

		if($reservation['Reservation']['client_id'] == $clientId) {
			return true;
		} else {
			return false;
		}
	}

	// 指定されたスタッフが編集可能な予約か判定して返す
	public function isEditableByThisStaff($id, $clientId) {

		// システム管理者は必ずOK
		$clientData = $this->_getCurrentUser();
		if ($clientData['is_system_admin']) {
			return true;
		}

		$conditions = array(
			'Reservation.id' => $id,
			'Reservation.client_id' => $clientId,
		);

		$joins = array(
			array(
				'type' => 'INNER',
				'table' => 'office_selection_permissions',
				'alias' => 'OfficeSelectionPermission',
				'conditions' => array(
					'OfficeSelectionPermission.staff_id' => $clientData['id'],
					'OfficeSelectionPermission.office_id = Reservation.rent_office_id',
				)
			),
		);

		$count = $this->find('count', array(
				'conditions' => $conditions,
				'joins' => $joins,
				'recursive' => -1,
			)
		);

		return $count > 0;
	}

	public function getReservationMailData($conditions) {
		$reservationMail = "(
				SELECT
					reservation_mails.*
				FROM
					reservation_mails
				WHERE
					reservation_mails.staff_id = 0
					AND reservation_mails.delete_flg = 0
				ORDER BY
					reservation_mails.created DESC
			)";

		$sql = array_merge($conditions,array(
				'joins'=>array(
						array(
								"type"=>"LEFT",
								"table"=>"commodity_items",
								"alias"=>"CommodityItems",
								"conditions"=>"CommodityItems.id = Reservation.commodity_item_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"commodities",
								"alias"=>"Commodity",
								"conditions"=>"CommodityItems.commodity_id = Commodity.id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"car_classes",
								"alias"=>"CarClasses",
								"conditions"=>"CommodityItems.car_class_id = CarClasses.id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"car_types",
								"alias"=>"CarType",
								"conditions"=>"CarType.id = CarClasses.car_type_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"offices",
								"alias"=>"RentOffices",
								"conditions"=>"RentOffices.id = Reservation.rent_office_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"offices",
								"alias"=>"ReturnOffices",
								"conditions"=>"ReturnOffices.id = Reservation.return_office_id"
						),
						array(
								"type"=>"INNER",
								"table"=>"clients",
								"alias"=>"Client",
								"conditions"=>"Client.id = Reservation.client_id"
						),
						array(
								"type"=>"INNER",
								"table"=>"reservation_statuses",
								"alias"=>"ReservationStatus",
								"conditions"=>"ReservationStatus.id = Reservation.reservation_status_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"{$reservationMail}",
								"alias"=>"ReservationMail",
								"conditions"=>"ReservationMail.reservation_id = Reservation.id"
						)
				)
				,
				'fields'=>array(
						'Reservation.*',
						'Client.*',
						'Commodity.*',
						'ReservationStatus.*',
						'ReservationMail.*',
						'CarClasses.*',
						'CarType.*',
						'RentOffices.*',
						'ReturnOffices.*'
				),
				'recursive'=>- 1
		));

		$reservation = $this->find('first',$sql);


		$this->Office = ClassRegistry::init('Office');
		// 出発営業所営業時間取得
		$rentOfficeTime = $this->Office->getOfficeBusinessHours($reservation['Reservation']['rent_office_id'], date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime'])));
		$reservation['RentOffices']['office_hours_from'] = $rentOfficeTime['start_time'];
		$reservation['RentOffices']['office_hours_to'] = $rentOfficeTime['end_time'];

		// 返却営業所営業時間取得
		$returnOfficeTime = $this->Office->getOfficeBusinessHours($reservation['Reservation']['return_office_id'], date('Y-m-d', strtotime($reservation['Reservation']['return_datetime'])));
		$reservation['ReturnOffices']['office_hours_from'] = $returnOfficeTime['start_time'];
		$reservation['ReturnOffices']['office_hours_to'] = $returnOfficeTime['end_time'];

		// 乗り捨て料金・深夜手数料
		$reservationDetailData = $this->query("
				SELECT
					reservation_details.*,
					detail_types.name
				FROM
					reservation_details
				INNER JOIN
					detail_types
				ON
					reservation_details.detail_type_id = detail_types.id
				WHERE
					reservation_details.reservation_id = ".$reservation['Reservation']['id']."
				AND
					reservation_details.detail_type_id
				IN (4,5)
				");
		$reservationDetailArray = array();
		if (!empty($reservationDetailData)) {
			foreach ($reservationDetailData as $reservationDetail) {
				$reservationDetailArray['ReservationDetail'][] = $reservationDetail['detail_types']['name'];
			}
		}
		if (!empty($reservationDetailArray)) {
			$reservation = array_merge_recursive($reservation, $reservationDetailArray);
		}

		// チャイルドシート
		$reservationChildSheetData = $this->query("
				SELECT
					reservation_child_sheets.*,
					privileges.*
				FROM
					reservation_child_sheets
				INNER JOIN
					privileges
				ON
					reservation_child_sheets.child_sheet_id = privileges.id
				WHERE
					reservation_child_sheets.reservation_id = ".$reservation['Reservation']['id']."
				");
		$reservationChildSheetArray = array();
		if (!empty($reservationChildSheetData)) {
			foreach ($reservationChildSheetData as $reservationChildSheet) {
				$reservationChildSheetArray['ReservationChildSheet'][] = $reservationChildSheet['privileges']['name'].' '.$reservationChildSheet['reservation_child_sheets']['count'];;
			}
		}
		if (!empty($reservationChildSheetArray)) {
			$reservation = array_merge_recursive($reservation, $reservationChildSheetArray);
		}

		// オプション（特典）
		$reservationPrivilegeData = $this->query("
				SELECT
					reservation_privileges.*,
					privileges.*
				FROM
					reservation_privileges
				INNER JOIN
					privileges
				ON
					reservation_privileges.privilege_id = privileges.id
				WHERE
					reservation_privileges.reservation_id = ".$reservation['Reservation']['id']."
				");
		$reservationPrivilegeArray = array();
		if (!empty($reservationPrivilegeData)) {
			foreach ($reservationPrivilegeData as $reservationPrivilege) {
				$reservationPrivilegeArray['ReservationPrivilege'][] = $reservationPrivilege['privileges']['name'].' '.$reservationPrivilege['reservation_privileges']['count'].$reservationPrivilege['privileges']['unit_name'];
			}
		}
		if (!empty($reservationPrivilegeArray)) {
			$reservation = array_merge_recursive($reservation, $reservationPrivilegeArray);
		}

		//$reservation['ReservationChildSheet'] = $this->ReservationChildSheet->childSheetMerge($reservation['Reservation']['id']);
		//$reservation['ReservationPrivilege'] = $this->ReservationPrivilege->privilegeMerge($reservation['Reservation']['id']);

		// 装備
		$Equipment = ClassRegistry::init('Equipment');
		$equipmentList = array(0 => '免責補償') + $Equipment->getCommodityEquipmentList($reservation['Commodity']['id']) + array('transmission_flg' => $reservation['Commodity']['transmission_flg'] ? 'MT車' : 'AT車');
		$reservation['equipment_text'] = implode('、', $equipmentList);

		return $reservation;
	}

	public function getReservationData($conditions, $acceptPrepay) {
		$sql = array_merge($conditions,array(
				'fields'=>array(
						'Reservation.id',
						'Reservation.control_number',
						'Reservation.reservation_status_id',
						'Reservation.reservation_key',
						'Reservation.rent_datetime',
						'Reservation.return_datetime',
						'Reservation.adults_count',
						'Reservation.children_count',
						'Reservation.infants_count',
						'Reservation.cars_count',
						'Reservation.last_name',
						'Reservation.first_name',
						'Reservation.email',
						'Reservation.tel',
						'Reservation.arrival_flight_number',
						'Reservation.departure_flight_number',
						'Reservation.mail_status',
						'Reservation.amount',
						'Reservation.cancel_datetime',
						'Reservation.created',
						'Reservation.sales_price',
						'Commodity.name',
						'Commodity.smoking_flg',
						'Commodity.sales_type',
						'CarClass.name',
						'CarType.name',
						'RentOffice.office_code',
						'RentOffice.name',
						'ReturnOffice.office_code',
						'ReturnOffice.name',
						'ReservationStatus.name'
				),
				'recursive'=>- 1
		));
		if ($acceptPrepay) {
			$sql['fields'][] = 'Reservation.payment_status';
		}

		return $this->find('all',$sql);
	}

	public function getReservationCount($conditions) {
		$options = array_merge($conditions,array(
				'fields'=>array(
						"SUM(Reservation.amount) as total_price",
						"COUNT(cancel_reason_id = 0 OR null) as cancel0_count",
						"COUNT(cancel_reason_id = 1 OR null) as cancel1_count",
						"COUNT(cancel_reason_id = 2 OR null) as cancel2_count",
						"COUNT(cancel_reason_id = 3 OR null) as cancel3_count",
						"COUNT(cancel_reason_id = 4 OR null) as cancel4_count",
						"COUNT(cancel_reason_id = 5 OR null) as cancel5_count",
						"COUNT(cancel_reason_id = 6 OR null) as cancel6_count",
						"COUNT(cancel_reason_id = 7 OR null) as cancel7_count",
						"COUNT(user_agent) as total_count",
						"COUNT((user_agent NOT LIKE '%iPhone%' AND Reservation.user_agent NOT LIKE '%Android%' AND Reservation.user_agent NOT LIKE '%Windows Phone%' AND Reservation.user_agent NOT LIKE '%BlackBerry%') OR null) as pc_count",
						"COUNT((user_agent LIKE '%iPhone%' OR Reservation.user_agent LIKE '%Android%' OR Reservation.user_agent LIKE '%Windows Phone%' OR Reservation.user_agent LIKE '%BlackBerry%') OR null) as sp_count"
				)
		));

		return $this->find('first',$options);
	}

	public function getReservationDataOptions($conditions) {
		$sql = array_merge($conditions,array(
				'joins'=>array(
						array(
								"type"=>"LEFT",
								"table"=>"commodity_items",
								"alias"=>"CommodityItem",
								"conditions"=>"CommodityItem.id = Reservation.commodity_item_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"commodities",
								"alias"=>"Commodity",
								"conditions"=>"CommodityItem.commodity_id = Commodity.id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"car_classes",
								"alias"=>"CarClasses",
								"conditions"=>"CommodityItem.car_class_id = CarClasses.id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"car_types",
								"alias"=>"CarType",
								"conditions"=>"CarType.id = CarClasses.car_type_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"offices",
								"alias"=>"RentOffices",
								"conditions"=>"RentOffices.id = Reservation.rent_office_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"offices",
								"alias"=>"ReturnOffices",
								"conditions"=>"ReturnOffices.id = Reservation.return_office_id"
						),
						array(
								"type"=>"LEFT",
								"table"=>"cancel_reasons",
								"alias"=>"CancelReason",
								"conditions"=>"CancelReason.id = Reservation.cancel_reason_id"
						),
						array(
								"type"=>"INNER",
								"table"=>"clients",
								"alias"=>"Client",
								"conditions"=>"Client.id = Commodity.client_id"
						)
				),
				'fields'=>array(
						'Reservation.id',
						'Reservation.reservation_key',
						'Reservation.rent_datetime',
						'Reservation.return_datetime',
						'Reservation.cars_count',
						'Reservation.amount',
						'Reservation.cancel_datetime',
						'Reservation.cancel_remark',
						'Reservation.cancel_reason_id',
						'Reservation.created',
						'Commodity.name',
						'CarClasses.name',
						'CarType.name',
						'Client.name',
						'RentOffices.name',
						'ReturnOffices.name',
						'CancelReason.reason'
				),
				'recursive'=>- 1
		));

		return $sql;
	}

	public function getExpectedValue($data, $clientId) {
		if(empty($data['stock_group_id'])) {
			return;
		}

		$flg = $this->getDayStandardClientFlg($clientId);

		$fromDatetime = $data['year'] . '-' . $data['month'] . '-01 00:00:00';
		$toDatetime = $data['year'] . '-' . $data['month'] . '-31 23:59:59';

		if($flg) {
			$conditions = array(
				'Reservation.client_id'=>$clientId,
				'Reservation.return_datetime >='=>$fromDatetime,
				'Reservation.return_datetime <='=>$toDatetime,
				'Reservation.cancel_flg'=>0
			);
		} else {
			$conditions = array(
				'Reservation.client_id'=>$clientId,
				'Reservation.rent_datetime >='=>$fromDatetime,
				'Reservation.rent_datetime <='=>$toDatetime,
				'Reservation.cancel_flg'=>0
			);
		}

		$joins = array(
			array(
				'type'=>'LEFT',
				'table'=>'commodity_items',
				'alias'=>'CommodityItem',
				'conditions'=>'Reservation.commodity_item_id = CommodityItem.id'
			),
			array(
				'type'=>'LEFT',
				'table'=>'commodities',
				'alias'=>'Commodity',
				'conditions'=>'Commodity.id = CommodityItem.Commodity_id'
			),
			array(
				'type'=>'LEFT',
				'table'=>'offices',
				'alias'=>'Office',
				'conditions'=>'Office.id = Reservation.rent_office_id'
			),
			array(
				'type'=>'LEFT',
				'table'=>'office_stock_groups',
				'alias'=>'OfficeStockGroup',
				'conditions'=>'Office.id = OfficeStockGroup.office_id'
			)
		);

		$conditions += array(
			'OfficeStockGroup.stock_group_id'=>$data['stock_group_id']
		);

		if(! empty($data['car_class_id'])) {
			$conditions += array(
				'CommodityItem.car_class_id'=>$data['car_class_id']
			);
		}

		if(! empty($data['commodity_group_id'])) {
			$conditions += array(
				'Commodity.commodity_group_id'=>$data['commodity_group_id']
			);
		}

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$joins[] = array(
				'type'=>'LEFT',
				'table'=>'car_classes',
				'alias'=>'CarClass',
				'conditions'=>'CarClass.id = CommodityItem.car_class_id'
			);
			$conditions['OR'] = array(
				array('CarClass.scope' => 0),
				array('CarClass.scope' => $clientData['id'])
			);
		}

		$this->recursive = - 1;
		$options = array(
			'fields'=>array(
				'SUM(Reservation.amount) AS amount',
				'count(Reservation.id) as count'
			),
			'conditions'=>$conditions,
			'joins'=>$joins,
			'recursive'=>- 1
		);

		$options = $this->find('first',$options);

		return $options;
	}

	/**
	 * ステータスを成約にするバッチ
	 * 会社は出発日・返却日が過ぎたら成約に変更
	 */
	public function contractDataInsert() {
		$this->Client = new Client();
		$clients = $this->Client->getClientByConclusionContractCriteria();
		$reservationKeys = [];


		$nowDateTime = date('Y-m-d H:i:s');

		// 貸出日が過ぎた予約のステータス変更
		$options = array(
			'joins'=>array(
					array(
							"type"=>"INNER",
							"table"=>"commodity_items",
							"alias"=>"commodity_items",
							"conditions"=>"commodity_items.id = Reservation.commodity_item_id"
					),
					array(
							"type"=>"INNER",
							"table"=>"commodities",
							"alias"=>"commodities",
							"conditions"=>"commodity_items.commodity_id = commodities.id"
					),
			),
			'fields' => array(
				'Reservation.id AS reservation_id',
				'Reservation.client_id',
				'Reservation.rent_office_id AS office_id',
				'Reservation.rent_datetime',
				'Reservation.return_datetime',
				'Reservation.commodity_item_id',
				'Reservation.price_span_id',
				'Reservation.cars_count',
				'Reservation.amount',
				'Reservation.payment_status',
				'Reservation.reservation_key',
				'commodities.sales_type'
			),
			'conditions' => array(
				'Reservation.rent_datetime <=' => $nowDateTime,
				'Reservation.reservation_status_id' => 1,
				'Reservation.client_id' => $clients['rent']
			),
			'recursive' => - 1
		);

		$reservation = $this->find('all', $options);

		if (!empty($reservation)) {

			$saveArray = array();
			$reservationIds = array();
			foreach ($reservation as $val) {
				if ($val['commodities']['sales_type'] == Constant::SALES_TYPE_AGENT_ORGANIZED) {
					if($val['Reservation']['payment_status'] != 'PAYED') {
						// 募集型かつ未入金の場合は対象外
						continue;
					}
					// それ以外で募集型の場合、予約キーを保持しておく
					$reservationKeys[] = $val['Reservation']['reservation_key'];
				}
				$reservationIds[] = $val['Reservation']['reservation_id'];

				$tmp = array();
				$tmp['reservation_id'] = $val['Reservation']['reservation_id'];
				$tmp['client_id'] = $val['Reservation']['client_id'];
				$tmp['office_id'] = $val['Reservation']['office_id'];
				$tmp['rent_datetime'] = $val['Reservation']['rent_datetime'];
				$tmp['reservation_datetime'] = $val['Reservation']['rent_datetime'];
				$tmp['return_datetime'] = $val['Reservation']['return_datetime'];
				$tmp['commodity_item_id'] = $val['Reservation']['commodity_item_id'];
				$tmp['price_span_id'] = $val['Reservation']['price_span_id'];
				$tmp['cars_count'] = $val['Reservation']['cars_count'];
				$tmp['amount'] = $val['Reservation']['amount'];
				array_push($saveArray,$tmp);
			}
			if ($this->Contract->saveMany($saveArray, array('validate' => false))) {

				//$reservationIds = $this->getDoneReservationIds();

				$updateParam = array(
					'conditions' => array(
						'Reservation.id' => $reservationIds,
						'Reservation.delete_flg' => 0
					),
					'fields' => array(
						'Reservation.reservation_status_id' => 2,
						'Reservation.staff_id' => 0,
						'Reservation.modified' => 'NOW()'
					)
				);

				$this->updateAll($updateParam['fields'], $updateParam['conditions']);
			}
		}

		// 返却日が過ぎた予約のステータス変更
		$options = array(
			'joins'=>array(
					array(
							"type"=>"INNER",
							"table"=>"commodity_items",
							"alias"=>"commodity_items",
							"conditions"=>"commodity_items.id = Reservation.commodity_item_id"
					),
					array(
							"type"=>"INNER",
							"table"=>"commodities",
							"alias"=>"commodities",
							"conditions"=>"commodity_items.commodity_id = commodities.id"
					),
			),
			'fields' => array(
				'Reservation.id AS reservation_id',
				'Reservation.client_id',
				'Reservation.rent_office_id AS office_id',
				'Reservation.rent_datetime',
				'Reservation.return_datetime',
				'Reservation.commodity_item_id',
				'Reservation.price_span_id',
				'Reservation.cars_count',
				'Reservation.amount',
				'Reservation.payment_status',
				'Reservation.reservation_key',
				'commodities.sales_type'
			),
			'conditions' => array(
				'Reservation.return_datetime <=' => $nowDateTime,
				'Reservation.reservation_status_id' => 1,
				'Reservation.client_id' => $clients['return']
			),
			'recursive' => - 1
		);

		$reservation = $this->find('all', $options);

		if (!empty($reservation)) {

			$saveArray = array();
			$reservationIds = array();
			foreach ($reservation as $val) {
				if ($val['commodities']['sales_type'] == Constant::SALES_TYPE_AGENT_ORGANIZED) {
					if($val['Reservation']['payment_status'] != 'PAYED') {
						// 募集型かつ未入金の場合は対象外
						continue;
					}
					// それ以外で募集型の場合、予約キーを保持しておく
					$reservationKeys[] = $val['Reservation']['reservation_key'];
				}
				$reservationIds[] = $val['Reservation']['reservation_id'];

				$tmp = array();
				$tmp['reservation_id'] = $val['Reservation']['reservation_id'];
				$tmp['client_id'] = $val['Reservation']['client_id'];
				$tmp['office_id'] = $val['Reservation']['office_id'];
				$tmp['rent_datetime'] = $val['Reservation']['rent_datetime'];
				$tmp['reservation_datetime'] = $val['Reservation']['rent_datetime'];
				$tmp['return_datetime'] = $val['Reservation']['return_datetime'];
				$tmp['commodity_item_id'] = $val['Reservation']['commodity_item_id'];
				$tmp['price_span_id'] = $val['Reservation']['price_span_id'];
				$tmp['cars_count'] = $val['Reservation']['cars_count'];
				$tmp['amount'] = $val['Reservation']['amount'];
				array_push($saveArray,$tmp);
			}
			if ($this->Contract->saveMany($saveArray, array(
				'validate'=>false
			))) {

				//$reservationIds = $this->getDoneReservationIds();

				$updateParam = array(
					'conditions' => array(
						'Reservation.id' => $reservationIds,
						'Reservation.delete_flg' => 0
					),
					'fields' => array(
						'Reservation.reservation_status_id' => 2,
						'Reservation.staff_id' => 0,
						'Reservation.modified' => 'NOW()'
					)
				);

				$this->updateAll($updateParam['fields'], $updateParam['conditions']);
			}
		}
		// 募集型予約テーブルの予約ステータスも成約にする
		if (!empty($reservationKeys)) {
			// 対象となる予約キーを指定して募集型予約テーブルのステータスを成約に更新
			$this->TourReservation = new TourReservation();
			$updateParam = array(
				'conditions' => array(
					'reservation_key' => $reservationKeys,
				),
				'fields' => array(
					'reservation_status_id' => 2,
				)
			);
			$this->TourReservation->updateAll($updateParam['fields'], $updateParam['conditions']);
		}
	}

	public function getDoneReservationIds() {
		$options = array(
				'fields'=>'reservation_id',
				'conditions'=>array(
						'delete_flg'=>0
				),
				'recursive'=>- 1
		);

		return $this->Contract->find('list',$options);
	}

	public function statisticsDataInsert() {
		$this->Statistic = new Statistic();

		$nowDateTime = date('Y-m-d H:i:s');

		$statisticReserve = $this->Statistic->getStatisticReserve();
		$statisticReserve = implode(',',$statisticReserve);
		if(!empty($statisticReserve)) {
			$reservationsWhere = "AND reservations.id NOT IN(" . $statisticReserve . ")";
		} else {
			$reservationsWhere = '';
		}

		$reservations = $this->query("
				SELECT
					reservations.id,
					reservations.client_id,
					reservations.rent_office_id,
					reservations.rent_datetime,
					reservations.return_datetime,
					reservations.commodity_item_id,
					reservations.reservation_datetime,
					reservations.reservation_status_id,
					reservations.price_span_id,
					reservations.amount,
					reservations.cancel_flg
				FROM
					reservations
				WHERE
					reservations.delete_flg = 0
					AND reservations.reservation_status_id = 2
					AND rent_datetime <= '" . $nowDateTime . "'
					" . $reservationsWhere . "
				ORDER BY
					reservations.id ASC",false);

		$statistics = array();
		foreach($reservations as $reservation) {

			$index = $reservation['reservations']['id'];

			$statistics[$index]['client_id'] = $reservation['reservations']['client_id'];
			$statistics[$index]['reservation_id'] = $reservation['reservations']['id'];
			$statistics[$index]['rent_office_id'] = $reservation['reservations']['rent_office_id'];
			$statistics[$index]['commodity_item_id'] = $reservation['reservations']['commodity_item_id'];
			$statistics[$index]['rent_datetime'] = $reservation['reservations']['rent_datetime'];
			$statistics[$index]['return_datetime'] = $reservation['reservations']['return_datetime'];
			$statistics[$index]['reservation_datetime'] = $reservation['reservations']['reservation_datetime'];
			$statistics[$index]['reservation_status_id'] = $reservation['reservations']['reservation_status_id'];
			$statistics[$index]['price'] = $reservation['reservations']['amount'];
			$statistics[$index]['cancel_flg'] = $reservation['reservations']['cancel_flg'];
			$statistics[$index]['statistic_date'] = substr($reservation['reservations']['rent_datetime'],0,8) . '01';
		}

		$statistics = array_values($statistics);
		if(!empty($statistics)) {
			$this->Statistic->saveMany($statistics,array(
					'validate'=>false
			));
		}
	}

	public function getSaleStatisticsSearch($data, $clientId) {

		// 条件指定フラグ
		$whereFlg = false;

		// 年指定時の処理
		if(! empty($data['Statistic']['year']) && $data['Statistic']['year']) {

			$dateFrom = $data['Statistic']['year'] . '-01-01 00:00:00';
			$dateTo = $data['Statistic']['year'] . '-12-31 23:59:59';
			// $dateWhere = "AND reservations.rent_datetime >= '".$dateFrom."' AND reservations.rent_datetime <= '".$dateTo."'";

			if(! empty($data['Statistic']['month']) && $data['Statistic']['month']) {
				$dateFrom = $data['Statistic']['year'] . '-' . $data['Statistic']['month'] . '-01 00:00:00';
				$dateTo = $data['Statistic']['year'] . '-' . $data['Statistic']['month'] . '-31 23:59:59';
			}

			//成約が返却日基準の場合
			if($this->getDayStandardClientFlg($clientId)) {
				$str = 'return';
				$dateReturnWhere = "AND return_datetime >= '" . $dateFrom . "' AND return_datetime <= '" . $dateTo . "'";
			//成約が出発日基準の場合
			} else {
				$str = 'rent';
				$dateReturnWhere = "AND rent_datetime >= '" . $dateFrom . "' AND rent_datetime <= '" . $dateTo . "'";
			}
		} else {
			$dateWhere = '';
		}

		// 商品指定時の処理
		if(! empty($data['Statistic']['commodity_id'])) {
			$commodityWhere = "AND commodities.id = " . $data['Statistic']['commodity_id'];
			$whereFlg = true;
		} else {
			$commodityWhere = '';
		}

		// 車両クラス指定時の処理
		if(! empty($data['Statistic']['car_class_id'])) {
			$carClassWhere = "AND car_classes.id = " . $data['Statistic']['car_class_id'];
			$whereFlg = true;
		} else {
			$carClassWhere = '';
		}

		// 営業所指定時の処理
		if(! empty($data['Statistic']['office_id'])) {
			$officeWhere = "AND reservations.rent_office_id = " . $data['Statistic']['office_id'];
			$whereFlg = true;
		} else {
			$officeWhere = '';
		}

		$reservationId = '';
		if(! empty($data['Statistic']['csv'])) {

			$reservations = $this->query("
					SELECT
						reservations.id
					FROM
						reservations
					INNER JOIN
						commodity_items on reservations.commodity_item_id = commodity_items.id
					INNER JOIN
						commodities on commodities.id = commodity_items.commodity_id
					WHERE
						reservation_status_id IN (2,3)
						{$dateReturnWhere}
			");

			$reservationId = "";
			foreach($reservations as $reservation) {
				$reservationId .= $reservation['reservations']['id'] . ',';
			}

			$reservationId = ' AND reservations.id IN (' . rtrim($reservationId,',') . ')
			 								AND reservations.reservation_status_id = 2
			 							';
		}

		// 条件がある場合
		if($whereFlg) {
			$statistics = $this->query("
						SELECT
							reservations.client_id,
							SUM(reservations.amount) AS price,
							COUNT(*) AS reservation_count,
							reservations." . $str . "_datetime as statistic_date,
							reservations.reservation_status_id,
							MONTH(reservations." . $str . "_datetime) as month,
							GROUP_CONCAT(reservations.id) as reservation_ids
						FROM
							reservations
						INNER JOIN
							(
								SELECT
									commodity_items.id
								FROM
									commodities,
									car_classes,
									commodity_items
								WHERE
									commodities.delete_flg = 0
									" . $carClassWhere . "
									" . $commodityWhere . "
									AND car_classes.id = commodity_items.car_class_id
									AND commodities.id = commodity_items.commodity_id
							) AS commodity_items
						WHERE
							reservations.client_id = " . $clientId . "
							" . $officeWhere . "
							AND reservations.delete_flg = 0
							" . $dateReturnWhere . "
							AND commodity_items.id = reservations.commodity_item_id
							AND reservations.reservation_status_id IN (2,3)
							" . $reservationId . "
						GROUP BY
							reservations.reservation_status_id,
							MONTH(reservations." . $str . "_datetime)
						ORDER BY
							MONTH(reservations." . $str . "_datetime) ASC",false);

			// 成約ID取得group_concatは文字数制限のため使用しない
			$reserveIds = $this->query("
						SELECT
							reservations.id,
							{$str}_datetime as date
						FROM
							reservations
						,
							(
								SELECT
									commodity_items.id
								FROM
									commodities,
									car_classes,
									commodity_items
								WHERE
									commodities.delete_flg = 0
									" . $carClassWhere . "
									" . $commodityWhere . "
									AND car_classes.id = commodity_items.car_class_id
									AND commodities.id = commodity_items.commodity_id
							) AS commodity_items

						WHERE
							reservations.client_id = " . $clientId . "
							" . $officeWhere . "
							AND reservations.delete_flg = 0
							" . $dateReturnWhere . "
							AND commodity_items.id = reservations.commodity_item_id
							AND reservations.reservation_status_id IN (2,3)
							" . $reservationId . "
						ORDER BY
							MONTH(reservations." . $str . "_datetime) ASC",false);

			if(! empty($reserveIds)) {
				foreach($reserveIds as $reserveId) {
					$key = date('Y-m',strtotime($reserveId['reservations']['date']));
					$reservetion['reservations'][$key][] = $reserveId['reservations']['id'];
				}
			}
		} else {

			$statistics = $this->query("
						SELECT
							reservations.client_id,
							SUM(reservations.amount) AS price,
							COUNT(*) AS reservation_count,
							reservations." . $str . "_datetime as statistic_date,
							reservations.reservation_status_id,
							MONTH(reservations." . $str . "_datetime) as month,
							GROUP_CONCAT(reservations.id) as reservation_ids
						FROM
							reservations
						WHERE
							reservations.client_id = " . $clientId . "
							AND reservations.delete_flg = 0
							" . $dateReturnWhere . "
							AND reservations.reservation_status_id IN (2,3)
							" . $reservationId . "
						GROUP BY
							reservations.reservation_status_id,
							MONTH(reservations." . $str . "_datetime)
						ORDER BY
							MONTH(reservations." . $str . "_datetime) ASC",false);

			// 成約ID取得group_concatは文字数制限のため使用しない
			$reserveIds = $this->query("
						SELECT
							reservations.id,
							{$str}_datetime as date
						FROM
							reservations
						WHERE
							reservations.client_id = " . $clientId . "
							AND reservations.delete_flg = 0
							" . $dateReturnWhere . "
							AND reservations.reservation_status_id IN (2,3)
							" . $reservationId . "
						ORDER BY
							MONTH(reservations." . $str . "_datetime) ASC",false);

			if(! empty($reserveIds)) {
				foreach($reserveIds as $reserveId) {
					$key = date('Y-m',strtotime($reserveId['reservations']['date']));
					$reservetion['reservations'][$key][] = $reserveId['reservations']['id'];
				}
			}
		}

		// 整形
		$staticArray = array();
		foreach($statistics as $statistic) {

			$key = $statistic[0]['month'];
			$status = $statistic['reservations']['reservation_status_id'];
			$staticArray[$key]['reservations'] = $statistic['reservations'];

			if($status == 2) {
				$staticArray[$key][0]['price'] = $statistic[0]['price'];
				$staticArray[$key][0]['reservation_count'] = $statistic[0]['reservation_count'];
				$staticArray[$key][0]['reservation_ids'] = $statistic[0]['reservation_ids'];
				if(! empty($reservetion['reservations'])) {
					$staticArray[$key][0]['reservations'] = $reservetion['reservations'];
				}
			} else if($status == 3) {
				$staticArray[$key][0]['cancel_price'] = $statistic[0]['price'];
				$staticArray[$key][0]['cancel_count'] = $statistic[0]['reservation_count'];
			}
		}

		foreach($staticArray as $key => $statistic) {

			$statisticData = date('Y-m',strtotime($statistic['reservations']['statistic_date']));
			if(! empty($statistic[0]['reservations'][$statisticData])) {

				$count = $this->find('count',array(
						'conditions'=>array(
								'Reservation.client_id'=>$clientId,
								'Reservation.id'=>$statistic[0]['reservations'][$statisticData],
								'Reservation.reservation_status_id'=>2
						),
						'joins'=>array(
								array(
										'type'=>'LEFT',
										'alias'=>'CommodityItem',
										'table'=>'commodity_items',
										'conditions'=>'Reservation.commodity_item_id = CommodityItem.id'
								),
								array(
										'type'=>'LEFT',
										'alias'=>'Commodity',
										'table'=>'commodities',
										'conditions'=>'CommodityItem.commodity_id = Commodity.id'
								)
						),
						'recursive'=>- 1
				));

				if($count != 0) {
					$staticArray[$key][0]['csvViewFlg'] = 1;
				}
			}
		}

		return $staticArray;

		// return $statistics;
	}

	/**
	 * バッチの機能
	 * 「成約」「キャンセル」のお客様の返信状況変更
	 */
	public function batchMailStatus() {
		$options = array(
				'fields'=>array(
						'id',
						'Reservation.reservation_status_id',
						'Reservation.mail_status',
						'rent_datetime',
						'return_datetime'
				),
				'conditions'=>array(
						'Reservation.reservation_status_id'=>array(
								2,
								3
						),
						'Reservation.mail_status'=>0
				),
				'recursive'=>- 1
		);
		$reservation = $this->find('all',$options);

		if(!empty($reservation)) {
			foreach($reservation as $key => $val) {
				$reservation[$key]['Reservation']['mail_status'] = 2;
			}
			if($this->saveMany($reservation,array( 'callbacks'=>'before'))) {
				$this->log('mail_status success');
			} else {
				$this->log('mai_status error');
			}
		}
	}

	/**
	 * 予約日ごとの予約・成約・キャンセル数
	 */
	function getReservedOperand($fromDate, $toDate, $salesType, $statusId = 1) {
		$fields = "DATE_FORMAT(Reservation.rent_datetime, '%Y') as year,
			DATE_FORMAT(Reservation.rent_datetime, '%c') as month";

		$group = "DATE_FORMAT(Reservation.rent_datetime, '%Y%m')";

		$this->recursive = - 1;
		return $this->find('all', array(
			'conditions' => array(
				'Reservation.reservation_status_id' => $statusId,
				'Reservation.created >=' => $fromDate . ' 00:00:00',
				'Reservation.created <=' => $toDate . ' 23:59:59',
				'Commodity.sales_type' => $salesType
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
				),
				array(
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => 'Commodity.id = CommodityItem.commodity_id'
				)
			),
			'fields' => array(
				"SUM(Reservation.amount) as price",
				"COUNT(Reservation.id) as count",
				'Reservation.reservation_status_id',
				"Reservation.client_id",
				$fields
			),
			'group' => array(
				$group
			)
		));
	}

	/**
	 * 貸出日ごとの予約・成約・キャンセル数
	 */
	function getProceeds($searchData, $clientId, $salesType) {
		$conditions = array(
			'Commodity.sales_type' => $salesType
		);

		if (!empty($searchData['region_link_cd'])) {
			$conditions += array(
				'Prefecture.region_link_cd' => $searchData['region_link_cd']
			);
		}

		if (!empty($searchData['office_id'])) {
			$conditions += array(
				'Reservation.rent_office_id' => $searchData['office_id']
			);
		}

		if (!empty($searchData['car_class_id'])) {
			$conditions += array(
				'CommodityItem.car_class_id' => $searchData['car_class_id']
			);
		}

		if (!empty($searchData['car_type_id'])) {
			$conditions += array(
				'CarClass.car_type_id' => $searchData['car_type_id']
			);
		}

		if (!$this->getDayStandardClientFlg($clientId)) {

			// 貸出日基準のクライアント
			if (empty($searchData['month'])) {
				$rentDateTime = "%" . $searchData['year'] . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%c') as date";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m')";
			} else {
				$rentDateTime = "%" . $searchData['year'] . '-' . $searchData['month'] . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%e') as date";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m%d')";
			}

			$conditions += array(
				'Reservation.reservation_status_id <>' => 0,
				'Reservation.rent_datetime like' => $rentDateTime,
				'Reservation.client_id' => $clientId
			);

			return $this->getProceedsExec($conditions, $fields, $group);
		} else {

			// 返却日基準のクライアント

			// 予約・成約
			if (empty($searchData['month'])) {
				$rentDateTime = "%" . $searchData['year'] . "%";
				$fields = "DATE_FORMAT(return_datetime, '%c') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m')";
			} else {
				$rentDateTime = "%" . $searchData['year'] . '-' . $searchData['month'] . "%";
				$fields = "DATE_FORMAT(return_datetime, '%e') as date";
				$group = "DATE_FORMAT(return_datetime, '%Y%m%d')";
			}

			$data1Conditions = array_merge($conditions, array(
				'Reservation.reservation_status_id <>' => 3,
				'Reservation.return_datetime like' => $rentDateTime,
				'Reservation.client_id' => $clientId
			));

			// 予約・成約
			$data1 = $this->getProceedsExec($data1Conditions, $fields, $group);

			// キャンセル
			if (empty($searchData['month'])) {
				$rentDateTime = "%" . $searchData['year'] . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%c') as date";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m')";
			} else {
				$rentDateTime = "%" . $searchData['year'] . '-' . $searchData['month'] . "%";
				$fields = "DATE_FORMAT(rent_datetime, '%e') as date";
				$group = "DATE_FORMAT(rent_datetime, '%Y%m%d')";
			}

			$data2Conditions = array_merge($conditions, array(
				'Reservation.reservation_status_id' => 3,
				'Reservation.rent_datetime like' => $rentDateTime,
				'Reservation.client_id' => $clientId
			));

			$data2 = $this->getProceedsExec($data2Conditions, $fields, $group);

			return array_merge($data1, $data2);
		}
	}

	/**
	 * クライアントの成約が貸出日基準か返却日基準か判定
	 * true : 返却日基準
	 * false : 貸出日基準
	 */
	function getDayStandardClientFlg($clientId) {
		$this->Client = new Client();
		$clients = $this->Client->getClientByConclusionContractCriteria();

		if(!empty($clients['rent'][$clientId])) {
			return false;
		} else {
			return true;
		}
	}

	// 実行
	function getProceedsExec($conditions, $fields, $group) {
		$joins = array(
			array(
				'type' => 'INNER',
				'table' => 'commodity_items',
				'alias' => 'CommodityItem',
				'conditions' => 'Reservation.commodity_item_id = CommodityItem.id'
			),
			array(
				'type' => 'INNER',
				'table' => 'commodities',
				'alias' => 'Commodity',
				'conditions' => 'Commodity.id = CommodityItem.commodity_id'
			),
			array(
				'type' => 'INNER',
				'table' => 'offices',
				'alias' => 'Office',
				'conditions' => 'Office.id = Reservation.rent_office_id'
			),
			array(
				'type' => 'INNER',
				'table' => 'areas',
				'alias' => 'Area',
				'conditions' => 'Area.id = Office.area_id'
			),
			array(
				'type' => 'INNER',
				'table' => 'prefectures',
				'alias' => 'Prefecture',
				'conditions' => 'Prefecture.id = Area.prefecture_id'
			),
			array(
				'type' => 'LEFT',
				'table' => 'office_stock_groups',
				'alias' => 'OfficeStockGroup',
				'conditions' => 'Office.id = OfficeStockGroup.office_id'
			),
			array(
				'type' => 'LEFT',
				'table' => 'clients',
				'alias' => 'Client',
				'conditions' => 'Client.id = Reservation.client_id'
			)
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_system_admin']) {
			$joins[] = array(
				'type' => 'INNER',
				'table' => 'office_selection_permissions',
				'alias' => 'OfficeSelectionPermission',
				'conditions' => array(
					'OfficeSelectionPermission.staff_id' => $clientData['id'],
					'OfficeSelectionPermission.office_id = Reservation.rent_office_id'
				)
			);
		}
		if (!$clientData['is_client_admin']) {
			$joins = array_merge($joins, array(
				array(
					'type' => 'INNER',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id',
						'OR' => array(
							array('CarClass.scope' => 0),
							array('CarClass.scope' => $clientData['id'])
						)
					)
				),
				array(
					'type' => 'LEFT',
					'table' => 'commodity_groups',
					'alias' => 'CommodityGroup',
					'conditions'=>array(
						'CommodityGroup.id = Commodity.commodity_group_id',
						'OR'=>array(
							array('CommodityGroup.scope' => 0),
							array('CommodityGroup.scope' => $clientData['id'])
						)
					)
				)
			));
			$conditions[] = array('OR' => array(
					array('Commodity.commodity_group_id IS NOT NULL', 'CommodityGroup.id IS NOT NULL'),
					array('Commodity.commodity_group_id IS NULL', 'CommodityGroup.id IS NULL')
				)
			);
		} else {
			$joins[] = array(
				'type' => 'INNER',
				'table' => 'car_classes',
				'alias' => 'CarClass',
				'conditions' => array(
					'CarClass.id = CommodityItem.car_class_id'
				)
			);
		}

		return $this->find('all', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => array(
				'SUM(Reservation.amount) as price',
				'COUNT(Reservation.id) as count',
				'Reservation.reservation_status_id',
				'Reservation.client_id',
				"truncate(SUM(Reservation.amount) * (Client.commission_rate / 100),0) as commission",
				$fields
			),
			'group' => array(
				'Reservation.client_id',
				'Reservation.reservation_status_id',
				$group
			)
		));
	}

	/**
	 * 予約日ごとの予約・成約・キャンセル数
	 */
	function getReservedCount($searchData, $clientId, $salesType) {
		if (!empty($searchData['day'])) {
			$rentDateTime = "%" . $searchData['year'] . '-' . $searchData['month'] . '-' . $searchData['day'] . "%";
			$fields = "DATE_FORMAT(Reservation.created, '%k') as date";
			$group = "DATE_FORMAT(Reservation.created, '%Y%m%d%h')";
		} else if (!empty($searchData['month'])) {
			$rentDateTime = "%" . $searchData['year'] . '-' . $searchData['month'] . "%";
			$fields = "DATE_FORMAT(Reservation.created, '%e') as date";
			$group = "DATE_FORMAT(Reservation.created, '%Y%m%d')";
		} else {
			$rentDateTime = "%" . $searchData['year'] . "%";
			$fields = "DATE_FORMAT(Reservation.created, '%c') as date";
			$group = "DATE_FORMAT(Reservation.created, '%Y%m')";
		}

		$conditions = array(
			'Reservation.reservation_status_id <>' => 0,
			'Reservation.created like' => $rentDateTime,
			'Reservation.client_id' => $clientId,
			'Commodity.sales_type' => $salesType
		);

		if (!empty($searchData['region_link_cd'])) {
			$conditions += array(
				'Prefecture.region_link_cd' => $searchData['region_link_cd']
			);
		}

		if (!empty($searchData['office_id'])) {
			$conditions += array(
				'Reservation.rent_office_id' => $searchData['office_id']
			);
		}

		if (!empty($searchData['car_class_id'])) {
			$conditions += array(
				'CommodityItem.car_class_id' => $searchData['car_class_id']
			);
		}

		if (!empty($searchData['car_type_id'])) {
			$conditions += array(
				'CarClass.car_type_id' => $searchData['car_type_id']
			);
		}

		$joins = array(
			array(
				'type' => 'INNER',
				'table' => 'commodity_items',
				'alias' => 'CommodityItem',
				'conditions' => 'Reservation.commodity_item_id = CommodityItem.id'
			),
			array(
				'type' => 'INNER',
				'table' => 'commodities',
				'alias' => 'Commodity',
				'conditions'=>'Commodity.id = CommodityItem.commodity_id'
			),
			array(
				'type' => 'INNER',
				'table' => 'offices',
				'alias' => 'Office',
				'conditions' => 'Office.id = Reservation.rent_office_id'
			),
			array(
				'type' => 'INNER',
				'table' => 'areas',
				'alias' => 'Area',
				'conditions' => 'Area.id = Office.area_id'
			),
			array(
				'type' => 'INNER',
				'table' => 'prefectures',
				'alias' => 'Prefecture',
				'conditions' => 'Prefecture.id = Area.prefecture_id'
			),
		);

		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_system_admin']) {
			$joins[] = array(
				'type' => 'INNER',
				'table' => 'office_selection_permissions',
				'alias' => 'OfficeSelectionPermission',
				'conditions' => array(
					'OfficeSelectionPermission.staff_id' => $clientData['id'],
					'OfficeSelectionPermission.office_id = Reservation.rent_office_id'
				)
			);
		}
		if (!$clientData['is_client_admin']) {
			$joins = array_merge($joins, array(
				array(
					'type' => 'INNER',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions'=>array(
						'CarClass.id = CommodityItem.car_class_id',
						'OR' => array(
							array('CarClass.scope' => 0),
							array('CarClass.scope' => $clientData['id'])
						)
					)
				),
				array(
					'type' => 'LEFT',
					'table' => 'commodity_groups',
					'alias' => 'CommodityGroup',
					'conditions' => array(
						'CommodityGroup.id = Commodity.commodity_group_id',
						'OR' => array(
							array('CommodityGroup.scope' => 0),
							array('CommodityGroup.scope' => $clientData['id'])
						)
					)
				)
			));
			$conditions[] = array('OR' => array(
					array('Commodity.commodity_group_id IS NOT NULL', 'CommodityGroup.id IS NOT NULL'),
					array('Commodity.commodity_group_id IS NULL', 'CommodityGroup.id IS NULL')
				)
			);
		} else {
			$joins[] = array(
				'type' => 'INNER',
				'table' => 'car_classes',
				'alias' => 'CarClass',
				'conditions' => array(
					'CarClass.id = CommodityItem.car_class_id'
				)
			);
		}

		return $this->find('all', array(
			'conditions' => $conditions,
			'fields' => array(
				"SUM(Reservation.amount) as price",
				"COUNT(Reservation.id) as count",
				'Reservation.reservation_status_id',
				"Reservation.client_id",
				$fields
			),
			'joins' => $joins,
			'group' => array(
				"client_id",
				$group
			)
		));
	}

	// 商品と商品に紐づくカークラスを取得
	public function getCommodityAndCarClassName($reservationIds, $clientId) {
		$commodityLists = $this->find('all',array(
				'conditions'=>array(
						'Reservation.id'=>$reservationIds,
						'Reservation.client_id'=>$clientId
				),
				'joins'=>array(
						array(
								'type'=>'LEFT',
								'alias'=>'CommodityItem',
								'table'=>'commodity_items',
								'conditions'=>'Reservation.commodity_item_id = CommodityItem.id'
						),
						array(
								'type'=>'LEFT',
								'alias'=>'Commodity',
								'table'=>'commodities',
								'conditions'=>'Commodity.id = CommodityItem.commodity_id'
						),
						array(
								'type'=>'LEFT',
								'alias'=>'CarClass',
								'table'=>'car_classes',
								'conditions'=>'CarClass.id = CommodityItem.car_class_id'
						),
						array(
								'type'=>'LEFT',
								'alias'=>'CarType',
								'table'=>'car_types',
								'conditions'=>'CarType.id = CarClass.car_type_id'
						)
				),
				'fields'=>array(
						'Commodity.id',
						'Commodity.name',
						'CarType.id',
						'CarType.name',
						'CarClass.*',
						'CommodityItem.*'
				),
				'recursive'=>- 1
		));

		$commodityList = array();
		if(! empty($commodityLists)) {
			foreach($commodityLists as $val) {
				$key = $val['Commodity']['id'];
				$commodityList[$key] = $val;
			}
		}

		return $commodityList;
	}

	// 電話番号からユーザーの利用回数を取得
	public function getReserveCountList($telList) {
		$reserveCountList = $this->find('all',array(
				'conditions'=>array(
						'tel'=>$telList
				),
				'fields'=>array(
						'tel',
						'count(Reservation.id) as count'
				),
				'group'=>array(
						'tel having count(*) >= 1'
				),
				'recursive'=>- 1
		));

		return Set::Combine($reserveCountList,'{n}.Reservation.tel','{n}.0.count');
	}

	public function getReservationFirstData($reservationId) {
		$options = array(
				'fields'=>array(
						'Reservation.*',
						'CommodityItem.*',
						'Commodity.*',
						'Client.*'
				),
				'joins'=>array(
						array(
								'table'=>'commodity_items',
								'alias'=>'CommodityItem',
								'type'=>'LEFT',
								'conditions'=>array(
										'CommodityItem.id = Reservation.commodity_item_id'
								)
						),
						array(
								'table'=>'commodities',
								'alias'=>'Commodity',
								'type'=>'LEFT',
								'conditions'=>array(
										'Commodity.id = CommodityItem.commodity_id'
								)
						),
						array(
								'table'=>'clients',
								'alias'=>'Client',
								'type'=>'LEFT',
								'conditions'=>array(
										'Reservation.client_id = Client.id'
								)
						)
				),
				'conditions'=>array(
						'Reservation.id'=>$reservationId,
						'Reservation.delete_flg'=>0
				),
				'recursive'=>- 1
		);
		$result = $this->find('first',$options);

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

	/**
	 * payment_statusを「返金期限切れ」にする(条件:入金ステータスが「返金依頼受付中」で、出発日から3ヶ月が経過している)
	 */
	public function paymentStatusToRefundExpired() {
		$nowDateTime = date('Y-m-d H:i:s');

		$options = array(
			'fields' => array(
				'Reservation.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'clients',
					'alias' => 'Client',
					'conditions' => 'Client.id = Reservation.client_id'
				),
			),
			'conditions' => array(
				'Reservation.payment_status' => 'TMP_REFUND_REQUEST', // 返金依頼受付中
				'Reservation.return_datetime <= ' => date('Y-m-d H:i:s', strtotime('-3 month')),
				'Reservation.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'group' => 'Reservation.id',
			'recursive' => - 1
		);

		$reservation = $this->find('all', $options);

		if (!empty($reservation)) {
			$ids = Hash::extract($reservation, '{n}.Reservation.id');
			if (is_array($ids) && count($ids) == 1) {
				$ids = $ids[0];
			}

			$updateParam = array(
				'conditions' => array(
					'Reservation.id =' => $ids,
				),
				'fields' => array(
					'Reservation.payment_status' => "'REFUND_EXPIRED'"
				)
			);

			$this->updateAll($updateParam['fields'], $updateParam['conditions']);
		}
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

	public function getCmApplicationId($reservationId)
	{
		$options = [
			'fields' => [
				'cmThApplicationDetail.cm_application_id'
			],
			'joins' => [
				[
					'type' => 'inner',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'cmThApplicationDetail',
					'conditions' => 'cmThApplicationDetail.application_id = Reservation.id'
				],
				[
					'type' => 'inner',
					'table' => 'skyticket.cm_th_application',
					'alias' => 'cmThApplication',
					'conditions' => 'cmThApplication.cm_application_id = cmThApplicationDetail.cm_application_id'
				],
			],
			'conditions' => [
				'cmThApplicationDetail.service_cd' => 'rc',
				'cmThApplicationDetail.application_id' => $reservationId,
			],
			'recursive' => - 1
		];
		$result = $this->find('first', $options);

		$cm_application_id = '';
		if (isset($result['cmThApplicationDetail']['cm_application_id'])) {
			$cm_application_id = $result['cmThApplicationDetail']['cm_application_id'];
		}

		return $cm_application_id;
	}

	public function getReservationDataForMail($reservationId)
	{
		$db = $this->getDataSource();

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
			),
			'conditions' => array(
				'Reservation.id' => $reservationId,
				'Reservation.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$result = $this->find('first', $options);
		return $result;
	}
}

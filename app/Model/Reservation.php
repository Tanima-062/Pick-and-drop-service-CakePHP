<?php

App::uses('AppModel', 'Model');
require_once("encrypt_class.php");

class Reservation extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'client_id' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'user_session_id' => array(
		//'numeric' => array(
		//'rule' => array('numeric'),
		//'message' => 'Your custom message here',
		//'allowEmpty' => false,
		//'required' => false,
		//'last' => false, // Stop validation after this rule
		//'on' => 'create', // Limit validation to 'create' or 'update' operations
		//),
		),
		'reservation_datetime' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'reservation_status_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'reservation_key' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'reservation_hash' => array(
			'notempty' => array(
				'rule' => array('notblank'),
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
		'rent_datetime' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'rent_office_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'return_datetime' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'return_office_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'adults_count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'children_count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'infants_count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cars_count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'commodity_json' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'last_name' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'first_name' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => "メールアドレスの入力形式が間違っています。<br>\n「..」、「.@」、「スペース（空白）」が含まれるアドレスは使用できません。<br>\n",
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_send_mail' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'tel' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => '電話番号が不正です。',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'mail_status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cancel_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cancel_datetime' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cancel_staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cancel_remark' => array(
			'notempty' => array(
				'rule' => array('notblank'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'cancel_reason_id' => array(
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
		'RentOffice' => array(
			'className' => 'RentOffice',
			'foreignKey' => 'rent_office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ReturnOffice' => array(
			'className' => 'ReturnOffice',
			'foreignKey' => 'return_office_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CancelStaff' => array(
			'className' => 'CancelStaff',
			'foreignKey' => 'cancel_staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CancelReason' => array(
			'className' => 'CancelReason',
			'foreignKey' => 'cancel_reason_id',
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
			'fields' => '',
			'order' => '',
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
		),
	);

	/**
	 * ハッシュキーのユニークチェック
	 * @param unknown $hashKey
	 * @return unknown
	 */
	public function uniqueCheckHashKey($hashKey) {
		$options = array(
			'fields' => array(
				'COUNT(*) as count'
			),
			'conditions' => array(
				'reservation_hash' => $hashKey
			),
			'recursive' => -1
		);
		$result = $this->find('first', $options);
		return $result[0]['count'];
	}

	// 予約番号のユニークチェック
	public function uniqueCheckReservationKey($reservation_key = null) {
		$options = array(
			'conditions' => array(
				'Reservation.reservation_key' => $reservation_key,
				'Reservation.created >' => date('Y-m-d H:i:s', strtotime('-5 min')),
			),
			'recursive' => -1,
		);
		return $this->find('first', $options);
	}

	public function getMaxReservationKey($reserveTag) {
		$maxReservationKey = $this->query("
				SELECT
					CASE WHEN
						MAX(SUBSTRING(reservation_key,3,12)) IS NULL
					THEN
						LPAD(1,11,'0')
					ELSE
						LPAD(MAX(SUBSTRING(reservation_key,3,12))+1,11,'0')
					END AS reservation_key
				FROM
					rentacar.reservations
				WHERE
					SUBSTRING(reservation_key,1,2) = :reserveTag", array('reserveTag' => $reserveTag), false);
		return $reserveTag . $maxReservationKey[0][0]['reservation_key'];
	}

	public function getReservationData($reservationId) {
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

	public function getReserveKeyByHash($hash) {
		$options = array(
			'conditions' => array(
				'reservation_hash' => $hash,
				'delete_flg' => 0
			),
			'recursive' => -1
		);
		return $this->find('first', $options);
	}

	/**
	 * ログイン画面
	 * マイページ用
	 */
	public function getMyPageLogin($reservationKey, $tel) {
		$options = array(
			'conditions' => array(
				'Reservation.reservation_key' => $reservationKey,
				'Reservation.tel' => $tel,
			),
			'recursive' => -1,
		);
		$result = $this->find('first', $options);
		return $result;
	}

	public function getMypageDatas($reservationKey, $tel) {
		$options = array(
			'fields' => array(
				'Reservation.*',
				'Client.*',
				'CommodityItem.*',
				'CarClass.*',
				'CarType.*',
				'Commodity.*',
				'RentOffice.*',
				'ReturnOffice.*',
				'CommodityTerm.*',
				'RentOfficeSupplement.method_of_transport',
				'ReturnOfficeSupplement.method_of_transport',
			),
			'joins' => array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
					'conditions' => array(
						'Client.id = Reservation.client_id'
					)
				),
				array(
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'type' => 'LEFT',
					'conditions' => array(
						'CommodityItem.id = Reservation.commodity_item_id'
					)
				),
				array(
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'type' => 'LEFT',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id'
					)
				),
				array(
					'table' => 'car_types',
					'alias' => 'CarType',
					'type' => 'LEFT',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id'
					)
				),
				array(
					'table' => 'commodities',
					'alias' => 'Commodity',
					'type' => 'LEFT',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					)
				),
				array(
					'table' => 'offices',
					'alias' => 'RentOffice',
					'type' => 'LEFT',
					'conditions' => array(
						'RentOffice.id = Reservation.rent_office_id'
					)
				),
				array(
					'table' => 'offices',
					'alias' => 'ReturnOffice',
					'type' => 'LEFT',
					'conditions' => array(
						'ReturnOffice.id = Reservation.return_office_id'
					)
				),
				array(
					'table' => 'commodity_terms',
					'alias' => 'CommodityTerm',
					'type' => 'LEFT',
					'conditions' => array(
						'CommodityTerm.commodity_id = Commodity.id'
					)
				),
				array(
					'table' => 'office_supplements',
					'alias' => 'RentOfficeSupplement',
					'type' => 'LEFT',
					'conditions' => array(
						'RentOfficeSupplement.office_id = RentOffice.id'
					)
				),
				array(
					'table' => 'office_supplements',
					'alias' => 'ReturnOfficeSupplement',
					'type' => 'LEFT',
					'conditions' => array(
						'ReturnOfficeSupplement.office_id = ReturnOffice.id'
					)
				),
			),
			'conditions' => array(
				'Reservation.reservation_key' => $reservationKey,
				'Reservation.tel' => $tel,
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

		return $result;
	}

	/**
	 * メールアドレスで再送予約件数を取得
	 */
	public function getResendEmailAll($email) {

		// タイムゾーンを日本に設定する
		date_default_timezone_set('Asia/Tokyo');
		$today = date('Y-m-d H:i:s');

		$options = array(
			'conditions' => array(
				'Reservation.email' => $email,
				'Reservation.rent_datetime >=' => $today,
				'Reservation.reservation_status_id != 3',
				'Reservation.delete_flg' => 0,
			),
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

	/**
	 * 予約連携API送信データ(reservation)取得
	 */
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
	 * レンナビステータス復元
	 */
	public function rennaviStatusRestore($clientId, $nowDatetime, $rentOfficeIds, $isRegistered = true) {
		if ($isRegistered) {
			$afterStatus = Constant::RENNAVI_STATUS_CANCEL_USER;
		} else {
			$afterStatus = Constant::RENNAVI_STATUS_CANCEL_NOTIME;
		}
		$sql = "
			UPDATE
				reservations r
			SET
				r.rennavi_status = ".$afterStatus."
			WHERE
				r.client_id = ".$clientId." AND r.rennavi_status = ".Constant::RENNAVI_STATUS_CXL_RESERVE_FIXING." AND
				r.registered_flg = ".($isRegistered ? 1 : 0)." AND r.return_datetime >= '".date('Y-m-01 00:00:00', strtotime($nowDatetime.' -3 month'))."'
		";
		if (!empty($rentOfficeIds)) {
			$sql .= "AND r.rent_office_Id IN (".implode(',', $rentOfficeIds).")";
		}

		$this->query($sql);

		return $this->getAffectedRows();
	}

	/**
	 * レンナビ予約件数取得
	 */
	public function rennaviReserveCount($clientId, $rennaviStatus, $nowDatetime, $rentOfficeIds) {
		$options = array(
			'fields' => array(
				'Reservation.id'
			),
			'conditions' => array(
				'Reservation.client_id' => $clientId,
				'Reservation.rennavi_status' => $rennaviStatus,
				'Reservation.return_datetime >=' => date('Y-m-01 00:00:00', strtotime($nowDatetime.' -3 month')),
			),
			'recursive' => -1,
		);
		if (!empty($rentOfficeIds)) {
			$options['conditions']['Reservation.rent_office_id'] = $rentOfficeIds;
		}
		// Jネットのみ、予約登録日時が指定日時以降の分に制限する
		if ($clientId == Constant::JNET_CLIENT_ID) {
			$options['conditions']['Reservation.created >='] = '2023-08-22 00:00:00';	// 予約登録日時 >= Jネット連携開始日
		}

		return $this->find('all', $options);
	}

	/**
	 * レンナビ予約件数取得（トランザクション登録）
	 */
	public function rennaviReserveEntry($transactionId, $clientId, $rennaviStatus, $nowDatetime, $rentOfficeIds) {
		// 検索条件は rennaviReserveCount() と同じ
		$sql = "
			UPDATE
				reservations r
			SET
				r.rennavi_one_time_pass = ".$transactionId."
			WHERE
				r.client_id = ".$clientId." AND r.rennavi_status IN (".implode(',', $rennaviStatus).") AND
				r.return_datetime >= '".date('Y-m-01 00:00:00', strtotime($nowDatetime.' -3 month'))."'
		";
		if (!empty($rentOfficeIds)) {
			$sql .= "AND r.rent_office_Id IN (".implode(',', $rentOfficeIds).")";
		}
		// Jネットのみ、予約登録日時が指定日時以降の分に制限する
		if ($clientId == Constant::JNET_CLIENT_ID) {
			$sql .= "AND r.created >= '2023-08-22 00:00:00'";	// 予約登録日時 >= Jネット連携開始日
		}

		$this->query($sql);

		return $this->getAffectedRows();
	}

	/**
	 * レンナビ予約ダウンロード
	 */
	public function rennaviReserveDownload($transactionId) {
		$options = array(
			'fields' => array(
				'Reservation.id',
				'Reservation.client_id',
				'Reservation.reservation_key',
				'Reservation.reservation_status_id',
				'Reservation.rennavi_status',
				'Reservation.last_name',
				'Reservation.first_name',
				'Reservation.adults_count',
				'Reservation.children_count',
				'Reservation.infants_count',
				'Reservation.tel',
				'Reservation.arrival_flight_number',
				'Reservation.departure_flight_number',
				'Reservation.rent_datetime',
				'Reservation.return_datetime',
				'Reservation.amount',
				'Reservation.payment_status',
				'Reservation.created',
				'Reservation.cancel_datetime',
				'RentOffice.id',
				'RentOffice.name',
				'ReturnOffice.id',
				'ReturnOffice.name',
				'CarClass.id',
				'CarClass.name',
				'Commodity.id',
				'Commodity.name',
				'Commodity.transmission_flg',
				'Commodity.new_car_registration',
				'Client.accept_card',
				'Client.accept_cash',
				'CarModel.name',
				'RentOfficeSupplement.method_of_transport',
				'RentOfficeSupplement.pickup_method',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'RentOffice',
					'conditions' => array(
						'RentOffice.id = Reservation.rent_office_id',
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'ReturnOffice',
					'conditions' => array(
						'ReturnOffice.id = Reservation.return_office_id',
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => array(
						'CommodityItem.id = Reservation.commodity_item_id',
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id',
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
					'table' => 'clients',
					'alias' => 'Client',
					'conditions' => array(
						'Client.id = Reservation.client_id',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'car_models',
					'alias' => 'CarModel',
					'conditions' => array(
						'CarModel.id = CommodityItem.car_model_id',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'office_supplements',
					'alias' => 'RentOfficeSupplement',
					'conditions' => array(
						'RentOfficeSupplement.office_id = RentOffice.id',
					),
				),
			),
			'conditions' => array(
				'Reservation.rennavi_one_time_pass' => $transactionId,
				'rennavi_status NOT IN' => array(
					Constant::RENNAVI_STATUS_EXCLUDED,
					Constant::RENNAVI_STATUS_RESERVE_FIXED,
					Constant::RENNAVI_STATUS_CANCEL_FIXED
				)
			),
			'order' => array(
				'Reservation.id' => 'asc',
			),
			'recursive' => -1,
		);

		$reservations = $this->find('all', $options);

		$reservationIds = Hash::extract($reservations, '{n}.Reservation.id');
		$commodityIds = array_unique(Hash::extract($reservations, '{n}.Commodity.id'));

		$reservationDetail = ClassRegistry::init('ReservationDetail');
		$commodityEquipment = ClassRegistry::init('CommodityEquipment');
		$reservationPrivilege = ClassRegistry::init('ReservationPrivilege');
		$reservationChildSheet = ClassRegistry::init('ReservationChildSheet');

		$details = $reservationDetail->getDetailsByReservationIds($reservationIds);
		$details = Hash::combine($details, '{n}.ReservationDetail.detail_type_id', '{n}.ReservationDetail', '{n}.ReservationDetail.reservation_id');
	
		$equipments = $commodityEquipment->getCommodityEquipmentByCommodityIds($commodityIds);
		$equipments = Hash::combine($equipments, '{n}.CommodityEquipment.equipment_id', '{n}.CommodityEquipment.equipment_id', '{n}.CommodityEquipment.commodity_id');

		$privileges = $reservationPrivilege->getReservationPrivilegeByReservationIds($reservationIds);
		$privileges = Hash::combine($privileges, '{n}.ReservationPrivilege.privilege_id', '{n}.ReservationPrivilege', '{n}.ReservationPrivilege.reservation_id');

		$childSheets = $reservationChildSheet->getReservationChildSheet($reservationIds);
		$childSheets = Hash::combine($childSheets, '{n}.ReservationChildSheet.child_sheet_id', '{n}.ReservationChildSheet', '{n}.ReservationChildSheet.reservation_id');

		foreach ($reservations as $index => $data) {
			$reservations[$index]['ReservationDetail'] = $details[$data['Reservation']['id']];
			$reservations[$index]['CommodityEquipment'] = $equipments[$data['Commodity']['id']];
			$reservations[$index]['ReservationPrivilege'] = $privileges[$data['Reservation']['id']];
			$reservations[$index]['ReservationChildSheet'] = $childSheets[$data['Reservation']['id']];
		}

		return $reservations;
	}

	/**
	 * レンナビ予約確定
	 */
	public function rennaviReserveFix($transactionId, $datetime) {
		$this->unbindModel(array('belongsTo' => array_keys($this->belongsTo)));

		$this->updateAll(
			array(
				'rennavi_status' => sprintf(
					'CASE rennavi_status WHEN %d THEN %d WHEN %d THEN %d WHEN %d THEN %d ELSE %d END',
					Constant::RENNAVI_STATUS_RESERVE, Constant::RENNAVI_STATUS_RESERVE_FIXED,
					Constant::RENNAVI_STATUS_RESERVE_CHANGED, Constant::RENNAVI_STATUS_RESERVE_CHANGED,
					Constant::RENNAVI_STATUS_CXL_RESERVE_FIXING, Constant::RENNAVI_STATUS_CANCEL_USER,
					Constant::RENNAVI_STATUS_CANCEL_FIXED),
				'registered_flg' => 1,
				'modified' => $this->getDataSource()->value($datetime)
			),
			array(
				'rennavi_one_time_pass' => $transactionId,
				'rennavi_status NOT IN' => array(
					Constant::RENNAVI_STATUS_EXCLUDED,
					Constant::RENNAVI_STATUS_RESERVE_FIXED,
					Constant::RENNAVI_STATUS_CANCEL_FIXED
				)
			)
		);

		return $this->getAffectedRows();
	}

	/**
	 * cm_application_idから予約番号を取得
	 */
	public function getReserveKeyByCmApplicationId($cmApplicationId) {
		$options = array(
			'fields' => array(
				'Reservation.reservation_key',
				'Reservation.tel',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'ctad',
					'conditions' => 'Reservation.id = ctad.application_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'skyticket.cm_th_application',
					'alias' => 'cta',
					'conditions' => 'ctad.cm_application_id = cta.cm_application_id',
				),
			),
			'conditions' => array(
				'cta.cm_application_id' => $cmApplicationId,
				'ctad.service_cd' => SERVICE_CD_RC,
			),
			'order' => 'Reservation.id',
			'recursive' => -1,
		);
		return $this->find('list', $options);

	}

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()) {
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();
		if (!empty($this->data['Reservation']['last_name'])) {
			$this->data['Reservation']['last_name'] = $encrypt->encrypt($this->data['Reservation']['last_name']);
		}
		if (!empty($this->data['Reservation']['first_name'])) {
			$this->data['Reservation']['first_name'] = $encrypt->encrypt($this->data['Reservation']['first_name']);
		}
		if (!empty($this->data['Reservation']['email'])) {
			$this->data['Reservation']['email'] = $encrypt->encrypt($this->data['Reservation']['email']);
		}
		if (!empty($this->data['Reservation']['tel'])) {
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
		$params = array(
			'reservationId' => $reservation_id,
		);

		$data = $this->query("
				SELECT
					CmThApplicationDetail.cm_application_id
				FROM
					rentacar.reservations as Reservation
				JOIN
					skyticket.cm_th_application_detail as CmThApplicationDetail
				ON
					Reservation.id = CmThApplicationDetail.application_id
				AND
					CmThApplicationDetail.service_cd = 'rc'
				WHERE
					Reservation.id = :reservationId
				LIMIT 1
				", $params);

		return $data[0]['CmThApplicationDetail']['cm_application_id'];
	}

	// 事前決済かどうか
	public function getWebFlg($reservation_id) {
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

	public function getExistCmApplicationIds($params) {
		$settings = [
			'fields' => [
				'CmThApplicationDetail.cm_application_id'
			],
			'joins' => [
				[
					'type'=>'INNER',
					'alias'=>'CmThApplicationDetail',
					'table'=>'skyticket.cm_th_application_detail',
					'conditions'=> [
						'CmThApplicationDetail.application_id = Reservation.id',
						'CmThApplicationDetail.service_cd' => 'rc',
					],
				],
			],
			'recursive' => -1,
		];

		if (isset($params['cm_application_ids'])) {
			$settings['conditions']['CmThApplicationDetail.cm_application_id'] = $params['cm_application_ids'];
		}

		return $this->find('all',$settings);
	}

	/**
	 * cm_application_idを引数に予約情報を取得
	 *
	 * @param numeric $cmApplicationId
	 * @return array 予約情報
	 */
	public function getReservationInfoByCmApplicationId($cmApplicationId) {
		$reservationInfo = array(
			'fields' => array(
				'Reservation.id',
				'Reservation.reservation_key',
				'Reservation.rent_datetime',
				'Reservation.return_datetime',
				'Reservation.amount',
				'Reservation.last_name',
				'Reservation.first_name',
				'Reservation.tel',
				'Reservation.email',
				'Reservation.advertising_cd',
				'Reservation.payment_limit_datetime',
				'Reservation.administrative_fee',
				'ctad.cm_application_id',
				'Client.name',
				'RentOffice.name',
				'ReturnOffice.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'ctad',
					'conditions' => 'Reservation.id = ctad.application_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'rentacar.clients',
					'alias' => 'Client',
					'conditions' => 'Reservation.client_id = Client.id',
				),
				array(
					'type' => 'INNER',
					'table' => 'rentacar.offices',
					'alias' => 'RentOffice',
					'conditions' => 'Reservation.rent_office_id = RentOffice.id',
				),
				array(
					'type' => 'INNER',
					'table' => 'rentacar.offices',
					'alias' => 'ReturnOffice',
					'conditions' => 'Reservation.return_office_id = ReturnOffice.id',
				),
			),
			'conditions' => array(
				'ctad.cm_application_id' => $cmApplicationId,
				'ctad.service_cd' => SERVICE_CD_RC,
			),
			'order' => 'Reservation.id',
			'recursive' => -1,
		);
		return $this->find('first', $reservationInfo);
	}

	/**
	 * cm_application_idを引数に予約情報を取得
	 *
	 * @param numeric $cmApplicationId
	 * @return array 予約情報
	 */
	public function getReservationCarNameByCmApplicationId($cmApplicationId) {
		$reservationInfo = array(
			'fields' => array(
				'Reservation.id',
				'Reservation.reservation_key',
				'Reservation.rent_datetime',
				'Reservation.return_datetime',
				'CommodityItem.car_model_id',
				'CommodityItem.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'ctad',
					'conditions' => 'Reservation.id = ctad.application_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'rentacar.commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => array(
						'CommodityItem.id = Reservation.commodity_item_id',
					),
				),
			),
			'conditions' => array(
				'ctad.cm_application_id' => $cmApplicationId,
				'ctad.service_cd' => SERVICE_CD_RC
			),
			'order' => 'Reservation.id',
			'recursive' => -1,
		);
		return $this->find('first', $reservationInfo);
	}

	/**
	 * reservation_idを引数に予約情報(金額)を取得
	 *
	 * @param array $reservationId
	 * @return array 予約情報
	 */
	public function getCommodityPrice($reservationId) {
		$sql = array(
			'fields' => array(
				'DetailType.name',
				'ReservationDetail.amount',
				'ReservationDetail.count',
				'ReservationDetail.detail_type_id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'ReservationDetail',
					'table' => 'reservation_details',
					'conditions' => 'Reservation.id = ReservationDetail.reservation_id',
				),
				array(
					'table' => 'detail_types',
					'alias' => 'DetailType',
					'type' => 'INNER',
					'conditions' => array(
						'DetailType.id = ReservationDetail.detail_type_id'
					),
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationId,
				'Reservation.delete_flg' => 0,
			),
			'order' => 'ReservationDetail.detail_type_id',
			'recursive' => -1,
		);
		return $this->find('all', $sql);
	}
}

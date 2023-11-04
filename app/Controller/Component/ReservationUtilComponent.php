<?php
/**
 * PlanUtilに予約関連のメソッドを追加するため継承
 */
App::uses('PlanUtilComponent', 'Controller/Component');
App::uses('SkyticketCakeEmail', 'Vendor');

class ReservationUtilComponent extends PlanUtilComponent {
	public $components = array('ReservationMailParam', 'CancelPolicy');

	public $reserveChangeFlg = array(
		'inquiry' => false,
		'airline' => false,
		'mail' => false,
		'tel' => false,
		'passenger_count' => false
	);

	public $changeString = array(
		'inquiry' => '【お問い合わせ】',
		'airline' => '【到着便/出発便】',
		'mail' => '【メールアドレス】',
		'tel' => '【電話番号】',
		'passenger_count' => '【ご利用人数】'
	);

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * 予約完了後のメール送信処理
	 * @param array $reservation
	 * @param boolean $fromStep1
	 * @param boolean $sendToUser
	 * @return void
	 */
	public function sendReservedMail($reservation, $fromStep1, $sendToUser = true) {
		$ClientEmail = ClassRegistry::init('ClientEmail');

		$emailView = $this->ReservationMailParam->getReservationMailParam($reservation);

		$emailView['fromStep1'] = $fromStep1;

		$emailView['status'] = '新規予約';
		$emailView['notification_detail'] = '下記のお客様よりご予約をいただきましたのでご報告いたします。';
		$emailView['domain'] = $this->controller->domain;

		$emailView['cancel_policy'] = $this->CancelPolicy->getTextLines($reservation['Reservation']['client_id'], $reservation['Reservation']['rent_datetime'], false);
		// INCIDENT-3044 取消手続料の徴収を廃止する
		//$emailView['adv_cancel_fee'] = $this->CancelPolicy->getAdvCancelFee();

		// 今日明日出発の場合はクライアント宛メールの件名変化
		$urgent = '';
		$rentDay = new DateTime(date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime'])));
		$today = new DateTime(date('Y-m-d'));
		$interval = $today->diff($rentDay);
		if ($interval->invert == 0) {
			if ($interval->days == 0) {
				$urgent = '【本日】';
			} elseif ($interval->days == 1) {
				$urgent = '【明日】';
			}
		}

		// クライアント宛メールタイトル
		if (!empty($reservation['ReservationMail']['contents'])) {
			$clientSubject = '【skyticket】' . $urgent . '新規予約（コメント有り）' . $reservation['Reservation']['last_name'] . ' ' . $reservation['Reservation']['first_name'] . '様 / ' . $reservation['CarType']['name'];
		} else {
			$clientSubject = '【skyticket】' . $urgent . '新規予約 ' . $reservation['Reservation']['last_name'] . ' ' . $reservation['Reservation']['first_name'] . '様 / ' . $reservation['CarType']['name'];
		}

		$emailConfig = 'smtp';

		// クライアントへの通知メール
		// 継承してメールクラスを利用
		$email = new SkyticketCakeEmail($emailConfig);
		// ユーザには非表示
		$email->non_show_user_flg = 1;
		$email
			->viewVars($emailView)
			->template('notification', 'suggestions_layout')
			->subject($clientSubject);

		$clientEmail = $ClientEmail->getEmail($reservation['Client']['id']);
		foreach ($clientEmail as $val) {
			if (!empty($val['ClientEmail']['reservation_email'])) {
				// 各アドレスに送信
				$email->to(trim($val['ClientEmail']['reservation_email']));
				$email->send();
			}
		}

		// 貸出店舗にメールアドレスが設定されていれば送信
		if (!empty($reservation['RentOffice']['reserve_mail'])) {
			$email->to(trim($reservation['RentOffice']['reserve_mail']));
			$email->send();
		}
		if (!empty($reservation['RentOffice']['reserve_mail2'])) {
			$email->to(trim($reservation['RentOffice']['reserve_mail2']));
			$email->send();
		}
		if (!empty($reservation['RentOffice']['reserve_mail3'])) {
			$email->to(trim($reservation['RentOffice']['reserve_mail3']));
			$email->send();
		}

		// お客様へのメール
		if (!empty($reservation['Reservation']['email']) && $sendToUser) {
			// 継承してメールクラスを利用
			$email = new SkyticketCakeEmail($emailConfig);
			// ユーザには表示
			$email->non_show_user_flg = 0;
			$email
				->to(trim($reservation['Reservation']['email']))
				->viewVars($emailView)
				->template('suggestions', 'suggestions_layout')
				->subject('【skyticket】レンタカー予約完了のお知らせ')
				->send();
		}
	}

	/**
	 * 通知メール送信処理
	 *
	 * @param int $reservationId
	 * @param string $status
	 * @return void
	 */
	public function sendNotificationMail($reservationId, $status) {

		// 継承してメールクラスを利用
		$email = new SkyticketCakeEmail('smtp');

		$Reservation = ClassRegistry::init('Reservation');
		$Privilege = ClassRegistry::init('Privilege');
		$PaymentLog = ClassRegistry::init('PaymentLog');
		$CancelReason = ClassRegistry::init('CancelReason');
		$ClientEmail = ClassRegistry::init('ClientEmail');

		$reservation = $Reservation->getReservationData($reservationId);

		// 曜日
		$weekday = array('日', '月', '火', '水', '木', '金', '土');
		$rentWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['rent_datetime']))];
		$returnWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['return_datetime']))];

		// オプション
		$privilegeList = $Privilege->getClientPrivilegeList($reservation['Client']['id']);
		$optionText = '';
		if (!empty($reservation['ReservationDetail'])) {
			// 乗り捨て料金・深夜手数料
			foreach ($reservation['ReservationDetail'] as $value) {
				if ($value['detail_type_id'] == Constant::DETAIL_TYPE_DROPOFFPRICE) {
					$optionText .= '乗り捨て料金 ' . number_format($value['amount']) . '円  ';
				} else if ($value['detail_type_id'] == Constant::DETAIL_TYPE_NIGHTFEE) {
					$optionText .= '深夜手数料 ' . number_format($value['amount']) . '円  ';
				}
			}
		}
		if (!empty($reservation['ReservationChildSheet'])) {
			// チャイルドシート
			foreach ($reservation['ReservationChildSheet'] as $value) {
				if (!empty($privilegeList[$value['child_sheet_id']])) {
					$optionText .= $privilegeList[$value['child_sheet_id']] . '×' . $value['count'] . ' ' . number_format($value['price']) . '円  ';
				}
			}
		}
		if (!empty($reservation['ReservationPrivilege'])) {
			// 特典
			foreach ($reservation['ReservationPrivilege'] as $value) {
				if (!empty($privilegeList[$value['privilege_id']])) {
					$optionText .= $privilegeList[$value['privilege_id']] . '×' . $value['count'] . ' ' . number_format($value['price']) . '円  ';
				}
			}
		}

		// お問い合わせ
		if (!empty($reservation['ReservationMail']['contents'])) {
			$contents = $reservation['ReservationMail']['contents'];
		} else {
			$contents = '';
		}

		$params = array(
			'reservation_id' => $reservation['Reservation']['id'],
			'reservation_key' => $reservation['Reservation']['reservation_key'],
			'reservation_hash' => $reservation['Reservation']['reservation_hash'],
			'rent_date' => date('Y年m月d日', strtotime($reservation['Reservation']['rent_datetime'])),
			'rent_week' => $rentWeekDay,
			'rent_time' => date('H:i', strtotime($reservation['Reservation']['rent_datetime'])),
			'return_date' => date('Y年m月d日', strtotime($reservation['Reservation']['return_datetime'])),
			'return_week' => $returnWeekDay,
			'return_time' => date('H:i', strtotime($reservation['Reservation']['return_datetime'])),
			'last_name' => $reservation['Reservation']['last_name'],
			'first_name' => $reservation['Reservation']['first_name'],
			'email' => $reservation['Reservation']['email'],
			'tel' => $reservation['Reservation']['tel'],
			'amount' => $reservation['Reservation']['amount'],
			'arrival_flight_number' => $reservation['Reservation']['arrival_flight_number'],
			'departure_flight_number' => $reservation['Reservation']['departure_flight_number'],
			'reservation_datetime' => $reservation['Reservation']['created'],
			'adults_count' => $reservation['Reservation']['adults_count'],
			'children_count' => $reservation['Reservation']['children_count'],
			'infants_count' => $reservation['Reservation']['infants_count'],
			'client_name' => $reservation['Client']['name'],
			'rent_office_name' => $reservation['RentOffice']['name'],
			'rent_office_tel' => $reservation['RentOffice']['tel'],
			'rent_office_hours_from' => $reservation['RentOffice']['office_hours_from'],
			'rent_office_hours_to' => $reservation['RentOffice']['office_hours_to'],
			'rent_office_address' => $reservation['RentOffice']['address'],
			'return_office_name' => $reservation['ReturnOffice']['name'],
			'return_office_tel' => $reservation['ReturnOffice']['tel'],
			'return_office_hours_from' => $reservation['ReturnOffice']['office_hours_from'],
			'return_office_hours_to' => $reservation['ReturnOffice']['office_hours_to'],
			'return_office_address' => $reservation['ReturnOffice']['address'],
			'commodity_name' => mb_convert_kana($reservation['Commodity']['name'], 'KV'),
			'car_class' => $reservation['CarClass']['name'],
			'car_type' => $reservation['CarType']['name'],
			'car_count' => $reservation['Reservation']['cars_count'],
			'client_reservation_content' => $reservation['Client']['reservation_content'],
			'client_cancel_policy' => $reservation['Client']['cancel_policy'],
			'cancel_policy' => $this->CancelPolicy->getTextLines($reservation['Reservation']['client_id'], $reservation['Reservation']['rent_datetime'], false),
			// INCIDENT-3044 取消手続料の徴収を廃止する
			//'adv_cancel_fee' => $this->CancelPolicy->getAdvCancelFee(),
			'reservation_email' => $contents,
			'option_list' => $optionText,
			'domain' => $this->controller->domain,
			'edit_tel' => $this->reserveChangeFlg['tel']
		);

		$count = $Reservation->getWebFlg($reservation['Reservation']['id']);
		$params['fromStep1'] = !($count);

		// お問合せ以外に変更があったか？
		$changeArr = array();
		foreach ($this->reserveChangeFlg as $k => $v) {
			if ($k != 'inquiry' && $v == true) {
				$changeArr[$k] = $v;
			}
		}

		// キャンセル理由
		if (!empty($reservation['Reservation']['cancel_reason_id'])) {
			$reason = $CancelReason->getCancelReasonList();
			$params['cancelReason'] = $reason[$reservation['Reservation']['cancel_reason_id']];
			$params['cancel_remark'] = $reservation['Reservation']['cancel_remark'];
		}

		if (strcmp($status, 'cancel') == 0) {
			$params['status'] = 'キャンセル';
			$params['notificationDetail'] = '下記のお客様がご予約をキャンセルされましたのでご報告いたします。';
		} else if (strcmp($status, 'inquiry') == 0) {
			if (empty($changeArr)) {
				$params['status'] = 'お問い合わせ';
				$params['notificationDetail'] = '下記のお客様より【お問い合わせ】をいただきましたのでご報告いたします。';
				$params['contents'] = str_replace(array("\r\n", "\r", "\n", "<br/>"), '', $reservation['ReservationMail']['contents']);
			} else {
				$params['status'] = '予約内容変更（コメント有り）';
				$params['contents'] = str_replace(array("\r\n", "\r", "\n", "<br/>"), '', $reservation['ReservationMail']['contents']);
				$params['notificationDetail'] = '下記のお客様より【お問い合わせ】及び以下の項目の変更がありましたのでご報告いたします。';

				$params['changeDetail'] = array();
				foreach ($this->reserveChangeFlg as $changeKey => $changeVal) {
					if ($changeVal) {
						$params['changeDetail'][] = $this->changeString[$changeKey];
					}
				}
			}
		} else if (strcmp($status, 'modify') == 0) {
			$params['status'] = '予約内容変更';
			$params['notificationDetail'] = '下記のお客様より以下の項目の変更がありましたのでご報告いたします。';

			$params['changeDetail'] = array();
			foreach ($this->reserveChangeFlg as $changeKey => $changeVal) {
				if ($changeVal) {
					$params['changeDetail'][] = $this->changeString[$changeKey];
				}
			}
		}

		// 今日明日出発の場合はクライアント宛メールの件名変化
		$urgent = '';
		$rentDay = new DateTime(date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime'])));
		$today = new DateTime(date('Y-m-d'));
		$interval = $today->diff($rentDay);
		if ($interval->invert == 0) {
			if ($interval->days == 0) {
				$urgent = '【本日】';
			} elseif ($interval->days == 1) {
				$urgent = '【明日】';
			}
		}

		// ユーザには非表示
		$email->non_show_user_flg = 1;
		// クライアントにメール
		$email
			->viewVars($params)
			->template('after_reserve', 'suggestions_layout')
			->subject('【skyticket】' . $urgent . $params['status'] . '　' . $params['last_name'] . '　' . $params['first_name'] . '様 / ' . $params['car_type']);
		$clientEmail = $ClientEmail->getEmail($reservation['Client']['id']);
		foreach ($clientEmail as $val) {
			if (!empty($val['ClientEmail']['reservation_email'])) {
				$email->to(trim($val['ClientEmail']['reservation_email']));
				$email->send();
			}
		}
		// 貸出店舗にメールアドレスが設定されていれば送信
		if (!empty($reservation['RentOffice']['reserve_mail'])) {
			$email->to(trim($reservation['RentOffice']['reserve_mail']));
			$email->send();
		}
		if (!empty($reservation['RentOffice']['reserve_mail2'])) {
			$email->to(trim($reservation['RentOffice']['reserve_mail2']));
			$email->send();
		}
		if (!empty($reservation['RentOffice']['reserve_mail3'])) {
			$email->to(trim($reservation['RentOffice']['reserve_mail3']));
			$email->send();
		}

		// ユーザにも表示
		$email->non_show_user_flg = 0;
		// お客様にメール
		if ($reservation['Reservation']['is_send_mail']) {
			if (strcmp($status, 'cancel') == 0) {
				$email
					->viewVars($params)
					->template('user_cancel', 'suggestions_layout')
					->subject('【skyticket】レンタカー予約キャンセルのお知らせ')
					->to(trim($reservation['Reservation']['email']));
				$email->send();
			} else if (strcmp($status, 'inquiry') == 0) {
				if (empty($changeArr)) {
					$email->template('inquiry_reserve', 'suggestions_layout');
					$email->subject('【skyticket】お問合せを承りました');
					$email->to(trim($reservation['Reservation']['email']));
					$email->send();
				} else {
					$email->template('change_reserve', 'suggestions_layout');
					$email->subject('【skyticket】予約内容を変更及びお問合せを承りました');
					$email->to(trim($reservation['Reservation']['email']));
					$email->send();
				}
			} else if (strcmp($status, 'modify') == 0) {
				$email->template('change_reserve', 'suggestions_layout');
				$email->subject('【skyticket】予約内容を変更いたしました');
				$email->to(trim($reservation['Reservation']['email']));
				$email->send();
			}
		}
	}

	/**
	 * 料金計算
	 * @param int $commodityItemId 商品アイテムID
	 * @param string $fromDateTime 貸出日時
	 * @param string $toDateTime 返却日時
	 * @param boolean $dayTimeFlg 暦日制・時間制フラグ
	 * @param int $optionPrivilege 貸出営業所ID
	 * @param int $optionPrivilege 返却営業所ID
	 * @param int $estimationTotalPrice 見積もり合計料金
	 * @param array $optionSheet オプション(シート)
	 * @param array $optionPrivilege オプション (特典)
	 * @return int|boolean
	 */
	public function priceCalculation($commodityItemId, $fromDateTime, $toDateTime, $dayTimeFlg,
		$fromOfficeId, $returnOfficeId, $estimationTotalPrice, $optionSheet = array(), $optionPrivilege = array()) {

		$CommodityItem = ClassRegistry::init('CommodityItem');
		$DisclaimerCompensation = ClassRegistry::init('DisclaimerCompensation');
		$CommodityPrivilege = ClassRegistry::init('CommodityPrivilege');
		$DropOffAreaRate = ClassRegistry::init('DropOffAreaRate');
		$Office = ClassRegistry::init('Office');
			
		$dateFrom = date('Y-m-d', strtotime($fromDateTime));
		$dateTo = date('Y-m-d', strtotime($toDateTime));
		$commodityItemPriceData = $CommodityItem->getCommodityItemPriceData($commodityItemId, $dateFrom);

		list($dayNight, $period, $period24) = $this->getPeriodArray($fromDateTime, $toDateTime);

		$price = 0;
		$afterPrice = 0;

		// 免責補償料金取得
		$disclaimerCompensationPrice = $DisclaimerCompensation->getFee(
			$commodityItemPriceData['CarClass']['id'],
			$dateFrom,
			$dateTo,
			$period,
			$period24
		);

		// 基本料金
		$price = $this->calcBasicPrice(
			$commodityItemPriceData['CommodityPrice'],
			$dayTimeFlg,
			$fromDateTime,
			$toDateTime,
			$period
		);

		$basicCharge = $price + $disclaimerCompensationPrice;

		// オプション計算
		$commodityPrivilegeData = $CommodityPrivilege->getCommodityPrivilegeData(
				$commodityItemPriceData['CommodityItem']['commodity_id'],
				$period,
				$period24
		);
		$commodityPrivilege = array();
		foreach ($commodityPrivilegeData as $value) {
			$commodityPrivilege[$value['Privilege']['id']] = $value[0]['Sum'];
		}
		$optionPrice = 0;
		$optionArray = $optionSheet + $optionPrivilege;
		if (!empty($optionArray)) {
			foreach ($commodityPrivilege as $privilegeId => $value) {
				if (!empty($optionArray[$privilegeId])) {
					$optionPrice += ($value * $optionArray[$privilegeId]);
				}
			}
		}

		// 乗り捨てエリア料金
		$dropPrice = 0;
		if ($fromOfficeId != $returnOfficeId) {
			$dropPrice = $DropOffAreaRate->getDropOffAreaPrice($fromOfficeId, $returnOfficeId, $commodityItemPriceData['CarClass']['id']);
			if ($dropPrice == null) {
				$dropPrice = 0;
			}
		}

		// 深夜手数料
		$fromData = array(
			'fromOfficeId' => $fromOfficeId,
			'fromTime' => $fromDateTime,
		);
		$returnData = array(
			'returnOfficeId' => $returnOfficeId,
			'returnTime' => $toDateTime,
		);
		$lateNightFee = $Office->getLateNightFee($fromData, $returnData);

		// 合計料金 = 基本料金 + オプション料金 + 乗り捨てエリア料金 + 深夜手数料
		$totalPrice = $basicCharge + $optionPrice + $dropPrice + $lateNightFee;

		if ($totalPrice == $estimationTotalPrice) {
			return $totalPrice;
		} else {
			return false;
		}
	}

	/**
	 * 料金計算（募集型）
	 * @param int $commodityItemId 商品アイテムID
	 * @param string $fromDateTime 貸出日時
	 * @param string $toDateTime 返却日時
	 * @param int $optionPrivilege 貸出営業所ID
	 * @param int $optionPrivilege 返却営業所ID
	 * @param int $estimationTotalPrice 見積もり合計料金
	 * @return int|boolean
	 */
	public function priceCalculationAgentOrganized($commodityItemId, $fromDateTime, $toDateTime,
		$fromOfficeId, $returnOfficeId, $estimationTotalPrice) {

		$CommodityItem = ClassRegistry::init('CommodityItem');
		$DisclaimerCompensation = ClassRegistry::init('DisclaimerCompensation');
		$CommodityPrivilege = ClassRegistry::init('CommodityPrivilege');
		$DropOffAreaRate = ClassRegistry::init('DropOffAreaRate');
		$Office = ClassRegistry::init('Office');
			
		$dateFrom = date('Y-m-d', strtotime($fromDateTime));
		$dateTo = date('Y-m-d', strtotime($toDateTime));
		$commodityItemPriceData = $CommodityItem->getCommodityItemPriceDataAgentOrganized($commodityItemId, $dateFrom);

		list($dayNight, $period, $period24) = $this->getPeriodArray($fromDateTime, $toDateTime);

		$price = 0;

		// 免責補償料金取得
		$disclaimerCompensationPrice = $DisclaimerCompensation->getFee(
			$commodityItemPriceData['CarClass']['id'],
			$dateFrom,
			$dateTo,
			$period,
			$period24
		);

		// 基本料金
		$price = $this->calcBasicPriceAgentOrganized(
			$commodityItemPriceData['CommodityPrice'],
			$period
		);

		$basicCharge = $price + $disclaimerCompensationPrice;

		// 乗り捨てエリア料金
		$dropPrice = 0;
		if ($fromOfficeId != $returnOfficeId) {
			$dropPrice = $DropOffAreaRate->getDropOffAreaPrice($fromOfficeId, $returnOfficeId, $commodityItemPriceData['CarClass']['id']);
			if ($dropPrice == null) {
				$dropPrice = 0;
			}
		}

		// 深夜手数料
		$fromData = array(
			'fromOfficeId' => $fromOfficeId,
			'fromTime' => $fromDateTime,
		);
		$returnData = array(
			'returnOfficeId' => $returnOfficeId,
			'returnTime' => $toDateTime,
		);
		$lateNightFee = $Office->getLateNightFee($fromData, $returnData);

		// 合計料金 = 基本料金 + 乗り捨てエリア料金 + 深夜手数料 * 上乗せ（10%）
		$totalPrice = intval($basicCharge) + intval($dropPrice) + intval($lateNightFee);
		$totalPrice *= Constant::ADDITIONAL_RATE;
		$totalPrice = intval(ceil(intval($totalPrice) / 10) * 10);

		if ($totalPrice == $estimationTotalPrice) {
			return $totalPrice;
		} else {
			return false;
		}
	}

	/**
	 * cm_th_application_detailのapplication_idを更新
	 *
	 * @param DB $db
	 * @param int $reservedId
	 * @param int $cmApplicationId
	 * @return int|booelan
	 */
	public function updateApplicationId($db, $reservedId, $cmApplicationId) {
		$sql = "UPDATE ".DB_NAME.".cm_th_application_detail "
			. "SET application_id =".(int)$reservedId
			. " WHERE application_id = 0 AND service_cd = 'rc' AND cm_application_id = ".(int)$cmApplicationId;

		if($db->execute($sql, array(), $st)) {
			return $db->getRowCount($st);
		} else {
			return false;
		}
	}

	/**
	 * cm_th_application_detailに登録
	 *
	 * @param DB $db
	 * @param int $reservedId
	 * @param int $cmApplicationId
	 * @return int|booelan
	 */
	public function insertApplicationDetail($db, $reservedId, $cmApplicationId) {
		$sql = "INSERT INTO ".DB_NAME.".cm_th_application_detail ("
			. "  cm_application_id "
			. ", application_id "
			. ", service_cd "
			. ", create_dt "
			. ") VALUES ("
			. "  :cm_application_id "
			. ", :application_id "
			. ", :service_cd "
			. ", NOW() "
			. ")";

		$param_array = array(
			':cm_application_id'	=> (int)$cmApplicationId,
			':application_id'	=> (int)$reservedId,
			':service_cd'	=> 'rc',
		);

		if ($db->execute($sql, $param_array, $st)) {
			return $db->getLastInsertId();
		} else {
			return false;
		}
	}

	/**
	 * reservationからreservation_keyをもとに検索
	 *
	 * @param DB $db
	 * @param int $reservationKey
	 * @return int|booelan
	 */
	public function findReservation($db, $reservationKey) {
		$sql = "SELECT * FROM ".DB_NAME_RENTACAR.".reservations 
				WHERE reservation_key ='$reservationKey'";

		if($db->execute($sql, array(), $st)) {
			$reservation_find = $db->executeFetchAll($sql, array());
			return $reservation_find;
		} else {
			return false;
		}
	}

}

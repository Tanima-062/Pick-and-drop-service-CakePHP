<?php
App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('SkyticketCakeEmail', 'Vendor');

/**
 * Statics Controller
 *
 * @property Statics $Statics
 */
class StaticsController extends AppController {

	public $components = array('Cookie', 'Session', 'BreadCrumb');
	public $uses = array(
			'Reservation', 'Privilege'
	);

	public function beforeFilter() {
		parent::beforeFilter();
		
		// robots noindex
		$this->set('meta_robots', 'noindex');
	}

	/**
	 * resend
	 * 予約内容再送
	 */
	public function resend() {

		if ($this->request->is('post') && !empty($this->data['email'])) {

			// 予約データ取得
			$resendEmails = $this->Reservation->getResendEmailAll($this->data['email']);

			if (!empty($resendEmails)) {

				// UA取得
				if (strcmp(uaCheck(), Constant::DEVICE_PC) == 0) {
					$ua = 'PC';
				} else if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
					$ua = 'スマートフォン';
				}

				foreach ($resendEmails as $key => $resendReservation) {
					$reservation = $this->Reservation->getReservationData($resendReservation['Reservation']['id']);

					// 曜日
					$weekday = array('日', '月', '火', '水', '木', '金', '土');
					$rentWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['rent_datetime']))];
					$returnWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['return_datetime']))];

					// オプション
					$privilegeList = $this->Privilege->getClientPrivilegeList($reservation['Reservation']['client_id']);
					$optionText = '';
					if (!empty($reservation['ReservationDetail'])) {
						// 乗り捨て料金・深夜手数料
						foreach ($reservation['ReservationDetail'] as $value) {
							if ($value['detail_type_id'] == Constant::DETAIL_TYPE_DROPOFFPRICE) {
								$optionText .= '乗り捨て料金 '.number_format($value['amount']).'円  ';
							} else if ($value['detail_type_id'] == Constant::DETAIL_TYPE_NIGHTFEE) {
								$optionText .= '深夜手数料 '.number_format($value['amount']).'円  ';
							}
						}
					}
					if (!empty($reservation['ReservationChildSheet'])) {
						// チャイルドシート
						foreach ($reservation['ReservationChildSheet'] as $value) {
							if (!empty($privilegeList[$value['child_sheet_id']])) {
								$optionText .= $privilegeList[$value['child_sheet_id']].'×'.$value['count'].' '.number_format($value['price']).'円  ';
							}
						}
					}
					if (!empty($reservation['ReservationPrivilege'])) {
						// 特典
						foreach ($reservation['ReservationPrivilege'] as $value) {
							if (!empty($privilegeList[$value['privilege_id']])) {
								$optionText .= $privilegeList[$value['privilege_id']].'×'.$value['count'].' '.number_format($value['price']).'円  ';
							}
						}
					}

					// お問い合わせ
					if (!empty($this->data['Reservation']['contents'])) {
						$contents = $this->data['Reservation']['contents'];
					} else {
						$contents = '';
					}

					// メール文面変数の整形
					$params[] = array(
							'reservation_id' => $reservation['Reservation']['id'],
							'reservation_key' => $reservation['Reservation']['reservation_key'],
							'reservation_hash' => $reservation['Reservation']['reservation_hash'],
							'rent_datetime' => date('Y年m月d日 H:i', strtotime($reservation['Reservation']['rent_datetime'])),
							'rent_week' => $rentWeekDay,
							'return_datetime' => date('Y年m月d日 H:i', strtotime($reservation['Reservation']['return_datetime'])),
							'return_week' => $returnWeekDay,
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
							'rent_office_access' => $reservation['RentOffice']['access'],
							'return_office_name' => $reservation['ReturnOffice']['name'],
							'return_office_tel' => $reservation['ReturnOffice']['tel'],
							'return_office_hours_from' => $reservation['ReturnOffice']['office_hours_from'],
							'return_office_hours_to' => $reservation['ReturnOffice']['office_hours_to'],
							'return_office_address' => $reservation['ReturnOffice']['address'],
							'return_office_access' => $reservation['ReturnOffice']['access'],
							'commodity_name' => $reservation['Commodity']['name'],
							'car_class' => $reservation['CarClass']['name'],
							'car_type' => $reservation['CarType']['name'],
							'car_smoking_flg' => $reservation['Commodity']['smoking_flg'],
							'car_count' => $reservation['Reservation']['cars_count'],
							'ua' => $ua,
							'client_reservation_content' => $reservation['Client']['reservation_content'],
							'reservation_email' => $contents,
							'option_list' => $optionText,
							'remark' => $reservation['ReservationMail']['contents'],
							'domain' => $this->domain,
					);
				}
			}


			if (!empty($params)) {
				$emailConfig = 'smtp';
				foreach ($params as $param) {
					// 継承してメールクラスを利用
					// $email = new CakeEmail($emailConfig);
					$email = new SkyticketCakeEmail($emailConfig);
					
					$email
					->to(trim($this->data['email']))
					->viewVars($param)
					->subject('【skyticket】レンタカー予約再送のお知らせ');
					$email->template('suggestions', 'suggestions_layout');
					$email->send();
				}
				// メール送信OK
				$this->redirect(array('action' => 'resend_complete', '?' => $this->data['email']));
			} else {
				// メール送信NG
				$this->redirect(array('action' => 'resend_error', '?' => $this->data['email']));
			}
		}

		$this->set('title_for_layout', '予約内容再送');
		$this->set('h1_for_layout', '予約内容再送');
		$this->set('top_txt', '予約時にご登録いただいたメールアドレスに予約内容を送信いたします。');
		$this->set('description_for_layout', '予約時にご登録いただいたメールアドレスに予約内容を送信いたします。');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setStatics($this->action);
		$this->set('progress_arr', $progressArr);
	}
	public function sp_resend() {
		$this->resend();
	}


	/**
	 * resend_complete
	 * 予約内容再送完了
	 */
	public function resend_complete() {

		$emailAdress = $_SERVER['QUERY_STRING'];

		if (preg_match('/@docomo.ne.jp/', $emailAdress)) {
			// docomo
			$emailDomain = 'docomo';
		} elseif (preg_match('/@ezweb.ne.jp|@ido.ne.jp/', $emailAdress)) {
			// au
			$emailDomain = 'au';
		} elseif (preg_match('/@softbank.ne.jp|@vodafone.ne.jp|@disney.ne.jp|@i.softbank.jp/', $emailAdress)) {
			// softbank
			$emailDomain = 'softbank';
		} else {
			// フリーメール,その他
			$emailDomain = 'other';
		}

		$this->set(compact('emailAdress', 'emailDomain'));

		$this->set('title_for_layout', '予約内容再送完了');
		$this->set('h1_for_layout', '予約内容再送完了');
		$this->set('top_txt', '予約時にご登録いただいたメールアドレスに予約内容を送信しました。');
		$this->set('description_for_layout', '予約時にご登録いただいたメールアドレスに予約内容を送信しました。');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setStatics($this->action);
		$this->set('progress_arr', $progressArr);
	}
	public function sp_resend_complete() {
		$this->resend_complete();
	}

	/**
	 * resend_error
	 * 予約内容再送エラー
	 */
	public function resend_error() {

		$emailAdress = $_SERVER['QUERY_STRING'];

		$this->set(compact('emailAdress'));

		$this->set('title_for_layout', '予約内容再送エラー');
		$this->set('h1_for_layout', '予約内容再送エラー');
		$this->set('top_txt', '予約内容を再送できませんでした。');
		$this->set('description_for_layout', '予約内容を再送できませんでした。');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setStatics($this->action);
		$this->set('progress_arr', $progressArr);
	}
	public function sp_resend_error() {
		$this->resend_error();
	}

}
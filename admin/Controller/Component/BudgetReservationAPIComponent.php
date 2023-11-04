<?php

App::uses('Component', 'Controller');
App::uses('HttpSocket', 'Network/Http');
require_once("encrypt_class.php");

class BudgetReservationAPIComponent extends Component {

	// バジェットレンタカー予約連携
	// 他社追随時はバジェットの仕様を元にする
	// ※可能な限り流用したい

	protected $clientName = 'バジェットレンタカー';

	protected $postData = null;

	// 管理画面送信データ準備
	public function setAdminReservationData($reservationId, $apiStatus) {
		$Reservation = ClassRegistry::init('Reservation');
		$ReservationDetail = ClassRegistry::init('ReservationDetail');
		$CommodityEquipment = ClassRegistry::init('CommodityEquipment');
		$Privilege = ClassRegistry::init('Privilege');

		// 予約データ
		$reservationData = $Reservation->getReservationApiPostData($reservationId);
		$reservation = array(
			'reservation_key'			 => $reservationData['Reservation']['reservation_key'],
			'reservation_status_id'		 => $apiStatus,
			'changed_by'				 => Constant::API_CHANGED_BY_CLIENT,
			'rent_datetime'				 => $reservationData[0]['rent_datetime'],
			'rent_shop_id'				 => $reservationData['RentOffice']['office_code'],
			'rent_shop_name'			 => $reservationData['RentOffice']['name'],
			'return_datetime'			 => $reservationData[0]['return_datetime'],
			'return_shop_id'			 => $reservationData['ReturnOffice']['office_code'],
			'return_shop_name'			 => $reservationData['ReturnOffice']['name'],
			'car_class_id'				 => $reservationData['CarClass']['id'],
			'car_class_name'			 => $reservationData['CarClass']['name'],
			'car_model_id'				 => $reservationData['CarModel']['id'],
			'car_model_name'			 => $reservationData['CarModel']['name'],
			'plan_id'					 => $reservationData['CommodityItem']['id'],
			'plan_name'					 => $reservationData['Commodity']['name'],
			'plan_type'					 => ($reservationData['Commodity']['sales_type'] == Constant::SALES_TYPE_ARRANGED) ? Constant::API_SALES_TYPE_ARRANGED : Constant::API_SALES_TYPE_AGENT_ORGANIZED,
			'last_name'					 => $reservationData['Reservation']['last_name'],
			'first_name'				 => $reservationData['Reservation']['first_name'],
			'tel'						 => $reservationData['Reservation']['tel'],
			'email'						 => $reservationData['Reservation']['email'],
			'arrival_flight_number'		 => $reservationData['Reservation']['arrival_flight_number'],
			'departure_flight_number'	 => $reservationData['Reservation']['departure_flight_number'],
			'adults_count'				 => $reservationData['Reservation']['adults_count'],
			'children_count'			 => $reservationData['Reservation']['children_count'],
			'infants_count'				 => $reservationData['Reservation']['infants_count'],
			'total_amount'				 => $reservationData['Reservation']['amount'],
		);

		// 予約明細データ
		$details = array();
		$reservationDetails = $ReservationDetail->getDetailApiPostData($reservationId);
		foreach ($reservationDetails as $v) {
			$details[] = array(
				'detail_type_id'	 => $v['ReservationDetail']['detail_type_id'],
				'amount'			 => $v['ReservationDetail']['amount'],
			);
		}

		// 標準オプション（装備）
		$normalOptions = array();
		$equipments = $CommodityEquipment->getEquipmentDataWithName($reservationData['Commodity']['id']);
		foreach ($equipments as $v) {
			$normalOptions[] = array(
				'option_id'		 => $v['Equipment']['id'],
				'option_name'	 => $v['Equipment']['name'],
				'count'			 => 1,
				'amount'		 => 0,
			);
		}

		// 選択オプション（特典）
		$extraOptions = array();
		$childSheetData = $Privilege->getPrivilegeApiPostData($reservationId, 1);
		$optionData = $Privilege->getPrivilegeApiPostData($reservationId, 0);
		$privileges = array_merge($childSheetData, $optionData);
		foreach ($privileges as $v) {
			$alias = $v[0]['alias'];
			$extraOptions[] = array(
				'option_id'		 => $v['Privilege']['id'],
				'option_name'	 => $v['Privilege']['name'],
				'count'			 => $v[$alias]['count'],
				'price'			 => ($v[$alias]['price'] > 0) ? ($v[$alias]['price'] / $v[$alias]['count'] ) : 0,
			);
		}

		// データセット
		$this->postData = array('request' => array(
			'reservation'		 => $reservation,
			'details'			 => $details,
			'normal_options'	 => $normalOptions,
			'extra_options'		 => $extraOptions,
		));
	}

	// 予約データ送信
	public function sendReservationData() {

		if (!isset($this->postData)) {
			throw new Exception('送信データが未設定の状態で、予約データ送信がコールされました。');
		}
		$data = $this->postData;

		$reservationKey = $data['request']['reservation']['reservation_key'];

		$this->log(sprintf("予約連携APIリクエスト (%s)\n%s", $reservationKey, print_r($this->encryptPersonalInfo($data), true)), 'debug');
		try {
			$http = new HttpSocket(array(
				'ssl_verify_host' => false,
				'ssl_cafile' => '/etc/pki/tls/certs/ca-bundle.crt',
				'timeout' => 60
			));

			$response = $http->post(
				$this->getUrl(),
				json_encode($data),
				array('header' => array('Content-Type' => 'application/json'))
			);
		} catch (Exception $e) {
			$this->log(sprintf("予約連携API実行で例外が発生しました。(%s)\n%s\n%s", $reservationKey, $e->getMessage(), $e->getTraceAsString()), 'error');
			return array(false, array());
		}
		$result = json_decode($response->body, true);

		$this->log(sprintf("予約連携APIレスポンス (%s)\n%s", $reservationKey, print_r($result, true)), 'debug');

		if (!$this->validateResponseData($result, $data['request']['reservation']['reservation_status_id'])) {
			$this->log(sprintf("予約連携APIのレスポンスが異常です。(%s)\n%s", $reservationKey, print_r($result, true)), 'error');
			return array(false, $result);
		}

		return array(true, $result['response']['result']);
	}

	// アラートメール送信（管理画面より）
	public function sendAlertFromAdmin($controlNumber/*, $domain*/) {
		if (!empty($this->postData['request']['reservation'])) {
			$statusName = Constant::apiStatusNames()[$this->postData['request']['reservation']['reservation_status_id']];
			$reservationKey = $this->postData['request']['reservation']['reservation_key'];

			// バジェット向け
			$body = $this->getAdminMailBody($controlNumber/*, $domain*/);
			$subject = sprintf("【skyticket】%s連携でエラーが発生しました（%s）", $statusName, $reservationKey);
			$this->sendAlertMail($body, $subject, IS_PRODUCTION ? Constant::BUDGET_ERROR_EMAIL : EMAIL_ADDRESS_SYS);


			// スカチケ運用向け
			$subject = sprintf("【skyticket】管理画面からの%s連携でエラーが発生しました（%s、%s）", $statusName, $this->clientName, $reservationKey);
			$this->sendAlertMailWithBodyPrefix('', $subject, EMAIL_ADDRESS_RENTACAR);
		}
	}

	// 送信先URL
	protected function getUrl() {
		// APIサーバを介して通信
		if (IS_STAGING) {
			return 'https://lb-v5k-internal-stg.skyticket.jp/rentacar/api/budget/v1/reservations';
		} else if (IS_PRODUCTION) {
			return 'https://rentacar-api.skyticket.com/rentacar/api/budget/v1/reservations';
		} else {
			return 'https://lb-ra8-internal-dev.skyticket.jp/rentacar/api/budget/v1/reservations';
		}
	}

	// 個人情報暗号化
	protected function encryptPersonalInfo($data) {
		$encryptData = $data;
		$target = array('last_name', 'first_name', 'tel', 'email');
		$encrypt = new Encrypt();
		foreach ($target as $v) {
			$encryptData['request']['reservation'][$v] = $encrypt->encrypt($data['request']['reservation'][$v]);
		}
		return $encryptData;
	}

	// レスポンスデータ形式チェック
	protected function validateResponseData($data, $apiStatus) {
		if (empty($data)) {
			return false;
		}
		if (empty($data['response'])) {
			return false;
		}
		if (empty($data['response']['result'])) {
			return false;
		}
		if (!isset($data['response']['result']['status'])) {
			return false;
		}
		if (!is_bool($data['response']['result']['status'])) {
			return false;
		}
		if ($apiStatus == Constant::API_STATUS_RESERVATION) {
			if ($data['response']['result']['status']) {
				if (empty($data['response']['result']['reserveno'])) {
					return false;
				}
			}
		}
		return true;
	}

	// アラートメール送信
	private function sendAlertMail($body, $subject=NULL, $to_address=NULL) {
		// 題名指定ない場合はデフォルト値設定
		if (empty($subject)) {
			$subject = ALERT_EMAIL_SUBJECT;
		}

		// 開発の場合は題名に prefix 付ける
		if (! IS_PRODUCTION) {
			$subject = '【開発】'.$subject;
		}

		// 宛先指定ない場合はデフォルト値設定
		if (empty($to_address)) {
			$to_address = EMAIL_ADDRESS_SYS;
		}

		// メール送信に mail 関数利用
		mail($to_address, $subject, $body);
	}

	// 定型文付きアラートメール送信
	private function sendAlertMailWithBodyPrefix($body, $subject=NULL, $to_address=NULL) {
		// 本文に定型の prefix 付ける
		//$body_prefix  = 'IP      : ' . $_SERVER['SERVER_ADDR'] . PHP_EOL;
		//$body_prefix .= 'HOSTNAME: ' . gethostname()           . PHP_EOL;
		$body_prefix = 'HOSTNAME: ' . gethostname()           . PHP_EOL;
		//$body_prefix .= 'REMOTE_IP: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
		/*if (!isset($_SERVER['REMOTE_HOST']) || $_SERVER['REMOTE_HOST'] == '') {
			if (!empty($_SERVER['REMOTE_ADDR'])) {
				$body_prefix .= 'REMOTE_HOSTNAME: ' . gethostbyaddr($_SERVER['REMOTE_ADDR']) . PHP_EOL;
			}
		}*/
		if (!empty($_SERVER['PHP_SELF'])) {
			$body_prefix .= 'URL     : ' . $_SERVER['PHP_SELF'] . PHP_EOL;
		}
		$body = $body_prefix . PHP_EOL . $body;

		$this->sendAlertMail($body, $subject, $to_address);
	}

	// バジェットに送るエラー通知メールの本文
	private function getAdminMailBody($controlNumber/*, $domain*/) {
		// メッセージ
		$body = $this->getAdminMailMessage();

		// 予約内容
		$body .= $this->getAdminMailData($controlNumber);

		// 署名
		$body .= '―――――――――――――――――――――――――――――' . PHP_EOL;
		$body .= '株式会社アドベンチャー　スカイチケットレンタカー' . PHP_EOL;
		$body .= '〒' . COMPANY_ZIP . PHP_EOL;
		$body .= COMPANY_ADDRESS . PHP_EOL;
		$body .= '事業者様専用tel  :' . ADV_SETTLEMENT_TEL . PHP_EOL;
		$body .= 'お客様専用tel  :' . DISPLAY_RENTACAR_TEL . PHP_EOL;
		$body .= 'mail :' . EMAIL_ADDRESS_RENTACAR . PHP_EOL;
		//$body .= 'URL  :https://' . $domain . '/rentacar/' . PHP_EOL;
		$body .= 'URL  :https://skyticket.jp/rentacar/' . PHP_EOL;
		$body .= '―――――――――――――――――――――――――――――';

		return $body;
	}

	// バジェットに送るエラー通知メールに記載するメッセージ
	private function getAdminMailMessage() {
		$status = $this->postData['request']['reservation']['reservation_status_id'];

		$body = '';

		$body .= Constant::apiStatusNames()[$status] . '連携でエラーが発生しました。' . PHP_EOL;
		$body .= '弊社にて対応いたしますが、貴社への問合せが発生する場合があります。' . PHP_EOL;
		$body .= 'あらかじめご了承ください。' . PHP_EOL;
		$body .= PHP_EOL;
		$body .= '＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝' . PHP_EOL;
		$body .= '※このメールは自動配信されております。' . PHP_EOL;
		$body .= '　お問合せは文末の電話番号、またはメールアドレスまでお願いします。' . PHP_EOL;
		$body .= '＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝' . PHP_EOL;
		$body .= PHP_EOL;

		return $body;
	}

	// バジェットに送るエラー通知メールに記載する予約内容
	// ほぼ notification と同じ
	private function getAdminMailData($controlNumber) {
		$body = '';

		if (isset($this->postData)) {
			$reservation = $this->postData['request']['reservation'];

			$weekDay = array('日', '月', '火', '水', '木', '金', '土');
			$rentDateArray = explode(" ", date('Y年m月d日 w H:i', strtotime($reservation['rent_datetime'])));
			$returnDateArray = explode(" ", date('Y年m月d日 w H:i', strtotime($reservation['return_datetime'])));
			$rentDateTime = $rentDateArray[0] . '(' . $weekDay[$rentDateArray[1]] . ') ' . $rentDateArray[2];
			$returnDateTime = $returnDateArray[0] . '(' . $weekDay[$returnDateArray[1]] . ') ' . $returnDateArray[2];

			$basicPrice = 0;
			$dropOffPrice = 0;
			$nightFee = 0;
			$disclaimer = 0;
			foreach ($this->postData['request']['details'] as $v) {
				switch ($v['detail_type_id']) {
					case Constant::DETAIL_TYPE_BASICPRICE:
						$basicPrice = $v['amount'];
						break;
					case Constant::DETAIL_TYPE_DROPOFFPRICE:
						$dropOffPrice = $v['amount'];
						break;
					case Constant::DETAIL_TYPE_NIGHTFEE:
						$nightFee = $v['amount'];
						break;
					case Constant::DETAIL_TYPE_DISCLAIMER:
						$disclaimer = $v['amount'];
						break;
					default:
						break;
				}
			}

			$options = array();
			if (!empty($this->postData['request']['extra_options'])) {
				foreach ($this->postData['request']['extra_options'] as $v) {
					$options[] = $v['option_name'] . '×' . $v['count'] . ' ' . number_format($v['amount'] * $v['count']) . '円';
				}
			}

			$body .= '■お客様情報' . PHP_EOL;
			$body .= '管理番号 : ' . (!empty($controlNumber) ? $controlNumber : '不明') . PHP_EOL;
			$body .= '予約番号 : ' . $reservation['reservation_key'] . PHP_EOL;
			$body .= 'ご利用者名 : ' . $reservation['last_name'] . ' ' . $reservation['first_name'] . PHP_EOL;
			$body .= '電話番号 : ' . $reservation['tel'] . PHP_EOL;
			$body .= 'メールアドレス : ' . $reservation['email'] . PHP_EOL;
			$body .= PHP_EOL;

			$body .= '■ご予約詳細' . PHP_EOL;
			$body .= 'プラン名 : ' . $reservation['plan_name'] . PHP_EOL;
			$body .= '車両クラス : ' . $reservation['car_class_name'] . PHP_EOL;
			$body .= '受取店舗 : ' . $reservation['rent_shop_name'] . PHP_EOL;
			$body .= '受取日時 : ' . $rentDateTime . PHP_EOL;
			$body .= '返却店舗 : ' . $reservation['return_shop_name'] . PHP_EOL;
			$body .= '返却日時 : ' . $returnDateTime . PHP_EOL;
			$body .= 'ご利用人数 : 大人' . $reservation['adults_count'] . '名' .
				(!empty($reservation['children_count']) ? (' 子供' . $reservation['children_count'] . '名') : '') .
				(!empty($reservation['infants_count']) ? (' 幼児' . $reservation['infants_count'] . '名') : '') . PHP_EOL;
			$body .= '合計料金 : ' . number_format($reservation['total_amount']) . '円' . PHP_EOL;
			$body .= ' - 基本料金 : ' . number_format($basicPrice) . '円' . PHP_EOL;
			if (!empty($options)) {
				$body .= ' - オプション : ' . PHP_EOL;
				foreach ($options as $v) {
					$body .= '   ' . $v . PHP_EOL;
				}
			}
			if (!empty($disclaimer)) {
				$body .= ' - 免責補償料金 : ' . number_format($disclaimer) . '円' . PHP_EOL;
			}
			if (!empty($nightFee)) {
				$body .= ' - 深夜手数料 : ' . number_format($nightFee) . '円' . PHP_EOL;
			}
			if (!empty($dropOffPrice)) {
				$body .= ' - 乗捨料金 : ' . number_format($dropOffPrice) . '円' . PHP_EOL;
			}
			$body .= PHP_EOL;

			if (!empty($reservation['arrival_flight_number']) || !empty($reservation['departure_flight_number'])) {
				$body .= '■航空便情報' . PHP_EOL;
				if (!empty($reservation['arrival_flight_number'])) {
					$body .= '到着便 : ' . $reservation['arrival_flight_number'] . PHP_EOL;
				}
				if (!empty($reservation['departure_flight_number'])) {
					$body .= '出発便 : ' . $reservation['departure_flight_number'] . PHP_EOL;
				}
			}
			$body .= PHP_EOL;
		}

		return $body;
	}
}

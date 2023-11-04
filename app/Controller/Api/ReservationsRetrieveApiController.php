<?php
App::uses('BaseRestApiController', 'Controller');
App::uses('PlansApiController', 'Controller');

class ReservationsRetrieveApiController extends BaseRestApiController {
	public $components = array(
		'YotpoAPI', 'CancelFeeCalculation', 'PaymentEcon', 'ReservationAPISelect',
		'CancelPolicy', 'CancelDeadline', 'Validation', 'ReservationUtil',
	);

	public $uses = array(
		'ReservationsEditApiValidation',
		'Reservation',
		'ReservationStatus',
		'ReservationPrivilege',
		'ReservationChildSheet',
		'ReservationMail',
		'ReservationDetail',
		'CommodityItem',
		'ClientCard',
		'PaymentLog',
		'Maintenance',
		'CarClassReservation',
		'CancelFee',
		'Refund',
	);

	private $isEconMaintenance = false;

	public function beforeFilter() {
		parent::beforeFilter();
		$this->ApiCommon->setCorsHeader();
		// メール送信で使用するため上書き
		$this->domain = $_SERVER['HTTP_HOST'];

		$this->isEconMaintenance = $this->Maintenance->isEconMaintenance();
	}

	// 予約確認
	public function view($key) {
		if (empty($this->request->data)) {
			// パラメータなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		// バリデーション
		if (empty($this->request->data['tel'])) {
			// 電話番号なし
			throw new ApiException(array('tel' => '電話番号は必須です'));
		}
		if (!preg_match('/^[0-9]+$/', $this->request->data['tel'])) {
			// 電話番号形式エラー
			throw new ApiException(array('tel' => '電話番号が正しくありません'));
		}

		// 予約データ取得
		$result = $this->Reservation->getMypageDatas(
			$key,
			$this->request->data['tel']
		);

		if (empty($result) || empty($result['Reservation'])) {
			// 予約なし
			throw new ApiException(ApiException::NO_RESERVATION, 404);
		}

		$client = $result['Client'];
		$reservation = $result['Reservation'];
		$rentOffice = $result['RentOffice'];
		$returnOffice = $result['ReturnOffice'];
		unset($result);

		$reservationId = $reservation['id'];

		$plan = $this->requestAction(
			array('controller' => 'plans_api', 'action' => 'view'),
			array('pass' => array('id' => $reservation['commodity_item_id']))
		);

		// オプションデータ取得
		$reservationPrivilege = $this->ReservationPrivilege->getReservationPrivilegeData($reservationId);
		// チャイルドシートデータ取得
		$reservationChildSheet = $this->ReservationChildSheet->getReservationChildSheetData($reservationId);

		$dropOffNightFee = $this->ReservationDetail->getDropOffNightFee($reservationId);

		// 明細
		$details = array();
		foreach ((array)$dropOffNightFee as $value) {
			$details[] = array(
				'detailName' => $value['DetailType']['name'],
				'count' => (int)$value['ReservationDetail']['count'],
				'price' => (int)$value['ReservationDetail']['amount'],
			);
		}
		foreach ((array)$reservationChildSheet as $value) {
			$details[] = array(
				'detailName' => $value['Privilege']['name'],
				'count' => (int)$value['ReservationChildSheet']['count'],
				'price' => (int)$value['ReservationChildSheet']['price'],
			);
		}
		foreach ((array)$reservationPrivilege as $value) {
			$details[] = array(
				'detailName' => $value['Privilege']['name'],
				'count' => (int)$value['ReservationPrivilege']['count'],
				'price' => (int)$value['ReservationPrivilege']['price'],
			);
		}

		// 支払方法（カード取得）
		if (!empty($client['accept_card'])) {
			$clientCards = $this->ClientCard->getCardByClientId($client['id']);
			$clientCards = $clientCards[$client['id']];
		}

		// キャンセルポリシー
		$cancelPolicy = $this->CancelPolicy->getTextLines($client['id'], $reservation['rent_datetime']);
		// INCIDENT-3044 取消手続料の徴収を廃止する
		//$advCancelFee = $this->CancelPolicy->getAdvCancelFee();

		// お問い合わせデータ
		$reservationMail = $this->ReservationMail->getMailById($reservationId);

		// 予約ステータス一覧取得
		$reservationStatus = $this->ReservationStatus->findC('list', array('recursive' => -1,));

		// 事前決済/現地決済
		$isPaidInAdvance = $this->PaymentLog->hasPaymentInfo($reservationId);

		// キャンセル手仕舞い日時
		$cancelDeadlineDatetime = $this->CancelDeadline->getDatetime($reservationId);

		$this->responseData = array(
			'reservationKey' => $reservation['reservation_key'],
			'reservationDatetime' => $reservation['reservation_datetime'],
			'reservationStatus' => (int)$reservation['reservation_status_id'],
			'cancelDatetime' => $reservation['cancel_datetime'],
			'userInfo' => array(
				'lastName' => $reservation['last_name'],
				'firstName' => $reservation['first_name'],
				'email' => $reservation['email'],
				'tel' => $reservation['tel'],
				'adultCount' => (int)$reservation['adults_count'],
				'childCount' => (int)$reservation['children_count'],
				'infantCount' => (int)$reservation['infants_count'],
			),
			'flightNumber' => array(
				'arrival' => $reservation['arrival_flight_number'],
				'departure' => $reservation['departure_flight_number'],
			),
			'rentInfo' => array(
				'dateTime' => $reservation['rent_datetime'],
				'shop' => array(
					'shopId' => (int)$rentOffice['id'],
					'shopName' => $rentOffice['name'],
					'openTime' => $rentOffice['office_hours_from'],
					'closeTime' => $rentOffice['office_hours_to'],
					'openingHoursInfo' => (!empty($rentOffice['start_day']) && !empty($rentOffice['end_day'])) ?
						date('Y/m/d', strtotime($rentOffice['start_day'])) . '～' . date('Y/m/d', strtotime($rentOffice['end_day'])) . 'は営業時間が通常と異なります。' :
						'',
					'tel' => $rentOffice['tel'],
					'address' => trim($rentOffice['address']),
					'access' => $rentOffice['access_dynamic'],
				),
				'meetingInfo' => nl2br($rentOffice['rent_meeting_info'], false),
			),
			'returnInfo' => array(
				'dateTime' => $reservation['return_datetime'],
				'shop' => array(
					'shopId' => (int)$returnOffice['id'],
					'shopName' => $returnOffice['name'],
					'openTime' => $returnOffice['office_hours_from'],
					'closeTime' => $returnOffice['office_hours_to'],
					'openingHoursInfo' => (!empty($returnOffice['start_day']) && !empty($returnOffice['end_day'])) ?
						date('Y/m/d', strtotime($returnOffice['start_day'])) . '～' . date('Y/m/d', strtotime($returnOffice['end_day'])) . 'は営業時間が通常と異なります。' :
						'',
					'tel' => $returnOffice['tel'],
					'address' => trim($returnOffice['address']),
					'access' => $returnOffice['access_dynamic'],
				),
				'meetingInfo' => nl2br($returnOffice['return_meeting_info'], false),
			),
			'planInfo' => $plan,
			'priceInfo' => array(
				'totalPrice' => (int)$reservation['amount'],
				'details' => $details,
			),
			'clausePdf' => $client['clause_pdf'],
			'cancelPolicy' => $cancelPolicy,
		);
		return $this->responseData;
	}

	// 予約情報変更
	public function edit($key) {
		if (empty($this->request->data)) {
			// パラメータなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		$params = $this->request->data;

		// 不要な文字削除
		if (isset($params['editInfo']['tel'])) {
			$params['editInfo']['tel'] = $this->ReservationUtil->telNormalization($params['editInfo']['tel']);
		}
		if (isset($params['editInfo']['email'])) {
			$params['editInfo']['email'] = $this->ReservationUtil->mailNormalization($params['editInfo']['email']);
		}
		if (isset($params['editInfo']['flightNumber']['arrival'])) {
			$params['editInfo']['flightNumber']['arrival'] = $this->Validation->removeControlChars($params['editInfo']['flightNumber']['arrival']);
		}
		if (isset($params['editInfo']['flightNumber']['departure'])) {
			$params['editInfo']['flightNumber']['departure'] = $this->Validation->removeControlChars($params['editInfo']['flightNumber']['departure']);
		}

		// バリデーション
		$this->ReservationsEditApiValidation->set($params);
		if (!$this->ReservationsEditApiValidation->validates()) {
			throw new ApiException($this->ReservationsEditApiValidation->validationErrors);
		}

		$editInfo = $params['editInfo'];

		// 予約データ取得
		$result = $this->Reservation->getMypageDatas($key, $params['tel']);

		if (empty($result) || empty($result['Reservation'])) {
			// 予約なし
			throw new ApiException(ApiException::NO_RESERVATION, 404);
		}

		if (Constant::isCanceledStatus($result['Reservation']['reservation_status_id'])) {
			// 変更不可
			throw new ApiException(ApiException::RESERVE_NO_CHANGE);
		}

		$reservation = $result['Reservation'];
		$clientId = $result['Client']['id'];
		unset($result);

		$reservationId = $reservation['id'];
		$commodityItemId = $reservation['commodity_item_id'];

		$data = array();

		// 到着便
		if (isset($editInfo['flightNumber']['arrival']) &&
			strcmp($editInfo['flightNumber']['arrival'], $reservation['arrival_flight_number']) !== 0) {
			$this->ReservationUtil->reserveChangeFlg['airline'] = true;
			$data['arrival_flight_number'] = $editInfo['flightNumber']['arrival'];
		}
		// 出発便
		if (isset($editInfo['flightNumber']['departure']) &&
			strcmp($editInfo['flightNumber']['departure'], $reservation['departure_flight_number']) !== 0) {
			$this->ReservationUtil->reserveChangeFlg['airline'] = true;
			$data['departure_flight_number'] = $editInfo['flightNumber']['departure'];
		}
		// メールアドレス変更
		if (isset($editInfo['email']) &&
			strcmp($editInfo['email'], $reservation['email']) !== 0) {
			$this->ReservationUtil->reserveChangeFlg['mail'] = true;
			$data['email'] = $editInfo['email'];
		}
		// 電話番号変更
		if (isset($editInfo['tel']) &&
			strcmp($editInfo['tel'], $reservation['tel']) !== 0) {
			$this->ReservationUtil->reserveChangeFlg['tel'] = true;
			$data['tel'] = $editInfo['tel'];
		}
		// ご利用人数変更
		if (isset($editInfo['adultCount']) &&
			strcmp($editInfo['adultCount'], $reservation['adults_count']) !== 0) {
			$this->ReservationUtil->reserveChangeFlg['passenger_count'] = true;
			$data['adults_count'] = $editInfo['adultCount'];
		}
		if (isset($editInfo['childCount']) &&
			strcmp($editInfo['childCount'], $reservation['children_count']) !== 0) {
			$this->ReservationUtil->reserveChangeFlg['passenger_count'] = true;
			$data['children_count'] = $editInfo['childCount'];
		}

		if (empty($data)) {
			// 変更データなし
			throw new ApiException(ApiException::NO_CHANGE);
		}

		// 定員チェック
		if ($this->ReservationUtil->reserveChangeFlg['passenger_count']) {
			$carInfoList = $this->CommodityItem->getCarInfo($commodityItemId, true);
			$capacity = (int)$carInfoList[$commodityItemId]['CarModel'][0]['capacity'];
			$passengerCount = $this->ReservationUtil->calcPersonCount(
				(isset($data['adults_count']) ? $data['adults_count'] : $reservation['adults_count']),
				(isset($data['children_count']) ? $data['children_count'] : $reservation['children_count']),
				$reservation['infants_count']
			);

			if ($capacity < $passengerCount) {
				// 定員オーバー
				throw new ApiException(ApiException::CAPACITY_OVER);
			}
		}

		$this->Reservation->id = $reservationId;

		// レンナビ予約連携API用
		if ($this->ReservationAPISelect->isRennaviApiTarget($clientId)) {
			if ($reservation['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_FIXED) {
				$data['rennavi_status'] = Constant::RENNAVI_STATUS_RESERVE_CHANGED;
				$this->log(sprintf("ReservationId : %s, ClientId : %s, ReservationStatusId : %s, RennaviStatus : %s 予約確認済(料金変更あり)",
					$reservationId, $clientId, $reservation['reservation_status_id'], $reservation['rennavi_status']),
					'debug'
				);
			}
		}

		$reservationAPI = null;
		$apiErrorMailRequired = false;

		try {
			$this->Reservation->begin();
			if (!$this->Reservation->save($data)) {
				// 予約データ更新失敗
				throw new ApiException(ApiException::RESERVE_UPDATE_ERROR, 500);
			}

			// 予約マスタのAPIステータスが対象外ではない場合
			// 対象外：連携していない会社のデータ or 連携開始前のデータ
			if ($reservation['api_status_id'] != Constant::API_STATUS_EXCLUDED) {
				$componentName = $this->ReservationAPISelect->getApiComponentName($clientId);
				if (!empty($componentName)) {
					$apiErrorMailRequired = true;

					// 会社別コンポーネントロード
					$reservationAPI = $this->Components->load($componentName);

					// 送信データ取得
					$reservationAPI->setMypageReservationData($reservationId, Constant::API_STATUS_CHANGE);

					// 送信
					list($success, $apiResult) = $reservationAPI->sendReservationData();
					if ($success && !$apiResult['status']) {
						$apiErrorMailRequired = false;
						// 変更連携失敗
						$errorString = sprintf("変更連携が失敗しました。(%s)", (!empty($apiResult['message']) ? $apiResult['message'] : ''));
						throw new ApiException($errorString, 500);
					} else if (!$success) {
						// 変更連携失敗
						$errorString = '変更連携中に何らかのエラーが発生しました。';
						throw new ApiException($errorString, 500);
					}
				}
			}

			$this->Reservation->commit();
			$apiErrorMailRequired = false;

		} catch (Exception $e) {
			$errorString = sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString());

			$this->Reservation->rollback();

			// TODO 今だけメール送らないようにしておく
			$apiErrorMailRequired = false;
			if ($apiErrorMailRequired) {
				$reservationAPI->sendAlertFromMypage($reservation['control_number'], $this->domain);
			}

			$this->log(sprintf("ReservationId : %s\n%s", $reservationId, $errorString), 'error');
			if (!empty($data['arrival_flight_number']) || !empty($data['departure_flight_number'])) {
				$this->log(sprintf("ReservationId : %s, arrival_flight_number : %s, departure_flight_number : %s",
					$reservationId, $data['arrival_flight_number'], $data['departure_flight_number'])
				);
			}

			throw $e;
		}

		// 予約内容変更通知
		$this->ReservationUtil->sendNotificationMail($reservationId, 'modify');

			// サンプル値
		$this->response->statusCode(204);
		$this->responseData = [];
	}

	// 予約キャンセル
	public function cancel($key) {
		if (empty($this->request->data)) {
			// パラメータなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		// バリデーション
		if (empty($this->request->data['tel'])) {
			// 電話番号なし
			throw new ApiException(array('tel' => '電話番号は必須です'));
		}
		if (!preg_match('/^[0-9]+$/', $this->request->data['tel'])) {
			// 電話番号形式エラー
			throw new ApiException(array('tel' => '電話番号が正しくありません'));
		}
		if (empty($this->request->data['cancelReason'])) {
			// キャンセル理由なし
			throw new ApiException(array('cancelReason' => 'キャンセル理由が未入力です'));
		}

		// 予約データ取得
		$result = $this->Reservation->getMypageDatas(
			$key,
			$this->request->data['tel']
		);

		if (empty($result) || empty($result['Reservation'])) {
			// 予約なし
			throw new ApiException(ApiException::NO_RESERVATION, 404);
		}

		$client = $result['Client'];
		$reservation = $result['Reservation'];
		unset($result);

		$reservationId = $reservation['id'];

		$cancelDeadlineDatetime = $this->CancelDeadline->getDatetime($reservationId);
		if ($cancelDeadlineDatetime <= date('Y-m-d H:i:s')) {
			// キャンセル期限切れ
			throw new ApiException(ApiException::CANCEL_DEADLINE);
		}

		$isPaidInAdvance = $this->PaymentLog->hasPaymentInfo($reservationId);
		if ($this->isEconMaintenance && $isPaidInAdvance) {
			// メンテナンス中
			throw new ApiException(ApiException::UNDER_MAINTENANCE, 503);
		}

		if ($reservation['reservation_status_id'] != Constant::STATUS_RESERVATION) {
			// キャンセル不可
			throw new ApiException(ApiException::RESERVE_NO_CANCEL);
		}

		// キャンセル処理
		$data = array(
			'id' => $reservationId,
			'reservation_status_id' => Constant::STATUS_CANCEL,
			'cancel_flg' => 1,
			'cancel_datetime' => date('Y-m-d H:i:s'),
			'cancel_staff_id' => 0,
			'cancel_reason_id' => 1,
			'cancel_remark' => $this->request->data['cancelReason'],
		);

		$paymentStatus = '';
		if (!empty($reservation['payment_status'])) {
			// TODO 決済しない前提で進めているのでここでエラーになるので一旦コメント
			// $paymentStatus = $this->__getPaymentStatus($reservation['Reservation']['reservation_status_id'], $reservation['Reservation']['cancel_reason_id']);
			// $reservation['Reservation']['payment_status'] = $paymentStatus;
		}

		// レンナビ予約連携API用
		if ($this->ReservationAPISelect->isRennaviApiTarget($client['id'])) {
			if ($reservation['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE) {
				$data['rennavi_status'] = Constant::RENNAVI_STATUS_CANCEL_NOTIME;
			} elseif ($reservation['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_FIXED ||
					$reservation['rennavi_status'] == Constant::RENNAVI_STATUS_RESERVE_CHANGED) {
				$data['rennavi_status'] = Constant::RENNAVI_STATUS_CANCEL_USER;
			}
		}

		$reservationAPI = null;
		$apiErrorMailRequired = false;

		try {
			$this->Reservation->begin();

			// 在庫から削除
			$this->CarClassReservation->updateAll(array('delete_flg' => 1), array('reservation_id' => $reservationId));

			$saveResult = $this->Reservation->save($data, false);
			if (!$this->Reservation->save($data)) {
				// 予約キャンセル失敗
				throw new ApiException(ApiException::RESERVE_CANCEL_ERROR, 500);
			}

			// 返金処理
			if ($paymentStatus == 'REFUNDED') {
				$cal_ret = $this->CancelFeeCalculation->calculate($reservationId); // キャンセル料明細に登録
				if (is_array($cal_ret) && $cal_ret['id'] != 0) { // キャンセル料明細に登録できた場合
					if ($this->PaymentEcon->refundByReservationId($reservationId, ((int)$cal_ret['sum']))) { // 決済返金
						$this->Refund->save([ // 返金テーブルに返金済みとして登録
							'reservation_id' => $reservationId,
							'amount' => ($reservation['amount'] - $cal_ret['sum']),
							'status' => Constant::STATUS_REFUNDED,
							'refunded' => date('Y-m-d H:i:s')
						]);
					} else {
						$this->Refund->save([ // 返金テーブルに返金予定として登録
							'reservation_id' => $reservationId,
							'amount' => ($reservation['amount'] - $cal_ret['sum']),
							'status' => Constant::STATUS_SCHEDULED_REFUND,
							'remarks' => '自動返金失敗につき'
						]);
					}
				}
			} elseif ($paymentStatus == 'REFUND_REQUEST') {
				$cal_ret = $this->CancelFeeCalculation->calculate($reservationId); // キャンセル料明細に登録
				if (is_array($cal_ret) && $cal_ret['id'] == 0) { // キャンセル料明細に登録できた場合
					$this->Refund->save([ // 返金テーブルに登録
						'reservation_id' => $reservationId,
						'amount' => ($reservation['amount'] - $cal_ret['sum']),
						'status' => Constant::STATUS_SCHEDULED_REFUND
					]);
				}
			}

			// 予約マスタのAPIステータスが対象外ではない場合
			// 対象外：連携していない会社のデータ or 連携開始前のデータ
			if ($reservation['api_status_id'] != Constant::API_STATUS_EXCLUDED) {
				$componentName = $this->ReservationAPISelect->getApiComponentName($client['id']);
				if (!empty($componentName)) {
					$apiErrorMailRequired = true;

					// 会社別コンポーネントロード
					$reservationAPI = $this->Components->load($componentName);

					// 送信データセット
					$reservationAPI->setMypageReservationData($reservationId, Constant::API_STATUS_CANCEL);

					// 送信
					list($success, $result) = $reservationAPI->sendReservationData();
					if ($success && !$result['status']) {
						$apiErrorMailRequired = false;
						// 変更連携失敗
						$errorString = sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
						throw new ApiException($errorString, 500);
					} else {
						// 変更連携失敗
						$errorString = 'キャンセル連携中に何らかのエラーが発生しました。';
						throw new ApiException($errorString, 500);
					}
				}
			}

			$this->Reservation->commit();
			$apiErrorMailRequired = false;

		} catch (Exception $e) {
			$errorString = sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString());

			$this->Reservation->rollback();

			// TODO 今だけメール送らないようにしておく
			$apiErrorMailRequired = false;
			if ($apiErrorMailRequired) {
				$reservationAPI->sendAlertFromMypage($reservation['control_number'], $this->domain);
			}

			$this->log(sprintf("ReservationId : %s\n%s", $reservationId, $errorString), 'error');

			throw $e;
		}

		// 予約キャンセル通知
		$this->ReservationUtil->sendNotificationMail($reservationId, 'cancel');

		Configure::load('YotpoConfig', 'default');
		$yotpoConfig = Configure::read('Yotpo');
		if ($yotpoConfig['is_active']) {
			//BOC Yotpo delete order
			$yotpoOrderIDsToDelete = array($reservationId);
			$this->YotpoAPI->deleteOrder($yotpoOrderIDsToDelete);
			//EOC Yotpo delete order
		}

		$this->response->statusCode(204);
		$this->responseData = [];
	}

}

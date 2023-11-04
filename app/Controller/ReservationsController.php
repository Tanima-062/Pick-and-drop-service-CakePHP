<?php

App::uses('AppController', 'Controller');
App::uses('Validation', 'Utility');

require_once("log_class.php");
require_once("encrypt_class.php");
require_once("db_class.php");
require_once("user_class.php");
require_once("application_class.php");
require_once("area_class.php");
require_once("classification_class.php");

/**
 * Reservations Controller
 *
 * @property Reservations $Reservations
 */
class ReservationsController extends AppController {

	public $components = array('Cookie', 'Session', 'Security', 'YotpoAPI', 'Validation',
		'PaymentEcon', 'PaymentAPI', 'ReservationAPISelect', 'BreadCrumb', 'CancelPolicy', 'TaxRate', 'ReservationUtil'
	);
	public $uses = array(
		'Reservation', 'CommodityItem', 'Commodity', 'Office', 'CommodityPrivilege', 'Privilege', 'CommodityTerm',
		'OfficeStockGroup', 'Client', 'ReservationMail', 'ReservationChildSheet', 'ReservationPrivilege', 'ReservationDetail',
		'CarClassReservation', 'ClientEmail', 'CommodityImage', 'ClientCard', 'CommodityEquipment', 'Equipment',
		'DropOffAreaRate', 'DisclaimerCompensation', 'CancelFee' , 'PaymentToken', 'Maintenance', 'Areas', 'Recommend', 'SettlementCompany'
	);
	// 車両年式
	public $newCarRegistration = array(
		1 => '新車登録1年以内',
		2 => '新車登録2年以内',
		3 => '新車登録3年以内',
		4 => '新車登録4年以内',
		5 => '新車登録5年以上'
	);
	// 喫煙・禁煙
	public $smokingCarList = array(
		0 => '禁煙車',
		1 => '喫煙車',
		2 => '指定なし',
	);
	// planのパラメータエラー時のView
	private $planErrorView = '/Errors/error404';

	// エラーでplanに戻された場合の文言
	private $reserveErrorMessage;
	private $isPaymentAPI = false;
	private $areas = [];

	function beforeFilter() {
		parent::beforeFilter();

		$this->isPaymentAPI = $this->Maintenance->isPaymentAPI();

		// Ajaxの場合はbeforeFilterの処理を通さない
		if ($this->request->is('ajax') && $this->action != 'prepare_payment' && $this->action != 'save_payment' && $this->action != 'store_log') {
			exit;
		}

		// HTTP でない場合に実行するメソッド名
		$this->Security->blackHoleCallback = 'forceSSL';
		$this->Security->requireSecure();
		$this->Security->validatePost = false;
		$this->Security->csrfCheck = false;

		// robots noindex
		$this->set('meta_robots', 'noindex');

		$this->set('smokingCarList', $this->smokingCarList);

		$this->set('paymentApi', $this->isPaymentAPI);

		// 予約失敗メッセージ
		// ※ rentacar/app/Controller/MypagesController.phpにも同じものを入れているため、メッセージ変更の場合は一緒に修正する
		$this->reserveErrorMessage = '予約手続きに失敗しました。<br>お手数ですが改めてご予約いただくか、下記までお問合せください。<br>スカイチケットレンタカーサポート<br>'
									. 'お問い合わせ先：<a href="tel:' . str_replace('-', '', DISPLAY_RENTACAR_TEL) . '">'
									. DISPLAY_RENTACAR_TEL . '</a><br>平日：10:00〜18:00 / 土日祝日：10:00〜18:00';
	}

	public function forceSSL() {
		$this->redirect("https://" . env('SERVER_NAME') . $_SERVER['REQUEST_URI']);
	}

	private function __sessionUniqIdCheck($postSessionUniqId) {
		$sessionUniqId = '';
		if ($this->Session->check('reservation.uniqId')) {
			$sessionUniqId = $this->Session->read('reservation.uniqId');
		}
		if ($postSessionUniqId != $sessionUniqId) {
			$this->Session->write('message.sessionUniq', 'データの不整合がありました。もう一度予約をお願いします。');
			if ($this->Session->check('reservation.redirectPlan')) {
				$redirectUrl = $this->Session->read('reservation.redirectPlan');
			} else {
				$redirectUrl = '/';
			}
			$this->redirect($redirectUrl);
		}
	}

	/**
	 * お客様情報入力
	 */
	public function step1() {
		$this->log($this->Session->id().'[reservation/step1]', LOG_DEBUG);
		// リクエスト値が無いのは異常
		if (empty($this->request->data) && empty($this->params['pass'][0])) {
			$this->response->statusCode(404);
			$this->render($this->planErrorView);
			return;
		}

		// ログイン
		$login_error_flg = false;
		if (isset($this->data['email']) && isset($this->data['password'])) {
			$arr = ["email" => $this->data["email"], "password" => $this->data["password"]];
			if (!_doLogin($arr)) {
				$login_error_flg = true;
			} else {
				//ログイン完了後...
				if (isset($this->data['ckbRememberLogin']) && !empty($_SESSION["user_id"])) {
					//オートログインのためにユーザーにトークンを設定する
					_processRememberMe($this->data['ckbRememberLogin'], $_SESSION["user_id"]);
				}
			}
		}

		// ログイン済みの場合はユーザー情報取得
		$login_flg = false;
		$application_user = array();
		if (_isLogin()) {
			$application_user = _getLoginUser();
			if (!empty($application_user)) {
				$login_flg = true;
			}
		}

		if (!empty($this->params['pass'][0])) {
			$this->request->data['Reservation']['uniqId'] = $this->params['pass'][0];
		}

		// 自画面「ログインして購入」から呼び出された場合
		if (!empty($this->data['Login']['uniqId'])) {
			$this->request->data['Reservation']['uniqId'] = $this->data['Login']['uniqId'];
		}

		$redirectFlg = false;

		// ユニークセッションIDチェック
		$this->__sessionUniqIdCheck($this->data['Reservation']['uniqId']);

		if ($this->Session->check('message')) {
			$sessionMessage = $this->Session->read('message');
			$this->set('sessionMessage', $sessionMessage);
			$this->Session->delete('message');
		}
		if ($this->Session->check('reservation.plan')) {
			if (!$this->request->is('post')) {
				$this->request->data = json_decode($this->Session->read('reservation.plan'), true);
			}
			// 自画面「ログインして購入」から呼び出された場合
			if (!empty($this->data['Login']['uniqId'])) {
				$this->request->data = json_decode($this->Session->read('reservation.plan'), true);
			}
		}

		if ($this->Session->check('reservation.step1')) {
			$sessionPostData = json_decode($this->Session->read('reservation.step1'), true);
			$this->set('sessionPostData', $sessionPostData);
		}
		if (!empty($sessionPostData)) {
			$this->request->data['Reservation'] = array_merge($this->request->data['Reservation'], $sessionPostData['Reservation']);
		}
		$commodityItemId = $this->request->data['Reservation']['commodityItemId'];
		$carInfoList = $this->CommodityItem->getCarInfo($commodityItemId);
		$carInfoList = $carInfoList[$commodityItemId];

		// 営業所が空港送迎に対応しているかチェック
		$fromOffice = $this->Office->getOfficeRentReturn($this->request->data['Reservation']['from_office']);
		$returnOffice = $this->Office->getOfficeRentReturn($this->request->data['Reservation']['return_office']);
		if (!empty($fromOffice['Office']['airport_id']) && $fromOffice['Landmark']['landmark_category_id'] == '1') {
			// ランドマークカテゴリが空港の場合のみ
			$methodOfTransport = $fromOffice['OfficeSupplement']['method_of_transport'];
			if ($methodOfTransport == 1 || $methodOfTransport == 2) {
				$this->set('arrivalAirport', true);
			}
		}
		if (!empty($returnOffice['Office']['airport_id']) && $returnOffice['Landmark']['landmark_category_id'] == '1') {
			// ランドマークカテゴリが空港の場合のみ
			$methodOfTransport = $returnOffice['OfficeSupplement']['method_of_transport'];
			if ($methodOfTransport == 1 || $methodOfTransport == 2) {
				$this->set('departureAirport', true);
			}
		}

		$dateFrom = date('Y-m-d', strtotime($this->data['Reservation']['from']));

		// プランデータを表示するための共通処理
		$this->__planView($this->request->data['Reservation']['commodityItemId'], $dateFrom);
		$estimationTotalPrice = $this->request->data['Reservation']['estimationTotalPrice'];

		// 乗り捨て料金が設定チェック
		if ($fromOffice['Office']['id'] != $returnOffice['Office']['id']) {
			$dropOffAreaRateData = $this->DropOffAreaRate->getDropOffAreaPrice(
				$this->request->data['Reservation']['from_office'],
				$this->request->data['Reservation']['return_office'],
				$this->request->data['Reservation']['carClassId']
			);
			if (!isset($dropOffAreaRateData)) {
				$redirectFlg = true;
				$this->Session->write('message.privilege', $returnOffice['Office']['name'] . 'への乗捨てはできません。');
			}
		}

		$confirmation = array();

		if (empty($this->data) || $this->Session->check('reservation.success')) {
			$redirectFlg = true;
		} else {
			if (!empty($this->data['sheet'])) {
				$this->request->data['sheet'] = array_filter($this->data['sheet']);
			}
			if (!empty($this->data['privilege'])) {
				$this->request->data['privilege'] = array_filter($this->data['privilege']);
			}

			if (empty($this->data['Reservation']['children'])) {
				$this->request->data['Reservation']['children'] = 0;
			}
			if (empty($this->data['Reservation']['infants'])) {
				$this->request->data['Reservation']['infants'] = 0;
			}
			// 車両台数
			$this->request->data['Reservation']['cars_count'] = 1;

			// 乗車人数
			$peopleCnt = $this->ReservationUtil->calcPersonCount(
				$this->data['Reservation']['adults'],
				$this->data['Reservation']['children'],
				$this->data['Reservation']['infants']
			);

			if ($this->data['Reservation']['adults'] == 0) {
				$redirectFlg = true;
				$this->Session->write('message.people', '大人が選択されていません。');
			}

			// 定員チェック
			$capacity = 999;

			// 車種から最小定員数を取得
			foreach ($carInfoList['CarModel'] as $carModel) {
				if ($carModel['capacity'] < $capacity) {
					$capacity = $carModel['capacity'];
				}
			}

			if ($peopleCnt > $capacity) {
				$redirectFlg = true;
				$this->Session->write('message.capacity', 'ご利用人数が車両の定員をオーバーしています。子供・幼児を含めた人数が定員におさまるように設定を確認してください。');
			}
			// シートチェック
			if (!empty($this->data['Reservation']['infants']) && empty($this->data['sheet'])) {
				$redirectFlg = true;
				$this->Session->write('message.sheet', 'チャイルドシートが選択されていません。幼児の乗車にはオプションのチャイルドシートの選択が必須です。');
			}
			// 各オプションの最大数チェック
			if (!empty($this->data['privilege'])) {
				$maxLimitCheck = $this->Privilege->maxLimitCheck($this->data['privilege']);
				if (!$maxLimitCheck) {
					$redirectFlg = true;
					$this->Session->write('message.privilege', 'オプションの最大数を超えています。');
				}
			}
			// 商品受付締切時間チェック
			$acceptanceDeadlineTime = $this->CommodityTerm->acceptanceDeadlineTime($this->data['Reservation']['commodityId'], $this->data['Reservation']['from']);
			if (!empty($acceptanceDeadlineTime)) {
				$redirectFlg = true;
				$this->Session->write('message.deadLine', $acceptanceDeadlineTime);
			}
			// 商品アイテムデータ取得
			$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($this->data['Reservation']['commodityItemId'], $dateFrom);
			// 在庫チェック
			$stockCheckParams = array(
				'from_office' => $this->data['Reservation']['from_office'],
				'from' => $this->data['Reservation']['from'],
				'to' => $this->data['Reservation']['to'],
				'cars_count' => $this->data['Reservation']['cars_count'],
			);
			$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $stockCheckParams);
			if (empty($remainingStock) || empty($remainingStock[$this->data['Reservation']['from_office']])) {
				$redirectFlg = true;
				$this->Session->write('message.stock', '申し訳ございません。<br>他のお客様のご予約により在庫がなくなってしまったため、本プランはご予約できません。<br>トップページに戻り、別のプランにて再度ご予約申込みお願いいたします。');
			}

			if (!$this->Session->check('reservation.plan')) {
				$sessionData = json_encode($this->data);
				// セッション書き込み
				if (!$this->Session->check('referer.plan')) {
					$this->Session->write('referer.plan', $this->referer());
				}
				$this->Session->write('reservation.plan', $sessionData);
			}

			if (isset($this->data['Reservation'])) {
				$requestData = $this->data;
				list($dayNight, $period, $period24) = $this->ReservationUtil->getPeriodArray($requestData['Reservation']['from'], $requestData['Reservation']['to']);

				$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($requestData['Reservation']['commodityId'], $period, $period24);
				$privilegeOption = array();
				foreach ($commodityPrivilegeData as $value) {
					if (!empty($requestData['sheet']) && !empty($requestData['sheet'][$value['Privilege']['id']])) {
						$description = $value['Privilege']['name'];
						if ($requestData['sheet'][$value['Privilege']['id']] > 1) {
							$description .= '×' . $requestData['sheet'][$value['Privilege']['id']];
						}
						$price = number_format($value[0]['Sum'] * $requestData['sheet'][$value['Privilege']['id']]);
						$privilegeOption[] = array($description, $price);
					}
					if (!empty($requestData['privilege']) && !empty($requestData['privilege'][$value['Privilege']['id']])) {
						$description = $value['Privilege']['name'];
						if ($requestData['privilege'][$value['Privilege']['id']] > 1) {
							$description .= '×' . $requestData['privilege'][$value['Privilege']['id']];
						}
						$price = number_format($value[0]['Sum'] * $requestData['privilege'][$value['Privilege']['id']]);
						$privilegeOption[] = array($description, $price);
					}
				}
				// 乗り捨て料金・深夜手数料取得
				$dropOffLateNightPrice = $this->DropOffAreaRate->dropOffLateNight(
					$requestData['Reservation']['from_office'],
					$requestData['Reservation']['return_office'],
					$requestData['Reservation']['carClassId'],
					date('H:i', strtotime($requestData['Reservation']['from'])),
					date('H:i', strtotime($requestData['Reservation']['to']))
				);

				// お支払方法
				// デフォルトは現地決済（従来通り）
				$paymentMethod = !empty($requestData['Reservation']['payment_method']) ? $requestData['Reservation']['payment_method'] : 0;

				$weekday = array('日', '月', '火', '水', '木', '金', '土');
				$fromW = $weekday[date('w', strtotime($requestData['Reservation']['from']))];
				$toW = $weekday[date('w', strtotime($requestData['Reservation']['to']))];
				$fromTime = date('H:i', strtotime($requestData['Reservation']['from']));
				$toTime = date('H:i', strtotime($requestData['Reservation']['to']));
				$fromString = date('Y年m月d日', strtotime($requestData['Reservation']['from'])) . '（' . $fromW . '）' . $fromTime;
				$toString = date('Y年m月d日', strtotime($requestData['Reservation']['to'])) . '（' . $toW . '）' . $toTime;

				$confirmationData = $this->Commodity->getConfirmationCommodity($requestData);

				if (!empty($confirmationData)) {
					$confirmation = array(
						'clientId' => $confirmationData['Client']['id'],
						'clientName' => $confirmationData['Client']['name'],
						'rentOfficeName' => $confirmationData['RentOffice']['office_name'],
						'rentOfficeTel' => $confirmationData['RentOffice']['office_tel'],
						'rentOfficeMeetingInfo' => $confirmationData['RentOffice']['rent_meeting_info'],
						'returnOfficeName' => $confirmationData['ReturnOffice']['office_name'],
						'returnOfficeTel' => $confirmationData['ReturnOffice']['office_tel'],
						'returnOfficeMeetingInfo' => $confirmationData['ReturnOffice']['return_meeting_info'],
						'cancelPolicy' => $this->CancelPolicy->getTextLines($confirmationData['Client']['id'], $requestData['Reservation']['from']),
						'clientCancelPolicy' => $confirmationData['Client']['cancel_policy'],
						'acceptCash' => $confirmationData['Client']['accept_cash'],
						'acceptCard' => $confirmationData['Client']['accept_card'],
						'precautions' => $confirmationData['Client']['precautions'],
						'from' => $fromString,
						'to' => $toString,
						'estimationTotalPrice' => $requestData['Reservation']['estimationTotalPrice'],
						'adults' => $requestData['Reservation']['adults'],
						'children' => $requestData['Reservation']['children'],
						'infants' => $requestData['Reservation']['infants'],
						'privilegeOption' => $privilegeOption,
						'dropOffLateNight' => $dropOffLateNightPrice,
					);
				}

				$cancelFreeLimit = $this->CancelFee->getCancelFreeLimit($confirmationData['Client']['id']);
				$cancelFreeLimitDay = preg_replace("/[^0-9]*/s", "", $cancelFreeLimit);
				$today = date("Y-m-d");
				$cancelFreeDate = date("Y-m-d", strtotime($requestData['Reservation']['from'] . "-" . $cancelFreeLimitDay . " day"));
				$isExpiredCancelLimit = strtotime($cancelFreeDate) < strtotime($today) ? true : false;
			}
		}

		if ($redirectFlg) {
			$this->redirect($this->Session->read('referer.plan'));
		}

		if ($this->Session->check('referer.plan')) {
			$refererPlan = $this->Session->read('referer.plan');
			$this->set('refererPlan', $refererPlan);
		}

		$this->set(compact('application_user', 'login_flg', 'login_error_flg', 'remainingStock', 'estimationTotalPrice', 'confirmation', 'paymentMethod', 'cancelFreeLimit', 'isExpiredCancelLimit'));

		$this->set('title_for_layout', '予約 STEP1');
		$this->set('h1_for_layout', '予約 STEP1');
		$this->set('top_txt', 'お客様情報入力。');
		$this->set('description_for_layout', 'お客様情報入力。');

		if (!$this->isPaymentAPI) {
			$this->set('econ_jsf_url', $this->PaymentEcon->getJsfUrl());
		}

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setReservations($this->action);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_step1() {
		$this->step1();
	}

	public function prepare_payment() {
		$this->autoRender = false;
		if ($this->request->is('ajax')) {

			// 申込合計金額計算
			$estimatedTotalPriceAll = 0;
			if ($this->Session->check('reservation.plan')) {
				$reservationPlan = json_decode($this->Session->read('reservation.plan'), true);
				$estimatedTotalPriceAll = $reservationPlan['Reservation']['estimationTotalPrice'];
			}

			$family_name = (!empty($this->request->data['family_name'])) ? $this->request->data['family_name'] : '';
			$first_name = (!empty($this->request->data['first_name'])) ? $this->request->data['first_name'] : '';
			$tel = (!empty($this->request->data['tel'])) ? $this->request->data['tel'] : '';
			$email = (!empty($this->request->data['email'])) ? $this->request->data['email'] : '';

			//$this->log($this->Session->id().'[prepare_payment]request_data:'.print_r($this->request->data, true), LOG_DEBUG);

			// ログインしていない場合はユーザ情報を作成する必要があるため、validateする
			$valid_param = [];
			if (!empty($family_name)) {
				$valid_param['last_name'] = $family_name;
			}
			if (!empty($first_name)) {
				$valid_param['first_name'] = $first_name;
			}
			if (!empty($tel)) {
				$valid_param['tel'] = $tel;
			}
			if (!empty($email)) {
				$valid_param['email'] = $email;
			}

			$valid_ret = $this->Validation->validatePersonalInfo($valid_param);

			if (!$valid_ret) {
				$this->log($this->Session->id().'[prepare_payment] valid_ret :'.$valid_ret, LOG_DEBUG);
				$message = '';
				foreach ($this->Session->read('message') as $key => $value) {
					$message .= $value."<br>"; // 値は1つしか入らないはず
				}
				// 入力後の値をフォームに維持したり、htmlのアラートを出したりするのが面倒なので、messageがあったら
				// 画面側ではそのままsubmitする
				return json_encode([
					'message' => $message
				]);
			}

			// ユーザ情報が変わっていればセッションを更新する
			if (
				$this->Session->read('payment.econ.family_name') != $family_name ||
				$this->Session->read('payment.econ.first_name') != $first_name ||
				$this->Session->read('payment.econ.tel') != $tel ||
				$this->Session->read('payment.econ.email') != $email
			) {
				$this->Session->write('payment.econ.family_name', $family_name);
				$this->Session->write('payment.econ.first_name', $first_name);
				$this->Session->write('payment.econ.tel', $tel);
				$this->Session->write('payment.econ.email', $email);
				$this->Session->write('payment.econ.user_equal', false);
			} else {
				$this->Session->write('payment.econ.user_equal', true);
			}

			//$this->log($this->Session->id().'[prepare_payment]payment.econ:'.print_r($this->Session->read('payment.econ'),true), LOG_DEBUG);

			$response_data_arr = [];
			if (!$this->isPaymentAPI){
				$econ_token = $this->PaymentEcon->createToken($estimatedTotalPriceAll);
				$response_data_arr['session_token'] = (!empty($econ_token['session_token'])) ? $econ_token['session_token'] : '';
				$response_data_arr['request_id'] = (!empty($econ_token['request_id'])) ? $econ_token['request_id'] : '';
			}

			$this->log($this->Session->id().'[prepare_payment]response_data_arr:'.print_r($response_data_arr, true), LOG_DEBUG);

			return json_encode($response_data_arr);
		}
	}

	/**
	 * AjaxによるJS処理のエラーログ収集
	 */
	public function store_log() {
		$this->autoRender = false;
		if ($this->request->is('ajax')) {
			$info = $this->request->data['info'];
			$status = $this->request->data['status'];
			$this->log(sprintf('%s:[info]%s, [status]%s', $this->Session->id(), $info, $status), LOG_DEBUG);
			return json_encode(['ret' => 'ok']);
		}
	}

	/**
	 * 予約確認ページ
	 */
	public function step2() {
		$this->log($this->Session->id().'[reservation/step2]', LOG_DEBUG);
		// リクエスト値が無いのは異常
		if (empty($this->request->data) && empty($this->params['pass'][0])) {
			$this->log($this->Session->id().'[reservation/step2]404 error', LOG_DEBUG);
			$this->response->statusCode(404);
			$this->render($this->planErrorView);
			return;
		}

		if (!empty($this->params['pass'][0])) {
			$this->request->data['Reservation']['uniqId'] = $this->params['pass'][0];
		}

		$redirectFlg = false;
		$redirectUrl = '/';

		// ユニークセッションIDチェック
		$this->__sessionUniqIdCheck($this->data['Reservation']['uniqId']);

		if (empty($this->data['Reservation']) || $this->Session->check('reservation.success')) {
			$redirectFlg = true;
		} else {
			$this->request->data['Reservation']['tel'] = $this->ReservationUtil->telNormalization($this->data['Reservation']['tel']);
			$this->request->data['Reservation']['email'] = $this->ReservationUtil->mailNormalization($this->data['Reservation']['email']);
			if (isset($this->request->data['Reservation']['arrival'])) {
				$this->request->data['Reservation']['arrival'] = $this->Validation->removeControlChars($this->request->data['Reservation']['arrival']);
			} else {
				$this->request->data['Reservation']['arrival'] = '';
			}
			if (isset($this->request->data['Reservation']['departure'])) {
				$this->request->data['Reservation']['departure'] = $this->Validation->removeControlChars($this->request->data['Reservation']['departure']);
			} else {
				$this->request->data['Reservation']['departure'] = '';
			}
			$sessionData = json_encode($this->request->data);
			// セッション書き込み
			$this->Session->write('referer.step1', '/reservations/step1/' . $this->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
			$this->Session->write('reservation.step1', $sessionData);
		}

		// バリデーション
		if (!$this->Validation->validatePersonalInfo($this->request->data['Reservation'])) {
			$this->redirect('/reservations/step1/' . $this->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
		}

		if (!$this->Session->check('reservation.plan') || !$this->Session->check('reservation.step1')) {
			$redirectFlg = true;
		} else {
			$sessionReservationData = $this->Session->read('reservation');
			$reservationPlan = json_decode($sessionReservationData['plan'], true);
			$reservationStep1 = json_decode($sessionReservationData['step1'], true);
			$reservationData = array_merge_recursive($reservationPlan, $reservationStep1);
		}

		// 導入データをチェック
		$reservationParams = array(
			'client_id' => $reservationData['Reservation']['clientId'],
			'commodity_item_id' => $reservationData['Reservation']['commodityItemId'],
			'rent_datetime' => $reservationData['Reservation']['from'],
			'return_datetime' => $reservationData['Reservation']['to'],
			'rent_office_id' => $reservationData['Reservation']['from_office'],
			'return_office_id' => $reservationData['Reservation']['return_office'],
			'last_name' => $reservationData['Reservation']['last_name'],
			'first_name' => $reservationData['Reservation']['first_name'],
			'tel' => $reservationData['Reservation']['tel'],
			'email' => $reservationData['Reservation']['email'],
			'arrival_flight_number' => $reservationData['Reservation']['arrival'],
			'departure_flight_number' => $reservationData['Reservation']['departure'],
			'adults_count' => $reservationData['Reservation']['adults'],
			'children_count' => $reservationData['Reservation']['children'],
			'infants_count' => $reservationData['Reservation']['infants'],
			'cars_count' => $reservationData['Reservation']['cars_count'],
			'amount' => $reservationData['Reservation']['estimationTotalPrice'],
			'is_send_mail' => $reservationData['Reservation']['is_send_mail'],
		);
		$this->Reservation->set($reservationParams);
		$validated = $this->Reservation->validates();
		if (!$validated) {
			$this->Session->write('message.error', $this->reserveErrorMessage);
			$this->log('message.error:' . print_r($reservationParams, true), 'error');
			$this->redirect('/reservations/step1/' . $this->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
		}

		$dateFrom = date('Y-m-d', strtotime($reservationData['Reservation']['from']));

		// プランデータを表示するための共通処理
		$this->__planView($reservationPlan['Reservation']['commodityItemId'], $dateFrom);
		$estimationTotalPrice = $reservationData['Reservation']['estimationTotalPrice'];

		$privilegeOptionAdd = [];
		if (!$redirectFlg) {
			// 商品情報取得
			$confirmationData = $this->Commodity->getConfirmationCommodity($reservationData);

			list($dayNight, $period, $period24) = $this->ReservationUtil->getPeriodArray($reservationPlan['Reservation']['from'], $reservationPlan['Reservation']['to']);

			// オプション料金データ取得
			$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($reservationPlan['Reservation']['commodityId'], $period, $period24);
			$privilegeOption = array();
			foreach ($commodityPrivilegeData as $value) {
				if (!empty($reservationPlan['sheet']) && !empty($reservationPlan['sheet'][$value['Privilege']['id']])) {
					$description = $value['Privilege']['name'];
					if ($reservationPlan['sheet'][$value['Privilege']['id']] > 1) {
						$description .= '×' . $reservationPlan['sheet'][$value['Privilege']['id']];
					}
					$price = number_format($value[0]['Sum'] * $reservationPlan['sheet'][$value['Privilege']['id']]);
					$privilegeOption[] = array($description, $price);
					$tmp_quantity = !empty($reservationPlan['sheet'][$value['Privilege']['id']]) ? $reservationPlan['sheet'][$value['Privilege']['id']] : 1;
					$privilegeOptionAdd[] = [
						'name' => $value['Privilege']['name'],
						'price' => $value[0]['Sum'],
						'quantity' => $tmp_quantity,
					];
				}
				if (!empty($reservationPlan['privilege']) && !empty($reservationPlan['privilege'][$value['Privilege']['id']])) {
					$description = $value['Privilege']['name'];
					if ($reservationPlan['privilege'][$value['Privilege']['id']] > 1) {
						$description .= '×' . $reservationPlan['privilege'][$value['Privilege']['id']];
					}
					$price = number_format($value[0]['Sum'] * $reservationPlan['privilege'][$value['Privilege']['id']]);
					$privilegeOption[] = array($description, $price);
					$tmp_quantity = !empty($reservationPlan['sheet'][$value['Privilege']['id']]) ? $reservationPlan['sheet'][$value['Privilege']['id']] : 1;
					$privilegeOptionAdd[] = [
						'name' => $value['Privilege']['name'],
						'price' => $value[0]['Sum'],
						'quantity' => $tmp_quantity,
					];
				}
			}
			// 乗り捨て料金・深夜手数料取得
			$dropOffLateNightPrice = $this->DropOffAreaRate->dropOffLateNight(
				$reservationPlan['Reservation']['from_office'],
				$reservationPlan['Reservation']['return_office'],
				$reservationPlan['Reservation']['carClassId'],
				date('H:i', strtotime($reservationPlan['Reservation']['from'])),
				date('H:i', strtotime($reservationPlan['Reservation']['to']))
			);

			// 営業所が空港送迎に対応しているかチェック
			$fromOffice = $this->Office->getOfficeRentReturn($reservationData['Reservation']['from_office']);
			$returnOffice = $this->Office->getOfficeRentReturn($reservationData['Reservation']['return_office']);
			if (!empty($fromOffice['Office']['airport_id']) && $fromOffice['Landmark']['landmark_category_id'] == '1') {
				// ランドマークカテゴリが空港の場合のみ
				$methodOfTransport = $fromOffice['OfficeSupplement']['method_of_transport'];
				if ($methodOfTransport == 1 || $methodOfTransport == 2) {
					$this->set('arrivalAirport', true);
				}
			}
			if (!empty($returnOffice['Office']['airport_id']) && $returnOffice['Landmark']['landmark_category_id'] == '1') {
				// ランドマークカテゴリが空港の場合のみ
				$methodOfTransport = $returnOffice['OfficeSupplement']['method_of_transport'];
				if ($methodOfTransport == 1 || $methodOfTransport == 2) {
					$this->set('departureAirport', true);
				}
			}

			$weekday = array('日', '月', '火', '水', '木', '金', '土');
			$fromW = $weekday[date('w', strtotime($reservationData['Reservation']['from']))];
			$toW = $weekday[date('w', strtotime($reservationData['Reservation']['to']))];
			$fromTime = date('H:i', strtotime($reservationData['Reservation']['from']));
			$toTime = date('H:i', strtotime($reservationData['Reservation']['to']));
			$fromString = date('Y年m月d日', strtotime($reservationData['Reservation']['from'])) . '（' . $fromW . '）' . $fromTime;
			$toString = date('Y年m月d日', strtotime($reservationData['Reservation']['to'])) . '（' . $toW . '）' . $toTime;
			if (!empty($reservationData['Reservation']['remark'])) {
				$remark = $reservationData['Reservation']['remark'];
			} else {
				$remark = '';
			}

			$confirmation = array(
				'clientId' => $confirmationData['Client']['id'],
				'clientName' => $confirmationData['Client']['name'],
				'rentOfficeName' => $confirmationData['RentOffice']['office_name'],
				'rentOfficeTel' => $confirmationData['RentOffice']['office_tel'],
				'rentOfficeMeetingInfo' => $confirmationData['RentOffice']['rent_meeting_info'],
				'returnOfficeName' => $confirmationData['ReturnOffice']['office_name'],
				'returnOfficeTel' => $confirmationData['ReturnOffice']['office_tel'],
				'returnOfficeMeetingInfo' => $confirmationData['ReturnOffice']['return_meeting_info'],
				'cancelPolicy' => $this->CancelPolicy->getTextLines($confirmationData['Client']['id'], $reservationData['Reservation']['from']),
				// INCIDENT-3044 取消手続料の徴収を廃止する
				//'advCancelFee' => $this->CancelPolicy->getAdvCancelFee(),
				'clientCancelPolicy' => $confirmationData['Client']['cancel_policy'],
				'acceptCash' => $confirmationData['Client']['accept_cash'],
				'acceptCard' => $confirmationData['Client']['accept_card'],
				'precautions' => $confirmationData['Client']['precautions'],
				'from' => $fromString,
				'to' => $toString,
				'estimationTotalPrice' => $reservationData['Reservation']['estimationTotalPrice'],
				'last_name' => $reservationData['Reservation']['last_name'],
				'first_name' => $reservationData['Reservation']['first_name'],
				'tel' => $reservationData['Reservation']['tel'],
				'email' => $reservationData['Reservation']['email'],
				'arrival' => $reservationData['Reservation']['arrival'],
				'departure' => $reservationData['Reservation']['departure'],
				'remark' => $remark,
				'adults' => $reservationData['Reservation']['adults'],
				'children' => $reservationData['Reservation']['children'],
				'infants' => $reservationData['Reservation']['infants'],
				'privilegeOption' => $privilegeOption,
				'dropOffLateNight' => $dropOffLateNightPrice,
			);

			// 商品アイテムデータ取得
			$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($reservationData['Reservation']['commodityItemId'], $dateFrom);
			// 在庫チェック
			$stockCheckParams = array(
				'from_office' => $reservationData['Reservation']['from_office'],
				'from' => $reservationData['Reservation']['from'],
				'to' => $reservationData['Reservation']['to'],
				'cars_count' => $reservationData['Reservation']['cars_count'],
			);
			$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $stockCheckParams);
			if (empty($remainingStock)) {
				$redirectFlg = true;
				$this->Session->write('message.stock', '申し訳ございません。<br>他のお客様のご予約により在庫がなくなってしまったため、本プランはご予約できません。<br>トップページに戻り、別のプランにて再度ご予約申込みお願いいたします。');
			}

			// 支払方法（カード取得）
			if (!empty($reservationData['Reservation']['clientId'])) {
				$clientCards = $this->ClientCard->getCardByClientId($reservationData['Reservation']['clientId']);
				$confirmation['Cards'] = $clientCards[$reservationData['Reservation']['clientId']];
			}
		}

		if ($redirectFlg) {
			$this->Session->write('message.stock', '申し訳ございません。<br>他のお客様のご予約により在庫がなくなってしまったため、本プランはご予約できません。<br>トップページに戻り、別のプランにて再度ご予約申込みお願いいたします。');
			if ($this->Session->check('referer.plan')) {
				$redirectUrl = $this->Session->read('referer.plan');
			}
			$this->redirect($redirectUrl);
		}

		$this->set(compact('confirmation', 'dropOffLateNightPrice', 'privilegeOption', 'remainingStock', 'estimationTotalPrice'));

		$this->set('title_for_layout', '予約 STEP2');
		$this->set('h1_for_layout', '予約 STEP2');
		$this->set('top_txt', '予約選択の確認ができます。');
		$this->set('description_for_layout', '予約選択の確認ができます。');

		// 決済API
		if ($this->isPaymentAPI) {
			$dropOffLateNight = null;
			if (!empty($confirmation['dropOffLateNight'])) {
				$dropOffLateNight = $confirmation['dropOffLateNight'];
			}
			$this->paymentApi($reservationData, $privilegeOptionAdd, $dropOffLateNight);
		} else {
			$this->set('econ_jsf_url', $this->PaymentEcon->getJsfUrl());
			$this->set('econ_token', $this->Session->read('payment.econ.session_token'));
		}

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setReservations($this->action);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_step2() {
		$this->step2();
	}

	/**
	 * 決済API
	 */
	private function paymentApi($reservationData, $privilegeOptionAdd, $dropOffLateNight) {
		// -------------------------------------------------
		// cm_application_id
		// -------------------------------------------------
		$cmApplicationId = $this->PaymentAPI->createEmptyApplication();

		// -------------------------------------------------
		// ユーザID取得
		// -------------------------------------------------
		$db = GetDBInstance(DB_MAIN_MASTER);
		$user = new User($db);

		$userId = null;
		if (!_isLogin()) {
			$cmTmUserParams = array(
				'family_name' => $reservationData['Reservation']['last_name'],
				'first_name' => $reservationData['Reservation']['first_name'],
				'tel' => $reservationData['Reservation']['tel'],
				'email' => $reservationData['Reservation']['email'],
				'mailmagazine_recept_flg' => 0,
				'password' => '',
				'member_status' => 0,
			);
			$userId = $user->insertUser($cmTmUserParams, $db);
		} else {
			$userId = $_SESSION['user_id'];
		}

		// -------------------------------------------------
		// カートIDの取得
		// -------------------------------------------------
		$cartId = '';
		// if (empty($this->Session->read('payment.api.get.cartid'))) {
		$url = $this->PaymentAPI->getApiUrlCentralCart();
		$param['offerId'] = ['rc'.$cmApplicationId];
		$paramJson = json_encode($param);
		$options = ['header' => ['Content-Type' => 'application/json']];
		$results = $this->PaymentAPI->runApi($url, 'post', $paramJson, $options);

		if ($results->code != 200) {
			if ($this->Session->check('payment.api.get.cartid')) {
				$cartId = $this->Session->read('payment.api.get.cartid');
			} else {
				$this->log($results, LOG_ERROR);
				$this->Session->write('message.session', $results->reasonPhrase);
				$this->redirect('/reservations/step1/' . $this->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
			}
		} else {
			$cartId = json_decode($results->body, true);
		}

		$this->Session->write('payment.api.get.cartid', $cartId);

		// -------------------------------------------------
		// クレジット登録済みかどうか（cm_tm_userのcredit_save)
		// -------------------------------------------------
		$isCreditSave = $user->getCreditSaveByCmApplicationId($cmApplicationId);

		// -------------------------------------------------
		// 支払期限日時
		// -------------------------------------------------		
		//$payment_limit_datetime = date("Y-m-d 23:59:59", strtotime("3 day"));
		$payment_limit_datetime = date('Y-m-d H:i:s', strtotime('15 min'));

		// -------------------------------------------------
		// 車種名取得
		// -------------------------------------------------
		$commodityInfo = $this->viewVars['commodityInfo'];

		$carModels = '';
		foreach ($commodityInfo['CarModel'] as $key => $carModel) {
			if ($carModel === reset($commodityInfo['CarModel'])) {
				$carModels = $carModel['name'];
			} else {
				$carModels .= '・'.$carModel['name'];
			}
		}
		$carName = $commodityInfo['CarType']['name'] .'（'. $carModels;
		$flgModelSelect = ( !empty( $commodityInfo['CommodityItem']['car_model_id']) );
		($flgModelSelect) ? $carName .= '）' : $carName .= '他）';
		//$carName = $commodityInfo['Client']['name'];

		// -------------------------------------------------
		// 明細の詳細
		// -------------------------------------------------
		$sessionReservationData = $this->Session->read('reservation');
		$orderNum = 1;
		$rcDetailOption[] = [
			'title' => '基本料金',
			'titleIndent' => false,
			'subTitle' => '',
			'currency' => 'JPY',
			'price' => $sessionReservationData['basicCharge'] ,
			'quantity' => 1,
			'order' => $orderNum,
		];

		if (!empty($dropOffLateNight['dropPrice'])) {
			$orderNum++;
			$rcDetailOption[] = [
				'title' => '乗り捨て料金',
				'titleIndent' => false,
				'subTitle' => '',
				'currency' => 'JPY',
				'price' => $dropOffLateNight['dropPrice'],
				'quantity' => 1,
				'order' => $orderNum,
			];
		}

		if (!empty($dropOffLateNight['nightFee'])) {
			$orderNum++;
			$rcDetailOption[] = [
				'title' => '深夜手数料',
				'titleIndent' => false,
				'subTitle' => '',
				'currency' => 'JPY',
				'price' => $dropOffLateNight['nightFee'] ,
				'quantity' => 1,
				'order' => $orderNum,
			];
		}

		if (!empty($privilegeOptionAdd)) {
			foreach ($privilegeOptionAdd as $val)
			{
				$orderNum++;
				$rcDetailOption[] = [
					'title' => $val['name'],
					'titleIndent' => false,
					'subTitle' => '',
					'currency' => 'JPY',
					'price' => $val['price'],
					'quantity' => $val['quantity'],
					'order' => $orderNum,
				];
			}
		}

		// -------------------------------------------------
		// 明細情報（paymentDetail）
		// -------------------------------------------------
		$paymentDetail['rc'][0] = [
			'subTotalPrice' => (int)$reservationData['Reservation']['estimationTotalPrice'],
			'currency' => 'JPY',
			'services' => [[
				'name' => $carName,
				'depatureDatetime' =>  $reservationData['Reservation']['from'],
				'arrivalDatetime' => $reservationData['Reservation']['to'],
				'detail' => $rcDetailOption
			]]
		];
		$paymentDetailJson = json_encode($paymentDetail, JSON_UNESCAPED_UNICODE);

		// -------------------------------------------------
		// 申込み情報（registrationData）
		// -------------------------------------------------
		$appendix = [
			'discountData' => []
		];

		// ログインユーザの場合は生年月日と性別、名前（英語）を取得
		$birthday = [];
		$gender = null;
		$firstName = '';
		$lastName = '';
		$loginFlag = 0;
		if (_isLogin()) {
			$loginUser = _getLoginUser();
			$birthday = $loginUser['birth_date_arr'];
			$firstName = $loginUser['first_name_passport'];
			$lastName = $loginUser['family_name_passport'];
			if ($loginUser['gender_id'] == 1) {
				$gender = 'Male';
			} else {
				$gender = 'Female';
			}
			$loginFlag = 1;
		}

		// -------------------------------------------------
		// 名前を取得(カナ)
		// -------------------------------------------------
		$firstNameKana = $reservationData['Reservation']['first_name'];	// カタカナ名
		$lastNameKana = $reservationData['Reservation']['last_name'];	// カタカナ姓

		// -------------------------------------------------
		// 広告コード取得(AppControllerでセット済み)
		// -------------------------------------------------
		$advertising_cd = '';
		if ($this->Cookie->read('advertising_cd')) {
			$advertising_cd = $this->Cookie->read('advertising_cd');
		}

		// -------------------------------------------------
		// 申込情報（registrationData）
		// -------------------------------------------------
		$rcApplicant = [
			'userId' => $userId,
			'firstNameKana' => $firstNameKana,
			'lastNameKana' => $lastNameKana,
			'familyName' => $lastName,
			'firstName' => $firstName,
			'country' => 'JPN',
		];
		if (!empty($birthday)){
			$rcApplicant['birth'] = $birthday;
		} 
		if (!empty($gender)){
			$rcApplicant['gender'] = $gender;
		}

		$rcDetail['jp'] = [
			'totalPrice' => $reservationData['Reservation']['estimationTotalPrice'],
			'totalOtherPrice' => $reservationData['Reservation']['estimationTotalPrice'],
			'userId' => $userId,
			'applicantFamilyName' => $lastName,
			'applicantFirstName' => $firstName,
			'applicantFamilyNameKana' => $lastNameKana,
			'applicantFirstNameKana' => $firstNameKana,
			'tel' => $reservationData['Reservation']['tel'],
			'localContact' => NULL,
			'email' => $reservationData['Reservation']['email'],
			'birth' => is_null($birthday) ? [] : $birthday,
			'gender' => $gender,
			'advertisingCode' => $advertising_cd,
			'paymentLimit' => $payment_limit_datetime,
			'systemFee' => 0,
			'detail' => [
				'rc' => [
					// 予約まだ作ってないから空
					'reservationKey' => '',
					'shopName' => $commodityInfo['Client']['name'],
					'startDate' => $reservationData['Reservation']['from'],
					'startSalesOfficeName' => $commodityInfo['RentOffice'][0]['name'],
					'endDate' => $reservationData['Reservation']['to'],
					'endSalesOfficeName' => $commodityInfo['ReturnOffice'][0]['name'],
					'totalPrice' => $reservationData['Reservation']['estimationTotalPrice'],
					'tax' => 0,
					'option' => $rcDetailOption
				]
			]
		];

		$rc = [
			'cmApplicationId' => $cmApplicationId,
			'currency' => 'JPY',
			'lang' => 'ja',
			'totalPrice' => $reservationData['Reservation']['estimationTotalPrice'],
			'totalOtherPrice' => $reservationData['Reservation']['estimationTotalPrice'],
			'totalOriginalPrice' => $reservationData['Reservation']['estimationTotalPrice'],
			'totalOtherOriginalPrice' => $reservationData['Reservation']['estimationTotalPrice'],
			'localPayment' => false,
			'basicAt' => date('Y-m-d H:i:s', strtotime($reservationData['Reservation']['from'])),
			// クーポンやgoto割引など
			'discountData' => [],
			// ポイント？
			'pointData' => [],
			'isPoint' => false,
			'detail' => $rcDetail,
		];

		$registrationData['appendix'] = $appendix; 
		$registrationData['reservation']['rc'] = [$rc];
		$registrationDataJson = json_encode($registrationData, JSON_UNESCAPED_UNICODE);

		// 決済用ユニークのハッシュキーを発行
		while (1) {
			$hashKey = md5(uniqid(rand(), 1));
			if (!$this->PaymentToken->uniqueCheckHashKey($hashKey)) {
				break;
			}
		}

		$this->Session->write('payment.hash_key', $hashKey);

		// -------------------------------------------------
		// 支払情報を登録パラメータ
		// -------------------------------------------------
		$param = [
			// カートID
			'cartId' => $cartId,
			// 価格
			'price' => (int)$reservationData['Reservation']['estimationTotalPrice'],
			// 他通貨価格
			'otherPrice' => $reservationData['Reservation']['estimationTotalPrice'],
			// 事務手数料フラグ
			'isServiceFee' => 0,
			// 通貨
			'currency' => 'JPY',
			// レート
			'rate' => 1,
			// サービスコード
			'serviceCd' => 'rc',
			// サービス戻り先URL(cartIdがpostされます)
			'returnUrl' => $this->PaymentAPI->getReturnUrl(). $hashKey. ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''),
			// サービス取消戻り先URL
			'cancelReturnUrl' => $this->PaymentAPI->getCancelReturnUrl(). $hashKey,
			// ユーザーID
			'userId' => $userId,
			// Eメール
			'mail' => $reservationData['Reservation']['email'],
			// 電話番号
			'tel' => $reservationData['Reservation']['tel'],
			// カタカナ名
			'firstNameKana' => $firstNameKana,
			// カタカナ姓
			'lastNameKana' => $lastNameKana,
			// 英語名
			'firstName' => $firstName,
			// 英語姓
			'lastName' => $lastName,
			// 言語
			'lang' => 'ja',
			// 予約済み?(予約済み=1)
			'hold' => 0,
			// 支払期限日時
			'dueDate' => $payment_limit_datetime,
			// 広告コード
			'advertisingCode' => $advertising_cd,
			// ログイン済みかどうか
			'isLogin' => $loginFlag,
			// 会員登録にチェックがあるかどうか
			'isMember' => 0,
			// クレジット登録済みかどうか（cm_tm_userのcredit_save)
			'isCreditSave' => $isCreditSave,
			// 与信コールバック先：与信処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
			'authorizeUrl' => $this->PaymentAPI->getAuthorizeUrl(),
			//'authorizeUrl' => 'https://jp-pay.skyticket.jp/api/test/authorize',
			// 取消コールバック先；取消処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
			'cancelUrl' => $this->PaymentAPI->getCancelUrl(),
			//'cancelUrl' => 'https://jp-pay.skyticket.jp/api/test/cancel',
			// 計上コールバック先：計上処理結果をサービスにコールバックするURLを設定する（決済基盤バックエンド → サービスバックエンド）
			'captureUrl' => $this->PaymentAPI->getCaptureUrl(),
			//'captureUrl' => 'https://jp-pay.skyticket.jp/api/test/capture',
			// 明細情報
			'paymentDetail' => $paymentDetailJson,
			// 申込み情報
			'registrationData' => $registrationDataJson,
			// 追加請求か
			'isAdditionalCharge' => 0,
		];

		// -------------------------------------------------
		// URL取得（支払情報を登録し、決済APIのリダイレクト先を取得する）
		// -------------------------------------------------
		$url = $this->PaymentAPI->getApiUrlPaymentsRegister();

		// -------------------------------------------------
		// 支払情報の登録　および　決済APIのリダイレクト先と決済トークンの取得
		// -------------------------------------------------
		$results = $this->PaymentAPI->runApi($url, 'post', $param);
		if ($results->code != 200) {
			$this->log($results, LOG_ERROR);
			$body = json_decode($results->body, true);

			foreach ($body['errors'] as $error) {
				foreach ($error as $val) {
					$msg[] = $val;
				}
			}
			$this->log(print_r($msg, true));
			$msg = implode('<br>', $msg);
			$this->Session->write('message.session', $msg);
			$this->redirect('/reservations/step1/' . $this->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
		}

		// -------------------------------------------------
		// 決済APIのリダイレクトURL、トークン
		// -------------------------------------------------
		$body = json_decode($results->body, true);
		$this->set('paymentRedirectUrl', $body['url']);
		$this->set('paymentToken', $body['token']);
		$this->Session->write('payment.api.get.token', $body['token']);

		// -------------------------------------------------
		// cm_application_idとtokenをテーブルに保存
		// -------------------------------------------------
		$results = $this->PaymentToken->saveInsertUpdate($cmApplicationId, $body['token'], $hashKey);
		if (empty($results)) {
			$this->log('トークンの保存に失敗', LOG_ERROR);
			$this->Session->write('message.session', '内部処理エラー');
			$this->redirect('/reservations/step1/' . $this->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
		}

		// APIの戻りがerrorだった場合にstep1へ戻すため
		$this->Session->write('step1.redirect.url', '/reservations/step1/' . $this->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));

	}

	/**
	 * 決済API（キャンセル）
	 */
	private function paymentApiCancel($orderCode)
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);

		if (!$this->isYoshin($orderCode)) {
			$this->log('alredy keijo', LOG_DEBUG);
			return false;
		}

		$url = $this->PaymentAPI->getApiUrlPayments();
		$param = [
			'orderCode' => $orderCode
		];

		$flag = false;
		$results = $this->PaymentAPI->runApi($url, 'delete', $param);
		$this->log($results, LOG_DEBUG);
		if ($results->code != 200) {
			$this->log($results, LOG_ERROR);
			$this->Session->write('message.session', $results->reasonPhrase);
		} else {
			$flag = true;
		}

		return $flag;
	}

	/**
	 * 与信データか確認
	 */
	private function isYoshin($orderCode)
	{
		$params = [
			'id'              => '',
			'orderCode'       => $orderCode,
			'paymentFlg'      => '',
			'cartId'          => '',
			'userId'          => '',
			'cmApplicationId' => '',
			'serviceCd'       => 'rc',
			'paymentMethodId' => '',
			'createdAtStart'  => '',
			'createdAtEnd'    => '',
			'progress'        => '2', // 与信しかキャンセルできない
			'limit'           => '1',
			'page'            => '1'
		];

		$url = $this->PaymentAPI->getApiUrlPaymentsList();
		$res = $this->PaymentAPI->runApi($url, 'get', $params);
		if ($res->code != 200) {
			$this->log($res, LOG_ERROR);
			$this->Session->write('message.session', $res->reasonPhrase);
			return false;
		}
		$arr = json_decode($res->body, true)['list'];

		// 与信ではない時はfalse
		if (empty($arr['data'])) {
			return false;
		}

		return true;
	}

	/**
	 * 決済API（計上）
	 */
	private function paymentApiKeijo($orderCode)
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$url = $this->PaymentAPI->getApiUrlPayments();
		$param = [
			'orderCode' => $orderCode
		];

		$results = $this->PaymentAPI->runApi($url, 'put', $param);
		$this->log($results, LOG_DEBUG);
		if ($results->code != 200) {
			$this->log($results, LOG_ERROR);
			$this->Session->write('message.session', $results->reasonPhrase);
			return false;
		}

		return $this->afterKeijoProcess();
	}


	/**
	 * サービスコールバック（完了処理の呼び出し）
	 */
	public function callBackReturn()
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$errorMsg = null;
		$query = $this->request->query;
		$this->log(print_r($query, true), LOG_DEBUG);

		if (empty($query['code'])) {
			$errorMsg = 'Parameters was empty.';
		} else {
			if ($query['code'] != 'success') {
				$errorMsg = 'Error parameters';
				if (!empty($query['message'])) {
					$errorMsg = $query['message'];
				}
			}
		}

		$key = $this->request->params['identification_key'];
		$this->log('identification_key:'. $key, LOG_DEBUG);
		$arrInfo = $this->PaymentToken->getPaymentInfoByidentificationKey($key);
		$this->log('order_code:'. $arrInfo['order_code'], LOG_DEBUG);

		if (!empty($errorMsg)) {
			if (!empty($arrInfo['order_code'])) {
				$this->paymentApiCancel($arrInfo['order_code']);
			}
			$this->Session->write('message.session', $errorMsg);
			$this->redirect($this->Session->read('step1.redirect.url'));
		} else {
			if (empty($arrInfo['order_code'])) {
				$this->Session->write('message.session', $this->reserveErrorMessage);
				$this->redirect($this->Session->read('step1.redirect.url'));
			}

			if (!$this->createReservation($arrInfo, $key)) {
				// 予約情報作成失敗
				$this->paymentApiCancel($arrInfo['order_code']);
				$this->Session->write('message.session', $this->reserveErrorMessage);
				if ($this->Session->check('step1.redirect.url')) {
					$this->redirect($this->Session->read('step1.redirect.url'));
				} else {
					$this->redirect('/');
				}
			}

			// 計上にする
			if (!$this->paymentApiKeijo($arrInfo['order_code'])) {
				$this->log('keijo failed', 'error');
			}

			if ($this->Session->check($key)) {
				$this->log($this->Session->id().'[callBackReturn] idenfication_key session delete', LOG_DEBUG);
				$this->Session->delete($key);
			}
			// 二重で処理をしている？のでコメント化する
			// $this->completion();
			$this->redirect('/reservations/completion/'. ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
		}
	}

	/**
	 * 決済システムを途中で離脱した時にリダイレクトする戻りURL
	 */
	public function callBackCancelReturn() {
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$key = $this->request->params['identification_key'];
		$orderCode = $this->PaymentToken->getPaymentInfoByidentificationKey($key)['order_code'];
		$this->log('order_code:'. $orderCode, LOG_DEBUG);

		if (!empty($orderCode)) {
			// orderCode発行後の離脱
			$this->paymentApiCancel($orderCode);
		}

		if ($this->Session->check($key)) {
			$this->Session->write('reservation', $this->Session->read($key.'.reservation'));
			$this->Session->write('payment.api.get.cartid', $this->Session->read($key.'.payment.api.get.cartid'));
			$this->Session->write('payment.api.get.token', $this->Session->read($key.'.payment.api.get.token'));
			$this->Session->write('step1.redirect.url', $this->Session->read($key.'.redirect.url'));
			$this->log($this->Session->id().'[callBackCancelReturn] idenfication_key session delete', LOG_DEBUG);
			$this->Session->delete($key);
		}

		$this->redirect($this->Session->read('step1.redirect.url'));
	}

	/**
	 * 与信コールバック
	 */
	public function callBackAuthorize()
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$result = $this->PaymentAPI->callBackAuthorize();

		$this->callBackEnd($result);
	}

	/**
	 * 計上コールバック
	 */
	public function callBackCapture()
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$result = $this->PaymentAPI->callBackCapture();

		$this->callBackEnd($result);
	}

	public function createReservation($arrInfo, $key)
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$redirectFlg = false;
		$checkFlg = true;
		$saveFlg = true;

		$this->log(print_r($arrInfo, true), LOG_DEBUG);
		$orderCode = $arrInfo['order_code'];
		$cmApplicationId = $arrInfo['cm_application_id'];
		$callBackValues = json_decode($arrInfo['call_back_values'], true);

		if (!$this->Session->check($key. '.reservation.plan') && !$this->Session->check($key. '.reservation.step1')) {
			$this->log($this->Session->id().'[createReservation] session empty error', LOG_DEBUG);
			$this->Session->write('message.session', $this->reserveErrorMessage);
			return false;
		}

		if ($this->Session->check('reservation.success')) {
			$time_start = microtime(true);
			// セッションが削除される（前の予約が完了する）まで待つ
			while (!$this->Session->check('reservation.success')) {
				$time = microtime(true) - $time_start;
				// 2秒経ったら強制終了
				if ($time > 2) {
					$redirectFlg = true;
					$this->log('[createReservation] time out error', LOG_DEBUG);
					break;
				}
				usleep(500000); //0.5秒待つ
			}
		}

		if ($redirectFlg) {
			// xxxxx キャンセル
			$this->paymentApiCancel($orderCode);
			$this->redirect($this->Session->read($key. '.redirect.url'));
		}
		$sessionReservationData = $this->Session->read($key. '.reservation');
		$this->log(print_r($sessionReservationData, true), LOG_DEBUG);

		if (!empty($sessionReservationData['plan'])) {
			$reservationPlan = json_decode($sessionReservationData['plan'], true);
		}
		if (!empty($sessionReservationData['step1'])) {
			$reservationStep1 = json_decode($sessionReservationData['step1'], true);
		}
		if (!empty($reservationPlan) && !empty($reservationStep1)) {
			$reservationData = array_merge_recursive($reservationPlan, $reservationStep1);
		} else {
			$reservationData = array();
		}

		if (!empty($reservationData['sheet'])) {
			$sheetArray = $reservationData['sheet'];
		} else {
			$sheetArray = array();
		}
		if (!empty($reservationData['privilege'])) {
			$privilegeArray = $reservationData['privilege'];
		} else {
			$privilegeArray = array();
		}

		if (empty($reservationData)) {
			$checkFlg = false;
			$this->log($this->Session->id().'[createReservation] reservationData empty error', LOG_DEBUG);
			$this->Session->write('message.session', $this->reserveErrorMessage);
		} else {
			// 受付締切時間チェック
			$acceptanceDeadlineTime = $this->CommodityTerm->acceptanceDeadlineTime($reservationData['Reservation']['commodityId'], $reservationData['Reservation']['from']);
			if (!empty($acceptanceDeadlineTime)) {
				$checkFlg = false;
				$this->log($this->Session->id().'[createReservation] acceptanceDeadlineTime error', LOG_DEBUG);
				$this->Session->write('message.session', '申し訳ございません。<br>'. $acceptanceDeadlineTime .'<br>トップページに戻り、別のプランにて再度ご予約申込みお願いいたします。');
			}
			// 料金計算チェック
			$totalPrice = $this->ReservationUtil->priceCalculation(
				$reservationData['Reservation']['commodityItemId'],
				$reservationData['Reservation']['from'],
				$reservationData['Reservation']['to'],
				$reservationData['Reservation']['dayTimeFlg'],
				$reservationData['Reservation']['from_office'],
				$reservationData['Reservation']['return_office'],
				$reservationData['Reservation']['estimationTotalPrice'],
				$sheetArray,
				$privilegeArray
			);
			if (empty($totalPrice)) {
				$checkFlg = false;
				$this->log($this->Session->id().'[createReservation] totalPrice empty error', LOG_DEBUG);
				$this->Session->write('message.session', $this->reserveErrorMessage);
			}

			// 商品アイテム取得
			$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($reservationData['Reservation']['commodityItemId'], date('Y-m-d', strtotime($reservationData['Reservation']['from'])));
			// 営業所在庫管理地域取得
			$officeStockGroup = $this->OfficeStockGroup->getOfficeStockGroupId($reservationData['Reservation']['from_office']);

			// 在庫チェック
			$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $reservationData['Reservation']);
			if (empty($remainingStock) || empty($remainingStock[$reservationData['Reservation']['from_office']])) {
				$checkFlg = false;
				$this->log($this->Session->id().'[createReservation] remainingStock error', LOG_DEBUG);
				$this->Session->write('message.stock', '申し訳ございません。<br>他のお客様のご予約により在庫がなくなってしまったため、本プランはご予約できません。<br>トップページに戻り、別のプランにて再度ご予約申込みお願いいたします。');
			}

			// 備考の有無での返信状況
			if (!empty($reservationData['Reservation']['remark'])) {
				$reservationData['Reservation']['mail_status'] = 0;
			} else {
				$reservationData['Reservation']['mail_status'] = 3;
			}
		}

		if (!$checkFlg) {
			$this->paymentApiCancel($orderCode);
			$this->redirect($this->Session->read($key. '.redirect.url'));
		}

		$reservationAPI = null;
		$cancelApiRequired = false;
		$controlNumber = '';

		try {
			// トランザクション
			$this->Reservation->begin();
			// skyticketデータベースに対するトランザクション
			$db = GetDBInstance(DB_MAIN_MASTER);   // マスター
			$db->beginTransaction();

			// ユニークなハッシュキーの生成
			while (1) {
				$hashKey = md5(uniqid(rand(), 1));
				if (!$this->Reservation->uniqueCheckHashKey($hashKey)) {
					break;
				}
			}
			// 予約番号の取得
			$clientData = $this->Client->getClientById($reservationData['Reservation']['clientId']);
			$reserveTag = $clientData['Client']['reserve_tag'];
			if (empty($reserveTag)) {
				$this->log($this->Session->id().'[createReservation] reserveTag empty error', LOG_DEBUG);
				return false;
			}
			$maxReservationKey = $this->Reservation->getMaxReservationKey($reserveTag);
			// reservation_key重複チェック
			$resultReservationKey = $this->Reservation->uniqueCheckReservationKey($maxReservationKey);
			if (!empty($resultReservationKey)) {
				$this->log($this->Session->id().'[createReservation] reservation_key duplicate error', LOG_DEBUG);
				return false;
			}

			// 現在時刻
			$currentTime = date('Y-m-d H:i:s');

			$advertising_cd = null;
			// 広告コード取得(AppControllerでセット済み)
			if ($this->Cookie->read('advertising_cd')) {
				$advertising_cd = $this->Cookie->read('advertising_cd');
			}

			$this->Reservation->create();

			if ($saveFlg) {
				// 予約データ
				$reservationParams = array(
					'client_id' => $reservationData['Reservation']['clientId'],
					'user_session_id' => $this->request->clientIP(),
					'user_agent' => env('HTTP_USER_AGENT'),
					'reservation_datetime' => $currentTime,
					'reservation_key' => $maxReservationKey,
					'reservation_hash' => $hashKey,
					'reservation_status_id' => Constant::STATUS_RESERVATION,
					'commodity_item_id' => $reservationData['Reservation']['commodityItemId'],
					'recommend_id' => $this->getRecommendId($reservationData['Reservation']['clientId'], $currentTime, $reservationData['Reservation']['from_office']),
					'rent_datetime' => $reservationData['Reservation']['from'],
					'return_datetime' => $reservationData['Reservation']['to'],
					'rent_office_id' => $reservationData['Reservation']['from_office'],
					'return_office_id' => $reservationData['Reservation']['return_office'],
					'last_name' => $reservationData['Reservation']['last_name'],
					'first_name' => $reservationData['Reservation']['first_name'],
					'tel' => $reservationData['Reservation']['tel'],
					'email' => $reservationData['Reservation']['email'],
					'arrival_flight_number' => $reservationData['Reservation']['arrival'],
					'departure_flight_number' => $reservationData['Reservation']['departure'],
					'adults_count' => $reservationData['Reservation']['adults'],
					'children_count' => $reservationData['Reservation']['children'],
					'infants_count' => $reservationData['Reservation']['infants'],
					'cars_count' => $reservationData['Reservation']['cars_count'],
					'amount' => $reservationData['Reservation']['estimationTotalPrice'],
					'is_send_mail' => $reservationData['Reservation']['is_send_mail'],
					'mail_status' => $reservationData['Reservation']['mail_status'],
					'advertising_cd' => $advertising_cd,
					'api_status_id' => $this->ReservationAPISelect->apiRequired($reservationData['Reservation']['clientId']) ? Constant::API_STATUS_INCLUDED : Constant::API_STATUS_EXCLUDED,
					'rennavi_status' => $this->ReservationAPISelect->isRennaviApiTarget($reservationData['Reservation']['clientId']) ? Constant::RENNAVI_STATUS_RESERVE : Constant::RENNAVI_STATUS_EXCLUDED,
				);

				$credit_fee = 0;
				// $callBackValues = $this->PaymentToken->getCallBackValuesByCmApplicationId($cmApplicationId, $this->Session->read($key.'.payment.api.get.token'));
				if (!empty($callBackValues['fee'])) {
					$credit_fee = (int)$callBackValues['fee'];
				}
				$reservationParams['amount'] += $credit_fee;
				$reservationParams['administrative_fee'] = $credit_fee;
				$reservationParams['payment_status'] = 'AUTH'; // 与信ステータス

				$reservationResult = $this->Reservation->save($reservationParams);
				if (empty($reservationResult)) {
					if (!empty($this->Reservation->validationErrors)) {
						foreach ($this->Reservation->validationErrors as $k => $v) {
							$this->Session->write('message.reservationResult' . $k, $v[0]);
						}
					}
					$saveFlg = false;
					$this->log($this->Session->id().'[createReservation] reservationResult empty error', LOG_DEBUG);
				}
			}

			if ($saveFlg && !empty($reservationData['Reservation']['remark'])) {
				// 予約メールデータ（備考）
				$reservationMailParams = array(
					'reservation_id' => $reservationResult['Reservation']['id'],
					'mail_datetime' => $currentTime,
					'staff_id' => 0,
					'contents' => $reservationData['Reservation']['remark'],
					'read_flg' => 0,
				);
				$reservationMailResult = $this->ReservationMail->save($reservationMailParams);
				if (empty($reservationMailResult)) {
					$saveFlg = false;
					$this->log($this->Session->id().'[createReservation] reservationMailResult empty error', LOG_DEBUG);
				}
			}

			list($dayNight, $period, $period24) = $this->ReservationUtil->getPeriodArray($reservationParams['rent_datetime'], $reservationParams['return_datetime']);

			// オプション料金（チャイルドシート・特典）
			$optionParams = array(
				'commodityId' => $reservationData['Reservation']['commodityId'],
				'period' => $period,
				'period24' => $period24,
				'sheet' => $sheetArray,
				'privilege' => $privilegeArray,
			);
			$reservationPrivilegeData = $this->CommodityPrivilege->getPrivilegeData($optionParams);

			if ($saveFlg) {

				// 予約チャイルドシートデータ
				if (!empty($reservationData['sheet'])) {
					foreach ($reservationData['sheet'] as $privilegeId => $sheetCount) {
						$reservationChildSheetParams[] = array(
							'reservation_id' => $reservationResult['Reservation']['id'],
							'child_sheet_id' => $privilegeId,
							'count' => $sheetCount,
							'price' => $reservationPrivilegeData[$privilegeId]['amount'],
						);
					}
					$reservationChildSheetResult = $this->ReservationChildSheet->saveMany($reservationChildSheetParams);
					if (empty($reservationChildSheetResult)) {
						$saveFlg = false;
						$this->log($this->Session->id().'[createReservation] reservationChildSheetResult empty error', LOG_DEBUG);
					}
				}
			}

			if ($saveFlg) {
				// 予約特典データ
				if (!empty($reservationData['privilege'])) {
					foreach ($reservationData['privilege'] as $privilegeId => $privilegeCount) {
						$reservationPrivilegeParams[] = array(
							'reservation_id' => $reservationResult['Reservation']['id'],
							'privilege_id' => $privilegeId,
							'count' => $privilegeCount,
							'price' => $reservationPrivilegeData[$privilegeId]['amount'],
						);
					}
					$reservationPrivilegeResult = $this->ReservationPrivilege->saveMany($reservationPrivilegeParams);
					if (empty($reservationPrivilegeResult)) {
						$saveFlg = false;
						$this->log($this->Session->id().'[createReservation] reservationPrivilegeResult empty error', LOG_DEBUG);
					}
				}
			}

			if ($saveFlg) {
				// 予約明細データ
				$dateString = $reservationData['Reservation']['from'] . '~' . $reservationData['Reservation']['to'];
				// 乗り捨て料金・深夜手数料
				$dropOffLateNight = $this->DropOffAreaRate->dropOffLateNight(
					$reservationData['Reservation']['from_office'],
					$reservationData['Reservation']['return_office'],
					$reservationData['Reservation']['carClassId'],
					$reservationData['Reservation']['from'],
					$reservationData['Reservation']['to']
				);
				$reservationDetailParams = array();
				if (!empty($dropOffLateNight['dropPrice'])) {
					// 乗り捨て料金
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationResult['Reservation']['id'],
						'detail_type_id' => Constant::DETAIL_TYPE_DROPOFFPRICE,
						'detail_date_string' => $dateString,
						'count' => $reservationData['Reservation']['cars_count'],
						'amount' => $dropOffLateNight['dropPrice'],
					);
				}
				if (!empty($dropOffLateNight['nightFee'])) {
					// 深夜手数料
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationResult['Reservation']['id'],
						'detail_type_id' => Constant::DETAIL_TYPE_NIGHTFEE,
						'detail_date_string' => $dateString,
						'count' => $reservationData['Reservation']['cars_count'],
						'amount' => $dropOffLateNight['nightFee'],
					);
				}
				$reservationSheetAmount = 0;
				$reservationSheetCount = 0;
				$reservationPrivilegeAmount = 0;
				$reservationPrivilegeCount = 0;
				foreach ($reservationPrivilegeData as $key => $reservationPrivilege) {
					if ($reservationPrivilege['option_flg'] == 1) {
						// チャイルドシート
						$reservationSheetAmount += $reservationPrivilege['amount'];
						$reservationSheetCount += $reservationPrivilege['count'];
					} else {
						// オプション（特典）
						$reservationPrivilegeAmount += $reservationPrivilege['amount'];
						$reservationPrivilegeCount += $reservationPrivilege['count'];
					}
				}
				if (!empty($reservationSheetCount)) {
					// チャイルドシート
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationResult['Reservation']['id'],
						'detail_type_id' => Constant::DETAIL_TYPE_CHILDSHEET,
						'detail_date_string' => $dateString,
						'count' => $reservationSheetCount,
						'amount' => $reservationSheetAmount
					);
				}
				if (!empty($reservationPrivilegeCount)) {
					// オプション（特典）
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationResult['Reservation']['id'],
						'detail_type_id' => Constant::DETAIL_TYPE_OPTIONPRICE,
						'detail_date_string' => $dateString,
						'count' => $reservationPrivilegeCount,
						'amount' => $reservationPrivilegeAmount,
					);
				}

				// 免責補償料金取得
				$dateFrom = date('Y-m-d', strtotime($reservationParams['rent_datetime']));
				$dateTo = date('Y-m-d', strtotime($reservationParams['return_datetime']));

				$disclaimerCompensationPrice = $this->DisclaimerCompensation->getFee(
					$reservationData['Reservation']['carClassId'],
					$dateFrom,
					$dateTo,
					$period,
					$period24
				);

				// 免責補償料金
				$reservationDetailParams[] = array(
					'reservation_id' => $reservationResult['Reservation']['id'],
					'detail_type_id' => Constant::DETAIL_TYPE_DISCLAIMER,
					'detail_date_string' => $dateString,
					'count' => $reservationData['Reservation']['cars_count'],
					'amount' => $disclaimerCompensationPrice,
				);

				// 基本料金からの免責補償料金の減算
				$basicPrice = $reservationData['Reservation']['basicPrice'] - $disclaimerCompensationPrice;
				// 基本料金
				$reservationDetailParams[] = array(
					'reservation_id' => $reservationResult['Reservation']['id'],
					'detail_type_id' => Constant::DETAIL_TYPE_BASICPRICE,
					'detail_date_string' => $dateString,
					'count' => $reservationData['Reservation']['cars_count'],
					'amount' => $basicPrice,
				);
				$reservationDetailResult = $this->ReservationDetail->saveMany($reservationDetailParams);
				if (empty($reservationDetailResult)) {
					$saveFlg = false;
					$this->log($this->Session->id().'[createReservation] reservationDetailResult empty error', LOG_DEBUG);
				}
			}

			if ($saveFlg) {
				// 在庫チェック
				$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $reservationData['Reservation']);
				if (empty($remainingStock) || empty($remainingStock[$reservationData['Reservation']['from_office']])) {
					$saveFlg = false;
					$this->log($this->Session->id().'[createReservation] reserve remainingStock empty error', LOG_DEBUG);
				}
			}

			if ($saveFlg) {
				// クラス共有在庫
				$from = strtotime(date('Y-m-d', strtotime($reservationData['Reservation']['from'])));
				$to = strtotime(date('Y-m-d', strtotime($reservationData['Reservation']['to'])));
				$step = 60 * 60 * 24;
				$arrayTime = range($from, $to, $step);
				foreach ($arrayTime as $time) {
					$carClassReservationParams[] = array(
						'client_id' => $reservationData['Reservation']['clientId'],
						'stock_group_id' => $officeStockGroup['OfficeStockGroup']['stock_group_id'],
						'car_class_id' => $commodityItemPriceData['CommodityItem']['car_class_id'],
						'stock_date' => date('Y-m-d', $time),
						'reservation_id' => $reservationResult['Reservation']['id'],
						'reservation_count' => $reservationData['Reservation']['cars_count'],
					);
				}
				$carClassReservationResult = $this->CarClassReservation->saveMany($carClassReservationParams);
				if (empty($carClassReservationResult)) {
					$saveFlg = false;
					$this->log($this->Session->id().'[createReservation] carClassReservationResult empty error', LOG_DEBUG);
				}
			}

			if ($saveFlg) {
				if (!_isLogin()) {
					// common.cm_m_user
					$cmTmUserParams = array(
						'family_name' => $reservationData['Reservation']['last_name'],
						'first_name' => $reservationData['Reservation']['first_name'],
						'tel' => $reservationData['Reservation']['tel'],
						'email' => $reservationData['Reservation']['email'],
						'mailmagazine_recept_flg' => 0,
						'password' => '',
						'member_status' => 0,
					);

					$user = new User($db);
					$user_id = $user->insertUser($cmTmUserParams, $db);
					if (!$user_id) {
						$saveFlg = false;
						$this->log($this->Session->id().'[createReservation] user_id empty error', LOG_DEBUG);
					}
				} else {
					$user_id = $_SESSION['user_id'];
				}
			}

			if ($saveFlg) {
				if (!$this->ReservationUtil->updateApplicationId($db, $reservationResult['Reservation']['id'], $cmApplicationId)) {
					$saveFlg = false;
					$this->log($this->Session->id().'[createReservation] cm_application_id empty error', LOG_DEBUG);
				}
			}

			if ($saveFlg) {
				// 予約連携API
				$componentName = $this->ReservationAPISelect->getApiComponentName($reservationData['Reservation']['clientId']);
				if (!empty($componentName)) {
					// 会社別コンポーネントロード
					$reservationAPI = $this->Components->load($componentName);

					$childSheetData = isset($reservationChildSheetParams) ? $reservationChildSheetParams : array();
					$privilegeData = isset($reservationPrivilegeParams) ? $reservationPrivilegeParams : array();

					// 送信データセット
					$reservationAPI->setFrontReservationData($reservationParams, $reservationDetailParams, $childSheetData, $privilegeData);

					// 送信
					list($success, $result) = $reservationAPI->sendReservationData();
					if ($success) {	// API成功
						if ($result['status']) {
							// コミットされるまでにエラー発生したら、キャンセル連携必要
							$cancelApiRequired = true;

							if (!empty($result['reserveno'])) {
								$controlNumber = $result['reserveno'];
								// 管理番号更新
								$updateResult = $this->Reservation->save(array(
									'id' => $reservationResult['Reservation']['id'],
									'control_number' => $controlNumber,
								));
								if (!is_array($updateResult)) {
									$saveFlg = false;
									$errorString = sprintf("管理番号の登録に失敗しました。(%s)", $result['reserveno']);
								}
							}
						} else {
							// 連携先予約NGの場合、キャンセル連携しないのでメール通知必要なし
							$saveFlg = false;
							$errorString = sprintf("予約連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
						}
					} else {
						// API失敗の場合、キャンセル連携必要
						$cancelApiRequired = true;
						$saveFlg = false;
						$errorString = '予約連携中に何らかのエラーが発生しました。';
					}
					if (!empty($errorString)) {
						$this->log(sprintf("SessionId : %s, ReservationId : %s (%s)\n%s", $this->Session->id(), $reservationResult['Reservation']['id'], $maxReservationKey, $errorString), 'error');
					}
				}
			}

			if ($saveFlg) {
				$db->commit();
				$this->Reservation->commit();
				$this->log(print_r($reservationResult, true), LOG_DEBUG);
				$this->log('[createReservation] commit', LOG_DEBUG);
				// 保存成功セッション
				$reservationId = $reservationResult['Reservation']['id'];
				$this->Session->write('reservation.success', $reservationId);
				// コミット後はキャンセル連携不要
				$cancelApiRequired = false;
			} else {
				$redirectFlg = true;
			}
		} catch (Exception $e) {
			$reservationId = !empty($reservationResult['Reservation']['id']) ? $reservationResult['Reservation']['id'] : '';
			$reservationKey = isset($maxReservationKey) ? $maxReservationKey : '';
			$this->log(sprintf("SessionId : %s, ReservationId : %s (%s)\n%s\n%s", $this->Session->id(), $reservationId, $reservationKey, $e->getMessage(), $e->getTraceAsString()), 'error');
			$redirectFlg = true;
			// xxxxx キャンセル
			$this->paymentApiCancel($orderCode);
		}

		if ($redirectFlg) {
			if (isset($db)) {
				$db->rollback();
			}
			$this->Reservation->rollback();
			$this->paymentApiCancel($orderCode);
			$this->Session->write('message.dberror', $this->reserveErrorMessage);
			$this->log('message.dberror:' . (!empty($reservationResult['Reservation']) ? print_r($reservationResult['Reservation'], true) : ''), 'error');

			if ($cancelApiRequired) {
				// 予約連携APIでエラー or 予約連携API成功後にエラーの場合
				// キャンセル連携
				$cancelSuccess = false;
				$reservationAPI->changeApiStatus(Constant::API_STATUS_CANCEL);
				try {
					list($success, $result) = $reservationAPI->sendReservationData();
					if ($success) {
						if ($result['status']) {
							$cancelSuccess = true;
						} else {
							$errorString = sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
						}
					} else {
						$errorString = 'キャンセル連携中に何らかのエラーが発生しました。';
					}
				} catch (Exception $e) {
					$errorString = sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString());
				}
				if (!$cancelSuccess) {
					// キャンセル連携もエラーの場合、メールで通知
					$this->log(sprintf("SessionId : %s, ReservationId : %s (%s)\n%s", $this->Session->id(), $reservationResult['Reservation']['id'], $maxReservationKey, $errorString), 'error');
					$reservationAPI->sendAlertFromFront($controlNumber, $this->domain);
				}
			}
			if ($this->Session->check('referer.plan')) {
				$redirectUrl = $this->Session->read('referer.plan');
			} else {
				$redirectUrl = '/';
			}
			$this->log($this->Session->id().'[createReservation] redirect 2', LOG_DEBUG);
			$this->redirect($redirectUrl);
		}
		return true;
	}

	/**
	 * 与信取消コールバック
	 */
	public function callBackCancel()
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$result = $this->PaymentAPI->callBackCancel();
		$this->callBackEnd($result);
	}

	/**
	 * 計上後の処理
	 */
	public function afterKeijoProcess()
	{
		$this->log('Start executing ' . __FUNCTION__ . ' function.', LOG_DEBUG);
		$this->log($this->Session->read('reservation.success'), LOG_DEBUG);
		$reservationId = $this->Session->read('reservation.success');
		$this->Reservation->save(['id' => $reservationId, 'payment_status' => 'PAYED']);
		// 予約メール送信処理
		$reservation = $this->Reservation->getReservationData($reservationId);
		$this->ReservationUtil->sendReservedMail($reservation, false);

		Configure::load('YotpoConfig', 'default');
		$yotpoConfig = Configure::read('Yotpo');
		if ($yotpoConfig['is_active']) {
			//BOC send MAP data to yotpo
			$office_url = $reservation['Client']['url'] . '/' . $reservation['RentOffice']['url'] . '/';
			$yotpo_order_info = array(
				'ordered_at' => date('Y-m-d', strtotime($reservation['Reservation']['return_datetime'])),
				'email' => $reservation['Reservation']['email'],
				'lastname' => $reservation['Reservation']['last_name'],
				'firstname' => $reservation['Reservation']['first_name'],
				'order_number' => $reservation['Reservation']['id'],
			);
			$yotpo_items = array(
				array(
					'item_code' => $reservation['RentOffice']['id'],
					'url' => $office_url,
					'name' => $reservation['Client']['name'] . '　|　' . $reservation['RentOffice']['name'],
					'group_name' => $reservation['Client']['id'],
					'clientProductName' => $reservation['Client']['name'],
					'clientProductURL' => $reservation['Client']['url'] . '/'
				)
			);
			if ($reservation['Client']['sp_logo_image']) {
				$yotpo_items[0]['clientImageURL'] = '/rentacar/img/logo/square/' . $reservation['Client']['id'] . '/' . $reservation['Client']['sp_logo_image'];
			}
			$json_order_info = json_encode($yotpo_order_info);
			$json_items = json_encode($yotpo_items);
			exec("php /var/www/skyticket.com/rentacar/app/Console/cake.php YotpoReview postOrderWrapper '$json_order_info' '$json_items' -app /var/www/skyticket.com/rentacar/app/ > /dev/null 2>&1 &");
			//EOC send MAP data to yotpo
		}
		return true;
	}

	/**
	 * コールバックの後処理
	 */
	public function callBackEnd($result) {
		$returnJson = json_encode($result, JSON_UNESCAPED_UNICODE);
		echo $returnJson;
		exit;
	}

	/**
	 * AjaxによるECON事前DBログ登録
	 */
	public function save_payment() {
		$this->autoRender = false;

		if ($this->request->is('ajax')) {
			$this->log($this->Session->id().'[save_payment] '. print_r($this->Session->read('reservation'), true), LOG_DEBUG);
			$msg = '';
			if ($this->isPaymentAPI) {
				$this->__sessionUniqIdCheck($this->request->data['uniq_id']);
				// 決済API導入以降はcommon.cm_th_other_payment_econ_credit_logへのデータ登録は無しとする。
				// session消えていたら重複なのでエラー
				if (!$this->Session->check('payment.api.get.cartid')) {
					$this->log($this->Session->id().'[save_payment]cartId session empty', LOG_DEBUG);
					$msg = 'システムエラーが発生しました。';
				}

				$this->Session->delete('payment.econ.cm_application_id');
				$this->Session->write($this->Session->read('payment.hash_key').'.reservation', $this->Session->read('reservation'));
				$this->Session->write($this->Session->read('payment.hash_key').'.payment.api.get.cartid', $this->Session->read('payment.api.get.cartid'));
				$this->Session->write($this->Session->read('payment.hash_key').'.payment.api.get.token', $this->Session->read('payment.api.get.token'));
				$this->Session->write($this->Session->read('payment.hash_key').'.redirect.url', $this->Session->read('step1.redirect.url'));
			} else {
				if (!$this->PaymentEcon->save()) {
					$this->log($this->Session->id().'[save_payment]PaymentEcon save fail', LOG_DEBUG);
					$msg = 'システムエラーが発生しました。';
				}
			}

			$response_data_arr = [
				'result' => 'OK',
				'msg' => $msg
			];

			return json_encode($response_data_arr);
		}
	}

	public function processing() {
		$session_token = $this->request->data['sessionToken'];
		$cd3secResFlg = $this->request->data['cd3secResFlg'];
		$this->log($this->Session->id().'[processing]sessionToken:'.$session_token, LOG_DEBUG);
		$this->log($this->Session->id().'[processing]cd3secResFlg:'.$cd3secResFlg, LOG_DEBUG);
		if (!$this->PaymentEcon->recvTokenCheck($session_token)) {
			$this->redirect($this->Session->read($this->PaymentEcon->getRedirect()));
		}

		$this->Session->write('payment.econ.recv.sessionToken', $session_token);
		$this->Session->write('payment.econ.recv.cd3secResFlg', $cd3secResFlg);
		$this->set('uniqId', $this->Session->read('reservation.uniqId'));
	}

	public function sp_processing() {
		$this->processing();
	}

	/**
	 * 予約完了ページ
	 */
	public function completion($ua='pc') {

		$remainingStock = 0;
		$redirectFlg = false;
		$checkFlg = true;
		$saveFlg = true;

		if (!$this->Session->check('reservation.success')) {

			$fromStep1 = 0; // xxxxx追加

			if (!$this->isPaymentAPI) {
				if (!isset($this->request->data['Reservation']['isStep1'])) {
					$this->response->statusCode(404);
					$this->render($this->planErrorView);
					return;
				}
				$fromStep1 = $this->request->data['Reservation']['isStep1'];
				$this->set('fromStep1', $fromStep1);

				if ($fromStep1) {
					// リクエスト値が無いのは異常
					if (empty($this->request->data) && empty($this->params['pass'][0])) {
						$this->response->statusCode(404);
						$this->render($this->planErrorView);
						return;
					}
					if (empty($this->request->data['Reservation'])) {
						$this->response->statusCode(404);
						$this->render($this->planErrorView);
						return;
					}
				} else { // step2から来た(決済あり)
					$session_token = $this->Session->read('payment.econ.recv.sessionToken');
					$cd3secResFlg = $this->Session->read('payment.econ.recv.cd3secResFlg');
					$this->log(print_r($this->request->data, true), LOG_DEBUG);
					$this->log(print_r($this->params, true), LOG_DEBUG);
					$this->log($this->Session->id() . '[completion]sessionToken:' . $session_token, LOG_DEBUG);
					$this->log($this->Session->id() . '[completion]cd3secResFlg:' . $cd3secResFlg, LOG_DEBUG);
					if (!$this->PaymentEcon->complete($session_token, $cd3secResFlg)) {
						$this->redirect($this->Session->read($this->PaymentEcon->getRedirect()));
					}
				}
			} else {
				// 決済APIを利用する設定で、現地支払いの場合にこの処理が必要となる
				if (!empty($this->request->data['Reservation']['isStep1'])) {
					$fromStep1 = $this->request->data['Reservation']['isStep1'];
					$this->set('fromStep1', $fromStep1);

					// リクエスト値が無いのは異常
					if (empty($this->request->data) && empty($this->params['pass'][0])) {
						$this->log($this->Session->id().'[completion] 404 error 1', LOG_DEBUG);
						$this->response->statusCode(404);
						$this->render($this->planErrorView);
						return;
					}
					if (empty($this->request->data['Reservation'])) {
						$this->log($this->Session->id().'[completion] 404 error 2', LOG_DEBUG);
						$this->response->statusCode(404);
						$this->render($this->planErrorView);
						return;
					}
				}
			}

			if ($fromStep1) {
				// ユニークセッションIDチェック
				if (!empty($this->params['pass'][0])) {
					$this->request->data['Reservation']['uniqId'] = $this->params['pass'][0];
				}
				$this->__sessionUniqIdCheck($this->request->data['Reservation']['uniqId']);

				// バリデーション
				$redirectFlg = !$this->Validation->validatePersonalInfo($this->request->data['Reservation']);
			}
		
			if (!$this->Session->check('reservation.plan') && !$this->Session->check('reservation.step1')) {
				$this->Session->write('message.session', $this->reserveErrorMessage);
				$this->log('message.session:' . print_r($this->Session->read('reservation'), true), 'error');
				$redirectFlg = true;
			}

			if ($redirectFlg) {
				if (!$fromStep1) {
					if (!$this->isPaymentAPI) {
						$this->PaymentEcon->cancelReservationFail();
					}
				}

				if (!$this->isPaymentAPI) {
					// もともと前画面（step1 or step2）へ戻るようになっていたが、動かしてみたら step2 の場合そのまま step1 にリダイレクトされ、さらにバリデーションエラー出ていたので step1 固定にする
					$this->redirect('/reservations/step1/' . $this->request->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
				} else {
					if ($this->Session->check('step1.redirect.url')) {
						$this->redirect($this->Session->read('step1.redirect.url'));
					} else {
						$this->log($this->Session->id(). '[completion] empty session -> redirect after_completion', LOG_DEBUG);
						if (preg_match('/pc/', $ua)) {
							$this->render('after_completion');
						} else if (preg_match('/sp/', $ua)) {
							$this->render('sp/sp_after_completion');
						}
						return;
					}
				}
			} else {
				if ($fromStep1) {
					$this->request->data['Reservation']['tel'] = $this->ReservationUtil->telNormalization($this->request->data['Reservation']['tel']);
					$this->request->data['Reservation']['email'] = $this->ReservationUtil->mailNormalization($this->request->data['Reservation']['email']);
					if (isset($this->request->data['Reservation']['arrival'])) {
						$this->request->data['Reservation']['arrival'] = $this->Validation->removeControlChars($this->request->data['Reservation']['arrival']);
					} else {
						$this->request->data['Reservation']['arrival'] = '';
					}
					if (isset($this->request->data['Reservation']['departure'])) {
						$this->request->data['Reservation']['departure'] = $this->Validation->removeControlChars($this->request->data['Reservation']['departure']);
					} else {
						$this->request->data['Reservation']['departure'] = '';
					}
					$sessionReservationStep1 = json_encode($this->request->data);
					$this->Session->write('reservation.step1', $sessionReservationStep1);
					$this->Session->write('referer.step1', '/reservations/step1/' . $this->request->data['Reservation']['uniqId'] . '/' . ($this->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : ''));
				}

				$sessionReservationData = $this->Session->read('reservation');

				if (!empty($sessionReservationData['plan'])) {
					$reservationPlan = json_decode($sessionReservationData['plan'], true);
				}
				if (!empty($sessionReservationData['step1'])) {
					$reservationStep1 = json_decode($sessionReservationData['step1'], true);
				}
				if (!empty($reservationPlan) && !empty($reservationStep1)) {
					$reservationData = array_merge_recursive($reservationPlan, $reservationStep1);
				} else {
					$reservationData = array();
				}
			}

			if (!empty($reservationData['sheet'])) {
				$sheetArray = $reservationData['sheet'];
			} else {
				$sheetArray = array();
			}
			if (!empty($reservationData['privilege'])) {
				$privilegeArray = $reservationData['privilege'];
			} else {
				$privilegeArray = array();
			}

			if (empty($reservationData)) {
				$checkFlg = false;
			} else {
				// 受付締切時間チェック
				$acceptanceDeadlineTime = $this->CommodityTerm->acceptanceDeadlineTime($reservationData['Reservation']['commodityId'], $reservationData['Reservation']['from']);
				if (!empty($acceptanceDeadlineTime)) {
					$checkFlg = false;
				}
				// 料金計算チェック
				$totalPrice = $this->ReservationUtil->priceCalculation(
					$reservationData['Reservation']['commodityItemId'],
					$reservationData['Reservation']['from'],
					$reservationData['Reservation']['to'],
					$reservationData['Reservation']['dayTimeFlg'],
					$reservationData['Reservation']['from_office'],
					$reservationData['Reservation']['return_office'],
					$reservationData['Reservation']['estimationTotalPrice'],
					$sheetArray,
					$privilegeArray
				);
				if (empty($totalPrice)) {
					$checkFlg = false;
				}

				// 商品アイテム取得
				$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($reservationData['Reservation']['commodityItemId'], date('Y-m-d', strtotime($reservationData['Reservation']['from'])));
				// 営業所在庫管理地域取得
				$officeStockGroup = $this->OfficeStockGroup->getOfficeStockGroupId($reservationData['Reservation']['from_office']);

				// 在庫チェック
				$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $reservationData['Reservation']);
				if (empty($remainingStock) || empty($remainingStock[$reservationData['Reservation']['from_office']])) {
					$checkFlg = false;
					$this->Session->write('message.stock', '申し訳ございません。<br>他のお客様のご予約により在庫がなくなってしまったため、本プランはご予約できません。<br>トップページに戻り、別のプランにて再度ご予約申込みお願いいたします。');
				}

				// 備考の有無での返信状況
				if (!empty($reservationData['Reservation']['remark'])) {
					$reservationData['Reservation']['mail_status'] = 0;
				} else {
					$reservationData['Reservation']['mail_status'] = 3;
				}
			}

			if ($checkFlg) {
				$reservationAPI = null;
				$cancelApiRequired = false;
				$controlNumber = '';

				try {
					// トランザクション
					$this->Reservation->begin();
					// skyticketデータベースに対するトランザクション
					$db = GetDBInstance(DB_MAIN_MASTER);   // マスター
					$db->beginTransaction();

					// ユニークなハッシュキーの生成
					while (1) {
						$hashKey = md5(uniqid(rand(), 1));
						if (!$this->Reservation->uniqueCheckHashKey($hashKey)) {
							break;
						}
					}
					// 予約番号の取得
					$clientData = $this->Client->getClientById($reservationData['Reservation']['clientId']);
					$reserveTag = $clientData['Client']['reserve_tag'];
					if (empty($reserveTag)) {
						return false;
					}
					$maxReservationKey = $this->Reservation->getMaxReservationKey($reserveTag);
					// reservation_key重複チェック
					$resultReservationKey = $this->Reservation->uniqueCheckReservationKey($maxReservationKey);
					if (!empty($resultReservationKey)) {
						return false;
					}

					// 現在時刻
					$currentTime = date('Y-m-d H:i:s');

					$advertising_cd = null;
					//広告コード取得(AppControllerでセット済み)
					if ($this->Cookie->read('advertising_cd')) {
						$advertising_cd = $this->Cookie->read('advertising_cd');
					}

					$travelko_code = null;
					//トラッキングコード取得(AppControllerでセット済み)
					if ($this->Cookie->read('travelko_code')) {
						$travelko_code = $this->Cookie->read('travelko_code');
					}

					$this->Reservation->create();

					if ($saveFlg) {
						// 予約データ
						$reservationParams = array(
							'client_id' => $reservationData['Reservation']['clientId'],
							'user_session_id' => $this->request->clientIP(),
							'user_agent' => env('HTTP_USER_AGENT'),
							'reservation_datetime' => $currentTime,
							'reservation_key' => $maxReservationKey,
							'reservation_hash' => $hashKey,
							'reservation_status_id' => Constant::STATUS_RESERVATION,
							'commodity_item_id' => $reservationData['Reservation']['commodityItemId'],
							'recommend_id' => $this->getRecommendId($reservationData['Reservation']['clientId'], $currentTime, $reservationData['Reservation']['from_office']),
							'rent_datetime' => $reservationData['Reservation']['from'],
							'return_datetime' => $reservationData['Reservation']['to'],
							'rent_office_id' => $reservationData['Reservation']['from_office'],
							'return_office_id' => $reservationData['Reservation']['return_office'],
							'last_name' => $reservationData['Reservation']['last_name'],
							'first_name' => $reservationData['Reservation']['first_name'],
							'tel' => $reservationData['Reservation']['tel'],
							'email' => $reservationData['Reservation']['email'],
							'arrival_flight_number' => $reservationData['Reservation']['arrival'],
							'departure_flight_number' => $reservationData['Reservation']['departure'],
							'adults_count' => $reservationData['Reservation']['adults'],
							'children_count' => $reservationData['Reservation']['children'],
							'infants_count' => $reservationData['Reservation']['infants'],
							'cars_count' => $reservationData['Reservation']['cars_count'],
							'amount' => $reservationData['Reservation']['estimationTotalPrice'],
							'is_send_mail' => $reservationData['Reservation']['is_send_mail'],
							'mail_status' => $reservationData['Reservation']['mail_status'],
							'advertising_cd' => $advertising_cd,
							'api_status_id' => $this->ReservationAPISelect->apiRequired($reservationData['Reservation']['clientId']) ? Constant::API_STATUS_INCLUDED : Constant::API_STATUS_EXCLUDED,
							'rennavi_status' => $this->ReservationAPISelect->isRennaviApiTarget($reservationData['Reservation']['clientId']) ? Constant::RENNAVI_STATUS_RESERVE : Constant::RENNAVI_STATUS_EXCLUDED,
						);

						if (!$fromStep1) {
							if (!$this->isPaymentAPI) {
								$credit_fee = $this->PaymentEcon->getCreditRate(); // 決済手数料
								$reservationParams['amount'] += $credit_fee;
								$reservationParams['administrative_fee'] = $credit_fee;
								$reservationParams['payment_status'] = 'PAYED';
							} else {
								$credit_fee = 0;
								$callBackValues = $this->PaymentToken->getCallBackValuesByCmApplicationId($this->Session->read('payment.cm_application_id'), $this->Session->read('payment.api.get.token'));
								if (!empty($callBackValues['fee'])) {
									$credit_fee = (int)$callBackValues['fee'];
								}
								$reservationParams['amount'] += $credit_fee;
								$reservationParams['administrative_fee'] = $credit_fee;
								$reservationParams['payment_status'] = 'PAYED';
							}
						}

						$reservationResult = $this->Reservation->save($reservationParams);
						if (empty($reservationResult)) {
							if (!empty($this->Reservation->validationErrors)) {
								foreach ($this->Reservation->validationErrors as $k => $v) {
									$this->Session->write('message.reservationResult' . $k, $v[0]);
								}
							}
							$saveFlg = false;
						}
					}

					if ($saveFlg && !empty($reservationData['Reservation']['remark'])) {
						// 予約メールデータ（備考）
						$reservationMailParams = array(
							'reservation_id' => $reservationResult['Reservation']['id'],
							'mail_datetime' => $currentTime,
							'staff_id' => 0,
							'contents' => $reservationData['Reservation']['remark'],
							'read_flg' => 0,
						);
						$reservationMailResult = $this->ReservationMail->save($reservationMailParams);
						if (empty($reservationMailResult)) {
							$saveFlg = false;
						}
					}

					list($dayNight, $period, $period24) = $this->ReservationUtil->getPeriodArray($reservationParams['rent_datetime'], $reservationParams['return_datetime']);

					// オプション料金（チャイルドシート・特典）
					$optionParams = array(
						'commodityId' => $reservationData['Reservation']['commodityId'],
						'period' => $period,
						'period24' => $period24,
						'sheet' => $sheetArray,
						'privilege' => $privilegeArray,
					);
					$reservationPrivilegeData = $this->CommodityPrivilege->getPrivilegeData($optionParams);

					if ($saveFlg) {

						// 予約チャイルドシートデータ
						if (!empty($reservationData['sheet'])) {
							foreach ($reservationData['sheet'] as $privilegeId => $sheetCount) {
								$reservationChildSheetParams[] = array(
									'reservation_id' => $reservationResult['Reservation']['id'],
									'child_sheet_id' => $privilegeId,
									'count' => $sheetCount,
									'price' => $reservationPrivilegeData[$privilegeId]['amount'],
								);
							}
							$reservationChildSheetResult = $this->ReservationChildSheet->saveMany($reservationChildSheetParams);
							if (empty($reservationChildSheetResult)) {
								$saveFlg = false;
							}
						}
					}

					if ($saveFlg) {
						// 予約特典データ
						if (!empty($reservationData['privilege'])) {
							foreach ($reservationData['privilege'] as $privilegeId => $privilegeCount) {
								$reservationPrivilegeParams[] = array(
									'reservation_id' => $reservationResult['Reservation']['id'],
									'privilege_id' => $privilegeId,
									'count' => $privilegeCount,
									'price' => $reservationPrivilegeData[$privilegeId]['amount'],
								);
							}
							$reservationPrivilegeResult = $this->ReservationPrivilege->saveMany($reservationPrivilegeParams);
							if (empty($reservationPrivilegeResult)) {
								$saveFlg = false;
							}
						}
					}

					if ($saveFlg) {
						// 予約明細データ
						$dateString = $reservationData['Reservation']['from'] . '~' . $reservationData['Reservation']['to'];
						// 乗り捨て料金・深夜手数料
						$dropOffLateNight = $this->DropOffAreaRate->dropOffLateNight(
							$reservationData['Reservation']['from_office'],
							$reservationData['Reservation']['return_office'],
							$reservationData['Reservation']['carClassId'],
							$reservationData['Reservation']['from'],
							$reservationData['Reservation']['to']
						);
						$reservationDetailParams = array();
						if (!empty($dropOffLateNight['dropPrice'])) {
							// 乗り捨て料金
							$reservationDetailParams[] = array(
								'reservation_id' => $reservationResult['Reservation']['id'],
								'detail_type_id' => Constant::DETAIL_TYPE_DROPOFFPRICE,
								'detail_date_string' => $dateString,
								'count' => $reservationData['Reservation']['cars_count'],
								'amount' => $dropOffLateNight['dropPrice'],
							);
						}
						if (!empty($dropOffLateNight['nightFee'])) {
							// 深夜手数料
							$reservationDetailParams[] = array(
								'reservation_id' => $reservationResult['Reservation']['id'],
								'detail_type_id' => Constant::DETAIL_TYPE_NIGHTFEE,
								'detail_date_string' => $dateString,
								'count' => $reservationData['Reservation']['cars_count'],
								'amount' => $dropOffLateNight['nightFee'],
							);
						}
						$reservationSheetAmount = 0;
						$reservationSheetCount = 0;
						$reservationPrivilegeAmount = 0;
						$reservationPrivilegeCount = 0;
						foreach ($reservationPrivilegeData as $key => $reservationPrivilege) {
							if ($reservationPrivilege['option_flg'] == 1) {
								// チャイルドシート
								$reservationSheetAmount += $reservationPrivilege['amount'];
								$reservationSheetCount += $reservationPrivilege['count'];
							} else {
								// オプション（特典）
								$reservationPrivilegeAmount += $reservationPrivilege['amount'];
								$reservationPrivilegeCount += $reservationPrivilege['count'];
							}
						}
						if (!empty($reservationSheetCount)) {
							// チャイルドシート
							$reservationDetailParams[] = array(
								'reservation_id' => $reservationResult['Reservation']['id'],
								'detail_type_id' => Constant::DETAIL_TYPE_CHILDSHEET,
								'detail_date_string' => $dateString,
								'count' => $reservationSheetCount,
								'amount' => $reservationSheetAmount
							);
						}
						if (!empty($reservationPrivilegeCount)) {
							// オプション（特典）
							$reservationDetailParams[] = array(
								'reservation_id' => $reservationResult['Reservation']['id'],
								'detail_type_id' => Constant::DETAIL_TYPE_OPTIONPRICE,
								'detail_date_string' => $dateString,
								'count' => $reservationPrivilegeCount,
								'amount' => $reservationPrivilegeAmount,
							);
						}

						// 免責補償料金取得
						$dateFrom = date('Y-m-d', strtotime($reservationParams['rent_datetime']));
						$dateTo = date('Y-m-d', strtotime($reservationParams['return_datetime']));

						$disclaimerCompensationPrice = $this->DisclaimerCompensation->getFee(
							$reservationData['Reservation']['carClassId'],
							$dateFrom,
							$dateTo,
							$period,
							$period24
						);

						// 免責補償料金
						$reservationDetailParams[] = array(
							'reservation_id' => $reservationResult['Reservation']['id'],
							'detail_type_id' => Constant::DETAIL_TYPE_DISCLAIMER,
							'detail_date_string' => $dateString,
							'count' => $reservationData['Reservation']['cars_count'],
							'amount' => $disclaimerCompensationPrice,
						);

						// 基本料金からの免責補償料金の減算
						$basicPrice = $reservationData['Reservation']['basicPrice'] - $disclaimerCompensationPrice;
						// 基本料金
						$reservationDetailParams[] = array(
							'reservation_id' => $reservationResult['Reservation']['id'],
							'detail_type_id' => Constant::DETAIL_TYPE_BASICPRICE,
							'detail_date_string' => $dateString,
							'count' => $reservationData['Reservation']['cars_count'],
							'amount' => $basicPrice,
						);
						$reservationDetailResult = $this->ReservationDetail->saveMany($reservationDetailParams);
						if (empty($reservationDetailResult)) {
							$saveFlg = false;
						}
					}

					if ($saveFlg) {
						// 在庫チェック
						$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $reservationData['Reservation']);
						if (empty($remainingStock) || empty($remainingStock[$reservationData['Reservation']['from_office']])) {
							$saveFlg = false;
						}
					}

					if ($saveFlg) {
						// クラス共有在庫
						$from = strtotime(date('Y-m-d', strtotime($reservationData['Reservation']['from'])));
						$to = strtotime(date('Y-m-d', strtotime($reservationData['Reservation']['to'])));
						$step = 60 * 60 * 24;
						$arrayTime = range($from, $to, $step);
						foreach ($arrayTime as $time) {
							$carClassReservationParams[] = array(
								'client_id' => $reservationData['Reservation']['clientId'],
								'stock_group_id' => $officeStockGroup['OfficeStockGroup']['stock_group_id'],
								'car_class_id' => $commodityItemPriceData['CommodityItem']['car_class_id'],
								'stock_date' => date('Y-m-d', $time),
								'reservation_id' => $reservationResult['Reservation']['id'],
								'reservation_count' => $reservationData['Reservation']['cars_count'],
							);
						}
						$carClassReservationResult = $this->CarClassReservation->saveMany($carClassReservationParams);
						if (empty($carClassReservationResult)) {
							$saveFlg = false;
						}
					}

					if ($saveFlg) {
						if (!_isLogin()) {
							// common.cm_m_user
							$cmTmUserParams = array(
								'family_name' => $reservationData['Reservation']['last_name'],
								'first_name' => $reservationData['Reservation']['first_name'],
								'tel' => $reservationData['Reservation']['tel'],
								'email' => $reservationData['Reservation']['email'],
								'mailmagazine_recept_flg' => 0,
								'password' => '',
								'member_status' => 0,
							);

							$user = new User($db);
							$user_id = $user->insertUser($cmTmUserParams, $db);
							if (!$user_id) {
								$saveFlg = false;
							}
						} else {
							$user_id = $_SESSION['user_id'];
						}
					}

					if ($saveFlg && $fromStep1) {
						// 新規登録 cm_th_application, cm_th_application_detail
						$application_data = array(
							"user_id" => $user_id,
						);
						$data = array(
							"application_id" => $reservationResult['Reservation']['id'],
							"service_cd" => "rc",
						);
						$data_array = array($data);
						$application = new Application($db);
						$cm_application_id = $application->insertApplication($application_data, $data_array, $db);
						if (!$cm_application_id) {
							$saveFlg = false;
						}
					}

					if ($saveFlg && !$fromStep1) {
						$cm_application_id = $this->Session->read('payment.cm_application_id');
						if (!$this->ReservationUtil->updateApplicationId($db, $reservationResult['Reservation']['id'], $cm_application_id)) {
							$saveFlg = false;
						}
					}

					if ($saveFlg) {
						// 予約連携API
						$componentName = $this->ReservationAPISelect->getApiComponentName($reservationData['Reservation']['clientId']);
						if (!empty($componentName)) {
							// 会社別コンポーネントロード
							$reservationAPI = $this->Components->load($componentName);

							$childSheetData = isset($reservationChildSheetParams) ? $reservationChildSheetParams : array();
							$privilegeData = isset($reservationPrivilegeParams) ? $reservationPrivilegeParams : array();

							// 送信データセット
							$reservationAPI->setFrontReservationData($reservationParams, $reservationDetailParams, $childSheetData, $privilegeData);

							// 送信
							list($success, $result) = $reservationAPI->sendReservationData();
							if ($success) {	// API成功
								if ($result['status']) {
									// コミットされるまでにエラー発生したら、キャンセル連携必要
									$cancelApiRequired = true;

									if (!empty($result['reserveno'])) {
										$controlNumber = $result['reserveno'];
										// 管理番号更新
										$updateResult = $this->Reservation->save(array(
											'id' => $reservationResult['Reservation']['id'],
											'control_number' => $controlNumber,
										));
										if (!is_array($updateResult)) {
											$saveFlg = false;
											$errorString = sprintf("管理番号の登録に失敗しました。(%s)", $result['reserveno']);
										}
									}
								} else {
									// 連携先予約NGの場合、キャンセル連携しないのでメール通知必要なし
									$saveFlg = false;
									$errorString = sprintf("予約連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
								}
							} else {
								// API失敗の場合、キャンセル連携必要
								$cancelApiRequired = true;
								$saveFlg = false;
								$errorString = '予約連携中に何らかのエラーが発生しました。';
							}
							if (!empty($errorString)) {
								$this->log(sprintf("SessionId : %s, ReservationId : %s (%s)\n%s", $this->Session->id(), $reservationResult['Reservation']['id'], $maxReservationKey, $errorString), 'error');
							}
						}
					}

					if ($saveFlg) {
						$db->commit();
						$this->Reservation->commit();
						// 保存成功セッション
						$reservationId = $reservationResult['Reservation']['id'];
						$this->Session->write('reservation.success', $reservationId);
						// コミット後はキャンセル連携不要
						$cancelApiRequired = false;
					} else {
						$redirectFlg = true;
					}
				} catch (Exception $e) {
					$reservationId = !empty($reservationResult['Reservation']['id']) ? $reservationResult['Reservation']['id'] : '';
					$reservationKey = isset($maxReservationKey) ? $maxReservationKey : '';
					$this->log(sprintf("SessionId : %s, ReservationId : %s (%s)\n%s\n%s", $this->Session->id(), $reservationId, $reservationKey, $e->getMessage(), $e->getTraceAsString()), 'error');
					$redirectFlg = true;
					if (!$fromStep1) {
						if (!$this->isPaymentAPI) {
							$this->PaymentEcon->cancelReservationFail();
						}
					}
				}

				if ($redirectFlg) {
					if (isset($db)) {
						$db->rollback();
					}
					$this->Reservation->rollback();
					$this->Session->write('message.dberror', $this->reserveErrorMessage);
					$this->log('message.dberror:' . (!empty($reservationResult['Reservation']) ? print_r($reservationResult['Reservation'], true) : ''), 'error');

					if ($cancelApiRequired) {
						// 予約連携APIでエラー or 予約連携API成功後にエラーの場合
						// キャンセル連携
						$cancelSuccess = false;
						$reservationAPI->changeApiStatus(Constant::API_STATUS_CANCEL);
						try {
							list($success, $result) = $reservationAPI->sendReservationData();
							if ($success) {
								if ($result['status']) {
									$cancelSuccess = true;
								} else {
									$errorString = sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
								}
							} else {
								$errorString = 'キャンセル連携中に何らかのエラーが発生しました。';
							}
						} catch (Exception $e) {
							$errorString = sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString());
						}
						if (!$cancelSuccess) {
							// キャンセル連携もエラーの場合、メールで通知
							$this->log(sprintf("SessionId : %s, ReservationId : %s (%s)\n%s", $this->Session->id(), $reservationResult['Reservation']['id'], $maxReservationKey, $errorString), 'error');
							$reservationAPI->sendAlertFromFront($controlNumber, $this->domain);
						}
					}
				} else {
					if (!$fromStep1) {
						// 与信 -> 計上にする
						if (!$this->isPaymentAPI) {
							if (!$this->PaymentEcon->cardCapture($reservationResult['Reservation']['id'], $reservationParams['amount'])) {
								$this->PaymentEcon->notice(sprintf("予約番号:%s", $reservationResult['Reservation']['reservation_key']), "ECON与信->計上失敗");
							}

							// 予約に成功したら決済結果をDBに登録する
							$this->PaymentEcon->saveResultData($reservationResult['Reservation']['reservation_key']);
						}
					}

					// 予約メール送信処理
					$reservation = $this->Reservation->getReservationData($reservationId);
					$this->ReservationUtil->sendReservedMail($reservation, $fromStep1);

					Configure::load('YotpoConfig', 'default');
					$yotpoConfig = Configure::read('Yotpo');
					if ($yotpoConfig['is_active']) {
						//BOC send MAP data to yotpo
						$office_url = $reservation['Client']['url'] . '/' . $reservation['RentOffice']['url'] . '/';
						$yotpo_order_info = array(
							'ordered_at' => date('Y-m-d', strtotime($reservation['Reservation']['return_datetime'])),
							'email' => $reservation['Reservation']['email'],
							'lastname' => $reservation['Reservation']['last_name'],
							'firstname' => $reservation['Reservation']['first_name'],
							'order_number' => $reservation['Reservation']['id'],
						);
						$yotpo_items = array(
							array(
								'item_code' => $reservation['RentOffice']['id'],
								'url' => $office_url,
								'name' => $reservation['Client']['name'] . '　|　' . $reservation['RentOffice']['name'],
								'group_name' => $reservation['Client']['id'],
								'clientProductName' => $reservation['Client']['name'],
								'clientProductURL' => $reservation['Client']['url'] . '/'
							)
						);
						if ($reservation['Client']['sp_logo_image']) {
							$yotpo_items[0]['clientImageURL'] = '/rentacar/img/logo/square/' . $reservation['Client']['id'] . '/' . $reservation['Client']['sp_logo_image'];
						}
						$json_order_info = json_encode($yotpo_order_info);
						$json_items = json_encode($yotpo_items);
						exec("php /var/www/skyticket.com/rentacar/app/Console/cake.php YotpoReview postOrderWrapper '$json_order_info' '$json_items' -app /var/www/skyticket.com/rentacar/app/ > /dev/null 2>&1 &");
						//EOC send MAP data to yotpo
					}

					// 非会員の新規予約の場合
					if (!_isLogin()) {
						// ワンタイムログイン
						$one_time_login_arr = array(
							"cm_application_id" => $cm_application_id,
							"last_name" => $reservationData['Reservation']['last_name'],
							"first_name" => $reservationData['Reservation']['first_name'],
							"tel" => $reservationData['Reservation']['tel'],
						);
						_doOneTimeLogin($one_time_login_arr);
					}
				}
			} else {
				$redirectFlg = true;
			}
		}

		if ($this->isPaymentAPI) {
			// 非会員の新規予約の場合
			if (!_isLogin()) {
				$reservationId = $this->Session->read('reservation.success');
				$reservation = $this->Reservation->getReservationData($reservationId);
				// ワンタイムログイン
				$one_time_login_arr = array(
					"cm_application_id" => $this->Session->read('payment.cm_application_id'),
					"last_name" => $reservationData['Reservation']['last_name'],
					"first_name" => $reservationData['Reservation']['first_name'],
					"tel" => $reservationData['Reservation']['tel'],
				);
				_doOneTimeLogin($one_time_login_arr);
			}
		}

		if (!$redirectFlg) {
			// セッション削除
			if ($this->Session->check('message')) {
				$this->log($this->Session->id().'[completion] message session delete', LOG_DEBUG);
				$this->Session->delete('message');
			}
			if ($this->Session->check('referer')) {
				$this->log($this->Session->id().'[completion] referer session delete', LOG_DEBUG);
				$this->Session->delete('referer');
			}
			if ($this->Session->check('reservation.plan')) {
				$this->log($this->Session->id().'[completion] reservation.plan session delete', LOG_DEBUG);
				$this->Session->delete('reservation.plan');
			}
			if ($this->Session->check('reservation.step1')) {
				$this->log($this->Session->id().'[completion] reservation.step1 session delete', LOG_DEBUG);
				$this->Session->delete('reservation.step1');
			}

			if ($this->Session->check('reservation.success')) {
				$reservationId = $this->Session->read('reservation.success');
				$reservation = $this->Reservation->getReservationData($reservationId);
				$this->log($this->Session->id().'[completion] reservation.success session delete', LOG_DEBUG);
				$this->Session->delete('reservation.success');
			}
			if ($this->Session->check('payment')) {
				$this->log($this->Session->id().'[completion] payment session delete', LOG_DEBUG);
				$this->Session->delete('payment');
			}
			if ($this->Session->check('recommend_flg')) {
				$this->log($this->Session->id().'[completion] recommend_flg session delete', LOG_DEBUG);
				$this->Session->delete('recommend_flg');
			}

			if (!empty($reservation)) {
				// Smart-C用注文識別情報を取得
				$reservation['Reservation']['smartc_reservation_key'] = $this->getSmartCReservationKey($reservation['Reservation']['reservation_key']);

				// JANet用注文識別情報を取得
				$reservation['Reservation']['janet_reservation_key'] = $this->getJanetReservationKey($reservation['Reservation']['reservation_key']);

				// 成果タグ用税抜価格
				$reservationTime = strtotime($reservation['Reservation']['reservation_datetime']);
				$reservationYear = date('Y', $reservationTime);
				$reservationMonth = date('n', $reservationTime);
				$reservation['Reservation']['amount_without_tax'] = floor($reservation['Reservation']['amount'] / $this->TaxRate->getConsumptionTaxRate($reservationYear, $reservationMonth));

				// トラベルコ成果タグ
				$reservation['Reservation']['travelko_code'] = $travelko_code;

				// 都道府県LINKCD設定
				$this->areas = $this->Areas->find('all', array(
					'fields' => array(
						'Areas.*',
						'Prefecture.*',
					),
					'conditions' => array(
						'Areas.delete_flg' => 0,
					),
					'joins' => array(
						array(
							'type' => 'INNER',
							'table' => 'prefectures',
							'alias' => 'Prefecture',
							'conditions' => array(
								'Areas.prefecture_id = Prefecture.id',
							)
						)
					),
					'recursive' => -1
				));
				$prefLinkCds = Hash::combine($this->areas, '{n}.Areas.id', '{n}.Prefecture.link_cd');
				$reservation['RentOffice']['pref_link_cd'] = $prefLinkCds[$reservation['RentOffice']['area_id']];
				$reservation['ReturnOffice']['pref_link_cd'] = $prefLinkCds[$reservation['ReturnOffice']['area_id']];

				// -------------------------------------------------
				// 車種名取得（拡張Eコマース用）
				// -------------------------------------------------
				// 現地で決済の場合
				if (!empty($commodityItemPriceData)) {
					$commodityItemPriceDataGA4 = $commodityItemPriceData;
				// クレジットカードで事前決済の場合
				} else if (isset($reservation['Reservation']['rent_datetime']) && isset($reservation['Reservation']['commodity_item_id'])) {
					$commodityItemPriceDataGA4 = $this->CommodityItem->getCommodityItemPriceData($reservation['Reservation']['commodity_item_id'], date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime'])));
				} else {
					$commodityItemPriceDataGA4 = array();
				}
				if (!empty($commodityItemPriceDataGA4)) {
					$carModels = '';
					foreach ($commodityItemPriceDataGA4['CarModel'] as $key => $carModel) {
						if ($carModel === reset($commodityItemPriceDataGA4['CarModel'])) {
							$carModels = $carModel['name'];
						} else {
							$carModels .= '・'.$carModel['name'];
						}
					}
					$carName = $reservation['CarType']['name'] .'（'. $carModels;
					$flgModelSelect = ( !empty( $reservation['CommodityItem']['car_model_id']) );
					($flgModelSelect) ? $carName .= '）' : $carName .= '他）';
					$reservation['CarName'] = $carName;
				}

				// 予約手数料の設定
				$profit = 0;
				$settlementCompany = $this->SettlementCompany->find('first', array(
					'conditions' => array('id' => $reservation['RentOffice']['settlement_company_id'])
				));
				if (!empty($settlementCompany)) {
					$rate = (float) $settlementCompany['SettlementCompany']['commission_rate'];
					if (empty($commodityItemPriceData)) {// 事前決済
						$rate += (float) $settlementCompany['SettlementCompany']['fee_rate'];
					}
					$profit = floor($reservation['Reservation']['amount'] * $rate / 100);
					if (!$settlementCompany['SettlementCompany']['is_internal_tax']) {
						// 外税なら成果基準日の税率で消費税を加算
						$contractUnixTime = strtotime($reservation['Client']['conclusion_contract_criteria'] ?
							$reservation['Reservation']['return_datetime'] : $reservation['Reservation']['rent_datetime']);
						$profit = floor($profit * $this->TaxRate->getConsumptionTaxRate(date('Y', $contractUnixTime), date('n', $contractUnixTime)));
					}
				}
				$reservation['Reservation']['profit'] = $profit;

				$this->set(compact('reservation'));

				// dトラベルの場合はテンプレート切替
				if (!empty($reservation['Reservation']['advertising_cd']) && strncmp($reservation['Reservation']['advertising_cd'], 'dtravel', 7) === 0) {
					$this->view = ($this->view !== 'sp/sp_completion') ? 'completion_dtravel' : 'sp/sp_completion_dtravel';
				}
			} else {
				$redirectFlg = true;
			}

			if ($this->Session->check('step1.redirect.url')) {
				$this->log($this->Session->id().'[completion] step1.redirect.url session delete', LOG_DEBUG);
				$this->Session->delete('step1.redirect.url');
			}
			
			// 広告コード削除
			$this->Cookie->delete('advertising_cd');
			
			// トラッキングコード削除
			$this->Cookie->delete('travelko_code');
		}

		if ($redirectFlg) {
			if ($this->Session->check('referer.plan')) {
				$redirectUrl = $this->Session->read('referer.plan');
			} else {
				$redirectUrl = '/';
			}
			$this->log($this->Session->id().'[completion] redirect 2', LOG_DEBUG);
			$this->redirect($redirectUrl);
		}

		$this->set('remainingStock', $remainingStock);
		$this->set('title_for_layout', '予約完了');
		$this->set('h1_for_layout', '予約完了');
		$this->set('top_txt', '予約完了');
		$this->set('description_for_layout', '予約完了');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setReservations($this->action, $reservation['Reservation']['advertising_cd']);
		$this->set('progress_arr', $progressArr);

	}

	public function sp_completion() {
		$this->completion('sp');
	}

	// Smart-C用注文識別情報を取得
	private function getSmartCReservationKey($reservation_key) {
		$key = '\t=9ya6ucTpGivwtgVacc^k!wq7h9Dga'; // 広告暗号化キー
		return $reservation_key . '_' . hash('sha256', $reservation_key . '_' . $key);
	}

	// JANet用注文識別情報を取得
	private function getJanetReservationKey($reservation_key) {
		$key = 'K%d#xpF8'; // 広告暗号化キー
		return $reservation_key . '_' . hash('sha256', $reservation_key . '_' . $key);
	}

	private function __planView($commodityItemId, $dateFrom) {
		$redirectFlg = false;

		// リクエスト条件
		if ($this->Session->check('reservation.requestData')) {
			$requestData = json_decode($this->Session->read('reservation.requestData'), true);
		} else {
			$redirectFlg = true;
		}

		// 基本料金
		if ($this->Session->check('reservation.basicCharge')) {
			$basicCharge = json_decode($this->Session->read('reservation.basicCharge'), true);
		} else {
			$redirectFlg = true;
		}

		// 商品アイテムデータ取得
		$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($commodityItemId, $dateFrom);
		if (!empty($commodityItemPriceData)) {
			// 商品データ取得
			$commodityData = $this->Commodity->getCommodityData($commodityItemPriceData['CommodityItem']['commodity_id'], $requestData);
		}

		if (empty($commodityItemPriceData) || empty($commodityData)) {
			$redirectFlg = true;
		} else {
			$commodityInfo = array_merge($commodityItemPriceData, $commodityData);

			$newCarRegistration = $this->newCarRegistration;

			// 参考車両イメージ
			//$commodityImages = $this->CommodityImage->getImageByCommodityId($commodityInfo['Commodity']['id']);

			// 装備セット
			$equipmentList = $this->Equipment->getEquipment();
			$commodityEquipment = $this->CommodityEquipment->getEquipmentData($commodityData['Commodity']['id']);

			if ($commodityData['Commodity']['day_time_flg'] == 0) {
				list($dayNight, $period, $period24) = $this->ReservationUtil->getPeriodArray($requestData['from'], $requestData['to']);

				// レンタル期間
				if ($dayNight > 0) {
					$rentalPeriod = $dayNight . '泊' . $period . '日';
				} else {
					$rentalPeriod = '日帰り';
				}
			} else {
				$reservationFrom = $requestData['from'];
				$reservationTo = $requestData['to'];
				// 時間制
				$rentalTime = abs((strtotime($reservationFrom) - strtotime($reservationTo)) / (60 * 60));
				// レンタル期間
				$rentalPeriod = $rentalTime . '時間';
			}

			// 支払方法（カード取得）
			if (!empty($commodityInfo['Client']['accept_card'])) {
				$clientCards = $this->ClientCard->getCardByClientId($commodityInfo['Client']['id']);
				$commodityInfo['Cards'] = $clientCards[$commodityInfo['Client']['id']];
			}

			$this->set(compact('commodityInfo', 'newCarRegistration', 'basicCharge', 'equipmentList', 'commodityEquipment', 'rentalPeriod'));
		}

		if ($redirectFlg) {
			if ($this->Session->check('referer.plan')) {
				$redirect = $this->Session->read('referer.plan');
			} else {
				$redirect = '/';
			}
			$this->redirect($redirect);
		}
	}

	private function getRecommendId($clientId, $date, $rentOfficeId) {
		// PRを経由したもの以外はレコメンドIDを設定しない
		if ($this->Session->check('recommend_flg')) {
			$recommend_flg = $this->Session->read('recommend_flg');
		}
		if ($recommend_flg != '1') {
			return 0;
		}
		$conditions = array(
			'fields' => array('Recommend.id'),
			'conditions' => array(
				'Recommend.client_id' => $clientId,
				'Recommend.apply_term_from <=' => $date,
				'Recommend.apply_term_to >=' => $date,
				'Recommend.is_published' => 1,
				'Recommend.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RentOffice',
					'table' => 'offices',
					'conditions' => array(
						'RentOffice.id' => $rentOfficeId,
						'RentOffice.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => array(
						'Area.id = RentOffice.area_id',
						'Area.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'RecommendPrefecture',
					'table' => 'recommend_prefectures',
					'conditions' => array(
						'RecommendPrefecture.recommend_id = Recommend.id',
						'RecommendPrefecture.prefecture_id = Area.prefecture_id',
						'RecommendPrefecture.delete_flg' => 0
					)
				),
			),
			'recursive' => -1
		);

		$result = $this->Recommend->findC('first', $conditions);

		return $result ? $result['Recommend']['id'] : 0;
	}
}

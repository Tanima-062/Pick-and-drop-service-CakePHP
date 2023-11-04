<?php
require_once("const/common_const.php");
require_once("payment/payment_interface.php");
require_once("payment/econ/econ_credit_log_class.php");
require_once("encrypt_class.php");
require_once("db_class.php");
require_once("classification_class.php");
require_once("application_class.php");
require_once("user_class.php");
require_once("notice_class.php");

class PaymentEconComponent extends Component {

	const JSF_PRO_URL = "https://www5.econ.ne.jp/multitoken/scripts/econScone.min.js";
	const JSF_DEV_URL = "https://test.econ.ne.jp/multitoken/scripts/econScone.min.js";

	private $redirect_url = '';
	private $econ_credit_log = null;
	private $order_id = '';
	public $msg = '';
	public $components = array('Session');

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	private function createUser($db) {
		// 新規登録 cm_th_application, cm_th_application_detail
		$user_id = (!empty($this->Session->read('payment.econ.user_id')) && $this->Session->read('payment.econ.user_equal') === true) ? $this->Session->read('payment.econ.user_id') : 0;
		if (!$user_id) {
			if (!_isLogin()) {
				// common.cm_tm_user
				$cmTmUserParams = array(
					'family_name' => (!empty($this->Session->read('payment.econ.family_name'))) ? $this->Session->read('payment.econ.family_name') : '',
					'first_name' => (!empty($this->Session->read('payment.econ.first_name'))) ? $this->Session->read('payment.econ.first_name') : '',
					'tel' => (!empty($this->Session->read('payment.econ.tel'))) ? $this->Session->read('payment.econ.tel') : '',
					'email' => (!empty($this->Session->read('payment.econ.email'))) ? $this->Session->read('payment.econ.email') : '',
					'mailmagazine_recept_flg' => 0,
					'password' => '',
					'member_status' => 0,
				);

				$user = new User($db);
				$user_id = $user->insertUser($cmTmUserParams, $db);
				if (!$user_id) {
					$this->log($this->Session->id()." user regist fail", LOG_DEBUG);
					$db->rollback();
					return '';
				}
			} else {
				$user_id = $_SESSION['user_id'];
			}
		}

		$this->Session->write('payment.econ.user_id', $user_id);

		return $user_id;
	}

	private function createEmptyApplication() {
		$db = GetDBInstance(DB_MAIN_MASTER);
		$db->beginTransaction();

		// 申込データにはユーザIDが必要なので作成する
		$user_id = $this->createUser($db);
		$this->log($this->Session->id()." user_id:".$user_id, LOG_DEBUG);

		$cm_application_id_old = $this->Session->read('payment.econ.cm_application_id');
		if (!empty($cm_application_id_old)) {
			$db->commit();
			return $cm_application_id_old;
		}

		$application_data = array(
			"user_id" => $user_id,
		);

		$data = array(
			"application_id" => 0,
			"service_cd" => SERVICE_CD_RC,
		);
		$data_array = array($data);
		$application = new Application($db);
		$cm_application_id = $application->insertApplication($application_data, $data_array, $db);
		if ($cm_application_id) {
			$db->commit();
			$this->Session->write('payment.econ.cm_application_id', $cm_application_id);
			$this->Session->write('payment.cm_application_id', $cm_application_id); // 後の処理でチェックされるためニコスのときの実装に合わせる。
			return $cm_application_id;
		} else {
			$db->rollback();
			return '';
		}
	}

	private function createOrderId() {
		$cm_application_id = $this->createEmptyApplication();
		$this->log($this->Session->id()." cm_application_id:".$cm_application_id, LOG_DEBUG);
		if (!empty($cm_application_id)) {
			return SERVICE_CD_RC. '-cc-'.$cm_application_id.'-'.date('YmdHis');
		} else {
			return '';
		}
	}

	public function getJsfUrl() {
		return (IS_PRODUCTION) ? self::JSF_PRO_URL : self::JSF_DEV_URL;
	}

	public function createToken($amount) {
		$econ = PaymentFactory::create('econtext');
		$econ->setType('card');
		$econ->with_cap = false;
		$econ->return_url = URL."rentacar/reservations/processing/". ($this->controller->viewVars['fromRentacarClient'] ? '?from_rentacar_client=true' : '');
		$econ->use_3Dsecure = 1;
		$fee = $this->getCreditRate();
		$econ->amount = intval($amount + $fee);
		$order_id = $this->createOrderId();
		if (empty($order_id)) {
			return false;
		}

		$econ_token = $econ->startSession($order_id);
		if (empty($econ_token)) {
			$this->log($this->Session->id()." ".$econ->error_msg, LOG_DEBUG);
			$this->log($this->Session->id()." ".$econ->error_code, LOG_DEBUG);
			return false;
		}

		$this->Session->write('payment.econ.session_token', $econ_token['session_token']);
		$this->Session->write('payment.econ.request_id', $econ_token['request_id']);
		$this->Session->write('payment.econ.order_id', $order_id);
		$this->Session->write('payment.econ.amount', $econ->amount);
		$this->Session->write('payment.econ.keijou', (int)$econ->with_cap);
		$this->Session->write('payment.econ.fee', $fee);

		return $econ_token;
	}

	/**
	 * 予約後にトークンを作る場合
	 */
	public function createTokenWithReservationId($reservation_id, $amount) {
		$res = $this->findPaymentLog($reservation_id);
		if (count($res) > 0 && !empty($res[0]['cm_application_id'])) {
			$cm_application_id = $res[0]['cm_application_id'];
		} else {
			$this->Session->write('message.error', 'システムエラーが発生しました。スカイチケットレンタカーサポートセンターにお問い合わせください。');
			return false;
		}

		$econ = PaymentFactory::create('econtext');
		$econ->setType('card');
		$econ->with_cap = false;
		$econ->return_url = URL."rentacar/mypages/completion/";
		$econ->use_3Dsecure = 1;
		//$fee = $this->getCreditRate();
		$fee = 0;
		$econ->amount = intval($amount + $fee);
		$order_id = SERVICE_CD_RC. '-cc-'.$cm_application_id.'-'.date('YmdHis');

		$econ_token = $econ->startSession($order_id);
		if (empty($econ_token)) {
			$this->log($this->Session->id()." ".$econ->error_msg, LOG_DEBUG);
			$this->log($this->Session->id()." ".$econ->error_code, LOG_DEBUG);
			$this->Session->write('message.error', 'システムエラーが発生しました(2)。スカイチケットレンタカーサポートセンターにお問い合わせください。');
			return false;
		}

		$this->Session->write('payment.econ.cm_application_id', $cm_application_id);
		$this->Session->write('payment.econ.session_token', $econ_token['session_token']);
		$this->Session->write('payment.econ.request_id', $econ_token['request_id']);
		$this->Session->write('payment.econ.order_id', $order_id);
		$this->Session->write('payment.econ.amount', $econ->amount);
		$this->Session->write('payment.econ.keijou', (int)$econ->with_cap);
		$this->Session->write('payment.econ.fee', $fee);
		$this->Session->write('payment.econ.user_id', $res[0]['user_id']);

		return $econ_token;
	}

	public function save() {
		$econCreditLog = new EconCreditLog();
		$econCreditLog->user_id = $this->Session->read('payment.econ.user_id');
		$econCreditLog->price = $this->Session->read('payment.econ.amount');
		$econCreditLog->fee = $this->Session->read('payment.econ.fee');
		$econCreditLog->other_payment_type_id = OTHER_TYPE_ECON_CREDIT;
		$econCreditLog->cm_application_id = $this->Session->read('payment.econ.cm_application_id');
		$econCreditLog->order_id = $this->Session->read('payment.econ.order_id');
		$econCreditLog->session_token = $this->Session->read('payment.econ.session_token');
		$econCreditLog->keijou = $this->Session->read('payment.econ.keijou');
		$econCreditLog->value = [
			'price_list' => [[
				'application_id' => 0,
				'cm_application_id' => $econCreditLog->cm_application_id,
				'service_cd' => SERVICE_CD_RC
			]],
			'session_id' => session_id()
		];

		$ret = $econCreditLog->saveBeforeData();
		return $ret;
	}

	public function recvTokenCheck($session_token) {
		if (empty($session_token)) {
			$this->redirect_url = 'referer.step1';
			$this->Session->write('message.error', 'システムエラーが発生しました。しばらくの時間経過後もう一度お試しください。');
			return false;
		}

		$econ_credit_log_arr = (new EconCreditLog())->getEconCreditLog(array(
			'session_token' => $session_token
		));

		if (count($econ_credit_log_arr) == 0) {
			$this->redirect_url = 'referer.step1';
			$this->Session->write('message.error', 'システムエラーが発生しました。しばらくの時間経過後もう一度お試しください。');
			return false;
		}

		$econ_credit_log = array_shift($econ_credit_log_arr);

		// 既に成功しているものがある場合はエラーとし以降の処理を行わない
		if ($econ_credit_log->info_code == '00000') {
			$this->redirect_url = 'referer.step1';
			$this->Session->write('message.error', '予約処理中です。完了しない場合はサポートにお問い合わせください。');
			return false;
		}

		// セッションチェック
		if (empty($econ_credit_log->value['session_id']) || $econ_credit_log->value['session_id'] != session_id()) {
			$econ_credit_log->updateEconCreditLog(array(
				'status'             => 500,                         // 使うわけではないのでなんでもいい
				'info_code'          => 'SYSTEM',
				'info'               => 'セッションエラー',
			));

			$this->redirect_url = 'referer.step1';
			$this->Session->write('message.error', 'セッションエラーが発生しました。最初からもう一度お試しください。');
			return false;
		}

		return $econ_credit_log;
	}

	public function complete($session_token, $cd3secResFlg) {
		$econ_credit_log = $this->recvTokenCheck($session_token);
		if (!$econ_credit_log) {
			return false;
		}

		if ($cd3secResFlg < 0 || 3 < $cd3secResFlg) { // エラー(APIの仕様)
			if ($cd3secResFlg == 4) {
				$info_code = '3DSEC';
				$info = 'パスワード判定エラー/パスワード未入力';

				$this->redirect_url = 'referer.step1';
				$this->Session->write('message.error', 'カード認証エラーです。詳細はカード会社にご確認ください。');
			} else if ($cd3secResFlg == 9) {
				$info_code = '3DSEC';
				$info = 'その他のエラー';

				$this->redirect_url = 'referer.step1';
				$this->Session->write('message.error', 'カード認証エラーです。詳細はカード会社にご確認ください。');
			} else { // このパターンはありえない(by ECON)ので出たら問題
				$info_code = 'REDIRECT';
				$info = 'その他のエラー';

				$this->redirect_url = 'referer.step1';
				$this->Session->write('message.error', 'カード認証エラーです。詳細はカード会社にご確認ください。');
			}

			$ret = $econ_credit_log->updateEconCreditLog(array(
				'status'             => $cd3secResFlg,
				'info_code'          => $info_code,
				'info'               => $info
			));

			if ($ret) {
				$econ_credit_log->saveResultData();
			}

			return false;
		}

		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');
		$pay_obj->amount = (int)$econ_credit_log->price; // ORDER_COMMITの場合必須

		$result_commit = $pay_obj->commitSession($session_token);

		if (!$result_commit) {
			$ret2 = $econ_credit_log->updateEconCreditLog(array(
				'status'             => $pay_obj->error_status,
				'info_code'          => $pay_obj->error_code,
				'info'               => $pay_obj->error_msg,
			));

			if ($ret2) {
				$econ_credit_log->saveResultData();
			}

			$this->redirect_url = 'referer.step1';
			$this->Session->write('message.error', $this->getMessageErrorCode($pay_obj->error_code));
			return false;
		}

		$ret3 = $econ_credit_log->updateEconCreditLog(array(
			'status'             => $result_commit['status'],
			'info_code'          => $result_commit['infoCode'],
			'info'               => $result_commit['info'],
			'econ_no'            => $result_commit['data']['econNo'],
			'econ_cardno4'       => $result_commit['data']['econCardno4'],
			'shonin_cd'          => $result_commit['data']['shoninCD'],
			'shimuke_cd'         => $result_commit['data']['shimukeCD'],
		));

		if (!$ret3) {
			$this->redirect_url = 'referer.step1';
			$this->Session->write('message.error', 'システムエラーが発生しました。サポートにお問い合わせください。');

			$pay_obj->is_member = false; // TODO:ECONカード会員登録する場合はtrueだが、今は固定
			if (!$pay_obj->orderCancel($econ_credit_log->order_id)) {
				$this->notice(sprintf("econ注文番号:%s ", $econ_credit_log->order_id), "ECON決済自動キャンセル失敗");
				return false;
			}

			$econ_credit_log->updateEconCreditLog(array(
				'cancel_dt' => date('Y-m-d H:i:s', time())
			));

			return false;
		}

		$this->econ_credit_log = $econ_credit_log;

		return true;
	}

	/*
	 * エラーコードに応じたメッセージを返す
	 */
	private function getMessageErrorCode($error_code) {

		switch ($error_code) {
			case 'C1470': // 決済失敗系エラー
				return 'カード決済に失敗しました。<br>システムエラーが発生しました。<br>スカイチケットフェリーサポートセンターまでご連絡ください。';
			case 'C1444': // 入力内容系エラー1
				return 'カードのセキュリティコードに誤りがあります。<br>正しい情報をご入力ください。';
			case 'C1465': // 入力内容系エラー2
				return 'カードの番号に誤りがあります。<br>正しい情報をご入力ください。';
			case 'C1483': // 入力内容系エラー3
				return 'カードの有効期限に誤りがあります。<br>正しい情報をご入力ください。';
			case 'C1490': // カード会社接続系エラー
			case 'C1499':
				return 'カード決済に失敗しました。<br>時間を置いてご利用ください。';
			case 'C1455': // カード状態系エラー
				return 'カードの利用限度額を超えています。<br>詳細はカード会社にご確認ください。';
			case 'C1401': // カード使用不可系エラー
			case 'C1412':
			case 'C1430':
			case 'C1454':
			case 'C1491':
			case 'C1492':
			case 'C1493':
			case 'C1494':
			case 'C1495':
			case 'C1496':
			case 'C1497':
			case 'C1498':
				return 'ご入力いただいたカードは、使用できません。<br>詳細はカード会社にご確認ください。';
			case 'E1112': // イーコン会員更新回数制限到達
				return 'ご入力いただいたカードは、使用できません。<br>時間を置いてご利用ください。';
			case '3DSEC': // 3Dセキュアエラー
				return 'カード認証エラーです。詳細はカード会社にご確認ください。';
			default:      // その他
				return 'カード決済に失敗しました。';
		}
	}

	/*
	 * 予約に成功したら決済結果をDBに登録する(登録に失敗したら決済キャンセル、決済キャンセルに失敗したら通知を出す)
	 */
	public function saveResultData($reservation_key) {
		if (!$this->econ_credit_log || !$this->econ_credit_log->saveResultData()) {
			$order_id = $this->Session->read('payment.econ.order_id');
			$subject = "予約成功 - ECON決済情報更新失敗";
			$message = sprintf("予約番号:%s - econ注文番号:%s", $reservation_key, $order_id);
			if (empty($order_id) || !$this->cancel($order_id)) {
				$subject = "予約成功 - ECON決済自動キャンセル失敗";
			}

			$this->notice($message, $subject);
		}
	}

	/*
	 * 予約失敗時に決済を取り消す(決済の取消に失敗したら通知を出す)
	 */
	public function cancelReservationFail() {
		$order_id = $this->Session->read('payment.econ.order_id');
		if (!$this->cancel($order_id)) {
			$this->notice(sprintf("econ注文番号:%s", $order_id), "予約失敗 - ECON決済自動キャンセル失敗");
		}
	}

	public function cancelByReservationId($reservation_id) {
		$order_id = $this->getOrderId($reservation_id);
		if (empty($order_id)) {
			return false;
		}

		if (!$this->cancel($order_id)) {
			$this->notice(sprintf("econ注文番号:%s", $order_id), "[app] ECON決済自動キャンセル失敗");
			return false;
		}

		return true;
	}

	private function cancel($order_id) {
		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');
		$pay_obj->is_member = false; // TODO:ECONカード会員登録する場合はtrueだが、今は固定
		if (!$pay_obj->orderCancel($order_id)) {
			return false;
		}

		$econ_credit_log_arr = (new EconCreditLog())->getEconCreditLog(array(
			'order_id' => $order_id
		));

		if (count($econ_credit_log_arr) == 0) {
			return false;
		}

		$econ_credit_log = array_shift($econ_credit_log_arr);

		if (!$econ_credit_log->updateEconCreditLog(array(
			'cancel_dt' => date('Y-m-d H:i:s', time())
		))) {
			return false;
		}

		return true;
	}

	public function inquiry($order_id) {
		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');

		return $pay_obj->orderStatus($order_id);
	}

	public function refundByReservationId($reservation_id, $amount) {
		$payment_log_arr = $this->findPaymentLog($reservation_id, ['info_code' => '00000']);

		foreach ($payment_log_arr as $payment_log) {
			$ret = $this->inquiry($payment_log['order_id']);
			if (!isset($ret['data']['amount'])) {
				$this->notice(sprintf("econ注文番号:%s", $payment_log['order_id']), "ECON決済自動返金失敗");
				return false;
			}

			if ($ret['data']['amount'] <= $amount) {
				if (!$this->refund($payment_log['order_id'], $ret['data']['amount'])) {
					$this->notice(sprintf("econ注文番号:%s", $payment_log['order_id']), "ECON決済自動返金失敗");
					return false;
				}
				$amount -= $ret['data']['amount'];
			} else {
				if (!$this->refund($payment_log['order_id'], $amount)) {
					$this->notice(sprintf("econ注文番号:%s", $payment_log['order_id']), "ECON決済自動返金失敗");
					return false;
				}
			}
		}

		return true;
	}

	private function refund($order_id, $amount) {
		$econ_credit_log_arr = (new EconCreditLog())->getEconCreditLog(array(
			'order_id' => $order_id
		));

		if (count($econ_credit_log_arr) == 0) {
			return false;
		}

		$econ_credit_log = array_shift($econ_credit_log_arr);

		if ((int)$econ_credit_log->price == (int)$amount) {
			return true;
		}

		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');
		$pay_obj->is_member = false; // TODO:ECONカード会員登録する場合はtrueだが、今は固定

		$ret = $pay_obj->orderChangeAmount($order_id, (int)$amount);
		if (!$ret) {
			$this->log("refund error:[error_status] ".$pay_obj->error_status,LOG_DEBUG);
			$this->log("refund error:[error_code] ".$pay_obj->error_code,LOG_DEBUG);
			$this->log("refund error:[error_msg] ".$pay_obj->error_msg,LOG_DEBUG);
			return false;
		}

		$action = ($pay_obj->is_member) ? 'card_change_amount_member' : 'card_change_amount';

		if (!$econ_credit_log->updateEconCreditLog(array(
			'action' => $action,
			'price' => $amount
		))) {
			return false;
		}

		return true;
	}

	public function getRedirect() {
		return $this->redirect_url;
	}

	public function getCreditRate() {
		$db = GetDBInstance(DB_MAIN_MASTER);    // master DB
		$classification = new Classification($db);
		$service_cd_idx = 5; // rentacar
		$temp_arr = $classification->getClassification(array("classification_cd" => "econ_credit_rate"));

		$credit_rate = 1080;
		
		foreach ($temp_arr AS $data) {
			if ($data["key_id"] == $service_cd_idx) {
				$credit_rate = $data['value_cd'];
				break;
			}
		}
		return floatval($credit_rate);
	}

	public function notice($message, $subject) {
		$room_id = 41382981; // レンタカー開発チーム(社内)

		$notice = new Notice($room_id);
		$ret = $notice->exec_notice($message, $subject);
		if (!$ret) {
			$this->log(sprintf("%s %s: 通知エラー([%s]%s)", $this->Session->id(), get_class(), $subject, $message), LOG_DEBUG);
		}
	}

	private function getOrderId($reservation_id) {
		if (!empty($this->order_id)) {
			return $this->order_id;
		}

		if ($this->econ_credit_log && !empty($this->econ_credit_log->order_id)) {
			return $this->econ_credit_log->order_id;
		}

		$res = $this->findPaymentLog($reservation_id);
		if (count($res) > 0 && !empty($res[0]['order_id'])) {
			$this->order_id = $res[0]['order_id'];
			return $res[0]['order_id'];
		}

		return '';
	}

	public function cardCapture($reservation_id, $amount) {
		$order_id = $this->getOrderId($reservation_id);
		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');
		$pay_obj->is_member = false; // TODO:ECONカード会員登録する場合はtrueだが、今は固定

		$result = $pay_obj->cardCapture($order_id, (int)$amount, date('Y/m/d'));
		if ($result) {
			$econ_credit_log_arr = (new EconCreditLog())->getEconCreditLog(array('order_id' => $order_id));
			$econ_credit_log = array_shift($econ_credit_log_arr);
			if (!$econ_credit_log->updateEconCreditLog(array('keijou' => 1))) {
				$this->msg = 'ログテーブルを与信から計上にするのに失敗しました';
				return false;
			}

			return true;
		} else {
			$this->msg = $pay_obj->error_msg."[".$pay_obj->error_code."]";
			return false;
		}
	}

	public function findPaymentLog($reservation_id, $condition=[]) {
		$db = GetDBInstance(DB_MAIN_MASTER);

		$sql = "
			SELECT
				re.id,
				re.reservation_key,
				cad.cm_application_id,
				cad.service_cd,
				coec.order_id,
				coec.price,
				coec.keijou,
				coec.user_id
			FROM
				rentacar.reservations AS re
			INNER JOIN
		  		skyticket.cm_th_application_detail AS cad 
		  	ON
		  		re.id = cad.application_id
		  	INNER JOIN
		  		common.cm_th_other_payment_econ_credit_log AS coec
		  	ON
		  		cad.cm_application_id = coec.cm_application_id
		  	AND
		  		cad.service_cd = 'rc'
		  	AND
		  		coec.info_code = '00000'
			AND
				coec.status = 1
		";

		$param_arr = [];
		if ($reservation_id) {
			$sql .=  " AND re.id = :reservation_id";
			$param_arr[':reservation_id'] = $reservation_id;
		}

		if (isset($condition['reservation_status_id'])) {
			$sql .= " AND re.reservation_status_id = :reservation_status_id";
			$param_arr[':reservation_status_id'] = $condition['reservation_status_id'];
		}

		if (isset($condition['info_code'])) {
			$sql .= " AND coec.info_code = :info_code";
			$param_arr[':info_code'] = $condition['info_code'];
		}

		if (isset($condition['keijou'])) {
			$sql .= " AND coec.keijou = :keijou";
			$param_arr[':keijou'] = $condition['keijou'];
		}

		if (isset($condition['create_dt_start'])) {
			$sql .= " AND coec.create_dt >= :create_dt_start";
			$param_arr[':create_dt_start'] = $condition['create_dt_start'];
		}

		if (isset($condition['create_dt_end'])) {
			$sql .= " AND coec.create_dt < :create_dt_end";
			$param_arr[':create_dt_end'] = $condition['create_dt_end'];
		}

		if (isset($condition['cancel_dt'])) {
			$sql .= " AND coec.cancel_dt = :cancel_dt";
			$param_arr[':cancel_dt'] = $condition['cancel_dt'];
		}

		$sql .= ' ORDER BY other_payment_econ_credit_log_id DESC';

		if ($db->execute($sql, $param_arr, $st)) {
			$res = $db->fetchAll($st);
			return $res;
		}

		return [];
	}

	/**
	 * 与信->計上にならなければならないのに、与信として残ってしまっているものがあれば通知する
	 */
	public function pickUpCapturedFailure() {
		$paymentLogArr = $this->findPaymentLog(null, [
			'keijou' => 0, // 与信
			'reservation_status_id' => Constant::STATUS_RESERVATION,
			'create_dt_start' => date('Y-m-d').' 00:00:00',
			'create_dt_end' => date('Y-m-d H:i:s', strtotime("-1 hour")),
			'cancel_dt' => '0000-00-00 00:00:00'
		]);

		if (count($paymentLogArr) > 0) {
			$subject = 'ECON与信->計上漏れ';
			$msg = '';
			foreach ($paymentLogArr as $paymentLog) {
				$msg .= '予約番号:'.$paymentLog['reservation_key']."\r\n";
			}
			$this->notice($msg, $subject);
		}
	}
}

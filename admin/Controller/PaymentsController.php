<?php

App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');

/**
 * Payments Controller
 *
 * @property Payment $Payment
 */
class PaymentsController extends AppController {
	public $components = array('PaymentEcon', 'PaymentAPI');
	public $uses = ['Payment', 'Reservation'];

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->loadModel('CancelReason');
		$this->loadModel('ReservationStatus');
		$payments = [];
		if (count($this->request->query) > 0) { // 検索 or csvボタン押下

			if (!empty($this->request->query['getCsv'])) {
				$this->__downloadCsvData();
			}
			else {
				$payments = $this->__search();
			}
		}
		else { // 初期値
			$this->set('cm_application_id', '');
			$this->set('order_id', '');
			$this->set('create_dt_start', date('Y-m-d'));
			$this->set('create_dt_end', '');
			$this->set('payment_result', '--');
			$this->set('reservation_key_compress', '');
			$this->set('reservation_status', '--');
			$this->set('payment_status', '--');
			$this->set('client_id', '--');
			$this->set('reserve_created_start', '');
			$this->set('reserve_created_end', '');
			$this->set('reserve_canceled_start', '');
			$this->set('reserve_canceled_end', '');
			$this->set('cancel_reason_id', '--');
		}

		$this->set('is_pagenate', count($payments));
		$this->set('payments', $payments);
		$this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
		$this->set('cancelReasons', $this->CancelReason->find('list', ['fields' => ['id', 'reason']]));
		$this->set('reservation_status_arr', $this->ReservationStatus->find('list', ['field' => ['id', 'name']]));
		$this->set('paymentComponent', $this->PaymentEcon);
	}

	public function new_index() {
		$payments = [];
		if (count($this->request->query) > 0) { // 検索 or csvボタン押下
			if ($this->checkParams($this->request->query)) {
				if (!empty($this->request->query['getCsv'])) {
					$this->__new_downloadCsvData();
				}
				else {
					$payments = $this->__new_search(20);
				}
			}
		}
		else { // 初期値
			$this->set('new_cm_application_id', '');
			$this->set('new_created_at_start', date('Y-m-d'));
			$this->set('new_created_at_end', date('Y-m-d'));
			$this->set('progress', '');
		}

		$this->set('is_pagenate', count($payments));
		$this->set('payments', $payments);
		$this->set('paymentAPI', $this->PaymentAPI);
	}

	private function __search() {
		$this->Payment->recursive = 0;
		$this->Paginator->settings = $this->__setCondition(20);
		$this->Paginator->settings['paramType'] = 'querystring';
		$payments = $this->paginate();

		return $this->Payment->changeViewList($payments);
	}

	private function __new_search($limit=0) {
		$arr = $this->__new_setCondition($limit);

		if(empty($arr)) {
			return [];
		}

		$cmApplicationIds = [];
		foreach ($arr as $key => $val) {
			if (empty($val['cm_application_ids']['rc'])) {
				$arr[$key]['cm_application_id'] = '';
				continue;
			}

			$cmApplicationIds[] = $val['cm_application_ids']['rc'][0];
			$arr[$key]['cm_application_id'] = $val['cm_application_ids']['rc'][0];
		}

		$params['cm_application_ids'] = array_unique($cmApplicationIds);

		$reservationData = $this->Reservation->makePRConditions($params);

		foreach ($arr as $key => $val) {
			foreach($reservationData as $index => $reservation) {
				if (empty($val['cm_application_id']) ||
					(int)$val['cm_application_id'] !== (int)$reservation['CmThApplicationDetail']['cm_application_id']) {
					continue;
				}

				$arr[$key] += $reservationData[$index];
			}
		}

		return $this->Payment->newChangeViewList($arr);
	}

	private function __downloadCsvData() {
		Configure::write('debug',0); // debugコードを出さない

		$paymentData = $this->Payment->changeViewList($this->Payment->find('all', $this->__setCondition()));

		/**
		 * CSVの処理
		 */
		$csvFile = 'payment-'.date('YmdHis') . '.csv';

		// ヘッダ出力
		header("Content-disposition: attachment; filename=" . $csvFile);
		header("Content-type: application/octet-stream; name=" . $csvFile);

		// ストリーム出力
		$fp = @fopen('php://output', 'w');
		if (!$fp) {
			exit;
		}

		// SJIS指定
		stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

		// 見出し
		$csvSubject = 'skyticket申込番号,'
					. 'econ注文番号,'
					. '与信/計上,'
					. 'econ会員/非会員,'
					. '決済開始日,'
					. '決済金額,'
					. '応答ステータス,'
					. '決済処理結果,'
					. '予約番号,'
					. '予約ステータス,'
					. '入金ステータス,'
					. '会社名,'
					. '申込日時,'
					. 'キャンセル申込日時,'
					. "キャンセル理由\r\n";

		fwrite($fp, $csvSubject);

		foreach($paymentData as $data) {
			$csvData = $data['Payment']['cm_application_id'].','
					 . $data['Payment']['order_id'].','
					 . $data['Payment']['keijou'].','
					 . $data['Payment']['is_member'].','
					 . $data['Payment']['create_dt'].','
					 . $data['Payment']['price'].','
					 . $data['Payment']['info'].','
					 . $data['Payment']['status_str'].','
					 . $data['Reservation']['reservation_key_compress'].','
					 . $data['Reservation']['reservation_status'].','
					 . $data['Reservation']['payment_status'].','
					 . $data['Client']['name'].','
					 . $data['Reservation']['created'].','
					 . $data['Reservation']['cancel_datetime'].','
					 . $data['CancelReason']['reason']."\r\n";

			fwrite($fp, $csvData);
		}

		fclose($fp);
		exit;
	}

	private function __new_downloadCsvData() {
		Configure::write('debug',0); // debugコードを出さない

		/**
		 * CSVの処理
		 */
		$csvFile = 'payment-'.date('YmdHis') . '.csv';

		// ヘッダ出力
		header("Content-disposition: attachment; filename=" . $csvFile);
		header("Content-type: application/octet-stream; name=" . $csvFile);

		// ストリーム出力
		$fp = @fopen('php://output', 'w');
		if (!$fp) {
			exit;
		}

		// SJIS指定
		stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

		// 見出し
		$csvSubject = 'skyticket申込番号,'
					. 'カート番号,'
					. '注文番号,'
					. '決済開始日,'
					. '決済金額,'
					. '決済処理結果,'
					. '予約番号,'
					. '予約ステータス,'
					. '入金ステータス,'
					. '会社名,'
					. '申込日時,'
					. 'キャンセル申込日時,'
					. "キャンセル理由\r\n";

		fwrite($fp, $csvSubject);

		$paymentData = $this->__new_search(50);
		$lastPage = $this->viewVars['last_page'];
		for ($i = 1; $i <= $lastPage; $i++) {
			if ($i > 1) {
				$this->request->query['page'] = $i;
				$paymentData = $this->__new_search(50);
			}
			foreach ($paymentData as $data) {
				$csvData = $data['cm_application_id'] . ','
				. $data['cart_id'] . ','
				. $data['order_code'] . ','
				. $data['created_at'] . ','
				. $data['price'] . ','
				. $data['progress_name'] . ','
				. $data['Reservation']['reservation_key'] . ','
				. $data['ReservationStatus']['name'] . ','
				. $data['Reservation']['payment_status'] . ','
				. $data['Client']['name'] . ','
				. $data['Reservation']['created'] . ','
				. $data['Reservation']['cancel_datetime'] . ','
				. $data['CancelReason']['reason'] . "\r\n";

				fwrite($fp, $csvData);
			}
		}

		fclose($fp);
		exit;
	}

	private function __setCondition($limit=0) {
		$cm_application_id =		(isset($this->request->query['cm_application_id']))			? trim($this->request->query['cm_application_id']) : '';
		$order_id =					(isset($this->request->query['order_id']))					? trim($this->request->query['order_id']) : '';
		$create_dt_start =			(isset($this->request->query['create_dt_start']))			? trim($this->request->query['create_dt_start']) : '';
		$create_dt_end =			(isset($this->request->query['create_dt_end']))				? trim($this->request->query['create_dt_end']) : '';
		$payment_result =			(isset($this->request->query['payment_result']))			? $this->request->query['payment_result'] : '';
		$reservation_key_compress =	(isset($this->request->query['reservation_key_compress']))	? trim($this->request->query['reservation_key_compress']) : '';
		$reservation_status =		(isset($this->request->query['reservation_status']))		? $this->request->query['reservation_status'] : '';
		$payment_status =			(isset($this->request->query['payment_status']))			? $this->request->query['payment_status'] : '';
		$client_id =				(isset($this->request->query['client_id']))					? $this->request->query['client_id'] : '';
		$reserve_created_start =	(isset($this->request->query['reserve_created_start']))		? trim($this->request->query['reserve_created_start']) : '';
		$reserve_created_end =		(isset($this->request->query['reserve_created_end']))		? trim($this->request->query['reserve_created_end']) : '';
		$reserve_canceled_start =	(isset($this->request->query['reserve_canceled_start']))	? trim($this->request->query['reserve_canceled_start']) : '';
		$reserve_canceled_end =		(isset($this->request->query['reserve_canceled_end']))		? trim($this->request->query['reserve_canceled_end']) : '';
		$cancel_reason_id =			(isset($this->request->query['cancel_reason_id']))			? $this->request->query['cancel_reason_id'] : '';

		$params = [
			'cm_application_id'        => $cm_application_id,
			'order_id'                 => $order_id,
			'create_dt_start'          => $create_dt_start,
			'create_dt_end'            => $create_dt_end,
			'payment_result'           => $payment_result,
			'reservation_key_compress' => $reservation_key_compress,
			'reservation_status'       => $reservation_status,
			'payment_status'           => $payment_status,
			'client_id'                => $client_id,
			'reserve_created_start'    => $reserve_created_start,
			'reserve_created_end'      => $reserve_created_end,
			'reserve_canceled_start'   => $reserve_canceled_start,
			'reserve_canceled_end'     => $reserve_canceled_end,
			'cancel_reason_id'         => $cancel_reason_id,
			'limit'                    => $limit
		];

		foreach($params as $key => $value) {
			$this->set($key, $value);
		}

		return $this->Payment->makePRConditions($params);
	}

	private function __new_setCondition($limit=0) {
		$cartId          = (isset($this->request->query['new_cart_id']))             ? trim($this->request->query['new_cart_id'])           : '';
		$orderCode       = (isset($this->request->query['new_order_code']))          ? trim($this->request->query['new_order_code'])        : '';
		$cmApplicationId = (isset($this->request->query['new_cm_application_id']))   ? trim($this->request->query['new_cm_application_id']) : '';
		$createdAtStart  = (isset($this->request->query['new_created_at_start']))    ? trim($this->request->query['new_created_at_start'])  : '';
		$createdAtEnd    = (isset($this->request->query['new_created_at_end']))	     ? trim($this->request->query['new_created_at_end'])    : '';
		$progress        = (isset($this->request->query['progress']))	         ? $this->request->query['progress']                : '';
		$page            = (isset($this->request->query['page']))   	         ? $this->request->query['page']                    : 1;

		$params = [
			'id'              => '',
			'orderCode'       => $orderCode,
			'paymentFlg'      => '',
			'cartId'          => $cartId,
			'userId'          => '',
			'cmApplicationId' => $cmApplicationId,
			'serviceCd'       => 'rc',
			'paymentMethodId' => '',
			'createdAtStart'  => $createdAtStart,
			'createdAtEnd'    => $createdAtEnd,
			'progress'        => $progress,
			'limit'           => $limit,
			'page'            => $page
		];

		$url = $this->PaymentAPI->getApiUrlPaymentsList();
		$res = $this->PaymentAPI->runApi($url, 'get', $params);
		$arr = json_decode($res->body, true)['list'];

		$params = [
			'new_order_code'        => $orderCode,
			'new_cart_id'           => $cartId,
			'new_cm_application_id' => $cmApplicationId,
			'new_created_at_start'  => $createdAtStart,
			'new_created_at_end'    => $createdAtEnd,
			'progress'              => $progress,
			'limit'                 => $limit,
			'page'                  => $page
		];

		$params['current_page'] = $arr['current_page'];
		$params['last_page'] = $arr['last_page'];
		$params['total'] = $arr['total'];

		$query = '';
		foreach($params as $key => $value) {
			$this->set($key, $value);
			$query = $query . $key. '='. $value. '&';
		}

		$paging = $this->paging($params['last_page'], $params['current_page'], $pageRange = 2, $query);
		$this->set('paging', $paging);

		return $arr['data'];
	}

	public function paging($totalPage, $page = 1, $pageRange = 2, $query) {
    
		// ページ番号
		$page = (int) htmlspecialchars($page);
		
		// 前ページと次ページの番号計算
		$prev = max($page - 1, 1);
		$next = min($page + 1, $totalPage);
		
		$nums = []; // ページ番号格納用
		$start = max($page - $pageRange, 2); // ページ番号始点
		$end = min($page + $pageRange, $totalPage - 1); // ページ番号終点
		
		if ($page === 1) { // １ページ目の場合
			$end = min($pageRange * 2, $totalPage - 1); // 終点再計算
		}
	  
		// ページ番号格納
		for ($i = $start; $i <= $end; $i++) {
			$nums[] = $i;
		}

		$html = '';

		// 前のページへのリンク
		if ($page > 1) {
			$html = $html. '<li><a href="?'. $query. 'page='. $prev. '">< 前へ</a></li>';
		} else {
			$html = $html. '<li class="disabled"><a>< 前へ</a></li>';
		}
		
		// 最初のページ番号へのリンク
		if ($page === 1) {
			$html = $html. '<li class="disabled"><a>1</a></li>';
		} else {
			$html = $html. '<li><a href="?'. $query. 'page=1">1</a></li>';
		}
		if ($start > $pageRange) $html = $html. '<li class="current disabled"><a>...</a></li>'; // ドットの表示
		  
		//ページリンク表示ループ
		foreach ($nums as $num) {
			// 現在地
			if ($num === $page) {
				$html = $html. '<li class="current disabled"> <a href="#">' . $num . '</a> </li>';
			} else {
			// ページ番号リンク表示
				$html = $html. '<li><a href="?'. $query. 'page='. $num .'" class="num">' . $num . '</a></li>';
			}
	  
		}
		
		if (($totalPage - 1) > $end ) $html = $html. '<li class="current disabled"><a>...</a></li>'; // ドットの表示
		  
		//最後のページ番号へのリンク
		if ($page < $totalPage) {
			$html = $html. '<li><a href="?'. $query. 'page='. $totalPage .'">' . $totalPage . '</a></li>';
		} elseif ($page !== 1) {
			$html = $html. '<li class="disabled"><a>' . $totalPage . '</a></li>';
		}
		
		// 次のページへのリンク
		if ($page < $totalPage){
			$html = $html. '<li><a href="?'. $query. 'page='.$next.'">次へ ></a></li>';
		} else {
			$html = $html. '<li class="disabled"><a>次へ ></a></li>';
		}

		return $html;	  
	}

	public function ajaxYoshinCancel() {
		$this->autoRender = false;

		if (!$this->request->is('ajax')) {
			return false;
		}

		if ($this->PaymentEcon->yoshinCancel($this->request->data['order_id'])) {
			return json_encode(['ret' => 'ok']);
		} else {
			return json_encode(['ret' => 'error', 'message' => 'キャンセル失敗']);
		}
	}

	public function ajaxCardCapture() {
		$this->autoRender = false;

		if (!$this->request->is('ajax')) {
			return false;
		}

		if ($this->PaymentEcon->cardCapture($this->request->data['order_id'])) {
			return json_encode(['ret' => 'ok']);
		} else {
			return json_encode(['ret' => 'error', 'message' => '計上失敗']);
		}
	}

	// 新決済用
	public function ajaxYoshinCancelForAPI() {
		$this->autoRender = false;

		if (!$this->request->is('ajax')) {
			return false;
		}

		if ($this->PaymentAPI->yoshinCancelForAPI($this->request->data['orderCode'])) {
			return json_encode(['ret' => 'ok']);
		} else {
			return json_encode(['ret' => 'error', 'message' => 'キャンセル失敗']);
		}
	}

	public function ajaxCardCaptureForAPI() {
		$this->autoRender = false;

		if (!$this->request->is('ajax')) {
			return false;
		}

		if ($this->PaymentAPI->cardCaptureForAPI($this->request->data['orderCode'], $this->request->data['reservationId'])) {
			return json_encode(['ret' => 'ok']);
		} else {
			return json_encode(['ret' => 'error', 'message' => '計上失敗']);
		}
	}

	// 新決済API用バリデーション
	public function checkParams($params) {
		// 決済APIでこの全てが空白だとNG
		$targetKey['new_cm_application_id'] = 'skyticket申込番号';
		$targetKey['new_cart_id'] = 'カート番号';
		$targetKey['new_order_code'] = '注文番号';
		$targetKey['new_created_at_start'] = '決済開始日';
		$targetKey['new_created_at_end'] = '決済開始日';

		$targetCnt = 0;
		$emptyCnt = 0;
		$start = '';
		$end = '';
		foreach ($params as $key => $val) {
			// falseだと__new_setConditionのsetを通らなくなるのでここで入れておく
			$this->set($key, $val);
			if (array_key_exists($key, $targetKey)) {
				$targetCnt++;
				if ($val == '') {
					$emptyCnt++;
				} else {
					if ($key == 'new_created_at_start') {
						$start = $val;
					}
					if ($key == 'new_created_at_end') {
						$end = $val;
					}
				}
			}
		}
		if ($emptyCnt == $targetCnt) {
			$this->Session->setFlash('「' . implode('」「', array_unique($targetKey)) . '」のいずれかを入力してください。','default',array('class'=>'alert alert-error'));
			return false;
		}
		if (!empty($start) || !empty($end)){
			if (!empty($start) && !empty($end)) {
				list($year, $month, $date) = explode('-', $start);
				if (!checkdate($month, $date, $year)) {
					$this->Session->setFlash('「' . $targetKey['new_created_at_start'] . '」には「yyyy-mm-dd」形式で正しい日付を入力してください。','default',array('class'=>'alert alert-error'));
					return false;
				}
				list($year, $month, $date) = explode('-', $end);
				if (!checkdate($month, $date, $year)) {
					$this->Session->setFlash('「' . $targetKey['new_created_at_start'] . '」には「yyyy-mm-dd」形式で正しい日付を入力してください。','default',array('class'=>'alert alert-error'));
					return false;
				}
				$start = new DateTime($start);
				$end = new DateTime($end);
				if ($start > $end) {
					$this->Session->setFlash('「' . $targetKey['new_created_at_start'] . '」の開始日と終了日を入れ替えてください。','default',array('class'=>'alert alert-error'));
					return false;
				}
				$interval = $start->diff($end)->format('%a');
				if ($interval > 90) {
					$this->Session->setFlash('「' . $targetKey['new_created_at_start'] . '」は90日間の範囲で入力してください。','default',array('class'=>'alert alert-error'));
					return false;
				}
			} else {
				$this->Session->setFlash('「' . $targetKey['new_created_at_start'] . '」は開始日、終了日の両方を入力してください。','default',array('class'=>'alert alert-error'));
				return false;
			}
		}
		return true;
	}

}

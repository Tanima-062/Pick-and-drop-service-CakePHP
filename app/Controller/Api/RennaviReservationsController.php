<?php
App::uses('BaseRestApiController', 'Controller');

class RennaviReservationsController extends BaseRestApiController {

	public $uses = array('Reservation', 'Office', 'RennaviTransaction', 'Equipment', 'Privilege', 'CarClass', 'OfficeStockGroup', 'CarClassStock');

	// トランザクションID登録試行上限
	const TID_TRY_MAX = 5;

	// トランザクションID
	private $f_tid = 0;

	// 予約件数取得
	public function count() {
		try {
			list($result, $errorCode, $params) = $this->prepare();
			if (!$result) {
				$this->setError($errorCode);
				return;
			}

			$now = date('Y-m-d H:i:s');
			$db = $this->Reservation->getDataSource();

			// 確定前の連携中にマイページからキャンセルされた場合、一旦予約未確認として連携する
			// 確定する際にキャンセル未確認へ更新し、次回キャンセル未確認として連携されるようにする
			// 何らかの理由で一時ステータス（CXL_RESERVE_FIXING）が次の件数取得まで残ってしまった場合、通常のステータスに戻しておく
			if (in_array(Constant::RENNAVI_STATUS_CANCEL_USER, $params['f_status'])) {
				$count = $this->Reservation->rennaviStatusRestore($params['f_no'], $now, $params['f_eid']);
				if ($count) {
					$this->outputLog('CXL_RESERVE_FIXING to CANCEL_USER count: '.$count, 'debug');
				}
			}
			if (in_array(Constant::RENNAVI_STATUS_CANCEL_NOTIME, $params['f_status'])) {
				$count = $this->Reservation->rennaviStatusRestore($params['f_no'], $now, $params['f_eid'], false);
				if ($count) {
					$this->outputLog('CXL_RESERVE_FIXING to CANCEL_NOTIME count: '.$count, 'debug');
				}
			}

			$data = $this->Reservation->rennaviReserveCount($params['f_no'], $params['f_status'], $now, $params['f_eid']);
			if (count($data) <= 0) {
				$this->setError('NML_NODATA', false);
				return;
			}
			$this->outputLog(sprintf('対象予約ID (%s)', implode(',', Hash::extract($data, '{n}.Reservation.id'))), 'debug');

			try {
				$db->begin();

				list($result, $errorCode) = $this->insertTransaction($params['f_no'], $now);
				if (!$result) {
					$this->setError($errorCode);
					return;
				}

				$count = $this->Reservation->rennaviReserveEntry($this->f_tid, $params['f_no'], $params['f_status'], $now, $params['f_eid']);
				if ($count <= 0) {
					$this->setError('NML_NODATA');
					return;
				}
			} catch (PDOException $e) {
				$this->outputLog(sprintf("DB例外が発生しました %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
				$this->setError('ERR_DB');
				return;
			}
			$db->commit();

			$this->setSuccess($count);
		} catch (Exception $e) {
			$this->outputLog(sprintf("例外が発生しました %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
			$this->setError('ERR_SYS');
		}
	}

	// 予約ダウンロード
	public function download() {
		try {
			list($result, $errorCode, $params) = $this->prepare();
			if (!$result) {
				$this->setError($errorCode);
				return;
			}

			$now = date('Y-m-d H:i:s');
			$db = $this->Reservation->getDataSource();

			$data = $this->Reservation->rennaviReserveDownload($this->f_tid);
			$count = count($data);
			if ($count <= 0) {
				$this->setError('ERR_NODATA');
				return;
			}

			$yoyakus = $this->makeYoyakuList($data);

			try {
				$updCount = $this->updateTransaction(Constant::RENNAVI_TRANSACTION_DOWNLOAD, $now);
				if ($updCount != 1) {
					throw new Exception(sprintf('トランザクションの更新に失敗しました (更新件数 %d)', $updCount));
				}
			} catch (PDOException $e) {
				$this->outputLog(sprintf("DB例外が発生しました %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
				$this->setError('ERR_DB');
				return;
			}
			$db->commit();

			$this->setSuccess($count, $yoyakus);
		} catch (Exception $e) {
			$this->outputLog(sprintf("例外が発生しました %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
			$this->setError('ERR_SYS');
		}
	}

	// 予約確定
	public function fix() {
		try {
			list($result, $errorCode, $params) = $this->prepare();
			if (!$result) {
				$this->setError($errorCode);
				return;
			}

			$now = date('Y-m-d H:i:s');
			$db = $this->Reservation->getDataSource();

			try {
				$db->begin();

				$count = $this->Reservation->rennaviReserveFix($this->f_tid, $now);
				if ($count <= 0) {
					$this->setError('ERR_NODATA');
					return;
				}

				$updCount = $this->updateTransaction(Constant::RENNAVI_TRANSACTION_FIX, $now);
				if ($updCount != 1) {
					throw new Exception(sprintf('トランザクションの更新に失敗しました (更新件数 %d)', $updCount));
				}
			} catch (PDOException $e) {
				$this->outputLog(sprintf("DB例外が発生しました %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
				$this->setError('ERR_DB');
				return;
			}
			$db->commit();

			$this->setSuccess($count);
		} catch (Exception $e) {
			$this->outputLog(sprintf("例外が発生しました %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
			$this->setError('ERR_SYS');
		}
	}

	// 在庫手仕舞い
	public function tejimai()
	{
		try {
			list($result, $errorCode, $params) = $this->prepare();
			if (!$result) {
				$this->setError($errorCode);
				return;
			}

			$db = $this->CarClassStock->getDataSource();
			
			$carClassId			 = $params['f_scid'];
			$officeId			 = $params['f_eid'];
			$fromDate			 = $this->getFotmatDate($params['f_fromdate']);
			$toDate				 = $this->getFotmatDate($params['f_todate']);
			$tejimaiFlag		 = $params['f_tejimai'];	// 1は販売停止、0は販売停止解除

			$officeStockGroup	 = $this->OfficeStockGroup->getOfficeStockGroupId($officeId);
			$stockGroupId		 = $officeStockGroup['OfficeStockGroup']['stock_group_id'];

			// 対象在庫データの取得
			$carClassStocks = $this->getTargetCarClassStocks($stockGroupId, $carClassId, $fromDate, $toDate, $tejimaiFlag);
			$count = count($carClassStocks);
			$this->outputLog('target carClassStocks count:'. $count, 'debug');
			if ($count == 0) {
				// 検索結果0件
				$this->setError('ERR_S_NODATA');
				return;
			}

			$db->begin();
			
			// 販売フラグの値を更新する
			try {
				// 更新対象となる在庫のみ取得する
				$targetCarClassStocks = $this->getUpdateTargetCarClassStocks($carClassStocks, $tejimaiFlag);
				if (count($targetCarClassStocks) > 0) {
					$carClassStockIds	 = array_column($targetCarClassStocks, 'id'); 	// idだけ取得
					$suspension		 = ($tejimaiFlag) ? 1 : 0;						// 販売停止は1、販売停止解除は0
	
					$this->outputLog('update target carClassStocks ids: ' . implode(',', $carClassStockIds), 'debug');
	
					$saveData = array(
						'id'			 => $carClassStockIds,
						'suspension'	 => $suspension,
						'modified'		 => "'".date('Y-m-d H:i:s')."'"
					);
					$this->CarClassStock->bulkUpdateSuspension($saveData);
				}
			} catch (Exception $e) {
				throw new Exception(sprintf('在庫の更新に失敗しました (id %d suspension %d) %s', implode(',', $carClassStockIds), $suspension, $e->getMessage()));
			} 

			// 返却値の作成
			$zaikos = $this->makeTejimaiResponse($carClassStocks, $carClassId, $officeId, $tejimaiFlag);

			$db->commit();

			$this->setSuccess($count, $zaikos);
		} catch (Exception $e) {
			$db->rollback();
			$this->outputLog(sprintf("例外が発生しました %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
			$this->setError('ERR_SYS');
		}
	}

	// 描画後
	public function afterFilter() {
		parent::afterFilter();

		if (Configure::read('debug')) {
			// デバッグONだとXmlViewがprettyオプションつけて整形してしまうので、クリア
			$this->response->body(preg_replace('/\n */', '', $this->response->body()));
		}
		if ($this->isReserveDownload()) {
			// 連想配列は同一キー(yoyaku)の繰り返しを表現できないので、XML変換後に置換する
			$this->response->body(preg_replace('/REPLACE[1-9][0-9]*/', 'yoyaku', $this->response->body()));
		}
		// 個人情報除きログ出力
		$personalInfos = array(
			'<yoyakusya_shimei>.*<\/yoyakusya_shimei>',
			'<yoyakusya_shimei_kana>.*<\/yoyakusya_shimei_kana>',
			'<riyousya_shimei>.*<\/riyousya_shimei>',
			'<riyousya_shimei_kana>.*<\/riyousya_shimei_kana>',
			'<riyousya_tel>.*<\/riyousya_tel>',
			'<kinkyu_tel>.*<\/kinkyu_tel>'
		);
		$this->outputLog(preg_replace('/('.implode('|', $personalInfos).')/', '', $this->response->body()), 'debug');
	}

	// トランザクション登録
	private function insertTransaction($clientId, $nowDatetime) {
		for ($i = 1; $i <= self::TID_TRY_MAX; $i++) {
			$transaction = $this->RennaviTransaction->findByOneTimePass($this->f_tid);
			if (!empty($transaction)) {
				if ($i < self::TID_TRY_MAX) {
					$oldTid = $this->f_tid;
					$this->f_tid = $this->publishTid();
					$this->outputLog(sprintf('トランザクションIDを再発行しました (旧ID %d)', $oldTid), 'debug');
				} else {
					$this->outputLog(sprintf('トランザクションIDが%d回連続で重複しました', self::TID_TRY_MAX), 'error');
					return array(false, 'ERR_D_TID');
				}
			} else {
				break;
			}
		}

		$result = $this->RennaviTransaction->save(array(
			'client_id'		 => $clientId,
			'one_time_pass'	 => $this->f_tid,
			'expiration'	 => date('Y-m-d H:i:s', strtotime($nowDatetime.' +1 day')),
			'status'		 => Constant::RENNAVI_TRANSACTION_COUNT
		));
		if ($result === false) {
			$this->outputLog('トランザクションIDの登録に失敗しました', 'error');
			return array(false, 'ERR_DB');
		}

		return array(true, '');
	}

	// トランザクション更新
	private function updateTransaction($status, $nowDatetime) {
		$this->RennaviTransaction->updateAll(
			array(
				'status'	 => $status,
				'modified'	 => $this->RennaviTransaction->getDataSource()->value($nowDatetime)
			),
			array('one_time_pass' => $this->f_tid)
		);
		return $this->RennaviTransaction->getAffectedRows();
	}

	// success設定
	private function setSuccess($count, $yoyakus = array()) {
		$this->initResponseData();
		$this->responseData['errors']['error']['key']	 = 'success';
		$this->responseData['yoyaku_log']['cnt']		 = $count;
		if ($this->isReserveCount()) {
			$this->responseData['transaction_info']['tid'] = $this->f_tid;
		} elseif ($this->isReserveDownload()) {
			$this->responseData['yoyakus'] = $yoyakus;
		} else if ($this->isTejimaiFix()) {
			unset($this->responseData['yoyaku_log']);
			$this->responseData['zaikos']['zaiko'] = $yoyakus;
		}
	}

	// error設定
	private function setError($errorCode, $logError = true) {
		if ($logError) {
			$this->outputLog(sprintf('エラーが発生しました (%s)', $errorCode), 'error');
		}
		$info = $this->getErrorInfo($errorCode);
		$this->initResponseData();
		$this->responseData['errors']['error']['key']		 = $info['key'];
		$this->responseData['errors']['error']['message']	 = $info['message'];
	}

	// エラー情報取得
	private function getErrorInfo($errorCode) {
		// コード => ['key' => エラーキー, 'message' => エラーメッセージ]
		$errorInfos = array(
			'ERR_SYS'		 => array('key' => 'rcback.system.error', 'message' => 'システムエラー'),
			'ERR_DB'		 => array('key' => 'rcback.db.error', 'message' => 'データベースエラー'),
			'ERR_SSL'		 => array('key' => 'rcback.ssl.error', 'message' => 'HTTPSでアクセスしてください'),
			'ERR_AUTH'		 => array('key' => 'rcback.authentication.error', 'message' => '認証エラー'),
			'ERR_SUSPENDED'	 => array('key' => 'rcback.suspended.error', 'message' => '予約APIを一時的に停止しています'),
			'ERR_NODATA'	 => array('key' => 'rcback.noreserve.error', 'message' => '確認可能な予約はありません'),
			'NML_NODATA'	 => array('key' => 'success', 'message' => ''), // 予約件数取得のNODATAは正常
			'ERR_V_DIGEST'	 => array('key' => 'rcback.validate.digest.error', 'message' => '認証用パスワードフォーマットチェックエラー'),
			'ERR_V_VER'		 => array('key' => 'rcback.validate.ver.error', 'message' => 'バージョン番号フォーマットチェックエラー'),
			'ERR_V_NO'		 => array('key' => 'rcback.validate.no.error', 'message' => '事業者IDフォーマットチェックエラー'),
			'ERR_V_EID'		 => array('key' => 'rcback.validate.eid.error', 'message' => '営業所IDフォーマットチェックエラー'),
			'ERR_EID'		 => array('key' => 'rcback.eid.error', 'message' => '営業所IDが不正です'),
			'ERR_V_STATUS'	 => array('key' => 'rcback.validate.status.error', 'message' => '予約ステータスフォーマットチェックエラー'),
			'ERR_V_TID'		 => array('key' => 'rcback.validate.tid.error', 'message' => 'トランザクションIDフォーマットチェックエラー'),
			'ERR_D_TID'		 => array('key' => 'rcback.duplicate.tid.error', 'message' => 'トランザクションID重複エラー'),
			'ERR_TID'		 => array('key' => 'rcback.tid.error', 'message' => 'トランザクションIDが不正です'),
			'ERR_TIMEOUT'	 => array('key' => 'rcback.timeout.tid.error', 'message' => 'トランザクションIDの有効期限が切れています'),
			'ERR_DL_SKIP'	 => array('key' => 'rcback.skip.dl.error', 'message' => '予約ダウンロードが行われていません'),
			'ERR_FIXED'		 => array('key' => 'rcback.fixed.error', 'message' => '予約確定済みです'),
			'ERR_S_NODATA'	 => array('key' => 'rcback.search.nodata.error', 'message' => '検索結果が0件'),
			'ERR_V_SCID'	 => array('key' => 'rcback.validate.scid.error', 'message' => '詳細車両クラスIDフォーマットチェックエラー'),
			'ERR_SCID'		 => array('key' => 'rcback.scid.error', 'message' => '詳細車両クラスIDが不正です'),
			'ERR_V_FROMDATE' => array('key' => 'rcback.validate.fromdate.error', 'message' => '開始日フォーマットチェックエラー'),
			'ERR_V_TODATE'	 => array('key' => 'rcback.validate.todate.error', 'message' => '終了日フォーマットチェックエラー'),
			'ERR_V_TEJIMAI'	 => array('key' => 'rcback.validate.tejimai.error', 'message' => '手仕舞いフラグフォーマットチェックエラー'),
			'ERR_P_FROMDATE' => array('key' => 'rcback.past.fromdate.error', 'message' => '開始日が過去の日付です'),
			'ERR_P_TODATE'	 => array('key' => 'rcback.past.todate.error', 'message' => '終了日が過去の日付です'),
			'ERR_S_PERIOD'	 => array('key' => 'rcback.search.period.error', 'message' => '指定期間が長すぎます'),
		);
		if (isset($errorInfos[$errorCode])) {
			return $errorInfos[$errorCode];
		} else {
			$this->outputLog(sprintf('指定されたエラーコードが見つかりません (%s)', $errorCode), 'error');
			return $errorInfos['ERR_SYS'];
		}
	}

	// レスポンス初期化
	private function initResponseData() {
		$this->responseData = array(
			'errors' => array('error' => array('key' => '', 'message' => '')),
			'yoyaku_log' => array('cnt' => 0)
		);
		if ($this->isReserveDownload()) {
			$rootNode = 'yoyaku_list';
			$this->responseData['yoyakus'] = array();
		} else if ($this->isTejimaiFix()) {
			$rootNode = 'zaiko_list';
			unset($this->responseData['yoyaku_log']);
			$this->responseData['zaikos']['zaiko'] = array();
		} else {
			$rootNode = 'yoyaku_cnt';
			if ($this->isReserveCount()) {
				$this->responseData['transaction_info'] = array(
					'tid' => '9999999999', // ありえない値(正常時はきちんと設定する)
				);
			}
		}
		$this->set('_rootNode', $rootNode);
	}

	// 前処理
	private function prepare() {
		$params = $this->getParams();

		list($result, $errorCode) = $this->checkInput($params);
		if (!$result) {
			return array(false, $errorCode, $params);
		}

		if ($this->isReserveCount()) {
			// 取得ステータス未指定か0指定の場合、全て取得と同義
			if (count($params['f_status']) <= 0 || $params['f_status'][0] == Constant::RENNAVI_STATUS_GET_ALL) {
				$params['f_status'] = Constant::rennaviStatusApiTarget();
			}
			// NOSHOWはスカチケに無いが指定される可能性あり、エラーとせず無視する
			$params['f_status'] = array_values(array_diff($params['f_status'], array(Constant::RENNAVI_STATUS_NOSHOW)));
			if (count($params['f_status']) <= 0) {
				// NOSHOWのみ指定の場合、検索自体行わない
				return array(false, 'ERR_NODATA', $params);
			}
		} 

		return array(true, '', $params);
	}

	// 入力パラメータ取得
	private function getParams() {
		// 同じキーが複数来る場合あるので、$this->request->dataは使えない
		$input = $this->request->input();
		$this->outputLog(sprintf('POST値 (%s)', $input), 'debug');

		$f_no		 = '';
		$f_digest	 = '';
		$f_ver		 = '';
		$f_eid		 = array();
		$f_status	 = array();
		$f_tid		 = '';
		$f_fromdate	 = '';
		$f_todate	 = '';
		$f_scid		 = '';
		$f_tejimai	 = '';

		$params = explode('&', $input);
		foreach ($params as $param) {
			$kv = explode('=', $param);
			switch ($kv[0]) {
				case 'f_no':		// 事業者ID
					$f_no		 = $kv[1];
					break;
				case 'f_digest':	// 認証用パスワード
					$f_digest	 = $kv[1];
					break;
				case 'f_ver':		// バージョン番号
					$f_ver		 = $kv[1];
					break;
				case 'f_eid':		// 営業所ID
					$f_eid[]	 = $kv[1];
					break;
				case 'f_status':	// 取得予約ステータス
					$f_status[]	 = $kv[1];
					break;
				case 'f_tid':		// トランザクションID
					$f_tid		 = $kv[1];
					break;
				case 'f_fromdate':	// 開始日
					$f_fromdate	 = $kv[1];
					break;
				case 'f_todate':	// 終了日
					$f_todate	 = $kv[1];
					break;
				case 'f_scid':		// 詳細車両クラスid
					$f_scid		 = $kv[1];
					break;
				case 'f_tejimai':	// 手仕舞いフラグ
					$f_tejimai	 = $kv[1];
					break;
				default:			// 営業所グループ(f_egid)や未知のパラメータは無視
					break;
			}
		}

		$result = array(
			'f_no'		 => $f_no,
			'f_digest'	 => $f_digest,
			'f_ver'		 => $f_ver,
		);
		if ($this->isReserveCount()) {
			$result['f_eid']	 = $f_eid;
			$result['f_status']	 = $f_status;
			$this->f_tid		 = $this->publishTid();	// 暫定
		} else if ($this->isTejimaiFix()) {
			$result['f_fromdate']	 = $f_fromdate;
			$result['f_todate']		 = $f_todate;
			$result['f_eid']		 = $f_eid[0];
			$result['f_scid']		 = $f_scid;
			$result['f_tejimai']	 = $f_tejimai;
		} else {
			$this->f_tid		 = $f_tid;
		}
		$this->outputLog(sprintf('入力パラメータ (%s)', json_encode($result)), 'debug');

		return $result;
	}

	// 入力チェック
	private function checkInput($params) {
		if (!$this->request->is('ssl')) {
			return array(false, 'ERR_SSL');
		}

		list($result, $errorCode) = $this->validateParams($params);
		if (!$result) {
			return array(false, $errorCode);
		}

		if (!$this->authenticate($params['f_no'], $params['f_digest'], $params['f_ver'])) {
			return array(false, 'ERR_AUTH');
		}

		if ($this->isSuspendedByConfig($params['f_no'])) {
			return array(false, 'ERR_SUSPENDED');
		}

		if ($this->isReserveCount()) {
			if (!empty($params['f_eid'])) {
				// 営業所IDの存在チェック
				if (!$this->Office->belongToClient($params['f_eid'], $params['f_no'])) {
					return array(false, 'ERR_EID');
				}
			}
		} else if ($this->isTejimaiFix()) {

			// 開発環境にてテスト実施されるため、ひとまず開発環境だけ動くように制御する
			if (IS_PRODUCTION) {
				return array(false, 'ERR_AUTH');
			}
			// 在庫手仕舞APIについては、Jネットレンタカーへの提供はまだ行わない為、認証エラーとする
			if ($params['f_no'] == Constant::JNET_CLIENT_ID) {
				return array(false, 'ERR_AUTH');
			}
			// 営業所IDの存在チェック
			if (!$this->Office->belongToClient([$params['f_eid']], $params['f_no']) || !$this->Office->isAvailable($params['f_eid'])) {
				return array(false, 'ERR_EID');
			}
			// 車両クラスIDの存在チェック
			if (!$this->CarClass->belongToClient($params['f_scid'], $params['f_no']) || !$this->CarClass->isAvailable($params['f_scid'])) {
				return array(false, 'ERR_SCID');
			}
			// 開始日が過去の日付でないかをチェック
			if (!$this->isNotPastDate($params['f_fromdate'])) {
				return array(false, 'ERR_P_FROMDATE');
			}
			// 終了日が過去の日付でないかをチェック
			if (!$this->isNotPastDate($params['f_todate'])) {
				return array(false, 'ERR_P_TODATE');
			}
			// 開始日と終了日の期間をチェック
			if (!$this->isSpecifiablePeriod($params['f_fromdate'], $params['f_todate'])) {
				return array(false, 'ERR_S_PERIOD');
			}
		} else {
			// トランザクションIDの存在、有効期限、ステータスチェック
			$transaction = $this->RennaviTransaction->findByOneTimePass($this->f_tid);
			if (empty($transaction)) {
				return array(false, 'ERR_TID');
			}
//			$this->outputLog(sprintf('トランザクション (%s)', json_encode($transaction)), 'debug');
			if ($transaction['RennaviTransaction']['client_id'] != $params['f_no']) {
				return array(false, 'ERR_TID');
			}
			if ($transaction['RennaviTransaction']['expiration'] < date('Y-m-d H:i:s')) {
				return array(false, 'ERR_TIMEOUT');
			}
			if ($transaction['RennaviTransaction']['status'] == Constant::RENNAVI_TRANSACTION_FIX) {
				return array(false, 'ERR_FIXED');
			}
			if ($this->isReserveFix()) {
				if ($transaction['RennaviTransaction']['status'] == Constant::RENNAVI_TRANSACTION_COUNT) {
					return array(false, 'ERR_DL_SKIP');
				}
			}
		}

		return array(true, '');
	}

	// バリデーション (フォーマットチェック)
	private function validateParams($params) {
		if (!preg_match('/^[0-9a-f]{32}$/', $params['f_digest'])) {
			return array(false, 'ERR_V_DIGEST');
		}
		if (!preg_match('/(^[1-9][0-9]{0,2}$)|(^[1-9].[0-9]$)/', $params['f_ver'])) {
			return array(false, 'ERR_V_VER');
		}
		if (!preg_match('/^[1-9][0-9]{0,2}$/', $params['f_no'])) {
			return array(false, 'ERR_V_NO');
		}
		if ($this->isReserveCount()) {
			foreach ($params['f_eid'] as $eid) {
				if (!preg_match('/^[1-9][0-9]{0,9}$/', $eid)) {
					return array(false, 'ERR_V_EID');
				}
			}
			$allowed = implode('', Constant::rennaviStatusApiTarget());
			if (count($params['f_status']) <= 1) {
				$allowed .= Constant::RENNAVI_STATUS_GET_ALL;
			}
			foreach ($params['f_status'] as $status) {
				if (!preg_match('/^['.$allowed.']$/', $status)) {
					return array(false, 'ERR_V_STATUS');
				}
			}
		} else if ($this->isTejimaiFix()) {
			if (!preg_match('/^[1-9][0-9]{1,7}$/', $params['f_eid'])) {
				return array(false, 'ERR_V_EID');
			}
			if (!preg_match('/^[1-9][0-9]{1,10}$/',$params['f_scid'])) {
				return array(false, 'ERR_V_SCID');
			}
			if (!preg_match('/^[0-1]{1}$/',$params['f_tejimai'])) {
				return array(false, 'ERR_V_TEJIMAI');
			}
			if (!$this->isDateFormatYmd($params['f_fromdate'])) {
				return array(false, 'ERR_V_FROMDATE');
			}
			if (!$this->isDateFormatYmd($params['f_todate'])) {
				return array(false, 'ERR_V_TODATE');
			}
		} else {
			if (!preg_match('/^[1-9][0-9]{0,9}$/', $this->f_tid)) {
				return array(false, 'ERR_V_TID');
			}
		}
		return array(true, '');
	}

	// 認証
	private function authenticate($f_no, $f_digest, $f_ver) {
		// 事業者ID => ['digest' => 認証パスワード, 'ver' => バージョン]
		// 事業者IDはこちらで決められるので、クライアントIDと一致させる
		$clientInfos = array(
			// Jネットレンタカー (そのうち連携するかも)
			Constant::JNET_CLIENT_ID => array(
				'digest'	 => IS_PRODUCTION ? 'de1e1975b8393241fdc2913021649d13' : 'e73a3bfec8eccdd53e783f9d44167236',
				'ver'		 => '1.0',
			),
			// スカイレンタカー
			Constant::SKY_CLIENT_ID => array(
				'digest'	 => IS_PRODUCTION ? 'f02316dd9e8b2509a4b98d9238a59bbf' : 'c6e4d910f48de064d4f07ec961a87cbc',
				'ver'		 => '1.0',
			),
		);
		return (isset($clientInfos[$f_no]) &&
			$clientInfos[$f_no]['digest'] == $f_digest && $clientInfos[$f_no]['ver'] == $f_ver);
	}

	// APIレスポンス許可確認
	private function isSuspendedByConfig($f_no) {
		// 会社別の設定を確認(ベースクラスはAPI全体)
		Configure::load('ApiConfig.php');
		$apiConfig = Configure::read('ApiConfig');
		if ($f_no == Constant::SKY_CLIENT_ID) {
			return !$apiConfig['RennaviReserveSky'];
		}
		if ($f_no == Constant::JNET_CLIENT_ID) {
			return !$apiConfig['RennaviReserveJnet'];
		}
		return true;
	}

	// 件数取得APIか？
	private function isReserveCount() {
		return ($this->request->params['action'] == 'count');
	}

	// ダウンロードAPIか？
	private function isReserveDownload() {
		return ($this->request->params['action'] == 'download');
	}

	// 確定APIか？
	private function isReserveFix() {
		return ($this->request->params['action'] == 'fix');
	}

	// 在庫手仕舞いAPIか？
	private function isTejimaiFix() {
		return ($this->request->params['action'] == 'tejimai');
	}

	// トランザクションID発行(1～4294967295)
	private function publishTid() {
		return mt_rand() + mt_rand() + 1;
	}

	// ログ出力
	private function outputLog($message, $type) {
		$this->log(sprintf('rennavi/reserve%s(%s)  %s', $this->request->params['action'], $this->f_tid, $message), $type);
	}

	// 指定byte数で文字列を切る(SJIS基準)
	private function limitStr($str, $length) {
		$tmp = mb_convert_encoding($str, 'cp932', 'utf-8');
		return mb_convert_encoding(mb_strcut($tmp, 0, $length, 'cp932'), 'utf-8', 'cp932');
	}

	// 予約リスト作成
	private function makeYoyakuList($data) {
		$equipmentList = $this->Equipment->getEquipmentListWithoutCondition();
		$privilegeList = $this->Privilege->getClientPrivilegeList($data[0]['Reservation']['client_id']);

		$yoyakus = array();

		$i = 1;
		foreach ($data as $r) {
			$reserveDatetime = date('Y/m/d H:i', strtotime($r['Reservation']['created']));
			$cancelDatetime = ($r['Reservation']['reservation_status_id'] == Constant::STATUS_CANCEL && $r['Reservation']['rennavi_status'] != Constant::RENNAVI_STATUS_CXL_RESERVE_FIXING) ?
								date('Y/m/d H:i', strtotime($r['Reservation']['cancel_datetime'])) : '';

			$applicantName = $this->limitStr($r['Reservation']['last_name'].$r['Reservation']['first_name'], 160);
			$passengerCount = $r['Reservation']['adults_count'] + $r['Reservation']['children_count'] + $r['Reservation']['infants_count'];

			$yoyakus['REPLACE'.$i] = array(
				// 必須でない、設定しない項目もキーは用意
				'status'						 => $this->limitStr(Constant::rennaviStatusNames()[$r['Reservation']['rennavi_status']], 100),
				'r_cd_yoyaku'					 => $r['Reservation']['reservation_key'],
				'nb_cd_yoyaku'					 => '',
				'package_cd_yoyaku'				 => '',
				'dt_uketuke'					 => $reserveDatetime,
				'dt_yoyaku_kakunin'				 => $reserveDatetime,
				'dt_cancel_uketuke'				 => $cancelDatetime,
				'dt_cancel'						 => $cancelDatetime,
				'yoyakusya_shimei'				 => $applicantName,
				'yoyakusya_shimei_kana'			 => $applicantName,
				'riyousya_shimei'				 => $applicantName,
				'riyousya_shimei_kana'			 => $applicantName,
				'riyousya_tel'					 => $r['Reservation']['tel'],
				'kinkyu_tel'					 => $r['Reservation']['tel'],
				'su_josya'						 => $passengerCount,
				'su_kodomo'						 => $passengerCount - $r['Reservation']['adults_count'],
				'no_flight'						 => $this->limitStr($r['Reservation']['arrival_flight_number'], 10),
				'hotel_name'					 => '',
				'hotel_zyusyo'					 => '',
				'hotel_tel'						 => '',
				'ry_cd_yoyaku'					 => '',
				'nc_haisya_sougei'				 => $this->deliveryMethod($r['RentOfficeSupplement']),
				'dt_kasiwatasi'					 => date('Y/m/d H:i', strtotime($r['Reservation']['rent_datetime'])),
				'cd_branch_kasiwatasi'			 => $r['RentOffice']['id'],
				'n_branch_kasiwatasi'			 => $this->limitStr($r['RentOffice']['name'], 40),
				'dt_henkyaku'					 => date('Y/m/d H:i', strtotime($r['Reservation']['return_datetime'])),
				'cd_branch_henkyaku'			 => $r['ReturnOffice']['id'],
				'n_branch_henkyaku'				 => $this->limitStr($r['ReturnOffice']['name'], 40),
				'cd_enterprise_car'				 => $r['CarClass']['id'],
				'cd_syosai_car'					 => $r['CarClass']['id'],
				'n_syosai_car'					 => $this->limitStr($r['CarClass']['name'], 40),
				'nc_at_mt'						 => $r['Commodity']['transmission_flg'] ? 'MT' : 'AT',
				'campaign_title'				 => $this->limitStr($r['Commodity']['name'], 80),
				'n_car_zokusei'					 => $this->limitStr($this->carInfo($r), 60),
				'kin_kihon'						 => $r['ReservationDetail'][Constant::DETAIL_TYPE_BASICPRICE]['amount'],
				'kin_norisute'					 => isset($r['ReservationDetail'][Constant::DETAIL_TYPE_DROPOFFPRICE]) ? $r['ReservationDetail'][Constant::DETAIL_TYPE_DROPOFFPRICE]['amount'] : 0,
				'kin_futai_service_syokei'		 => $r['ReservationDetail'][Constant::DETAIL_TYPE_DISCLAIMER]['amount'] +
													(isset($r['ReservationDetail'][Constant::DETAIL_TYPE_OPTIONPRICE]) ? $r['ReservationDetail'][Constant::DETAIL_TYPE_OPTIONPRICE]['amount'] : 0) +
													(isset($r['ReservationDetail'][Constant::DETAIL_TYPE_CHILDSHEET]) ? $r['ReservationDetail'][Constant::DETAIL_TYPE_CHILDSHEET]['amount'] : 0),
				'kin_kasiwatasi_warimasi'		 => isset($r['ReservationDetail'][Constant::DETAIL_TYPE_NIGHTFEE]) ? $r['ReservationDetail'][Constant::DETAIL_TYPE_NIGHTFEE]['amount'] : 0,
				'kin_henkyaku_sinya_warimasi'	 => 0,
				'kin_goukei'					 => $r['Reservation']['amount'],
				'riyou_point'					 => 0,
				'riyou_coupon'					 => 0,
				'sashihiki'						 => $r['Reservation']['amount'],
				'kin_kazei'						 => $r['Reservation']['amount'],
				'kin_hikazei'					 => 0,
				'kin_cancel'					 => 0,
				'bikou'							 => $this->limitStr($this->remarks($r['Reservation']), 500),
				'futai_service'					 => $this->options($r, $equipmentList, $privilegeList),
				'r_henkouriyu'					 => '',
				'w_user_agent'					 => '',
				'mobile_model'					 => '',
				'nc_kessai'						 => !empty($r['Reservation']['payment_status']) ? 'オンラインカード決済' : '現地決済',
				'nc_siharai_method'				 => $this->paymentMethod($r),
				'su_point'						 => 0,
				'su_point_coupon'				 => 0,
			);

			$i++;
		}

		return $yoyakus;
	}

	// 貸渡方法 (予約リスト)
	private function deliveryMethod($officeSupplement) {
		if (!is_null($officeSupplement['method_of_transport'])) {
			if ($officeSupplement['method_of_transport'] == 1 || $officeSupplement['method_of_transport'] == 2) {
				switch ($officeSupplement['pickup_method']) {
					case 0:
						return '最寄り交通機関への送迎';
						break;
					case 1:
						return '最寄り交通機関カウンター';
						break;
					case 2:
						return '最寄り交通機関への配車';
						break;
					default:
						break;
				}
			}
		}
		return '来店';
	}

	// 車両属性 (予約リスト)
	private function carInfo($data) {
		$carInfo = array();
		if (!empty($data['CarModel']['name'])) {
			$carInfo[] = '車種指定:'.$data['CarModel']['name'];
		}
		if (!empty($data['Commodity']['new_car_registration'])) {
			$carInfo[] = '新車登録'.$data['Commodity']['new_car_registration'].'年以内';
		}
		return implode(',', $carInfo);
	}

	// 備考 (予約リスト)
	private function remarks($reservation) {
		$remarks = array();
		if (!empty($reservation['arrival_flight_number'])) {
			$remarks[] = '到着便:'.$reservation['arrival_flight_number'];
		}
		if (!empty($reservation['departure_flight_number'])) {
			$remarks[] = '出発便:'.$reservation['departure_flight_number'];
		}
		return implode(' ', $remarks);
	}

	// 付帯サービス (予約リスト)
	private function options($data, $equipmentList, $privilegeList) {
		$options = array($this->option('免責補償', $data['ReservationDetail'][Constant::DETAIL_TYPE_DISCLAIMER]['amount'], 1));
		foreach ((array)$data['CommodityEquipment'] as $equipmentId) {
			$options[] = $this->option($equipmentList[$equipmentId], 0, 1);
		}
		foreach ((array)$data['ReservationPrivilege'] as $r) {
			$options[] = $this->option($privilegeList[$r['privilege_id']], $r['price'] / $r['count'], $r['count']);
		}
		foreach ((array)$data['ReservationChildSheet'] as $r) {
			$options[] = $this->option($privilegeList[$r['child_sheet_id']], $r['price'] / $r['count'], $r['count']);
		}
		return implode(',', $options);
	}
	private function option($name, $price, $count) {
		return sprintf('%s:%dx%d', $name, $price, $count);
	}

	// 支払い方法 (予約リスト)
	private function paymentMethod($data) {
		if (!empty($data['Reservation']['payment_status'])) {
			return 'オンラインカード決済';
		}
		if ($data['Client']['accept_cash'] && $data['Client']['accept_card']) {
			return '現金/カード払い可能';
		} elseif ($data['Client']['accept_cash']) {
			return '現金払いのみ';
		} elseif ($data['Client']['accept_card']) {
			return 'カード払いのみ';
		}
		return '';
	}

	// yyyymmddの日付文字列を指定されたフォーマットに整形して返す
	private function getFotmatDate($str, $formate = 'Y-m-d') {
		$yyyy = substr($str, 0, 4);
		$mm = substr($str, 4, 2);
		$dd = substr($str, 6, 2);
		return date($formate, strtotime("{$yyyy}/{$mm}/{$dd} 00:00:00"));
	}

	// Ymd形式の日付文字列であるか確認
	private function isDateFormatYmd($str) {
		if (!preg_match('/^[0-9]{4}[0-9]{2}[0-9]{2}$/', $str)) {
			return false;
		}
		$date = $this->getFotmatDate($str, "Ymd");
		return ($str == $date);
	}

	// 過去の日付でないことを確認
	private function isNotPastDate($date) {
		$today = date("Ymd");
		return ($date >= $today);
	}

	// 指定可能期間か確認（１ヶ月程度：31日）
	private function isSpecifiablePeriod($start, $end) {
		$startDate = new DateTime($this->getFotmatDate($start));
		$endDate = new DateTime($this->getFotmatDate($end));
		$diff = $endDate->diff($startDate);	// 差分
		return ($diff->format('%a') < 31);
	}

	// 手仕舞い対象データの取得
	private function getTargetCarClassStocks($stockGroupId, $carClassId, $fromDate, $toDate, $tejimaiFlag)
	{
		$result = $this->CarClassStock->getCarClassStockDateRange($stockGroupId, $carClassId, $fromDate, $toDate);
		if (count($result) == 0) {
			return [];
		}
		return $result;
	}

	// 手仕舞い対象データのうち、更新対象のみ取得
	private function getUpdateTargetCarClassStocks($carClassStocks, $tejimaiFlag)
	{
		$targetSuspention = ($tejimaiFlag) ? 0 : 1;	//$tejimaiFlag=1は販売停止、0は販売停止解除

		// 販売停止する場合は、suspensionが０のみ、販売再開はsuspensionが1のみ
		// 既に変更後の値と同じものは取得しない
		return Hash::extract($carClassStocks, '{n}.CarClassStock[suspension=' . $targetSuspention .']');
	}

	// 手仕舞いAPIの返却値を返却
	private function makeTejimaiResponse($data, $carClassId, $officeId, $tejimaiFlag) {

		$carClass = $this->CarClass->find('first', array('conditions' => array('CarClass.id' => $carClassId)));
		$office = $this->Office->find('first', array('conditions' => array('Office.id' => $officeId)));

		// 固定項目の生成
		// 仕様書によると使用しない項目のため、適当な値でいいとのこと。サンプルの値(2byte）を入れておく
		$total	 = array();
		$yoyaku	 = array();
		$noUse	 = array();
		for ($i = 0; $i <= 23; $i++) {
			$number = sprintf('%02d', $i);
			$total["total_{$number}"]	 = 10;		// $idx時台総数
			$yoyaku["yoyaku_{$number}"]	 = 10;		// $idx時台予約数
			$noUse["no_use_{$number}"]	 = 10;		// $idx時台在庫数
		}

		foreach ($data as $carClassStock) {
			$stockDate = str_replace('-', '', $carClassStock['CarClassStock']['stock_date']);
			$tmp = array(
				'branch_grp_id'			 => '0',							// 営業所グループid
				'branch_grp_name'		 => '',								// 営業所グループ名
				'kyoyu_zaiko_grp_id'	 => '0',							// 共有在庫グループid
				'kyoyu_zaiko_grp_name'	 => '',								// 共有在庫グループ名
				'branch_id'				 => $office['Office']['id'],		// 営業所id
				'branch_name'			 => $office['Office']['name'],		// 営業所名
				'enterprise_car_id'		 => $carClass['CarClass']['id'],	// 事業者車両クラスid
				'enterprise_car_name'	 => $carClass['CarClass']['name'],	// 事業者車両クラス名
				'syousai_car_id'		 => $carClass['CarClass']['id'],	// 詳細車両クラスid
				'syousai_car_name'		 => $carClass['CarClass']['name'],	// 詳細車両クラス名
				'nengappi'				 => $stockDate,						// 年月日(yyyymmdd)
			);
			$tmp2 = array(
				'tejimai'	 => $tejimaiFlag	// 手仕舞いフラグ 0：OFF、1：ON
			);

			$zaiko = array_merge($tmp, $total, $yoyaku, $noUse, $tmp2);
			$zaikos[] = $zaiko;
		}
		return $zaikos;
	}
}

<?php
App::import('Vendor','TCPDF/tcpdf');
App::import('Vendor','TCPDF/fpdi/autoload');

use setasign\Fpdi\Tcpdf\Fpdi;

class ReceiptComponent extends Component{

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 * 詳細取得
	 * @param string $receiptId
	 * @return array $receipt
	 */
	public function getDetail($receiptId)
	{
		$CmThReceipt = ClassRegistry::init('CmThReceipt');
		$receipt = $CmThReceipt->find('first', [
			'conditions' => ['CmThReceipt.receipt_id' => $receiptId],
			'recursive' => -1
		]);

		if (empty($receipt)) {
			return [];
		}

		// 郵便番号がint型で保存されているため、所定の桁数に0埋めもしくは空文字に変換
		if ($receipt['CmThReceipt']['send_zip_code1'] == 0) {
			$receipt['CmThReceipt']['send_zip_code1'] = '';
		} else {
			$receipt['CmThReceipt']['send_zip_code1'] = sprintf('%03d', $receipt['CmThReceipt']['send_zip_code1']);;
		}
		if ($receipt['CmThReceipt']['send_zip_code2'] == 0) {
			$receipt['CmThReceipt']['send_zip_code2'] = '';
		} else {
			$receipt['CmThReceipt']['send_zip_code2'] = sprintf('%04d', $receipt['CmThReceipt']['send_zip_code2']);;
		}

		return $receipt['CmThReceipt'];
	}

	/**
	 * 領収書の発行可能額を取得する
	 * @param int $reservationId
	 * @param int $currentReceiptId
	 */
	public function getIssuableAmount($reservationId, $currentReceiptId = 0)
	{
		if (empty($reservationId)) {
			return 0;
		}

		$Reservation = ClassRegistry::init('Reservation');
		$reservation = $Reservation->find('first', [
			'conditions' => ['Reservation.id' => $reservationId],
			'recursive' => -1
		]);
		if (empty($reservation['Reservation']['payment_status'])) {// 未入金
			return 0;
		}

		// 予約の合計金額を算出（事務手数料は含まれる）
		$issuableAmount = $reservation['Reservation']['amount'];
		// キャンセル料を取得し、加算する
		$CancelDetail = ClassRegistry::init('CancelDetail');
		$cancelDetails = $CancelDetail->find('all', [
			'conditions' => ['CancelDetail.reservation_id' => $reservationId],
			'recursive' => -1
		]);
		foreach ($cancelDetails as $c) {
			$issuableAmount += ($c['CancelDetail']['amount'] * $c['CancelDetail']['count']);
		}

		// 発行済みの領収書を取得
		$CmThApplicationDetail = ClassRegistry::init('CmThApplicationDetail');
		$cmApplicationId = $CmThApplicationDetail->getCmApplicationIdByReservationId($reservationId);
		$receiptConditions = [
			'conditions' => ['CmThReceipt.cm_application_id' => $cmApplicationId, 'CmThReceipt.delete_flg' => 0],
			'recursive' => -1
		];
		// 現在編集中の領収書は除く
		if (!empty($currentReceiptId)) {
			$receiptConditions['conditions']['CmThReceipt.receipt_id <>'] = $currentReceiptId;
		}

		$CmThReceipt = ClassRegistry::init('CmThReceipt');
		$issuedReceipts = $CmThReceipt->find('all', $receiptConditions);
		// 発行済みの領収書額を減算する
		foreach ($issuedReceipts as $issuedReceipt) {
			$issuableAmount -= $issuedReceipt['CmThReceipt']['receipt_price'];
		}

		// 算出結果を返却（マイナスになった場合、0を返却）
		return max($issuableAmount, 0);
	}

	/**
	 * 保存
	 * @param array $param
	 */
	public function save($param)
	{
		//// 入力値チェック
		// 必須のIDが無い場合、システム的におかしいので例外をスロー
		if (empty($param['cm_application_id']) || empty($param['reservation_id'])) {
			throw new \Exception();
		}

		// 通常の入力項目
		$errorMessage = [];
		if (empty($param['receipt_name_enc'])) {
			$errorMessage[] = '領収書宛名を入力してください。';
		}
		if (empty($param['receipt_title'])) {
			$errorMessage[] = '但し書きを入力してください。';
		}
		if (empty($param['receipt_price'])) {
			$errorMessage[] = '金額を入力してください。';
		} else if (!preg_match("/^[0-9]*$/", $param['receipt_price'])) {
			$errorMessage[] = '金額は半角数字で入力してください。';
		}

		// 郵送の場合のみ入力される項目
		if (isset($param['mail_flg']) && $param['mail_flg']== '1') {
			if (empty($param['send_zip_code1']) || empty($param['send_zip_code2'])) {
				$errorMessage[] = '郵送の場合、郵便番号を入力してください。';
			} else if (!preg_match("/^[0-9]{3}$/", $param['send_zip_code1']) || !preg_match("/^[0-9]{4}$/", $param['send_zip_code2'])) {
				$errorMessage[] = '郵便番号は正しい桁数の半角数字で入力してください。';
			}
			if (empty($param['send_address1_enc'])) {
				$errorMessage[] = '郵送の場合、都道府県を選択してください。';
			}
			if (empty($param['send_address2_enc'])) {
				$errorMessage[] = '郵送の場合、住所を入力してください。';
			}
			if (empty($param['send_name_enc'])) {
				$errorMessage[] = '郵送の場合、送付先宛名を入力してください。';
			}
		}

		// 金額チェック
		if (empty($param['ignore_flg'])) {
			// 上限無視フラグがOFFの場合、発行可能額を取得して金額チェックを実施する
			$issuableAmount = $this->getIssuableAmount($param['reservation_id'], $param['receipt_id']);
			if ($issuableAmount == 0) {
				$errorMessage[] = '発行可能額が0円のため、領収書を発行できません。';
			} else if ($param['receipt_price'] > $issuableAmount) {
				$errorMessage[] = '金額は'.$issuableAmount.'円以内で入力してください。';
			}
		}

		// エラーがある場合、処理を中断してエラーメッセージを返却
		if (!empty($errorMessage)) {
			return ['ret' => 'ng', 'error' => $errorMessage];
		}

		//// 保存処理
		// 保存データ生成
		$saveData = [];
		if (empty($param['receipt_id'])) {
			$saveData['create_dt'] = date('Y-m-d H:i:s');
		} else {
			$saveData['receipt_id'] = $param['receipt_id'];
		}
		$saveData['update_dt'] = date('Y-m-d H:i:s');
		$saveData['cm_application_id'] = $param['cm_application_id'];
		$saveData['receipt_name_enc'] = $param['receipt_name_enc'];
		$saveData['receipt_title'] = $param['receipt_title'];
		$saveData['receipt_price'] = $param['receipt_price'];
		$saveData['other_receipt_price'] = $param['receipt_price'];
		$saveData['mail_flg'] = !isset($param['mail_flg']) ? '0' : $param['mail_flg'];
		$saveData['receipt_status'] = !isset($param['receipt_status']) ? '0' : $param['receipt_status'];
		$saveData['lang_id'] = !isset($param['lang_id']) ? '1' : $param['lang_id'];
		$saveData['ignore_flg'] = !isset($param['ignore_flg']) ? '0' : $param['ignore_flg'];
		$saveData['reissue_flg'] = !isset($param['reissue_flg']) ? '0' : $param['reissue_flg'];
		$saveData['delete_flg'] = !isset($param['delete_flg']) ? '0' : $param['delete_flg'];
		$saveData['receipt_payment_type'] = ['1' => 'クレジットカード', '2' => 'Credit Card'][$saveData['lang_id']];
		if (isset($param['mail_flg']) && $param['mail_flg'] == '1') {
			$saveData['send_zip_code1'] = $param['send_zip_code1'];
			$saveData['send_zip_code2'] = $param['send_zip_code2'];
			$saveData['send_address1_enc'] = $param['send_address1_enc'];
			$saveData['send_address2_enc'] = $param['send_address2_enc'];
			$saveData['send_address3_enc'] = $param['send_address3_enc'];
			$saveData['send_name_enc'] = $param['send_name_enc'];
		}

		$messageData = [];
		$messageData['reservation_id'] = $param['reservation_id'];
		$messageData['category_cd'] = 'PAYMENT_DETAIL';
		$messageData['staff_id'] = $param['staff_id'];

		// データ登録・更新実行
		$CmThReceipt = ClassRegistry::init('CmThReceipt');
		$MessageBoard = ClassRegistry::init('MessageBoard');
		$CmThReceipt->begin();
		$MessageBoard->begin();
		try {
			$CmThReceipt->save($saveData);
			if (empty($param['receipt_id'])) {
				$messageData['message'] = '領収書作成（領収書番号：' . $CmThReceipt->id . '）';
			} else {
				$messageData['message'] = '領収書編集（領収書番号：' . $param['receipt_id'] . '）';
			}
			$MessageBoard->save($messageData);

			$CmThReceipt->commit();
			$MessageBoard->commit();
		} catch (PDOException $e) {
			$this->log($e->getMessage(), LOG_ERROR);
			$this->log($e->getTraceAsString(), LOG_ERROR);
			$CmThReceipt->rollback();
			$MessageBoard->rollback();
			return ['ret' => 'ng', 'error' => 'db save error'];
		}

		$savedReceiptId = !isset($CmThReceipt->id) ? $param['receipt_id'] : $CmThReceipt->id;
		return ['ret' => 'ok', 'receipt_id' => $savedReceiptId];
	}

	/**
	 * ダウンロード
	 * @param array $receiptDetail
	 * @param bool $previewFlg
	 */
	public function download($receiptDetail, $previewFlg = false)
	{
		// 入金日を取得
		$paymentDate = $this->getPaymentDate($receiptDetail['cm_application_id']);
		if (!empty($paymentDate)) {
			$paymentDate = date('Y/m/d', strtotime($paymentDate));
		}

		// 領収書IDが存在し（＝新規発行ではない）、かつプレビューでもない場合、ダウンロード日時を更新する
		if (!empty($receiptDetail['receipt_id']) && !$previewFlg) {
			$CmThReceipt = ClassRegistry::init('CmThReceipt');
			$receipt = $CmThReceipt->find('first', [
				'conditions' => ['CmThReceipt.receipt_id' => $receiptDetail['receipt_id']],
				'recursive' => -1
			]);
			$receipt['CmThReceipt']['receipt_dt'] = date('Y-m-d H:i:s');
			$receipt['CmThReceipt']['update_dt'] = date('Y-m-d H:i:s');
			$CmThReceipt->id = $receiptDetail['receipt_id'];
			$CmThReceipt->save($receipt);
		}

		// PDFテンプレートを読み込んで領収書の空ページを生成
		$pdf = new Fpdi();
		$pdf->setPrintHeader(false);
		$pdf->AddPage();
		$pdf->setSourceFile($this->getReceiptTemplateFilePath($receiptDetail, $previewFlg));
		$pdf->useTemplate($pdf->importPage(1));

		// 各項目を、座標とフォントを指定して描画していく
		if (!isset($receiptDetail['lang_id']) || $receiptDetail['lang_id'] == '1') {
			//// 日本語版
			// 領収書宛先（右寄せ）
			$pdf->setFont('kozminproregular', '', 10);
			$pdf->setY(32.5);
			$pdf->setRightMargin(120);
			$pdf->Write(0, $receiptDetail['receipt_name_enc'], '', '', 'R');

			// 領収書金額（右寄せ）
			$pdf->setFont('kozgopromedium', '', 24);
			$pdf->setY(45);
			$pdf->Write(0, '￥'.number_format($receiptDetail['receipt_price']).' -', '', '', 'R');

			// 領収書番号
			$pdf->setFont('kozminproregular', '', 12);
			$pdf->Text(150, 33, $receiptDetail['receipt_id']);
			// 但し書き
			$pdf->setFont('kozgopromedium', '', 8);
			$pdf->Text(36, 57, $receiptDetail['receipt_title']);
			// 領収日
			$pdf->setFont('kozminproregular', '', 12);
			$pdf->Text(30, 64.5, $paymentDate);
			// 支払方法
			$pdf->setFont('kozminproregular', '', 10);
			$pdf->Text(36, 79, $receiptDetail['receipt_payment_type']);
			// 発行日
			$pdf->setFont('kozminproregular', '', 12);
			$pdf->Text(30, 71.5, date('Y/m/d', strtotime($receiptDetail['create_dt'])));
			// URL
			$pdf->setFont('kozminproregular', '', 9);
			$pdf->Text(108, 76, 'https://skyticket.jp/rentacar/');
		} else {
			//// 英語版
			// 領収書宛先
			$pdf->setFont('kozgopromedium', '', 10);
			$pdf->Text(45, 28, $receiptDetail['receipt_name_enc']);
			// 領収書金額
			$pdf->setFont('kozgopromedium', '', 14);
			$pdf->Text(50, 47, '￥'.number_format($receiptDetail['receipt_price']).' -');
			// 領収書番号
			$pdf->setFont('kozminproregular', '', 12);
			$pdf->Text(150, 28, $receiptDetail['receipt_id']);
			// 但し書き
			$pdf->setFont('kozgopromedium', '', 8);
			$pdf->Text(45, 39, $receiptDetail['receipt_title']);
			// 領収日
			$pdf->setFont('kozminproregular', '', 12);
			$pdf->Text(46, 58.5, $paymentDate);
			// 支払方法
			$pdf->setFont('kozminproregular', '', 11);
			$pdf->Text(46, 76.5, $receiptDetail['receipt_payment_type']);
			// 発行日
			$pdf->setFont('kozminproregular', '', 12);
			$pdf->Text(46, 65.5, date('Y/m/d', strtotime($receiptDetail['create_dt'])));
			// URL
			$pdf->setFont('kozminproregular', '', 9);
			$pdf->Text(99, 76, 'https://skyticket.jp/rentacar/');
		}

		// 出力タイプを設定（基本はダウンロード、プレビュー指定の場合のみブラウザ表示）
		$outputType = 'D';
		if ($previewFlg) {
			$outputType = 'I';
		}

		//出力
		$pdf->Output('receipt.pdf', $outputType);
		exit;
	}

	/**
	 * 領収書のテンプレートファイルのパスを取得する
	 * @param array $receiptDetail
	 * @param bool $previewFlg
	 */
	private function getReceiptTemplateFilePath($receiptDetail, $previewFlg)
	{
		$basePath = ROOT.DS.WEBROOT_DIR.DS.'admin'.DS.'files'.DS.'templates'.DS.'receipt'.DS;
		if (!isset($receiptDetail['lang_id']) || $receiptDetail['lang_id'] == '1') {
			// 日本語版
			if ($previewFlg) {
				return $basePath.'receipt_japanese_sample.pdf'; // プレビュー表示
			}
			if (!empty($receiptDetail['reissue_flg'])) {
				return $basePath.'receipt_japanese_re.pdf'; // 再発行
			}
			return $basePath.'receipt_japanese.pdf'; // 通常
		}
		// 英語版
		if ($previewFlg) {
			return $basePath.'receipt_english_sample.pdf'; // プレビュー表示
		}
		if (!empty($receiptDetail['reissue_flg'])) {
			return $basePath.'receipt_english_re.pdf'; // 再発行
		}
		return $basePath.'receipt_english.pdf'; // 通常
	}

	/**
	 * 予約の入金日を取得する
	 * 未入金、もしくは何らかの原因で取得できない場合は空文字を返却
	 * @param int $cmApplicationId
	 */
	private function getPaymentDate($cmApplicationId)
	{
		$maxPayDate = '';
		if ($this->controller->PaymentAPI->getPaymentFlag($cmApplicationId)) {
			// カートID取得API
			$targetUrl = $this->controller->PaymentAPI->getApiUrlByApplicationId($cmApplicationId);
			$res = $this->controller->PaymentAPI->runApi($targetUrl, 'get');
			$body = json_decode($res->body, true);
			if (empty($body)) {
				$this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
				$this->log("failed result:".print_r($res, true), LOG_ERROR);
				// 新カート基盤で取れなかった場合、旧カート基盤も見る
				$targetUrl = $this->controller->PaymentAPI->getApiUrlOldByApplicationId($this->cmApplicationId);
				$res = $this->controller->PaymentAPI->runApi($targetUrl, 'get');
				if ($res->code === '404') {
					$this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
					$this->log("failed result:".print_r($res, true), LOG_ERROR);
				} else {
					$cartId = json_decode($res->body, true)['cart_id'];
				}
			} else {
				$cartId = $body[0]['cartId'];
			}
			if (isset($cartId)) {
				// 入金情報取得API
				$targetUrl = $this->controller->PaymentAPI->getApiUrlPayments();
				$res = $this->controller->PaymentAPI->runApi($targetUrl, 'get', 'cartId='. $cartId . '&serviceCd=rc');

				if ($res->code !== '200') {
					$this->log("failed URL:".print_r($targetUrl, true), LOG_ERROR);
					$this->log("failed result:".print_r($res, true), LOG_ERROR);
				} else {
					$res = json_decode($res->body, true);

					foreach ($res['detail'] as $detail) {
						if ($detail['paymentDt'] > $maxPayDate) {
							$maxPayDate = $detail['paymentDt'];
						}
					}
				}
			}
		} else {
			$Payment = ClassRegistry::init('Payment');
			$cond = $Payment->makePRConditions(['cm_application_id' => $cmApplicationId, 'payment_result' => 'success']);
			$payments = $Payment->find('all', $cond);

			foreach ($payments as $p) {
				$ret = $this->controller->PaymentEcon->inquiry($p['Payment']['order_id']);
				if (isset($ret['data']['amount']) && ($ret['data']['status'] != 0)) {
					if ($ret['data']['payDate'] > $maxPayDate) {
						$maxPayDate = $ret['data']['payDate'];
					}
				}
			}
		}

		return $maxPayDate;
	}
}

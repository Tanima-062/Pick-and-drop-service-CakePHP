<?php
/*
 * コントローラー側で各モデル(Reservation,CancelFee,CancelDetail)を読み込むこと
 */

class CancelFeeCalculationComponent extends Component{

	private $save = true;
	private $sum = 0;

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function calculate($reservation_id, $save=true) {
		$this->save = $save;

		$reservationData = $this->controller->Reservation->findById($reservation_id, null, null, -1);

		if ($this->save && $reservationData['Reservation']['cancel_datetime'] == '0000-00-00 00:00:00') { // エラー
			return ['id' => 0, 'sum' => $this->sum];
		}

		// キャンセル理由が「お客様都合によるキャンセル」、「交通手段の欠航・運休によるキャンセル」以外だった場合、自動ではやらない
		if ($this->save && $reservationData['Reservation']['cancel_reason_id'] != 1 && $reservationData['Reservation']['cancel_reason_id'] != 2) {
			return ['id' => 0, 'sum' => $this->sum];
		}

		// キャンセル理由が「交通手段の欠航・運休によるキャンセル」だった場合、決済手数料だけかかる
		if ($this->save && $reservationData['Reservation']['cancel_reason_id'] == 2) {
			$this->__saveCancelDetail($reservationData['Reservation']['id'], 'ADMINISTRATIVE_FEE', $reservationData['Reservation']['administrative_fee'], 1);
			return ['id' => 0, 'sum' => $this->sum];
		}

		$applyCancelFee = $this->__searchApplyRecord($reservationData);
		if (!$applyCancelFee) {
			return ['id' => 0, 'sum' => $this->sum];
		}

		$applyFee = 0;
		if ($applyCancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_FIXED_AMOUNT') { // 計上単位
			$applyFee = $applyCancelFee['CancelFee']['cancel_fee'];
		}
		else {
			if ($applyCancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_FIXED_RATE') {
				$tmpFee = (float)$reservationData['Reservation']['amount'] * ((float)$applyCancelFee['CancelFee']['cancel_fee'] / 100); // 計上単位
			}
			elseif ($applyCancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_BASIC_RATE') {
				$reservationDetail = $this->controller->ReservationDetail->find('first', [
					'conditions' => [
						'reservation_id' => $reservationData['Reservation']['id'],
						'detail_type_id' => 1
					]
				]);
				$tmpFee = (float)$reservationDetail['ReservationDetail']['amount'] * ((float)$applyCancelFee['CancelFee']['cancel_fee'] / 100); // 計上単位
			}

			if ($applyCancelFee['CancelFee']['fraction_round'] == 'TRUNCATE') { // 切り捨て
				$tmpFee = floor($tmpFee/$applyCancelFee['CancelFee']['fraction_unit']) * $applyCancelFee['CancelFee']['fraction_unit'];
			}
			elseif ($applyCancelFee['CancelFee']['fraction_round'] == 'ROUNDUP') { // 切り上げ
				$tmpFee = ceil($tmpFee/$applyCancelFee['CancelFee']['fraction_unit']) * $applyCancelFee['CancelFee']['fraction_unit'];
			}
			elseif ($applyCancelFee['CancelFee']['fraction_round'] == 'ROUNDINGOFF') { // 四捨五入
				$fraction_unit_len = strlen($applyCancelFee['CancelFee']['fraction_unit']);
				$tmpFee = round($tmpFee, -1 * ($fraction_unit_len - 1));
			}

			if (!empty($applyCancelFee['CancelFee']['cancel_fee_min']) && $tmpFee < $applyCancelFee['CancelFee']['cancel_fee_min']) { // 最低額を下回っていた場合は最低額を使う
				$applyFee = $applyCancelFee['CancelFee']['cancel_fee_min'];
			}
			elseif (!empty($applyCancelFee['CancelFee']['cancel_fee_max']) && $applyCancelFee['CancelFee']['cancel_fee_max'] < $tmpFee) { // 最高額を上回っていた場合は最高額を使う
				$applyFee = $applyCancelFee['CancelFee']['cancel_fee_max'];
			}
			else {
				$applyFee = $tmpFee;
			}
		}

		// キャンセル料を登録する
		$this->__saveData($applyCancelFee, $reservationData, $applyFee);

		return ['id' => $applyCancelFee['CancelFee']['id'], 'sum' => $this->sum];
	}

	/*
	 * 適用レコードを探す
	 */
	private function __searchApplyRecord($reservationData) {
		// キャンセル日
		$cancelDatetime = $reservationData['Reservation']['cancel_datetime'];
		if (!$this->save) {
			$cancelDatetime = date('Y-m-d H:i:s');
		}

		// 出発日
		$rentDatetime = $reservationData['Reservation']['rent_datetime']; // 貸出日を取得
		$rentDatetimeYmd = date('Y-m-d', strtotime($rentDatetime));

		$cancelFees = $this->controller->CancelFee->find('all', [
			'conditions' => [
				'CancelFee.client_id' => $reservationData['Reservation']['client_id'],
				'CancelFee.sales_type' => Constant::SALES_TYPE_ARRANGED,
				'CancelFee.delete_flg' => 0,
			]
		]);

		// 適用レコードを探す
		$apply_time = strtotime('9999-12-31 23:59:59'); // 初期化
		$applyCancelFee = null;
		foreach($cancelFees as $cancelFee) {
			if (!$cancelFee['CancelFee']['is_published']) { // 非公開だったら適用しない
				continue;
			}

			$apply_term_point = ($cancelFee['CancelFee']['apply_term_point']) ? $rentDatetime : $cancelDatetime;
			// 適用期間外だったらパス
			if (strtotime($apply_term_point) < strtotime($cancelFee['CancelFee']['apply_term_from']) ||
				strtotime($cancelFee['CancelFee']['apply_term_to']) < strtotime($apply_term_point)) {
				continue;
			}

			if (!$cancelFee['CancelFee']['is_after_departure'] && (strtotime($cancelDatetime) <= strtotime($rentDatetime))) { // 出発前
				if ($cancelFee['CancelFee']['cancel_limit_unit'] == 'DAY') { // 期限単位(日)
					$cancelFeeTime = strtotime('-'.$cancelFee['CancelFee']['cancel_limit'].' day', strtotime($rentDatetimeYmd.' 23:59:59'));
					if (strtotime($cancelDatetime) <= $cancelFeeTime && $cancelFeeTime <= $apply_time) {
						$apply_time = $cancelFeeTime;
						$applyCancelFee = $cancelFee;
					}
				}
				else { // 期限単位(時間)
					$cancelFeeTime = strtotime('-'.$cancelFee['CancelFee']['cancel_limit'].' hour', strtotime($rentDatetime));
					if (strtotime($cancelDatetime) <= $cancelFeeTime && $cancelFeeTime <= $apply_time) {
						$apply_time = $cancelFeeTime;
						$applyCancelFee = $cancelFee;
					}
				}
			}
			elseif($cancelFee['CancelFee']['is_after_departure'] && strtotime($rentDatetime) < strtotime($cancelDatetime)){ // 出発後
				$applyCancelFee = $cancelFee;
			}
		}

		return $applyCancelFee;
	}

	/*
	 * キャンセル料を登録する
	 */
	private function __saveData($applyCancelFee, $reservationData, $applyFee) {
		if ($applyCancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_FIXED_AMOUNT' || $applyCancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_FIXED_RATE') { // 予約あたりの場合
			$this->__saveCancelDetail($reservationData['Reservation']['id'], 'RESERVE_FIXED_AMOUNT', $applyFee, 1, '適用ID:'.$applyCancelFee['CancelFee']['id']);
		}
		elseif ($applyCancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_BASIC_RATE') { // 基本料金に対して
			$this->__saveCancelDetail($reservationData['Reservation']['id'], 'RESERVE_BASIC_AMOUNT', $applyFee, 1, '適用ID:'.$applyCancelFee['CancelFee']['id']);
		}

		// INCIDENT-3044 取消手続料の徴収を廃止する
		/*if ($applyCancelFee['CancelFee']['adv_cancel_fee'] > 0) {
			// 取消手数料とキャンセル料の合計が搭乗料金を上待っていた場合、残り全額が取消手数料となる
			if ($reservationData['Reservation']['amount'] - $reservationData['Reservation']['administrative_fee'] < $this->sum + $applyCancelFee['CancelFee']['adv_cancel_fee']) {
				$applyCancelFee['CancelFee']['adv_cancel_fee'] = $reservationData['Reservation']['amount'] - $reservationData['Reservation']['administrative_fee'] - $this->sum;
			}

			$this->__saveCancelDetail($reservationData['Reservation']['id'], 'ADVENTURE_FEE', $applyCancelFee['CancelFee']['adv_cancel_fee'], 1, '適用ID:'.$applyCancelFee['CancelFee']['id']);
		}*/

		$this->__saveCancelDetail($reservationData['Reservation']['id'], 'ADMINISTRATIVE_FEE', $reservationData['Reservation']['administrative_fee'], 1);
	}

	/*
	 * キャンセル料明細に登録する
	 */
	private function __saveCancelDetail($reservation_id, $account_code, $amount, $count, $remarks='') {
		$this->sum += $amount * $count;

		if (!$this->save) {
			return;
		}

		$staff_id = (!empty($this->controller->cdata['id'])) ? $this->controller->cdata['id'] : 0;

		$this->controller->CancelDetail->create();
		$this->controller->CancelDetail->save(
			[
				'reservation_id' => $reservation_id,
				'account_code' => $account_code,
				'amount' => $amount,
				'count' => $count,
				'staff_id' => $staff_id,
				'remarks' => $remarks
			]
		);
	}
}
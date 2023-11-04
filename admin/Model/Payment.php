<?php
App::uses('AppModel', 'Model');
/**
 * Payment Model
 */
class Payment extends AppModel {
	const STAT_TEMPORARY = 99999; // 仮入金

	public $useDbConfig = 'common';
	public $useTable = 'cm_th_other_payment_econ_credit_log';

	public function test(){
		return 'test';
	}

	/*
	 * 入金情報(Payment)と予約情報(Reservation)を連結する条件を作成する
	 */
	public function makePRConditions($params) {
		$settings = [
			'fields' => [
				'Payment.*',
				'Reservation.*',
				'ReservationStatus.*',
				'Client.name',
				'CancelReason.id',
				'CancelReason.reason'
			],
			'joins' => [
				[
					'type' => 'INNER',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'CmThApplicationDetail',
					'conditions' => 'Payment.cm_application_id = CmThApplicationDetail.cm_application_id',
				],
				[
					'type' => 'LEFT',
					'table' => 'rentacar.reservations',
					'alias' => 'Reservation',
					'conditions' => 'Reservation.id = CmThApplicationDetail.application_id',
				],
				[
					'type' => 'LEFT',
					'table' => 'rentacar.clients',
					'alias' => 'Client',
					'conditions' => 'Client.id = Reservation.client_id',
				],
				[
					'type' => 'LEFT',
					'table' => 'rentacar.cancel_reasons',
					'alias' => 'CancelReason',
					'conditions' => 'CancelReason.id = Reservation.cancel_reason_id',
				],
				[
					'type' => 'LEFT',
					'table' => 'rentacar.reservation_statuses',
					'alias' => 'ReservationStatus',
					'conditions' => 'ReservationStatus.id = Reservation.reservation_status_id',
				],
			],
			'conditions' => [
				'CmThApplicationDetail.service_cd' => 'rc',
			],
			'order' => [
				'Payment.other_payment_econ_credit_log_id' => 'desc',
			]
		];

		if (isset($params['limit']) && $params['limit'] > 0) {
			$settings['limit'] = $params['limit'];
		}

		// 検索ボタンが押されたら一覧表示に条件追加
		if (isset($params['cm_application_id']) && $params['cm_application_id'] != '') { // skyticket申込番号
			$settings['conditions']['Payment.cm_application_id'] = $params['cm_application_id'];
		}

		if (isset($params['order_id']) && $params['order_id'] != '') { // econ注文番号
			$settings['conditions']['Payment.order_id'] = $params['order_id'];
		}

		if (isset($params['create_dt_start']) && $params['create_dt_start'] != '') { // 決済開始日(start)
			$settings['conditions']['Payment.create_dt >= '] = $params['create_dt_start'].' 00:00:00';
		}

		if (isset($params['create_dt_end']) && $params['create_dt_end'] != '') { // 決済開始日(end)
			$settings['conditions']['Payment.create_dt <= '] = $params['create_dt_end'].' 23:59:59';
		}

		if (isset($params['payment_result']) && $params['payment_result'] != '') { // 決済処理結果
			switch($params['payment_result']) {
				case 'success':
					$settings['conditions']['Payment.status'] = 1;
					$settings['conditions']['Payment.info_code'] = '00000';
					break;
				case 'hold':
					$settings['conditions']['Payment.status'] = 0;
					$settings['conditions']['Payment.info_code'] = '';
					break;
				case 'error':
					$settings['conditions']['NOT'] = [
						'Payment.status between ? and ? ' => [0,3]
					];
					break;
				case 'cancel':
					$settings['conditions']['Payment.cancel_dt != '] = '0000-00-00 00:00:00';
					break;
			}
		}

		if (isset($params['reservation_id']) && $params['reservation_id'] != '') { // 予約ID
			$settings['conditions']['Reservation.id'] = $params['reservation_id'];
		}

		if (isset($params['reservation_key_compress']) && $params['reservation_key_compress'] != '') {
			$settings['conditions']['Reservation.reservation_key'] = $params['reservation_key_compress'];
		}

		if (isset($params['reservation_status']) && $params['reservation_status'] != '') { // 予約ステータス
			$settings['conditions']['Reservation.reservation_status_id'] = $params['reservation_status'];
		}

		if (isset($params['payment_status']) && $params['payment_status'] != '') { // 入金ステータス
			$settings['conditions']['Reservation.payment_status'] = $params['payment_status'];
		}

		if (isset($params['client_id']) && $params['client_id'] != '') { // 会社ID
			$settings['conditions']['Reservation.client_id'] = $params['client_id'];
		}

		if (isset($params['reserve_created_start']) && $params['reserve_created_start'] != '') { // 申込日時(start)
			$settings['conditions']['Reservation.created >= '] = $params['reserve_created_start'].' 00:00:00';
		}

		if (isset($params['reserve_created_end']) && $params['reserve_created_end'] != '') { // 申込日時(end)
			$settings['conditions']['Reservation.created <= '] = $params['reserve_created_end'].' 23:59:59';
		}

		if (isset($params['reserve_canceled_start']) && $params['reserve_canceled_start'] != '') { // 申込日時(start)
			$settings['conditions']['Reservation.cancel_datetime >= '] = $params['reserve_canceled_start'].' 00:00:00';
		}

		if (isset($params['reserve_canceled_end']) && $params['reserve_canceled_end'] != '') { // 申込日時(end)
			$settings['conditions']['Reservation.cancel_datetime <= '] = $params['reserve_canceled_end'].' 23:59:59';
		}

		if (isset($params['cancel_reason_id']) && $params['cancel_reason_id'] != '') { // 申込日時(end)
			$settings['conditions']['CancelReason.id'] = $params['cancel_reason_id'];
		}

		return $settings;
	}

	/*
	 * モデルデータを表示用に変える
	 */
	public function changeViewList($oldPaymentArr) {
		$newPaymentArr = [];
		foreach($oldPaymentArr as $oldPayment) {
			$tmpPayment = $oldPayment;

			$tmpPayment['Payment']['keijou'] = ($tmpPayment['Payment']['keijou']) ? '計上':'与信';
			$tmpPayment['Payment']['is_member'] = ($tmpPayment['Payment']['is_member']) ? '会員':'非会員';
			$tmpPayment['Payment']['price'] = (int)$tmpPayment['Payment']['price'];

			if ($tmpPayment['Payment']['cancel_dt'] != '0000-00-00 00:00:00') {
				$tmpPayment['Payment']['status_str'] = '決済キャンセル';
			}
			else if ($tmpPayment['Payment']['status'] == 1 && $tmpPayment['Payment']['info_code'] == '00000') {
				$tmpPayment['Payment']['status_str'] = '決済正常完了';
			}
			else if ($tmpPayment['Payment']['status'] == 0 && $tmpPayment['Payment']['info_code'] == '') {
				$tmpPayment['Payment']['status_str'] = '決済通知待ち';
			}
			elseif ($tmpPayment['Payment']['status'] == self::STAT_TEMPORARY) {
				$tmpPayment['Payment']['status_str'] = '仮入金';
			}
			else {
				$tmpPayment['Payment']['status_str'] = '決済失敗';
			}

			$tmpPayment['Reservation']['payment_status'] = (isset($tmpPayment['Reservation']['payment_status'])) ? Constant::paymentStatus()[$tmpPayment['Reservation']['payment_status']] : '';

			$tmpPayment['Reservation']['cancel_datetime'] = (!empty($tmpPayment['Reservation']['cancel_datetime']) && $tmpPayment['Reservation']['cancel_datetime'] != '0000-00-00 00:00:00') ? $tmpPayment['Reservation']['cancel_datetime'] : '';

			$newPaymentArr[] = $tmpPayment;
		}

		return $newPaymentArr;
	}

	/*
	 * モデルデータを表示用に変える
	 */
	public function newChangeViewList($oldPaymentArr) {
		$newPaymentArr = [];
		foreach($oldPaymentArr as $oldPayment) {
			$tmpPayment = $oldPayment;

			$tmpPayment['progress_name'] = isset($tmpPayment['progress_name']) ? $tmpPayment['progress_name'] : $tmpPayment['progressName'];
			$tmpPayment['price'] = isset($tmpPayment['payment_price']) ? (int)$tmpPayment['payment_price'] : (int)$tmpPayment['price'];
			$tmpPayment['cart_id'] = isset($tmpPayment['cart_id']) ? $tmpPayment['cart_id'] : $tmpPayment['cartId'];
			if (isset($tmpPayment['created_at'])) {
				$tmpPayment['created_at'] = $tmpPayment['created_at'];
			} elseif (isset($tmpPayment['paymentDt'])) {
				$tmpPayment['created_at'] = $tmpPayment['paymentDt'];
			} else {
				$tmpPayment['created_at'] = '';
			}
			if (isset($tmpPayment['order_code'])) {
				$tmpPayment['order_code'] = $tmpPayment['order_code'];
			} elseif (isset($tmpPayment['orderCode'])) {
				$tmpPayment['order_code'] = $tmpPayment['orderCode'];
			} else {
				$tmpPayment['order_code'] = '';
			}
			$tmpPayment['Reservation']['payment_status'] = (isset($tmpPayment['Reservation']['payment_status'])) ? Constant::paymentStatus()[$tmpPayment['Reservation']['payment_status']] : '';
			$tmpPayment['Reservation']['cancel_datetime'] = (!empty($tmpPayment['Reservation']['cancel_datetime']) && $tmpPayment['Reservation']['cancel_datetime'] != '0000-00-00 00:00:00') ? $tmpPayment['Reservation']['cancel_datetime'] : '';
			$tmpPayment['Reservation']['id'] = !empty($tmpPayment['Reservation']['id']) ? $tmpPayment['Reservation']['id']: '';
			$tmpPayment['Reservation']['reservation_key'] = !empty($tmpPayment['Reservation']['reservation_key']) ? $tmpPayment['Reservation']['reservation_key']: '';
			$tmpPayment['ReservationStatus']['name'] = !empty($tmpPayment['ReservationStatus']['name']) ? $tmpPayment['ReservationStatus']['name']: '';
			$tmpPayment['Reservation']['payment_status'] = !empty($tmpPayment['Reservation']['payment_status']) ? $tmpPayment['Reservation']['payment_status'] : '';
			$tmpPayment['Client']['name'] = !empty($tmpPayment['Client']['name']) ? $tmpPayment['Client']['name'] : '';
			$tmpPayment['Reservation']['created'] = !empty($tmpPayment['Reservation']['created']) ? $tmpPayment['Reservation']['created'] : '';
			$tmpPayment['CancelReason']['reason'] = !empty($tmpPayment['CancelReason']['reason']) ? $tmpPayment['CancelReason']['reason'] : '';

			$newPaymentArr[] = $tmpPayment;
		}

		return $newPaymentArr;
	}
}

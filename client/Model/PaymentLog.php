<?php
App::uses('AppModel', 'Model');
/**
 * PaymentInfo Model
 */
class PaymentLog extends AppModel {
	const STAT_TEMPORARY = 99999; // 仮入金

	public $useDbConfig = 'common';
	public $useTable = 'cm_th_other_payment_econ_credit_log';

	/*
	 * 指定した予約に入金情報(Payment)が存在するか否かを返却する
	 */
	public function hasPaymentInfo($reservationId) {
		$settings = [
			'joins' => [
				[
					'type' => 'INNER',
					'table' => 'skyticket.cm_th_application_detail',
					'alias' => 'CmThApplicationDetail',
					'conditions' => 'PaymentLog.cm_application_id = CmThApplicationDetail.cm_application_id',
				],
				[
					'type' => 'INNER',
					'table' => 'rentacar.reservations',
					'alias' => 'Reservation',
					'conditions' => 'Reservation.id = CmThApplicationDetail.application_id',
				],
			],
			'conditions' => [
				'CmThApplicationDetail.service_cd' => 'rc',
				'Reservation.id' => $reservationId,
			],
			'order' => [
				'PaymentLog.other_payment_econ_credit_log_id' => 'desc',
			]
		];

		$result = $this->find('count', $settings);

		return ($result > 0) ? true : false;
	}
}

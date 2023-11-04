<?php
App::uses('AppShell', 'Console/Command');
App::uses('SkyticketCakeEmail', 'Vendor');
require_once("encrypt_class.php");

class PaymentReminderShell extends AppShell {

	public $uses = array('Reservation');

	public function main() {

		$params = $this->Reservation->find('all', [
			'fields' => [
				'Reservation.last_name',
				'Reservation.first_name',
				'Reservation.payment_limit_datetime',
				'Reservation.reservation_hash',
				'Reservation.email',
			],
			'conditions' => [
				'Reservation.payment_limit_datetime <= ' => date('Y-m-d H:i:s', strtotime('+3 days')),
				'Reservation.payment_limit_datetime >= ' => date('Y-m-d H:i:s'),
				'Reservation.payment_status' => '',
				'Reservation.cancel_flg' => 0
			],
			'recursive' => -1,
		]);

		if (IS_PRODUCTION) {
			$domain = 'skyticket.jp';
		} else {
			$domain = 'jp.skyticket.jp';
		}

		foreach($params as $param) {
			$param['domain'] = $domain;
			// 継承してメールクラスを利用
			$email = new SkyticketCakeEmail('smtp');
			$email
				->viewVars($param)
				->template('payment_reminder', 'suggestions_layout')
				->subject('【skyticket】※至急ご確認ください※レンタカー追加代金お支払いのお願い')
				->to(trim($param['Reservation']['email']))
				->send();
		}
	}
}

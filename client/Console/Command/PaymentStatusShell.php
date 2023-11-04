<?php
App::uses('AppShell', 'Console/Command');

class PaymentStatusShell extends AppShell {

	public $uses = array('Reservation');

	public function main() {

		// 入金ステータス「返金依頼受付中」で一定期間経過したものを「返金期限切れ」にする
		$this->Reservation->paymentStatusToRefundExpired();

	}
}

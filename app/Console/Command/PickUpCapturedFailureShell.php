<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('PaymentEconComponent', 'Controller/Component');

class PickUpCapturedFailureShell extends AppShell {

	public $components = array('PaymentEcon');

	public function startup() {
		$collection = new ComponentCollection();
		$this->PaymentEcon = new PaymentEconComponent($collection);

		parent::startup();
	}

	public function main() {

		// 与信->計上にならなければならないのに、与信として残ってしまっているものがあれば通知する
		$this->PaymentEcon->pickUpCapturedFailure();

	}
}

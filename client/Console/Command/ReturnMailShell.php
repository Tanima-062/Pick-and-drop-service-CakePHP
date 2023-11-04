<?php
App::uses('AppShell', 'Console/Command');

class ReturnMailShell extends AppShell {

	public $uses = array('DeliveryMail');

	public function main() {

		//メールの送信に失敗したユーザーのメールアドレスを取得しDBに保存する
		$this->DeliveryMail->saveReturnMailInfo();
	}
}

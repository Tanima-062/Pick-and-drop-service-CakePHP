<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('PaymentEconComponent', 'Controller/Component');
App::uses('PaymentAPIComponent', 'Controller/Component');

class PickUpAuthStatusShell extends AppShell {

	public $components = array('PaymentEcon', 'PaymentAPI');

	public function startup() {
		$collection = new ComponentCollection();
		$this->PaymentEcon = new PaymentEconComponent($collection);
		$this->PaymentAPI = new PaymentAPIComponent($collection);

		parent::startup();
	}

	public function main() {
		// 決済API移行に伴いpickUpCapturedFailureの処理をこちらに変更
		$authData = $this->PaymentAPI->getMistakeDataForBatch(2); // 2:与信

		$date = date('Y-m-d H:i:s', strtotime('-1 hour'));
		$msg = '';
		foreach($authData as $data) {
			// 直近一時間はスキップ
			if (strtotime($date) < strtotime($data['created_at'])) {
				continue;
			}
			
			$msg .= 'skyticket申込番号:'.$data['cm_application_ids']['rc'][0]."\r\n";
		}

		if (!empty($msg)) {
			// 与信が残っていたらチャットワークへ通知
			$this->PaymentEcon->notice($msg, '与信->計上漏れ');
		}
	}
}

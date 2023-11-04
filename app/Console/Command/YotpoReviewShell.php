<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('YotpoAPIComponent', 'Controller/Component');

class YotpoReviewShell extends AppShell {

	public function startup() {
		$collection = new ComponentCollection();
		$this->controller = new Controller();
		$this->YotpoAPI = new YotpoAPIComponent($collection);
		$this->YotpoAPI->startup($this->controller);
		parent::startup();
	}

	public function postOrderWrapper() {

		$argCount = count($this->args);
		if ($argCount != 2) {
			CakeLog::error("YotpoReview postOrderWrapper param count error (count = $argCount)");
			return;
		}

		$yotpo_order_info = json_decode($this->args[0], true);
		$yotpo_items = json_decode($this->args[1], true);

		$result = $this->YotpoAPI->postOrder($yotpo_order_info, $yotpo_items);
		if (!$result) {
			$orderNo = isset($yotpo_order_info['order_number']) ? $yotpo_order_info['order_number'] : 'undefined';
			CakeLog::error("YotpoReview postOrderWrapper postOrder failed (orderNo = $orderNo)");
		}
	}
}
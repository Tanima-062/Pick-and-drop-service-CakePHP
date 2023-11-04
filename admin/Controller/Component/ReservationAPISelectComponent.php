<?php

App::uses('Component', 'Controller');

class ReservationAPISelectComponent extends Component {

	private $config = null;

	public function startup(&$controller) {
		Configure::load('ApiConfig', 'default');
		$this->config = Configure::read('ApiConfig');
		$this->controller = $controller;
	}

	// 予約連携API実行要否
	public function apiRequired($clientId) {
		switch ($clientId) {
			case Constant::BUDGET_CLIENT_ID:
				return $this->config['all'] && $this->config['BudgetReservations'];
			default:
				return false;
		}
	}

	// 会社別APIコンポーネント名取得
	public function getApiComponentName($clientId) {
		if (!$this->apiRequired($clientId)) {
			return '';
		}

		switch ($clientId) {
			case Constant::BUDGET_CLIENT_ID:
				return 'BudgetReservationAPI';
			default:
				return '';
		}
	}
}

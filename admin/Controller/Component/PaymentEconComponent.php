<?php
require_once("payment/payment_interface.php");

class PaymentEconComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function inquiry($order_id) {
		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');

		return $pay_obj->orderStatus($order_id);
	}

	public function refund($order_id, $amount) {
		$econ_credit_log = $this->controller->Payment->find('first', [
			'conditions' => ['order_id' => $order_id]
		]);

		if (empty($econ_credit_log['Payment']['price']) || (int)$econ_credit_log['Payment']['price'] == (int)$amount) {
			return false;
		}

		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');
		$pay_obj->is_member = false; // TODO:ECONカード会員登録する場合はtrueだが、今は固定

		$ret = $pay_obj->orderChangeAmount($order_id, (int)$amount);
		if (!$ret) {
			return false;
		}

		$action = ($pay_obj->is_member) ? 'card_change_amount_member' : 'card_change_amount';

		try {
			$data = [
				'Payment.action' => "'" . $action . "'",
				'Payment.price' => $amount,
				'Payment.update_dt' => "'" . date('Y-m-d H:i:s') . "'"
			];

			$conditions = [
				'Payment.other_payment_econ_credit_log_id' => $econ_credit_log['Payment']['other_payment_econ_credit_log_id']
			];

			if (!$this->controller->Payment->updateAll($data, $conditions)) {
				return false;
			}
		} catch(Exception $e) {
			$this->log($e->getMessage(), LOG_DEBUG);
			return false;
		}

		return true;
	}

	public function isYoshinCancel($payment) {
		if ($payment['Payment']['keijou'] == '与信' &&
			is_null($payment['Reservation']['id']) &&
			$payment['Payment']['cancel_dt'] == '0000-00-00 00:00:00' &&
			$payment['Payment']['status_str'] == '決済正常完了'
			) {
			return true;
		} else {
			return false;
		}
	}

	public function yoshinCancel($order_id) {
		$econ_credit_log = $this->controller->Payment->find('first', [
			'conditions' => ['order_id' => $order_id]
		]);

		if (!$econ_credit_log || $econ_credit_log['Payment']['keijou'] == 1) {
			return false;
		}

		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');
		if ($pay_obj->orderCancel($order_id)) {
			if ($this->controller->Payment->updateAll(
				['Payment.cancel_dt' => "'" . date('Y-m-d H:i:s') . "'"],
				['Payment.other_payment_econ_credit_log_id' => $econ_credit_log['Payment']['other_payment_econ_credit_log_id'],]
			)) {
				return true;
			}
		}

		return false;
	}

	public function canCardCapture($payment)
	{
		if ($payment['Payment']['keijou'] == '与信' &&
			$payment['Payment']['cancel_dt'] == '0000-00-00 00:00:00' &&
			$payment['Payment']['status_str'] == '決済正常完了' &&
			!empty($payment['Reservation']['reservation_status_id']) &&
			($payment['Reservation']['reservation_status_id'] == 1 || // 予約
			$payment['Reservation']['reservation_status_id'] == 2) // 成約
			) {
			return true;
		} else {
			return false;
		}
	}

	public function cardCapture($order_id) {
		$econ_credit_log = $this->controller->Payment->find('first', [
			'conditions' => ['order_id' => $order_id]
		]);

		if (!$econ_credit_log || $econ_credit_log['Payment']['keijou'] == 1) {
			return false;
		}

		$pay_obj = PaymentFactory::create('econtext');
		$pay_obj->setType('card');
		$pay_obj->is_member = false; // TODO:ECONカード会員登録する場合はtrueだが、今は固定

		$result = $pay_obj->cardCapture($order_id, (int)$econ_credit_log['Payment']['price'], date('Y/m/d'));
		if ($result) {
			if ($this->controller->Payment->updateAll(
				['Payment.keijou' => 1],
				['Payment.other_payment_econ_credit_log_id' => $econ_credit_log['Payment']['other_payment_econ_credit_log_id'],]
			)) {
				return true;
			}
		}

		return false;
	}
}

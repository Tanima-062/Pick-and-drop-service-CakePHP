<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CancelPolicyComponent', 'Controller/Component');
App::uses('SkyticketCakeEmail', 'Vendor');

class ConfirmReserveShell extends AppShell {

	public $uses = array('Reservation', 'PaymentLog');

	private $wday = array('日','月','火','水','木','金','土');

	public function startup() {
		$collection = new ComponentCollection();
		$this->controller = new Controller();
		$this->CancelPolicy = new CancelPolicyComponent($collection);
		$this->CancelPolicy->startup($this->controller);
		parent::startup();
	}

	public function main() {

		$rentDay = date('Y-m-d', strtotime('+3 day'));

		$targetList = $this->Reservation->find('all', [
			'fields' => [
				'Reservation.id',
			],
			'joins' => [
				[
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => 'CommodityItem.id = Reservation.commodity_item_id',
				],
				[
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => 'Commodity.id = CommodityItem.commodity_id',
				],
			],
			'conditions' => [
				'Reservation.reservation_status_id' => Constant::STATUS_RESERVATION,
				'Reservation.rent_datetime >=' => $rentDay.' 00:00:00',
				'Reservation.rent_datetime <=' => $rentDay.' 23:59:59',
				'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
			],
			'recursive' => -1,
		]);

		if (IS_PRODUCTION) {
			$domain = 'skyticket.jp';
		} else {
			$domain = 'jp.skyticket.jp';
		}

		foreach ($targetList as $t) {
			$count = $this->Reservation->find('count', [
				'conditions' => [
					'Reservation.id' => $t['Reservation']['id'],
					'NOT' => ['payment_status' => NULL],
				]
			]);

			$fromStep1 = !($count);

			$options = array(
				'conditions' => array(
					'Reservation.id' => $t['Reservation']['id'],
				),
				'recursive' => -1
			);
			$params = $this->Reservation->getReservationMailData($options);

			$params['domain'] = $domain;
			$params['Reservation']['rent_date'] = date('Y年m月d日',strtotime($params['Reservation']['rent_datetime']));
			$params['Reservation']['rent_week'] = $this->wday[date('w',strtotime($params['Reservation']['rent_datetime']))];
			$params['Reservation']['rent_time'] = date('H:i',strtotime($params['Reservation']['rent_datetime']));
			$params['Reservation']['return_date'] = date('Y年m月d日',strtotime($params['Reservation']['return_datetime']));
			$params['Reservation']['return_week'] = $this->wday[date('w',strtotime($params['Reservation']['return_datetime']))];
			$params['Reservation']['return_time'] = date('H:i',strtotime($params['Reservation']['return_datetime']));
			$params['Reservation']['fromStep1'] = $fromStep1;
			$params['Commodity']['name'] = mb_convert_kana($params['Commodity']['name'], 'KV');
			$params['RentOffices']['rent_meeting_info'] = mb_convert_kana($params['RentOffices']['rent_meeting_info'], 'KV');
			$params['RentOffices']['rent_office_notification'] = mb_convert_kana($params['RentOffices']['notification'], 'KV');
			$params['ReturnOffices']['return_meeting_info'] = mb_convert_kana($params['ReturnOffices']['return_meeting_info'], 'KV');
			$params['CancelPolicy'] = $this->CancelPolicy->getTextLines($params['Reservation']['client_id'], $params['Reservation']['rent_datetime'], false);
			// INCIDENT-3044 取消手続料の徴収を廃止する
			//$params['AdvCancelFee'] = $this->CancelPolicy->getAdvCancelFee();

			$email = new SkyticketCakeEmail('smtp');
			$email
				->viewVars($params)
				->template('confirm_reserve', 'suggestions_layout')
				->subject('【skyticket】まもなく'.$params['Client']['name'].'ご利用のお日にちです')
				->to(trim($params['Reservation']['email']))
				->send();
		}
	}
}

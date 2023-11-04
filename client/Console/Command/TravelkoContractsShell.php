<?php
App::uses('AppShell', 'Console/Command');
App::uses('HttpSocket', 'Network/Http');
App::uses('Hash', 'Utility');

// トラベルコ確定API
class TravelkoContractsShell extends AppShell {

	public $uses = array('Reservation');

	public function main() {
		$url = 'https://www.tour.ne.jp/api/json/tracking/commit/';

		$contractDate = !empty($this->args[0])?date("Y-m-d", strtotime($this->args[0])):date("Y-m-d", strtotime("-1 day"));
		$from = date("Y-m-d 00:00:00", strtotime($contractDate));
		$to = date("Y-m-d 23:59:59", strtotime($contractDate));

		$reservationList = $this->Reservation->find('all', [
			'fields' => [
				'Reservation.reservation_key',
			],
			'conditions' => [
				'Reservation.reservation_status_id' => Constant::STATUS_CONTRACT,
				'Reservation.advertising_cd' => 'travelko_rc',
				"(CASE Client.conclusion_contract_criteria WHEN 0 THEN Reservation.rent_datetime ELSE Reservation.return_datetime END) between ? and ? " => [$from, $to],
			],
			'joins' => [
				[
					'type' => 'INNER',
					'table' => 'clients',
					'alias' => 'Client',
					'conditions' => [
						'Client.id = Reservation.client_id',
					]
				]
			],
			'recursive' => -1,
		]);

		$reservationKeys = Hash::extract($reservationList, '{n}.Reservation.reservation_key');

		$param = [
			'menu_code' => Constant::TRAVELKO_MENU_CODE,
			'agent_code' => Constant::TRAVELKO_AGENT_CODE,
			'commit_date' => $contractDate,
			'reservation_id' => implode(',', $reservationKeys)
		];

		$httpSocket = new HttpSocket();
		$res = $httpSocket->get($url, $param, null);

		if ($res->code !== '200') {
			$this->log("failed URL:".print_r($url, true), LOG_ERROR);
			$this->log("failed result:".print_r($res, true), LOG_ERROR);
		}
	}
}


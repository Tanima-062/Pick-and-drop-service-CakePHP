<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('PaymentEconComponent', 'Controller/Component');
App::uses('PaymentAPIComponent', 'Controller/Component');

class PickUpMistakeCaptureShell extends AppShell {

	public $uses = array('Reservation');
	public $components = array('PaymentEcon', 'PaymentAPI');

	public function startup() {
		$collection = new ComponentCollection();
		$this->PaymentEcon = new PaymentEconComponent($collection);
		$this->PaymentAPI = new PaymentAPIComponent($collection);

		parent::startup();
	}

	public function main() {
		// 予約情報のない計上データを取得
		$mistakeData = $this->PaymentAPI->getMistakeDataForBatch(3); // 3:計上

		$cmApplicationIds = [];
		foreach ($mistakeData as $data) {
			if (empty($data['cm_application_ids']['rc'])) {
				continue;
			}

			$cmApplicationIds[] = $data['cm_application_ids']['rc'][0];
        }

		$params['cm_application_ids'] = array_unique($cmApplicationIds);
		$reservationData = $this->Reservation->getExistCmApplicationIds($params);

		$reservationCmApplicationIds = [];
		foreach ($reservationData as $index => $reservation) {
			$reservationCmApplicationIds[] = (int)$reservation['CmThApplicationDetail']['cm_application_id'];
        }

		$results = array_diff($cmApplicationIds, $reservationCmApplicationIds);

		$msg = '';
		foreach ($results as $cmApplicationId) {
			$msg .= 'skyticket申込番号:'.$cmApplicationId."\r\n";
		}

		if (!empty($msg)) {
			// 予約情報のない計上があればチャットワークへ通知
			$this->PaymentEcon->notice($msg, '予約情報のない計上データ');
		}
	}
}

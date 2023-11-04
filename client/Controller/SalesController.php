<?php
class SalesController extends AppController {

	public $uses = array('CarClass','StockGroup','Office','CarType');

	public function beforeFilter() {

		parent::beforeFilter();

		$carClassLists = $this->CarClass->getCarClassLists($this->clientData['client_id']);
		$this->set('carClassLists', $carClassLists);

		$officeLists = $this->Office->getOfficeLists($this->clientData['client_id']);

		$this->set('officeLists',$officeLists);

		$this->set('regionList', Constant::regions());
	}

	// 月別（手配）
	public function month() {
		$this->month_main();
	}
	// 月別（募集）
	public function month_organized() {
		$this->month_main(Constant::SALES_TYPE_AGENT_ORGANIZED);
		$this->render('month');
	}

	private function month_main($salesType = Constant::SALES_TYPE_ARRANGED) {
		$searchData = array();
		if (empty($this->request->query)) {
			$searchData['year'] = $this->request->data['Reservation']['year']['year'] = date('Y');
			$searchData['region_link_cd'] = '';
			$searchData['office_id'] = '';
			$searchData['car_class_id'] = '';
			$searchData['car_type_id'] = '';
		} else {
			$searchData = $this->request->query;
			$searchData['year'] = $searchData['year']['year'];
			$this->request->data['Reservation'] = $this->request->query;
		}

		$clientId = $this->clientData['client_id'];

		// 成約などの予約データ取得
		$this->Reservation->recursive = -1;
		$proceeds = $this->Reservation->getProceeds($searchData, $clientId, $salesType);

		$data = $this->__formatData($proceeds);

		// 予約獲得取得
		$this->Reservation->recursive = -1;
		$proceeds = $this->Reservation->getReservedCount($searchData, $clientId, $salesType);

		$data2 = $this->__formatDataNonStatus($proceeds);

		//成約が貸出日基準か、返却日基準か判定
		$dayStandardClientFlg = $this->Reservation->getDayStandardClientFlg($clientId);

		$this->set('carTypeLists', $this->CarClass->getCarTypeByClientID($clientId));

		$this->set('data', $data);
		$this->set('data2', $data2);
		$this->set('clientList', $this->Client->find('list'));
		$this->set('dayStandardClientFlg', $dayStandardClientFlg);

		$this->set('searchData', $searchData);

		$this->set('salesType', $salesType);
	}

	// 日別（手配）
	public function daily() {
		$this->daily_main();
	}
	// 日別（募集）
	public function daily_organized() {
		$this->daily_main(Constant::SALES_TYPE_AGENT_ORGANIZED);
		$this->render('daily');
	}

	private function daily_main($salesType = Constant::SALES_TYPE_ARRANGED) {
		$searchData = array();
		if (empty($this->request->query)) {
			$searchData['year'] = $this->request->data['Reservation']['year']['year'] = date('Y');
			$searchData['month'] = $this->request->data['Reservation']['month']['month'] = date('m');
			$searchData['region_link_cd'] = '';
			$searchData['office_id'] = '';
			$searchData['car_class_id'] = '';
			$searchData['car_type_id'] = '';
		} else {
			$searchData = $this->request->query;
			$searchData['year'] = $searchData['year']['year'];
			$searchData['month'] = $searchData['month']['month'];
			$this->request->data['Reservation'] = $this->request->query;
		}

		$clientId = $this->clientData['client_id'];

		// 成約とか予約データ取得
		$this->Reservation->recursive = -1;
		$proceeds = $this->Reservation->getProceeds($searchData, $clientId, $salesType);

		$data = $this->__formatData($proceeds);

		$this->set('data',$data);

		// 予約獲得取得
		$this->Reservation->recursive = -1;
		$proceeds = $this->Reservation->getReservedCount($searchData, $clientId, $salesType);

		$data2 = $this->__formatDataNonStatus($proceeds);

		// 成約が貸出日基準か、返却日基準か判定
		$dayStandardClientFlg = $this->Reservation->getDayStandardClientFlg($clientId);

		$this->set('carTypeLists',$this->CarClass->getCarTypeByClientID($clientId));

		$this->set('data2', $data2);
		$this->set('clientList', $this->Client->find('list'));
		$this->set('month', $searchData['month']);
		$this->set('year', $searchData['year']);
		$this->set('dayStandardClientFlg', $dayStandardClientFlg);
		$this->set('searchData', $searchData);
		$this->set('salesType', $salesType);
	}

	//キャンセル日別
	public function cancel_daily() { // 現在使ってない
		$this->daily();
	}

	//キャンセル月別
	public function cancel_month() { // 現在使ってない
		$this->month();
	}

	//日別予約された数
	public function daily_reservation_operand() { // 現在使ってない

		if($this->request->is('post')) {
			$year = $this->request->data['Reservation']['year'];
			$month = $this->request->data['Reservation']['month'];
		} else {
			$year = $this->request->data['Reservation']['year'] = date('Y');
			$month = $this->request->data['Reservation']['month'] = date('m');
		}

		//成約とか予約データ取得
		$this->Reservation->recursive = -1;
		$procaeeds = $this->Reservation->getReservedOperand($year,$month);

		$data = $this->__formatDataNonStatus($procaeeds);

		$this->set('data',$data);
		$this->set('clientList',$this->Client->find('list'));

	}

	//月別予約された数
	public function month_reservation_operand() { // 現在使ってない

		if($this->request->is('post')) {
			$year = $this->request->data['Reservation']['year'];
		} else {
			$year = $this->request->data['Reservation']['year'] = date('Y');
		}

		//成約とか予約データ取得
		$this->Reservation->recursive = -1;
		$procaeeds = $this->Reservation->getReservedOperand($year);

		$data = $this->__formatDataNonStatus($procaeeds);

		$this->set('data',$data);

		$this->set('clientList',$this->Client->find('list'));

	}

	//時間別予約された数
	public function hour_reservation_operand() { // 現在使ってない
		if($this->request->is('post')) {
			$year = $this->request->data['Reservation']['year'];
			$month = $this->request->data['Reservation']['month'];
			$day = $this->request->data['Reservation']['day'];
		} else {
			$year = $this->request->data['Reservation']['year'] = date('Y');
			$month = $this->request->data['Reservation']['month'] = date('m');
			$day = $this->request->data['Reservation']['day'] = date('d');
		}

		//成約とか予約データ取得
		$this->Reservation->recursive = -1;
		$procaeeds = $this->Reservation->getReservedOperand($year,$month,$day);

		$data = $this->__formatDataNonStatus($procaeeds);

		$this->set('data',$data);

		$this->set('clientList',$this->Client->find('list'));
	}

	//取得したデータを集計する
	private function __formatData($procaeeds) {

		$data = array();
		foreach($procaeeds as $key => $val) {

			//
			$date = $val[0]['date'];


			$statusArray = array('booking','agreement','cancel','expected_revenue');

			/**
			 * 初期化処理
			 */
			foreach($statusArray as $status) {
				if(empty($data[$date][$status]['price'])) {
					$data[$date][$status]['price'] = 0;
				}

				if(empty($data[$date][$status]['count'])) {
					$data[$date][$status]['count'] = 0;
				}

				if(empty($data['sum'][$status]['price'])) {
					$data['sum'][$status]['price'] = 0;
				}

				if(empty($data['sum'][$status]['count'])) {
					$data['sum'][$status]['count'] = 0;
				}
			}


			if($val['Reservation']['reservation_status_id'] == 0) {
				continue;
				//予約
			} else if($val['Reservation']['reservation_status_id'] == 1) {
				$status = 'booking';
				//成約
			} else if($val['Reservation']['reservation_status_id'] == 2) {
				$status = 'agreement';
				//キャンセル
			} else if($val['Reservation']['reservation_status_id'] == 3) {
				$status = 'cancel';
			}

			// クライアント別見込み売上　予約分のみ
			if($status == 'booking') {

				$data['sum']['expected_revenue']['price'] += $val[0]['price'];
				$data['sum']['expected_revenue']['count'] += $val[0]['count'];

				$data[$date]['expected_revenue']['price'] += $val[0]['price'];
				$data[$date]['expected_revenue']['count'] += $val[0]['count'];
			}

			 // 成約データ & 予約データ
			//合計
			$data['sum'][$status]['price'] += $val[0]['price'];
			$data['sum'][$status]['count'] += $val[0]['count'];

			//日時別
			$data[$date][$status]['price'] = $val[0]['price'];
			$data[$date][$status]['count'] = $val[0]['count'];

		}

		return $data;

	}

	//取得したデータを集計するステータスを見ない
	private function __formatDataNonStatus($procaeeds) {

		$data = array();
		foreach($procaeeds as $key => $val) {

			//
			$date 		= $val[0]['date'];

			$statusArray = array('booking','agreement','cancel','expected_revenue');

			/**
			 * 初期化処理
			 */
			foreach($statusArray as $status) {

				if(empty($data[$date][$status]['price'])) {
					$data[$date][$status]['price'] = 0;
				}

				if(empty($data[$date][$status]['count'])) {
					$data[$date][$status]['count'] = 0;
				}

				if(empty($data['sum'][$status]['price'])) {
					$data['sum'][$status]['price'] = 0;
				}

				if(empty($data['sum'][$status]['count'])) {
					$data['sum'][$status]['count'] = 0;
				}
			}


			//予約
			if($val['Reservation']['reservation_status_id'] == 0) {
				continue;
			} else {
				$status = 'booking';
			}


			/**
			 * クライアント別見込み売上
			 */

			$data['sum']['expected_revenue']['price'] += $val[0]['price'];
			$data['sum']['expected_revenue']['count'] += $val[0]['count'];


			$data[$date]['expected_revenue']['price'] += $val[0]['price'];
			$data[$date]['expected_revenue']['count'] += $val[0]['count'];

			/**
			 * 成約データ & 予約データ
			 */

			//合計
			$data['sum'][$status]['price'] += $val[0]['price'];
			$data['sum'][$status]['count'] += $val[0]['count'];

			$data[$date][$status]['price'] = $val[0]['price'];
			$data[$date][$status]['count'] = $val[0]['count'];

		}

		return $data;

	}

}
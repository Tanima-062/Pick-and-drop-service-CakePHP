<?php
App::uses('AppController', 'Controller');
/**
 * Statistics Controller
 *
 * @property Statistics $Statistics
 */
class StatisticsController extends AppController {

	public $monthArray;

	public $uses = array(
			'CarClassStock', 'CarClass', 'StockGroup', 'Reservation', 'Statistic', 'Office', 'Commodity', 'Reservation',
			'ReservationChildSheet', 'Privilege', 'ReservationPrivilege'
	);

	public function sales() {

		if (isset($this->data['search']) && !empty($this->data['Statistic']['year'])) {
			$this->request->data = Sanitize::clean($this->data, array('encode' => false));
		} else {
			if(!empty($this->request->query)) {
				$this->request->data['Statistic'] = $data = $this->request->query;
				if(!empty($data['csv'])) {
					$this->request->data['Statistic']['csv'] = 1;
				}
			} else {
				//初期値
				$this->request->data['Statistic']['year'] = date('Y');
			}
		}

		$statistics = $this->Reservation->getSaleStatisticsSearch($this->data, $this->clientData['client_id']);



		$commodityLists = $this->Commodity->getCommodityLists($this->clientData['client_id']);
		$carClassLists = $this->CarClass->getCarClassLists($this->clientData['client_id']);
		$officeLists = $this->Office->getOfficeLists($this->clientData['client_id']);
		$client = $this->Client->getClient($this->clientData['client_id']);
		$yearArray = $this->__getYearArray();
		$url = http_build_query($this->request->data['Statistic']);

		$this->set('url',$url);
		$this->set('statistics', $statistics);
		$this->set('commodityLists', $commodityLists);
		$this->set('carClassLists', $carClassLists);
		$this->set('officeLists', $officeLists);
		$this->set('yearArray', $yearArray);
		$this->set('client', $client);


		if(!empty($data['csv'])) {
			$this->__downloadCsv($statistics);
		}

	}

	public function commodity() {

		if (isset($this->data['search']) && ((!empty($this->data['Statistic']['year']) &&
						!empty($this->data['Statistic']['month'])) || !empty($this->data['Statistic']['commodity_key']))){

			$this->data = Sanitize::clean($this->data, array('encode' => false));
			$statistics = $this->Statistic->getCommodityStatisticsSearch($this->data, $this->clientData['client_id']);
			$this->set('statistics', $statistics);

		} else {

			//初期値
			$this->request->data['Statistic']['year'] = date('Y');
			$this->request->data['Statistic']['month'] = date('m');
			$statistics = $this->Statistic->getCommodityStatisticsSearch($this->request->data, $this->clientData['client_id']);
			$this->set('statistics', $statistics);

		}

		$carClassLists = $this->CarClass->getCarClassLists($this->clientData['client_id']);
		$this->set('carClassLists', $carClassLists);

		$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
		$this->set('stockGroups', $stockGroups);

		$yearArray = $this->__getYearArray();
		$this->set('yearArray', $yearArray);

		$this->set('monthArray', $this->monthArray);
	}

	//
	private function __getYearArray($minYear = '2013') {

		$yearArray = array();
		for($i = $minYear; $i <= date('Y');$i++) {
			$yearArray[$i] = $i;
		}

		return $yearArray;
	}

	//
	private function __downloadCsv($statistics) {

		Configure::write('debug', 0); // debugコードを出さない
		$this->autoRender = false; // Viewを使わない

		/**
		 *
		 * CSVの処理
		 */

		$csvFile = date('YmdHis'). '.csv';

		foreach($statistics as $statistic) {
			$date = date('Y-m',strtotime($statistic['reservations']['statistic_date']));
			$reservationIds = $statistic[0]['reservations'][$date];
		}

		//予約取得
		$reservations = $this->Reservation->find('all', array(
				'fields' => array(
						'Reservation.*',
						'CommodityItem.id',
						'CommodityItem.commodity_id',
						'CommodityItem.car_class_id',
						'Commodity.id',
						'Commodity.name',
						'CarClass.id',
						'CarClass.name',
				),
				'joins' => array(
						array(
								'table' => 'commodity_items',
								'alias' => 'CommodityItem',
								'type' => 'LEFT',
								'conditions' => array(
										'CommodityItem.id = Reservation.commodity_item_id'
								),
						),
						array(
								'table' => 'commodities',
								'alias' => 'Commodity',
								'type' => 'LEFT',
								'conditions' => array(
										'Commodity.id = CommodityItem.commodity_id'
								),
						),
						array(
								'table' => 'car_classes',
								'alias' => 'CarClass',
								'type' => 'LEFT',
								'conditions' => array(
										'CarClass.id = CommodityItem.car_class_id'
								),
						),
				),
				'conditions'=>array(
						'Reservation.id'=>$reservationIds
				),
				'order'=>array(
						'Reservation.id'=>'desc'
				),
				'recursive' => -1,
		));

		// チャイルドシート
		$reservationIdArray = Hash::extract($reservations, '{n}.Reservation.id');
		$childSheetData = $this->ReservationChildSheet->find('all', array(
				'conditions' => array(
						'ReservationChildSheet.reservation_id' => $reservationIdArray,
						'ReservationChildSheet.delete_flg' => 0,
				),
				'recursive' => -1,
		));
		$childSheetData = Hash::combine($childSheetData, '{n}.ReservationChildSheet.id', '{n}.ReservationChildSheet', '{n}.ReservationChildSheet.reservation_id');
		$sheetList = $this->Privilege->find('list', array(
				'conditions' => array(
						'Privilege.client_id' => $this->clientData['Client']['id'],
						'Privilege.option_flg' => 1,
				),
				'recursive' => -1,
		));

		// オプション
		$privilegeData = $this->ReservationPrivilege->find('all', array(
				'fields' => array(
						'ReservationPrivilege.*',
						'Privilege.*',
				),
				'joins' => array(
						array(
								'table' => 'privileges',
								'alias' => 'Privilege',
								'type' => 'LEFT',
								'conditions' => array(
										'Privilege.id = ReservationPrivilege.privilege_id'
								),
						),
				),
				'conditions' => array(
						'ReservationPrivilege.reservation_id' => $reservationIdArray,
						'ReservationPrivilege.delete_flg' => 0,
						'Privilege.client_id' => $this->clientData['Client']['id'],
				),
				'recursive' => -1,
		));
		$privilegeData = Hash::combine($privilegeData, '{n}.ReservationPrivilege.id', '{n}', '{n}.ReservationPrivilege.reservation_id');

		//営業所リスト
		$officeList = $this->Office->getCsvOfficeList($this->clientData['client_id']);

		$csvData = "NO,予約番号,氏名カナ,ご利用人数(大人),ご利用人数(子供),ご利用人数(幼児),出発日,返却日,到着便名,出発便名,合計金額,お申込みプラン,車両タイプ,車両台数,貸出店舗,返却店舗,シート,特典オプション\n";

		$i = 1;
		foreach($reservations as $reservation) {
			$reservationId = $reservation['Reservation']['id'];
			$commodityId = $reservation['CommodityItem']['commodity_id'];

			// チャイルドシート
			$childSheet = '';
			if (!empty($childSheetData[$reservation['Reservation']['id']])) {
				foreach ($childSheetData[$reservation['Reservation']['id']] as $value) {
					if (!empty($sheetList[$value['child_sheet_id']])) {
						if (empty($childSheet)) {
							if (!empty($value['count'])) {
								$childSheet .= $sheetList[$value['child_sheet_id']].'×'.$value['count'];
							}
						} else {
							if (!empty($value['count'])) {
								$childSheet .= ' '.$sheetList[$value['child_sheet_id']].'×'.$value['count'];
							}
						}
					}
				}
			}

			// 特典オプション
			$privilege = '';
			if (!empty($privilegeData[$reservation['Reservation']['id']])) {
				foreach ($privilegeData[$reservation['Reservation']['id']] as $key => $value) {
					$privilege .= ' '.$value['Privilege']['name'].'×'.$value['ReservationPrivilege']['count'];
				}
			}

			//NO
			$csvData .= $i .',';

			//予約番号
			$csvData .= "\"".$reservation['Reservation']['reservation_key']."\"" . ',';

			//氏名カナ
			$csvData .= "\"".$reservation['Reservation']['last_name'] . ' ' . $reservation['Reservation']['first_name']."\"" . ',';

			//ご利用人数（大人）
			$csvData .= "\"".$reservation['Reservation']['adults_count']."\"" . ',';

			//ご利用人数(子供)
			$csvData .= "\"".$reservation['Reservation']['children_count']."\"" . ',';

			//ご利用人数(幼児)
			$csvData .= "\"".$reservation['Reservation']['infants_count']."\"" . ',';

			//出発日
			$csvData .= "\"".date('Y-m-d H:i',strtotime($reservation['Reservation']['rent_datetime']))."\"" . ',';

			//返却日
			$csvData .= "\"".date('Y-m-d H:i',strtotime($reservation['Reservation']['return_datetime']))."\"" . ',';

			//到着便名
			$csvData .= "\"".$reservation['Reservation']['arrival_flight_number']."\"" . ',';

			//出発便名
			$csvData .= "\"".$reservation['Reservation']['departure_flight_number']."\"" . ',';

			//合計金額
			$csvData .= "\"".$reservation['Reservation']['amount']."\"" . ',';

			//お申込みプラン
			$csvData .= "\"".$reservation['Commodity']['name']."\"" . ',';

			//車両タイプ
			$csvData .= "\"".$reservation['CarClass']['name']."\"" . ',';

			//車両台数
			$csvData .= "\"".$reservation['Reservation']['cars_count']."\"" . ',';

			//貸出店舗
			$csvData .= "\"".$officeList[$reservation['Reservation']['rent_office_id']]."\"" . ',';

			//返却店舗
			$csvData .= "\"".$officeList[$reservation['Reservation']['return_office_id']]."\"" . ',';

			// シート
			$csvData .= "\"".$childSheet."\"" . ',';

			// 特典オプション
			$csvData .= "\"".$privilege."\"" . "\n";

			$i++;
		}

		header ("Content-disposition: attachment; filename=" . $csvFile);
		header ("Content-type: application/octet-stream; name=" . $csvFile);
		$interenc = mb_internal_encoding();
		mb_convert_variables('SJIS', $interenc, $csvData);

		echo $csvData;
		exit;


	}
}
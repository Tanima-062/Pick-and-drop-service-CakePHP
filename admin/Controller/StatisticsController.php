<?php

class StatisticsController extends AppController {

	public $uses = array('Client', 'Reservation','Prefecture','Landmark','Station','Area','Office','OfficeStation','CommissionRateHistory','SettlementCompany','CancelDetail','SettlementHistory','Recommend');
	public $components = array('TaxRate', 'SettlementData');

	public $areas = null;
	public $stationIds = null;
	public $clientList = null;

	public function beforeFilter() {
		parent::beforeFilter();

		$this->set('areaTypeList',$this->getAreaTypeList());

		$prefectureList = $this->Prefecture->getPrefectureList();
		$this->set('prefectureList',$prefectureList);

		$this->areas = $this->Area->getPrefectureKeyAreaList();
		$jsonAreas = json_encode($this->areas);
		$this->set('area_arr', $jsonAreas);

		if ($this->request->is('post') && empty($this->request->data['Reservation']['settlement'])) {
			$prefectureId = $this->request->data['Reservation']['prefectureList'];
		} else {
			$prefectureId = 1;
		}

		$areaList = array('0' => 'すべてのエリア');
		foreach($this->areas[$prefectureId] as $k => $v){
			$areaList[$k] = $v;
		}

		$this->set('areaList',$areaList);

		$regionList = array_merge(array('' => 'すべてのエリア'), Constant::regions());
		$this->set('regionList',$regionList);

		$airportList = $this->Landmark->getPrefectureAirportList();
		$airportList = array_merge(array('0' => 'すべての空港'), $airportList);
		$this->set('airportList',$airportList);

		$this->stationIds = array();
		$stationList = $this->Station->getPrefectureMainStationList();
		foreach($stationList as $prefecture){
			foreach($prefecture as $stationId => $station){
				$this->stationIds[] = $stationId;
			}
		}
		$stationList = array_merge(array('0' => 'すべての主要駅'), $stationList);
		$this->set('stationList',$stationList);

		$this->Client->recursive = -1;
		$fields = array('id','name','commission_rate');
		$clientList = $this->Client->find('all',array('fields' => $fields));

		$this->clientList = $clientList;
	}

	private function getAreaTypeList(){
		$items = array();
		$items[] = '';
		$items[] = '都道府県';
		$items[] = 'エリア';
		$items[] = '空港';
		$items[] = '駅';
		return $items;
	}

	private function getSortList($function){
		$items = array();
		$items[] = '';
		if($function == 'sales_summary'){
			$items[] = '成約数';
			$items[] = '確定売上';
			$items[] = '予約単価';

		} elseif($function == 'cancel_summary'){
			$items[] = 'キャンセル数';
			$items[] = 'キャンセル率';
			$items[] = '見込売上';
			$items[] = '見込予約単価';

		} elseif($function == 'reservation_summary'){
			$items[] = '予約獲得数';
			$items[] = '見込売上';
			$items[] = '予約単価';
		}
		return $items;
	}

	private function getFiltredOfficeIds($areaType){

		$officeIds = array();
		$recursive = -1;
		$conditions = array();

		if($areaType == 1){
			$fields = array('DISTINCT Office.id');
			$prefectureId = $this->request->data['Reservation']['prefectureList'];

			if(!empty($this->request->data['Reservation']['areaList'])){
				$areaIds = $this->request->data['Reservation']['areaList'];
			} else {
				$areaIds = array_keys($this->areas[$prefectureId]);
			}

			$conditions['Office.area_id'] = $areaIds;
			$options = compact('fields','conditions','recursive');
			$offices = $this->Office->find('all',$options);
			if(empty($offices)){
				$officeIds = array(0);
			} else {
				$officeIds = Hash::extract($offices, '{n}.Office.id');
			}
			

		} elseif($areaType == 2) {
			$fields = array('DISTINCT Office.id');
			$joins = array(
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.id = Office.area_id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => 'Prefecture.id = Area.prefecture_id'
				)
			);
			$regionLinkCd = $this->request->data['Reservation']['regionList'];

			if (!empty($regionLinkCd)) {
				$conditions['Prefecture.region_link_cd'] = $regionLinkCd;
			} else {
				$conditions['Prefecture.region_link_cd'] = array_keys(Constant::regions());
			}
			$options = compact('fields','joins','conditions','recursive');
			$offices = $this->Office->find('all',$options);

			if(empty($offices)){
				$officeIds = array(0);
			} else {
				$officeIds = Hash::extract($offices, '{n}.Office.id');
			}

		} elseif($areaType == 3) {
			$fields = array('DISTINCT Office.id');
			$aiportId = $this->request->data['Reservation']['airportList'];
			
			if(!empty($aiportId)){
				$conditions['Office.airport_id'] = $aiportId;
				
			} else {
				$conditions[] = 'Office.airport_id IS NOT NULL';
			}
			$options = compact('fields','conditions','recursive');
			$offices = $this->Office->find('all',$options);
			
			if(empty($offices)){
				$officeIds = array(0);
			} else {
				$officeIds = Hash::extract($offices, '{n}.Office.id');
			}

		} elseif($areaType == 4) {
			$fields = array('DISTINCT OfficeStation.office_id');

			$stationId = $this->request->data['Reservation']['stationList'];
			
			if(!empty($stationId)){
				$conditions['OfficeStation.station_id'] = $stationId;
			} else {
				$conditions['OfficeStation.station_id'] = $this->stationIds;
			}
			$options = compact('fields','conditions','recursive');
			$offices = $this->OfficeStation->find('all',$options);
			
			if(empty($offices)){
				$officeIds = array(0);
			} else {
				$officeIds = Hash::extract($offices, '{n}.OfficeStation.office_id');
			}

		}
		return $officeIds;
	}

	// 売上集計
	public function sales_summary() {

		$this->set('sortList',$this->getSortList(__FUNCTION__));

		if ($this->request->is('post')) {
			$year = $this->request->data['Reservation']['date']['year'];
			$month = $this->request->data['Reservation']['date']['month'];
			$day = $this->request->data['Reservation']['date']['day'];
			$sortList = $this->request->data['Reservation']['sortList'];
			$sortType = $this->request->data['Reservation']['sortType'];
			$areaType = $this->request->data['Reservation']['areaTypeList'];
		} else {
			$year = $this->request->data['Reservation']['date']['year'] = date('Y');
			$month = $this->request->data['Reservation']['date']['month'] = date('m');
			$day = $this->request->data['Reservation']['date']['day'] = date('d');
			$sortList = 0;
			$sortType = 0;
			$areaType = 0;
		}

		// 成約・予約データ取得
		$this->Reservation->recursive = -1;

		// エリアに対する店舗IDを取得
		$officeIds = array();
		if(!empty($areaType)){
			$officeIds = $this->getFiltredOfficeIds($areaType);
		}

		if (empty($this->request->data['Reservation']['segment'])) {
			// 月別
			$proceeds = $this->Reservation->getProceeds($year, '', $officeIds, true);
			$commissionRates = $this->SettlementData->getCommissionRates($year, '');
			$period = array('year'=> $year, 'month' => '', 'day'=> '');
			$type = "monthly";
		} else if ($this->request->data['Reservation']['segment'] == '1') {
			// 日別
			$proceeds = $this->Reservation->getProceeds($year, $month, $officeIds, true);
			$commissionRates = $this->SettlementData->getCommissionRates($year, $month);
			$period = array('year'=> $year, 'month' => $month, 'day'=> '');
			$type = "daily";
		}
		

		$data = $this->__formatData($proceeds,$period,$commissionRates,__FUNCTION__,'',$year);
		$data['function'] = __FUNCTION__;
		$data['type'] = $type;

		$this->set('data', $data);
		$clientList = $this->clientList;
		$clientList = $this->__sortClient($data,$clientList,$sortList,$sortType);
		
		$this->set('clientList', $clientList);

		if(!empty($this->request->data['getCsv'])) {
			$this->__downloadCsvData($data,$clientList);
		}
	}

	// キャンセル数集計
	public function cancel_summary() {

		$this->set('sortList',$this->getSortList(__FUNCTION__));

		if ($this->request->is('post')) {
			$year = $this->request->data['Reservation']['date']['year'];
			$month = $this->request->data['Reservation']['date']['month'];
			$day = $this->request->data['Reservation']['date']['day'];
			$sortList = $this->request->data['Reservation']['sortList'];
			$sortType = $this->request->data['Reservation']['sortType'];
			$areaType = $this->request->data['Reservation']['areaTypeList'];
		} else {
			$year = $this->request->data['Reservation']['date']['year'] = date('Y');
			$month = $this->request->data['Reservation']['date']['month'] = date('m');
			$day = $this->request->data['Reservation']['date']['day'] = date('d');
			$sortList = 0;
			$sortType = 0;
			$areaType = 0;
		}

		// 成約・予約データ取得
		$this->Reservation->recursive = -1;

		// エリアに対する店舗IDを取得
		$officeIds = array();
		if(!empty($areaType)){
			$officeIds = $this->getFiltredOfficeIds($areaType);
		}

		if (empty($this->request->data['Reservation']['segment'])) {
			// 月別
			$proceeds = $this->Reservation->getProceeds($year, '', $officeIds, true);
			$commissionRates = $this->SettlementData->getCommissionRates($year, '');
			$period = array('year'=> $year, 'month' => '', 'day'=> '');
			$type = "monthly";
		} else if ($this->request->data['Reservation']['segment'] == '1') {
			// 日別
			$proceeds = $this->Reservation->getProceeds($year, $month, $officeIds, true);
			$commissionRates = $this->SettlementData->getCommissionRates($year, $month);
			$period = array('year'=> $year, 'month' => $month, 'day'=> '');
			$type = "daily";
		}

		$data = $this->__formatData($proceeds,$period,$commissionRates,__FUNCTION__,'',$year);
		$data['function'] = __FUNCTION__;
		$data['type'] = $type;

		$this->set('data', $data);
		$clientList = $this->clientList;
		$clientList = $this->__sortClient($data,$clientList,$sortList,$sortType);
		$this->set('clientList', $clientList);

		if(!empty($this->request->data['getCsv'])) {
			$this->__downloadCsvData($data,$clientList);
		}
	}

	// 予約獲得数集計
	public function reservation_summary() {

		$this->set('sortList',$this->getSortList(__FUNCTION__));

		if ($this->request->is('post')) {
			$year = $this->request->data['Reservation']['date']['year'];
			$month = $this->request->data['Reservation']['date']['month'];
			$day = $this->request->data['Reservation']['date']['day'];
			$sortList = $this->request->data['Reservation']['sortList'];
			$sortType = $this->request->data['Reservation']['sortType'];
			$areaType = $this->request->data['Reservation']['areaTypeList'];
		} else {
			$year = $this->request->data['Reservation']['date']['year'] = date('Y');
			$month = $this->request->data['Reservation']['date']['month'] = date('m');
			$day = $this->request->data['Reservation']['date']['day'] = date('d');
			$sortList = 0;
			$sortType = 0;
			$areaType = 0;
		}

		// 予約データ取得
		$this->Reservation->recursive = -1;

		// エリアに対する店舗IDを取得
		$officeIds = array();
		if(!empty($areaType)){
			$officeIds = $this->getFiltredOfficeIds($areaType);
		}

		if (empty($this->request->data['Reservation']['segment'])) {
			// 月別
			$proceeds = $this->Reservation->getReservedOperand($year, '', '', $officeIds);
			$commissionRates = $this->SettlementData->getCommissionRates($year, '');
			$period = array('year'=> $year, 'month' => '', 'day'=> '');
			$type = "monthly";
		} else if ($this->request->data['Reservation']['segment'] == '1') {
			// 日別
			$proceeds = $this->Reservation->getReservedOperand($year, $month, '', $officeIds);
			$commissionRates = $this->SettlementData->getCommissionRates($year, $month);
			$period = array('year'=> $year, 'month' => $month, 'day'=> '');
			$type = "daily";
		} else if ($this->request->data['Reservation']['segment'] == '2') {
			// 時間別
			$proceeds = $this->Reservation->getReservedOperand($year, $month, $day, $officeIds);
			$commissionRates = $this->SettlementData->getCommissionRates($year, $month);
			$period = array('year'=> $year, 'month' => $month, 'day'=> '');
			$type = "hourly";
		}

		$data = $this->__formatData($proceeds,$period,$commissionRates,__FUNCTION__,'booking',$year);
		$data['function'] = __FUNCTION__;
		$data['type'] = $type;

		$this->set('data', $data);
		$clientList = $this->clientList;
		$clientList = $this->__sortClient($data,$clientList,$sortList,$sortType);
		$this->set('clientList', $clientList);

		if(!empty($this->request->data['getCsv'])) {
			$this->__downloadCsvData($data,$clientList);
		}
	}

	private function __sortClient($data,$clientList,$sortList,$sortType){
		$tempData = $data;
		
		$clientMap = array();
	
		if(!empty($sortList)){
			
			if($data['function'] == 'sales_summary'){
				if($sortList == 1){ //成約数
					$item = array('agreement','count');
				} elseif($sortList == 2){ //確定売上
					$item = array('agreement','price');
				} elseif($sortList == 3){ //予約単価
					$item = array('agreement','avg_price_count');
				}
				

			} elseif($data['function'] == 'cancel_summary'){
				if($sortList == 1){ //キャンセル数
					$item = array('cancel','count');
				} elseif($sortList == 2){ //キャンセル率
					$item = array('cancel','rate_cancel');
				} elseif($sortList == 3){ //見込売上
					$item = array('cancel','price');
				} elseif($sortList == 4){ //見込予約単価
					$item = array('cancel','avg_price_count');
				}

			} elseif($data['function'] == 'reservation_summary'){
				if($sortList == 1){ //予約獲得数
					$item = array('booking','count');
				} elseif($sortList == 2){ //見込売上
					$item = array('booking','price');
				} elseif($sortList == 3){ //予約単価
					$item = array('booking','avg_price_count');
				}
			}

			foreach($tempData as $clientId => $clientData){
				if(is_numeric($clientId) && !empty($clientId)){
					foreach($clientData as $date => $dateData){
						if($date == 'all'){
							foreach($dateData as $status => $value){
								if($status == $item[0]){
									$clientMap[$clientId] = floatval($data[$clientId][$date][$status][$item[1]]);;
								}
							}
						}
					}
				}
			}

			if($sortType == 2){
				arsort($clientMap);
			} else {
				asort($clientMap);
			}

			$newClientList = array();
			foreach($clientMap as $clientId => $value){
				foreach($clientList as $client){
					if($client['Client']['id'] == $clientId){
						$newClientList[] = $client;
					}
				}
			}
			foreach($clientList as $client){
				if(!array_key_exists($client['Client']['id'], $clientMap)){
					$newClientList[] = $client;
				}
			}
			
			return $newClientList;
		}
		
		return $clientList;
	}

	private function __downloadCsvData($data,$clientList){
		
		Configure::write('debug',0); // debugコードを出さない
		$this->autoRender = false; // Viewを使わない
		$csvFile = date('YmdHis').'_'.$data['function'].'_'.$data['type'].'.csv';
		
		// ヘッダ出力
		header("Content-disposition: attachment; filename=" . $csvFile);
		header("Content-type: application/octet-stream; name=" . $csvFile);

		// ストリーム出力
		$fp = @fopen('php://output', 'w');
		if (!$fp) {
			exit;
		}
		
		// SJIS指定
		stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');
		
		$year = $this->request->data['Reservation']['date']['year'];
		$month = $this->request->data['Reservation']['date']['month'];
		$day = $this->request->data['Reservation']['date']['day'];
		$lastDay = date("t", mktime(0, 0, 0, $month, 1, $year));
		$week = array("日", "月", "火", "水", "木", "金", "土");

		$header = '';
		$i = 1;
		$max = 0;
		if($data['type'] == 'monthly'){
			$istart = 1;
			$max = 12;
			for ($i = $istart; $i <= $max; $i++) {
				$header .= ','.$year.'/'.$i;
			}
		} elseif($data['type'] == 'daily'){
			$istart = 1;
			$max = $lastDay;
			for ($i = $istart; $i <= $max; $i++) {
				$header .= ','.$year.'/'.$month.'/'.$i;
			}
		} elseif($data['type'] == 'hourly'){
			$istart = 0;
			$max = 23;
			for ($i = $istart; $i <= $max; $i++) {
				$header .= ','.$year.'/'.$month.'/'.$day.' '.$i.':00';
			}
		}

		$items = array();
		if($data['type'] == 'monthly'){
			if($data['function'] == 'sales_summary'){
				$items[] = array('予約数','booking','count',0);
				$items[] = array('見込売上','expected_revenue','price',0);
				$items[] = array('成約数','agreement','count',0);
				$items[] = array('ー前年比(%)','agreement','year_count',3);
				$items[] = array('ー前月比(%)','agreement','month_count',3);
				$items[] = array('件数シェア','agreement','rate_count',3);
				$items[] = array('確定売上','agreement','price',0);
				$items[] = array('ー前年比(%)','agreement','year_price',3);
				$items[] = array('ー前月比(%)','agreement','month_price',3);
				$items[] = array('売上シェア','agreement','rate_price',3);
				$items[] = array('予約単価','agreement','avg_price_count',0);
				$items[] = array('ー前年比(%)','agreement','year_avg_price_count',3);
				$items[] = array('ー前月比(%)','agreement','month_avg_price_count',3);
				$items[] = array('粗利','agreement','commission',0);
				$items[] = array('ー前年比(%)','agreement','year_commission',3);
				$items[] = array('ー前月比(%)','agreement','month_commission',3);
	
			} elseif($data['function'] == 'cancel_summary'){
				$items[] = array('キャンセル数','cancel','count',0);
				$items[] = array('ー前年比(%)','cancel','year_count',3);
				$items[] = array('ー前月比(%)','cancel','month_count',3);
				$items[] = array('キャンセル率','cancel','rate_cancel',3);
				$items[] = array('ー前年比(pt)','cancel','year_rate_cancel',3);
				$items[] = array('ー前月比(pt)','cancel','month_rate_cancel',3);
				$items[] = array('見込売上','cancel','price',0);
				$items[] = array('ー前年比(%)','cancel','year_price',3);
				$items[] = array('ー前月比(%)','cancel','month_price',3);
				$items[] = array('見込予約単価','cancel','avg_price_count',0);
				$items[] = array('ー前年比(%)','cancel','year_avg_price_count',3);
				$items[] = array('ー前月比(%)','cancel','month_avg_price_count',3);
				$items[] = array('見込粗利','cancel','commission',0);
				$items[] = array('ー前年比(%)','cancel','year_commission',3);
				$items[] = array('ー前月比(%)','cancel','month_commission',3);
	
			} elseif($data['function'] == 'reservation_summary'){
				$items[] = array('予約獲得数','booking','count',0);
				$items[] = array('ー前年比(%)','booking','year_count',3);
				$items[] = array('ー前月比(%)','booking','month_count',3);
				$items[] = array('件数シェア','booking','rate_count',3);
				$items[] = array('見込売上','booking','price',0);
				$items[] = array('ー前年比(%)','booking','year_price',3);
				$items[] = array('ー前月比(%)','booking','month_price',3);
				$items[] = array('売上シェア','booking','rate_price',3);
				$items[] = array('予約単価','booking','avg_price_count',0);
				$items[] = array('ー前年比(%)','booking','year_avg_price_count',3);
				$items[] = array('ー前月比(%)','booking','month_avg_price_count',3);
				$items[] = array('見込粗利','booking','commission',0);
				$items[] = array('ー前年比(%)','booking','year_commission',3);
				$items[] = array('ー前月比(%)','booking','month_commission',3);
	
			}
		}else{
			if($data['function'] == 'sales_summary'){
				$items[] = array('予約数','booking','count',0);
				$items[] = array('見込売上','expected_revenue','price',0);
				$items[] = array('成約数','agreement','count',0);
				$items[] = array('件数シェア','agreement','rate_count',3);
				$items[] = array('確定売上','agreement','price',0);
				$items[] = array('売上シェア','agreement','rate_price',3);
				$items[] = array('予約単価','agreement','avg_price_count',0);
				$items[] = array('粗利','agreement','commission',0);

			} elseif($data['function'] == 'cancel_summary'){
				$items[] = array('キャンセル数','cancel','count',0);
				$items[] = array('キャンセル率','cancel','rate_cancel',3);
				$items[] = array('見込売上','cancel','price',0);
				$items[] = array('見込予約単価','cancel','avg_price_count',0);
				$items[] = array('見込粗利','cancel','commission',0);

			} elseif($data['function'] == 'reservation_summary'){
				$items[] = array('予約獲得数','booking','count',0);
				$items[] = array('件数シェア','booking','rate_count',3);
				$items[] = array('見込売上','booking','price',0);
				$items[] = array('売上シェア','booking','rate_price',3);
				$items[] = array('予約単価','booking','avg_price_count',0);
				$items[] = array('見込粗利','booking','commission',0);

			}
		}
		
		//$jump = '<br>';
		$jump = "\r\n";
		$csvData = '会社名,項目,合計'.$header.$jump;
		//echo $csvData;
		fwrite($fp, $csvData);
		foreach($items as $item){
			$csvData = '全体'.','.$item[0];
			$value = number_format($data[0]['all'][$item[1]][$item[2]],$item[3],'.','');
			$csvData .= ','.$value;
			for ($i = $istart; $i <= $max; $i++) {
				$value = '';
				if(!empty($data[0][$i][$item[1]][$item[2]])){
					$value = number_format($data[0][$i][$item[1]][$item[2]],$item[3],'.','');
				}
				$csvData .= ','.$value;
			}
			
			$csvData .= $jump;
			//echo $csvData;
			fwrite($fp, $csvData);
		}
		if($data['type'] == 'monthly'){
			if($data['function'] == 'sales_summary'){
				$items = array();
				$items[] = array('予約数','booking','count',0);
				$items[] = array('見込売上','expected_revenue','price',0);
				$items[] = array('成約数','agreement','count',0);
				$items[] = array('ー前年比(%)','agreement','year_count',3);
				$items[] = array('ー前月比(%)','agreement','month_count',3);
				$items[] = array('件数シェア','agreement','rate_count',3);
				$items[] = array('ー前年比(pt)','agreement','year_rate_count',3);
				$items[] = array('ー前月比(pt)','agreement','month_rate_count',3);
				$items[] = array('確定売上','agreement','price',0);
				$items[] = array('ー前年比(%)','agreement','year_price',3);
				$items[] = array('ー前月比(%)','agreement','month_price',3);
				$items[] = array('売上シェア','agreement','rate_price',3);
				$items[] = array('ー前年比(pt)','agreement','year_rate_price',3);
				$items[] = array('ー前月比(pt)','agreement','month_rate_price',3);
				$items[] = array('予約単価','agreement','avg_price_count',0);
				$items[] = array('ー前年比(%)','agreement','year_avg_price_count',3);
				$items[] = array('ー前月比(%)','agreement','month_avg_price_count',3);
				$items[] = array('粗利','agreement','commission',0);
				$items[] = array('ー前年比(%)','agreement','year_commission',3);
				$items[] = array('ー前月比(%)','agreement','month_commission',3);
	
			} elseif($data['function'] == 'reservation_summary'){
				$items = array();
				$items[] = array('予約獲得数','booking','count',0);
				$items[] = array('ー前年比(%)','booking','year_count',3);
				$items[] = array('ー前月比(%)','booking','month_count',3);
				$items[] = array('件数シェア','booking','rate_count',3);
				$items[] = array('ー前年比(pt)','booking','year_rate_count',3);
				$items[] = array('ー前月比(pt)','booking','month_rate_count',3);
				$items[] = array('見込売上','booking','price',0);
				$items[] = array('ー前年比(%)','booking','year_price',3);
				$items[] = array('ー前月比(%)','booking','month_price',3);
				$items[] = array('売上シェア','booking','rate_price',3);
				$items[] = array('ー前年比(pt)','booking','year_rate_price',3);
				$items[] = array('ー前月比(pt)','booking','month_rate_price',3);
				$items[] = array('予約単価','booking','avg_price_count',0);
				$items[] = array('ー前年比(%)','booking','year_avg_price_count',3);
				$items[] = array('ー前月比(%)','booking','month_avg_price_count',3);
				$items[] = array('見込粗利','booking','commission',0);
				$items[] = array('ー前年比(%)','booking','year_commission',3);
				$items[] = array('ー前月比(%)','booking','month_commission',3);
	
			}
		}
		foreach ($clientList as $key => $clientData) {

			$clientId = $clientData['Client']['id'];
			$clientName = $clientData['Client']['name'];
			$commissionRate = $clientData['Client']['commission_rate'];
			if($clientId == 1){
				continue;
			}
			foreach($items as $item){
				$csvData = $clientName.','.$item[0];

				$value = '';
				if(array_key_exists($clientId, $data)){
					if(array_key_exists($item[1],$data[$clientId]['all'])){
						$value = number_format($data[$clientId]['all'][$item[1]][$item[2]],$item[3],'.','');
					}
				}

				$csvData .= ','.$value;
				for ($i = $istart; $i <= $max; $i++) {
					$value = '';
					if(!empty($data[$clientId][$i][$item[1]][$item[2]])){
						$value = number_format($data[$clientId][$i][$item[1]][$item[2]],$item[3],'.','');
					}
					$csvData .= ','.$value;
				}
				
				$csvData .= $jump;
				//echo $csvData;
				fwrite($fp, $csvData);
			}
		}

		fclose($fp);
		exit();
	}

	//取得したデータを集計する
	private function __formatData($proceeds,$period,$commissionRates,$function,$param_status = '',$year = '') {
		
		$data = array();
		foreach ($proceeds as $key => $val) {

			$clientId = $val['Reservation']['client_id'];
			$settlementCompanyId = $val['Office']['settlement_company_id'];
			if(isset($val[0]['Ydate']) && strpos($val[0]['Ydate'],$year) === false){
				$date = $val[0]['Ydate'];
			}else{
				$date = $val[0]['date'];
			}

			if(empty($param_status)){
				if ($val['Reservation']['reservation_status_id'] == 0) {
					continue;
					//予約
				} else if ($val['Reservation']['reservation_status_id'] == 1) {
					$status = 'booking';
					//成約
				} else if ($val['Reservation']['reservation_status_id'] == 2) {
					$status = 'agreement';
					//キャンセル
				} else if ($val['Reservation']['reservation_status_id'] == 3) {
					$status = 'cancel';
				}
			} else {
				if ($val['Reservation']['reservation_status_id'] == 0) {
					continue;
					//予約
				} else {
					$status = $param_status;
				}
			}

			/**
			 * 全社の合計(見込み売上 & 予約数)
			 */
			//合計 見込み売上初期化
			if (empty($data['0'][$date]['expected_revenue']['price']))
				$data['0'][$date]['expected_revenue']['price'] = 0;
			if (empty($data['0'][$date]['expected_revenue']['count']))
				$data['0'][$date]['expected_revenue']['count'] = 0;

			//期間の合計初期化
			if (empty($data['0']['all']['expected_revenue']['price']))
				$data['0']['all']['expected_revenue']['price'] = 0;
			if (empty($data['0']['all']['expected_revenue']['count']))
				$data['0']['all']['expected_revenue']['count'] = 0;

			//IDリスト初期化(デバッグ用)
			if (empty($data['0'][$date]['expected_revenue']['reservation_ids']))
				$data['0'][$date]['expected_revenue']['reservation_ids'] = '';

			//合計見込み売上 予約分のみ
			if ($status == 'booking') {
				$data['0'][$date]['expected_revenue']['price'] += $val[0]['price'];
				$data['0'][$date]['expected_revenue']['count'] += $val[0]['count'];
				if(!isset($val[0]['Ydate']) || strpos($val[0]['Ydate'],$year) !== false){
					$data['0']['all']['expected_revenue']['price'] += $val[0]['price'];
					$data['0']['all']['expected_revenue']['count'] += $val[0]['count'];
				}

				if(isset($val[0]['reservation_ids'])){
					$data['0'][$date]['expected_revenue']['reservation_ids'] .= $val[0]['reservation_ids'] . ',';
				}
			}

			//合計 成約数 & 予約数 & 確定売上 & 見込み売上 初期化
			if (empty($data['0'][$date][$status]['price']))
				$data['0'][$date][$status]['price'] = 0;
			if (empty($data['0'][$date][$status]['count']))
				$data['0'][$date][$status]['count'] = 0;
			if (empty($data['0'][$date][$status]['commission']))
				$data['0'][$date][$status]['commission'] = '';
			if (empty($data['0'][$date][$status]['reservation_ids']))
				$data['0'][$date][$status]['reservation_ids'] = '';
			if (empty($data['0'][$date][$status]['local_pay']))
				$data['0'][$date][$status]['local_pay'] = 0;
			if (empty($data['0'][$date][$status]['local_amount']))
				$data['0'][$date][$status]['local_amount'] = 0;

			//期間の合計 初期化
			if (empty($data['0']['all'][$status]['price']))
				$data['0']['all'][$status]['price'] = 0;
			if (empty($data['0']['all'][$status]['count']))
				$data['0']['all'][$status]['count'] = 0;
			if (empty($data['0']['all'][$status]['commission']))
				$data['0']['all'][$status]['commission'] = 0;
			if (empty($data['0']['all'][$status]['rate_count']))
				$data['0']['all'][$status]['rate_count'] = 100;
			if (empty($data['0']['all'][$status]['rate_price']))
				$data['0']['all'][$status]['rate_price'] = 100;

			$data['0'][$date][$status]['price'] += $val[0]['price'];
			$data['0'][$date][$status]['count'] += $val[0]['count'];
			if(!isset($val[0]['Ydate']) || strpos($val[0]['Ydate'],$year) !== false){
				$data['0']['all'][$status]['price'] += $val[0]['price'];
				$data['0']['all'][$status]['count'] += $val[0]['count'];
			}else{
				$data['0']['b_all'][$status]['price'] += $val[0]['price'];
				$data['0']['b_all'][$status]['count'] += $val[0]['count'];
			}

			$rate = $this->SettlementData->__getCommissionRate($clientId, $settlementCompanyId,$commissionRates,$period,$date);

			$rate = $rate / 100;
			$commission = floor($val[0]['price'] * $rate);

			$data['0'][$date][$status]['commission'] += $commission;
			if(!isset($val[0]['Ydate']) || strpos($val[0]['Ydate'],$year) !== false){
				$data['0']['all'][$status]['commission'] += $commission;
			}else{
				$data['0']['b_all'][$status]['commission'] += $commission;
			}

			if(isset($val[0]['reservation_ids'])){
				$data['0'][$date][$status]['reservation_ids'] .= $val[0]['reservation_ids'] . ',';
			}

			// 現地精算 - 成約件数
			$data['0'][$date][$status]['local_pay'] += isset($val[0]['local_pay']) ? $val[0]['local_pay'] : 0;

			// 現地精算 - 成約金額
			$data['0'][$date][$status]['local_amount'] += isset($val[0]['local_amount']) ? $val[0]['local_amount'] : 0;

			/**
			 * クライアント別見込み売上初期化
			 */
			if (empty($data[$clientId][$date]['expected_revenue']['price']))
				$data[$clientId][$date]['expected_revenue']['price'] = 0;
			if (empty($data[$clientId][$date]['expected_revenue']['count']))
				$data[$clientId][$date]['expected_revenue']['count'] = 0;
			if (empty($data[$clientId][$date]['expected_revenue']['reservation_ids']))
				$data[$clientId][$date]['expected_revenue']['reservation_ids'] = '';

			//全合計
			if (empty($data[$clientId]['all']['expected_revenue']['price']))
				$data[$clientId]['all']['expected_revenue']['price'] = 0;
			if (empty($data[$clientId]['all']['expected_revenue']['count']))
				$data[$clientId]['all']['expected_revenue']['count'] = 0;

			// クライアント別見込み売上 予約分のみ
			if ($status == 'booking') {
				$data[$clientId][$date]['expected_revenue']['price'] += $val[0]['price'];
				$data[$clientId][$date]['expected_revenue']['count'] += $val[0]['count'];
				if(!isset($val[0]['Ydate']) || strpos($val[0]['Ydate'],$year) !== false){
					$data[$clientId]['all']['expected_revenue']['price'] += $val[0]['price'];
					$data[$clientId]['all']['expected_revenue']['count'] += $val[0]['count'];
				}else{
					$data[$clientId]['b_all']['expected_revenue']['price'] += $val[0]['price'];
					$data[$clientId]['b_all']['expected_revenue']['count'] += $val[0]['count'];
				}

				if(isset($val[0]['reservation_ids'])){
					$data[$clientId][$date]['expected_revenue']['reservation_ids'] .= $val[0]['reservation_ids'] . ',';
				}
			}

			/**
			 * 成約データ & 予約データ
			 */
			if(!isset($data[$clientId][$date][$status]['price'])){
				$data[$clientId][$date][$status]['price'] = 0;
			}
			if(!isset($data[$clientId]['all'][$status]['price'])){
				$data[$clientId]['all'][$status]['price'] = 0;
			}
			if(!isset($data[$clientId][$date][$status]['count'])){
				$data[$clientId][$date][$status]['count'] = 0;
			}
			if(!isset($data[$clientId]['all'][$status]['count'])){
				$data[$clientId]['all'][$status]['count'] = 0;
			}
			if(!isset($data[$clientId][$date][$status]['commission'])){
				$data[$clientId][$date][$status]['commission'] = 0;
			}
			if(!isset($data[$clientId]['all'][$status]['commission'])){
				$data[$clientId]['all'][$status]['commission'] = 0;
			}
			$data[$clientId][$date][$status]['price'] += $val[0]['price'];
			$data[$clientId][$date][$status]['count'] += $val[0]['count'];

			$data[$clientId][$date][$status]['commission'] += $commission;

			if(!isset($val[0]['Ydate']) || strpos($val[0]['Ydate'],$year) !== false){
				$data[$clientId]['all'][$status]['price'] += $val[0]['price'];
				$data[$clientId]['all'][$status]['count'] += $val[0]['count'];

				$data[$clientId]['all'][$status]['commission'] += $commission;
			}else{
				$data[$clientId]['b_all'][$status]['price'] += $val[0]['price'];
				$data[$clientId]['b_all'][$status]['count'] += $val[0]['count'];

				$data[$clientId]['b_all'][$status]['commission'] += $commission;
			}

			$data[$clientId][$date][$status]['avg_price_count'] = $data[$clientId][$date][$status]['price'] / $data[$clientId][$date][$status]['count'];

			/**
			 * 予約ID　確認用
			 */
			if(isset($val[0]['reservation_ids'])){
				$data[$clientId][$date][$status]['reservation_ids'] = $val[0]['reservation_ids'];
			}
		}

		$tempData = $data;
		foreach($tempData as $clientId => $clientData){
			if($clientId != '0'){
				foreach($clientData as $date => $dateData){
					if(($date != 'all') && ($date != 'b_all')){
						foreach($dateData as $status => $value){
							if($status == 'agreement' || $status == 'cancel' || $status == 'booking'){
								$data[$clientId][$date][$status]['rate_count'] = $data[$clientId][$date][$status]['count'] / $data['0'][$date][$status]['count'] * 100;

								$data[$clientId][$date][$status]['rate_price'] = $data[$clientId][$date][$status]['price'] / $data['0'][$date][$status]['price'] * 100;

								$data[$clientId]['all'][$status]['avg_price_count'] = $data[$clientId]['all'][$status]['price'] / $data[$clientId]['all'][$status]['count'];

								$data[$clientId]['all'][$status]['rate_count'] = $data[$clientId]['all'][$status]['count'] / $data['0']['all'][$status]['count'] * 100;

								$data[$clientId]['all'][$status]['rate_price'] = $data[$clientId]['all'][$status]['price'] / $data['0']['all'][$status]['price'] * 100;	

								$data[$clientId]['b_all'][$status]['avg_price_count'] = $data[$clientId]['b_all'][$status]['price'] / $data[$clientId]['b_all'][$status]['count'];
								$data[$clientId]['b_all'][$status]['rate_count'] = $data[$clientId]['b_all'][$status]['count'] / $data['0']['b_all'][$status]['count'] * 100;
								$data[$clientId]['b_all'][$status]['rate_price'] = $data[$clientId]['b_all'][$status]['price'] / $data['0']['b_all'][$status]['price'] * 100;	
							}
						}
					}
				}
			} else {
				foreach($clientData as $date => $dateData){
					if($date != 'all'){
						foreach($dateData as $status => $value){
							if($status == 'agreement' || $status == 'cancel' || $status == 'booking'){
								$data['0'][$date][$status]['rate_count'] = 100;

								$data['0'][$date][$status]['rate_price'] = 100;

								$data['0'][$date][$status]['avg_price_count'] = $data['0'][$date][$status]['price'] / $data['0'][$date][$status]['count'];

								$data['0']['all'][$status]['avg_price_count'] = $data['0']['all'][$status]['price'] / $data['0']['all'][$status]['count'];
							}
						}
					}
				}
			}
		}

		$tempData = $data;
		foreach($tempData as $clientId => $clientData){
			foreach($clientData as $date => $dateData){
				
				if(array_key_exists('cancel', $data[$clientId][$date])) {
					$data[$clientId][$date]['cancel']['rate_cancel'] = 0;

					if(array_key_exists('agreement', $data[$clientId][$date])){

						$data[$clientId][$date]['cancel']['rate_cancel'] = $data[$clientId][$date]['cancel']['count'] / ($data[$clientId][$date]['agreement']['count'] + $data[$clientId][$date]['cancel']['count']) * 100;
					}
				}
			}
		}
		//前年前月比較
		foreach($tempData as $clientId => $clientData){
			foreach($clientData as $date => $dateData){
				if($date == "b_all"){
					continue;
				}
				if($date != "all"){
					$Ydate = ($year-1).$date;
					$Mdate = $date-1;
					if($Mdate == 0){
						$Mdate = ($year-1).'12';
					}
				}
				foreach($dateData as $status => $value){
					if($function == "sales_summary" && $status != "agreement"){
						continue;
					}elseif($function == "cancel_summary" && $status != "cancel"){
						continue;
					}elseif($function == "reservation_summary" && $status != "booking"){
						continue;
					}
					if($date == 'all'){
						$data[$clientId]['all'][$status]['year_count'] = $data[$clientId]['all'][$status]['count'] / $data[$clientId]['b_all'][$status]['count'] * 100;
						$data[$clientId]['all'][$status]['month_count'] = '';
						if(!empty($data[$clientId]['all'][$status]['rate_count']) && !empty($data[$clientId]['b_all'][$status]['rate_count'])){
							$data[$clientId]['all'][$status]['year_rate_count'] = $data[$clientId]['all'][$status]['rate_count'] - $data[$clientId]['b_all'][$status]['rate_count'];
						}else{
							$data[$clientId]['all'][$status]['year_rate_count'] = '';
						}
						$data[$clientId]['all'][$status]['month_rate_count'] = '';
						$data[$clientId]['all'][$status]['year_price'] = $data[$clientId]['all'][$status]['price'] / $data[$clientId]['b_all'][$status]['price'] * 100;
						$data[$clientId]['all'][$status]['month_price'] = '';
						if(!empty($data[$clientId]['all'][$status]['rate_price']) && !empty($data[$clientId]['b_all'][$status]['rate_price'])){
							$data[$clientId]['all'][$status]['year_rate_price'] = $data[$clientId]['all'][$status]['rate_price'] - $data[$clientId]['b_all'][$status]['rate_price'];
						}else{
							$data[$clientId]['all'][$status]['year_rate_price'] = '';
						}
						$data[$clientId]['all'][$status]['month_rate_price'] = '';
						$data[$clientId]['all'][$status]['year_avg_price_count'] = $data[$clientId]['all'][$status]['avg_price_count'] / $data[$clientId]['b_all'][$status]['avg_price_count'] * 100;
						$data[$clientId]['all'][$status]['month_avg_price_count'] = '';
						$data[$clientId]['all'][$status]['year_commission'] = $data[$clientId]['all'][$status]['commission'] / $data[$clientId]['b_all'][$status]['commission'] * 100;
						$data[$clientId]['all'][$status]['month_commission'] = '';
						if(!empty($data[$clientId]['all'][$status]['rate_cancel']) && !empty($data[$clientId]['b_all'][$status]['rate_cancel'])){
							$data[$clientId]['all'][$status]['year_rate_cancel'] = $data[$clientId]['all'][$status]['rate_cancel'] - $data[$clientId]['b_all'][$status]['rate_cancel'];
						}else{
							$data[$clientId]['all'][$status]['year_rate_cancel'] = '';
						}
						$data[$clientId]['all'][$status]['month_rate_cancel'] = '';
					}else{
						$data[$clientId][$date][$status]['year_count'] = $this->__getRate($data[$clientId][$date][$status]['count'], $data[$clientId][$Ydate][$status]['count']);
						$data[$clientId][$date][$status]['month_count'] = $this->__getRate($data[$clientId][$date][$status]['count'], $data[$clientId][$Mdate][$status]['count']);
						if(!empty($data[$clientId][$date][$status]['rate_count']) && !empty($data[$clientId][$Ydate][$status]['rate_count'])){
							$data[$clientId][$date][$status]['year_rate_count'] = $data[$clientId][$date][$status]['rate_count'] - $data[$clientId][$Ydate][$status]['rate_count'];
						}else{
							$data[$clientId][$date][$status]['year_rate_count'] = '';
						}
						if(!empty($data[$clientId][$date][$status]['rate_count']) && !empty($data[$clientId][$Mdate][$status]['rate_count'])){
							$data[$clientId][$date][$status]['month_rate_count'] = $data[$clientId][$date][$status]['rate_count'] - $data[$clientId][$Mdate][$status]['rate_count'];
						}else{
							$data[$clientId][$date][$status]['month_rate_count'] = '';
						}
						$data[$clientId][$date][$status]['year_price'] = $this->__getRate($data[$clientId][$date][$status]['price'], $data[$clientId][$Ydate][$status]['price']);
						$data[$clientId][$date][$status]['month_price'] = $this->__getRate($data[$clientId][$date][$status]['price'], $data[$clientId][$Mdate][$status]['price']);
						if(!empty($data[$clientId][$date][$status]['rate_price']) && !empty($data[$clientId][$Ydate][$status]['rate_price'])){
							$data[$clientId][$date][$status]['year_rate_price'] = $data[$clientId][$date][$status]['rate_price'] - $data[$clientId][$Ydate][$status]['rate_price'];
						}else{
							$data[$clientId][$date][$status]['year_rate_price'] = '';
						}
						if(!empty($data[$clientId][$date][$status]['rate_price']) && !empty($data[$clientId][$Mdate][$status]['rate_price'])){
							$data[$clientId][$date][$status]['month_rate_price'] = $data[$clientId][$date][$status]['rate_price'] - $data[$clientId][$Mdate][$status]['rate_price'];
						}else{
							$data[$clientId][$date][$status]['month_rate_price'] = '';
						}
						$data[$clientId][$date][$status]['year_avg_price_count'] = $this->__getRate($data[$clientId][$date][$status]['avg_price_count'], $data[$clientId][$Ydate][$status]['avg_price_count']);
						$data[$clientId][$date][$status]['month_avg_price_count'] = $this->__getRate($data[$clientId][$date][$status]['avg_price_count'], $data[$clientId][$Mdate][$status]['avg_price_count']);
						$data[$clientId][$date][$status]['year_commission'] = $this->__getRate($data[$clientId][$date][$status]['commission'], $data[$clientId][$Ydate][$status]['commission']);
						$data[$clientId][$date][$status]['month_commission'] = $this->__getRate($data[$clientId][$date][$status]['commission'], $data[$clientId][$Mdate][$status]['commission']);

						if(!empty($data[$clientId][$date]['cancel']['rate_cancel']) && !empty($data[$clientId][$Ydate]['cancel']['rate_cancel'])){
							$data[$clientId][$date]['cancel']['year_rate_cancel'] = $data[$clientId][$date]['cancel']['rate_cancel'] - $data[$clientId][$Ydate]['cancel']['rate_cancel'];
						}else{
							$data[$clientId][$date]['cancel']['year_rate_cancel'] = '';
						}
						if(!empty($data[$clientId][$date]['cancel']['rate_cancel']) && !empty($data[$clientId][$Mdate]['cancel']['rate_cancel'])){
							$data[$clientId][$date]['cancel']['month_rate_cancel'] = $data[$clientId][$date]['cancel']['rate_cancel'] - $data[$clientId][$Mdate]['cancel']['rate_cancel'];
						}else{
							$data[$clientId][$date]['cancel']['month_rate_cancel'] = '';
						}
					}
				}
			}
		}
		return $data;
	}

	// 精算額集計
	public function settlement_summary() {
		$year = '';
		$month = '';
		$clientId = '';
		$settlementCompanyId = '';
		if (isset($this->request->data['Reservation'])) {
			$year = $this->request->data['Reservation']['date']['year'];
			$month = $this->request->data['Reservation']['date']['month'];
			$clientId = $this->request->data['Reservation']['clientList'];
			$settlementCompanyId = $this->request->data['Reservation']['settlementCompanyList'];
		}
		$getCsv = !empty($this->request->data['getCsv']);
		$settlementFinish = !empty($this->request->data['settlementFinish']);
		$defaultYear = date('Y', strtotime(date('Y-m-1') . '-1 month'));
		$defaultMonth = date('m', strtotime(date('Y-m-1') . '-1 month'));

		if (empty($year) || empty($month)) {
			$year = $defaultYear;
			$month = $defaultMonth;
		}

		$taxRate = $this->TaxRate->getConsumptionTaxRate($year, $month);

		// 精算完了日時取得
		list($finishedMonth, $finishedAt) = $this->SettlementData->__getSettlementFinished();

		// 予約データ取得
		$this->Reservation->recursive = -1;

		// TODO 2022/01/11 できれば該当月以外のデータは取らないようにしたい...
		$proceeds = $this->Reservation->getProceedsSettlement($year, '', array(), true);
		$commissionRates = $this->SettlementData->getCommissionRates($year);
		$period = array('year'=> $year, 'month' => '', 'day'=> '');

		// レコメンド
		$recommendSettlement = $this->SettlementData->__getRecommendSettlement($year, $month);

		// 精算管理会社単位で使う
		$dataSettles = $this->SettlementData->__formatDataSettlement($proceeds, $period, $commissionRates, $recommendSettlement, (int)$month);
		// 合計額を精算管理会社単位から計算する
		$data = $this->SettlementData->__aggregateDataSettlement($dataSettles);

		$clientList = $this->Client->find('list');

		$this->SettlementData->__addSettlementFormat($data, $dataSettles, (int)$month);

		$this->SettlementData->__addCancelDetailFormat($data, $dataSettles, $year, $month);

		$dataSettles['function'] = __FUNCTION__;

		if (!empty($getCsv)) {
			$this->__downloadCsvSettlement($dataSettles, $clientList, $year, (int)$month, $clientId, $settlementCompanyId);
		} else {
			if (!empty($settlementFinish)) {
				// 精算完了日時保存
				// 精算完了月<=指定月<=前月は完了可
				if ($finishedMonth <= $year.$month && $year.$month <= $defaultYear.$defaultMonth) {
					$this->SettlementHistory->save(array('settlement_month' => $year.$month));
				}
			}
			$this->set('data', $data);
			$this->set('dataSettles', $dataSettles);
			$this->set('clientList', $clientList);
			$this->set('settlementCompanyList', $this->SettlementCompany->find('list'));
			$this->set('year', $year);
			$this->set('month', (int)$month);
			$this->set('selectClientId', $clientId);
			$this->set('selectSettlementCompanyId', $settlementCompanyId);
			$this->set('taxRate', $taxRate);
			$this->set('finishedMonth', $finishedMonth);
		}
	}

	// 精算額集計(会社別集計 - csv)
	private function __downloadCsvSettlement($data, $clientList, $year, $month, $selectClientId, $selectSettlementCompanyId)
	{
		$taxRate = $this->TaxRate->getConsumptionTaxRate($year, $month);

		Configure::write('debug',0); // debugコードを出さない
		$this->autoRender = false; // Viewを使わない
		$csvFile = date('YmdHis').'_'.$data['function'].'.csv';

		// ヘッダ出力
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=" . $csvFile);

		// ストリーム出力
		$fp = @fopen('php://output', 'w');
		if (!$fp) {
			exit;
		}

		// SJIS指定
		stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

		$title = array(
			'クライアント名',
			'経理用管理コード',
			'精算管理会社名',
			'計上月',
			'合計 - 成約件数',
			'合計 - 成約金額',
			'現地精算 - 成約件数',
			'現地精算 - 成約金額',
			'事前決済 - 成約件数',
			'事前決済 - 成約金額',
			'決済キャンセル件数',
			'キャンセル料合計',
			'レコメンド - 対象件数',
			'レコメンド - 対象成約金額',
			'レコメンド手数料 - 税抜',
			'レコメンド手数料 - 消費税',
			'レコメンド手数料 - 税込',
			'販売手数料 - 料率',
			'販売手数料 - 税抜',
			'販売手数料 - 消費税',
			'販売手数料 - 税込',
			'決済手数料 - 料率',
			'決済手数料 - 税抜',
			'決済手数料 - 消費税',
			'決済手数料 - 税込',
			'精算金額',
			'精算種別',
			'銀行名',
			'支店名',
			'種別',
			'口座番号',
			'口座名義カナ',
			'請求先メールアドレス1',
			'請求先メールアドレス2',
			'請求先メールアドレス3',
			'請求先メールアドレス4',
			'請求先メールアドレス5',
			'請求先メールアドレス6',
			'請求先メールアドレス7',
			'請求先メールアドレス8',
			'請求先メールアドレス9',
			'請求先メールアドレス10'
		);

		$jump = "\r\n";
		fputcsv($fp, $title);

		foreach ($data as $clientId => $dataSettle) {
			if (
				$clientId == 0 ||
				($selectClientId != $clientId && !empty($selectClientId)) // セレクトボックスで絞り込み
			) {
				continue;
			}

			foreach ($dataSettle as $settlementCompanyId => $d) {
				$body = array();
				if ($selectSettlementCompanyId != $d[$month]['settlement_company_id'] && !empty($selectSettlementCompanyId)) { // セレクトボックスで絞り込み
					continue;
				}

				// 請求内容は変わらないはずだが、見た目変わるインパクト大きいので躊躇
				/*if (!isset($d[$month])) {
					continue;
				}*/

				$body[] = $clientList[$clientId];

				$body[] = (isset($d[$month]['accounting_code'])) ? $d[$month]['accounting_code'] : '';

				$body[] = (isset($d[$month]['settlement_company_name'])) ? $d[$month]['settlement_company_name'] : '';

				$body[] = $year . sprintf('%02d', $month);

				// 合計 - 成約件数
				$body[] = (isset($d[$month]['agreement']['count'])) ? number_format($d[$month]['agreement']['count']) : '';

				// 合計 - 成約金額
				$body[] = (isset($d[$month]['agreement']['price'])) ? number_format($d[$month]['agreement']['price']) : '';

				// 現地精算 - 成約件数
				$body[] = (isset($d[$month]['agreement']['local_pay'])) ? number_format($d[$month]['agreement']['local_pay']) : '';

				// 現地精算 - 成約金額
				$body[] = (isset($d[$month]['agreement']['local_amount'])) ? number_format($d[$month]['agreement']['local_amount']) : '';

				// 事前決済 - 成約件数
				$body[] = (isset($d[$month]['agreement']['local_pay'])) ? number_format($d[$month]['agreement']['count'] - $d[$month]['agreement']['local_pay']) : '';

				// 事前決済 - 成約金額
				$body[] = (isset($d[$month]['agreement']['local_amount'])) ? number_format($d[$month]['agreement']['price'] - $d[$month]['agreement']['local_amount']) : '';

				// 決済キャンセル件数
				if (isset($d[$month]['cancel']['count']) && isset($d[$month]['cancel']['local_pay'])) {
					$body[] = number_format($d[$month]['cancel']['count'] - $d[$month]['cancel']['local_pay']);
				} else {
					$body[] = '';
				}

				// キャンセル料合計
				$body[] = (isset($d[$month]['cancel']['cancel_detail_amount'])) ? number_format($d[$month]['cancel']['cancel_detail_amount']) : '';

				// レコメンド - 対象件数
				$body[] = (!empty($d[$month]['recommend']['recommend_count'])) ? number_format($d[$month]['recommend']['recommend_count']) : '';

				// レコメンド - 対象成約金額
				$body[] = (!empty($d[$month]['recommend']['recommend_price'])) ? number_format($d[$month]['recommend']['recommend_price']) : '';

				// レコメンド手数料 - 税抜
				$body[] = (!empty($d[$month]['recommend']['recommend_without_tax'])) ? number_format($d[$month]['recommend']['recommend_without_tax']) : '';

				// レコメンド手数料 - 消費税
				$body[] = (!empty($d[$month]['recommend']['recommend_tax'])) ? number_format($d[$month]['recommend']['recommend_tax']) : '';

				// レコメンド手数料 - 税込
				$body[] = (!empty($d[$month]['recommend']['recommend_with_tax'])) ? number_format($d[$month]['recommend']['recommend_with_tax']) : '';

				// 販売手数料 - 料率
				$body[] = (isset($d[$month]['agreement']['commission_rate'])) ? number_format($d[$month]['agreement']['commission_rate'] * 100, 1) : '';

				// 販売手数料 - 税抜
				$body[] = (isset($d[$month]['agreement']['commission'])) ? number_format($d[$month]['agreement']['commission']) : '';

				// 販売手数料 - 消費税
				$body[] = (isset($d[$month]['agreement']['commission'])) ? number_format(floor(($taxRate - 1.0) * $d[$month]['agreement']['commission'])) : '';

				// 販売手数料 - 税込
				$body[] = (isset($d[$month]['agreement']['commission'])) ? number_format(floor($taxRate * $d[$month]['agreement']['commission'])) : '';

				// 決済手数料 - 料率
				$body[] = (isset($d[$month]['fee_rate'])) ? number_format($d[$month]['fee_rate'], 1) : '';

				// 決済手数料 - 税抜
				$body[] = (!empty($d[$month]['agreement']['settlement_fee'])) ? number_format($d[$month]['agreement']['settlement_fee']) : '';

				// 決済手数料 - 消費税
				if (!empty($d[$month]['agreement']['settlement_fee'])) {
					$body[] = number_format(floor($d[$month]['agreement']['settlement_fee'] * ($taxRate - 1.0)));
				} else {
					$body[] = '';
				}

				// 決済手数料 - 税込
				if (!empty($d[$month]['agreement']['settlement_fee'])) {
					$includeTaxAdministrativeFee = $d[$month]['agreement']['settlement_fee'] + floor($d[$month]['agreement']['settlement_fee'] * ($taxRate - 1.0));
					$body[] = number_format($includeTaxAdministrativeFee);
				} else {
					$body[] = '';
				}

				// 精算金額
				$commissionTax = ($d[$month]['is_internal_tax'] || !isset($d[$month]['agreement']['commission'])) ? 0 : floor(($taxRate - 1.0) * (int)$d[$month]['agreement']['commission']);
				$settlementTax = ($d[$month]['is_internal_tax']) ? 0 : floor($d[$month]['agreement']['settlement_fee'] * ($taxRate - 1.0));
				$settlementPrice = ((isset($d[$month]['agreement']['price']) ? $d[$month]['agreement']['price'] : 0) - (isset($d[$month]['agreement']['local_amount']) ? (int)$d[$month]['agreement']['local_amount'] : 0) + (isset($d[$month]['cancel']['cancel_detail_amount']) ? (int)$d[$month]['cancel']['cancel_detail_amount'] : 0)) -
					((isset($d[$month]['agreement']['commission']) ? (int)$d[$month]['agreement']['commission'] : 0) + $commissionTax + $d[$month]['agreement']['settlement_fee'] + $settlementTax + (isset($d[$month]['recommend']['recommend_with_tax']) ? (int)$d[$month]['recommend']['recommend_with_tax'] : 0));
				$body[] = number_format($settlementPrice);

				// 精算種別
				$body[] = ($settlementPrice > 0) ? '支払い' : '請求';

				// 銀行名
				$body[] = (isset($d[$month]['bank_name'])) ? $d[$month]['bank_name'] : '';
				// 支店名
				$body[] = (isset($d[$month]['bank_branch_name'])) ? $d[$month]['bank_branch_name'] : '';
				// 種別
				if (isset($d[$month]['account_type'])) {
					if ($d[$month]['account_type'] == 0) {
						$body[] = '普通';
					} else {
						$body[] = '当座';
					}
				} else {
					$body[] = '';
				}
				// 口座番号
				$body[] = (isset($d[$month]['account_number'])) ? $d[$month]['account_number'] : '';
				// 口座名義カナ
				$body[] = (isset($d[$month]['account_holder'])) ? $d[$month]['account_holder'] : '';
				// 請求先メールアドレス1
				$body[] = (isset($d[$month]['billing_email1'])) ? $d[$month]['billing_email1'] : '';
				// 請求先メールアドレス2
				$body[] = (isset($d[$month]['billing_email2'])) ? $d[$month]['billing_email2'] : '';
				// 請求先メールアドレス3
				$body[] = (isset($d[$month]['billing_email3'])) ? $d[$month]['billing_email3'] : '';
				// 請求先メールアドレス4
				$body[] = (isset($d[$month]['billing_email4'])) ? $d[$month]['billing_email4'] : '';
				// 請求先メールアドレス5
				$body[] = (isset($d[$month]['billing_email5'])) ? $d[$month]['billing_email5'] : '';
				// 請求先メールアドレス6
				$body[] = (isset($d[$month]['billing_email6'])) ? $d[$month]['billing_email6'] : '';
				// 請求先メールアドレス7
				$body[] = (isset($d[$month]['billing_email7'])) ? $d[$month]['billing_email7'] : '';
				// 請求先メールアドレス8
				$body[] = (isset($d[$month]['billing_email8'])) ? $d[$month]['billing_email8'] : '';
				// 請求先メールアドレス9
				$body[] = (isset($d[$month]['billing_email9'])) ? $d[$month]['billing_email9'] : '';
				// 請求先メールアドレス10
				$body[] = (isset($d[$month]['billing_email10'])) ? $d[$month]['billing_email10'] : '';

				fputcsv($fp, $body);
			}
		}

		fclose($fp);
		exit();
	}

	// 精算後調整
	public function settlement_adjust() {
		$year = $this->request->data['Reservation']['date']['year'];
		$month = $this->request->data['Reservation']['date']['month'];
		$defaultYear = date('Y', strtotime(date('Y-m-1')));
		$defaultMonth = date('m', strtotime(date('Y-m-1')));
		$getCsv = $this->request->data['getCsv'];

		list($finishedMonth, $finishedAt) = $this->SettlementData->__getSettlementFinished();

		if (empty($year) || empty($month)) {
			$year = $defaultYear;
			$month = $defaultMonth;
		}

		// 未来月の変更データはありえない（異常検知のためガード外してもいい？）
		// 指定月の精算完了から次月の精算完了までの間に変更されたデータが対象
		$data = array();
		if ($year.$month <= $defaultYear.$defaultMonth) {
			$data = $this->__getSettlementAdjustData($year, $month);
		}

		if (!empty($getCsv)) {
			$this->__downloadCsvSettlementAdjust($data);
		}

		$this->set('month', (int)$month);
		$this->set('finishedMonth', $finishedMonth);
		$this->set('finishedAt', $finishedAt);
		$this->set('data', $data);
	}

	// 精算後調整データ取得
	private function __getSettlementAdjustData($year, $month)
	{
		// 前提：精算は毎月行われる

		$currentMonth = $year.$month;
		$nextMonth = date('Ym', strtotime(sprintf('%d-%02d-01 +1 month', $year, $month)));

		// 指定月以前に行われた最新の精算完了履歴を取得
		$current = $this->SettlementHistory->find('first', array(
			'conditions' => array("DATE_FORMAT(created, '%Y%m') <=" => $currentMonth),
			'order' => array('created' => 'DESC')
		));
		// 翌月行われた精算完了履歴を取得
		$next = $this->SettlementHistory->find('first', array(
			'conditions' => array("DATE_FORMAT(created, '%Y%m')" => $nextMonth),
			'order' => array('created' => 'DESC')
		));

		if (empty($current)) {
			// 機能導入前、精算完了履歴が不明
			return array();
		}

		// オリックス、タイムズ、日産、ニッポン、トヨタ（大手）は対象外
		$nonTargetClients = array(4, 33, 46, 55, 75);

		if (!empty($next)) {
			// 精算完了間に変更されたデータ
			// 予約ステータス変更データ
			$statusChanged = $this->Reservation->find('all', array(
				'fields' => array(
					'Reservation.id',
					'Reservation.reservation_key',
					'Reservation.cancel_datetime',
					'Reservation.client_id'
				),
				'conditions' => array(
					// 成約→キャンセル
					"DATE_FORMAT(CASE Client.conclusion_contract_criteria WHEN 0 THEN Reservation.rent_datetime ELSE Reservation.return_datetime END, '%Y%m') <= " => $current['SettlementHistory']['settlement_month'],
					'Reservation.cancel_datetime >=' => $current['SettlementHistory']['created'],
					'Reservation.cancel_datetime <= ' => $next['SettlementHistory']['created'],
					'Reservation.cancel_flg' => 1,
					'Reservation.reservation_status_id' => 3
					// キャンセル→成約は考慮しない
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'clients',
						'alias' => 'Client',
						'conditions' => array(
							'Client.id = Reservation.client_id',
							'Client.id NOT IN ' => $nonTargetClients
						)
					),
					array(
						'type' => 'INNER',
						'table' => 'commodity_items',
						'alias' => 'CommodityItem',
						'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
					),
					array(
						'type' => 'INNER',
						'table' => 'commodities',
						'alias' => 'Commodity',
						'conditions' => array(
							'Commodity.id = CommodityItem.commodity_id',
							'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
						)
					)
				),
				'recursive' => -1
			));

			// キャンセル明細追加データ
			$detailsAdded = $this->Reservation->find('all', array(
				'fields' => array(
					'Reservation.id',
					'Reservation.reservation_key',
					'CancelDetail.created',
					'Reservation.client_id'
				),
				'conditions' => array(
					"DATE_FORMAT(CASE Client.conclusion_contract_criteria WHEN 0 THEN Reservation.rent_datetime ELSE Reservation.return_datetime END, '%Y%m') <= " => $current['SettlementHistory']['settlement_month'],
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'clients',
						'alias' => 'Client',
						'conditions' => array(
							'Client.id = Reservation.client_id',
							'Client.id NOT IN ' => $nonTargetClients
						)
					),
					array(
						'type' => 'INNER',
						'table' => 'cancel_details',
						'alias' => 'CancelDetail',
						'conditions' => array(
							'CancelDetail.reservation_id = Reservation.id',
							// 精算後に明細が追加された
							'CancelDetail.created >=' => $current['SettlementHistory']['created'],
							'CancelDetail.created <= ' => $next['SettlementHistory']['created'],
							'CancelDetail.delete_flg' => 0
						)
					),
					array(
						'type' => 'INNER',
						'table' => 'commodity_items',
						'alias' => 'CommodityItem',
						'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
					),
					array(
						'type' => 'INNER',
						'table' => 'commodities',
						'alias' => 'Commodity',
						'conditions' => array(
							'Commodity.id = CommodityItem.commodity_id',
							'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
						)
					)
				),
				'group' => array('Reservation.reservation_key', 'CancelDetail.created'),
				'recursive' => -1
			));
		} else {
			// 計上月＝指定月の精算がまだ行われていない場合
			// 予約ステータス変更データ
			$statusChanged = $this->Reservation->find('all', array(
				'fields' => array(
					'Reservation.id',
					'Reservation.reservation_key',
					'Reservation.cancel_datetime',
					'Reservation.client_id'
				),
				'conditions' => array(
					// 成約→キャンセル
					"DATE_FORMAT(CASE Client.conclusion_contract_criteria WHEN 0 THEN Reservation.rent_datetime ELSE Reservation.return_datetime END, '%Y%m') <= " => $current['SettlementHistory']['settlement_month'],
					"DATE_FORMAT(Reservation.cancel_datetime, '%Y%m')" => $currentMonth,
					'Reservation.cancel_datetime >= ' => $current['SettlementHistory']['created'],
					'Reservation.cancel_flg' => 1,
					'Reservation.reservation_status_id' => 3
					// キャンセル→成約は考慮しない
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'clients',
						'alias' => 'Client',
						'conditions' => array(
							'Client.id = Reservation.client_id',
							'Client.id NOT IN ' => $nonTargetClients
						)
					),
					array(
						'type' => 'INNER',
						'table' => 'commodity_items',
						'alias' => 'CommodityItem',
						'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
					),
					array(
						'type' => 'INNER',
						'table' => 'commodities',
						'alias' => 'Commodity',
						'conditions' => array(
							'Commodity.id = CommodityItem.commodity_id',
							'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
						)
					)
				),
				'recursive' => -1
			));

			// キャンセル明細追加データ
			$detailsAdded = $this->Reservation->find('all', array(
				'fields' => array(
					'Reservation.id',
					'Reservation.reservation_key',
					'CancelDetail.created',
					'Reservation.client_id'
				),
				'conditions' => array(
					"DATE_FORMAT(CASE Client.conclusion_contract_criteria WHEN 0 THEN Reservation.rent_datetime ELSE Reservation.return_datetime END, '%Y%m') <= " => $current['SettlementHistory']['settlement_month'],
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'clients',
						'alias' => 'Client',
						'conditions' => array(
							'Client.id = Reservation.client_id',
							'Client.id NOT IN ' => $nonTargetClients
						)
					),
					array(
						'type' => 'INNER',
						'table' => 'cancel_details',
						'alias' => 'CancelDetail',
						'conditions' => array(
							'CancelDetail.reservation_id = Reservation.id',
							// 精算後に明細が追加された
							"DATE_FORMAT(CancelDetail.created, '%Y%m')" => $currentMonth,
							'CancelDetail.created >= ' => $current['SettlementHistory']['created'],
							'CancelDetail.delete_flg' => 0
						)
					),
					array(
						'type' => 'INNER',
						'table' => 'commodity_items',
						'alias' => 'CommodityItem',
						'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
					),
					array(
						'type' => 'INNER',
						'table' => 'commodities',
						'alias' => 'Commodity',
						'conditions' => array(
							'Commodity.id = CommodityItem.commodity_id',
							'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
						)
					)
				),
				'group' => array('Reservation.reservation_key', 'CancelDetail.created'),
				'recursive' => -1
			));
		}

		$data = array();
		foreach ($statusChanged as $r) {
			$data[$r['Reservation']['reservation_key']] = array(
				'id' => $r['Reservation']['id'],
				'updated_at' => $r['Reservation']['cancel_datetime'],
				'client_id' => $r['Reservation']['client_id']
			);
		}
		foreach ($detailsAdded as $r) {
			if (!isset($data[$r['Reservation']['reservation_key']])) {
				$data[$r['Reservation']['reservation_key']] = array(
					'id' => $r['Reservation']['id'],
					'updated_at' => $r['CancelDetail']['created'],
					'client_id' => $r['Reservation']['client_id']
				);
			} else {
				// より新しい変更日時を残す
				if ($r['CancelDetail']['created'] > $data[$r['Reservation']['reservation_key']]['updated_at']) {
					$data[$r['Reservation']['reservation_key']]['updated_at'] = $r['CancelDetail']['created'];
				}
			}
		}

		return $data;
	}

	// 精算後調整CSV
	private function __downloadCsvSettlementAdjust($data)
	{
		Configure::write('debug', 0); // debugコードを出さない
		$this->autoRender = false; // Viewを使わない
		$csvFile = date('YmdHis').'_settlement_adjust'.'.csv';

		// ヘッダ出力
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=" . $csvFile);

		// ストリーム出力
		$fp = @fopen('php://output', 'w');
		if (!$fp) {
			exit;
		}

		// SJIS指定
		stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

		fputcsv($fp, array('予約番号', '変更日時'));
		foreach ($data as $key => $value) {
			fputcsv($fp, array($key, $value['updated_at']));
		}

		fclose($fp);
		exit();
	}

	private function __getRate($numerator, $denominator)
	{
		if(!empty($numerator) && !empty($denominator)){
			$rate = $numerator / $denominator * 100;
		}else{
			$rate = '';
		}
		return $rate;
	}
}

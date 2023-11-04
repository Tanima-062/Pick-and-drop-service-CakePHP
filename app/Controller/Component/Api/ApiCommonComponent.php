<?php
class ApiCommonComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	/**
	 *  ローカル環境(主にaxios)CORS対応のヘッダをセットする
	 *
	 * @return void
	 */
	public function setCorsHeader() {
		if (!IS_PRODUCTION) {
			$this->controller->response->header('Access-Control-Allow-Origin: http://localhost:3000');
			$this->controller->response->header('Access-Control-Allow-Credentials: true');
			$this->controller->response->header('Access-Control-Allow-Headers', '*');
			$this->controller->response->header('Access-Control-Allow-Methods', '*');
		}
	}
	
	// 営業時間文字列組み立て
	public function createShopTimeString($office) {
		if (empty($office)) {
			return '';
		}
		
		$office_hours = date('H:i', strtotime($office['office_hours_from'])) . '～' . date('H:i', strtotime($office['office_hours_to']));
		
		$sat_hours = '';
		if (!empty($office['sat_hours_from']) && !empty($office['sat_hours_to'])) {
			$sat_hours = date('H:i', strtotime($office['sat_hours_from'])) . '～' . date('H:i', strtotime($office['sat_hours_to']));
		}
		$sun_hours = '';
		if (!empty($office['sun_hours_from']) && !empty($office['sun_hours_to'])) {
			$sun_hours = date('H:i', strtotime($office['sun_hours_from'])) . '～' . date('H:i', strtotime($office['sun_hours_to']));
		}
		$hol_hours = '';
		if (!empty($office['hol_hours_from']) && !empty($office['hol_hours_to'])) {
			$hol_hours = date('H:i', strtotime($office['hol_hours_from'])) . '～' . date('H:i', strtotime($office['hol_hours_to']));
		}
		
		$shop_time = '';
		if ($office_hours == $sat_hours && $sat_hours == $sun_hours && $sun_hours == $hol_hours) {
			$shop_time = $office_hours;
		} else {
			$shop_time = '平日' . $office_hours;
			
			if (!empty($sat_hours)) {
				if ($sat_hours == $sun_hours && $sun_hours == $hol_hours) {
					$shop_time .= ' 土日祝日' . $sat_hours;
				} else if ($sat_hours == $sun_hours && $sun_hours != $hol_hours) {
					$shop_time .= ' 土日' . $sat_hours;
				} else {
					$shop_time .= ' 土' . $sat_hours;
				}
			}
			
			if (!empty($sun_hours) && $sat_hours != $sun_hours) {
				if ($sun_hours == $hol_hours) {
					$shop_time .= ' 日祝日' . $sun_hours;
				} else {
					$shop_time .= ' 日' . $sun_hours;
				}
			}
			
			if (!empty($hol_hours) && $sun_hours != $hol_hours) {
				$shop_time .= ' 祝日' . $sat_hours;
			}
		}

		return $shop_time;
	}
	
	// {id}-{name}\t{id}-{name}...の形式になっている駅データを配列に変換する
	public function convertStationInfo($stations) {
		if (empty($stations)) {
			return array();
		}
		
		$stations = explode("\t", $stations);
		$stationInfo = array();
		
		foreach ($stations as $station) {
			$station = explode('-', $station);
			$stationInfo[] = array(
				'station_id' => $station[0],
				'station_name' => $station[1],
			);
		}
		
		return $stationInfo;
	}
	
	// yyyymmddhhmm形式の文字列を検索パラメータの配列に変換する
	public function convertDatetime($value, $prefix = '') {
		if (empty($value)) {
			return array();
		}
		
		$dateTime = new DateTime($value);
		$ret[$prefix . 'year']	 = $dateTime->format('Y');
		$ret[$prefix . 'month']	 = $dateTime->format('m');
		$ret[$prefix . 'day']		 = $dateTime->format('d');
		$ret[$prefix . 'time']	 = $dateTime->format('H') . '-' . $dateTime->format('i');
		
		return $ret;
	}

}

<?php
class TravelkoComponent extends Component {
	public $components = array('ApiCommon');

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}
	
	// レスポンス値をJSON形式で返す
	public function createResponseJson($value, $debug = false) {
		if (!$debug) {
			return json_encode($value, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
		} else {
			return '<pre>' . json_encode($value, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT) . '</pre>';
		}
	}

	 // トラベルコのパラメータからスカイチケットの検索パラメータを取得
	public function getSearchParams($params) {
		$ret = array();

		// 貸出場所
		switch ($params['rental_area_type']) {
			case '1': // 市区町村
				$ret['place'] = '1';
				$ret['city_id'] = $this->getCityId($params['rental_area']);
				break;
			case '2': // 空港
				$ret['place'] = '3';
				$ret['airport_id'] = $this->getAirportId($params['rental_area']);
				break;
			case '3': // 駅
				$ret['place'] = '4';
				$ret['station_id'] = $this->getStationId($params['rental_area']);
				break;
		}

		// 貸出時間
		$rentalTime = $this->ApiCommon->convertDateTime($params['rental_time']);
		$ret += $rentalTime;

		// 返却場所
		if (!empty($params['return_area_type']) && !empty($params['return_area'])) {
			if ($params['rental_area_type'] != $params['return_area_type'] || $params['rental_area'] != $params['return_area']) {
				$ret['return_way'] = '1';
			} else {
				$ret['return_way'] = '0';
			}
		} else {
			$ret['return_way'] = '0';
		}

		if (!empty($ret['return_way'])) {
			switch ($params['return_area_type']) {
				case '1': // 市区町村
					$ret['return_place'] = '1';
					$ret['return_city_id'] = $this->getCityId($params['return_area']);
					break;
				case '2': // 空港
					$ret['return_place'] = '3';
					$ret['return_airport_id'] = $this->getAirportId($params['return_area']);
					break;
				case '3': // 駅
					$ret['return_place'] = '4';
					$ret['return_station_id'] = $this->getStationId($params['return_area']);
					break;
			}
		}

		// 返却時間
		$returnTime = $this->ApiCommon->convertDateTime($params['return_time'], 'return_');
		$ret += $returnTime;

		// 固定値
		$ret['adults_count']	 = '2';
		$ret['transmission_flg'] = '2';
		$ret['smoking_flg']		 = '2';

		return $ret;
	}

	// トラベルコの市区町村IDからスカイチケットの市区町村ID取得
	private function getCityId($id) {
		if (empty($id)) {
			return 0;
		}

		$ret = $this->controller->City->findC('first', array(
			'fields' => 'City.id',
			'conditions' => array(
				'City.travelko_city_id' => $id,
				'City.delete_flg' => false,
			),
			'recursive' => -1,
		));

		return !empty($ret) ? $ret['City']['id'] : 0;
	}

	// トラベルコの空港IDからスカイチケットの空港ID取得
	private function getAirportId($id) {
		if (empty($id)) {
			return 0;
		}

		$ret = $this->controller->Landmark->findC('first', array(
			'fields' => 'Landmark.id',
			'conditions' => array(
				'Landmark.travelko_id' => $id,
				'Landmark.landmark_category_id' => 1,
				'Landmark.delete_flg' => false,
			),
			'recursive' => -1,
		));

		return !empty($ret) ? $ret['Landmark']['id'] : 0;
	}

	// トラベルコの駅IDからスカイチケットの駅ID取得
	private function getStationId($id) {
		if (empty($id)) {
			return 0;
		}

		$ret = $this->controller->Station->findC('first', array(
			'fields' => 'Station.id',
			'conditions' => array(
				'Station.travelko_id' => $id,
				'Station.delete_flg' => false,
			),
			'recursive' => -1,
		));

		return !empty($ret) ? $ret['Station']['id'] : 0;
	}
}

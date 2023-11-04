<?php

class SkyscannerComponent extends Component {

	public $components = array('ApiCommon');

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	// APIのパラメータからスカイチケットの検索パラメータを取得
	public function getSearchParams($params) {
		$ret = array();

		// 貸出場所
		switch ($params['rental_type']) {
			case '1': // 市区町村
				$ret['place'] = '1';
				$ret['city_id'] = $params['rental_point'];
				break;
			case '2': // 空港
				$ret['place'] = '3';
				$ret['airport_id'] = $this->getAirportId($params['rental_point']);
				break;
			case '3': // 駅
				$ret['place'] = '4';
				$ret['station_id'] = $params['rental_point'];
				break;
		}

		// 貸出時間
		$rentalTime = $this->ApiCommon->convertDateTime($params['rental_datetime']);
		$ret += $rentalTime;

		// 返却場所
		if ($params['rental_type'] != $params['return_type'] || $params['rental_point'] != $params['return_point']) {
			$ret['return_way'] = '1';
		} else {
			$ret['return_way'] = '0';
		}

		if (!empty($ret['return_way'])) {
			switch ($params['return_type']) {
				case '1': // 市区町村
					$ret['return_place'] = '1';
					$ret['return_city_id'] = $params['return_point'];
					break;
				case '2': // 空港
					$ret['return_place'] = '3';
					$ret['return_airport_id'] = $this->getAirportId($params['return_point']);
					break;
				case '3': // 駅
					$ret['return_place'] = '4';
					$ret['return_station_id'] = $params['return_point'];
					break;
			}
		}

		// 返却時間
		$returnTime = $this->ApiCommon->convertDateTime($params['return_datetime'], 'return_');
		$ret += $returnTime;

		// 固定値
		$ret['adults_count'] = '2';
		$ret['transmission_flg'] = '2';
		$ret['smoking_flg'] = '2';

		return $ret;
	}

	// IATAコードから空港ID取得
	private function getAirportId($id) {
		if (empty($id)) {
			return 0;
		}

		$ret = $this->controller->Landmark->findC('first', array(
			'fields' => 'Landmark.id',
			'conditions' => array(
				'Landmark.iata_cd' => $id,
				'Landmark.landmark_category_id' => 1,
				'Landmark.delete_flg' => false,
			),
			'recursive' => -1,
		));

		return !empty($ret) ? $ret['Landmark']['id'] : 0;
	}

}

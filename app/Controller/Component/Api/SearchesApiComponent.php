<?php

class SearchesApiComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	// リクエストパラメータを1次元配列にする
	public function flattenParams($params) {
		$ret = array();

		foreach ((array)$params as $key => $value) {
			if ($key != 'location') {
				$ret += $value;
			} else {
				if (isset($value['departure'])) {
					$ret += $value['departure'];
				}
				if (isset($value['arrival'])) {
					// キーが被るのでreturn_付ける
					foreach ($value['arrival'] as $k => $v) {
						$k = 'return' . ucfirst($k);
						$ret += array($k => $v);
					}
				}
			}
		}

		return $ret;
	}

	// APIのパラメータからスカイチケットの検索パラメータを取得
	public function getSearchParams($params) {
		$ret = $params;

		// IATAコードがある場合は空港出発
		if (!empty($params['IATACode'])) {
			$ret['place'] = '3';
			$ret['airportId'] = $this->getAirportId($params['IATACode']);
		} else if (!empty($params['airportId'])) {
			$ret['place'] = '3';
		} else if (!empty($params['stationId'])) {
			$ret['place'] = '4';
		} else if (!empty($params['areaId'])) {
			$ret['place'] = '1';
		} else {
			// それ以外は座標から店舗を取得
			$ret['place'] = '5';
		}

		// IATAコードがある場合は空港返却
		if (!empty($params['returnIATACode'])) {
			if (empty($params['IATACode']) || $params['IATACode'] != $params['returnIATACode']) {
				$ret['returnWay'] = '1';
				$ret['returnPlace'] = '3';
				$ret['returnAirportId'] = $this->getAirportId($params['returnIATACode']);
			}
		} else if (!empty($params['returnAirportId'])) {
			if (empty($params['airportId']) || $params['airportId'] != $params['returnAirportId']) {
				$ret['returnWay'] = '1';
				$ret['returnPlace'] = '3';
			}
		} else if (!empty($params['returnStationId'])) {
			if (empty($params['stationId']) || $params['stationId'] != $params['returnStationId']) {
				$ret['returnWay'] = '1';
				$ret['returnPlace'] = '4';
			}
		} else if (!empty($params['returnAreaId'])) {
			if (empty($params['areaId']) || $params['areaId'] != $params['returnAreaId']) {
				$ret['returnWay'] = '1';
				$ret['returnPlace'] = '1';
			}
		} else if ($params['latitude'] != $params['returnLatitude'] || $params['longitude'] != $params['returnLongitude']) {
			// 空港以外は座標から店舗を取得
			$ret['returnWay'] = '1';
			$ret['returnPlace'] = '5';
		}

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

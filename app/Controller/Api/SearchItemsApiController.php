<?php
App::uses('BaseRestApiController', 'Controller');

class SearchItemsApiController extends BaseRestApiController {
	public $uses = array('Landmark', 'Area', 'Station');

	// 主要空港リスト
	private $mainAirports = array(
		330 => '新千歳空港',
		326 => '那覇空港',
		309 => '福岡空港',
		280 => '羽田空港',
		281 => '成田空港',
		292 => '関西国際空港',
		288 => '中部国際空港(セントレア)',
	);
	
	public function beforeFilter() {
		parent::beforeFilter();

		foreach ((array)$this->uses as $model) {
			$this->$model->setDataSource('default_slave');
		}

		$this->ApiCommon->setCorsHeader();
	}

	// 空港一覧
	public function airports() {
		$createAirports = function($airports) {
			$ret = array();
			foreach ($airports as $id => $name) {
				$ret[] = array(
					'airportId' => $id,
					'airportName' => $name,
				);
			}
			return $ret;
		};

		// 主要空港
		$airports = array(
			array(
				'主要空港' => $createAirports($this->mainAirports),
			)
		);

		// 空港と新幹線駅のリストを取得
		$landmarkList = $this->Landmark->getAllLandmarks();

		// 空港(在庫があるもののみ)
		foreach ($landmarkList['ArrayInStock'] as $prefecture => $values) {
			$airports[] = array(
				$prefecture => $createAirports($values)
			);
		}

		$this->responseData = $airports;
	}

	// 駅一覧
	public function stations($id) {
		// 全駅取得(在庫があるもののみ)
		$stationList = $this->Station->getStationAll(false, true);

		if (empty($stationList[$id])) {
			return;
		}

		$stations = array(
			array(
				'主要駅' => array(),
			)
		);

		foreach ($stationList[$id] as $values) {
			$areaStations = array();

			foreach ($values as $value) {
				$station = array(
					'stationId' => (int)$value['id'],
					'stationName' => $value['name'],
				);

				// 主要駅の場合
				if (!empty($value['major'])) {
					$stations[0]['主要駅'][] = $station;
				// その他駅
				} else {
					$areaStations[] = $station;
				}
			}

			// エリア毎の駅追加
			if (!empty($areaStations)) {
				$areaName = $values[0]['area_name'];
				$stations[] = array($areaName => $areaStations);
			}
		}

		$this->responseData = $stations;
	}

	// エリア一覧
	public function areas($id) {
		//全エリア取得(在庫があるもののみ)
		$areaList = $this->Area->getAreaAll(true);

		if (empty($areaList[$id])) {
			return;
		}

		$areas = array();

		foreach ($areaList[$id] as $area) {
			$areas[] = array(
				'areaId' => $area['id'],
				'areaName' => $area['name'],
			);
		}

		$this->responseData = $areas;
	}
}

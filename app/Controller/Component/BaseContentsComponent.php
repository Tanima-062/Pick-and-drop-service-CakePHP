<?php

App::uses('Component', 'Controller');

class BaseContentsComponent extends Component {

	// 車両タイプ別最安値のリンク先（パラメータ設定用）
	public $bestPriceCarTypes = [];

	public function initialize(Controller $controller) {
		$activeWebp = strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
		$type = $activeWebp ? '.webp' : '.png';

		$this->controller = $controller;
		$this->bestPriceCarTypes = array(
			1 => array(
				'name' => '軽自動車・コンパクト',
				'img' => array(
					1 => '/rentacar/img/car_type_kei'.$type,
					2 => '/rentacar/img/car_type_compact'.$type,
				),
				'params' => array('car_type' => array(1, 2)),
			),
			3 => array(
				'name' => 'ミドル・セダン・エコカー・ハイブリッド',
				'img' => array(
					6 => '/rentacar/img/car_type_hybrid'.$type,
					3 => '/rentacar/img/car_type_sedan'.$type,
				),
				'params' => array('car_type' => array(3, 6)),
			),
			9 => array(
				'name' => 'RV・ミニバン・1BOX・ワゴン',
				'img' => array(
					5 => '/rentacar/img/car_type_miniban'.$type,
					9 => '/rentacar/img/car_type_wagon'.$type,
				),
				'params' => array('car_type' => array(5, 9)),
			),
		);
	}

	public function getBestPricesForCarTypes($prices) {
		$sortedPrices = Hash::sort($prices, '{n}.carTypeId', 'asc');
		$typePrices = Hash::combine($sortedPrices, '{n}.commodityItemId', '{n}', '{n}.carTypeId');
		$typeBestPrices = array();
		foreach ($typePrices as $k => $v) {
			$typeBestPrices[$k] = array(
				'carTypeName' => current($v)['carTypeName'],
				'bestPrice' => PHP_INT_MAX
			);
			foreach ($v as $r) {
				if ($typeBestPrices[$k]['bestPrice'] > $r['price']) {
					$typeBestPrices[$k]['bestPrice'] = $r['price'];
				}
			}
		}
		unset($typePrices);
		// 車両タイプをまとめる
		// 軽自動車とコンパクト（keyは軽自動車のID=1）
		$typeBestPrices = $this->integratePrice($typeBestPrices, 1, 2);
		// ミドル・セダンとエコカー・ハイブリッド（keyはミドル・セダンのID=3）
		$typeBestPrices = $this->integratePrice($typeBestPrices, 3, 6);
		// ミニバンとワゴン（keyはミニバンのID=9）
		$typeBestPrices = $this->integratePrice($typeBestPrices, 9, 5);
		return $typeBestPrices;
	}

	private function integratePrice($priceList, $main, $sub) {
		$list = $priceList;
		if (isset($list[$main]) && isset($list[$sub])) {
			if ($list[$sub]['bestPrice'] < $list[$main]['bestPrice']) {
				$list[$main]['bestPrice'] = $list[$sub]['bestPrice'];
			}
			unset($list[$sub]);
		} elseif (isset($list[$sub])) {
			$list[$main] = $list[$sub];
			unset($list[$sub]);
		}
		return $list;
	}

	public function getCarTypeCapacityList() {
		$CarType = ClassRegistry::init('CarType');

		$capacityList = $CarType->findC('list',
			array(
				'fields' => array(
					'id',
					'capacity'
				),
				'conditions' => array(
					'delete_flg' => 0
				)
			)
		);
		if (!empty($capacityList)) {
			// 車両タイプをまとめる
			// 軽自動車とコンパクト（keyは軽自動車のID=1）
			$capacityList = $this->integrateCapacity($capacityList, 1, 2);
			// ミドル・セダンとエコカー・ハイブリッド（keyはミドル・セダンのID=3）
			$capacityList = $this->integrateCapacity($capacityList, 3, 6);
			// ミニバンとワゴン（keyはミニバンのID=9）
			$capacityList = $this->integrateCapacity($capacityList, 9, 5);
		}
		return $capacityList;
	}

	private function integrateCapacity($capacityList, $main, $sub) {
		$list = $capacityList;
		if (isset($list[$main]) && isset($list[$sub])) {
			if ($list[$sub] < $list[$main]) {
				$list[$main] = $list[$sub];
			}
			unset($list[$sub]);
		} elseif (isset($list[$sub])) {
			$list[$main] = $list[$sub];
			unset($list[$sub]);
		}
		return $list;
	}

	protected function rankLandmarkCount($reservationData) {
		$ranking = array();
		foreach ((array)$reservationData as $r) {
			if (!empty($r['Landmark']['id'])) {
				if (isset($ranking[$r['Landmark']['id']])) {
					$ranking[$r['Landmark']['id']]['count'] += 1;
				} else {
					$ranking[$r['Landmark']['id']] = array('type' => 'Landmark', 'id' => $r['Landmark']['id'], 'name' => $r['Landmark']['name'], 'count' => 1);
				}
			}
			if (!empty($r['Station']['id'])) {
				if (isset($ranking[$r['Station']['id']])) {
					$ranking[$r['Station']['id']]['count'] += 1;
				} else {
					$ranking[$r['Station']['id']] = array(
						'type' => 'Station',
						'id' => $r['Station']['id'],
						'prefecture_id' => $r['Station']['prefecture_id'],
						'name' => $r['Station']['name'].($r['Station']['type'] ? '停留場' : '駅'),
						'count' => 1
					);
				}
			}
		}
		usort($ranking, function($a, $b) {
			return $a['count'] < $b['count'] ? 1 : -1;
		});

		return $ranking;
	}

	protected function addPriceAndCapacityInfo($landmarkRanking, $searchParams, $clientId = null) {
		if (empty($landmarkRanking)) {
			return array();
		}

		define('RANK_MAX', 3);

		$ranking = $landmarkRanking;
		$Office = ClassRegistry::init('Office');
		$Commodity = ClassRegistry::init('Commodity');

		$max = count($ranking) > RANK_MAX ? RANK_MAX : count($ranking);
		for ($i = 0; $i < $max; $i++) {
			$params = $searchParams;
			$params['sort'] = 2;
			if (!is_null($clientId)) {
				$params['client_id'] = $clientId;
			}
			if ($ranking[$i]['type'] == 'Landmark') {
				$params['place'] = 3;
				$params['airport_id'] = $ranking[$i]['id'];
				$officeInfo = $Office->getOfficeNearListByAirportId($ranking[$i]['id'], $clientId);
			} else {
				$params['place'] = 4;
				$params['prefecture'] = $ranking[$i]['prefecture_id'];
				$params['station_id'] = $ranking[$i]['id'];
				$officeInfo = $Office->getOfficeNearListByStationId($ranking[$i]['id'], $clientId);
			}
			$officeIds = Hash::extract($officeInfo, '{n}.Office.id');
			$prices = $Commodity->getPriceByOfficeId($officeIds);
			$typeBestPrices = $this->getBestPricesForCarTypes($prices);
			$bestPriceCarTypes = $this->bestPriceCarTypes;
			if ($this->controller->viewVars['fromRentacarClient']) {
				$params['from_rentacar_client'] = 'true';
			}
			foreach ($bestPriceCarTypes as $carTypeId => &$carType) {
				$carType['url'] = '/rentacar/searches?'.urldecode(http_build_query(array_merge($params, $carType['params'])));
				$carType['price'] = !empty($typeBestPrices[$carTypeId]) ? '&yen;' . number_format($typeBestPrices[$carTypeId]['bestPrice']) : '最安値を検索';
			}
			$ranking[$i]['bestPriceCarTypes'] = $bestPriceCarTypes;
			// 余計な情報は消しておく
			unset($ranking[$i]['type']);
			unset($ranking[$i]['id']);
			unset($ranking[$i]['count']);
		}
		return array_slice($ranking, 0, RANK_MAX);
	}
}

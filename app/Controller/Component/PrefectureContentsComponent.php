<?php

App::uses('BaseContentsComponent', 'Controller/Component');

class PrefectureContentsComponent extends BaseContentsComponent {

	public function getPopularLandmarkRanking($areaIds, $searchParams) {
		$Reservation = ClassRegistry::init('Reservation');
		$OfficeStation = ClassRegistry::init('OfficeStation');

		$conditions = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Station.id',
				'Station.prefecture_id',
				'Station.name',
				'Station.type',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => array(
						'Office.id = Reservation.rent_office_id',
						'Office.area_id' => $areaIds,
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'office_supplements',
					'alias' => 'OfficeSupplement',
					'conditions' => array(
						'OfficeSupplement.office_id = Office.id',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'landmarks',
					'alias' => 'Landmark',
					'conditions' => array(
						'Landmark.id = Office.airport_id',
						'OfficeSupplement.nearest_transport' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'table' => "({$OfficeStation->getIndexGroupByOfficeSubQuery()})",
					'alias' => 'OfficeStation',
					'conditions' => array(
						'OfficeStation.office_id = Reservation.rent_office_id',
						'OfficeStation.idx' => 0,// 営業所ごとの先頭レコード
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'stations',
					'alias' => 'Station',
					'conditions' => array(
						'Station.id = OfficeStation.station_id',
						'OfficeSupplement.nearest_transport' => 1,
					),
				),
			),
			'conditions' => array(
				'Reservation.reservation_datetime >=' => date('Y-m-d 00:00:00', strtotime('-61 day')),
				'Reservation.reservation_datetime <=' => date('Y-m-d 23:59:59', strtotime('-1 day')),
			),
			'recursive' => -1,
		);
		$result = $Reservation->findC('all', $conditions, '1day');

		$ranking = $this->rankLandmarkCount($result);

		return $this->addPriceAndCapacityInfo($ranking, $searchParams);
	}

	public function getBestPricesForAreas($priceCurrentMonth, $priceNextMonth, $prefectureId, $areaList, $clientList, $searchParams) {
		$areaToPriceCurrent = Hash::combine($priceCurrentMonth, '{n}.commodityItemId', '{n}', '{n}.areaId');
		$areaToPriceNext = Hash::combine($priceNextMonth, '{n}.commodityItemId', '{n}', '{n}.areaId');

		$addParams = array(
			'prefecture' => $prefectureId,
			'sort' => 2,
		);
		$emptyPriceInfo = $this->emptyPriceInfo();

		$bestPriceAreas = array();
		foreach ($areaList as $areaId => $area) {
			$addParams['area_id'] = $areaId;
			$currentCount = count($areaToPriceCurrent[$areaId]);
			$nextCount = count($areaToPriceNext[$areaId]);

			$priceInfo = $emptyPriceInfo;
			$priceInfo['url'] = '/rentacar/searches?'.urldecode(http_build_query(array_merge($searchParams, $addParams)));
			$priceInfo['plan_count'] = $currentCount > $nextCount ? $currentCount : $nextCount;

			if ($currentCount > 0) {
				foreach ($areaToPriceCurrent[$areaId] as $plan) {
					if ($priceInfo['price_info'][$plan['carTypeId']]['current_month']['best_price'] === '' ||
						$priceInfo['price_info'][$plan['carTypeId']]['current_month']['best_price'] > $plan['price']) {
						$priceInfo['price_info'][$plan['carTypeId']]['current_month']['client_name'] = $clientList[$plan['clientId']]['name'];
						$priceInfo['price_info'][$plan['carTypeId']]['current_month']['best_price'] = $plan['price'];
					}
				}
			}
			if ($nextCount > 0) {
				foreach ($areaToPriceNext[$areaId] as $plan) {
					if ($priceInfo['price_info'][$plan['carTypeId']]['next_month']['best_price'] === '' ||
						$priceInfo['price_info'][$plan['carTypeId']]['next_month']['best_price'] > $plan['price']) {
						$priceInfo['price_info'][$plan['carTypeId']]['next_month']['client_name'] = $clientList[$plan['clientId']]['name'];
						$priceInfo['price_info'][$plan['carTypeId']]['next_month']['best_price'] = $plan['price'];
					}
				}
			}
			foreach ($priceInfo['price_info'] as $key => $info) {
				if ($info['current_month']['best_price'] !== '') {
					$priceInfo['price_info'][$key]['current_month']['best_price'] = '&yen;'.number_format($info['current_month']['best_price']);
				}
				if ($info['next_month']['best_price'] !== '') {
					$priceInfo['price_info'][$key]['next_month']['best_price'] = '&yen;'.number_format($info['next_month']['best_price']);
				}
			}
			$bestPriceAreas[$area['name']] = $priceInfo;
		}
		return $bestPriceAreas;
	}

	private function emptyPriceInfo() {
		$CarType = ClassRegistry::init('CarType');
		$carTypeList = $CarType->getCarTypeList();
		$priceInfo = array('url' => '', 'plan_count' => 0, 'price_info' => array());
		foreach ($carTypeList as $id => $name) {
			$priceInfo['price_info'][$id] = array(
				'car_type_name' => $name,
				'current_month' => array(
					'client_name' => '',
					'best_price' => ''
				),
				'next_month' => array(
					'client_name' => '',
					'best_price' => ''
				)
			);
		}
		return $priceInfo;
	}

	// このメソッドと内部で呼んでいる～Filter()は、
	// CommodityモデルやCommoditySubQueryBehaviorのメソッドをエリア別最安値用に改変したもの
	// 置き場所に困ったがひとまずこのコンポーネントに置く
	public function getPriceByPrefectureArea($areaId, $dateFrom, $dateTo) {
		$Commodity = ClassRegistry::init('Commodity');

		$datetimeFrom = $dateFrom.' 00:00:00';
		$datetimeTo = $dateTo.' 23:59:59';

		$options = array(
			'fields'=>array(
				'Commodity.client_id',
				'Commodity.day_time_flg',
				'CommodityItem.id',
				'CommodityItem.car_class_id',
				'CarType.id',
				'CarType.name',
				'RentOffice.area_id',
				//'OfficeStockGroup.stock_group_id'
			),
			'conditions' => array(
				'Commodity.is_published' => 1,
				'CommodityTerm.available_from <=' => $datetimeTo,
				'CommodityTerm.available_to >=' => $datetimeFrom,
				'Commodity.delete_flg' => 0,
				'RentOffice.delete_flg' => 0,
				'RentOffice.area_id' => $areaId,
				'CommodityTerm.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
				'CarClass.delete_flg' => 0,
				'CarType.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'CommodityRentOffice',
					'table'=>'commodity_rent_offices',
					'conditions'=>'CommodityRentOffice.commodity_id = Commodity.id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'RentOffice',
					'table'=>'offices',
					'conditions'=>'RentOffice.id = CommodityRentOffice.office_id'
				),
				/*array(
					'type'=>'INNER',
					'alias'=>'OfficeStockGroup',
					'table'=>'office_stock_groups',
					'conditions'=>'RentOffice.id = OfficeStockGroup.office_id'
				),*/
				array(
					'type'=>'INNER',
					'alias'=>'CommodityTerm',
					'table'=>'commodity_terms',
					'conditions'=>'CommodityTerm.commodity_id = Commodity.id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'CommodityItem',
					'table'=>'commodity_items',
					'conditions'=>'CommodityItem.commodity_id = Commodity.id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'CarClass',
					'table'=>'car_classes',
					'conditions'=>'CarClass.id = CommodityItem.car_class_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'CarType',
					'table'=>'car_types',
					'conditions'=>'CarType.id = CarClass.car_type_id'
				)
			),
			'group' => array(
				'CommodityItem.id',
				'RentOffice.area_id'
			),
			'recursive' => -1
		);

		$commodityList = $Commodity->findC('all', $options, '1day');

		$itemToArea = array();
		$param = array();
		foreach ($commodityList as $v) {
			if (isset($itemToArea[$v['CommodityItem']['id']])) {
				$itemToArea[$v['CommodityItem']['id']][] = $v['RentOffice']['area_id'];
			} else {
				$itemToArea[$v['CommodityItem']['id']] = array($v['RentOffice']['area_id']);
				$param[] = array(
					'clientId' => $v['Commodity']['client_id'],
					'dayTimeFlg' => $v['Commodity']['day_time_flg'],
					'commodityItemId' => $v['CommodityItem']['id'],
					'carClassId' => $v['CommodityItem']['car_class_id'],
					'carTypeId' => $v['CarType']['id'],
					'carTypeName' => $v['CarType']['name'],
					//'stockGroupId' => $v['OfficeStockGroup']['stock_group_id']
				);
			}
		}

		// 在庫確認
		//$param = $this->carClassStockFilter($param, $dateFrom, $dateTo);
		// 暦日制料金（1日）
		$day_price = $this->dayPriceFilter($param, $dateFrom, $dateTo);
		// 時間制料金（6時間）
		$time_price = $this->timePriceFilter($param, $dateFrom, $dateTo);

		// 暦日制/時間制をマージ
		$prices = array_merge($day_price, $time_price);
		unset($day_price);
		unset($time_price);

		// 免責補償料金
		$prices = $this->disclaimerCompensationFilter($prices, $dateFrom, $dateTo);

		// 日産の当日予約専用を除く
		$prices = Hash::extract($prices, '{n}[basePrice<99999999]');

		// エリア分増幅
		$result = array();
		foreach ($prices as $p) {
			foreach ($itemToArea[$p['commodityItemId']] as $areaId) {
				$result[] = $p + array('areaId' => $areaId);
			}
		}

		return $result;
	}

	private function carClassStockFilter($commodities, $dateFrom, $dateTo) {
		if (empty($commodities)) {
			return array();
		}

		$stockGroups = Hash::extract($commodities, '{n}.stockGroupId');
		if (empty($stockGroups)) {
			return array();
		}

		$carClassIds = Hash::extract($commodities, '{n}.carClassId');
		if (empty($carClassIds)) {
			return array();
		}

		$Commodity = ClassRegistry::init('Commodity');

		list($stockGroupsParam, $stockGroupsValue) = $Commodity->createBindArray('stockGroup', $stockGroups);
		list($carClassIdsParam, $carClassIdsValue) = $Commodity->createBindArray('carClassId', $carClassIds);

		$params = array(
			'dateFrom' => $dateFrom,
			'dateTo' => $dateTo,
		);
		$params += $stockGroupsValue + $carClassIdsValue;

		$sql = "
			SELECT
				car_class_stocks.car_class_id,
				car_class_stocks.stock_group_id
			FROM
				rentacar.car_class_stocks
			LEFT JOIN
			(
				SELECT
					car_class_reservations.id,
					car_class_reservations.stock_group_id,
					car_class_reservations.car_class_id,
					car_class_reservations.stock_date,
					SUM(car_class_reservations.reservation_count) AS reservation_count
				FROM
					rentacar.car_class_reservations
				WHERE
					car_class_reservations.stock_group_id IN ({$stockGroupsParam})
				AND
					car_class_reservations.car_class_id IN ({$carClassIdsParam})
				AND
					car_class_reservations.stock_date >= :dateFrom
				AND
					car_class_reservations.stock_date <= :dateTo
				AND
					car_class_reservations.delete_flg = 0
				GROUP BY
					car_class_reservations.stock_group_id,
					car_class_reservations.car_class_id,
					car_class_reservations.stock_date
			) AS car_class_reservations ON car_class_stocks.stock_group_id = car_class_reservations.stock_group_id
			AND
				car_class_stocks.car_class_id = car_class_reservations.car_class_id
			AND
				car_class_stocks.stock_date = car_class_reservations.stock_date
			WHERE
				car_class_stocks.stock_group_id IN ({$stockGroupsParam})
			AND
				car_class_stocks.car_class_id IN ({$carClassIdsParam})
			AND
				car_class_stocks.stock_date >= :dateFrom
			AND
				car_class_stocks.stock_date <= :dateTo
			AND
				car_class_stocks.stock_count > 0
			AND
				car_class_stocks.suspension = 0
			AND
				(car_class_stocks.stock_count - coalesce(car_class_reservations.reservation_count,0)) > 0
			GROUP BY
				car_class_stocks.car_class_id,
				car_class_stocks.stock_group_id
			HAVING
				COUNT(car_class_stocks.car_class_id) > 0
		";

		$ret = $Commodity->queryC($sql, $params, '1hour');
		if (empty($ret)) {
			return array();
		}

		// 在庫がある商品のみを返す
		$ret_commodities = array();
		foreach ($commodities as $commodity) {
			foreach ($ret as $stock) {
				if ($commodity['stockGroupId'] == $stock['car_class_stocks']['stock_group_id'] && $commodity['carClassId'] == $stock['car_class_stocks']['car_class_id']) {
					$ret_commodities[] = $commodity;
				}
			}
		}

		return $ret_commodities;
	}

	private function dayPriceFilter($commodities, $dateFrom, $dateTo) {
		if (empty($commodities)) {
			return array();
		}

		$commodityItemIds = Hash::extract($commodities, '{n}[dayTimeFlg=0].commodityItemId');
		if (empty($commodityItemIds)) {
			return array();
		}

		$Commodity = ClassRegistry::init('Commodity');
		$price = array();
		$campaignPrice = array();

		list($commodityItemIdsParam, $commodityItemIdsValue) = $Commodity->createBindArray('commodityItemIds', $commodityItemIds);

		$Campaign = ClassRegistry::init('Campaign');
		$Campaign->setDataSource($Commodity->getDataSource()->configKeyName);
		$ret = $Campaign->getCampaignIds(false, $commodityItemIds, $dateFrom, $dateTo);
		if (!empty($ret)) {
			// キャンペーン料金
			$campaignIds = Hash::extract($ret, '{n}.CampaignTerm.campaign_id');

			list($campaignIdsParam, $campaignIdsValue) = $Commodity->createBindArray('campaignIds', $campaignIds);

			$params = $commodityItemIdsValue + $campaignIdsValue;

			$sql = "
					SELECT
					  commodity_campaign_prices.commodity_item_id
					  , MIN(commodity_campaign_prices.price) AS price
					FROM
					  rentacar.commodity_campaign_prices
					WHERE
					  commodity_campaign_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_campaign_prices.campaign_id IN ({$campaignIdsParam})
					  AND commodity_campaign_prices.price > 0
					  AND commodity_campaign_prices.span_count = 1
					  AND commodity_campaign_prices.delete_flg = 0
					GROUP BY
					  commodity_campaign_prices.commodity_item_id
				";

			$ret = $Commodity->queryC($sql, $params, '1day');
			if (!empty($ret)) {
				$campaignPrice = Hash::combine($ret, '{n}.commodity_campaign_prices.commodity_item_id', '{n}.0.price');
			}
		}

		if (!empty($commodityItemIds)) {
			// 通常料金
			$params = $commodityItemIdsValue;

			$sql = "
					SELECT
					  commodity_prices.commodity_item_id
					  , commodity_prices.price AS price
					FROM
					  rentacar.commodity_prices
					WHERE
					  commodity_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_prices.price > 0
					  AND commodity_prices.span_count = 1
					  AND commodity_prices.delete_flg = 0
				";

			$ret = $Commodity->queryC($sql, $params, '1day');
			if (!empty($ret)) {
				$price = Hash::combine($ret, '{n}.commodity_prices.commodity_item_id', '{n}.commodity_prices.price');
			}
		}

		if (empty($price)) {
			return array();
		}

		// 料金がある商品のみを返す
		$ret_commodities = array();
		foreach ($commodities as $commodity) {
			if (!empty($price[$commodity['commodityItemId']])) {
				$p = $price[$commodity['commodityItemId']];
				if (isset($campaignPrice[$commodity['commodityItemId']])) {
					// キャンペーン料金あるときは安い方
					$cp = $campaignPrice[$commodity['commodityItemId']];
					$p = $p <= $cp ? $p : $cp;
				}
				if (empty($commodity['price'])) {
					$commodity['price'] = $p;
				} else {
					$commodity['price'] += $p;
				}
				$commodity['basePrice'] = $p;

				$ret_commodities[] = $commodity;
			}
		}

		return $ret_commodities;
	}

	private function timePriceFilter($commodities, $dateFrom, $dateTo) {
		if (empty($commodities)) {
			return array();
		}

		$commodityItemIds = Hash::extract($commodities, '{n}[dayTimeFlg=1].commodityItemId');
		if (empty($commodityItemIds)) {
			return array();
		}

		$Commodity = ClassRegistry::init('Commodity');
		$price = array();
		$campaignPrice = array();

		list($commodityItemIdsParam, $commodityItemIdsValue) = $Commodity->createBindArray('commodityItemIds', $commodityItemIds);

		$Campaign = ClassRegistry::init('Campaign');
		$Campaign->setDataSource($Commodity->getDataSource()->configKeyName);
		$ret = $Campaign->getCampaignIds(false, $commodityItemIds, $dateFrom, $dateTo);
		if (!empty($ret)) {
			// キャンペーン料金
			$campaignIds = Hash::extract($ret, '{n}.CampaignTerm.campaign_id');

			list($campaignIdsParam, $campaignIdsValue) = $Commodity->createBindArray('campaignIds', $campaignIds);

			$params = $commodityItemIdsValue + $campaignIdsValue;

			$sql = "
					SELECT
					  commodity_campaign_prices.commodity_item_id
					  , MIN(commodity_campaign_prices.price) AS price
					FROM
					  rentacar.commodity_campaign_prices
					WHERE
					  commodity_campaign_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_campaign_prices.campaign_id IN ({$campaignIdsParam})
					  AND commodity_campaign_prices.price > 0
					  AND commodity_campaign_prices.span_count = 6
					  AND commodity_campaign_prices.delete_flg = 0
					GROUP BY
					  commodity_campaign_prices.commodity_item_id
				";

			$ret = $Commodity->queryC($sql, $params, '1day');
			if (!empty($ret)) {
				$campaignPrice = Hash::combine($ret, '{n}.commodity_campaign_prices.commodity_item_id', '{n}.0.price');
			}
		}

		if (!empty($commodityItemIds)) {
			// 通常料金
			$params = $commodityItemIdsValue;

			$sql = "
					SELECT
					  commodity_prices.commodity_item_id
					  , commodity_prices.price AS price
					FROM
					  rentacar.commodity_prices
					WHERE
					  commodity_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_prices.price > 0
					  AND commodity_prices.span_count = 6
					  AND commodity_prices.delete_flg = 0
				";

			$ret = $Commodity->queryC($sql, $params, '1day');
			if (!empty($ret)) {
				$price = Hash::combine($ret, '{n}.commodity_prices.commodity_item_id', '{n}.commodity_prices.price');
			}
		}

		if (empty($price)) {
			return array();
		}

		// 料金がある商品のみを返す
		$ret_commodities = array();
		foreach ($commodities as $commodity) {
			if (!empty($price[$commodity['commodityItemId']])) {
				$p = $price[$commodity['commodityItemId']];
				if (isset($campaignPrice[$commodity['commodityItemId']])) {
					// キャンペーン料金あるときは安い方
					$cp = $campaignPrice[$commodity['commodityItemId']];
					$p = $p <= $cp ? $p : $cp;
				}
				if (empty($commodity['price'])) {
					$commodity['price'] = $p;
				} else {
					$commodity['price'] += $p;
				}
				$commodity['basePrice'] = $p;

				$ret_commodities[] = $commodity;
			}
		}

		return $ret_commodities;
	}

	private function disclaimerCompensationFilter($commodities, $dateFrom, $dateTo) {
		if (empty($commodities)) {
			return array();
		}

		$carClassIds = Hash::extract($commodities, '{n}.carClassId');
		if (empty($carClassIds)) {
			return array();
		}

		$Commodity = ClassRegistry::init('Commodity');

		list($carClassIdsParam, $carClassIdsValue) = $Commodity->createBindArray('carClassIds', $carClassIds);

		$params = array(
			'dateFrom' => $dateFrom,
			'dateTo' => $dateTo,
		);

		$params += $carClassIdsValue;

		$sql = "
			SELECT
			  car_class_id,
			  MIN(price) as price
			FROM
			  rentacar.disclaimer_compensations
			WHERE
			  car_class_id IN ({$carClassIdsParam})
			AND
			  start_date <= :dateTo
			AND
			  end_date >= :dateFrom
			AND
			  delete_flg = 0
			GROUP BY
			  car_class_id
		    ";

		$ret = $Commodity->queryC($sql, $params, '1day');
		if (empty($ret)) {
			return array();
		}
		$ret = Hash::combine($ret, '{n}.disclaimer_compensations.car_class_id', '{n}.0.price');

		// 料金がある商品のみを返す
		$ret_commodities = array();
		foreach ($commodities as $commodity) {
			if (isset($ret[$commodity['carClassId']])) {
				$p = $ret[$commodity['carClassId']];
				if (empty($commodity['price'])) {
					$commodity['price'] = $p;
				} else {
					$commodity['price'] += $p;
				}
				$commodity['disclaimerCompensation'] = $p;

				$ret_commodities[] = $commodity;
			}
		}

		return $ret_commodities;
	}
}

<?php

App::uses('BaseContentsComponent', 'Controller/Component');

class StationContentsComponent extends BaseContentsComponent {

	/**
	 * 駅に紐づく会社と店舗の一覧を取得する
	 *
	 * @param int $stationId
	 * @return array
	 */
	public function getClientAndOfficeListByStationId($stationId) {
		$Office = ClassRegistry::init('Office');
		$OfficeSupplement = ClassRegistry::init('OfficeSupplement');

		// 駅に紐づく店舗一覧を取得
		$officeInfo = $Office->getOfficeNearListByStationId($stationId);

		if (empty($officeInfo)) {
			return array([], []);
		}

		$clientList = Hash::combine($officeInfo, '{n}.Client.id', '{n}.Client');
		$officeInfoList = Hash::combine($officeInfo, '{n}.Office.id', '{n}.Office');

		// 補足情報も追加する
		$officeSupplements = $OfficeSupplement->getOfficeSupplementByOfficeId(Hash::extract($officeInfoList, '{n}.id'));
		foreach ($officeInfoList as $k => &$v) {
			if (isset($officeSupplements[$k])) {
				unset($officeSupplements[$k]['office_id']);
				$v += $officeSupplements[$k];
			}
		}
		unset($v);

		return array($clientList, $officeInfoList);
	}

	/**
	 * 車両タイプの平均価格を返す
	 *
	 * @param int $carTypeId
	 * @param array $prices
	 * @return string
	 */
	public function getAveragePriceByCarType($carTypeId, $prices) {
		if (empty($carTypeId) || empty($prices)) {
			return 0;
		}

		$total = 0;
		$cnt = 0;

		foreach ($prices as $v) {
			// 車両タイプIDが一致する場合のみ
			if ($v['carTypeId'] == $carTypeId) {
				$total += $v['price'];
				$cnt += 1;
			}
		}

		// 見栄えが悪いので1の位切り捨て
		return $cnt > 0 ? number_format((floor($total / $cnt / 10) * 10)) : 0;
	}

	public function getPriceAndRankForClients($prices) {
		$clientPrices = Hash::combine($prices, '{n}.commodityItemId', '{n}', '{n}.clientId');
		$clientBestPrices = array();
		foreach ($clientPrices as $k => $v) {
			$clientBestPrices[$k] = array(
				'clientId' => $k,
				'bestPrice' => PHP_INT_MAX
			);
			foreach ($v as $r) {
				if ($clientBestPrices[$k]['bestPrice'] > $r['basePrice']) {
					$clientBestPrices[$k]['carTypeName'] = $r['carTypeName'];
					$clientBestPrices[$k]['bestPrice'] = $r['basePrice'];
				}
			}
		}
		unset($clientPrices);
		$clientRank = Hash::sort($clientBestPrices, '{n}.bestPrice', 'asc');
		unset($clientBestPrices);
		$clientBestPriceAndRank = array();
		foreach ($clientRank as $k => $v) {
			$clientBestPriceAndRank[$v['clientId']] = array(
				'id' => $v['clientId'],
				'carTypeName' => $v['carTypeName'],
				'rank' => $k + 1,
				'bestPrice' => $v['bestPrice']
			);
		}
		unset($clientRank);
		return $clientBestPriceAndRank;
	}

	/**
	 * おすすめ車種情報の取得
	 *
	 * @param array $officeIds
	 * @param array $carTypeIds
	 * @return array $recommendCarList
	 */
	public function getRecommendCarList(array $officeIds, array $carTypeIds = array()) {
		if (empty($officeIds) || empty($carTypeIds)) {
			return array();
		}

		$CommodityRentOffice = ClassRegistry::init('CommodityRentOffice');
		$Equipment = ClassRegistry::init('Equipment');
		$CommodityEquipment = ClassRegistry::init('CommodityEquipment');
		$CommodityPrivilege = ClassRegistry::init('CommodityPrivilege');

		// クエリキャッシュを効かせたいので1週間後10時固定にする
		$datetime = date('Y-m-d 10:00:00', strtotime('+7 day'));

		$ret = $CommodityRentOffice->findC('all', array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Commodity.id',
				'Commodity.new_car_registration',
				'Commodity.image_relative_url',
				'CommodityItem.id',
				'CarType.id',
				'CarType.name',
				'CarType.description',
				'CarModel.name',
				'CarModel.capacity',
				'CarModel.package_num',
				'Automaker.name'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('CommodityRentOffice.client_id = Client.id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Commodity',
					'table' => 'commodities',
					'conditions' => array('Commodity.id = CommodityRentOffice.commodity_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => array('Commodity.id = CommodityItem.commodity_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityTerm',
					'table' => 'commodity_terms',
					'conditions' => array('Commodity.id = CommodityTerm.commodity_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarClass',
					'table' => 'car_classes',
					'conditions' => array('CarClass.id = CommodityItem.car_class_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarType',
					'table' => 'car_types',
					'conditions' => array('CarType.id = CarClass.car_type_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarModel',
					'table' => 'car_models',
					'conditions' => array('CarModel.id = CommodityItem.car_model_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Automaker',
					'table' => 'automakers',
					'conditions' => array('Automaker.id = CarModel.automaker_id')
				),
			),
			'conditions' => array(
				'CommodityRentOffice.office_id' => $officeIds,
				'CommodityRentOffice.delete_flg' => 0,
				'Commodity.is_published' => 1,
				'Commodity.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
				'CommodityTerm.available_from <=' => $datetime,
				'CommodityTerm.available_to >=' => $datetime,
				'CommodityTerm.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'CarClass.delete_flg' => 0,
				'CarType.id' => $carTypeIds,
				'CarType.delete_flg' => 0,
				'CarModel.delete_flg' => 0,
				'Automaker.delete_flg' => 0,
			),
			'group' => array(
				'CarType.sort',
				'Client.sort',
				'CarModel.id',
			),
			'recursive' => -1,
		));

		if (empty($ret)) {
			return array();
		}

		$tmp = array();
		$recommendCarList = array();
		foreach ($ret as $v) {
			$carTypeId = $v['CarType']['id'];
			// 1車両タイプにつき1車種のみにする
			if (isset($tmp[$carTypeId])) {
				continue;
			}
			$recommendCarList[] = $v;
			$tmp[$carTypeId] = true;
		}
		unset($ret, $tmp);

		$commodityIds = Hash::extract($recommendCarList, '{n}.Commodity.id');

		// 装備取得
		$equipmentList = $Equipment->getEquipmentList();
		$commodityEquipments = $CommodityEquipment->getCommodityEquipment($commodityIds);

		// 特典取得
		$optionCategories = Constant::optionCategories();
		$commodityPrivileges = $CommodityPrivilege->getOptionCategoryIdList($commodityIds);

		// viewに必要な項目をセットする
		foreach ($recommendCarList as &$recommendCar) {
			//装備
			$equipments = array();
			if (!empty($commodityEquipments[$recommendCar['Commodity']['id']])) {
				foreach ($commodityEquipments[$recommendCar['Commodity']['id']] as $l => $v) {
					if (!empty($equipmentList[$l])) {
						$equipments[] = $equipmentList[$l];
					}
				}
			}
			$recommendCar['Equipment'] = $equipments;

			// 特典
			$privileges = array();
			if (!empty($commodityPrivileges[$recommendCar['Commodity']['id']])) {
				foreach ($commodityPrivileges[$recommendCar['Commodity']['id']] as $l => $v) {
					if (isset($optionCategories[$v])) {
						$privileges[] = $optionCategories[$v]['name'];
					}
				}
			}
			$recommendCar['Privilege'] = $privileges;
		}
		unset($recommendCar);

		return $recommendCarList;
	}

	/**
	 * 駅に紐づく店舗のレビューを返す
	 *
	 * @param int $stationId
	 * @return array
	 */
	public function getYotpoReviews($stationId){
		$YotpoReview = ClassRegistry::init('YotpoReview');

		$reviews = $YotpoReview->getReviewsByStationId($stationId);
		$reviewCount = $YotpoReview->getReviewCountByStationId($stationId);
		$yotpoReviews = array();

		$yotpoReviewLimit = Constant::YOTPO_REVIEW_LIMIT;

		foreach ($reviews as $review) {

			if($yotpoReviewLimit > count($yotpoReviews)){
				$yotpoReviews[] = array(
					'title' => $review['YotpoReview']['title'],
					'content' => $review['YotpoReview']['content'],
					'score' => $review['YotpoReview']['score'],
					'created_at' => $review[0]['created_at'],
					'client_id' => $review['Client']['id'],
					'client_name' => $review['Client']['name'],
					'client_url' => $review['Client']['url'],
					'office_name' => $review['Office']['name'],
					'office_url' => $review['Office']['url']
				);
			}

			$yotpoReviewOnlyScore[] = array(
				'score' => $review['YotpoReview']['score']
			);
		}

		return array($reviewCount, $yotpoReviews,$yotpoReviewOnlyScore);
	}

	/**
	 * おすすめ順に並べ替えた営業所を返す
	 *
	 * @param array $clientList
	 * @param array $officeInfoList
	 * @return array recomendedOfficeIds
	 */
	public function getRecomendedOfficeIds(array $clientList, array $officeInfoList) {
		$clientSortOrder = array();
		$officeSortOrder = array();
		$ret = array();

		foreach ($officeInfoList as $k => $v) {
			$clientSortOrder[] = $clientList[$v['client_id']]['sort'];
			$officeSortOrder[] = $v['sort'];
			$ret[] = $v['id'];
		}

		array_multisort($clientSortOrder, SORT_ASC, $officeSortOrder, SORT_ASC, $ret);

		return $ret;
	}

	/**
	 * 乗り捨て料金表を取得する
	 * 必要なもの 会社のリスト、営業所のリスト、緯度、経度、駅かどうか
	 *
	 * @param array $clients
	 * @param array $offices
	 * @param array $landmark
	 * @param boolean $isStation
	 * @return array
	 */
	public function getDropOffTable($clients, $offices, $landmark, $isStation = true) {
		if (empty($clients) || empty($offices) || empty($landmark)) {
			return array();
		}

		$Landmark = ClassRegistry::init('Landmark');
		$DropOffAreaRate = ClassRegistry::init('DropOffAreaRate');
		$Station = ClassRegistry::init('Station');

		$officeIds = array_values(array_unique(Hash::extract($offices, '{n}.id')));
		$areaIds = array_values(array_unique(Hash::extract($offices, '{n}.area_id')));

		// 最寄りの空港を選定
		$selectAirport = $Landmark->getNearestAirport($landmark['latitude'], $landmark['longitude']);

		// 駅ページの呼び出しの場合は自分自身を除外する
		$excludedStationId = !empty($isStation) ? $landmark['id'] : 0;

		// 予約実績に基づく駅の選定
		$selectStations = $Station->getStationFromReturnResult($officeIds, $areaIds, $excludedStationId, $selectAirport['short_name']);

		// 駅名に駅をつける(空港名と同じ場合は内部的には駅だが表示上は空港とする)
		foreach ($selectStations as &$station) {
			if (mb_substr($station['name'], -2) !== '空港') {
				$station['name'] .= '駅';
			}
		}
		unset($station);

		$locations = array_merge(array($selectAirport), $selectStations);

		// ヘッダの組み立て
		$header = array('会社名', '', '', '');

		foreach ($locations as $k => $location) {
			$name = isset($location['short_name']) ? $location['short_name'] : $location['name'];
			$header[$k + 1] = $name;
		}

		// 乗り捨て料金を取得
		$lowestPriceAirport = $DropOffAreaRate->getLowestPriceAirport($officeIds, $selectAirport['id']);
		$lowestPriceStation = $DropOffAreaRate->getLowestPriceStation($officeIds, Hash::extract($selectStations, '{n}.id'));

		// テーブルの組み立て
		$table = [];
		foreach ($clients as $clientId => $client) {
			$prices = array('', '', '');

			foreach ($locations as $k => $location) {
				if ($location['category'] == 'airport') {
					// 空港料金
					if (isset($lowestPriceAirport[$clientId][$location['id']])) {
						$prices[$k] = $lowestPriceAirport[$clientId][$location['id']];
					}
				} else if ($location['category'] == 'station') {
					// 駅料金
					if (isset($lowestPriceStation[$clientId][$location['id']])) {
						$prices[$k] = $lowestPriceStation[$clientId][$location['id']];
					}
				}
			}

			// 全てが空の場合は乗り捨て先が無い会社なので追加しない
			if ($prices[0] == '' && $prices[1] == '' && $prices[2] == '') {
				continue;
			}

			$table[] = array_merge(array($clientId), $prices);
		}

		// 最も返却された場所を取得する
		$popularLocation = call_user_func(function($locations) {
			$ret;
			foreach ($locations as $k => $v) {
				if (!isset($ret) || $ret['cnt'] < $v['cnt']) {
					$ret = array(
						'id' => $v['id'],
						'name' => isset($v['short_name']) ? $v['short_name'] : $v['name'],
						'cnt' => $v['cnt'],
						'num' => $k + 1, // この添え字は列との比較に使うので+1する
					);
				}
			}
			return $ret;
		}, $locations);

		// その他の場所を取得する
		$otherLocation = array();
		foreach ($header as $k => $v) {
			if ($k > 0 && $k != $popularLocation['num'] && $v != '') {
				$otherLocation[] = $v;
			}
		}

		$lowestPriceClientList = array();
		$lowestPrice;

		// 最安値を取得
		foreach ($table as &$tr) {
			$cnt = count($tr);
			for ($i = 1; $i <= $cnt; $i++) {
				if (!isset($locations[$i - 1])) {
					continue;
				}

				// 最も返却された場所の場合は最安値を取得する
				if ($i == $popularLocation['num'] && $tr[$i] != '') {
					if (!isset($lowestPrice) || $lowestPrice < $tr[$i]) {
						$lowestPrice = $tr[$i];
					}
				}
			}
		}

		// 各項目の数値を表示文言に置き換える
		foreach ($table as &$tr) {
			$cnt = count($tr);
			for ($i = 1; $i <= $cnt; $i++) {
				if (!isset($locations[$i - 1])) {
					continue;
				}

				// 最も返却された場所の最安値の場合は最安値クライアントリストに追加
				if ($i == $popularLocation['num'] && $tr[$i] != '') {
					if ($lowestPrice == $tr[$i]) {
						$lowestPriceClientList[] = $tr[0];
					}
				}
				$tr[$i] = $this->getDropOffPriceText($tr[$i]);
			}
		}
		unset($tr);

		return compact('header', 'table', 'popularLocation', 'otherLocation', 'lowestPriceClientList');
	}

	/**
	 * おすすめのレンタカーの情報を取得する
	 *
	 * @param array $clients
	 * @param array $offices
	 * @param string $stationName
	 * @param array $methodOfTransports
	 * @return array
	 */
	public function getAboutRecommendRentacar($clients, $offices, $stationName, $methodOfTransports = array()) {
		$ret = array();

		foreach ($clients as $clientId => $clientName) {
			foreach ($offices as $office) {
				// 最寄り交通機関が駅と一致する場合のみ
				if ($office['client_id'] == $clientId && strpos($office['access_dynamic'], $stationName) === 0) {
					$ret[] = array(
						'name' => $clientName . ' ' . $office['name'],
						'methodOfTransport' => $methodOfTransports[$office['method_of_transport']],
						'requiredTransportTime' => $office['required_transport_time'],
					);
					break;
				}
			}
		}

		return $ret;
	}

	/**
	 * レンタカー貸出までにかかる時間を取得する
	 *
	 * @param array $clients
	 * @param array $offices
	 * @param string $stationName
	 * @return array
	 */
	public function getAboutTimeRentacar($clients, $offices, $stationName) {
		$ret = array(
			'rentProcTime' => 0,
			'rentProcTimeBusy' => 0,
			'pickupCount' => 0,
			'walkCount' => 0,
			'pickupWaitTime' => 0,
			'requiredTransportTime' => 0,
		);

		$cnt = 0;
		foreach ($offices as $officeId => $office) {
			// 補足情報が無ければスキップ
			if (!isset($office['rent_proc_time'])) {
				continue;
			}

			$cnt += 1;

			$ret['rentProcTime'] += $office['rent_proc_time'];
			$ret['rentProcTimeBusy'] += $office['rent_proc_time_busy'];

			if (!isset($ret['minRentProcTime']) || $ret['minRentProcTime'] > $office['rent_proc_time']) {
				$ret['minRentClientName'] = $clients[$office['client_id']]['name'];
				$ret['minRentProcTime'] = $office['rent_proc_time'];
			}

			$accessDynamic = !empty($office['access_dynamic']) ? $office['access_dynamic'] : '';

			// 最寄り交通機関が駅と一致するか
			if (strpos($accessDynamic, $stationName) === 0) {
				// 徒歩
				if ($office['method_of_transport'] == '0') {
					$ret['walkCount'] += 1;
				}
				// 送迎
				if ($office['method_of_transport'] == '1' || $office['method_of_transport'] == '2') {
					$ret['pickupCount'] += 1;
					// 送迎待ち時間
					$ret['pickupWaitTime'] += $office['pickup_wait_time'];
					// 送迎時間
					$ret['requiredTransportTime'] += $office['required_transport_time'];
				}
			}
		}

		// 合計値から平均値へ
		if ($cnt > 0) {
			$ret['rentProcTime'] = (int)($ret['rentProcTime'] / $cnt);
			$ret['rentProcTimeBusy'] = (int)($ret['rentProcTimeBusy'] / $cnt);
		}
		if ($ret['pickupCount'] > 0) {
			$ret['pickupWaitTime'] = (int)($ret['pickupWaitTime'] / $ret['pickupCount']);
			$ret['requiredTransportTime'] = (int)($ret['requiredTransportTime'] / $ret['pickupCount']);
		}

		return $ret;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $officeIdList
	 * @return void
	 */
	public function getPopularCarType($offices) {
		$CarModel = ClassRegistry::init('CarModel');

		// 車種数カウント
		$carModelCount = $CarModel->countByOfficeId($offices);

		// 直近2か月間を対象とする
		$from = date('Y-m-d 00:00:00', strtotime('-61 day'));
		$to = date('Y-m-d 23:59:59', strtotime('-1 day'));

		// 人気の車両タイプを取得
		$CarModel->virtualFields = array(
			'max_capacity'		 => 'MAX(CarModel.capacity)',
			'max_trunk'			 => 'MAX(CarModel.trunk_space)',
			'avg_capacity'	 => 'TRUNCATE(AVG(Reservation.adults_count + Reservation.children_count * (2 / 3) + Reservation.infants_count * (2 / 3)), 0)',
		);

		$options = array(
			'fields' => array(
				'CarType.id',
				'CarType.name',
				'max_capacity',
				'max_trunk',
				'avg_capacity',
			),
			'joins' => array(
				array(
					'table' => 'automakers',
					'alias' => 'Automaker',
					'type' => 'INNER',
					'conditions' => 'Automaker.id = CarModel.automaker_id'
				),
				array(
					'table' => 'client_car_models',
					'alias' => 'ClientCarModel',
					'type' => 'INNER',
					'conditions' => 'CarModel.id = ClientCarModel.car_model_id'
				),
				array(
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'type' => 'INNER',
					'conditions' => 'CarClass.id = ClientCarModel.car_class_id'
				),
				array(
					'table' => 'car_types',
					'alias' => 'CarType',
					'type' => 'INNER',
					'conditions' => 'CarType.id = CarClass.car_type_id'
				),
				array(
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'type' => 'INNER',
					'conditions' => 'CarClass.id = CommodityItem.car_class_id',
				),
				array(
					'table' => 'reservations',
					'alias' => 'Reservation',
					'type' => 'INNER',
					'conditions' => 'CommodityItem.id = Reservation.commodity_item_id',
				),
			),
			'conditions' => array(
				'Reservation.rent_office_id' => $offices,
				'Reservation.reservation_datetime >=' => $from,
				'Reservation.reservation_datetime <=' => $to,
				'Reservation.delete_flg' => 0,
				'Automaker.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
				'CarClass.delete_flg' => 0,
				'CarType.delete_flg' => 0,
				'ClientCarModel.delete_flg' => 0,
				'CarModel.delete_flg' => 0,
			),
			'group' => 'CarType.id',
			'order' => 'count(*) DESC',
			'recursive' => -1,
		);
		$carTypeResult = $CarModel->find('first', $options);

		if (empty($carTypeResult)) {
			return array();
		}

		$carTypeId = $carTypeResult['CarType']['id'];

		$CarModel->virtualFields = array(
			'name' => "CONCAT(Automaker.name, ' ', CarModel.name)"
		);

		// 人気の車両タイプから人気の車種を取得する
		$options = array(
			'fields' => 'name',
			'joins' => $options['joins'],
			'conditions' => $options['conditions'] + array('car_type_id' => $carTypeId),
			'group' => 'CarModel.id',
			'order' => 'count(*) DESC',
			'limit' => 2,
			'recursive' => -1,
		);

		$carModelResult = $CarModel->find('list', $options);

		if(empty($carModelResult)) {
			return array();
		}

		return array(
			'carModelCount' => $carModelCount,
			'carTypeId' => $carTypeId,
			'carTypeName' => $carTypeResult['CarType']['name'],
			'maxCapacity' => $carTypeResult['CarModel']['max_capacity'],
			'maxTrunk' => $carTypeResult['CarModel']['max_trunk'],
			'avgCapacity' => $carTypeResult['CarModel']['avg_capacity'],
			'carModelNames' => array_values($carModelResult),
		);
	}

	/**
	 * 乗り捨て料金を表示用に整形する
	 *
	 * @param int $value
	 * @return string
	 */
	private function getDropOffPriceText($value) {
		if (is_numeric($value)) {
			return ($value > 0) ? '&yen' . number_format($value) : '無料';
		} else if ($value == '') {
			return '乗り捨て適用なし';
		} else {
			return $value;
		}
	}

	/**
	 * 駅IDから１年分の車種別最低価格カレンダーデータを返す
	 */
	public function createBestPriceCalendar($stationId) {
        $dispMonths = 12;
        // 重いページはカレンダー期間を3ヶ月にする
	    if (in_array($stationId, array(
            70,      // 札幌
            97,      // 旭川
            2075,    // 新潟
            1,       // 函館
            891,     // 仙台
            4039,    // 博多
        ))) {
	        $dispMonths = 3;
        }
		$Commodity = ClassRegistry::init('Commodity');
		$from = new DateTime('first day of today');
		$to = new DateTime('last day of +'.($dispMonths - 1).' month today');
		$from = new DateTime($from->format('Y-m-d 00:00:00'));
		$to = new DateTime($to->format('Y-m-d 23:59:59'));
		$commodities = $this->_getCommoditiesByStation($stationId, $from, $to);
		$campaigns = $this->_getCampaigns($commodities, $from, $to);
		$disclaimers = $this->_getDisclaimer($commodities, $from, $to);
		$public_holidays = $this->_getPublicHolidaysByTerm($from, $to);
		$stock_group_ids = array_unique(Hash::extract($commodities, '{n}.OfficeStockGroup.stock_group_id'));
		$car_class_ids = array_unique(Hash::extract($commodities, '{n}.CarClass.id'));
		$stock_info = $this->_getStocks($stock_group_ids, $car_class_ids, $from, $to);
		$stocks = $stock_info['stocks'];
		$reservations = $stock_info['reservations'];
		$weekday_abbrs = ['sun', 'mon', 'tue', 'web', 'thu', 'fri', 'sat'];
		$today_unixtime = strtotime('today');
		$calendar = array();

		// デバッグ用
		if (Configure::read('debug') > 0) {
			$calendar['commodities'] = $commodities;
			$calendar['public_holidays'] = $public_holidays;
			$calendar['campaigns'] = $campaigns;
			$calendar['stock_info'] = $stock_info;
			$calendar['disclaimers'] = $disclaimers;
		}

		// 下準備
		foreach ($commodities as $key => $item) {
			$term = $item['CommodityTerm'];
			$commodities[$key]['CommodityTerm']['available_from'] = new DateTime(explode(' ', $term['available_from'])[0]);
			$commodities[$key]['CommodityTerm']['available_to'] = new DateTime(explode(' ', $term['available_to'])[0]);
			$commodities[$key]['CommodityPrice']['price'] = intval($item['CommodityPrice']['price']);
			if ($commodities[$key]['OfficeBusinessHours']['start_day']) {
				$commodities[$key]['OfficeBusinessHours']['start_day'] = new DateTime($commodities[$key]['OfficeBusinessHours']['start_day']);
			}
			if ($commodities[$key]['OfficeBusinessHours']['end_day']) {
				$commodities[$key]['OfficeBusinessHours']['end_day'] = new DateTime($commodities[$key]['OfficeBusinessHours']['end_day']);
			}
		}

		// カレンダーを作成
		for ($i = 0; $i < $dispMonths; $i++) {
			// 月
			$month = new DateTime('+' . $i . 'month ' . $from->format('Y-m'));
			$year = $month->format('Y');
			if (!isset($calendar[$year])) {
				$calendar[$year] = array();
			}

			// 日
			$yearMonth = $month->format('Y-m');
			$last_day = (new DateTime('last day of ' . $yearMonth))->format('d');
			$days = array();
			for ($day = 1; $day < $last_day + 1; $day++) {
				$date = new DateTime($yearMonth . '-' . $day);
				$date_string = $date->format('Y-m-d');
				// カレンダー作成時に11時を指定しているので11時で判定する
				$datetime_string = $date_string . ' 11:00:00';
				$week_day_num = intval($date->format('w'));
				$is_holiday = isset($public_holidays[$date_string]);
				$days_wait = ($date->format('U') - $today_unixtime) / 86400;

				// 各種価格等を初期化
				$prices = array(
					'1' => '',
					'2' => '',
					'3' => '',
					'5' => '',
					'9' => '',
					'day' => $week_day_num,
					'is_holiday' => $is_holiday,
				);

				// デバッグ用データ
				if (Configure::read('debug') > 0) {
					$prices['commodities'] = array();
					$prices['days_wait'] = $days_wait;
				}

				// 過去の日付の場合は最安値を検索しない
				if ($days_wait < 0) {
					$days[$day] = $prices;
					continue;
				}

				// この日に借りられる商品を選別して最安値であれば代入
				foreach($commodities as $item) {
					// 該当する車種ではなければ無視
					$car_type_id = $item['CarClass']['car_type_id'];
					if (!isset($prices[$car_type_id])) {
						continue;
					}

					// 期間外であれば無視
					$term = $item['CommodityTerm'];
					$businessHours = $item['OfficeBusinessHours'];
					if (!($term['available_from'] <= $date && $term['available_to'] >= $date)) {
						continue;
					}

					// 受付可能日数以内でなければ無視
					if (!is_null($term['bookable_days']) && $term['bookable_days'] < $days_wait) {
						continue;
					}

					// 締切時間の判定
					$deadline = $Commodity->calculateDeadline($date_string, $datetime_string, $term['deadline_hours'], $term['deadline_days'], $term['deadline_time']);
					if (!$deadline) {
					        continue;
					}

					// 営業日でなければ無視
					$weekday_abbr = $is_holiday ? 'hol' : $weekday_abbrs[$week_day_num];
					if (
						$businessHours['start_day'] !== null
						&& $businessHours['end_day'] !== null
						&& !(
							$businessHours['start_day'] <= $date
							&& $businessHours['end_day'] >= $date
						)
						&& (
							$businessHours[$weekday_abbr . '_hours_from'] === null
							|| $businessHours[$weekday_abbr . '_hours_to'] === null
						)
					) {
						continue;
					}

					// 在庫がなければ無視
					$car_class_id = $item['CarClass']['id'];
					$stock_group_id = $item['OfficeStockGroup']['stock_group_id'];
					$stock = isset($stocks[$stock_group_id][$car_class_id][$date_string]) ? $stocks[$stock_group_id][$car_class_id][$date_string] : 0;
					$reservation = isset($reservations[$stock_group_id][$car_class_id][$date_string]) ? $reservations[$stock_group_id][$car_class_id][$date_string] : 0;
					$remains = $stock - $reservation;
					if ($remains <= 0) {
						continue;
					}

					// 基本価格取得
					$item_id = $item['CommodityItem']['id'];
					$base_price = $item['CommodityPrice']['price'];
					$campaign_price = 0;
					$disclaimer_price = null;

					// campaign があれば $basePrice を置き換え
					if (isset($campaigns[$item_id])) {
						foreach($campaigns[$item_id] as $campaign) {
							if (in_array($week_day_num, $campaign['allow_days']) ||
								(in_array(7, $campaign['allow_days']) && $is_holiday)) {
								if ($campaign['start_date'] <= $date &&  $campaign['end_date'] >= $date) {
									$base_price = $campaign['price'];
									$campaign_price = $campaign['price'];
								}
							}
						}
					}

					// 免責補償金額
					if (isset($disclaimers[$car_class_id])) {
						foreach($disclaimers[$car_class_id] as $disclaimer) {
							if ($disclaimer['start_date'] <= $date &&  $disclaimer['end_date'] >= $date) {
								$disclaimer_price = $disclaimer['price'];
							}
						}
					}

					// 免責補償がない場合は無視
					if ($disclaimer_price === null) {
						continue;
					}

					// 価格
					$price = $base_price + $disclaimer_price;

					// 最安値であれば代入
					if ($prices[$car_type_id] === '' || $prices[$car_type_id] > $price) {
						$prices[$car_type_id] = $price;
					}

					// デバッグ用データ
					if (Configure::read('debug') > 0) {
						$prices['commodities'][] = array(
							'id' => $item['Commodity']['id'],
							'price' => $price,
							'item_id' => $item_id,
							'car_type_id' => $car_type_id,
							'car_class_id' => $car_class_id,
							'stock_group_id' => $stock_group_id,
							'base_price' => $base_price,
							'campaign_price' => $campaign_price,
							'disclaimer_price' => $disclaimer_price,
							'remains' => $remains,
							'stock' => $stock,
							'reservation' => $reservation,
							'commodity' => $item,
						);
					}
				}
				$days[$day] = $prices;
			}
			$calendar[$year][$month->format('n')] = $days;
		}

		return $calendar;
	}


	/**
	 * 駅IDと期間から商品リストを取得
	 */
	protected function _getCommoditiesByStation($stationId, $from, $to) {
		$Station = ClassRegistry::init('Station');
		$term_from = $from->format('Y-m-d 00:00:00');
		$term_to = $to->format('Y-m-d 23:59:59');
		$term_day_from = $from->format('Y-m-d');
		$term_day_to = $to->format('Y-m-d');
		$conditions = array(
			'Station.id' => $stationId,
			'Office.accept_rent' => true,
			'NOT' => array(
				'CommodityRentOffice.commodity_id' => null,
			),
			'Commodity.is_published' => true,
			array(
				'OR' => array(
					'CommodityTerm.available_from between ? and ?' => array($term_from, $term_to),
					'CommodityTerm.available_to between ? and ?' => array($term_from, $term_to),
					'AND' => array(
						'CommodityTerm.available_from <' => $term_from,
						'CommodityTerm.available_to >' => $term_to,
					),
				),
			),
			'CommodityPrice.span_count' => 1,
			'Station.delete_flg' => 0,
			'OfficeStation.delete_flg' => 0,
			'Office.delete_flg' => 0,
			'Client.delete_flg' => 0,
			'CommodityRentOffice.delete_flg' => 0,
			'Commodity.delete_flg' => 0,
			'CommodityTerm.delete_flg' => 0,
			'CommodityItem.delete_flg' => 0,
			'CarClass.delete_flg' => 0,
			'CommodityPrice.delete_flg' => 0,
		);
		$params = array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'OfficeStation',
					'table' => 'office_stations',
					'conditions' => array(
						'OfficeStation.station_id = Station.id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => array(
						'Office.id = OfficeStation.office_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'OfficeStockGroup',
					'table' => 'office_stock_groups',
					'conditions' => array(
						'OfficeStockGroup.office_id = Office.id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array(
						'Client.id = Office.client_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityRentOffice',
					'table' => 'commodity_rent_offices',
					'conditions' => array(
						'CommodityRentOffice.office_id = Office.id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Commodity',
					'table' => 'commodities',
					'conditions' => array(
						'Commodity.id = CommodityRentOffice.commodity_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityTerm',
					'table' => 'commodity_terms',
					'conditions' => array(
						'CommodityTerm.commodity_id = Commodity.id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => array(
						'CommodityItem.commodity_id = Commodity.id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarClass',
					'table' => 'car_classes',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityPrice',
					'table' => 'commodity_prices',
					'conditions' => array(
						'CommodityPrice.commodity_item_id = CommodityItem.id',
						'CommodityPrice.price != 0',
					)
				),
				array(
					'type' => 'LEFT',
					'alias' => 'OfficeBusinessHours',
					'table' => 'office_business_hours',
					'conditions' => array(
						'OfficeBusinessHours.office_id = Office.id',
						'OfficeBusinessHours.delete_flg' => 0,
						array(
							'OR' => array(
								'OfficeBusinessHours.start_day between ? and ?' => array($term_day_from, $term_day_to),
								'OfficeBusinessHours.end_day between ? and ?' => array($term_day_from, $term_day_to),
								'AND' => array(
									'OfficeBusinessHours.start_day <' => $term_day_from,
									'OfficeBusinessHours.end_day >' => $term_day_to,
								),
							),
						),
					)
				),
			),
			'recursive' => -1,
			'fields' => array(
				'Station.id',
				'Station.name',
				'Commodity.id',
				'CommodityItem.id',
				'CommodityTerm.id',
				'CommodityTerm.available_from',
				'CommodityTerm.available_to',
				'CommodityTerm.bookable_days',
				'CommodityTerm.deadline_hours',
				'CommodityTerm.deadline_days',
				'CommodityTerm.deadline_time',
				'CarClass.id',
				'CarClass.car_type_id',
				'CommodityPrice.id',
				'CommodityPrice.price',
				'CommodityPrice.span_count',
				'OfficeStockGroup.stock_group_id',
				'OfficeStockGroup.office_id',
				'OfficeBusinessHours.*',
			),
			'group' => array(
				'Commodity.id',
				'CommodityItem.id',
				'CommodityItem.car_class_id',
				'CommodityTerm.id',
				'CarClass.car_type_id',
				'CommodityPrice.id',
				'OfficeStockGroup.stock_group_id',
				'OfficeBusinessHours.id',
			),
		);
		$commodities = $Station->findC('all', $params);
		return $commodities;
	}

	/**
	 * 商品リストと期間からキャンペーン価格を取得
	 */
	protected function _getCampaigns($commodities, $from, $to) {
		$CommodityCampaignPrice = ClassRegistry::init('CommodityCampaignPrice');
		$term_from = $from->format('Y-m-d');
		$term_to = $to->format('Y-m-d');
		$commodity_item_ids = array();
		foreach($commodities as $commodity) {
			$item_id = $commodity['CommodityItem']['id'];
			if (!in_array($item_id, $commodity_item_ids)) {
				$commodity_item_ids[] = $item_id;
			}
		}

		$options = array(
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Campaign',
					'table' => 'campaigns',
					'conditions' => array(
						'Campaign.id = CommodityCampaignPrice.campaign_id',
						'Campaign.delete_flg' => 0,
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CampaignTerm',
					'table' => 'campaign_terms',
					'conditions' => array(
						'CampaignTerm.campaign_id = Campaign.id',
						'CampaignTerm.delete_flg' => 0,
						array(
							'OR' => array(
								'CampaignTerm.start_date between ? and ?' => array($term_from, $term_to),
								'CampaignTerm.end_date between ? and ?' => array($term_from, $term_to),
								'AND' => array(
									'CampaignTerm.start_date <' => $term_from,
									'CampaignTerm.end_date >' => $term_to,
								),
							),
						),
					)
				),
			),
			'conditions' => array(
				'CommodityCampaignPrice.commodity_item_id' => $commodity_item_ids,
				'CommodityCampaignPrice.span_count' => 1,
				'CommodityCampaignPrice.delete_flg' => 0,
			),
			'recursive' => -1,
			'fields' => array(
				'CommodityCampaignPrice.commodity_item_id',
				'CommodityCampaignPrice.price',
				'CommodityCampaignPrice.span_count',
				'CampaignTerm.start_date',
				'CampaignTerm.end_date',
				'CampaignTerm.mon',
				'CampaignTerm.tue',
				'CampaignTerm.wed',
				'CampaignTerm.thu',
				'CampaignTerm.fri',
				'CampaignTerm.sat',
				'CampaignTerm.sun',
				'CampaignTerm.hol',
			)
		);
		$campaigns = $CommodityCampaignPrice->findC('all', $options);
		$result = array();
		foreach($campaigns as $campaign) {
			$item_id = $campaign['CommodityCampaignPrice']['commodity_item_id'];
			if (!isset($result[$item_id])) {
				$result[$item_id] = array();
			}
			$result[$item_id][] = array(
				'price' => intval($campaign['CommodityCampaignPrice']['price']),
				'start_date' => new DateTime($campaign['CampaignTerm']['start_date']),
				'end_date' => new DateTime($campaign['CampaignTerm']['end_date']),
				'allow_days' => $this->_getDayForCampaignTerm($campaign['CampaignTerm'])
			);
		}
		return $result;
	}

	/**
	 * 商品リストと期間から補償金額を取得
	 */
	protected function _getDisclaimer($commodities, $from, $to) {
		$DisclaimerCompensation = ClassRegistry::init('DisclaimerCompensation');
		$term_from = $from->format('Y-m-d');
		$term_to = $to->format('Y-m-d');

		// 車クラスID一覧を作成
		$car_class_ids = array();
		foreach($commodities as $item) {
			$car_class_id = $item['CarClass']['id'];
			if (!in_array($car_class_id, $car_class_ids)) {
				$car_class_ids[] = $car_class_id;
			}
		}

		// 補償金額を取得
		$options = array(
			'conditions' => array(
				'DisclaimerCompensation.car_class_id' => $car_class_ids,
				'DisclaimerCompensation.delete_flg' => 0,
				array(
					'OR' => array(
						'DisclaimerCompensation.start_date between ? and ?' => array($term_from, $term_to),
						'DisclaimerCompensation.end_date between ? and ?' => array($term_from, $term_to),
						'AND' => array(
							'DisclaimerCompensation.start_date <' => $term_from,
							'DisclaimerCompensation.end_date >' => $term_to,
						),
					),
				),
			),
			'recursive' => -1,
			'fields' => array(
				'DisclaimerCompensation.*'
			)
		);
		$disclaimers = $DisclaimerCompensation->findC('all', $options);
		$result = array();
		foreach($disclaimers as $disclaimer) {
			$car_class_id = $disclaimer['DisclaimerCompensation']['car_class_id'];
			if (!isset($result[$car_class_id])) {
				$result[$car_class_id] = array();
			}
			$result[$car_class_id][] = array(
				'price' => intval($disclaimer['DisclaimerCompensation']['price']),
				'start_date' => new DateTime($disclaimer['DisclaimerCompensation']['start_date']),
				'end_date' => new DateTime($disclaimer['DisclaimerCompensation']['end_date']),
			);
		}
		return $result;
	}

	/**
	 * 指定された期間内の祝日を取得
	 */
	protected function _getPublicHolidaysByTerm($from, $to) {
		$PublicHoliday = ClassRegistry::init('PublicHoliday');
		$term_from = $from->format('Y-m-d');
		$term_to = $to->format('Y-m-d');
		$public_holidays = $PublicHoliday->findC('list', array(
			'conditions' => array(
				'PublicHoliday.date >=' => $term_from,
				'PublicHoliday.date <=' => $term_to,
				'PublicHoliday.delete_flg' => 0,
			),
			'recursive' => -1,
			'fields' => array(
				'PublicHoliday.date',
				'PublicHoliday.name',
			)
		));
		return $public_holidays;
	}

	/**
	 * 在庫管理地域IDリスト、車クラスIDリスト、期間から在庫データを取得
	 */
	protected function _getStocks($stock_group_ids, $car_class_ids, $from, $to) {
		if (empty($stock_group_ids) || empty($car_class_ids)) {
			return array(
				'stocks' => array(),
				'reservations' => array(),
			);
		}
		$CarClassStock = ClassRegistry::init('CarClassStock');
		$CarClassReservation = ClassRegistry::init('CarClassReservation');
		$term_from = $from->format('Y-m-d');
		$term_to = $to->format('Y-m-d');

		// 割当
		$stocks = $CarClassStock->findC('all', array(
			'conditions' => array(
				'CarClassStock.stock_group_id IN' => $stock_group_ids,
				'CarClassStock.car_class_id IN' => $car_class_ids,
				'CarClassStock.stock_date >=' => $term_from,
				'CarClassStock.stock_date <=' => $term_to,
				'CarClassStock.suspension =' => 0,			// 販売中のみ
			),
			'recursive' => -1,
		));
		$stockList = array();
		foreach ($stocks as $item) {
			$group_id = $item['CarClassStock']['stock_group_id'];
			$car_class_id = $item['CarClassStock']['car_class_id'];
			$date = $item['CarClassStock']['stock_date'];
			$count = intval($item['CarClassStock']['stock_count']);

			if (!isset($stockList[$group_id])) {
				$stockList[$group_id] = array();
			}
			if (!isset($stockList[$group_id][$car_class_id])) {
				$stockList[$group_id][$car_class_id] = array();
			}

			$stockList[$group_id][$car_class_id][$date] = $count;
		}

		// 引当
		$reservations = $CarClassReservation->findC('all', array(
			'conditions' => array(
				'CarClassReservation.stock_group_id IN' => $stock_group_ids,
				'CarClassReservation.car_class_id IN' => $car_class_ids,
				'CarClassReservation.stock_date >=' => $term_from,
				'CarClassReservation.stock_date <=' => $term_to,
			),
			'recursive' => -1,
		));
		$reservationList = array();
		foreach ($reservations as $item) {
			$group_id = $item['CarClassReservation']['stock_group_id'];
			$car_class_id = $item['CarClassReservation']['car_class_id'];
			$date = $item['CarClassReservation']['stock_date'];
			$count = intval($item['CarClassReservation']['reservation_count']);

			if (!isset($reservationList[$group_id])) {
				$reservationList[$group_id] = array();
			}
			if (!isset($reservationList[$group_id][$car_class_id])) {
				$reservationList[$group_id][$car_class_id] = array();
			}

			$reservationList[$group_id][$car_class_id][$date] = $count;
		}

		return array(
			'stocks' => $stockList,
			'reservations' => $reservationList,
		);
	}

	private function _getDayForCampaignTerm($campaignTerm) {
		$res = [];
		if ($campaignTerm['mon']) {
			$res[] = 1;
		}
		if ($campaignTerm['tue']) {
			$res[] = 2;
		}
		if ($campaignTerm['wed']) {
			$res[] = 3;
		}
		if ($campaignTerm['thu']) {
			$res[] = 4;
		}
		if ($campaignTerm['fri']) {
			$res[] = 5;
		}
		if ($campaignTerm['sat']) {
			$res[] = 6;
		}
		if ($campaignTerm['sun']) {
			$res[] = 0;
		}
		if ($campaignTerm['hol']) {
			$res[] = 7;
		}
		return $res;
	}
}

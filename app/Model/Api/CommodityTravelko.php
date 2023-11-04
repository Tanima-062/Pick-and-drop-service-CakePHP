<?php

App::uses('BaseCommodityMetasearch', 'Model');

final class CommodityTravelko extends BaseCommodityMetasearch {

	// paginateをオーバーライド
	// ページャー処理は$this->_commoditiesを使ってphp側で制御するのでクエリでは処理しない。
	// また、クエリの結果はcommodityのみを取得しているので、$this->_commoditiesから必要な項目を追加している。
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		if (empty($this->_commodities)) {
			return array();
		}
		
		$ret = $this->findC('all', array(
			'conditions' => $conditions,
			'fields' => $fields,
		));

		$commodities = array_slice($this->_commodities, ($page - 1) * $limit, $limit);
		
		// 車種取得
		$CommodityItem = new CommodityItem();
		$carInfoList = $CommodityItem->getCarInfo($this->extract($commodities, '{n}.commodityItemId'));

		// 装備取得
		$CommodityEquipment = new CommodityEquipment();
		$commodityEquipment = $CommodityEquipment->getCommodityEquipment($this->extract($commodities, '{n}.commodityId'));
		
		$optionCategories = Constant::optionCategories();
		
		// webサーバをurlとして返したいので
		$img_url = $this->getImageUrl();
		
		$plan_list = array();
		
		foreach ((array)$commodities as $k => $commodity) {
			foreach ((array)$ret as $ret_k => $ret_v) {
				$commodity_id = $ret_v['Commodity']['id'];
			
				if ($commodity_id != $commodity['commodityId']) {
					continue;
				}
				
				// オプション合計料金＝オプションリストの合計料金＋免責料金＋深夜料金
				$total_option_price = $commodity['disclaimerCompensation'];
				if (!empty($commodity['lateNightFee'])) {
					$total_option_price += $commodity['lateNightFee'];
				}

				// 合計料金＝基本料金＋オプション合計料金＋乗捨料金
				$total_price = $commodity['basePrice'] + $total_option_price;
				if (!empty($commodity['dropOffCharge'])) {
					$total_price += $commodity['dropOffCharge'];
				}
				
				$shop_info = array(
					'shop_id' => $commodity['officeId'],
					'shop_name' => $commodity['clientName'] . ' ' . $commodity['officeName'],
				);
				
				$carInfo = $carInfoList[$commodity['commodityItemId']];
				$car_model = $carInfo['CarModel'][0];
				
				$plan_info = array(
					'plan_id'			 => $commodity['commodityItemId'],
					'plan_name'			 => $ret_v['Commodity']['name'],
					'plan_detail'		 => mb_substr(trim(strip_tags(str_replace(array("\r\n", "\r", '<br>'), "\n", $ret_v['Commodity']['description']))), 0, 500, 'utf-8'),
					'car_id'			 => $carInfo['CarType']['travelko_id'],
					'car_name'			 => $carInfo['CarType']['name'],
					'car_img_url'		 => $img_url . $ret_v['Commodity']['client_id'] . '/' . $ret_v['Commodity']['image_relative_url'],
					'car_capa'			 => $car_model['capacity'],
					'car_type'			 => $this->getCarModelName('・', $carInfo['CarModel']),
					'car_type_on'		 => '0', // 指定出来ないので0固定
					'car_escape_flg'	 => '1', // 免責料金あり
					'car_escape_price'	 => $commodity['disclaimerCompensation'],
					'base_price'		 => $commodity['basePrice'],
					'total_price'		 => $total_price,
					'total_option_price' => $total_option_price,
				);
				
				if (!empty($commodity['lateNightFee'])) {
					$plan_info['night_price'] = $commodity['lateNightFee'];
				}

				$plan_info['status'] = '1'; // 指定出来ないので1固定
				
				// 禁煙・喫煙は商品のフラグから
				$smoking_id = !empty($ret_v['Commodity']['smoking_flg']) ? 1 : 2;

				// オプションのリストを生成する
				$option_list = array();
				$option_list[] = array(
					'option_id'		 => $optionCategories[$smoking_id]['travelko_id'],
					'option_name'	 => $optionCategories[$smoking_id]['name'],
					'option_default' => '1',
					'option_num'	 => '1',
					'option_price'	 => '0',
				);
				
				$options = !empty($commodity['Option']) ? $commodity['Option'] : array();
				
				foreach ($options as $option) {
					if (!isset($optionCategories[$option['option_category_id']])) {
						continue;
					}
					
					$option_category = $optionCategories[$option['option_category_id']];
					
					if (isset($option_category['travelko_id'])) {
						$option_list[] = array(
							'option_id'		 => $option_category['travelko_id'],
							'option_name'	 => $option['name'],
							'option_default' => $option['option_default'],
							'option_num'	 => '1',
							'option_price'	 => $option['price'],
						);
					}
				}
				if (!empty($option_list)) {
					$plan_info['option_list'] = $option_list;
				}
				
				// 返却営業所のリストを生成する
				$return_shop_list = array();
				$returnOffices = !empty($commodity['returnOffice']) ? $commodity['returnOffice'] : array();

				foreach ($returnOffices as $office) {
					if ($commodity['officeId'] != $office['id']) {
						$return_shop_list[] = array(
							'shop_id'		 => $office['id'],
							'shop_name'		 => $office['name'],
							'return_price'	 => $office['price'],
						);
					}
				}
				if (!empty($return_shop_list)) {
					$plan_info['return_shop_list'] = $return_shop_list;
				}
				
				$plan_list[] = array(
					'shop_info' => $shop_info,
					'plan_info' => $plan_info,
				);
			}
		}
		
		$response['response'] = array(
			'total_shop' => $this->getTotalShopCount(),
			'total_plan' => $this->paginateCount(),
			'plan_list'	 => $plan_list,
		);
		
		return $response;
	}
	
	/**
	 * 検索条件に応じて商品のデータを取得するクエリを返す関数
	 * 高速化のため検索段階を二段階に分けています
	 * 第一段階 -> エリアや日付で商品を絞る
	 * 第二段階 -> 第一段階で絞った商品の値段と在庫を確認するSQLクエリを生成・返却
	 *
	 * @param array $searchConditions 検索条件
	 * @param number $page データのページ数
	 * @param number $limit データの取得件数
	 *
	 */
	public function getCommodityQuery($searchConditions, $page = 1, $limit = 20) {
		$this->_commodities = array();
		// 今はディープリンク不要
		// $this->_planQueryString = $this->createPlanQueryString($searchConditions);

		/**
		 * ***************************************************
		 * 第一段階
		 * レンタルエリア・レンタル期間・車両タイプ・AT/MT・禁煙/喫煙で商品を絞る
		 * ***************************************************
		 */
		$this->Office = new Office();
		$this->CarType = new CarType();
		$this->OfficeStockGroup = new OfficeStockGroup();
		$this->PublicHoliday = new PublicHoliday();
		$this->OfficeStation = new OfficeStation();
		$this->CommodityRentOffice = new CommodityRentOffice();
		$this->CommodityReturnOffice = new CommodityReturnOffice();
		$this->Maintenance = new Maintenance();
		$this->Client = new Client();

		// 今日の日付
		$today = date('Y-m-d');

		// 出発日時
		$datetimeFrom = $searchConditions['year'] . '-' . $searchConditions['month'] . '-' . $searchConditions['day'] . ' ' . str_replace('-', ':', $searchConditions['time']) . ':00';
		$dateFrom = $searchConditions['year'] . '-' . $searchConditions['month'] . '-' . $searchConditions['day'];
		$unixTimeDatetimeFrom = strtotime($datetimeFrom);
		$unixTimeDateFrom = strtotime($dateFrom);
		$timeFrom = date('H:i:s', $unixTimeDatetimeFrom);

		// 返却日時
		$datetimeTo = $searchConditions['return_year'] . '-' . $searchConditions['return_month'] . '-' . $searchConditions['return_day'] . ' ' . str_replace('-', ':', $searchConditions['return_time']) . ':00';
		$dateTo = $searchConditions['return_year'] . '-' . $searchConditions['return_month'] . '-' . $searchConditions['return_day'];
		$unixTimeDatetimeTo = strtotime($datetimeTo);
		$unixTimeDateTo = strtotime($dateTo);
		$timeTo = date('H:i:s', $unixTimeDatetimeTo);

		// 出発までの日数
		$daysWait = (strtotime($dateFrom) - strtotime($today)) / 86400;

		// 市区町村
		$cityId = 0;
		if (!empty($searchConditions['city_id'])) {
			$cityId = $searchConditions['city_id'];
		}

		// 返却市区町村
		$returnCityId = 0;
		if (!empty($searchConditions['return_city_id'])) {
			$returnCityId = $searchConditions['return_city_id'];
		}

		// 空港
		$airportId = 0;
		if (!empty($searchConditions['airport_id'])) {
			$airportId = $searchConditions['airport_id'];
		}

		// 返却空港
		$returnAirportId = 0;
		if (!empty($searchConditions['return_airport_id'])) {
			$returnAirportId = $searchConditions['return_airport_id'];
		}

		// 貸出場所
		$place = 1;
		if (!empty($searchConditions['place'])) {
			$place = $searchConditions['place'];
		}

		// 返却場所
		$returnPlace = 1;
		if (!empty($searchConditions['return_place'])) {
			$returnPlace = $searchConditions['return_place'];
		}

		$adultsCount = 2;
		if (!empty($searchConditions['adults_count'])) {
			$adultsCount = $searchConditions['adults_count'];
		}

		$childrenCount = 0;
		if (!empty($searchConditions['children_count'])) {
			$childrenCount = $searchConditions['children_count'];
		}

		$infantsCount = 0;
		if (!empty($searchConditions['infants_count'])) {
			$infantsCount = $searchConditions['infants_count'];
		}

		// 駅
		$stationId = 0;
		if (!empty($searchConditions['station_id'])) {
			$stationId = $searchConditions['station_id'];
		}

		// 返却駅
		$returnStationId = 0;
		if (!empty($searchConditions['return_station_id'])) {
			$returnStationId = $searchConditions['return_station_id'];
		}

		// 人数
		$personCount = $this->calcPersonCount($adultsCount, $childrenCount, $infantsCount);

		/**
		 * 出発の営業所ID取得
		 */
		$officeIds = array();
		if ($place == 1) {
			// 市区町村の場合
			$officeIds = $this->Office->getOfficeIdListByCityId($cityId);
		} else if ($place == 3) {
			// 空港の場合
			$officeIds = $this->Office->getOfficeIdListByAirportId($airportId);
		} else if ($place == 4) {
			// 駅の場合
			$officeIds = $this->OfficeStation->getOfficeIdList($stationId);
		}
		$officeIds = $this->extract($officeIds, '{n}.{n}');

		/**
		 * 返却の営業所ID取得
		 */
		$returnOfficeIds = array();
		if ($searchConditions['return_way'] == 0) {
			// 貸出店舗に返却の場合
			$returnOfficeIds = $officeIds;
		} else {
			if ($returnPlace == 1) {
				// 市区町村の場合
				$returnOfficeIds = $this->Office->getOfficeIdListByCityId($returnCityId);
			} else if ($returnPlace == 3) {
				// 空港の場合
				$returnOfficeIds = $this->Office->getOfficeIdListByAirportId($returnAirportId);
			} else if ($returnPlace == 4) {
				// 駅の場合
				$returnOfficeIds = $this->OfficeStation->getOfficeIdList($returnStationId);
			}
		}
		$returnOfficeIds = $this->extract($returnOfficeIds, '{n}.{n}');

		// 検索条件
		$conditions = array();

		// AT・MTが選択されていた場合
		if (isset($searchConditions['transmission_flg']) && $searchConditions['transmission_flg'] != 2) {
			$conditions += array(
				'Commodity.transmission_flg' => $searchConditions['transmission_flg']
			);
		}

		// 禁煙・喫煙が選択されていた場合
		if (isset($searchConditions['smoking_flg']) && $searchConditions['smoking_flg'] != 2) {
			$smokingFlg = array(
//					$searchConditions['smoking_flg'], 2
				// それだけ only
				$searchConditions['smoking_flg']
			);
			$conditions += array(
				'Commodity.smoking_flg' => $smokingFlg
			);
		}

		// イーコンメンテモードか？
		if ($this->Maintenance->isEconMaintenance()) {
			// WEB事前決済のみは除く
			$conditions += array(
				'Commodity.payment_method <>' => 1,
			);
		}

		// クライアントIDがURLパラメータに含まれる場合
		if (!empty($searchConditions['client_id'])) {
			$conditions += array(
				'Client.id' => $searchConditions['client_id']
			);
		}

		// 祝日・曜日判定
		$previousDayInfo = $this->PublicHoliday->getDayInfo(date('Y-m-d', strtotime($dateFrom . ' -1 day')));
		$fromDayInfo = $this->PublicHoliday->getDayInfo($dateFrom);
		$toDayInfo = $this->PublicHoliday->getDayInfo($dateTo);

		// 出発営業所サブクエリ
		$commodityRentOffices = $this->getCommodityRentOfficeSubQuery($fromDayInfo['identifier'], $previousDayInfo['identifier'], $unixTimeDateFrom, $timeFrom, $officeIds);

		// 返却営業所サブクエリ
		$commodityReturnOffices = $this->getCommodityReturnOfficeSubQuery($toDayInfo['identifier'], $unixTimeDateTo, $timeTo, $returnOfficeIds);

		if (empty($commodityReturnOffices)) {
			return false;
		}

		// 出発と返却を満たす商品IDを取得
		$commodityIds = array_intersect(array_keys($commodityRentOffices), array_keys($commodityReturnOffices));

		// 存在しない商品IDを除外する
		$flipIds = array_flip($commodityIds);

		foreach ($commodityRentOffices as $k => $v) {
			if (!isset($flipIds[$k])) {
				unset($commodityRentOffices[$k]);
			}
		}
		foreach ($commodityReturnOffices as $k => $v) {
			if (!isset($flipIds[$k])) {
				unset($commodityReturnOffices[$k]);
			}
		}

		// 必須条件
		$basicConditions = array(
			'Commodity.id' => $commodityIds,
			array(
				'OR' => array(
					array(
						'Commodity.rent_time_from < Commodity.rent_time_to',
						"'{$timeFrom}' BETWEEN Commodity.rent_time_from AND Commodity.rent_time_to"
					),
					array(
						"Commodity.rent_time_from  > Commodity.rent_time_to",
						'OR' => array(
							"'{$timeFrom}'  BETWEEN Commodity.rent_time_from AND  '{$timeFrom}'",
							"'{$timeFrom}'  BETWEEN '00:00:00' AND Commodity.rent_time_to"
						)
					)
				)
			),
			array(
				'OR' => array(
					array(
						'Commodity.return_time_from < Commodity.return_time_to',
						"'{$timeTo}' BETWEEN Commodity.return_time_from AND Commodity.return_time_to"
					),
					array(
						"Commodity.return_time_from  > Commodity.return_time_to",
						'OR' => array(
							"'{$timeTo}'  BETWEEN Commodity.return_time_from AND  '{$timeTo}'",
							"'{$timeTo}'  BETWEEN '00:00:00' AND Commodity.return_time_to"
						)
					)
				)
			),
			'CommodityTerm.available_from <=' => $datetimeFrom,
			'CommodityTerm.available_to >=' => $datetimeTo,
			array(
				'OR' => array(
					'CommodityTerm.bookable_days IS NULL',
					"CommodityTerm.bookable_days >= {$daysWait}"
				),
			),
			'Commodity.is_published' => 1,
			'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
			'Commodity.delete_flg' => 0,
			'CommodityTerm.delete_flg' => 0,
			'Client.delete_flg' => 0,
		);

		//必須条件とpostされてきた条件をマージ
		$conditions = array_merge($basicConditions, $conditions);

		//商品データを取得
		$commodityLists = $this->findC('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'CommodityTerm',
					'table' => 'commodity_terms',
					'conditions' => array(
						'Commodity.id = CommodityTerm.commodity_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityItem',
					'table' => 'commodity_items',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarClass',
					'table' => 'car_classes',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id',
						'CarClass.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array(
						'Client.id = Commodity.client_id'
					)
				),
			),
			'fields' => array(
				'Commodity.id',
				'Commodity.day_time_flg',
				'CommodityItem.id',
				'CommodityItem.car_class_id',
				'CarClass.drop_off_price_pattern',
				'Client.id',
				'Client.name',
				'CommodityTerm.deadline_hours',
				'CommodityTerm.deadline_days',
				'CommodityTerm.deadline_time',
			),
			'recursive' => - 1
		));

		// 商品がなかった場合falseを返却
		if (empty($commodityLists)) {
			return false;
		}

		// 検索対象外のクライアントを取得
		$notSearchableList = $this->Client->notSearchableList();

		$commodities = array();

		// 商品ID・商品アイテムIDを取得
		foreach ($commodityLists as $key => $val) {
			// 検索対象外を除く
			if (isset($notSearchableList[$val['Client']['id']])) {
				continue;
			}

			// 締切時間の判定
			$term = $val['CommodityTerm'];
			$deadline = $this->calculateDeadline($dateFrom, $datetimeFrom, $term['deadline_hours'], $term['deadline_days'], $term['deadline_time']);
			if (!$deadline) {
				continue;
			}

			$rentOffices = $commodityRentOffices[$val['Commodity']['id']];

			// 出発営業所毎に判定する
			foreach ($rentOffices as $rentOffice) {
				// 営業開始時刻の判定
				if (!$this->isOfficeOpenOK($dateFrom, $datetimeFrom, $rentOffice['office_hours_from'], $rentOffice['office_hours_to_previous'], $term['deadline_hours'], $term['deadline_days'], $term['deadline_time'])) {
					continue;
				}

				$k = $val['Commodity']['id'];

				$commodities[$k][$rentOffice['id']] = array(
					'commodityId'			 => $val['Commodity']['id'],
					'commodityItemId'		 => $val['CommodityItem']['id'],
					'carClassId'			 => $val['CommodityItem']['car_class_id'],
					'dropOffPricePattern'	 => $val['CarClass']['drop_off_price_pattern'],
					'clientName'			 => $val['Client']['name'],
					'stockGroupId'			 => $rentOffice['office_stock_group_id'],
					'officeId'				 => $rentOffice['id'],
					'officeName'			 => $rentOffice['name'],
					'deadlineHours'			 => $term['deadline_hours'],
					'deadline'				 => $deadline,
					'dayTimeFlg'			 => empty($val['Commodity']['day_time_flg']) ? 0 : 1,
					'returnOffice'			 => array(),
				);

				if (!empty($searchConditions['return_way'])) {
					$commodities[$k][$rentOffice['id']]['returnOffice'] = $commodityReturnOffices[$k];
				}
			}
		}

		// 商品がなかった場合falseを返却
		if (empty($commodities)) {
			return false;
		}

		$commodities = Hash::extract($commodities, '{n}.{n}');

		unset($basicConditions, $conditions, $commodityLists, $commodityRentOffices, $commodityReturnOffices, $flipIds);

		/**
		 * *****************************************
		 * 第二段階
		 * 在庫・料金・オプションで商品を絞りSQLクエリを返却
		 * *****************************************
		 */

		// 対象を出来る限り少なくするため在庫よりも料金よりも先に乗り捨てを見る
		 if (!empty($searchConditions['return_way'])) {
			$commodities = $this->dropOffAreaRatesSubQuery($commodities, $officeIds, $returnOfficeIds);

			if (empty($commodities)) {
				return false;
			}
		}

		// 借りる期間算出
		list($spanCount, $spanCount24) = $this->getSpanCount($datetimeFrom, $datetimeTo);

		// 在庫確認
		$commodities = $this->carClassStockSubQuery($commodities, $dateFrom, $dateTo, $spanCount);
		
		if (empty($commodities)) {
			return false;
		}

		/**
		 * ここから料金を取得するサブクエリ
		 * 商品の値段を求めるロジック参考URL
		 * https://rent.toyota.co.jp/service/charge/shikumi.aspx
		 */

		// 出発・返却日時からレンタル時間を算出
		$rentTime = ceil(abs(($unixTimeDatetimeFrom - $unixTimeDatetimeTo) / 3600));

		// 借りる日数
		$count = $spanCount - 5;

		// 歴日制超過日
		$superOtherDay1 = ($count > 0) ? $count : 0;

		// 超過時間
		$overtime = (($rentTime - 24) > 0) ? ceil($rentTime - 24) : 0;

		// 超過日2
		$superOtherDay2 = (floor($overtime / 24) > 0) ? floor($overtime / 24) : 0; // 切り捨て
		// 超過日3
		$superOtherDay3 = (ceil($overtime / 24) > 0) ? ceil($overtime / 24) : 0; // 切り上げ
		// 余り時間
		$restTime = (($overtime % 24) > 0) ? $overtime % 24 : 0;

		// 歴日制料金取得
		$commodities_day = $this->daySubQuery($commodities, $superOtherDay1, $spanCount, $dateFrom, $dateTo);

		// 時間制料金取得
		$commodities_time = $this->timeSubQuery($commodities, $superOtherDay2, $superOtherDay3, $rentTime, $restTime, $dateFrom, $dateTo);

		if (empty($commodities_day) && empty($commodities_time)) {
			return false;
		}

		// 歴日制と時間制の配列を結合する
		$commodities = array_merge($commodities_day, $commodities_time);
		unset($commodities_day, $commodities_time);
		
		// 免責補償料金取得
		$commodities = $this->disclaimerCompensationSubQuery($commodities, $spanCount, $spanCount24, $dateFrom);
		
		if (empty($commodities)) {
			return false;
		}
		
		// 料金が0のものは除外する
		foreach ($commodities as $k => $commodity) {
			if (empty($commodity['price'])) {
				unset($commodities[$k]);
			}
		}
		
		// オプション料金取得
		$commodities = $this->optionSubQuery($commodities, $spanCount, $spanCount24);
		
		// 深夜手数料取得
		$commodities = $this->nightFeeSubQuery($commodities, $timeFrom, $timeTo);
		
		// ソート順を指定する
		$commodities = Hash::sort($commodities, '{n}.price', 'asc');
		
		// 確定した内容をメンバ変数に保存する
		$this->_commodities = $commodities;

		// ページャーを考慮し範囲指定してcommodity_idを渡す
		$commodities = array_slice($commodities, ($page - 1) * $limit, $limit);
		$commodityIds = $this->extract($commodities, '{n}.commodityId');

		// ページ内に商品が無い時
		if (empty($commodityIds)) {
			return false;
		}

		return array(
			'conditions' => array(
				'Commodity.id' => $commodityIds,
			),
			'fields' => array(
				'Commodity.id',
				'Commodity.client_id',
				'Commodity.name',
				'Commodity.image_relative_url',
				'Commodity.description',
				'Commodity.smoking_flg',
			),
			'limit' => $limit,
			'page' => $page,
		);
	}

}

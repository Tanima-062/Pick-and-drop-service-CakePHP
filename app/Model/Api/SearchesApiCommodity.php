<?php

App::uses('BaseCommodityMetasearch', 'Model');
App::uses('SippCodeLetter', 'Model');
App::uses('Equipment', 'Model');

final class SearchesApiCommodity extends BaseCommodityMetasearch {
	public $actsAs = array('SearchesApiCommodity');

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

		$commodities = $this->_commodities;

		// 車種取得
		$CommodityItem = new CommodityItem();
		$carInfoList = $CommodityItem->getCarInfo($this->extract($commodities, '{n}.commodityItemId'));

		// 装備取得
		$Equipment = new Equipment();
		$equipmentList = $Equipment->getEquipment();
		$equipmentList = Hash::combine($equipmentList, '{n}.Equipment.option_category_id', '{n}.Equipment.description');

		// レビュー取得
		$YotpoReview = new YotpoReview();
		$ratings = $YotpoReview->getRatingsGroupByClientId();

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

				$shop_info = array(
					'shopName' => $commodity['officeName'],
				);

				// 商品IDがリスト追加済みの場合は店舗だけ追加する
				if (isset($plan_list[$commodity_id])) {
					$plan_list[$commodity_id]['shops'][] = $shop_info;
					continue;
				}

				// 基本料金＝基本料金＋免責料金＋深夜料金
				$base_price = $commodity['basePrice'] + $commodity['disclaimerCompensation'];
				if (!empty($commodity['lateNightFee'])) {
					$base_price += $commodity['lateNightFee'];
				}

				$carInfo = $carInfoList[$commodity['commodityItemId']];

				$sipp_code = !empty($commodity['sippCode']) ? $commodity['sippCode'] : '';
				$car_model = $carInfo['CarModel'][0];
				$car_registration = $ret_v['Commodity']['new_car_registration'];
				$model_select = !empty($commodity['carModelId']); // 車種指定
				$rating = !empty($ratings[$ret_v['Commodity']['client_id']]) ? $ratings[$ret_v['Commodity']['client_id']] : array();

				// プラン名を生成する
				$plan_name = $this->createPlanName($carInfo['CarType'], $carInfo['CarModel'], $model_select);

				// オプションのリストを生成する
				$equipment_list = array(
					array(
						'equipmentName'		 => '免責補償',
						'description'		 => '免責補償料金込みプラン',
						'optionCategoryId'	 => 3,
					)
				);

				$option_list = array();
				$options = !empty($commodity['Option']) ? $commodity['Option'] : array();

				foreach ($options as $option) {
					if (empty($optionCategories[$option['option_category_id']])) {
						continue;
					}

					$option_category = $optionCategories[$option['option_category_id']];

					if (!empty($option['option_default'])) {
						$equipment_list[] = array(
							'equipmentName'		 => $option['name'],
							'description'		 => trim($equipmentList[$option_category['id']]),
							'optionCategoryId'	 => $option_category['id'],
						);
					} else {
						// シートとその他で分ける
						$ctg = !empty($option['option_flg']) ? 'sheets' : 'others';
						$option_list[$ctg][] = array(
							'optionName'		 => $option['name'],
							'optionCategoryId'	 => $option_category['id'],
						);
					}
				}

				if (empty($ret_v['Commodity']['transmission_flg'])) {
					$equipment_list[] = array(
						'equipmentName'		 => 'AT車',
						'description'		 => 'オートマチックトランスミッションの車です',
						'optionCategoryId'	 => 0,
					);
				}

				$plan_info = array(
					'planId'			 => (int)$commodity['commodityItemId'],
					'planName'			 => $plan_name,
					'planImage'			 => $img_url . $ret_v['Commodity']['client_id'] . '/' . $ret_v['Commodity']['image_relative_url'],
					'clientId'			 => (int)$ret_v['Commodity']['client_id'],
					'clientName'		 => $commodity['clientName'],
					'carTypeId'			 => (int)$carInfo['CarType']['id'],
					'sippCode'			 => $sipp_code,
					'shops'				 => array($shop_info),
					'equipments'		 => $equipment_list,
					'options'			 => $option_list,
					'smoking'			 => !empty($ret_v['Commodity']['smoking_flg']),
					'capacity'			 => (int)$car_model['capacity'],
					'baggage'			 => (int)$car_model['trunk_space'],
					'modelSelect'		 => $model_select,
					'newCar'			 => ($car_registration == 1 || $car_registration == 2),
					'payment'			 => !empty($ret_v['Commodity']['payment_method']),
					'basePrice'			 => $base_price,
					'currency'			 => 'JPY',
					'stockCount'		 => (int)$commodity['numberRemaining'],
					'reviewScore'		 => isset($rating['rating']) ? (double)number_format(floatval($rating['rating']), 1, '.', '') : null,
					'reviewCount'		 => isset($rating['count']) ? (int)$rating['count'] : 0,
				);

				$plan_list[$commodity_id] = $plan_info;
			}
		}

		return array_values($plan_list);
	}

	/**
	 * 検索条件に応じて商品のデータを取得するクエリを返す関数
	 * 高速化のため検索段階を二段階に分けています
	 * 第一段階 -> エリアや日付で商品を絞る
	 * 第二段階 -> 第一段階で絞った商品の値段と在庫を確認するSQLクエリを生成・返却
	 *
	 * @param array $searchConditions 検索条件
	 *
	 */
	public function getCommodityQuery($searchConditions) {
		$this->_commodities = array();

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
		$datetimeFrom = $searchConditions['startDate'] . ' ' . $searchConditions['startTime'] . ':00';
		$dateFrom = $searchConditions['startDate'];
		$unixTimeDatetimeFrom = strtotime($datetimeFrom);
		$unixTimeDateFrom = strtotime($dateFrom);
		$timeFrom = date('H:i:s', $unixTimeDatetimeFrom);

		// 返却日時
		$datetimeTo = $searchConditions['endDate'] . ' ' . $searchConditions['endTime'] . ':00';
		$dateTo = $searchConditions['endDate'];
		$unixTimeDatetimeTo = strtotime($datetimeTo);
		$unixTimeDateTo = strtotime($dateTo);
		$timeTo = date('H:i:s', $unixTimeDatetimeTo);

		// 出発までの日数
		$daysWait = (strtotime($dateFrom) - strtotime($today)) / 86400;

		// 市区町村
		$cityId = 0;
		if (!empty($searchConditions['cityId'])) {
			$cityId = $searchConditions['cityId'];
		}

		// 返却市区町村
		$returnCityId = 0;
		if (!empty($searchConditions['returnCityId'])) {
			$returnCityId = $searchConditions['returnCityId'];
		}

		// 空港
		$airportId = 0;
		if (!empty($searchConditions['airportId'])) {
			$airportId = $searchConditions['airportId'];
		}

		// 返却空港
		$returnAirportId = 0;
		if (!empty($searchConditions['returnAirportId'])) {
			$returnAirportId = $searchConditions['returnAirportId'];
		}

		// 貸出場所
		$place = 1;
		if (!empty($searchConditions['place'])) {
			$place = $searchConditions['place'];
		}

		// 返却場所
		$returnPlace = 1;
		if (!empty($searchConditions['returnPlace'])) {
			$returnPlace = $searchConditions['returnPlace'];
		}

		$adultsCount = 2;
		if (!empty($searchConditions['adultCount'])) {
			$adultsCount = $searchConditions['adultCount'];
		}

		$childrenCount = 0;
		if (!empty($searchConditions['childCount'])) {
			$childrenCount = $searchConditions['childCount'];
		}

		$infantsCount = 0;
		if (!empty($searchConditions['infantCount'])) {
			$infantsCount = $searchConditions['infantCount'];
		}

		// 駅
		$stationId = 0;
		if (!empty($searchConditions['stationId'])) {
			$stationId = $searchConditions['stationId'];
		}

		// 返却駅
		$returnStationId = 0;
		if (!empty($searchConditions['returnStationId'])) {
			$returnStationId = $searchConditions['returnStationId'];
		}

		// 人数
		$personCount = $this->calcPersonCount($adultsCount, $childrenCount, $infantsCount);

		// 人数からカータイプ取得
		$carTypeIds = $this->CarType->getCarTypeIdByPersonCount($personCount);

		if (empty($carTypeIds)) {
			$carTypeIds = array(0);
		}

		// 車種が選択されていた場合
		if (!empty($searchConditions['carTypes']) && is_array($searchConditions['carTypes'])) {
			// ご利用人数から算出した車両タイプと検索された車両タイプの重複のみ抽出し検索
			$carTypeIds = array_intersect($searchConditions['carTypes'], $carTypeIds);
		}

		// エリアID
		$areaId = 0;
		if (!empty($searchConditions['areaId'])) {
			$areaId = $searchConditions['areaId'];
		}

		// 返却エリアID
		$returnAreaId = 0;
		if (!empty($searchConditions['returnAreaId'])) {
			$returnAreaId = $searchConditions['returnAreaId'];
		}

		// クライアントID
		$clientIds = array();
		if (!empty($searchConditions['clients'])) {
			$clientIds = $searchConditions['clients'];
		}

		/**
		 * 出発の営業所ID取得
		 */
		$officeIds = array();
		if ($place == 1) {
			// エリアの場合
			$officeIds = $this->Office->getOfficeIdListByAreaId($areaId);
			// 空港の場合
		} else if ($place == 3) {
			$officeIds = $this->Office->getOfficeIdListByAirportId($airportId);
			// 駅
		} else if ($place == 4) {
			$officeIds = $this->OfficeStation->getOfficeIdList($stationId);
		} else if ($place == 5) {
			// 座標
			$officeIds =$this->Office->getOfficeIdListByLocation($searchConditions['latitude'], $searchConditions['longitude'], $clientIds);
		}
		$officeIds = $this->extract($officeIds, '{n}.{n}');

		/**
		 * 返却の営業所ID取得
		 */
		$returnOfficeIds = array();
		if ($searchConditions['returnWay'] == 0) {
			// 貸出店舗に返却の場合
			$returnOfficeIds = $officeIds;
		} else {
			if ($returnPlace == 1) {
				// エリアの場合
				$returnOfficeIds = $this->Office->getOfficeIdListByAreaId($returnAreaId);
			} else if ($returnPlace == 3) {
				// 空港の場合
				$returnOfficeIds = $this->Office->getOfficeIdListByAirportId($returnAirportId);
			} else if ($returnPlace == 4) {
				// 駅の場合
				$returnOfficeIds = $this->OfficeStation->getOfficeIdList($returnStationId);
			} else if ($returnPlace == 5) {
				// 座標
				$returnOfficeIds =$this->Office->getOfficeIdListByLocation($searchConditions['returnLatitude'], $searchConditions['returnLongitude'], $clientIds);
			}
		}
		$returnOfficeIds = $this->extract($returnOfficeIds, '{n}.{n}');

		// 検索条件
		$conditions = array();

		// 禁煙・喫煙が選択されていた場合
		if (isset($searchConditions['smokingType']) && $searchConditions['smokingType'] != 2) {
			$smokingFlg = array(
				$searchConditions['smokingType']
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
		if (!empty($clientIds)) {
			$conditions += array(
				'Client.id' => $clientIds
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
				'CommodityItem.car_model_id',
				'CommodityItem.sipp_code',
				'CarClass.car_type_id',
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
					'carModelId'			 => $val['CommodityItem']['car_model_id'],
					'sippCode'				 => $val['CommodityItem']['sipp_code'],
					'carTypeId'				 => $val['CarClass']['car_type_id'],
					'dropOffPricePattern'	 => $val['CarClass']['drop_off_price_pattern'],
					'clientName'			 => $val['Client']['name'],
					'stockGroupId'			 => $rentOffice['office_stock_group_id'],
					'officeId'				 => $rentOffice['id'],
					'officeName'			 => $rentOffice['name'],
					'canPickup'				 => $rentOffice['can_pickup'],
					'deadlineHours'			 => $term['deadline_hours'],
					'deadline'				 => $deadline,
					'dayTimeFlg'			 => empty($val['Commodity']['day_time_flg']) ? 0 : 1,
					'returnOffice'			 => array(),
				);

				if (!empty($searchConditions['returnWay'])) {
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
		if (!empty($searchConditions['returnWay'])) {
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

		// 車両タイプフィルター用に反転
		$carTypeIds = array_flip($carTypeIds);

		foreach ($commodities as $k => $v) {
			// 選択されていない車両タイプは除外する
			if (!isset($carTypeIds[$v['carTypeId']])) {
				unset($commodities[$k]);
				continue;
			}
		}

		// ソート順を指定する
		$commodities = Hash::sort($commodities, '{n}.price', 'asc');

		// 確定した内容をメンバ変数に保存する
		$this->_commodities = $commodities;

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
				'Commodity.new_car_registration',
				'Commodity.smoking_flg',
				'Commodity.transmission_flg',
				'Commodity.payment_method',
			),
		);
	}

	// API用の項目を追加するためオーバーライド
	public function getCommodityInfoByCommodityItemId($commodityItemId) {
		return $this->findC('first', array(
			'fields' => array(
				'Commodity.id',
				'Commodity.name',
				'Commodity.image_relative_url',
				'Commodity.new_car_registration',
				'Commodity.smoking_flg',
				'Commodity.transmission_flg',
				'Commodity.payment_method',
				'CommodityItem.car_model_id',
				'CommodityItem.sipp_code',
				'Client.id',
				'Client.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => 'CommodityItem.commodity_id = Commodity.id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Client.id = Commodity.client_id',
				),
			),
			'conditions' => array(
				'CommodityItem.id' => $commodityItemId,
				'CommodityItem.delete_flg' => 0,
				'Commodity.is_published' => 1,
				'Commodity.delete_flg' => 0,
			),
			'recursive' => -1,
		));
	}

	// プラン抽出用のパラメータに変換するためオーバーライド
	public function getCommodityData($commodityId, $params) {
		$requestData = array(
			'from' => $params['startDate'] . ' ' . $params['startTime'] . ':00',
			'to' => $params['endDate'] . ' ' . $params['endTime'] . ':00',
			'client_id' => $params['clients'][0],
			'airport_id' => !empty($params['airportId']) ? $params['airportId'] : null,
			'station_id' => !empty($params['stationId']) ? $params['stationId'] : null,
			'area_id' => !empty($params['areaId']) ? $params['areaId'] : null,
			'return_airport_id' => !empty($params['returnAirportId']) ? $params['returnAirportId'] : null,
			'return_station_id' => !empty($params['returnStationId']) ? $params['returnStationId'] : null,
			'return_area_id' => !empty($params['returnAreaId']) ? $params['returnAreaId'] : null,
		);

		// 座標の場合は店舗IDにする
		$this->Office = new Office();
		if ($params['place'] == 5) {
			$officeIds =$this->Office->getOfficeIdListByLocation($params['latitude'], $params['longitude'], $params['clients']);
			$requestData['office_id'] = $this->extract($officeIds, '{n}.{n}');
		}
		if ($params['returnPlace'] == 5) {
			$returnOfficeIds =$this->Office->getOfficeIdListByLocation($params['returnLatitude'], $params['returnLongitude'], $params['clients']);
			$requestData['return_office_id'] = $this->extract($returnOfficeIds, '{n}.{n}');
		}

		return parent::getCommodityData($commodityId, $requestData);
	}
}

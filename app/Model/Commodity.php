<?php

App::uses('AppModel', 'Model');
App::uses('Area', 'Model');
App::uses('CarType', 'Model');
App::uses('Office', 'Model');
App::uses('OfficeStockGroup', 'Model');
App::uses('CommodityItem', 'Model');
App::uses('CommodityRentOffice', 'Model');
App::uses('CommodityReturnOffice', 'Model');
App::uses('CommodityPrice', 'Model');
App::uses('PublicHoliday', 'Model');
App::uses('Landmark', 'Model');
App::uses('Station', 'Model');
App::uses('OfficeStation', 'Model');
App::uses('Client', 'Model');
App::uses('CommodityEquipment', 'Model');
App::uses('CommodityPrivilege', 'Model');
App::uses('YotpoReview', 'Model');
App::uses('Campaign', 'Model');
App::uses('Maintenance', 'Model');
App::uses('Recommend', 'Model');
App::uses('RecommendPrefecture', 'Model');

class Commodity extends AppModel {
	public $actsAs = array('CommodityCommon', 'CommoditySubQuery');

	public $isOutOfStock = false; // 暫定対応 在庫なし判定

	protected $cacheConfig = '1hour';
	protected $_commodities = array();
	protected $_commodityIds = array();
	protected $_prCommodityIds = array();
	protected $_lowestPriceOfCarType = array();
	protected $_planQueryString = '';
	public $inheritPR = array();

	public function getLowestPriceOfCarType() {
		return $this->_lowestPriceOfCarType;
	}

	public function getPlanQueryString() {
		return $this->_planQueryString;
	}

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

		// ページャーを考慮し範囲指定してcommodity_idを渡す
		$commodityIds = array_slice($this->_commodityIds, ($page - 1) * $limit, $limit);

		// ページ内に商品が無い時
		if (empty($commodityIds)) {
			return array();
		}

		// PR商品があればcommodity_idを追加
		if (!empty($this->_prCommodityIds)) {
			$prCommodityId = array();
			// 表示上限以上のPR=ランダム表示
			if (count($this->_prCommodityIds) > Constant::RECOMMEND_LIMIT_CNT) {
				// 地図->プランに移動した時だけ以前表示していたPRを1度だけ引き継ぐ
				if (!empty($this->inheritPR)) {
					foreach ($this->inheritPR as $key => $clientId) {
						// ページまたぎ中に引き継ぎPRが消えている可能性もあるため存在チェックする
						if (!empty($this->_prCommodityIds[$clientId])) {
							$prCommodityId[] = $this->_prCommodityIds[$clientId];
							if (count($prCommodityId) >= Constant::RECOMMEND_LIMIT_CNT) {
								break;
							}
						}
					}
				}
				// 引き継ぎ用PRがない時/不足している時は追加でランダムで取ってくる
				if (count($prCommodityId) < Constant::RECOMMEND_LIMIT_CNT) {
					// 表示不足分をシャッフル->上から表示とする(ランダムPRの時しか上回らないはず)
					$prCommodityIdsKey = array_keys($this->_prCommodityIds);
					shuffle($prCommodityIdsKey);
					foreach($prCommodityIdsKey as $key => $val){
						// すでに設定されているPRは除外する
						if (!in_array($this->_prCommodityIds[$val], $prCommodityId)) {
							$prCommodityId[] = $this->_prCommodityIds[$val];
							if (count($prCommodityId) >= Constant::RECOMMEND_LIMIT_CNT) {
								break;
							}
						}
					}
				}
			} else {
				// 上限以下ならランダムでも固定でもそのまま出す
				$prCommodityId = $this->_prCommodityIds;
			}
			$commodityIds = array_unique(array_merge($commodityIds, $prCommodityId));
		}

		$commodityIds = array_flip($commodityIds);

		$optionCategories = Constant::optionCategories();

		$return_arr = array();

		foreach ((array)$commodities as $k => $commodity) {
			foreach ((array)$ret as $ret_k => $ret_v) {
				// レコメンド商品であるがランダム枠(表示枠)に選ばれなかった商品は通さない
				// レコメンド商品と同じ商品IDだけどレコメンド扱いされていないものは通る
				if (!empty($commodity['pr']) && !in_array($commodity['commodityId'],$prCommodityId)) {
					continue;
				}
				// commoditiesを作る際にレコメンド用商品IDを重複して入れているため、すでに表示した商品をまた表示しないように表示用配列に入れたあとは通さない
				if ($ret_v['Commodity']['id'] != $commodity['commodityId']) {
					continue;
				}
				// 今表示しているページ以外の商品は通さない
				if (!isset($commodityIds[$ret_v['Commodity']['id']])) {
					continue;
				}

				// オプションのリストを生成する
				$option_list = array();
				$options = !empty($commodity['Option']) ? $commodity['Option'] : array();

				foreach ($options as $option) {
					if (!isset($optionCategories[$option['option_category_id']])) {
						continue;
					}

					$option_category = $optionCategories[$option['option_category_id']];

					$option_list[] = array(
						'option_category'	 => $option_category['id'],
						'option_name'		 => $option['name'],
						'option_default'	 => $option['option_default'],
						'option_num'		 => '1',
						'option_price'		 => $option['price'],
					);
				}

				// クエリ結果に追加
				$item = $ret_v + array(
					'CommodityItem' => array(
						'id'			 => $commodity['commodityItemId'],
						'car_model_id'	 => $commodity['carModelId'],
					),
					'CommodityPrice' => array(
						'price' => $commodity['price'],
					),
					'CommodityTerm' => array(
						'deadline_hours'	 => $commodity['deadlineHours'],
						'deadline'			 => $commodity['deadline'],
					),
					'CarClassStock' => array(
						'day_count' => $commodity['dayCount'],
						'numberRemaining' => $commodity['numberRemaining'],
					),
					'Option' => $option_list,
					'pr' => !empty($commodity['pr']),
					'pr_title' => $commodity['pr_title'],
					'pr_space' => $commodity['pr_space'],
					'minDropPrice' => !empty($commodity['minDropPrice']) ? $commodity['minDropPrice'] : null,
					'minLateNightFee' => isset($commodity['minLateNightFee']) ? $commodity['minLateNightFee'] : null
				);
				$item['Commodity']['day_time_flg'] = $commodity['dayTimeFlg'];

				$return_arr[] = $item;

				unset($ret[$ret_k]);
				break 1;
			}
		}

		return $return_arr;
	}

	// paginateCountをオーバーライド
	// ページャー処理は$this->_commoditiesを使ってphp側で制御しているのでクエリでは処理しない。
	public function paginateCount() {
		if (empty($this->_commodities)) {
			return 0;
		}
		$ids = Hash::extract($this->_commodities, '{n}.commodityId');
		return count(array_unique($ids));
	}

	// 地図検索用のプランを返す
	// $this->getCommodityQuery()の後に呼ぶこと。
	public function getPlansForMap($commodityQuery, $searchConditions) {
		if (empty($this->_commodities)) {
			return array();
		}

		$commodities = $this->_commodities;
		$commodityIds = $this->_commodityIds;

		// 商品が無い時
		if (empty($commodityIds)) {
			return array();
		}

		$ret = $this->findC('all', $commodityQuery);
		$ret = Hash::combine($ret, '{n}.Commodity.id', '{n}');

		$optionCategories = Constant::optionCategories();

		$prClientIds = array();
		$prTitles = array();
		$prSpaces = array();
		$tmpCommodities = array();
		$commodityItemIds = array();
		foreach ((array)$commodities as $commodity) {
			$commodityInfo = $ret[$commodity['commodityId']];
			$commodityItemIds[] = $commodity['commodityItemId'];

			// PR対象は会社
			if ($commodity['pr'] && !in_array($commodityInfo['Commodity']['client_id'], $prClientIds)) {
				$prClientIds[] = $commodityInfo['Commodity']['client_id'];
				$prTitles[$commodityInfo['Commodity']['client_id']] = $commodity['pr_title'];
				$prSpaces[$commodityInfo['Commodity']['client_id']] = $commodity['pr_space'];
			}

			// オプションのリストを生成する
			$options = !empty($commodity['Option']) ? $commodity['Option'] : array();

			$option_list = array();
			foreach ($options as $option) {
				if (!isset($optionCategories[$option['option_category_id']])) {
					continue;
				}

				$option_category = $optionCategories[$option['option_category_id']];

				$option_list[] = array(
					'option_category'	 => $option_category['id'],
					'option_name'		 => $option['name'],
					'option_default'	 => $option['option_default'],
					'option_num'		 => '1',
					'option_price'		 => $option['price'],
				);
			}

			// クエリ結果に追加
			$tmpCommodities[$commodity['commodityId']] = $commodityInfo + array(
				'CommodityItem' => array(
					'id'			 => $commodity['commodityItemId'],
					'car_model_id'	 => $commodity['carModelId'],
				),
				'CommodityPrice' => array(
					'price' => $commodity['price'],
				),
				'CommodityTerm' => array(
					'deadline_hours'	 => $commodity['deadlineHours'],
					'deadline'			 => $commodity['deadline'],
				),
				'CarClassStock' => array(
					'day_count' => $commodity['dayCount'],
					'numberRemaining' => $commodity['numberRemaining'],
				),
				'Option' => $option_list,
				'minDropPrice' => !empty($commodity['minDropPrice']) ? $commodity['minDropPrice'] : null,
				'minLateNightFee' => isset($commodity['minLateNightFee']) ? $commodity['minLateNightFee'] : null
			);
			$tmpCommodities[$commodity['commodityId']]['Commodity']['day_time_flg'] = $commodity['dayTimeFlg'];
		}

		$fromDatetime = $searchConditions['from'] . ' ' . str_replace('-',':',$searchConditions['time']);
		$this->CommodityRentOffice = new CommodityRentOffice();
		$this->CommodityRentOffice->setDataSource($this->getDataSource()->configKeyName);

		// 出発店舗取得
		if ($searchConditions['place'] == 2) {
			// 新幹線の場合
			$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityIds, $searchConditions['bullet_train_id'], 2, $searchConditions['from'], $fromDatetime, $searchConditions['to'], true);
		} else if ($searchConditions['place'] == 3) {
			// 空港の場合
			$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityIds, $searchConditions['airport_id'], 3, $searchConditions['from'], $fromDatetime, $searchConditions['to'], true);
		} else if ($searchConditions['place'] == 4) {
			// ローカル駅の場合
			$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityIds, $searchConditions['station_id'], 4, $searchConditions['from'], $fromDatetime, $searchConditions['to'], true);
		} else {
			// 通常は都道府県
			$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityIds, $searchConditions['area_id'], 1, $searchConditions['from'], $fromDatetime, $searchConditions['to'], true);
		}

		// 店舗はID順（レコメンドあれば優先、A枠はB枠より優先）
		$tmpOffices = $rentOfficeList;
		usort($tmpOffices, function ($a, $b) use ($prClientIds, $prSpaces) {
			if (in_array($a['client_id'], $prClientIds) && !in_array($b['client_id'], $prClientIds)) {
				return -1;
			} elseif (!in_array($a['client_id'], $prClientIds) && in_array($b['client_id'], $prClientIds)) {
				return 1;
			} elseif (in_array($a['client_id'], $prClientIds) && in_array($b['client_id'], $prClientIds)) {
				return ($prSpaces[$a['client_id']] < $prSpaces[$b['client_id']]) ? -1 : 1;
			} else {
				return ($a['id'] <= $b['id']) ? -1 : 1;
			}
		});

		// プランを店舗ごとにグルーピングして返す
		$return_arr = array();
		// 地図用ランダム処理
		if (!empty($prClientIds)) {
			$targetPrClientIds = array();
			// 表示上限以上のPR=ランダム表示
			if (count($prClientIds) > Constant::RECOMMEND_LIMIT_CNT) {
				// プラン->地図に移動した時だけ以前表示していたPRを1度だけ引き継ぐ
				if (!empty($this->inheritPR)) {
					foreach ($this->inheritPR as $key => $clientId) {
						// ページまたぎ中に引き継ぎPRが消えている可能性もあるため存在チェックする
						if (in_array($clientId, $prClientIds)) {
							$targetPrClientIds[] = $clientId;
							if(count($targetPrClientIds) >= Constant::RECOMMEND_LIMIT_CNT){
								break;
							}
						}
					}
				}
				// 引き継ぎ用PRがない時/不足している時は追加でランダムで取ってくる
				if (count($targetPrClientIds) < Constant::RECOMMEND_LIMIT_CNT) {
					// 表示不足分をシャッフル->上から表示とする(ランダムPRの時しか上回らないはず)
					shuffle($prClientIds);
					foreach($prClientIds as $key => $val){
						// すでに設定されているPRは除外する
						if (!in_array($val, $targetPrClientIds)) {
							$targetPrClientIds[] = $val;
							if (count($targetPrClientIds) >= Constant::RECOMMEND_LIMIT_CNT) {
								break;
							}
						}
					}
				}
			} else {
				// 上限以下ならランダムでも固定でもそのまま出す
				$targetPrClientIds = $prClientIds;
			}
		}
		foreach ($tmpOffices as $office) {
			$officeId = $office['id'];
			foreach ($office['commodityIds'] as $commodityId) {
				$return_arr[$officeId][] = $tmpCommodities[$commodityId];
			}
			// プランは価格の安い順
			$return_arr[$officeId] = Hash::sort($return_arr[$officeId], '{n}.CommodityPrice.price');
			// 営業所PR設定
			$rentOfficeList[$officeId]['pr'] = false;
			if (in_array($office['client_id'], $targetPrClientIds)) {
				$rentOfficeList[$officeId]['pr'] = true;
				$rentOfficeList[$officeId]['pr_title'] = $prTitles[$office['client_id']];
				$rentOfficeList[$officeId]['pr_space'] = $prSpaces[$office['client_id']];
			}
		}
		$rentOfficeList = Hash::remove($rentOfficeList, '{n}.commodityIds');

		unset($tmpCommodities, $tmpOffices);

		return array($return_arr, $rentOfficeList, $commodityIds, $commodityItemIds);
	}

//	// 必要か不明だがエラーが発生する時は、オーバーライドしているこの関数の引数
//	//  $name が本当に存在するフィールドの場合だけ trueを返すようにメンテする
//	public function hasField($name, $checkVirtual = false) {
//		return true;
//	}

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
		$this->_commodityIds = array();
		$this->_prCommodityIds = array();
		$this->_lowestPriceOfCarType = array();
		$this->_planQueryString = $this->createPlanQueryString($searchConditions);

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, $searchConditions);
		$cache_ret = $this->readCache($cache_name, '10minutes');

		if ($cache_ret !== false) {
			$this->_commodities = $cache_ret['commodities'];
			$this->_commodityIds = $cache_ret['commodityIds'];
			$this->_prCommodityIds = $cache_ret['prCommodityIds'];
			$this->_lowestPriceOfCarType = $cache_ret['lowestPriceOfCarType'];
		} else {
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
			$this->Landmark = new Landmark();
			$this->Station = new Station();
			$this->OfficeStation = new OfficeStation();
			$this->Area = new Area();
			$this->Client = new Client();
			$this->CommodityRentOffice = new CommodityRentOffice();
			$this->CommodityReturnOffice = new CommodityReturnOffice();
			$this->Maintenance = new Maintenance();
			$this->Recommend = new Recommend();

			$this->Office->setDataSource($this->getDataSource()->configKeyName);
			$this->CarType->setDataSource($this->getDataSource()->configKeyName);
			$this->OfficeStockGroup->setDataSource($this->getDataSource()->configKeyName);
			$this->PublicHoliday->setDataSource($this->getDataSource()->configKeyName);
			$this->Landmark->setDataSource($this->getDataSource()->configKeyName);
			$this->Station->setDataSource($this->getDataSource()->configKeyName);
			$this->OfficeStation->setDataSource($this->getDataSource()->configKeyName);
			$this->Area->setDataSource($this->getDataSource()->configKeyName);
			$this->Client->setDataSource($this->getDataSource()->configKeyName);
			$this->CommodityRentOffice->setDataSource($this->getDataSource()->configKeyName);
			$this->CommodityReturnOffice->setDataSource($this->getDataSource()->configKeyName);
			$this->Maintenance->setDataSource($this->getDataSource()->configKeyName);
			$this->Recommend->setDataSource($this->getDataSource()->configKeyName);

			// 今日の日付
			$today = date('Y-m-d');
			$now = date('Y-m-d H:i:s');

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

			// 人数からカータイプ取得
			$carTypeIds = $this->CarType->getCarTypeIdByPersonCount($personCount);

			if (empty($carTypeIds)) {
				$carTypeIds = array(0);
			}

			// 車種が選択されていた場合
			if (!empty($searchConditions['car_type']) && is_array($searchConditions['car_type'])) {
				// ご利用人数から算出した車両タイプと検索された車両タイプの重複のみ抽出し検索
				$carTypeIds = array_intersect($searchConditions['car_type'], $carTypeIds);
			}

			// エリアID
			$areaId = 0;
			if (!empty($searchConditions['area_id'])) {
				$areaId = $searchConditions['area_id'];
			}



			// 返却エリアID
			$returnAreaId = 0;
			if (!empty($searchConditions['return_area_id'])) {
				$returnAreaId = $searchConditions['return_area_id'];
			}

			// ソート順
			$sort = call_user_func(function($c) {
				$orders = Constant::searchSortOrders();
				$i = (!empty($c['sort'])) ? $c['sort'] : current($orders)['id'];

				// 空港以外は近い順を表示させない
				if (empty($c['place']) || $c['place'] != 3) {
					unset($orders[5]);
				}

				return isset($orders[$i]) ? $orders[$i] : current($orders);

			}, $searchConditions);

			// レーティング
			$ratings = array();
			if ($sort['order'] == 'rating') {
				$ratings  = (new YotpoReview())->getRatingsGroupByClientId();
			}

			/**
			 * 出発の営業所ID取得
			 */
			$officeIds = array();
			if ($place == 1) {
				// 都道府県の場合
				$officeIds = $this->Office->getOfficeIdListByAreaId($areaId);
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
					$returnOfficeIds = $this->Office->getOfficeIdListByAreaId($returnAreaId);
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

			// WEB決済可能が選択されていた場合
			$byWebSelected = false;
			if (!empty($searchConditions['option'])) {
				foreach ($searchConditions['option'] as $k => $v) {
					if ($v == 99) {
						$byWebSelected = true;
						// 本来オプションではないので、後続処理に影響出ないようunset
						unset($searchConditions['option'][$k]);
						if (empty($searchConditions['option'])) {
							unset($searchConditions['option']);
						}
						break;
					}
				}
			}

			// イーコンメンテモードか？
			if ($this->Maintenance->isEconMaintenance()) {
				// WEB事前決済のみは除く
				$conditions += array(
					'Commodity.payment_method <>' => 1,
				);
			} elseif ($byWebSelected) {
				// 現地決済のみは除く
				$conditions += array(
					'Commodity.payment_method <>' => 0,
				);
			}

			// クライアントIDがURLパラメータに含まれる場合
			if (!empty($searchConditions['client_id'])) {
				// client_id=1:全国(area_type = 1)
				// client_id=2:地域(area_type = 2)のクライアントとする
				if (!is_array($searchConditions['client_id']) && $searchConditions['client_id'] <= 2) {
					$client_id = $this->Client->getClientListByAreaType($searchConditions['client_id']);
				} else {
					$client_id = $searchConditions['client_id'];
				}
				// CAR-388 特定のclient_idを含む検索をされた場合、Constantで設定されたclient_idも同時に検索するようにする
				// client_idの送信方法がページごとに配列と変数で別れているので取得と設定方法を分ける
				$searchClientIdBind = Constant::searchClientIdBind();
				$targetSearchClientId = array_keys($searchClientIdBind);
				if(is_array($client_id)){
					foreach($targetSearchClientId as $tk => $tv){
						if(in_array($tv, $client_id)){
							$client_id = array_merge($client_id, $searchClientIdBind[$tv]);
						}
					}
				}else{
					if(in_array($client_id, $targetSearchClientId)){
						$client_id = array_merge(array($client_id), $searchClientIdBind[$client_id]);
					}
				}

				$conditions += array(
					'Client.id' => $client_id,
				);
			}

			// 祝日・曜日判定
			$previousDayInfo = $this->PublicHoliday->getDayInfo(date('Y-m-d', strtotime($dateFrom . ' -1 day')));
			$fromDayInfo = $this->PublicHoliday->getDayInfo($dateFrom);
			$toDayInfo = $this->PublicHoliday->getDayInfo($dateTo);

			// 出発営業所サブクエリ
			$commodityRentOffices = $this->getCommodityRentOfficeSubQuery($fromDayInfo['identifier'], $previousDayInfo['identifier'], $unixTimeDateFrom, $timeFrom, $officeIds);

			if (empty($commodityRentOffices)) {
				return false;
			}

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
								"'{$timeFrom}' BETWEEN Commodity.rent_time_from AND '{$timeFrom}'",
								"'{$timeFrom}' BETWEEN '00:00:00' AND Commodity.rent_time_to"
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
							"Commodity.return_time_from > Commodity.return_time_to",
							'OR' => array(
								"'{$timeTo}' BETWEEN Commodity.return_time_from AND '{$timeTo}'",
								"'{$timeTo}' BETWEEN '00:00:00' AND Commodity.return_time_to"
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
				'Commodity.sales_type' => isset($searchConditions['sales_type']) ? $searchConditions['sales_type'] : constant::SALES_TYPE_ARRANGED,
				'Commodity.delete_flg' => 0,
				'CommodityTerm.delete_flg' => 0,
				'Client.delete_flg' => 0,
			);

			// 必須条件とpostされてきた条件をマージ
			$conditions = array_merge($basicConditions, $conditions);

			$conditions = array(
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
					'Commodity.sales_type',
					'Client.id',
					'Client.sort',
					'CommodityItem.id',
					'CommodityItem.car_class_id',
					'CommodityItem.car_model_id',
					'CarClass.car_type_id',
					'CarClass.drop_off_price_pattern',
					'CommodityTerm.deadline_hours',
					'CommodityTerm.consider_opening_hours',
					'CommodityTerm.deadline_days',
					'CommodityTerm.deadline_time',
				),
				'recursive' => - 1
			);

			// ソートに必要な条件を追加する
			switch ($sort['order']) {
				case 'year':
					$conditions['fields'][] = 'Commodity.new_car_registration';
					break;
			}

			// 商品データを取得
			$commodityLists = $this->find('all', $conditions);

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
				$firstRentOffice = current($rentOffices);
				$firstNearestTransport = $firstRentOffice['nearest_transport'];
				$firstRequiredTransportTime = $firstRentOffice['required_transport_time'];
				$stockGroupIds = [];

				// 出発営業所毎に判定する
				foreach ($rentOffices as $rentOffice) {
					// 追加済みの在庫管理地域の判定
					if (isset($stockGroupIds[$rentOffice['office_stock_group_id']])) {
						continue;
					}

					if ($term['consider_opening_hours']) {
						// 営業開始時刻の判定
						if (!$this->isOfficeOpenOK($dateFrom, $datetimeFrom, $rentOffice['office_hours_from'], $rentOffice['office_hours_to_previous'], $term['deadline_hours'], $term['deadline_days'], $term['deadline_time'])) {
							continue;
						}
					}

					$commodity = array(
						'commodityId'			 => $val['Commodity']['id'],
						'salesType'				 => $val['Commodity']['sales_type'],
						'clientId'				 => $val['Client']['id'],
						'recommended'			 => $val['Client']['sort'],
						'commodityItemId'		 => $val['CommodityItem']['id'],
						'carClassId'			 => $val['CommodityItem']['car_class_id'],
						'carModelId'			 => $val['CommodityItem']['car_model_id'],
						'carTypeId'				 => $val['CarClass']['car_type_id'],
						'dropOffPricePattern'	 => $val['CarClass']['drop_off_price_pattern'],
						'stockGroupId'			 => $rentOffice['office_stock_group_id'],
						'deadlineHours'			 => $term['deadline_hours'],
						'deadline'				 => $deadline,
						'dayTimeFlg'			 => empty($val['Commodity']['day_time_flg']) ? 0 : 1,
					);

					$stockGroupIds[$rentOffice['office_stock_group_id']] = true;

					// ソートに必要な条件を追加する
					switch ($sort['order']) {
						case 'rating':
							$commodity['rating'] = !empty($ratings[$val['Client']['id']]) ? $ratings[$val['Client']['id']]['rating'] : 0;
							break;
						case 'year':
							$commodity['year'] = !empty($val['Commodity']['new_car_registration']) ? $val['Commodity']['new_car_registration'] : 99;
							break;
						case 'nearest':
							if (!isset($commodity['nearest'])) {
								$commodity['nearest'] = ($firstNearestTransport == 0) ? $firstRequiredTransportTime : 99;
							}
							break;
					}

					$commodities[] = $commodity;
				}
			}

			// 商品がなかった場合falseを返却
			if (empty($commodities)) {
				return false;
			}

			// 深夜手数料の最安値を取得
			// commodityRent/ReturnOfficesがあるうちに
			$commodities = $this->lateNightFeeSubQuery($commodities, $officeIds, $returnOfficeIds, $timeFrom, $timeTo, $commodityRentOffices, $commodityReturnOffices);

			unset($basicConditions, $conditions, $commodityLists, $commodityRentOffices, $commodityReturnOffices, $flipIds);

			/**
			 * *****************************************
			 * 第二段階
			 * 在庫・料金・オプションで商品を絞りSQLクエリを返却
			 * *****************************************
			 */

			// 検索条件にオプションがあれば条件に追加
			if (!empty($searchConditions['option'])) {
				$commodities = $this->optionSubQuery($commodities, $searchConditions['option']);

				if (empty($commodities)) {
					return false;
				}
			}

			// 対象を出来る限り少なくするため在庫よりも料金よりも先に乗り捨てを見る
			if (!empty($searchConditions['return_way']) && ($place != $returnPlace || $officeIds != $returnOfficeIds)) {
				// エリアが異なる場合は乗捨ての設定があるか見る
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
			$commodities = $this->optionPriceSubQuery($commodities, $spanCount, $spanCount24);

			// 車両タイプ別の最安値を取得
			foreach ($commodities as $k => $commodity) {
				if (empty($this->_lowestPriceOfCarType[$commodity['carTypeId']]) ||
					$this->_lowestPriceOfCarType[$commodity['carTypeId']] > $commodity['price']) {
					$this->_lowestPriceOfCarType[$commodity['carTypeId']] = $commodity['price'];
				}
			}

			// ソート順を指定する
			$sort_order1 = array();
			$sort_order2 = array();
			$sort_order3 = array();

			// 2番目のソート条件は
			// おすすめ順の場合は料金の安い順、他はおすすめ順とする
			$sort_key1 = $sort['order'];
			$sort_key2 = ($sort_key1 == 'recommended') ? 'price' : 'recommended';

			// 車両タイプフィルター用に反転
			$carTypeIds = array_flip($carTypeIds);

			foreach ($commodities as $k => $v) {
				// 選択されていない車両タイプは除外する
				if (!isset($carTypeIds[$v['carTypeId']])) {
					unset($commodities[$k]);
					continue;
				}

				$sort_order1[$k] = $v[$sort_key1];
				$sort_order2[$k] = $v[$sort_key2];
				$sort_order3[$k] = $v['commodityItemId'];
			}

			// ソートの実行
			array_multisort($sort_order1, $sort['direction'], $sort_order2, SORT_ASC, $sort_order3, SORT_ASC, $commodities);

			// pr表示チェック
			$prPrefecture = $this->getPrPrefecture($place, $areaId, $airportId, $stationId);

			$recommends = $this->Recommend->find('all',
				[
					'conditions' =>
					[
						'Recommend.apply_term_from <=' => $now,
						'Recommend.apply_term_to >=' => $now,
						'Recommend.is_published' => 1,
						'Recommend.delete_flg' => 0,
						'RecommendPrefecture.prefecture_id' => $prPrefecture,
						'RecommendPrefecture.delete_flg' => 0
					],
					'joins' => [
						[
							'type' => "INNER",
							'alias' => "RecommendPrefecture",
							'table' => "recommend_prefectures",
							'conditions' => "Recommend.id = RecommendPrefecture.recommend_id"
						]
					],
					'recursive' => -1,
				]
			);

			// PR商品を商品の最初に追加しcommodity_idを取得する
			if (!empty($recommends)) {
				$recommendClientIds = Hash::extract($recommends, '{n}.Recommend.client_id');
				$recommendTable = Hash::combine($recommends, '{n}.Recommend.client_id', '{n}.Recommend');
				$recommendCommodities = array();
				foreach ($commodities as $k => $v) {
					// PRする会社
					if (in_array($v['clientId'], $recommendClientIds) && !isset($recommendTable[$v['clientId']]['done'])) {
						$recommendTable[$v['clientId']]['done'] = true;
						$v['pr'] = true;
						$v['pr_title'] = $recommendTable[$v['clientId']]['pr_title'];
						$v['pr_space'] = (int)$recommendTable[$v['clientId']]['space'];
						$recommendCommodities[] = $v;
						if (count($recommendCommodities) == count($recommendClientIds)) {
							break;
						}
					}
				}
				if (!empty($recommendCommodities)) {
					$recommendCommodities = Hash::sort($recommendCommodities, '{n}.pr_space', 'desc');
					foreach ($recommendCommodities as $r) {
						array_unshift($commodities, $r);
						$this->_prCommodityIds[$r['clientId']] = $r['commodityId'];
					}
				}
			}

			// 確定した内容をメンバ変数に保存する
			$this->_commodities = $commodities;
			$this->_commodityIds = array_unique(Hash::extract($commodities, '{n}.commodityId'));

			// 返り値をキャッシュに設定 ※取り扱い注意
			$cache_ret = array(
				'commodities' => $commodities,
				'commodityIds' => $this->_commodityIds,
				'prCommodityIds' => $this->_prCommodityIds,
				'lowestPriceOfCarType' => $this->_lowestPriceOfCarType,
			);
			$this->writeCache($cache_name, $cache_ret, '10minutes');
		}

		// ページャーを考慮し範囲指定してcommodity_idを渡す
		$commodityIds = $limit === false ? $this->_commodityIds : array_slice($this->_commodityIds, ($page - 1) * $limit, $limit);

		// ページ内に商品が無い時
		if (empty($commodityIds)) {
			return false;
		}

		return array(
			'conditions' => array(
				'Commodity.id' => $this->_commodityIds,
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
			'paramType' => 'querystring',
		);
	}

	/**
	 * 商品マスタデータ取得
	 * STEP1で使用
	 */
	public function getCommodityData($commodityId, $requestData) {
		$resultFlg = true;

		$today = date('Y-m-d');
		$dateFrom = date('Y-m-d', strtotime($requestData['from']));
		$daysWait = (strtotime($dateFrom) - strtotime($today)) / 86400;

		$fromTime = date('H:i:s', strtotime($requestData['from']));
		$returnTime = date('H:i:s', strtotime($requestData['to']));

		$options = array(
			'fields' => array(
				'Commodity.*',
				'CommodityTerm.*',
				'Client.*'
			),
			'joins' => array(
				array(
					'table' => 'commodity_terms',
					'alias' => 'CommodityTerm',
					'type' => 'INNER',
					'conditions' => array(
						'CommodityTerm.commodity_id = Commodity.id'
					)
				),
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'LEFT',
					'conditions' => array(
						'Client.id = Commodity.client_id'
					)
				)
			),
			'conditions' => array(
				'Commodity.id' => $commodityId,
				array(
					'OR' => array(
						array(
							'Commodity.rent_time_from < Commodity.rent_time_to',
							"'{$fromTime}' BETWEEN Commodity.rent_time_from AND Commodity.rent_time_to"
						),
						array(
							"Commodity.rent_time_from  > Commodity.rent_time_to",
							'OR' => array(
								"'{$fromTime}'  BETWEEN Commodity.rent_time_from AND  '{$fromTime}'",
								"'{$fromTime}'  BETWEEN '00:00:00' AND Commodity.rent_time_to"
							)
						)
					)
				),
				array(
					'OR' => array(
						array(
							'Commodity.return_time_from < Commodity.return_time_to',
							"'{$returnTime}' BETWEEN Commodity.return_time_from AND Commodity.return_time_to"
						),
						array(
							"Commodity.return_time_from  > Commodity.return_time_to",
							'OR' => array(
								"'{$returnTime}'  BETWEEN Commodity.return_time_from AND  '{$returnTime}'",
								"'{$returnTime}'  BETWEEN '00:00:00' AND Commodity.return_time_to"
							)
						)
					)
				),
				'Commodity.is_published' => 1,
				'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
				'Commodity.delete_flg' => 0,
				'CommodityTerm.available_from <=' => $requestData['from'],
				'CommodityTerm.available_to >=' => $requestData['to'],
				array(
					'OR' => array(
						'CommodityTerm.bookable_days IS NULL',
						"CommodityTerm.bookable_days >= {$daysWait}"
					),
				),
				'CommodityTerm.delete_flg' => 0,
				'Client.delete_flg' => 0
			),
			'recursive' => - 1
		);
		$result = $this->find('first', $options);

		if (empty($result)) {
			$resultFlg = false;
		}

		$term = $result['CommodityTerm'];

		// 祝日・曜日判定
		$this->PublicHoliday = new PublicHoliday();
		$this->PublicHoliday->setDataSource($this->getDataSource()->configKeyName);

		$rentDay = date('Y-m-d', strtotime($requestData['from']));
		$returnDay = date('Y-m-d', strtotime($requestData['to']));

		$previousDayInfo = $this->PublicHoliday->getDayInfo(date('Y-m-d', strtotime($rentDay . ' -1 day')));
		$fromDayInfo = $this->PublicHoliday->getDayInfo($rentDay);
		$toDayInfo = $this->PublicHoliday->getDayInfo($returnDay);

		$unixTimeDateFrom = strtotime($rentDay);
		$unixTimeDateTo = strtotime($returnDay);

		// 締切時間の判定
		if (!$this->calculateDeadline($rentDay, $requestData['from'], $term['deadline_hours'], $term['deadline_days'], $term['deadline_time'])) {
			$resultFlg = false;
		}

		$this->Office = new Office();
		$this->Office->setDataSource($this->getDataSource()->configKeyName);

		// サブクエリ用のビヘイビアロード
		$this->Behaviors->load('CommodityDataBuildStatement');

		// 出発営業所サブクエリ
		$rentOfficeSql = $this->getRentOfficeSubQuery($fromDayInfo['identifier'], $previousDayInfo['identifier'], $unixTimeDateFrom, $requestData['client_id']);

		// 貸出営業所情報取得
		$rentOfficeOptions = array(
			'fields' => array(
				'CommodityRentOffice.*',
				'Office.*',
				'OfficeSupplement.nearest_transport',
				'OfficeSupplement.method_of_transport',
				'OfficeSupplement.required_transport_time',
			),
			'joins' => array(
				array(
					'table' => "({$rentOfficeSql})",
					'alias' => 'Office',
					'type' => 'INNER',
					'conditions' => array(
						'Office.id = CommodityRentOffice.office_id'
					)
				),
				array(
					'table' => "office_supplements",
					'alias' => 'OfficeSupplement',
					'type' => 'LEFT',
					'conditions' => array(
						'Office.id = OfficeSupplement.office_id'
					)
				),
			),
			'conditions' => array(
				'CommodityRentOffice.commodity_id' => $commodityId,
				array(
					'OR' => array(
						array(
							'Office.office_hours_from < Office.office_hours_to',
							"'{$fromTime}' BETWEEN Office.office_hours_from AND Office.office_hours_to"
						),
						array(
							"Office.office_hours_from > Office.office_hours_to",
							'OR' => array(
								"'{$fromTime}' BETWEEN Office.office_hours_from AND '{$fromTime}'",
								"'{$fromTime}' BETWEEN '00:00:00' AND Office.office_hours_to"
							)
						)
					)
				)
			),
			'order' => array(
				'Office.sort',
				'Office.id',
			),
			'recursive' => - 1
		);

		// エリア絞り込み
		if (!empty($requestData['area_id'])) {
			$rentOfficeOptions['fields'][] = 'Area.id';
			$rentOfficeOptions['fields'][] = 'Area.name';
			$rentOfficeOptions['conditions']['Office.area_id'] = $requestData['area_id'];
			$rentOfficeOptions['joins'][] = array(
				'table' => 'areas',
				'alias' => 'Area',
				'type' => 'INNER',
				'conditions' => array(
					'Area.id = Office.area_id'
				)
			);
			$rentOfficeOptions['conditions']['Area.id'] = $requestData['area_id'];
			$rentOfficeOptions['conditions']['Area.delete_flg'] = 0;
		}
		// 新幹線駅対応
		if (!empty($requestData['bullet_train_id'])) {
			$rentOfficeOptions['conditions']['Office.bullet_train_id'] = $requestData['bullet_train_id'];
		}
		// 空港対応
		if (!empty($requestData['airport_id'])) {
			$rentOfficeOptions['conditions']['Office.airport_id'] = $requestData['airport_id'];
		}
		// ローカル駅対応
		if (!empty($requestData['station_id'])) {
			$rentOfficeOptions['joins'][] = array(
				'table' => 'office_stations',
				'alias' => 'OfficeStation',
				'type' => 'INNER',
				'conditions' => array(
					'Office.id = OfficeStation.office_id',
					'OfficeStation.delete_flg = 0',
				),
			);
			$rentOfficeOptions['conditions']['OfficeStation.station_id'] = $requestData['station_id'];
		}
		// 営業所ID指定対応
		if (!empty($requestData['office_id'])) {
			$rentOfficeOptions['conditions']['Office.id'] = $requestData['office_id'];
		}

		$this->CommodityRentOffice = ClassRegistry::init('CommodityRentOffice');
		$this->CommodityRentOffice->setDataSource($this->getDataSource()->configKeyName);

		$commodityRentOfficeQuery = $this->CommodityRentOffice->find('all', $rentOfficeOptions);
		if (empty($commodityRentOfficeQuery)) {
			$resultFlg = false;
		} else {
			if ($term['consider_opening_hours']) {
				foreach ($commodityRentOfficeQuery as $key => $value) {
					// 営業開始時刻の判定
					$office = $value['Office'];
					if (!$this->isOfficeOpenOK($rentDay, $requestData['from'], $office['office_hours_from'], $office['office_hours_to_previous'], $term['deadline_hours'], $term['deadline_days'], $term['deadline_time'])) {
						unset($commodityRentOfficeQuery[$key]);
					}
				}
			}
			if (empty($commodityRentOfficeQuery)) {
				$resultFlg = false;
			} else {


				$rentOfficeList = array();
				foreach ($commodityRentOfficeQuery as $key => $value) {
					$o = !empty($value['OfficeSupplement']) ? $value['Office'] + $value['OfficeSupplement'] : $value['Office'];
					$rentOfficeList[] = $o;
					$commodityRentOfficeQuery[$key] = $value['CommodityRentOffice'];
				}
				$result['RentOffice'] = $rentOfficeList;
				$result['CommodityRentOffice'] = $commodityRentOfficeQuery;

				// 貸出営業所ID取得
				$rentOfficeIdArray = array();
				foreach ($commodityRentOfficeQuery as $key => $value) {
					$rentOfficeIdArray[] = $value['office_id'];
				}
				$rentOfficeIdUniques = array_unique($rentOfficeIdArray);

				// 在庫管理地域情報取得
				$officeStockGroupOptions = array(
					'conditions' => array(
						'OfficeStockGroup.office_id' => $rentOfficeIdUniques,
						'OfficeStockGroup.delete_flg' => 0
					),
					'recursive' => - 1
				);
				$this->OfficeStockGroup = ClassRegistry::init('OfficeStockGroup');
				$this->OfficeStockGroup->setDataSource($this->getDataSource()->configKeyName);

				$officeStockGroupQuery = $this->OfficeStockGroup->find('all', $officeStockGroupOptions);
			}
		}

		// 返却営業所サブクエリ
		$returnOfficeSql = $this->getReturnOfficeSubQuery($toDayInfo['identifier'], '', $unixTimeDateTo, $requestData['client_id']);

		// サブクエリ用のビヘイビアアンロード
		$this->Behaviors->unload('CommodityDataBuildStatement');

		// 返却営業所情報取得
		$returnOfficeOptions = array(
			'fields' => array(
				'CommodityReturnOffice.*',
				'Office.*'
			),
			'joins' => array(
				array(
					'table' => "({$returnOfficeSql})",
					'alias' => 'Office',
					'type' => 'INNER',
					'conditions' => array(
						'Office.id = CommodityReturnOffice.office_id'
					)
				)
			),
			'conditions' => array(
				'CommodityReturnOffice.commodity_id' => $commodityId,
				array(
					'OR' => array(
						array(
							'Office.office_hours_from < Office.office_hours_to',
							"'{$returnTime}' BETWEEN Office.office_hours_from AND Office.office_hours_to"
						),
						array(
							"Office.office_hours_from > Office.office_hours_to",
							'OR' => array(
								"'{$returnTime}' BETWEEN Office.office_hours_from AND '{$returnTime}'",
								"'{$returnTime}' BETWEEN '00:00:00' AND Office.office_hours_to"
							)
						)
					)
				)
			),
			'order' => array(
				'Office.sort',
				'Office.id',
			),
			'recursive' => - 1
		);

		// エリア対応
		if (!empty($requestData['return_area_id'])) {
			$returnOfficeOptions['fields'][] = 'Area.id';
			$returnOfficeOptions['fields'][] = 'Area.name';
			$returnOfficeOptions['conditions']['Office.area_id'] = $requestData['return_area_id'];
			$returnOfficeOptions['joins'][] = array(
				'table' => 'areas',
				'alias' => 'Area',
				'type' => 'INNER',
				'conditions' => array(
					'Area.id = Office.area_id'
				)
			);
			$returnOfficeOptions['conditions']['Area.id'] = $requestData['return_area_id'];
			$returnOfficeOptions['conditions']['Area.delete_flg'] = 0;
		}
		// 新幹線駅対応
		if (!empty($requestData['return_bullet_train_id'])) {
			$returnOfficeOptions['conditions']['Office.bullet_train_id'] = $requestData['return_bullet_train_id'];
		}
		// 空港対応
		if (!empty($requestData['return_airport_id'])) {
			$returnOfficeOptions['conditions']['Office.airport_id'] = $requestData['return_airport_id'];
		}
		// ローカル駅対応
		if (!empty($requestData['return_station_id'])) {
			$returnOfficeOptions['joins'][] = array(
				'table' => 'office_stations',
				'alias' => 'OfficeStation',
				'type' => 'INNER',
				'conditions' => array(
					'Office.id = OfficeStation.office_id',
					'OfficeStation.delete_flg = 0',
				),
			);
			$returnOfficeOptions['conditions']['OfficeStation.station_id'] = $requestData['return_station_id'];
		}
		// 営業所ID指定対応
		if (!empty($requestData['return_office_id'])) {
			$rentOfficeOptions['conditions']['Office.id'] = $requestData['return_office_id'];
		}

		$this->CommodityReturnOffice = ClassRegistry::init('CommodityReturnOffice');
		$this->CommodityReturnOffice->setDataSource($this->getDataSource()->configKeyName);

		$commodityReturnOfficeQuery = $this->CommodityReturnOffice->find('all', $returnOfficeOptions);
		if (empty($commodityReturnOfficeQuery)) {
			$resultFlg = false;
		} else {

			$returnOfficeList = array();
			foreach ($commodityReturnOfficeQuery as $key => $value) {
				$returnOfficeList[] = $value['Office'];
				$commodityReturnOfficeQuery[$key] = $value['CommodityReturnOffice'];
			}
			$result['ReturnOffice'] = $returnOfficeList;
			$result['CommodityReturnOffice'] = $commodityReturnOfficeQuery;
		}

		if (empty($officeStockGroupQuery)) {
			$resultFlg = false;
		} else {
			foreach ($officeStockGroupQuery as $key => $value) {
				$officeStockGroupQuery[$key] = $value['OfficeStockGroup'];
			}
			$result['OfficeStockGroup'] = $officeStockGroupQuery;
		}

		if ($resultFlg) {
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * 予約フォーム（確認ページ）
	 *
	 * @param unknown $reservationData
	 */
	public function getConfirmationCommodity($reservationData) {
		// サブクエリ用のビヘイビアロード
		$this->Behaviors->load('ConfirmationCommodityBuildStatement');

		$subQueryFromOffice = $this->getRentOfficeSubQuery($reservationData['Reservation']['from_office']);
		$subQueryReturnOffice = $this->getReturnOfficeSubQuery($reservationData['Reservation']['return_office']);

		// サブクエリ用のビヘイビアアンロード
		$this->Behaviors->unload('ConfirmationCommodityBuildStatement');

		$options = array(
			'fields' => array(
				'Client.*',
				'Commodity.*',
				'RentOffice.*',
				'ReturnOffice.*'
			),
			'joins' => array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'INNER',
					'conditions' => array(
						'Client.id = Commodity.client_id'
					)
				),
				array(
					'table' => "({$subQueryFromOffice})",
					'alias' => 'RentOffice',
					'type' => 'INNER',
					'conditions' => array(
						'RentOffice.commodity_id = Commodity.id'
					)
				),
				array(
					'table' => "({$subQueryReturnOffice})",
					'alias' => 'ReturnOffice',
					'type' => 'INNER',
					'conditions' => array(
						'ReturnOffice.commodity_id = Commodity.id'
					)
				)
			),
			'conditions' => array(
				'Commodity.id' => $reservationData['Reservation']['commodityId'],
				'Commodity.delete_flg' => 0,
				'Client.id' => $reservationData['Reservation']['clientId']
			),
			'recursive' => - 1
		);
		$result = $this->find('first', $options);
		return $result;
	}

	/**
	 * 指定されたエリアの店舗受取の料金を取得する
	 */
	public function getPriceForCityPage($areaId) {
		$defaultDate = date('Y-m-d', strtotime('+7 days'));

		$options = array(
			'fields'=>array(
				'Commodity.client_id',
				'Commodity.day_time_flg',
				'CommodityItem.id',
				'CommodityItem.car_class_id',
				'CarType.id',
				'CarType.name',
				'OfficeStockGroup.stock_group_id'
			),
			'conditions' => array(
				'Commodity.is_published' => 1,
				'CommodityTerm.available_from <=' => $defaultDate,
				'CommodityTerm.available_to >=' => $defaultDate,
				'Commodity.delete_flg' => 0,
				'RentOffice.delete_flg' => 0,
				'CommodityTerm.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
				'CarClass.delete_flg' => 0,
				'CarType.delete_flg' => 0,
				'Area.id' => $areaId,
				'Area.delete_flg' => 0
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
				array(
					'type'=>'INNER',
					'alias'=>'OfficeStockGroup',
					'table'=>'office_stock_groups',
					'conditions'=>'RentOffice.id = OfficeStockGroup.office_id'
				),
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
				),
				array(
					'type'=>'INNER',
					'alias'=>'Area',
					'table'=>'areas',
					'conditions'=>'Area.id = RentOffice.area_id'
				)
			),
			'group' => array(
				'CommodityItem.id'
			),
			'recursive' => -1
		);
		$commodityList = $this->findC('all', $options);

		$param = array();
		foreach ($commodityList as $v) {
			$param[] = array(
				'clientId' => $v['Commodity']['client_id'],
				'dayTimeFlg' => $v['Commodity']['day_time_flg'],
				'commodityItemId' => $v['CommodityItem']['id'],
				'carClassId' => $v['CommodityItem']['car_class_id'],
				'carTypeId' => $v['CarType']['id'],
				'carTypeName' => $v['CarType']['name'],
				'stockGroupId' => $v['OfficeStockGroup']['stock_group_id']
			);
		}
		// 在庫確認
		$param = $this->carClassStockSubQuery($param, $defaultDate, $defaultDate, 1);
		// 暦日制料金（1日）
		$day_price = $this->daySubQuery($param, 0, 1, $defaultDate, $defaultDate);
		// 時間制料金（6時間）
		$time_price = $this->timeSubQuery($param, 0, 0, 6, 0, $defaultDate, $defaultDate);

		// 暦日制/時間制をマージ
		$prices = array_merge($day_price, $time_price);
		unset($day_price);
		unset($time_price);

		// 免責補償料金
		$prices = $this->disclaimerCompensationSubQuery($prices, 1, 1, date('Y/m/d'));

		// 日産の当日予約専用を除く
		$prices = Hash::extract($prices, '{n}[basePrice<99999999]');

		return $prices;
	}

	/**
	 * 指定された市区町村の店舗受取の料金を取得する
	 */
	public function getPriceForMunicipalityPage($cityId) {
		$defaultDate = date('Y-m-d', strtotime('+7 days'));

		$options = array(
			'fields'=>array(
				'Commodity.client_id',
				'Commodity.day_time_flg',
				'CommodityItem.id',
				'CommodityItem.car_class_id',
				'CarType.id',
				'CarType.name',
				'OfficeStockGroup.stock_group_id'
			),
			'conditions' => array(
				'Commodity.is_published' => 1,
				'CommodityTerm.available_from <=' => $defaultDate,
				'CommodityTerm.available_to >=' => $defaultDate,
				'Commodity.delete_flg' => 0,
				'RentOffice.delete_flg' => 0,
				'CommodityTerm.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
				'CarClass.delete_flg' => 0,
				'CarType.delete_flg' => 0,
				'City.id' => $cityId,
				'City.delete_flg' => 0
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
				array(
					'type'=>'INNER',
					'alias'=>'OfficeStockGroup',
					'table'=>'office_stock_groups',
					'conditions'=>'RentOffice.id = OfficeStockGroup.office_id'
				),
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
				),
				array(
					'type'=>'INNER',
					'alias'=>'City',
					'table'=>'cities',
					'conditions'=>'City.id = RentOffice.city_id'
				)
			),
			'group' => array(
				'CommodityItem.id'
			),
			'recursive' => -1
		);
		$commodityList = $this->findC('all', $options);

		$param = array();
		foreach ($commodityList as $v) {
			$param[] = array(
				'clientId' => $v['Commodity']['client_id'],
				'dayTimeFlg' => $v['Commodity']['day_time_flg'],
				'commodityItemId' => $v['CommodityItem']['id'],
				'carClassId' => $v['CommodityItem']['car_class_id'],
				'carTypeId' => $v['CarType']['id'],
				'carTypeName' => $v['CarType']['name'],
				'stockGroupId' => $v['OfficeStockGroup']['stock_group_id']
			);
		}
		// 在庫確認
		$param = $this->carClassStockSubQuery($param, $defaultDate, $defaultDate, 1);
		// 暦日制料金（1日）
		$day_price = $this->daySubQuery($param, 0, 1, $defaultDate, $defaultDate);
		// 時間制料金（6時間）
		$time_price = $this->timeSubQuery($param, 0, 0, 6, 0, $defaultDate, $defaultDate);

		// 暦日制/時間制をマージ
		$prices = array_merge($day_price, $time_price);
		unset($day_price);
		unset($time_price);

		// 免責補償料金
		$prices = $this->disclaimerCompensationSubQuery($prices, 1, 1, date('Y/m/d'));

		// 日産の当日予約専用を除く
		$prices = Hash::extract($prices, '{n}[basePrice<99999999]');

		return $prices;
	}

	/**
	 * 指定された店舗受取の料金を取得する
	 *
	 * @param int|int[] $officeId
	 * @return array
	 */
	public function getPriceByOfficeId($officeId) {
		$defaultDate = date('Y-m-d', strtotime('+7 days'));
		$defaultDateTime = $defaultDate . ' 11:00:00';

		$options = array(
			'fields'=>array(
				'Commodity.client_id',
				'Commodity.day_time_flg',
				'CommodityItem.id',
				'CommodityItem.car_class_id',
				'CarType.id',
				'CarType.name',
                                'CommodityTerm.deadline_hours',
                                'CommodityTerm.deadline_days',
                                'CommodityTerm.deadline_time',
				'OfficeStockGroup.stock_group_id'
			),
			'conditions' => array(
				'Commodity.is_published' => 1,
				'CommodityTerm.available_from <=' => $defaultDate,
				'CommodityTerm.available_to >=' => $defaultDate,
				'Commodity.delete_flg' => 0,
				'Commodity.sales_type' => constant::SALES_TYPE_ARRANGED,
				'RentOffice.id' => $officeId,
				'RentOffice.delete_flg' => 0,
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
					'alias'=>'OfficeStockGroup',
					'table'=>'office_stock_groups',
					'conditions'=>'RentOffice.id = OfficeStockGroup.office_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'CarType',
					'table'=>'car_types',
					'conditions'=>'CarType.id = CarClass.car_type_id'
				)
			),
			'group' => array(
				'CommodityItem.id'
			),
			'recursive' => -1
		);

		$commodityList = $this->findC('all', $options);
		$param = array();
		foreach ($commodityList as $v) {
			// 締切時間の判定
			$term = $v['CommodityTerm'];
			$deadline = $this->calculateDeadline($defaultDate, $defaultDateTime, $term['deadline_hours'], $term['deadline_days'], $term['deadline_time']);
			if (!$deadline) {
				continue;
			}

			$param[] = array(
				'clientId' => $v['Commodity']['client_id'],
				'dayTimeFlg' => $v['Commodity']['day_time_flg'],
				'commodityItemId' => $v['CommodityItem']['id'],
				'carClassId' => $v['CommodityItem']['car_class_id'],
				'carTypeId' => $v['CarType']['id'],
				'carTypeName' => $v['CarType']['name'],
				'stockGroupId' => $v['OfficeStockGroup']['stock_group_id']
			);
		}

		// 在庫確認
		$param = $this->carClassStockSubQuery($param, $defaultDate, $defaultDate, 1);
		// 暦日制料金（1日）
		$day_price = $this->daySubQuery($param, 0, 1, $defaultDate, $defaultDate);
		// 時間制料金（6時間）
		$time_price = $this->timeSubQuery($param, 0, 0, 6, 0, $defaultDate, $defaultDate);

		// 暦日制/時間制をマージ
		$prices = array_merge($day_price, $time_price);
		unset($day_price);
		unset($time_price);

		// 免責補償料金
		$prices = $this->disclaimerCompensationSubQuery($prices, 1, 1, date('Y/m/d'));

		// 日産の当日予約専用を除く
		$prices = Hash::extract($prices, '{n}[basePrice<99999999]');

		return $prices;
	}

	/**
	 * プラン情報を取得する（クライアント系API用）
	 */
	public function getClientPlans($clientId) {
		$options = array(
			'fields' => array(
				'CommodityItem.id',
				'Commodity.name',
				"DATE_FORMAT(CommodityTerm.available_from, '%Y/%m/%d %H:%i:%s') AS available_from",
				"DATE_FORMAT(CommodityTerm.available_to, '%Y/%m/%d %H:%i:%s') AS available_to",
				'CommodityItem.car_class_id',
				'CommodityItem.car_model_id',
				'Commodity.is_published',
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
					'table' => 'commodity_terms',
					'alias' => 'CommodityTerm',
					'conditions' => 'CommodityTerm.commodity_id = Commodity.id',
				),
			),
			'conditions' => array(
				'Commodity.client_id' => $clientId,
				'Commodity.delete_flg' => 0,
			),
			'order' => 'Commodity.id',
			'recursive' => -1,
		);

		return $this->findC('all', $options);
	}

	public function getFirstImageByClientId($clientId) {

		$options = array(
			'fields' => array(
				'Commodity.image_relative_url'
			),
			'conditions' => array(
				'Commodity.client_id' => $clientId,
				'Commodity.is_published' => 1,
				'Commodity.delete_flg' => 0,
				'Commodity.image_relative_url != ""',
			),
			'recursive' => -1,
		);

		$image = $this->find('first', $options);

		return $image;
	}

	public function getCommodityInfoByCommodityItemId($commodityItemId) {
		return $this->findC('first', array(
			'fields' => array(
				'Commodity.id',
				'Commodity.client_id',
				'Commodity.name',
				'Commodity.description',
				'Commodity.remark',
				'CommodityItem.car_class_id',
				'CommodityItem.car_model_id',
				'CarType.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => array(
						'CommodityItem.commodity_id = Commodity.id',
						'CommodityItem.id' => $commodityItemId,
						'CommodityItem.delete_flg' => 0,
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'conditions' => array(
						'CarClass.id = CommodityItem.car_class_id',
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'car_types',
					'alias' => 'CarType',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id',
					),
				),
			),
			'conditions' => array(
				'Commodity.is_published' => 1,
				'Commodity.delete_flg' => 0,
			),
			'recursive' => -1,
		));
	}

	public function getMinDeadlineHour($date,$officeIds) {
		$basicConditions = array(
				'CommodityTerm.available_from <=' => $date,
				'CommodityTerm.available_to <=' => $date,
				'Commodity.is_published' => 1,
				'Commodity.delete_flg' => 0,
				'CommodityTerm.delete_flg' => 0,
				'CommodityTerm.deadline_hours IS NOT NULL',
				'CommodityRentOffice.office_id' => $officeIds
			);

		$conditions = array(
			'conditions' => $basicConditions,
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
					'alias' => 'CommodityRentOffice',
					'table' => 'commodity_rent_offices',
					'conditions' => array(
						'Commodity.id = CommodityRentOffice.commodity_id'
					)
				),
			),
			'fields' => array(
				'min(CommodityTerm.deadline_hours) as deadline_hours',
			),
			'recursive' => - 1
		);
		return $this->find('first', $conditions);
	}

	public function getPriceByAreaId($areaId,$datetimeFrom,$datetimeTo) {

		$options = array(
			'fields'=>array(
				'Commodity.client_id',
				'Commodity.day_time_flg',
				'CommodityItem.id',
				'CommodityItem.car_class_id',
				'CarType.id',
				'CarType.name',
				'OfficeStockGroup.stock_group_id'
			),
			'conditions' => array(
				'Commodity.is_published' => 1,
				'CommodityTerm.available_from <=' => $datetimeFrom,
				'CommodityTerm.available_to >=' => $datetimeTo,
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
				),
				array(
					'type'=>'INNER',
					'alias'=>'OfficeStockGroup',
					'table'=>'office_stock_groups',
					'conditions'=>'RentOffice.id = OfficeStockGroup.office_id'
				)
			),
			'group' => array(
				'CommodityItem.id'
			),
			'recursive' => -1
		);

		$commodityList = $this->findC('all', $options);

		$param = array();
		foreach ($commodityList as $v) {
			$param[] = array(
				'clientId' => $v['Commodity']['client_id'],
				'dayTimeFlg' => $v['Commodity']['day_time_flg'],
				'commodityItemId' => $v['CommodityItem']['id'],
				'carClassId' => $v['CommodityItem']['car_class_id'],
				'carTypeId' => $v['CarType']['id'],
				'carTypeName' => $v['CarType']['name'],
				'stockGroupId' => $v['OfficeStockGroup']['stock_group_id']
			);
		}

		$unixTimeDatetimeFrom = strtotime($datetimeFrom);
		$tmpDateFrom = new DateTime($datetimeFrom);
		$dateFrom = $tmpDateFrom->format('Y-m-d');

		$unixTimeDatetimeTo = strtotime($datetimeTo);
		$tmpDateTo = new DateTime($datetimeTo);
		$dateTo = $tmpDateTo->format('Y-m-d');

		list($spanCount, $spanCount24) = $this->getSpanCount($datetimeFrom, $datetimeTo);

		// 在庫確認
		$param = $this->carClassStockSubQuery($param, $dateFrom, $dateTo, $spanCount);

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
		$commodities_day = $this->daySubQuery($param, $superOtherDay1, $spanCount, $dateFrom, $dateTo);

		// 時間制料金取得
		$commodities_time = $this->timeSubQuery($param, $superOtherDay2, $superOtherDay3, $rentTime, $restTime, $dateFrom, $dateTo);

		// 暦日制/時間制をマージ
		$prices = array_merge($commodities_day, $commodities_time);
		unset($commodities_day);
		unset($commodities_time);

		// 免責補償料金
		$prices = $this->disclaimerCompensationSubQuery($prices, $spanCount, $spanCount24, $dateFrom);

		// 日産の当日予約専用を除く
		$prices = Hash::extract($prices, '{n}[basePrice<99999999]');

		return $prices;
	}

	/**
	 * pr表示対象地域取得
	 * @param $place (1:都道府県、3:空港、4:駅)
	 * @param $areaId
	 * @param $airportId
	 * @param $stationId
	 * @return bool
	 */
	private function getPrPrefecture($place, $areaId, $airportId, $stationId) {
		$prefectureId = 0;
		if ($place == 1) {
			// 都道府県の場合
			$areaInfo = $this->Area->getPrefectureIdByAreaId($areaId);
			$prefectureId = $areaInfo['Area']['prefecture_id'];
		} else if ($place == 3) {
			// 空港の場合
			$landmarkInfo = $this->Landmark->getPrefectureIdByAirportId($airportId);
			$prefectureId = $landmarkInfo['Landmark']['prefecture_id'];
		} else if ($place == 4) {
			// 駅の場合
			$stationInfo = $this->Station->getPrefectureIdByStationId($stationId);
			$prefectureId = $stationInfo['Station']['prefecture_id'];
		}

		return $prefectureId;
	}
}

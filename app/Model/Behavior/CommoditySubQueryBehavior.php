<?php
/**
 * Commodityモデルが検索系のサブクエリ関数でFat化してしまったので、
 * あまり良い方法ではないがこのビヘイビアに集約する。
 */
class CommoditySubQueryBehavior extends ModelBehavior {
	/**
	 * 出発営業所サブクエリ
	 * @param Model $model
	 * @param string $identifier
	 * @param string $previousIdentifier
	 * @param int $time
	 * @param string $timeStr
	 * @param mixed $officeId
	 * @return array
	 */
	public function getCommodityRentOfficeSubQuery(Model $model, $identifier, $previousIdentifier, $time, $timeStr, $officeId) {
		$db = $model->getDataSource();
		$previousDay = strtotime('-1 day', $time);

		$conditions = array(
			'fields' => array(
				'Office.id',
				'Office.name',
				'office_stock_groups.stock_group_id AS office_stock_group_id',
				'office_supplements.nearest_transport',
				'office_supplements.required_transport_time',
				"CASE"
				. " WHEN office_business_hours.office_id IS NOT NULL"
				. " THEN office_business_hours.{$identifier}_hours_from"
				. " ELSE Office.{$identifier}_hours_from"
				. " END AS office_hours_from",
				"CASE"
				. " WHEN office_business_hours.office_id IS NOT NULL"
				. " THEN office_business_hours.{$identifier}_hours_to"
				. " ELSE Office.{$identifier}_hours_to"
				. " END AS office_hours_to",
				'CASE'
				. ' WHEN office_supplements.method_of_transport IN (1, 2)'
				. ' THEN 1'
				. ' ELSE 0'
				. ' END AS can_pickup',
				"CASE"
				. " WHEN OfficeBusinessHourPrevious.office_id IS NOT NULL"
				. " THEN OfficeBusinessHourPrevious.{$previousIdentifier}_hours_to"
				. " ELSE Office.{$previousIdentifier}_hours_to"
				. " END AS office_hours_to_previous",
			),
			'table' => $db->fullTableName('offices'),
			'alias' => 'Office',
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'office_business_hours',
					'conditions' => array(
						'Office.id = office_business_hours.office_id',
						'office_business_hours.start_day_unixtime <=' => $time,
						'office_business_hours.end_day_unixtime >=' => $time,
						'office_business_hours.delete_flg = 0',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'office_business_hours',
					'alias' => 'OfficeBusinessHourPrevious',
					'conditions' => array(
						'Office.id = OfficeBusinessHourPrevious.office_id',
						'OfficeBusinessHourPrevious.start_day_unixtime <=' => $previousDay,
						'OfficeBusinessHourPrevious.end_day_unixtime >=' => $previousDay,
						'OfficeBusinessHourPrevious.delete_flg = 0',
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'office_stock_groups',
					'conditions' => array(
						'office_stock_groups.office_id = Office.id',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'office_supplements',
					'conditions' => array(
						'office_supplements.office_id = Office.id',
					),
				),
			),
			'conditions' => array(
				'Office.id' => $officeId,
				'Office.accept_rent' => 1,
				'Office.delete_flg' => 0,
			),
			'order' => 'Office.sort'
		);

		$rentOfficeSql = $db->buildStatement($conditions, $model);

		$ret = $model->CommodityRentOffice->findC('all', array(
			'fields' => array(
				'CommodityRentOffice.commodity_id',
				'RentOffice.id',
				'RentOffice.name',
				'RentOffice.office_stock_group_id',
				'RentOffice.nearest_transport',
				'RentOffice.required_transport_time',
				'RentOffice.office_hours_from',
				'RentOffice.can_pickup',
				'RentOffice.office_hours_to_previous',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RentOffice',
					'table' => "({$rentOfficeSql})",
					'conditions' => 'RentOffice.id = CommodityRentOffice.office_id'
				),
			),
			'conditions' => array(
				'OR' => array(
					array(
						'RentOffice.office_hours_from < RentOffice.office_hours_to',
						"'{$timeStr}' BETWEEN RentOffice.office_hours_from AND RentOffice.office_hours_to"
					),
					array(
						"RentOffice.office_hours_from > RentOffice.office_hours_to",
						'OR' => array(
							"'{$timeStr}' BETWEEN RentOffice.office_hours_from AND '{$timeStr}'",
							"'{$timeStr}' BETWEEN '00:00:00' AND RentOffice.office_hours_to"
						)
					)
				)
			),
			'recursive' => -1
		));

		$ret = Hash::combine($ret, '{n}.RentOffice.id', '{n}.RentOffice', '{n}.CommodityRentOffice.commodity_id');

		return $ret;
	}

	/**
	 * 返却営業所サブクエリ
	 * @param Model $model
	 * @param string $identifier
	 * @param int $time
	 * @param string $timeStr
	 * @param mixed $officeId
	 * @return array
	 */
	public function getCommodityReturnOfficeSubQuery(Model $model, $identifier, $time, $timeStr, $officeId) {
		$db = $model->getDataSource();

		$conditions = array(
			'fields' => array(
				'Office.id',
				'Office.name',
				"CASE"
				. " WHEN office_business_hours.office_id IS NOT NULL"
				. " THEN office_business_hours.{$identifier}_hours_from"
				. " ELSE Office.{$identifier}_hours_from"
				. " END AS office_hours_from",
				"CASE"
				. " WHEN office_business_hours.office_id IS NOT NULL"
				. " THEN office_business_hours.{$identifier}_hours_to"
				. " ELSE Office.{$identifier}_hours_to"
				. " END AS office_hours_to",
			),
			'table' => $db->fullTableName('offices'),
			'alias' => 'Office',
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'office_business_hours',
					'conditions' => array(
						'Office.id = office_business_hours.office_id',
						'office_business_hours.start_day_unixtime <=' => $time,
						'office_business_hours.end_day_unixtime >=' => $time,
						'office_business_hours.delete_flg = 0',
					),
				),
			),
			'conditions' => array(
				'Office.id' => $officeId,
				'Office.accept_return' => 1,
				'Office.delete_flg' => 0,
			),
		);
		$returnOfficeSql = $db->buildStatement($conditions, $model);

		$ret = $model->CommodityReturnOffice->findC('list', array(
			'fields' => array(
				'ReturnOffice.id',
				'ReturnOffice.name',
				'CommodityReturnOffice.commodity_id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'ReturnOffice',
					'table' => "({$returnOfficeSql})",
					'conditions' => 'ReturnOffice.id = CommodityReturnOffice.office_id'
				),
			),
			'conditions' => array(
				'OR' => array(
					array(
						'ReturnOffice.office_hours_from < ReturnOffice.office_hours_to',
						"'{$timeStr}' BETWEEN ReturnOffice.office_hours_from AND ReturnOffice.office_hours_to"
					),
					array(
						"ReturnOffice.office_hours_from > ReturnOffice.office_hours_to",
						'OR' => array(
							"'{$timeStr}' BETWEEN ReturnOffice.office_hours_from AND '{$timeStr}'",
							"'{$timeStr}' BETWEEN '00:00:00' AND ReturnOffice.office_hours_to"
						)
					)
				)
			),
			'group' => array(
				'CommodityReturnOffice.Commodity_id',
				'ReturnOffice.id',
			),
			'recursive' => -1
		));

		return $ret;
	}

	/**
	 * オプション取得サブクエリ
	 */
	public function optionSubQuery(Model $model, $commodities, $optionIds) {
		if (empty($commodities) || empty($optionIds)) {
			return array();
		}

		$commodityIds = $model->extract($commodities, '{n}.commodityId');

		if (empty($commodityIds)) {
			return array();
		}

		// 装備と特典のオプションカテゴリIDを全て取得する
		$model->virtualFields = array(
			'equipmentOptionIds' => 'GROUP_CONCAT(DISTINCT Equipment.option_category_id)',
			'priviegeOptionIds' => 'GROUP_CONCAT(DISTINCT Privilege.option_category_id)'
		);

		$options = array(
			'fields' => array(
				'Commodity.id',
				'equipmentOptionIds',
				'priviegeOptionIds',
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'CommodityEquipment',
					'table' => 'commodity_equipments',
					'conditions' => array(
						'Commodity.id = CommodityEquipment.commodity_id',
						'CommodityEquipment.delete_flg' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'alias' => 'Equipment',
					'table' => 'equipments',
					'conditions' => array(
						'Equipment.id = CommodityEquipment.equipment_id',
						'Equipment.is_published' => 1,
						'Equipment.delete_flg' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'alias' => 'CommodityPrivilege',
					'table' => 'commodity_privileges',
					'conditions' => array(
						'Commodity.id = CommodityPrivilege.commodity_id',
						'CommodityPrivilege.delete_flg' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'alias' => 'Privilege',
					'table' => 'privileges',
					'conditions' => array(
						'Privilege.id = CommodityPrivilege.privilege_id',
						'Privilege.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'Commodity.id' => $commodityIds,
			),
			'group' => 'Commodity.id',
			'recursive' => -1,
		);

		$ret = $model->findC('all', $options);
		$model->virtualFields = null;

		if (empty($ret)) {
			return array();
		}

		if (!is_array($optionIds)) {
			$optionIds = array($optionIds);
		}

		// 選択されたオプションの個数
		$cnt = count($optionIds);

		$ret_commodities = array();

		foreach ($commodities as $commodity) {
			$c = null;
			foreach ($ret as $k => $v) {
				if ($v['Commodity']['id'] == $commodity['commodityId']) {
					$c = $v['Commodity'];
					unset($ret[$k]);
					break 1;
				}
			}

			if (!$c) {
				continue;
			}

			// 装備と特典のオプションカテゴリIDをマージする
			$equipmentOptionIds = !empty($c['equipmentOptionIds']) ? explode(',', $c['equipmentOptionIds']) : array();
			$priviegeOptionIds = !empty($c['priviegeOptionIds']) ? explode(',', $c['priviegeOptionIds']) : array();
			$idList = array_unique(array_merge($equipmentOptionIds, $priviegeOptionIds));

			// 選択されたオプションが全て、オプションIDのリストに含まれているかチェック
			if (count(array_intersect($optionIds, $idList)) == $cnt) {
				$ret_commodities[] = $commodity;
			}
		}

		return $ret_commodities;
	}

	public function optionPriceSubQuery(Model $model, $commodities, $spanCount, $spanCount24) {
		if (empty($commodities)) {
			return array();
		}

		$commodityIds = $model->extract($commodities, '{n}.commodityId');

		if (empty($commodityIds)) {
			return array();
		}

		list($commodityIdsParam, $commodityIdsValue) = $model->createBindArray('commodityIds', $commodityIds);
		$params = $commodityIdsValue;

		if ($spanCount <= 31) {
			// 31日以内
			$params += array(
				'spanCount' => $spanCount,
				'superOtherDay' => 0,
			);
		} else {
			// 31日+超過分
			$params += array(
				'spanCount' => 31,
				'superOtherDay' => $spanCount - 31,
			);
		}

		if ($spanCount24 <= 31) {
			// 31日以内
			$params += array(
				'spanCount24' => $spanCount24,
				'superOtherDay24' => 0,
			);
		} else {
			// 31日+超過分
			$params += array(
				'spanCount24' => 31,
				'superOtherDay24' => $spanCount24 - 31,
			);
		}

		// 特典クエリ
		$sql = "
			SELECT
			  commodities.id
			  , `privileges`.option_category_id AS option_category_id
			  , `privileges`.name
			  , 0 AS option_default
			  , (
			    CASE
			      WHEN day_prices.price IS NOT NULL
			      AND `privileges`.period_flg = 0
			        THEN day_prices.price + (span_count_zero.price * :superOtherDay)
			      WHEN day_prices.price IS NOT NULL
			      AND `privileges`.period_flg = 1
			        THEN day_prices.price + (span_count_zero.price * :superOtherDay24)
			      WHEN rental_prices.price IS NOT NULL
			        THEN rental_prices.price
			      ELSE 0
			      END
			  ) price
			FROM
			  rentacar.commodities
			  INNER JOIN rentacar.commodity_privileges
			    ON commodities.id = commodity_privileges.commodity_id
			  INNER JOIN rentacar.`privileges`
			    ON `privileges`.id = commodity_privileges.privilege_id
			  LEFT JOIN rentacar.privilege_prices AS rental_prices
			    ON `privileges`.id = rental_prices.privilege_id
			    AND `privileges`.shape_flg = 0
			    AND rental_prices.span_count = 1
			    AND rental_prices.delete_flg = 0
			  LEFT JOIN rentacar.privilege_prices AS day_prices
			    ON `privileges`.id = day_prices.privilege_id
			    AND `privileges`.shape_flg = 1
			    AND (
			      (
			        `privileges`.period_flg = 0
			        AND day_prices.span_count = :spanCount
			      )
			      OR (
			        `privileges`.period_flg = 1
			        AND day_prices.span_count = :spanCount24
			      )
			    )
			    AND day_prices.delete_flg = 0
			  LEFT JOIN rentacar.privilege_prices AS span_count_zero
			    ON `privileges`.id = span_count_zero.privilege_id
			    AND `privileges`.shape_flg = 1
			    AND span_count_zero.span_count = 0
			    AND span_count_zero.delete_flg = 0
			WHERE
			  commodities.id IN ({$commodityIdsParam})
			  AND commodity_privileges.delete_flg = 0
			  AND `privileges`.delete_flg = 0
		";

		$ret = $model->queryC($sql, $params);

		if (empty($ret)) {
			return $commodities;
		}

		// オプション料金を追加する
		foreach ($commodities as $k => $commodity) {
			foreach ($ret as $price) {
				if ($commodity['commodityId'] == $price['commodities']['id']) {
					$commodities[$k]['Option'][] = array(
						'option_category_id'	 => $price['privileges']['option_category_id'],
						'name'					 => $price['privileges']['name'],
						'option_default'		 => $price[0]['option_default'],
						'price'					 => $price[0]['price'],
					);
				}
			}
		}

		return $commodities;
	}

	/**
	 * 乗捨て対象取得
	 * 商品に紐づく貸出営業所と返却営業所の組み合わせが存在する商品を返す
	 * ※正しい結果を判定するには出発と返却の営業所の組み合わせが
	 * 　商品検索と乗り捨てで一致するものをチェックしないといけない
	 */
	public function dropOffAreaRatesSubQuery(Model $model, $commodities, $officeIds, $returnOfficeIds) {
		if (empty($commodities) || empty($officeIds) || empty($returnOfficeIds)) {
			return array();
		}

		$commodityIds = $model->extract($commodities, '{n}.commodityId');

		if (empty($commodityIds)) {
			return array();
		}

		list($commodityIdsParam, $commodityIdsValue) = $model->createBindArray('commodityIds', $commodityIds);
		list($officeIdsParam, $officeIdsValue) = $model->createBindArray('officeIds', $officeIds);
		list($returnOfficeIdsParam, $returnOfficeIdsValue) = $model->createBindArray('returnOfficeIds', $returnOfficeIds);

		$params = $commodityIdsValue + $officeIdsValue + $returnOfficeIdsValue;

		$sql = "
			SELECT
			  commodity_rent_offices.commodity_id
			  , MIN(drop_off_area_rates.price) AS price
			  , MIN(drop_off_area_rates.price2) AS price2
			  , MIN(drop_off_area_rates.price3) AS price3
			FROM
			  rentacar.commodity_rent_offices USE INDEX(commodity_rent_offices_IX2)
			  INNER JOIN rentacar.offices AS rent_office
				ON rent_office.id = commodity_rent_offices.office_id
			  INNER JOIN rentacar.drop_off_areas AS rent_drop_off_areas
				ON rent_drop_off_areas.id = rent_office.area_drop_off_id
			  INNER JOIN rentacar.commodity_return_offices USE INDEX(commodity_return_offices_IX2)
				ON commodity_rent_offices.commodity_id = commodity_return_offices.commodity_id
			  INNER JOIN rentacar.offices AS return_office
				ON return_office.id = commodity_return_offices.office_id
			  INNER JOIN rentacar.drop_off_areas AS return_drop_off_areas
				ON return_drop_off_areas.id = return_office.area_drop_off_id
			  INNER JOIN rentacar.drop_off_area_rates
				ON rent_drop_off_areas.id = drop_off_area_rates.rent_drop_off_area_id
				AND return_drop_off_areas.id = drop_off_area_rates.return_drop_off_area_id
			WHERE
			  commodity_rent_offices.commodity_id IN ({$commodityIdsParam})
			  AND rent_office.id IN ({$officeIdsParam})
			  AND return_office.id IN ({$returnOfficeIdsParam})
			  AND drop_off_area_rates.delete_flg = 0
			  AND rent_drop_off_areas.delete_flg = 0
			  AND return_drop_off_areas.delete_flg = 0
			GROUP BY commodity_rent_offices.commodity_id
		";

		$ret = $model->queryC($sql, $params);
		
		if (empty($ret)) {
			return array();
		}

		$ret_commodities = array();

		// 乗り捨て設定がある商品のみを返す
		foreach ($commodities as $commodity) {
			foreach ($ret as $area) {
				if ($commodity['commodityId'] == $area['commodity_rent_offices']['commodity_id']) {
					$price = ($commodity['dropOffPricePattern'] > 1) ? 'price' . $commodity['dropOffPricePattern'] : 'price';
					$commodity['minDropPrice'] = $area[0][$price];
					$ret_commodities[] = $commodity;
				}
			}
		}

		return $ret_commodities;
	}

	/**
	 * 在庫を取得するサブクエリ
	 */
	public function carClassStockSubQuery(Model $model, $commodities, $dateFrom, $dateTo, $spanCount) {
		if (empty($commodities)) {
			return array();
		}

		$stockGroups = $model->extract($commodities, '{n}.stockGroupId');

		if (empty($stockGroups)) {
			return array();
		}

		$carClassIds = $model->extract($commodities, '{n}.carClassId');

		if (empty($carClassIds)) {
			return array();
		}

		list($stockGroupsParam, $stockGroupsValue) = $model->createBindArray('stockGroup', $stockGroups);
		list($carClassIdsParam, $carClassIdsValue) = $model->createBindArray('carClassId', $carClassIds);

		$params = array(
			'dateFrom' => $dateFrom,
			'dateTo' => $dateTo,
			'spanCount' => $spanCount,
		);

		$params += $stockGroupsValue + $carClassIdsValue;

		$sql = "
			SELECT
				car_class_stocks.car_class_id,
				car_class_stocks.stock_group_id,
				COUNT(car_class_stocks.car_class_id) AS day_count,
				car_class_stocks.stock_count - coalesce(car_class_reservations.reservation_count,0) AS numberRemaining
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
				(car_class_stocks.stock_count - coalesce(car_class_reservations.reservation_count,0)) > 0
			AND
				car_class_stocks.suspension = 0
			GROUP BY
				car_class_stocks.car_class_id,
				car_class_stocks.stock_group_id
			HAVING
				COUNT(car_class_stocks.car_class_id) = :spanCount
		";

		$ret = $model->queryC($sql, $params, '3minutes');

		if (empty($ret)) {
			$model->isOutOfStock = true; // 暫定対応 在庫なし判定
			return array();
		}

		$ret_commodities = array();

		// 在庫がある商品のみを返す
		foreach ($commodities as $commodity) {
			foreach ($ret as $stock) {
				if ($commodity['stockGroupId'] == $stock['car_class_stocks']['stock_group_id'] && $commodity['carClassId'] == $stock['car_class_stocks']['car_class_id']) {
					$commodity['dayCount'] = $stock[0]['day_count'];
					$commodity['numberRemaining'] = $stock[0]['numberRemaining'];
					$ret_commodities[] = $commodity;
				}
			}
		}

		return $ret_commodities;
	}

	/**
	 * 歴日制料金取得サブクエリ
	 *
	 * 借りる日数が1～5日の場合 -> 日帰りから4泊5日までの該当料金を見る
	 * 借りる日数が6日以上の場合 -> 4泊5日料金 + (以後一日料金 * 歴日制超過日));
	 * commodity_prices.span_count = 0は超過分のレコード
	 */
	public function daySubQuery(Model $model, $commodities, $superOtherDay, $spanCount, $dateFrom, $dateTo) {
		if (empty($commodities)) {
			return array();
		}

		$commodityItemIds = $model->extract($commodities, '{n}[dayTimeFlg=0].commodityItemId');

		if (empty($commodityItemIds)) {
			return array();
		}

		$price = array();
		$campaignPrice = array();

		list($commodityItemIdsParam, $commodityItemIdsValue) = $model->createBindArray('commodityItemIds', $commodityItemIds);

		$Campaign = ClassRegistry::init('Campaign');
		$Campaign->setDataSource($model->getDataSource()->configKeyName);
		$ret = $Campaign->getCampaignIds(true, $commodityItemIds, $dateFrom);
		if (!empty($ret)) {
			// キャンペーン料金
			$campaignIds = $model->extract($ret, '{n}.CampaignTerm.campaign_id');

			list($campaignIdsParam, $campaignIdsValue) = $model->createBindArray('campaignIds', $campaignIds);

			$params = $commodityItemIdsValue + $campaignIdsValue;

			if ($spanCount <= 5) {
				// 日数が5日以内の場合
				$params += array(
					'spanCount' => $spanCount,
				);

				$sql = "
					SELECT
					  commodity_campaign_prices.commodity_item_id
					  , commodity_campaign_prices.price AS price
					FROM
					  rentacar.commodity_campaign_prices
					WHERE
					  commodity_campaign_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_campaign_prices.campaign_id IN ({$campaignIdsParam})
					  AND commodity_campaign_prices.span_count = :spanCount
					  AND commodity_campaign_prices.delete_flg = 0
				";
			} else {
				// 日数が5日より大きいは超過分も計算する
				$params += array(
					'superOtherDay' => $superOtherDay,
				);

				$sql = "
					SELECT
					  commodity_campaign_prices.commodity_item_id
					  , commodity_campaign_prices.price + (span_count_zero.price * :superOtherDay) AS price
					FROM
					  rentacar.commodity_campaign_prices
					  INNER JOIN rentacar.commodity_campaign_prices AS span_count_zero
					    ON commodity_campaign_prices.commodity_item_id = span_count_zero.commodity_item_id
					    AND span_count_zero.campaign_id IN ({$campaignIdsParam})
					    AND span_count_zero.span_count = 0
					    AND span_count_zero.delete_flg = 0
					WHERE
				      commodity_campaign_prices.commodity_item_id IN ({$commodityItemIdsParam})
				      AND commodity_campaign_prices.campaign_id IN ({$campaignIdsParam})
					  AND commodity_campaign_prices.span_count = 5
					  AND commodity_campaign_prices.delete_flg = 0
				";
			}

			$ret = $model->queryC($sql, $params);

			if (!empty($ret)) {
				if ($spanCount <= 5) {
					$campaignPrice = Hash::combine($ret, '{n}.commodity_campaign_prices.commodity_item_id', '{n}.commodity_campaign_prices.price');
				} else {
					$campaignPrice = Hash::combine($ret, '{n}.commodity_campaign_prices.commodity_item_id', '{n}.0.price');
				}
			}
		}

		//予約範囲内で0円キャンペーン(非表示)を含む場合上書きする
		$CommodityCampaignPrice = ClassRegistry::init('CommodityCampaignPrice');
		$campaignPrice = array_replace($campaignPrice, $CommodityCampaignPrice->getCampaignInvalidDate($commodityItemIds, $dateFrom, $dateTo, 1));

		if (!empty($campaignPrice)) {
			// キャンペーン料金のある商品を除く
			$commodityItemIds = array_values(array_diff($commodityItemIds, array_keys($campaignPrice)));
			if (!empty($commodityItemIds)) {
				list($commodityItemIdsParam, $commodityItemIdsValue) = $model->createBindArray('commodityItemIds', $commodityItemIds);
			}
		}

		if (!empty($commodityItemIds)) {
			// 通常料金
			$params = $commodityItemIdsValue;
			if (current($commodities)['salesType'] == constant::SALES_TYPE_AGENT_ORGANIZED) {
				// 募集型料金
				$params += [
					'spanCount' => $spanCount,
					'dateFrom' => $dateFrom,
				];

				$sql = "
					select
						 commodity_item_id
						,max(case :spanCount
							  when 1 then 0
							  when 2 then price_stay_1
							  when 3 then price_stay_2
							  when 4 then price_stay_3
							  else (price_stay_3 + (price_stay_over * (:spanCount - 4)))
							end) as price
					  from
					  	rentacar.agent_organized_prices as commodity_prices
					 where delete_flg = 0
					   and commodity_item_id IN ({$commodityItemIdsParam})
					   and start_date <= :dateFrom
					   and end_date >= :dateFrom
					group by commodity_item_id
					having price > 0
				";

			} else {
				// 手配型料金
				if ($spanCount <= 5) {
					// 日数が5日以内の場合
					$params += array(
						'spanCount' => $spanCount,
					);

					$sql = "
						SELECT
						commodity_prices.commodity_item_id
						, commodity_prices.price AS price
						FROM
						rentacar.commodity_prices
						WHERE
						commodity_prices.commodity_item_id IN ({$commodityItemIdsParam})
						AND commodity_prices.price > 0
						AND commodity_prices.span_count = :spanCount
						AND commodity_prices.delete_flg = 0
					";
				} else {
					// 日数が5日より大きいは超過分も計算する
					$params += array(
						'superOtherDay' => $superOtherDay,
					);

					$sql = "
						SELECT
						commodity_prices.commodity_item_id
						, commodity_prices.price + (span_count_zero.price * :superOtherDay) AS price
						FROM
						rentacar.commodity_prices
						INNER JOIN rentacar.commodity_prices AS span_count_zero
							ON commodity_prices.commodity_item_id = span_count_zero.commodity_item_id
							AND span_count_zero.span_count = 0
							AND span_count_zero.price > 0
							AND span_count_zero.delete_flg = 0
						WHERE
						commodity_prices.commodity_item_id IN ({$commodityItemIdsParam})
						AND commodity_prices.span_count = 5
						AND commodity_prices.price > 0
						AND commodity_prices.delete_flg = 0
					";
				}
			}
			$ret = $model->queryC($sql, $params);

			if (!empty($ret)) {
				if (isset(current($ret)['commodity_prices']['commodity_item_id']) && isset(current($ret)['commodity_prices']['price'])) {
					$price = Hash::combine($ret, '{n}.commodity_prices.commodity_item_id', '{n}.commodity_prices.price');
				} else {
					$price = Hash::combine($ret, '{n}.commodity_prices.commodity_item_id', '{n}.0.price');
				}
			}
		}

		if (empty($price) && empty($campaignPrice)) {
			return array();
		}
		$price += $campaignPrice;

		$ret_commodities = array();

		// 料金がある商品のみを返す
		foreach ($commodities as $commodity) {
			if (!empty($price[$commodity['commodityItemId']])) {
				$p = $price[$commodity['commodityItemId']];
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

	/**
	 * 時間制料金取得サブクエリ
	 *
	 * 借りる時間数が1～24時間の場合
	 * １～24の間の該当料金を見る
	 * 借りる時間数が25時間以上の場合
	 * 24時間料金 + ((以後一日料金 * 超過日2) + (超過一時間料金 * 余り時間))
	 * もしくは
	 * 24時間料金 + (以後一日料金 * 超過日3)
	 * いずれかの安い金額を適用
	 */
	public function timeSubQuery(Model $model, $commodities, $floorSuperOtherDay, $ceilSuperOtherDay, $rentTime, $restTime, $dateFrom, $dateTo) {
		if (empty($commodities)) {
			return array();
		}
		if (current($commodities)['salesType'] == constant::SALES_TYPE_AGENT_ORGANIZED) {
			return [];
		}

		$commodityItemIds = $model->extract($commodities, '{n}[dayTimeFlg=1].commodityItemId');

		if (empty($commodityItemIds)) {
			return array();
		}

		$price = array();
		$campaignPrice = array();

		list($commodityItemIdsParam, $commodityItemIdsValue) = $model->createBindArray('commodityItemIds', $commodityItemIds);

		$Campaign = ClassRegistry::init('Campaign');
		$Campaign->setDataSource($model->getDataSource()->configKeyName);
		$ret = $Campaign->getCampaignIds(true, $commodityItemIds, $dateFrom);
		if (!empty($ret)) {
			// キャンペーン料金
			$campaignIds = $model->extract($ret, '{n}.CampaignTerm.campaign_id');

			list($campaignIdsParam, $campaignIdsValue) = $model->createBindArray('campaignIds', $campaignIds);

			$params = $commodityItemIdsValue + $campaignIdsValue;

			if ($rentTime <= 24) {
				// 24時間以内
				$params += array(
					'rentTime' => $rentTime,
				);

				$sql = "
					SELECT
					  commodity_campaign_prices.commodity_item_id
					  , commodity_campaign_prices.price AS price
					FROM
					  rentacar.commodity_campaign_prices
					WHERE
					  commodity_campaign_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_campaign_prices.campaign_id IN ({$campaignIdsParam})
					  AND commodity_campaign_prices.span_count = :rentTime
					  AND commodity_campaign_prices.delete_flg = 0
				";
			} else {
				// 24時間より大きい場合は超過分も計算する
				$params += array(
					'restTime' => $restTime,
					'floorSuperOtherDay' => $floorSuperOtherDay,
					'ceilSuperOtherDay' => $ceilSuperOtherDay,
				);

				$sql = "
					SELECT
					  commodity_campaign_prices.commodity_item_id
					  , commodity_campaign_prices.price + (
						CASE
						  WHEN (span_count_zero.price * :floorSuperOtherDay) + (span_count_25.price * :restTime) < (span_count_zero.price * :ceilSuperOtherDay)
							THEN (span_count_zero.price * :floorSuperOtherDay) + (span_count_25.price * :restTime)
						  ELSE (span_count_zero.price * :ceilSuperOtherDay)
						  END
					  ) AS price
					FROM
					  rentacar.commodity_campaign_prices
					  INNER JOIN rentacar.commodity_campaign_prices AS span_count_zero
						ON commodity_campaign_prices.commodity_item_id = span_count_zero.commodity_item_id
						AND span_count_zero.campaign_id IN ({$campaignIdsParam})
						AND span_count_zero.span_count = 0
						AND span_count_zero.delete_flg = 0
					  INNER JOIN rentacar.commodity_campaign_prices AS span_count_25
						ON commodity_campaign_prices.commodity_item_id = span_count_25.commodity_item_id
						AND span_count_25.campaign_id IN ({$campaignIdsParam})
						AND span_count_25.span_count = 25
						AND span_count_25.delete_flg = 0
					WHERE
					  commodity_campaign_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_campaign_prices.campaign_id IN ({$campaignIdsParam})
					  AND commodity_campaign_prices.span_count = 24
					  AND commodity_campaign_prices.delete_flg = 0
				";
			}

			$ret = $model->queryC($sql, $params);

			if (!empty($ret)) {
				if ($rentTime <= 24) {
					$campaignPrice = Hash::combine($ret, '{n}.commodity_campaign_prices.commodity_item_id', '{n}.commodity_campaign_prices.price');
				} else {
					$campaignPrice = Hash::combine($ret, '{n}.commodity_campaign_prices.commodity_item_id', '{n}.0.price');
				}
			}
		}

		//予約範囲内で0円キャンペーン(非表示)を含む場合上書きする
		$CommodityCampaignPrice = ClassRegistry::init('CommodityCampaignPrice');
		$campaignPrice = array_replace($campaignPrice, $CommodityCampaignPrice->getCampaignInvalidDate($commodityItemIds, $dateFrom, $dateTo, 0));

		if (!empty($campaignPrice)) {
			// キャンペーン料金のある商品を除く
			$commodityItemIds = array_values(array_diff($commodityItemIds, array_keys($campaignPrice)));
			if (!empty($commodityItemIds)) {
				list($commodityItemIdsParam, $commodityItemIdsValue) = $model->createBindArray('commodityItemIds', $commodityItemIds);
			}
		}

		if (!empty($commodityItemIds)) {
			// 通常料金
			$params = $commodityItemIdsValue;

			if ($rentTime <= 24) {
				// 24時間以内
				$params += array(
					'rentTime' => $rentTime,
				);

				$sql = "
					SELECT
					  commodity_prices.commodity_item_id
					  , commodity_prices.price AS price
					FROM
					  rentacar.commodity_prices
					WHERE
					  commodity_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_prices.price > 0
					  AND commodity_prices.span_count = :rentTime
					  AND commodity_prices.delete_flg = 0
				";
			} else {
				// 24時間より大きい場合は超過分も計算する
				$params += array(
					'restTime' => $restTime,
					'floorSuperOtherDay' => $floorSuperOtherDay,
					'ceilSuperOtherDay' => $ceilSuperOtherDay,
				);

				$sql = "
					SELECT
					  commodity_prices.commodity_item_id
					  , commodity_prices.price + (
						CASE
						  WHEN (span_count_zero.price * :floorSuperOtherDay) + (span_count_25.price * :restTime) < (span_count_zero.price * :ceilSuperOtherDay)
							THEN (span_count_zero.price * :floorSuperOtherDay) + (span_count_25.price * :restTime)
						  ELSE (span_count_zero.price * :ceilSuperOtherDay)
						  END
					  ) AS price
					FROM
					  rentacar.commodity_prices
					  INNER JOIN rentacar.commodity_prices AS span_count_zero
						ON commodity_prices.commodity_item_id = span_count_zero.commodity_item_id
						AND span_count_zero.span_count = 0
						AND span_count_zero.price > 0
						AND span_count_zero.delete_flg = 0
					  INNER JOIN rentacar.commodity_prices AS span_count_25
						ON commodity_prices.commodity_item_id = span_count_25.commodity_item_id
						AND span_count_25.span_count = 25
						AND span_count_25.price > 0
						AND span_count_25.delete_flg = 0
					WHERE
					  commodity_prices.commodity_item_id IN ({$commodityItemIdsParam})
					  AND commodity_prices.price > 0
					  AND commodity_prices.span_count = 24
					  AND commodity_prices.delete_flg = 0
				";
			}

			$ret = $model->queryC($sql, $params);

			if (!empty($ret)) {
				if ($rentTime <= 24) {
					$price = Hash::combine($ret, '{n}.commodity_prices.commodity_item_id', '{n}.commodity_prices.price');
				} else {
					$price = Hash::combine($ret, '{n}.commodity_prices.commodity_item_id', '{n}.0.price');
				}
			}
		}

		if (empty($price) && empty($campaignPrice)) {
			return array();
		}
		$price += $campaignPrice;

		$ret_commodities = array();

		// 料金がある商品のみを返す
		foreach ($commodities as $commodity) {
			if (!empty($price[$commodity['commodityItemId']])) {
				$p = $price[$commodity['commodityItemId']];
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

	/**
	 * 免責補償料金を求める
	 */
	public function disclaimerCompensationSubQuery(Model $model, $commodities, $spanCount, $spanCount24, $dateFrom) {
		if (empty($commodities)) {
			return array();
		}

		$carClassIds = $model->extract($commodities, '{n}.carClassId');

		if (empty($carClassIds)) {
			return array();
		}

		list($carClassIdsParam, $carClassIdsValue) = $model->createBindArray('carClassIds', $carClassIds);

		$params = array(
			'spanCount'		 => $spanCount,
			'spanCount24'	 => $spanCount24,
			'dateFrom'		 => $dateFrom,
		);

		$params += $carClassIdsValue;

		$sql = "
			  SELECT
				car_class_id,
				IF(period_flg = 1,
					(
						CASE
						WHEN
							period_limit = 0 THEN price * :spanCount24
						WHEN
							period_limit < :spanCount24 THEN price * period_limit
						ELSE
							price * :spanCount24
						END
					),
					(
						CASE
						WHEN
							period_limit = 0 THEN price * :spanCount
						WHEN
							period_limit < :spanCount THEN price * period_limit
						ELSE
							price * :spanCount
						END
					)
				)
				AS price
			 FROM
			  rentacar.disclaimer_compensations
			WHERE
			  car_class_id IN ({$carClassIdsParam})
			AND
			  start_date <= :dateFrom
			AND
			  end_date >= :dateFrom
			AND
			  delete_flg = 0
		    ";

		$ret = $model->queryC($sql, $params);

		if (empty($ret)) {
			return array();
		}

		$ret_commodities = array();

		// 料金がある商品のみを返す
		foreach ($commodities as $commodity) {
			foreach ($ret as $price) {
				if ($commodity['carClassId'] == $price['disclaimer_compensations']['car_class_id']) {
					$p = isset($price[0]) ? $price[0]['price'] : $price['disclaimer_compensations']['price'];
					if (empty($commodity['price'])) {
						$commodity['price'] = $p;
					} else {
						$commodity['price'] += $p;
					}
					$commodity['disclaimerCompensation'] = $p;

					$ret_commodities[] = $commodity;
				}
			}
		}

		return $ret_commodities;
	}

	/**
	 * 深夜手数料最安値取得
	 * 貸出・返却の営業所と時刻から深夜手数料を取得する
	 */
	public function lateNightFeeSubQuery(Model $model, $commodities, $officeIds, $returnOfficeIds, $timeFrom, $timeTo, $commodityRentOffices, $commodityReturnOffices) {
		if (empty($commodities) ||
			empty($officeIds) || empty($returnOfficeIds) ||
			empty($timeFrom) || empty($timeTo) ||
			empty($commodityRentOffices) || empty($commodityReturnOffices)) {
			return array();
		}

		list($lateNightFees, $returnLateNightFees) = $model->Office->getLateNightFees($officeIds, $returnOfficeIds, $timeFrom, $timeTo);

		if (empty($lateNightFees) && empty($returnLateNightFees)) {
			return $commodities;
		}

		// commodityRentOfficesとcommodityReturnOfficesとは形式が異なる
		// commodityReturnOfficesを使いやすく整形
		$returnOfficeIdsGroupByCommodity = array();
		foreach ($commodityReturnOffices as $commodityId => $officeInfo) {
			$returnOfficeIdsGroupByCommodity[$commodityId] = array_keys($officeInfo);
		}

		$ret_commodities = array();

		// 深夜手数料の最安値をセット
		foreach ($commodities as $commodity) {
			$tmpFee = null;
			$commodityId = $commodity['commodityId'];
			// 貸出営業所
			if (!empty($lateNightFees)) {
				$commodityRentOfficeIds = Hash::extract($commodityRentOffices[$commodityId], '{n}.id');
				if (!empty($commodityRentOfficeIds)) {
					$possibles = array();
					foreach ($commodityRentOfficeIds as $id) {
						$possibles[] = !empty($lateNightFees[$id]) ? $lateNightFees[$id]['price'] : 0;
					}
					if (!(min($possibles) == 0 && max($possibles) == 0)) { // 最大最小両方ゼロは深夜手数料設定なし
						$tmpFee = min($possibles);
					}
				}
			}
			// 返却営業所
			if (!empty($returnLateNightFees)) {
				$commodityReturnOfficeIds = $returnOfficeIdsGroupByCommodity[$commodityId];
				if (!empty($commodityReturnOfficeIds)) {
					$possibles = array();
					$feeAlreadySet = !empty($tmpFee);
					foreach ($commodityReturnOfficeIds as $id) {
						if (!empty($returnLateNightFees[$id]) &&
							!($feeAlreadySet && $returnLateNightFees[$id]['price_addition_flg'] == 1)) {
							$possibles[] = $returnLateNightFees[$id]['price'];
						} else {
							$possibles[] = 0;
						}
					}
					if (!(min($possibles) == 0 && max($possibles) == 0)) {
						$tmpFee = ((int) $tmpFee) + min($possibles);
					}
				}
			}
			if (!is_null($tmpFee)) {
				$commodity['minLateNightFee'] = $tmpFee;
			}
			$ret_commodities[] = $commodity;
		}

		return $ret_commodities;
	}
}

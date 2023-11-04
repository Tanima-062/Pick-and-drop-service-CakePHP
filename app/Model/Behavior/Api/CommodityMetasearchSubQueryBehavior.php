<?php
class CommodityMetasearchSubQueryBehavior extends ModelBehavior {

	/**
	 * 乗捨て料金取得
	 * 商品に紐づく貸出営業所と返却営業所の全ての組み合わせの料金を返す
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
			  , rent_office.id
			  , return_office.id
			  , drop_off_area_rates.price
			  , drop_off_area_rates.price2
			  , drop_off_area_rates.price3
			FROM
			  rentacar.commodity_rent_offices
			  INNER JOIN rentacar.offices AS rent_office
				ON rent_office.id = commodity_rent_offices.office_id
			  INNER JOIN rentacar.drop_off_areas AS rent_drop_off_areas
				ON rent_drop_off_areas.id = rent_office.area_drop_off_id
			  INNER JOIN rentacar.commodity_return_offices
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
		";

		$ret = $model->queryC($sql, $params);
		
		$dropOffList = array();
		foreach ($ret as $v) {
			$id = $v['commodity_rent_offices']['commodity_id'];
			
			if (isset($dropOffList[$id])) {
				$dropOffList[$id][] = $v;
			} else {
				$dropOffList[$id] = array($v);
			}
		}

		$ret_commodities = array();

		// 乗り捨て設定から返却営業所のリストを再作成する
		foreach ($commodities as $commodity) {
			$returnOffice = array();
			
			// 商品IDが存在するもののみ
			if (empty($dropOffList[$commodity['commodityId']])) {
				continue;
			}

			foreach ($dropOffList[$commodity['commodityId']] as $v) {
				$returnOfficeId = $v['return_office']['id'];

				// 出発と返却の営業所が一致するもののみ
				if ($commodity['officeId'] != $v['rent_office']['id'] || !isset($commodity['returnOffice'][$returnOfficeId])) {
					 continue;
				}

				// 乗捨料金パターンで適用する料金を変える
				$price = ($commodity['dropOffPricePattern'] > 1) ?
					$v['drop_off_area_rates']['price' . $commodity['dropOffPricePattern']] :
					$v['drop_off_area_rates']['price'];

				$p = isset($v[0]) ? $v[0]['price'] : $price;
				// 最安値の更新
				if (!isset($commodity['dropOffCharge']) || $commodity['dropOffCharge'] < $p) {
					$commodity['dropOffCharge'] = $p;
				}
				// 返却営業所に乗り捨て料金を追加する
				$returnOffice[$returnOfficeId] = array(
					'id'	 => $returnOfficeId,
					'name'	 => $commodity['returnOffice'][$returnOfficeId],
					'price'	 => $price,
				);

			}
			$commodity['returnOffice'] = $returnOffice;
			
			if ($officeIds == $returnOfficeIds || isset($commodity['dropOffCharge'])) {
				// 別エリアで最安値がセットされていない＝乗り捨て設定が無い
				$ret_commodities[] = $commodity;
			}
		}

		return $ret_commodities;
	}

	/**
	 * オプション料金取得サブクエリ
	 * メタサーチ向けに装備と特典両方を取得する
	 * privilege_prices.span_count = 0は32日以降のレコード
	 */
	public function optionSubQuery(Model $model, $commodities, $spanCount, $spanCount24) {
		if (empty($commodities)) {
			return array();
		}

		$commodityIds = $model->extract($commodities, '{n}.commodityId');

		if (empty($commodityIds)) {
			return array();
		}

		list($commodityIdsParam, $commodityIdsValue) = $model->createBindArray('commodityIds', $commodityIds);
		$params = $commodityIdsValue;

		// 装備クエリ
		$sql = "
			SELECT
			  commodities.id
			  , equipments.option_category_id AS option_category_id
			  , equipments.name
			  , 0 AS option_flg
			  , 1 AS option_default
			  , 0 AS price
			FROM
			  rentacar.commodities
			  INNER JOIN rentacar.commodity_equipments
				ON commodities.id = commodity_equipments.commodity_id
			  INNER JOIN rentacar.equipments
				ON equipments.id = commodity_equipments.equipment_id
			WHERE
			  commodities.id IN ({$commodityIdsParam})
			  AND commodity_equipments.delete_flg = 0
			  AND equipments.delete_flg = 0
			UNION
		";

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
		$sql .= "
			SELECT
			  commodities.id
			  , `privileges`.option_category_id AS option_category_id
			  , `privileges`.name
			  , `privileges`.option_flg AS option_flg
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
				$price = $price[0];
				
				if ($commodity['commodityId'] == $price['id']) {
					unset($price['id']);
					$commodities[$k]['Option'][] = $price;
				}
			}
		}

		return $commodities;
	}

	/**
	 * 深夜手数料取得サブクエリ
	 */
	public function nightFeeSubQuery(Model $model, $commodities, $fromTime, $returnTime) {
		if (empty($commodities)) {
			return array();
		}

		// 出発と返却両方のIDで検索する
		$officeIds = array_merge(Hash::extract($commodities, '{n}.officeId'), Hash::extract($commodities, '{n}.returnOffice.{n}.id'));

		if (empty($officeIds)) {
			return array();
		}

		$officeIds = array_unique($officeIds);
		sort($officeIds);

		$options = array(
			'fields' => array(
				'Office.id',
				'LateNightFee.target_time_from',
				'LateNightFee.target_time_to',
				'LateNightFee.price',
				'LateNightFee.price_addition_flg',
			),
			'joins' => array(
				array(
					'table' => 'late_night_fees',
					'alias' => 'LateNightFee',
					'type' => 'INNER',
					'conditions' => 'LateNightFee.id = Office.late_night_fee_flg'
				),
			),
			'conditions' => array(
				'Office.id' => $officeIds,
				'Office.delete_flg' => false,
				'LateNightFee.delete_flg' => false,
			),
			'recursive' => -1,
		);
		
		$ret = $model->Office->findC('all', $options);

		if (empty($ret)) {
			return $commodities;
		}
		
		$offices = Hash::combine($ret, '{n}.Office.id', '{n}.LateNightFee');
		
		$fromTime = strtotime($fromTime);
		$returnTime = strtotime($returnTime);
		
		// 深夜手数料を追加する
		foreach ($commodities as $k => $commodity) {
			$officeId = $commodity['officeId'];
			$price = 0;

			// 出発店の設定がある場合は貸出分の計算をする
			if (!empty($offices[$officeId])) {
				$price = $model->getLateNightFee($offices[$officeId], $fromTime);

				// 出発・返却いずれかで1回のみ加算の場合は返却分の計算をスキップする
				if (!empty($offices[$officeId]['price_addition_flg']) && $price > 0) {
					$commodities[$k]['lateNightFee'] = $price;
					continue;
				}
			}

			// 返却店の設定がある場合は返却分の計算をする
			if (!empty($commodity['returnOffice'])) {
				$returnOffice = $commodity['returnOffice'];
				$max_return_price = 0;

				// 全ての返却店の計算をする
				foreach ($returnOffice as $office) {
					$officeId = $office['id'];

					if (empty($offices[$officeId])) {
						continue;
					}
					// 出発・返却ともに加算、または出発が加算されていない場合のみ
					if (empty($offices[$officeId]['price_addition_flg']) || $price == 0) {
						$return_price = $model->getLateNightFee($offices[$officeId], $returnTime);

						if ($return_price > $max_return_price) {
							$max_return_price = $return_price;
						}
					}
				}
				// 全ての返却店から最大の手数料を加算する
				$price += $max_return_price;

			} else if (!empty($offices[$officeId])) {
				// 乗り捨てでない場合は自店舗の返却分の計算をする

				// 出発・返却ともに加算、または出発が加算されていない場合のみ
				if (empty($offices[$officeId]['price_addition_flg']) || $price == 0) {
					$price += $model->getLateNightFee($offices[$officeId], $returnTime);
				}
			}
			
			if ($price > 0) {
				$commodities[$k]['lateNightFee'] = $price;
			}
		}

		return $commodities;
	}

}

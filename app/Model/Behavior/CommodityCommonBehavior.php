<?php
class CommodityCommonBehavior extends ModelBehavior {

	//プラン詳細へのパラメータを生成
	public function createPlanQueryString(Model $model, $params) {

		$keys = array(
			'place',					 // 出発場所の種類
			'return_place',				 // 返却場所の種類
			'from',						 // 出発日
			'to',						 // 返却日
			'area_id',					 // 出発エリア
			'return_area_id',			 // 返却エリア
			'bullet_train_id',			 // 出発駅
			'return_bullet_train_id',	 // 返却駅
			'airport_id',				 // 出発空港
			'return_airport_id',		 // 返却空港
			'adults_count',				 // 大人人数
			'children_count',			 // 子供人数
			'infants_count',			 // 幼児人数
			'time',						 // 出発時間
			'return_time',				 // 返却時間
			'station_id',				 // 出発駅
			'return_station_id',		 // 返却駅
			'office_id',				 // 営業所
			'return_office_id',			 // 返却営業所
			'sales_type',				 // 販売種別,
			'from_rentacar_client',		 // レンタカー会社のサイトからアクセス
		);

		$ret = array();
		foreach($keys as $key) {
			if (!empty($params[$key])) {
				$ret[$key] = $params[$key];
			}
		}

		return http_build_query($ret);
	}

	// 期間の日数算出
	public function getSpanCount(Model $model, $from, $to) {
		$fromDate = preg_split('/[^0-9]/', $from);
		$fromDate = array_filter($fromDate, "strlen");
		$fromDate = array_values($fromDate);

		$toDate = preg_split('/[^0-9]/', $to);
		$toDate = array_filter($toDate, "strlen");
		$toDate = array_values($toDate);

		$diffDate = gmmktime(0, 0, 0, $toDate[1], $toDate[2], $toDate[0]) - gmmktime(0, 0, 0, $fromDate[1], $fromDate[2], $fromDate[0]);

		$dayCount = $diffDate / (60 * 60 * 24) + 1;

		$diffTime = abs(strtotime($to) - strtotime($from));
		$dayCount24 = ceil($diffTime / 3600 / 24);

		return array($dayCount, $dayCount24);
	}

	// 締切時間の算出
	public function calculateDeadline(Model $model, $dateFrom, $datetimeFrom, $hours, $days, $time) {
		if (isset($days) && !empty($time)) {
			// 時刻指定の場合
			$deadline = strtotime($dateFrom . ' ' . $time . ' -' . $days . ' day');
		} else if (isset($hours)) {
			// 時間指定の場合
			$deadline = strtotime($datetimeFrom . ' -' . $hours . ' hour');
		} else {
			return false;
		}
		// 締切時間内の場合タイムスタンプを返す
		return (time() <= $deadline) ? $deadline : false;
	}

	// 営業開始時刻の判定
	public function isOfficeOpenOK(Model $model, $dateFrom, $datetimeFrom, $officeHoursFrom, $officeHoursTo, $hours, $days, $time)
	{
		if (isset($days) && !empty($time)) {
			// 時刻指定の場合は判定対象外
			return true;
		} else if (!isset($hours)) {
			return false;
		}
		// 時間指定の場合
		$now = time();
		$openTime = strtotime($dateFrom . ' ' . $officeHoursFrom);
		if (empty($officeHoursTo)) {
			$closeTime = strtotime($dateFrom . ' -1 day');
		} else if ($officeHoursFrom < $officeHoursTo) {
			$closeTime = strtotime($dateFrom . ' ' . $officeHoursTo . ' -1 day');
		} else {
			$closeTime = strtotime($dateFrom . ' ' . $officeHoursTo);
		}
		if ($closeTime <= $now && $now < $openTime) {
			// 営業時間外に翌日の予約を取る場合、営業開始時刻＋手仕舞い時間以降の予約しか取れない
			// 例）手仕舞い1時間の商品、8時営業開始の営業所の場合、夜12時に予約しようとすると9時からしか取れない
			$startTime = strtotime('+' . $hours . ' hour', $openTime);
			return (strtotime($datetimeFrom) >= $startTime);
		}
		return true;
	}

	// Hash::extractのソート版
	public function extract(Model $model, $data, $path) {
		$arr = Hash::extract($data, $path);
		if (empty($arr)) {
			return array();
		}
		$arr = array_unique($arr);
		sort($arr);
		return $arr;
	}

	/**
	 * 乗車人数を計算する。
	 * (子供、幼児は大人2/3として扱う)
	 *
	 * @param Model $model
	 * @param int $adultCount
	 * @param int $childCount
	 * @param int $infantCount
	 * @return float
	 */
	public function calcPersonCount(Model $model, $adultCount, $childCount = 0, $infantCount = 0) {
		return intval($adultCount) + intval($childCount) + intval($infantCount);
	}
}
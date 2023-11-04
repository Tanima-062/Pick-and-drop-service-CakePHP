<?php
class CommodityMetasearchCommonBehavior extends ModelBehavior {

	// webサーバの商品画像のURLを返す
	public function getImageUrl(Model $model) {
		return 'https://' . ((IS_PRODUCTION) ? 'skyticket.jp' : 'jp.skyticket.jp') . '/rentacar/img/commodity_reference/';
	}
	
	// webサーバのプラン詳細のURLを返す
	public function getPlanUrl(Model $model, $commodityItemId) {
		if (empty($commodityItemId) || $model->_planQueryString == '') {
			return '';
		}
		
		$domain = 'https://' . ((IS_PRODUCTION) ? 'skyticket.jp' : 'jp.skyticket.jp');
		return $domain . '/rentacar/plan/' . $commodityItemId . '/?' . $model->_planQueryString;
	}
	
	// 連結した車種名の取得
	public function getCarModelName(Model $model, $glue = '', $carModel) {
		if (empty($carModel)) {
			return '';
		}
		
		return implode($glue, Hash::extract($carModel, '{n}.name'));
	}
	
	// 店舗の合計数を返す
	public function getTotalShopCount(Model $model) {
		if (!empty($model->_commodities)) {
			return 0;
		}
		
		return count(array_unique(array_column($model->_commodities, 'officeId')));
	}

	/**
	 * タイムスタンプが対象時間内であれば深夜手数料を返す
	 */
	public function getLateNightFee(Model $model, $office, $time) {
		$from = strtotime($office['target_time_from']);
		$to = strtotime($office['target_time_to']);
		$price = 0;

		if (($from < $to && $from <= $time && $to >= $time) ||
			($from > $to && ($from <= $time || $to >= $time))) {
			$price = $office['price'];
		}
		
		return $price;
	}

}
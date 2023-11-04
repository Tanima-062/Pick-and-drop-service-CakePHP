<?php
class SearchesApiCommodityBehavior extends ModelBehavior {

	/**
	 * 車両タイプ、車種からプラン名を生成する
	 *
	 * @param Model $model
	 * @param array $carType 車両タイプ
	 * @param array $carModels 車種
	 * @param boolean $modelSelect 車種指定
	 * @return string
	 */
	public function createPlanName(Model $model, $carType, $carModels, $modelSelect = false) {
		$carModel = '';

		$carModeLists = Hash::extract($carModels, '{n}.name');
		if(!empty($carModeLists)) {
			$carModel = implode($carModeLists,'・');
		}

		$planName = $carType['name'] .'（'. $carModel;
		$planName .= ($modelSelect) ? '）' : '他）';

		return $planName;
	}

}
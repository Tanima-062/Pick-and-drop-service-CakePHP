<?php
App::uses('Commodity', 'Model');
App::uses('Equipment', 'Model');

/**
 * Class PlanApiCommodity
 */
final class PlanApiCommodity extends Commodity {

	/**
	 * 使用テーブル名
	 * @var string
	 */
	public $useTable = 'commodities';

	/**
	 * エイリアス名
	 * @var string
	 */
	public $alias = 'Commodity';

	/**
	 * paginateをオーバーライド
	 * ページャー処理は$this->_commoditiesを使ってphp側で制御するのでクエリでは処理しない。
	 * また、クエリの結果はcommodityのみを取得しているので、$this->_commoditiesから必要な項目を追加している。
	 *
	 * @param $conditions
	 * @param $fields
	 * @param $order
	 * @param $limit
	 * @param int $page
	 * @param null $recursive
	 * @param array $extra
	 * @return array
	 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {

		// 商品一覧初期値
		$plan_list = array();

		// 取得した商品情報の存在チェック
		if (empty($this->_commodities)) {
			return $plan_list;
		}

		// 商品情報詳細データ取得
		$ret = $this->findC('all', array(
			'conditions' => $conditions,
			'fields' => array(
				'Commodity.id',
				'Commodity.image_relative_url',
				'Commodity.client_id',
				'Commodity.transmission_flg',
				'Commodity.smoking_flg',
				'Commodity.sales_type',
				'CommodityItem.sipp_code',
				'Client.name',
				'Client.url',
			),
			'joins' => array(
				array(
					"table" => "commodity_items",
					"alias" => "CommodityItem",
					'type' => 'inner',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					),
				),
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'inner',
					'conditions' => array(
						'Commodity.client_id = Client.id'
					),
				),
			),
			'recursive' => -1,
		));

		// 対象の営業所を取得するための必要パラメータを設定
		$place      = $extra['request']['place'];
		$area_id    = isset($extra['request']['area_id']) ? $extra['request']['area_id'] : NULL;
		$airport_id = isset($extra['request']['airport_id']) ? $extra['request']['airport_id'] : NULL;
		$station_id = isset($extra['request']['station_id']) ? $extra['request']['station_id'] : NULL;
		$dateFrom = sprintf('%s-%s-%s', $extra['request']['year'], $extra['request']['month'], $extra['request']['day']);
		$datetimeFrom = sprintf('%s %s', $dateFrom, $extra['request']['time']);
		$dateTo = sprintf('%s-%s-%s', $extra['request']['return_year'], $extra['request']['return_month'], $extra['request']['return_day']);
		$datetimeTo = sprintf('%s %s', $dateTo, $extra['request']['return_time']);

		// 貸出店舗に返却する場合は同じパラメータを設定
		if ($extra['request']['return_way'] == 0) {
			$r_place      = $place;
			$r_area_id    = $area_id;
			$r_airport_id = $airport_id;
			$r_station_id = $station_id;
		} else {
			$r_place      = $extra['request']['return_place'];
			$r_area_id    = isset($extra['request']['return_area_id']) ? $extra['request']['return_area_id'] : NULL;
			$r_airport_id = isset($extra['request']['return_airport_id']) ? $extra['request']['return_airport_id'] : NULL;
			$r_station_id = isset($extra['request']['return_station_id']) ? $extra['request']['return_station_id'] : NULL;
		}

		// 車種取得
		$c_item        = new CommodityItem();
		$car_info_list = $c_item->getCarInfo($this->extract($this->_commodities, '{n}.commodityItemId'));

		// 受取営業所取得
		$model = new CommodityRentOffice();
		if ($place == 1) {
			$rent_office_list = $model->getRentOfficeListByPlaceAndId($this->_commodityIds, $area_id, 1, $dateFrom, $datetimeFrom, $dateTo, false, true);
		} else if ($place == 3) {
			$rent_office_list = $model->getRentOfficeListByPlaceAndId($this->_commodityIds, $airport_id, 3, $dateFrom, $datetimeFrom, $dateTo, false, true);
		} else if ($place == 4) {
			$rent_office_list = $model->getRentOfficeListByPlaceAndId($this->_commodityIds, $station_id, 4, $dateFrom, $datetimeFrom, $dateTo, false, true);
		}

		// 返却営業所取得
		$model = new CommodityReturnOffice();
		if ($r_place == 1) {
			$return_office_list = $model->getReturnOfficeListByPlaceAndId($this->_commodityIds, $r_area_id, 1, $dateTo, $datetimeTo);
		} else if ($r_place == 3) {
			$return_office_list = $model->getReturnOfficeListByPlaceAndId($this->_commodityIds, $r_airport_id, 3, $dateTo, $datetimeTo);
		} else if ($r_place == 4) {
			$return_office_list = $model->getReturnOfficeListByPlaceAndId($this->_commodityIds, $r_station_id, 4, $dateTo, $datetimeTo);
		}

		// 装備取得
		$equipment_list = $this->getEquipmentListByCommodityId($this->_commodityIds);

		// オプションカテゴリ取得
		$option_list = $this->getPrivilegeListByCommodityId($this->_commodityIds);

		// レビュー取得
		$yotpoReview = new YotpoReview();
		$ratings     = $yotpoReview->getRatingsGroupByClientId();

		foreach ($this->_commodities as $commodity) {
			foreach ($ret as $key => $value) {

				// IDが一致しない場合、次へ
				if ($value['Commodity']['id'] !== $commodity['commodityId']) {
					continue;
				}

				// 車種の特定
				$carInfo = $car_info_list[$commodity['commodityItemId']];

				// 車種指定
				$model_select = boolval($commodity['carModelId']);

				// プランイメージ
				$imageRelativeUrl = !empty($value['Commodity']['image_relative_url'])
					? "/img/commodity_reference/{$value['Commodity']['client_id']}/{$value['Commodity']['image_relative_url']}"
					: '/img/noimage.png';

				// 受取営業所
				$shops = array();
				if (isset($rent_office_list[$value['Commodity']['id']])) {
					foreach ($rent_office_list[$value['Commodity']['id']] as $rent_office) {
						$shops[] = $rent_office;
					}
				}

				// 返却営業所
				$return_shops = array();
				if (isset($return_office_list[$value['Commodity']['id']])) {
					foreach ($return_office_list[$value['Commodity']['id']] as $return_office) {
						$return_shops[] = $return_office;
					}
				}

				// 装備一覧
				$equipments = array();
				if (isset($equipment_list[$value['Commodity']['id']])) {
					foreach ($equipment_list[$value['Commodity']['id']] as $equipment) {
						$equipments[] = array(
							'equipmentName'    => $equipment['name'],
							'description'      => trim($equipment['description']),
							'optionCategoryId' => (int)$equipment['option_category_id'],
						);
					}
				}

				// 装備一覧にAT車追加
				if (empty($value['Commodity']['transmission_flg'])) {
					$equipments[] = array(
						'equipmentName'    => 'AT車',
						'description'      => 'オートマチックトランスミッションの車です',
						'optionCategoryId' => 0,
					);
				}

				// オプション一覧
				$options = array();
				if (isset($option_list[$value['Commodity']['id']])) {
					foreach ($option_list[$value['Commodity']['id']] as $option) {
						// シートとその他で分ける
						$ctg = empty($option['option_flg']) ? 'others' : 'sheets';

						$options[$ctg][] = array(
							'optionId'         => intval($option['id']),
							'optionName'       => $option['name'],
							'optionCategoryId' => intval($option['option_category_id']),
						);
					}
				}

				// 基本料金＝基本料金＋免責料金＋深夜料金＋乗り捨て料金
				$base_price = $commodity['price'] + $commodity['minDropPrice'];
				if (!empty($commodity['lateNightFee'])) {
					$base_price += $commodity['lateNightFee'];
				}

				//上乗せ率
				$addition_rate = Constant::ADDITIONAL_RATE;

				//販売価格計算
				$sales_price = floor($base_price * $addition_rate);

				//販売価格切り上げ処理
				$roundup_sales_price = ceil($sales_price/10)*10;

				// レビュー
				$rating = array();
				if ($ratings[$value['Commodity']['client_id']]) {
					$rating = $ratings[$value['Commodity']['client_id']];
				}

				//販売種別
				$sales_type = $extra['request']['sales_type'];

				//販売種別によるレスポンス分岐処理
				if (isset($sales_type) && $value['Commodity']['sales_type'] !== $sales_type){

				} else {

					// 商品一覧に追加
						$plan_list[$value['Commodity']['id']] = array(
							'planId'      => intval($commodity['commodityItemId']),
							'planName'    => $this->createPlanName($carInfo['CarType'], $carInfo['CarModel'], $model_select),
							'planImage'   => $imageRelativeUrl,
							'clientId'    => intval($value['Commodity']['client_id']),
							'clientName'  => $value['Client']['name'],
							'clientUrl'   => $value['Client']['url'],
							'carTypeId'   => intval($carInfo['CarType']['id']),
							'sippCode'    => $value['CommodityItem']['sipp_code'],
							'shops'       => $shops,
							'returnShops' => $return_shops,
							'equipments'  => $equipments,
							'options'     => $options,
							'smoking'     => boolval($value['Commodity']['smoking_flg']),
							'capacity'    => intval($carInfo['CarModel'][0]['capacity']),
							'baggage'     => intval($carInfo['CarModel'][0]['trunk_space']),
							'modelSelect' => $model_select,
							'newCar'      => ($commodity['new_car_registration'] == 1 || $commodity['new_car_registration'] == 2),
							'payment'     => boolval($commodity['payment_method']),
							'basePrice'   => $base_price,
							'salesPrice'  => $roundup_sales_price,
							'currency'    => Configure::read('currency'),
							'stockCount'  => intval($commodity['numberRemaining']),
							'reviewScore' => isset($rating['rating']) ? (double)number_format(floatval($rating['rating']), 1, '.', '') : null,
							'reviewCount' => isset($rating['count']) ? intval($rating['count']) : 0,
							'recommended' => $commodity['recommended'],
						);
				}

				// 処理軽減
				unset($ret[$key]);
			}
		}

		return array_values($plan_list);
	}

	/**
	 * 車両タイプ、車種からプラン名を生成する
	 *
	 * @param array   $carType     車両タイプ
	 * @param array   $carModels   車種
	 * @param boolean $modelSelect 車種指定
	 * @return string
	 */
	public function createPlanName($carType, $carModels, $modelSelect = false) {

		$carModelLists = Hash::extract($carModels, '{n}.name');
		$carModel = empty($carModelLists) ? '' : implode($carModelLists,'・');
		return "{$carType['name']}（{$carModel}" . ($modelSelect ? '）' : '他）');
	}

	/**
	 * 商品ID別に受取営業所一覧を取得する
	 *
	 * @param int|array $commodity_id 商品ID
	 * @param int       $place        出発場所タイプ
	 * @param int|null  $area_id      エリアID
	 * @param int|null  $airport_id   空港ID
	 * @param int|null  $station_id   駅ID
	 * @return array
	 */
	private function getRentOfficeListByCommodityId($commodity_id, $place, $area_id, $airport_id, $station_id) {

		// Query
		$query = array(
			'fields' => array(
				'CommodityRentOffice.commodity_id',
				'Office.id',
				'Office.name',
				'Office.office_hours_from',
				'Office.office_hours_to',
				'Office.tel',
				'Office.address',
				'Office.access_dynamic',
				'OfficeSupplement.nearest_transport',
				'OfficeSupplement.method_of_transport',
				'OfficeSupplement.required_transport_time',
			),
			'joins' => array(
				array(
					'table' => 'office_supplements',
					'alias' => 'OfficeSupplement',
					'type'  => 'inner',
					'conditions' => array(
						'Office.id = OfficeSupplement.office_id',
						'OfficeSupplement.delete_flg = 0',
					),
				),
			),
			'conditions' => array(),
		);

		// 選択した出発場所のタイプによる設定
		switch ($place) {
			// 空港の場合
			case 3:
				$query['conditions']['Office.airport_id'] = $airport_id;
				break;

			// 駅の場合
			case 4:
				$query['joins'][] = array(
					'table' => 'office_stations',
					'alias' => 'OfficeStation',
					'type'  => 'inner',
					'conditions' => array(
						'Office.id = OfficeStation.office_id',
						'OfficeStation.delete_flg = 0',
					),
				);

				$query['conditions']['OfficeStation.station_id'] = $station_id;
				break;

			// 上記以外（都道府県）の場合
			default:
				$query['conditions']['Office.area_id'] = $area_id;
				break;
		}

		$model = new CommodityRentOffice();
		$list  = $model->getRentOfficeListByCommodityId($commodity_id, $query);

		$ret = array();
		foreach ($list as $value) {
			$ret[$value['CommodityRentOffice']['commodity_id']][] = $value['Office'] + $value['OfficeSupplement'];
		}

		return $ret;
	}

	/**
	 * 商品ID別に返却営業所一覧を取得する
	 *
	 * @param int|array $commodity_id 商品ID
	 * @param int       $place        出発場所タイプ
	 * @param int|null  $area_id      エリアID
	 * @param int|null  $airport_id   空港ID
	 * @param int|null  $station_id   駅ID
	 * @return array
	 */
	private function getReturnOfficeListByCommodityId($commodity_id, $place, $area_id, $airport_id, $station_id) {

		// Query
		$query = array(
			'fields' => array(
				'CommodityReturnOffice.commodity_id',
				'Office.id',
				'Office.name',
				'Office.office_hours_from',
				'Office.office_hours_to',
				'Office.tel',
				'Office.address',
				'Office.access_dynamic',
				'OfficeSupplement.nearest_transport',
				'OfficeSupplement.method_of_transport',
				'OfficeSupplement.required_transport_time',
			),
			'joins' => array(
				array(
					'table' => 'office_supplements',
					'alias' => 'OfficeSupplement',
					'type'  => 'inner',
					'conditions' => array(
						'Office.id = OfficeSupplement.office_id',
						'OfficeSupplement.delete_flg = 0',
					),
				),
			),
			'conditions' => array(),
		);

		// 選択した出発場所のタイプによる設定
		switch ($place) {
			// 空港の場合
			case 3:
				$query['conditions']['Office.airport_id'] = $airport_id;
				break;

			// 駅の場合
			case 4:
				$query['joins'][] = array(
					'table' => 'office_stations',
					'alias' => 'OfficeStation',
					'type'  => 'inner',
					'conditions' => array(
						'Office.id = OfficeStation.office_id',
						'OfficeStation.delete_flg = 0',
					),
				);

				$query['conditions']['OfficeStation.station_id'] = $station_id;
				break;

			// 上記以外（都道府県）の場合
			default:
				$query['conditions']['Office.area_id'] = $area_id;
				break;
		}

		$model = new CommodityReturnOffice();
		$list  = $model->getReturnOfficeListByCommodityId($commodity_id, $query);

		$ret = array();
		foreach ($list as $value) {
			$ret[$value['CommodityReturnOffice']['commodity_id']][] = $value['Office'] + $value['OfficeSupplement'];
		}

		return $ret;
	}

	/**
	 * 商品ID別に装備一覧を取得
	 *
	 * @param array $commodity_ids
	 * @return array
	 */
	private function getEquipmentListByCommodityId($commodity_ids) {

		$model = new CommodityEquipment();

		$list = $model->getEquipmentListByCommodityId($commodity_ids, array(
			'CommodityEquipment.commodity_id',
			'Equipment.option_category_id',
			'Equipment.name',
			'Equipment.description',
		));

		$ret = array();
		foreach ($list as $value) {
			$ret[$value['CommodityEquipment']['commodity_id']][] = $value['Equipment'];
		}

		return $ret;
	}

	/**
	 * 商品ID別にオプションカテゴリを取得する
	 *
	 * @param array $commodity_ids
	 * @return array
	 */
	private function getPrivilegeListByCommodityId($commodity_ids) {

		$model = new CommodityPrivilege();

		$list = $model->getPrivilegeListByCommodityId($commodity_ids, array(
			'CommodityPrivilege.commodity_id',
			'Privilege.id',
			'Privilege.option_category_id',
			'Privilege.name',
			'Privilege.option_flg',
		));

		$ret = array();
		foreach ($list as $value) {
			$ret[$value['CommodityPrivilege']['commodity_id']][] = $value['Privilege'];
		}

		return $ret;
	}

}

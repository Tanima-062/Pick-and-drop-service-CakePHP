<?php

App::uses('AppModel', 'Model');
App::uses('Campaign', 'Model');

class CommodityItem extends AppModel {

	protected $cacheConfig = '1hour';

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Commodity' => array(
			'className' => 'Commodity',
			'foreignKey' => 'commodity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CarClass' => array(
			'className' => 'CarClass',
			'foreignKey' => 'car_class_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'CommodityPriceTemplate' => array(
			'className' => 'CommodityPriceTemplate',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CommodityPrice' => array(
			'className' => 'CommodityPrice',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CommodityRanking' => array(
			'className' => 'CommodityRanking',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CommodityReservation' => array(
			'className' => 'CommodityReservation',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'CommodityStock' => array(
			'className' => 'CommodityStock',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Contract' => array(
			'className' => 'Contract',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Estimate' => array(
			'className' => 'Estimate',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Reservation' => array(
			'className' => 'Reservation',
			'foreignKey' => 'commodity_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function getOfficeStocks($carClassId, $reservation) {
		$stocks = array();

		//車種ID画ない場合
		if (empty($carClassId)) {
			return $stocks;
		}

		//予約情報がない場合
		if (empty($reservation)) {
			return $stocks;
		} else {
			if (!isset($reservation['from_office'])) {
				return $stocks;
			}
		}

		$officeIds = $reservation['from_office'];

		$officeStockGroup = $this->getOfficeStockGroup($officeIds);

		//在庫グループ取得できない場合
		if (empty($officeStockGroup)) {
			return $stocks;
		}

		$span = $this->dateSpanArray($reservation['from'], $reservation['to']);

		$stockGroupIds = array_unique(array_values($officeStockGroup));

		list($stockGroupIdsParam, $stockGroupIdsValue) = $this->createBindArray('stockGroupIds', $stockGroupIds);

		$params = array(
			'from'			 => substr($reservation['from'], 0, 10),
			'to'			 => substr($reservation['to'], 0, 10),
			'carClassId'	 => $carClassId,
			'carsCount'		 => $reservation['cars_count'],
		);

		$params += $stockGroupIdsValue;

		$result = $this->query("
			SELECT
				ccs.stock_group_id
				, MIN(
					ccs.stock_count - COALESCE(ccr.reservation_count, 0)
				) AS stock
				, COUNT(
					ccs.stock_date
				) AS date_count
			FROM
				rentacar.car_class_stocks AS ccs
				LEFT JOIN (
					SELECT
					stock_group_id
					, car_class_id
					, stock_date
					, SUM(reservation_count) AS reservation_count
					FROM
						rentacar.car_class_reservations
				WHERE
					delete_flg = 0
					AND stock_date >= :from
					AND stock_date <= :to
					AND car_class_id = :carClassId
					AND stock_group_id IN ({$stockGroupIdsParam})
				GROUP BY
					stock_group_id
					, car_class_id
					, stock_date
				) AS ccr
				ON ccs.stock_group_id = ccr.stock_group_id
				AND ccs.car_class_id = ccr.car_class_id
				AND ccs.stock_date = ccr.stock_date
			WHERE
				ccs.stock_date >= :from
				AND ccs.stock_date <= :to
				AND ccs.car_class_id = :carClassId
				AND ccs.stock_group_id IN ({$stockGroupIdsParam})
				AND ccs.suspension = 0
			GROUP BY
				ccs.stock_group_id", $params, false);

		if (empty($result)) {
			return $stocks;
		}

		$result = Hash::combine($result, '{n}.ccs.stock_group_id', '{n}');

		if (empty($result)) {
			return $stocks;
		}

		foreach ($officeStockGroup as $officeId => $stockGroupId) {
			if ($result[$stockGroupId][0]['date_count'] < count($span)) {
				// 日数分の在庫レコードが必要
				continue;
			}
			// レコードが存在する場合は残数を取得
			$stock = (isset($result[$stockGroupId]) && $result[$stockGroupId][0]['stock'] > 0) ? (int)$result[$stockGroupId][0]['stock'] : 0;
			$stocks[$officeId] = $stock;
		}

		return $stocks;
	}

	public function dateSpanArray($from, $to) {

		$dateFrom = substr($from, 0, 10);
		$from = strtotime(substr($from, 0, 10));
		$to = strtotime(substr($to, 0, 10));

		$spanArray = array($dateFrom);
		for ($i = $dateFrom; $from < $to;) {
			$dateFrom = date("Y-m-d", strtotime($dateFrom . " +1 day"));
			array_push($spanArray, $dateFrom);
			$from = strtotime($dateFrom);
		}

		return $spanArray;
	}

	public function getOfficeStockGroup($officeIds) {
		list($officeIdsParam, $officeIdsValue) = $this->createBindArray('officeIds', $officeIds);

		$officeStockGroup = $this->query("
			SELECT
				office_stock_groups.stock_group_id,
				office_stock_groups.office_id
			FROM
				office_stock_groups
			WHERE
				delete_flg = 0
				AND office_id IN ({$officeIdsParam})", $officeIdsValue, false);

		if (empty($officeStockGroup)) {
			return array();
		}

		return Hash::combine($officeStockGroup, '{n}.office_stock_groups.office_id', '{n}.office_stock_groups.stock_group_id');
	}

	/**
	 * プラン詳細他で使用
	 * @param unknown $commodityItemId
	 */
	public function getCommodityItemPriceData($commodityItemId, $dateFrom) {
		$Campaign = ClassRegistry::init('Campaign');
		$Campaign->setDataSource($this->getDataSource()->configKeyName);
		$campaignIds = $Campaign->getCampaignIds(true, $commodityItemId, $dateFrom);

		// キャンペーン対象か否かで料金の参照テーブルを変える
		if (empty($campaignIds)) {
			$table = 'commodity_prices';
			$alias = 'CommodityPrice';
		} else {
			$table = 'commodity_campaign_prices';
			$alias = 'CommodityCampaignPrice';
		}
		$join = array(
			'table' => "{$table}",
			'alias' => "{$alias}",
			'type' => 'INNER',
			'conditions' => array(
				"{$alias}.commodity_item_id = CommodityItem.id"
			),
		);
		if (!empty($campaignIds)) {
			$join['conditions']["{$alias}.campaign_id"] = $campaignIds[0]['CampaignTerm']['campaign_id'];
		}

		$options = array(
			'fields' => array(
				'CommodityItem.id',
				'CommodityItem.client_id',
				'CommodityItem.commodity_id',
				'CommodityItem.car_class_id',
				'CommodityItem.car_model_id',
				'CommodityItem.sipp_code',
				'CommodityItem.need_commodity_stocks',
				"{$alias}.id",
				"{$alias}.client_id",
				"{$alias}.span_count",
				"{$alias}.price",
				"{$alias}.commodity_item_id",
				'CarClass.id',
				'CarClass.name',
				'CarClass.car_type_id',
				'CarType.id',
				'CarType.name',
				'CarType.capacity',
				'ClientCarModel.*',
				'CarModel.*',
				'ClientCarModelSort.sort',
			),
			'joins' => array(
				$join,
				array(
					'table' => 'commodities',
					'alias' => 'Commodity',
					'type' => 'INNER',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					),
				),
				array(
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'type' => 'INNER',
					'conditions' => array(
						'CarClass.client_id = CommodityItem.client_id',
						'CarClass.id = CommodityItem.car_class_id'
					),
				),
				array(
					'table' => 'car_types',
					'alias' => 'CarType',
					'type' => 'INNER',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id'
					),
				),
				array(
					'table' => 'client_car_models',
					'alias' => 'ClientCarModel',
					'type' => 'INNER',
					'conditions' => array(
						'ClientCarModel.client_id = CommodityItem.client_id',
						'ClientCarModel.car_class_id = CarClass.id'
					),
				),
				array(
					'table' => 'car_models',
					'alias' => 'CarModel',
					'type' => 'INNER',
					'conditions' => array(
						'CarModel.id = ClientCarModel.car_model_id'
					),
				),
				array(
					'table' => 'client_car_model_sorts',
					'alias' => 'ClientCarModelSort',
					'type' => 'LEFT',
					'conditions' => array(
						'ClientCarModelSort.client_id = CommodityItem.client_id',
						'ClientCarModelSort.car_model_id = CarModel.id',
						'ClientCarModelSort.delete_flg' => 0
					),
				),
			),
			'conditions' => array(
				'CommodityItem.id' => $commodityItemId,
				'CommodityItem.delete_flg' => 0,
				'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
				"{$alias}.delete_flg" => 0,
				'CarClass.delete_flg' => 0,
				'CarType.delete_flg' => 0,
				'ClientCarModel.delete_flg' => 0,
				'CarModel.delete_flg' => 0,
			),
			'order' => array(
				'CarModel.capacity',
				'ClientCarModelSort.sort',
			),
			'recursive' => -1,
		);
		$results = $this->find('all', $options);
		$result = array();
		foreach ($results as $key => $value) {
			$result['CommodityItem'] = $value['CommodityItem'];
			$result['CarClass'] = $value['CarClass'];
			$result['CarType'] = $value['CarType'];
			$result['CarModel'][$value['CarModel']['id']] = $value['CarModel'];
			// キャンペーン対象の場合も、返却データのキーは CommodityPrice（後続処理は共通にしたいので）
			$result['CommodityPrice'][$value[$alias]['span_count']] = $value[$alias];
		}
		$tmp = array();
		$uniqueArray = array();

		if (!empty($results)) {
			foreach ($result['CarModel'] as $carModel) {
				if (!in_array($carModel['name'], $tmp)) {
					$tmp[] = $carModel['name'];
					$uniqueArray[] = $carModel;
				}
			}
			$result['CarModel'] = $uniqueArray;
		}
		return $result;
	}

	/**
	 * 募集型在庫用
	 * @param $commodityItemId
	 * @param $dateFrom
	 * @return array
	 */
	public function getCommodityItemPriceDataAgentOrganized($commodityItemId, $dateFrom) {
		$table = 'agent_organized_prices';
		$alias = 'CommodityPrice';
		$join = array(
			'table' => "{$table}",
			'alias' => "{$alias}",
			'type' => 'INNER',
			'conditions' => [
				"{$alias}.commodity_item_id = CommodityItem.id",
				"{$alias}.start_date <= '{$dateFrom}'",
				"{$alias}.end_date >= '{$dateFrom}'",
			],
		);

		$options = array(
			'fields' => array(
				'CommodityItem.id',
				'CommodityItem.client_id',
				'CommodityItem.commodity_id',
				'CommodityItem.car_class_id',
				'CommodityItem.car_model_id',
				'CommodityItem.sipp_code',
				'CommodityItem.need_commodity_stocks',
				"{$alias}.id",
				"{$alias}.client_id",
				"{$alias}.price_stay_1",
				"{$alias}.price_stay_2",
				"{$alias}.price_stay_3",
				"{$alias}.price_stay_over",
				"{$alias}.commodity_item_id",
				'CarClass.id',
				'CarClass.name',
				'CarClass.car_type_id',
				'CarType.id',
				'CarType.name',
				'CarType.capacity',
				'ClientCarModel.*',
				'CarModel.*',
				'ClientCarModelSort.sort',
			),
			'joins' => array(
				$join,
				array(
					'table' => 'commodities',
					'alias' => 'Commodity',
					'type' => 'INNER',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					),
				),
				array(
					'table' => 'car_classes',
					'alias' => 'CarClass',
					'type' => 'INNER',
					'conditions' => array(
						'CarClass.client_id = CommodityItem.client_id',
						'CarClass.id = CommodityItem.car_class_id'
					),
				),
				array(
					'table' => 'car_types',
					'alias' => 'CarType',
					'type' => 'INNER',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id'
					),
				),
				array(
					'table' => 'client_car_models',
					'alias' => 'ClientCarModel',
					'type' => 'INNER',
					'conditions' => array(
						'ClientCarModel.client_id = CommodityItem.client_id',
						'ClientCarModel.car_class_id = CarClass.id'
					),
				),
				array(
					'table' => 'car_models',
					'alias' => 'CarModel',
					'type' => 'INNER',
					'conditions' => array(
						'CarModel.id = ClientCarModel.car_model_id'
					),
				),
				array(
					'table' => 'client_car_model_sorts',
					'alias' => 'ClientCarModelSort',
					'type' => 'LEFT',
					'conditions' => array(
						'ClientCarModelSort.client_id = CommodityItem.client_id',
						'ClientCarModelSort.car_model_id = CarModel.id',
						'ClientCarModelSort.delete_flg' => 0
					),
				),
			),
			'conditions' => array(
				'CommodityItem.id' => $commodityItemId,
				'CommodityItem.delete_flg' => 0,
				'Commodity.sales_type' => Constant::SALES_TYPE_AGENT_ORGANIZED,
				"{$alias}.delete_flg" => 0,
				'CarClass.delete_flg' => 0,
				'CarType.delete_flg' => 0,
				'ClientCarModel.delete_flg' => 0,
				'CarModel.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$results = $this->find('all', $options);
		$result = array();
		foreach ($results as $key => $value) {
			$result['CommodityItem'] = $value['CommodityItem'];
			$result['CarClass'] = $value['CarClass'];
			$result['CarType'] = $value['CarType'];
			$result['CarModel'][$value['CarModel']['id']] = $value['CarModel'];
			$result['CarModel'][$value['CarModel']['id']]['sort'] = $value['ClientCarModelSort']['sort'];
			$result[$alias] = $value[$alias];
		}
		$tmp = array();
		$uniqueArray = array();

		if (!empty($results)) {
			foreach ($result['CarModel'] as $carModel) {
				if (!in_array($carModel['name'], $tmp)) {
					$tmp[] = $carModel['name'];
					$uniqueArray[] = $carModel;
				}
			}
			$result['CarModel'] = $uniqueArray;
			$result['CarModel'] = Hash::sort($result['CarModel'], '{n}.sort', 'asc');
		}
		return $result;
	}

	//車種とクラス、参考モデルを取得
	public function getCarInfo($commodityItemId, $isIgnoreDeleteCarClass = false) {

		$conditions = array(
			'conditions' => array(
				'CommodityItem.id' => $commodityItemId,
				'CarType.delete_flg' => 0,
				'ClientCarModels.delete_flg' => 0,
				'CarModel.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'CarClass',
					'table' => 'car_classes',
					'conditions' => 'CarClass.id = CommodityItem.car_class_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarType',
					'table' => 'car_types',
					'conditions' => 'CarType.id = CarClass.car_type_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'ClientCarModels',
					'table' => 'client_car_models',
					'conditions' => array(
						'CarClass.id = ClientCarModels.car_class_id',
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'CarModel',
					'table' => 'car_models',
					'conditions' => 'CarModel.id = ClientCarModels.car_model_id',
				),
				array(
					'type' => 'LEFT',
					'alias' => 'ClientCarModelSort',
					'table' => 'client_car_model_sorts',
					'conditions' => array(
						'CommodityItem.client_id = ClientCarModelSort.client_id',
						'CarModel.id = ClientCarModelSort.car_model_id',
					)
				)
			),
			'fields' => array(
				'CommodityItem.id',
				'CarType.id',
				'CarType.name',
				'CarType.travelko_id',
				'CarModel.id',
				'CarModel.name',
				'CarModel.trunk_space',
				'CarModel.capacity',
				'CarModel.package_num',
				'CarModel.recommended_capacity',
				'CarModel.door',
			),
			'group' => 'CommodityItem.id,CarModel.name',
			'order' => array(
				'CarModel.capacity',
				'ClientCarModelSort.sort',
			),
			'recursive' => -1
		);
		
		if (empty($isIgnoreDeleteCarClass)) {
			$conditions['conditions']['CarClass.delete_flg'] = 0;
		}

		
		$carInfoArray = $this->findC('all', $conditions);

		$carInfos = array();
		foreach ($carInfoArray as $val) {
			$commodityItemkey = $val['CommodityItem']['id'];

			$carInfos[$commodityItemkey]['CarType'] = $val['CarType'];
			$carInfos[$commodityItemkey]['CarModel'][] = $val['CarModel'];
		}

		return $carInfos;
	}

	public function getCommodityItemRelations($commodityItemId) {
		return  $this->findC('first', array(
			'fields' => array(
				'CarClass.id',
				'CarClass.name',
				'CarModel.id',
				'CarModel.name',
				'Commodity.id',
				'Commodity.name',
				'Commodity.sales_type',
			),
			'joins' => array(
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
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'car_models',
					'alias' => 'CarModel',
					'conditions' => array(
						'CarModel.id = CommodityItem.car_model_id',
					),
				),
			),
			'conditions' => array(
				'CommodityItem.id' => $commodityItemId,
			),
			'recursive' => -1,
		));
	}
}

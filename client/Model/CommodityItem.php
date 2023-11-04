<?php
App::uses('AppModel', 'Model');
/**
 * CommodityItem Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property CarClass $CarClass
 * @property Staff $Staff
 * @property CommodityItemReservation $CommodityItemReservation
 * @property CommodityItemStock $CommodityItemStock
 * @property CommodityPriceTemplate $CommodityPriceTemplate
 * @property CommodityPrice $CommodityPrice
 * @property CommodityRanking $CommodityRanking
 * @property Contract $Contract
 * @property Estimate $Estimate
 * @property Reservation $Reservation
 */
class CommodityItem extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'commodity_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'car_class_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => '車両クラスを選択してください',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
		'CommodityItemReservation' => array(
			'className' => 'CommodityItemReservation',
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
		'CommodityItemStock' => array(
			'className' => 'CommodityItemStock',
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

	public function getCarClassList($clientId, $needStock) {

		$options = array(
				'fields' => array(
						'CarClass.id',
						'CarClass.name'
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'CarClass',
								'table' => 'car_classes',
								'conditions' => array(
										'CommodityItem.car_class_id = CarClass.id'
								)
				),),
				'conditions' => array(
						'CommodityItem.client_id' => $clientId,
						'CommodityItem.delete_flg' => 0,
						'CommodityItem.need_commodity_stocks' => $needStock
				),
				'recursive' => -1
		);

		$carClasses = $this->find('all',$options);
		$carClassLists = array();
		foreach ($carClasses as $val) {
			$carClassLists[$val['CarClass']['id']] = $val['CarClass']['name'];
		}

		return $carClassLists;
	}

	public function getCommodityLists($clientId, $needStock) {

		$options = array(
				'fields' => array(
						'Commodity.id',
						'Commodity.name'
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'Commodity',
								'table' => 'commodities',
								'conditions' => array(
										'CommodityItem.commodity_id = Commodity.id'
								)
				),),
				'conditions' => array(
						'CommodityItem.client_id' => $clientId,
						'CommodityItem.delete_flg' => 0,
						'Commodity.delete_flg' => 0,
						'CommodityItem.need_commodity_stocks' => $needStock
				),
				'group' => 'Commodity.id',
				'recursive' => -1
		);

		$commodities = $this->find('all',$options);

		$commodityLists = array();
		foreach ($commodities as $val) {
			$commodityLists[$val['Commodity']['id']] = $val['Commodity']['name'];
		}

		return $commodityLists;
	}

	public function getCommodityItemByCommodityId($commodityId, $needStock) {

		$options = array(
				'fields' => array(
						'CommodityItem.*',
						'Commodity.name',
						'CarClass.name',
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'Commodity',
								'table' => 'commodities',
								'conditions' => array(
										'CommodityItem.commodity_id = Commodity.id',
										'Commodity.delete_flg = 0'
								)
						),
						array(
								'type' => 'INNER',
								'alias' => 'CarClass',
								'table' => 'car_classes',
								'conditions' => array(
										'CommodityItem.car_class_id = CarClass.id',
										'CarClass.delete_flg = 0'
								)
						)),
				'conditions' => array(
						'CommodityItem.commodity_id' => $commodityId,
						'CommodityItem.need_commodity_stocks' => $needStock
				),
				'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getCommodityItemByClientId($clientId, $needStock) {

		$options = array(
				'fields' => array(
						'CommodityItem.*',
						'Commodity.name',
						'CarClass.name',
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'Commodity',
								'table' => 'commodities',
								'conditions' => array(
										'CommodityItem.commodity_id = Commodity.id',
										'Commodity.delete_flg = 0'
								)
						),
						array(
								'type' => 'INNER',
								'alias' => 'CarClass',
								'table' => 'car_classes',
								'conditions' => array(
										'CommodityItem.car_class_id = CarClass.id',
										'CarClass.delete_flg = 0'
								)
						)),
				'conditions' => array(
						'CommodityItem.client_id' => $clientId,
						'CommodityItem.need_commodity_stocks' => $needStock
				),
				'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getSixRankingCommodityItemId() {

		$subQuery = "(
						SELECT
						car_classes.id
						FROM
						car_types
						LEFT JOIN
						car_classes
						ON car_types.id = car_classes.car_type_id
						WHERE
						car_types.id IN (4,5))";

		$options = array(
				'fields' => array(
						'CommodityItem.id'
				),
				'joins' => array(
						array(
								"type" => 'INNER',
								"alias" => 'CarClass',
								"table" => "{$subQuery}",
								"conditions" => array(
										'CommodityItem.car_class_id = CarClass.id'
								)
								)
								),
								'conditions' => array(
										'CommodityItem.delete_flg' => 0
								),
								'recursive' => -1
								);

		return $this->find('list', $options);

	}
	public function stockCheck($reservation) {

		$options = array(
				'conditions' => array(
						'id' => $reservation['commodity_item_id']
				),
				'recursive' => -1
		);

		$commodityItem = $this->find('first', $options);

		if ($commodityItem['CommodityItem']['need_commodity_stocks']) {
			$isStock = $this->carClassStockCheck($commodityItem['CommodityItem']['car_class_id'], $reservation);
			if ($isStock) {
				$isStock = $this->commodityItemStockCheck($commodityItem['CommodityItem']['id'], $reservation);
			}
		} else {
			$isStock = $this->carClassStockCheck($commodityItem['CommodityItem']['car_class_id'], $reservation);
		}

		return $isStock;

	}

	public function commodityItemStockCheck($commmodityItemId, $reservation) {

		$stockGroupId = $this->getStockGroup($reservation['rent_office_id']);
		$span = $this->dateSpanArray($reservation['rent_datetime'],$reservation['return_datetime']);

		$result = $this->query("
				SELECT
				commodity_item_stocks.commodity_item_id AS id,
				commodity_item_stocks.stock_count,
				commodity_item_stocks.stock_date,
				commodity_item_reservations.reservation_count
				FROM
				commodity_item_stocks
				LEFT JOIN
				(
				SELECT
				commodity_item_reservations.id,
				commodity_item_reservations.client_id,
				commodity_item_reservations.stock_group_id,
				commodity_item_reservations.commodity_item_id,
				commodity_item_reservations.stock_date,
				SUM(commodity_item_reservations.reservation_count) AS reservation_count
				FROM
				commodity_item_reservations
				WHERE
				commodity_item_reservations.delete_flg = 0
				GROUP BY
				commodity_item_reservations.stock_group_id,
				commodity_item_reservations.commodity_item_id,
				commodity_item_reservations.stock_date
		) AS commodity_item_reservations
				ON commodity_item_stocks.stock_group_id = commodity_item_reservations.stock_group_id
				AND commodity_item_stocks.commodity_item_id = commodity_item_reservations.commodity_item_id
				AND commodity_item_stocks.stock_date = commodity_item_reservations.stock_date
				WHERE
				commodity_item_stocks.stock_date >= '".substr($reservation['rent_datetime'], 0, 10)."'
				AND commodity_item_stocks.stock_date <= '".substr($reservation['return_datetime'], 0, 10)."'
				AND commodity_item_stocks.stock_group_id = ".$stockGroupId."
				AND commodity_item_stocks.stock_count > 0
				AND
				(
				commodity_item_stocks.stock_count - commodity_item_reservations.reservation_count IS NULL
				OR commodity_item_stocks.stock_count - commodity_item_reservations.reservation_count >= ".$reservation['cars_count']."
		)
				ORDER BY
				commodity_item_stocks.stock_date");

		$isStock = false;
		for ($i = 0;$i < count($span);$i++) {
			if (isset($result[$i])) {
				if (strcmp($result[$i]['commodity_item_stocks']['stock_date'], $span[$i]) == 0) {
					$isStock = true;
				}
			} else {
				$isStock = false;
			}
		}

		return $isStock;
	}

	public function carClassStockCheck($carClassId, $reservation) {

		$stockGroupId = $this->getStockGroup($reservation['rent_office_id']);
		$span = $this->dateSpanArray($reservation['rent_datetime'],$reservation['return_datetime']);

		$dateFrom = $reservation['rent_datetime']['year'].'-'.$reservation['rent_datetime']['month'].'-'.$reservation['rent_datetime']['day'];
		$dateTo = $reservation['return_datetime']['year'].'-'.$reservation['return_datetime']['month'].'-'.$reservation['return_datetime']['day'];

		$result = $this->query("
				SELECT
				car_class_stocks.car_class_id AS id,
				car_class_stocks.stock_count,
				car_class_stocks.stock_date,
				car_class_reservations.reservation_count
				FROM
				car_class_stocks
				LEFT JOIN
				(
				SELECT
				car_class_reservations.id,
				car_class_reservations.client_id,
				car_class_reservations.stock_group_id,
				car_class_reservations.car_class_id,
				car_class_reservations.stock_date,
				SUM(car_class_reservations.reservation_count) AS reservation_count
				FROM
				car_class_reservations
				WHERE
				car_class_reservations.delete_flg = 0
				GROUP BY
				car_class_reservations.stock_group_id,
				car_class_reservations.car_class_id,
				car_class_reservations.stock_date
		) AS car_class_reservations
				ON car_class_stocks.stock_group_id = car_class_reservations.stock_group_id
				AND car_class_stocks.car_class_id = car_class_reservations.car_class_id
				AND car_class_stocks.stock_date = car_class_reservations.stock_date
				WHERE
				car_class_stocks.stock_count > 0
				AND car_class_stocks.suspension = 0
				AND car_class_stocks.stock_date >= '".$dateFrom."'
				AND car_class_stocks.stock_date <= '".$dateTo."'
				AND car_class_stocks.car_class_id = ".$carClassId."
				AND car_class_stocks.stock_group_id = ".$stockGroupId."
				AND
				(
					car_class_stocks.stock_count - CASE WHEN car_class_reservations.reservation_count IS NULL THEN 0 ELSE car_class_reservations.reservation_count END  >= ".$reservation['cars_count']."
				)
				ORDER BY
				car_class_stocks.stock_date ASC", false);

		$isStock = false;
		for ($i = 0;$i < count($span);$i++) {
			if (isset($result[$i])) {
				if (strcmp($result[$i]['car_class_stocks']['stock_date'], $span[$i]) == 0) {
					$isStock = true;
				}
			} else {
				$isStock = false;
			}
		}
		return $isStock;
	}

	public function dateSpanArray($from, $to) {

		$dateFrom = $from['year'].'-'.$from['month'].'-'.$from['day'];
		$from = strtotime($dateFrom);
		$to = strtotime($to['year'].'-'.$to['month'].'-'.$to['day']);

		$spanArray = array($dateFrom);
		for ($i = $dateFrom;$from < $to;) {
			$dateFrom = date("Y-m-d", strtotime($dateFrom." +1 day"));
			array_push($spanArray, $dateFrom);
			$from = strtotime($dateFrom);
		}

		return $spanArray;
	}

	public function getStockGroup($officeId) {

		$stockGroup = $this->query("
				SELECT
				office_stock_groups.stock_group_id
				FROM
				office_stock_groups
				WHERE
				delete_flg = 0
				AND office_id = ".$officeId, false);

		return $stockGroup[0]['office_stock_groups']['stock_group_id'];
	}

	public function saveCommodityPrice($commodityId,$newCommodityId) {

		$bindModel = array(
				'hasMany' => array(
						'CommodityPrice'
				),
		);
		$this->unbindFully($bindModel);

		$options = array(
				'conditions' => array(
						'commodity_id' => $commodityId,
						'delete_flg' => 0,
				),
				'recursive' => 1,
		);
		$commodityItem = $this->find('all',$options);

		// トランザクション開始
		$this->begin();

		foreach ($commodityItem as $key => $val) {

			unset($commodityItem[$key]['CommodityItem']['id']);
			$commodityItem[$key]['CommodityItem']['commodity_id'] = $newCommodityId;
			$commodityItem[$key]['CommodityItem']['created'] = date('Y-m-d H:i:s');
			$commodityItem[$key]['CommodityItem']['modified'] = date('Y-m-d H:i:s');

			$this->create();
			if ($this->save($commodityItem[$key]['CommodityItem'])) {

				foreach ($commodityItem[$key]['CommodityPrice'] as $pKey => $pVal) {

					unset($commodityItem[$key]['CommodityPrice'][$pKey]['id']);
					$commodityItem[$key]['CommodityPrice'][$pKey]['commodity_item_id'] = $this->id;
					$commodityItem[$key]['CommodityPrice'][$pKey]['created'] = date('Y-m-d H:i:s');
					$commodityItem[$key]['CommodityPrice'][$pKey]['modified'] = date('Y-m-d H:i:s');
				}

				if (!$this->CommodityPrice->saveMany($commodityItem[$key]['CommodityPrice'])) {
					$this->rollback();
					return false;
				}
			} else {
				$this->rollback();
				return false;
			}
		}

		$this->commit();
		return true;
	}


	/**
	 * プレビューで使用
	 * 商品アイテムデータ取得
	 */
	public function getCommodityPreview($commodityId, $carClassId) {
		$options = array(
			'fields' => array(
				'CommodityItem.id',
				'Commodity.*',
				'Client.*',
			),
			'joins' => array(
				array(
					'table' => 'commodities',
					'alias' => 'Commodity',
					'type' => 'INNER',
					'conditions' => array(
						'Commodity.id = CommodityItem.commodity_id'
					)
				),
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'INNER',
					'conditions' => array(
						'Client.id = CommodityItem.client_id'
					)
				),
			),
			'conditions' => array(
				'CommodityItem.commodity_id' => $commodityId,
				'CommodityItem.car_class_id' => $carClassId,
				'CommodityItem.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->find('first', $options);
	}

	/**
	 * 	プレビューで使用
	 * 商品アイテム料金取得
	 */
	public function getCommodityItemPriceData($commodityItemId) {
		// appと異なりキャンペーン料金は見ない
		$options = array(
			'fields' => array(
				'CommodityItem.id',
				'CommodityItem.car_model_id',
				'CommodityPrice.span_count',
				'CommodityPrice.price',
				'CarClass.id',
				'CarType.name',
                'CarModel.id',
				'CarModel.name',
				'CarModel.capacity',
				'ClientCarModelSort.sort',
			),
			'joins' => array(
				array(
					'table' => 'commodity_prices',
					'alias' => 'CommodityPrice',
					'type' => 'INNER',
					'conditions' => array(
						'CommodityPrice.commodity_item_id = CommodityItem.id'
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
				'CommodityPrice.delete_flg' => 0,
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
			$result['CommodityPrice'][$value['CommodityPrice']['span_count']] = $value['CommodityPrice'];
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
	 * 免責補償料金の商品ごとの最小料金を取得する
	 * @param unknown $commodityItemId
	 */
	public function getDisclaimerCompensationPrice($commodityId) {
		$options = array(
			'fields' => array(
				'CommodityItem.commodity_id',
				'MIN(DisclaimerCompensation.price) as price',
			),
			'joins' => array(
				array(
					'table' => 'disclaimer_compensations',
					'alias' => 'DisclaimerCompensation',
					'type' => 'INNER',
					'conditions' => array(
						'DisclaimerCompensation.car_class_id = CommodityItem.car_class_id'
					),
				),
			),
			'conditions' => array(
				'CommodityItem.commodity_id' => $commodityId,
				'CommodityItem.delete_flg' => 0,
				'DisclaimerCompensation.delete_flg' => 0,
			),
			'group' => 'CommodityItem.commodity_id',
			'recursive' => -1,
		);
		$results = $this->find('all', $options);
		if(!empty($results)){
			return $results;
		}
		return 0;
	}

	/**
	 * 車両クラスから商品IDを取得する
	 * @param unknown $commodityItemId
	 */
	public function getCommodityId($carClassId) {
		$options = array(
			'fields' => array(
				'CommodityItem.commodity_id',
			),
			'joins' => array(
				array(
					'table' => 'disclaimer_compensations',
					'alias' => 'DisclaimerCompensation',
					'type' => 'INNER',
					'conditions' => array(
						'DisclaimerCompensation.car_class_id = CommodityItem.car_class_id'
					),
				),
			),
			'conditions' => array(
				'CommodityItem.car_class_id' => $carClassId,
				'CommodityItem.delete_flg' => 0,
				'DisclaimerCompensation.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);
		return Hash::extract($result,'{n}.CommodityItem.commodity_id');
	}

	/*
	app配下の同名関数を引用、パラメータを管理画面用に変更
	途中にあるselect用のparamsから使用されていないcarsCountを削除(1台ずつしか予約できないから使う必要なし?)
	既に登録済みである期間は判定から除外する
	*/
	public function getOfficeStocks($carClassId, $reservation, $stockDates) {
		$stocks = array();

		//車種ID画ない場合
		if (empty($carClassId)) {
			return $stocks;
		}

		//予約情報がない場合
		if (empty($reservation)) {
			return $stocks;
		} else {
			if (!isset($reservation['rent_office_id'])) {
				return $stocks;
			}
		}

		$officeIds = $reservation['rent_office_id'];

		$officeStockGroup = $this->getOfficeStockGroup($officeIds);

		//在庫グループ取得できない場合
		if (empty($officeStockGroup)) {
			return $stocks;
		}

		// 過去の在庫を取得する必要がないため、出発日が過去の場合は今日を始点とする
		if (strtotime($reservation['rent_datetime']) < strtotime(date('Y/m/d'))) {
			$reservation['rent_datetime'] = date('Y-m-d H:i:s');
		}

		$from = strtotime($reservation['rent_datetime']);
		$to = strtotime($reservation['return_datetime']);
		$span = $this->dateSpanArray(
			array('year' => date('Y', $from), 'month' => date('m', $from), 'day' => date('d', $from)),
			array('year' => date('Y', $to), 'month' => date('m', $to), 'day' => date('d', $to))
		);
		$requiredDates = array_diff($span, $stockDates);

		$stockGroupIds = array_unique(array_values($officeStockGroup));

		list($stockGroupIdsParam, $stockGroupIdsValue) = $this->createBindArray('stockGroupIds', $stockGroupIds);

		$params = array(
			'from'			 => substr($reservation['rent_datetime'], 0, 10),
			'to'			 => substr($reservation['return_datetime'], 0, 10),
			'carClassId'	 => $carClassId,
		);

		$params += $stockGroupIdsValue;

		// バインドではinに配列を渡せないため無理やりSQL追加
		if (empty($stockDates)) {
			$stockDateSQL = '';
			$ccsStockDateSQL = '';
		} else {
			$stockDateSQL = " AND stock_date NOT IN ('" . implode("','", $stockDates) . "') ";
			$ccsStockDateSQL = " AND ccs.stock_date NOT IN ('" . implode("','", $stockDates) . "') ";
		}

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
					AND stock_group_id IN ({$stockGroupIdsParam})".
					$stockDateSQL
				."GROUP BY
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
				AND ccs.suspension = 0".
				$ccsStockDateSQL
			."GROUP BY
				ccs.stock_group_id", $params, false);
		if (empty($result)) {
			return $stocks;
		}

		$result = Hash::combine($result, '{n}.ccs.stock_group_id', '{n}');

		if (empty($result)) {
			return $stocks;
		}

		foreach ($officeStockGroup as $officeId => $stockGroupId) {
			if ($result[$stockGroupId][0]['date_count'] < count($requiredDates)) {
				// 日数分の在庫レコードが必要
				continue;
			}
			// レコードが存在する場合は残数を取得
			$stock = (isset($result[$stockGroupId]) && $result[$stockGroupId][0]['stock'] > 0) ? (int)$result[$stockGroupId][0]['stock'] : 0;
			$stocks[$officeId] = $stock;
		}

		return $stocks;
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

}

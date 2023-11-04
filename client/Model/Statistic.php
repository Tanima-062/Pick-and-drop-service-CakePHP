<?php
App::uses('AppModel', 'Model');
/**
 * Statistic Model
 *
 * @property Client $Client
 * @property CommodityItem $CommodityItem
 * @property Staff $Staff
 */
class Statistic extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'commodity_item_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'reservation_count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'price' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'date' => array(
			'date' => array(
				'rule' => array('date'),
			),
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
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
		'CommodityItem' => array(
			'className' => 'CommodityItem',
			'foreignKey' => 'commodity_item_id',
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

	public function getCommodityStatisticsSearch($data, $clientId) {

		// 在庫管理地域指定時の処理
		if (!empty($data['Statistic']['stock_group_id'])) {
			$stockGroupWhere = "AND office_stock_groups.stock_group_id = ".$data['Statistic']['stock_group_id'];
		} else {
			$stockGroupWhere = '';
		}

		// 年月指定時の処理
		if (!empty($data['Statistic']['year']) && !empty($data['Statistic']['month'])) {
			$date = $data['Statistic']['year'].'-'.$data['Statistic']['month'].'-01';
			$dateWhere = "AND statistics.statistic_date = '".$date."'";
		} else {
			$dateWhere = '';
		}

		// 商品管理番号指定時の処理
		if (!empty($data['Statistic']['commodity_key'])) {
			$commodityWhere = "AND commodities.commodity_key = '".$data['Statistic']['commodity_key']."'";
		} else {
			$commodityWhere = '';
		}

		// 車両クラス指定時の処理
		if (!empty($data['Statistic']['car_class_id'])) {
			$carClassWhere = "AND car_classes.id = ".$data['Statistic']['car_class_id'];
		} else {
			$carClassWhere = '';
		}

		$statistics = $this->query("
				SELECT
					statistics.client_id,
					SUM(statistics.price) AS price,
					COUNT(*) AS reservation_count,
					statistics.statistic_date,
					commodity_items.commodity_name,
					commodity_items.car_class_name,
					commodity_items.stock_group_name,
					commodity_items.commodity_key
				FROM
					statistics,
					(
						SELECT
							commodities.name AS commodity_name,
							commodities.commodity_key,
							car_classes.name AS car_class_name,
							commodity_rent_offices.stock_group_name,
							commodity_items.id
						FROM
							commodities,
							car_classes,
							commodity_items,
							(
								SELECT
									commodity_rent_offices.commodity_id,
									stock_groups.name AS stock_group_name
								FROM
									stock_groups,
									office_stock_groups,
									commodity_rent_offices
								WHERE
									office_stock_groups.office_id = commodity_rent_offices.office_id
									AND office_stock_groups.stock_group_id = stock_groups.id
									".$stockGroupWhere."
								GROUP BY
									commodity_rent_offices.commodity_id
							) AS commodity_rent_offices
						WHERE
							commodities.delete_flg = 0
							".$carClassWhere."
							".$commodityWhere."
							AND car_classes.id = commodity_items.car_class_id
							AND commodities.id = commodity_items.commodity_id
							AND commodity_rent_offices.commodity_id = commodities.id
					) AS commodity_items
				WHERE
					statistics.client_id = ".$clientId."
					AND statistics.delete_flg = 0
					AND statistics.reservation_status_id = 2
					".$dateWhere."
					AND commodity_items.id = statistics.commodity_item_id
				GROUP BY
					statistics.commodity_item_id",false);

		return $statistics;
	}

	public function getSaleStatisticsSearch($data, $clientId) {

		// 条件指定フラグ
		$whereFlg = false;

		// 年指定時の処理
		if (!empty($data['Statistic']['year'])) {
			$dateFrom = $data['Statistic']['year'].'-01-01 00:00:00';
			$dateTo = $data['Statistic']['year'].'-12-31 23:59:59';
			$dateWhere = "AND statistics.rent_datetime >= '".$dateFrom."' AND statistics.rent_datetime <= '".$dateTo."'";
		} else {
			$dateWhere = '';
		}

		// 商品指定時の処理
		if (!empty($data['Statistic']['commodity_id'])) {
			$commodityWhere = "AND commodities.id = ".$data['Statistic']['commodity_id'];
			$whereFlg = true;
		} else {
			$commodityWhere = '';
		}

		// 車両クラス指定時の処理
		if (!empty($data['Statistic']['car_class_id'])) {
			$carClassWhere = "AND car_classes.id = ".$data['Statistic']['car_class_id'];
			$whereFlg = true;
		} else {
			$carClassWhere = '';
		}

		// 営業所指定時の処理
		if (!empty($data['Statistic']['office_id'])) {
			$officeWhere = "AND statistics.rent_office_id = ".$data['Statistic']['office_id'];
			$whereFlg = true;
		} else {
			$officeWhere = '';
		}

		if ($whereFlg) {
			$statistics = $this->query("
					SELECT
						statistics.client_id,
						SUM(statistics.price) AS price,
						COUNT(*) AS reservation_count,
						statistics.statistic_date
					FROM
						statistics,
						(
							SELECT
								commodity_items.id
							FROM
								commodities,
								car_classes,
								commodity_items
							WHERE
								commodities.delete_flg = 0
								".$carClassWhere."
								".$commodityWhere."
								AND car_classes.id = commodity_items.car_class_id
								AND commodities.id = commodity_items.commodity_id
						) AS commodity_items
					WHERE
						statistics.client_id = ".$clientId."
						".$officeWhere."
						AND statistics.delete_flg = 0
						".$dateWhere."
						AND commodity_items.id = statistics.commodity_item_id
						AND statistics.reservation_status_id = 2
					GROUP BY
						MONTH(statistics.statistic_date)
					ORDER BY
						statistics.statistic_date ASC",false);
		} else {
			$statistics = $this->query("
					SELECT
						statistics.client_id,
						SUM(statistics.price) AS price,
						COUNT(*) AS reservation_count,
						statistics.statistic_date
					FROM
						statistics
					WHERE
						statistics.client_id = ".$clientId."
						AND statistics.delete_flg = 0
						".$dateWhere."
						AND statistics.reservation_status_id = 2
					GROUP BY
						MONTH(statistics.statistic_date)
					ORDER BY
						statistics.statistic_date ASC",false);
		}

		return $statistics;
	}

	public function getRentalStatisticsSearch($data, $clientId) {

		// 年指定時の処理
		if (!empty($data['Statistic']['year'])) {
			$dateFrom = $data['Statistic']['year'].'-01-01';
			$dateTo = $data['Statistic']['year'].'-12-31';
			$dateWhere = "AND statistics.statistic_date >= '".$dateFrom."' AND statistics.statistic_date <= '".$dateTo."'";
		} else {
			$dateWhere = '';
		}

		$statistics = $this->query("
				SELECT
					statistics.*,
					COUNT(*) AS reservation_count
				FROM
					statistics
				WHERE
					statistics.client_id = ".$clientId."
					AND statistics.delete_flg = 0
					AND statistics.reservation_status_id = 2
					".$dateWhere."
				GROUP BY
					statistics.statistic_date,
					statistics.span_count
				ORDER BY
					statistics.statistic_date ASC,
					statistics.span_count ASC",false);

		$totalCount = 0;
		$other = 0;
		$result = array();
		foreach ($statistics as $key => $statistic) {

			if (isset($index) && strcmp($index, $statistics[$key]['statistics']['statistic_date']) != 0) {
				$totalCount = 0;
				$other = 0;
			}
			$index = $statistic['statistics']['statistic_date'];
			$result[$index]['statistic_date'] = $statistic['statistics']['statistic_date'];

			$totalCount += $statistic[0]['reservation_count'];

			if ($statistic['statistics']['price_span_id'] == 1) {
				switch ($statistic['statistics']['span_count']) {
					case 1:
						$result[$index]['one_day'] = $statistic[0]['reservation_count'];
						break;
					case 2:
						$result[$index]['two_day'] = $statistic[0]['reservation_count'];
						break;
					case 3:
						$result[$index]['three_day'] = $statistic[0]['reservation_count'];
						break;
					case 4:
						$result[$index]['four_day'] = $statistic[0]['reservation_count'];
						break;
					case 5:
						$result[$index]['five_day'] = $statistic[0]['reservation_count'];
						break;
					default:
						$other += $statistic[0]['reservation_count'];
						break;
				}
			} else if ($statistic['statistics']['price_span_id'] == 2) {

				$result[$index]['weekly'] += $statistic[0]['reservation_count'];

			} else if ($statistic['statistics']['price_span_id'] == 3) {

				$result[$index]['monthly'] += $statistic[0]['reservation_count'];

			}

			$result[$index]['other'] = $other;
			$result[$index]['total_count'] = $totalCount;

		}

		$result = array_values($result);

		return $result;
	}

	public function getStatisticReserve() {

		$options = array(
				'fields' => array(
						'reservation_id'
				),
				'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function diffDataDelete() {

		$deleteReservation = $this->query("
			SELECT
				reservations.id,
				reservations.reservation_status_id,
				statistics.reservation_id
			FROM
				statistics LEFT JOIN
				reservations
				ON statistics.reservation_id = reservations.id
			WHERE
				reservations.reservation_status_id NOT IN(2)
				AND statistics.delete_flg = 0",
		false);

		$reservationIds = array();
		foreach ($deleteReservation as $reservation) {
			array_push($reservationIds,$reservation['statistics']['reservation_id']);
		}

		$fields = array(
				'delete_flg' => 1
		);
		$conditions = array(
				'reservation_id' => $reservationIds
		);
		$this->unbindModel(array( 'belongsTo' => array_keys($this->belongsTo)));
		$this->updateAll($fields,$conditions);

	}
}

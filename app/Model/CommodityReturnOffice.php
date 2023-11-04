<?php

App::uses('AppModel', 'Model');

/**
 * CommodityReturnOffice Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property Office $Office
 * @property Staff $Staff
 */
class CommodityReturnOffice extends AppModel {

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
		'office_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
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
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
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

	// CommodityRentOfficeのgetRentOfficeListByPlaceAndIdと異なり、最初からツアーAPI向け（それ以外の用途出てきたら書き換えて）
	public function getReturnOfficeListByPlaceAndId($commodityId, $Id = '', $place = 1, $dateTo = '', $datetimeTo = '') {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		// 祝日・曜日判定
		$this->PublicHoliday = new PublicHoliday();
		$toDayInfo = $this->PublicHoliday->getDayInfo($dateTo);

		$unixTimeDateTo = strtotime($dateTo);

		$toTime = date('H:i:s', strtotime($datetimeTo));
		$identifier = $toDayInfo['identifier'];

		$conditions = array(
			'CommodityReturnOffice.commodity_id' => $commodityId,
			'Office.delete_flg' => 0,
		);

		if (!empty($Id)) {
			if ($place == 2) {
				$conditions += array('Office.bullet_train_id' => $Id);
			} elseif ($place == 3) {
				$conditions += array('Office.airport_id' => $Id);
			} elseif ($place == 4) {
				$conditions += array('Station.id' => $Id);
			} else {
				$conditions += array('Office.area_id' => $Id);
			}
		}

		$options = array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => 'Office.id = CommodityReturnOffice.office_id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'OfficeSupplement',
					'table' => 'office_supplements',
					'conditions' => array(
						'Office.id = OfficeSupplement.office_id',
						'OfficeSupplement.delete_flg' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'alias' => 'OfficeBusinessHours',
					'table' => 'office_business_hours',
					'conditions' => array(
						'OfficeBusinessHours.office_id = Office.id',
						'OfficeBusinessHours.start_day_unixtime <=' => $unixTimeDateTo,
						'OfficeBusinessHours.end_day_unixtime >=' => $unixTimeDateTo,
						'OfficeBusinessHours.delete_flg' => 0,
					),
				),
			),
			'fields' => array(
				'CommodityReturnOffice.commodity_id',
				'Office.id',
				'Office.name',
				'Office.url',
				'Office.tel',
				'Office.address',
				'Office.access_dynamic',
				'OfficeSupplement.nearest_transport',
				'OfficeSupplement.method_of_transport',
				'OfficeSupplement.required_transport_time',
				'(CASE'
				. ' WHEN OfficeBusinessHours.office_id IS NOT NULL'
				. " THEN OfficeBusinessHours.{$identifier}_hours_from"
				. " ELSE Office.{$identifier}_hours_from"
				. ' END) AS office_hours_from',
				'(CASE'
				. ' WHEN OfficeBusinessHours.office_id IS NOT NULL'
				. " THEN OfficeBusinessHours.{$identifier}_hours_to"
				. " ELSE Office.{$identifier}_hours_to"
				. ' END) AS office_hours_to',
			),
			'order' => 'Office.sort'
		);

		if (!empty($Id) && $place == 4) {
			$options['joins'] = array_merge($options['joins'], array(
				array(
					'type' => 'INNER',
					'alias' => 'OfficeStation',
					'table' => 'office_stations',
					'conditions' => array(
						'Office.id = OfficeStation.office_id',
						'OfficeStation.delete_flg' => 0,
					),
				),
				array(
					'type' => 'INNER',
					'alias' => 'Station',
					'table' => 'stations',
					'conditions' => array(
						'Station.id = OfficeStation.station_id',
						'Station.delete_flg' => 0,
					),
				),
			));
		}

		$this->recursive = -1;
		$returnOfficeArray = $this->findC('all', $options);

		$returnOfficeList = array();
		foreach ($returnOfficeArray as $val) {
			// 営業時間が未設定の場合外す
			$officeHours = $val[0];
			if ($officeHours['office_hours_from'] == null && $officeHours['office_hours_to'] == null) {
				continue;
			}
			// 営業時間外は外す
			if ($officeHours['office_hours_from'] < $officeHours['office_hours_to']) {
				if (!($officeHours['office_hours_from'] <= $toTime && $toTime <= $officeHours['office_hours_to'])) {
					continue;
				}
			} else {
				if (!($toTime <= $officeHours['office_hours_to'] || $officeHours['office_hours_from'] <= $toTime)) {
					continue;
				}
			}
			$commodityId = $val['CommodityReturnOffice']['commodity_id'];
			$val['Office']['office_hours_from'] = $officeHours['office_hours_from'];
			$val['Office']['office_hours_to'] = $officeHours['office_hours_to'];
			$val['Office']['nearest_transport'] = $val['OfficeSupplement']['nearest_transport'];
			$val['Office']['method_of_transport'] = $val['OfficeSupplement']['method_of_transport'];
			$val['Office']['required_transport_time'] = $val['OfficeSupplement']['required_transport_time'];
			$returnOfficeList[$commodityId][] = $val['Office'];
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $returnOfficeList);

		return $returnOfficeList;
	}

	/**
	 * @param int|array $commodity_id 商品ID
	 * @param array     $custom_query カスタムクエリ
	 * @return array|null
	 */
	public function getReturnOfficeListByCommodityId($commodity_id, $custom_query = array()) {

		// 取得フィールド
		$fields = array('*');

		// カスタムクエリが存在すれば変更する
		if (!empty($custom_query['fields'])) {
			$fields = $custom_query['fields'];
		}

		// 結合テーブル
		$joins = array(
			array(
				"table" => "offices",
				"alias" => "Office",
				'type' => 'inner',
				'conditions' => array(
					'CommodityReturnOffice.office_id = Office.id',
					'Office.delete_flg = 0',
				),
			),
		);

		// カスタムクエリが存在すれば変更する
		if (!empty($custom_query['joins'])) {
			$joins = array_merge($joins, $custom_query['joins']);
		}

		// 検索条件
		$conditions = array(
			'CommodityReturnOffice.commodity_id' => $commodity_id,
			'CommodityReturnOffice.delete_flg'   => 0,
		);

		// カスタムクエリが存在すれば変更する
		if (!empty($custom_query['conditions'])) {
			$conditions = array_merge($conditions, $custom_query['conditions']);
		}

		// データ取得
		return $this->find('all', array(
			'fields'     => $fields,
			'joins'      => $joins,
			'conditions' => $conditions,
			'recursive'  => -1,
		));
	}

}

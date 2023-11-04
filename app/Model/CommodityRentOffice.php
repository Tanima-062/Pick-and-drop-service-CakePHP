<?php

App::uses('AppModel', 'Model');
App::uses('PublicHoliday', 'Model');
App::uses('ClientCard', 'Model');
App::uses('Office', 'Model');

/**
 * CommodityRentOffice Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property Office $Office
 * @property Staff $Staff
 */
class CommodityRentOffice extends AppModel {

	protected $cacheConfig = '1hour';

	public function getRentOfficeListByPlaceAndId($commodityId, $Id = '', $place = 1, $dateFrom = '', $datetimeFrom = '', $dateTo = '', $flip = false, $isTour = false) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		// 祝日・曜日判定
		$this->PublicHoliday = new PublicHoliday();
		$fromDayInfo = $this->PublicHoliday->getDayInfo($dateFrom);

		$unixTimeDateFrom = strtotime($dateFrom);
		// OfficeBusinessHours.end_day_unixtime >= $unixTimeDateToの条件があったが、右辺は$unixTimeDateFromの誤り
		// このメソッドの引数$dateToも不要になるが、あちこちいじりたくないので残しておく
		//$unixTimeDateTo = strtotime($dateTo);

		$unixTimePreviousDay = strtotime($dateFrom . ' -1 day');
		$previousDayInfo = $this->PublicHoliday->getDayInfo(date('Y-m-d', $unixTimePreviousDay));

		$fromTime = date('H:i:s', strtotime($datetimeFrom));
		$identifier = $fromDayInfo['identifier'];
		$previousIdentifier = $previousDayInfo['identifier'];

		$conditions = array(
			'CommodityRentOffice.commodity_id' => $commodityId,
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
					'conditions' => 'Office.id = CommodityRentOffice.office_id'
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
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Office.client_id = Client.id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'CommodityTerm',
					'table' => 'commodity_terms',
					'conditions' => array(
						'CommodityTerm.commodity_id = CommodityRentOffice.commodity_id',
						'CommodityTerm.delete_flg' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'alias' => 'OfficeBusinessHours',
					'table' => 'office_business_hours',
					'conditions' => array(
						'OfficeBusinessHours.office_id = Office.id',
						'OfficeBusinessHours.start_day_unixtime <=' => $unixTimeDateFrom,
						'OfficeBusinessHours.end_day_unixtime >=' => $unixTimeDateFrom,
						'OfficeBusinessHours.delete_flg' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'alias' => 'OfficeBusinessHoursPrevious',
					'table' => 'office_business_hours',
					'conditions' => array(
						'OfficeBusinessHoursPrevious.office_id = Office.id',
						'OfficeBusinessHoursPrevious.start_day_unixtime <=' => $unixTimePreviousDay,
						'OfficeBusinessHoursPrevious.end_day_unixtime >=' => $unixTimePreviousDay,
						'OfficeBusinessHoursPrevious.delete_flg' => 0,
					),
				)
			),
			'fields' => array(
				'CommodityRentOffice.commodity_id',
				'CommodityTerm.deadline_hours',
				'CommodityTerm.consider_opening_hours',
				'CommodityTerm.deadline_days',
				'CommodityTerm.deadline_time',
				'Office.id',
 				'Office.client_id',
 				'Office.name',
 				'Office.url',
 				'Office.address',
 				'Office.access_dynamic',
 				'Office.latitude',
 				'Office.longitude',
				'Office.mon_hours_from',
				'Office.mon_hours_to',
				'Office.tue_hours_from',
				'Office.tue_hours_to',
				'Office.wed_hours_from',
				'Office.wed_hours_to',
				'Office.thu_hours_from',
				'Office.thu_hours_to',
				'Office.fri_hours_from',
				'Office.fri_hours_to',
				'Office.sat_hours_from',
				'Office.sat_hours_to',
				'Office.sun_hours_from',
				'Office.sun_hours_to',
				'Office.hol_hours_from',
				'Office.hol_hours_to',
 				'Office.office_hours_remark',
 				'Office.office_holiday_remark',
 				'Office.rent_meeting_info',
				'OfficeSupplement.nearest_transport',
				'OfficeSupplement.method_of_transport',
				'OfficeSupplement.required_transport_time',
 				'Client.id',
 				'Client.accept_card',
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
				'(CASE'
				. ' WHEN OfficeBusinessHoursPrevious.office_id IS NOT NULL'
				. " THEN OfficeBusinessHoursPrevious.{$previousIdentifier}_hours_to"
				. " ELSE Office.{$previousIdentifier}_hours_to"
				. ' END) AS office_hours_to_previous',
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
		if ($isTour) {
			$options['fields'] = array_merge($options['fields'], array(
				'Office.tel',
			));
		}

		$this->recursive = -1;
		$rentOfficeArray = $this->findC('all', $options);

		$this->Behaviors->load('CommodityCommon');
		$this->loadComponent('OfficeUtil');
		$this->ClientCard = new ClientCard();

		$rentOfficeList = array();
		foreach ($rentOfficeArray as $val) {
			// 営業時間が未設定の場合外す
			$officeHours = $val[0];
			if ($officeHours['office_hours_from'] == null && $officeHours['office_hours_to'] == null) {
				continue;
			}
			// 営業時間外は外す
			if ($officeHours['office_hours_from'] < $officeHours['office_hours_to']) {
				if (!($officeHours['office_hours_from'] <= $fromTime && $fromTime <= $officeHours['office_hours_to'])) {
					continue;
				}
			} else {
				if (!($fromTime <= $officeHours['office_hours_to'] || $officeHours['office_hours_from'] <= $fromTime)) {
					continue;
				}
			}
			// 手仕舞いの考慮
			$term = $val['CommodityTerm'];
			if ($term['consider_opening_hours']) {
				if (!$this->isOfficeOpenOK($dateFrom, $datetimeFrom, $officeHours['office_hours_from'], $officeHours['office_hours_to_previous'], $term['deadline_hours'], $term['deadline_days'], $term['deadline_time'])) {
					continue;
				}
			}
			$commodityId = $val['CommodityRentOffice']['commodity_id'];
			if ($isTour) {
				$val['Office']['office_hours_from'] = $officeHours['office_hours_from'];
				$val['Office']['office_hours_to'] = $officeHours['office_hours_to'];
				$val['Office']['nearest_transport'] = $val['OfficeSupplement']['nearest_transport'];
				$val['Office']['method_of_transport'] = $val['OfficeSupplement']['method_of_transport'];
				$val['Office']['required_transport_time'] = $val['OfficeSupplement']['required_transport_time'];
			} else {
				$val['Office']['name'] = $this->addSuffixOfOffice($val['Office']['name']);
				$val['Office']['businessHours'] = $this->OfficeUtil->formatOfficeBusinessHours($val['Office']);
				$val['Office']['accept_card'] = $val['Client']['accept_card'];
				$val['Office']['clientCardInfo'] = !empty($val['Client']['accept_card']) ? $this->ClientCard->getCardByClientId($val['Client']['id']) : array();
				$val['Office']['airport_transfer_service'] = ($val['OfficeSupplement']['nearest_transport'] == 0 && in_array($val['OfficeSupplement']['method_of_transport'], array(1, 2)));
			}
			if ($flip) {
				$rentOffice = $val['Office'];
				if (isset($rentOfficeList[$rentOffice['id']])) {
					$rentOfficeList[$rentOffice['id']]['commodityIds'][] = $commodityId;
				} else {
					$rentOffice['commodityIds'] = [$commodityId];
					$rentOfficeList[$rentOffice['id']] = $this->adjustCache($rentOffice, $isTour);
				}
			} else {
				$rentOfficeList[$commodityId][] = $this->adjustCache($val['Office'], $isTour);
			}
		}

		$this->Behaviors->unload('CommodityCommon');

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $rentOfficeList);

		return $rentOfficeList;
	}

	/**
	 * @param int|array $commodity_id 商品ID
	 * @param array     $custom_query カスタムクエリ
	 * @return array|null
	 */
	public function getRentOfficeListByCommodityId($commodity_id, $custom_query = array()) {

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
					'CommodityRentOffice.office_id = Office.id',
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
			'CommodityRentOffice.commodity_id' => $commodity_id,
			'CommodityRentOffice.delete_flg'   => 0,
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

	private function adjustCache($office, $isTour){
		unset($office['mon_hours_from']);
		unset($office['mon_hours_to']);
		unset($office['tue_hours_from']);
		unset($office['tue_hours_to']);
		unset($office['wed_hours_from']);
		unset($office['wed_hours_to']);
		unset($office['thu_hours_from']);
		unset($office['thu_hours_to']);
		unset($office['fri_hours_from']);
		unset($office['fri_hours_to']);
		unset($office['sat_hours_from']);
		unset($office['sat_hours_to']);
		unset($office['sun_hours_from']);
		unset($office['sun_hours_to']);
		unset($office['hol_hours_from']);
		unset($office['hol_hours_to']);
		if ($isTour) {
			unset($office['client_id']);
			unset($office['latitude']);
			unset($office['longitude']);
			unset($office['office_hours_remark']);
			unset($office['office_holiday_remark']);
			unset($office['rent_meeting_info']);
			unset($office['accept_card']);
		}

		return $office;
	}
}

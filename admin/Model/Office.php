<?php
App::uses('AppModel','Model');
App::uses('PublicHoliday','Model');
/**
 * Office Model
 *
 * @property Client $Client
 * @property Area $Area
 * @property Staff $Staff
 * @property Contract $Contract
 * @property Distance $Distance
 * @property OfficeArea $OfficeArea
 * @property OfficeStockGroup $OfficeStockGroup
 * @property CommodityRent $CommodityRent
 * @property CommodityReturn $CommodityReturn
 * @property SettlementCompany $SettlementCompany
 */
class Office extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			'client_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'name'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'office hours_from'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'office hours_to'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'tel'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'address'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'access'=>array(
					'notempty'=>array(
							'rule'=>array(
									'notempty'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'station_landmark_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'accept_rent'=>array(
					'boolean'=>array(
							'rule'=>array(
									'boolean'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'accept_return'=>array(
					'boolean'=>array(
							'rule'=>array(
									'boolean'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'provide_charge'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'provide_time'=>array(
					'time'=>array(
							'rule'=>array(
									'time'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'drop_off_charge'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'staff_id'=>array(
					'numeric'=>array(
							'rule'=>array(
									'numeric'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			),
			'delete_flg'=>array(
					'boolean'=>array(
							'rule'=>array(
									'boolean'
							)
					)
					// 'message' => 'Your custom message here',
					// 'allowEmpty' => false,
					// 'required' => false,
					// 'last' => false, // Stop validation after this rule
					// 'on' => 'create', // Limit validation to 'create' or 'update' operations

			)
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			'Client'=>array(
					'className'=>'Client',
					'foreignKey'=>'client_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
			'Staff'=>array(
					'className'=>'Staff',
					'foreignKey'=>'staff_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
			'Area'=>array(
					'className'=>'Area',
					'foreignKey'=>'area_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
			'SettlementCompany'=>array(
					'className'=>'SettlementCompany',
					'foreignKey'=>'settlement_company_id',
					'conditions'=>'',
					'fields'=>'',
					'order'=>''
			),
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			'Contract'=>array(
					'className'=>'Contract',
					'foreignKey'=>'office_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			),
			'Distance'=>array(
					'className'=>'Distance',
					'foreignKey'=>'office_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			),
			'OfficeArea'=>array(
					'className'=>'OfficeArea',
					'foreignKey'=>'office_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			),
			'OfficeStockGroup'=>array(
					'className'=>'OfficeStockGroup',
					'foreignKey'=>'office_id',
					'dependent'=>false,
					'conditions'=>'',
					'fields'=>'',
					'order'=>'',
					'limit'=>'',
					'offset'=>'',
					'exclusive'=>'',
					'finderQuery'=>'',
					'counterQuery'=>''
			)
	);

	public function getAllList($clientId = '') {
		$options = array(
				'Office.delete_flg'=>0,
				'recursive'=>- 1
		);

		if(! empty($clientId)) {
			$options['conditions']['Office.client_id'] = $clientId;
		}

		$offices = $this->find('all',$options);
		$result = array();
		foreach($offices as $office) {

			$result[$office['Office']['id']] = $office['Office'];
		}

		return $result;
	}

	public function getOfficeList($clientId) {
		return $this->find('list',array(
				'conditions'=>array(
						'client_id'=>$clientId
				)
		));
	}

	public function getClientName($officeId) {
		return $this->find('first',array(
				'conditions'=>array(
						'Office.id'=>$officeId
				)
		));
	}

	public function getOffice($id) {
		$options = array(
				'conditions'=>array(
							/*'Office.delete_flg' => 0,*/
							'Office.id'=>$id
				),
				'recursive'=>- 1
		);

		return $this->find('first',$options);
	}

	/**
	 * 営業所IDと日付から営業所の営業時間を取得・返却する
	 *
	 * @param int $officeId
	 * @param date $date
	 *        	return array
	 */
	public function getOfficeBusinessHours($officeId, $date) {
		$this->PublicHoliday = new PublicHoliday();

		$date = date('Y-m-d',strtotime($date));

		// 祝日判定 曜日の識別子を取得
		$dateInfo = $this->PublicHoliday->getDayInfo($date);

		$timestamp = strtotime($date);

		$officeBusinessHourSubQuery = "
			select
				office_business_hours.{$dateInfo['identifier']}_hours_from,
				office_business_hours.{$dateInfo['identifier']}_hours_to,
				office_business_hours.office_id
			from
				offices
			inner join
				office_business_hours on offices.id = office_business_hours.office_id
			where
				offices.id = {$officeId}
			and
				office_business_hours.start_day_unixtime <= {$timestamp}
			and
				office_business_hours.end_day_unixtime >=  {$timestamp}
			and
				office_business_hours.delete_flg = 0
		";

		$this->recursive = - 1;
		$officeInfo = $this->find("first",array(
				"conditions"=>array(
						"Office.id"=>$officeId
				),
				"joins"=>array(
						array(
								"type"=>"LEFT",
								"alias"=>"OfficeBusinessHour",
								"table"=>"({$officeBusinessHourSubQuery})",
								"conditions"=>"Office.id = OfficeBusinessHour.office_id"
						)
				),
				"fields"=>array(
						"OfficeBusinessHour.office_id",
						"coalesce(OfficeBusinessHour.{$dateInfo['identifier']}_hours_from,Office.{$dateInfo['identifier']}_hours_from) as office_hours_from",
						"coalesce(OfficeBusinessHour.{$dateInfo['identifier']}_hours_to,Office.{$dateInfo['identifier']}_hours_to) as office_hours_to"
				)
		));

		return $officeInfo[0];
	}

	public function getOfficePrefecture($clientId) {
		$options = array(
				'fields' => array(
						'Office.*',
						'Area.*',
						'Prefecture.*',
				),
				'joins' => array(
						array(
								'table' => 'areas',
								'alias' => 'Area',
								'type' => 'LEFT',
								'conditions' => array(
										'Area.id = Office.area_id',
								),
						),
						array(
								'table' => 'prefectures',
								'alias' => 'Prefecture',
								'type' => 'LEFT',
								'conditions' => array(
										'Prefecture.id = Area.prefecture_id'
								),
						),
				),
				'conditions' => array(
						'Office.client_id' => $clientId,
						'Office.delete_flg' => 0,
						'Area.delete_flg' => 0,
						'Prefecture.delete_flg' => 0,
				),
				'order' => array(
						'Prefecture.sort' => 'asc',
						'Office.sort' => 'asc',
						'Office.id' => 'asc',
				),
				'recursive'=>-1,
		);
		return $this->find('all', $options);
	}

	public function getOfficeByClientIata($clientId, $iataCd) {
		return $this->find('list', array(
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'landmarks',
					'alias' => 'Landmark',
					'conditions' => array(
						'Landmark.id = Office.airport_id',
						'Landmark.iata_cd' => $iataCd,
						'Landmark.delete_flg' => 0
					)
				)
			),
			'conditions' => array(
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0
			)
		));
	}

	public function getTourOfficeInfo($officeId) {
		$ret = $this->find('first', array(
			'fields' => array(
				'Office.name',
				'Office.tel',
				'Office.url',
				'Client.url'
			),
			'conditions' => array(
				'Office.id' => $officeId
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'clients',
					'alias' => 'Client',
					'conditions' => 'Client.id = Office.client_id'
				)
			),
			'recursive' => -1
		));
		$ret['office_contents_url'] = sprintf('rentacar/company/%s/%s/', $ret['Client']['url'], $ret['Office']['url']);
		return $ret;
	}

	/**
	 * 営業所IDと日付から各営業所の営業時間を取得・返却する
	 * appにあるgetOfficeBusinessHoursの複数対応版
	 * @param int $officeId
	 * @param date $date
	 *
	 * 戻り値の形状 array[$officeId][$date] = array(office_hours_from,office_hours_to,start_day,end_day)
	 */
	public function getOfficeBusinessHoursMulti($targetDatetime, $dates) {
		$officeBusinessDatetime = array();
		// 特殊営業時間取得
		foreach ($targetDatetime as $pattern => $reservationData) {
			foreach ($reservationData as $reservationId => $officeData) {
				foreach ($officeData as $officeId => $datetime) {
					$date = date('Y-m-d',strtotime($datetime));
					$timestamp = strtotime($date);
					$officeIds[] = $officeId;
					$condition = array(
						'Office.id' => $officeId,
						'OfficeBusinessHour.start_day_unixtime <=' => $timestamp,
						'OfficeBusinessHour.end_day_unixtime >=' => $timestamp
					);
					$conditions[] = $condition;
				}
			}
		}
		$options = array(
			'fields' => array(
				"OfficeBusinessHour.mon_hours_from",
				"OfficeBusinessHour.mon_hours_to",
				"OfficeBusinessHour.tue_hours_from",
				"OfficeBusinessHour.tue_hours_to",
				"OfficeBusinessHour.wed_hours_from",
				"OfficeBusinessHour.wed_hours_to",
				"OfficeBusinessHour.thu_hours_from",
				"OfficeBusinessHour.thu_hours_to",
				"OfficeBusinessHour.fri_hours_from",
				"OfficeBusinessHour.fri_hours_to",
				"OfficeBusinessHour.sat_hours_from",
				"OfficeBusinessHour.sat_hours_to",
				"OfficeBusinessHour.sun_hours_from",
				"OfficeBusinessHour.sun_hours_to",
				"OfficeBusinessHour.hol_hours_from",
				"OfficeBusinessHour.hol_hours_to",
				'OfficeBusinessHour.office_id',
				"OfficeBusinessHour.start_day",
				"OfficeBusinessHour.end_day",
				"OfficeBusinessHour.start_day_unixtime",
				"OfficeBusinessHour.end_day_unixtime"
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'office_business_hours',
					'alias' => 'OfficeBusinessHour',
					'conditions' => 'Office.id = OfficeBusinessHour.office_id',
				),
			),
			'conditions' => array(
				'OfficeBusinessHour.delete_flg = 0',
				'OR' => $conditions
			),
			'recursive'=> -1,
		);
		$result = $this->find('all', $options);
		if (!empty($result)) {
			foreach ($result as $key => $officeBusinessHour) {
				$officeBusinessHourData[$officeBusinessHour['OfficeBusinessHour']['office_id']]['special'][] = $officeBusinessHour['OfficeBusinessHour'];
			}
		}

		// 通常営業時間取得
		$options = array(
			"conditions" => array(
				"Office.id" => $officeIds
			),
			"fields" => array(
				"Office.id",
				"Office.mon_hours_from",
				"Office.mon_hours_to",
				"Office.tue_hours_from",
				"Office.tue_hours_to",
				"Office.wed_hours_from",
				"Office.wed_hours_to",
				"Office.thu_hours_from",
				"Office.thu_hours_to",
				"Office.fri_hours_from",
				"Office.fri_hours_to",
				"Office.sat_hours_from",
				"Office.sat_hours_to",
				"Office.sun_hours_from",
				"Office.sun_hours_to",
				"Office.hol_hours_from",
				"Office.hol_hours_to",
			),
			'recursive'=> -1,
		);
		$result = $this->find('all', $options);
		if (!empty($result)) {
			foreach ($result as $key => $officeBusinessHour) {
				$officeBusinessHourData[$officeBusinessHour['Office']['id']]['default'] = $officeBusinessHour['Office'];
			}
		}

		//祝日判定 曜日の識別子を取得
		$this->PublicHoliday = new PublicHoliday();
		$dateInfo = $this->PublicHoliday->getDayInfoMulti($dates);

		// 特定店舗の特定日付の営業時間取得(特殊営業時間が優先)
		foreach ($targetDatetime as $pattern => $reservationData) {
			foreach ($reservationData as $reservationId => $officeData) {
				foreach ($officeData as $officeId => $datetime) {
					$date = date('Y-m-d',strtotime($datetime));
					$timestamp = strtotime($date);
					$identifier = $dateInfo[$date];
					if (empty($officeBusinessDatetime[$officeId][$date])) {
						if (!empty($officeBusinessHourData[$officeId]['special'])) {
							foreach ($officeBusinessHourData[$officeId]['special'] as $key => $officeBusinessHour) {
								if ($officeBusinessHour['start_day_unixtime'] <= $timestamp && $officeBusinessHour['end_day_unixtime'] >= $timestamp) {
									$officeBusinessDatetime[$officeId][$date] = array(
										'office_hours_from' => $officeBusinessHour[$identifier . '_hours_from'],
										'office_hours_to' => $officeBusinessHour[$identifier . '_hours_to'],
										'start_day' => $officeBusinessHour['start_day'],
										'end_day' => $officeBusinessHour['end_day'],
									);
									break;
								}
								if (!empty($officeBusinessHourData[$officeId]['default'])) {
									// default がないことはないと思うが念の為
									$officeBusinessDatetime[$officeId][$date] = array(
										'office_hours_from' => $officeBusinessHourData[$officeId]['default'][$identifier . '_hours_from'],
										'office_hours_to' => $officeBusinessHourData[$officeId]['default'][$identifier . '_hours_to'],
									);
								}
							}
						} elseif (!empty($officeBusinessHourData[$officeId]['default'])) {
							// default がないことはないと思うが念の為
							$officeBusinessDatetime[$officeId][$date] = array(
								'office_hours_from' => $officeBusinessHourData[$officeId]['default'][$identifier . '_hours_from'],
								'office_hours_to' => $officeBusinessHourData[$officeId]['default'][$identifier . '_hours_to'],
							);
						}
					}
				}
			}
		}
		return $officeBusinessDatetime;
	}

}

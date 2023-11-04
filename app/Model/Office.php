<?php

App::uses('AppModel', 'Model');
App::uses('PublicHoliday', 'Model');

class Office extends AppModel {
	public $actsAs = array('KeywordReplace');

	protected $cacheConfig = '1hour';

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
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'image_relative_url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'office hours_from' => array(
			'time' => array(
				'rule' => array('time'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'office hours_to' => array(
			'time' => array(
				'rule' => array('time'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'tel' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'access' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'pickup' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'closed_on' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'station_landmark_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'accept_rent' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'accept_return' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'provide_charge' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'provide_time' => array(
			'time' => array(
				'rule' => array('time'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'drop_off_charge' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			//'message' => 'Your custom message here',
			//'allowEmpty' => false,
			//'required' => false,
			//'last' => false, // Stop validation after this rule
			//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'seo' => array(
			'notempty' => array(
				'rule' => array('notempty'),
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
		'Contract' => array(
			'className' => 'Contract',
			'foreignKey' => 'office_id',
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
		'OfficeStockGroup' => array(
			'className' => 'OfficeStockGroup',
			'foreignKey' => 'office_id',
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
	);

	/**
	 * hasAndBelongsToMany associations
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'CommodityRent' => array(
			'className' => 'CommodityRentOffice',
			'joinTable' => 'commodity_rent_offices',
			'foreignKey' => 'office_id',
			'associationForeignKey' => 'commodity_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'CommodityReturn' => array(
			'className' => 'CommodityReturnOffice',
			'joinTable' => 'commodity_return_offices',
			'foreignKey' => 'office_id',
			'associationForeignKey' => 'commodity_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	public function getOfficeClientListByCondition($clientId, $type, $conditionId) {

		$conditions = array(
			'Office.client_id' => $clientId,
			'Office.delete_flg' => 0
		);

		//エリアID
		if ($type == '1') {
			$conditions += array('Office.area_id' => $conditionId);
		} else if ($type == '3') {
			//空港ID
			$conditions += array('Office.airport_id' => $conditionId);
		} else if ($type == '2') {
			//新幹線ID
			$conditions += array('Office.bullet_train_id' => $conditionId);
		}

		$options = array(
			'conditions' => $conditions,
			'recursive' => -1,
			'fields' => array(
				'Office.id',
				'Office.name',
				'Office.access',
				'Office.area_id',
				'Office.url',
				'Client.url'
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = Client.id AND Client.delete_flg = 0'
				),
			),
		);

		$result = $this->find('all', $options);

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	//顧客毎の事業所を取得する
	public function getOfficeClientListByClientId($clientId) {
		$conditions = array(
			'Office.client_id' => $clientId,
			'Office.delete_flg' => 0
		);

		$result = $this->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Office.area_id = Area.id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = client.id AND client.delete_flg = 0'
				)
			),
			'fields' => array(
				'Office.id',
				'Office.name',
				'Office.url',
				'Office.airport_id',
				'Office.sort',
				'client.url',
				'Area.prefecture_id',
				'Area.id',
			),
			'order' => array(
				'Area.prefecture_id' => 'asc',
				'Area.id' => 'asc',
				'Office.sort' => 'asc'
			),
			'recursive' => -1
		));

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	//都道府県から店舗を持つ顧客一覧を取得する
	public function getAllOfficeClientListByPrefectureId($prefectureId) {

		$conditions = array(
			'Office.delete_flg' => 0
		);

		return $this->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => array(
						'Office.area_id = Area.id',
						'Area.prefecture_id' => $prefectureId,
					),
				),
				array(
					'type' => 'INNER',
					'alias' => 'client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = client.id AND client.delete_flg = 0'
				)
			),
			'fields' => array(
				'client.id',
				'client.name',
				'client.sp_logo_image',
				'client.url',
				'Office.id',
				'Office.name',
				'Office.url',
			),
			'group' => array(
				'Office.client_id'
			),
			'recursive' => -1
		));
	}

	//都道府県から店舗を持つ顧客一覧を取得する(内部リンク変換用)
	public function getAllClientListByPrefectureId($prefectureId) {

		$conditions = array(
			'Office.delete_flg' => 0
		);

		$result = $this->findC('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => array(
						'Office.area_id = Area.id',
						'Area.prefecture_id' => $prefectureId,
					),
				),
				array(
					'type' => 'INNER',
					'alias' => 'client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = client.id AND client.delete_flg = 0'
				)
			),
			'fields' => array(
				'client.name',
				'client.url',
				'Office.name',
				'Office.url'
			),
			'recursive' => -1
		));

		$combined = array();
		if (!empty($result)) {
			foreach ($result as $data) {
				$name = $data['client']['name'] . $data['Office']['name'];
				$url = '/rentacar/company/'. $data['client']['url'] . '/' . $data['Office']['url']. '/';
				$combined[] = array(
					'name' => $name,
					'url' => $url,
					'link_cd' => $data['Office']['url'],
					'length' => mb_strlen($name)
				);
				// 空白除いた名前も登録
				if (mb_strpos($name, ' ') !== false) {
					$replaced = str_replace(' ', '', $name);
					$combined[] = array(
						'name' => $replaced,
						'url' => $url,
						'link_cd' => $data['Office']['url'],
						'length' => mb_strlen($replaced)
					);
				}
			}
		}

		return $combined;
	}

	public function getOfficeRentReturn($officeId) {

		$options = array(
			'fields' => array(
				'Office.id',
				'Office.name',
				'Office.area_drop_off_id',
				'Office.airport_id',
				'OfficeSupplement.method_of_transport',
				'Landmark.landmark_category_id',
			),
			'conditions' => array(
				'Office.id' => $officeId,
				'Office.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'office_supplements',
					'alias' => 'OfficeSupplement',
					'conditions' => 'OfficeSupplement.office_id = Office.id',
				),
				array(
					'type' => 'LEFT',
					'table' => 'landmarks',
					'alias' => 'Landmark',
					'conditions' => array(
						'Landmark.id = Office.airport_id',
						'Landmark.delete_flg' => 0,
					),
				),
			),
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getOfficeById($id) {
		$options = array(
			'conditions' => array(
				'id' => $id,
				'delete_flg' => 0
			),
			'recursive' => -1
		);

		$result = $this->find('all', $options);

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	public function getOfficeIdByLinkCd($clientId, $linkCd) {
		$options = array(
			'fields' => 'Office.id',
			'conditions' => array(
				'Office.client_id' => $clientId,
				'Office.url' => $linkCd,
				'Office.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('first', $options);
	}

	/**
	 * 深夜手数料取得
	 * @param unknown $fromData
	 * @param unknown $returnData
	 */
	public function getLateNightFee($fromData, $returnData) {

		$fromOfficeOptions = array(
			'fields' => array(
				'LateNightFee.price_addition_flg',
				'LateNightFee.price',
			),
			'joins' => array(
				array(
					'table' => 'late_night_fees',
					'alias' => 'LateNightFee',
					'type' => 'INNER',
					'conditions' => array(
						'LateNightFee.id = Office.late_night_fee_flg'
					),
				),
			),
			'conditions' => array(
				'Office.id' => $fromData['fromOfficeId'],
				'Office.delete_flg' => 0,
				'OR' => array(
					array(
						'LateNightFee.target_time_from < LateNightFee.target_time_to',
						'LateNightFee.target_time_from <=' => $fromData['fromTime'],
						'LateNightFee.target_time_to >=' => $fromData['fromTime'],
					),
					array(
						'LateNightFee.target_time_from > LateNightFee.target_time_to',
						'OR' => array(
							'LateNightFee.target_time_from <=' => $fromData['fromTime'],
							'LateNightFee.target_time_to >=' => $fromData['fromTime'],
						),
					),
				),
				'LateNightFee.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$returnOfficeOptions = array(
			'fields' => array(
				'LateNightFee.price_addition_flg',
				'LateNightFee.price',
			),
			'joins' => array(
				array(
					'table' => 'late_night_fees',
					'alias' => 'LateNightFee',
					'type' => 'INNER',
					'conditions' => array(
						'LateNightFee.id = Office.late_night_fee_flg'
					),
				),
			),
			'conditions' => array(
				'Office.id' => $returnData['returnOfficeId'],
				'Office.delete_flg' => 0,
				'OR' => array(
					array(
						'LateNightFee.target_time_from < LateNightFee.target_time_to',
						'LateNightFee.target_time_from <=' => $returnData['returnTime'],
						'LateNightFee.target_time_to >=' => $returnData['returnTime'],
					),
					array(
						'LateNightFee.target_time_from > LateNightFee.target_time_to',
						'OR' => array(
							'LateNightFee.target_time_from <=' => $returnData['returnTime'],
							'LateNightFee.target_time_to >=' => $returnData['returnTime'],
						),
					),
				),
				'LateNightFee.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		$fromOffce = $this->find('first', $fromOfficeOptions);
		$returnOffce = $this->find('first', $returnOfficeOptions);

		if (empty($fromOffce) && empty($returnOffce)) {
			$result = false;
		} else {
			$nightFee = 0;
			// 出発営業所
			if (!empty($fromOffce)) {
				if ($fromOffce['LateNightFee']['price_addition_flg'] == 1) {
					if (empty($nightFee)) {
						$nightFee = $fromOffce['LateNightFee']['price'];
					}
				} else {
					if (empty($nightFee)) {
						$nightFee = $fromOffce['LateNightFee']['price'];
					} else {
						$nightFee += $fromOffce['LateNightFee']['price'];
					}
				}
			}
			// 返却営業所
			if (!empty($returnOffce)) {
				if ($returnOffce['LateNightFee']['price_addition_flg'] == 1) {
					if (empty($nightFee)) {
						$nightFee = $returnOffce['LateNightFee']['price'];
					}
				} else {
					if (empty($nightFee)) {
						$nightFee = $returnOffce['LateNightFee']['price'];
					} else {
						$nightFee += $returnOffce['LateNightFee']['price'];
					}
				}
			}
			$result = $nightFee;
		}

		return $result;
	}

	/**
	 * 深夜手数料取得
	 * 複数営業所に対し取得して計算は呼び出し元で行う
	 * @param array $officeIds
	 * @param array $returnOfficeIds
	 * @param string $timeFrom
	 * @param string $timeTo
	 */
	public function getLateNightFees($officeIds, $returnOfficeIds, $timeFrom, $timeTo) {

		$fromOfficeOptions = array(
			'fields' => array(
				'Office.id',
				'LateNightFee.price_addition_flg',
				'LateNightFee.price',
			),
			'joins' => array(
				array(
					'table' => 'late_night_fees',
					'alias' => 'LateNightFee',
					'type' => 'INNER',
					'conditions' => array(
						'LateNightFee.id = Office.late_night_fee_flg'
					),
				),
			),
			'conditions' => array(
				'Office.id' => $officeIds,
				'Office.delete_flg' => 0,
				'OR' => array(
					array(
						'LateNightFee.target_time_from < LateNightFee.target_time_to',
						'LateNightFee.target_time_from <=' => $timeFrom,
						'LateNightFee.target_time_to >=' => $timeFrom,
					),
					array(
						'LateNightFee.target_time_from > LateNightFee.target_time_to',
						'OR' => array(
							'LateNightFee.target_time_from <=' => $timeFrom,
							'LateNightFee.target_time_to >=' => $timeFrom,
						),
					),
				),
				'LateNightFee.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$returnOfficeOptions = array(
			'fields' => array(
				'Office.id',
				'LateNightFee.price_addition_flg',
				'LateNightFee.price',
			),
			'joins' => array(
				array(
					'table' => 'late_night_fees',
					'alias' => 'LateNightFee',
					'type' => 'INNER',
					'conditions' => array(
						'LateNightFee.id = Office.late_night_fee_flg'
					),
				),
			),
			'conditions' => array(
				'Office.id' => $returnOfficeIds,
				'Office.delete_flg' => 0,
				'OR' => array(
					array(
						'LateNightFee.target_time_from < LateNightFee.target_time_to',
						'LateNightFee.target_time_from <=' => $timeTo,
						'LateNightFee.target_time_to >=' => $timeTo,
					),
					array(
						'LateNightFee.target_time_from > LateNightFee.target_time_to',
						'OR' => array(
							'LateNightFee.target_time_from <=' => $timeTo,
							'LateNightFee.target_time_to >=' => $timeTo,
						),
					),
				),
				'LateNightFee.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		$fromOffices = $this->findC('all', $fromOfficeOptions);
		$returnOffices = $this->findC('all', $returnOfficeOptions);

		if (!empty($fromOffices)) {
			$fromOffices = Hash::combine($fromOffices, '{n}.Office.id', '{n}.LateNightFee');
		}
		if (!empty($returnOffices)) {
			$returnOffices = Hash::combine($returnOffices, '{n}.Office.id', '{n}.LateNightFee');
		}

		return array($fromOffices, $returnOffices);
	}

	public function getOfficeIdListByAirportId($airportId, $clientId = null) {
		if (empty($airportId)) {
			return array();
		}

		$options = array(
			'fields' => array(
				'Office.id',
				'Office.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Client.id = Office.client_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Landmark',
					'table' => 'landmarks',
					'conditions' => 'Landmark.id = Office.airport_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'LandmarkCategory',
					'table' => 'landmark_categories',
					'conditions' => 'LandmarkCategory.id = Landmark.landmark_category_id',
				),
			),
			'conditions' => array(
				'Office.airport_id' => $airportId,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Landmark.delete_flg' => 0,
				'LandmarkCategory.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		if (!empty($clientId)) {
			$options['conditions']['Office.client_id'] = $clientId;
		}

		return $this->findC('list', $options);
	}

	public function getOfficeIdListByCityId($cityId) {
		if (empty($cityId)) {
			return array();
		}

		$options = array(
			'fields' => array(
				'Office.id',
				'Office.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Client.id = Office.client_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'City',
					'table' => 'cities',
					'conditions' => 'City.id = Office.city_id',
				),
			),
			'conditions' => array(
				'Office.city_id' => $cityId,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'City.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('list', $options);
	}

	public function getOfficeIdListByAreaId($areaId) {
		if (empty($areaId)) {
			return array();
		}

		$options = array(
			'fields' => array(
				'Office.id',
				'Office.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Client.id = Office.client_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.id = Office.area_id',
				),
			),
			'conditions' => array(
				'Office.area_id' => $areaId,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Area.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('list', $options);
	}

	// 座標から店舗IDを取得
	public function getOfficeIdListByLocation($lat, $lng, $clientIds = array()) {
		if (!isset($lat) || !isset($lng)) {
			return array();
		}

		$params = array(
			'lat' => $lat,
			'lng' => $lng,
		);

		// ひとまず距離2km以内、最大10件にしておく
		$sql = "
			SELECT
			o.id,
			(
				6371 * ACOS(
				COS(RADIANS(:lat))
				* COS(RADIANS(o.latitude))
				* COS(RADIANS(o.longitude) - RADIANS(:lng))
				+ SIN(RADIANS(:lat))
				* SIN(RADIANS(o.latitude))
				)
			) AS distance
			FROM rentacar.offices AS o
			  INNER JOIN rentacar.clients AS c
			    ON c.id = o.client_id
			WHERE
			  c.delete_flg = 0
			  AND o.delete_flg = 0
		";

		// 会社指定がある場合
		if (!empty($clientIds)) {
			list($clientIdsParam, $clientIdsValue) = $this->createBindArray('client_id', $clientIds);
			$params += $clientIdsValue;
			$sql .= "AND c.id IN ({$clientIdsParam})\n";
		}

		$sql .= "
			  HAVING
			  distance <= 2
			ORDER BY
			  distance
			LIMIT
			  10
		";

		$ret = $this->query($sql, $params);

		return !empty($ret) ? Hash::combine($ret, '{n}.o.id', '{n}.o.id') : array();
	}

	public function getOfficeIdListByStationId($station_id) {

		$options = array(
			'conditions' => array(
				'Office_station.station_id' => $station_id,
				'Office_station.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'fields' => array(
				'Office.id',
				'Office.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Office.client_id = Client.id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office_station',
					'table' => 'office_stations',
					'conditions' => array('Office.id = Office_station.office_id')
				),
			),
			'recursive' => -1
		);

		return $this->findC('list', $options);
	}

	public function getOfficeAreaIdList($params = array()) {

		$conditions = array(
			'Office.delete_flg' => 0,
			'Client.delete_flg' => 0,
			'Area.delete_flg' => 0,
		);

		if (empty($params)) {
			return '';
		}

		if (!empty($params['airport_id']) && empty($params['city_id']) && empty($params['bullet_train_id'])) {
			$conditions += array('Office.airport_id' => $params['airport_id']);
		} else if (empty($params['airport_id']) && !empty($params['city_id']) && empty($params['bullet_train_id'])) {
			$conditions += array('Office.city_id' => $params['city_id']);
		} else if (empty($params['airport_id']) && empty($params['city_id']) && !empty($params['bullet_train_id'])) {
			$conditions += array('Office.bullet_train_id' => $params['bullet_train_id']);
		} else {
			return '';
		}

		return $this->findC('list', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Client.id = Office.client_id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.id = Office.area_id'
				)
			),
			'fields' => array(
				'Area.id',
				'Area.id'
			),
			'recursive' => -1,
		));
	}

	public function getOfficePrefectureIdList($clientId) {

		$conditions = array();
		$conditions += array('Office.client_id' => $clientId);

		return $this->find('list', array(
					'conditions' => $conditions,
					'joins' => array(
						array(
							'type' => 'INNER',
							'alias' => 'Area',
							'table' => 'areas',
							'conditions' => 'Office.area_id = Area.id'
						),
						array(
							'type' => 'INNER',
							'alias' => 'Prefecture',
							'table' => 'prefectures',
							'conditions' => 'Area.prefecture_id = Prefecture.id'
						),
					),
					'fields' => array(
						'Prefecture.id',
						'Prefecture.name'
					),
					'order' => array(
						'Prefecture.id'
					)
						)
		);
	}

	/**
	 * 営業所IDと日付から営業所の営業時間を取得・返却する
	 *
	 * @param int $officeId
	 * @param date $date
	 *
	 * return array
	 */
	public function getOfficeBusinessHours($officeId, $date) {

		$this->PublicHoliday = new PublicHoliday();

		//祝日判定 曜日の識別子を取得
		$dateInfo = $this->PublicHoliday->getDayInfo($date);

		$timestamp = strtotime($date);

		$db = $this->getDataSource();

		$officeBusinessHourSubQuery = $db->buildStatement(array(
			'fields' => array(
				"office_business_hours.{$dateInfo['identifier']}_hours_from",
				"office_business_hours.{$dateInfo['identifier']}_hours_to",
				'office_business_hours.office_id',
				"office_business_hours.start_day",
				"office_business_hours.end_day"
			),
			'table' => $db->fullTableName($this),
			'alias' => 'Office',
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'office_business_hours',
					'conditions' => 'Office.id = office_business_hours.office_id',
				),
			),
			'conditions' => array(
				'Office.id' => $officeId,
				'office_business_hours.start_day_unixtime <=' => $timestamp,
				'office_business_hours.end_day_unixtime >=' => $timestamp,
				'office_business_hours.delete_flg = 0',
			),
		), $this);

		$this->recursive = -1;
		$officeInfo = $this->find("first", array(
			"conditions" => array(
				"Office.id" => $officeId
			),
			"joins" => array(
				array(
					"type" => "LEFT",
					"alias" => "OfficeBusinessHour",
					"table" => "({$officeBusinessHourSubQuery})",
					"conditions" => "Office.id = OfficeBusinessHour.office_id"
				)
			),
			"fields" => array(
				"OfficeBusinessHour.office_id",
				"coalesce(OfficeBusinessHour.{$dateInfo['identifier']}_hours_from,Office.{$dateInfo['identifier']}_hours_from) as office_hours_from",
				"coalesce(OfficeBusinessHour.{$dateInfo['identifier']}_hours_to,Office.{$dateInfo['identifier']}_hours_to) as office_hours_to",
				"OfficeBusinessHour.start_day",
				"OfficeBusinessHour.end_day"
			)
				)
		);

		return array(
			'start_time' => $officeInfo[0]['office_hours_from'],
			'end_time' => $officeInfo[0]['office_hours_to'],
			'start_day' => $officeInfo['OfficeBusinessHour']['start_day'],
			'end_day' => $officeInfo['OfficeBusinessHour']['end_day'],
		);
	}

	/**
	 * エリア付近の営業所リストを返す(指定エリア内の営業所を返す)
	 * @param type $area_id
	 * @return type
	 */
	public function getOfficeNearList($area_id) {

		return $this->find('list', array(
					'conditions' => array('Office.area_id' => $area_id),
					'fields' => array(
						'Office.id',
						'Office.name'
					)
						)
		);
	}

	/**
	 * エリア付近の営業所リストを返す(指定エリア内の営業所を返す)
	 * office_idに指定されたidは取得しない
	 * @param type $area_id
	 * @return type
	 */
	public function getOfficeNearListAddUrlData($area_id, $office_id) {

		$result = $this->find('all', array(
			'conditions' => array(
				'Office.area_id' => $area_id,
				'Office.delete_flg' => false,
				'NOT' => array(
					'Office.id' => $office_id
				)
			),
			'fields' => array(
				'Office.id',
				'Office.name',
				'Office.url',
				'Client.url',
				'Client.name'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array(
						'Office.client_id = Client.id',
						'Client.delete_flg' => false,
					),
				),
			),
			'recursive' => -1
				)
		);

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	/**
	 * 空港付近の営業所リストを返す(指定空港内の営業所を返す)
	 * @param type $area_id
	 * @return type
	 */
	public function getOfficeNearListByAirportId($airport_id, $clientId = null) {

		$options = array(
			'conditions' => array(
				'Office.airport_id' => $airport_id,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'fields' => '*',
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = Client.id',
				),
			),
			'recursive' => -1
		);
		if (!empty($clientId)) {
			$options['conditions']['Office.client_id'] = $clientId;
		}

		$result = $this->findC('all', $options);

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	/**
	 * 市区町村付近の営業所リストを返す(指定市区町村内の営業所を返す)
	 * @param type $area_id
	 * @return type
	 */
	public function getOfficeNearListByAreaId($area_id) {

		$result = $this->findC('all', array(
			'conditions' => array(
				'Office.area_id' => $area_id,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'fields' => '*',
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = Client.id',
				),
			),
			'recursive' => -1
		));

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	/**
	 * リンクコードから営業所とクライアントを返す
	 * @param string $clientLinkCd
	 * @param string $officeLinkCd
	 * @return array
	 */
	public function getOfficeWithClientByLinkCd($clientLinkCd, $officeLinkCd) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, $clientLinkCd, $officeLinkCd);
		$cache_ret = $this->readCache($cache_name);

		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$options = array(
			'fields' => array(
				'Client.*',
				'Office.*',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Office.client_id = Client.id')
				),
			),
			'conditions' => array(
				'Client.url' => $clientLinkCd,
				'Office.url' => $officeLinkCd,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
			),
			'recursive' => -1
		);

		$result = $this->find('first', $options);

		if (!empty($result)) {
			$result['Office']['name'] = $this->addSuffixOfOffice($result['Office']['name']);
			$result['Office']['address_replaced'] = $this->replaceAddress($result['Office']['address']);
			$result['Office']['access_dynamic'] = $this->replaceAccess($result['Office']['access_dynamic'], $result['Office']['area_id']);
		}

		$this->writeCache($cache_name, $result);

		return $result;
	}

	private function replaceAddress($address) {
		if (empty($address)) {
			return $address;
		}

		$Prefecture = new Prefecture();

		$keywordList = $Prefecture->getPrefectureLinkCdList();

		// キーワードを長さの降順に処理することで、aタグ多重化を回避
		usort($keywordList, array($this, 'compareKeywords'));

		$this->skipLinkCd = '';
		$this->keywordList = $keywordList;
		$this->keywordCount = count($keywordList);
		$replaced = array();
		for ($i = 0; $i < $this->keywordCount; ++$i) {
			$replaced[] = false;
		}
		$this->keywordReplaced = $replaced;

		return $this->replaceLinkAll($address);
	}

	private function replaceAccess($access, $areaId) {
		$Landmark = new Landmark();
		$Station = new Station();
		$Area = new Area();

		$Area->id = $areaId;
		$prefectureId = $Area->field('prefecture_id');

		$airportList = $Landmark->getAirportLinkCd();
		$stationList = $Station->getAllStationListByPrefectureId($prefectureId);

		$keywordList = array_merge($airportList, $stationList);
		// キーワードを長さの降順に処理することで、aタグ多重化を回避
		usort($keywordList, array($this, 'compareKeywords'));

		$this->skipLinkCd = '';
		$this->keywordList = $keywordList;
		$this->keywordCount = count($keywordList);
		$replaced = array();
		for ($i = 0; $i < $this->keywordCount; ++$i) {
			$replaced[] = false;
		}
		$this->keywordReplaced = $replaced;

		return $this->replaceLinkAll($access);
	}

	/**
	 * オフィスIDから営業所とクライアントのリンクコードを返す
	 * @param string $officeId
	 * @return array
	 */
	public function getLinkCdById($officeId) {
		$options = array(
			'fields' => array(
				'Client.url',
				'Office.url',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Office.client_id = Client.id')
				),
			),
			'conditions' => array(
				'Office.id' => $officeId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
			),
			'recursive' => -1
		);

		$result = $this->find('first', $options);

		return $result;
	}

	/**
	 * 駅付近の営業所リストを返す
	 * @param type $station_id
	 * @return type
	 */
	public function getOfficeNearListByStationId($station_id, $clientId = null) {

		$options = array(
			'conditions' => array(
				'Office_station.station_id' => $station_id,
				'Office_station.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'fields' => array(
				'Office.*', 'Client.*',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Office.client_id = Client.id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office_station',
					'table' => 'office_stations',
					'conditions' => array('Office.id = Office_station.office_id')
				),
			),
			'recursive' => -1
		);
		if (!empty($clientId)) {
			$options['conditions']['Office_station.client_id'] = $clientId;
		}

		$result = $this->findC('all', $options);

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	/**
	 * 市区町村の営業所リストを返す
	 * @param type $cityId
	 * @return type
	 */
	public function getOfficeNearListByCityId($cityId) {

		$result = $this->findC('all', array(
			'conditions' => array(
				'Office.city_id' => $cityId,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'fields' => array(
				'Office.id',
				'Office.name',
				'Office.url',
				'Office.address',
				'Office.access_dynamic',
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
				'Office.latitude',
				'Office.longitude',
				'Client.id',
				'Client.name',
				'Client.url',
				'Client.sort',
				'Client.logo_image',
				'Client.sp_logo_image'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Office.client_id = Client.id')
				),
			),
			'order' => 'Client.sort ASC',
			'recursive' => -1
		));

		foreach ($result as &$value) {
			$value['Office']['name'] = $this->addSuffixOfOffice($value['Office']['name']);
		}

		return $result;
	}

	/**
	 * 指定された会社・都道府県の店舗を会社でグループ化して返す
	 * @param array $clientIds
	 * @param type $prefectureId
	 * @return array
	 */
	public function getOfficeListGroupByClientId($clientIds, $prefectureId) {
		$options = array(
			'fields' => array(
				'Office.client_id',
				'Office.id',
				'Office.name',
				'Office.url'
			),
			'conditions' => array(
				'Office.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array(
						'Client.id = Office.client_id',
						'Client.id' => $clientIds,
						'Client.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => array(
						'Area.id = Office.area_id',
						'Area.prefecture_id' => $prefectureId,
						'Area.delete_flg' => 0
					)
				)
			),
			'sort' => array(
				'Office.sort' => 'ASC',
				'Office.id' => 'ASC'
			),
			'recursive' => -1
		);

		$result = $this->findC('all', $options);
		if (!empty($result)) {
			$result = Hash::combine($result, '{n}.Office.id', '{n}.Office', '{n}.Office.client_id');
		}

		return $result;
	}

	/**
	 * 指定された会社・都道府県から同一都道府県内で乗捨て可能な店舗を返す
	 * @param type $clientIds
	 * @param type $prefectureId
	 * @return array
	 */
	public function getDropOffOfficeByClientAndPrefectureId($clientId, $prefectureId) {
		$sql = "
			SELECT DISTINCT
			  ReturnOffice.id,
			  ReturnOffice.name
			FROM
			  rentacar.offices AS RentOffice
			INNER JOIN
			  rentacar.clients AS Client
			    ON Client.id = :clientId AND
			       Client.delete_flg = 0 AND
			       Client.id = RentOffice.client_id
			INNER JOIN
			  rentacar.areas AS RentArea
			    ON RentArea.id = RentOffice.area_id AND
			       RentArea.prefecture_id = :prefectureId AND
			       RentArea.delete_flg = 0
			INNER JOIN
			  rentacar.offices AS ReturnOffice
			    ON ReturnOffice.client_id = Client.id AND
			       ReturnOffice.delete_flg = 0
			INNER JOIN
			  rentacar.areas AS ReturnArea
			    ON ReturnArea.id = ReturnOffice.area_id AND
			       ReturnArea.prefecture_id = :prefectureId AND
			       ReturnArea.delete_flg = 0
			INNER JOIN
			  rentacar.drop_off_areas AS RentDropOffArea
			    ON RentDropOffArea.id = RentOffice.area_drop_off_id AND
			       RentDropOffArea.delete_flg = 0
			INNER JOIN
			  rentacar.drop_off_areas AS ReturnDropOffArea
			    ON ReturnDropOffArea.id = ReturnOffice.area_drop_off_id AND
			       ReturnDropOffArea.delete_flg = 0
			INNER JOIN
			  rentacar.drop_off_area_rates AS DropOffAreaRate
			    ON DropOffAreaRate.rent_drop_off_area_id = RentDropOffArea.id AND
			       DropOffAreaRate.return_drop_off_area_id = ReturnDropOffArea.id AND
			       DropOffAreaRate.delete_flg = 0
			WHERE
			  RentOffice.delete_flg = 0
			ORDER BY
			  ReturnOffice.id
		";
		$params = array(':clientId' => $clientId, ':prefectureId' => $prefectureId);

		$returnOffice = $this->queryC($sql, $params);

		// 2018/08/09 スロークエリ対応
		// 上記SQLの結合テーブルを減らし、あとからCommodityReturnOfficeにレコードあるか見る
		$limited = array();
		if (!empty($returnOffice)) {
			$CommodityReturnOffice = ClassRegistry::init('CommodityReturnOffice');
			foreach ($returnOffice as $v) {
				$returnOfficeExist = $CommodityReturnOffice->findC('first',
					array(
						'fields' => array(
							'CommodityReturnOffice.id',
						),
						'conditions' => array(
							'CommodityReturnOffice.office_id' => $v['ReturnOffice']['id'],
						),
						'recursive' => -1,
					)
				);
				if (!empty($returnOfficeExist)) {
					$limited[] = $v;
					if (count($limited) >= 2) {
						break;
					}
				}
			}
		}

		return empty($limited) ? array() : Hash::combine($limited, '{n}.ReturnOffice.id', '{n}.ReturnOffice.name');
	}

	/**
	 * 指定された会社・都道府県の店舗の最寄り空港のリストを返す
	 * @param type $clientId
	 * @param type $prefectureId
	 * @return array
	 */
	public function getNearestAirportListByClientAndPrefectureId($clientId, $prefectureId) {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				"COUNT('Office.id')"
			),
			'conditions' => array(
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => array(
						'Area.id = Office.area_id',
						'Area.prefecture_id' => $prefectureId,
						'Area.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Landmark',
					'table' => 'landmarks',
					'conditions' => array(
						'Landmark.id = Office.airport_id',
						'Landmark.delete_flg' => 0
					)
				)
			),
			'group' => array(
				'Landmark.id',
			),
			'order' => array(
				"COUNT('Office.id')" => 'DESC',
				'Landmark.sort' => 'ASC',
			),
			'recursive' => -1
		);

		$result = $this->findC('all', $options);

		if (!empty($result)) {
			$result = Hash::extract($result, '{n}.Landmark.name');
		}

		return $result;
	}

	/**
	 * 指定された会社・都道府県の店舗の最寄り駅のリストを返す
	 * @param type $clientId
	 * @param type $prefectureId
	 * @return array
	 */
	public function getNearestStationListByClientAndPrefectureId($clientId, $prefectureId) {
		$options = array(
			'fields' => array(
				'Station.id',
				'Station.name',
				'Station.type',
				"COUNT('OfficeStation.id')"
			),
			'conditions' => array(
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => array(
						'Area.id = Office.area_id',
						'Area.prefecture_id' => $prefectureId,
						'Area.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'OfficeStation',
					'table' => 'office_stations',
					'conditions' => array(
						'OfficeStation.office_id = Office.id',
						'OfficeStation.delete_flg' => 0
					)
				),
				array(
					'type' => 'INNER',
					'alias' => 'Station',
					'table' => 'stations',
					'conditions' => array(
						'Station.id = OfficeStation.station_id',
						'Station.delete_flg' => 0
					)
				)
			),
			'group' => array(
				'Station.id',
			),
			'order' => array(
				"COUNT('OfficeStation.id')" => 'DESC',
				'Station.sort IS NULL' => 'ASC',
				'Station.sort' => 'ASC',
				'Station.major_flg' => 'DESC',
				'Station.id' => 'ASC'
			),
			'recursive' => -1
		);

		$result = $this->findC('all', $options);
		if (!empty($result)) {
			$result = Hash::extract($result, '{n}.Station');
		}

		return $result;
	}

	public function getOfficeCodeAndName($officeId) {
		return $this->findC('first', array(
			'fields' => array(
				'Office.office_code',
				'Office.name',
			),
			'conditions' => array(
				'Office.id' => $officeId,
			),
			'recursive' => -1,
		));
	}

	public function belongToClient($officeIds, $clientId) {
		$count = $this->findC('count', array(
			'conditions' => array(
				'id' => $officeIds,
				'client_id' => $clientId,
			),
			'recursive' => -1,
		));

		return (count($officeIds) == $count);
	}

	public function isAvailable($officeId)
	{
		$count = $this->findC('count', array(
			'conditions' => array(
				'id' => $officeId,
				'delete_flg' => 0,
			),
			'recursive' => -1,
		));

		return ($count == 1);
	}
}

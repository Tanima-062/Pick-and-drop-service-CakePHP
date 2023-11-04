<?php
App::uses('AppModel', 'Model');
App::uses('OfficeSelectionPermission', 'Model');
/**
 * Office Model
 *
 * @property Client $Client
 * @property Staff $Staff
 * @property Contract $Contract
 * @property Distance $Distance
 * @property OfficeArea $OfficeArea
 * @property OfficeStockGroup $OfficeStockGroup
 */
class Office extends AppModel {

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
		'url' => array(
			'isnotuniquelinkcd' => array(
				'rule' => array('isNotUniqueLinkCd'),
				'message' => '登録済みのリンク用URLです',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'tel' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '電話番号は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '住所は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'reserve_mail' => array(
				/*
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '予約完了通知メールアドレスは必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'email' => array(
				'rule' => array('email'),
				'message' => '予約完了通知メールアドレスの形式が間違っています',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			*/
		),
		'reserve_mail2' => array(
		),
		'reserve_mail3' => array(
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
		'OfficeSupplement' => array(
			'className' => 'OfficeSupplement',
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
		)
	);

	public function isNotUniqueLinkCd($data) {
		$ret = $this->find('count', array(
			'conditions' => array(
				'url' => $data['url'],
				'client_id' => $this->data[$this->name]['client_id'],
				'id != ' => $this->data[$this->name]['id'],
			),
			'recursive' => -1,
		));

		return empty($ret);
	}

	public function beforeFind($queryData) {

		//ログインしているユーザーの情報を取得
		$clientData = $this->_getCurrentUser();

		//管理者ではない場合
		if(empty($clientData['is_system_admin']) && !isset($queryData['conditions']['Office.id <>']) && !isset($queryData['conditions']['Office.id']) ) {

			$this->OfficeSelectionPermission = new OfficeSelectionPermission();

			//ユーザーが選択可能なOfficeを取得
			$officeIdList = $this->OfficeSelectionPermission->getPermissionOfficeList($clientData['id']);

			//条件を追加
			if(empty($queryData['conditions'])) {
				$queryData['conditions'] = array();
			}
			$queryData['conditions'] += array('Office.id'=> $officeIdList);

			return $queryData;
		}
	}

	public function afterSave($created) {

		parent::afterSave($created);

		//ログインしているユーザーの情報を取得
		$clientData = $this->_getCurrentUser();
		if($created) {
			echo $officeId = $this->getLastInsertID();
			if(empty($this->idList)) {
				$this->idList = array();
			}
			$this->idList[$officeId]['office_id'] = $officeId;
			$this->idList[$officeId]['staff_id'] = $clientData['id'];
		}
		return true;
	}

	public function getOfficeFirst($id) {

		$options = array(
				'conditions' => array(
						'delete_flg' => false,
						'id' => $id
				),
				'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getOfficeLists($clientId) {

		$options = array(
				'conditions' => array(
						'client_id' => $clientId,
						'delete_flg' => 0
				),
		    'order'=>'sort asc',
				'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function saveOfficeImage($file, $upLoadDir) {
		return $this->imageResizeUp($file, $upLoadDir);
	}

	public function saveOfficePdf($file) {
		return $this->savePdf($file,'hotel_pdf');
	}

	public function getAllOffice() {

		$options = array(
				'conditions' => array(
						'delete_flg' => 0,
						'latitude IS NOT NULL',
						'longitude IS NOT NULL',
						'latitude >' => 0,
						'longitude >' => 0,
				),
				'recursive' => -1
		);

		return $this->find('all',$options);

	}

	/**
	 * csv出力に必要な営業所をを取得
	 * 過去の営業所のデータも取得しないといけないので、delete_flgは条件に加えない。
	 */
	public function getCsvOfficeList($clientId) {
		$options = array(
				'conditions' => array(
						'Office.client_id' => $clientId,
				),
				'recursive' => -1,
		);
		return $this->find('list', $options);
	}

	public function getAllList($clientId = '') {

		$options = array(
				'Office.delete_flg'=>0,
				'recursive' => -1
		);

		if(!empty($clientId)) {
			$options['conditions']['Office.client_id'] = $clientId;
		}

		$offices = $this->find('all',$options);
		$result = array();
		foreach ($offices as $office) {

			$result[$office['Office']['id']] = $office['Office'];
		}

		return $result;
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

		$this->PublicHoliday = ClassRegistry::init('PublicHoliday');

		//祝日判定 曜日の識別子を取得
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
					office_business_hours.delete_flg = 0";

		$this->recursive = -1;
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
						)
		);
		return array(
				'start_time'=>$officeInfo[0]['office_hours_from'],
				'end_time'=>$officeInfo[0]['office_hours_to'],
		);

	}

	/**
	 * 編集・削除対象のデータが該当クライアントのものかチェックする
	 *
	 */
	public function clientCheck($id,$clientId) {

		$this->recursive = -1;
		$count = $this->find('count',array('conditions'=>array('id'=>$id,'client_id'=>$clientId)));
		if(empty($count)) {
			return false;
		}

		return true;

	}

	/**
	 * 営業所の交通アクセスを動的に生成して返す
	 */
	public function getAccessDynamic($nearestTransport, $id, $otherTransport, $methodOfTransport, $requiredTransportTime, $fromBatch = false) {
		switch ($nearestTransport) {
			case 0:
				if ($fromBatch) {
					$sql = '
						SELECT
						  rl.name
						FROM
						  rentacar.offices ro
						INNER JOIN
						  rentacar.landmarks rl
						    ON rl.id = ro.airport_id AND
						       rl.delete_flg = 0
						WHERE
						  ro.id = :id
					';
				} else {
					$sql = '
						SELECT
						  rl.name
						FROM
						  rentacar.landmarks rl
						WHERE
						  rl.id = :id AND
						  rl.delete_flg = 0
					';
				}
				$ret = $this->query($sql, array('id' => $id));
				$nearestTransportName = empty($ret) ? '最寄り空港' : $ret[0]['rl']['name'];
				break;
			case 1:
				if ($fromBatch) {
					$sql = '
						SELECT
						  rs.name,
						  rs.type
						FROM
						  rentacar.office_stations ros
						INNER JOIN
						  rentacar.stations rs
						    ON rs.id = ros.station_id AND
						       rs.delete_flg = 0
						WHERE
						  ros.office_id = :id AND
						  ros.delete_flg = 0
						ORDER BY
						  ros.id
						LIMIT 1
					';
				} else {
					$sql = '
						SELECT
						  rs.name,
						  rs.type
						FROM
						  rentacar.stations rs
						WHERE
						  rs.id = :id AND
						  rs.delete_flg = 0
					';
				}
				$ret = $this->query($sql, array('id' => $id));
				if (empty($ret)) {
					$nearestTransportName = '最寄り駅';
				} else {
					$station = $ret[0]['rs'];
					$nearestTransportName = $station['name'] . ($station['type'] == 0 ? '駅' : '停留場');
				}
				break;
			case 2:
				$nearestTransportName = $otherTransport;
				break;
		}
		if (!(isset($nearestTransportName) && !empty($nearestTransportName))) {
			$nearestTransportName = '最寄り交通機関';
		}

		$byYourself = false;
		$optionalTransfer = false;
		switch ($methodOfTransport) {
			case 0:
				$method = '徒歩';
				$byYourself = true;
				break;
			case 1:
				$method = '無料送迎車';
				break;
			case 2:
				$method = '有料送迎車';
				break;
			case 3:
				$method = '車';
				$byYourself = true;
				break;
			case 4:
				$method = '徒歩';
				// 歩けるけど、送って欲しければ送るよ？的な感じ
				$optionalTransfer = true;
				break;
		}

		return $nearestTransportName . 'より' . $method . 'で約' . $requiredTransportTime . '分' . ($byYourself ? '（送迎なし）' : ($optionalTransfer ? '（送迎あり）' : ''));
	}

	public function getOfficeToPrefecture($clientId = '', $prefectureId = '') {

		if (empty($clientId)) {
			return array();
		}

		$options = array(
			'fields' => array(
				'Office.id',
				'Prefecture.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'areas',
					'alias' => 'Area',
					'conditions' => 'Area.id = Office.area_id',
				),
				array(
					'type' => 'INNER',
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'conditions' => 'Prefecture.id = Area.prefecture_id',
				),
			),
			'conditions' => array(
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		if(!empty($prefectureId)) {
			$options['conditions']['Area.prefecture_id'] = $prefectureId;
		}

		$offices = $this->find('all',$options);
		$result = array();
		foreach ($offices as $office) {

			$result[$office['Office']['id']] = $office['Prefecture']['id'];
		}

		return $result;
	}
}

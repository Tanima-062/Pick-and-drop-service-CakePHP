<?php
App::uses('AppModel', 'Model');
/**
 * Landmark Model
 *
 * @property LandmarkCategory $LandmarkCategory
 * @property Area $Area
 * @property Staff $Staff
 * @property Distance $Distance
 * @property LandmarkDescription $LandmarkDescription
 */
class Landmark extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'landmark_category_id' => array(
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
			'notblank' => array(
				'rule' => array('notblank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'link_cd' => array(
			'isunique' => array(
				'rule' => array('isunique'),
				'message' => '登録済みのリンク用URLです',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'airport_id' => array(
			'requiredAirportItem' => array(
				'rule' => array('requiredAirportItem'),
				'message' => '空港の場合は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isunique' => array(
				'rule' => array('isunique'),
				'message' => '登録済みの空港IDです',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'iata_cd' => array(
			'requiredAirportItem' => array(
				'rule' => array('requiredAirportItem'),
				'message' => '空港の場合は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'isunique' => array(
				'rule' => array('isunique'),
				'message' => '登録済みのIATAコードです',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'existsAirport' => array(
				'rule' => array('existsAirport'),
				'message' => '航空券側の空港マスタに存在しない空港です',
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
		'LandmarkCategory' => array(
			'className' => 'LandmarkCategory',
			'foreignKey' => 'landmark_category_id',
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
		// 'Distance' => array(
		// 	'className' => 'Distance',
		// 	'foreignKey' => 'landmark_id',
		// 	'dependent' => false,
		// 	'conditions' => '',
		// 	'fields' => '',
		// 	'order' => '',
		// 	'limit' => '',
		// 	'offset' => '',
		// 	'exclusive' => '',
		// 	'finderQuery' => '',
		// 	'counterQuery' => ''
		// ),
		// 'LandmarkDescription' => array(
		// 	'className' => 'LandmarkDescription',
		// 	'foreignKey' => 'landmark_id',
		// 	'dependent' => false,
		// 	'conditions' => '',
		// 	'fields' => '',
		// 	'order' => '',
		// 	'limit' => '',
		// 	'offset' => '',
		// 	'exclusive' => '',
		// 	'finderQuery' => '',
		// 	'counterQuery' => ''
		// ),
	);

	// validation用
	public function requiredAirportItem($data) {
		if ($this->data[$this->name]['landmark_category_id'] == '1') {
			return !empty($data[key($data)]);
		}
		return true;
	}

	// validation用
	public function existsAirport($data) {
		if ($this->data[$this->name]['landmark_category_id'] != '1') {
			return true;
		}

		$params = array(
			'airport_id' => $this->data[$this->name]['airport_id']
		);

		$sql = "SELECT iata_cd FROM common.cm_tm_airport"
			 . " WHERE airport_id = :airport_id AND da_flg = 1 AND delete_flg = 0";

		$ret = $this->query($sql, $params);

		return (!empty($ret[0]['cm_tm_airport']['iata_cd']) && $ret[0]['cm_tm_airport']['iata_cd'] == $data['iata_cd']);
	}

	public function beforeValidate($options = array()){
		parent::beforeValidate($options);
		
		// ソート時はセットされていない
		if (!isset($this->data[$this->name]['landmark_category_id'])) {
			return true;
		}
		
		$category_id = $this->data[$this->name]['landmark_category_id'];
		
		// 空港以外の場合は特定の項目を使用しない
		if ($category_id != '1') {
			unset($this->data[$this->name]['airport_id']);
			unset($this->data[$this->name]['iata_cd']);
			// 新幹線駅のみ変換後の駅IDが入っているので残す
			if ($category_id != '2') {
				unset($this->data[$this->name]['travelko_id']);
			}
		}

		return true;
	}


	public function getPrefectureAirportList($cacheConfig = '1hour') {
		
		$prefectures = $this->findC('all', array(
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'Landmark.landmark_category_id' => 1
			),
			'joins' => array(
				array(
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => array(
						'Prefecture.id = Landmark.prefecture_id',
						'Prefecture.delete_flg = 0'
					)
				)
			),
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.landmark_category_id',
				'Prefecture.name'
			),
			'recursive' => - 1,
			'order' => array(
				'Prefecture.id', 'Landmark.sort',
			),
		),$cacheConfig);

		$landMarkArray = array();
		
		foreach ($prefectures as $val) {
			//都道府県がなければcontinue
			if (empty($val['Prefecture']['name'])) {
				continue;
			}

			$prefectureName = $val['Prefecture']['name'];
			$landmarkId = $val['Landmark']['id'];
			$landmarkArray[$prefectureName][$landmarkId] = $val['Landmark']['name'];
		}

		return $landmarkArray;
	}

	public function getAirportList() {
		return $this->find('all', array(
			'conditions' => array(
				'Landmark.landmark_category_id' => 1,
				'Landmark.delete_flg' => 0
			),
			'order' => array(
				'Landmark.sort' => 'ASC'
			),
			'recursive' => -1
		));
	}
}

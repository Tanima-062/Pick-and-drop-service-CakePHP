<?php
App::uses('AppModel', 'Model');
/**
 * Station Model
 *
 * @property StationCategory $StationCategory
 * @property Area $Area
 * @property Staff $Staff
 * @property Distance $Distance
 * @property StationDescription $StationDescription
 */
class Station extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getPrefectureMainStationList($cacheConfig = '1hour') {
		
		$prefectures = $this->findC('all', array(
			'conditions' => array(
				'Station.delete_flg' => 0,
				'Station.major_flg' => 1
			),
			'joins' => array(
				array(
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => array(
						'Prefecture.id = Station.prefecture_id',
						'Prefecture.delete_flg = 0'
					)
				)
			),
			'fields' => array(
				'Station.id',
				'Station.name',
				'Prefecture.name'
			),
			'recursive' => - 1,
			'order' => array(
				'Prefecture.id','Station.sort',
			),
		),$cacheConfig);

		$stationArray = array();
		
		foreach ($prefectures as $val) {
			//都道府県がなければcontinue
			if (empty($val['Prefecture']['name'])) {
				continue;
			}

			$prefectureName = $val['Prefecture']['name'];
			$stationId = $val['Station']['id'];
			$stationArray[$prefectureName][$stationId] = $val['Station']['name'];
		}

		return $stationArray;
	}
}

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
		'LandmarkCategory' => array(
			'className' => 'LandmarkCategory',
			'foreignKey' => 'landmark_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Area' => array(
			'className' => 'Area',
			'foreignKey' => 'area_id',
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
		'Distance' => array(
			'className' => 'Distance',
			'foreignKey' => 'landmark_id',
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
		'LandmarkDescription' => array(
			'className' => 'LandmarkDescription',
			'foreignKey' => 'landmark_id',
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


	public function getAllLandmark() {

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

	public function getAirportAndBulletTrainArrayList() {

		$landmarks = $this->find('all',array(
				'conditions'=>array(
						'Landmark.landmark_category_id'=>array(1,2),
						'Landmark.delete_flg'=>0
				),
				'joins'=>array(
						array(
								'type'=>'LEFT',
								'alias'=>'BulletTrainArea',
								'table'=>'bullet_train_areas',
								'conditions'=>'BulletTrainArea.id = Landmark.bullet_train_area_id'
						),
						array(
								'type'=>'LEFT',
								'alias'=>'Prefecture',
								'table'=>'prefectures',
								'conditions'=>'Prefecture.id = Landmark.prefecture_id'
						),
				),
				'fields'=>array(
						'Landmark.*','BulletTrainArea.*,Prefecture.*'
				),
				'recursive'=>-1
				)
			);

		$landMarkArray = array();
		foreach($landmarks as $val) {
			if($val['Landmark']['landmark_category_id'] == 1) {
				$prefectureName = $val['Prefecture']['name'];
				$landmarkId = $val['Landmark']['id'];
				$landmarkArray['airportArray'][$prefectureName][$landmarkId] = $val['Landmark']['name'];
			} else if($val['Landmark']['landmark_category_id'] == 2) {
				$bulletTrainAreaName = $val['BulletTrainArea']['name'];
				$landmarkId = $val['Landmark']['id'];
				$landmarkArray['bulletTrainArray'][$bulletTrainAreaName][$landmarkId] = $val['Landmark']['name'];
			}
		}

		return $landmarkArray;

	}

	public function getAllLandmarks() {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'LandmarkCategory',
					'table' => 'landmark_categories',
					'conditions' => 'LandmarkCategory.id = Landmark.landmark_category_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => 'Prefecture.id = Landmark.prefecture_id',
				),
			),
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'LandmarkCategory.delete_flg' => 0,
				'Prefecture.delete_flg' => 0,
			),
			'order' => array(
				'Prefecture.sort',
				'Prefecture.id',
				'Landmark.sort',
				'Landmark.id',
			),
			'recursive' => -1,
		);

		return $this->find('list', $options);
	}
}

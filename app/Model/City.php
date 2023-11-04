<?php
App::uses('AppModel', 'Model');
/**
 * City Model
 *
 * @property Prefecture $Prefecture
 * @property Staff $Staff
 */
class City extends AppModel {

	protected $cacheConfig = '1day';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'prefecture_id' => array(
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
		/*'travelko_city_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),*/
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
		'Prefecture' => array(
			'className' => 'Prefecture',
			'foreignKey' => 'prefecture_id',
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

	public function getCityListWithAreaByPrefectureId($prefectureId) {
		$options = array(
			'fields'=>array(
				'City.id',
				'City.name',
				'City.link_cd',
				'Area.id',
				'Area.name',
				'Area.area_link_cd'
			),
			'conditions' => array(
				'City.prefecture_id' => $prefectureId,
				'City.delete_flg' => 0,
				'Area.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Area',
					'table'=>'areas',
					'conditions'=>'Area.id = City.area_id'
				)
			),
			'order' => array(
				'Area.sort' => 'ASC',
				'City.id' => 'ASC'
			),
			'recursive' => -1
		);

		return $this->findC('all', $options);
	}

	public function getCityByCityLinkCd($linkCd) {
		$option = array(
			'fields' => array(
				'City.id',
				'City.prefecture_id',
				'City.name',
				'City.area_id',
				'Prefecture.name'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'conditions' => 'Prefecture.id = City.prefecture_id'
				)
			),
			'conditions' => array(
				'City.link_cd' => $linkCd,
				'City.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->findC('all', $option);
	}

	public function getCitiesByAreaId($areaId) {
		$option = array(
			'fields' => array(
				'Prefecture.region_link_cd',
				'Prefecture.link_cd',
				'City.id',
				'City.name',
				'City.link_cd'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'conditions' => 'Prefecture.id = City.prefecture_id'
				),
			),
			'conditions' => array(
				'City.area_id' => $areaId,
				'City.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->findC('all', $option);
	}

	// 内部リンク変換用
	public function getAllCityListByPrefectureId($prefectureId) {

		$conditions = array(
			'City.prefecture_id' => $prefectureId,
			'City.delete_flg' => 0
		);

		$result = $this->findC('all', array(
			'conditions' => $conditions,
			'fields' => array(
				'City.name',
				'City.link_cd',
				'Prefecture.region_link_cd',
				'Prefecture.link_cd'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'conditions' => 'Prefecture.id = City.prefecture_id'
				)
			),
			'recursive' => -1
		));

		$combined = array();
		if (!empty($result)) {
			foreach ($result as $data) {
				$name = $data['City']['name'];
				$regionLinkCd = str_replace('area_', '', $data['Prefecture']['region_link_cd']);
				$url = '/rentacar/' . $regionLinkCd . '/';
				if ($regionLinkCd !== $data['Prefecture']['link_cd']) {
					$url .= $data['Prefecture']['link_cd'] . '/';
				}
				$url .= $data['City']['link_cd'] . '/';
				$combined[] = array(
					'name' => $name,
					'url' => $url,
					'link_cd' => $data['City']['link_cd'],
					'length' => mb_strlen($name)
				);
			}
		}

		return $combined;
	}
}

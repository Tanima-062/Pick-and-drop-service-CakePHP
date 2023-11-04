<?php
App::uses('AppModel', 'Model');
/**
 * Area Model
 *
 * @property Staff $Staff
 * @property AreaStockGroup $AreaStockGroup
 * @property Landmark $Landmark
 */
class Area extends AppModel {

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
		'sort' => array(
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
		'AreaStockGroup' => array(
			'className' => 'AreaStockGroup',
			'foreignKey' => 'area_id',
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
		'Landmark' => array(
			'className' => 'Landmark',
			'foreignKey' => 'area_id',
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

	public function getAreaList() {
		$this->recurcive = -1;
		return $this->find('list',array('conditions'=>array('delete_flg'=>0)));
	}

	public function getPrefectureAreaList() {
		$this->recurcive = -1;
		return $this->find('list',
			array(
				'conditions'=>array(
					'Area.delete_flg'=>0
				),
				'joins'=>array(
					array(
						'type'=>'LEFT',
						'alias'=>'Prefecture',
						'table'=>'prefectures',
						'conditions'=>'Prefecture.id = Area.prefecture_id',
					)
				),
				'fields'=>array(
					'Area.id',
					'Area.name',
					'Prefecture.name'
				),
				'order'=>'Area.sort,Prefecture.sort'
			)
		);
	}

	public function getPrefectureIdByAreaId($areaId) {
		return $this->find('first',
			array(
				'fields' => array(
					'Prefecture.id',
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'prefectures',
						'alias' => 'Prefecture',
						'conditions' => 'Prefecture.id = Area.prefecture_id',
					),
				),
				'conditions' => array(
					'Area.id' => $areaId,
				),
				'recursive' => -1,
			)
		);
	}
}

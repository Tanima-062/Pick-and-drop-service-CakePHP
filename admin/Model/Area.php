<?php
App::uses('AppModel', 'Model');
/**
 * Area Model
 *
 * @property Staff $Staff
 * @property Landmark $Landmark
 * @property Office $Office
 * @property Office $Office
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
				'message' => 'エリア名は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'area_link_cd' => array(
			'isunique' => array(
				'rule' => array('isunique'),
				'message' => '登録済みのリンク用URLです',
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
		),
		'Prefecture' => array(
			'className' => 'Prefecture',
			'foreignKey' => 'prefecture_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
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
		),
		'Office' => array(
			'className' => 'Office',
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


/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Office' => array(
			'className' => 'Office',
			'joinTable' => 'office_areas',
			'foreignKey' => 'area_id',
			'associationForeignKey' => 'office_id',
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

	public function getAreaList() {
		return $this->find('list',array('conditions'=>array('delete_flg'=>0)));
	}


	public function getPrefectureKeyAreaList($cacheConfig = '1hour') {
		// 全都道府県のエリア取得
		$ret = array();

		$options = array(
			'fields' => array('id', 'name', 'prefecture_id'),
			'conditions' => array('delete_flg' => 0),
			'order' => array('sort' => 'asc'),
			'recursive' => -1
		);

		$prefecture_list = $this->findC('list', $options, $cacheConfig);
		
		foreach ($prefecture_list as $pref_k => $pref_v) {
			foreach ($pref_v as $k => $v) {
				$ret[$pref_k][$k] = $v;
			}
		}
		
		ksort($ret);

		return $ret;
	}
}

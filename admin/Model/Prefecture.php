<?php
App::uses('AppModel', 'Model');
/**
 * Prefecture Model
 *
 * @property Staff $Staff
 * @property Reservation $Reservation
 */
class Prefecture extends AppModel {

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
		'link_cd' => array(
			'isunique' => array(
				'rule' => array('isUnique'),
				'message' => 'リンク用URLが重複しています。',
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

	public function getPrefectureList() {
		return $this->find('list',array('conditions'=>array('delete_flg'=>0),'order'=>('Prefecture.sort')));
	}

	public function getRecommendPrefectureList() {
		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'OR' => array(
					'recommend_random_flg is null',
					'recommend_random_flg' => 0
				)
			),
			'order' => ('sort')
		);
		return $this->find('list', $options);
	}

	public function getRecommendRandomPrefectureList() {
		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'recommend_random_flg' => 1
			),
			'order' => ('sort')
		);
		return $this->find('list', $options);
	}

}

<?php

App::uses('AppModel', 'Model');

/**
 * CarType Model
 *
 * @property Staff $Staff
 * @property CarClass $CarClass
 */
class CarType extends AppModel {

	protected $cacheConfig = '1day';

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
		'description' => array(
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
		'CarClass' => array(
			'className' => 'CarClass',
			'foreignKey' => 'car_type_id',
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

	public function getCarTypeInfo() {

		$options = array(
			'fields' => array(
				'id',
				'name',
				'description',
			),
			'conditions' => array(
				'delete_flg' => false
			),
			'order' => array(
				'sort',
				'id',
			),
			'recursive' => -1,
		);

		return $this->findC('all', $options);
	}

	public function getCarTypeList() {

		$options = array(
			'fields' => array(
				'id',
				'name',
			),
			'conditions' => array(
				'delete_flg' => false
			),
			'order' => array(
				'sort',
				'id',
			),
			'recursive' => -1,
		);

		return $this->findC('list', $options);
	}

	public function getCarTypeIdByPersonCount($personCount = 2) {

		return $this->findC('list', array(
			'conditions' => array(
				'capacity >=' => $personCount,
				'delete_flg' => 0,
			),
			'fields' => array('CarType.id', 'CarType.id')
		));
	}

}

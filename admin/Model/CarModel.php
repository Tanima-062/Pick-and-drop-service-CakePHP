<?php
App::uses('AppModel', 'Model');
/**
 * CarModel Model
 *
 * @property Automaker $Automaker
 * @property Staff $Staff
 */
class CarModel extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'automaker_id' => array(
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
				'rule' => array('notblank'),
				'message' => '車種名は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'trunk_space' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'スーツケースは必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'golf_bag' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'ゴルフバッグは必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'displacement' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => '排気量は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'image_relative_url' => array(
			//'notempty' => array(
				//'rule' => array('notblank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			//),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notblank'),
				'message' => '説明は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'capacity' => array(
				'notempty' => array(
						'rule' => array('notblank'),
						'message' => '法定定員は必須です',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
		),
		'recommended_capacity' => array(
				'notempty' => array(
						'rule' => array('notblank'),
						'message' => '推奨定員は必須です',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
		),
		'package_num' => array(
				'notempty' => array(
						'rule' => array('notblank'),
						'message' => '推奨荷物の数は必須です',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
		),
		'mileage' => array(
					'notempty' => array(
							'rule' => array('notblank'),
							'message' => '燃費は必須です',
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
		'Automaker' => array(
			'className' => 'Automaker',
			'foreignKey' => 'automaker_id',
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

	public function getCarModel($automakerId) {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
				'automaker_id' => $automakerId,
			),
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

}

<?php
App::uses('AppModel', 'Model');
/**
 * CancelReason Model
 *
 * @property Staff $Staff
 */
class CancelReason extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'reason' => array(
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

	public function getCancelReasonList() {

		return $this->find('list',
				array('fields' =>array('id','reason'),
						'conditions' => array('delete_flg' => 0,'staff_id' => 0),
						'order' => array(
								'FIELD(CancelReason.id,1,2,3,4,5)'
						),
						'recursive' => -1
				)
		);
	}
}

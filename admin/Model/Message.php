<?php
App::uses('AppModel', 'Model');
/**
 * New Message
 *
 * @property Staff $Staff
 */
class Message extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'ユーザが不正です',
			),
		),
		'modified_staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'ユーザが不正です',
			),
		),
		'title' => array(
			'notempty' => array(
				'rule' => array('notblank'),
				'message' => 'タイトルは必須です',
			),
		),
		'message' => array(
			'notempty' => array(
				'rule' => array('notblank'),
				'message' => '本文は必須です',
			),
		),
		'from_time' => array(
			'datetime' => array(
	            'rule' => array('datetime'),
	            'message' => '不正な開始日時です',
	            'allowEmpty' => false
        	),
        	'datetime2' => array(
	            'rule' => array('checkDate'),
	            'message' => '期間が不正です',
        	),
		),
		'to_time' => array(
			'datetime' => array(
	            'rule' => array('datetime'),
	            'message' => '不正な終了日時です',
	            'allowEmpty' => false
        	)
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
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
		'ModifiedStaff' => array(
			'className' => 'Staff',
			'foreignKey' => 'modified_staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function checkDate($check){
		$from_time = $this->data['Message']['from_time'];
		$to_time = $this->data['Message']['to_time'];

		return strtotime($from_time) < strtotime($to_time);
	}
}

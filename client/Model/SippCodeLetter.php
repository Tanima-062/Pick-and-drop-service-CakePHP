<?php
App::uses('AppModel', 'Model');
/**
 * SippCodeLetter Model
 *
 * @property Staff $Staff
 */
class SippCodeLetter extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
//	public $validate = array(
//		'name' => array(
//			'notempty' => array(
//				'rule' => array('notempty'),
//				//'message' => 'Your custom message here',
//				//'allowEmpty' => false,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//		'sort' => array(
//			'numeric' => array(
//				'rule' => array('numeric'),
//				//'message' => 'Your custom message here',
//				//'allowEmpty' => false,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//		'staff_id' => array(
//			'numeric' => array(
//				'rule' => array('numeric'),
//				//'message' => 'Your custom message here',
//				//'allowEmpty' => false,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//		'delete_flg' => array(
//			'boolean' => array(
//				'rule' => array('boolean'),
//				//'message' => 'Your custom message here',
//				//'allowEmpty' => false,
//				//'required' => false,
//				//'last' => false, // Stop validation after this rule
//				//'on' => 'create', // Limit validation to 'create' or 'update' operations
//			),
//		),
//	);

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

	// マスタの内容を全て取得する
	public function getAllLetters() {
		$ret = $this->find('all', array(
			'fields' => array(
				'letter_number',
				'letter',
				'name',
				'description',
			),
			'conditions' => array(
				'delete_flg' => false,
			),
			'recursive' => -1,
		));
		return $ret;
	}

	// SIPPコードのレターリストを取得する
	public function getSippCodeLettersList() {
		$letters = $this->getAllLetters();

		if (empty($letters)) {
			return array();
		}

		$ret = array();

		for ($i = 0; $i < 4; $i++) {
			$matcher = '{n}.SippCodeLetter[letter_number=' . ($i + 1) . ']';
			$ret[$i] = Hash::combine($letters, $matcher . '.letter', $matcher . '.name');
		}

		return $ret;
	}

}

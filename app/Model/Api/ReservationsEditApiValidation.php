<?php
App::uses('AppModel', 'Model');

class ReservationsEditApiValidation extends AppModel {

	/**
	 * Use table
	 *
	 * @var mixed False or table name
	 */
	public $useTable = false;

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'tel' => array(
			array(
				'rule' => 'notblank',
				'message' => '電話番号は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('custom', '/^[0-9]+$/'),
				'message' => '電話番号が正しくありません',
				'last' => false,
			),
		),
		'editInfo' => array(
			array(
				'rule' => array('email', 'editInfo', 'email'),
				'message' => 'メールアドレスが正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('regex', 'editInfo', 'tel', '/^[0-9]+$/'),
				'message' => '電話番号が正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('number', 'editInfo', 'adultCount'),
				'message' => '大人人数が正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('number', 'editInfo', 'childCount', true),
				'message' => '子供人数が正しくありません',
				'last' => false,
			),
		),
	);

	// バリデーションを配列の階層を掘って出来ないのでカスタム関数化
	public function email($check, $key1, $key2) {
		return !isset($check[$key1][$key2]) || Validation::email($check[$key1][$key2]);
	}
	public function regex($check, $key1, $key2, $regex) {
		return !isset($check[$key1][$key2]) || preg_match($regex, $check[$key1][$key2]);
	}
	public function number($check, $key1, $key2, $allowZero = false) {
		return !isset($check[$key1][$key2]) || Validation::naturalNumber($check[$key1][$key2], $allowZero);
	}
}

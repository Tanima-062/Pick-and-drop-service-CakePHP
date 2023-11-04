<?php
App::uses('AppModel', 'Model');

class ReservationsApiValidation extends AppModel {

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
		'userId' => array(
			'rule' => 'naturalNumber',
			'message' => 'ユーザーIDが正しくありません',
			'last' => false,
		),
		'cmApplicationId' => array(
			'rule' => 'naturalNumber',
			'message' => '申込番号が正しくありません',
			'last' => false,
		),
		'currency' => array(
			'rule' => array('custom', '/^[A-Za-z]+$/'),
			'message' => '通貨が正しくありません',
			'last' => false,
		),
		'ipAddress' => array(
			'rule' => 'ip',
			'message' => 'IPアドレスが正しくありません',
			'last' => false,
		),
		'advertisingCd' => array(
			'rule' => array('custom', '/^[0-9A-Za-z_]+$/'),
			'message' => '広告コードが正しくありません',
			'last' => false,
		),
		'userInfo' => array(
			array(
				'rule' => array('blank', 'userInfo', 'lastName'),
				'message' => '性は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('kana', 'userInfo', 'lastName'),
				'message' => '性が正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('blank', 'userInfo', 'firstName'),
				'message' => '名は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('kana', 'userInfo', 'firstName'),
				'message' => '名が正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('blank', 'userInfo', 'email'),
				'message' => 'メールアドレスは必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('email', 'userInfo', 'email'),
				'message' => 'メールアドレスが正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('blank', 'userInfo', 'tel'),
				'message' => '電話番号は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('regex', 'userInfo', 'tel', '/^[0-9]+$/'),
				'message' => '電話番号が正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('blank', 'userInfo', 'adultCount'),
				'message' => '大人人数は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('number', 'userInfo', 'adultCount'),
				'message' => '大人人数が正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('blank', 'userInfo', 'childCount'),
				'message' => '子供人数は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('number', 'userInfo', 'childCount', true),
				'message' => '子供人数が正しくありません',
				'last' => false,
			),
			array(
				'rule' => array('blank', 'userInfo', 'infantCount'),
				'message' => '幼児人数は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('number', 'userInfo', 'infantCount', true),
				'message' => '幼児人数が正しくありません',
				'last' => false,
			),
		),
	);

	// バリデーションを配列の階層を掘って出来ないのでカスタム関数化
	public function blank($check, $key1, $key2) {
		return Validation::notBlank($check[$key1][$key2]);
	}
	public function kana($check, $key1, $key2) {
		return preg_match('/^[ァ-ヶー]+$/u', $check[$key1][$key2]);
	}
	public function email($check, $key1, $key2) {
		return Validation::email($check[$key1][$key2]);
	}
	public function regex($check, $key1, $key2, $regex) {
		return preg_match($regex, $check[$key1][$key2]);
	}
	public function number($check, $key1, $key2, $allowZero = false) {
		return Validation::naturalNumber($check[$key1][$key2], $allowZero);
	}
}

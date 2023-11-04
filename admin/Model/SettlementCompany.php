<?php
App::uses('AppModel', 'Model');

class SettlementCompany extends AppModel {

	public $belongsTo = [
		'Staff' => [
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Client' => [
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];

	public $validate = [
		'accounting_code' => [
			'rule' => 'oriAlphaNumeric',
			'message' => '文字と数字だけで入力してください',
			'allowEmpty' => false
		],
		'invoice_number' => [
			'rule' => 'invoiceNumberCheck',
			'message' => '先頭にTと13桁の数字で入力してください',
			'allowEmpty' => true
		],
		'account_holder' => [
			'rule' => 'kana',
			'message' => '全角数字、全角大文字英字、全角記号（），．「」－、カタカナで入力してください',
			'allowEmpty' => true
		],
		'billing_email1' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'required' => true
		],
		'billing_email2' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email3' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email4' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email5' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email6' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email7' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email8' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email9' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
		'billing_email10' => [
			'rule' => 'email',
			'message' => 'メールアドレスを入力してください',
			'allowEmpty' => true
		],
	];

	public function beforeValidate() {
		// 最初と最後の半角・全角スペースをtrimする
		array_walk( $this->data[$this->name], function( &$item) {
            $item = preg_replace( '/^[ 　]+/u', '', $item);
			$item = preg_replace( '/[ 　]+$/u', '', $item);
        });
	}

	/**
	 * alphaNumericが効かないので自作
	 */
	public function oriAlphaNumeric($check)
	{
		return preg_match('/^[a-zA-Z0-9]+$/', $check[key($check)]) ? true : false;
	}

	/*
	 * カタカナのチェック(間にスペースを許す)
	 */
	public function kana($check)
	{
		return preg_match("/^[（），．「」－Ａ-Ｚ０-９ァ-ヾ 　]+$/u", $check[key($check)]) ? true: false;
	}

	/**
	 * インボイス登録番号用
	 */
	public function invoiceNumberCheck($check)
	{
		return preg_match('/^T[0-9]{13}$/', $check[key($check)]) ? true : false;
	}
}

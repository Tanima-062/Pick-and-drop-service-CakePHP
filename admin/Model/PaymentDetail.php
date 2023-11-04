<?php
App::uses('AppModel', 'Model');
/**
 * PaymentDetail Model
 */
class PaymentDetail extends AppModel {
	public $validate = [
		'account_code' => [
			'rule' => ['custom', '/^[A-Z_]+$/'],
			'message' => '科目名を選択してください。',
			'required' => true
		],
		'amount' => [
			'rule' => ['custom', '/^[-]?[0-9]+$/'],
			'message' => '半角数字0-9までの数値を入力してください。',
			'required' => true
		],
		'count' => [
			'rule' => ['custom', '/^[0-9]+$/'],
			'message' => '半角数字0-9までの数値を入力してください。',
			'required' => true
		],
		'account_and_amount_check' => [
			'check1' => [
				'rule' => 'account_and_amount_check1',
				'message' => '減額調整の単価は0未満の数値を入力してください。',
			],
			'check2' => [
				'rule' => 'account_and_amount_check2',
				'message' => '追加調整の単価は1以上の数値を入力してください。',
			],
		],
	];

	/*
	/ account_code と amountの組み合わせチェック
	*/
	public function account_and_amount_check1() {	

		// チェック処理確認
		if (!$this->check_judgment()){
			return true; 
		}

		// 減額調整チェック
		$data = $this->data[$this->name];
		if ($data['account_code'] == 'ADJUST_REDUCTION' && $data['amount'] >= 0){
			return false;
		}

		return true;
	}

	/*
	/ account_code と amountの組み合わせチェック
	*/
	public function account_and_amount_check2() {		

		// チェック処理確認
		if (!$this->check_judgment()){
			return true;
		}

		// 追加調整チェック
		$data = $this->data[$this->name];
		if ($data['account_code'] == 'ADJUST_ADDITION' && $data['amount'] <= 0){
			return false;
		}

		return true;
	}


	/*
	/ account_code と amountの組み合わせチェックを行うかの確認
	*/
	public function check_judgment() {
		
		$data = $this->data[$this->name];

		// チェックしない
		if (!isset($data['account_and_amount_check'])){
			return false;
		}

		// チェックしない
		if ((boolean)$data['account_and_amount_check'] == 1){
			return false;
		}

		// 科目名が選択されてない場合はチェックしない
		if (!isset($data['account_code'])){
			return false;
		}

		// 金額が選択されてない場合はチェックしない
		if (!isset($data['amount'])){
			return false;
		}

		// 差額調整の場合はチェックしない
		if ($data['account_code'] == 'ADJUST_DIFFERENCE'){
			return false;
		}

        return true;
    }


}

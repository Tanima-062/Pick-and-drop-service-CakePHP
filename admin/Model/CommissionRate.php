<?php
App::uses('AppModel', 'Model');
/**
 * CommissionRate Model
 */
class CommissionRate extends AppModel {
	public $errorMsg = '';

	// 表示用に各マスタのIDから名前を取得する
	public $belongsTo = [
		'Client' => [
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'fields' => [
				'Client.name'
			]
		],
		'SettlementCompany' => [
			'className' => 'SettlementCompany',
			'foreignKey' => 'settlement_company_id',
			'fields' => [
				'SettlementCompany.name'
			]
		],
		'Staff' => [
			'className' => 'Staff',
			'fields' => [
				'Staff.name'
			]
		]
	];

	public $validate = [
		'apply_term_to' => [
			'rule' => 'validateRange',
			'message' => '適用期間を正しく入力してください。'
		],
		'step_condition_value1' => [
			'rule' => ['custom', '/^[0-9]+$/'],
			'message' => '半角数字0-9までの数値を入力してください。',
			'allowEmpty' => true
		],
		'step_condition_value2' => [
			'only_numeric' => [
				'rule' => ['custom', '/^[0-9]+$/'],
				'message' => '半角数字0-9までの数値を入力してください。',
				'allowEmpty' => true
			],
			'range' => [
				'rule' => 'validateRangeValue',
				'message' => '数値の範囲が間違っています。',
				'allowEmpty' => true
			]
		],
		'commission_rate' => [
			'rule' => ['custom', '/^[0-9]+(\.[0-9])?$/'],
			'message' => '正の整数または小数(小数点以下第1位まで)を入力してください。',
			'allowEmpty' => true
		],
		'step_condition_type' => [
			'rule' => 'validateStepConditionType',
			'message' => '段階条件指標の種別を選択してください。'
		]
	];

	/*
	 * 適用期間が開始と終了が反対だったらエラー
	 */
	public function validateRange() {
		if ($this->data['CommissionRate']['apply_term_from'] > $this->data['CommissionRate']['apply_term_to']) {
			return false;
		}

		return true;
	}

	/*
	 * 段階条件指標の数値が大小逆だったらエラー
	 */
	public function validateRangeValue() {
		if ($this->data['CommissionRate']['step_condition_value1'] > $this->data['CommissionRate']['step_condition_value2']) {
			return false;
		}

		return true;
	}

	/*
	 * 段階条件定率のときに段階条件指標の種別が選択されていなかったらエラー
	 */
	public function validateStepConditionType() {
		if ($this->data['CommissionRate']['accounting_condition'] == 'STEP_RATE' &&
			empty($this->data['CommissionRate']['step_condition_type'])) {
			return false;
		}

		return true;
	}

	/*
	 * モデルデータを表示用に変える
	 */
	public function changeViewList($oldCommissionRateArr) {
		$newCommissionRateArr = [];
		foreach($oldCommissionRateArr as $oldCommissionRate) {
			$tmpCommissionRate = $oldCommissionRate;

			$unit = '';
			if (strpos($tmpCommissionRate['CommissionRate']['step_condition_type'], 'AMOUNT') !== false) {
				$unit = '円';
			}
			elseif (strpos($tmpCommissionRate['CommissionRate']['step_condition_type'], 'NUM') !== false) {
				$unit = '件';
			}

			$tmpCommissionRate['CommissionRate']['step_condition_value1'] .= $unit;
			$tmpCommissionRate['CommissionRate']['step_condition_value2'] .= $unit;

			$tmpCommissionRate['CommissionRate']['is_published'] = ($tmpCommissionRate['CommissionRate']['is_published']) ? '公開' : '非公開';

			$newCommissionRateArr[] = $tmpCommissionRate;
		}

		return $newCommissionRateArr;
	}

	/*
	 * 1.保存時に計上条件が定率のときは段階条件指標をNULLにする
	 * 2.条件判定チェック
	 * 3.ポリシーの重複チェック
	 */
	public function beforeSave($options = []) {
		if (isset($this->data['CommissionRate']['accounting_condition']) && $this->data['CommissionRate']['accounting_condition'] == 'FIXED_RATE') {
			$this->data['CommissionRate']['step_condition_type'] = null;
			$this->data['CommissionRate']['step_condition_value1'] = null;
			$this->data['CommissionRate']['step_condition_value2'] = null;
		}

		if ($this->data['CommissionRate']['contract_condition'] == 'CLIENT' &&
			$this->data['CommissionRate']['settlement_company_id'] > 0
		) {
			$this->errorMsg = ' 条件判定にクライアントを選んだときは精算管理会社は選択出来ません';
			return false;
		}

		if ($this->data['CommissionRate']['contract_condition'] == 'SETTLEMENT_COMPANY' &&
			$this->data['CommissionRate']['settlement_company_id'] == 0
		) {
			$this->errorMsg = ' 条件判定に精算管理会社を選んだときは精算管理会社は必須です';
			return false;
		}

		if ($this->isDuplicatePolicy()) {
			$this->errorMsg = ' ポリシーが重複しています';
			return false;
		}

		return true;
	}

	/*
	 * ポリシーが重複しているか
	 */
	private function isDuplicatePolicy() {
		if ($this->data['CommissionRate']['contract_condition'] === 'CLIENT') {
			$conditionKeyData = ['CommissionRate.client_id' => $this->data['CommissionRate']['client_id']];
		} else {
			$conditionKeyData = ['CommissionRate.settlement_company_id' => $this->data['CommissionRate']['settlement_company_id']];
		}

		$conditions = [
			$conditionKeyData,
			'OR' => [ // 指定した期間が含まれている期間を抽出
				['CommissionRate.apply_term_from between ? and ? ' => [$this->data['CommissionRate']['apply_term_from'], $this->data['CommissionRate']['apply_term_to']]],
				['CommissionRate.apply_term_to between ? and ? ' => [$this->data['CommissionRate']['apply_term_from'], $this->data['CommissionRate']['apply_term_to']]],
				['CommissionRate.apply_term_from <= ' => $this->data['CommissionRate']['apply_term_from'],
				 'CommissionRate.apply_term_to >= ' => $this->data['CommissionRate']['apply_term_to']]
			],
			'CommissionRate.is_published' => 1,
			'CommissionRate.delete_flg' => 0
		];

		if (isset($this->data['CommissionRate']['id'])) { // edit時は必要
			$conditions['CommissionRate.id != '] = $this->data['CommissionRate']['id'];
		}

		$commissionRates = $this->find('all',[
			'conditions' => [$conditions]
		]);

		foreach($commissionRates as $commissionRate) {
			if ($this->data['CommissionRate']['accounting_condition'] == 'FIXED_RATE' &&
				$this->data['CommissionRate']['accounting_condition'] == $commissionRate['CommissionRate']['accounting_condition']
			) { // 計上条件が定率で同じだったら重複
				return true;
			}
			elseif ($this->data['CommissionRate']['accounting_condition'] == 'STEP_RATE') { // 計上条件が段階条件定率
				if (!empty($this->data['CommissionRate']['step_condition_type']) &&
					$this->data['CommissionRate']['step_condition_type'] != $commissionRate['CommissionRate']['step_condition_type']) { // 段階条件(種別) が違ったら重複
					return true;
				}
			}
			elseif ($this->data['CommissionRate']['accounting_condition'] != $commissionRate['CommissionRate']['accounting_condition']) {
				// 計上条件が違ったら登録できない
				return true;
			}
		}

		return false;
	}
}

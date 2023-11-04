<?php
App::uses('AppModel', 'Model');
/**
 * CancelFee Model
 */
class CancelFee extends AppModel {
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
		'Staff' => [
			'className' => 'Staff',
			'fields' => [
				'Staff.name'
			]
		]
	];

	public $validate = [
		'apply_term_from' => [
			'rule' => ['date', 'ymd'],
			'message' => '有効な日付を YY-MM-DDフォーマットで入力してください。',
			'required' => true
		],
		'apply_term_to' => [
			'rule' => ['date', 'ymd'],
			'message' => '有効な日付を YY-MM-DDフォーマットで入力してください。',
			'required' => true
		],
		'from_cancel_limit' => [
			'rule' => 'validateFromCancaelLimit',
			'message' => '半角数字0-9までの正しい数値を入力してください。',
		],
		'cancel_limit' => [
			'rule' => 'validateCancaelLimit',
			'message' => '半角数字0-9までの正しい数値を入力してください。',
		],
		'cancel_fee' => [
			'rule' => ['custom', '/^([1-9]\d*|0)(\.\d+)?$/'],
			'message' => '適切な数値を入力してください。',
			'required' => true
		],
		'adv_cancel_fee' => [
			'numeric' => [
				'rule' => ['custom', '/^([1-9]\d*|0)(\.\d+)?$/'],
				'message' => '適切な数値を入力してください。',
				'required' => true
			],
		],
		'cancel_fee_min' => [
			'rule' => ['custom', '/^[0-9]+$/'],
			'message' => '半角数字0-9までの数値を入力してください。',
			'allowEmpty' => true
		],
		'cancel_fee_max' => [
			'rule' => ['custom', '/^[0-9]+$/'],
			'message' => '半角数字0-9までの数値を入力してください。',
			'allowEmpty' => true
		],
		'sales_type' => [
			'rule' => ['custom', '/^(ARRANGED|AGENT-ORGANIZED)$/'],
			'message' => '販売方法の値が不正です。',
			'required' => true
		]
	];

	public function beforeValidate($options = array())
	{
		// バリデーション追加：募集型企画の編集では取消手続料金変更不可
		if (
			isset($this->data['CancelFee']['id']) &&
			$this->data['CancelFee']['sales_type'] === Constant::SALES_TYPE_AGENT_ORGANIZED
		) {
			$this->validate['adv_cancel_fee']['equals'] = [
				'rule' => 'equalsAdvCancelFee',
				'message' => '募集型企画は取消手続料金を変更できません。',
				'required' => true
			];
		}
		return true;
	}

	/*
	 * 出発前選択時のみ期限はチェックする
	 * validateCancaelLimitとほぼ同じだがnullも許可する
	 */
	public function validateFromCancaelLimit($val_arr) {
		$ret = true;
		foreach($val_arr as $val) {
			if (isset($this->data['CancelFee']['is_after_departure']) &&
				$this->data['CancelFee']['is_after_departure'] == 0 ) {
				if (!preg_match('/^[0-9]*$/', $val)) {
					$ret = false;
				}
			}
		}

		return $ret;
	}

	/*
	 * 出発前選択時のみ期限はチェックする
	 */
	public function validateCancaelLimit($val_arr) {
		$ret = true;
		foreach($val_arr as $val) {
			if (isset($this->data['CancelFee']['is_after_departure']) &&
				$this->data['CancelFee']['is_after_departure'] == 0 ) {
				if (!preg_match('/^[0-9]+$/', $val)) {
					$ret = false;
				}
			}
		}

		return $ret;
	}

	/*
	 * モデルデータを表示用に変える
	 */
	public function changeViewList($oldCancelFeeArr) {
		$newCancelFeeArr = [];
		foreach($oldCancelFeeArr as $oldCancelFee) {
			$tmpCancelFee = $oldCancelFee;
			// テーブルカラム文字列の方がわかりやすいかも(define的な方法もあり)
			$tmpCancelFee['CancelFee']['apply_term_point'] = ($tmpCancelFee['CancelFee']['apply_term_point']) ? '出発日' : 'キャンセル日';
			$tmpCancelFee['CancelFee']['is_after_departure'] = ($tmpCancelFee['CancelFee']['is_after_departure']) ? '出発後' : '出発前';

			$cancelLimitUnit = Constant::cancelLimitUnit();
			if (isset($cancelLimitUnit[$tmpCancelFee['CancelFee']['from_cancel_limit_unit']])) {
				$tmpCancelFee['CancelFee']['from_cancel_limit_unit'] = $cancelLimitUnit[$tmpCancelFee['CancelFee']['from_cancel_limit_unit']];
			}
			if (isset($cancelLimitUnit[$tmpCancelFee['CancelFee']['cancel_limit_unit']])) {
				$tmpCancelFee['CancelFee']['cancel_limit_unit'] = $cancelLimitUnit[$tmpCancelFee['CancelFee']['cancel_limit_unit']];
			}

			if (strpos($tmpCancelFee['CancelFee']['cancel_fee_unit'], '_AMOUNT')) {
				$tmpCancelFee['CancelFee']['cancel_fee'] .= '円';
			}
			else {
				$tmpCancelFee['CancelFee']['cancel_fee'] .= '%';
			}

			$cancelFeeUnit = Constant::cancelFeeUnit();
			if (isset($cancelFeeUnit[$tmpCancelFee['CancelFee']['cancel_fee_unit']])) {
				$tmpCancelFee['CancelFee']['cancel_fee_unit'] = $cancelFeeUnit[$tmpCancelFee['CancelFee']['cancel_fee_unit']];
			}

			$fractionUnit = Constant::fractionUnit();
			if (isset($fractionUnit[$tmpCancelFee['CancelFee']['fraction_unit']])) {
				$tmpCancelFee['CancelFee']['fraction_unit'] = $fractionUnit[$tmpCancelFee['CancelFee']['fraction_unit']] . '円';
			}

			$fractionRound = Constant::fractionRound();
			if (isset($fractionRound[$tmpCancelFee['CancelFee']['fraction_round']])) {
				$tmpCancelFee['CancelFee']['fraction_round'] = $fractionRound[$tmpCancelFee['CancelFee']['fraction_round']];
			}

			$tmpCancelFee['CancelFee']['cancel_fee_min'] .= ($tmpCancelFee['CancelFee']['cancel_fee_min']) ? '円' : '';
			$tmpCancelFee['CancelFee']['cancel_fee_max'] .= ($tmpCancelFee['CancelFee']['cancel_fee_max']) ? '円' : '';
			$tmpCancelFee['CancelFee']['adv_cancel_fee'] .= ($tmpCancelFee['CancelFee']['adv_cancel_fee']) ? '円' : '';

			$tmpCancelFee['CancelFee']['is_published'] = ($tmpCancelFee['CancelFee']['is_published']) ? '公開' : '非公開';
			$tmpCancelFee['CancelFee']['sales_type'] = Constant::salesType()[$tmpCancelFee['CancelFee']['sales_type']];

			$newCancelFeeArr[] = $tmpCancelFee;
		}

		return $newCancelFeeArr;
	}

	/*
	 * 各画面でフォーマット変更するのは面倒なので可能なものはここで行う
	 */
	public function afterFind($results) {
		foreach($results as $key => $val) {
			if (isset($results[$key]['CancelFee']['apply_term_from'])) {
				$results[$key]['CancelFee']['apply_term_from'] = date('Y-m-d', strtotime($results[$key]['CancelFee']['apply_term_from']));
			}

			if (isset($results[$key]['CancelFee']['apply_term_to'])) {
				$results[$key]['CancelFee']['apply_term_to'] = date('Y-m-d', strtotime($results[$key]['CancelFee']['apply_term_to']));
			}
		}

		return $results;
	}

	/*
	 * 1.保存時に出発後のときは期限をNULLに、計上単位が定額のときは端数処理、最低額、上限額をNULLにする
	 * 2.期限の日時チェック
	 * 3.ポリシーの重複チェック
	 */
	public function beforeSave($options = []) {
		if (is_null($this->data['CancelFee']['from_cancel_limit']) || $this->data['CancelFee']['from_cancel_limit'] == '') {
			//数値がないのに日/時間だけ保存しても邪魔なので消す
			$this->data['CancelFee']['from_cancel_limit_unit'] = null;
		}
		if (isset($this->data['CancelFee']['is_after_departure']) && $this->data['CancelFee']['is_after_departure']) {
			$this->data['CancelFee']['from_cancel_limit'] = null;
			$this->data['CancelFee']['from_cancel_limit_unit'] = null;
			$this->data['CancelFee']['cancel_limit'] = null;
			$this->data['CancelFee']['cancel_limit_unit'] = null;
		}

		if (isset($this->data['CancelFee']['cancel_fee_unit']) && $this->data['CancelFee']['cancel_fee_unit'] == 'RESERVE_FIXED_AMOUNT') {
			$this->data['CancelFee']['fraction_unit'] = null;
			$this->data['CancelFee']['fraction_round'] = null;
			$this->data['CancelFee']['cancel_fee_min'] = null;
			$this->data['CancelFee']['cancel_fee_max'] = null;
		}

		if (isset($this->data['CancelFee']['apply_term_to'])) {
			$this->data['CancelFee']['apply_term_to'] = date('Y-m-d 23:59:59', strtotime($this->data['CancelFee']['apply_term_to']));
		}

		if ($this->isCancelLimit()) {
			$this->errorMsg = ' 期限の日時が 左 > 右 になるよう設定してください';
			return false;
		}

		if ($this->isDuplicatePolicy()) {
			$this->errorMsg = ' ポリシーが重複しています';
			return false;
		}

		return true;
	}

	/*
	 * 期限の日時が 左>右 か判定
	 */
	private function isCancelLimit() {
		// 出発前+期限+期限単位
		if ($this->data['CancelFee']['is_after_departure'] == 0) { // 出発前
			if(empty($this->data['CancelFee']['from_cancel_limit']) || empty($this->data['CancelFee']['cancel_limit'])){
				//fromはnull許可,cancel_limitがnullだと「当日」に置き換わるためどちらかがnullは判定せずに終了
				return false;
			}

			$fromCancelLimit = $this->data['CancelFee']['from_cancel_limit'];
			//比較のために日付の場合は時間に換算する
			if($this->data['CancelFee']['from_cancel_limit_unit'] == 'DAY'){
				$fromCancelLimit *= 24;
			}
			$cancelLimit = $this->data['CancelFee']['cancel_limit'];
			//比較のために日付の場合は時間に換算する
			if($this->data['CancelFee']['cancel_limit_unit'] == 'DAY'){
				$cancelLimit *= 24;
			}

			if ($fromCancelLimit < $cancelLimit) {
				return true;
			}
		}

		return false;
	}

	/*
	 * ポリシーが重複しているか
	 */
	private function isDuplicatePolicy() {
		$conditions = [
			'CancelFee.client_id' => $this->data['CancelFee']['client_id'],
			'OR' => [ // 指定した期間が含まれている期間を抽出
				['CancelFee.apply_term_from between ? and ? ' => [$this->data['CancelFee']['apply_term_from'], $this->data['CancelFee']['apply_term_to']]],
				['CancelFee.apply_term_to between ? and ? ' => [$this->data['CancelFee']['apply_term_from'], $this->data['CancelFee']['apply_term_to']]],
				['CancelFee.apply_term_from <= ' => $this->data['CancelFee']['apply_term_from'],
				 'CancelFee.apply_term_to >= ' => $this->data['CancelFee']['apply_term_to']]
			],
			'CancelFee.delete_flg' => 0,
			'CancelFee.sales_type' => $this->data['CancelFee']['sales_type']
		];

		if (isset($this->data['CancelFee']['id'])) { // edit時は必要
			$conditions['CancelFee.id != '] = $this->data['CancelFee']['id'];
		}

		$cancelFees = $this->find('all',[
			'conditions' => [$conditions]
		]);

		// 出発前+期限+期限単位 or 出発後が同じなら重複ポリシー
		foreach($cancelFees as $cancelFee) {
			if (isset($this->data['CancelFee']['is_after_departure']) &&
					$cancelFee['CancelFee']['is_after_departure'] == $this->data['CancelFee']['is_after_departure']) {
				if ($this->data['CancelFee']['is_after_departure'] == 0) { // 出発前
					if ($cancelFee['CancelFee']['from_cancel_limit'] == $this->data['CancelFee']['from_cancel_limit'] && // 期限
						(isset($this->data['CancelFee']['from_cancel_limit']) && $this->data['CancelFee']['from_cancel_limit'] > 0) && // 期限
							$cancelFee['CancelFee']['from_cancel_limit_unit'] == $this->data['CancelFee']['from_cancel_limit_unit']) { // 期限単位
						return true;
					}
					if ($cancelFee['CancelFee']['cancel_limit'] == $this->data['CancelFee']['cancel_limit'] && // 期限
						$cancelFee['CancelFee']['cancel_limit_unit'] == $this->data['CancelFee']['cancel_limit_unit']) { // 期限単位
						return true;
					}
				}
				else if ($this->data['CancelFee']['is_after_departure'] == 1) { // 出発後
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * 取消手続料の改ざんをチェック
	 * 
	 * @return bool
	 */
	public function equalsAdvCancelFee()
	{
		$advCancelFee = $this->field('adv_cancel_fee', ['id' => $this->data['CancelFee']['id']]);
		if ($this->data['CancelFee']['adv_cancel_fee'] === $advCancelFee) {
			return true;
		}
		return false;
	}

	// 会社ごとのキャンセル料データを全て取得する
	public function getCancelFees($clientId)
	{
		return $this->find('all', array(
			'conditions' => array(
				'client_id' => $clientId,
				'is_published' => 1,
				'sales_type' => Constant::SALES_TYPE_ARRANGED,
				'delete_flg' => 0,
			),
			'order' => array(
				'cancel_limit' => 'desc',
			),
			'recursive' => -1,
		));
	}

	// 予約IDごとのキャンセル料データを全て取得する
	public function getCancelFeesMulti($reservationIds)
	{
		$cancelFees = array();
		$options = array(
			'fields' => array(
				'CancelFee.*',
				'Reservation.id',
				'Reservation.rent_datetime',
			),
			'joins'=>array(
				array(
					'table'=>'reservations',
					'alias'=>'Reservation',
					'type'=>'RIGHT',
					'conditions'=>array(
						'Reservation.client_id = CancelFee.client_id'
					)
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationIds,
				'CancelFee.is_published' => 1,
				'CancelFee.sales_type' => Constant::SALES_TYPE_ARRANGED,
				'CancelFee.delete_flg' => 0,
			),
			'order' => array(
				'Reservation.id' => 'asc',
				'cancel_limit' => 'desc',
			),
			'recursive' => -1,
		);
		$result = $this->find('all', $options);
		if (!empty($result)) {
			foreach ($result as $key => $val) {
				$cancelFees[$val['Reservation']['id']][$val['Reservation']['rent_datetime']][] = $val;
			}
		}
		return $cancelFees;
	}
}

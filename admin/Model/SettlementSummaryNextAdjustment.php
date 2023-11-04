<?php
App::uses('AppModel', 'Model');
/**
 * SettlementSummaryNextAdjustment Model
 */
class SettlementSummaryNextAdjustment extends AppModel {

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'SettlementSummary'=>array(
			'className'=>'SettlementSummary',
			'foreignKey'=>'settlement_summary_id',
			'conditions'=>'',
			'fields'=>'',
			'order'=>''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'create_staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	public $validate = [
		'settlement_month' => [
			'rule' => ['between', 6, 6],
			'message' => 'yyyymm形式となるよう選択してください',
			'allowEmpty' => false
		],
		'item_name' => [
			'rule' => 'checkStringTax',
			'allowEmpty' => true
		],
		'count' => [
			'rule' => 'numeric',
			'message' => '件数は数字で指定してください',
			'allowEmpty' => true
		],
		'commission_rate' => [
			'rule' => ['custom', '/^[0-9]+(\.[0-9])?$/'],
			'message' => '料率は正の整数または小数(小数点以下第1位まで)を入力してください',
			'allowEmpty' => true
		],
		'payment_amount' => [
			'rule' => 'numeric',
			'message' => '金額は数字で指定してください',
			'allowEmpty' => true
		],
		'billing_amount' => [
			'rule' => 'numeric',
			'message' => '金額は数字で指定してください',
			'allowEmpty' => true
		],
	];

	public function checkStringTax($data) {
		// 精算書の「合計」ではConstant::TAX_NAMEという文字列のデータが除外されるため次月調整では登録させない
		if ($data['item_name'] == Constant::TAX_NAME) {
			return '品目は「'.Constant::TAX_NAME.'」以外で入力してください。';
		}
		return true;
	}
}

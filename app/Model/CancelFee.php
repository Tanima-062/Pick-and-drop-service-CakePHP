<?php
App::uses('AppModel', 'Model');
/**
 * CancelFee Model
 */
class CancelFee extends AppModel {

	protected $cacheConfig = '1day';

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

	// プラン詳細の煽り文言に表示するキャンセル無料の期限（出発〇〇前までキャンセル料はかかりません）
	public function getCancelFreeLimit($clientId) {
		$options = array(
			'fields' => array('cancel_limit', 'cancel_limit_unit'),
			'conditions' => array(
				'client_id' => $clientId,
				'is_after_departure' => 0,
				'cancel_fee' => 0,
				'is_published' => 1,
				'sales_type' => Constant::SALES_TYPE_ARRANGED,
				'delete_flg' => 0,
			),
			// 複数レコードヒットはないということだが、
			// 万が一ヒットした場合の順序を固定する
			'order' => array(
				'cancel_limit' => 'desc',
				'cancel_limit_unit' => 'asc',
			),
			'recursive' => -1,
		);

		$data = $this->findC('first', $options);

		if (empty($data)) {
			return '7日';
		}
		if ($data['CancelFee']['cancel_limit'] == 0) {
			return '';
		}
		return $data['CancelFee']['cancel_limit'].Constant::cancelLimitUnit()[$data['CancelFee']['cancel_limit_unit']];
	}

	// 会社ごとのキャンセル料データを全て取得する
	public function getCancelFees($clientId) {
		return $this->findC('all', array(
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
}

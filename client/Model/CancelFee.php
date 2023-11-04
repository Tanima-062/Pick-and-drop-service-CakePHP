<?php
App::uses('AppModel', 'Model');
/**
 * CancelFee Model
 */
class CancelFee extends AppModel {

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

	// 会社ごとのキャンセル料データを全て取得する
	public function getCancelFees($clientId) {
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
}

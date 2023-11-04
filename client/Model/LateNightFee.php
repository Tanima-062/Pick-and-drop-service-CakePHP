<?php
App::uses('AppModel', 'Model');
/**
 * LateNightFee Model
 *
 */
class LateNightFee extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'target_time_from' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'target_time_to' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'price' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '料金は必須項目です。',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => '料金は整数値のみ登録可能です。',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'price_addition_flg' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '加算回数を選択してください。',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'client_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	/**
	 * 編集・削除対象のデータが該当クライアントのものかチェックする
	 *
	 */
	public function clientCheck($id,$clientId) {

		$count = $this->find('count',array('conditions'=>array('id'=>$id,'client_id'=>$clientId)));
		if(empty($count)) {
			return false;
		}

		return true;

	}

	/**
	 *
	 * 深夜手数料のリストを取得する
	 * キーは深夜手数料ID,バリューは対象時間と料金を連結
	 *
	 * @param int $clientId
	 * @return array || null  深夜手数料のリスト
	 */
	public function getLateNightFeeList($clientId)  {

		$datas = $this->find('all',array('conditions'=>array('client_id'=>$clientId,'delete_flg'=>0)));

		$lateNightFeeList = array();
		foreach($datas as $data) {
			$data = $data['LateNightFee'];
			$key = $data['id'];

			$lateNightFeeList[$key] = date('H時i分',strtotime($data['target_time_from'])) . '～' .
													date('H時i分',strtotime($data['target_time_to'])) .
													"&nbsp; &yen" . number_format($data['price']);
		}

		return $lateNightFeeList;
	}
}

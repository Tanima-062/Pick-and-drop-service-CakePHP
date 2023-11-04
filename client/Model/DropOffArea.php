<?php
App::uses('AppModel', 'Model');
/**
 * DropOffArea Model
 *
 * @property Client $Client
 */
class DropOffArea extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '乗捨エリア名は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed


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
	 * 乗捨エリアのリストを取得
	 * @param int $clientId
	 */
	public function getDropOffAreaList($clientId) {
		return $this->find('list',array('conditions'=>array('client_id'=>$clientId,'delete_flg'=>0)));
	}
}

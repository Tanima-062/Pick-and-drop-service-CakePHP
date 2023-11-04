<?php
App::uses('AppModel', 'Model');
App::import('Model', 'Client');
App::import('Model', 'Prefecture');

/**
 * Recommend Model
 */
class Recommend extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'クライアント名は必須です'
			),
			'existsClient' => array(
				'rule' => array('existsClient'),
				'message' => 'クライアント名が不正です'
			)
		),
		'pr_title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'PRタイトルは必須です'
			),
		),
		'apply_term_from' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => true,
				'message' => '開始日が不正です'
			),
			'fromTo' => array(
				'rule' => array('fromTo'),
				'message' => '期間は開始日 <= 終了日にしてください'
			),
			'checkDuplicate' => array(
				'rule' => array('checkDuplicate'),
				'message' => '対象地域と期間で重複しています'
			)
		),
		'apply_term_to' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => true,
				'message' => '終了日が不正です'
			)
		),
		'prefectures' => array(
			'prefecture' => array(
				'rule'     => array('multiple', array('min' => 1)),
				'required' => true,
				'message'  => '対象地域を選択してください',
			),
			'checkPrefecture' => array(
				'rule' => array('checkPrefecture'),
				'required' => true,
				'message' => '対象地域が不正です',
			),
		),
		'recommend_fee' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '手数料は必須です'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'required' => true,
				'message' => '手数料は半角数字で必ず入力してください',
			),
		),
		'recommend_fee_unit' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '手数料単位を選択してください',
				'required' => true,
			),
		),
		'is_internal_tax' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '税金種類を選択してください',
				'required' => true,
			),
		),
		'is_published' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '公開を選択してください',
				'required' => true,
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	public $hasMany = array(
		'RecommendPrefecture' => array(
			'className' => 'RecommendPrefecture',
			'foreignKey' => 'recommend_id',
			'dependent' => false,
			'conditions' => ['delete_flg' => 0],
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

	// クライアントバリデーション
	public function existsClient($data) {
		if (empty($data['client_id'])) {
			return false;
		}
		$id = $data['client_id'];
		$clientList = (new Client())->find('list', array('conditions' => array('delete_flg' => 0), 'recursive' => -1));
		if (!isset($clientList[$id])) {
			return false;
		}
		return true;
	}

	// 期間バリデーション
	public function fromTo($data) {
		$from = $data['apply_term_from'];
		$to = $this->data[$this->name]['apply_term_to'];
		return ($from <= $to);
	}

	// 重複バリデーション
	public function checkDuplicate($data) {
		$from = $data['apply_term_from'];
		$to = $this->data[$this->name]['apply_term_to'];
		$prefectures = $this->data[$this->name]['prefectures'];
		$id = !empty($this->data[$this->name]['id'])?$this->data[$this->name]['id']:null;
		$isPublished = !empty($this->data[$this->name]['is_published'])?$this->data[$this->name]['is_published']:0;
		$deleteFlg = !empty($this->data[$this->name]['delete_flg'])?$this->data[$this->name]['delete_flg']:0;

		if ($isPublished == 0 || $deleteFlg == 1) {
			return true;
		}

		$conditions = [
			'conditions' => [
				'Recommend.apply_term_from <= ' => $to,
				'Recommend.apply_term_to >= ' => $from,
				'Recommend.delete_flg' => 0,
				'Recommend.is_published' => 1,
			]
		];

		$result = $this->find('all', $conditions);

		foreach ($result as $val) {
			if (!empty($id) && $val['Recommend']['id'] == $id) {
				continue;
			}
			$checkRes = Hash::extract($val['RecommendPrefecture'], '{n}.prefecture_id');
			$intersection = array_values(array_intersect($checkRes, $prefectures));

			if (!empty($intersection)) {
				return false;
			}
		}

		return true;
	}

	// 対象地域バリデーション
	public function checkPrefecture($data) {
		$prefectures = $data['prefectures'];

		foreach ($prefectures as $val) {
			if ($val == 0){
				return true;
			}
			$prefecture = (new Prefecture())->find('first', array('conditions' => array('delete_flg' => 0, 'id' => $val), 'recursive' => -1));
			if (empty($prefecture)) {
				return false;
			}
		}
		return true;
	}
}

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
		'space' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '掲載枠を選択してください',
				'required' => true,
			),
		),
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
			'checkDuplicateSameSpace' => array(
				'rule' => array('checkDuplicateSameSpace'),
				'message' => '同じ掲載枠で対象地域と期間が重複しています'
			),
			'checkDuplicateOtherSpace' => array(
				'rule' => array('checkDuplicateOtherSpace'),
				'message' => '異なる掲載枠でクライアント、対象地域と期間が重複しています'
			),
			'checkDuplicateRandom' => array(
				'rule' => array('checkDuplicateRandom'),
				'message' => 'ランダム枠でクライアント、対象地域と期間が重複しています'
			),
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
				'rule'     => array('checkCountPrefecture'),
				'required' => true,
				'message'  => '対象地域を選択してください',
			),
			'checkPrefecture' => array(
				'rule' => array('checkPrefecture'),
				'required' => true,
				'message' => '対象地域が不正です',
			),
		),
		'randomPrefectures' => array(
			'prefecture' => array(
				'rule'     => array('checkCountPrefecture'),
				'required' => true,
				'message'  => '対象地域を選択してください',
			),
			'checkPrefecture' => array(
				'rule' => array('checkRandomPrefecture'),
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
	public function checkDuplicateSameSpace($data) {
		return $this->checkDuplicate($data, true, false);
	}

	public function checkDuplicateOtherSpace($data) {
		return $this->checkDuplicate($data, false, false);
	}

	public function checkDuplicateRandom($data) {
		return $this->checkDuplicate($data, false, true);
	}

	private function checkDuplicate($data, $isSameSpace, $randomFlg) {
		$from = $data['apply_term_from'];
		$to = $this->data[$this->name]['apply_term_to'];
		if ($randomFlg) {
			$prefectures = $this->data[$this->name]['randomPrefectures'];
		} else {
			$prefectures = $this->data[$this->name]['prefectures'];
		}
		$id = !empty($this->data[$this->name]['id'])?$this->data[$this->name]['id']:null;
		$isPublished = !empty($this->data[$this->name]['is_published'])?$this->data[$this->name]['is_published']:0;
		$deleteFlg = !empty($this->data[$this->name]['delete_flg'])?$this->data[$this->name]['delete_flg']:0;
		$space = $this->data[$this->name]['space'];
		$clientId = $this->data[$this->name]['client_id'];

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
		if ($randomFlg) {
			$conditions['conditions']['Recommend.client_id'] = $clientId;
		} else {
			if ($isSameSpace) {
				$conditions['conditions']['Recommend.space'] = $space;
			} else {
				$conditions['conditions']['Recommend.space <>'] = $space;
				$conditions['conditions']['Recommend.client_id'] = $clientId;
			}
		}

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

	// 対象地域チェックバリデーション
	public function checkCountPrefecture($data) {
		$deleteTarget = array(0);
		// 後でマージしたいから空の場合、array()を差し込む
		$fixedPrefectures = (!empty($this->data[$this->name]['prefectures'])) ? $this->data[$this->name]['prefectures'] : array();
		$randomPrefectures = (!empty($this->data[$this->name]['randomPrefectures'])) ? $this->data[$this->name]['randomPrefectures'] : array();
		$mergePrefectures = array_merge($fixedPrefectures, $randomPrefectures);
		// 「0」は「全て」なので削除する
		$prefectures = array_diff($mergePrefectures, $deleteTarget);
		if (empty($prefectures)) {
			// １つも対象地域を選ばれてない場合NG
			return false;
		}
		return true;
	}

	// 固定地域バリデーション
	public function checkPrefecture($data) {
		$prefectures = $data['prefectures'];

		foreach ($prefectures as $val) {
			if ($val == 0){
				return true;
			}
			$options = array(
				'conditions' => array(
					'delete_flg' => 0,
					'OR' => array(
						'recommend_random_flg is null',
						'recommend_random_flg' => 0
					),
					'id' => $val
				),
				'recursive' => -1
			);
			$prefecture = (new Prefecture())->find('first', $options);
			if (empty($prefecture)) {
				return false;
			}
		}
		return true;
	}

	// ランダム地域バリデーション
	public function checkRandomPrefecture($data) {
		$prefectures = $data['randomPrefectures'];

		foreach ($prefectures as $val) {
			if ($val == 0){
				return true;
			}
			$options = array(
				'conditions' => array(
					'delete_flg' => 0,
					'OR' => array(
						'recommend_random_flg' => 1
					),
					'id' => $val
				),
				'recursive' => -1
			);
			$prefecture = (new Prefecture())->find('first', $options);
			if (empty($prefecture)) {
				return false;
			}
		}
		return true;
	}

	public function getSettlementTarget($year, $month) {
		return $this->find('all', array(
			'fields' => array(
				'Recommend.id',
				'Recommend.recommend_fee',
				'Recommend.recommend_fee_unit',
				'Recommend.is_internal_tax',
				'Recommend.settlement_timing',
			),
			'conditions' => array(
				'OR' => array(
					array(
						'Recommend.recommend_fee_unit' => 0,// 定額
						"DATE_FORMAT(Recommend.apply_term_to, '%Y%m')" => $year.$month,// 対象月がレコメンド期間終了日を含む月
					),
					array(// 定率
						'Recommend.recommend_fee_unit' => 1,// 定率
						"DATE_FORMAT(Recommend.apply_term_from, '%Y%m') <=" => $year.$month,//レコメンド期間開始日以降（少しでもレコード取得減らしたい）
					),
				)
			),
			'recursive' => -1
		));
	}

	public function checkPeriodDuplicate($prefectureId) {
		// prefectureIdに紐づいているレコメンドで同じ都道府県で同じスペース、期間重複がいくつあるかをカウントする(1個でも被ったらNG)
		$options = array(
			'fields' => array(
				'Recommend.id as RecommendId',
				'Recommend2.id as Recommend2Id',
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'RecommendPrefecture1',
					'table' => 'recommend_prefectures',
					'conditions' => 'Recommend.id = RecommendPrefecture1.recommend_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Recommend2',
					'table' => 'recommends',
					'conditions' => 'Recommend.id != Recommend2.id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'RecommendPrefecture2',
					'table' => 'recommend_prefectures',
					'conditions' => 'Recommend2.id = RecommendPrefecture2.recommend_id',
				),
			),
			'conditions' => array(
				'Recommend.delete_flg' => '0',
				'Recommend.is_published' => '1',
				'RecommendPrefecture1.prefecture_id' => $prefectureId,
				'Recommend2.delete_flg' => '0',
				'Recommend2.is_published' => '1',
				'RecommendPrefecture2.prefecture_id' => $prefectureId,
				'Recommend.space = Recommend2.space',
				'RecommendPrefecture1.prefecture_id = RecommendPrefecture2.prefecture_id',
				'RecommendPrefecture1.delete_flg' => '0',
				'RecommendPrefecture2.delete_flg' => '0',
				'OR' => array(
					array(
						'Recommend.apply_term_from >= Recommend2.apply_term_from',
						'Recommend.apply_term_from <= Recommend2.apply_term_to',
					),
					array(
						'Recommend.apply_term_to >= Recommend2.apply_term_from',
						'Recommend.apply_term_to <= Recommend2.apply_term_to',
					),
				),
			),
			'recursive' => -1,
		);
		$duplicateCnt = $this->find('count', $options);
		if ($duplicateCnt > 0) {
			return true;
		} else {
			return false;
		}
	}
}

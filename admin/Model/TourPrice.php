<?php
App::uses('AppModel', 'Model');
App::import('Model', 'Landmark');
/**
 * TourPrice Model
 */
class TourPrice extends AppModel {
	public $useDbConfig = 'common';
	public $useTable = 'cm_tm_rc_tour_price';

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'iata_cd' => array(
			'existsIATA' => array(
				'rule' => array('existsIATA'),
				'required' => true,
				'message' => '利用空港が不正です'
			)
		),
		'date_from' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => true,
				'message' => '販売開始日が不正です'
			),
			'fromTo' => array(
				'rule' => array('fromTo'),
				'message' => '販売期間は開始日 <= 終了日にしてください'
			)
		),
		'date_to' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => true,
				'message' => '販売終了日が不正です'
			)
		),
		'time_start' => array(
			'time' => array(
				'rule' => array('time'),
				'required' => true,
				'message' => '営業開始時刻が不正です'
			)
		),
		'time_end' => array(
			'time' => array(
				'rule' => array('time'),
				'required' => true,
				'message' => '営業終了時刻が不正です'
			)
		),
		'passenger_count' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber'),
				'required' => true,
				'message' => '乗車人数は1人から8人の間で設定してください'
			),
			'range' => array(
				'rule' => array('range', 0, 9),
				'message' => '乗車人数は1人から8人の間で設定してください'
			),
			'duplicate' => array(
				'rule' => array('duplicate'),
				'message' => '利用空港、販売期間、乗車人数の組み合わせが重複するレコードがあります'
			)
		),
		'tour_car_type_id' => array(
			'tourCarType' => array(
				'rule' => array('tourCarType'),
				'required' => true,
				'message' => '車両クラスが不正です'
			)
		),
		'price' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'required' => true,
				'message' => '販売単価は整数で入力してください'
			)
		),
		'staff_id' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true)
			)
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean')
			)
		)
	);

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
		)
	);

	// 利用空港バリデーション
	public function existsIATA($data) {
		$iataCdList = Hash::extract((new Landmark())->getAirportList(), '{n}.Landmark.iata_cd');
		return in_array($this->data[$this->name]['iata_cd'], array_merge($iataCdList, array('TYO', 'OSA')));
	}

	// 販売期間バリデーション
	public function fromTo($data) {
		$from = $this->data[$this->name]['date_from'];
		$to = $this->data[$this->name]['date_to'];
		return ($from <= $to);
	}

	// 車両クラスバリデーション
	public function tourCarType($data) {
		// 画面上自動選択なので普通は異常値入らないはずだが...
		$id = $this->data[$this->name]['tour_car_type_id'];
		$name = $this->data[$this->name]['tour_car_type_name'];
		$ex = $this->data[$this->name]['tour_car_example'];
		$passenger = $this->data[$this->name]['passenger_count'];

		if (empty($id) || empty($name) || empty($ex)) {
			return false;
		}

		$info = array();
		$infos = Constant::tourCarTypes();
		foreach ($infos as $v) {
			foreach ($v['passenger'] as $p) {
				if ($p == $passenger) {
					$info = $v;
					break 2;
				}
			}
		}

		if (empty($info)) {
			return false;
		}
		if ($info['id'] != $id || $info['name'] != $name || $info['example'] != $ex) {
			return false;
		}

		return true;
	}

	// 重複チェック
	public function duplicate($data) {
		if ($this->data[$this->name]['delete_flg']) {
			return true;
		}
		$conditions = array(
			'iata_cd' => $this->data[$this->name]['iata_cd'],
			'passenger_count' => $this->data[$this->name]['passenger_count'],
			'date_from <=' => $this->data[$this->name]['date_to'],
			'date_to >=' => $this->data[$this->name]['date_from'],
			'delete_flg' => 0
		);
		if (isset($this->data[$this->name]['id'])) {
			$conditions['id <>'] = $this->data[$this->name]['id'];
		}
		$ret = $this->find('first', array('conditions' => $conditions));
		return empty($ret);
	}
}

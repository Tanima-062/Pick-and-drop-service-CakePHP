<?php
App::uses('AppModel', 'Model');
App::import('Model', 'Client');
App::import('Model', 'CarType');
App::import('Model', 'Office');
require_once('encrypt_class.php');
/**
 * TourReservation Model
 */
class TourReservation extends AppModel {
	public $useDbConfig = 'common';
	public $useTable = 'cm_th_rc_tour_reservation';

	private $encTargets = array('last_name', 'first_name', 'email', 'tel');

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'reservation_status_id' => array(
			'existsStatus' => array(
				'rule' => array('existsStatus'),
				'required' => true,
				'message' => 'ステータスが不正です'
			),
			'isReserved' => array(
				'rule' => array('isReserved'),
				'message' => '予約番号を入力してください'
			)
		),
		'rent_dt' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => true,
				'message' => '利用開始日が不正です'
			),
			/*'beforeArrival' => array(
				'rule' => array('beforeArrival'),
				'message' => '受取日時は到着後にしてください'
			)*/
		),
		'return_dt' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => true,
				'message' => '利用終了日が不正です'
			),
			/*'afterDeparture' => array(
				'rule' => array('afterDeparture'),
				'message' => '返却日時は出発前にしてください'
			)*/
		),
		'client_id' => array(
			/*'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
				'message' => '利用会社は必須です'
			),*/
			'existsClient' => array(
				'rule' => array('existsClient'),
				'message' => '利用会社が不正です'
			)
		),
		'car_type_id' => array(
			/*'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
				'message' => '実手配車両クラスは必須です'
			),*/
			'existsCarType' => array(
				'rule' => array('existsCarType'),
				'message' => '実手配車両クラスが不正です'
			)
		),
		'rent_office_id' => array(
			/*'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
				'message' => '受取店舗は必須です'
			),*/
			'rentOffice' => array(
				'rule' => array('rentOffice'),
				'required' => true,
				'message' => '受取店舗が不正です'
			)
		),
		'return_office_id' => array(
			/*'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
				'message' => '返却店舗は必須です'
			),*/
			'returnOffice' => array(
				'rule' => array('returnOffice'),
				'required' => true,
				'message' => '返却店舗が不正です'
			)
		),
		'net_price' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'required' => true,
				'allowEmpty' => true,
				'message' => '仕入価格は整数で入力してください'
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

	// ステータスバリデーション
	public function existsStatus($data) {
		return isset(Constant::tourReservationStatus()[$this->data[$this->name]['reservation_status_id']]);
	}

	// 予約or成約のとき予約番号入っているか
	public function isReserved($data) {
		$status = $this->data[$this->name]['reservation_status_id'];
		if ($status != 2) {
			return true;
		}
		return !empty($this->data[$this->name]['reservation_key']);
	}

	// 受取日時が到着後か
	public function beforeArrival($data) {
		return $this->data[$this->name]['rent_dt'] > $this->data[$this->name]['arrival_dt'];
	}

	// 返却日時が出発前か
	public function afterDeparture($data) {
		return $this->data[$this->name]['return_dt'] < $this->data[$this->name]['departure_dt'];
	}

	// 利用会社バリデーション
	public function existsClient($data) {
		if (empty($this->data[$this->name]['client_id'])) {
			return true;
		}
		$id = $this->data[$this->name]['client_id'];
		$clientList = (new Client())->find('list', array('conditions' => array('delete_flg' => 0), 'recursive' => -1));
		if (!isset($clientList[$id])) {
			return false;
		}
		return $clientList[$id] == $this->data[$this->name]['client_name'];
	}

	// 実手配車両クラスバリデーション
	public function existsCarType($data) {
		if (empty($this->data[$this->name]['car_type_id'])) {
			return true;
		}
		$id = $this->data[$this->name]['car_type_id'];
		$carTypeList = (new CarType())->find('list', array('conditions' => array('delete_flg' => 0), 'recursive' => -1));
		if (!isset($carTypeList[$id])) {
			return false;
		}
		return $carTypeList[$id] == $this->data[$this->name]['car_type_name'];
	}

	// 受取店舗バリデーション
	public function rentOffice($data) {
		if (empty($this->data[$this->name]['rent_office_id'])) {
			return true;
		}
		$office = (new Office())->getTourOfficeInfo($this->data[$this->name]['rent_office_id']);
		if (empty($office)) {
			return false;
		}
		if ($office['Office']['name'] != $this->data[$this->name]['rent_office_name']) {
			return false;
		}
		if ($office['Office']['tel'] != $this->data[$this->name]['rent_office_tel']) {
			return false;
		}
		if ($office['office_contents_url'] != $this->data[$this->name]['rent_office_url']) {
			return false;
		}
		return true;
	}

	// 返却店舗バリデーション
	public function returnOffice($data) {
		if (empty($this->data[$this->name]['return_office_id'])) {
			return true;
		}
		$office = (new Office())->getTourOfficeInfo($this->data[$this->name]['return_office_id']);
		if (empty($office)) {
			return false;
		}
		if ($office['Office']['name'] != $this->data[$this->name]['return_office_name']) {
			return false;
		}
		if ($office['Office']['tel'] != $this->data[$this->name]['return_office_tel']) {
			return false;
		}
		if ($office['office_contents_url'] != $this->data[$this->name]['return_office_url']) {
			return false;
		}
		return true;
	}

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()){
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();
		foreach ($this->encTargets as $name) {
			if (!empty($this->data[$this->name][$name])){
				$this->data[$this->name][$name.'_enc'] = $encrypt->encrypt($this->data[$this->name][$name]);
				unset($this->data[$this->name][$name]);
			}
			if (!empty($this->data[$name])){
				$this->data[$name.'_enc'] = $encrypt->encrypt($this->data[$name]);
				unset($this->data[$name]);
			}
		}
		return true;
	}

	/**
	 * 検索後処理
	 */
	public function afterFind($results, $primary = false) {
		// 対象フィールドを復号
		$encrypt = new Encrypt();
		foreach ($results as $key => $val) {
			foreach ($this->encTargets as $name) {
				if (isset($val[$this->name][$name.'_enc'])) {
					$results[$key][$this->name][$name] = $encrypt->decrypt($val[$this->name][$name.'_enc']);
					unset($this->data[$this->name][$name.'_enc']);
				}
			}
		}
		return $results;
	}

	/**
	 * 検索前処理
	 */
	public function beforeFind($queryData) {
		// 対象検索条件を暗号化
		$encrypt = new Encrypt();
		foreach ($this->encTargets as $name) {
			if (!empty($queryData['conditions']['TourReservation.'.$name])) {
				$queryData['conditions']['TourReservation.'.$name.'_enc'] = $encrypt->encrypt($queryData['conditions']['TourReservation.'.$name]);
				unset($queryData['conditions']['TourReservation.'.$name]);
			}
			if (!empty($queryData['conditions'][$name])) {
				$queryData['conditions'][$name.'_enc'] = $encrypt->encrypt($queryData['conditions'][$name]);
				unset($queryData['conditions'][$name]);
			}

			if (!empty($queryData['conditions']['TourReservation.'.$name.' like'])) {
				$val = trim($queryData['conditions']['TourReservation.'.$name.' like'], '%');
				$queryData['conditions']['TourReservation.'.$name.'_enc like'] = '%' . $encrypt->encrypt($val) . '%';
				unset($queryData['conditions']['TourReservation.'.$name.' like']);
			}
			if (!empty($queryData['conditions'][$name.' like'])) {
				$val = trim($queryData['conditions'][$name.' like'], '%');
				$queryData['conditions'][$name.'_enc like'] = '%' . $encrypt->encrypt($val) . '%';
				unset($queryData['conditions'][$name.' like']);
			}
		}
		return $queryData;
	}
}

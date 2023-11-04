<?php
App::uses('AppModel', 'Model');
/**
 * OfficeBusinessHour Model
 *
 * @property Client $Client
 * @property Office $Office
 * @property Staff $Staff
 */
class OfficeBusinessHour extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
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
		'office_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'start_day' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'end_day' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'start_day_unixtime' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'end_day_unixtime' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'start_time' => array(
			'time' => array(
				'rule' => array('time'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'end_time' => array(
			'time' => array(
				'rule' => array('time'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'day_of_week' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'staff_id' => array(
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

	public function getSpecialBusinessHours($officeId) {

		$time = strtotime(date('Y-m-d 00:00:00'));
		$options = array(
				'conditions' => array(
						'OfficeBusinessHour.office_id' => $officeId,
						'OfficeBusinessHour.end_day_unixtime >='=>$time,
						'OfficeBusinessHour.delete_flg' => 0,
				),
				'order'=>'end_day_unixtime desc',
				'recursive' => -1,
		);
		$results = $this->find('all', $options);
		return $results;
	}

	/**
	 * アップデートするデータのチェック
	 */
	public function updateDataCheck($groupKey, $startDayUnixtime, $endDayUnixtime) {
		$options = array(
				'conditions' => array(
						'OfficeBusinessHour.group_key' => $groupKey,
						'OfficeBusinessHour.start_day_unixtime' => $startDayUnixtime,
						'OfficeBusinessHour.end_day_unixtime' => $endDayUnixtime,
				),
				'recursive' => -1,
		);
		$result = $this->find('all', $options);
		return $result;
	}

	/**
	 * 日付重複チェック
	 */
	public function dateDuplicateCheck($officeId, $startDayUnixtime, $endDayUnixtime,$notId = '') {
		$options = array(
				'conditions' => array(
						'OfficeBusinessHour.office_id' => $officeId,
						'OfficeBusinessHour.delete_flg' => 0,
						'OR' => array(
								'OR' => array(
										array(
												'OfficeBusinessHour.start_day_unixtime <=' => $startDayUnixtime,
												'OfficeBusinessHour.end_day_unixtime >=' => $startDayUnixtime,
										),
										array(
												'OfficeBusinessHour.start_day_unixtime <=' => $endDayUnixtime,
												'OfficeBusinessHour.end_day_unixtime >=' => $endDayUnixtime,
										),
								),
								array(
										array(
												'OfficeBusinessHour.start_day_unixtime >=' => $startDayUnixtime,
												'OfficeBusinessHour.start_day_unixtime <=' => $endDayUnixtime,
										),
										array(
												'OfficeBusinessHour.end_day_unixtime >=' => $startDayUnixtime,
												'OfficeBusinessHour.end_day_unixtime <=' => $endDayUnixtime,
										),
								),
						),
				),
				'recursive' => -1,
		);

		if(!empty($notId)) {
		  $options['conditions'] += array('OfficeBusinessHour.id <>'=>$notId);
		}

		$result = $this->find('all', $options);

		return $result;
	}

	/**
	 * 営業所 特別営業時間取得
	 */
	public function getOfficeBusinessHour($id) {

		$options = array(
				'conditions' => array(
						'OfficeBusinessHour.id' => $id,
						'OfficeBusinessHour.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		$result = $this->find('first', $options);
		return $result['OfficeBusinessHour'];
	}

	//設定されている特別営業時間がクライアントの特別営業時間か確認
	public function getOfficeBusinessHourByClientId($officeBusinessHourId,$clientId) {
	  return (bool)$this->find('count',array('conditions'=>array('id'=>$officeBusinessHourId,'client_id'=>$clientId)));
	}


}

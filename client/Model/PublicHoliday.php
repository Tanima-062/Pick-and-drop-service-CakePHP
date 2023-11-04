<?php
App::uses('AppModel', 'Model');
/**
 * PublicHoliday Model
 *
 */
class PublicHoliday extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'date' => array(
			'date' => array(
				'rule' => array('date'),
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
	);

	/**
	 * 祝日か登録されているかどうか判定する関数
	 *
	 * 存在する場合はtrue
	 * 存在しない場合はfalse
	 *
	 * @return bool
	 */
	public function checkRegistPublicHoliday($date) {

	  return (bool)$this->find('count', array(
	      'conditions' => array(
	          'date'=>$date
	      ),
	      'recursive' => -1,
	      'callbacks' => false
	  ));
	}

	/**
	 * 指定の日付の曜日番号と曜日識別子を返却する
	 *
	 * identifier 曜日識別子
	 * number 曜日番号
	 *
	 * @return array
	 */
	public function getDayInfo($date) {
		//祝日判定
		$publicHoliday = (bool)$this->find('count', array(
				'conditions' => array(
						'date'=>$date
				),
				'recursive' => -1,
				'callbacks' => false,
		));

		$dayInfo = array();
		if($publicHoliday) {
			$dayInfo['identifier'] = 'hol';
			$dayInfo['number'] = '7';
		} else {
			$dayInfo['identifier'] = mb_strtolower(date('D',strtotime($date)));
			$dayInfo['number'] = date('w',strtotime($date));
		}

		$dayInfo['date'] = $date;

		return $dayInfo;
	}

	/**
	 * 指定の月の祝日リストを返却する
	 *
	 * date 日付
	 * name 祝日名
	 *
	 * @return array
	 */
	public function getHolidaysByMonth($date) {
		$currentMonth = date('Y-m', strtotime($date));
		$dayOfMonth = date('t', strtotime($date));
		$firstDay = $currentMonth.'-'.'01';
		$lastDay = $currentMonth.'-'.$dayOfMonth;

		$holidays = $this->find('all',
			array(
				'fields' => array(
					"DATE_FORMAT(PublicHoliday.date, '%e') AS day",
					'PublicHoliday.date',
					'PublicHoliday.name'
				),
				'conditions' => array(
					'PublicHoliday.date >=' => $firstDay,
					'PublicHoliday.date <=' => $lastDay,
					'PublicHoliday.delete_flg' => 0
				)
			)
		);

		return Hash::combine($holidays, '{n}.0.day', '{n}.PublicHoliday');
	}
}

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
				'callbacks' => false
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

	/**
	 * 営業日取得
	 */
	public function getBusinessDay() {
		$currentTime = strtotime('now');
		$currentYear = date('Y', $currentTime);
		$currentMonth = date('n', $currentTime);
		$today = date('j', $currentTime);
		$businessDayCount = 0;
		if ($today >= 3) {
			$holidays = $this->getHolidaysByMonth(date('Y-m', $currentTime));
			if ($currentMonth == 1) {
				// 1/2 & 1/3 が休日になるが、祝日マスタには登録できない
				// （祝日とすると、店舗の営業時間も祝日仕様になってしまう）
				$holidays[2] = $currentYear.'-01-02'; // emptyでなければ何でも良い
				$holidays[3] = $currentYear.'-01-03';
			}

			for ($i = 1; $i <= $today; $i++) {
				$timeStamp = mktime(0, 0, 0, $currentMonth, $i, $currentYear);
				$dayOfWeek = date('w', $timeStamp);
				if ($dayOfWeek == 0 || $dayOfWeek == 6) {
					continue;
				} elseif (!empty($holidays[$i])) {
					continue;
				}
				$businessDayCount++;
			}
		}
		return $businessDayCount;
	}

	/**
	 * 指定の日付の曜日番号と曜日識別子を全て返却する
	 * identifier 曜日識別子
	 * number 曜日番号
	 *
	 * @return array
	 */
	public function getDayInfoMulti($dates) {
		//祝日判定
		$publicHoliday = $this->find('list', array(
			'fields' => array(
				'id',
				'date',
			),
			'conditions' => array(
				'date' => $dates,
				'delete_flg' => 0
			),
			'recursive' => -1,
			'callbacks' => false
		));

		$dayInfo = array();
		foreach ($dates as $date) {
			if (in_array($date, $publicHoliday)) {
				$dayInfo[$date] = 'hol';
			} else {
				$dayInfo[$date] = mb_strtolower(date('D', strtotime($date)));
			}
		}

		return $dayInfo;
	}

}

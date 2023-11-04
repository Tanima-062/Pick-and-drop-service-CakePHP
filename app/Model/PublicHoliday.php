<?php

App::uses('AppModel', 'Model');

/**
 * PublicHoliday Model
 *
 */
class PublicHoliday extends AppModel {

	protected $cacheConfig = '1day';

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
		$publicHoliday = (bool) $this->findC('count', array(
			'conditions' => array(
				'date' => $date,
				'delete_flg' => 0
			),
			'recursive' => -1,
			'callbacks' => false
		));

		$dayInfo = array();
		if ($publicHoliday) {
			$dayInfo['identifier'] = 'hol';
			$dayInfo['number'] = '7';
		} else {
			$dayInfo['identifier'] = mb_strtolower(date('D', strtotime($date)));
			$dayInfo['number'] = date('w', strtotime($date));
		}

		$dayInfo['date'] = $date;

		return $dayInfo;
	}

	/**
	 * 指定の日付の範囲の曜日番号と曜日識別子を返却する
	 *
	 * $date 開始日
	 * $dateTo 終了日
	 *
	 * identifier 曜日識別子
	 * number 曜日番号
	 *
	 * @return array
	 */
	public function getMultiDayInfo($date, $dateTo) {
		//祝日判定
		$ret = $this->findC('all', array(
			'fields' => array('date'),
			'conditions' => array(
				'date between ? and ? ' => array($date, $dateTo),
				'delete_flg' => 0
			),
			'recursive' => -1,
			'callbacks' => false
		));
		$publicHoliday = Hash::combine($ret,'{n}.PublicHoliday.date','{n}.PublicHoliday.date');
		$dayInfo = array();

		while(strtotime($date) <= strtotime($dateTo)){
			if (isset($publicHoliday[$date])) {
				$dayInfo[$date]['identifier'] = 'hol';
				$dayInfo[$date]['number'] = '7';
			} else {
				$dayInfo[$date]['identifier'] = mb_strtolower(date('D', strtotime($date)));
				$dayInfo[$date]['number'] = date('w', strtotime($date));
			}
			$date = date('Y-m-d',strtotime($date. " +1 day"));
		}

		return $dayInfo;
	}

}

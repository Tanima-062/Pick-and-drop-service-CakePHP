<?php
App::uses('AppShell', 'Console/Command');

require_once("notice_class.php");

class ReservationAlertShell extends AppShell {
	public $uses = array('Reservation');

	private $roomId = 41382981;	// レンタカー開発チーム(社内)
	private $userType = 2;		// 自動投稿システム2号
	private $hour = 1;
	private $cnt = 1;

	public function startup() {
		parent::startup();
	}

	public function main() {
		// 本番以外は回さない
		if (!IS_PRODUCTION) {
			return 0;
		}

		$now = time();
		if (strtotime('01:00:00') <= $now && $now < strtotime('08:00:00')) {
			return 0;
		}

		$time = strtotime("-{$this->hour} hour");

		$cnt = $this->countReservation($time);

		if ($cnt < $this->cnt) {
			$this->notice(sprintf('直近%s時間の予約件数が%s件です。(%s)', $this->hour, $cnt, gethostname()), '予約アラート通知');
		}

		return 0;
	}

	private function countReservation($time) {
		return $this->Reservation->find('count', array(
			'conditions' => array(
				'Reservation.created >=' => date('Y-m-d H:i:s', $time),
			),
			'recursive' => -1,
		));
	}

	private function notice($message, $subject) {
		$notice = new Notice($this->roomId, $this->userType);
		$ret = $notice->exec_notice($message, $subject);
		if (!$ret) {
			$this->log(sprintf("通知エラー([%s]%s)", $subject, $message), LOG_DEBUG);
		}
	}

}

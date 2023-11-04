<?php
App::uses('AppShell', 'Console/Command');

class StatisticsShell extends AppShell {

	public $uses = array('Reservation','Statistic');

	public function main() {

		// 統計データインサート
		$this->Reservation->statisticsDataInsert();

	}

	public function diffDelete() {

		$this->autoRender = false;

		// 予約テーブルとの誤差（キャンセルされたものを成約テーブルから論理削除）
		$this->Statistic->diffDataDelete();
	}
}

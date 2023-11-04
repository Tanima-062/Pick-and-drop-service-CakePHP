<?php
App::uses('AppShell', 'Console/Command');

class ContractsShell extends AppShell {

	public $uses = array('Reservation');

	public function main() {

		// 成約データ作成
		$this->Reservation->contractDataInsert();

	}
}

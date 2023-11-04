<?php
App::uses('AppShell', 'Console/Command');
App::uses('CakeSession', 'Model/Datasource');



class MailStatusShell extends AppShell {

	public $uses = array('Reservation');

	public function main() {

		$this->Reservation->batchMailStatus();

	}
}

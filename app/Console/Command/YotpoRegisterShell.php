<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('YotpoAPIComponent', 'Controller/Component');

class YotpoRegisterShell extends AppShell {

	public function startup() {
		$collection = new ComponentCollection();
		$this->controller = new Controller();
		$this->YotpoAPI = new YotpoAPIComponent($collection);
		$this->YotpoAPI->startup($this->controller);
		parent::startup();
	}

	public function main() {
                if (!IS_PRODUCTION) {
                        return true;
                }
		$now = date('Y-m-d H:i:s');
		echo "Yotpo Register Batch Start : $now \n";

		$registerData = $this->YotpoAPI->getYotpoRegisterData();
		$this->YotpoAPI->addUpdateClientIDProductNoCheck($registerData);

		$now = date('Y-m-d H:i:s');
		echo "Yotpo Register Batch End   : $now \n";
	}
}

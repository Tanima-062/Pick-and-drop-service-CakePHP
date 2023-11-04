<?php
App::uses('AppController', 'Controller');

class ResetController extends AppController {

	public $uses = false;
	public $autoRender = false;

	public function index() {
		if (IS_PRODUCTION) {
			throw new NotFoundException();
		}
		$this->Cookie->destroy();
		session_destroy();
		echo '<a href="/rentacar/" target="_blank">レンタカーTOPへ</a>';
	}

}

<?php
App::uses('Controller', 'Controller');

class ApiErrorController extends Controller {
	public $uses = false;
	public $autoRender = false;

	public function index() {
		$statusCode = ($this->params['status_code']) ? $this->params['status_code'] : 503;
		$this->response->statusCode($statusCode);
	}
}

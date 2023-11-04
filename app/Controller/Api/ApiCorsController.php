<?php
App::uses('BaseRestApiController', 'Controller');

class ApiCorsController extends BaseRestApiController {

	public function index() {
		$this->ApiCommon->setCorsHeader();
	}
}

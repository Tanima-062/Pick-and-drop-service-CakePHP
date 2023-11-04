<?php
App::uses('BaseRestApiController', 'Controller');

class BudgetCarClassesController extends BaseRestApiController {
	// バジェット
	protected $clientId = Constant::BUDGET_CLIENT_ID;

	public $uses = array('CarClass');

	public function index() {
		$ret = $this->CarClass->getCarClassListWithCarType($this->clientId);

		if (empty($ret)) {
			return;
		}

		$car_class_list = array();

		foreach ($ret as $v) {
			$cc = $v['CarClass'];
			$ct = $v['CarType'];

			$car_class = array(
				'car_class_id'		 => $cc['id'],
				'car_class_name'	 => $cc['name'],
				'car_type_id'		 => $ct['id'],
				'car_type_name'		 => $ct['name'],
			);

			$car_class_list[] = $car_class;
		}

		$this->responseData['response'] = array(
			'car_class_list' => $car_class_list,
		);

	}

}

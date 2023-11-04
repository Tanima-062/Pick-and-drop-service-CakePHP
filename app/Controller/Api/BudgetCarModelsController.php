<?php
App::uses('BaseRestApiController', 'Controller');

class BudgetCarModelsController extends BaseRestApiController {
	// バジェット
	protected $clientId = Constant::BUDGET_CLIENT_ID;

	public $uses = array('CarModel');

	public function index() {
		$ret = $this->CarModel->getCarModelListWithAutomaker($this->clientId);

		if (empty($ret)) {
			return;
		}

		$car_model_list = array();

		foreach ($ret as $v) {
			$a = $v['Automaker'];
			$c = $v['CarModel'];

			$car_model = array(
				'car_model_id'		 => $c['id'],
				'car_model_name'	 => $c['name'],
				'automaker_id'		 => $a['id'],
				'automaker_name'	 => $a['name'],
			);

			$car_model_list[] = $car_model;
		}

		$this->responseData['response'] = array(
			'car_model_list' => $car_model_list,
		);

	}

}

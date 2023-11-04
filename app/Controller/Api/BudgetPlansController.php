<?php
App::uses('BaseRestApiController', 'Controller');

class BudgetPlansController extends BaseRestApiController {
	// バジェット
	protected $clientId = Constant::BUDGET_CLIENT_ID;

	public $uses = array('Commodity');

	public function index() {
		$ret = $this->Commodity->getClientPlans($this->clientId);

		if (empty($ret)) {
			return;
		}

		$plan_list = array();

		foreach ($ret as $v) {
			$co = $v['Commodity'];
			$ci = $v['CommodityItem'];
			$ct = $v[0];

			$plan = array(
				'plan_id'			 => $ci['id'],
				'plan_name'			 => $co['name'],
				'available_from'	 => $ct['available_from'],
				'available_to'		 => $ct['available_to'],
				'car_class_id'		 => $ci['car_class_id'],
				'car_model_id'		 => isset($ci['car_model_id']) ? $ci['car_model_id'] : '',
				'is_published'		 => $co['is_published'],
			);

			$plan_list[] = $plan;
		}

		$this->responseData['response'] = array(
			'plan_list' => $plan_list,
		);

	}

}

<?php
App::uses('BaseRestApiController', 'Controller');

class BudgetStockGroupsController extends BaseRestApiController {
	// バジェット
	protected $clientId = Constant::BUDGET_CLIENT_ID;

	public $uses = array('StockGroup');

	public function index() {
		$ret = $this->StockGroup->getStockGroupListWithPrefecture($this->clientId);

		if (empty($ret)) {
			return;
		}

		$stock_group_list = array();

		foreach ($ret as $v) {
			$s = $v['StockGroup'];
			$p = $v['Prefecture'];

			$stock_group = array(
				'stock_group_id'	 => $s['id'],
				'stock_group_name'	 => $s['name'],
				'prefecture_id'		 => $p['id'],
				'prefecture_name'	 => $p['name'],
			);

			$stock_group_list[] = $stock_group;
		}

		$this->responseData['response'] = array(
			'stock_group_list' => $stock_group_list,
		);

	}

}

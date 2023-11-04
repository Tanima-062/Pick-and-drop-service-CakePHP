<?php
App::uses('BaseRestApiController', 'Controller');

class SkyscannerPlansController extends BaseRestApiController {
	public $components = array('Skyscanner');
	public $uses = array(
		'CommoditySkyscanner',
		'Area',
		'Office',
		'OfficeStation',
		'Landmark',
		'Station',
		'SearchSkyscanner',
	);
	
	public function index() {
		if (empty($this->request->params)) {
			$this->response->statusCode(404);
			return;
		}

		// バリデーションチェック
		$this->SearchSkyscanner->set($this->request->params);
		if (!$this->SearchSkyscanner->validates()) {
			$this->log($this->SearchSkyscanner->validationErrors, 'skyscanner');
			return;
		}

		// APIのパラメータからスカイチケットのパラメータに変換する
		$params = $this->Skyscanner->getSearchParams($this->request->params);

		//年月日を連結
		$params['from'] = $params['year'] . '-' . $params['month'] . '-' . $params['day'];
		$params['to'] = $params['return_year'] . '-' . $params['return_month'] . '-' . $params['return_day'];

		$fromDatetime = $params['from'] . ' ' . str_replace('-',':',$params['time']);
		$toDatetime = $params['to'] . ' ' . str_replace('-',':',$params['return_time']);

		/**
		 * 市区町村の場合はディープリンク用のエリアIDを取得
		 */
		if ($params['place'] == '1') {
			$params['area_id'] = $this->Office->getOfficeAreaIdList(array(
				'city_id' => $params['city_id']
			));
		}
		if (!empty($params['return_way']) && $params['return_place'] == '1') {
			$params['return_area_id'] = $this->Office->getOfficeAreaIdList(array(
				'city_id' => $params['return_city_id']
			));
		}

		$response = array();
		$query = $this->CommoditySkyscanner->getCommodityQuery($params);

		if(!empty($query)) {
			// 商品情報を取得
			$this->paginate = $query;
			$response = $this->paginate();
		}
		
		$this->responseData = $response;

	}

}

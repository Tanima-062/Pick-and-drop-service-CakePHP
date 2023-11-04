<?php
App::uses('BaseRestApiController', 'Controller');

class AjaxPlanInfoController extends BaseRestApiController {

	public $uses = array('Commodity', 'CommodityImage', 'CarModel');

	public function index() {
		define('MAX_AGE', 3600);

		$this->HttpHeader = $this->Components->load('HttpHeader');
		$this->HttpHeader->initialize($this);

		if ($this->request->is('ajax')) {
			if (empty($this->params['id']) && empty($this->request->query('id'))) {
				$this->response->statusCode(404);
				return;
			} else {
				if (!empty($this->params['id'])) {
					$id = $this->params['id'];
				} else {
					if (!preg_match('/^[1-9][0-9]*$/', $this->request->query('id'))) {
						$this->response->statusCode(404);
						return;
					}
					$id = $this->request->query('id');
				}
				$commodityInfo = $this->Commodity->getCommodityInfoByCommodityItemId($id);
				if (empty($commodityInfo)) {
					$this->response->statusCode(404);
					return;
				}
				$commodityImages = array();
				$tmp = $this->CommodityImage->getImageByCommodityId($commodityInfo['Commodity']['id']);
				if (!empty($tmp)) {
					foreach ($tmp as $v) {
						if (!empty($v['image_relative_url'])) {
							$commodityImages[] = [
								'url' => $v['image_relative_url'],
								'remark' => $v['remark']
							];
						}
					}
				}
				$carModels = array();
				$tmp = $this->CarModel->getCarModelListByClientIdAndCarClassId($commodityInfo['Commodity']['client_id'], $commodityInfo['CommodityItem']['car_class_id']);
				if (!empty($tmp)) {
					foreach ($tmp as $v) {
						$carModels[] = $v['CarModel']['name'];
					}
				}
				$this->response->header($this->HttpHeader->getPublicCacheConfig(MAX_AGE));
				$this->responseData = array(
					'id' => $id,
					'client_id' => $commodityInfo['Commodity']['client_id'],
					'description' => $commodityInfo['Commodity']['description'],
					'remark' => $commodityInfo['Commodity']['remark'],
					'plan_name' => $commodityInfo['Commodity']['name'],
					'images' => $commodityImages,
					'models' => $carModels,
					'car_type_name' => $commodityInfo['CarType']['name'],
					'flg_model_select' => !empty($commodityInfo['CommodityItem']['car_model_id']),
				);
			}
		} else {
			$this->response->statusCode(404);
			return;
		}
	}

}

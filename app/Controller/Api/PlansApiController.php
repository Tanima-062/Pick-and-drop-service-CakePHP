<?php
App::uses('BaseRestApiController', 'Controller');

class PlansApiController extends BaseRestApiController {
	public $components = array('SearchesApi', 'PlanUtil', 'CancelPolicy');
	public $uses = array(
		'SearchesApiCommodity',
		'SearchesApiValidation',
		'PlansApiCalcValidation',
		'CommodityItem',
		'Equipment',
		'CommodityEquipment',
		'CommodityPrivilege',
		'Landmark',
		'DisclaimerCompensation',
		'DropOffAreaRate',
		'Office',
		'Client',
		'CancelFee',
	);

	// 検索ボックスのオプションリスト
	protected $options = array(
		4	 => 'カーナビ搭載',
		5	 => 'ETC搭載',
		6	 => 'スタッドレスタイヤ',
		7	 => 'タイヤチェーン',
		8	 => '4WD',
		12	 => 'ETCカード',
		13	 => 'NOC補償',
		14	 => '運転サポート',
		15	 => 'バックモニター',
		16	 => 'AUXケーブル',
		17	 => 'Bluetooth',
		18	 => 'ドライブレコーダー',
		99	 => 'WEB決済可能',
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->ApiCommon->setCorsHeader();
		// $this->options = Constant::options();
		// $this->displayClients = Constant::displayClients();
	}

	// プランを取得する
	public function view($id) {
		$ret = $this->SearchesApiCommodity->getCommodityInfoByCommodityItemId($id);

		if (empty($ret)) {
			return array();
		}

		$commodity = $ret['Commodity'];
		$commodityItem = $ret['CommodityItem'];
		$client = $ret['Client'];

		// 車種取得
		$carInfo = $this->CommodityItem->getCarInfo($id);
		$carInfo = $carInfo[$id];

		// レビュー取得
		$YotpoReview = new YotpoReview();
		$ratings = $YotpoReview->getRatingsGroupByClientId();

		// webサーバをurlとして返したいので
		$img_url = $this->SearchesApiCommodity->getImageUrl();
		$sipp_code = !empty($commodityItem['sipp_code']) ? $commodityItem['sipp_code'] : '';
		$car_model = $carInfo['CarModel'][0];
		$car_registration = $commodity['new_car_registration'];
		$model_select = !empty($commodityItem['car_model_id']); // 車種指定
		$rating = !empty($ratings[$client['id']]) ? $ratings[$client['id']] : array();

		// プラン名を生成する
		$plan_name = $this->SearchesApiCommodity->createPlanName($carInfo['CarType'], $carInfo['CarModel'], $model_select);

		$optionCategories = Constant::optionCategories();

		// 装備のリストを生成する
		$equipments = array(
			array(
				'equipmentName'		 => '免責補償',
				'description'		 => '免責補償料金込みプラン',
				'optionCategoryId'	 => 3,
			)
		);

		// 装備セット
		$equipmentList = $this->Equipment->getEquipment();
		$commodityEquipment = $this->CommodityEquipment->getEquipmentData($commodity['id']);

		foreach ($equipmentList as $equipment) {
			$equipment = $equipment['Equipment'];
			if (empty($commodityEquipment[$equipment['id']]) ||empty($optionCategories[$equipment['option_category_id']])) {
				continue;
			}

			$equipments[] = array(
				'equipmentName'		 => $equipment['name'],
				'description'		 => trim($equipment['description']),
				'optionCategoryId'	 => (int)$equipment['option_category_id'],
			);
		}

		if (empty($commodity['transmission_flg'])) {
			$equipments[] = array(
				'equipmentName'		 => 'AT車',
				'description'		 => 'オートマチックトランスミッションの車です',
				'optionCategoryId'	 => 0,
			);
		}

		// オプションのリストを生成する
		$options = array();
		$privileges = $this->CommodityPrivilege->getPrivileges($commodity['id']);

		foreach ($privileges as $privilege) {
			$privilege = $privilege['Privilege'];
			if (empty($optionCategories[$privilege['option_category_id']])) {
				continue;
			}

			// シートとその他で分ける
			$ctg = !empty($privilege['option_flg']) ? 'sheets' : 'others';
			$options[$ctg][] = array(
				'optionName'		 => $privilege['name'],
				'optionCategoryId'	 => (int)$privilege['option_category_id'],
			);
		}

		$plan_info = array(
			'planId'			 => (int)$id,
			'planName'			 => $plan_name,
			'planImage'			 => $img_url . $client['id'] . '/' . $commodity['image_relative_url'],
			'clientId'			 => (int)$client['id'],
			'clientName'		 => $client['name'],
			'carTypeId'			 => (int)$carInfo['CarType']['id'],
			'sippCode'			 => $sipp_code,
			'shops'				 => array(),
			'equipments'		 => $equipments,
			'options'			 => $options,
			'smoking'			 => !empty($commodity['smoking_flg']),
			'capacity'			 => (int)$car_model['capacity'],
			'baggage'			 => (int)$car_model['trunk_space'],
			'modelSelect'		 => $model_select,
			'newCar'			 => ($car_registration == 1 || $car_registration == 2),
			'payment'			 => !empty($commodity['payment_method']),
			'basePrice'			 => 0,
			'currency'			 => '',
			'stockCount'		 => 0,
			'reviewScore'		 => isset($rating['rating']) ? (double)number_format(floatval($rating['rating']), 1, '.', '') : null,
			'reviewCount'		 => isset($rating['count']) ? (int)$rating['count'] : 0,
		);

		$this->responseData = $plan_info;
		return $this->responseData;
	}

	// プラン詳細を取得する。
	public function detail($id) {
		if (empty($this->request->data)) {
			// パラメータなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		// 1次元配列にする
		$params = $this->SearchesApi->flattenParams($this->request->data);

		// バリデーションチェック
		$this->SearchesApiValidation->set($params);
		if (!$this->SearchesApiValidation->validates()) {
			throw new ApiException($this->SearchesApiValidation->validationErrors);
		}

		// APIのパラメータからスカイチケットのパラメータに変換する
		$params = $this->SearchesApi->getSearchParams($params);

		$from = $params['startDate'] . ' ' . $params['startTime'] . ':00';
		$to = $params['endDate'] . ' ' . $params['endTime'] . ':00';

		// 商品アイテムデータ取得
		$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($id, $params['startDate']);

		if (empty($commodityItemPriceData)) {
			// プランなし
			throw new ApiException(ApiException::NO_PLAN, 404);
		}

		$commodityItem = $commodityItemPriceData['CommodityItem'];
		// クライアントID
		$params['clients'] = array($commodityItem['client_id']);

		// 商品データ取得
		$commodityData = $this->SearchesApiCommodity->getCommodityData($commodityItem['commodity_id'], $params);

		if (empty($commodityData)) {
			// プランなし
			throw new ApiException(ApiException::NO_PLAN, 404);
		}

		$commodity = $commodityData['Commodity'];
		$client = $commodityData['Client'];

		// レビュー取得
		$YotpoReview = new YotpoReview();
		$ratings = $YotpoReview->getRatingsGroupByClientId();

		// webサーバをurlとして返したいので
		$img_url = $this->SearchesApiCommodity->getImageUrl();
		// $sipp_code = !empty($commodityItem['sipp_code']) ? $commodityItem['sipp_code'] : '';
		$car_model = $commodityItemPriceData['CarModel'][0];
		$car_registration = $commodity['new_car_registration'];
		$model_select = !empty($commodityItem['car_model_id']); // 車種指定
		$rating = !empty($ratings[$client['id']]) ? $ratings[$client['id']] : array();

		// プラン名を生成する
		$plan_name = $this->SearchesApiCommodity->createPlanName($commodityItemPriceData['CarType'], $commodityItemPriceData['CarModel'], $model_select);

		// 貸出営業所一覧データ取得
		$officeDatas = $commodityData['RentOffice'];

		$stockCheckParams = array(
			'from_office' => Hash::extract($officeDatas, '{n}.id'),
			'from' => $from,
			'to' => $to,
			'cars_count' => 1,
		);

		// 営業所毎の在庫数取得
		$officeStocks = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $stockCheckParams);

		$shops = array();
		$stockCount = null;
		foreach ($officeDatas as $key => $officeData) {
			$officeId = $officeData['id'];
			$officeName = $officeData['name'];

			if (empty($officeStocks[$officeId])) {
				$officeName = $officeData['name'] . '（在庫なし）';
			} else if (!isset($stockCount) || $stockCount > $officeStocks[$officeId]) {
				// 在庫がある店舗で最小の在庫を取得
				$stockCount = $officeStocks[$officeId];
			}

			$shops[] = array(
				'shopId'	 => (int)$officeId,
				'shopName'	 => $officeName,
				'openTime'	 => $officeData['office_hours_from'],
				'closeTime'	 => $officeData['office_hours_to'],
				'tel'		 => $officeData['tel'],
				'address'	 =>	trim($officeData['address']),
				'access'	 => $officeData['access_dynamic'],
				'nearestTransport'	 =>	(int)$officeData['nearest_transport'],
				'methodOfTransport'	 =>	(int)$officeData['method_of_transport'],
				'requiredTransportTime'	 =>	(int)$officeData['required_transport_time'],
				'enabled'	 => !empty($officeStocks[$officeId]),
			);
		}

		// 返却営業所一覧データ取得
		$returnOfficeDatas = $commodityData['ReturnOffice'];

		$return_shops = array();
		foreach ($returnOfficeDatas as $key => $returnOffice) {
			$officeId = $returnOffice['id'];
			$officeName = $returnOffice['name'];

			$return_shops[] = array(
				'shopId'	 => (int)$officeId,
				'shopName'	 => $officeName,
				'openTime'	 => $returnOffice['office_hours_from'],
				'closeTime'	 => $returnOffice['office_hours_to'],
				'tel'		 => $returnOffice['tel'],
				'address'	 =>	trim($returnOffice['address']),
				'access'	 => $returnOffice['access_dynamic'],
				'enabled'	 => true,
			);
		}

		list($dayNight, $period, $period24) = $this->PlanUtil->getPeriodArray($from, $to);

		// 基本料金
		$price = $this->PlanUtil->calcBasicPrice(
			$commodityItemPriceData['CommodityPrice'],
			$commodity['day_time_flg'],
			$from,
			$to,
			$period
		);

		// 免責補償料金
		$disclaimerCompensationPrice = $this->DisclaimerCompensation->getFee(
			$commodityItemPriceData['CarClass']['id'],
			$params['startDate'],
			$params['endDate'],
			$period,
			$period24
		);

		$basicCharge = $price + $disclaimerCompensationPrice;

		$optionCategories = Constant::optionCategories();

		// 装備のリストを生成する
		$equipments = array(
			array(
				'equipmentName'		 => '免責補償',
				'description'		 => '免責補償料金込みプラン',
				'optionCategoryId'	 => 3,
			)
		);

		// 装備セット
		$equipmentList = $this->Equipment->getEquipment();
		$commodityEquipment = $this->CommodityEquipment->getEquipmentData($commodityItem['commodity_id']);

		foreach ($equipmentList as $equipment) {
			$equipment = $equipment['Equipment'];
			if (empty($commodityEquipment[$equipment['id']]) ||empty($optionCategories[$equipment['option_category_id']])) {
				continue;
			}

			$equipments[] = array(
				'equipmentName'		 => $equipment['name'],
				'description'		 => trim($equipment['description']),
				'optionCategoryId'	 => (int)$equipment['option_category_id'],
			);
		}

		if (empty($commodity['transmission_flg'])) {
			$equipments[] = array(
				'equipmentName'		 => 'AT車',
				'description'		 => 'オートマチックトランスミッションの車です',
				'optionCategoryId'	 => 0,
			);
		}

		// オプションのリストを生成する
		$options = array();
		$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($commodityItem['commodity_id'], $period, $period24);

		foreach ($commodityPrivilegeData as $privilege) {
			$price = $privilege[0]['Sum'];
			$privilege = $privilege['Privilege'];

			// シートとその他で分ける
			$ctg = !empty($privilege['option_flg']) ? 'sheets' : 'others';
			$options[$ctg][] = array(
				'optionId'			 => (int)$privilege['id'],
				'optionName'		 => $privilege['name'],
				'optionCategoryId'	 => (int)$privilege['option_category_id'],
				'maximum'			 => (int)$privilege['maximum'],
				'unitName'			 => $privilege['unit_name'],
				'price'				 => (int)$price,
			);
		}

		$this->responseData = array(
			'planId'			 => (int)$id,
			'planName'			 => $plan_name,
			'planImage'			 => $img_url . $client['id'] . '/' . $commodity['image_relative_url'],
			'clientId'			 => (int)$client['id'],
			'clientName'		 => $client['name'],
			'carTypeId'			 => (int)$commodityItemPriceData['CarType']['id'],
			'carTypeName'		 => $commodityItemPriceData['CarType']['name'],
			'description'		 => nl2br($commodity['description'], false),
			'remarks'			 => nl2br($commodity['remark'], false),
			'shops'				 => $shops,
			'returnShops'		 => $return_shops,
			'equipments'		 => $equipments,
			'options'			 => $options,
			'smoking'			 => !empty($commodity['smoking_flg']),
			'capacity'			 => (int)$car_model['capacity'],
			'baggage'			 => (int)$car_model['trunk_space'],
			'modelSelect'		 => $model_select,
			'newCar'			 => ($car_registration == 1 || $car_registration == 2),
			'payment'			 => !empty($commodity['payment_method']),
			'basePrice'			 => $basicCharge,
			'currency'			 => 'JPY',
			'stockCount'		 => (isset($stockCount) ? $stockCount : 0),
			'reviewScore'		 => isset($rating['rating']) ? (double)number_format(floatval($rating['rating']), 1, '.', '') : null,
			'reviewCount'		 => isset($rating['count']) ? (int)$rating['count'] : 0,
			// 予約時に表示する情報
			'cancelPolicy'		 => $this->CancelPolicy->getTextLines($client['id'], $from),
			// INCIDENT-3044 取消手続料の徴収を廃止する
			//'advCancelFee'		 => (int)$this->CancelPolicy->getAdvCancelFee(),
			'clientCancelPolicy' => $client['cancel_policy'],
			'acceptCash'		 => (bool)$client['accept_cash'],
			'acceptCard'		 => (bool)$client['accept_card'],
			'precautions'		 => $client['precautions'],
			'cancelFreeLimit'	 => $this->CancelFee->getCancelFreeLimit($client['id']),
		);
	}

	// プラン詳細を取得する。
	public function detailBasic($id) {
		// IDで検索するので座標関連のルールを削除する
		$rules = $this->SearchesApiValidation->validator();
		unset($rules['latitude'], $rules['longitude'], $rules['returnLatitude'], $rules['returnLongitude']);

		$this->detail($id);
	}

	// 乗捨料金・深夜料金の計算をする
	public function dropoff($id) {
		if (empty($this->request->query)) {
			// パラメータなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		$params = $this->request->query;

		// バリデーションチェック
		$this->PlansApiCalcValidation->set($params);
		if (!$this->PlansApiCalcValidation->validates()) {
			throw new ApiException($this->PlansApiCalcValidation->validationErrors);
		}

		// 車両クラスID取得
		$this->CommodityItem->recursive = -1;
		$commodityItem = $this->CommodityItem->read('car_class_id', $id);

		if (empty($commodityItem)) {
			// プランなし
			throw new ApiException(ApiException::NO_PLAN, 404);
		}

		// 乗り捨てエリア料金
		$dropOffAreaPrice = $this->DropOffAreaRate->getDropOffAreaPrice(
			$params['fromShopId'],
			$params['toShopId'],
			$commodityItem['CommodityItem']['car_class_id']
		);

		// 乗り捨てが出来ない場合(料金0円は乗り捨て可)
		if (!isset($dropOffAreaPrice) && $params['fromShopId'] != $params['toShopId']) {
			// 乗り捨て不可
			throw new ApiException(ApiException::DO_NOT_DROPOFF);
		}

		// 深夜手数料
		$fromData = array(
			'fromOfficeId'	 => $params['fromShopId'],
			'fromTime'		 => $params['startTime'],
		);
		$returnData = array(
			'returnOfficeId' => $params['toShopId'],
			'returnTime'	 => $params['endTime'],
		);
		$lateNightFee = $this->Office->getLateNightFee($fromData, $returnData);

		$this->responseData = array(
			'dropoffPrice'	 => (int)$dropOffAreaPrice,
			'nightPrice'	 => !empty($lateNightFee) ? (int)$lateNightFee : 0,
			'currency'		 => 'JPY',
		);
	}

	// オプションの取得をする
	public function options($id) {
		if (empty($this->request->query)) {
			// パラメータなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		$params = $this->request->query;

		// バリデーションチェック
		$this->PlansApiCalcValidation->set($params);
		if (!$this->PlansApiCalcValidation->validates()) {
			throw new ApiException($this->PlansApiCalcValidation->validationErrors);
		}

		// 商品ID取得
		$this->CommodityItem->recursive = -1;
		$commodityItem = $this->CommodityItem->read(array('client_id', 'commodity_id'), $id);

		if (empty($commodityItem)) {
			// プランなし
			throw new ApiException(ApiException::NO_PLAN, 404);
		}

		$this->Client->recursive = -1;
		$client = $this->Client->read('need_remark', $commodityItem['CommodityItem']['client_id']);

		if (empty($client)) {
			// プランなし
			throw new ApiException(ApiException::NO_PLAN, 404);
		}

		$commodityId = $commodityItem['CommodityItem']['commodity_id'];
		$needRemarks = $client['Client']['need_remark'];

		$from = $params['startDate'] . ' ' . $params['startTime'] . ':00';
		$to = $params['endDate'] . ' ' . $params['endTime'] . ':00';

		list($dayNight, $period, $period24) = $this->PlanUtil->getPeriodArray($from, $to);

		// オプションのリストを生成する
		$options = array();
		$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($commodityId, $period, $period24);

		foreach ($commodityPrivilegeData as $privilege) {
			$price = $privilege[0]['Sum'];
			$privilege = $privilege['Privilege'];

			// シートとその他で分ける
			$ctg = !empty($privilege['option_flg']) ? 'sheets' : 'others';
			$options[$ctg][] = array(
				'optionId'			 => (int)$privilege['id'],
				'optionName'		 => $privilege['name'],
				'optionCategoryId'	 => (int)$privilege['option_category_id'],
				'maximum'			 => (int)$privilege['maximum'],
				'unitName'			 => $privilege['unit_name'],
				'price'				 => (int)$price,
			);
		}

		$this->responseData = array(
			'needRemarks'	 => $needRemarks,
			'currency'		 => 'JPY',
			'options'		 => $options,
		);
	}
}

<?php
App::uses('BaseRestApiController', 'Controller');

/**
 * Class PlanApiController
 * @property Client Client
 * @property CommodityEquipment CommodityEquipment
 * @property CommodityItem CommodityItem
 * @property CommodityPrivilege CommodityPrivilege
 * @property DisclaimerCompensation DisclaimerCompensation
 * @property　　DropOffAreaRate
 * @property Equipment Equipment
 * @property PlanApiCommodity PlanApiCommodity
 * @property PlanUtilComponent PlanUtil
 * @property PlanApiValidation PlanApiValidation
 * @property YotpoReview YotpoReview
 */
class PlanApiController extends BaseRestApiController {

	/**
	 * @var string[]
	 */
	public $components = array(
		'PlanUtil',
	);

	/**
	 * 使用Model一覧
	 * @var string[]
	 */
	public $uses = array(
		'Client',
		'CommodityEquipment',
		'CommodityItem',
		'CommodityPrivilege',
		'DisclaimerCompensation',
		'DropOffAreaRate',
		'Equipment',
		'PlanApiCommodity',
		'PlanApiValidation',
		'YotpoReview',
	);

	/**
	 * プラン検索
	 * @throws Exception
	 */
	public function index() {

		//バリデーションのためパラメータセット
		$req = $this->PlanApiValidation->set($this->request->query);

		// 条件付きのバリデーションを設定
		$this->PlanApiValidation->setComplexValidate();

		// バリデーションチェック
		if (!$this->PlanApiValidation->validates()) {
			throw new ApiException($this->PlanApiValidation->validationErrors);
		}

		// 検索用にパラメータを整形
		$params = $this->trimParameters($this->request);

		// 検索条件に応じた商品のデータを取得するクエリを取得
		$query = $this->PlanApiCommodity->getCommodityQuery($params, 1, false);

		// エラーおよび空チェック
		if (empty($query)) {
			throw new ApiException(ApiException::NO_PLAN);
		}

		// リクエストパラメータ
		$query['request'] = $params;

		// 商品情報を取得
		$this->paginate = $query;
		$response = $this->paginate($this->PlanApiCommodity);

		//存在確認
        if(empty($response)){
            throw new ApiException(ApiException::NO_PLAN);
        }

		//　事前決済専用プランの表示制御
		foreach($response as $key => $value){
			if($value['payment_method'] === "1"){
				unset($response[$key]);
			}
		}

		// エラーおよび空チェック
		if (empty($response)) {
			throw new ApiException(ApiException::NO_PLAN);
		}
		//ソート実行
		$sort = $req["PlanApiValidation"]["sort"];

		if($sort == "1"){
			//おすすめ順
			$sort_recommend = array();
			foreach($response as $key => $value){
				array_push($sort_recommend , $value["recommended"]);
			}
			array_multisort($sort_recommend , SORT_ASC , $response);
		}else if($sort == "3"){
			//レビュー評価が高い順
			$sort_review = array();
			foreach($response as $key => $value){
				array_push($sort_review , $value["reviewScore"]);
			}
			array_multisort($sort_review , SORT_DESC , $response);
		}else if($sort == "4"){
			//車両が新しい順

		}else if($sort == "5"){
			//空港から近い順
			$sort_near = array();
			foreach($response as $key => $value){
				array_push($sort_near , $value["shops"][0]["required_transport_time"]);
			}
			array_multisort($sort_near , SORT_ASC , $response);
		}else{
			//金額が安い順
			$sort_price = array();
			foreach($response as $key => $value){
				array_push($sort_price , $value["basePrice"]);
			}
			array_multisort($sort_price , SORT_ASC , $response);
		}

		//レスポンスに不要なデータ削除
		foreach($response as $key => $value){
			unset($response[$key]['recommended']);
			unset($response[$key]['payment_method']);
		}
		// レスポンスデータ生成
		$this->responseData = $response;
	}

	/**
	 * プラン詳細
	 * @param int $plan_id プランID
	 * @throws Exception
	 */
	public function details($plan_id) {

		//バリデーションのためパラメータセット
		$this->PlanApiValidation->set($this->request->query);

		// 条件付きのバリデーションを設定
		$this->PlanApiValidation->setComplexValidate();

		// バリデーションチェック
		if (!$this->PlanApiValidation->validates()) {
			throw new ApiException($this->PlanApiValidation->validationErrors);
		}

		// 検索用にパラメータを整形
		$params = $this->trimParameters($this->request);

		// 出発日時と返却日時
		$from = $params['datetime']->format('Y-m-d H:i:s');
		$to   = $params['return_datetime']->format('Y-m-d H:i:s');

		$params['start_date'] = $params['datetime']->format('Y-m-d');
		$params['end_date']   = $params['return_datetime']->format('Y-m-d');

		// 商品アイテムデータ取得
		$plan_data = $this->CommodityItem->getCommodityItemPriceData($plan_id, $params['start_date']);

		// 商品アイテムデータなし
		if (empty($plan_data)) {
			throw new ApiException(ApiException::NO_PLAN);
		}

		// 商品アイテム
		$commodity_item = $plan_data['CommodityItem'];

		// クライアントID
		$params['clients'] = array($commodity_item['client_id']);

		// 対象の営業所を取得するための必要パラメータを設定
		$area_id    = isset($params['area_id']) ? $params['area_id'] : NULL;
		$airport_id = isset($params['airport_id']) ? $params['airport_id'] : NULL;
		$station_id = isset($params['station_id']) ? $params['station_id'] : NULL;

		// 貸出店舗に返却する場合は同じパラメータを設定
		if ($params['return_way'] == 0) {
			$r_area_id    = $area_id;
			$r_airport_id = $airport_id;
			$r_station_id = $station_id;
		} else {
			$r_area_id    = isset($params['return_area_id']) ? $params['return_area_id'] : NULL;
			$r_airport_id = isset($params['return_airport_id']) ? $params['return_airport_id'] : NULL;
			$r_station_id = isset($params['return_station_id']) ? $params['return_station_id'] : NULL;
		}

		// 商品データ取得
		$commodity_data = $this->PlanApiCommodity->getCommodityData($commodity_item['commodity_id'], array(
			'from'              => $from,
			'to'                => $to,
			'client_id'         => $commodity_item['client_id'],
			'area_id'           => $area_id,
			'airport_id'        => $airport_id,
			'station_id'        => $station_id,
			'return_area_id'    => $r_area_id,
			'return_airport_id' => $r_airport_id,
			'return_station_id' => $r_station_id,
		));

		// 商品データなし
		if (empty($commodity_data)) {
			throw new ApiException(ApiException::NO_PLAN);
		}

		$commodity = $commodity_data['Commodity'];
		$client    = $commodity_data['Client'];

		// レビュー取得
		$ratings = $this->YotpoReview->getRatingsGroupByClientId();

		$car_model        = $plan_data['CarModel'][0];
		$car_registration = $commodity['new_car_registration'];
		$model_select     = !empty($commodity_item['car_model_id']);
		$rating           = !empty($ratings[$client['id']]) ? $ratings[$client['id']] : array();

		// 営業所毎の在庫数取得
		$office_stocks = $this->CommodityItem->getOfficeStocks($plan_data['CarClass']['id'], array(
			'from_office' => Hash::extract($commodity_data['RentOffice'], '{n}.id'),
			'from'        => $from,
			'to'          => $to,
			'cars_count'  => 1,
		));

		// 出発営業所一覧データ取得
		$shops      = array();
		$stockCount = null;
		foreach ($commodity_data['RentOffice'] as $office_data) {
			$officeName = $office_data['name'];
			if (empty($office_stocks[$office_data['id']])) {
				$officeName .= '（在庫なし）';
			} else if (!isset($stockCount) || $stockCount > $office_stocks[$office_data['id']]) {
				// 在庫がある店舗で最小の在庫を取得
				$stockCount = $office_stocks[$office_data['id']];
			}

			$shops[] = array(
				'shopId'                => intval($office_data['id']),
				'shopName'              => $officeName,
				'openTime'              => $office_data['office_hours_from'],
				'closeTime'             => $office_data['office_hours_to'],
				'tel'                   => $office_data['tel'],
				'address'               => trim($office_data['address']),
				'access'                => $office_data['access_dynamic'],
				'nearestTransport'      => intval($office_data['nearest_transport']),
				'methodOfTransport'     => intval($office_data['method_of_transport']),
				'requiredTransportTime' => intval($office_data['required_transport_time']),
				'enabled'               => !empty($office_stocks[$office_data['id']]),
			);
		}

		// 返却営業所一覧データ取得
		$return_shops = array();
		foreach ($commodity_data['ReturnOffice'] as $return_office) {
			$return_shops[] = array(
				'shopId'    => intval($return_office['id']),
				'shopName'  => $return_office['name'],
				'openTime'  => $return_office['office_hours_from'],
				'closeTime' => $return_office['office_hours_to'],
				'tel'       => $return_office['tel'],
				'address'   => trim($return_office['address']),
				'access'    => $return_office['access_dynamic'],
				'enabled'   => true,
			);
		}

		// レンタル期間から暦日制と24時間制の日数を算出する
		list($dayNight, $period, $period24) = $this->PlanUtil->getPeriodArray($from, $to);

		// 基本料金
		$price = $this->PlanUtil->calcBasicPrice($plan_data['CommodityPrice'], $commodity['day_time_flg'], $from, $to, $period);

		// 免責補償料金
		$dc_price = $this->DisclaimerCompensation->getFee($plan_data['CarClass']['id'], $from, $to, $period, $period24);

		$drop_off_area_price = 0;
        // 乗り捨てエリア料金
        if ($params['return_way'] === "1") {
            // 乗り捨て料金取得
            $drop_off_area_price = $this->DropOffAreaRate->getDropOffAreaPrice($shops[0]['shopId'], $return_shops[0]['shopId'], $commodity_item['car_class_id']);
            // 存在チェック
            if (!isset($drop_off_area_price)) {
                throw new ApiException(ApiException::DO_NOT_DROPOFF);
            }
        }


        // 基本料金 + 免責補償料金 + 乗り捨て料金 
        $basic_charge = $price + $dc_price + $drop_off_area_price;

		//上乗せ率
		$addition_rate = Constant::ADDITIONAL_RATE;
						
		//販売価格計算
		$sales_price = floor($basic_charge * $addition_rate);

		//販売価格切り上げ処理
		$roundup_sales_price = ceil($sales_price/10)*10;

		// オプションカテゴリ一覧取得
		$option_categories = Constant::optionCategories();

		// 装備のリストを生成する
		$equipments = array(
			array(
				'equipmentName'    => '免責補償',
				'description'      => '免責補償料金込みプラン',
				'optionCategoryId' => 3,
			)
		);

		// 装備セット
		$equipment_list      = $this->Equipment->getEquipment();
		$commodity_equipment = $this->CommodityEquipment->getEquipmentData($commodity_item['commodity_id']);

		// 対象の装備セットを抽出
		foreach ($equipment_list as $equipment) {
			if (
				!empty($commodity_equipment[$equipment['Equipment']['id']])
				&& !empty($option_categories[$equipment['Equipment']['option_category_id']])
			) {
				$equipments[] = array(
					'equipmentName'    => $equipment['Equipment']['name'],
					'description'      => trim($equipment['Equipment']['description']),
					'optionCategoryId' => intval($equipment['Equipment']['option_category_id']),
				);
			}
		}

		// フラグを確認してAT車を装備セットに登録
		if (empty($commodity['transmission_flg'])) {
			$equipments[] = array(
				'equipmentName'    => 'AT車',
				'description'      => 'オートマチックトランスミッションの車です',
				'optionCategoryId' => 0,
			);
		}

		// 特典一覧を取得
		$commodity_privilege_data = $this->CommodityPrivilege->getCommodityPrivilegeData($commodity_item['commodity_id'], $period, $period24);

		// オプションのリストを生成する
		$options = array();
		foreach ($commodity_privilege_data as $privilege) {
			// シートとその他で分ける
			$ctg = !empty($privilege['Privilege']['option_flg']) ? 'sheets' : 'others';

			// オプション追加
			$options[$ctg][] = array(
				'optionId'         => intval($privilege['Privilege']['id']),
				'optionName'       => $privilege['Privilege']['name'],
				'optionCategoryId' => intval($privilege['Privilege']['option_category_id']),
				'maximum'          => intval($privilege['Privilege']['maximum']),
				'unitName'         => $privilege['Privilege']['unit_name'],
				'price'            => intval($privilege[0]['Sum']),
			);
		}


		//販売種別
		$sales_type = $params['sales_type'];

		//販売種別によるレスポンス分岐処理
		if(isset($sales_type) && $sales_type !== $commodity_data['Commodity']['sales_type']){

		}else{
			// レスポンスデータ生成
			$this->responseData = array(
				'planId'      => intval($plan_id),
				'planName'    => $this->PlanApiCommodity->createPlanName($plan_data['CarType'], $plan_data['CarModel'], $model_select),
				'planImage'   => "{$client['id']}/{$commodity['image_relative_url']}",
				'clientId'    => intval($client['id']),
				'clientName'  => $client['name'],
				'carTypeId'   => intval($plan_data['CarType']['id']),
				'carTypeName' => $plan_data['CarType']['name'],
				'description' => nl2br($commodity['description'], false),
				'remarks'     => nl2br($commodity['remark'], false),
				'shops'       => $shops,
				'returnShops' => $return_shops,
				'equipments'  => $equipments,
				'options'     => $options,
				'smoking'     => !empty($commodity['smoking_flg']),
				'capacity'    => intval($car_model['capacity']),
				'baggage'     => intval($car_model['trunk_space']),
				'modelSelect' => $model_select,
				'newCar'      => ($car_registration == 1 || $car_registration == 2),
				'payment'     => !empty($commodity['payment_method']),
				'basePrice'   => $basic_charge,
				'salesPrice'  => $roundup_sales_price,
				'currency'    => Configure::read('currency'),
				'stockCount'  => isset($stockCount) ? $stockCount : 0,
				'reviewScore' => isset($rating['rating']) ? doubleval(number_format(floatval($rating['rating']), 1, '.', '')) : null,
				'reviewCount' => isset($rating['count']) ? intval($rating['count']) : 0,
			);
		}
	}

	/**
	 * オプション取得
	 * @param int $plan_id プランID
	 */
	public function options($plan_id) {

		// 商品IDの取得
		$this->CommodityItem->recursive = -1;
		$commodity_item = $this->CommodityItem->read(array('client_id', 'commodity_id'), $plan_id);

		// 商品IDなし
		if (empty($commodity_item)) {
			throw new ApiException(ApiException::NO_PLAN, 404);
		}

		// 備考欄有無の取得
		$this->Client->recursive = -1;
		$client = $this->Client->read('need_remark', $commodity_item['CommodityItem']['client_id']);

		// 備考欄有無なし
		if (empty($client)) {
			throw new ApiException(ApiException::NO_PLAN, 404);
		}

		// 出発日時と返却日時
		$from = $this->request->query('startDateTime');
		$to   = $this->request->query('endDateTime');

		// レンタル期間から暦日制と24時間制の日数を算出する
		list($dayNight, $period, $period24) = $this->PlanUtil->getPeriodArray($from, $to);

		// 特典一覧を取得
		$commodity_privilege_data = $this->CommodityPrivilege->getCommodityPrivilegeData($commodity_item['CommodityItem']['commodity_id'], $period, $period24);

		// オプションのリストを生成する
		$options = array();
		foreach ($commodity_privilege_data as $privilege) {
			// シートとその他で分ける
			$ctg = !empty($privilege['Privilege']['option_flg']) ? 'sheets' : 'others';

			// オプション追加
			$options[$ctg][] = array(
				'optionId'         => intval($privilege['Privilege']['id']),
				'optionName'       => $privilege['Privilege']['name'],
				'optionCategoryId' => intval($privilege['Privilege']['option_category_id']),
				'maximum'          => intval($privilege['Privilege']['maximum']),
				'unitName'         => $privilege['Privilege']['unit_name'],
				'price'            => intval($privilege[0]['Sum']),
			);
		}

		// レスポンスデータ生成
		$this->responseData = array(
			'needRemarks' => $client['Client']['need_remark'],
			'currency'    => Configure::read('currency'),
			'options'     => $options,
		);
	}

	/**
	 * パラメータを整形する
	 * @param CakeRequest $request リクエストパラメータ
	 * @return array
	 * @throws Exception
	 */
	private function trimParameters($request) {

		// 検索用のパラメータを生成
		$params = array();

		// 出発日時
		if (isset($request->query['startDateTime'])) {
			// DateTimeに変換
			$datetime = new DateTime($request->query('startDateTime'));

			$params['datetime'] = $datetime;
			$params['year']     = $datetime->format('Y');
			$params['month']    = $datetime->format('m');
			$params['day']      = $datetime->format('d');
			$params['time']     = $datetime->format('H-i');
		}

		// 返却日時
		if (isset($request->query['endDateTime'])) {
			// DateTimeに変換
			$datetime = new DateTime($request->query('endDateTime'));

			$params['return_datetime'] = $datetime;
			$params['return_year']     = $datetime->format('Y');
			$params['return_month']    = $datetime->format('m');
			$params['return_day']      = $datetime->format('d');
			$params['return_time']     = $datetime->format('H-i');
		}

		// タバコ
		if (isset($request->query['smokingType'])) {
			$params['smoking_flg'] = $request->query('smokingType');
		}

		// 出発店舗へ返却
		if (isset($request->query['returnWay'])) {
			$params['return_way'] = $request->query('returnWay');
		}

		// 出発場所
		if (isset($request->query['place'])) {
			$params['place'] = $request->query('place');
		}

		// 出発場所 - エリアID
		if (isset($request->query['areaId'])) {
			$params['area_id'] = $request->query('areaId');
		}

		// 出発場所 - 空港ID
		if (isset($request->query['airportId'])) {
			$params['airport_id'] = $request->query('airportId');
		}

		// 出発場所 - 駅ID
		if (isset($request->query['stationId'])) {
			$params['station_id'] = $request->query('stationId');
		}

		// 返却場所
		if (isset($request->query['returnPlace'])) {
			$params['return_place'] = $request->query('returnPlace');
		}

		// 返却場所 - エリアID
		if (isset($request->query['returnAreaId'])) {
			$params['return_area_id'] = $request->query('returnAreaId');
		}

		// 返却場所 - 空港ID
		if (isset($request->query['returnAirportId'])) {
			$params['return_airport_id'] = $request->query('returnAirportId');
		}

		// 返却場所 - 駅ID
		if (isset($request->query['returnStationId'])) {
			$params['return_station_id'] = $request->query('returnStationId');
		}

		// 車両タイプ
		if (isset($request->query['carTypes'])) {
			$params['car_type'] = explode(',', $request->query('carTypes'));
		}

		// オプション
		if (isset($request->query['options'])) {
			$params['option'] = explode(',', $request->query('options'));
		}

		// 会社指定
		if (isset($request->query['clients'])) {
			$params['client_id'] = explode(',', $request->query('clients'));
		}

		// 販売種別指定
		if (isset($request->query['salesType'])) {
			$params['sales_type'] = $request->query('salesType');
		}

		return $params;
	}

}

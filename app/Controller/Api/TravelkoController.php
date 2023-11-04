<?php
App::uses('Controller', 'Controller');

class TravelkoController extends Controller {
	// 独自処理APIのためAppControllerを継承しない
	public $uses = array(
		'CommodityTravelko',
		'City',
		'Office',
		'OfficeStation',
		'Landmark',
		'Station',
		'SearchTravelko',
	);
    public $components = array('ApiCommon', 'Travelko');
	public $autoRender = false;
	
	private $doInvokeAction = false;

	public function beforeFilter() {
		parent::beforeFilter();

		// APIのレスポンス設定を取得
		Configure::load('ApiConfig.php');
		$apiConfig = Configure::read('ApiConfig');

		// actionを実行するか判定
		$this->doInvokeAction = (!empty($apiConfig['all']) && !empty($apiConfig[$this->name]));
	}
	
	public function invokeAction(CakeRequest $request) {
		if (!$this->doInvokeAction) {
			return json_encode(array());
		}

		try{
			// actionの実行
			return parent::invokeAction($request);
		} catch (Exception $e) {
			// 例外発生時はログ出力し空のレスポンスを返す
			$this->log($e->getTraceAsString(), 'travelko');
			return json_encode(array());
		}
	}

	public function api_plan_list() {
		if (empty($this->request->query)) {
			return json_encode(array());
		}

		// バリデーションチェック
		$this->SearchTravelko->set($this->request->query);
		if (!$this->SearchTravelko->validates()) {
			$this->log($this->SearchTravelko->validationErrors, 'travelko');
			return json_encode(array());
		}

		// デフォルト値設定
		$limit = !empty($this->request->query['limit']) ? $this->request->query['limit'] : 20;
		$page = !empty($this->request->query['offset']) ? $this->request->query['offset'] + 1: 1;
		$sort = !empty($this->request->query['sort']) ? $this->request->query['sort'] : 1;

		// トラベルコのパラメータからスカイチケットのパラメータに変換する
		$params = $this->Travelko->getSearchParams($this->request->query);

		//年月日を連結
		$params['from'] = $params['year'] . '-' . $params['month'] . '-' . $params['day'];
		$params['to'] = $params['return_year'] . '-' . $params['return_month'] . '-' . $params['return_day'];

		$fromDatetime = $params['from'] . ' ' . str_replace('-',':',$params['time']);
		$toDatetime = $params['to'] . ' ' . str_replace('-',':',$params['return_time']);

		$response = array();
		$query = $this->CommodityTravelko->getCommodityQuery($params, $page, $limit);

		if(!empty($query)) {
			// 商品情報を取得
			$this->paginate = $query;
			$response = $this->paginate();
		}

		return $this->Travelko->createResponseJson($response, !empty($this->request->query['debug']));

	}

	public function api_shop_list() {

		$options = array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Office.id',
				'Office.name',
				'Office.office_hours_from',
				'Office.office_hours_to',
				'Office.sat_hours_from',
				'Office.sat_hours_to',
				'Office.sun_hours_from',
				'Office.sun_hours_to',
				'Office.hol_hours_from',
				'Office.hol_hours_to',
				'Office.tel',
				'Office.access',
				'Office.access_dynamic',
				'Office.latitude',
				'Office.longitude',
//				'Office.image_relative_url',
				'Office.address',
				'Office.latitude',
				'Office.longitude',
				'Office.access',
				'Office.tel',
			),
			'joins' => array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'INNER',
					'conditions' => 'Client.id = Office.client_id',
				),
			),
			'conditions' => array(
				'Client.delete_flg' => false,
				'Office.delete_flg' => false,
			),
			'recursive' => -1,
		);

		$ret = $this->Office->findC('all', $options);

		if (empty($ret)) {
			return json_encode(array());
		}

		$shop_list = array();

		foreach ($ret as $v) {
			$c = $v['Client'];
			$o = $v['Office'];

			$shop = array(
				'brand_id'		 => $c['id'],
				'brand_name'	 => $c['name'],
				'shop_id'		 => $o['id'],
				'shop_name'		 => $c['name'] . ' ' . $o['name'],
				'shop_address'	 => $o['address'],
			);

			if (!empty($o['latitude'])) {
				$shop['shop_geocode_lat'] = $o['latitude'];
			}

			if (!empty($o['longitude'])) {
				$shop['shop_geocode_lng'] = $o['longitude'];
			}

			// 店舗へのアクセスが動的に出せない場合は文言から取得する(旧仕様)
			if (!empty($o['access_dynamic'])) {
				$shop['shop_access'] = $o['access_dynamic'];
			} else {
				$shop['shop_access'] = trim(strip_tags(str_replace(array("\r\n", "\r", '<br>'), "\n", $o['access'])));
			}

			if (!empty($o['office_hours_from'])) {
				$shop['shop_open_time'] = date('Hi', strtotime($o['office_hours_from']));
			}

			if (!empty($o['office_hours_to'])) {
				$shop['shop_close_time'] = date('Hi', strtotime($o['office_hours_to']));
			}

			$shop['shop_time'] = $this->ApiCommon->createShopTimeString($o);
			$shop['shop_phone'] = $o['tel'];

			$shop_list[] = $shop;
		}

		$response = array(
			'response' => array(
				'shop_list' => $shop_list,
			),
		);

		return $this->Travelko->createResponseJson($response, !empty($this->request->query['debug']));

	}

	// テスト確認用のaction
	public function search_test() {
		$all_options = array(
			'airport'	 => array(),
			'station'	 => array(),
			'city'		 => array(),
		);

		foreach ($all_options as $k => $v) {
			$table = ($k != 'city') ? 'travelko_' . $k . 's' : 'travelko_areas';

			$ret = $this->CommodityTravelko->queryC(
				"SELECT prefecture_name, {$k}_id, {$k}_name FROM rentacar.{$table}"
			);
			$all_options[$k] = Hash::combine($ret, "{n}.{$table}.{$k}_id", "{n}.{$table}.{$k}_name", "{n}.{$table}.prefecture_name");
		}

		$type_text = array(
			'airport'	 => '空港',
			'station'	 => '駅',
			'city'		 => '市区町村',
		);

		$optionsText = "<option value=\"\"></option>\n";

		foreach ($all_options as $type => $options) {
			foreach ($options as $pref => $option) {
				// 全て出力されると見辛いのでひとまず4都道府県のみセットしておく
				if (!in_array($pref, array('北海道', '東京都', '福岡県', '長崎県'))) {
					continue;
				}

				$optionsText .= "<optgroup label=\"{$pref} - {$type_text[$type]}\">\n";

				foreach ($option as $k => $v) {
					$optionsText .= "<option value=\"$k\">{$v}</option>\n";
				}

				$optionsText .= "</optgroup>\n";
			}
		}

		$rentalAreaTypeOptions = array(
			'type' => 'radio',
			'legend' => false,
			'options' => array(2 => '空港', 3 => '駅', 1 => '市区町村'),
			'default' => 2,
		);

		$rentalTimeTypeOptions = array(
			'label' => '出発日 ',
			'value' => date('Ymd1100', strtotime('+ 2 days')),
			'maxLength' => 12,
		);

		$returnAreaTypeOptions = $rentalAreaTypeOptions;
		$returnAreaTypeOptions['default'] = false;
		$returnTimeTypeOptions = $rentalTimeTypeOptions;
		$returnTimeTypeOptions['label'] = '返却日 ';
		$returnTimeTypeOptions['value'] = date('Ymd1700', strtotime('+ 2 days'));

		$this->set(compact('optionsText', 'rentalAreaTypeOptions', 'returnAreaTypeOptions', 'rentalTimeTypeOptions', 'returnTimeTypeOptions'));

		$this->autoRender = true;
		$this->autoLayout = false;
	}

}

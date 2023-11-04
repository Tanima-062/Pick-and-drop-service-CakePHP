<?php

class OptionsManageComponent extends Component {

	// 主要空港リスト
	private $mainAirports = array(
		330 => '新千歳空港',
		326 => '那覇空港',
		309 => '福岡空港',
		280 => '羽田空港',
		281 => '成田空港',
		292 => '関西国際空港',
		288 => '中部国際空港(セントレア)',
	);
	
	// エリアタイプリスト
	private $areaTypes = array(
		0 => '指定しない',
		1 => '大手の会社のみ',
	);
	
	// 表示する会社リスト
	private $displayClients = array(
		1, 4, 5, 13, 33, 35, 43, 46, 55, 75
	);

	// オプションリスト
	private $options = array(
		4	 => 'カーナビ搭載',
		5	 => 'ETC搭載',
		17	 => 'Bluetooth',
		15	 => 'バックモニター',
		12	 => 'ETCカード',
		13	 => 'NOC補償',
		14	 => '運転サポート',
		99	 => 'WEB決済可能',
		8	 => '4WD',
		16	 => 'AUXケーブル',
		6	 => 'スタッドレスタイヤ',
		7	 => 'タイヤチェーン',
		18	 => 'ドライブレコーダー',
	);

	// タバコオプションリスト
	private $smokingOptions = array(
		0 => '禁煙',
		1 => '喫煙',
		2 => '指定なし'
	);

	// 引数で渡されたパラメータ
	private $params = array();
	// 検索履歴cookieのパラメータ
	private $cookieParams = array();
	// 取得済みのパラメータ(cookie保存用)
	private $acquiredParams = array();
	// 検索履歴cookie名
	private $historyCookieName = 'rc_f8tcKehPYjwZ2hJ3';
	// 検索履歴cookieの有効期限(1ヶ月)
	private $historyCookieDuration = 2592000;
	// 暗号化形式
	private $encMethod = 'AES-256-CBC';
	// 暗号化キー
	private $encKey = 'npAMHrvMRv7HARr8';
	// 初期化ベクトル(固定は望ましくない)
	private $encIv = 'f8tcKehPYjwZ2hJ3';

	public function getOptions(){
		return $this->options;
	}

	public function getSmokingOptions(){
		return $this->smokingOptions;
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;

		// 検索履歴cookieを取得する
		$cookie = filter_input(INPUT_COOKIE, $this->historyCookieName);
		if (!empty($cookie)) {
			$cookie = openssl_decrypt($cookie, $this->encMethod, $this->encKey, 0, $this->encIv);
			$this->cookieParams = json_decode($cookie, true);
			// 位置情報JSをロードするか判定するためにLayoutにフラグを渡す
			$this->controller->set('existsHistoryCookie', true);
		}
	}

	/**
	 * 検索フォームに初期値やGETクエリの値をセットする
	 */
	public function setSearchOptions($params) {
		$this->params = $params;

		//コンポーネントの独立モデルを定義
		$Prefecture = ClassRegistry::init('Prefecture');
		$Area = ClassRegistry::init('Area');
		$Station = ClassRegistry::init('Station');
		$Landmark = ClassRegistry::init('Landmark');
		$Equipment = ClassRegistry::init('Equipment');
		$CarType = ClassRegistry::init('CarType');
		$Client = ClassRegistry::init('Client');
		$Maintenance = ClassRegistry::init('Maintenance');

		$Prefecture->setDataSource('default_slave');
		$Area->setDataSource('default_slave');
		$Station->setDataSource('default_slave');
		$Landmark->setDataSource('default_slave');
		$Equipment->setDataSource('default_slave');
		$CarType->setDataSource('default_slave');
		$Client->setDataSource('default_slave');
		$Maintenance->setDataSource('default_slave');

		$client_id = isset($params['client_id']) ? $params['client_id'] : 0;

		if ($this->controller->viewVars['fromRentacarClient']) {
			if (!empty($client_id)) {
				$this->controller->set('client_id', $client_id);
			} else {
				$this->controller->set('fromRentacarClient', false);
			}
		} else {
			// 会社ページから検索したときIDを保持したい
			// 「人気のレンタカー会社」であれば検索フォームにチェックがつくので、それ以外の場合
			if (!is_array($client_id) && !empty($client_id) && !in_array($client_id, $this->displayClients)) {
				$this->controller->set('client_id_from_company_page', $client_id);
			}
		}

		if ($this->controller->viewVars['fromRentacarClient']) {
			//レンタカー会社の営業所があるエリア取得（在庫は見ない）
			$areas = $Area->getAreaAllByClientId($client_id);
		} else {
			//全エリア取得
			$areas = $Area->getAreaAll(true);
		}
		$jsonAreas = json_encode($areas);
		$this->controller->set('area_arr', $jsonAreas);

		if ($this->controller->viewVars['fromRentacarClient']) {
			// レンタカー会社の営業所の最寄り駅取得（在庫は見ない）
			$stations = $Station->getStationAllByClientId($client_id, false);
		} else {
			//全駅取得
			$stations = $Station->getStationAll(false, true);
		}
		$jsonStations = json_encode($stations);
		$this->controller->set('station_arr', $jsonStations);

		/**
		 * 貸出方法オプション
		 */
		$borrowPlaceOptions = array(
			'type' => 'radio',
			'label' => true,
			'options' => array(
				'3' => '空港',
				'1' => '都道府県',
				'4' => '駅'
			)
		);

		$borrowPlace = $this->getParam('place', 3);
		$returnPlace = !empty($params['return_place']) ? $params['return_place'] : 3;

		$this->controller->set('borrowPlace', $borrowPlace);
		$this->controller->set('returnPlace', $returnPlace);
		$this->controller->set('borrowPlaceOptions', $borrowPlaceOptions);

		/**
		 * 都道府県オプション
		 */
		// 都道府県リスト取得
		if ($this->controller->viewVars['fromRentacarClient']) {
			$prefectureList = $Prefecture->getPrefectureList(false);
			$prefectureList = array_intersect_key($prefectureList, $areas);
			$defaultPrefectureId = empty($prefectureList) ? 1 : array_keys($prefectureList)[0];
		} else {
			$prefectureList = $Prefecture->getPrefectureList(true);
			$defaultPrefectureId = 1;
		}
		$prefectureId = $this->getParam('prefecture', $defaultPrefectureId);
		$returnPrefectureId = !empty($params['return_prefecture']) ? $params['return_prefecture'] : 0;
		$prefectureList = !empty($prefectureList) ? $prefectureList : array();
		$prefectureList = array('都道府県') + $prefectureList;
		$prefectureOptions = array(
			'type' => 'select',
			'options' => $prefectureList,
			'default' => $prefectureId
		);
		$this->controller->set('prefectureId', $prefectureId);
		$this->controller->set('returnPrefectureId', $returnPrefectureId);
		$this->controller->set('prefectureOptions', $prefectureOptions);
		$returnPrefectureOptions = array(
			'type' => 'select',
			'options' => $prefectureList,
			'default' => $returnPrefectureId
		);
		$this->controller->set('returnPrefectureOptions', $returnPrefectureOptions);

		// from_rentacar_client時の出発/返却のエリア/駅リストは、特に会社で絞らなくても表示上問題なかった

		/**
		 * 出発エリアオプション
		 */
		$areaId = $this->getParam('area_id', 0);
		if (!filter_var($areaId, FILTER_VALIDATE_INT)) {
			$areaId = 0;
		}
		$this->controller->set('areaId', $areaId);

		// エリアリスト取得
		$areaList = $Area->getAreaListByPrefectureId($prefectureId, 0);

		$areaOptions = array(
			'type' => 'select',
			'options' => $areaList,
			'default' => $areaId
		);

		$this->controller->set('areaOptions', $areaOptions);

		/**
		 * 出発ローカル駅オプション
		 */
		$stationId = $this->getParam('station_id', 70);
		$this->controller->set('stationId', $stationId);
		// ローカル駅リスト取得
		$stationList = $Station->getStationListByPrefectureId($prefectureId);

		$stationOptions = array(
			'type' => 'select',
			'options' => $stationList,
			'default' => $stationId
		);

		$this->controller->set('stationOptions', $stationOptions);

		/*
		 * 返却方法オプション
		 */
		$returnWayOptions = array(
			'type' => 'radio',
			'default' => !empty($params['return_way']) ? $params['return_way'] : 0,
			'label' => true,
			'options' => array(
				0 => '出発店舗に返却',
				1 => '乗り捨て利用'
			)
		);

		$this->controller->set('returnWayOptions', $returnWayOptions);

		$returnWay = isset($params['return_way']) ? $params['return_way'] : 0;
		$this->controller->set('returnWay', $returnWay);

		/**
		 * 返却エリアオプション
		 */
		$returnAreaId = !empty($params['return_area_id']) ? $params['return_area_id'] : 1;
		$this->controller->set('returnAreaId', $returnAreaId);

		// 返却エリアリスト取得
		// 都道府県idが同一でない場合は返却エリアリスト取得
		$returnAreaList = ($prefectureId == $returnPrefectureId) ? $areaList : $Area->getAreaListByPrefectureId($returnPrefectureId, 0);
		$returnAreaOptions = array(
			'type' => 'select',
			'disabled' => 'disabled',
			'options' => $returnAreaList,
			'default' => $returnAreaId
		);

		$this->controller->set('returnAreaOptions', $returnAreaOptions);

		/**
		 * 返却ローカル駅オプション
		 */
		$returnStationId = !empty($params['return_station_id']) ? $params['return_station_id'] : 1;
		$this->controller->set('returnStationId', $returnStationId);
		// ローカル駅リスト取得
		$returnStationList = ($prefectureId == $returnPrefectureId) ? $stationList : $Station->getStationListByPrefectureId($returnPrefectureId);

		$returnStationOptions = array(
			'type' => 'select',
			'disabled' => 'disabled',
			'options' => $returnStationList,
			'default' => $returnStationId
		);

		$this->controller->set('returnStationOptions', $returnStationOptions);

		/**
		 * 空港と新幹線駅 オプション
		 */
		// 空港と新幹線駅のリストを取得
		if ($this->controller->viewVars['fromRentacarClient']) {
			// 在庫見てない（既存処理の流れに乗せるため ArrayInStock = Array）
			$landmarkList = $Landmark->getAllLandmarksByClientId($client_id);
			$defaultAirportId = !empty($landmarkList['ArrayInStock']) ? array_keys(current($landmarkList['ArrayInStock']))[0] : 0;
		} else {
			$landmarkList = $Landmark->getAllLandmarks();
			$defaultAirportId = 0;
		}

		// 空港
		$airportId = $this->getParam('airport_id', $defaultAirportId);
		$returnAirportId = !empty($params['return_airport_id']) ? $params['return_airport_id'] : '';

		// 主要空港のoption項目を生成する
		$createMainAirportOptions = function($id) {
			if (empty($id)) {
				return $this->mainAirports;
			}

			$params = array();
			foreach ($this->mainAirports as $value => $name) {
				$option = array('name' => $name, 'value' => $value);
				if ($value == $id) {
					$option['selected'] = true;
				}
				$params[] = $option;
			}

			return $params;
		};

		// 空港初期項目
		$airportSelectHeader = array(
			'空港・港',
		);
		if (!$this->controller->viewVars['fromRentacarClient']) {
			$airportSelectHeader['主要空港'] = $createMainAirportOptions($airportId);
		}
		// 空港
		$airportInStockOptions = array(
			'type' => 'select',
			'options' => array_merge($airportSelectHeader, $landmarkList['ArrayInStock']),
		);
		// 主要空港以外の空港IDの場合は選択値にする
		if ($this->controller->viewVars['fromRentacarClient'] || !isset($this->mainAirports[$airportId])) {
			$airportInStockOptions['default'] = $airportId;
		}

		// 返却空港
		if (!empty($returnAirportId) && $returnAirportId == $airportId) {
			$returnAirportInStockOptions = $airportInStockOptions;
		} else {
			$airportSelectHeader = array(
				'空港・港',
			);
			if (!$this->controller->viewVars['fromRentacarClient']) {
				$airportSelectHeader['主要空港'] = $createMainAirportOptions($returnAirportId);
			}
			// 空港
			$returnAirportInStockOptions = array(
				'type' => 'select',
				'options' => array_merge($airportSelectHeader, $landmarkList['ArrayInStock']),
			);
			// 主要空港以外の空港IDの場合は選択値にする
			if ($this->controller->viewVars['fromRentacarClient'] || !isset($this->mainAirports[$returnAirportId])) {
				$returnAirportInStockOptions['default'] = $returnAirportId;
			}
		}

		$this->controller->set('airportId', $airportId);
		$this->controller->set('returnAirportId', $returnAirportId);
		$this->controller->set('airportInStockOptions', $airportInStockOptions);
		$this->controller->set('returnAirportInStockOptions', $returnAirportInStockOptions);

		/**
		 * 車両タイプオプション
		 */
		// 車両タイプリスト取得
		$carTypeInfo = $CarType->getCarTypeInfo();

		// カータイプで検索された場合
		$carTypeArray = array();
		if (!empty($params['car_type'])) {
			foreach ($params['car_type'] as $val) {
				$carTypeArray[] = (int) $val;
			}
		}

		$this->controller->set(compact('carTypeArray', 'carTypeInfo'));

		/**
		 * 年月日オプション
		 */
		// 今年から一年後の配列作成
		$yearArray = array();
		foreach (range(date('Y'), date('Y', strtotime('+1 year'))) as $key => $val) {
			$yearArray[$val] = $val;
		}

		$yearOptions = array(
			'empty' => false,
			'class' => 'js-selectYear',
			'size' => 1,
			'options' => $yearArray
		);

		$this->controller->set('yearOptions', $yearOptions);

		$monthArray = array();
		for ($i = 1; $i <= 12; $i ++) {
			$monthArray[$i] = $i;
		}

		$monthOptions = array(
			'default' => date('m'),
			'empty' => false,
			'class' => 'js-selectMonth',
			'size' => 1,
			'options' => $monthArray
		);

		$this->controller->set('monthOptions', $monthOptions);

		$dayArray = array();
		for ($i = 1; $i <= 31; $i ++) {
			$dayArray[$i] = $i;
		}

		$dayOptions = array(
			'empty' => false,
			'options' => $dayArray,
			'size' => 1,
			'class' => 'js-selectDay'
		);

		$this->controller->set('dayOptions', $dayOptions);

		$hours = array();
		foreach (range(0, 23.5, 0.5) as $hour) {
			$hour = ($hour === round($hour)) ? sprintf('%02d:00', ($hour)) : sprintf('%02d:30', ($hour));
			$hour2 = str_replace(':', '-', $hour);
			$hours[$hour2] = $hour;
		}
		$timeOptions = array(
			'empty' => false,
			'options' => $hours
		);

		$this->controller->set('timeOptions', $timeOptions);

		// 日付関連変数
		$_2dayslater = strtotime('+2 day');

		// 日付関連初期値
		$departureYear = date('Y', $_2dayslater);
		$departureMonth = date('m', $_2dayslater);
		$departureDay = date('d', $_2dayslater);

		$returnYear = date('Y', $_2dayslater);
		$returnMonth = date('m', $_2dayslater);
		$returnDay = date('d', $_2dayslater);

		if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
			//スマホ
			$date = $this->getParam('date', date('Y/m/d', $_2dayslater));
			if ($date) {
				$dateArr = explode('/', $date);
				if (count($dateArr) == 3) {
					$departureYear = $dateArr[0];
					$departureMonth = $dateArr[1];
					$departureDay = $dateArr[2];
				}
			}

			$returnDate = $this->getParam('return_date', date('Y/m/d', $_2dayslater));
			if ($returnDate) {
				$returnDateArr = explode('/', $returnDate);
				if (count($returnDateArr) == 3) {
					$returnYear = $returnDateArr[0];
					$returnMonth = $returnDateArr[1];
					$returnDay = $returnDateArr[2];
				}
			}

		} else {
			// PC
			$departureYear = $this->getParam('year', date('Y', $_2dayslater));
			$departureMonth = $this->getParam('month', date('m', $_2dayslater));
			$departureDay = $this->getParam('day', date('d', $_2dayslater));

			$returnYear = $this->getParam('return_year', date('Y', $_2dayslater));
			$returnMonth = $this->getParam('return_month', date('m', $_2dayslater));
			$returnDay = $this->getParam('return_day', date('d', $_2dayslater));
		}

		$departureTime = $this->getParam('time', '11-00');
		$returnTime = $this->getParam('return_time', '17-00');

		$fromDate = $departureYear . '/' . $departureMonth . '/' . $departureDay;
		$toDate = $returnYear . '/' . $returnMonth . '/' . $returnDay;

		// 出発の日付チェック
		if (strtotime($fromDate . ' ' . str_replace('-', ':', $departureTime)) < time()) {
			// 過去の時刻の場合はクリア
			$departureYear = date('Y', $_2dayslater);
			$departureMonth = date('m', $_2dayslater);
			$departureDay = date('d', $_2dayslater);
			$fromDate = date('Y/m/d', $_2dayslater);
			$departureTime = '11-00';
			unset($this->acquiredParams['year']);
			unset($this->acquiredParams['month']);
			unset($this->acquiredParams['day']);
			unset($this->acquiredParams['date']);
			unset($this->acquiredParams['time']);
		}

		// 返却の日付チェック
		if (strtotime($toDate . ' ' . str_replace('-', ':', $returnTime)) < strtotime($fromDate . ' ' . str_replace('-', ':', $departureTime))) {
			// 出発より前の時刻の場合は出発より後にする
			$returnYear = $departureYear;
			$returnMonth = $departureMonth;
			$returnDay = $departureDay;
			$toDate = $fromDate;
			$returnTime = '17-00';
			unset($this->acquiredParams['return_year']);
			unset($this->acquiredParams['return_month']);
			unset($this->acquiredParams['return_day']);
			unset($this->acquiredParams['return_date']);
			unset($this->acquiredParams['return_time']);
		}

		$this->controller->set('departureYear', $departureYear);
		$this->controller->set('returnYear', $returnYear);
		$this->controller->set('departureMonth', $departureMonth);
		$this->controller->set('returnMonth', $returnMonth);
		$this->controller->set('departureDay', $departureDay);
		$this->controller->set('returnDay', $returnDay);
		$this->controller->set('departureTime', $departureTime);
		$this->controller->set('returnTime', $returnTime);
		$this->controller->set('fromDate', $fromDate);
		$this->controller->set('toDate', $toDate);

		/**
		 * AT/MTオプション
		 */
		$atMtOptions = array(
			'type' => 'radio',
			'label' => true,
			'default' => isset($params['transmission_flg']) ? $params['transmission_flg'] : 0,
			'options' => array(
				0 => 'AT',
				1 => 'MT',
				2 => 'どちらでもよい'
			)
		);

		$this->controller->set('atMtOptions', $atMtOptions);

		/**
		 * AT/MTオプション スマホ用
		 */
		$spAtMtOptions = array(
			'type' => 'select',
			'label' => false,
			'id' => 'select_trans_atmt',
			'default' => isset($params['transmission_flg']) ? $params['transmission_flg'] : 0,
			'options' => array(
				0 => 'AT',
				1 => 'MT',
				2 => '指定なし'
			)
		);

		$this->controller->set('spAtMtOptions', $spAtMtOptions);

		/**
		 * 禁煙/喫煙オプション
		 */
		$smokingOptions = array(
			'type' => 'radio',
			'label' => true,
			'default' => isset($params['smoking_flg']) ? $params['smoking_flg'] : 2,
			'options' => $this->smokingOptions
		);

		$this->controller->set('smokingOptions', $smokingOptions);
		
		/**
		 * 禁煙/喫煙オプションスマホ用
		 * ラジオ形式
		 */
		$spSmokingOptions = array(
			'label' => false,
			'type' => 'radio',
			'id' => 'select_smoking',
			'default' => isset($params['smoking_flg']) ? $params['smoking_flg'] : 2,
			'options' => array(
				0 => '禁煙',
				1 => '喫煙可',
				2 => 'こだわらない'
			),
		);

		$this->controller->set('spSmokingOptions', $spSmokingOptions);

		/**
		 * オプション
		 */
		$optionValueArray = !empty($params['option']) ? Hash::extract($params['option'], '{n}') : array();

		$setOptions = $this->options;
		// イーコンメンテモード時
		if ($Maintenance->isEconMaintenance()) {
			unset($setOptions[99]);
		}

		$optionOptions = array(
			'type' => 'select',
			'multiple' => 'checkbox',
			'options' => $setOptions,
			'value' => $optionValueArray
		);

		$this->controller->set('optionValueArray', $optionValueArray);
		$this->controller->set('options', $setOptions);
		$this->controller->set('optionOptions', $optionOptions);

		/**
		 * 会社指定オプション
		 */
		$area_type = isset($params['area_type']) ? $params['area_type'] : 0;
		$clientList = call_user_func(function($values) {
			// 大量の項目になってしまうので会社を絞る
			$ret = array();
			foreach ($values as $c) {
				if (in_array($c['Client']['id'], $this->displayClients)) {
					$ret[] = $c;
				}
			}

			return $ret;
			
		}, $Client->getClientListWithAreaType());

		if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
			// SP

			// 複数指定出来ないので先頭のIDにする
			if (is_array($client_id)) {
				$client_id = $client_id[0];
			}

			$clientIdOptions = array(
				'type' => 'select',
				'label' => false,
				'id' => 'select_client_id',
				'default' => $client_id,
				'options' => $this->areaTypes,
			);

			// エリアタイプとクライアントで1つのセレクトにする
			$clientIdOptions['options'] += Hash::combine($clientList, '{n}.Client.id', '{n}.Client.name');
			
			$this->controller->set('clientIdOptions', $clientIdOptions);

		} else {
			// PC
			$areaTypeOptions = array(
				'type' => 'radio',
				'label' => true,
				'default' => $area_type,
				'options' => $this->areaTypes,
			);

			// 複数のチェックボックスがあるため各項目を動的に組み立てる
			$clientIds = array();
			foreach ($clientList as $c) {
				$c = $c['Client'];

				$client = array(
					'name' => $c['name'],
					'value' => $c['id'],
					'data-area_type' => $c['area_type'],
				);

				if (is_array($client_id) && in_array($c['id'], $client_id)) {
					$client['checked'] = true;
				} else if ($c['id'] == $client_id) {
					$client['checked'] = true;
				}

				$clientIds[] = $client;
			}

			$clientIdOptions = array(
				'type' => 'select',
				'options' => $clientIds,
				'class' => 'checkbox',
				'multiple' => 'checkbox',
			);

			$this->controller->set(compact(
				'areaTypeOptions', 'clientIdOptions'
			));
		}

		/**
		 * ご利用人数オプション初期値
		 */
		$adultCount = !empty($params['adults_count']) ? $params['adults_count'] : 2;
		$childrenCount = !empty($params['children_count']) ? $params['children_count'] : '';
		$infantsCount = !empty($params['infants_count']) ? $params['infants_count'] : '';

		$num = array();
		foreach (range(1, 9) as $val) {
			$num[$val] = sprintf('0%d', $val);
		}

		// 大人オプション
		$adultCountOptions = array(
			'default' => $adultCount,
			'type' => 'select',
			'options' => $num
		);

		// 子供・幼児オプション初期値
		$childrenCountOptions = array(
			'empty' => '-',
			'type' => 'select',
			'options' => $num
		);

		$this->controller->set('adultCountOptions', $adultCountOptions);
		$this->controller->set('childrenCount', $childrenCount);
		$this->controller->set('infantsCount', $infantsCount);
		$this->controller->set('childrenCountOptions', $childrenCountOptions);


		$departureDatetime = $departureYear . '-' . $departureMonth . '-' . $departureDay . ' ' . str_replace('-', ':', $departureTime);
		$this->controller->set('departureDatetime', $departureDatetime);

		/**
		 * 検索ページで表示する検索条件をセット (貸出日付、返却日付、貸出場所)
		 */
		if (!empty($this->acquiredParams)) {

			// レンタル期間
			$departureDate = date('n月j日', strtotime($fromDate));
			$returnDate = date('n月j日', strtotime($toDate));

			$this->controller->set('departureDate', $departureDate);
			$this->controller->set('returnDate', $returnDate);

			$searchPlace = '';
			$prefectureName = '';
			// 都道府県の場合
			if ($borrowPlace == 1) {
				$searchPlace = '';
				if (!empty($prefectureList[$prefectureId]) && !empty($areaList[$areaId])) {
					$searchPlace = $prefectureList[$prefectureId] . '・' . $areaList[$areaId];
				} else if (!empty($prefectureList[$prefectureId])) {
					$searchPlace = $prefectureList[$prefectureId];
				}
				$prefectureName = $searchPlace;

				// 空港の場合
			} else if ($borrowPlace == 3) {
				$airportList = Hash::extract($landmarkList['Array'], "{s}.{$airportId}");
				$searchPlace = !empty($airportList) ? $airportList[0] : '';
				
				foreach($landmarkList['Array'] as $k => $list){
					foreach($list as $a){
						if($a == $searchPlace){
							$prefectureName = $k;
							break;
						}
					}
				}

				// ローカル駅の場合
			} else if ($borrowPlace == 4) {
				if (array_key_exists($stationId, $stationList)) {
					$searchPlace = $stationList[$stationId] . '駅';
				} else {
					$searchPlace = '';
				}
				if(!empty($prefectureList[$prefectureId])){
					$prefectureName = $prefectureList[$prefectureId];
				}
			}

			$this->controller->set('prefectureName',$prefectureName);

			//クライアントが条件に含まれていた場合
			if (!empty($client_id)) {
				if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
					// SP
					if (isset($this->areaTypes[$client_id])) {
						$clientName = $this->areaTypes[$client_id];
					} else {
						$clientName = $Client->getClientById($client_id);
						$clientName = (!empty($clientName['Client']['name'])) ? $clientName['Client']['name'] : '';
					}

				} else {
					// PC
					if (is_array($client_id) && count($client_id) > 1) {
						$clientName = '複数の会社指定';
					} else {
						$clientName = $Client->getClientById($client_id);
						$clientName = (!empty($clientName['Client']['name'])) ? $clientName['Client']['name'] : '';
					}
				}
				
				if (!empty($clientName)) {
					if ($searchPlace != '') {
						$searchPlace .= '・';
					}
					$searchPlace .= $clientName;
				}
			}

			$this->controller->set('searchPlace', $searchPlace);
		}

	}

	/**
	 * 新幹線駅のパラメータを駅のパラメータに変換
	 */
	public function convertBulletTrainParamsToStationParams($params) {
		if (empty($params['bullet_train_id']) && empty($params['return_bullet_train_id'])) {
			return $params;
		}

		$Landmark = ClassRegistry::init('Landmark');
		$stationList = $Landmark->getConversionStationList();

		$prefixes = array('', 'return_');

		// 出発と返却パラメータの変換
		foreach ($prefixes as $prefix) {
			if (empty($params[$prefix . 'bullet_train_id'])) {
				continue;
			}

			$id = $params[$prefix . 'bullet_train_id'];

			if (!empty($stationList[$id])) {
				$station = $stationList[$id];
				$params[$prefix . 'place'] = 4; // 駅
				$params[$prefix . 'prefecture'] = key($station);
				$params[$prefix . 'station_id'] = $station[key($station)];

				unset($params[$prefix . 'bullet_train_id']); // 新幹線駅IDを破棄
			}
		}

		return $params;
	}

	/**
	 * 検索履歴cookieを保存する(プログラム内でgetParamした項目のみ)
	 * @return void
	 */
	public function saveHistoryCookie() {
		if (!empty($this->acquiredParams)) {
			$value = json_encode($this->acquiredParams);
			$value = openssl_encrypt($value, $this->encMethod, $this->encKey, 0, $this->encIv);
			$expire = time() + $this->historyCookieDuration;
			setcookie($this->historyCookieName, $value, $expire, '/', '', true, true);
		}
	}

	/**
	 * 指定したキーの値を取得する
	 * 優先度 1:パラメータから 2:cookieパラメータから 3:デフォルト値
	 * @param string $key キー
	 * @param mixed $default デフォルト値
	 * @return mixed
	 */
	private function getParam($key, $default = 0) {
		$ret = $default;
		if (!empty($this->params[$key])) {
			$ret = $this->params[$key];
		} else if (!empty($this->cookieParams[$key]) && !$this->controller->viewVars['fromRentacarClient']) {
			$ret = $this->cookieParams[$key];
		}
		$this->acquiredParams[$key] = $ret;
		return $ret;
	}

}

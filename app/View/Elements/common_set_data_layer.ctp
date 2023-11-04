<!-- ログイン判定とアプリ閲覧判定 -->
<script>
let isLogin = false;
let isApp = false;
<?php
	$is_login = json_decode(_isLogin(), true);
	$is_app = USER_TERMINAL_SOFT == TERMINAL_SOFT_APP;

	if (!empty($is_login)) {
?>
isLogin = true
<?php
	}
	if (!empty($is_app)) {
?>
isApp = true
<?php
	}
?>
</script>

<script>
<?php
	// GTMの申込情報取得
	if ($this->request->controller === 'reservations' && ($this->request->action === 'completion' || $this->request->action === 'sp_completion')) {
?>
dataLayer.push({
	'conversion_id': '<?= $reservation['Reservation']['reservation_key']; ?>',
	'conversion_value': '<?= $reservation['Reservation']['amount']; ?>',
	'conversion_currency': 'JPY',
	'conversion_email': '<?= $reservation['Reservation']['email']; ?>'
});
<?php
	}
?>
</script>
<?php
	/* GA4用eコマースデータ作成 */
	// サービス名
	$affiliation = 'rentacar';
	$url = $_SERVER['REQUEST_URI'];
    $url_path = parse_url($url, PHP_URL_PATH);

	// TOP
	if(preg_match("/\/rentacar\/$/", $url_path)) {
		$ecommerce_data = array(
			'event' => 'TOP',
			'affiliation' => $affiliation,
			'index' => 'TOP',
		);
		$ecommerce_data_json = json_encode($ecommerce_data, JSON_UNESCAPED_UNICODE);
	}
	// 検索結果一覧ページ
	if ($this->request->params['is_move_search'] === true && !empty($this->request->query)) {

		parse_str($_SERVER['QUERY_STRING'], $param);

		// 検索結果数
		$search_result = $this->Paginator->counter('{:count}');

		// エリア検索時「北海道・小樽・キロロ・積丹」から北海道だけを取得するため
		$arrival_place = preg_match('/^[^・]*/u', $prefectureName, $matches) ? $matches[0] : '';

		// 空港・港/駅/エリア
		$search_type = '';
		// 出発空港・港名/駅名/エリア名
		$arrival_airport = '';
		if (isset($param['place'])) {
			switch ($param['place']) {
				case '1':
					$search_type = 'area';
					$arrival_airport = isset($param['area_id']) ? $areaOptions['options'][intval($param['area_id'])] : '';
					break;
				case '3':
					$search_type = 'airport';
					$arrival_airport = isset($param['airport_id']) ? array_column($airportInStockOptions['options'], intval($param['airport_id']))[0] : '';
					break;
				case '4':
					$search_type = 'station';
					$arrival_airport = isset($param['station_id']) ? $stationOptions['options'][intval($param['station_id'])] : '';
					break;
				default:
					break;
			}
		}

		// 出発日時
		$departure_date = $param['date'] ?: ($param['year'] && $param['month'] && $param['day'] ? sprintf('%04d-%02d-%02d', $param['year'], $param['month'], $param['day']) : '');
		$departure_time = str_replace('-', ':', $param['time']);

		// 返却日時
		$return_date = $param['return_date'] ?: ($param['return_year'] && $param['return_month'] && $param['return_day'] ? sprintf('%04d-%02d-%02d', $param['return_year'], $param['return_month'], $param['return_day']) : '');
		$return_time = str_replace('-', ':', $param['return_time']);

		// 乗り捨てあり/なし
		// pc: return_way = 0(乗り捨てなし) / 1(乗り捨てあり), return_place = 1(エリア) / 3(空港・港) / 4(駅)
		// sp: return_way = 0(乗り捨てなし) / 1(乗り捨てあり-エリア) / 3(乗り捨てあり-空港・港) / 4(乗り捨てあり-駅)
		$rentacar = intval($param['return_way']) === 0 ? 'returnway_false' : (intval($param['return_way']) > 0 ? 'returnway_true' : '');

		// オプション
		$optionNameArray = [];
		foreach ($param['option'] as $option) {
			array_push($optionNameArray, $optionOptions['options'][intval($option)]);
		}
		$option = implode(',', $optionNameArray);

		// 車両タイプ
		$carTypeArray = array_column($carTypeInfo, 'CarType');
		$carTypeIdArray = array_column($carTypeArray, 'id');
		$carTypeNameArray = [];
		foreach ($param['car_type'] as $carType) {
			$carTypeIdx = array_search(intval($carType), $carTypeIdArray);
			array_push($carTypeNameArray, $carTypeInfo[$carTypeIdx]['CarType']['name']);
		}
		$type = implode(',', $carTypeNameArray);

		//　会社指定
		$brand = '';
		if(isset($param['client_id'])) {
			// pc
			if(is_array($param['client_id'])) {
				$clientNameArray = [];
				$clientIdArray = array_column($clientIdOptions['options'], 'value');
				foreach ($param['client_id'] as $clientId) {
					$clientIdx = array_search(intval($clientId), $clientIdArray);
					array_push($clientNameArray, $clientIdOptions['options'][$clientIdx]['name']);
				}
				$brand = implode(',', $clientNameArray);
			// sp
			} else {
				$brand = $clientIdOptions['options'][intval($param['client_id'])];
			}
		}
		$search_condition = [
			'event' => 'search_TOP',
			'affiliation' => $affiliation,
			'index' => 'search_TOP',
			'search_result' => $search_result,
			'arrival_place' => $arrival_place,
			'search_type' => $search_type,
			'arrival_airport' => $arrival_airport,
			'departure_date' => $departure_date,
			'return_date' => $return_date,
			'departure_time' => $departure_time,
			'return_time' => $return_time,
			'rentacar' => $rentacar,
			'option' => $option,
			'type' => $type,
			'brand' => $brand,
		];
		$search_condition_json = json_encode($search_condition, JSON_UNESCAPED_UNICODE);

		$items = [];
		foreach ($commodities as $commodity) {

			$commodityItemId = $commodity['CommodityItem']['id'];
			$commodityId = $commodity['Commodity']['id'];
			$clientId = $commodity['Commodity']['client_id'];

			// 車種名
			$carName = $carInfoList[$commodityItemId]['CarType']['name'];
			// 出発店舗名
			$rentOfficeNameArray = [];
			foreach ($rentOfficeList[$commodityId] as $rentOffice) {
				array_push($rentOfficeNameArray, $rentOffice['name']);
			}
			$rentOfficeName = implode(',', $rentOfficeNameArray);
			// オプション
			// 喫煙・禁煙
			$smokingCarString = $smokingCarList[$commodity['Commodity']['smoking_flg']];
			// 定員
			$recommendedCapacity = 0;
			//定員人数（推奨人数から変更）
			if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
				$recommendedCapacity = Hash::get($carInfoList[$commodityItemId]['CarModel'],'0.capacity');
			}
			$recommendedCapacityString = '定員'.$recommendedCapacity.'名';
			// 車種指定フラグ
			$flgModelSelect = ( !empty( $commodity['CommodityItem']['car_model_id']) );
			$flgModelSelectString = $flgModelSelect ? '車種指定' : '';
			// 新車
			$newCarString = $commodity['Commodity']['new_car_registration'] == 1 || $commodity['Commodity']['new_car_registration'] == 2 ? '新車' : '';
			// 装備
			$equipmentName = implode(',', array_filter(array_map(function ($equipment) use ($commodityEquipment, $commodityId) {
				return !empty($commodityEquipment[$commodityId][$equipment['id']]) ? $equipment['name'] : null;
			}, array_column($equipmentList, 'Equipment'))));
			// AT車
			$atcarString = $commodity['Commodity']['transmission_flg'] == 0 ? 'AT車' : '';
			$optionName = implode(',', array_filter([$smokingCarString, $recommendedCapacityString, $flgModelSelectString, $newCarString, $equipmentName, $atcarString]));

			$price = $commodity['CommodityPrice']['price'];
			$item_id = $commodity['Commodity']['name'];
			$item_name = $carName;
			$item_brand = $clientList[$clientId]['name'];
			$item_category = $rentOfficeName;
			$item_category2 = $arrival_place;
			$item_variant = $optionName;

			$item = [
				'price' => $price,
				'item_id' => $item_id,
				'item_name' => $item_name,
				'item_brand' => $item_brand,
				'item_category' => $item_category,
				'item_category2' => $item_category2,
				'item_variant' => $item_variant,
			];
			array_push($items, $item);
		}
		$ecommerce_data = [
			'event' => 'view_item_list',
			'ecommerce' => [
				'affiliation' => $affiliation,
				'index' => 'search',
				'search_result' => $search_result,
				'items' => $items,
			],
		];
		$ecommerce_data_json = json_encode($ecommerce_data, JSON_UNESCAPED_UNICODE);
	}
	// Planページ
	if(preg_match("/\/rentacar\/plan\//", $url_path)) {

		// 目的都道府県名（出発店舗がある都道府県）
		$address = current($commodityInfo['RentOffice'])['address'];
		preg_match("/(\S+?[都道府県])/u", $address, $matches);

		// オプション
		// 喫煙・禁煙
		$smokingCarString = $smokingCarList[$commodityInfo['Commodity']['smoking_flg']];
		// 定員
		$recommendedCapacity = 0;
		$recommendedCapacity = Hash::extract($commodityInfo['CarModel'], '{n}.capacity');
		$recommendedCapacityString = '定員'.$recommendedCapacity[0].'名';
		// 車種指定フラグ
		$flgModelSelect = ( !empty( $commodityInfo['CommodityItem']['car_model_id']) );
		$flgModelSelectString = $flgModelSelect ? '車種指定' : '';
		$newCarString = $commodityInfo['Commodity']['new_car_registration'] == 1 || $commodityInfo['Commodity']['new_car_registration'] == 2 ? '新車' : '';
		// 装備
		$equipmentName = implode(',', array_filter(array_map(function ($equipment) use ($commodityEquipment) {
			if (!empty($commodityEquipment[$equipment['id']])) {
				return $equipment['name'];
			}
		}, array_column($equipmentList, 'Equipment')), function($value) {
			return !empty($value);
		}));
		// AT車
		$atcarString = $commodityInfo['Commodity']['transmission_flg'] == 0 ? 'AT車' : '';
		$optionName = implode(',', array_filter([$smokingCarString, $recommendedCapacityString, $flgModelSelectString, $newCarString, $equipmentName, $atcarString]));

		// 商品金額
		$price = intval(isset($estimationTotalPrice) ? $estimationTotalPrice : $basicCharge);
		// プラン名
		$item_id = $commodityInfo['Commodity']['name'];
		// 車種名
		$item_name = $commodityInfo['CarType']['name'];
		// レンタカー会社
		$item_brand = $commodityInfo['Client']['name'];
		// 出発店舗名
		$item_category = implode(',', array_column($commodityInfo['RentOffice'], 'name'));
		// 目的都道府県名（出発店舗がある都道府県）
		$item_category2 = $matches[1];
		// オプション
		$item_variant = $optionName;
		// 出発日
		$item_category3 = date('Y-m-d', strtotime($requestData['from']));
		//　返却日
		$item_category4 = date('Y-m-d', strtotime($requestData['to']));

		$items = [];
		$item = [
			'price' => $price,
			'item_id' => $item_id,
			'item_name' => $item_name,
			'item_brand' => $item_brand,
			'item_category' => $item_category,
			'item_category2' => $item_category2,
			'item_category3' => $item_category3,
			'item_category4' => $item_category4,
			'item_variant' => $item_variant,
		];
		array_push($items, $item);
		$ecommerce_data = [
			'event' => 'view_item',
			'ecommerce' => [
				'affiliation' => $affiliation,
				'index' => 'detail',
				'items' => $items,
			],
		];
		$ecommerce_data_json = json_encode($ecommerce_data, JSON_UNESCAPED_UNICODE);
	}
	// 入力/確認
	if(preg_match("/\/rentacar\/reservations\/step(1|2)\/$/", $url_path)) {

		if(preg_match("/\/rentacar\/reservations\/step1\/$/", $url_path)) {
			$isStep1 = true;
			$event = 'select_item';
			$index = 'input';
		} else {
			$event = 'confirm';
			$index = 'confirm';
		}

		// 目的都道府県名（出発店舗がある都道府県）
		$address = current($commodityInfo['RentOffice'])['address'];
		preg_match("/(\S+?[都道府県])/u", $address, $matches);

		// オプション
		// 喫煙・禁煙
		$smokingCarString = $smokingCarList[$commodityInfo['Commodity']['smoking_flg']];
		// 定員
		$recommendedCapacity = 0;
		$recommendedCapacity = Hash::extract($commodityInfo['CarModel'], '{n}.capacity');
		$recommendedCapacityString = '定員'.$recommendedCapacity[0].'名';
		// 車種指定フラグ
		$flgModelSelect = ( !empty( $commodityInfo['CommodityItem']['car_model_id']) );
		$flgModelSelectString = $flgModelSelect ? '車種指定' : '';
		$newCarString = $commodityInfo['Commodity']['new_car_registration'] == 1 || $commodityInfo['Commodity']['new_car_registration'] == 2 ? '新車' : '';
		// 装備
		$equipmentName = implode(',', array_filter(array_map(function ($equipment) use ($commodityEquipment) {
			if (!empty($commodityEquipment[$equipment['id']])) {
				return $equipment['name'];
			}
		}, array_column($equipmentList, 'Equipment')), function($value) {
			return !empty($value);
		}));
		// AT車
		$atcarString = $commodityInfo['Commodity']['transmission_flg'] == 0 ? 'AT車' : '';
		$optionName = implode(',', array_filter([$smokingCarString, $recommendedCapacityString, $flgModelSelectString, $newCarString, $equipmentName, $atcarString]));

		$fromDate = strtotime(str_replace(['年', '月'], ['-', '-'], strstr($confirmation['from'], '日', true)));
		$fromDate = date('Y-m-d', $fromDate);
		$toDate = strtotime(str_replace(['年', '月'], ['-', '-'], strstr($confirmation['to'], '日', true)));
		$toDate = date('Y-m-d', $toDate);

		// 商品金額
		$price = intval(isset($estimationTotalPrice) ? $estimationTotalPrice : $basicCharge);
		// プラン名
		$item_id = $commodityInfo['Commodity']['name'];
		// 車種名
		$item_name = $commodityInfo['CarType']['name'];
		// レンタカー会社
		$item_brand = $commodityInfo['Client']['name'];
		// 出発店舗名
		$item_category = $confirmation['rentOfficeName'];
		// 目的都道府県名（出発店舗がある都道府県）
		$item_category2 = $matches[1];
		// オプション
		$item_variant = $optionName;
		// 出発日
		$item_category3 = $fromDate;
		//　返却日
		$item_category4 = $toDate;
		// 大人人数,子供人数,幼児人数
		$quantity = $confirmation['adults'].','.$confirmation['children'].','.$confirmation['infants'];

		$item = [
			'price' => $price,
			'item_id' => $item_id,
			'item_name' => $item_name,
			'item_brand' => $item_brand,
			'item_category' => $item_category,
			'item_category2' => $item_category2,
			'item_category3' => $item_category3,
			'item_category4' => $item_category4,
			'item_variant' => $item_variant,
			'quantity' => $quantity,
		];

		if(preg_match("/\/rentacar\/reservations\/step1\/$/", $url_path)) {
			$items = [];
			array_push($items, $item);

			$ecommerce_data = [
				'event' => $event,
				'ecommerce' => [
					'affiliation' => $affiliation,
					'index' => $index,
					'items' => $items,
				],
			];
		} else {
			$ecommerce_data = array(
				'event' => $event,
				'affiliation' => $affiliation,
				'index' => $index,
			);

			$ecommerce_data = array_merge($ecommerce_data, $item);
		}
		$ecommerce_data_json = json_encode($ecommerce_data, JSON_UNESCAPED_UNICODE);
	}
	// 申込完了/決済完了
	if(preg_match("/\/rentacar\/reservations\/completion\/$/", $url_path)) {

		$item_list_id = $reservation['Reservation']['payment_status'] === 'PAYED' ? 'advance' : 'later';

		if ($item_list_id === 'later') {
			$event = 'application';
			$index = 'application';
		} else {
			$event = 'purchase';
			$index = 'purchase';
		}

		// 予約弁号
		$transaction_id = $reservation['Reservation']['reservation_key'];
		// 通貨
		$currency = 'JPY';
		// 合計金額
		$value = intval($reservation['Reservation']['amount']);

		// 目的都道府県名（出発店舗がある都道府県）
		$address = $reservation['RentOffice']['address'];
		preg_match("/(\S+?[都道府県])/u", $address, $matches);

		$fromDate = date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime']));
		$toDate = date('Y-m-d', strtotime($reservation['Reservation']['return_datetime']));
		// 乗り捨てあり/なし
		$return_way = $reservation['RentOffice']['city_id'] !== $reservation['ReturnOffice']['city_id'] ? 'returnway_true' : 'returnway_false';

		// 商品金額
		$price = intval($reservation['Reservation']['amount']);
		// プラン名
		$item_id = $reservation['Commodity']['name'];
		// 車種名
		$item_name = $reservation['CarType']['name'];
		// レンタカー会社名
		$item_brand = $reservation['Client']['name'];
		// 出発場所
		$item_category = $reservation['RentOffice']['name'];
		// 目的都道府県名（出発店舗がある都道府県）
		$item_category2 = $matches[1];
		// 出発日
		$item_category3 = $fromDate;
		//　返却日
		$item_category4 = $toDate;
		// 乗り捨てあり/なし
		$item_variant = $return_way;
		// 大人人数,子供人数,幼児人数
		$quantity = $reservation['Reservation']['adults_count'].','.$reservation['Reservation']['children_count'].','.$reservation['Reservation']['infants_count'];

		$item = [
			'price' => $price,
			'item_id' => $item_id,
			'item_name' => $item_name,
			'item_brand' => $item_brand,
			'item_category' => $item_category,
			'item_category2' => $item_category2,
			'item_category3' => $item_category3,
			'item_category4' => $item_category4,
			'item_variant' => $item_variant,
			'quantity' => $quantity,
		];

		if ($item_list_id === 'later') {
			$ecommerce_data = array(
				'event' => $event,
				'transaction_id' => $transaction_id,
				'currency' => "JPY",
				'value' => $value,
				'affiliation' => $affiliation,
				'index' => $index,
				'item_list_id' => $item_list_id,
			);

			$ecommerce_data = array_merge($ecommerce_data, $item);
		} else {
			$items = [];
			array_push($items, $item);

			$ecommerce_data = [
				'event' => $event,
				'ecommerce' => [
					'transaction_id' => $transaction_id,
					'currency' => "JPY",
					'value' => $value,
					'affiliation' => $affiliation,
					'index' => $index,
					'item_list_id' => $item_list_id,
					'items' => $items,
				],
			];
		}


		$ecommerce_data_json = json_encode($ecommerce_data, JSON_UNESCAPED_UNICODE);
	}
	// 静的ページ
	// 静的ページ情報定義
	$staticPages = [
		'region' => '地方',
		'prefectures' => '都道府県',
		'airportlist' => '空港',
		'fromairport' => '空港',
		'stationlist' => '駅',
		'station' => '駅',
		'ferryterminallist' => 'フェリーターミナル',
		'ferryterminal' => 'フェリーターミナル',
		'companylist' => 'レンタカー会社',
		'company' => 'レンタカー会社',
		'localstoredetail' => '店舗',
		'wpcampaign' => '特集',
	];

	// 現在リクエストページが静的ページか確認
	$isStatic = isset($staticPages[$this->request->controller]) &&
				($this->request->action === 'index' || $this->request->action === 'sp_index' ||
				$this->request->action === 'reviews' || $this->request->action === 'sp_reviews');

	// 静的ページの場合データ生成
	if ($isStatic) {
		$item_id = $staticPages[$this->request->controller];
		$promotion_name = '';

		if ($this->request->controller === 'wpcampaign') {
			$promotion_name = $url;
		}

		$ecommerce_data = [
			'event' => 'view_promotion',
			'ecommerce' => [
				'affiliation' => $affiliation,
				'index' => '静的',
				'item_id' => $item_id,
				'promotion_name' => $promotion_name,
			],
		];

		$ecommerce_data_json = json_encode($ecommerce_data, JSON_UNESCAPED_UNICODE);
	}
?>
<script>
	window.dataLayer = window.dataLayer || [];
<?php
	if ($this->request->params['is_move_search'] === true && !empty($this->request->query)) {
?>
	dataLayer.push({ ecommerce: null });
	dataLayer.push(<?= $search_condition_json; ?>);
<?php
	}
	if(isset($ecommerce_data_json)) {
?>
	dataLayer.push({ ecommerce: null });
	dataLayer.push(<?= $ecommerce_data_json; ?>);
<?php
	}
	if(isset($isStep1)) {
?>
	dataLayer.push({
		'login_status': isLogin ? 'member' : 'guest',
	});
<?php
	}
	if(preg_match("/\/rentacar\/reservations\/completion\/$/", $url_path)) {
?>
		dataLayer.push({
			'profit': <?= $reservation['Reservation']['profit']; ?>,
		});
<?php
	}
?>
</script>
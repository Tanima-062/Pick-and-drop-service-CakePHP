<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

	// API
	// 対応するフォーマット
	Router::parseExtensions('json', 'xml');

	// スカイスキャナー
	Router::connect('/api/skyscanner/:revision/plans/:rental_type/:rental_point/:return_type/:return_point/:rental_datetime/:return_datetime',
		array('[method]' => 'GET', 'controller' => 'skyscanner_plans', 'action' => 'index', 'ext' => 'json'),
		array('revision' => 'v1', 'rental_type' => '[0-9]', 'rental_point' => '([0-9]+|[A-Za-z]{3})', 'return_type' => '[0-9]', 'return_point' => '([0-9]+|[A-Za-z]{3})', 'rental_datetime' => '2[0-9]{3}[0-1][0-9][0-3][0-9][0-2][0-9](00|30)', 'return_datetime' => '2[0-9]{3}[0-1][0-9][0-3][0-9][0-2][0-9](00|30)')
	);
	Router::connect('/api/skyscanner/:revision/shops', array('[method]' => 'GET', 'controller' => 'skyscanner_shops', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/skyscanner/:revision/shops/:id', array('[method]' => 'GET', 'controller' => 'skyscanner_shops', 'action' => 'view', 'ext' => 'json'), array('revision' => 'v1', 'id' => '[0-9]+'));
	Router::connect('/api/skyscanner/:revision/airports', array('[method]' => 'GET', 'controller' => 'skyscanner_airports', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/skyscanner/:revision/airports/:id', array('[method]' => 'GET', 'controller' => 'skyscanner_airports', 'action' => 'view', 'ext' => 'json'), array('revision' => 'v1', 'id' => '([0-9]+|[A-Za-z]{3})'));
	Router::connect('/api/skyscanner/:revision/cities', array('[method]' => 'GET', 'controller' => 'skyscanner_cities', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/skyscanner/:revision/cities/:id', array('[method]' => 'GET', 'controller' => 'skyscanner_cities', 'action' => 'view', 'ext' => 'json'), array('revision' => 'v1', 'id' => '[0-9]+'));
	Router::connect('/api/skyscanner/:revision/stations', array('[method]' => 'GET', 'controller' => 'skyscanner_stations', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/skyscanner/:revision/stations/:id', array('[method]' => 'GET', 'controller' => 'skyscanner_stations', 'action' => 'view', 'ext' => 'json'), array('revision' => 'v1', 'id' => '[0-9]+'));

	// バジェット
	Router::connect('/api/budget/:revision/stock_groups', array('[method]' => 'GET', 'controller' => 'budget_stock_groups', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/budget/:revision/car_classes', array('[method]' => 'GET', 'controller' => 'budget_car_classes', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/budget/:revision/car_models', array('[method]' => 'GET', 'controller' => 'budget_car_models', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/budget/:revision/plans', array('[method]' => 'GET', 'controller' => 'budget_plans', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/budget/:revision/stocks', array('[method]' => 'GET', 'controller' => 'budget_stocks', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/budget/:revision/stocks', array('[method]' => 'POST', 'controller' => 'budget_stocks', 'action' => 'edit', 'ext' => 'json'), array('revision' => 'v1'));
	Router::connect('/api/budget/:revision/reservations', array('[method]' => 'POST', 'controller' => 'budget_reservations', 'action' => 'index', 'ext' => 'json'), array('revision' => 'v1'));
	// 開発用
	if (!IS_PRODUCTION) {
		Router::connect('/api/budget/:revision/reservations/test', array('[method]' => 'POST', 'controller' => 'budget_reservations', 'action' => 'test', 'ext' => 'json'), array('revision' => 'v1'));
	}

	// レンナビ
	Router::connect('/api/rennavi/reservecount', array('[method]' => 'POST', 'controller' => 'rennavi_reservations', 'action' => 'count', 'ext' => 'xml'));
	Router::connect('/api/rennavi/reservedownload', array('[method]' => 'POST', 'controller' => 'rennavi_reservations', 'action' => 'download', 'ext' => 'xml'));
	Router::connect('/api/rennavi/reservefix', array('[method]' => 'POST', 'controller' => 'rennavi_reservations', 'action' => 'fix', 'ext' => 'xml'));
	Router::connect('/api/rennavi/tejimai', array('[method]' => 'POST', 'controller' => 'rennavi_reservations', 'action' => 'tejimai', 'ext' => 'xml'));

	// 汎用API
	/**
	 * Router::connect()の引数が長いので簡略化する
	 */
	$routeForApi = function ($url, $method, $controller, $action = 'index', $options = array()) {
		Router::connect('/api/v1' . $url,
			array('[method]' => $method, 'controller' => $controller, 'action' => $action, 'ext' => 'json'),
			array_merge($options, array('lang' => 'ja'))
		);
		Router::connect('/api/v1/:lang' . $url,
			array('[method]' => $method, 'controller' => $controller, 'action' => $action, 'ext' => 'json'),
			array_merge($options, array('lang' => '[A-Za-z]{2}(|-[A-Za-z]{2})'))
		);
	};

	$routeForApi('/searches', 'GET', 'searches_api');
	$routeForApi('/searches', 'POST', 'searches_api', 'search');
	$routeForApi('/searches/basic', 'POST', 'searches_api', 'searchBasic');

	$routeForApi('/searchItems/airports', 'GET', 'search_items_api', 'airports');
	$routeForApi('/searchItems/:id/stations', 'GET', 'search_items_api', 'stations', array('pass' => array('id'), 'id' => '[0-9]+'));
	$routeForApi('/searchItems/:id/areas', 'GET', 'search_items_api', 'areas', array('pass' => array('id'), 'id' => '[0-9]+'));

	$routeForApi('/plans/:id', 'GET', 'plans_api', 'view', array('pass' => array('id'), 'id' => '[0-9]+'));
	$routeForApi('/plans/:id', 'POST', 'plans_api', 'detail', array('pass' => array('id'), 'id' => '[0-9]+'));
	$routeForApi('/plans/:id/basic', 'POST', 'plans_api', 'detailBasic', array('pass' => array('id'), 'id' => '[0-9]+'));
	$routeForApi('/plans/:id/dropoff', 'GET', 'plans_api', 'dropoff', array('pass' => array('id'), 'id' => '[0-9]+'));
	$routeForApi('/plans/:id/options', 'GET', 'plans_api', 'options', array('pass' => array('id'), 'id' => '[0-9]+'));

	$routeForApi('/reservations', 'POST', 'reservations_api', 'add');
	$routeForApi('/reservations/:key', 'POST', 'reservations_retrieve_api', 'view', array('pass' => array('key'), 'key' => '[A-Za-z]{2}[0-9]{11}'));
	$routeForApi('/reservations/:key', 'PUT', 'reservations_retrieve_api', 'edit', array('pass' => array('key'), 'key' => '[A-Za-z]{2}[0-9]{11}'));
	$routeForApi('/reservations/:key/cancel', 'POST', 'reservations_retrieve_api', 'cancel', array('pass' => array('key'), 'key' => '[A-Za-z]{2}[0-9]{11}'));
	$routeForApi('/applications/:id', 'GET', 'applications_retrieve_api', 'view', array('pass' => array('id'), 'id' => '[0-9]+'));

	// ツアー向けAPI
	Router::connect('/api/v1/item/:id/area',
		array('[method]' => 'GET', 'controller' => 'item_api', 'action' => 'area', 'ext' => 'json'),
		array('pass' => array('id'), 'id' => '[0-9]+')
	);
	Router::connect('/api/v1/item/cartype', array('[method]' => 'GET', 'controller' => 'item_api', 'action' => 'carType', 'ext' => 'json'));
	Router::connect('/api/v1/item/client', array('[method]' => 'GET', 'controller' => 'item_api', 'action' => 'client', 'ext' => 'json'));
	Router::connect('/api/v1/item/equipment', array('[method]' => 'GET', 'controller' => 'item_api', 'action' => 'equipment', 'ext' => 'json'));

	Router::connect('/api/v1/plan', array('[method]' => 'GET', 'controller' => 'plan_api', 'ext' => 'json'));
	Router::connect('/api/v1/plan/:id', array('[method]' => 'GET', 'controller' => 'plan_api', 'action' => 'details', 'ext' => 'json'), array('pass' => array('id'), 'id' => '[0-9]+'));
	Router::connect('/api/v1/plan/:id/option', array('[method]' => 'GET', 'controller' => 'plan_api', 'action' => 'options', 'ext' => 'json'), array('pass' => array('id'), 'id' => '[0-9]+'));

	Router::connect('/api/v1/reservation', array('[method]' => 'GET', 'controller' => 'reservation_api', 'ext' => 'json'));
	Router::connect('/api/v1/reservation', array('[method]' => 'POST', 'controller' => 'reservation_api', 'action' => 'register', 'ext' => 'json'));
	Router::connect('/api/v1/reservation/:key/payment', array('[method]' => 'PUT', 'controller' => 'reservation_api', 'action' => 'payment', 'ext' => 'json'), array('pass' => array('key')));
	Router::connect('/api/v1/reservation/:key', array('[method]' => 'DELETE', 'controller' => 'reservation_api', 'action' => 'cancel', 'ext' => 'json'), array('pass' => array('key')));
	Router::connect('/api/v1/retrieval/:id', array('[method]' => 'GET', 'controller' => 'reservations_detail_retrieve_api', 'action' => 'index', 'ext' => 'json'), array('pass' => array('id'), 'id' => '[0-9]+'));

	// yotpoクライアント登録処理
	Router::connect('/api/v1/yotpo/receive', array('controller' => 'yotpo_receive_api', 'action' => 'index', 'ext' => 'json'));

	// 位置情報
	Router::connect('/api/ajax/:revision/current_location/:lat/:lng',
		array('[method]' => 'GET', 'controller' => 'ajax_current_location', 'action' => 'index', 'ext' => 'json'),
		array('revision' => 'v1', 'lat' => '-?[0-9]+(\.[0-9]+)?', 'lng' => '-?[0-9]+(\.[0-9]+)?')
	);

	// ローカル環境CORS対応
	if (!IS_PRODUCTION) {
		Router::connect('/api/*', array('[method]' => 'OPTIONS', 'controller' => 'api_cors', 'action' => 'index', 'ext' => 'json'));
	}

	// プラン情報
	/*Router::connect('/api/ajax/:revision/plan_infos/:id',
		array('[method]' => 'GET', 'controller' => 'ajax_plan_info', 'action' => 'index', 'ext' => 'json'),
		array('revision' => 'v1', 'id' => '[1-9][0-9]*')
	);*/
	Router::connect('/plan/getPlanInfo',
		array('[method]' => 'GET', 'controller' => 'ajax_plan_info', 'action' => 'index', 'ext' => 'json')
	);

	Router::connect('/api/*', array('controller' => 'api_error', 'action' => 'index', 'status_code' => 400));

	// TOPﾍﾟｰｼﾞ
	Router::connect('/', array('controller' => 'tops', 'action' => 'index'));

	Router::connect('/photogallery', array('controller' => 'tops', 'action' => 'photogallery'));

	Router::connect('/getPublicHoliday', array('controller' => 'tops', 'action' => 'getPublicHoliday'));

	// プラン詳細
	Router::connect('/plan/:commodityItemId', array('controller' => 'plan', 'action' => 'index'), array('commodityItemId' => '[0-9]+'));

	// 予約フォーム
	Router::connect('/reservations/step1/*', array('controller' => 'reservations', 'action' => 'step1'));
	Router::connect('/reservations/step2/*', array('controller' => 'reservations', 'action' => 'step2'));
	Router::connect('/reservations/completion/*', array('controller' => 'reservations', 'action' => 'completion'));
	Router::connect('/reservations/callBackReturn/:identification_key', array('controller' => 'reservations', 'action' => 'callBackReturn'), array('identification_key' => '[0-9a-z]+'));
	Router::connect('/reservations/callBackCancelReturn/:identification_key', array('controller' => 'reservations', 'action' => 'callBackCancelReturn'), array('identification_key' => '[0-9a-z]+'));

	// マイページ
	Router::connect('/mypages/', array('controller' => 'mypages', 'action' => 'index'));
	Router::connect('/mypages/logout/', array('controller' => 'mypages', 'action' => 'logout'));
	Router::connect('/mypages/login/', array('controller' => 'mypages', 'action' => 'login'));
	Router::connect('/mypages/login/:hash/', array('controller' => 'mypages', 'action' => 'login'));
	Router::connect('/mypages/edit/:content/', array('controller' => 'mypages', 'action' => 'edit'));
	Router::connect('/mypages/edit/:content/check/', array('controller' => 'mypages', 'action' => 'check'));
	Router::connect('/mypages/change_finish/', array('controller' => 'mypages', 'action' => 'change_finish'));
	Router::connect('/mypages/cancel/', array('controller' => 'mypages', 'action' => 'cancel'));
	Router::connect('/mypages/cancel_check/', array('controller' => 'mypages', 'action' => 'cancel_check'));
	Router::connect('/mypages/cancel_finish/', array('controller' => 'mypages', 'action' => 'cancel_finish'));
	Router::connect('/mypages/callBackReturn/:identification_key', array('controller' => 'mypages', 'action' => 'callBackReturn'), array('identification_key' => '[0-9a-z]+'));
	Router::connect('/mypages/callBackCancelReturn/:identification_key', array('controller' => 'mypages', 'action' => 'callBackCancelReturn'), array('identification_key' => '[0-9a-z]+'));

	Router::connect('/callBackCancelRefund/:reservation_id', array('controller' => 'mypages', 'action' => 'call_back_cancel_refund'), array('pass' => array('reservation_id'), 'reservation_id' => '[0-9]+'));
	Router::connect('/callBackRefund/:reservation_id', array('controller' => 'mypages', 'action' => 'call_back_refund'), array('pass' => array('reservation_id'), 'reservation_id' => '[0-9]+'));

	// 企業ページ
	// ajaxは許可する
	Router::connect('/company/ajaxAction/', array('controller' => 'company', 'action' => 'ajaxAction'));
	// /rentacar/company/リンクコード/でアクセスさせる
	Router::connect('/company/:link_cd', array('controller' => 'company', 'action' => 'index'), array('link_cd' => '[0-9a-z_-]+'));

	Router::connect('/company/:link_cd/review/', array('controller' => 'company', 'action' => 'reviews'), array('link_cd' => '[0-9a-z_-]+'));

	// 店舗ページ
	// /rentacar/company/リンクコード/リンクコード/でアクセスさせる
	Router::connect('/company/:client_link_cd/:office_link_cd', array('controller' => 'localstoredetail', 'action' => 'index'), array('client_link_cd' => '[0-9a-z_-]+', 'office_link_cd' => '[0-9a-z_-]+'));
	Router::connect('/company/:client_link_cd/:office_link_cd/review/', array('controller' => 'localstoredetail', 'action' => 'reviews'), array('client_link_cd' => '[0-9a-z_-]+', 'office_link_cd' => '[0-9a-z_-]+'));

	// /rentacar/localstoredetail?store_id=～は導線が生きているようなので正しいURLに301転送
	Router::connect('/localstoredetail', array('controller' => 'localstoredetail', 'action' => 'moved_url2'));

	// その他アクセスは404とする
	Router::connect('/company/*', array('status' => 404));

	// 空港ページ
	// /rentacar/fromairport/～は重複コンテンツとなってしまうので404とする
	Router::connect('/fromairport/*', array('status' => 404));

	// 都道府県ページ
	// /rentacar/prefectures/xx/～は導線が生きているようなので正しいURLに301転送
	Router::connect('/prefectures/:prefecture_id/*', array('controller' => 'prefectures', 'action' => 'moved_url'), array('prefecture_id' => '[0-9]+'));

	// 予約メール再送
	// TASK-2209の理由でコメントアウト
	Router::connect('/statics/*', array('status' => 404));

	// 静的ページ
	Router::connect('/infos/reserve', array('status' => 404));
	Router::connect('/infos/qanda', array('status' => 404));
	Router::connect('/infos/kiyaku', array('status' => 404));
	Router::connect('/infos/:link_cd', array('controller' => 'infos', 'action' => 'article'), array('link_cd' => '[0-9a-z_-]+'));

	Router::connect('/campaign/:link_cd', array('controller' => 'wpcampaign', 'action' => 'index'), array('link_cd' => '[0-9a-z_-]+'));

	// アフィリエイト用リンク用
	Router::connect('/af_relay/:affiliate_id/', array('controller' => 'affiliaterelays', 'action' => 'index'));

	//都道府県 正しいURLに301転送
//	Router::connect('/hokkaido', array('controller' => 'prefectures', 'action' => 'index', 'prefecture_id' => 1));
	Router::connect('/aomori', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 2));
	Router::connect('/iwate', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 3));
	Router::connect('/miyagi', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 4));
	Router::connect('/akita', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 5));
	Router::connect('/yamagata', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 6));
	Router::connect('/fukushima', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 7));
	Router::connect('/ibaraki', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 8));
	Router::connect('/tochigi', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 9));
	Router::connect('/gunma', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 10));
	Router::connect('/saitama', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 11));
	Router::connect('/chiba', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 12));
	Router::connect('/tokyo', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 13));
	Router::connect('/kanagawa', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 14));
	Router::connect('/niigata', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 15));
	Router::connect('/toyama', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 16));
	Router::connect('/ishikawa', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 17));
	Router::connect('/fukui', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 18));
	Router::connect('/yamanashi', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 19));
	Router::connect('/nagano', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 20));
	Router::connect('/gifu', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 21));
	Router::connect('/shizuoka', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 22));
	Router::connect('/aichi', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 23));
	Router::connect('/mie', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 24));
	Router::connect('/shiga', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 25));
	Router::connect('/kyoto', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 26));
	Router::connect('/osaka', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 27));
	Router::connect('/hyogo', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 28));
	Router::connect('/nara', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 29));
	Router::connect('/wakayama', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 30));
	Router::connect('/tottori', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 31));
	Router::connect('/shimane', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 32));
	Router::connect('/okayama', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 33));
	Router::connect('/hiroshima', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 34));
	Router::connect('/yamaguchi', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 35));
	Router::connect('/tokushima', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 36));
	Router::connect('/kagawa', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 37));
	Router::connect('/ehime', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 38));
	Router::connect('/kouchi', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 39));
	Router::connect('/fukuoka', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 40));
	Router::connect('/saga', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 41));
	Router::connect('/nagasaki', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 42));
	Router::connect('/kumamoto', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 43));
	Router::connect('/oita', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 44));
	Router::connect('/miyazaki', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 45));
	Router::connect('/kagoshima', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 46));
//	Router::connect('/okinawa', array('controller' => 'prefectures', 'action' => 'index', 'prefecture_id' => 47));

	//URL修正したため、以前のURLの場合リダイレクトさせる
	Router::connect('/hukushima', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 7));
	Router::connect('/hukui', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 18));
	Router::connect('/gihu', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 21));
	Router::connect('/hukuoka', array('controller' => 'prefectures', 'action' => 'moved_url', 'prefecture_id' => 40));

	Router::connect('/kagoshima_remote_islands', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'kyushu', 'pref_link_cd' => 'kagoshima', 'area_link_cd' => 'yakushima'));
	Router::connect('/west_coast_east_coast', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'onna_nago_motobu_kunigami'));
	Router::connect('/southern_part', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'naha'));
	Router::connect('/motobu_nago_kunigami', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'onna_nago_motobu_kunigami'));
	Router::connect('/okinawa_remote_islands', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'ishigakijima'));
	Router::connect('/koza_chatan_ginowan', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'koza_chatan_ginowan_uruma'));

	Router::connect('/kagoshima/kagoshima_remote_islands', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'kyushu', 'pref_link_cd' => 'kagoshima', 'area_link_cd' => 'yakushima'));
	Router::connect('/kyushu/kagoshima/kagoshima_remote_islands', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'kyushu', 'pref_link_cd' => 'kagoshima', 'area_link_cd' => 'yakushima'));
	Router::connect('/okinawa/west_coast_east_coast', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'onna_nago_motobu_kunigami'));
	Router::connect('/okinawa/southern_part', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'naha'));
	Router::connect('/okinawa/motobu_nago_kunigami', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'onna_nago_motobu_kunigami'));
	Router::connect('/okinawa/okinawa_remote_islands', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'ishigakijima'));
	Router::connect('/okinawa/koza_chatan_ginowan', array('controller' => 'city', 'action' => 'moved_url2', 'region_link_cd' => 'okinawa', 'pref_link_cd' => 'okinawa', 'area_link_cd' => 'koza_chatan_ginowan_uruma'));

	Router::connect('/sendai_station', array('controller' => 'station', 'action' => 'moved_url2', 'region_link_cd' => 'tohoku', 'pref_link_cd' => 'miyagi', 'url' => 'miyagi_sendai_station'));
	Router::connect('/tohoku/miyagi/sendai_station', array('controller' => 'station', 'action' => 'moved_url2', 'region_link_cd' => 'tohoku', 'pref_link_cd' => 'miyagi', 'url' => 'miyagi_sendai_station'));
	Router::connect('/kyushu/kagoshima/sendai_station', array('controller' => 'station', 'action' => 'moved_url2', 'region_link_cd' => 'kyushu', 'pref_link_cd' => 'kagoshima', 'url' => 'kagoshima_sendai_station'));

	//地方ページ 正しいURLに301転送
	Router::connect('/area_tohoku', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'tohoku'));
	Router::connect('/area_kanto', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'kanto'));
	Router::connect('/area_koushinetsu', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'koushinetsu'));
	Router::connect('/area_hokuriku', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'hokuriku'));
	Router::connect('/area_tokai', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'tokai'));
	Router::connect('/area_kansai', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'kansai'));
	Router::connect('/area_chugoku', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'chugoku'));
	Router::connect('/area_shikoku', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'shikoku'));
	Router::connect('/area_kyushu', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'kyushu'));
	Router::connect('/area_okinawa', array('controller' => 'region', 'action' => 'moved_url', 'region_link_cd' => 'okinawa'));

	// /rentacar/region/～は重複コンテンツとなってしまうので404とする
	Router::connect('/region', array('status' => 404));

	//テスト用
	if (Configure::read('debug') >= 1) {
		Router::connect('/test/', array('controller' => 'test', 'action' => 'index'));
	}
/**
 * Load all plugin routes.  See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';

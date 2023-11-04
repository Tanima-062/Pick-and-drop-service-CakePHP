<?php

App::uses('AppController', 'Controller');

class StationController extends AppController {

	public $components = array('Areamap', 'OfficeUtil', 'StationContents', 'BreadCrumb');
	public $uses = array('CommodityRentOffice', 'Prefecture','City', 'Landmark', 'Area', 'RcPostmeta', 'Office', 'Station', 'YotpoReview',
		'Commodity', 'CommodityPrivilege', 'Client', 'ClientCarModel', 'OfficeStation', 'DropOffAreaRate', 'OfficeSupplement',
		'CarModel', 'StockGroup', 'CarClassStock', 'Reservation', 'CommodityEquipment', 'Equipment');
	public $use_searchbox = true;
	// 仮で、option_manage.jsを指定
	public $new_js = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $helpers = array('CreateUrl');

	private $methodOfTransportOptions = array(
		0 => '徒歩',
		1 => '送迎車',
		2 => '送迎車',
		3 => '車'
	);

	private $carTypeCatchCopyList = array(
		1 => '細い路地や短距離の移動に便利な軽自動車',
		2 => '街乗りにぴったりなコンパクト',
		3 => '安定の走りで長距離移動も安心のミドル・セダンタイプ',
		5 => '車内広々で長時間移動でも快適な1BOXタイプ',
		9 => 'ご家族での遠出に最適のRV・ミニバン',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		// Dispatcherで取得しないので一時的にここで駅情報取得
		if (!empty($this->request->params['url'])) {
			$this->request->params['station_arr'] = $this->Station->getStationByStationLinkCd($this->request->params['url']);
		}
		if (!empty($this->request->params['pref_link_cd'])) {
			if ($this->request->params['pref_link_cd'] == 'hokkaido' || $this->request->params['pref_link_cd'] == 'okinawa') {
				$this->request->params['region_link_cd'] = 'area_' . $this->request->params['pref_link_cd'];
			}
		}
	}

	// 正しいURLに301転送
	public function moved_url() {
		$params = $this->request->params;

		if (empty($params['url'])) {
			throw new NotFoundException();
		}

		$station_arr = $params['station_arr'];
		if (empty($station_arr)) {
			throw new NotFoundException();
		}

		// 都道府県リンクコードを取得する
		$prefecture_arr = $this->Prefecture->getLinkCdAndRegionLinkCdById($station_arr[0]['Station']['prefecture_id']);
		if (empty($prefecture_arr)) {
			throw new NotFoundException();
		}

		$region_link_cd = str_replace('area_', '', $prefecture_arr['Prefecture']['region_link_cd']);
		$pref_link_cd = $prefecture_arr['Prefecture']['link_cd'];
		$qs = !empty($this->request->query) ? '?' . http_build_query($this->request->query) : '';

		// 北海道・沖縄のみ都道府県 = 地方とする
		if ($region_link_cd === $pref_link_cd) {
			$this->redirect(DS . $pref_link_cd . DS . $params['url'] . DS . $qs, 301);
		} else {
			$this->redirect(DS . $region_link_cd . DS . $pref_link_cd . DS . $params['url'] . DS . $qs, 301);
		}
	}

	// 正しいURLに301転送
	public function moved_url2() {
		$params = $this->request->params;
		if (empty($params['region_link_cd']) || empty($params['pref_link_cd']) || empty($params['url'])) {
			throw new NotFoundException();
		}

		$region_link_cd = str_replace('area_', '', $params['region_link_cd']);
		$pref_link_cd = $params['pref_link_cd'];
		$qs = !empty($this->request->query) ? '?' . http_build_query($this->request->query) : '';

		// 北海道・沖縄のみ都道府県 = 地方とする
		if ($region_link_cd === $pref_link_cd) {
			$this->redirect(DS . $pref_link_cd . DS . $params['url'] . DS . $qs, 301);
		} else {
			$this->redirect(DS . $region_link_cd . DS . $pref_link_cd . DS . $params['url'] . DS . $qs, 301);
		}
	}

	public function index() {

		$params = $this->request->params;

		if (empty($params['url'])) {
			throw new NotFoundException();
		}

		$stationLinkCd = $params['url'];

		// 駅の情報取得
		$stationList = $params['station_arr'][0]['Station'];

		$regionLinkCd = str_replace('area_', '', $params['region_link_cd']);
		$prefectureLinkCd = $params['pref_link_cd'];
		$baseUrl = ($regionLinkCd === $prefectureLinkCd) ? $regionLinkCd . DS : $regionLinkCd . DS . $prefectureLinkCd . DS;

		$stationId = $stationList['id'];
		$stationName = $stationList['name'] . Constant::stationTypes()[$stationList['type']];

		// 都道府県を取得
		$prefectureId = $stationList['prefecture_id'];
		$prefectureList = $this->Prefecture->getAllLinkCdAndRegionLinkCd();
		$prefectureName = $prefectureList[$prefectureId]['Prefecture']['name'];
		// 地方別都道府県
		$prefectureListGroupByRegion = Hash::combine($prefectureList, '{n}.Prefecture.link_cd', '{n}.Prefecture.name', '{n}.Prefecture.region_link_cd');
		unset($prefectureList);

		$this->set(compact('regionLinkCd', 'baseUrl', 'prefectureLinkCd', 'prefectureName', 'stationName', 'prefectureListGroupByRegion'));

		$regions = Constant::regions();
		$this->set('regions', $regions);
		$this->set('regionName', $regions[$regionLinkCd]);

		$defaultDate = explode('-', $defaultYmd = date('Y-m-d', strtotime('+7 days')));

		// 検索パラメータ設定
		$search['params'] = $params = array(
			'place' => '4',
			'station_id' => $stationId,
			'prefecture' => $prefectureId,
			'year' => $defaultDate[0],
			'month' => $defaultDate[1],
			'day' => $defaultDate[2],
			'time' => '11-00',
			'return_way' => '0',
			'return_year' => $defaultDate[0],
			'return_month' => $defaultDate[1],
			'return_day' => $defaultDate[2],
			'return_time' => '17-00',
		);
		$search['baseUrl'] = '/rentacar/searches?' . urldecode(http_build_query($search['params']));

		// 年月日を連結
		$params['from'] = $defaultYmd;
		$params['to'] = $defaultYmd;

		$this->OptionsManage->setSearchOptions($search['params']);

		$this->set(compact('search'));

		// 駅の投稿を取得
		$stationContents = $this->RcPostmeta->getStationPostmetaDataByStationLinkCd($stationLinkCd, $prefectureId);
		$this->set(compact('stationContents'));

		// 駅に紐づく店舗一覧を取得
		list($clientList, $officeInfoList) = $this->StationContents->getClientAndOfficeListByStationId($stationId);

		foreach ($officeInfoList as &$info) {
			$businessHours = $this->OfficeUtil->formatOfficeBusinessHours($info);
			$info['businessHours'] = $businessHours;
		}
		unset($info);

		$this->set(compact('clientList', 'officeInfoList'));
		$officeIds = Hash::extract($officeInfoList, '{n}.id');

		//// 最安値 ////
		$prices = $this->Commodity->getPriceByOfficeId($officeIds);

		// 車種ごとに最安値を出したい
		$typeBestPrices = $this->StationContents->getBestPricesForCarTypes($prices);

		// 車両タイプ別最安値のリンク先
		$bestPriceCarTypes = $this->StationContents->bestPriceCarTypes;

		// SPでの日付はdate,return_dateのためパラメータに追加する
		$rankingDate = array(
			'date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
			'return_date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
		);
		foreach ($bestPriceCarTypes as $carTypeId => &$carType) {
			$carType['url'] = '/rentacar/searches?'.urldecode(http_build_query(array_merge($search['params'], $carType['params'], $rankingDate)));
			$carType['price'] = !empty($typeBestPrices[$carTypeId]) ? '&yen;' . number_format($typeBestPrices[$carTypeId]['bestPrice']) : '最安値を検索';
		}

		// 車種ごとの定員
		$typeCapacityList = $this->StationContents->getCarTypeCapacityList();

		// 会社別基本料金の最安値
		$clientBestPriceAndRank = $this->StationContents->getPriceAndRankForClients($prices);
		$lowestPriceClient = array();
		if (!empty($clientBestPriceAndRank)) {
			$lowestPriceClient = $clientBestPriceAndRank[key($clientBestPriceAndRank)];
			$lowestPriceClient['name'] = $clientList[$lowestPriceClient['id']]['name'];
		}

		$this->set(compact('bestPriceCarTypes', 'typeBestPrices', 'typeCapacityList', 'clientBestPriceAndRank'));

		// おすすめ車種を取得
		$catchCopyList = $this->carTypeCatchCopyList;
		$recommendCarList = $this->StationContents->getRecommendCarList($officeIds, array_keys($catchCopyList));

		// 会社別、Yotpoレビューのレーティングと数を取得
		$clientRatings = $this->YotpoReview->getRatingsGroupByClientId();
		// レビューのある会社のリスト（3社まで）
		foreach ($clientRatings as $clientId => $rating) {
			// レーティングは小数点以下第一位まで
			$clientRatings[$clientId]['rating'] = number_format($rating['rating'], 1, '.', '');
		}
		// 口コミ
		list($reviewCount, $yotpoReviews,$yotpoReviewOnlyScore) = $this->StationContents->getYotpoReviews($stationId);

		$yotpoReviewsByClient = array();
		$yotpoReviewLimit = Constant::YOTPO_REVIEW_LIMIT;

		foreach ($yotpoReviews as $review) {
			$yotpoReviewsByClient[$review['client_id']][] = $review;
		}
		$this->set(compact('reviewCount', 'yotpoReviews', 'yotpoReviewOnlyScore', 'clientRatings', 'yotpoReviewsByClient', 'yotpoReviewLimit'));

		// 都道府県マップ
		$this->Areamap->setAreamapViewVars($prefectureId, '04', $stationName . '周辺');

		// 営業所IDをおすすめ順に並べ替える
		$recommendOfficeIds = $this->StationContents->getRecomendedOfficeIds($clientList, $officeInfoList);

		// 乗り捨て料金表を取得する
		$dropOffTable = $this->StationContents->getDropOffTable($clientList, $officeInfoList, $stationList);

		$this->set(compact('catchCopyList', 'recommendCarList', 'recommendOfficeIds', 'dropOffTable'));

		$recommendClients = Hash::combine(array_slice($clientList, 0, 2), '{n}.id', '{n}.name');

		// おすすめのレンタカーについて
		$recommendOfficeInfo = $this->StationContents->getAboutRecommendRentacar($recommendClients, $officeInfoList, $stationName, $this->methodOfTransportOptions);
		// レンタカー貸出までにかかる時間について
		$aboutTimeInfo = $this->StationContents->getAboutTimeRentacar($clientList, $officeInfoList, $stationName);
		// 人気のレンタカー車両タイプ
		$popularCarTypeInfo = $this->StationContents->getPopularCarType($officeIds);
		// 人気の車両タイプの平均料金
		if (!empty($popularCarTypeInfo)) {
			$popularCarTypeInfo['avgPrice'] = $this->StationContents->getAveragePriceByCarType($popularCarTypeInfo['carTypeId'], $prices);
		}

		$this->set(compact('recommendClients', 'recommendOfficeInfo', 'lowestPriceClient', 'aboutTimeInfo', 'popularCarTypeInfo'));

		if ($prefectureName == '北海道'){
			$title_prefecture = $prefectureName;
		} else {
			$title_prefecture = mb_substr($prefectureName, 0, -1, "utf-8");
		}
		$this->set('title_prefecture', $title_prefecture);

		// meta系
		$this->set('title_for_layout', $stationName . '周辺の格安レンタカー比較・予約｜スカイチケット');
		$this->set('description_for_layout', $stationName . 'の格安レンタカー（乗り捨て可）を比較・予約するならスカイチケット！当日予約や24時間営業店舗などのプランを安い価格でご提供。'.$stationName.'前、駅近くのレンタカーをスカイチケットで簡単に料金比較して、最安値でお得に予約！');
		$this->set('keywords', $stationName .','. $prefectureName. ',' . $title_prefecture . ",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");

		// パンくずリスト設定
		$progressArr = $this->BreadCrumb->setStation($this->action, $regions[$regionLinkCd], $regionLinkCd, $prefectureName, $prefectureLinkCd, $stationName, $stationLinkCd);
		$this->set('progress_arr', $progressArr);

		// 車種別最安値カレンダー
		$calendar = $this->StationContents->createBestPriceCalendar($stationId);
		$this->set('calendar', $calendar);
	}

	public function sp_index() {

		$this->index();

		$weekdayListGroup = array(array('mon' => '月', 'tue' => '火', 'wed' => '水', 'thu' => '木', 'fri' => '金'), array('sat' => '土', 'sun' => '日', 'hol' => '祝'));
		$weekdayList = array();

		// 曜日の営業時間の表記をひとまとめにする
		foreach ($this->viewVars['officeInfoList'] as $key => $office) {
			$officeInfo = $office['Office'];
			foreach ($weekdayListGroup as $weekdayGroup) {

				$hours_from = null;
				$hours_to = null;
				$weekdayGroupKey = null;

				foreach ($weekdayGroup as $weekday => $weekdayName) {
					if (!empty($officeInfo[$weekday . '_hours_from'])) {
						if ($hours_from == $officeInfo[$weekday . '_hours_from'] AND $hours_to == $officeInfo[$weekday . '_hours_to']) {
							$weekdayList[$weekdayGroupKey] .= $weekdayName;
						} else {
							$weekdayGroupKey = $weekday;
							$hours_from = $officeInfo[$weekday . '_hours_from'];
							$hours_to = $officeInfo[$weekday . '_hours_to'];
							$weekdayList[$weekdayGroupKey] = $weekdayName;
						}
					}
				}
			}
			$this->viewVars['officeInfoList'][$key]['Office']['weekdayList'] = $weekdayList;
		}
	}
}

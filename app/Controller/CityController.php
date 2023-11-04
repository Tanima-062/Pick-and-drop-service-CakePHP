<?php

App::uses('AppController', 'Controller');

class CityController extends AppController {

	public $uses = array('Commodity',
		'Prefecture', 'Landmark', 'Area',
		'RcPostmeta', 'Office', 'City','YotpoReview','Client');
	public $use_searchbox = true;
	//仮で、option_manage.jsを指定
	public $new_js = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $helpers = array('CreateUrl');
	// スマホ用ページ内リンク
	public $sp_menu = array(
		array('#01', 'レンタカー会社・店舗一覧'),
		array("#02", 'レンタカー乗り捨て料金'),
		array("#03", '空港内受付カウンター'),
		array('#04', '近隣空港から探す'),
		array('#05', '近隣エリアから探す'),
		array("#06", 'レンタカー情報'),
	);
	public $components = array('BreadCrumb','BaseContents');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	// 正しいURLに301転送
	public function moved_url() {
		$params = $this->request->params;
		if (empty($params['area_link_cd'])) {
			throw new NotFoundException();
		}

		$area_arr = $params['area_arr'];
		if (empty($area_arr)) {
			throw new NotFoundException();
		}

		// 都道府県リンクコードを取得する
		$prefecture_arr = $this->Prefecture->getLinkCdAndRegionLinkCdById($area_arr[0]['Area']['prefecture_id']);
		if (empty($prefecture_arr)) {
			throw new NotFoundException();
		}

		$region_link_cd = str_replace('area_', '', $prefecture_arr['Prefecture']['region_link_cd']);
		$pref_link_cd = $prefecture_arr['Prefecture']['link_cd'];
		$qs = !empty($this->request->query) ? '?' . http_build_query($this->request->query) : '';

		// 北海道・沖縄のみ都道府県 = 地方とする
		if ($region_link_cd === $pref_link_cd) {
			$this->redirect(DS . $pref_link_cd . DS . $params['area_link_cd'] . DS . $qs, 301);
		} else {
			$this->redirect(DS . $region_link_cd . DS . $pref_link_cd . DS . $params['area_link_cd'] . DS . $qs, 301);
		}
	}

	// 正しいURLに301転送
	public function moved_url2() {
		$params = $this->request->params;
		if (empty($params['region_link_cd']) || empty($params['pref_link_cd']) || empty($params['area_link_cd'])) {
			throw new NotFoundException();
		}

		$region_link_cd = str_replace('area_', '', $params['region_link_cd']);
		$pref_link_cd = $params['pref_link_cd'];
		$qs = !empty($this->request->query) ? '?' . http_build_query($this->request->query) : '';

		// 北海道・沖縄のみ都道府県 = 地方とする
		if ($region_link_cd === $pref_link_cd) {
			$this->redirect(DS . $pref_link_cd . DS . $params['area_link_cd'] . DS . $qs, 301);
		} else {
			$this->redirect(DS . $region_link_cd . DS . $pref_link_cd . DS . $params['area_link_cd'] . DS . $qs, 301);
		}
	}

	public function index() {
		$this->loadComponent('OfficeUtil');
		$params = $this->request->params;

		if (empty($params['area_link_cd'])) {
			throw new NotFoundException();
		}

		$areaLinkCd = $params['area_link_cd'];

		$region_link_cd = str_replace('area_', '', $params['region_link_cd']);
		$this->set('regionLinkCd',$region_link_cd);
		$regions = Constant::regions();
		$regionName = $regions[$region_link_cd];
		$pref_link_cd = $params['pref_link_cd'];
		$base_url = ($region_link_cd === $pref_link_cd) ? $region_link_cd . DS : $region_link_cd . DS . $pref_link_cd . DS;

		//市区町村の情報取得
		$areaList = $this->request->params['area_arr'];
		$areaName = $areaList[0]['Area']['name'];

		//市区町村の投稿を取得
		$cityContents = $this->RcPostmeta->getAreaPostmetaDataByAreaLinkCd($areaLinkCd, $areaList['0']['Area']['prefecture_id']);

		//検索用空港IDを取得
		$prefecture = $this->Prefecture->getNameById($areaList['0']['Area']['prefecture_id']);

		$landmark['prefecture_name'] = $prefecture['Prefecture']['name'];
		$prefectureName = $landmark['prefecture_name'];

		//対象都道府県のエリア一覧
		$landmarkList['area'] = $this->Area->getAreaListByPrefectureId($areaList['0']['Area']['prefecture_id'], 0);

		//対象都道府県の空港一覧
		$airportLinkCdList = $this->Landmark->getAirportLinkCdListByPrefectureId($areaList['0']['Area']['prefecture_id']);

		$this->set('airportLinkCdList', $airportLinkCdList);

		$areaLinkCdList = array();
		foreach ($landmarkList['area'] as $key => $val) {
			$area = $this->Area->getAreaById($key);

			if (isset($area['0']['Area']['area_link_cd']) && $key != $areaList['0']['Area']['id']) {
				$areaLinkCdList[$area['0']['Area']['area_link_cd']] = $val;
			}
		}

		$this->set('areaLinkCdList', $areaLinkCdList);

		//エリアから店舗一覧を取得
		$officeInfo = $this->Office->getOfficeNearListByAreaId($areaList['0']['Area']['id']);

		$officeInfoList = array();

		if (!empty($officeInfo)) {
			foreach ($officeInfo as $info) {
				$businessHours = $this->OfficeUtil->formatOfficeBusinessHours($info['Office']);
				$info['Office']['businessHours'] = $businessHours;
				$officeInfoList[$info['Office']['id']] = $info;
			}
		}

		//エリアの市区町村一覧を取得
		$cityInfoList = $this->City->getCitiesByAreaId($areaList['0']['Area']['id']);

		$defaultDate = explode('-', $defaultYmd = date('Y-m-d', strtotime('+7 days')));

		// 会社別、Yotpoレビューのレーティングと数を取得
		$clientRatings = $this->YotpoReview->getRatingsGroupByClientId();
		// レビューのある会社のリスト（3社まで）
		foreach ($clientRatings as $clientId => $rating) {
			// レーティングは小数点以下第一位まで
			$clientRatings[$clientId]['rating'] = number_format($rating['rating'], 1, '.', '');
		}
		// エリアに寄せられたYotpoレビュー
		$reviews = $this->YotpoReview->getReviewsByAreaId($areaList['0']['Area']['id']);
		$reviewCount = $this->YotpoReview->getReviewsCountByAreaId($areaList['0']['Area']['id']);
		$yotpoReviews = array();
		$yotpoReviewsByClient = array();

		$yotpoReviewLimit = Constant::YOTPO_REVIEW_LIMIT;

		foreach ($reviews as $review) {
			$yotpoReview = array(
				'title' => $review['YotpoReview']['title'],
				'content' => $review['YotpoReview']['content'],
				'score' => $review['YotpoReview']['score'],
				'created_at' => $review[0]['created_at'],
				'client_id' => $review['Client']['id'],
				'client_name' => $review['Client']['name'],
				'client_url' => $review['Client']['url'],
				'office_name' => $review['Office']['name'],
				'office_url' => $review['Office']['url']
			);

			if($yotpoReviewLimit > count($yotpoReviews)){
				$yotpoReviews[] = $yotpoReview;
			}

			// 総合得点のみ設定
			$yotpoReviewOnlyScore[] = array(
				'score' => $review['YotpoReview']['score']
			);

			$yotpoReviewsByClient[$review['Client']['id']][] = $yotpoReview;
		}
		unset($reviews);
		$clientList = $this->Client->getClientList();
		if(!empty($clientList)){
			$clientList = Hash::combine($clientList, '{n}.Client.id', '{n}.Client');
		}
		$this->set(compact('reviewCount', 'yotpoReviews', 'yotpoReviewOnlyScore', 'yotpoReviewsByClient', 'clientRatings', 'clientList', 'yotpoReviewLimit'));

		//検索パラメータ設定
		$search['params'] = $params = array(
			'place' => '1',
			'area_id' => $areaList['0']['Area']['id'],
			'prefecture' => $areaList['0']['Area']['prefecture_id'],
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
		$search['url'] = '/rentacar/searches?' . urldecode(http_build_query($search['params']));

		//年月日を連結
		$params['from'] = $defaultYmd;
		$params['to'] = $defaultYmd;

		$this->OptionsManage->setSearchOptions($search['params']);

		// 最安値
		$prices = $this->Commodity->getPriceForCityPage($areaList['0']['Area']['id']);
		$typeBestPrices = $this->BaseContents->getBestPricesForCarTypes($prices);

		$bestPriceCarTypes = $this->BaseContents->bestPriceCarTypes;
		foreach ($bestPriceCarTypes as $carTypeId => &$carType) {
			$carType['url'] = '/rentacar/searches?'.urldecode(http_build_query(array_merge($search['params'], $carType['params'])));
			$carType['price'] = !empty($typeBestPrices[$carTypeId]) ? '&yen;' . number_format($typeBestPrices[$carTypeId]['bestPrice']) : '最安値を検索';
		}
		$this->set('bestPriceCarTypes', $bestPriceCarTypes);
		$typeCapacityList = $this->BaseContents->getCarTypeCapacityList();
		$this->set('typeCapacityList', $typeCapacityList);

		$this->set('landmarkList', $landmarkList);
		$this->set('officeInfoList', $officeInfoList);
		$this->set('cityInfoList', $cityInfoList);
		$this->set('areaList', $areaList);
		$this->set('regionName', $regionName);
		$this->set('cityContents', $cityContents);
		$this->set('landmark', $landmark);
		$this->set('prefectureLinkCd', $pref_link_cd);
		$this->set('search', $search);
		$this->set('base_url', $base_url);


		if($prefectureName == '北海道'){
			$title_prefecture = $prefectureName;
		}else{
			$title_prefecture = mb_substr($prefectureName,0,-1,"utf-8");
		}
		$this->set('areaName',$areaName);
		//meta系
		$this->set('title_for_layout', $areaName . '周辺の格安レンタカー料金比較・予約｜スカイチケット');
		$this->set('description_for_layout', $areaName . '周辺の格安レンタカーを比較・予約！乗り捨てや当日予約、24時間営業の店舗の検索はもちろん、車種・オプションやおすすめレンタカー会社も指定可能なので簡単に比較できる！'.$areaName.'のレンタカーを予約するならスカイチケットが便利でお得！');
		$this->set('keywords', $areaName . ',' . $prefectureName .','. $title_prefecture . ",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");

		// スマホ用ページ内リンク設定
		$this->set('sp_menu_max', count($this->sp_menu));
		if (empty($officeInfoList)) {
			unset($this->sp_menu[0]);
		}
		if (empty($airportLinkCdList)) {
			unset($this->sp_menu[3]);
		}
		if (empty($landmarkList['area'])) {
			unset($this->sp_menu[4]);
		}
		$this->sp_menu = array_merge($this->sp_menu);
		$this->set('sp_menu', $this->sp_menu);

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setCity($this->action, 
			$region_link_cd, $regionName, $pref_link_cd, $prefectureName, $areaLinkCd, $areaName
		);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {

		$this->index();

		$weekdayListGroup = array(array('mon' => '月', 'tue' => '火', 'wed' => '水', 'thu' => '木', 'fri' => '金'), array('sat' => '土', 'sun' => '日', 'hol' => '祝'));
		$weekdayList = array();

		//曜日の営業時間の表記をひとまとめにする
		foreach ($this->viewVars['officeInfoList'] as $key => $office) {
			$officeInfo = $office['Office'];
			foreach ($weekdayListGroup as $weekdayGroup) {

				$hours_from = null;
				$hours_to = null;
				$weekdayGroupKey = null;
				$weekdayGroupText = null;

				foreach ($weekdayGroup as $weekday => $weekdayName) {
					if (!empty($officeInfo[$weekday . '_hours_from'])) {
						if ($hours_from == $officeInfo[$weekday . '_hours_from'] AND $hours_to == $officeInfo[$weekday . '_hours_to']) {
							$weekdayList[$weekdayGroupKey] .= $weekdayName;
						} else {
							$weekdayGroupKey = $weekday;
							$weekdayGroupText = $weekdayName;
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

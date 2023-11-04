<?php

App::uses('AppController', 'Controller');

class MunicipalityController extends AppController {

	public $uses = array('Landmark', 'Office', 'Station', 'City', 'Commodity', 'YotpoReview','Client');
	public $use_searchbox = true;
	//仮で、option_manage.jsを指定
	public $new_js = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $helpers = array('CreateUrl');
	public $components = array('BreadCrumb','BaseContents');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function index() {
		$this->loadComponent('OfficeUtil');
		$params = $this->request->params;

		if (empty($params['link_cd'])) {
			throw new NotFoundException();
		}

		// 市区町村の情報取得
		$city = $params['city_arr'][0]['City'];
		$prefecture = $params['city_arr'][0]['Prefecture'];
		$prefectureName = $prefecture['name'];
		$this->set(compact('city', 'prefecture','prefectureName'));

		// ベースURL
		$region_link_cd = str_replace('area_', '', $params['region_link_cd']);
		$this->set('regionLinkCd',$region_link_cd);
		$regions = Constant::regions();
		$this->set('regionName',$regions[$region_link_cd]);
		$pref_link_cd = $params['pref_link_cd'];
		$base_url = ($region_link_cd === $pref_link_cd) ? $region_link_cd . DS : $region_link_cd . DS . $pref_link_cd . DS;
		$this->set('base_url', $base_url);
		$city_link_cd = $params['link_cd'];

		$defaultDate = explode('-', $defaultYmd = date('Y-m-d', strtotime('+7 days')));

		// 検索パラメータ設定
		$search['params'] = $params = array(
			'place' => '1',
			'area_id' => $city['area_id'],
			'prefecture' => $city['prefecture_id'],
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

		// 年月日を連結
		$params['from'] = $defaultYmd;
		$params['to'] = $defaultYmd;

		$this->OptionsManage->setSearchOptions($search['params']);
		$this->set('search', $search);

		// 最安値
		$prices = $this->Commodity->getPriceForMunicipalityPage($city['id']);
		$typeBestPrices = $this->BaseContents->getBestPricesForCarTypes($prices);

		$bestPriceCarTypes = $this->BaseContents->bestPriceCarTypes;
		foreach ($bestPriceCarTypes as $carTypeId => &$carType) {
			$carType['url'] = '/rentacar/searches?'.urldecode(http_build_query(array_merge($search['params'], $carType['params'])));
			$carType['price'] = !empty($typeBestPrices[$carTypeId]) ? '&yen;' . number_format($typeBestPrices[$carTypeId]['bestPrice']) : '最安値を検索';
		}
		$this->set('bestPriceCarTypes', $bestPriceCarTypes);
		$typeCapacityList = $this->BaseContents->getCarTypeCapacityList();
		$this->set('typeCapacityList', $typeCapacityList);

		// 市区町村にある店舗のリスト
		$officeInfo = $this->Office->getOfficeNearListByCityId($city['id']);
		$officeInfoList = array();
		if (!empty($officeInfo)) {
			foreach ($officeInfo as $info) {
				$businessHours = $this->OfficeUtil->formatOfficeBusinessHours($info['Office']);
				$info['Office']['businessHours'] = $businessHours;
				$officeInfoList[$info['Office']['id']] = $info;
			}
		}
		$this->set('officeInfoList', $officeInfoList);

		// 動的コンテンツ
		$activeContents = $this->getActiveContents($city, $officeInfoList, $prices);
		$this->set('activeContents', $activeContents);

		// 同エリアにある市区町村のリスト
		$neighbors = $this->City->getCitiesByAreaId($city['area_id']);
		$neighborList = array();
		foreach ($neighbors as $neighbor) {
			if ($neighbor['City']['id'] != $city['id']) {
				$region = str_replace('area_', '', $neighbor['Prefecture']['region_link_cd']);
				$pref = $neighbor['Prefecture']['link_cd'];
				$url = '/rentacar/' . $region . DS;
				if ($region != $pref) {
					$url .= $pref . DS;
				}
				$url .= $neighbor['City']['link_cd'] . DS;
				$neighborList[] = array(
					'name' => $neighbor['City']['name'],
					'url' => $url
				);
			}
		}
		$this->set('neighborList', $neighborList);

		// 会社別、Yotpoレビューのレーティングと数を取得
		$clientRatings = $this->YotpoReview->getRatingsGroupByClientId();
		// レビューのある会社のリスト（3社まで）
		foreach ($clientRatings as $clientId => $rating) {
			// レーティングは小数点以下第一位まで
			$clientRatings[$clientId]['rating'] = number_format($rating['rating'], 1, '.', '');
		}
		// 市区町村に寄せられたYotpoレビュー
		$reviews = $this->YotpoReview->getReviewsByCityId($city['id']);
		$reviewCount = $this->YotpoReview->getReviewsCountByCityId($city['id']);
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

		// 同都道府県の空港リスト
		$airportLinkCdList = $this->Landmark->getAirportLinkCdListByPrefectureId($city['prefecture_id']);

		$this->set('airportLinkCdList', $airportLinkCdList);

		// 同都道府県の主要駅リスト
		$stationList = $this->Station->getStationListWithAreaByPrefectureId($city['prefecture_id']);
		$majorStationList = Hash::extract($stationList, '{n}.Station[major_flg=1]');
		$stationCount = count($majorStationList);
		for ($i = 0; $i < $stationCount; $i++) {
			$majorStationList[$i]['name'] .= ($majorStationList[$i]['type'] == 0) ? '駅' : '停留場';
		}
		$this->set('majorStationList', $majorStationList);

		if($prefectureName == '北海道'){
			$title_prefecture = $prefectureName;
		}else{
			$title_prefecture = mb_substr($prefectureName,0,-1,"utf-8");
		}
		$this->set('title_prefecture',$title_prefecture);

		// meta系
		$this->set('title_for_layout', $city['name'] . 'の格安レンタカー比較・予約｜スカイチケット');
		$this->set('description_for_layout', $city['name'] . 'の格安レンタカーを予約するならスカイチケット！乗り捨て対応・市内24時間営業の店舗の指定や、免責補償、カーナビ付きなど絞り込み検索でレンタカープランを一括検索可能。日帰りなど短時間の利用はもちろん、一週間以上の長期予約も。レンタカー料金を簡単比較して最安値で予約！');
		$this->set('keywords', $city['name'] .','. $prefectureName .','. $title_prefecture . ",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setMunicipality($this->action, $regions[$region_link_cd], $region_link_cd, $prefectureName, $pref_link_cd, $city['name'], $city_link_cd);
		$this->set('progress_arr', $progressArr);
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

	private function getActiveContents($cityInfo, $officeInfoList, $priceList) {
		$contents = '';

		if (!empty($officeInfoList)) {
			$contents .= $cityInfo['name'] . 'で利用できるレンタカー会社は' . count($officeInfoList) . '店舗あります。';

			$groupByClient = Hash::combine($officeInfoList, '{n}.Office.id', '{n}', '{n}.Client.id');
			$pickupOffices = array();
			foreach ($groupByClient as $clientId => $officeInfo) {
				$first = current($officeInfo);
				$first['Client']['name'] = str_replace(' ', '', $first['Client']['name']);
				$pickupOffices[] = $first;
				if (count($pickupOffices) >= 2) {
					break;
				}
			}
			$pickupCount = count($pickupOffices);

			$contents .= $cityInfo['name'] . 'のレンタカーを借りるなら、口コミ高評価の';
			if ($pickupCount > 1) {
				$contents .= $pickupOffices[0]['Client']['name'] . 'や' . $pickupOffices[1]['Client']['name'] . 'などがおすすめ。';
			} else {
				$contents .= $pickupOffices[0]['Client']['name'] . 'がおすすめ。';
			}

			for ($i = 0; $i < $pickupCount; $i++) {
				$office = $pickupOffices[$i]['Office'];
				$client = $pickupOffices[$i]['Client'];
				$access_dynamic = str_replace('（送迎なし）', '', $office['access_dynamic']);

				$contents .= $client['name'] . $office['name'] . 'は' . $access_dynamic;
				if ($pickupCount > 1 && $i == 0) {
					$contents .= '、';
				}
			}
			$contents .= 'の位置にあり、アクセスが便利です。';

			if (!empty($priceList)) {
				$sortPrice = Hash::sort($priceList, '{n}.price', 'asc');
				$clientList = Hash::combine($officeInfoList, '{n}.Client.id', '{n}.Client.name');
				if (isset($clientList[$sortPrice[0]['clientId']])) {
					$clientName = str_replace(' ', '', $clientList[$sortPrice[0]['clientId']]);
					$contents .= 'また、' . $cityInfo['name'] . 'で最安値のレンタカーを提供するのは' . $clientName . 'です。';
					$contents .= $cityInfo['name'] . 'の' . $clientName . 'では日帰り利用で' . $sortPrice[0]['carTypeName'] . $sortPrice[0]['price'] . '円～の格安で利用できます。';
				}
			}

			$contents .= $cityInfo['name'] . 'で大人気の格安レンタカーは売り切れる場合もありますので、ご予約はお早めに。';
		}

		return $contents;
	}
}

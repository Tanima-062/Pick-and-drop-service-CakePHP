<?php

App::uses('AppController', 'Controller');

class FerryterminalController extends AppController {

	public $uses = array('Commodity',
		'Prefecture',
		'Landmark',
		'Area',
		'RcPostmeta', 'Office', 'YotpoReview', 'Client');
	public $use_searchbox = true;
	// 仮で、option_manage.jsを指定
	public $new_js = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $helpers = array('CreateUrl');
	// スマホ用ページ内リンク
	public $sp_menu = array(
		array('#01', 'レンタカー会社・店舗一覧'),
		array("#02", 'レンタカー乗り捨て料金'),
		array("#03", 'ターミナル内受付カウンター'),
		array("#04", 'ロコミ'),
		array('#05', '近隣空港から探す'),
		array('#06', '近隣エリアから探す'),
		array("#07", 'レンタカー情報'),
	);
	public $components = array('BreadCrumb', 'LandmarkContents');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	// 正しいURLに301転送
	public function moved_url() {
		$params = $this->request->params;

		if (empty($params['link_cd'])) {
			throw new NotFoundException();
		}

		// 空港IDから都道府県リンクコードを取得する
		$prefecture_arr = $this->Prefecture->getLinkCdAndRegionLinkCdById($params['airport_arr']['prefecture_id']);
		if (empty($prefecture_arr)) {
			throw new NotFoundException();
		}

		$region_link_cd = str_replace('area_', '', $prefecture_arr['Prefecture']['region_link_cd']);
		$pref_link_cd = $prefecture_arr['Prefecture']['link_cd'];
		$qs = !empty($this->request->query) ? '?' . http_build_query($this->request->query) : '';

		// 北海道・沖縄のみ都道府県 = 地方とする
		if ($region_link_cd === $pref_link_cd) {
			$this->redirect(DS . $pref_link_cd . DS . $params['link_cd'] . DS . $qs, 301);
		} else {
			$this->redirect(DS . $region_link_cd . DS . $pref_link_cd . DS . $params['link_cd'] . DS . $qs, 301);
		}
	}

	public function index() {
		$this->loadComponent('OfficeUtil');
		$params = $this->request->params;

		if (empty($params['link_cd'])) {
			throw new NotFoundException();
		}

		$link_cd = $params['link_cd'];

		$region_link_cd = str_replace('area_', '', $params['region_link_cd']);
		$this->set('regionLinkCd',$region_link_cd);
		$regions = Constant::regions();
		$this->set('regions', $regions);
		$this->set('regionName',$regions[$region_link_cd]);
		$pref_link_cd = $params['pref_link_cd'];
		$base_url = ($region_link_cd === $pref_link_cd) ? $region_link_cd . DS : $region_link_cd . DS . $pref_link_cd . DS;

		// スカイチケットでの空港IDを取得
		$landmark = $params['airport_arr'];

		// 空港の投稿を取得
		$airportContents = $this->RcPostmeta->getAirportPostmetaDataByAirportCd($link_cd, $link_cd, $landmark['prefecture_id']);

		$prefectureList = $this->Prefecture->getAllLinkCdAndRegionLinkCd();
		// 地方別都道府県
		$prefectureListGroupByRegion = Hash::combine($prefectureList, '{n}.Prefecture.link_cd', '{n}.Prefecture.name', '{n}.Prefecture.region_link_cd');
		$this->set('prefectureListGroupByRegion', $prefectureListGroupByRegion);

		$landmark['prefecture_name'] = $prefectureList[$landmark['prefecture_id']]['Prefecture']['name'];
		unset($prefectureList);

		$prefectureName = $landmark['prefecture_name'];
		$landmarkName = $landmark['name'];

		// 対象都道府県のエリア一覧
		$landmarkList['area'] = $this->Area->getAreaListByPrefectureId($landmark['prefecture_id'], 0);
		$areaLinkCd = $this->Area->getAreaLinkCdListByAreaIds(array_keys($landmarkList['area']));

		// 対象都道府県の空港一覧
		$airportLinkCdList = $this->Landmark->getAirportLinkCdListByPrefectureId($landmark['prefecture_id']);
		unset($airportLinkCdList[$link_cd]);

		$this->set('airportLinkCdList', $airportLinkCdList);

		// 空港から店舗一覧を取得
		$officeInfo = $this->Office->getOfficeNearListByAirportId($landmark['id']);
		$officeInfoList = array();

		if (!empty($officeInfo)) {
			foreach ($officeInfo as $info) {
				$businessHours = $this->OfficeUtil->formatOfficeBusinessHours($info['Office']);
				$info['Office']['businessHours'] = $businessHours;
				$officeInfoList[$info['Office']['id']] = $info;
			}
		}

		// キャッチ画像
		if (isset($airportContents['airport-photo']) &&
			!empty($airportContents['airport-photo'])) {
			if ($airportContents['airport-photo']) {
				$params = array(
					'conditions' => array(
						"RcPostmeta.post_id" => $airportContents['airport-photo'],
						"RcPostmeta.meta_key" => "_wp_attached_file"
					)
				);
				$imgPathData = $this->RcPostmeta->find('first', $params);
				$airportContents['airport-photo'] = $imgPathData["RcPostmeta"]["meta_value"];
			}
		}

		// 画像
		if(isset($airportContents['airport-single-contents-list']) &&
			!empty($airportContents['airport-single-contents-list'])) {

			foreach ($airportContents['airport-single-contents-list'] as $key => $val) {
				if ($val['contents-img']) {
					$params = array(
						'conditions' => array(
							"RcPostmeta.post_id" => $val['contents-img'],
							"RcPostmeta.meta_key" => "_wp_attached_file"
						)
					);
					$imgPathData = $this->RcPostmeta->find('first', $params);
					$airportContents['airport-single-contents-list'][$key]['contents-img'] = $imgPathData["RcPostmeta"]["meta_value"];
				}
			}
		}

		// 会社別、Yotpoレビューのレーティングと数を取得
		$clientRatings = $this->YotpoReview->getRatingsGroupByClientId();
		// レビューのある会社のリスト（3社まで）
		foreach ($clientRatings as $clientId => $rating) {
			// レーティングは小数点以下第一位まで
			$clientRatings[$clientId]['rating'] = number_format($rating['rating'], 1, '.', '');
		}
		// 空港に寄せられたYotpoレビュー
		$reviews = $this->YotpoReview->getReviewsByAirportId($landmark['id']);
		$reviewCount = $this->YotpoReview->getReviewsCountByAirportId($landmark['id']);
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

			if ($yotpoReviewLimit > count($yotpoReviews)) {
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
		if (!empty($clientList)) {
			$clientList = Hash::combine($clientList, '{n}.Client.id', '{n}.Client');
		}
		$this->set(compact('reviewCount', 'yotpoReviews', 'yotpoReviewOnlyScore', 'yotpoReviewsByClient', 'clientRatings', 'clientList', 'yotpoReviewLimit'));

		$defaultDate = explode('-', $defaultYmd = date('Y-m-d', strtotime('+7 days')));

		// 検索パラメータ設定
		$search['params'] = $params = array(
			'place' => '3',
			'airport_id' => $landmark['id'],
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

		// 最安値
		$officeIds = Hash::extract($officeInfoList, '{n}.Office.id');
		$prices = $this->Commodity->getPriceByOfficeId($officeIds);
		$typeBestPrices = $this->LandmarkContents->getBestPricesForCarTypes($prices);

		$bestPriceCarTypes = $this->LandmarkContents->bestPriceCarTypes;

		// SPでの日付はdate,return_dateのためパラメータに追加する
		$rankingDate = array(
			'date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
			'return_date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
		);
		foreach ($bestPriceCarTypes as $carTypeId => &$carType) {
			$carType['url'] = '/rentacar/searches?'.urldecode(http_build_query(array_merge($search['params'], $carType['params'], $rankingDate)));
			$carType['price'] = !empty($typeBestPrices[$carTypeId]) ? '&yen;' . number_format($typeBestPrices[$carTypeId]['bestPrice']) : '最安値を検索';
		}
		$this->set('bestPriceCarTypes', $bestPriceCarTypes);
		$typeCapacityList = $this->LandmarkContents->getCarTypeCapacityList();
		$this->set('typeCapacityList', $typeCapacityList);

		// 年月日を連結
		$params['from'] = $defaultYmd;
		$params['to'] = $defaultYmd;

		$params['area_id'] = $this->Office->getOfficeAreaIdList(array(
			'airport_id' => $params['airport_id']
		));

		$this->OptionsManage->setSearchOptions($search['params']);
		$this->set('landmark', $landmark);
		$this->set('prefectureLinkCd', $pref_link_cd);
		$this->set('landmarkList', $landmarkList);
		$this->set('areaLinkCd', $areaLinkCd);
		$this->set('airportContents', $airportContents);
		$this->set('officeInfoList', $officeInfoList);
		$this->set('search', $search);
		$this->set('base_url', $base_url);

		if ($prefectureName == '北海道') {
			$title_prefecture = $prefectureName;
		} else {
			$title_prefecture = mb_substr($prefectureName, 0, -1, "utf-8");
		}

		// meta系
		$this->set('title_for_layout', $landmarkName . 'の格安レンタカー比較・予約｜スカイチケット');
		$this->set('description_for_layout', $landmarkName . 'の格安レンタカーを比較・予約！港で借りて空港で返せる乗り捨てプランや、おすすめのレンタカー会社を簡単に一括検索！'.$landmarkName.'前、フェリーターミナル近くのレンタカーをスカイチケットで比較して、安い料金でお得に予約！');

		$landmarkNameKeyword = str_replace(' ', ',', $landmarkName);
		$this->set('keywords', "レンタカー,$landmarkNameKeyword,$prefectureName,$title_prefecture,格安,比較,予約,乗り捨て,スカイチケット");

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

		// パンくずリスト設定
		$progressArr = $this->BreadCrumb->setFerryterminal($this->action, $landmarkName, $region_link_cd, $pref_link_cd, $link_cd, $regions[$region_link_cd], $prefectureName);
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

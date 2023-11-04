<?php
App::uses('AppController', 'Controller');
/**
 * Prefectures Controller
 *
 * @property Prefectures $Prefectures
 */
class PrefecturesController extends AppController {

	public $uses = array('Area', 'Prefecture', 'Landmark', 'RcPostmeta', 'Client', 'Office', 'City', 'Commodity', 'YotpoReview', 'CarModel', 'Station', 'CarType','Equipment', 'Privilege');
	public $use_searchbox = true;
	public $new_js = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $components = array('BreadCrumb', 'PrefectureContents');

	// 近隣の都道府県
	private $neighbors = array(
		1  => array(2),								// 北海道→青森
		2  => array(1, 3, 5),						// 青森　→北海道、岩手、秋田
		3  => array(2, 4, 5),						// 岩手　→青森、宮城、秋田
		4  => array(3, 5, 6, 7),					// 宮城　→岩手、秋田、山形、福島
		5  => array(2, 3, 4, 6),					// 秋田　→青森、岩手、宮城、山形
		6  => array(4, 5, 7, 15),					// 山形　→宮城、秋田、福島、新潟
		7  => array(4, 6, 8, 9, 10, 15),			// 福島　→宮城、山形、茨城、栃木、群馬、新潟
		8  => array(7, 9, 11, 12),					// 茨城　→福島、栃木、埼玉、千葉
		9  => array(7, 8, 10, 11),					// 栃木　→福島、茨城、群馬、埼玉
		10 => array(7, 9, 11, 15, 20),				// 群馬　→福島、栃木、埼玉、新潟、長野
		11 => array(8, 9, 10, 12, 13, 19),			// 埼玉　→茨城、栃木、群馬、千葉、東京、山梨
		12 => array(8, 11, 13),						// 千葉　→茨城、埼玉、東京
		13 => array(11, 12, 14, 19),				// 東京　→埼玉、千葉、神奈川、山梨
		14 => array(13, 19, 22),					// 神奈川→東京、山梨、静岡
		15 => array(6, 7, 10, 16, 20),				// 新潟　→山形、福島、群馬、富山、長野
		16 => array(15, 17, 20, 21),				// 富山　→新潟、石川、長野、岐阜
		17 => array(16, 18, 21),					// 石川　→富山、福井、岐阜
		18 => array(17, 21, 25, 26),				// 福井　→石川、岐阜、滋賀、京都
		19 => array(11, 13, 14, 20, 22),			// 山梨　→埼玉、東京、神奈川、長野、静岡
		20 => array(10, 15, 16, 19, 21, 22, 23),	// 長野　→群馬、新潟、富山、山梨、岐阜、静岡、愛知
		21 => array(16, 17, 18, 20, 23, 25),		// 岐阜　→富山、石川、福井、長野、愛知、滋賀
		22 => array(14, 19, 20, 23),				// 静岡　→神奈川、山梨、長野、愛知
		23 => array(20, 21, 22, 24),				// 愛知　→長野、岐阜、静岡、三重
		24 => array(23, 25, 26, 29, 30),			// 三重　→愛知、滋賀、京都、奈良、和歌山
		25 => array(18, 21, 24, 26),				// 滋賀　→福井、岐阜、三重、京都
		26 => array(18, 24, 25, 27, 28, 29),		// 京都　→福井、三重、滋賀、大阪、兵庫、奈良
		27 => array(26, 28, 29, 30),				// 大阪　→京都、兵庫、奈良、和歌山
		28 => array(26, 27, 31, 33),				// 兵庫　→京都、大阪、鳥取、岡山
		29 => array(24, 26, 27, 30),				// 奈良　→三重、京都、大阪、和歌山
		30 => array(24, 27, 29),					// 和歌山→三重、大阪、奈良
		31 => array(28, 32, 33, 34),				// 鳥取　→兵庫、島根、岡山、広島
		32 => array(31, 33, 34, 35),				// 島根　→鳥取、岡山、広島、山口
		33 => array(28, 31, 34),					// 岡山　→兵庫、鳥取、広島
		34 => array(31, 32, 33, 35),				// 広島　→鳥取、島根、岡山、山口
		35 => array(32, 34, 40),					// 山口　→島根、広島、福岡
		36 => array(37, 38, 39),					// 徳島　→香川、愛媛、高知
		37 => array(33, 36, 38),					// 香川　→岡山、徳島、愛媛
		38 => array(36, 37, 39),					// 愛媛　→徳島、香川、高知
		39 => array(36, 38),						// 高知　→徳島、愛媛
		40 => array(35, 41, 43, 44),				// 福岡　→山口、佐賀、熊本、大分
		41 => array(40, 42, 43),					// 佐賀　→福岡、長崎、熊本
		42 => array(40, 41, 43),					// 長崎　→福岡、佐賀、熊本
		43 => array(40, 41, 42, 44, 45, 46),		// 熊本　→福岡、佐賀、長崎、大分、宮崎、鹿児島
		44 => array(40, 43, 45),					// 大分　→福岡、熊本、宮崎
		45 => array(43, 44, 46),					// 宮崎　→熊本、大分、鹿児島
		46 => array(43, 45),						// 鹿児島→熊本、宮崎
		47 => array()								// 沖縄　→なし
	);

	public function beforeFilter() {
		parent::beforeFilter();
	}

	// 正しいURLに301転送
	public function moved_url() {
		$prefectureId = $this->params['prefecture_id'];
		if (empty($prefectureId)) {
			throw new NotFoundException();
		} elseif ($prefectureId < 1 || $prefectureId > 47) {
			throw new NotFoundException();
		}

		$ret = $this->Prefecture->getLinkCdAndRegionLinkCdById($prefectureId);

		if (empty($ret)) {
			throw new NotFoundException();
		}

		$qs = !empty($this->request->query) ? '?' . http_build_query($this->request->query) : '';
		$this->redirect(DS . str_replace('area_', '', $ret['Prefecture']['region_link_cd']) . DS . $ret['Prefecture']['link_cd'] . DS . $qs, 301);
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {

		$regions = Constant::regions();
		$this->set('regions', $regions);

		$prefectureId = $this->params['prefecture_id'];
		if (empty($prefectureId)) {
			throw new NotFoundException();
		} elseif ($prefectureId < 1 || $prefectureId > 47) {
			throw new NotFoundException();
		}
		$this->set(compact('prefectureId'));

		$regionLinkCd = str_replace('area_', '', $this->params['region_link_cd']);
		$regionName = $regions[$regionLinkCd];

		$prefLinkCd = $this->params['pref_link_cd'];
		$baseUrl = ($regionLinkCd === $prefLinkCd) ? $regionLinkCd . DS : $regionLinkCd . DS . $prefLinkCd . DS;

		$this->set(compact('baseUrl'));

		// エリアリスト
		$areaInfo = $this->Area->getAreaInfoByPrefectureId($prefectureId);
		$areaList = Hash::combine($areaInfo, '{n}.Area.id', '{n}.Area');
		$areaIds = array_keys($areaList);
		unset($areaInfo);
		$this->set(compact('areaList'));

		// 当該都道府県に店舗が存在する会社のリスト
		$clientList = $this->Client->getClientListByPrefectureId($prefectureId);

		// リンク用日付配列
		$linkDateArr = date_parse(date('Ymd', strtotime('+7 day')));

		// 検索用エリア文字列を生成する
		$areaListSearchString = '';
		foreach($areaList as $key => $val){
			$areaListSearchString .= '&area_id[]='.$key;
		}

		$defaultDate = explode('-', $defaultYmd = date('Y-m-d', strtotime('+7 days')));
		$first_area_id = current(array_keys($areaList));
		$search['params'] = array(
			'place'			=> '1',
			'area_id'		=> $first_area_id,
			'prefecture'	=> $prefectureId,
			'year'			=> $defaultDate[0],
			'month'			=> $defaultDate[1],
			'day'			=> $defaultDate[2],
			'time'			=> '11-00',
			'return_way'	=> '0',
			'return_year'	=> $defaultDate[0],
			'return_month'	=> $defaultDate[1],
			'return_day'	=> $defaultDate[2],
			'return_time'	=> '17-00',
		);
		// 検索フォーム設定
		$this->OptionsManage->setSearchOptions($search['params']);

		$this->set(compact('linkDateArr', 'areaListSearchString', 'search'));

		// 都道府県のWP投稿を取得する
		$prefectureData = $this->RcPostmeta->getPrefecturePostmetaData($prefectureId, $prefLinkCd);
		$this->set(compact('prefectureData'));

		//// 都道府県関係 ////
		$prefectureList = $this->Prefecture->getAllLinkCdAndRegionLinkCd();

		$prefectureName = $prefectureList[$prefectureId]['Prefecture']['name'];

		$neighborhoodPrefectureList = $prefectureListGroupByRegion = array();
		if (!empty($prefectureList)) {
			// 近隣の都道府県
			$neighborhoodPrefectureList = $this->getNeighborhood($prefectureId, $prefectureList);
			// 地方別都道府県
			$prefectureListGroupByRegion = Hash::combine($prefectureList, '{n}.Prefecture.link_cd', '{n}.Prefecture.name', '{n}.Prefecture.region_link_cd');
		}

		unset($prefectureList);
		$this->set(compact('prefectureName', 'neighborhoodPrefectureList', 'prefectureListGroupByRegion', 'regionLinkCd', 'regionName'));

		//// 空港関係 ////

		// 空港リスト
		$airportLinkCdList = $this->Landmark->getAirportLinkCdListByPrefectureId($prefectureId);

		$this->set('airportLinkCdList', $airportLinkCdList);

		//// 料金関係 ////
		// 車両タイプ別最安値（人気空港(港)・駅別）
		$params = $search['params'];
		unset($params['area_id'], $params['prefecture']);
		// SPでの日付はdate,return_dateのためパラメータに追加する
		$rankingDate = array(
			'date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
			'return_date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
		);
		$params = array_merge($params, $rankingDate);

		$landmarkRanking = $this->PrefectureContents->getPopularLandmarkRanking($areaIds, $params);
		$typeCapacityList = $this->PrefectureContents->getCarTypeCapacityList();

		$now = strtotime('now');
		$next = strtotime('+1 month');
		$currentMonth = date('n', $now);
		$nextMonth = date('n', $next);

		// 当月の価格情報を取得
		$priceCurrentMonth = $this->PrefectureContents->getPriceByPrefectureArea($areaIds, date('Y-m-01', $now), date('Y-m-t', $now));
		// 来月の価格情報を取得
		$priceNextMonth = $this->PrefectureContents->getPriceByPrefectureArea($areaIds, date('Y-m-01', $next), date('Y-m-t', $next));

		// エリア別最安値を集計
		$bestPriceAreas = $this->PrefectureContents->getBestPricesForAreas($priceCurrentMonth, $priceNextMonth, $prefectureId, $areaList, $clientList, $params);

		// 会社別基本料金の最安値
		$clientBestPriceAndRank = $this->getPriceAndRankForClients($priceCurrentMonth);

		$this->set(compact('landmarkRanking', 'typeCapacityList', 'bestPriceAreas', 'currentMonth', 'nextMonth', 'clientBestPriceAndRank'));

		//// 会社関係 ////

		// 会社別、Yotpoレビューのレーティングと数を取得
		$clientRatings = $this->YotpoReview->getRatingsGroupByClientId();

		// レビューのある会社のリスト（3社まで）
		$pickupClientList = array();
		foreach ($clientRatings as $clientId => $rating) {
			if (count($pickupClientList) < 3 && isset($clientList[$clientId])) {
				$pickupClientList[] = $clientId;
			}
			// レーティングは小数点以下第一位まで
			$clientRatings[$clientId]['rating'] = number_format($rating['rating'], 1, '.', '');
		}

		$officeListForPickupClients = $carModelsForPickupClients = $clientPlanImages = $clientContents = array();

		// レビューのある会社について
		if (!empty($pickupClientList)) {
			// 店舗
			$officeListForPickupClients = $this->Office->getOfficeListGroupByClientId($pickupClientList, $prefectureId);

			// 車種
			$carModels = $this->CarModel->getListByClientAndPrefectureId($pickupClientList, $prefectureId);
			$carModelsCombined = Hash::combine($carModels, '{n}.CarModel.id', '{n}', '{n}.Client.id');
			unset($carModels);

			foreach ($carModelsCombined as $clientId => $models) {
				// 会社別の車種数
				// 表示するときは10の倍数で（5以下は5）
				$count = count($models);
				$carModelCount[$clientId] = ($count > 5) ? intval(round($count, -1)) : 5;
				// 会社別車両タイプ別の車種
				if (!isset($carModelsForPickupClients[$clientId])) {
					$carModelsForPickupClients[$clientId] = array('types' => array());
				}
				foreach ($models as $v) {
					if (!isset($carModelsForPickupClients[$clientId]['types'][$v['CarType']['id']])) {
						$carModelsForPickupClients[$clientId]['types'][$v['CarType']['id']] = $v['CarType'];
						$carModelsForPickupClients[$clientId]['types'][$v['CarType']['id']]['models'] = array();
					}
					$carModelsForPickupClients[$clientId]['types'][$v['CarType']['id']]['models'][] = $v['Automaker']['name'] . $v['CarModel']['name'];
				}
			}
			unset($carModelsCombined);

			foreach ($pickupClientList as $clientId) {
				// 会社プランの画像
				$clientPlanImages[$clientId] = "/rentacar/img/noimage.png";
				$image = $this->Commodity->getFirstImageByClientId($clientId);
				if (!empty($image)) {
					$clientPlanImages[$clientId] = "/rentacar/img/commodity_reference/{$clientId}/{$image['Commodity']['image_relative_url']}";
				}
				
				// 最寄り空港のリスト
				$nearestAirportList = $this->Office->getNearestAirportListByClientAndPrefectureId($clientId, $prefectureId);
				// 最寄り駅のリスト
				$nearestStationList = $this->Office->getNearestStationListByClientAndPrefectureId($clientId, $prefectureId);
				// 装備
				$equipmentList = $this->Equipment->getEquipmentListByClientAndPrefectureId($clientId, $prefectureId);
				// オプション
				$optionList = $this->Privilege->getOptionListByClientAndPrefectureId($clientId, $prefectureId);
				// 乗捨て可能な店舗
				$dropOffOfficeList =  $this->Office->getDropOffOfficeByClientAndPrefectureId($clientId, $prefectureId);
				// 会社コンテンツ
				$clientContents[$clientId] = $this->getClientContents($prefectureName, $clientList[$clientId]['name'], $clientList[$clientId]['area_type'],
						$nearestAirportList, $nearestStationList, $equipmentList, $optionList,
						count($officeListForPickupClients[$clientId]),
						!empty($carModelCount[$clientId]) ? $carModelCount[$clientId] : 0,
						!empty($carModelsForPickupClients[$clientId]) ? $carModelsForPickupClients[$clientId] : array(),
						!empty($clientBestPriceAndRank[$clientId]) ? $clientBestPriceAndRank[$clientId] : array(),
						$dropOffOfficeList
				);
			}

			// 店舗数MAX 5
			foreach ($officeListForPickupClients as $clientId => $offices) {
				if (count($offices) > 5) {
					$officeListForPickupClients[$clientId] = array_slice($offices, 0, 5);
				}
			}
		}
		unset($prices);

		$this->set(compact('clientList', 'clientRatings', 'pickupClientList', 'officeListForPickupClients', 'clientPlanImages', 'clientContents'));

		//// レビューカルーセル ////

		// 都道府県の店舗に寄せられたYotpoレビュー
		$reviews = $this->YotpoReview->getReviewsByPrefectureId($prefectureId);
		$reviewCount = $this->YotpoReview->getReviewCountByPrefectureId($prefectureId);
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
		$this->set(compact('reviewCount', 'yotpoReviews', 'yotpoReviewOnlyScore', 'yotpoReviewsByClient', 'yotpoReviewLimit'));

		//// 駅関係 ////
		$stationList = $this->Station->getStationListWithAreaByPrefectureId($prefectureId);

		$stationTypes = Constant::stationTypes();
		$stationListGroupByArea = $majorStationList = $mapStationList = array();
		if (!empty($stationList)) {
			// エリア別駅リスト
			$stationListGroupByArea['areas'] = array();
			foreach ($stationList as $k => $v) {
				if (!isset($stationListGroupByArea['areas'][$v['Area']['id']])) {
					$stationListGroupByArea['areas'][$v['Area']['id']] = $v['Area'];
					$stationListGroupByArea['areas'][$v['Area']['id']]['stations'] = array();
				}
				$stationListGroupByArea['areas'][$v['Area']['id']]['stations'][$v['Station']['id']] = $v['Station'];
				if (isset($stationTypes[$v['Station']['type']])) {
					$type = $stationTypes[$v['Station']['type']];
				} else {
					$type = '駅';
				}
				$stationListGroupByArea['areas'][$v['Area']['id']]['stations'][$v['Station']['id']]['type'] = $type;
				$stationList[$k]['Station']['type'] = $type;
			}
			// 主要駅リスト
			$majorStationList = Hash::extract($stationList, '{n}.Station[major_flg=1]');
			// 地図表示駅リスト
			$mapStationList = Hash::extract($stationList, '{n}.Station[pref_map_flg=1]');
		}

		unset($stationList);
		$this->set(compact('stationListGroupByArea', 'majorStationList', 'mapStationList'));

		//// 市区町村関係 ////
		$cityList = $this->City->getCityListWithAreaByPrefectureId($prefectureId);

		$cityListGroupByArea = array();
		if (!empty($cityList)) {
			// エリア別市区町村リスト
			$cityListGroupByArea['areas'] = array();
			foreach ($cityList as $v) {
				if (!isset($cityListGroupByArea['areas'][$v['Area']['id']])) {
					$cityListGroupByArea['areas'][$v['Area']['id']] = $v['Area'];
					$cityListGroupByArea['areas'][$v['Area']['id']]['cities'] = array();
				}
				$cityListGroupByArea['areas'][$v['Area']['id']]['cities'][$v['City']['id']] = $v['City'];
			}
		}

		unset($cityList);
		$this->set(compact('cityListGroupByArea'));

		// meta系
		// 今後2ヶ月間エリアごとの価格から、都道府県の最安値を計算 (メタタグで使用する)
		array_walk_recursive($bestPriceAreas, function ($value, $key) use (&$new_arr) {
			if ($key == 'best_price' && !empty($value)) {
				$new_arr[] = preg_replace(['/&yen;/','/,/'],'',$value);
			}
		});
		$lowest_pref_price = '&yen;'.min($new_arr).'〜';

		if ($prefectureName == '北海道') {
			$title_prefecture = $prefectureName;
		} else {
			$title_prefecture = mb_substr($prefectureName, 0, -1, "utf-8");
		}
		$this->set('title_for_layout', $title_prefecture.'の格安レンタカー比較・予約（乗り捨て可）｜スカイチケット');
		$this->set('description_for_layout', $title_prefecture.'の格安レンタカー '.$lowest_pref_price.' 簡単に比較・予約！ 乗り捨てや当日予約、24時間営業の店舗も検索可能。トヨタレンタカー、ニッポンレンタカー、オリックスレンタカーなどおすすめのレンタカー会社のプランやキャンペーンが満載。安い料金でレンタカーを予約するならスカイチケット！');
		$this->set('keywords', $prefectureName.','.$title_prefecture.',レンタカー,格安,比較,予約,乗り捨て,スカイチケット');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setPrefectures($this->action, $regionName, $regionLinkCd, $prefectureName, $prefLinkCd);
		$this->set('progress_arr', $progressArr);
		}

	public function sp_index() {
		$this->index();
	}

	private function getPriceAndRankForClients($prices) {
		$clientPrices = Hash::combine($prices, '{n}.commodityItemId', '{n}', '{n}.clientId');
		$clientBestPrices = array();
		foreach ($clientPrices as $k => $v) {
			$clientBestPrices[$k] = array(
				'clientId' => $k,
				'bestPrice' => PHP_INT_MAX
			);
			foreach ($v as $r) {
				if ($clientBestPrices[$k]['bestPrice'] > $r['basePrice']) {
					$clientBestPrices[$k]['bestPrice'] = $r['basePrice'];
				}
			}
		}
		unset($clientPrices);
		$clientRank = Hash::sort($clientBestPrices, '{n}.bestPrice', 'asc');
		unset($clientBestPrices);
		$clientBestPriceAndRank = array();
		foreach ($clientRank as $k => $v) {
			$clientBestPriceAndRank[$v['clientId']] = array('rank' => $k + 1, 'bestPrice' => $v['bestPrice']);
		}
		unset($clientRank);
		return $clientBestPriceAndRank;
	}

	private function getClientContents($prefectureName, $clientName, $clientArea, $airportList, $stationList, $equipmentList, $optionList,
			$officeCount, $carModelCount, $carModels, $priceRank, $dropOffOfficeList) {
		// 会社名から空白を除く
		$clientName = preg_replace('/ /', '', $clientName);

		// コンテンツ編集
		$contents = $prefectureName . 'には、';
		$landmarks = array();
		if (!empty($airportList[0])) {
			$landmarks[] = $airportList[0];
		}
		if (!empty($stationList)) {
			$stationTypes = Constant::stationTypes();
			foreach ($stationList as $station) {
				if (count($landmarks) < 2) {
					$landmarks[] = $station['name'] . $stationTypes[$station['type']];
				} else {
					break;
				}
			}
		}
		if (!empty($landmarks)) {
			$landmarkCount = count($landmarks);
			for ($i = 0; $i < $landmarkCount; $i++) {
				$contents .= $landmarks[$i];
				if ($i < $landmarkCount - 1) {
					$contents .= 'や';
				}
			}
			$contents .= 'の周辺';
			if ($officeCount > 2) {
				$contents .= 'を中心';
			}
			$contents .= 'に';
		}
		$contents .= $clientName . 'の店舗が'. $officeCount . '店舗あります。';

		$feature = '';
		if ($clientArea == 1) {
			$feature = '全国展開で豊富なレンタカー車種やサービスが安心の';
		} else if ($clientArea == 2) {
			$feature = '地域限定で展開する、価格や車種が特徴的な';
		}
		if (!empty($feature)) {
			$contents .= $clientName . 'といえば、' . $feature . 'レンタカーブランドです。';
		}

		$contents .= $clientName . 'のスカイチケット限定プランでは、';
		if (!empty($equipmentList) || !empty($optionList)) {
			if (!empty($equipmentList)) {
				$i = 0;
				$loopMax = (count($equipmentList) > 2) ? 2 : count($equipmentList);
				foreach ($equipmentList as $equipment) {
					$contents .= $equipment;
					if (++$i < $loopMax) {
						$contents .= 'や';
					} else {
						break;
					}
				}
				$contents .= 'などの標準装備車';
				if (!empty($optionList)) {
					$contents .= 'や、';
				}
			}
			if (!empty($optionList)) {
				$contents .= current($optionList) . 'の追加希望可能';
			}
			$contents .= 'など、';
		}
		$contents .= 'お好みのプランをご予約いただけます。';

		if (!empty($carModels)) {
			$contents .= 'レンタル可能な車種は約' . $carModelCount . '種類。';

			$modelAdded = false;
			// 軽自動車、コンパクト、ワゴン
			$carTypes = array(1, 2, 5);
			foreach ($carTypes as $type) {
				if (!empty($carModels['types'][$type])) {
					$cars = $carModels['types'][$type];
					$contents .= $cars['name'] . 'は';

					$modelCount = count($cars['models']);
					for ($i = 0; $i < $modelCount; $i++) {
						$contents .= $cars['models'][$i];
						if ($i >= 1 || $i + 1 >= $modelCount) {
							break;
						} else {
							$contents .= 'や';
						}
					}
					$contents .= '、';
					$modelAdded = true;
				}
			}
			if ($modelAdded) {
				$contents = preg_replace('/[、]+$/u', '', $contents);
				$contents .= '等の車種がレンタル可能です。';
			}
		}

		if (!empty($priceRank)) {
			$contents .= date('n') . '月のレンタル基本料金は' . number_format($priceRank['bestPrice']) . '円～と' . $prefectureName . 'のレンタカー会社の中で' . $priceRank['rank'] . '番目に安い料金になります。';
		}

		if (!empty($dropOffOfficeList)) {
			$i = 0;
			$officeCount = count($dropOffOfficeList);
			foreach ($dropOffOfficeList as $office) {
				$contents .= $clientName . $office . '店';
				if (++$i < $officeCount) {
					$contents .= 'や';
				}
			}
			$contents .= 'などの店舗への乗り捨ても可能です。';
		}

		return $contents;
	}

	private function getNeighborhood($prefectureId, $prefectureList) {
		$neighbors  = $this->neighbors[$prefectureId];
		$result = array();
		if (!empty($neighbors)) {
			$matcher = '(^'. implode('$|^', $neighbors) .'$)';
			$result = Hash::extract($prefectureList, "{n}.Prefecture[id=/$matcher/]");
		}
		return $result;
	}

}

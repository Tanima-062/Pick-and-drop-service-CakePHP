<?php
App::uses('AppController', 'Controller');

class LocalstoredetailController extends AppController {

	public $uses = array('Client', 'Office', 'ClientCard', 'Area', 'Prefecture', 'Landmark', 'CarType', 'Equipment', 'RcPostmeta','YotpoReview', 'OfficeStation');
	public $use_searchbox = true;
	public $new_js = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $components = array('BreadCrumb');

	public function beforeFilter() {
		parent::beforeFilter();

		foreach ((array)$this->uses as $model) {
			$this->$model->setDataSource('default_slave');
		}

		$clientList = $this->Client->getClientList();
		if (!empty($clientList)) {
			$clientList = Hash::combine($clientList, '{n}.Client.id', '{n}.Client');
		}
		$this->set('clientList', $clientList);
	}

	// 正しいURLに301転送
	public function moved_url() {
		$params = $this->request->params;

		if (empty($params['client_link_cd']) || empty($params['office_link_cd'])) {
			throw new NotFoundException();
		}

		$this->redirect('/company/' . $params['client_link_cd'] . DS . $params['office_link_cd'] . DS, 301);
	}

	// 正しいURLに301転送
	public function moved_url2() {
		$params = $this->request->query;

		if (empty($params['store_id']) || !is_numeric($params['store_id'])) {
			throw new NotFoundException();
		}

		$link_cd_arr = $this->Office->getLinkCdById($params['store_id']);

		if (empty($link_cd_arr)) {
			throw new NotFoundException();
		}

		$this->redirect('/company/' . $link_cd_arr['Client']['url'] .  DS . $link_cd_arr['Office']['url'] . DS, 301);
	}

	public function index() {
		$this->loadComponent('OfficeUtil');
		$params = $this->request->params;

		if (empty($params['client_link_cd']) || empty($params['office_link_cd'])) {
			throw new NotFoundException();
		}
		$office_arr = $this->Office->getOfficeWithClientByLinkCd($params['client_link_cd'], $params['office_link_cd']);

		if (empty($office_arr)) {
			throw new NotFoundException();
		}

		$this->set('regions', Constant::regions());

		$this->request->query['store_id'] = $office_arr['Office']['id'];

		// 変数初期宣言
		$officeInfo = $office_arr['Office'];
		$clientInfo = $office_arr['Client'];
		$officeName = $officeInfo['name'];
		$clientName = $clientInfo['name'];
		$officeNearList = array();
		$clientCardInfo = array();

		// 初期都道府県は北海道
		$prefectureId = 1;
		// 初期札幌
		$areaId = 1;
		// 初期は高輪台営業所2(札幌にある営業所)
		$officeId = $officeInfo['id'];
		$clientId = $clientInfo['id'];

		$prefectureList = $this->Prefecture->getAllLinkCdAndRegionLinkCd();
		// 地方別都道府県
		$prefectureListGroupByRegion = Hash::combine($prefectureList, '{n}.Prefecture.link_cd', '{n}.Prefecture.name', '{n}.Prefecture.region_link_cd');
		$this->set('prefectureListGroupByRegion', $prefectureListGroupByRegion);

		// store_idが入っていれば各種情報を取得する
		if (isset($this->request->query['store_id'])) {
			// エリアIDの取得
			if (isset($officeInfo['area_id'])) {
				$areaId = $officeInfo['area_id'];
			}

			// クレジットカード情報の取得
			$clientCardInfo = !empty($clientInfo['accept_card']) ? $this->ClientCard->getCardByClientId($clientId) : array();
			// 同じエリアの営業所一覧を出す
			$officeNearList = $this->Office->getOfficeNearListAddUrlData($areaId, $officeId);
			// 都道府県の取得
			$prefectureData = $this->Area->getPrefectureIdByAreaId($areaId);

			if (isset($prefectureData['Area']['prefecture_id'])) {
				$prefectureId = $prefectureData['Area']['prefecture_id'];
				$client_pref = $prefectureList[$prefectureId]['Prefecture']['name'];
			}

			$stationId = $this->OfficeStation->getNearestStation($officeId);

			$defaultDate = explode('-', $defaultYmd = date('Y-m-d', strtotime('+7 days')));

			// 検索フォームに初期値をセットする
			$searchOption = array(
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
			if (!empty($officeInfo['airport_id'])) {
				$searchOption['place'] = 3;
				$searchOption['airport_id'] = $officeInfo['airport_id'];
			} elseif (!empty($stationId)) {
				$searchOption['place'] = 4;
				$searchOption['prefecture'] = $prefectureId;
				$searchOption['station_id'] = $stationId;
			} else {
				$searchOption['place'] = 1;
				$searchOption['prefecture'] = $prefectureId;
				$searchOption['area_id'] = $areaId;
			}
			$this->OptionsManage->setSearchOptions($searchOption);
		}
		unset($prefectureList);

		// リンク用日付配列
		$link_date_arr = date_parse(date('Ymd',strtotime('+7 day')));		// 7日後の日付
		$link_date_arr2 = date_parse(date('Ymd',strtotime('+11 day')));		// 11日後の日付
		$this->set(compact('link_date_arr', 'link_date_arr2'));

		// 店舗の特徴を取得する
		$officeCharacterContents = $this->RcPostmeta->getShopPostmetaData($officeId);
		$officeCharacterDataArray = array();

		foreach ($officeCharacterContents as $key => $val) {
			if (substr($val['RcPostmeta']['meta_key'], 0, 13) == 'shop-contents' && substr($val['RcPostmeta']['meta_key'], -11) == 'shop-head-s') {
				// mata_keyの数値を取得する
				$metaKeyNum = $this->cutBeforeText($val['RcPostmeta']['meta_key'], 14);
				$metaKeyNum = $this->cutAfterText($metaKeyNum, 12);
				$officeCharacterDataArray[$metaKeyNum]['head'] = $val['RcPostmeta']['meta_value'];
			}
			if (substr($val['RcPostmeta']['meta_key'], 0, 13) == 'shop-contents' && substr($val['RcPostmeta']['meta_key'], -9) == 'shop-text') {
				// mata_keyの数値を取得する
				$metaKeyNum = $this->cutBeforeText($val['RcPostmeta']['meta_key'], 14);
				$metaKeyNum = $this->cutAfterText($metaKeyNum, 10);
				$officeCharacterDataArray[$metaKeyNum]['text'] = $val['RcPostmeta']['meta_value'];
			}
			if (substr($val['RcPostmeta']['meta_key'], 0, 13) == 'shop-contents' && substr($val['RcPostmeta']['meta_key'], -8) == 'shop-img') {
				// mata_keyの数値を取得する
				$metaKeyNum = $this->cutBeforeText($val['RcPostmeta']['meta_key'], 14);
				$metaKeyNum = $this->cutAfterText($metaKeyNum, 9);
				$officeCharacterDataArray[$metaKeyNum]['img'] = $val['RcPostmeta']['meta_value'];
			}
		}

		// 店舗に寄せられたYotpoレビュー
		$reviews = $this->YotpoReview->getReviewsByOfficeId($officeId);
		$reviewCount = $this->YotpoReview->getReviewsCountByOfficeId($officeId);
		$reviewAvg = $this->YotpoReview->getReviewsAvgByOfficeId($officeId);
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
		$this->set(compact('reviewCount', 'reviewAvg', 'yotpoReviewOnlyScore', 'yotpoReviews', 'yotpoReviewsByClient', 'yotpoReviewLimit'));

		$this->set('place', $searchOption['place']);
		$this->set('prefectureId', $prefectureId);
		$this->set('areaId', $areaId);
		$this->set('clientId', $clientId);
		$this->set('client_pref', $client_pref);
		$officeInfo['businessHours'] = $this->OfficeUtil->formatOfficeBusinessHours($officeInfo);
		$this->set('officeInfo', $officeInfo);
		$this->set('clientInfo', $clientInfo);
		$this->set('clientCardInfo', $clientCardInfo);
		$this->set('officeNearList', $officeNearList);
		$this->set('officeCharacterDataArray', $officeCharacterDataArray);

		$this->set('title_for_layout', $clientName.' '.$officeName.'（'.$client_pref.'）の予約・プラン比較');
		$this->set('description_for_layout', $clientName.' '.$officeName.'（'.$client_pref.'）のレンタカーを今すぐ予約！気になる口コミや営業時間、交通アクセスと送迎情報を掲載。'.$clientName.' '.$officeName.'（'.$client_pref.'）周辺のレンタカー営業所から比較も可能。');
		$this->set('keywords', $clientName . ',' . $officeName . ",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");

		// パンくずリスト設定
		if (!empty($clientInfo['url'])) {
			$parentUrl = '/rentacar/company/'.$clientInfo['url'].'/';
		} else {
			$parentUrl = '/rentacar/company?company_id='.$clientInfo['id'].'/';
		}

		$progressArr = $this->BreadCrumb->setLocalstoredetail($this->action, $parentUrl, $officeName, $clientName, $params['office_link_cd']);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {
		$this->index();
	}

	public function reviews() {

		$params = $this->request->params;

		if (empty($params['client_link_cd']) || empty($params['office_link_cd'])) {
			throw new NotFoundException();
		}
		$office_arr = $this->Office->getOfficeWithClientByLinkCd($params['client_link_cd'], $params['office_link_cd']);

		if (empty($office_arr)) {
			throw new NotFoundException();
		}

		$this->request->query['store_id'] = $office_arr['Office']['id'];

		// 変数初期宣言
		$officeInfo = $office_arr['Office'];
		$clientInfo = $office_arr['Client'];
		$officeName = $officeInfo['name'];
		$clientName = $clientInfo['name'];

		// 初期都道府県は北海道
		$prefectureId = 1;
		// 初期札幌
		$areaId = 1;
		// 初期は高輪台営業所2(札幌にある営業所)
		$officeId = $officeInfo['id'];
		$clientId = $clientInfo['id'];

		// store_idが入っていれば各種情報を取得する
		if (isset($this->request->query['store_id'])) {
			// エリアIDの取得
			if (isset($officeInfo['area_id'])) {
				$areaId = $officeInfo['area_id'];
			}

			// 都道府県の取得
			$prefectureData = $this->Area->getPrefectureIdByAreaId($areaId);

			if (isset($prefectureData['Area']['prefecture_id'])) {
				$prefectureId = $prefectureData['Area']['prefecture_id'];
			}

			$stationId = $this->OfficeStation->getNearestStation($officeId);

			$defaultDate = explode('-', $defaultYmd = date('Y-m-d', strtotime('+7 days')));

			// 検索フォームに初期値をセットする
			$searchOption = array(
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
			if (!empty($officeInfo['airport_id'])) {
				$searchOption['place'] = 3;
				$searchOption['airport_id'] = $officeInfo['airport_id'];
			} elseif (!empty($stationId)) {
				$searchOption['place'] = 4;
				$searchOption['prefecture'] = $prefectureId;
				$searchOption['station_id'] = $stationId;
			} else {
				$searchOption['place'] = 1;
				$searchOption['prefecture'] = $prefectureId;
				$searchOption['area_id'] = $areaId;
			}
			$this->OptionsManage->setSearchOptions($searchOption);
		}

		// 店舗に寄せられたYotpoレビュー
		$this->YotpoReview->setLimit(100000);
		$reviews = $this->YotpoReview->getReviewsByOfficeId($officeId);
		$reviewCount = $this->YotpoReview->getReviewsCountByOfficeId($officeId);
		$reviewAvg = $this->YotpoReview->getReviewsAvgByOfficeId($officeId);
		$yotpoReviews = array();
		$yotpoReviewsByClient = array();
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
			$yotpoReviews[] = $yotpoReview;
			$yotpoReviewsByClient[$review['Client']['id']][] = $yotpoReview;
		}
		unset($reviews);
		$this->set(compact('reviewCount', 'reviewAvg', 'yotpoReviews', 'yotpoReviewsByClient'));

		$this->set('officeInfo', $officeInfo);
		$this->set('clientInfo', $clientInfo);
		$this->set('title_for_layout',$clientName.' '.$officeInfo['name'].' の評判・口コミ');

		$this->set('clientId', $clientId);

		// パンくずリスト設定
		if (!empty($clientInfo['url'])) {
			$parentUrl = '/rentacar/company/'.$clientInfo['url'].'/';
		} else {
			$parentUrl = '/rentacar/company?company_id='.$clientInfo['id'].'/';
		}
		$progressArr = $this->BreadCrumb->setLocalstoredetail($this->action, $parentUrl, $officeName, $clientName, $params['office_link_cd']);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_reviews() {
		$this->reviews();
	}

	public function cutBeforeText($text, $num){
		$replace = substr( $text , $num , strlen($text)-$num );
		return $replace;
	}

	public function cutAfterText($text, $num){
		$replace = substr( $text , 0 , strlen($text)-$num );
		return $replace;
	}
}

<?php

App::uses('AppController', 'Controller');

class RegionController extends AppController {

	public $uses = array('Commodity','Prefecture','Landmark','Area','CarType','Equipment','RcPostmeta','Office','YotpoReview');
	public $use_searchbox = true;
	public $new_js = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $helpers = array('CreateUrl');
	public $components = array('BreadCrumb');

	// 正しいURLに301転送
	public function moved_url() {
		$params = $this->request->params;
		if (empty($params['region_link_cd'])) {
			throw new NotFoundException();
		}

		$qs = !empty($this->request->query) ? '?' . http_build_query($this->request->query) : '';
		$this->redirect(DS . str_replace('area_', '', $params['region_link_cd']) . DS . $qs, 301);
	}
	
	public function index() {
		$params = $this->request->params;
		
		if (empty($params['region_link_cd'])) {
			throw new NotFoundException();
		}

		$regionLinkCd = $params['region_link_cd'];
		$region_link_cd = str_replace('area_', '', $regionLinkCd);
		$base_url = ($region_link_cd === 'hokkaido' || $region_link_cd === 'okinawa') ? '' : $region_link_cd . DS;
	
		// 地方の投稿を取得
		$regionContents = $this->RcPostmeta->getRegionPostmetaDataByRegionLinkCd($regionLinkCd);

		$_prefectureList = !empty($this->request->params['region_arr']) ? $this->request->params['region_arr'] : array();

		// 都道府県の一覧を取得
		$prefectureList = array();
		foreach ($_prefectureList as $k => $v) {
			$prefectureList[$k] = current($v);
		}

		// 都道府県のlinkcdを取得
		$prefectureLinkCd = array();
		foreach ($_prefectureList as $k => $v) {
			$prefectureLinkCd[$k] = key($v);
		}
		// メモリ確保
		unset($_prefectureList);

		// 地方の一覧を取得
		$_regionList = $this->RcPostmeta->getRegionPostData(true);
		$regionList = array();
		// 北海道は都道府県ページにする
		foreach ($_regionList as $k => $v) {
			$regionList += ($k === 'area_hokkaido') ? array('hokkaido'=> $v) : array($k => $v);
		}
		// メモリ確保
		unset($_regionList);

		// エリアと空港のリンクコードを取得
		$_areaList = $this->_getAreaLinkCdByPrefectureIds(array_keys($prefectureList));
		$_airportList = $this->_getAllAirportListByAirportCd();

		$areaLinkCdList = array();
		$airportLinkCdList = array();

		foreach($regionContents['pref_details'] as $key => $val){
			foreach($val['rows'] as $k => $v){
				if(isset($v['city'])){
					$city = $v['city'];
					if (isset($_areaList[$city])) {
						$areaLinkCdList[$city] = $_areaList[$city];
					} else {
						// マスタに存在しない都市の記事は消す
						unset($regionContents['pref_details'][$key]['rows'][$k]);
					}
				}
				if(isset($v['airport'])){
					$airport = $v['airport'];
					if (isset($_airportList[$airport])) {
						$airportLinkCdList += $_airportList[$airport];
						// airline_cdをlink_cdに変換
						$regionContents['pref_details'][$key]['rows'][$k]['airport'] = key($_airportList[$airport]);
					} else {
						// マスタに存在しない空港の記事は消す
						unset($regionContents['pref_details'][$key]['rows'][$k]);
					}
				}
			}
		}
		
		foreach($regionContents['pref_details'] as $key => $val){
			if($val['img']){
				$params = array(
					'conditions' => array(
						"RcPostmeta.post_id"=>$val['img'],
						"RcPostmeta.meta_key"=>"_wp_attached_file"
					)
				);
				$imgPathData = $this->RcPostmeta->find('first',$params);
				$regionContents['pref_details'][$key]['img'] = $imgPathData["RcPostmeta"]["meta_value"];
			}
			foreach($val['rows'] as $key2 => $val2){
				if($val2['img']){
					$params = array(
						'conditions' => array(
							"RcPostmeta.post_id"=>$val2['img'],
							"RcPostmeta.meta_key"=>"_wp_attached_file"
						)
					);
					$imgPathData = $this->RcPostmeta->find('first',$params);
					$regionContents['pref_details'][$key]['rows'][$key2]['img'] = $imgPathData["RcPostmeta"]["meta_value"];
				}
			}
		}
		
		// メモリ確保
		unset($_areaList);
		unset($_airportList);

		// ランドマークの一覧を取得
		$_landmarkAiportList = $this->_getAirportListByPrefectureIds(array_keys($prefectureList));
		$_landmarkAreaList = $this->_getAreaListByPrefectureIds(array_keys($prefectureList));

		foreach($prefectureList as $key => $val) {
			$landmarkList[$val]['prefecture_id'] = $key;
			$landmarkList[$val]['airport'] = isset($_landmarkAiportList[$key]) ? $_landmarkAiportList[$key] : array();
			$landmarkList[$val]['area'] = isset($_landmarkAreaList[$key]) ? $_landmarkAreaList[$key] : array();
		}
		// メモリ確保
		unset($_landmarkAiportList);
		unset($_landmarkAreaList);

		//// レビュー ////

		// 地方の店舗に寄せられたYotpoレビュー
		$reviews = $this->YotpoReview->getReviewsByRegionLinkCd($regionLinkCd);
		$reviewCount = $this->YotpoReview->getReviewCountByRegionLinkCd($regionLinkCd);
		$yotpoReviews = array();

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
		}

		unset($reviews);
		$this->set(compact('reviewCount', 'yotpoReviews', 'yotpoReviewOnlyScore', 'yotpoReviewLimit'));

		// デフォルト都道府県
		$defaultPrefectureId = Constant::regionRepresentative($region_link_cd);
		// デフォルトエリア
		$areaInfo = $this->Area->getAreaInfoByPrefectureId($defaultPrefectureId);
		if (!empty($areaInfo)) {
			$areaList = Hash::combine($areaInfo, '{n}.Area.id', '{n}.Area');
			$defaultAreaId = current(array_keys($areaList));
		}
		unset($areaInfo, $areaList);

		$defaultDate = explode('-',$defaultYmd = date('Y-m-d',strtotime('+7 days')));

		$search['params'] = array(
			'place'			=> '1',
			'prefecture'	=> Constant::regionRepresentative($region_link_cd),
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
		if (isset($defaultAreaId)) {
			$search['params']['area_id'] = $defaultAreaId;
		}
		// 検索フォーム設定
		$this->OptionsManage->setSearchOptions($search['params']);
		
		$this->set('regionContents', $regionContents);
		$this->set('prefectureList', $prefectureList);
		$this->set('prefectureLinkCd', $prefectureLinkCd);
		$this->set('regionList', $regionList);
		$this->set('landmarkList', $landmarkList);
		$this->set('base_url', $base_url);
		
		$this->set('areaLinkCdList', $areaLinkCdList);
		$this->set('airportLinkCdList', $airportLinkCdList);
		$this->set('title_for_layout',$regionContents['region-name']."の格安レンタカー比較・予約（乗り捨て可）｜スカイチケット");
		$this->set('description_for_layout',$regionContents['region-name']."の格安レンタカー簡単に比較・予約！乗り捨てや当日予約、24時間営業の店舗も検索可能。トヨタレンタカー、ニッポンレンタカー、オリックスレンタカーなどおすすめのレンタカー会社のプランやキャンペーンが満載。安い料金でレンタカーを予約するならスカイチケット！");
		$this->set('keywords',$regionContents['region-name'].",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setRegion($this->action, $regionContents['region-name'], $region_link_cd);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {
		
		$this->index();
		
	}
	
	//-----------------------------------------------------
	// 複数のprefecture_idに紐づいたリンクコードを取得
	//-----------------------------------------------------
	private function _getAreaLinkCdByPrefectureIds($prefectureIds) {
		$options = array(
			'fields' => array(
				'Area.area_link_cd',
				'Area.name',
			),
			'conditions' => array(
				'Area.prefecture_id' => $prefectureIds,
			),
		);
		
		return $this->Area->find('list', $options);
	}
	
	//-----------------------------------------------------
	// 複数のprefecture_idに紐づいたエリアを取得
	//-----------------------------------------------------
	private function _getAreaListByPrefectureIds($prefectureIds) {
		$options = array(
			'fields' => array('id', 'name', 'prefecture_id'),
			'conditions' => array(
				'prefecture_id' => $prefectureIds,
				'delete_flg' => 0,
			),
			'recursive'=>-1,
		);

		return $this->Area->find('list', $options);
	}
	
	 //-----------------------------------------------------
	// 全airport_cdに紐づいた空港の情報を取得
	//------------------------------------------------------
	private function _getAllAirportListByAirportCd() {
		$sql = 'SELECT'
			 . ' l.iata_cd,'
			 . ' l.link_cd,'
			 . ' l.name'
			 . ' FROM'
			 . ' rentacar.landmarks AS l'
			 . ' WHERE l.delete_flg = 0'
			 . ' AND l.landmark_category_id = 1'
			 . ' ORDER BY l.sort, l.id';

		$result = $this->Landmark->query($sql);

		if(!empty($result[0])){
			$ret = array();
			
			foreach ($result as $k => $v) {
				$ret[$v['l']['iata_cd']] = array(
					$v['l']['link_cd'] => $v['l']['name'],
				);
			}
			
			return $ret;
		}

		return false;
	}
	
	 //-----------------------------------------------------
	// 複数のprefecture_idに紐づいた空港を取得
	//------------------------------------------------------
	private function _getAirportListByPrefectureIds($prefectureIds) {
		$options = array(
			'fields' => array('id', 'name', 'prefecture_id'),
			'conditions' => array(
				'prefecture_id' => $prefectureIds,
				'landmark_category_id' => 1,
				'delete_flg' => 0,
			),
			'order' => array(
				'sort', 'id'
			),
			'recursive' => -1
		);

		return $this->Landmark->find('list', $options);
	}
	
}
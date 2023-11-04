<?php

App::uses('Component', 'Controller');

class BreadCrumbComponent extends Component {
	public $components = array('Session');

	public function initialize(Controller $controller) {
		$this->controller = $controller;
    }

    public function setAirportlist(){
		$progress_arr = $this->createArr(
			array('/rentacar/airportlist/'),
			array('空港一覧'));
        $progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
    }

    public function setArticle(){
		$progress_arr = $this->createArr(
			array(),
			array());
        $progress_arr[1]['class'] = 'is-current';
        return $progress_arr;
	}

	public function setCity($action, $regionLinkCd, $regionName, $prefLinkCd, $prefectureName, $areaLinkCd, $areaName){
		if($prefectureName == '北海道' || $prefectureName == '沖縄県'){
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$areaLinkCd.'/'),
				array($regionName, $areaName)
			);
			$progress_arr[3]['class'] = 'is-current';
		} else {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/'.$areaLinkCd.'/'),
				array($regionName, $prefectureName, $areaName)
			);
			$progress_arr[4]['class'] = 'is-current';
		}
       return $progress_arr;
    }

    public function setCompany($action, $link_cd, $clientName){
		switch($action){
			case 'index' :
			case 'sp_index' :
				$progress_arr = $this->createArr(
					array('/rentacar/companylist/', '/rentacar/company/'.$link_cd.'/'),
					array('レンタカー会社一覧', $clientName));
				$progress_arr[3]['class'] = 'is-current';
			break;
			case 'reviews' :
			case 'sp_reviews' :
				$progress_arr = $this->createArr(
					array('/rentacar/companylist/', '/rentacar/company/'.$link_cd.'/', '/rentacar/company/'.$link_cd.'/review/'),
					array('レンタカー会社一覧', $clientName, '口コミ'));
				$progress_arr[4]['class'] = 'is-current';
			break;
		}
        return $progress_arr;
    }

    public function setCompanylist(){		
		$progress_arr = $this->createArr(
			array('/rentacar/companylist/'),
			array('レンタカー会社一覧'));

			$progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
    }

    public function setFerryterminallist(){
		$progress_arr = $this->createArr(
			array('/rentacar/ferryterminallist/'),
			array('フェリーターミナル一覧'));
        $progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
	}

    public function setFerryterminal($action, $landmarkName, $regionLinkCd, $prefLinkCd, $linkcd, $regionName, $prefectureName){
		if($prefectureName == '北海道' || $prefectureName == '沖縄県') {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$linkcd.'/'),
				array($regionName, $landmarkName));
			$progress_arr[3]['class'] = 'is-current';
		} else {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/'.$linkcd.'/'),
				array($regionName, $prefectureName, $landmarkName));
			$progress_arr[4]['class'] = 'is-current';
		}
        return $progress_arr;
    }

    public function setFromairport($action, $landmarkName, $regionLinkCd, $prefLinkCd, $linkcd, $regionName, $prefectureName){
		if($prefectureName == '北海道' || $prefectureName == '沖縄県') {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$linkcd.'/'),
				array($regionName, $landmarkName));
			$progress_arr[3]['class'] = 'is-current';
		} else {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/'.$linkcd.'/'),
				array($regionName, $prefectureName, $landmarkName));
			$progress_arr[4]['class'] = 'is-current';
		}
        return $progress_arr;
    }

    public function setInfos($action){
        switch ($action) {
            case 'reserve' :
            case 'sp_reserve' :
				$progress_arr = $this->createArr(
					array('/rentacar/infos/reserve/'),
					array('サイトでの予約から当日までの流れ'));
		        $progress_arr[2]['class'] = 'is-current';
                break;
            case 'qanda' :
            case 'sp_qanda' :
				$progress_arr = $this->createArr(
					array('/info/faq/', '/rentacar/infos/qanda/'),
					array('よくあるご質問', 'レンタカーご利用の際によくある質問'));
		        $progress_arr[3]['class'] = 'is-current';
                break;
            case 'article' :
            case 'sp_article' :
				$progress_arr = $this->createArr(
					array('/rentacar/infos/article/'),
					array('レンタカー記事ページ'));
        		$progress_arr[2]['class'] = 'is-current';
                break;
        }
        return $progress_arr;
    }

    public function setLocalstoredetail($action, $parentUrl, $officeName, $clientName, $officeLinkCd){
		switch($action){
			case 'index' :
			case 'sp_index' :
				$progress_arr = $this->createArr(
					array('/rentacar/companylist/', $parentUrl, $parentUrl.$officeLinkCd.'/'), 
					array('レンタカー会社一覧', $clientName, $clientName .$officeName));
    			$progress_arr[4]['class'] = 'is-current';
			break;
			case 'reviews' :
			case 'sp_reviews' :
				$progress_arr = $this->createArr(
					array('/rentacar/companylist/', $parentUrl, $parentUrl.$officeLinkCd.'/', $parentUrl.$officeLinkCd.'/review/'), 
					array('レンタカー会社一覧', $clientName, $clientName .$officeName, '口コミ'));
	    		$progress_arr[5]['class'] = 'is-current';
			break;
				}
        return $progress_arr;
	}

    public function setMunicipality($action, $regionName, $regionLinkCd, $prefectureName, $prefLinkCd, $cityName, $cityLinkCd){
		if($prefectureName == '北海道' || $prefectureName == '沖縄県'){
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$cityLinkCd.'/'),
				array($regionName, $cityName));
			$progress_arr[3]['class'] = 'is-current';
		} else {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/'.$cityLinkCd.'/'),
				array($regionName, $prefectureName, $cityName));
			$progress_arr[4]['class'] = 'is-current';
		}
        return $progress_arr;
	}

    public function setMypages($action){
        switch ($action) {
            case 'index' :
			case 'sp_index' :
				$progress_arr = $this->createArrForMypages(
					array(),
					array());
		        $progress_arr[1]['class'] = 'is-current';
                break;
            case 'login' :
            case 'sp_login' :
				$progress_arr = $this->createArrForMypages(
					array(),
					array());
		        $progress_arr[1]['class'] = 'is-current';
                break;
            case 'cancel' :
            case 'sp_cancel' :
				$progress_arr = $this->createArrForMypages(
					array('/rentacar/mypages/cancel/'),
					array('予約キャンセル'));
        		$progress_arr[2]['class'] = 'is-current';
                break;
            case 'cancel_check' :
            case 'sp_cancel_check' :
				$progress_arr = $this->createArrForMypages(
					array('/rentacar/mypages/cancel_check/'),
					array('予約キャンセル'));
        		$progress_arr[2]['class'] = 'is-current';
                break;
            case 'cancel_finish' :
            case 'sp_cancel_finish' :
				$progress_arr = $this->createArrForMypages(
					array('/rentacar/mypages/cancel/', '/rentacar/mypages/cancel_finish/'),
					array('予約キャンセル', '予約キャンセル完了'));
        		$progress_arr[2]['class'] = 'disabled';
        		$progress_arr[3]['class'] = 'is-current';
                break;
            case 'input' :
            case 'sp_input' :
				$progress_arr = $this->createArrForMypages(
					array('/rentacar/mypages/input/'),
					array('お支払い情報入力'));
        		$progress_arr[2]['class'] = 'is-current';
                break;
            case 'completion' :
            case 'sp_completion' :
				$progress_arr = $this->createArrForMypages(
					array('/rentacar/mypages/input/', '/rentacar/mypages/completion/'),
					array('お支払い情報入力', 'お支払い完了'));
        		$progress_arr[2]['class'] = 'disabled';
        		$progress_arr[3]['class'] = 'is-current';
                break;
        }
        return $progress_arr;
    }



    public function setNews($action, $title = '', $linkCd = ''){
		switch($action){
			case 'index' :
			case 'sp_index' :
				$progress_arr = $this->createArr(
					array('/rentacar/news/'),
					array('レンタカーのお知らせ'));
	    		$progress_arr[2]['class'] = 'is-current';
			break;
			case 'show' :
			case 'sp_show' :
				$progress_arr = $this->createArr(
					array('/rentacar/news/', $linkCd),
					array('レンタカーのお知らせ', $title));
				$progress_arr[3]['class'] = 'is-current';
			break;
			}
        return $progress_arr;
	}

    public function setPrefectures($action, $regionName, $regionLinkCd, $prefectureName, $prefLinkCd){
		if($prefectureName == '北海道' || $prefectureName == '沖縄県'){
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/'),
				array($regionName));
			$progress_arr[2]['class'] = 'is-current';
		} else {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefLinkCd.'/'),
				array($regionName, $prefectureName));
			$progress_arr[3]['class'] = 'is-current';
        }
        return $progress_arr;
    }

    public function setRegion($action, $regionName, $regionLinkCd){
		$progress_arr = $this->createArr(
			array('/rentacar/'.$regionLinkCd.'/'),
			array($regionName));
		$progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
    }

    public function setPlan($action){
		switch ($action) {
			case 'index' :
				$progress_arr = $this->createArr(
					array($this->Session->read('BreadCrumb.search'), '/rentacar/plan/', '/rentacar/reservations/step1/', '/rentacar/reservations/completion/'),
					array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込完了'));
				$progress_arr[3]['class'] = 'is-current';
				$progress_arr[4]['class'] = 'disabled';
				$progress_arr[5]['class'] = 'disabled';
				break;
            case 'sp_index' :
				$progress_arr = $this->createArr(
					array($this->Session->read('BreadCrumb.search'), '/rentacar/plan/'),
					array('格安レンタカー一覧', 'お見積り'));
				$progress_arr[3]['class'] = 'is-current';
				break;
		}
        return $progress_arr;
	}

	public function setReservations($action, $reservationAdvertisingCd = ''){
		switch ($action) {
            case 'step1' :
				$progress_arr = $this->createArr(
					array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/', '/rentacar/reservations/completion/'),
					array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込完了'));
				$progress_arr[3]['class'] = 'disabled';
				$progress_arr[4]['class'] = 'is-current';
				$progress_arr[5]['class'] = 'disabled';
				break;
            case 'step2' :
				$progress_arr = $this->createArr(
					array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/', '/rentacar/reservations/step2/', '/rentacar/reservations/completion/'),
					array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込内容確認', '申込完了'));
				$progress_arr[3]['class'] = 'disabled';
				$progress_arr[4]['class'] = 'disabled';
				$progress_arr[5]['class'] = 'is-current';
				$progress_arr[6]['class'] = 'disabled';
				break;
            case 'completion' :
				if (!empty($reservationAdvertisingCd) && strncmp($reservationAdvertisingCd, 'dtravel', 7) === 0) {
					$progress_arr = $this->createArr(
						array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/', '/rentacar/reservations/step2/', '/rentacar/reservations/completion/'),
						array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込内容確認', '申込完了'));
					$progress_arr[4]['class'] = 'disabled';
					$progress_arr[5]['class'] = 'disabled';
					$progress_arr[6]['class'] = 'is-current';
				} else {
					$progress_arr = $this->createArr(
						array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/', '/rentacar/reservations/completion/'),
						array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込完了'));
					$progress_arr[5]['class'] = 'is-current';
				}
				$progress_arr[3]['class'] = 'disabled';
				$progress_arr[4]['class'] = 'disabled';
                break;
            case 'sp_step1' :
				$progress_arr = $this->createArr(
					array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/'),
					array('格安レンタカー一覧', 'お見積り', 'お客様情報入力'));
				$progress_arr[4]['class'] = 'is-current';
                break;
            case 'sp_step2' :
				$progress_arr = $this->createArr(
					array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/', '/rentacar/reservations/step2/'),
					array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込内容確認'));
				$progress_arr[4]['class'] = 'disabled';
				$progress_arr[5]['class'] = 'is-current';
                break;
            case 'sp_completion' :
				if (!empty($reservationAdvertisingCd) && strncmp($reservationAdvertisingCd, 'dtravel', 7) === 0) {
					$progress_arr = $this->createArr(
						array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/', '/rentacar/reservations/step2/', '/rentacar/reservations/completion/'),
						array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込内容確認', '申込完了'));
					$progress_arr[5]['class'] = 'disabled';
					$progress_arr[6]['class'] = 'is-current';
				} else {
					$progress_arr = $this->createArr(
						array($this->Session->read('BreadCrumb.search'), $this->Session->read('referer.plan'), '/rentacar/reservations/step1/', '/rentacar/reservations/completion/'),
						array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込完了'));
					$progress_arr[5]['class'] = 'is-current';
				}
				$progress_arr[3]['class'] = 'disabled';
				$progress_arr[4]['class'] = 'disabled';
				break;
		}
        return $progress_arr;
    }

	public function setSearches($action){
		if($action === 'index'){ // SP版は表示しない
			$progress_arr = $this->createArr(
				array('/rentacar/searches/', '/rentacar/plan/', '/rentacar/reservations/step1/', '/rentacar/reservations/completion/'),
				array('格安レンタカー一覧', 'お見積り', 'お客様情報入力', '申込完了'));
			$progress_arr[3]['class'] = 'disabled';
			$progress_arr[4]['class'] = 'disabled';
			$progress_arr[5]['class'] = 'disabled';
		} else{
			$progress_arr = $this->createArr(
				array('/rentacar/searches/'),
				array('格安レンタカー一覧'));
		}
		$progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
	}

    public function setStatics($action){
        switch ($action) {
            case 'resend' :
            case 'sp_resend' :
				$progress_arr = $this->createArr(
					array('/rentacar/statics/resend/'),
					array('予約内容の再送'));
                break;
            case 'resend_complete' :
            case 'sp_resend_complete' :
				$progress_arr = $this->createArr(
					array('/rentacar/statics/resend_complete/'),
					array('予約内容再送完了'));
                break;
            case 'resend_error' :
            case 'sp_resend_error' :
				$progress_arr = $this->createArr(
					array('/rentacar/statics/resend_error/'),
					array('予約内容未再送'));
                break;
        }
		$progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
    }

	public function setStation($action, $regionName, $regionLinkCd, $prefectureName, $prefectureLinkCd, $stationName, $stationLinkCd){
		if($prefectureName == '北海道' || $prefectureName == '沖縄県'){
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$stationLinkCd.'/'),
				array($regionName, $stationName));
			$progress_arr[3]['class'] = 'is-current';
		} else {
			$progress_arr = $this->createArr(
				array('/rentacar/'.$regionLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefectureLinkCd.'/', '/rentacar/'.$regionLinkCd.'/'.$prefectureLinkCd.'/'.$stationLinkCd.'/'),
				array($regionName, $prefectureName, $stationName));
			$progress_arr[4]['class'] = 'is-current';
		}
        return $progress_arr;
	}

    public function setStationlist(){
		$progress_arr = $this->createArr(
			array('/rentacar/stationlist/'),
			array('駅一覧'));
		$progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
	}

	public function setTops($action){
		switch($action){
			case 'index' :
			case 'sp_index' :
				$progress_arr = $this->createArr(
					array(),
					array());
			break;
			case 'photogallery' :
			case 'sp_photogallery' :
				$progress_arr = $this->createArr(
					array('/rentacar/tops/photogallery/'),
					array('レンタカー会社に関する写真投稿'));
				$progress_arr[2]['class'] = 'is-current';
			break;
		}
        return $progress_arr;
    }

    public function setWpcampaign($action, $titleName, $link_cd){
		$progress_arr = $this->createArr(
			array('/rentacar/campaign/'.$link_cd.'/'),
			array($titleName));
		$progress_arr[2]['class'] = 'is-current';
        return $progress_arr;
	}

	private function createArr(array $addUrlArray, array $addNameArray){
        $progress_arr = [];

		$urlArr = array('/', '/rentacar/');
		$nameArr = array('TOP', '格安レンタカー比較・予約');

		$mergedUrlArray = array_merge($urlArr, $addUrlArray);
		$mergedNameArray = array_merge($nameArr, $addNameArray);

		foreach ($mergedUrlArray as $k => $mergeUrl) {
			$progress_arr[] = array(
				'url' => $mergeUrl,
				'name' => $mergedNameArray[$k],
				'class' => ''
			);
		}
        return $progress_arr;
	}

	private function createArrForMypages(array $addUrlArray, array $addNameArray){
        $progress_arr = [];

		$urlArr = array('/', '/rentacar/mypages/');
		$nameArr = array('TOP', '予約確認');

		$mergedUrlArray = array_merge($urlArr, $addUrlArray);
		$mergedNameArray = array_merge($nameArr, $addNameArray);

		foreach ($mergedUrlArray as $k => $mergeUrl) {
			$progress_arr[] = array(
				'url' => $mergeUrl,
				'name' => $mergedNameArray[$k],
				'class' => ''
			);
		}
        return $progress_arr;
	}
}

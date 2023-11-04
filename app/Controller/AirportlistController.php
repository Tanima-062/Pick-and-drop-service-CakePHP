<?php
App::uses('AppController', 'Controller');

class AirportlistController extends AppController {

    public $uses = array('Landmark','RcPostmeta', 'Prefecture');
	public $components = array('BreadCrumb');

    public function index() {

		//空港と新幹線駅のリストを取得
		$landmarkList = $this->Landmark->getAirportAndBulletTrainArrayList();
		$this->set('landmarkList', $landmarkList);

		//空港のリンクIDを取得
		$airportLinkCdList = $this->Landmark->getAirportLinkCdList();
		$this->set('airportLinkCdList', $airportLinkCdList);
		
		$prefectureLinkCdList = $this->Prefecture->getLinkCdAndRegionLinkCd();
		$base_url_arr = array();
		foreach ($prefectureLinkCdList as $k => $v) {
			if ($k === '北海道' || $k === '沖縄県') {
				$base_url_arr[$k] = key($v) . DS;
			} else {
				$base_url_arr[$k] = str_replace('area_', '', $v[key($v)]) . DS . key($v) . DS;
			}
		}
		unset($prefectureLinkCdList);
		$this->set('base_url_arr', $base_url_arr);

		// リンク用日付配列
		$link_date_arr = date_parse(date('Ymd',strtotime('+7 day')));		// 7日後の日付
		$link_date_arr2 = date_parse(date('Ymd',strtotime('+11 day')));		// 11日後の日付

		$this->set(compact('link_date_arr', 'link_date_arr2'));

		//コンテンツ見出し・本文を取得
		$airportContents = $this->RcPostmeta->getAirportPostmetaData();
		//ページ上部、下部に表示させるコンテンツが異なるため配列を2つ用意する
		$airportDataArrayTop = array();
		$airportDataArrayBottom = array();
		$countMiddleAirportData = floor(count($airportContents) / 4) - 1;
		foreach($airportContents as $key => $val){
			if(substr($val['RcPostmeta']['meta_key'], 0, 16) == 'airport-contents' && substr($val['RcPostmeta']['meta_key'], -14) == 'airport-head-s'){
				//mata_keyの数値を取得する
				$metaKeyNum = $this->cutBeforeText($val['RcPostmeta']['meta_key'], 17);
				$metaKeyNum = $this->cutAfterText($metaKeyNum, 15);
				if($metaKeyNum <= $countMiddleAirportData){
					$airportDataArrayTop[$metaKeyNum]['head'] = $val['RcPostmeta']['meta_value'];
				} else {
					$airportDataArrayBottom[$metaKeyNum]['head'] = $val['RcPostmeta']['meta_value'];
				}
			}
			if(substr($val['RcPostmeta']['meta_key'], 0, 16) == 'airport-contents' && substr($val['RcPostmeta']['meta_key'], -12) == 'airport-text'){
				//mata_keyの数値を取得する
				$metaKeyNum = $this->cutBeforeText($val['RcPostmeta']['meta_key'], 17);
				$metaKeyNum = $this->cutAfterText($metaKeyNum, 13);
				if($metaKeyNum <= $countMiddleAirportData){
					$airportDataArrayTop[$metaKeyNum]['text'] = $val['RcPostmeta']['meta_value'];
				} else {
					$airportDataArrayBottom[$metaKeyNum]['text'] = $val['RcPostmeta']['meta_value'];
				}
			}
		}

		$this->set('airportDataArrayTop', $airportDataArrayTop);
		$this->set('airportDataArrayBottom', $airportDataArrayBottom);

		$this->set('title_for_layout', '空港一覧から格安レンタカー料金比較・予約｜スカイチケットレンタカー');
		$this->set('description_for_layout','格安レンタカーが最大70％オフ！新千歳空港や那覇空港など全国の空港周辺にある格安レンタカーを比較・予約。空港送迎はもちろん、当日予約や乗り捨て、24時間営業にも対応。全国の空港レンタカーを空港一覧からかんたんに予約できます。');
		$this->set('keywords','レンタカー,格安,比較,予約,新千歳空港,那覇空港,福岡空港,乗り捨て,スカイチケット');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setAirportlist();
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {
		$this->index();
	}

	private function cutBeforeText($text, $num){
	    $replace = substr( $text , $num , strlen($text)-$num );
	    return $replace;
	}

	private function cutAfterText($text, $num){
	    $replace = substr( $text , 0 , strlen($text)-$num );
	    return $replace;
	}
}

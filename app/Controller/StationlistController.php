<?php
App::uses('AppController', 'Controller');

class StationlistController extends AppController {

	public $uses = array('Landmark','RcPostmeta','Station','Prefecture','Client');
	public $components = array('BreadCrumb');

	public function index() {

		//コンテンツ見出し・本文を取得
		$stationlistContents = $this->RcPostmeta->getStationlistPostmetaData();

		$this->set('stationlistContents', $stationlistContents);
		$this->set('clientList', $this->Client->getClientList());

		$this->set('title_for_layout', '駅一覧から格安レンタカー料金比較・予約｜スカイチケットレンタカー');
		$this->set('description_for_layout','格安レンタカーが最大70％オフ！札幌駅、名古屋駅、博多駅など大きなターミナル駅から地下鉄、路面電車などのローカル駅まで最安値のレンタカーを比較・予約。当日予約や乗り捨て、24時間営業にも対応。全国の駅一覧からかんたんにレンタカー予約ができます。');
		$this->set('keywords','レンタカー,格安,比較,予約,札幌駅,博多駅,名古屋駅,乗り捨て,スカイチケット');

		//  パンくずリスト設定
		$this->set('progress_arr', $this->BreadCrumb->setStationlist($this->action));
	}

	public function sp_index() {
		$this->index();
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

<?php
App::uses('AppController', 'Controller');

class FerryterminallistController extends AppController {

	public $uses = array('Landmark', 'RcPostmeta', 'Prefecture');
	public $components = array('BreadCrumb');

	public function index() {

		// 港と新幹線駅のリストを取得
		$landmarkList = $this->Landmark->getFerryTerminalArrayList();
		$this->set('landmarkList', $landmarkList);

		// ターミナルのリンクIDを取得
		$terminalLinkCdList = $this->Landmark->getFerryTerminalLinkCdList();
		$this->set('terminalLinkCdList', $terminalLinkCdList);

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
		$link_date_arr = date_parse(date('Ymd', strtotime('+7 day')));		// 7日後の日付
		$link_date_arr2 = date_parse(date('Ymd', strtotime('+11 day')));		// 11日後の日付

		$this->set(compact('link_date_arr', 'link_date_arr2'));

		// コンテンツ見出し・本文を取得
		$ferryTerminalContents = $this->RcPostmeta->getFerryTerminalPostmetaData();
		
		// ページ上部、下部に表示させるコンテンツが異なるため配列を2つ用意する
		$ferryTerminalDataArrayTop = array();
		$ferryTerminalDataArrayBottom = array();
		$countMiddle = floor(count($ferryTerminalContents) / 4) - 1;
		foreach ($ferryTerminalContents as $key => $val) {
			if (substr($val['RcPostmeta']['meta_key'], 0, 22) == 'port_terminal-contents' && substr($val['RcPostmeta']['meta_key'], -20) == 'port_terminal-head-s'){
				// mata_keyの数値を取得する
				$metaKeyNum = $this->cutBeforeText($val['RcPostmeta']['meta_key'], 23);
				$metaKeyNum = $this->cutAfterText($metaKeyNum, 21);
				if ($metaKeyNum <= $countMiddle) {
					$ferryTerminalDataArrayTop[$metaKeyNum]['head'] = $val['RcPostmeta']['meta_value'];
				} else {
					$ferryTerminalDataArrayBottom[$metaKeyNum]['head'] = $val['RcPostmeta']['meta_value'];
				}
			}
			if (substr($val['RcPostmeta']['meta_key'], 0, 22) == 'port_terminal-contents' && substr($val['RcPostmeta']['meta_key'], -18) == 'port_terminal-text') {
				// mata_keyの数値を取得する
				$metaKeyNum = $this->cutBeforeText($val['RcPostmeta']['meta_key'], 23);
				$metaKeyNum = $this->cutAfterText($metaKeyNum, 19);
				if ($metaKeyNum <= $countMiddle) {
					$ferryTerminalDataArrayTop[$metaKeyNum]['text'] = $val['RcPostmeta']['meta_value'];
				} else {
					$ferryTerminalDataArrayBottom[$metaKeyNum]['text'] = $val['RcPostmeta']['meta_value'];
				}
			}
		}

		$this->set('ferryTerminalDataArrayTop', $ferryTerminalDataArrayTop);
		$this->set('ferryTerminalDataArrayBottom', $ferryTerminalDataArrayBottom);

		$this->set('title_for_layout', 'フェリーターミナル一覧から格安レンタカー料金比較・予約｜スカイチケットレンタカー');
		$this->set('description_for_layout', '格安レンタカーが最大70％オフ！沖縄のとまりんや石垣離島ターミナルをはじめ、全国のフェリーターミナル周辺にある格安レンタカーを比較・予約。フェリーターミナルから送迎ありや当日予約にも対応。かんたんに最安値のレンタカーが予約できます。');
		$this->set('keywords', 'レンタカー,格安,比較,予約,フェリーターミナル,とまりん,離島ターミナル,港,スカイチケット');

		// パンくずリスト設定
		$progressArr = $this->BreadCrumb->setFerryterminallist();
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
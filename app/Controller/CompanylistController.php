<?php

App::uses('AppController', 'Controller');

class CompanylistController extends AppController {

    public $uses = array('Client');
    public $use_yotpo = true;
    public $use_yotpo_rating = true;
	public $components = array('BreadCrumb');
	
    public function index() {
        $clientList = $this->Client->getClientListAndPostmetaData();
        $this->set('clientList',$clientList);
        $this->set('title_for_layout','レンタカー会社一覧から格安レンタカー料金比較・予約｜スカイチケット');
        $this->set('description_for_layout','格安レンタカーが最大70％オフ！沖縄や北海道をはじめ全国4000店舗から最安値のレンタカーを比較・予約。当日予約や乗り捨て、24時間営業など幅広いプランとキャンペーンを掲載中。レンタカー会社一覧からかんたんに予約ができます。');
        $this->set('keywords','レンタカー,格安,比較,予約,乗り捨て,沖縄,北海道,スカイチケット');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setCompanylist();
		$this->set('progress_arr', $progressArr);
    }
	public function sp_index() {
		$this->index();
	}
}

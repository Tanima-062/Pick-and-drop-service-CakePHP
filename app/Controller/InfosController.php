<?php
App::uses('AppController', 'Controller');
/**
 * Infos Controller
 *
 * @property Infos $Infos
 */
class InfosController extends AppController {

	public $uses = array('RcPostmeta', 'Client');
//	public $components = array('OptionsManage');
	public $components = array('BreadCrumb');
	
	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function reserve() {
		$this->set('title_for_layout','格安レンタカー料金比較・予約方法・当日の流れ｜スカイチケット');
		$this->set('description_for_layout','格安レンタカーが最大70％オフ！沖縄や北海道をはじめ全国4000店舗から最安値のレンタカーを比較・予約。当日予約や乗り捨てなど幅広いプラン比較が可能。レンタカー料金の比較・予約方法と、レンタカー出発当日の流れを詳しく解説。');
		$this->set('keywords','レンタカー,流れ,利用方法,格安,比較,予約,乗り捨て,スカイチケット');


		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setInfos($this->action);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_reserve() {
		$this->reserve();
	}

	public function qanda() {
		$this->set('title_for_layout','格安レンタカー予約でよくある質問｜スカイチケットレンタカー');
		$this->set('description_for_layout','格安レンタカーが最大70％オフ！沖縄や北海道をはじめ全国4000店舗から最安値のレンタカーを比較・予約。当日予約や乗り捨てなど幅広いプラン比較が可能。レンタカー料金の比較・予約時のよくあるご質問Q&Aを詳しく掲載しています。');
		$this->set('keywords','レンタカー,よくあるご質問,FAQ,格安,比較,予約,乗り捨て,スカイチケット');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setInfos($this->action);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_qanda() {
		$this->qanda();
	}
	
	public function article() {
		$this->set('clientList', $this->Client->getClientList());
		
		if(empty($this->params['link_cd'])){
			return;
		}
		
		//URLアクション名取得
		$infosLinkCd = $this->params['link_cd'];

		//記事の投稿を取得
		$articleContents = $this->RcPostmeta->getArticlePostmetaDataByLinkCd($infosLinkCd);

		$this->set('articleContents', $articleContents);

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setInfos($this->action);
		$this->set('progress_arr', $progressArr);
	}
	
	public function sp_article() {
		$this->article();
	}

	public function kiyaku() {
		$this->set('title_for_layout','利用規約 - 国内レンタカーの予約・比較はスカイチケット');
		$this->set('description_for_layout','利用規約 - 国内レンタカーの予約・比較するならskyticket（スカイチケット）。国内線飛行機チケットも予約できるスカイチケットで旅先の移動のためのレンタカーも予約できます。');
		$this->set('keywords','利用規約,規約,レンタカー,skyticket,スカイチケット,レンタカー予約,航空券');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setInfos($this->action);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_kiyaku() {
		$this->kiyaku();
	}

}

<?php
class CakeErrorController extends AppController {
	public $name = 'CakeError';
//	public $layout = false;
	public $uses = false;

	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		if (count(Router::extensions())) {
			$this->components[] = 'RequestHandler';
		}
		$this->constructClasses();
		if ($this->Components->enabled('Auth')) {
			$this->Components->disable('Auth');
		}
		if ($this->Components->enabled('Security')) {
			$this->Components->disable('Security');
		}
		$this->startupProcess();

		$this->_set(array('cacheAction' => false, 'viewPath' => 'Errors'));
	}
	
	public function afterFilter() {
		parent::afterFilter();
		
		if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
			$this->layout = 'sp_default';
		}
		$this->set('title_for_layout','国内レンタカーの予約・比較はスカイチケット');
		$this->set('description_for_layout','沖縄や北海道をはじめ全国の格安レンタカーが最大70％オフ！早朝や深夜、24時間対応など幅広いプランを簡単検索・予約。那覇空港や新千歳空港などの空港はもちろん、宮古島などの離島も予約可能です。');
		$this->set('keywords','沖縄,北海道,レンタカー,格安,予約,比較');
		
		// DBコネクトエラー時は503を返す
		if (isset($this->viewVars['error']) && $this->viewVars['error'] instanceof MissingConnectionException) {
			$this->response->statusCode(503);
			$this->set('title_for_layout','メンテナンス中 - 国内レンタカーの予約・比較はスカイチケット');
		} else if (IS_PRODUCTION) {
			$this->render('error400');
		}
	}
}

<?php
App::uses('Controller', 'Controller');
App::uses('ApiException', 'Error');

abstract class BaseRestApiController extends Controller {
	// 独自処理APIのためAppControllerを継承しない
	public $components = array('RequestHandler', 'ApiCommon', 'ApiAuth');
	protected $responseData = array();
	protected $clientId = 0;
	protected $doInvokeAction = false;
	// componentのマージ先をAppControllerから変更
	protected $_mergeParent = 'BaseRestApiController';

	// コンポーネントをロードし初期化する
	public function loadComponent($component) {
		$this->$component = $this->Components->load($component);
		$this->$component->initialize($this);
	}

	public function beforeFilter() {
		parent::beforeFilter();

		// APIのレスポンス設定を取得
		Configure::load('ApiConfig.php');
		$apiConfig = Configure::read('ApiConfig');

		// actionを実行するか判定
		$this->doInvokeAction = (!empty($apiConfig['all']) && !empty($apiConfig[$this->name]));

		// 通貨の設定
		$currency = isset($this->request->query['currency']) ? $this->request->query('currency') : 'JPY';
		Configure::write('currency', $currency);
	}

	// render時にresponseDataの内容をセット
	// 何も値がなければnullになるはず
	public function beforeRender() {
		foreach ($this->responseData as $key => $value) {
			$this->set($key, $value);
		}
		$this->set('_serialize', array_keys($this->responseData));
	}

	// action実行させたくない場合、ここで止める
	public function invokeAction(CakeRequest $request) {
		if (!$this->doInvokeAction) {
			return false;
		}

		// 会社個別APIの場合
		if (!empty($this->clientId)) {
			if (!$this->ApiAuth->authenticate($request, $this->clientId)) {
				$this->response->statusCode(401);
				return false;
			}
		}

		try {
			// actionの実行
			return parent::invokeAction($request);
		} catch (ApiException $e) {
			// 共通のエラーメッセージを返す
			$this->response->statusCode($e->getCode());
			$this->responseData = $e->getResponseData();
		} catch (Exception $e) {
			// その他例外発生時はログ出力し空のレスポンスを返す
			$this->log($e->getTraceAsString(), 'error');
		}
		return false;
	}
}

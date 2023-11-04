<?php
App::uses('BaseRestApiController', 'Controller');

class SearchesApiController extends BaseRestApiController {
	public $components = array('SearchesApi');
	public $uses = array(
		'SearchesApiCommodity',
		'SearchesApiValidation',
		'CarType',
		'Client',
		'Landmark',
		'Office',
	);

	// 検索ボックスで表示する会社リスト
	protected $displayClients = array(
		4, 5, 13, 33, 35, 43, 46, 55, 75
	);

	// 検索ボックスのオプションリスト
	protected $options = array(
		4	 => 'カーナビ搭載',
		5	 => 'ETC搭載',
		6	 => 'スタッドレスタイヤ',
		7	 => 'タイヤチェーン',
		8	 => '4WD',
		12	 => 'ETCカード',
		13	 => 'NOC補償',
		14	 => '運転サポート',
		15	 => 'バックモニター',
		16	 => 'AUXケーブル',
		17	 => 'Bluetooth',
		18	 => 'ドライブレコーダー',
		99	 => 'WEB決済可能',
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->ApiCommon->setCorsHeader();
		// $this->options = Constant::options();
		// $this->displayClients = Constant::displayClients();
	}

	// 検索条件の定義を返す
	public function index() {
		// 車両タイプ取得
		$tmpCarTypes = $this->CarType->getCarTypeInfo();

		$carTypes = array();
		foreach ($tmpCarTypes as $v) {
			$carType = $v['CarType'];

			$carTypes[] = array(
				'carTypeId' => (int)$carType['id'],
				'carTypeName' => $carType['name'],
				'description' => trim($carType['description']),
			);
		}

		// オプション取得
		$options = array();
		foreach ($this->options as $k => $v) {
			$options[] = array(
				'optionId' => $k,
				'optionName' => $v,
			);
		}

		// クライアント取得
		$tmpClients = $this->Client->findC('list', array(
			'fields' => array(
				'id', 'name',
			),
			'conditions' => array(
				'id' => $this->displayClients,
			),
			'order' => array(
				'sort', 'id'
			),
			'recursive' => -1,
		));

		$clients = array();
		foreach ($tmpClients as $k => $v) {
			$clients[] = array(
				'clientId' => (int)$k,
				'clientName' => $v,
			);
		}

		$this->responseData = array(
			'carTypes' => $carTypes,
			'options' => $options,
			'clients' => $clients,
		);
	}

	// プラン検索をする
	public function search() {
		if (empty($this->request->data)) {
			// プランなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		// 1次元配列にする
		$params = $this->SearchesApi->flattenParams($this->request->data);

		// バリデーションチェック
		$this->SearchesApiValidation->set($params);
		if (!$this->SearchesApiValidation->validates()) {
			throw new ApiException($this->SearchesApiValidation->validationErrors);
		}

		// APIのパラメータからスカイチケットのパラメータに変換する
		$params = $this->SearchesApi->getSearchParams($params);

		$response = array();
		$query = $this->SearchesApiCommodity->getCommodityQuery($params);

		if(!empty($query)) {
			// 商品情報を取得
			$this->paginate = $query;
			$response = $this->paginate();
		}
		
		$this->responseData = $response;
	}

	// プラン検索をする
	public function searchBasic() {
		// IDで検索するので座標関連のルールを削除する
		$rules = $this->SearchesApiValidation->validator();
		unset($rules['latitude'], $rules['longitude'], $rules['returnLatitude'], $rules['returnLongitude']);

		$this->search();
	}
}

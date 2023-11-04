<?php

App::uses('Component', 'Controller');
App::uses('HttpSocket', 'Network/Http');

class YotpoAPIComponent extends Component {

	private $config = null;
	private $controller = null;

	public function startup(&$controller) {
		Configure::load('YotpoConfig', 'default');
		$this->config = Configure::read('Yotpo');
		$this->controller = $controller;
	}

	/**
	 * App keyを取得
	 */
	public function getAppKey() {
		return $this->config['app_key'];
	}

	/**
	 * App secretを取得
	 */
	public function getAppSecret() {
		return $this->config['app_secret'];
	}

	/**
	 * domainを取得
	 */
	public function getDomain() {
		return $this->config['domain'];
	}

	/**
	 * ログ出力
	 */
	private function _output_log($message) {
		CakeLog::write('yotpo', $message);
	}

	/**
	 * HTTP上で要求の送信
	 * @param string $url
	 * @param array $data
	 * @param string $type
	 */
	private function sendRequest($url, $data, $type = 'post') {
		$ret = null;

		try {
			$http = new HttpSocket(array('ssl_verify_host' => false));

			if ($type == 'post') {
				$response = $http->post(
						$url, json_encode($data), array('header' => array('Content-Type' => 'application/json'))
				);
			} else if ($type == 'get') {
				$response = $http->get(
						$url, $data
				);
			} elseif ($type == 'delete') {
				$response = $http->delete(
						$url, json_encode($data), array('header' => array('Content-Type' => 'application/json'))
				);
			} elseif ($type == 'put') {
				$response = $http->put(
						$url, json_encode($data), array('header' => array('Content-Type' => 'application/json'))
				);
			}
			$ret = json_decode($response->body);

		} catch (Exception $e) {
			$this->log(sprintf("yotopo error %s\n%s", $e->getMessage(), $e->getTraceAsString()), 'error');
		}

		$this->_output_log(json_encode($ret));
		return $ret;
	}

	/**
	 * OAuthを使用して、utokenの取得
	 */
	private function getAccessToken() {
		// Request Data
		$data = array(
			'client_id' => $this->config['app_key'],
			'client_secret' => $this->config['app_secret'],
			'grant_type' => 'client_credentials',
		);
		$response = $this->sendRequest($this->config['oauth_url'], $data, 'post');

		if (!empty($response->access_token)) {
			return $response->access_token;
		}

		$this->_output_log('utokenの取得を失敗しました。');
		return null;
	}

	private function createGroupIfNotExist($utoken, $groupName) {

		$checkIfGroupNameExitsURL = 'https://api.yotpo.com/v1/apps/' . $this->config['app_key'] . '/products_groups/' . $groupName;
		$checkIfGroupNameExitsData = array('utoken' => $utoken);

		$checkIfGroupNameExitsResponse = $this->sendRequest($checkIfGroupNameExitsURL, $checkIfGroupNameExitsData, 'get');

		if ($checkIfGroupNameExitsResponse->status->code == '200') {
			$this->_output_log('Group Name ' . $groupName . " exists");
			return $checkIfGroupNameExitsResponse;
		} else {

			$createGroupURL = 'https://api.yotpo.com/v1/apps/' . $this->config['app_key'] . '/products_groups';
			$createGroupData = array(
				'group_name' => $groupName,
				'utoken' => $utoken
			);

			$createGroupResponse = $this->sendRequest($createGroupURL, $createGroupData, 'post');

			$this->_output_log('Group Name ' . $groupName . " created");
			return $createGroupResponse;
		}
	}

	private function addProductToGroup($utoken, $groupName, $productID) {
		$addProductToGroupURL = 'https://api.yotpo.com/v1/apps/' . $this->config['app_key'] . '/products_groups/' . $groupName . '?utoken=' . $utoken;
		$addProductToGroupData = array(
			'product_ids_to_add' => array($productID),
			'utoken' => $utoken
		);

		$addProductToGroupResponse = $this->sendRequest($addProductToGroupURL, $addProductToGroupData, 'put');

		if (empty($addProductToGroupResponse) || $addProductToGroupResponse->status->code != '200') {
			$this->_output_log('Product ID: ' . $productID . ' added to Group ' . $groupName . ' NG');
		} else {
			$this->_output_log('Product ID: ' . $productID . ' added to Group ' . $groupName . ' OK');
		}
		return $addProductToGroupResponse;
	}

	public function addUpdateClientIDProduct($utoken, $groupName, $clientProductName, $clientProductURL, $productImage) {
		$checkIfClientProductExitsURL = 'https://api.yotpo.com/v1/apps/' . $this->config['app_key'] . '/products';
		$checkIfClientProductExitsData = array(
			'utoken' => $utoken,
			'count' => 10000,
		);
		$checkIfGroupNameExitsResponse = $this->sendRequest($checkIfClientProductExitsURL, $checkIfClientProductExitsData, 'get');
		if ($checkIfGroupNameExitsResponse->status->code == '200') {

			function getIDs($product) {
				return $product->external_product_id;
			}

			$productIDs = array_map("getIDs", $checkIfGroupNameExitsResponse->products);
			if (in_array($groupName, $productIDs)) {//Check if client product exist
				$updateClientProductURL = 'https://api.yotpo.com/apps/' . $this->config['app_key'] . '/products/mass_update';
				$updateClientProductData = array(
					'utoken' => $utoken,
					"products" => array(
						$groupName => array(
							"name" => $clientProductName,
							"url" => $clientProductURL
						)
					)
				);
				if (!empty($productImage)) {
					$updateClientProductData['products'][$groupName]['image_url'] = $productImage;
				}

				$updateClientProductResponse = $this->sendRequest($updateClientProductURL, $updateClientProductData, 'put');
				if (empty($updateClientProductResponse) || $updateClientProductResponse->code != '200') {
					$this->_output_log('Client Product ID: ' . $groupName . ' updated  NG');
				} else {
					$this->_output_log('Client Product ID: ' . $groupName . ' updated  OK');
				}
			} else {//Client product doesn't exist create one
				$createClientProductURL = 'https://api.yotpo.com/apps/' . $this->config['app_key'] . '/products/mass_create';
				$createClientProductData = array(
					'utoken' => $utoken,
					"products" => array(
						$groupName => array(
							"name" => $clientProductName,
							"url" => $clientProductURL
						)
					)
				);
				if (!empty($productImage)) {
					$createClientProductData['products'][$groupName]['image_url'] = $productImage;
				}

				$createClientProductResponse = $this->sendRequest($createClientProductURL, $createClientProductData, 'post');
				if (empty($createClientProductResponse) || $createClientProductResponse->code != '200') {
					$this->_output_log('Client Product ID ' . $groupName . " created NG");
				} else {
					$this->_output_log('Client Product ID ' . $groupName . " created successfully");
				}
			}
		}
	}

	/**
	 * 注文情報をYOTPOへの登録
	 * @param unknown $order_info
	 * @param unknown $items
	 * @return boolean
	 */
	public function postOrder($order_info, $items) {
		if (!IS_PRODUCTION) {
			return true;
		}

		if (!$this->config['is_active']) {
			return false;
		}
		//get utoken
		$utoken = $this->getAccessToken();
		if ($utoken == null) {
			return false;
		}

		// 注文情報の生成
		$order_date = date('Y-m-d', strtotime($order_info['ordered_at']));
		$data = array(
			'platform' => 'general',
			'utoken' => $utoken,
			'email' => $order_info['email'],
			'customer_name' => $order_info['lastname'] . '　' . $order_info['firstname'],
			'order_id' => $order_info['order_number'],
			'order_date' => $order_date,
			'currency_iso' => 'JPY',
			'products' => array()
		);

		// 商品情報の生成
		foreach ($items as $item) {
			$productID = $item['item_code'];
			$protocol = 'http';
			if ($this->config['is_ssl']) {
				$protocol = 'https';
			}
			$item_url = $protocol . '://' . $this->config['domain'] . '/rentacar/company/' . $item['url'];
			$data['products'][$productID] = array(
				'url' => $item_url,
				'name' => $item['name']
			);
			if (array_key_exists('clientImageURL', $item)) {
				$data['products'][$productID]['image'] = $protocol . '://' . $this->config['domain'] . $item['clientImageURL'];
			}
		}

		// POST
		$result = $this->sendRequest($this->config['api_url'] . '/' . $this->config['app_key'] . '/purchases', $data, 'post');
		if (empty($result) || $result->code != '200') {
			// error
			$this->_output_log($order_info['order_number'] . ' NG');
			return false;
		}
		$this->_output_log($order_info['order_number'] . ' OK');

		//BOC add product to group
		foreach ($items as $item) {
			$productID = $item['item_code'];
			$groupName = $item['group_name'];
			$dummyId = $groupName . 'cl';
			$clientProductName = $item['clientProductName'];
			$protocol = 'http';
			if ($this->config['is_ssl']) {
				$protocol = 'https';
			}
			$clientProductURL = $protocol . '://' . $this->config['domain'] . '/rentacar/company/' . $item['clientProductURL'];
			$productImage = '';
			if (array_key_exists('clientImageURL', $item)) {
				$productImage = $protocol . '://' . $this->config['domain'] . $item['clientImageURL'];
			}
			$groupExistResponse = $this->createGroupIfNotExist($utoken, $groupName);
			if ($groupExistResponse->status->code == '200') {
				$this->addUpdateClientIDProduct($utoken, $dummyId, $clientProductName, $clientProductURL, $productImage);
				$this->addProductToGroup($utoken, $groupName, $dummyId);
				$this->addProductToGroup($utoken, $groupName, $productID);
			}
		}
		//EOC add product to group

		return true;
	}

	/**
	 * 注文情報の削除
	 * @param $orderIDs
	 * @return true:成功,false:失敗
	 */
	public function deleteOrder($orderIDs) {
		if (!IS_PRODUCTION) {
			return true;
		}

		if (!$this->config['is_active']) {
			return false;
		}
		//get utoken
		$utoken = $this->getAccessToken();
		if ($utoken == null) {
			return false;
		}

		$data = array();
		$data['utoken'] = $utoken;
		$data['orders'] = array();
		foreach ($orderIDs as $orderID) {
			$data['orders'][] = array('order_id' => $orderID);
		}


		$result = $this->sendRequest($this->config['api_url'] . '/' . $this->config['app_key'] . '/purchases', $data, 'delete');
		if (empty($result) || $result->code != '200') {
			$this->_output_log('Delete order ids: ' . implode(",", $orderIDs) . ' NG');
			return false;
		}
		$this->_output_log('Delete order ids: ' . implode(",", $orderIDs) . ' OK');
		return true;
	}

	/**
	 * レビューをYOTPOからの取得
	 * @return multitype:
	 */
	public function retrieveAllReviews($sinceUpdatedAt, $page) {
		if (!$this->config['is_active']) {
			return array();
		}

		//get utoken
		$utoken = $this->getAccessToken();
		if ($utoken == null) {
			return array();
		}

		$data = array(
			'utoken' => $utoken,
			'count' => 100,
			'deleted' => 'true',
			'page' => $page,
			'since_updated_at' => $sinceUpdatedAt
		);

		$result = $this->sendRequest('https://api.yotpo.com/v1/apps/' . $this->config['app_key'] . '/reviews', $data, 'get');

		if (count($result) > 0) {
			return $result;
		}
		return array();
	}

	/**
	 * 注文情報を取得
	 * @return array
	 */
	public function retrieveOrders() {
		$utoken = $this->getAccessToken();
		if ($utoken == null) {
			return array();
		}

		$data = array(
			'utoken' => $utoken,
			'count' => 200,
		);

		$result = $this->sendRequest($this->config['api_url'] . '/' . $this->config['app_key'] . '/purchases', $data, 'get');

		if ($result->status->code == '200') {
			return $result->response->purchases;
		}
		$this->_output_log('注文一覧の取得を失敗しました。');
		return array();
	}

}

<?php
ini_set("max_execution_time",300);
App::uses('BaseRestApiController', 'Controller');

class YotpoReceiveApiController extends BaseRestApiController {
	public $components = array('YotpoAPI');

	public $uses = array('YotpoUnregisteredData');

	public function index() {
		$request = $this->request;

		/*
		header 情報で、"x-cio-timestamp" と "x-cio-signature" というヘッダを含み、"Customer.io" という文字列を含むユーザーエージェントに含んでいる
		*/
		// yotpoからのアクセス判定用
		$ua = $request->header('USER_AGENT');
		$signature = $request->header('X_CIO_SIGNATURE');
		$timestamp = $request->header('X_CIO_TIMESTAMP');

		if (strpos($ua, 'Customer.io') !== FALSE && !empty($signature) && !empty($timestamp)) {
			if ($request->data('status') == 'Succeeded') {
				$requestId = $request->data('request_id');
				$options = array(
					'fields' => array(
						'group_name',
						'item_code',
					),
					'conditions' => array(
						'request_id' => $requestId
					),
					'group' => array(
						'group_name',
						'item_code',
					),
				);
				$registerData = $this->YotpoUnregisteredData->find('all', $options);

				if (count($registerData) > 0) {
					$this->log("receive target count:".count($registerData), 'debug');
					foreach ($registerData as $rk => $rv) {
						$YotpoUnregisteredData = $rv['YotpoUnregisteredData'];
						$productID = $YotpoUnregisteredData['item_code'];
						$groupName = $YotpoUnregisteredData['group_name'];
						$dummyId = $groupName . 'cl';

						//get utoken
						// ループ中にutokenの寿命がくる可能性があるため都度呼び出す
						$utoken = $this->YotpoAPI->getAccessToken();
						if ($utoken == null) {
							return false;
						}

						$this->YotpoAPI->addProductToGroup($utoken, $groupName, $dummyId);
						$this->YotpoAPI->addProductToGroup($utoken, $groupName, $productID);
						// 1秒間に5リクエストまでらしいので1秒止める
						sleep(1);
					}
					// 全ての送信が終わったら物理削除する
					$conditions = array(
						'request_id' => $requestId,
					);
					$this->YotpoUnregisteredData->deleteAll($conditions);
					$this->log("receive delete:".json_encode($request->data), 'debug');
				}

				$registerData = $this->YotpoAPI->getYotpoRegisterData();
				if (count($registerData) > 0){
					// yotpo送信対象がまだ残っていたら再送信する。
					$this->YotpoAPI->addUpdateClientIDProductNoCheck($registerData);
				}

			} else {
				// yotpoからのアクセスだが更新に失敗している
				$this->log(json_encode($request->data), 'error');
			}
		} else {
			// yotpo以外からのアクセス
			$this->log(json_encode($request->data), 'notice');
		}

	}
}

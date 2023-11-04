<?php
App::uses('BaseRestApiController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

class BudgetReservationsController extends BaseRestApiController {
	// バジェット
	protected $clientId = Constant::BUDGET_CLIENT_ID;

	// 予約連携
	public function index() {
		$data = $this->request->data;

//		$this->log(sprintf("予約連携APIリクエスト\n%s", print_r($data, true)), 'debug');
		try {
			$http = new HttpSocket(array('timeout' => 60));

			$response = $http->post(
				$this->getUrl(),
				json_encode($data),
				array('header' => array('Content-Type' => 'application/json'))
			);
			$this->log(sprintf("予約連携API生レスポンス\n%d\n%s", $response->code, $response), 'debug');
			if (!$response->isOk()) {
				$this->log("予約連携レスポンスのステータスコードが200ではありません。", 'error');
				$result = array();
			} else {
				$result = json_decode($response, true);
			}
		} catch (Exception $e) {
			$this->log(sprintf("予約連携で例外が発生しました。\n%s\n%s\n%s", print_r($data, true), $e->getMessage(), $e->getTraceAsString()), 'error');
			// 呼び出し元でエラーになるよう、空の配列を返す
			$result = array();
		}
//		$this->log(sprintf("予約連携APIレスポンス\n%s", print_r($result, true)), 'debug');

		$this->responseData = $result;
	}

	// 送信先URL
	protected function getUrl() {
		// 開発で test() に飛ばしたいとき
		//return IS_PRODUCTION ? 'https://ext.budgetrentacar.co.jp/skyticket/skyticket/v1/reserve-upload/' : 'https://jp.skyticket.jp/rentacar/api/budget/v1/reservations/test';
		return IS_PRODUCTION ? 'https://ext.budgetrentacar.co.jp/skyticket/skyticket/v1/reserve-upload/' : 'https://ext.budgetrentacar.co.jp/test-skyticket/skyticket/v1/reserve-upload/';
	}

	// 開発用
	// 予約連携API受信して、結果を返す
	public function test() {

//		$this->log(print_r($this->request->data, true), 'debug');

		$this->responseData['response'] = array(
			'result' => array('status' => true, 'reserveno' => sprintf("%011d", rand(0, 9999999))),
			//'result' => array('status' => false, 'message' => '何らかのエラー'),
		);

	}

}

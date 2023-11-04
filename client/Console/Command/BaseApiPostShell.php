<?php
App::uses('AppShell', 'Console/Command');
App::uses('HttpSocket', 'Network/Http');

abstract class BaseApiPostShell extends AppShell {

	// メイン処理
	final public function main() {
		$now = date('Y-m-d H:i:s');
		$this->outputLog("開始 ({$now})");

		if (!$this->canExecute()) {
			$this->outputLog('実行不可のため、処理を終了します');
		} else {
			$this->executeMain();
		}

		$now = date('Y-m-d H:i:s');
		$this->outputLog("終了 ({$now})");
	}

	// 実行メイン
	abstract protected function executeMain();

	// 実行可否
	protected function canExecute() {
		Configure::load('ApiConfig.php');
		$apiConfig = Configure::read('ApiConfig');

		$this->outputLog(sprintf("\$config['ApiConfig']['all'] = %d, \$config['ApiConfig']['%s'] = %d", $apiConfig['all'], $this->name, $apiConfig[$this->name]));

		return ($apiConfig['all'] && $apiConfig[$this->name]);
	}

	// データ送信
	protected function sendPostData($url, $data, $addHeader = array()) {
		$http = new HttpSocket();

		$header = array_merge($addHeader, array(
			'Content-Type' => 'application/json',
		));

		$response = $http->post($url, json_encode($data), array('header' => $header));

		return json_decode($response->body, true);
	}

	// ログ出力
	protected function outputLog($message) {
		// 標準出力
		echo sprintf("%s : %s\n", $this->name, $message);
	}

}

<?php

App::uses('AppController', 'Controller');

/**
 * Users Controller
 */
class SystemController extends AppController {
	public $autoRender = false;

	private $remote_ip = '';
	private $server_ip = '';
	private $ip_list = array();

	function beforeFilter() {
		$this->remote_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$this->server_ip = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';

		$this->initIpList();

		if (!isset($this->ip_list[$this->remote_ip])) {
			exit;
		}

		if (APP_DIR != 'app') {
			// app以外はとりあえず全actionの認証を無しにしておく
			$this->Auth->allow();
			if (APP_DIR == 'client') {
				$this->initAuth();
			}
		}
	}

	public function load_model($model = null) {
		if ($model && $this->loadModel($model)) {
			$ret = $this->$model->find('first');
			var_dump($ret);
		}
	}
	
	public function clear_model() {
		$ret = (Cache::clear(false, '_cake_model_') && Cache::clear(false, '_cake_core_'));
		echo ($ret) ? "{$this->server_ip} : モデルキャッシュクリア成功\n" : "{$this->server_ip} : モデルキャッシュクリア失敗\n";
	}
	
	public function clear_model_all() {
		// 全てのサーバのキャッシュをクリアしに行く
		foreach ($this->ip_list as $ip => $val) {
			if ($val === false) {
				continue;
			}

			if ($ip == $this->server_ip) {
				$this->clear_model();
			} else {
				$url = 'https://' . $ip . $this->request->webroot . $this->request->params['controller'] . '/clear_model';
				$response = @file_get_contents($url);
				echo $response;
			}
		}
	}
	
	private function initIpList() {
		// ハードコーディングするのでサーバ増えたら追加してください
		// valueはサーバ間での呼び出しの有無
		// 社内とホストOSはfalse
		if (IS_STAGING) {
			// 確認環境
			$this->ip_list = array(
				'10.138.0.12'	 => true,
				'39.110.207.189' => false,
			);
			
		} else if (IS_PRODUCTION) {
			// 本番環境
			$this->ip_list = array(
				'10.138.0.2'	 => true,
				'10.138.0.3'	 => true,
				'10.138.0.4'	 => true,
				'10.138.0.47'	 => true,
				'10.138.0.48'	 => true,
				'10.138.0.59'	 => true,
				'39.110.207.189' => false,
				'10.146.0.5' => false,
			);

		} else if ($this->remote_ip != '192.168.33.1') {
			// 開発環境
			$this->ip_list = array(
				'10.146.0.2'	 => true,
				'10.146.0.3'	 => true,
				'39.110.207.189' => false,
			);

		} else {
			// ローカル環境
			$this->ip_list = array(
				'127.0.0.1'		 => true,
				'192.168.33.1'	 => false,
			);
		}
	}

}

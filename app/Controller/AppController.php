<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $inputDefaults;
	public $domain;
	// 検索ボックス使用フラグ
	public $use_searchbox = false;
	// yotpo使用フラグ
	public $use_yotpo = false;
	public $use_yotpo_rating = false;

	// ユーザーエージェントがGoogleサーチコンソールかページスピオードインサイトのフラグ
	public $is_google_user_agent = false;
	
	//仮で、option_manage.jsを指定
	public $new_js = false;
	public $components = array('Session','Cookie');

	public $from_client_id = 0;

	// リクエスト値検証用リスト
	private $_validation_targets = array(
		// 予約系
		'Reservation' => array(
			'adults'				 => null,
			'basicPrice'			 => null,
			'capacity'				 => null,
			'carClassId'			 => null,
			'children'				 => null,
			'clientId'				 => null,
			'commodityId'			 => null,
			'commodityItemId'		 => null,
			'dayTimeFlg'			 => null,
			'estimationTotalPrice'	 => null,
			'from'					 => '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
			'from_office'			 => null,
			'infants'				 => null,
			'return_office'			 => null,
			'submitFlg'				 => null,
			'to'					 => '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
			'uniqId'				 => '/^[a-zA-Z0-9_]+$/',
		),
		// Cakeシステム系
		'_Token' => array(
			'fields'				 => '/^[a-zA-Z0-9_%.]+$/',
			'key'					 => '/^[a-zA-Z0-9_]+$/',
			'unlocked'				 => '/^[a-zA-Z0-9_]+$/',
		),
		// 検索系
		'place'						 => null,
		'prefecture'				 => null,
		'airport_id'				 => null,
		'bullet_train_id'			 => null,
		'station_id'				 => null,
		'area_id'					 => null,
		'office_id'					 => null,
		'year'						 => null,
		'month'						 => null,
		'day'						 => null,
		'time'						 => '/^[0-2][0-9]-(00|30)$/',
		'return_way'				 => null,
		'return_place'				 => null,
		'return_prefecture'			 => null,
		'return_airport_id'			 => null,
		'return_bullet_train_id'	 => null,
		'return_station_id'			 => null,
		'return_area_id'			 => null,
		'return_office_id'			 => null,
		'return_year'				 => null,
		'return_month'				 => null,
		'return_day'				 => null,
		'return_time'				 => '/^[0-2][0-9]-(00|30)$/',
		'adults_count'				 => null,
		'children_count'			 => null,
		'infants_count	'			 => null,
		'client_id'					 => null,
		'transmission_flg'			 => null,
		'smoking_flg'				 => null,
		'car_type'					 => null,
		'area_type'					 => null,
		'option'					 => null,
		'sort'						 => null,
		'date'						 => '/^2[0-9]{3}\/[0-1]?[0-9]\/[0-3]?[0-9]$/',
		'return_date'				 => '/^2[0-9]{3}\/[0-1]?[0-9]\/[0-3]?[0-9]$/',
		'from'						 => '/^\d{4}-\d{1,2}-\d{1,2}(| \d{2}:\d{2}:\d{2})$/',
		'to'						 => '/^\d{4}-\d{1,2}-\d{1,2}(| \d{2}:\d{2}:\d{2})$/',
	);

	private $_ng_iplist = array(
		'81.0.0.0/8',			// ipvanish.com(ヨーロッパ)
		'209.107.192.0/19',		// ipvanish.com(US)
		'209.107.196.0/24',		// ipvanish.com(US)
	);
	
	function beforeFilter() {
		// リクエスト値検証
		if (!$this->_invalidIp() ||
			!$this->_validation($this->request->query) || !$this->_validation($this->request->data)) {
//			$this->log($this->request->data, LOG_DEBUG);
			session_destroy();
			usleep(100000);
			exit;
		}

		// 検索とプラン詳細のみBOT対策をする
		if (($this->name == 'Searches' || $this->name == 'Plan') && $this->action == 'index') {
			$this->loadComponent('BotMeasures');
// TASK-10779 大量アクセス来てないから容量食うreCAPTCHA外したいと
//			if (!IS_PRODUCTION) {
				// 本番以外はreCAPTCHAしない（設定に各個人のローカルサーバIPを追加するのが大変）
				$this->BotMeasures->jsChallenge($this->Session, $this->request);
//			} else {
//				$this->BotMeasures->recaptchaChallenge($this->Session, $this->request);
//			}
		}

		// 検索ボックスを使用する時はOptionsManageComponentロード
		if ($this->use_searchbox) {
			$this->loadComponent('OptionsManage');
		}

		// Ajaxの場合はbeforeFilterの処理を通さない
		if ($this ->request->is( 'ajax' )) {
			if ($this->name == 'Reservations' && $this->action == 'prepare_payment') {
				if (isset($this->request->data['from_rentacar_client']) && $this->request->data['from_rentacar_client'] == 'true') {
					$this->set('fromRentacarClient', true);
				}
			}
			$this->set('is_dtravel', false);
			$this->set('is_etour', false);
			$this->set('yotpo_is_active', false);
			return;
		}

		//広告コード取得
		$advertising_cd = '';
		if (isset($this->request->query['ad'])) {
			$advertising_cd = $this->request->query['ad'];
		} elseif (isset($this->request->data['ad'])) {
			$advertising_cd = $this->request->data['ad'];
		} elseif (isset($this->request->query['mediacd'])) {
			$advertising_cd = $this->request->query['mediacd'];
		} elseif(isset($this->request->query['utm_campaign'])) {
			$advertising_cd = $this->request->query['utm_campaign'];
		}

		//広告コード保存
		if ($advertising_cd){
			$expire_add_time = 2592000;	// 60 * 60 * 24 * 30 = 2592000(seconds) = 30(days)
			$this->Cookie->write('advertising_cd',$advertising_cd,true,$expire_add_time);
			$_SESSION['advertising_cd'] = $advertising_cd;

			if ($this->name == 'Company') {
				$splitCd = explode('_', $advertising_cd);
				// レンタカー会社サイトからスカチケ会社ページにランディングした場合（広告コードfm-cl...付き）
				if ($splitCd[0] == Constant::FROM_CLIENT_AD_PREFIX) {
					$this->loadModel('Client');
					$client = $this->Client->getSpecificClientByLinkCd($splitCd[1]);
					$this->from_client_id = $client['Client']['id'];
				}
			}
		}

		//トラベルコODトラッキングコード取得
		$travelko_code = '';
		if ($advertising_cd == 'travelko_rc') {
			if (isset($this->request->query['travelko_code'])) {
				$travelko_code = $this->request->query['travelko_code'];
			} elseif (isset($this->request->data['travelko_code'])) {
				$travelko_code = $this->request->data['travelko_code'];
			}

			//トラベルコODトラッキングコード保存
			if ($travelko_code) {
				$expire_add_time = 2592000;	// 60 * 60 * 24 * 30 = 2592000(seconds) = 30(days)
				$this->Cookie->write('travelko_code', $travelko_code,true, $expire_add_time);
				$_SESSION['travelko_code'] = $travelko_code;
			}
		} else {
			// 広告コードがトラベルコではない場合、初期化する
			$this->Cookie->delete('travelko_code');
			$_SESSION['travelko_code'] = null;
		}

		// 検索一覧以後のページではパラメータを引き継ぐ
		$fromRentacarClient = false;
		if (($this->name == 'Searches' || $this->name == 'Plan') && $this->action == 'index') {
			if ((isset($this->request->query['from_rentacar_client']) && $this->request->query['from_rentacar_client'] == 'true')) {
				$fromRentacarClient = true;
			}
		} elseif ($this->name == 'Reservations') {
			if (isset($this->request->query['from_rentacar_client']) && $this->request->query['from_rentacar_client'] == 'true') {
				$fromRentacarClient = true;
			} elseif ($this->action == 'step1') {
				if ((isset($this->request->data['Login']['from_rentacar_client']) && $this->request->data['Login']['from_rentacar_client'] == 'true') ||
					(isset($this->request->data['Reservation']['from_rentacar_client']) && $this->request->data['Reservation']['from_rentacar_client'] == 'true')) {
					$fromRentacarClient = true;
				}
			} elseif ($this->action == 'step2' || $this->action == 'completion') {
				if ((isset($this->request->data['Reservation']['from_rentacar_client']) && $this->request->data['Reservation']['from_rentacar_client'] == 'true')) {
					$fromRentacarClient = true;
				}
			}
		}
		$this->set('fromRentacarClient', $fromRentacarClient);

		//BOC set yotpo config available for all view
		$this->set('yotpo_is_active', false);
		$this->set('use_yotpo', $this->use_yotpo);
		$this->set('use_yotpo_rating', $this->use_yotpo_rating);
		if ($this->use_yotpo) {
			$ratings = array();
			Configure::load('YotpoConfig', 'default');
			$yotpoConfig = Configure::read('Yotpo');
			$this->set('yotpo_is_active', $yotpoConfig['is_active']);
			if ($yotpoConfig['is_active']) {
				$this->set('yotpo_app_key', $yotpoConfig['app_key']);
				$this->set('yotpo_app_secret', $yotpoConfig['app_secret']);
				$this->set('yotpo_domain', $yotpoConfig['domain']);

				if ($this->use_yotpo_rating) {
					$this->loadModel('YotpoReview');
					$ratings_tmp = $this->YotpoReview->getRatingsGroupByClientId();
					$ratings = array();
					foreach ($ratings_tmp as $client_id => $rating) {
						$rating['rating'] = floatval($rating['rating']);
						$rating['rating'] = number_format($rating['rating'], 1, '.', '');
						$ratings[$client_id] = $rating;
					}
					$ratings_tmp = null;
				}
			}
			$this->set('ratings', $ratings);
		}
		//EOC set yotpo config available for all views

		// アフィリエイトB判定
		$this->set('is_afb', (!empty($_SESSION['advertising_cd']) && $_SESSION['advertising_cd'] === 'afb_RC'));
		// dトラベル判定
		$this->set('is_dtravel', (!empty($_SESSION['advertising_cd']) && strncmp($_SESSION['advertising_cd'], 'dtravel', 7) === 0));
		// イーツアー判定
		$this->set('is_etour', (!empty($_SESSION['advertising_cd']) && $_SESSION['advertising_cd'] === 'etour'));

		// URL末尾スラッシュなしの場合、スラッシュありに301リダイレクト
		if (isset($this->request->url)) {
			$uri = $this->request->url;
			if (!empty($uri) && substr($uri, -1) != '/' && empty($this->request->query)) {
				$this->redirect('/' . $uri . '/', 301);
			}
		}

		// スマホでのアクセス時にスマホのレイアウトを表示する
		if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
			// スマホページのチェック,ページがなかったらPCのページを表示
			$files = APP.'View'.DS.$this->viewPath.DS.'sp'.DS.'sp_'.$this->action.'.ctp';
			if (file_exists($files)) {
				$this->layout = 'sp_default';
				$this->action = 'sp_'.$this->action;
				$this->view = 'sp/sp_'.$this->view;
			}
		}

		$this->domain = $_SERVER['HTTP_HOST'];

		// Google reCAPTCHA v3 用のサイトキー
		$this->set('recaptcha_key', IS_PRODUCTION ? constant::RECAPTCHA_SITE_KEY_PROD : constant::RECAPTCHA_SITE_KEY_DEV);

		// フォームヘルパー用inputDefaults初期化
		$this->inputDefaults = array(
			'label'	=> false,
			'div'		=> false,
			'error' => false
		);

		$this->set('inputDefaults', $this->inputDefaults);
		$this->set('use_searchbox', $this->use_searchbox);

		//仮で、option_manage.jsを指定
		$this->set('new_js', $this->new_js);

		// 個別ページでtitle、metaが未設定の場合、以下の値（TOP画面より）を使用する
		$this->set('title_for_layout','格安レンタカー料金比較・予約（乗り捨て可）｜スカイチケットレンタカー');
		$this->set('description_for_layout','格安レンタカーの料金比較ならスカイチケット！一日¥1050〜　近くの1番安いレンタカーを簡単に比較。最安値で予約。乗り捨て、24時間営業店、当日の利用、1週間、1ヶ月以上の長期利用も検索できる。高級車、10人乗り、スタッドレスなど、車種やオプションの検索も簡単、便利！車レンタルをするなら、スカイチケットで。');
		$this->set('keywords','レンタカー,格安,比較,予約,安い,乗り捨て,沖縄,北海道,スカイチケット');

		//ログインしていなくてCookieにデータがある時オートログイン
		if (!_isLogin() && isset($_COOKIE[AUTOLOGIN_TOKEN_NAME])) {
			_doRememberMeLogin($_COOKIE[AUTOLOGIN_TOKEN_NAME]);
		}
		
		// GoogleのUAかどうか判定
		$google_agents = array('Chrome-Lighthouse', 'Google Page Speed Insights', 'Googlebot');
		foreach($google_agents as $agent)
		{
			if (strpos((string)$_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
				$this->is_google_user_agent = true;
				break;
			}
		}
		$this->set('is_google_user_agent', $this->is_google_user_agent);
	}

	// 複数同時呼び出しロードモデル
	public function loadModels() {
		$models = func_get_args();
		foreach ($models as $key => $model) {
			$this->loadModel($model);
		}
	}

	// コンポーネントをロードし初期化する
	public function loadComponent($component) {
		$this->$component = $this->Components->load($component);
		$this->$component->initialize($this);
	}

	// アプリの ?_app= がある場合にそのstring除去
	public function referer($default = null, $local = false) {
		$referer = parent::referer($default, $local);
		return preg_replace('/\?_app=[0-9]/', '', $referer);
	}

	// リクエストパラメータを検証する
	private function _validation($data = null) {
		// 値が存在しなければ何もしない
		if (empty($data)) {
			return true;
		}

		// 暫定対応
		if (isset($data['akamai-feo'])) {
			return false;
		}

		// 検証メイン処理
		$_execute = function ($value, $rule) {
			// 配列の値に対応
			$values = is_array($value) ? $value : array($value);

			foreach ($values as $v) {
				// 数値項目の場合
				if ($rule === null) {
					// 1文字以上で数値じゃなければ偽
					if (strlen($v) > 0 && !ctype_digit($v)) {
						return false;
					}

				// 数値項目以外の場合
				} else {
					// 正規表現にマッチしなければ偽
					if (!preg_match($rule, $v)) {
						return false;
					}
				}
			}

			return true;
		};

		foreach ((array)$this->_validation_targets as $arr_key => $arr_value) {
			// 値が存在しなければ何もしない
			if (empty($data[$arr_key])) {
				continue;
			}

			// 評価項目が配列でない場合
			if (!is_array($arr_value)) {
				$value = $data[$arr_key];

				if (!$_execute($value, $arr_value)) {
					return false;
				}
			// 評価項目が配列の場合
			} else {
				foreach ($arr_value as $k => $rule) {
					if (empty($data[$arr_key][$k])) {
						continue;
					}
					$value = $data[$arr_key][$k];

					if (!$_execute($value, $rule)) {
						return false;
					}
				}
			}
		}

		return true;
	}

	// 有効なIPか検証する
	private function _invalidIp() {
		// IPが存在しなければ何もしない
		if (!isset($_SERVER['REMOTE_ADDR'])) {
			return true;
		}
		return true;

		$ips = array(filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP));

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$xForwardedFor = explode(', ', filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR'));

			if ($ips[0] != $xForwardedFor[0]) {
				// 先頭IPが一致しない場合は国外からのアクセスは通さない
				if (IS_PRODUCTION && !filter_input(INPUT_SERVER, 'HTTP_JUDGE_JAPAN', FILTER_SANITIZE_NUMBER_INT)) {
					echo '500 Internal Server Error';
					return false;
				}
				$ips += $xForwardedFor;
			} else {
				$ips = $xForwardedFor;
			}
		}

		// 全経路のIPを調べる
		foreach ($ips as $ip) {
			if (!filter_var($ip, FILTER_VALIDATE_IP)) {
				continue;
			}

			foreach ($this->_ng_iplist as $ng_ip) {
				list($ng_ip, $mask) = explode('/', $ng_ip);

				$ng_long = ip2long($ng_ip) >> (32 - $mask);
				$ip_long = ip2long($ip) >> (32 - $mask);

				if ($ng_long == $ip_long) {
					echo '500 Internal Server Error';
					return false;
				}
			}
		}

		return true;
	}
}

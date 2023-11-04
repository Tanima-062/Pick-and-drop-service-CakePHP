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

	// 使用コンポーネントの登録
	public $components = array('Auth', 'Session', 'Paginator');

	public $uses = array('Client', 'Staff', 'Reservation', 'Statistic', 'Bbs', 'Page', 'PageViewingPermission');

	public $wday = array('日', '月', '火', '水', '木', '金', '土');

	function beforeFilter() {

		/**
		 * ログイン時の認証処理
		 * Authコンポーネントを使用
		 */
		// 認証処理
		$this->initAuth();

		// ログインユーザ情報取得
		$this->clientData = $this->Auth->user();

		// ログインユーザ情報の staff_id を id で上書き（混同しがちなので）
		$this->clientData['staff_id'] = $this->clientData['id'];

		// Ajaxの場合はbeforeFilterの処理を通さない
		if ($this->request->is('ajax')) {
			// 管理者でログインした場合
			if ($this->clientData['is_system_admin'] == 1) {
				$this->_overwriteClientData();
			}
			$this->set('clientData', $this->clientData);
			// キャッシュの無効化
			$this->response->disableCache();
			return;
		}

		// パスワード有効期限のチェック
		if (!$this->_checkPasswordExpired($this->clientData)) {
			$msg = '<strong>今回が初めてのログインであるか、パスワードの有効期限が切れています。<br>新しいパスワードを設定してください。</strong>';
			$this->Session->setFlash($msg, 'default', array('class' => 'alert alert-success'), 'auth');
			$this->redirect(array('controller' => 'staff', 'action' => 'edit'));
		}

		// ページ取得
		if ($this->clientData['is_system_admin'] == 1) {
			$pages = $this->Page->getAllPages();
		} else {
			$pages = $this->PageViewingPermission->getPages($this->clientData['id']);
		}
		$this->set('pages', $pages);

		// 管理者でログインした場合
		if ($this->clientData['is_system_admin'] == 1) {
			if ($this->name != 'Users' && $this->action != 'changeClientId' && $this->action != 'logout') {
				$this->_overwriteClientData();
				if (!$this->clientData['Client']['is_managed_package']) {
					if (isset($pages[3])) {// 3：売上確認
						foreach ($pages[3]['page'] as $k => $v) {
							if ($v['url'] == 'Sales/month_organized' || $v['url'] == 'Sales/daily_organized') {
								unset($pages[3]['page'][$k]);
							}
						}
						if (empty($pages[3])) {
							unset($pages[3]);
						}
					}
				}
				$this->set('pages', $pages);
			}
		// ログイン情報無い場合はログイン画面にリダイレクト
		} elseif (empty($this->clientData['id'])) {
			$url = Router::url();
			if (preg_match('/\/client\/users\/log(in|out)/i', $url)) {
				// スルーして処理続行
			} else {
				// ログイン画面にリダイレクト
				$this->redirect('/users/login');
			}
		// 一般ユーザーでログインした場合
		} else {

			// アクセス権限をチェック
			$accessFlg = false;
			$url = Router::url();
			$url = str_replace('/rentacar', '', $url);

			if (
				$url == '/client/' ||
				$url == '/client/users/login' ||
				$url == '/client/users/logout' ||
				$url == '/client/Tops' ||
				$this->name == 'CakeError' ) {
				$accessFlg = true;
			} else {

				if ($url == '/client/') {
					$url = 'Tops';
				}

				$url = str_replace('/client/', '', $url);
				$url = rtrim($url, '/');

				$explodeUrl = explode('/', $url);

				for ($i = count($explodeUrl) - 1; $i >= 0; $i--) {
					$_url = implode('/', $explodeUrl);

					// Controller名はバックキャメルに統一
					if ($i == 0) {
						$_url = Inflector::camelize($_url);
					}
					$pages = $this->Page->getPageInfo($_url);

					if (!empty($pages['Page']['id'])) {
						$accessFlg = $this->PageViewingPermission->checkPermission($pages['Page']['id'], $this->clientData['id']);
						break;
					}

					// 最後の要素を削除
					array_pop($explodeUrl);
				}
			}

			if (!$accessFlg) {
				$this->Session->setFlash('不正なアクセスです', 'default', array('class' => 'alert alert-danger'));
				$this->redirect('/Tops/');
			}
		}

		$this->set('currentClientId', $this->clientData['client_id']);
		if ($this->request->is('post') || $this->request->is('put')){
			if (isset($this->request->data['Custom'])) {
				if (isset($this->request->data['Custom']['current_client_id'])) {
					if ($this->request->data['Custom']['current_client_id'] != $this->clientData['client_id']) {
						$this->Session->setFlash('client_idが異なるため変更できませんでした', 'default', array('class' => 'alert alert-danger'));
						$this->redirect('/Tops/');
					}
				}
			}
		}

		$this->set('clientData', $this->clientData);

		// キャッシュの無効化
		$this->response->disableCache();

		// フォーム select用年月
		$currentYear = date('Y');
		$this->yearArray = array();
		for ($i = 2013; $i <= $currentYear + 1; $i++) {
			$this->yearArray[$i] = $i;
		}
		$this->monthArray = array();
		for ($i = 1; $i < 13; $i++) {
			$this->monthArray[sprintf("%02d", $i)] = sprintf("%02d", $i);
		}
		$this->dayArray = array();
		for ($i = 1; $i < 32; $i++) {
			$this->dayArray[sprintf("%02d", $i)] = sprintf("%02d", $i);
		}

	 	// delete_flg(削除フラグ)のオプション配列をセットする
		$this->setPublicFlg();
	}

	/**
	 * 認証処理
	 */
	protected function initAuth() {

		$this->groupModel = $this->Client;
		$this->userModel = $this->Staff;

		// 権限チェック コントローラごとに処理する
		$this->Auth->authorize =  array('Controller');

		$this->Auth->authenticate = array('Form' => array('userModel' => $this->userModel->name));

		// 認証設定
		$this->Auth->authenticate = array(
			'Form' => array(
				'userModel' => 'Staff',
				'fields' => array(
					'Staff.username' => 'username',
					'Staff.password' => 'password'
				),
				'scope' => array(
					'Staff.delete_flg' => 0
				),
			),
		);

		// ログインページのパス
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');

		// ログイン後の移動先
		$this->Auth->loginRedirect = array('controller' => 'Tops', 'action' => 'index');

		// ログアウト後の移動先
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');

		// 未ログイン時のメッセージ
		$this->Auth->authError = 'システムへのログインユーザー名とパスワードを入力して下さい。';
	}

	/**
	 * 権限チェック
	 * (AuthComponentのコールバック処理)
	 * @return boolean true:許可 / false:拒否
	 */
	function isAuthorized($user) {
		return true;
	}


	/**
	 * delete_flg(削除フラグ)のオプション配列をセットする
	 */
	private function setPublicFlg() {

		$deleteFlgOptions = array(
			0 => '公開',
			1 => '非公開',
			2 => '削除'
		);
		$isPublishedOptions = array(
			0 => '公開',
			1 => '非公開'
		);

		$this->set('deleteFlgOptions', $deleteFlgOptions);
		$this->set('isPublishedOptions', $isPublishedOptions);
	}

	public function diffUpdate() {

		$this->autoRender = false;
		$this->Statistic->diffDataDelete();
	}

	private function _overwriteClientData() {
		$newClientData = $this->Session->read('clientData');

		// cidが渡された時
		if (!empty($this->request->query['cid'])) {
			$clientId = $this->request->query['cid'];

			// セッション情報なしまたはクライアントIDが異なる場合は新しいクライアント情報をセッションに上書きする
			if (empty($newClientData) || $newClientData['Client']['id'] != $clientId) {
				$this->Client->recursive = -1;
				$newClientData = $this->Client->find('first', array('conditions' => array('id' => $clientId)));
				$this->Session->write('clientData', $newClientData);
			}
		}

		//セッション上書き
		if (!empty($newClientData)) {
			$this->clientData['client_id'] = $newClientData['Client']['id'];
			$this->clientData['Client'] = $newClientData['Client'];
		} else {
			$this->redirect(array('controller' => 'Users', 'action' => 'changeClientId'));
		}
	}

	/**
	 * パスワード有効期限チェック
	 * @param array $cdata
	 * @return boolean
	 */
	private function _checkPasswordExpired($cdata) {
		if (empty($cdata['password_modified']) || $this->name == 'CakeError') {
			return true;
		}

		// チェックしない画面
		$allows = array(
			'Staff' => array(
				'edit' => true,
			),
			'Users' => array(
				'login' => true,
				'logout' => true,
				'changeClientId' => true,
			),
		);

		if (!isset($allows[$this->name][$this->action])) {
			$modified = strtotime($cdata['password_modified']);
			
			if (strtotime(Constant::PASSWORD_EXPIRATION) > $modified) {
				return false;
			}
		}

		return true;
	}

}
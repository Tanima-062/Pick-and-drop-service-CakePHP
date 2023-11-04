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
 * @copyright	 Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link		  http://cakephp.org CakePHP(tm) Project
 * @package	   app.Controller
 * @since		 CakePHP(tm) v 0.2.9
 * @license	   MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');
App::uses('Sanitize', 'Utility');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	// 使用コンポーネントの登録
	public $components = array(
		'Auth',
		'Session',
		'Paginator'
	);
	public $uses = array(
		'Client',
		'Staff',
//		'Bbs'
	);
	public $helpers = array(
		'Session',
		'Html' => array(
			'className' => 'TwitterBootstrap.BootstrapHtml'
		),
		'Form' => array(
			'className' => 'TwitterBootstrap.BootstrapForm'
		),
		'Paginator' => array(
			'className' => 'TwitterBootstrap.BootstrapPaginator'
		)
	);
	public $client;
	public $staff;

	function beforeFilter() {
		ini_set('memory_limit', '2048M');


		// ログインユーザ情報取得
		$this->cdata = $this->Auth->user();

		// ログインユーザ情報の staff_id を id で上書き（混同しがちなので）
		$this->cdata['staff_id'] = $this->cdata['id'];

		if ($this->name != 'Users' && $this->action != 'login') {
			if (empty($this->cdata['is_system_admin'])) {
				$this->redirect('/users/login/');
			}
		}

		$this->Auth->userModel = 'Staff';
		$this->Auth->fields = array(
			'username' => 'username',
			'password' => 'password'
		);

		// 認証方式
		$this->Auth->authenticate = array(
			'Form' => array(
				'userModel' => 'Staff',
				'scope' => array(
					'Staff.is_system_admin' => 1,
				),
			)
				);

		// ログイン後の移動先
		$this->Auth->loginRedirect = array(
			'controller' => 'Dashboard',
			'action' => 'index'
		);

		// ログアウト後の移動先
		$this->Auth->logoutRedirect = array(
			'controller' => 'users',
			'action' => 'login'
		);

		// ログインページのパス
		$this->Auth->loginAction = array(
			'controller' => 'users',
			'action' => 'login'
		);

		// 未ログイン時のメッセージ
		$this->Auth->authError = 'あなたのお名前とパスワードを入力して下さい。';

		// 認証不要のページの指定
		$this->Auth->allow('isAuthorized');

		// パスワード有効期限のチェック
		if (!$this->_checkPasswordExpired($this->cdata)) {
			$msg = '<strong>今回が初めてのログインであるか、パスワードの有効期限が切れています。<br>新しいパスワードを設定してください。</strong>';
			$this->Session->setFlash($msg, 'default', array('class'=>'alert alert-success'), 'auth');
			$this->redirect('/Users/edit/' . $this->cdata['id']);
		}

		$isPublishedOptions = array(
			'0' => '非公開',
			'1' => '公開',
		);

		$deleteFlgOptions = array(
			0 => '公開',
			1 => '非公開'
		);

		$this->set(compact('isPublishedOptions', 'deleteFlgOptions'));
		$this->set('cdata', $this->cdata);

	}

	function beforeRender() {

	}

	function isAuthorized($user) {

		$clientId = $this->Auth->user('client_id');
		$staffId = $this->Auth->user('id');

		$this->client = $this->Client->find('first', array(
			'conditions' => array(
				'id' => $clientId
			)
		));
		$this->staff = $this->Staff->find('first', array(
			'conditions' => array(
				'id' => $staffId
			)
		));

		$this->set('Client', $this->client);
		$this->set('Staff', $this->Auth->staff);

		return true;
	}

	/**
	 * パスワード有効期限チェック
	 *
	 * @param array $cdata
	 * @return boolean
	 */
	private function _checkPasswordExpired($cdata) {
		if (empty($cdata['password_modified']) || $this->name == 'CakeError') {
			return true;
		}
		
		// チェックしない画面
		$allows = array(
			'Users' => array(
				'edit' => true,
				'login' => true,
				'logout' => true,
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

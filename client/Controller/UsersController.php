<?php

App::uses('AppController', 'Controller');

/**
 * Users Controller
 */
class UsersController extends AppController {

	var $useTable = false;
	// 使用モデルの指定
	public $uses = array('Staff', 'Client', 'Commodity');

	function beforeFilter() {

		// 親クラスのbeforeFilterの読み込み
		parent::beforeFilter();

		// 認証不要のページの指定
		$this->Auth->allow('login', 'logout', 'changeClientId');
	}

	public function login() {

		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$clientData = $this->Auth->user();
				//管理者の場合クライアントログインページへリダイレクト
				if ($clientData['is_system_admin'] == 1) {
					$this->redirect(array('action' => 'changeClientId'));
				} else {
					$this->redirect($this->Auth->redirect());
				}
			} else {
				$this->Session->setFlash('認証に失敗しました。ユーザ名かパスワードが間違っています。', '', array(), 'auth');
			}
		}
	}

	public function logout() {
		$this->Session->destroy();
		$this->redirect($this->Auth->logout());
	}

	/**
	 * 管理者でログインしたらリダイレクトされるページ
	 */
	public function changeClientId() {

		$clientData = $this->Auth->user();
		if (empty($clientData['id']) || $clientData['is_system_admin'] != 1) {
			$this->redirect(array('controller' => 'Users', 'action' => 'logout'));
		}

		$this->Client->recursive = -1;
		if ($this->request->is('post')) {
			$clientId = $this->request->data['ClientData'];

			if (!empty($clientId['client'])) {
				$clientData = $this->Client->find('first', array('conditions' => array('id' => $clientId['client'])));
				$this->Session->write('clientData', $clientData);
				$this->redirect($this->Auth->redirect());
			}
		}

		$clientAllDatas = $this->Client->find('all', array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Client.sp_logo_image',
			),
			'conditions' => array(
				'Client.id >' => 1,
				'Client.delete_flg' => 0,
			),
			'order' => array(
				'FIELD(Client.area_type,1,2,0)',
				'Client.id ASC',
			),
		));

		foreach ((array)$clientAllDatas as $client) {
			$clientList[$client['Client']['id']] = '<img width="48" height="48" src="/rentacar/img/logo/square/' . $client['Client']['id'] . '/' . $client['Client']['sp_logo_image'] . '"><br>' . $client['Client']['name'];
		}

		//営業所リストをセット
		$this->set('clientFormOptions', array(
			'type' => 'radio',
			'options' => $clientList,
			'empty' => '',
			'lable' => false,
			'div' => false,
			'legend' => false,
			'before' => '<div class="radio span3">',
			'after' => '</div>',
			'separator' => '</div><div class="radio span3">',
			'class' => 'test',
		));

		// カテゴリ未設定のオプションを検索
		$unsetCategories = $this->Client->find('list', array(
			'fields' => array(
				'Privilege.name',
				'Client.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'privileges',
					'alias' => 'Privilege',
					'conditions' => array(
						'Privilege.client_id = Client.id'
					),
				),
			),
			'conditions' => array(
				'Client.id >' => 1,
				'Client.delete_flg' => 0,
				'Privilege.delete_flg' => 0,
				'Privilege.option_category_id' => 0,
			),
			'recursive' => -1
		));

		// URL未設定の営業所を検索
		$unsetOfficeUrls = $this->Client->find('list', array(
			'fields' => array(
				'Office.name',
				'Client.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => array(
						'Office.client_id = Client.id'
					),
				),
			),
			'conditions' => array(
				'Client.id >' => 1,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Office.url' => null,
			),
			'recursive' => -1
		));

		// 市区町村未設定の営業所を検索
		$unsetOfficeCityIds = $this->Client->find('list', array(
			'fields' => array(
				'Office.name',
				'Client.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => array(
						'Office.client_id = Client.id'
					),
				),
			),
			'conditions' => array(
				'Client.id >' => 1,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'OR' => array(
					'Office.zipcode' => null,
					'Office.city_id' => null,
				),
			),
			'recursive' => -1
		));

		// SIPPコード未設定の商品を検索
		$unsetSippCodes = array();
		$unsetSippCodes = $this->Commodity->find('list', array(
			'fields' => array(
				'Commodity.name',
				'Client.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'clients',
					'alias' => 'Client',
					'conditions' => array(
						'Client.id = Commodity.client_id'
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'commodity_items',
					'alias' => 'CommodityItem',
					'conditions' => array(
						'CommodityItem.commodity_id = Commodity.id'
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'commodity_terms',
					'alias' => 'CommodityTerm',
					'conditions' => array(
						'CommodityTerm.commodity_id = Commodity.id'
					),
				),
			),
			'conditions' => array(
				'Client.id >' => 1,
				'Client.delete_flg' => 0,
				'Commodity.is_published' => 1,
				'Commodity.delete_flg' => 0,
				'CommodityItem.delete_flg' => 0,
				'CommodityItem.sipp_code' => null,
				'CommodityTerm.available_to >= NOW()',
				'CommodityTerm.delete_flg' => 0,
			),
			'limit' => 30,
			'recursive' => -1
		));

		$isExistAlert = (!empty($unsetCategories) || !empty($unsetOfficeUrls) || !empty($unsetOfficeCityIds) || !empty($unsetSippCodes));
		$this->set(compact('isExistAlert', 'unsetCategories', 'unsetOfficeUrls', 'unsetOfficeCityIds', 'unsetSippCodes'));
	}

}

<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

	// 使用モデルの指定（省略可）
	public $uses = array(
		'Staff', 'Client','StaffStockGroup','StockGroup','Office','Page','PageViewingPermission', 'OfficeSelectionPermission',
		'PageCategory', 'Prefecture'
	);

	function beforeFilter() {

		// 親クラスのbeforeFilterの読み込み
		parent::beforeFilter();

		// 認証不要のページの指定
		$this->Auth->allow('login', 'logout');
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {

		$this->Staff->recursive = 0;

		$conditions = array();

		$clientList = $this->Client->find('list');
		$this->set('clientList', $clientList);

		$roleList = array(1 => '一般スタッフ',
						  2 => '社内管理者',
						  3 => 'クライアント管理者');
		$this->set('roleList', $roleList);

		if(!empty($this->request->query['client_id'])) {
			$conditions['client_id'] = $this->request->query['client_id'];
			$this->request->data['User']['client_id'] = $this->request->query['client_id'];
		} 
		if(!empty($this->request->query['role'])) {
			if($this->request->query['role'] == 1){
				$conditions['is_system_admin'] = 0;
				$conditions['is_client_admin'] = 0;
			} elseif($this->request->query['role'] == 2){
				$conditions['is_system_admin'] = 1;
				$conditions['is_client_admin'] = 1;
			} elseif($this->request->query['role'] == 3){
				$conditions['is_system_admin'] = 1;
				$conditions['is_client_admin'] = 0;
			}
			$this->request->data['User']['role'] = $this->request->query['role'];
		} 

		$this->paginate = array('conditions'=> $conditions,'limit' => 50);

		$this->set('staffs', $this->paginate());
	}

	public function add() {
		if ($this->request->is('post')) {
			$data = $this->request->data;
			$data['Staff']['staff_id'] = $this->cdata['id'];
			switch ($data['Staff']['is_admin']) {
				case '0':
					$data['Staff']['is_system_admin'] = 0;
					$data['Staff']['is_client_admin'] = 0;
					break;
				case '1':
					$data['Staff']['is_system_admin'] = 0;
					$data['Staff']['is_client_admin'] = 1;
					break;
				case '2':
					$data['Staff']['is_system_admin'] = 1;
					$data['Staff']['is_client_admin'] = 1;
					break;
			}
			unset($data['Staff']['is_admin']);

			$this->Staff->create();
			// 新規登録されたstaff.idを取得
			$newData = $this->Staff->save($data);
			if ($newData) {
				$savePVP = true;
				// クライアント管理者、一般スタッフの時に処理
				if ($data['Staff']['is_system_admin'] == 0 ) {
					// 管理画面TOP画面 権限追加
					$saveData[0]['staff_id'] = $newData['Staff']['id'];
					$saveData[0]['page_id'] = 1;
					// パスワード変更画面 権限追加
					$saveData[1]['staff_id'] = $newData['Staff']['id'];
					$saveData[1]['page_id'] = 24;
					$save = $this->PageViewingPermission->saveAll($saveData);
					if (empty($save)) {
						$savePVP = false;
					}
				}
				if ($savePVP == true) {
					$this->Session->setFlash('スタッフを追加しました','default',array('class'=>'alert alert-success'));
				} else {
					$this->Session->setFlash('スタッフの権限追加に失敗しました','default',array('class'=>'alert alert-error'));
					// staffの作成だけは成功しているのでリダイレクトはさせる
				}
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('スタッフの追加に失敗しました','default',array('class'=>'alert alert-error'));

			}
		}

		$this->set('clientList', $this->Client->find('list', array('conditions' => array('delete_flg' => 0), 'recursive' => -1)));
	}

	function login() {
		$sideLock = 1;
		$this->set('sideLock',$sideLock);

		$this->Auth->logout();
		unset($this->viewVars['cdata']);

		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash('ID及びPWが正しくありません、入力し直して下さい。','default',array('class'=>'alert alert-error'));
			}
		}
	}

	// ログアウトアクション（認証が不要なページ）
	function logout() {
		$this->Auth->logout();
		unset($this->viewVars['cdata']);
	}


	function edit($id = null){

		$this->Staff->id = $id;
		if (!$this->Staff->exists()) {
			throw new NotFoundException(__('Invalid staff'));
		}

 		if ($this->request->is('post') || $this->request->is('put')) {

			$data['id'] = $id;
			$data['name'] = $this->request->data['Staff']['name'];
			$data['staff_id'] = $this->cdata['id'];
 			if ($this->cdata['id'] != $id) {
				$data['delete_flg'] = $this->request->data['Staff']['delete_flg'];
				if ($data['delete_flg'] == 1) {
					if ($this->Staff->field('delete_flg') == 0) {
						$data['deleted'] = date("Y-m-d H:i:s", time());
					}
				} else {
					$data['deleted'] = null;
				}
 			}

			$update = $this->request->data['Staff'];

			// パスワード入力が無ければ担当者名変更
			if (empty($update['new_password'])) {
				if ($this->Staff->save($data)) {
					$this->Session->setFlash('担当者名を修正しました','default',array('class'=>'alert alert-success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('担当者名の修正に失敗しました','default',array('class'=>'alert alert-error'));
					return;
				}
			}

			if (!preg_match('/' . Constant::PATTERN_STRICT_PASSWORD . '/', $update['new_password'])) {
				$this->Session->setFlash(__('新しいパスワードはアルファベット大文字、小文字、数字を含む8文字以上で入力してください'), 'default', array('class'=>'alert alert-danger'));
				return;
			}

			//入力したパスワードと確認用パスワードが一致するかチェック
			if($update['new_password'] != $update['re_password']) {
				$this->Session->setFlash('新しいパスワードと確認用パスワードが一致しません','default',array('class'=>'alert alert-error'));
				return;
			}

			//ハッシュ化
			$password = Security::hash($update['new_password'], null, true);

			//登録されてるパスワードと整合性チェック
			$this->Staff->recursive = -1;
			$clientInfo = $this->Staff->find('first',array('conditions'=>array('id'=>$id,'password'=>$password)));

			if(!empty($clientInfo['Staff']['id'])) {
				$this->Session->setFlash(__('現在のパスワードと新しいパスワードが同じです'), 'default', array('class'=>'alert alert-danger'));
				return;
			}

			$data['password'] = $update['new_password'];
			 if ($this->cdata['id'] != $id) {
				// ログインユーザー以外の場合はパスワード更新日時をリセット
				$data['password_modified'] = date('Y/m/d H:i:s', strtotime(Constant::PASSWORD_EXPIRATION));
			 } else {
				$data['password_modified'] = date('Y/m/d H:i:s');
			 }
			
			if($this->Staff->save($data)) {
				if ($this->cdata['id'] == $id) {
					// 変更した内容でセッションを更新する
					$this->Session->write('Auth.User.password_modified', $data['password_modified']);
				}
				$this->Session->setFlash('担当者名とパスワードを修正しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			}

		} else {
			$loginStaff = $this->Staff->find('first', array(
					'conditions' => array(
						'Staff.id' => $id,
					),
					'recursive' => -1,
				)
			);
			$this->request->data = $loginStaff;
		}

	}

	// 営業所権限設定
	public function office_authority_management($staffId) {

		if (($this->request->is('post') || $this->request->is('put')) && !isset($this->data['refine'])) {

			// 物理削除
			$this->OfficeSelectionPermission->deleteAll(array('staff_id'=>$staffId),false);

			$officeIds = json_decode($this->request->data['Staff']['json_office_id'], true);
			if(!empty($officeIds)) {
				$i = 0;
				$saveData = array();
				foreach($officeIds as $officeId) {
					if(!empty($officeId)) {
						$saveData[$i]['office_id'] = $officeId;
						$saveData[$i]['staff_id'] = $staffId;
						$i++;
					}
				}
				if (!empty($saveData)) {
					$save = $this->OfficeSelectionPermission->saveAll($saveData);
					if(!empty($save)) {
						$this->Session->setFlash('編集しました','default',array('class'=>'alert alert-success'));
					} else {
						$this->Session->setFlash('編集に失敗しました','default',array('class'=>'alert alert-error'));
					}
				} else {
					$this->Session->setFlash('編集しました','default',array('class'=>'alert alert-success'));
				}
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash('編集しました','default',array('class'=>'alert alert-success'));
			}

		} else {
			$this->request->data['Staff']['office_id'] = $this->OfficeSelectionPermission->find('list',array('conditions'=>array('staff_id'=>$staffId),'fields'=>array('office_id','office_id')));
		}

		// 都道府県リスト
		$prefectureList = $this->Prefecture->getPrefectureList();
		$this->set('prefectureList', $prefectureList);

		// スタッフデータ
		$staffData = $this->Staff->getStaffData($staffId);
		$this->set('staffData', $staffData);

		// クライアントID
		$clientId = $staffData['Staff']['client_id'];

		// 営業所データ
		$officeDatas = $this->Office->getOfficePrefecture($clientId);
		$offices = Hash::combine($officeDatas, '{n}.Office.id', '{n}.Office', '{n}.Prefecture.id');
		$this->set('offices',$offices);

	}

	// メニュー権限設定
	public function page_authority_management($staffId) {

		if (($this->request->is('post') || $this->request->is('put')) && !isset($this->data['refine'])) {

			// 物理削除
			$this->PageViewingPermission->deleteAll(array('staff_id'=>$staffId),false);

			$this->request->data['Staff']['page_id'][1] = 1;
			// パスワード変更画面は強制でチェック状態保持
			$this->request->data['Staff']['page_id'][24] = 24;
			if (!empty($this->request->data['Staff']['page_id'])) {
				$i = 0;
				$saveData = array();
				foreach ($this->request->data['Staff']['page_id'] as $pageId) {
					if (!empty($pageId)) {
						$saveData[$i]['page_id'] = $pageId;
						$saveData[$i]['staff_id'] = $staffId;
						$i++;
					}
				}
				if (!empty($saveData)) {
					$save = $this->PageViewingPermission->saveAll($saveData);
					if (!empty($save)) {
						$this->Session->setFlash('編集しました','default',array('class'=>'alert alert-success'));
					} else {
						$this->Session->setFlash('編集に失敗しました','default',array('class'=>'alert alert-error'));
					}
				} else {
					$this->Session->setFlash('編集しました','default',array('class'=>'alert alert-success'));
				}
				$this->redirect($this->referer());
			}

		} else {
			$this->request->data['Staff']['page_id'] = $this->PageViewingPermission->find('list', array('conditions' => array('staff_id' => $staffId), 'fields' => array('page_id', 'page_id')));
		}

		// ページカテゴリ
		$pageCategories = $this->PageCategory->getPageCategoryData();
		$this->set('pageCategories', $pageCategories);

		// スタッフデータ
		$staffData = $this->Staff->getStaffData($staffId);
		$this->set('staffData', $staffData);
	}

	function isAuthorized($user) {
		parent::isAuthorized($user);
	}
}

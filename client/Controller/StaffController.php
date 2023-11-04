<?php
class StaffController extends AppController {

	public $uses = array('Staff');


	function beforeFilter() {
		// 親クラスのbeforeFilterの読み込み
		parent::beforeFilter();
	}

	/**
	 * 編集アクション
	 *
	 * @param string $id ユーザマスタID
	 * @return void
	 */
	public function edit() {

		//登録チェック
		$this->Staff->id = $this->clientData['id'];
		if (!$this->Staff->exists()) {
			// メッセージ
			$this->Session->setFlash(__('不正なログインです'), 'default', array('class'=>'alert alert-danger'),'auth');
			// 一覧へリダイレクト
			$this->redirect(array('controller'=>'Users','action' => 'logout'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$update = $this->request->data['Staff'];

			// パスワード入力チェック
			if (empty($update['new_password'])) {
				$this->Session->setFlash(__('新しいパスワードを入力してください'), 'default', array('class'=>'alert alert-danger'),'auth');
				return;
			}

			if (!preg_match('/' . Constant::PATTERN_STRICT_PASSWORD . '/', $update['new_password'])) {
				$this->Session->setFlash(__('新しいパスワードはアルファベット大文字、小文字、数字を含む8文字以上で入力してください'), 'default', array('class'=>'alert alert-danger'),'auth');
				return;
			}

			if ($update['password'] == $update['new_password']) {
				$this->Session->setFlash(__('現在のパスワードと新しいパスワードが同じです'), 'default', array('class'=>'alert alert-danger'),'auth');
				return;
			}

			//入力したパスワードと確認用パスワードが一致するかチェック
			if($update['new_password'] != $update['re_password']) {
				$this->Session->setFlash(__('新しいパスワードと確認用パスワードが一致しません'), 'default', array('class'=>'alert alert-danger'),'auth');
				return;
			}

			//ハッシュ化
			$password = Security::hash($update['password'], null, true);

			//登録されてるパスワードと整合性チェック
			$this->Staff->recursive = -1;
			$clientInfo = $this->Staff->find('first',array('conditions'=>array('id'=>$this->clientData['id'],'password'=>$password)));

			//登録されてるパスワードと一致したらパスワード変更
			if(empty($clientInfo['Staff']['id'])) {
				$this->Session->setFlash(__('現在のパスワードが一致しません'), 'default', array('class'=>'alert alert-danger'),'auth');
				return;
			}

			$changePass['id'] = $clientInfo['Staff']['id'];
			$changePass['password'] = Security::hash($update['new_password'], null, true);
			$changePass['password_modified'] = date('Y/m/d H:i:s');
			$changePass['staff_id'] = $this->clientData['id'];

			if($this->Staff->save($changePass)) {
				// 変更した内容でセッションを更新する
				$this->Session->write('Auth.User.password_modified', $changePass['password_modified']);
				$this->Session->setFlash(__('パスワードを変更しました'), 'default', array('class'=>'alert alert-success'),'auth');
				$this->redirect(array('controller' => 'Tops','action' => 'index'));
			} else {
				$this->Session->setFlash(__('パスワード変更に失敗しました'), 'default', array('class'=>'alert alert-danger'),'auth');
			}
		}
	}
}

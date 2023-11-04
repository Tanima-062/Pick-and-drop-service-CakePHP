<?php
App::uses('AppController', 'Controller');
/**
 * ClientTemplates Controller
 *
 * @property ClientTemplate $ClientTemplate
 */
class ClientTemplatesController extends AppController {

	public $uses = array('ClientTemplate');

	public function beforeFilter() {
		parent::beforeFilter();

		// 管理者権限を持っていないログインユーザー判定
		if (empty($this->clientData['is_system_admin'])) {
			$this->loginStaffId = $this->clientData['id'];
		}

		/**
		 * 編集・削除対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit'),$this->action)) {
			//編集・削除対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集・削除対象IDとクライアントIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->ClientTemplate->clientCheck($this->passedArgs[0],$this->clientData['Client']['id'], $this->loginStaffId)) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}
		$this->set('is_check_user', true);
	}

	public function index() {

		/**
		 * 並び順を保存するを押されたとき
		 */
		if($this->request->is('post')) {
			if (!empty($this->loginStaffId)) {
				if(!empty($this->request->data['ClientTemplate']['order'])) {

					//テンプレートリスト取得
					$templateList = $this->ClientTemplate->getClientTemplateList($this->clientData['Client']['id'], $this->loginStaffId);

					$order = $this->request->data['ClientTemplate']['order'];
					$orderArray = explode(',',$order);
					$saveData = array();
					$i = 1;
					foreach($orderArray as $val) {
						if(empty($templateList[$val])) {
							continue;
						}

						$saveData[$i]['id'] = $val;
						$saveData[$i]['client_id'] = $this->clientData['Client']['id'];
						$saveData[$i]['sort'] = $i;
						$i++;
					}

					if($this->ClientTemplate->saveAll($saveData)) {
						$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
						$this->redirect(array('action'=>'index'));

					} else {
						$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
					}
				}
			} else {
				$this->Session->setFlash('管理者はソート操作ができません。','default',array('class'=> 'alert alert-error'));
				$this->redirect(array('action' => 'index'));
			}
		}

		$conditions = array(
					'conditions'=>array(
							'ClientTemplate.delete_flg' => 0,
							'ClientTemplate.client_id'=>$this->clientData['Client']['id']
					),
					'order'=>'sort'
		);
		// 管理者以外のログインユーザー
		if (!empty($this->loginStaffId)) {
			$conditions['conditions']['ClientTemplate.login_staff_id'] = $this->loginStaffId;
		}

		$this->paginate = $conditions;

		$this->ClientTemplate->recursive = -1;
		$this->set('clientTemplates', $this->paginate());

		$staffList = $this->Staff->find('list', array(
				'fields' => array(
						'Staff.id',
						'Staff.name'
				),
				'conditions' => array(
						'OR' => array(
								'Staff.client_id' => $this->clientData['Client']['id'],
								'Staff.is_system_admin' => 1,
						),
				),
				'recursive' => -1,
		));
		$this->set('staffList', $staffList);

	}

	public function add() {

		if ($this->request->is('post')) {

			$saveData = $this->data['ClientTemplate'];

			$saveData['sort'] = 1000;
			$saveData['client_id'] = $this->clientData['Client']['id'];
			$saveData['staff_id'] = $this->clientData['id'];
			$saveData['login_staff_id'] = $this->clientData['id'];

			if ($this->ClientTemplate->save($saveData)) {

				$this->Session->setFlash('登録が正しく完了しました。','default',array('class'=>'alert alert-info'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。','default',array('class'=>'alert alert-error'));
			}
		}
	}

	public function edit($id) {

		if ($this->request->is('put')) {

			$saveData = $this->data['ClientTemplate'];
			$saveData['client_id'] = $this->clientData['Client']['id'];
			$saveData['staff_id'] = $this->clientData['id'];

			if ($this->ClientTemplate->save($saveData)) {

				$this->Session->setFlash('登録が正しく完了しました。','default',array('class'=>'alert alert-info'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。','default',array('class'=>'alert alert-error'));
			}
		}

		$options = array(
				'conditions' => array('id' => $id),
				'recursive' => -1
		);

		$this->request->data = $this->ClientTemplate->find('first',$options);

	}

}

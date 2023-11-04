<?php
App::uses('AppController', 'Controller');
/**
 * LateNightFees Controller
 *
 * @property LateNightFee $LateNightFee
 */
class LateNightFeesController extends AppController {

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array();
/**
 * Components
 *
 * @var array
 */
	public $components = array('Session');

	public function beforeFilter() {

		parent::beforeFilter();

		/**
		 * 加算回数オプションをセット
		 */
		$priceAdditionFlgOptions = array(
				0=>'出発・返却共に加算',
				1=>'出発・返却いずれかで１回のみ加算',
		);

		$this->set('priceAdditionFlgOptions',$priceAdditionFlgOptions);

		/**
		 * 編集・削除対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit','delete'),$this->action)) {
			//編集・削除対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集・削除対象IDとクライアントIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->LateNightFee->clientCheck($this->passedArgs[0],$this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}
		$isClientAdmin = $this->clientData['is_client_admin'];
		$staffId = $this->clientData['id'];
		$scopeList = array(0 => '共通');
		if ($isClientAdmin) {
			$scopeList += $this->Staff->getStaffList($this->clientData['client_id']);
		} else {
			$scopeList[$staffId] = $this->clientData['name'];
		}
		$this->set(compact('scopeList', 'isClientAdmin', 'staffId'));
		$this->set('is_check_user', true);
	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->LateNightFee->recursive = 0;

		$conditions['conditions']['LateNightFee.delete_flg'] = 0;
		$conditions['conditions']['LateNightFee.client_id'] = $this->clientData['client_id'];
		if (!$this->clientData['is_client_admin']) {
			$conditions['conditions']['OR'] = array(
				array('LateNightFee.scope' => 0),
				array('LateNightFee.scope' => $this->clientData['id'])
			);
		}
		$this->paginate = $conditions;
		$this->set('lateNightFees', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {

			$this->LateNightFee->create();
			$this->request->data['LateNightFee']['client_id'] = $this->clientData['client_id'];
			if ($this->LateNightFee->save($this->request->data)) {
				$this->Session->setFlash( '深夜手数料を登録しました。', 'default', array( 'class' => 'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash( '深夜手数料の登録に失敗しました。', 'default', array( 'class' => 'alert alert-error'));
			}
		}
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->LateNightFee->id = $id;

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['LateNightFee']['client_id'] = $this->clientData['client_id'];
			if ($this->LateNightFee->save($this->request->data)) {
				$this->Session->setFlash( '深夜手数料を編集しました。', 'default', array('class' => 'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash( '深夜手数料の編集に失敗しました。', 'default', array('class' => 'alert alert-error'));
			}
		} else {
			$this->request->data = $this->LateNightFee->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		//論理削除
		$saveData= array('id'=>$id,'delete_flg'=>1);
		if ($this->LateNightFee->save($saveData)) {
				$this->Session->setFlash( '深夜手数料を削除しました。', 'default', array( 'class' => 'alert alert-success')
				);
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash( '深夜手数料の削除に失敗しました。', 'default', array( 'class' => 'alert alert-error'));
		$this->redirect(array('action' => 'index'));
	}
}

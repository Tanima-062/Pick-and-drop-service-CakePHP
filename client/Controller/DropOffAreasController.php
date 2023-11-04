<?php
App::uses('AppController', 'Controller');
/**
 * DropOffAreas Controller
 *
 * @property DropOffArea $DropOffArea
 */
class DropOffAreasController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();

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
				if(!$this->DropOffArea->clientCheck($this->passedArgs[0],$this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}
		$this->set('is_check_user', true);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->DropOffArea->recursive = -1;
		$this->paginate = array('conditions'=>array('client_id'=>$this->clientData['Client']['id'],'delete_flg'=>array(0,1)));
		$this->set('dropOffAreas', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->DropOffArea->create();
			$this->request->data['DropOffArea']['client_id'] = $this->clientData['Client']['id'];
			$this->request->data['DropOffArea']['staff_id'] = $this->clientData['id'];

			if ($this->DropOffArea->save($this->request->data)) {
				$this->Session->setFlash('乗捨エリアを登録しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('乗捨エリアの登録に失敗しました','default',array('class'=>'alert alert-error'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['DropOffArea']['client_id'] = $this->clientData['Client']['id'];
			$this->request->data['DropOffArea']['staff_id'] = $this->clientData['id'];
			$this->request->data['DropOffArea']['deleted'] = ($this->request->data['DropOffArea']['delete_flg'] == '2') ? date('Y-m-d H:i:s') : null;

			if ($this->DropOffArea->save($this->request->data)) {
				$this->Session->setFlash('乗捨エリアを編集しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('乗捨エリアの編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$options = array('conditions' => array('DropOffArea.' . $this->DropOffArea->primaryKey => $id));
			$this->request->data = $this->DropOffArea->find('first', $options);
		}
	}

/**
 * 乗捨エリアを削除する
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {

		//論理削除
		$saveData = array(
			'id' => $id,
			'staff_id' => $this->clientData['id'],
			'delete_flg' => 2,
			'deleted' => date('Y-m-d H:i:s'),
		);

		if ($this->DropOffArea->save($saveData)) {
			$this->Session->setFlash('乗捨エリアを削除しました','default',array('class'=>'alert alert-success'));
			$this->redirect('/DropOffAreas/');
		}

		$this->Session->setFlash('乗捨エリアの削除に失敗しました。','default',array('class'=>'alert alert-error'));
		$this->redirect('/DropOffAreas/');
	}

}

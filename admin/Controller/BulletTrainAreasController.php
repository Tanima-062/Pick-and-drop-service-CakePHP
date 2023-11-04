<?php
App::uses('AppController', 'Controller');
/**
 * BulletTrainAreas Controller
 *
 * @property BulletTrainArea $BulletTrainArea
 */
class BulletTrainAreasController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();

	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->BulletTrainArea->recursive = -1;
		$this->set('bulletTrainAreas', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->BulletTrainArea->exists($id)) {
			throw new NotFoundException(__('Invalid bullet train area'));
		}
		$options = array('conditions' => array('BulletTrainArea.' . $this->BulletTrainArea->primaryKey => $id));
		$this->set('bulletTrainArea', $this->BulletTrainArea->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		if ($this->request->is('post')) {
			$this->BulletTrainArea->create();
			$this->request->data['BulletTrainArea']['client_id'] = $this->clientData['Client']['id'];
			if ($this->BulletTrainArea->save($this->request->data)) {
				$this->Session->setFlash('新幹線エリアを登録しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('新幹線エリアの登録に失敗しました','default',array('class'=>'alert alert-error'));
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
		if (!$this->BulletTrainArea->exists($id)) {
			throw new NotFoundException(__('Invalid bullet train area'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->BulletTrainArea->save($this->request->data)) {
				$this->Session->setFlash('新幹線エリアを編集しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('新幹線エリアの編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$options = array('conditions' => array('BulletTrainArea.' . $this->BulletTrainArea->primaryKey => $id));
			$this->request->data = $this->BulletTrainArea->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {

		//論理削除
		$saveData= array('id'=>$id,'delete_flg'=>1);
		$bulletTrainArea = $this->BulletTrainArea->save($saveData);
		if ($bulletTrainArea) {
			$this->Session->setFlash('乗捨エリアを削除しました','default',array('class'=>'alert alert-success'));
			$this->redirect('/bullet_train_areas/');
		}

		$this->Session->setFlash('乗捨エリアの削除に失敗しました。','default',array('class'=>'alert alert-error'));
//		$this->redirect('/bullet_train_areas/');

	}
}

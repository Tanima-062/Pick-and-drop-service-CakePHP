<?php
App::uses('AppController', 'Controller');
/**
 * Automakers Controller
 *
 * @property Automaker $Automaker
 */
class AutomakersController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Automaker->recursive = 0;
		$this->set('automakers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Automaker->id = $id;
		if (!$this->Automaker->exists()) {
			throw new NotFoundException(__('Invalid automaker'));
		}
		$this->set('automaker', $this->Automaker->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->request->data['Automaker']['staff_id'] = $this->cdata['id'];
			if ($this->request->data['Automaker']['delete_flg'] == 1) {
				$this->request->data['Automaker']['deleted'] = date("Y-m-d H:i:s", time());
			}
			$this->Automaker->create();
			if ($this->Automaker->save($this->request->data)) {
				$this->Session->setFlash( '自動車メーカーを追加しました', 'default', array( 'class' => 'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash( '自動車メーカーの追加に失敗しました', 'default', array( 'class' => 'alert alert-error'));
			}
		}
		$staffs = $this->Automaker->Staff->find('list');
		$this->set(compact('staffs'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Automaker->id = $id;
		if (!$this->Automaker->exists()) {
			throw new NotFoundException(__('Invalid automaker'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Automaker']['staff_id'] = $this->cdata['id'];
			if ($this->request->data['Automaker']['delete_flg'] == 1) {
				if ($this->Automaker->field('delete_flg') == 0) {
					$this->request->data['Automaker']['deleted'] = date("Y-m-d H:i:s", time());
				}
			} else {
				$this->request->data['Automaker']['deleted'] = null;
			}
			if ($this->Automaker->save($this->request->data)) {
				$this->Session->setFlash( '自動車メーカーを編集しました', 'default', array( 'class' => 'alert alert-success'));

				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash( '自動車メーカーの編集に失敗しました', 'default', array( 'class' => 'alert alert-error'));
			}
		} else {
			$this->request->data = $this->Automaker->read(null, $id);
		}
		$staffs = $this->Automaker->Staff->find('list');
		$this->set(compact('staffs'));
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Automaker->id = $id;
		if (!$this->Automaker->exists()) {
			throw new NotFoundException(__('Invalid automaker'));
		}
		if ($this->Automaker->delete()) {
			$this->Session->setFlash(__('Automaker deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Automaker was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

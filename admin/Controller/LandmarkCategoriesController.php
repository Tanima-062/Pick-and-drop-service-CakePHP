<?php
App::uses('AppController', 'Controller');
/**
 * LandmarkCategories Controller
 *
 * @property LandmarkCategory $LandmarkCategory
 */
class LandmarkCategoriesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();

	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->LandmarkCategory->recursive = -1;
		$this->set('LandmarkCategories', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->LandmarkCategory->exists($id)) {
			throw new NotFoundException(__('Invalid landmark category'));
		}
		$options = array('conditions' => array('LandmarkCategory.' . $this->LandmarkCategory->primaryKey => $id));
		$this->set('LandmarkCategory', $this->LandmarkCategory->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		if ($this->request->is('post')) {
			$this->LandmarkCategory->create();
			if ($this->LandmarkCategory->save($this->request->data)) {
				$this->Session->setFlash('ランドマークカテゴリを登録しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('ランドマークカテゴリの登録に失敗しました','default',array('class'=>'alert alert-error'));
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
		if (!$this->LandmarkCategory->exists($id)) {
			throw new NotFoundException(__('Invalid landmark category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->LandmarkCategory->save($this->request->data)) {
				$this->Session->setFlash('ランドマークカテゴリを編集しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('ランドマークカテゴリの編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$options = array('conditions' => array('LandmarkCategory.' . $this->LandmarkCategory->primaryKey => $id));
			$this->request->data = $this->LandmarkCategory->find('first', $options);
		}
	}

}

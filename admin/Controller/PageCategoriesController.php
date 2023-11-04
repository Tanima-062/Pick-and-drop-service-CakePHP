<?php
App::uses('AppController', 'Controller');
/**
 * PageCategories Controller
 *
 * @property PageCategory $PageCategory
 */
class PageCategoriesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {

		//並び順を保存するを押されたとき
		if($this->request->is('post')) {
			if(!empty($this->request->data['PageCategory']['sort'])) {
				$order = $this->request->data['PageCategory']['sort'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {
					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->PageCategory->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}

		$this->PageCategory->recursive = -1;
		$options = array('conditions'=>array('PageCategory.delete_flg'=>0),'order'=>'sort asc');
		$this->set('pageCategories', $this->PageCategory->find('all',$options));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->PageCategory->id = $id;
		if (!$this->PageCategory->exists()) {
			throw new NotFoundException(__('Invalid page category'));
		}
		$this->set('pageCategory', $this->PageCategory->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->PageCategory->create();
			$this->request->data['PageCategory']['sort'] = $this->PageCategory->getSortNum();
			if ($this->PageCategory->save($this->request->data)) {
				$this->Session->setFlash('登録に成功しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('登録に失敗しました','default',array('class'=>'alert alert-danger'));
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
		$this->PageCategory->id = $id;
		if (!$this->PageCategory->exists()) {
			throw new NotFoundException(__('Invalid page category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->PageCategory->save($this->request->data)) {
				$this->Session->setFlash('編集しました','default',array('class'=> 'alert alert-success'));

				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('編集に失敗しました','default',array('class'=>'alert alert-danger'));
			}
		} else {
			$this->request->data = $this->PageCategory->read(null, $id);
		}
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
		$this->PageCategory->id = $id;
		if (!$this->PageCategory->exists()) {
			throw new NotFoundException(__('Invalid page category'));
		}
		$saveData = array('id'=>$id,'delete_flg'=>1);
		if ($this->PageCategory->save($saveData)) {
			$this->Session->setFlash('削除しました','default',array('class'=> 'alert alert-success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('削除に失敗しました','default',array('class'=> 'alert alert-danger'));
		$this->redirect(array('action' => 'index'));
	}
}

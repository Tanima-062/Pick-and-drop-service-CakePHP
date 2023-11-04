<?php
App::uses('AppController', 'Controller');
/**
 * Pages Controller
 *
 * @property Page $Page
 */
class PagesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {

		//並び順を保存するを押されたとき
		if($this->request->is('post')) {
			if(!empty($this->request->data['Page']['sort'])) {
				$order = $this->request->data['Page']['sort'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {
					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->Page->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}

		$this->Page->recursive = 0;
		$options = array('conditions'=>array('Page.delete_flg'=>0),'order'=>'Page.sort asc');
		$this->set('pages', $this->Page->find('all',$options));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Page->id = $id;
		if (!$this->Page->exists()) {
			throw new NotFoundException(__('Invalid page'));
		}
		$this->set('page', $this->Page->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Page->create();
			$this->request->data['Page']['sort'] = $this->Page->getSortNum();
			if ($this->Page->save($this->request->data)) {
				$this->Session->setFlash('登録に成功しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('登録に失敗しました','default',array('class'=>'alert alert-danger'));
			}
		}
		$pageCategories = $this->Page->PageCategory->find('list',array('conditions'=>array('delete_flg'=>0), 'order'=>'sort asc'));
		$this->set(compact('pageCategories', 'staffs'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Page->id = $id;
		if (!$this->Page->exists()) {
			throw new NotFoundException(__('Invalid page'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Page->save($this->request->data)) {
				$this->Session->setFlash('編集しました','default',array('class'=> 'alert alert-success'));

				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('編集に失敗しました','default',array('class'=>'alert alert-danger'));
			}
		} else {
			$this->request->data = $this->Page->read(null, $id);
		}
		$pageCategories = $this->Page->PageCategory->find('list',array('conditions'=>array('delete_flg'=>0), 'order'=>'sort asc'));
		$this->set(compact('pageCategories'));
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
		$this->Page->id = $id;
		if (!$this->Page->exists()) {
			throw new NotFoundException(__('Invalid page'));
		}
		$saveData = array('id'=>$id,'delete_flg'=>1);
		if ($this->Page->save($saveData)) {
			$this->Session->setFlash('削除しました','default',array('class'=> 'alert alert-success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('削除に失敗しました','default',array('class'=> 'alert alert-danger'));
		$this->redirect(array('action' => 'index'));
	}
}

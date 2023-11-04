<?php
App::uses('AppController', 'Controller');
/**
 * PublicHolidays Controller
 *
 * @property PublicHoliday $PublicHoliday
 */
class PublicHolidaysController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->PublicHoliday->recursive = 0;
		$this->paginate = array('conditions'=>array('delete_flg'=>0),'limit'=>100);
		$this->set('publicHolidays', $this->paginate());
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
	  if ($this->request->is('post')) {
	    $this->PublicHoliday->create();
	    if ($this->PublicHoliday->save($this->request->data)) {
	      $this->Session->setFlash('登録が完了しました。','default',array('class'=>'alert alert-success'));

	      $this->redirect(array('action' => 'index'));
	    } else {
	      $this->Session->setFlash('登録に失敗しました。','default',array('class'=>'alert alert-danger'));
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
	  if (!$this->PublicHoliday->exists($id)) {
	    throw new NotFoundException(__('Invalid public holiday'));
	  }
	  if ($this->request->is('post') || $this->request->is('put')) {
	    if ($this->PublicHoliday->save($this->request->data)) {
	      $this->Session->setFlash('編集が完了しました。','default',array('class'=>'alert alert-success'));
	      $this->redirect(array('action' => 'index'));
	    } else {
	      $this->Session->setFlash('編集に失敗しました。','default',array('class'=>'alert alert-danger'));
	    }
	  } else {
	    $options = array('conditions' => array('PublicHoliday.' . $this->PublicHoliday->primaryKey => $id));
	    $this->request->data = $this->PublicHoliday->find('first', $options);
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
	  $this->PublicHoliday->id = $id;
	  if (!$this->PublicHoliday->exists()) {
	    throw new NotFoundException(__('Invalid public holiday'));
	  }
	  if ($this->PublicHoliday->delete()) {
	    $this->Session->setFlash('削除しました。','default',array('class'=>'alert alert-success'));
	    $this->redirect(array('action' => 'index'));
	  }
	  $this->Session->setFlash(__('Public holiday was not deleted'));
	  $this->redirect(array('action' => 'index'));
	}
}

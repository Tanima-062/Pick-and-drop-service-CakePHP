<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'imageResizeUpLoad');

/**
 * Equipment Controller
 *
 * @property Equipment $Equipment
 */
class EquipmentController extends AppController {

	public $components = array('FileUp');
	public $uses = array('Equipment');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('optionCategories', Hash::combine(Constant::optionCategories(), '{n}.id', '{n}.name'));
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {

		// 並び順を保存するを押されたとき
		if ($this->request->is('post')) {

			if (!empty($this->request->data['Equipment']['sort'])) {
				$order = $this->request->data['Equipment']['sort'];
				$orderArray = explode(',', $order);
				$saveData = array();
				$i = 1;
				foreach ($orderArray as $val) {

					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i ++;
				}

				if ($this->Equipment->saveAll($saveData)) {
					$this->Session->setFlash('並び順を保存しました。', 'default', array('class' => 'alert alert-success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。', 'default', array('class' => 'alert alert-error'));
				}
			}
		}

		$this->Equipment->recursive = 0;
		$equipment = $this->Equipment->find('all', array(
			'conditions' => array('Equipment.delete_flg' => 0),
			'order' => 'Equipment.sort asc'
		));
		$this->set('equipment', $equipment);
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->Equipment->recursive = 0;
		$this->Equipment->id = $id;
		if (!$this->Equipment->exists()) {
			throw new NotFoundException(__('Invalid equipment'));
		}
		$this->set('equipment', $this->Equipment->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {

			$data = $this->request->data['Equipment'];
			$data['sort'] = 1000;
			$data['staff_id'] = $this->cdata['id'];

			if ($this->Equipment->save($data)) {
				$this->Session->setFlash('登録が正しく完了しました。', 'default', array('class' => 'alert alert-info'));

				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('入力に失敗しました、各項目を見なおして下さい。', 'default', array('class' => 'alert alert-error'));
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
		$this->Equipment->recursive = 0;
		$this->Equipment->id = $id;
		if (!$this->Equipment->exists()) {
			throw new NotFoundException(__('Invalid equipment'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			$data = $this->request->data['Equipment'];
			$data['staff_id'] = $this->cdata['id'];

			if ($this->Equipment->save($data)) {
				$this->Session->setFlash('登録が正しく完了しました。', 'default', array(
					'class' => 'alert alert-info'
				));

				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('入力に失敗しました、各項目を見なおして下さい。', 'default', array(
					'class' => 'alert alert-error'
				));
			}
		} else {
			$this->request->data = $this->Equipment->read(null, $id);
		}
	}

}

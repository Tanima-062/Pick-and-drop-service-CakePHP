<?php
App::uses('AppController', 'Controller');
/**
 * CarTypes Controller
 *
 * @property CarType $CarType
 */
class CarTypesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		//並び順を保存するを押されたとき
		if ($this->request->is('post')) {
			if (!empty($this->request->data['Sort']['sort'])) {
				$order = $this->request->data['Sort']['sort'];
				$orderArray = explode(',', $order);
				$saveData = array();
				$i = 1;
				foreach ($orderArray as $val) {

					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if ($this->CarType->saveAll($saveData)) {
					$this->Session->setFlash('並び順を保存しました。', 'default', array('class' => 'alert alert-success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。', 'default', array('class' => 'alert alert-error'));
				}
			}
		}

		$this->CarType->recursive = -1;
		$this->Paginator->settings = array(
			'fields' => array(
				'CarType.id',
				'CarType.name',
				'CarType.sort',
				'CarType.capacity',
				'CarType.description',
				'CarType.travelko_id',
			),
			'conditions' => array(
				'CarType.delete_flg' => 0,
			),
			'order' => array(
				'CarType.sort',
				'CarType.id',
			),
			'limit' => 500,
		);
		$this->set('carTypes', $this->paginate());
	}


/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->request->data['CarType']['staff_id'] = $this->cdata['id'];
			$this->CarType->create();
			if ($this->CarType->save($this->request->data)) {
				$this->Session->setFlash('車両タイプを追加しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('車両タイプの追加に失敗しました。','default',array('class'=>'alert alert-error'));
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
		if (!$this->CarType->exists($id)) {
			throw new NotFoundException(__('Invalid car type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['CarType']['staff_id'] = $this->cdata['id'];
			if ($this->request->data['CarType']['delete_flg'] == 1) {
				$this->request->data['CarType']['deleted'] = date("Y-m-d H:i:s", time());
			}
			if ($this->CarType->save($this->request->data)) {
				$this->Session->setFlash('車両タイプを修正しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('車両タイプの修正に失敗しました','default',array('class'=>'alert alert-alert'));
			}
		} else {
			$options = array('conditions' => array('CarType.' . $this->CarType->primaryKey => $id));
			$this->request->data = $this->CarType->find('first', $options);
		}
	}

}

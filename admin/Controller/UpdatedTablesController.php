<?php
App::uses('AppController', 'Controller');
/**
 * UpdatedTables Controller
 *
 * @property UpdatedTable $UpdatedTable
 */
class UpdatedTablesController extends AppController {

	public $uses = array('UpdatedTable', 'Staff', 'Client');

	function beforeFilter()
	{
		parent::beforeFilter();

		$categoryList = array(
			'顧客管理' => '顧客管理',
			'在庫管理' => '在庫管理',
			'在庫管理【引継ぎ】' => '在庫管理【引継ぎ】',
			'在庫管理【売止め】' => '在庫管理【売止め】',
			'在庫管理【満車処理】' => '在庫管理【満車処理】',
			'商品一覧' => '商品一覧',
			'商品グループ管理' => '商品グループ管理',
			'キャンペーン期間管理' => 'キャンペーン期間管理',
			'オプション管理' => 'オプション管理',
			'基本情報管理' => '基本情報管理',
			'営業所一覧' => '営業所一覧',
			'特別営業時間' => '特別営業時間',
			'在庫管理地域一覧' => '在庫管理地域一覧',
			'車種一覧' => '車種一覧',
			'車両クラス管理' => '車両クラス管理',
			'免責補償料金設定' => '免責補償料金設定',
			'乗捨エリア一覧' => '乗捨エリア一覧',
			'乗捨料金一覧' => '乗捨料金一覧',
			'深夜手数料' => '深夜手数料',
			'パスワード変更' => 'パスワード変更',
		);
		$this->set('categoryList', $categoryList);
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {

		$options = array();
		$options['order'] ='UpdatedTable.id desc';

		if ($this->request->is('get')) {
			//getで来た場合 検索フォームの値を保持
			$this->request->data['UpdatedTables'] = array_diff_key($this->request->params['named'], array(
					'sort'=>0, 'direction'=>0, 'page'=>0));
			$data = $this->request->params['named'];
		} else {
			$data = $this->request->data['UpdatedTables'];
		}

		$options['conditions'] = array();
		if (!empty($data['staff_id'])) {
			$options['conditions'] = array('UpdatedTable.staff_id'=>$data['staff_id']);
		}

		// デフォルトの抽出範囲は当月以降とする
		if (empty($data['from']['year']) || !$data['from']['month']) {
			$data['from']['year'] = date('Y');
			$data['from']['month'] = date('m');
			$data['from']['day'] = '01';

			$this->request->data['UpdatedTables']['from'] = $data['from'];
		}

		if (!empty($data['from']['year']) && $data['from']['month']) {
			if (!empty($data['from']['day'])) {
				$options['conditions'] += array('UpdatedTable.created >='=>$data['from']['year'] . "-" . $data['from']['month'] . "-" . $data['from']['day'] . " 00:00:00");
			} else {
				$options['conditions'] += array('UpdatedTable.created >='=>$data['from']['year'] . "-" . $data['from']['month'] . "-01 00:00:00");
			}
		}

		if (!empty($data['to']['year']) && $data['to']['month']) {
			if (!empty($data['from']['day'])) {
				$options['conditions'] += array('UpdatedTable.created <='=>$data['to']['year'] . "-" . $data['to']['month'] . "-" . $data['to']['day'] . " 23:59:59");
			} else {
				$options['conditions'] += array('UpdatedTable.created <='=>$data['to']['year'] . "-" . $data['to']['month'] . "-01 23:59:59");
			}
		}

		if (!empty($data['category'])) {
			$options['conditions'] += array('UpdatedTable.category'=>$data['category']);
		}

		if (!empty($data['content'])) {
			$options['conditions'] += array('UpdatedTable.content LIKE' => '%'.$data['content'].'%');
		}

		if (!empty($data['client_id'])) {
			$options['conditions'] += array('UpdatedTable.client_id'=>$data['client_id']);
		}

		$this->paginate = $options;

		$this->UpdatedTable->recursive = 0;
		$this->set('updatedTables', $this->paginate());

		$this->set('staffList', $this->Staff->find('list'));
		$this->set('clientList', $this->Client->find('list', array('order' => 'sort asc')));

		$this->set('postConditions', $this->request->data['UpdatedTables']);
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->UpdatedTable->id = $id;
		if (!$this->UpdatedTable->exists()) {
			throw new NotFoundException(__('Invalid updated table'));
		}
		$this->set('updatedTable', $this->UpdatedTable->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->UpdatedTable->create();
			if ($this->UpdatedTable->save($this->request->data)) {
				$this->Session->setFlash(__('The updated table has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The updated table could not be saved. Please, try again.'));
			}
		}
		$tables = $this->UpdatedTable->Table->find('list');
		$operations = $this->UpdatedTable->Operation->find('list');
		$staffs = $this->UpdatedTable->Staff->find('list');
		$this->set(compact('tables', 'operations', 'staffs'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->UpdatedTable->id = $id;
		if (!$this->UpdatedTable->exists()) {
			throw new NotFoundException(__('Invalid updated table'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->UpdatedTable->save($this->request->data)) {
				$this->Session->setFlash(__('The updated table has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The updated table could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->UpdatedTable->read(null, $id);
		}
		$tables = $this->UpdatedTable->Table->find('list');
		$operations = $this->UpdatedTable->Operation->find('list');
		$staffs = $this->UpdatedTable->Staff->find('list');
		$this->set(compact('tables', 'operations', 'staffs'));
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
		$this->UpdatedTable->id = $id;
		if (!$this->UpdatedTable->exists()) {
			throw new NotFoundException(__('Invalid updated table'));
		}
		if ($this->UpdatedTable->delete()) {
			$this->Session->setFlash(__('Updated table deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Updated table was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}

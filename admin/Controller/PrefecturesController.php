<?php
App::uses('AppController', 'Controller');
/**
 * Prefectures Controller
 *
 * @property Prefecture $Prefecture
 */
class PrefecturesController extends AppController {

	public $uses = array('Prefecture', 'Recommend');

/**
 * 一覧ページ
 *
 * @return void
 */
	public function index() {

		//並び順を保存するを押されたとき
		if($this->request->is('post')) {
			if(!empty($this->request->data['Prefecture']['sort'])) {
				$order = $this->request->data['Prefecture']['sort'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {

					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->Prefecture->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}


		$this->Prefecture->recursive = 0;
		$prefectures = $this->Prefecture->find('all',array('conditions'=>array('Prefecture.delete_flg'=>0),'order'=>'Prefecture.sort asc,Prefecture.id asc'));
		$this->set('prefectures',$prefectures);
	}

/**
 * 新規追加
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Prefecture->create();
			$this->request->data['Prefecture']['staff_id'] = $this->cdata['id'];
			if ($this->Prefecture->save($this->request->data)) {
				$this->Session->setFlash('都道府県を登録しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('都道府県の登録に失敗しました','default',array('class'=>'alert alert-success'));
			}
		}
		$staffs = $this->Prefecture->Staff->find('list');
		$this->set(compact('staffs'));
	}

/**
 * 編集ページ
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$saveFlg = true;
		$this->Prefecture->id = $id;
		if (!$this->Prefecture->exists()) {
			throw new NotFoundException(__('Invalid prefecture'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->request->data['Prefecture']['recommend_random_flg'] != '1') {
				if ($this->Recommend->checkPeriodDuplicate($id)) {
					$this->Session->setFlash('掲載枠と期間が重複しているレコメンドがあるためランダム以外の保存ができません。','default',array('class'=>'alert alert-error'));
					$saveFlg = false;
				}
			}
			if ($saveFlg) {
				$this->request->data['Prefecture']['staff_id'] = $this->cdata['id'];
				if ($this->Prefecture->save($this->request->data)) {
					$this->Session->setFlash('都道府県を編集しました','default',array('class'=>'alert alert-success'));

					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('都道府県の編集に失敗しました','default',array('class'=>'alert alert-error'));
				}
			}
		} else {
			$this->request->data = $this->Prefecture->read(null, $id);
		}
		$staffs = $this->Prefecture->Staff->find('list');
		$this->set(compact('staffs'));
	}

/**
 * 削除メソッド
 * 論理削除
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$saveData['id'] = $id;
		$saveData['staff_id'] = $this->cdata['id'];
		$saveData['delete_flg'] = 1;
		$saveData['deleted'] = date("Y-m-d H:i:s", time());

		if($this->Prefecture->save($saveData)) {
			$this->Session->setFlash('削除しました','default',array('class'=>'alert alert-success'));
		} else {
			$this->Session->setFlash('削除に失敗しました','default',array('class'=>'alert alert-error'));
		}

		$this->redirect($this->referer());

	}
}

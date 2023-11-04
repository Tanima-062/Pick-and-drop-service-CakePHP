<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'imageResizeUpLoad');

class TopContentsController extends AppController {

	public $uses = array('Content');

	public function beforeFilter() {
		parent::beforeFilter();

		$this->set('categoryList', $this->Content->getContentsCategoryList());
		// AppControllerの定義を上書き
		$this->set('deleteFlgOptions', array(
			0 => '削除しない',
			1 => '削除する',
		));
		$this->set('urlPrefix', 'https://'.(IS_PRODUCTION ? 'skyticket.jp' : 'jp.skyticket.jp').'/');
	}

	public function index() {
		$this->Content->recursive = -1;

		$conditions = array('delete_flg' => 0);

		// カテゴリ
		if (!empty($this->request->query['contents_category_id'])) {
			$conditions['contents_category_id'] = $this->request->query['contents_category_id'];
		}

		$this->request->data['Content'] = $this->request->query;
		$this->paginate = array('conditions' => $conditions, 'order' => 'Content.id asc');

		$this->set('contents', $this->paginate());
	}

	public function add() {

		if ($this->request->is('post')) {
			$saveData = $this->request->data['Content'];

			if (!empty($this->request->data['Content']['image_tmp']['tmp_name'])) {
				// 画像リサイズアップロード
				$this->ImageResize = new ImageResizeUpLoad();
				$upLoadDir = 'contents' . DS . 'top' . DS;
				$imgInfo = getimagesize($this->request->data['Content']['image_tmp']['tmp_name']); // サイズ変えない
				$imgName = $this->ImageResize->resizeUpLoad($this->request->data['Content']['image_tmp'], $upLoadDir, null, $imgInfo);

				if ($imgName) {
					$saveData['image'] = $imgName;
				}
			}

			$saveData['staff_id']  = $this->cdata['id'];
			if ($saveData['delete_flg'] == 1) {
				$saveData['deleted'] = date("Y-m-d H:i:s", time());
			}

			if ($this->Content->save($saveData)) {
				$this->Session->setFlash('トップコンテンツを新規登録しました', 'default', array('class' => 'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('トップコンテンツの新規登録に失敗しました', 'default', array('class' => 'alert alert-error'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->Content->exists($id)) {
			throw new NotFoundException(__('Invalid top content'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$saveData = $this->request->data['Content'];

			if (!empty($this->request->data['Content']['image_tmp']['tmp_name'])) {
				// 画像リサイズアップロード
				$this->ImageResize = new ImageResizeUpLoad();
				$upLoadDir = 'contents' . DS . 'top' . DS;
				$imgInfo = getimagesize($this->request->data['Content']['image_tmp']['tmp_name']); // サイズ変えない
				$imgName = $this->ImageResize->resizeUpLoad($this->request->data['Content']['image_tmp'], $upLoadDir, null, $imgInfo);

				if ($imgName) {
					$saveData['image'] = $imgName;
				}
			}

			$saveData['staff_id']  = $this->cdata['id'];
			// deleteは別メソッドでやることになったけど、あって困るものでもなし
			if ($saveData['delete_flg'] == 1) {
				$this->Content->id = $id;
				if ($this->Content->field('delete_flg') == 0) {
					$saveData['deleted'] = date("Y-m-d H:i:s", time());
				}
			} else {
				$saveData['deleted'] = null;
			}

			if ($this->Content->save($saveData)) {
				$this->Session->setFlash('トップコンテンツを編集しました', 'default', array('class' => 'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('トップコンテンツの編集に失敗しました', 'default', array('class' => 'alert alert-error'));
			}
		} else {
			$this->Content->recursive = -1;
			$content = $this->Content->find('first', array('conditions' => array('Content.id'=> $id, 'Content.delete_flg' => 0)));
			$this->request->data['Content'] = $content['Content'];
		}
	}

	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$saveData['id'] = $id;
		$saveData['staff_id'] = $this->cdata['id'];
		$saveData['delete_flg'] = 1;
		$saveData['deleted'] = date("Y-m-d H:i:s", time());

		if ($this->Content->save($saveData)) {
			$this->Session->setFlash('削除しました', 'default', array('class' => 'alert alert-success'));
		} else {
			$this->Session->setFlash('削除に失敗しました', 'default', array('class' => 'alert alert-error'));
		}

		$this->redirect($this->referer());
	}
}
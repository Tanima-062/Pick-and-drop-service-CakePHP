<?php
App::uses('AppController', 'Controller');
/**
 * Areas Controller
 *
 * @property Area $Area
 */
class AreasController extends AppController {

	public $uses = array('Area','Prefecture');

	public function beforeFilter() {
		parent::beforeFilter();

		$prefectureList = $this->Prefecture->getPrefectureList();
		$this->set('prefectureList',$prefectureList);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		//並び順を保存するを押されたとき
		if($this->request->is('post')) {
			if(!empty($this->request->data['Sort']['sort'])) {
				$order = $this->request->data['Sort']['sort'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {

					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->Area->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}


		$params = array(
			'conditions' => array(
				'Area.delete_flg' => 0,
			),
			'order'=>'sort asc',
			'recursive' => -1,
		);
		$areas = $this->Area->find('all',$params);
		$this->set('areas', $areas);

	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Area->id = $id;
		if (!$this->Area->exists()) {
			throw new NotFoundException(__('Invalid area'));
		}
		$area = $this->Area->find('all', array(
			'conditions' => array(
				'Area.id' => $id,
			),
			'recursive' => -1,
		));
		$this->set('area', $area);
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Area->create();
			$this->request->data['Area']['staff_id'] = $this->cdata['id'];
			$this->request->data['Area']['sort'] = 1000;
			if ($this->Area->save($this->request->data)) {
				$this->Session->setFlash('エリアを追加しました。','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('エリア登録に失敗しました。','default',array('class'=>'alert alert-error'));
			}
		}

		$this->set(compact('staffs', 'offices'));

	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Area->id = $id;
		if (!$this->Area->exists()) {
			throw new NotFoundException(__('Invalid area'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Area']['staff_id'] = $this->cdata['id'];
			if ($this->Area->save($this->request->data)) {
				$this->Session->setFlash('エリアを編集しました。','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('エリア編集に失敗しました。','default',array('class'=>'alert alert-error'));

			}
		} else {
			$options = array(
				'conditions' => array(
					'Area.id' => $id,
				),
				'recursive' => -1,
			);
			$this->request->data = $this->Area->find('first', $options);
		}
		$staffs = $this->Area->Staff->find('list');
		$offices = $this->Area->Office->find('list');
		$this->set(compact('staffs', 'offices'));
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
		$this->Area->id = $id;
		if (!$this->Area->exists()) {
			throw new NotFoundException(__('Invalid area'));
		}
		$saveData = array(
			'id' => $id,
			'staff_id' => $this->cdata['id'],
			'delete_flg' => 1,
			'deleted' => date("Y-m-d H:i:s", time()),
		);
		if ($this->Area->Save($saveData)) {
			$this->Session->setFlash('削除しました。','default',array('class'=>'alert alert-success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('削除に失敗しました。','default',array('class'=>'alert alert-error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * 画像アップロード処理
	 */
	public function imageUploadFile() {
		$this->layout = false;
		if (!empty($this->data['image'])) {
			foreach ($this->data['image'] as $key => $value) {
				// 保存場所
				$path = WWW_ROOT.'../img/mypage/';

				// 画像判定
				$type = @exif_imagetype($value['tmp_name']);
				if (empty($errorFile)) {
					$errorFile = '';
				}
				if (!in_array($type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG), true)) {
					$errorFile .= '&nbsp;'.$value['name'];
					continue;
				}

				// エラーチェック
				$isUploaded = $this->isUploadedFile($value);
				if ($isUploaded === true) {
					move_uploaded_file($value['tmp_name'], $path.$value['name']);
				}
			}
		}
		if (!empty($errorFile)) {
			$this->Session->setFlash('('.$errorFile.')形式が未対応です。');
		}
		$this->redirect(array('controller' => 'Areas', 'action' => 'index'));
	}

	/**
	 * アップロードエラーチェック
	 * @param unknown $params
	 */
	public function isUploadedFile($params) {
		if ((isset($params['error']) && $params['error'] == 0) || (!empty( $params['tmp_name']) && $params['tmp_name'] != 'none')) {
			return is_uploaded_file($params['tmp_name']);
		}
		return false;
	}
}

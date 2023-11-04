<?php
App::uses('AppController', 'Controller');
App::uses('SkyticketData', 'Vendor');

/**
 * Landmarks Controller
 *
 * @property airport $Landmark
 */
class LandmarksController extends AppController {

	public $uses = array('Landmark','LandmarkCategory','Prefecture');

	public function beforeFilter() {
		parent::beforeFilter();

		$prefectureList = $this->Prefecture->getPrefectureList();
		$landmarkCategoryList = $this->LandmarkCategory->getLandmarkCategoryList();
		$this->set(compact('prefectureList', 'landmarkCategoryList'));
	}

	public function index() {

		//並び順を保存するを押されたとき
		if($this->request->is('post')) {
			if(!empty($this->request->data['Landmark']['sort'])) {
				$order = $this->request->data['Landmark']['sort'];
				$orderArray = explode(',',$order);
				$saveData = array();
				$i = 1;
				foreach($orderArray as $val) {

					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if($this->Landmark->saveAll($saveData)) {
					$this->Session->setFlash( '並び順を保存しました。', 'default', array( 'class' => 'alert alert-success'));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。','default',array('class'=> 'alert alert-error'));
				}
			}
		}

		$this->Landmark->recursive = 0;
		$conditions = array(
			// 'fields' => array(
			// ),
			'conditions' => array(
				'Landmark.name <>' => '',
				// 'Landmark.landmark_category_id' => 1,
				'LandmarkCategory.delete_flg' => 0,
			),
			'order' => array(
				'Landmark.sort', 'Landmark.id',
			),
		);
		$landmarks = $this->Landmark->find('all', $conditions);
		$this->set('landmarks',$landmarks);
	}

	public function add() {

		if($this->request->is('post')) {
			$saveData = $this->request->data['Landmark'];
			$saveData['staff_id']  = $this->cdata['id'];

			if($this->Landmark->save($saveData)) {
				$this->Session->setFlash('ランドマークを追加しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('ランドマークの登録に失敗しました','default',array('class'=>'alert alert-error'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->Landmark->exists($id)) {
			throw new NotFoundException(__('Invalid landmark'));
		}

		if($this->request->is(array('put', 'post'))) {
			$saveData = $this->request->data['Landmark'];
			$saveData['staff_id']  = $this->cdata['id'];

			if ($saveData['delete_flg'] == 1) {
				$this->Landmark->id = $id;
				if ($this->Landmark->field('delete_flg') == 0) {
					$saveData['deleted'] = date("Y-m-d H:i:s", time());
				}
			} else {
				$saveData['deleted'] = null;
			}

			if($this->Landmark->save($saveData)) {
				$this->Session->setFlash('ランドマークを編集しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('ランドマークの編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$this->Landmark->recursive = -1;
			$landmarks = $this->Landmark->find('first', array('conditions' => array('Landmark.id'=> $id)));
			$this->request->data['Landmark'] = $landmarks['Landmark'];
		}
	}
}

?>
<?php
class BulletTrainsController extends AppController {

	public $uses = array('Landmark','LandmarkCategory','BulletTrainArea','Prefecture');

	public function beforeFilter() {
		parent::beforeFilter();
		$bulletTrainAreaList = $this->BulletTrainArea->getBulletTrainAreaList();
		$this->set('bulletTrainAreaList',$bulletTrainAreaList);
		$prefectureList = $this->Prefecture->getPrefectureList();
		$this->set('prefectureList',$prefectureList);
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
		$conditions = array('Landmark.name <>'=>'',
							'Landmark.landmark_category_id'=>2
							);
		$landmarks = $this->Landmark->find('all',array('conditions'=> $conditions,'order'=>'Landmark.sort asc,Landmark.id asc'));
		$this->set('landmarks',$landmarks);

		//$landmarkCategoryList = $this->LandmarkCategory->getLandmarkCategoryList();
		//$this->set(compact('landmarkCategoryList'));
	}

	public function add() {

		if($this->request->is('post')) {
			$saveData = array();
			$saveData = $this->request->data['BulletTrain'];

			$saveData['client_id'] = $this->cdata['client_id'];
			$saveData['staff_id']  = $this->cdata['id'];
			$saveData['landmark_category_id'] = 2;
			$saveData['delete_flg'] = 0;

			if($this->Landmark->save($saveData)) {
				$this->Session->setFlash('新幹線駅を追加しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('新幹線駅の登録に失敗しました','default',array('class'=>'alert alert-error'));
			}
		}
	}

	public function edit($id = null) {

		if($this->request->is('post')) {
			$saveData = array();
			$saveData = $this->request->data['BulletTrain'];

			$saveData['client_id'] = $this->cdata['client_id'];
			$saveData['staff_id']  = $this->cdata['id'];
			$saveData['landmark_category_id'] = 2;

			if($this->Landmark->save($saveData)) {
				$this->Session->setFlash('新幹線駅を編集しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('新幹線駅の編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$this->Landmark->recursive = -1;
			$airports = $this->Landmark->find('first', array('conditions' => array('Landmark.id'=> $id)));
			$this->request->data['BulletTrain'] = $airports['Landmark'];
		}
	}
}
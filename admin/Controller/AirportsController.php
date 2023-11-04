<?php
App::uses('AppController', 'Controller');
App::uses('SkyticketData', 'Vendor');

/**
 * Airports Controller
 *
 * @property airport $Landmark
 */
class AirportsController extends AppController {

	public $uses = array('Landmark','LandmarkCategory','Prefecture');

	public function beforeFilter() {
		parent::beforeFilter();
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
							'Landmark.landmark_category_id'=>1
							);
		$landmarks = $this->Landmark->find('all',array('conditions'=> $conditions,'order'=>'Landmark.sort asc,Landmark.id asc'));
		$this->set('landmarks',$landmarks);
		
		$airportLinkCd = $this->Landmark->getAirportLinkCd();
		foreach($airportLinkCd as $v){
			$linkcds[$v['a']['landmark_id']] = $v['r']['link_cd'];
		}
		$this->set('linkcds',$linkcds);

		$landmarkCategoryList = $this->LandmarkCategory->getLandmarkCategoryList();
		$this->set(compact('landmarkCategoryList'));
	}

	public function add() {

		if($this->request->is('post')) {
			$saveData = $this->request->data['Airport'];

			$saveData['staff_id']  = $this->cdata['id'];
			$saveData['landmark_category_id'] = 1;
			$saveData['delete_flg'] = 0;

			if($this->Landmark->save($saveData)) {
				$this->Session->setFlash('空港を追加しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('空港の登録に失敗しました','default',array('class'=>'alert alert-error'));
			}
		}
	}

	public function edit($id = null) {

		if($this->request->is('post')) {
			$saveData = $this->request->data['Airport'];

			$saveData['staff_id']  = $this->cdata['id'];
			$saveData['landmark_category_id'] = 1;
			if ($saveData['delete_flg'] == 1) {
				$this->Landmark->id = $id;
				if ($this->Landmark->field('delete_flg') == 0) {
					$saveData['deleted'] = date("Y-m-d H:i:s", time());
				}
			} else {
				$saveData['deleted'] = null;
			}

			if($this->Landmark->save($saveData)) {
				$this->Session->setFlash('空港を編集しました','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('空港の編集に失敗しました','default',array('class'=>'alert alert-error'));
			}
		} else {
			$this->Landmark->recursive = -1;
			$airports = $this->Landmark->find('first', array('conditions' => array('Landmark.id'=> $id)));
			$this->request->data['Airport'] = $airports['Landmark'];
		}
	}
}

?>
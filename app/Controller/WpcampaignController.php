<?php
App::uses('AppController', 'Controller');
/**
 * Wpcampaign Controller
 *
 * @property Wpcampaign $Wpcampaign
 */
class WpcampaignController extends AppController {

	public $uses = array('RcPostmeta','Client','CarType','Station','Landmark', 'KeyValue');
	public $components = array('BreadCrumb');

	public $use_yotpo = true;
	public $use_searchbox = true;
	
	public function beforeFilter() {
		parent::beforeFilter();
	}

	private function redirect404(){
		$this->response->statusCode(404);
		$this->render('/Errors/error404');
	}

	public function index() {

		$this->CarType->recursive = -1;
		$carTypes = $this->CarType->find('all');
		foreach($carTypes as $carType){
			$carTypeList[$carType['CarType']['id']] = $carType;
		}

		$link_cd = $this->params['link_cd'];
		$data = $this->RcPostmeta->getCampaignPostmetaData($link_cd);
		
		if(empty($data)){
			$this->redirect404();
			return;
		}
		$options = array('conditions'=>array('major_flg'=>1,'delete_flg'=>0));
		$stationListTemp = $this->Station->findC('all',$options,'1day');
		foreach($stationListTemp as $station){
			$station['Station']['name'] = $station['Station']['name'].'駅';
			$stationList[$station['Station']['id']] = $station;
		}
		$this->set('stationList',$stationList);

		$options = array('conditions'=>array('landmark_category_id'=>1,'delete_flg'=>0));
		$airportList = $this->Landmark->findC('list',$options,'1day');
		$this->set('airportList',$airportList);

		//仕様は現在、複数のクライエントは対応していない
		$clienId = $data['cp-price'][0]['cp-price-client']['value'];
        $clientInfo = $this->Client->getClientById($clienId);

        // キャッシュされたYOTPOのjsonをDBから取得
        $main_widget = '';
        $jsonKeyValue = $this->KeyValue->find('first', array('conditions' => array('key'=> 'yotpo_json_company_'.$clientInfo['Client']['id'].'cl')));
        if($jsonKeyValue){
            $review = json_decode($jsonKeyValue['KeyValue']['value']);
            $main_widget = $review[0]->result;
        }

		$options = $this->OptionsManage->getOptions();
		$smokingOptions = $this->OptionsManage->getSmokingOptions();

        $this->set('main_widget', $main_widget);
		$this->set('options',$data);
		$this->set('smokingOptions',$smokingOptions);
		$this->set('carTypeList',$carTypeList);
		$this->set('clientInfo',$clientInfo);
		$this->set('data',$data);

		$this->set('title_for_layout',$data['cp-title']);
		$this->set('description_for_layout',$data['cp-meta_description']);
		$this->set('keywords',$data['cp-meta_keyword']);

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setWpcampaign($this->action, $data['cp-title'], $link_cd);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {
		$this->index();
	}
}

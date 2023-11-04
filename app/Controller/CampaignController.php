<?php

App::uses('AppController', 'Controller');

class CampaignController extends AppController {
    
   	public $uses = array('Prefecture','Landmark','Area','CarType','Equipment','Client', 'Office', 'Landmark', 'ClientCampaign');
	public $components = array('OptionsManage');

	public function beforeFilter() {
		parent::beforeFilter();
	}
    
        
    public function index() {
        
        if($_GET['cam']){
            $campaignId = $_GET['cam'];
        } else {
            $this->redirect('/');
        }
        //キャンペーンデータ
        $campaignData = $this->ClientCampaign->getCampaignData($campaignId);  
        //クライアントデータ
        $clientInfo = $this->Client->getClientById($campaignData['ClientCampaign']['client_id']);
        
        $this->set('campaignData', $campaignData);
	$this->set('clientInfo', $clientInfo);
        
        if(empty($campaignData)){
            $this->redirect('/');
        }
    }
    public function sp_index() {
        $this->index();
    }
    

}

<?php
App::uses('AppModel', 'Model');
/**
 * ClientCampaign Model
 *
 * @property Client $Client
 */
class ClientCampaign extends AppModel {
    
        public function getCampaignData($id) {
                $options = array(
			'conditions' => array(
					'delete_flg' => 0,
                                        'id' => $id
			),
			'recursive' => -1
		);
		return $this->find('first', $options);
	}
    
    	public function getCampaignDatabyclientid($clientId) {
                $options = array(
			'conditions' => array(
					'delete_flg' => 0,
                                        'client_id' => $clientId,
                                        'OR' => array(
                                            array('period_end' => null), 
                                            array('period_end > ' => date('Y-m-d'))
                                        )
			),
			'order' => array('rank' => 'asc', 'id' => 'asc'),
			'recursive' => -1
		);
		return $this->find('all', $options);
	}
        
        public function getPagenate($clientId){
            $option = array(
                'limit' => 6,
                'conditions' => array(
			'delete_flg' => 0,
                        'client_id' => $clientId,
                        'OR' => array(
                            array('period_end' => null), 
                            array('period_end > ' => date('Y-m-d'))
                        )
		),
		'order' => array('rank' => 'asc', 'id' => 'asc'),
            );
            return $option;
        }
}

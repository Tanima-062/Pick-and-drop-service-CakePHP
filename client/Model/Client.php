<?php
App::uses('AppModel', 'Model');
/**
 * Client Model
 */
class Client extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getClient($clientId) {

		$options = array(
				'conditions' => array(
						'Client.delete_flg' => 0,
						'Client.id' => $clientId
				),
				'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getClientCount() {

		$options = array(
				'recursive' => -1
		);

		return $this->find('count',$options);
	}

	public function getClientList() {

		$options = array(
				'order' => 'id',
				'recursive' => -1
		);

		return $this->find('list',$options);
	}

	public function getDiffClient($limit) {

		$options = array(
				'order' => 'id DESC',
				'limit' => $limit,
				'recursive' => -1
		);

		return $this->find('list',$options);
	}

	public function getClientByConclusionContractCriteria($clientId = '') {

		$conditions = array(
			'delete_flg'=>0
		);

		if(!empty($clientId)) {
			$conditions += array('id'=>$clientId);
		}

		$clients = $this->find('all',array(
							'conditions'=>$conditions,
							'recursive'=>-1
			)
		);

		$clientArray = array();
		foreach($clients as $client) {
			$clientId = $client['Client']['id'];
			if($client['Client']['conclusion_contract_criteria'] == 0) {
				$clientArray['rent'][$clientId] =$clientId;
			} else if($client['Client']['conclusion_contract_criteria'] == 1) {
				$clientArray['return'][$clientId] = $clientId;
			}
		}

		return $clientArray;

	}
}

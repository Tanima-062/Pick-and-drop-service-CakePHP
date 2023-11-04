<?php
App::uses('AppModel', 'Model');
/**
 * CommodityGroup Model
 *
 */
class CommodityGroup extends AppModel {

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
			'Commodity' => array(
					'className' => 'Commodity',
					'foreignKey' => 'commodity_group_id',
					'dependent' => false,
					'conditions' => '',
					'fields' => '',
					'order' => '',
					'limit' => '',
					'offset' => '',
					'exclusive' => '',
					'finderQuery' => '',
					'counterQuery' => ''
			)
	);

	public function getList($clientId) {

		$options = array(
			'conditions' => array(
				'client_id' => $clientId,
				'delete_flg' => 0
			),
			'order'=>'sort asc',
			'recursive' => -1
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('scope' => 0),
				array('scope' => $clientData['id'])
			);
		}

		return $this->find('list',$options);
	}

	public function getFirst($id) {

		$options = array(
				'conditions' => array(
						'id' => $id,
						'delete_flg' => 0
				),
				'recursive' => -1
		);

		return $this->find('first',$options);
	}

	public function getAll($clientId) {

		$options = array(
			'fields' => array(
				'id',
				'name',
				'scope'
			),
			'conditions' => array(
				'client_id' => $clientId,
				'delete_flg' => 0
			),
			'order'=>'sort asc',
			'recursive' => -1
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('scope' => 0),
				array('scope' => $clientData['id'])
			);
		}

		return $this->find('all',$options);
	}

	/**
	 * 編集ID、クライアントID、スタッフIDで不正アクセスを判定
	 * @param unknown $id
	 * @param unknown $clientId
	 */
	public function clientCheck($id, $clientId) {
		$options = array(
			'conditions' => array(
				'id' => $id,
				'client_id' => $clientId,
			),
			'recursive' => -1,
		);
		$clientData = $this->_getCurrentUser();
		if (!$clientData['is_client_admin']) {
			$options['conditions']['OR'] = array(
				array('scope' => 0),
				array('scope' => $clientData['id'])
			);
		}

		$result = $this->find('first', $options);

		if (!empty($result)) {
			return true;
		} else {
			return false;
		}
	}
}

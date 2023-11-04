<?php
App::uses('AppModel', 'Model');
/**
 * Staff Model
 */
class Staff extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	public function getClientId($id) {

		$options = array(
				'conditions' => array(
						'id' => $id
				),
				'recursive' => -1
		);
		$staff = $this->find('first',$options);

		return $staff['Staff']['client_id'];
	}

	public function getStaffList($clientId) {
		return $this->find('list', array(
				'fields' => array('id', 'name'),
				'conditions' => array(
						'Staff.client_id' => $clientId,
						'Staff.delete_flg' => 0
				),
				'order' => 'Staff.id ASC',
				'recursive' => -1
			)
		);
	}
}

<?php
App::uses('AppModel', 'Model');
/**
 * MessageBoard Model
 */
class MessageBoard extends AppModel {

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
		),
	);

	public function getReservationResponseHistories($reservationId) {
		$options = array(
			'fields' => array(
				'Staff.name',
				'MessageBoard.created',
				'MessageBoard.message',
			),
			'conditions' => array(
				'MessageBoard.reservation_id' => $reservationId,
				'MessageBoard.category_cd' => 'RESERVATION_DETAIL'
			),
			'joins' => array(
				array(
					'table' => 'staffs',
					'alias' => 'Staff',
					'type' => 'INNER',
					'conditions' => array(
						'Staff.id = MessageBoard.staff_id'
					),
				),
			),
			'order' => 'MessageBoard.created asc',
			'recursive' => -1,
		);
		return $this->find('all', $options);
	}
}

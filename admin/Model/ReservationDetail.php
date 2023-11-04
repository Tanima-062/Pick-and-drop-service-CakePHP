<?php
App::uses('AppModel', 'Model');
/**
 * ReservationDetail Model
 */
class ReservationDetail extends AppModel {

	public $belongsTo = [
		'DetailType' => [
			'className' => 'DetailType',
			'fields' => [
				'DetailType.name'
			]
		]
	];

	public function getDetailApiPostData($reservationId) {
		$options = array(
			'fields' => array(
				'ReservationDetail.detail_type_id',
				'ReservationDetail.amount',
			),
			'conditions' => array(
				'ReservationDetail.reservation_id' => $reservationId,
			),
			'order' => array(
				'ReservationDetail.id',
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}
}

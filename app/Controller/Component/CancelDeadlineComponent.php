<?php

App::uses('Component', 'Controller');

class CancelDeadlineComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function getDatetime($reservationId)
	{
		$Reservation = ClassRegistry::init('Reservation');

		$data = $Reservation->find('first', array(
			'fields' => array(
				'Reservation.rent_datetime',
				'Client.cancel_deadline_hours',
				'Client.cancel_deadline_days',
				'Client.cancel_deadline_time',
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'clients',
					'alias' => 'Client',
					'conditions' => array(
						'Client.id = Reservation.client_id',
					),
				),
			),
			'conditions' => array(
				'Reservation.id' => $reservationId,
			),
			'recursive' => -1,
		));

		$datetime = $data['Reservation']['rent_datetime'];
		if (!is_null($data['Client']['cancel_deadline_hours'])) {
			$datetime = date('Y-m-d H:i:s', strtotime($datetime.' -'.$data['Client']['cancel_deadline_hours'].' hour'));
		} elseif (!is_null($data['Client']['cancel_deadline_days'])) {
			$date = date('Y-m-d', strtotime($datetime.' -'.$data['Client']['cancel_deadline_days'].' day'));
			$datetime = $date.' '.$data['Client']['cancel_deadline_time'];
		}

		return $datetime;
	}
}

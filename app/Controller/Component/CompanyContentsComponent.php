<?php

App::uses('BaseContentsComponent', 'Controller/Component');

class CompanyContentsComponent extends BaseContentsComponent {

	public function getPopularLandmarkRanking($clientId, $searchParams) {
		$Reservation = ClassRegistry::init('Reservation');
		$OfficeStation = ClassRegistry::init('OfficeStation');

		$conditions = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Station.id',
				'Station.prefecture_id',
				'Station.name',
				'Station.type',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => array(
						'Office.id = Reservation.rent_office_id',
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'office_supplements',
					'alias' => 'OfficeSupplement',
					'conditions' => array(
						'OfficeSupplement.office_id = Office.id',
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'landmarks',
					'alias' => 'Landmark',
					'conditions' => array(
						'Landmark.id = Office.airport_id',
						'OfficeSupplement.nearest_transport' => 0,
					),
				),
				array(
					'type' => 'LEFT',
					'table' => "({$OfficeStation->getIndexGroupByOfficeSubQuery()})",
					'alias' => 'OfficeStation',
					'conditions' => array(
						'OfficeStation.office_id = Reservation.rent_office_id',
						'OfficeStation.idx' => 0,// 営業所ごとの先頭レコード
					),
				),
				array(
					'type' => 'LEFT',
					'table' => 'stations',
					'alias' => 'Station',
					'conditions' => array(
						'Station.id = OfficeStation.station_id',
						'OfficeSupplement.nearest_transport' => 1,
					),
				),
			),
			'conditions' => array(
				'Reservation.client_id' => $clientId,
				'Reservation.reservation_datetime >=' => date('Y-m-d 00:00:00', strtotime('-61 day')),
				'Reservation.reservation_datetime <=' => date('Y-m-d 23:59:59', strtotime('-1 day')),
			),
			'recursive' => -1,
		);
		$result = $Reservation->findC('all', $conditions, '1day');

		$ranking = $this->rankLandmarkCount($result);

		return $this->addPriceAndCapacityInfo($ranking, $searchParams, $clientId);
	}
}

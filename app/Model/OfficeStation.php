<?php

App::uses('AppModel', 'Model');

class OfficeStation extends AppModel {

	protected $cacheConfig = '1hour';

	public function getOfficeIdList($stationId) {
		$options = array(
			'fields' => array(
				'OfficeStation.office_id', 'OfficeStation.office_id'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Station',
					'table' => 'stations',
					'conditions' => 'OfficeStation.id = Station.id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => 'OfficeStation.office_id = Office.id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = Client.id'
				),
			),
			'conditions' => array(
				'OfficeStation.station_id' => $stationId,
				'OfficeStation.delete_flg' => 0,
				'Station.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('list', $options);
	}

	public function getAreaIdListByStationId($stationId) {
		$options = array(
			'fields' => array(
				'Area.id', 'Area.id'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Station',
					'table' => 'stations',
					'conditions' => 'OfficeStation.id = Station.id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => 'OfficeStation.office_id = Office.id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = Client.id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Office.area_id = Area.id'
				),
			),
			'conditions' => array(
				'OfficeStation.station_id' => $stationId,
				'OfficeStation.delete_flg' => 0,
				'Station.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		
		$ids = $this->findC('list', $options);
		return array_unique($ids);
	}

	public function getNearestStation($officeId) {
		$options = array(
			'fields' => array(
				'OfficeStation.station_id'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Station',
					'table' => 'stations',
					'conditions' => 'OfficeStation.id = Station.id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => 'OfficeStation.office_id = Office.id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => 'Office.client_id = Client.id'
				),
			),
			'conditions' => array(
				'OfficeStation.office_id' => $officeId,
				'OfficeStation.delete_flg' => 0,
				'Station.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'order' => array(
				'OfficeStation.id' => 'asc'
			),
			'recursive' => -1,
		);

		$firstStation = $this->findC('first', $options);
		if (!empty($firstStation)) {
			return $firstStation['OfficeStation']['station_id'];
		} else {
			return 0;
		}
	}

	public function getIndexGroupByOfficeSubQuery() {
		$db = $this->getDataSource();

		$sub = $db->buildStatement(array(
			'fields' => array(
				'count(*)'
			),
			'table' => $db->fullTableName('office_stations'),
			'alias' => 'In',
			'conditions' => array(
				'In.office_id = Ex.office_id',
				'In.id < Ex.id',
				'In.delete_flg' => 0,
			),
		), $this);

		$conditions = array(
			'fields' => array(
				"({$sub}) as idx",
				'Ex.office_id',
				'Ex.station_id',
			),
			'table' => $db->fullTableName('office_stations'),
			'alias' => 'Ex',
			'conditions' => array(
				'Ex.delete_flg' => 0,
			),
		);

		return $db->buildStatement($conditions, $this);
	}
}

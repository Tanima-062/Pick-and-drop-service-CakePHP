<?php
App::uses('AppModel', 'Model');

/**
 * OfficeStation Model
 *
 */
class OfficeStation extends AppModel {

	public function getStationsByOfficeId($officeId, $fields = array(), $deleteFlg = 0){

		$joins = array(
				array(
				'table' => 'stations',
	            'alias' => 'Station',
	            'type' => 'INNER',
	            'conditions' => array(
	                'OfficeStation.station_id = Station.id'
	            ))
	        );

		$conditions = array('OfficeStation.office_id' => $officeId);

		if(isset($deleteFlg)){
			$conditions['OfficeStation.delete_flg'] = $deleteFlg;
		}
		
		$options = compact('fields','joins','conditions');

		$stations = $this->find('all',$options);

		return $stations;

	}
}

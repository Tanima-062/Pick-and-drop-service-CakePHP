<?php
App::uses('AppModel', 'Model');

/**
 * Station Model
 *
 */
class Station extends AppModel {
	
	public function getStationsByAreaId($areaId,$fields = array()){
		
		$joins = array(
			array(
			'table' => 'prefectures',
            'alias' => 'Prefecture',
            'type' => 'INNER',
            'conditions' => array(
                'Station.prefecture_id = Prefecture.id'
            )),
			array(
			'table' => 'areas',
            'alias' => 'Area',
            'type' => 'INNER',
            'conditions' => array(
                'Prefecture.id = Area.prefecture_id'
            )),
        );
		$conditions = array('Area.id' => $areaId,
							'Area.delete_flg' => 0,
							'Station.delete_flg' => 0);
		$options = compact('fields','joins','conditions');
		$stations = $this->find('all',$options);
		return $stations;
	}	
}

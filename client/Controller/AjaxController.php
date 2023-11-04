<?php
App::uses('AppController', 'Controller');

class AjaxController extends AppController {

	public $uses = array('Area','Station','OfficeStation');


	public function get_stations_by_area($areaId = '') {

		$this->autoLayout = false;
		$stations = null;
		
		if(!empty($areaId)){
			
			$fields = array('Station.id','Station.name');
			$stations = $this->Station->getStationsByAreaId($areaId,$fields);
		}

		$this->set('stations',$stations);
	}
}
?>
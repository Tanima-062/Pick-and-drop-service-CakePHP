<?php
App::uses('BaseRestApiController', 'Controller');

class SkyscannerStationsController extends BaseRestApiController {
	public $uses = array('Station');
	
	public function index($id = null) {
		$options = array(
			'fields' => array(
				'Station.id',
				'Station.name',
				'Station.latitude',
				'Station.longitude',
				'Prefecture.id',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'type' => 'INNER',
					'conditions' => array(
						'Prefecture.id = Station.prefecture_id',
						'Prefecture.delete_flg' => false,
					),
				),
			),
			'conditions' => array(
				'Station.delete_flg' => false,
			),
			'order' => 'Station.id',
			'recursive' => -1,
		);
		
		if (!empty($id)) {
			$options['conditions'] = array( 'Station.id' => $id ) + $options['conditions'];
		}

		$ret = $this->Station->findC('all', $options);
		
		if (empty($ret)) {
			$this->response->statusCode(404);
			return;
		}
		
		$station_list = array();
		
		foreach ($ret as $v) {
			$c = $v['Station'];
			$p = $v['Prefecture'];
			
			$station = array(
				'station_id'			 => $c['id'],
				'station_name'			 => $c['name'],
				'prefecture_id'		 => $p['id'],
				'prefecture_name'	 => $p['name'],
				'country_id'		 => 'JP',	// 今は固定
				'country_name'		 => '日本',
			);
			
			if (!empty($c['latitude'])) {
				$station['geocode_lat'] = $c['latitude'];
			}

			if (!empty($c['longitude'])) {
				$station['geocode_lng'] = $c['longitude'];
			}
			
			$station_list[] = $station;
		}
		
		$this->responseData['response'] = array(
			'station_list' => $station_list,
		);
		
	}

	public function view($id = null) {
		if (empty($id)) {
			if (empty($this->request->params['id'])) {
				$this->response->statusCode(404);
				return;
			}
			$id = $this->request->params['id'];
		}
		
		return $this->index($id);
	}

}

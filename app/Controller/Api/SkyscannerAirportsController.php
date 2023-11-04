<?php
App::uses('BaseRestApiController', 'Controller');

class SkyscannerAirportsController extends BaseRestApiController {
	public $uses = array('Landmark');
	
	public function index($id = null, $iata_cd = null) {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.latitude',
				'Landmark.longitude',
				'Landmark.iata_cd',
				'Prefecture.id',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'type' => 'INNER',
					'conditions' => array(
						'Prefecture.id = Landmark.prefecture_id',
						'Prefecture.delete_flg' => false,
					),
				),
			),
			'conditions' => array(
				'Landmark.landmark_category_id' => 1,
				'Landmark.delete_flg' => false,
			),
			'order' => 'Landmark.id',
			'recursive' => -1,
		);
		
		if (!empty($id)) {
			$options['conditions'] = array( 'Landmark.id' => $id ) + $options['conditions'];
		}

		if (!empty($iata_cd)) {
			$options['conditions'] += array( 'Landmark.iata_cd' => $iata_cd );
		}
		
		$ret = $this->Landmark->findC('all', $options);
		
		if (empty($ret)) {
			$this->response->statusCode(404);
			return;
		}
		
		$airport_list = array();
		
		foreach ($ret as $v) {
			$l = $v['Landmark'];
			$p = $v['Prefecture'];
			
			$airport = array(
				'airport_id'		 => $l['id'],
				'airport_name'		 => $l['name'],
				'prefecture_id'		 => $p['id'],
				'prefecture_name'	 => $p['name'],
				'country_id'		 => 'JP',	// 今は固定
				'country_name'		 => '日本',
				'iata_code'			 => $l['iata_cd'],
			);
			
			if (!empty($l['latitude'])) {
				$airport['geocode_lat'] = $l['latitude'];
			}

			if (!empty($l['longitude'])) {
				$airport['geocode_lng'] = $l['longitude'];
			}
			
			$airport_list[] = $airport;
		}
		
		$this->responseData['response'] = array(
			'airport_list' => $airport_list,
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
		
		if (is_numeric($id)) {
			return $this->index($id, null);
		} else if (is_string($id)) {
			return $this->index(null, $id);
		}
	}

}

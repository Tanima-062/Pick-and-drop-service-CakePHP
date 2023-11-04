<?php
App::uses('BaseRestApiController', 'Controller');

class SkyscannerCitiesController extends BaseRestApiController {
	public $uses = array('City');
	
	public function index($id = null) {
		$options = array(
			'fields' => array(
				'City.id',
				'City.name',
				'City.latitude',
				'City.longitude',
				'Prefecture.id',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'type' => 'INNER',
					'conditions' => array(
						'Prefecture.id = City.prefecture_id',
						'Prefecture.delete_flg' => false,
					),
				),
			),
			'conditions' => array(
				'City.delete_flg' => false,
			),
			'order' => 'City.id',
			'recursive' => -1,
		);
		
		if (!empty($id)) {
			$options['conditions'] = array( 'City.id' => $id ) + $options['conditions'];
		}

		$ret = $this->City->findC('all', $options);
		
		if (empty($ret)) {
			$this->response->statusCode(404);
			return;
		}
		
		$city_list = array();
		
		foreach ($ret as $v) {
			$c = $v['City'];
			$p = $v['Prefecture'];
			
			$city = array(
				'city_id'			 => $c['id'],
				'city_name'			 => $c['name'],
				'prefecture_id'		 => $p['id'],
				'prefecture_name'	 => $p['name'],
				'country_id'		 => 'JP',	// 今は固定
				'country_name'		 => '日本',
			);
			
			if (!empty($c['latitude'])) {
				$city['geocode_lat'] = $c['latitude'];
			}

			if (!empty($c['longitude'])) {
				$city['geocode_lng'] = $c['longitude'];
			}
			
			$city_list[] = $city;
		}
		
		$this->responseData['response'] = array(
			'city_list' => $city_list,
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

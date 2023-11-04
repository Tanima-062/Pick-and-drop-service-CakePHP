<?php
App::uses('BaseRestApiController', 'Controller');

class AjaxCurrentLocationController extends BaseRestApiController {
	public $uses = array('City');
	
	public function index() {
		if (!$this ->request->is( 'ajax' )) {
			$this->response->statusCode(404);
			return;
		}
		
		if (!is_numeric($this->params['lat']) || !is_numeric($this->params['lng'])) {
			$this->response->statusCode(404);
			return;
		}

		$lat = $this->params['lat'];
		$lng = $this->params['lng'];
		
		$params = array(
			'lat' => $lat,
			'lng' => $lng,
		);

		$sql = "
			SELECT
			  c.id
			  , c.name
			  , c.latitude
			  , c.longitude
			  , p.id
			  , p.name
			  , POWER(ABS(c.latitude - :lat), 2) + POWER(ABS(c.longitude - :lng), 2) distance
			FROM
			  rentacar.cities AS c
			  INNER JOIN rentacar.prefectures AS p
			    ON p.id = c.prefecture_id
			    AND p.delete_flg = 0
			WHERE
			  c.delete_flg = 0
			ORDER BY
			  distance
			LIMIT
			  1
		";

		$ret = $this->City->queryC($sql, $params);

		if (empty($ret)) {
			$this->response->statusCode(404);
			return;
		}
		
		$c = $ret[0]['c'];
		$p = $ret[0]['p'];
		
		$city = array(
			'city_id'			 => $c['id'],
			'city_name'			 => $c['name'],
			'prefecture_id'		 => $p['id'],
			'prefecture_name'	 => $p['name'],
			// 地球の丸みを考慮していないのでdistanceをそのまま距離として使ってはいけない
			// 'distance'			 => $ret[0]['distance'],
		);
		
		if (!empty($c['latitude'])) {
			$city['geocode_lat'] = $c['latitude'];
		}

		if (!empty($c['longitude'])) {
			$city['geocode_lng'] = $c['longitude'];
		}
		
		$this->responseData['response'] = $city;
		
	}

}

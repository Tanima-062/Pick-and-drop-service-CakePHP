<?php
App::uses('BaseRestApiController', 'Controller');

class SkyscannerShopsController extends BaseRestApiController {
	public $uses = array('Office');

	public function index($id = null) {
		$options = array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Office.id',
				'Office.name',
				'Office.office_hours_from',
				'Office.office_hours_to',
				'Office.sat_hours_from',
				'Office.sat_hours_to',
				'Office.sun_hours_from',
				'Office.sun_hours_to',
				'Office.hol_hours_from',
				'Office.hol_hours_to',
				'Office.tel',
				'Office.access',
				'Office.access_dynamic',
				'Office.latitude',
				'Office.longitude',
				'Office.address',
				'Office.latitude',
				'Office.longitude',
				'Office.access',
				'Office.tel',
				'City.id',
				'City.name',
				'Airport.id',
				'Airport.name',
				// 駅レコードの結合 {id}-{name}\t{id}-{name}...
				"GROUP_CONCAT(CONCAT(Station.id, '-', Station.name) ORDER BY Station.id SEPARATOR '\\t') AS Stations",
			),
			'joins' => array(
				array(
					'table' => 'clients',
					'alias' => 'Client',
					'type' => 'INNER',
					'conditions' => array(
						'Client.id = Office.client_id',
						'Client.delete_flg' => false,
					),
				),
				array(
					'table' => 'cities',
					'alias' => 'City',
					'type' => 'LEFT',
					'conditions' => array(
						'City.id = Office.city_id',
						'City.delete_flg' => false,
					),
				),
				array(
					'table' => 'landmarks',
					'alias' => 'Airport',
					'type' => 'LEFT',
					'conditions' => array(
						'Airport.id = Office.airport_id',
						'Airport.landmark_category_id' => 1,
						'Airport.delete_flg' => false,
					),
				),
				array(
					'table' => 'office_stations',
					'alias' => 'OfficeStation',
					'type' => 'LEFT',
					'conditions' => array(
						'Office.id = OfficeStation.office_id',
						'OfficeStation.delete_flg' => false,
					),
				),
				array(
					'table' => 'stations',
					'alias' => 'Station',
					'type' => 'LEFT',
					'conditions' => array(
						'Station.id = OfficeStation.station_id',
						'Station.delete_flg' => false,
					),
				),
			),
			'conditions' => array(
				'Office.delete_flg' => false,
			),
			'group' => array(
				'Office.id',
			),
			'recursive' => -1,
		);

		if (!empty($id)) {
			$options['conditions'] = array( 'Office.id' => $id ) + $options['conditions'];
		}

		$ret = $this->Office->findC('all', $options);

		if (empty($ret)) {
			$this->response->statusCode(404);
			return;
		}

		$shop_list = array();

		foreach ($ret as $v) {
			$c = $v['Client'];
			$o = $v['Office'];

			$city = $v['City'];
			$airport = $v['Airport'];
			$stations = $v[0]['Stations'];

			$shop = array(
				'brand_id'		 => $c['id'],
				'brand_name'	 => $c['name'],
				'shop_id'		 => $o['id'],
				'shop_name'		 => $c['name'] . ' ' . $o['name'],
				'shop_address'	 => $o['address'],
			);

			if (!empty($o['latitude'])) {
				$shop['shop_geocode_lat'] = $o['latitude'];
			}

			if (!empty($o['longitude'])) {
				$shop['shop_geocode_lng'] = $o['longitude'];
			}

			// 店舗へのアクセスが動的に出せない場合は文言から取得する(旧仕様)
			if (!empty($o['access_dynamic'])) {
				$shop['shop_access'] = $o['access_dynamic'];
			} else {
				$shop['shop_access'] = trim(strip_tags(str_replace(array("\r\n", "\r", '<br>'), "\n", $o['access'])));
			}

			if (!empty($o['office_hours_from'])) {
				$shop['shop_open_time'] = date('Hi', strtotime($o['office_hours_from']));
			}

			if (!empty($o['office_hours_to'])) {
				$shop['shop_close_time'] = date('Hi', strtotime($o['office_hours_to']));
			}

			$shop['shop_time'] = $this->ApiCommon->createShopTimeString($o);
			$shop['shop_phone'] = $o['tel'];

			// 市区町村情報
			$shop['shop_city_info'] = array(
				'city_id' => $city['id'],
				'city_name' => $city['name'],
			);

			// 空港情報
			if (!empty($airport['id'])) {
				$shop['shop_airport_info'] = array(
					'airport_id' => $airport['id'],
					'airport_name' => $airport['name'],
				);
			}

			// 駅情報
			if (!empty($stations)) {
				$shop['shop_station_info'] = $this->ApiCommon->convertStationInfo($stations);
			}

			$shop_list[] = $shop;
		}

		$this->responseData['response'] = array(
			'shop_list' => $shop_list,
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

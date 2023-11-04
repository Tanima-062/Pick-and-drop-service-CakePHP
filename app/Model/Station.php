<?php

App::uses('AppModel', 'Model');
APP::import('Model', 'Landmark');

/**
 * Station Model
 *
 * @property Staff $Staff
 * @property Landmark $Landmark
 * @property Office $Office
 */
class Station extends AppModel {

	protected $cacheConfig = '1day';

	public function getStationAll($majorFlg = 0, $stockFlg = 0) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_conf = ($stockFlg) ? '10minutes' : $this->cacheConfig;
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $cache_conf);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		//営業所とつながっている駅を取得
		$OfficeStation = ClassRegistry::init('OfficeStation');
		$OfficeStation->setDataSource($this->getDataSource()->configKeyName);

		$fields = array(
			'Station.id',
			'Station.name',
			'Station.prefecture_id',
			'Station.major_flg',
			'OfficeStation.office_id',
			'Area.id',
			'Area.name',
		);
		$joins = array(
			array(
				'type' => 'INNER',
				'alias' => 'Station',
				'table' => 'stations',
				'conditions' => 'OfficeStation.station_id = Station.id'
			),
			array(
				'type' => 'INNER',
				'alias' => 'City',
				'table' => 'cities',
				'conditions' => 'Station.city_id = City.id'
			),
			array(
				'type' => 'INNER',
				'alias' => 'Area',
				'table' => 'areas',
				'conditions' => 'City.area_id = Area.id'
			),
		);
		$conditions = array(
			'Station.delete_flg' => 0,
			'OfficeStation.delete_flg' => 0,
			'City.delete_flg' => 0,
			'Area.delete_flg' => 0,
		);
		if ($majorFlg) {
			$conditions['Station.major_flg'] = 1;
		}
		$order = array(
			'Station.sort' => 'asc',
			'Station.id' => 'asc',
		);
		$options = compact('fields', 'joins', 'conditions', 'order');
		$results = $OfficeStation->findC('all', $options, '1hour');

		$list = array();
		$stationIds = array();
		if ($stockFlg) {
			// 在庫のある営業所取得
			$Landmark = ClassRegistry::init('Landmark');
			$Landmark->setDataSource($this->getDataSource()->configKeyName);

			// *本当は営業所だけの在庫を取得するべき
			$officeIds = $Landmark->getLandmarkIdAndAreaIdArrayInStock()[2];

			// 在庫のある駅の配列を作成
			foreach ($results as $result) {

				if (in_array($result['OfficeStation']['office_id'], $officeIds)) {
					if (!in_array($result['Station']['id'], $stationIds)) {
						$prefectureId = $result['Station']['prefecture_id'];
						$areaId = $result['Area']['id'];
						$list[$prefectureId][$areaId][] = array(
							'id' => $result['Station']['id'],
							'name' => $result['Station']['name'],
							'major' => $result['Station']['major_flg'],
							'area_name' => $result['Area']['name'],
						);
						//重複駅を除くため
						$stationIds[] = $result['Station']['id'];
					}
				}
			}
		} else {
			// 全て駅の配列を作成
			foreach ($results as $result) {
				if (!in_array($result['Station']['id'], $stationIds)) {
					$prefectureId = $result['Station']['prefecture_id'];
					$areaId = $result['Area']['id'];
					$list[$prefectureId][$areaId][] = array(
						'id' => $result['Station']['id'],
						'name' => $result['Station']['name'],
						'major' => $result['Station']['major_flg'],
						'area_name' => $result['Area']['name'],
					);
					//重複駅を除くため
					$stationIds[] = $result['Station']['id'];
				}
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		if ($list) {
			$this->writeCache($cache_name, $list, $cache_conf);
		}

		return $list;
	}

	public function getStationAllByClientId($clientId, $majorFlg = 0) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $this->cacheConfig);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		//営業所とつながっている駅を取得
		$OfficeStation = ClassRegistry::init('OfficeStation');
		$OfficeStation->setDataSource($this->getDataSource()->configKeyName);

		$fields = array(
			'Station.id',
			'Station.name',
			'Station.prefecture_id',
			'Station.major_flg',
			'OfficeStation.office_id',
			'Area.id',
			'Area.name',
		);
		$joins = array(
			array(
				'type' => 'INNER',
				'alias' => 'Station',
				'table' => 'stations',
				'conditions' => 'OfficeStation.station_id = Station.id'
			),
			array(
				'type' => 'INNER',
				'alias' => 'City',
				'table' => 'cities',
				'conditions' => 'Station.city_id = City.id'
			),
			array(
				'type' => 'INNER',
				'alias' => 'Area',
				'table' => 'areas',
				'conditions' => 'City.area_id = Area.id'
			),
		);
		$conditions = array(
			'Station.delete_flg' => 0,
			'OfficeStation.delete_flg' => 0,
			'OfficeStation.client_id' => $clientId,
			'City.delete_flg' => 0,
			'Area.delete_flg' => 0,
		);
		if ($majorFlg) {
			$conditions['Station.major_flg'] = 1;
		}
		$order = array(
			'Station.sort' => 'asc',
			'Station.id' => 'asc',
		);
		$options = compact('fields', 'joins', 'conditions', 'order');
		$results = $OfficeStation->findC('all', $options, '1hour');

		$list = array();
		$stationIds = array();
		// 全て駅の配列を作成
		foreach ($results as $result) {
			if (!in_array($result['Station']['id'], $stationIds)) {
				$prefectureId = $result['Station']['prefecture_id'];
				$areaId = $result['Area']['id'];
				$list[$prefectureId][$areaId][] = array(
					'id' => $result['Station']['id'],
					'name' => $result['Station']['name'],
					'major' => $result['Station']['major_flg'],
					'area_name' => $result['Area']['name'],
				);
				//重複駅を除くため
				$stationIds[] = $result['Station']['id'];
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		if ($list) {
			$this->writeCache($cache_name, $list, $this->cacheConfig);
		}

		return $list;
	}

	public function getStationListByPrefectureId($prefectureId) {
		if ($prefectureId == 0) {
			return array();
		}

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$conditions = array('prefecture_id' => $prefectureId,
			'delete_flg' => 0
		);
		$options = compact('conditions');
		$ret = $this->find('list', $options);

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $ret);

		return $ret;
	}

	public function getStationByStationLinkCd($stationLinkCd) {
		$option = array(
			'fields' => array(
				'Station.id',
				'Station.prefecture_id',
				'Station.name',
				'Station.latitude',
				'Station.longitude',
				'Station.type',
			),
			'conditions' => array(
				'Station.url' => $stationLinkCd,
				'Station.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->findC('all', $option);
	}

	public function getStationById($id) {
		$option = array(
			'fields' => array(
				'Station.id',
				'Station.prefecture_id',
				'Station.name',
				'Station.url',
				'Station.latitude',
				'Station.longitude',
				'Station.type',
			),
			'conditions' => array(
				'Station.id' => $id,
				'Station.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->findC('first', $option);
	}

	public function getStationListWithAreaByPrefectureId($prefectureId) {
		$options = array(
			'fields' => array(
				'Station.id',
				'Station.name',
				'Station.url',
				'Station.major_flg',
				'Station.pref_map_flg',
				'Station.type',
				'Area.id',
				'Area.name',
				'Area.area_link_cd'
			),
			'conditions' => array(
				'City.delete_flg' => 0,
				'Area.delete_flg' => 0,
				'Station.prefecture_id' => $prefectureId,
				'Station.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'City',
					'table' => 'cities',
					'conditions' => 'City.id = Station.city_id'
				),
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.id = City.area_id'
				)
			),
			'order' => array(
				'Area.sort' => 'ASC',
				'Station.sort IS NULL' => 'ASC',
				'Station.sort' => 'ASC',
				'Station.id' => 'ASC'
			),
			'recursive' => -1
		);

		return $this->findC('all', $options);
	}

	// 内部リンク変換用
	public function getAllStationListByPrefectureId($prefectureId) {

		$conditions = array(
			'Station.prefecture_id' => $prefectureId,
			'Station.delete_flg' => 0
		);

		$result = $this->findC('all', array(
			'conditions' => $conditions,
			'fields' => array(
				'Station.name',
				'Station.url',
				'Station.type',
				'Prefecture.region_link_cd',
				'Prefecture.link_cd'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'conditions' => 'Prefecture.id = Station.prefecture_id'
				)
			),
			'recursive' => -1
		));

		$combined = array();
		if (!empty($result)) {
			foreach ($result as $data) {
				$name = $data['Station']['name'] . (($data['Station']['type'] == 0) ? '駅' : '停留場');
				$regionLinkCd = str_replace('area_', '', $data['Prefecture']['region_link_cd']);
				$url = '/rentacar/' . $regionLinkCd . '/';
				if ($regionLinkCd !== $data['Prefecture']['link_cd']) {
					$url .= $data['Prefecture']['link_cd'] . '/';
				}
				$url .= $data['Station']['url'] . '/';
				$combined[] = array(
					'name' => $name,
					'url' => $url,
					'link_cd' => $data['Station']['url'],
					'length' => mb_strlen($name)
				);
			}
		}

		return $combined;
	}

	/**
	 * 過去の予約実績から最適な乗り捨て駅を取得する
	 *
	 * @param int[] $officeIds
	 * @param int[] $areaIds
	 * @param int $excludedId 除外する駅ID
	 * @param string $excludedName 除外する駅名
	 * @return array
	 */
	public function getStationFromReturnResult($officeIds, $areaIds, $excludedId = 0, $excludedName = '') {
		if (empty($officeIds) || empty($areaIds)) {
			return array();
		}

		// 直近2か月間を対象とする
		$from = date('Y-m-d 00:00:00', strtotime('-61 day'));
		$to = date('Y-m-d 23:59:59', strtotime('-1 day'));

		$this->virtualFields = array(
			'cnt' => 'count(*)',
			'category' => "'station'",
		);

		// 営業所が属するエリアの中で返却された駅の実績を取得する
		$options = array(
			'fields' => array(
				'Station.id',
				'Station.name',
				'cnt',
				'category',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'OfficeStation',
					'table' => 'office_stations',
					'conditions' => array('Station.id = OfficeStation.station_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => array('Office.id = OfficeStation.office_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Reservation',
					'table' => 'reservations',
					'conditions' => array('Office.id = Reservation.rent_office_id')
				),
			),
			'conditions' => array(
				'Office.area_id' => $areaIds,
				'Reservation.reservation_datetime >=' => $from,
				'Reservation.reservation_datetime <=' => $to,
				'Station.type' => 0,
				'Station.delete_flg' => 0,
				'OfficeStation.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Reservation.delete_flg' => 0,
			),
			'group' => array(
				'Station.id'
			),
			'order' => array(
				'count(*)' => 'desc'
			),
			'recursive' => -1
		);

		$ret1 = $this->find('all', $options);

		// 営業所から返却された駅の実績を取得する
		$options['joins'] = array(
			array(
				'type' => 'INNER',
				'alias' => 'OfficeStation',
				'table' => 'office_stations',
				'conditions' => array('Station.id = OfficeStation.station_id')
			),
			array(
				'type' => 'INNER',
				'alias' => 'Reservation',
				'table' => 'reservations',
				'conditions' => array('Reservation.return_office_id = OfficeStation.office_id')
			),
			array(
				'type' => 'INNER',
				'alias' => 'Office',
				'table' => 'offices',
				'conditions' => array('Office.id = Reservation.rent_office_id')
			),
		);

		$options['conditions'] = array(
			'Office.id' => $officeIds,
			'Reservation.reservation_datetime >=' => $from,
			'Reservation.reservation_datetime <=' => $to,
			'Reservation.rent_office_id != Reservation.return_office_id',
			'Station.type' => 0,
			'Station.delete_flg' => 0,
			'OfficeStation.delete_flg' => 0,
			'Office.delete_flg' => 0,
			'Reservation.delete_flg' => 0,
		);

		$ret2 = $this->find('all', $options);

		$this->virtualFields = null;

		if (empty($ret1) && empty($ret2)) {
			return array();
		}

		// 2つのクエリの結果を交互に組み合わせる
		$stations = array();
		foreach ($ret1 as $k => $v) {
			if ($v['Station']['id'] != $excludedId && $v['Station']['name'] != $excludedName) {
				$stations[$k * 2] = $v;
			}
		}
		foreach ($ret2 as $k => $v) {
			if ($v['Station']['id'] != $excludedId && $v['Station']['name'] != $excludedName) {
				$stations[$k * 2 + 1] = $v;
			}
		}

		$ret = array();
		if (!empty($stations)) {
			// 先頭2件を切り出す
			$stations = array_slice(array_merge($stations), 0, 2);
			$ret = Hash::extract($stations, '{n}.Station');
		}

		return $ret;
	}

	/**
	 * 駅IDから都道府県IDを返す
	 * @param int $airport_id
	 * @return int $prefecture_id
	 */
	public function getPrefectureIdByStationId($stationId) {

		$option = array(
			'fields' => array(
				'Station.prefecture_id'
			),
			'conditions' => array(
				'Station.id' => $stationId,
				'Station.delete_flg' => 0
			),
		);
		return $this->findC('first', $option);
	}
}

<?php

App::uses('AppModel', 'Model');
APP::import('Model', 'Landmark');

/**
 * Area Model
 *
 * @property Staff $Staff
 * @property Landmark $Landmark
 * @property Office $Office
 */
class Area extends AppModel {

	protected $cacheConfig = '1day';

	// Landmarkモデル用ロジック
	private $_landmark = null;

	private function getLandmark() {
		if (!isset($_landmark)) {
			$this->_landmark = new Landmark();
			$this->_landmark->setDataSource($this->getDataSource()->configKeyName);
		}
		return $this->_landmark;
	}

	public function getArea($type = 'all') {

		$options = array(
			'fields' => array('id', 'name', 'prefecture_id'),
			'conditions' => array('delete_flg' => 0),
			'order' => array('sort' => 'asc'),
			'recursive' => -1
		);

		return $this->findC($type, $options);
	}

	public function getAreaByClientId($clientId, $type = 'all') {

		$options = array(
			'fields' => array(
				'Area.id',
				'Area.name',
				'Area.prefecture_id'
			),
			'conditions' => array(
				'Area.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Office.client_id' => $clientId
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => array(
						'Office.area_id = Area.id'
					)
				)
			),
			'order' => array(
				'Area.sort' => 'asc'
			),
			'recursive' => -1
		);

		return $this->findC($type, $options);
	}

	public function getAreaById($id) {

		$options = array(
			'conditions' => array(
				'id' => $id,
				'delete_flg' => 0
			),
			'order' => array('sort' => 'asc'),
			'recursive' => -1
		);

		return $this->findC('all', $options);
	}

	public function getAreaListByPrefectureId($prefectureId, $stockFlg = 0) {
		if ($prefectureId == 0) {
			return array();
		}

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_conf = ($stockFlg == 1) ? '10minutes' : $this->cacheConfig;
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $cache_conf);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$data_arr = $this->findC('list', array(
			'conditions' => array(
				'prefecture_id' => $prefectureId,
				'delete_flg' => 0
			),
			'order' => 'sort asc',
			'recursive' => -1,
		));

		$area_arr = array();
		if ($stockFlg == 1) { // 在庫があるエリアのみ
			list($area_id_arr, $landmark_id_arr) = $this->getLandmark()->getLandmarkIdAndAreaIdArrayInStock();
			foreach ($data_arr as $k => $v) {
				if (in_array($k, $area_id_arr)) {
					$area_arr[$k] = $v;
				}
			}
		} else {
			$area_arr = $data_arr;
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $area_arr, $cache_conf);

		return $area_arr;
	}

	//ClientIdを条件に追加
	public function getAreaListByPrefectureClientId($prefectureId, $stockFlg = 0, $clientId) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_conf = ($stockFlg == 1) ? '10minutes' : $this->cacheConfig;
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $cache_conf);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$data_arr = $this->findC('list', array(
			'conditions' => array(
				'Area.prefecture_id' => $prefectureId,
				'Area.delete_flg' => 0
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => array(
						'Office.area_id = Area.id ',
						'Office.client_id' => $clientId
					)
				),
			),
			'order' => 'Area.sort asc',
			'recursive' => -1,
		), '1hour');

		$area_arr = array();
		if ($stockFlg == 1) { // 在庫があるエリアのみ
			list($area_id_arr, $landmark_id_arr) = $this->getLandmark()->getLandmarkIdAndAreaIdArrayInStock();
			foreach ($data_arr as $k => $v) {
				if (in_array($k, $area_id_arr)) {
					$area_arr[$k] = $v;
				}
			}
		} else {
			$area_arr = $data_arr;
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $area_arr, $cache_conf);

		return $area_arr;
	}

	/**
	 * エリアIDから都道府県IDを取得する
	 * @param type $areaId
	 * @return type
	 */
	public function getPrefectureIdByAreaId($areaId) {
		$option = array(
			'fields' => array(
				'Area.prefecture_id'
			),
			'conditions' => array(
				'Area.id' => $areaId
			),
		);
		return $this->findC('first', $option);
	}

	public function getAreaByAreaLinkCd($areaLinkCd) {
		$option = array(
			'fields' => array(
				'Area.id', 'Area.prefecture_id', 'Area.name',
			),
			'conditions' => array(
				'Area.area_link_cd' => $areaLinkCd,
				'Area.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->findC('all', $option);
	}

	public function getAreaLinkCdListByAreaIds($areaIds) {
		$option = array(
			'fields' => array(
				'Area.area_link_cd'
			),
			'conditions' => array(
				'Area.id' => $areaIds
			),
		);
		$result = $this->find('list', $option);

		$area = array();
		foreach ($result as $key => $val) {
			$area[$key]['area_link_cd'] = $val;
		}
		return $area;
	}

	// 検索ボックス用 全てのエリアのデータを返す
	public function getAreaAll($stockFlg = false) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_conf = ($stockFlg) ? '10minutes' : $this->cacheConfig;
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $cache_conf);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		// 全都道府県のエリア取得
		$ret = array();

		$prefecture_list = $this->getArea('list');

		if ($stockFlg) {
			// 在庫のあるエリア取得
			$area_id_arr = array_flip($this->getLandmark()->getLandmarkIdAndAreaIdArrayInStock()[0]);

			// 在庫のあるエリアの配列を作成
			foreach ($prefecture_list as $pref_k => $pref_v) {
				foreach ($pref_v as $k => $v) {
					if (isset($area_id_arr[$k])) {
						$ret[$pref_k][] = array(
							'id' => $k,
							'name' => $v,
						);
					}
				}
			}
		} else {
			// 全てのエリアの配列を作成
			foreach ($prefecture_list as $pref_k => $pref_v) {
				foreach ($pref_v as $k => $v) {
					$ret[$pref_k][] = array(
						'id' => $k,
						'name' => $v,
					);
				}
			}
		}

		ksort($ret); // 都道府県順にソート
		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $ret, $cache_conf);

		return $ret;
	}

	// 検索ボックス用 指定された会社の営業所があるエリアのデータを返す
	public function getAreaAllByClientId($clientId) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $this->cacheConfig);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		// 全都道府県のエリア取得
		$prefecture_list = $this->getAreaByClientId($clientId, 'list');

		$ret = array();
		// 全てのエリアの配列を作成
		foreach ($prefecture_list as $pref_k => $pref_v) {
			foreach ($pref_v as $k => $v) {
				$ret[$pref_k][] = array(
					'id' => $k,
					'name' => $v,
				);
			}
		}

		ksort($ret); // 都道府県順にソート
		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $ret, $this->cacheConfig);

		return $ret;
	}

	// 内部リンク変換用
	public function getAreaLinkCd($type = 'all') {

		$options = array(
			'fields' => array(
				'Area.area_link_cd',
				'Area.name',
				'Prefecture.region_link_cd',
				'Prefecture.link_cd'
			),
			'conditions' => array('Area.delete_flg' => 0),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => array(
						'Prefecture.id = Area.prefecture_id',
						'Prefecture.delete_flg' => 0
					)
				)
			),
			'order' => array('Area.sort' => 'asc'),
			'recursive' => -1
		);
		$result = $this->findC($type, $options);

		$combined = array();
		if (!empty($result)) {
			foreach ($result as $data) {
				$regionLinkCd = str_replace('area_', '', $data['Prefecture']['region_link_cd']);
				$url = '/rentacar/' . $regionLinkCd . '/';
				if ($regionLinkCd != $data['Prefecture']['link_cd']) {
					$url .= $data['Prefecture']['link_cd'] . '/';
				}
				$url .= $data['Area']['area_link_cd'] . '/';
				$combined[] = array(
					'name' => $data['Area']['name'],
					'url' => $url,
					'link_cd' => $data['Area']['area_link_cd'],
					'length' => mb_strlen($data['Area']['name'])
				);
			}
		}

		return $combined;
	}

	public function getAreaInfoByPrefectureId($prefectureId) {

		$options = array(
			'fields' => array(
				'Area.id',
				'Area.name',
				'Area.area_link_cd'
			),
			'conditions' => array(
				'prefecture_id' => $prefectureId,
				'delete_flg' => 0
			),
			'order' => 'sort asc',
			'recursive' => -1,
		);

		return $this->findC('all', $options);
	}

	public function getViews($area_id){
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_conf = '10minutes';
		$cache_name = $this->getCacheKey($this->getLastModified, __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $cache_conf);

		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$ret = rand(15,30);

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $ret, $cache_conf);

		return $ret;
	}

}

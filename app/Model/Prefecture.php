<?php

App::uses('AppModel', 'Model');
APP::import('Model', 'Landmark');

/**
 * Prefecture Model
 *
 * @property Staff $Staff
 * @property Reservation $Reservation
 */
class Prefecture extends AppModel {

	protected $cacheConfig = '1day';
	// Landmarkモデル用ロジック
	private $_landmark = null;

	private function getLandmark() {
		if (!isset($_landmark)) {
			$this->_landmark = new Landmark();
		}
		return $this->_landmark;
	}

	public function getPrefectureList($stockFlg = false) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_conf = ($stockFlg) ? '10minutes' : $this->cacheConfig;
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $cache_conf);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$ret = array();

		if ($stockFlg) {
			$options = array(
				'fields' => array(
					'Prefecture.id',
					'Prefecture.name',
					'Area.id',
				),
				'conditions' => array(
					'Prefecture.delete_flg' => 0,
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'alias' => 'Area',
						'table' => 'areas',
						'conditions' => 'Prefecture.id = Area.prefecture_id',
					)
				),
				'order' => array(
					'Prefecture.sort',
				)
			);

			$prefecture_list = $this->findC('list', $options);

			// 在庫のあるエリア取得
			$area_id_arr = $this->getLandmark()->getLandmarkIdAndAreaIdArrayInStock()[0];

			// 在庫のあるエリアの配列を作成
			foreach ($area_id_arr as $area_v) {
				if (isset($prefecture_list[$area_v])) {
					$v = $prefecture_list[$area_v];
					$ret[key($v)] = $v[key($v)];
				}
			}
		} else {
			$options = array(
				'conditions' => array(
					'Prefecture.delete_flg' => 0,
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'alias' => 'Area',
						'table' => 'areas',
						'conditions' => 'Prefecture.id = Area.prefecture_id',
					)
				),
				'order' => array(
					'Prefecture.sort',
				)
			);

			$ret = $this->findC('list', $options);
		}

		ksort($ret); // 都道府県順にソート
		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $ret, $cache_conf);

		return $ret;
	}

	// 内部リンク変換用
	public function getPrefectureLinkCdList($type = 'all') {

		$options = array(
			'fields' => array('region_link_cd', 'link_cd', 'name'),
			'conditions' => array('delete_flg' => 0),
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
				$combined[] = array(
					'name' => $data['Prefecture']['name'],
					'url' => $url,
					'link_cd' => $data['Prefecture']['link_cd'],
					'length' => mb_strlen($data['Prefecture']['name'])
				);
			}
		}

		return $combined;
	}

	public function getPrefectureListByRegionLinkCd($regionLinkCd) {
		$options = array(
			'fields' => array(
				'Prefecture.link_cd', 'Prefecture.name', 'Prefecture.id'
			),
			'conditions' => array(
				'Prefecture.delete_flg' => 0,
				'Prefecture.region_link_cd' => $regionLinkCd,
				'Area.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Prefecture.id = Area.prefecture_id',
				)
			),
			'order' => array(
				'Prefecture.sort',
			),
			'recursive' => -1,
		);

		return $this->findC('list', $options);
	}

	public function getNameById($id) {

		$options = array(
			'fields' => array('id', 'name'),
			'conditions' => array('id' => $id),
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getLinkCdAndRegionLinkCdById($id) {

		$options = array(
			'fields' => array('link_cd', 'region_link_cd'),
			'conditions' => array('id' => $id),
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getLinkCdAndRegionLinkCd() {

		$options = array(
			'fields' => array('link_cd', 'region_link_cd', 'name'),
			'conditions' => array('delete_flg' => 0),
			'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function getLinkCdById($id) {

		$options = array(
			'fields' => array('link_cd'),
			'conditions' => array('id' => $id),
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function getRegionLinkCdById($id) {

		$options = array(
			'fields' => array('id', 'region_link_cd'),
			'conditions' => array('id' => $id),
			'recursive' => -1
		);

		return $this->find('list', $options);
	}

	public function getUrlById($id) {
		$ret = $this->findC('first', array(
			'fields' => array('link_cd', 'region_link_cd'),
			'conditions' => array('id' => $id, 'delete_flg' => 0),
			'recursive' => -1,
		));

		$url = '';
		if (!empty($ret)) {
			$regionLinkCd = str_replace('area_', '', $ret['Prefecture']['region_link_cd']);
			$url = $regionLinkCd . '/';
			if ($regionLinkCd != $ret['Prefecture']['link_cd']) {
				$url .= $ret['Prefecture']['link_cd'] . '/';
			}
		}

		return $url;
	}

	public function getPrefectureListInStock() {

		// 在庫のある都道府県を取得
		$Landmark = new Landmark();
		list($area_id_arr, $landmark_id_arr) = $Landmark->getLandmarkIdAndAreaIdArrayInStock();

		if (count($area_id_arr) > 0) {
			return $this->find('list', array(
				'conditions' => array(
					'Prefecture.delete_flg' => 0,
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'alias' => 'Area',
						'table' => 'areas',
						'conditions' => array(
							'Prefecture.id = Area.prefecture_id',
							'Area.id' => $area_id_arr,
						)
					)
				),
				'order' => array(
					'Prefecture.sort',
				)
			));
		} else {
			// 在庫がない時は空配列を返却
			return array();
		}
	}

	public function getAllLinkCdAndRegionLinkCd() {

		$options = array(
			'fields' => array('id', 'link_cd', 'region_link_cd', 'name'),
			'conditions' => array('delete_flg' => 0),
			'recursive' => -1
		);

		$result = $this->findC('all', $options);

		$replaceArea = array();
		foreach ($result as $k => $v) {
			$replaceArea[$k] = $v;
			$replaceArea[$k]['Prefecture']['region_link_cd'] = str_replace('area_', '', $replaceArea[$k]['Prefecture']['region_link_cd']);
		}

		return Hash::combine($replaceArea, '{n}.Prefecture.id', '{n}');
	}
}

<?php

App::uses('AppModel', 'Model');
App::uses('Area', 'Model');
App::uses('Client', 'Model');
App::uses('Landmark', 'Controller');
App::uses('Office', 'Model');
App::uses('Prefecture', 'Model');
App::uses('City', 'Model');
App::uses('Station', 'Model');

/**
 * RcPostmeta Model
 */
class RcPostmeta extends AppModel {
	public $actsAs = array('KeywordReplace');

	public function getPrefecturePostmetaData($prefectureId, $prefectureLinkCd) {
		// postidを取得
		$options = array(
			'fields' => array(
				'RcPost.id'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RcPost',
					'table' => 'rc_posts',
					'conditions' => array(
						'RcPostmeta.post_id = RcPost.id',
						'OR' => array(
							array('RcPost.post_status' => 'publish'),
							array('RcPost.post_status' => 'draft')
						)
					)
				)
			),
			'conditions' => array(
				'RcPostmeta.meta_key' => 'pre-id',
				'RcPostmeta.meta_value' => $prefectureId
			),
			'recursive' => -1
		);

		$rcPostId = $this->find('first', $options);
		$rcPostId = $rcPostId['RcPost']['id'];

		$options = array(
			'conditions' => array(
				'post_id' => $rcPostId
			),
			'recursive' => -1
		);

		$prefectureContents = $this->find('all', $options);

		$prefectureDataArray = array();
		foreach($prefectureContents as $key => $val){
			// 大見出し
			if($val['RcPostmeta']['meta_key'] == 'pre-head'){
				$prefectureDataArray[0]['pre-head'] = $val['RcPostmeta']['meta_value'];
			}
			// 見出し文
			if($val['RcPostmeta']['meta_key'] == 'pre-head-text'){
				$prefectureDataArray[0]['pre-head-text'] = $val['RcPostmeta']['meta_value'];
			}
			// コンテンツ
			if (preg_match('/^pre-contents_([0-9]+)_pre-(head-l|head-s|text|img)$/', $val['RcPostmeta']['meta_key'], $matches)) {
				$metaKeyNum = $matches[1];
				$field = $matches[2];
				if ($field == 'img') {
					// 画像
					$conditions = array(
						'conditions' => array(
							"RcPostmeta.post_id" => $val['RcPostmeta']['meta_value'],
							"RcPostmeta.meta_key" => "_wp_attached_file"
						)
					);
					$imgPathData = $this->find('first', $conditions);
					$prefectureDataArray[$metaKeyNum]['img'] = $imgPathData["RcPostmeta"]["meta_value"];
				} else {
					// 以外（大見出し、小見出し、本文）
					$prefectureDataArray[$metaKeyNum][$field] = $val['RcPostmeta']['meta_value'];
				}
			}
		}

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->name, __FUNCTION__, func_get_args(), $prefectureDataArray);
		$cache_ret = $this->readCache($cache_name, 'contents');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$this->initReplaceKeywordsPrefecture($prefectureLinkCd, $prefectureId);
		foreach ($prefectureDataArray as $k => $v) {
			if (isset($v['pre-head-text'])) {
				$prefectureDataArray[$k]['pre-head-text'] = $this->replaceLinkAll($v['pre-head-text']);
			}
			if (isset($v['text'])) {
				$prefectureDataArray[$k]['text'] = $this->replaceLinkAll($v['text']);
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $prefectureDataArray, 'contents');

		return $prefectureDataArray;
	}

	public function getCompanyPostmetaData($companyId, $companyLinkCd, $fromRentacarClient = false) {

		// postidを取得
		$options = array(
			'fields' => array(
				'RcPost.id'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RcPost',
					'table' => 'rc_posts',
					'conditions' => array(
						'RcPostmeta.post_id = RcPost.id',
						'OR' => array(
							array('RcPost.post_status' => 'publish'),
							array('RcPost.post_status' => 'draft')
						)
					)
				)
			),
			'conditions' => array(
				'RcPostmeta.meta_key' => 'company-id',
				'RcPostmeta.meta_value' => $companyId
			),
			'recursive' => -1
		);

		$rcPostId = $this->find('first', $options);
		$rcPostId = $rcPostId['RcPost']['id'];

		$options = array(
			'conditions' => array(
				'post_id' => $rcPostId,
			),
			'order' => array(
				'meta_key' => 'asc'
			),
			'recursive' => -1
		);

		// 会社の特徴を取得する
		$companyCharacterContents = $this->find('all', $options);

		$data = $this->structureWpData($companyCharacterContents,array('company-outline','company-fee-contents','company-insurance-contents','company-today-contents'));

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->name, __FUNCTION__, func_get_args(), $data);
		$cache_ret = $this->readCache($cache_name, 'contents');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		if (!$fromRentacarClient) {
			$this->initReplaceKeywords($companyLinkCd);
			if (!empty($data['company-pre-contents'])) {
				$data['company-pre-contents'] = $this->replaceLinkAll($data['company-pre-contents']);
			}
			if (!empty($data['company-outline'])) {
				foreach ($data['company-outline'] as $key => $val) {
					if ($val['field'] == 'rentacar-body-text') {
						$data['company-outline'][$key]['value'] = $this->replaceLinkAll($val['value']);
					}
				}
			}
			if (!empty($data['company-fee-contents'])) {
				foreach ($data['company-fee-contents'] as $key => $val) {
					if ($val['field'] == 'rentacar-body-text') {
						$data['company-fee-contents'][$key]['value'] = $this->replaceLinkAll($val['value']);
					}
				}
			}
			if (!empty($data['company-insurance-contents'])) {
				foreach ($data['company-insurance-contents'] as $key => $val) {
					if ($val['field'] == 'rentacar-body-text') {
						$data['company-insurance-contents'][$key]['value'] = $this->replaceLinkAll($val['value']);
					}
				}
			}
			if (!empty($data['company-today-contents'])) {
				foreach ($data['company-today-contents'] as $key => $val) {
					if ($val['field'] == 'rentacar-body-text') {
						$data['company-today-contents'][$key]['value'] = $this->replaceLinkAll($val['value']);
					}
				}
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $data, 'contents');

		return $data;
	}

	public function getShopPostmetaData($shopId) {
		// postidを取得
		$options = array(
			'fields' => array(
				'RcPost.id'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RcPost',
					'table' => 'rc_posts',
					'conditions' => array(
						'RcPostmeta.post_id = RcPost.id',
						'OR' => array(
							array('RcPost.post_status' => 'publish'),
							array('RcPost.post_status' => 'draft')
						)
					)
				)
			),
			'conditions' => array(
				'RcPostmeta.meta_key' => 'shop-id',
				'RcPostmeta.meta_value' => $shopId
			),
			'recursive' => -1
		);

		$rcPostId = $this->find('first', $options);

		if (empty($rcPostId)) {
			return array();
		}

		$rcPostId = $rcPostId['RcPost']['id'];

		$options = array(
			'conditions' => array(
				'post_id' => $rcPostId
			),
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getAirportPostmetaData() {
		$options = array(
			'fields' => array(
				'RcPostmeta.meta_key',
				'RcPostmeta.meta_value'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RcPost',
					'table' => 'rc_posts',
					'conditions' => array(
						'RcPostmeta.post_id = RcPost.id',
						'OR' => array(
							array('RcPost.post_status' => 'publish'),
							array('RcPost.post_status' => 'draft')
						)
					)
				)
			),
			'conditions' => array(
				'OR' => array(
					array('LEFT(RcPostmeta.meta_key,16)' => 'airport-contents',
						'RIGHT(RcPostmeta.meta_key,14)' => 'airport-head-s',
					),
					array('LEFT(RcPostmeta.meta_key,16)' => 'airport-contents',
						'RIGHT(RcPostmeta.meta_key,12)' => 'airport-text',
					),
				),
			),
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getFerryTerminalPostmetaData() {
		$options = array(
			'fields' => array(
				'RcPostmeta.meta_key',
				'RcPostmeta.meta_value'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RcPost',
					'table' => 'rc_posts',
					'conditions' => array(
						'RcPostmeta.post_id = RcPost.id',
						'OR' => array(
							array('RcPost.post_status' => 'publish'),
							array('RcPost.post_status' => 'draft')
						)
					)
				)
			),
			'conditions' => array(
				'OR' => array(
					array('LEFT(RcPostmeta.meta_key,22)' => 'port_terminal-contents',
						'RIGHT(RcPostmeta.meta_key,20)' => 'port_terminal-head-s',
					),
					array('LEFT(RcPostmeta.meta_key,22)' => 'port_terminal-contents',
						'RIGHT(RcPostmeta.meta_key,18)' => 'port_terminal-text',
					),
				),
			),
			'recursive' => -1
		);

		return $this->find('all', $options);
	}

	public function getAirportPostmetaDataByAirportCd($airportCd, $airportLinkCd = '', $prefectureId = null) {

		$postmetaData = null;
		if (empty($airportCd)) {
			return $postmetaData;
		}

		$sql = "SELECT
			RcPostmeta.meta_key
			, RcPostmeta.meta_value
			FROM rentacar.rc_postmetas AS RcPostmeta
			WHERE post_id =
			(
			        SELECT post_id
			        FROM rentacar.rc_postmetas AS RcPostmeta
				INNER JOIN rentacar.rc_posts AS RcPost ON RcPostmeta.post_id = RcPost.id
			        WHERE meta_key = 'airport-single-airport'
			        AND meta_value = :airportCd
			        AND (RcPost.post_status = 'publish' OR RcPost.post_status = 'draft')
			        AND post_type = 'post'
			        LIMIT 1
			)
		";

		$param_arr = array(':airportCd' => $airportCd);

		$data_arr = $this->query($sql, $param_arr);

		foreach ($data_arr as $v) {

			if (strpos($v["RcPostmeta"]["meta_key"], '_') !== 0) {
				$postmetaData[$v["RcPostmeta"]["meta_key"]] = $v["RcPostmeta"]["meta_value"];
				// 行頭に「_」が付いているキーは無視する
			} else {
				continue;
			}

			// レンタカー情報
			if (preg_match('/airport-single-contents_([0-9]+)_airport-single-([a-zA-z\-]+)/', $v["RcPostmeta"]["meta_key"], $match)) {
				$postmetaData['airport-single-contents-list'][$match[1]][$match[2]] = $v["RcPostmeta"]["meta_value"];
			}

			// レンタカー会社の受付カウンターの場所
			if (preg_match('/airport-single-counter-map_([0-9]+)_airport-single-([a-zA-z\-]+)/', $v["RcPostmeta"]["meta_key"], $match)) {
				if ($match[2] == 'counter-point') {
					$v["RcPostmeta"]["meta_value"] = @unserialize($v["RcPostmeta"]["meta_value"]);
				}
				$postmetaData['airport-single-counter-list'][$match[1]][$match[2]] = $v["RcPostmeta"]["meta_value"];
			}
		}

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->name, __FUNCTION__, func_get_args(), $postmetaData);
		$cache_ret = $this->readCache($cache_name, 'contents');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$this->initReplaceKeywordsPrefecture($airportLinkCd, $prefectureId);
		$postmetaData['airport-single-head'] = $this->replaceLinkAll($postmetaData['airport-single-head']);
		$postmetaData['airport-single-shop-text'] = $this->replaceLinkAll($postmetaData['airport-single-shop-text']);
		$postmetaData['airport-single-price-text'] = $this->replaceLinkAll($postmetaData['airport-single-price-text']);
		$postmetaData['airport-single-counter-text'] = $this->replaceLinkAll($postmetaData['airport-single-counter-text']);
		if (!empty($postmetaData['airport-single-contents-list'])) {
			foreach ($postmetaData['airport-single-contents-list'] as $key => $val) {
				$postmetaData['airport-single-contents-list'][$key]['contents-text'] = $this->replaceLinkAll($val['contents-text']);
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $postmetaData, 'contents');

		return $postmetaData;
	}

	public function getRegionPostmetaDataByRegionLinkCd($regionLinkCd) {

		$postmetaData = null;
		if (empty($regionLinkCd)) {
			return $postmetaData;
		}

		$sql = 'SELECT'
				. ' RcPostmeta.meta_key,'
				. ' RcPostmeta.meta_value'
				. ' FROM'
				. ' rentacar.rc_postmetas AS RcPostmeta'
				. ' INNER JOIN'
				. ' rentacar.rc_posts AS RcPost'
				. ' ON'
				. ' RcPostmeta.post_id = RcPost.id'
				. ' INNER JOIN'
				. ' rentacar.rc_postmetas AS RcPostmetaTmp'
				. ' ON'
				. ' RcPostmetaTmp.post_id = RcPost.id'
				. " WHERE RcPost.post_type = 'post'"
				. " AND RcPost.post_status IN ('publish', 'draft')"
				. " AND RcPostmetaTmp.meta_key = 'region-id'"
				. ' AND RcPostmetaTmp.meta_value = :regionLinkCd'
				. " AND RcPostmeta.meta_value != ''"
				// 行頭に「_」が付いているキーは無視する
				. " AND RcPostmeta.meta_key NOT LIKE '\_%'";

		$param_arr = array(':regionLinkCd' => $regionLinkCd);

		$data_arr = $this->query($sql, $param_arr);

		foreach ($data_arr as $v) {
			$meta_key = $v["RcPostmeta"]["meta_key"];
			$meta_value = $v["RcPostmeta"]["meta_value"];
			$added = false;

			// レンタカー情報
			if (preg_match('/prefectures_([0-9]+)_prefectures-([a-zA-z\-]+)/', $meta_key, $match)) {
				$postmetaData['pref_details'][$match[1]][$match[2]] = $meta_value;
				$added = true;
			}

			if (preg_match('/prefectures_([0-9]+)_prefectures-rentacar_([0-9]+)_prefectures-rentacar-([a-zA-z\-]+)/', $meta_key, $match)) {
				$postmetaData['pref_details'][$match[1]]['rows'][$match[2]][$match[3]] = $meta_value;
				$added = true;
			}

			if (!$added) {
				$postmetaData[$meta_key] = $meta_value;
			}
		}
		// メモリ確保
		unset($data_arr);

		$regionPostData = $this->getRegionPostData();

		$postmetaData["region-name"] = $regionPostData[$regionLinkCd];

		return $postmetaData;
	}

	public function getRegionNameByRegionLinkCd($regionLinkCd) {

		$regionNames = $this->getRegionPostData();

		$regionName = $regionNames[$regionLinkCd];

		return $regionName;
	}

	// 順番が保証されないので注意
	public function getRegionPostData($sortFlg = false) {

		$sql = "SELECT post_content FROM rentacar.rc_posts WHERE post_excerpt = 'region-id'";
		$data_arr = $this->query($sql);
		$post_content = @unserialize($data_arr["0"]["rc_posts"]["post_content"]);

		$regionPostData = array();
		foreach ((array) $post_content["choices"] as $key => $val) {
			$regionPostData[$key] = $val;
		}

		if (!$sortFlg) {
			return $regionPostData;
		}

		// ソート順対応
		$sql = 'SELECT DISTINCT region_link_cd FROM rentacar.prefectures'
				. ' WHERE delete_flg = 0 ORDER BY sort';

		$data_arr = $this->query($sql);
		$ret = array();

		foreach ((array) $data_arr as $k => $v) {
			$v = $v['prefectures']['region_link_cd'];
			if (isset($regionPostData[$v])) {
				$ret[$v] = $regionPostData[$v];
			}
		}
		return $ret;
	}

	public function getAreaPostmetaDataByAreaLinkCd($areaLinkCd, $prefectureId = null) {

		$postmetaData = null;
		if (empty($areaLinkCd)) {
			return $postmetaData;
		}

		$sql = "SELECT
			RcPostmeta.meta_key
			, RcPostmeta.meta_value
			FROM rentacar.rc_postmetas AS RcPostmeta
			WHERE post_id = (
			        SELECT post_id
				FROM rentacar.rc_postmetas AS RcPostmeta
				INNER JOIN rentacar.rc_posts AS RcPost ON RcPostmeta.post_id = RcPost.id
			        WHERE meta_key = 'city-single-city'
			        AND meta_value = :areaLinkCd
			        AND (RcPost.post_status = 'publish' OR RcPost.post_status = 'draft')
			        AND post_type = 'post'
			        LIMIT 1
			)
		";

		$param_arr = array(':areaLinkCd' => $areaLinkCd);

		$data_arr = $this->query($sql, $param_arr);

		foreach ($data_arr as $v) {

			if (strpos($v["RcPostmeta"]["meta_key"], '_') !== 0) {
				$postmetaData[$v["RcPostmeta"]["meta_key"]] = $v["RcPostmeta"]["meta_value"];
				// 行頭に「_」が付いているキーは無視する
			} else {
				continue;
			}

			if (!$v["RcPostmeta"]["meta_value"]) {
				continue;
			}

			// レンタカー情報
			if (preg_match('/city-single-contents_([0-9]+)_city-single-contents-([a-zA-z\-]+)/', $v["RcPostmeta"]["meta_key"], $match)) {
				$postmetaData[$match[1]][$match[2]] = $v["RcPostmeta"]["meta_value"];
				unset($postmetaData[$v["RcPostmeta"]["meta_key"]]);
			}

			// キャッチ画像
			if (!empty($postmetaData['city-photo'])) {
				$postmetaData['city-photo-guid'] = $this->getImageUrlById($postmetaData['city-photo']);
			}
		}

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->name, __FUNCTION__, func_get_args(), $postmetaData);
		$cache_ret = $this->readCache($cache_name, 'contents');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$this->initReplaceKeywords($postmetaData['city-single-city'], $prefectureId);
		$postmetaData['city-single-city-head'] = $this->replaceLinkAll($postmetaData['city-single-city-head']);
		$postmetaData['city-single-shop-text'] = $this->replaceLinkAll($postmetaData['city-single-shop-text']);
		$postmetaData['city-single-price-text'] = $this->replaceLinkAll($postmetaData['city-single-price-text']);
		$postmetaData[0]['text'] = $this->replaceLinkAll($postmetaData[0]['text']);
		$postmetaData[1]['text'] = $this->replaceLinkAll($postmetaData[1]['text']);

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $postmetaData, 'contents');

		return $postmetaData;
	}

	function getImageUrlById($id) {

		$image_url = null;

		if (empty($id) OR ! is_numeric($id)) {
			return $image_url;
		}

		$sql = "SELECT guid FROM rentacar.rc_posts AS RcPosts WHERE id = '" . $id . "'";
		$data_arr = $this->query($sql);
		if (!empty($data_arr[0]) AND ! empty($data_arr[0]['RcPosts'])) {
			$image_url = str_replace('http://160.16.81.254/wp/rentacar/wp-content/uploads/', '/rentacar/wp/img/', $data_arr[0]['RcPosts']['guid']);
		}
		return $image_url;
	}

	// キーワード置換用のリストを準備する
	// replaceLinkAll()の前に呼ぶこと
	function initReplaceKeywords($skipLinkCd = '', $prefectureId = null) {
		$Prefecture = new Prefecture();
		$Landmark = new Landmark();
		$Area = new Area();
		$Client = new Client();
		$Office = new Office();
		$City = new City();
		$Station = new Station();

		$prefectureList = $Prefecture->getPrefectureLinkCdList();
		$airportList = $Landmark->getAirportLinkCd();
		$areaList = $Area->getAreaLinkCd();
		$clientList = $Client->getAllClientList();
		$officeList = $Office->getAllClientListByPrefectureId($prefectureId);
		$cityList = $City->getAllCityListByPrefectureId($prefectureId);
		$stationList = $Station->getAllStationListByPrefectureId($prefectureId);

		$keywordList = array_merge($prefectureList, $airportList, $areaList, $clientList, $officeList, $cityList, $stationList);
		// キーワードを長さの降順に処理することで、aタグ多重化を回避
		usort($keywordList, array($this, 'compareKeywords'));

		$this->skipLinkCd = $skipLinkCd;
		$this->keywordList = $keywordList;
		$this->keywordCount = count($keywordList);
		$replaced = array();
		for ($i = 0; $i < $this->keywordCount; ++$i) {
			$replaced[] = false;
		}
		$this->keywordReplaced = $replaced;
	}

	// 都道府県のみのキーワード置換用のリストを準備する
	// replaceLinkAll()の前に呼ぶこと
	function initReplaceKeywordsPrefecture($skipLinkCd = '', $prefectureId = null) {
		$Prefecture = new Prefecture();

		$prefectureList = $Prefecture->getPrefectureLinkCdList();

		$keywordList = $prefectureList;
		// キーワードを長さの降順に処理することで、aタグ多重化を回避
		usort($keywordList, array($this, 'compareKeywords'));

		$this->skipLinkCd = $skipLinkCd;
		$this->keywordList = $keywordList;
		$this->keywordCount = count($keywordList);
		$replaced = array();
		for ($i = 0; $i < $this->keywordCount; ++$i) {
			$replaced[] = false;
		}
		$this->keywordReplaced = $replaced;
	}

	public function getStationPostmetaDataByStationLinkCd($stationLinkCd, $prefectureId = null) {

		$postmetaData = null;
		if (empty($stationLinkCd)) {
			return $postmetaData;
		}

		$sql = "SELECT
			RcPostmeta.meta_key
			, RcPostmeta.meta_value
			FROM rentacar.rc_postmetas AS RcPostmeta
			WHERE post_id = (
			        SELECT post_id
				FROM rentacar.rc_postmetas AS RcPostmeta
				INNER JOIN rentacar.rc_posts AS RcPost ON RcPostmeta.post_id = RcPost.id
			        WHERE meta_key = 'station-single-station'
			        AND meta_value = :stationLinkCd
			        AND (RcPost.post_status = 'publish' OR RcPost.post_status = 'draft')
			        AND post_type = 'post'
			        LIMIT 1
			)
		";

		$param_arr = array(':stationLinkCd' => $stationLinkCd);

		$data_arr = $this->query($sql, $param_arr);

		if (empty($data_arr)) {
			return $postmetaData;
		}

		foreach ($data_arr as $v) {

			if (strpos($v["RcPostmeta"]["meta_key"], '_') !== 0) {
				$postmetaData[$v["RcPostmeta"]["meta_key"]] = $v["RcPostmeta"]["meta_value"];
				// 行頭に「_」が付いているキーは無視する
			} else {
				continue;
			}

			if (!$v["RcPostmeta"]["meta_value"]) {
				continue;
			}

			// レンタカー情報
			if (preg_match('/station-single-contents_([0-9]+)_station-single-contents-([a-zA-z\-]+)/', $v["RcPostmeta"]["meta_key"], $match)) {
				if ($match[2] === 'img') {
					// 画像の場合 URL を取得
					$postmetaData[$match[1]][$match[2]] = $this->getImageUrlById($v["RcPostmeta"]["meta_value"]);
				} else {
					$postmetaData[$match[1]][$match[2]] = $v["RcPostmeta"]["meta_value"];
				}
				unset($postmetaData[$v["RcPostmeta"]["meta_key"]]);
			}

			// キャッチ画像
			if (!empty($postmetaData['station-photo'])) {
				$postmetaData['station-photo-guid'] = $this->getImageUrlById($postmetaData['station-photo']);
			}
		}

		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->name, __FUNCTION__, func_get_args(), $postmetaData);
		$cache_ret = $this->readCache($cache_name, 'contents');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$this->initReplaceKeywords($postmetaData['station-single-station'], $prefectureId);
		$postmetaData['station-single-station-head'] = $this->replaceLinkAll($postmetaData['station-single-station-head']);
		$postmetaData['station-single-shop-text'] = $this->replaceLinkAll($postmetaData['station-single-shop-text']);
		$postmetaData[0]['text'] = $this->replaceLinkAll($postmetaData[0]['text']);
		$postmetaData[1]['text'] = $this->replaceLinkAll($postmetaData[1]['text']);

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $postmetaData, 'contents');

		return $postmetaData;
	}

	public function getStationlistPostmetaData() {

		$postmetaData = null;

		$sql = "SELECT
			RcPostmeta.meta_key
			, RcPostmeta.meta_value
			FROM rentacar.rc_postmetas AS RcPostmeta
			WHERE post_id = (
			        SELECT post_id
				FROM rentacar.rc_postmetas AS RcPostmeta
				INNER JOIN rentacar.rc_posts AS RcPost ON RcPostmeta.post_id = RcPost.id
			        WHERE meta_value = 'stationlist'
			        AND (RcPost.post_status = 'publish' OR RcPost.post_status = 'draft')
			        AND post_type = 'post'
			        LIMIT 1
			)
		";

		$data_arr = $this->query($sql);

		foreach ($data_arr as $v) {

			if (strpos($v["RcPostmeta"]["meta_key"], '_') !== 0) {
				$postmetaData[$v["RcPostmeta"]["meta_key"]] = $v["RcPostmeta"]["meta_value"];
				// 行頭に「_」が付いているキーは無視する
			} else {
				continue;
			}

			if (!$v["RcPostmeta"]["meta_value"]) {
				continue;
			}
		}

		// 繰り返し情報
		if (!empty($postmetaData['rentacar-body'])) {
			$area_blog = unserialize($postmetaData['rentacar-body']);
			foreach ($area_blog as $k => $v) {
				if (preg_match('/rentacar-body-([a-zA-z\-]+)-flex/', $v, $match)) {
					foreach ($postmetaData as $k2 => $v2) {
						if (preg_match('/rentacar-body_' . $k . '_rentacar-body-(' . $match[1] . '.*)/', $k2, $match2)) {
							$postmetaData['rentacar-body-list'][$k][$match2[1]] = $v2;
						}
					}
				}
			}

			// ゴミ情報を削除
			foreach ($postmetaData as $k => $v) {
				if (preg_match('/rentacar-body_([0-9]+)_rentacar-body-([a-zA-z\-_]+)/', $k)) {
					unset($postmetaData[$k]);
				}
			}
		}

		// 繰り返し画像
		if (!empty($postmetaData['rentacar-body-list'])) {
			foreach ($postmetaData['rentacar-body-list'] as $key => $val) {
				// 画像有り
				if (!empty($val['img'])) {
					$postmetaData['rentacar-body-list'][$key]['photo-guid'] = $this->getImageUrlById($val['img']);
				}
			}
		}
		return $postmetaData;
	}

	public function getArticlePostmetaDataByLinkCd($infosLinkCd) {

		$postmetaData = null;

		$sql = "SELECT
			RcPostmeta.meta_key
			, RcPostmeta.meta_value
			FROM rentacar.rc_postmetas AS RcPostmeta
			WHERE post_id = (
			        SELECT post_id
				FROM rentacar.rc_postmetas AS RcPostmeta
				INNER JOIN rentacar.rc_posts AS RcPost ON RcPostmeta.post_id = RcPost.id
			        WHERE meta_value = :infosLinkCd
			        AND (RcPost.post_status = 'publish' OR RcPost.post_status = 'draft')
			        AND post_type = 'post'
			        LIMIT 1
			)
		";

		$param_arr = array(':infosLinkCd' => $infosLinkCd);

		$data_arr = $this->query($sql, $param_arr);

		foreach ($data_arr as $v) {

			if (strpos($v["RcPostmeta"]["meta_key"], '_') !== 0) {
				$postmetaData[$v["RcPostmeta"]["meta_key"]] = $v["RcPostmeta"]["meta_value"];
				// 行頭に「_」が付いているキーは無視する
			} else {
				continue;
			}

			if (!$v["RcPostmeta"]["meta_value"]) {
				continue;
			}
		}

		// 繰り返し情報
		if (!empty($postmetaData['rentacar_body'])) {
			$area_blog = unserialize($postmetaData['rentacar_body']);
			foreach ($area_blog as $k => $v) {
				if (preg_match('/rentacar-body-([a-zA-z\-]+)-flex/', $v, $match)) {
					foreach ($postmetaData as $k2 => $v2) {
						if (preg_match('/rentacar_body_' . $k . '_rentacar-body-(' . $match[1] . '.*)/', $k2, $match2)) {
							$postmetaData['rentacar-body-list'][$k][$match2[1]] = $v2;
						}
					}
				}
				if (preg_match('/wifi-body-([a-zA-z\-]+)-flex/', $v, $match)) {
					foreach ($postmetaData as $k2 => $v2) {
						if (preg_match('/rentacar_body_' . $k . '_rentacar-body-(' . $match[1] . '.*)/', $k2, $match2)) {
							$postmetaData['rentacar-body-list'][$k][$match2[1]] = $v2;
						}
					}
				}
			}

			// ゴミ情報を削除
			foreach ($postmetaData as $k => $v) {
				if (preg_match('/rentacar-body_([0-9]+)_rentacar-body-([a-zA-z\-_]+)/', $k)) {
					unset($postmetaData[$k]);
				}
			}
		}

		// 繰り返し画像
		if (!empty($postmetaData['rentacar-body-list'])) {
			foreach ($postmetaData['rentacar-body-list'] as $key => $val) {
				//画像有り
				if (!empty($val['img'])) {
					$postmetaData['rentacar-body-list'][$key]['photo-guid'] = $this->getImageUrlById($val['img']);
				}
			}
		}

		return $postmetaData;
	}

	public function getCampaignPostmetaData($campaignId) {
		// postidを取得
		$options = array(
			'fields' => array(
				'RcPost.id'
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RcPost',
					'table' => 'rc_posts',
					'conditions' => array(
						'RcPostmeta.post_id = RcPost.id',
						'OR' => array(
							array('RcPost.post_status' => 'publish'),
							array('RcPost.post_status' => 'draft')
						)
					)
				)
			),
			'conditions' => array(
				'RcPostmeta.meta_key' => 'cp-custom-permalink',
				'RcPostmeta.meta_value' => $campaignId
			),
			'recursive' => -1
		);

		$rcPostId = $this->find('first', $options);
		if (!empty($rcPostId)) {
			$rcPostId = $rcPostId['RcPost']['id'];

			$options = array(
				'conditions' => array(
					'post_id' => $rcPostId
				),
				'recursive' => -1
			);

			$rawData = $this->find('all', $options);
			$data = $this->structureWpData($rawData, array('cp-contents', 'cp-price'));
			return $data;
		}
		
		return null;
	}

	// ワードプレスのデータを整理
	private function structureWpData($rawData, $sections){
		$data = array();
		$indexes = array();
		foreach ($rawData as $v) {
			if (in_array($v["RcPostmeta"]["meta_key"], $sections)) {
				if ($this->isSerialized($v["RcPostmeta"]["meta_value"])) {
					$indexes[$v["RcPostmeta"]["meta_key"]] = unserialize($v["RcPostmeta"]["meta_value"]);
				} elseif (is_numeric($v["RcPostmeta"]["meta_value"])) {
					$indexes[$v["RcPostmeta"]["meta_key"]] = intval($v["RcPostmeta"]["meta_value"]);
				}
			} else {
				if (strpos($v["RcPostmeta"]["meta_key"], '_') !== 0) {
					$find = false;
					foreach ($sections as $section) {
						if (strpos($v["RcPostmeta"]["meta_key"], $section.'_') !== false) {
							$find = true;
						}
					}
					if (!$find) {
						if (strpos($v["RcPostmeta"]["meta_key"], 'img') !== false && is_numeric($v["RcPostmeta"]["meta_value"])) {
							$value = $this->getImageUrlById($v["RcPostmeta"]["meta_value"]);
						} else {
							$value = $v["RcPostmeta"]["meta_value"];
						}
						$data[$v["RcPostmeta"]["meta_key"]] = $value;
					}
				}
			}
		}
		
		foreach ($indexes as $k => $index) {
			if (is_array($index)) {
				foreach ($index as $i => $field) {
					$field = str_replace("-flex", "", $field);
					// WPの方のフィルドは正しくネーミングされていないため、例外
					if ($field == 'wifi-body-text') {
						$field = 'rentacar-body-text';
					}
					$key = $k.'_'.$i.'_'.$field;

					foreach ($rawData as $v) {
						if ($v["RcPostmeta"]["meta_key"] == $key) {
							$value = null;
							if ($this->isSerialized($v["RcPostmeta"]["meta_value"])) {
								$value = unserialize($v["RcPostmeta"]["meta_value"]);
							} else {
								if (strpos($v["RcPostmeta"]["meta_key"], 'img') !== false && is_numeric($v["RcPostmeta"]["meta_value"])) {
									$value = $this->getImageUrlById($v["RcPostmeta"]["meta_value"]);
								} else {
									$value = $v["RcPostmeta"]["meta_value"];
								}
							}
							$data[$k][] = array('raw_field' => $v["RcPostmeta"]["meta_key"],
												'field' => $field,
												'value' => $value);
							break;
						}
					}
				}
			} elseif (is_numeric($index)) {
				for ($i = 0; $i < $index; $i++) {
					$key = $k.'_'.$i.'_';
					foreach ($rawData as $v) {
						if (strpos($v["RcPostmeta"]["meta_key"], $key) === 0) {
							$field = str_replace($key, "", $v["RcPostmeta"]["meta_key"]);
							$value = null;
							if ($this->isSerialized($v["RcPostmeta"]["meta_value"])) {
								$value = unserialize($v["RcPostmeta"]["meta_value"]);
							} else {
								if (strpos($v["RcPostmeta"]["meta_key"], 'img') !== false && is_numeric($v["RcPostmeta"]["meta_value"])) {
									$value = $this->getImageUrlById($v["RcPostmeta"]["meta_value"]);
								} else {
									$value = $v["RcPostmeta"]["meta_value"];
								}
							}

							$data[$k][$i][$field] = array('raw_field' => $v["RcPostmeta"]["meta_key"],
												'field' => $field,
												'value' => $value);
						}
					}
				}
			}

		}
		return $data;
	}

	// WPのSerializedデータかどうか
	private function isSerialized($data) {
		if (!is_string($data))
			return false;
		$data = trim($data);
		if ('N;' == $data)
			return true;
		if (!preg_match('/^([adObis]):/', $data, $badions))
			return false;
		switch ($badions[1]) {
			case 'a' :
			case 'O' :
			case 's' :
				if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
					return true;
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if (preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data))
					return true;
				break;
		}
		return false;
	}

}

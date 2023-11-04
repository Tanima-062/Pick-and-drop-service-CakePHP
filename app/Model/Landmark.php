<?php

App::uses('AppModel', 'Model');
APP::import('Model', 'CarClassStock');


/**
 * Landmark Model
 *
 * @property LandmarkCategory $LandmarkCategory
 * @property Area $Area
 * @property Staff $Staff
 * @property Distance $Distance
 * @property LandmarkDescription $LandmarkDescription
 */
class Landmark extends AppModel {

	protected $cacheConfig = '1day';

	private $periodMonthStock = 1;
	// CarClassStockモデル用ロジック
	private $_car_class_stock = null;

	private function getCarClassStock() {
		if (!isset($_car_class_stock)) {
			$this->_car_class_stock = new CarClassStock();
			$this->_car_class_stock->setDataSource($this->getDataSource()->configKeyName);
		}
		return $this->_car_class_stock;
	}


	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'landmark_category_id' => array(
			'numeric' => array()
		// 'message' => 'Your custom message here',
		// 'allowEmpty' => false,
		// 'required' => false,
		// 'last' => false, // Stop validation after this rule
		// 'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
		'area_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		// 'message' => 'Your custom message here',
		// 'allowEmpty' => false,
		// 'required' => false,
		// 'last' => false, // Stop validation after this rule
		// 'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
		'name' => array(
			'notempty' => array(
				'rule' => array(
					'notempty'
				)
			)
		// 'message' => 'Your custom message here',
		// 'allowEmpty' => false,
		// 'required' => false,
		// 'last' => false, // Stop validation after this rule
		// 'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array(
					'numeric'
				)
			)
		// 'message' => 'Your custom message here',
		// 'allowEmpty' => false,
		// 'required' => false,
		// 'last' => false, // Stop validation after this rule
		// 'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array(
					'boolean'
				)
			)
		// 'message' => 'Your custom message here',
		// 'allowEmpty' => false,
		// 'required' => false,
		// 'last' => false, // Stop validation after this rule
		// 'on' => 'create', // Limit validation to 'create' or 'update' operations
		)
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'LandmarkCategory' => array(
			'className' => 'LandmarkCategory',
			'foreignKey' => 'landmark_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Area' => array(
			'className' => 'Area',
			'foreignKey' => 'area_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Distance' => array(
			'className' => 'Distance',
			'foreignKey' => 'landmark_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'LandmarkDescription' => array(
			'className' => 'LandmarkDescription',
			'foreignKey' => 'landmark_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	//リンク用コードから空港を返却する
	public function getAirportByLinkCd($link_cd) {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.prefecture_id',
				'Landmark.name',
				'Landmark.airport_id',
				'Landmark.iata_cd AS airport_cd',
			),
			'conditions' => array(
				'Landmark.link_cd' => $link_cd,
				'Landmark.landmark_category_id' => 1,
				'Landmark.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('first', $options);
	}

	public function getAirportById($id) {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.prefecture_id',
				'Landmark.name',
				'Landmark.airport_id',
				'Landmark.iata_cd AS airport_cd',
				'Landmark.link_cd'
			),
			'conditions' => array(
				'Landmark.id' => $id,
				'Landmark.landmark_category_id' => 1,
				'Landmark.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('first', $options);
	}

	public function getFerryTerminalByLinkCd($link_cd) {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.prefecture_id',
				'Landmark.name',
				'Landmark.airport_id',
				'Landmark.iata_cd AS airport_cd',
			),
			'conditions' => array(
				'Landmark.link_cd' => $link_cd,
				'Landmark.landmark_category_id' => 3,
				'Landmark.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('first', $options);
	}

	public function getFerryTerminalById($id) {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.prefecture_id',
				'Landmark.name',
				'Landmark.airport_id',
				'Landmark.iata_cd AS airport_cd',
				'Landmark.link_cd'
			),
			'conditions' => array(
				'Landmark.id' => $id,
				'Landmark.landmark_category_id' => 3,
				'Landmark.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('first', $options);
	}

	public function getAllLandmarks() {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, '10minutes');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$landmarks = $this->findC('list', array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'LandmarkCategory',
					'table' => 'landmark_categories',
					'conditions' => 'LandmarkCategory.id = Landmark.landmark_category_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => 'Prefecture.id = Landmark.prefecture_id',
				),
			),
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'LandmarkCategory.delete_flg' => 0,
				'Prefecture.delete_flg' => 0,
			),
			'order' => array(
				'Prefecture.sort',
				'Prefecture.id',
				'Landmark.sort',
				'Landmark.id',
			),
			'recursive' => -1,
		));

		// 在庫があるエリアID,ランドマークIDの配列を取得
		list($area_id_arr, $landmark_id_arr) = $this->getLandmarkIdAndAreaIdArrayInStock();

		$landmarkArray = array();
		
		foreach ($landmarks as $prefectureName => $landmark) {
			foreach ($landmark as $landmarkId => $val) {
				$landmarkArray['Array'][$prefectureName][$landmarkId] = $val;

				// 在庫があるランドマークのみ
				if (in_array($landmarkId, $landmark_id_arr)) {
					$landmarkArray['ArrayInStock'][$prefectureName][$landmarkId] = $val;
				}
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $landmarkArray, '10minutes');

		return $landmarkArray;
	}

	public function getAllLandmarksByClientId($clientId) {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, $this->cacheConfig);
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$landmarks = $this->findC('list', array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'LandmarkCategory',
					'table' => 'landmark_categories',
					'conditions' => 'LandmarkCategory.id = Landmark.landmark_category_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => 'Prefecture.id = Landmark.prefecture_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => 'Office.airport_id = Landmark.id',
				),
			),
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'LandmarkCategory.delete_flg' => 0,
				'Prefecture.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'Office.client_id' => $clientId,
			),
			'order' => array(
				'Prefecture.sort',
				'Prefecture.id',
				'Landmark.sort',
				'Landmark.id',
			),
			'recursive' => -1,
		));

		$landmarkArray = array(
			'Array' => $landmarks,
			'ArrayInStock' => $landmarks
		);

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $landmarkArray, $this->cacheConfig);

		return $landmarkArray;
	}

	public function getFerryTerminalArrayList() {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, '10minutes');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$prefectures = $this->findC('all', array(
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'Landmark.landmark_category_id' => 3
			),
			'joins' => array(
				array(
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => array(
						'Prefecture.id = Landmark.prefecture_id',
						'Prefecture.delete_flg = 0'
					)
				)
			),
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.landmark_category_id',
				'Prefecture.name'
			),
			'recursive' => - 1,
			'order' => array(
				'Prefecture.sort',
				'Prefecture.id',
				'Landmark.sort',
				'Landmark.id',
			),
		));

		// 在庫があるエリアID,ランドマークIDの配列を取得
		list($area_id_arr, $landmark_id_arr) = $this->getLandmarkIdAndAreaIdArrayInStock();

		$landMarkArray = array();
		$landmarkArray['airportArrayInStock'] = array();

		foreach ($prefectures as $val) {
			//都道府県がなければcontinue
			if (empty($val['Prefecture']['name'])) {
				continue;
			}
			$prefectureName = $val['Prefecture']['name'];
			$landmarkId = $val['Landmark']['id'];
			$landmarkArray['airportArray'][$prefectureName][$landmarkId] = $val['Landmark']['name'];

			// 在庫がある空港のみ
			if (in_array($landmarkId, $landmark_id_arr)) {
				$landmarkArray['airportArrayInStock'][$prefectureName][$landmarkId] = $val['Landmark']['name'];
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $landmarkArray, '10minutes');

		return $landmarkArray;
	}
	
	public function getAirportAndBulletTrainArrayList() {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, '10minutes');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$bullet_trains = $this->findC('all', array(
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'Landmark.landmark_category_id' => 2
			),
			'joins' => array(
				array(
					'alias' => 'BulletTrainArea',
					'table' => 'bullet_train_areas',
					'conditions' => array(
						'BulletTrainArea.id = Landmark.bullet_train_area_id',
						'BulletTrainArea.delete_flg = 0'
					)
				),
			),
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.landmark_category_id',
				'BulletTrainArea.name',
			),
			'recursive' => - 1,
			'order' => array(
				'Landmark.sort',
			),
		));
		$prefectures = $this->findC('all', array(
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'Landmark.landmark_category_id' => 1
			),
			'joins' => array(
				array(
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => array(
						'Prefecture.id = Landmark.prefecture_id',
						'Prefecture.delete_flg = 0'
					)
				)
			),
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.landmark_category_id',
				'Prefecture.name'
			),
			'recursive' => - 1,
			'order' => array(
				'Prefecture.sort',
				'Prefecture.id',
				'Landmark.sort',
				'Landmark.id',
			),
		));

		// 在庫があるエリアID,ランドマークIDの配列を取得
		list($area_id_arr, $landmark_id_arr) = $this->getLandmarkIdAndAreaIdArrayInStock();

		$landMarkArray = array();
		$landmarkArray['airportArrayInStock'] = array();
		$landmarkArray['bulletTrainArrayInStock'] = array();

		foreach ($bullet_trains as $val) {
			//都道府県がなければcontinue
			if (empty($val['BulletTrainArea']['name'])) {
				continue;
			}
			$bulletTrainAreaName = $val['BulletTrainArea']['name'];
			$landmarkId = $val['Landmark']['id'];
			$landmarkArray['bulletTrainArray'][$bulletTrainAreaName][$landmarkId] = $val['Landmark']['name'];

			// 在庫がある新幹線のみ
			if (in_array($landmarkId, $landmark_id_arr)) {
				$landmarkArray['bulletTrainArrayInStock'][$bulletTrainAreaName][$landmarkId] = $val['Landmark']['name'];
			}
		}

		foreach ($prefectures as $val) {
			//都道府県がなければcontinue
			if (empty($val['Prefecture']['name'])) {
				continue;
			}
			$prefectureName = $val['Prefecture']['name'];
			$landmarkId = $val['Landmark']['id'];
			$landmarkArray['airportArray'][$prefectureName][$landmarkId] = $val['Landmark']['name'];

			// 在庫がある空港のみ
			if (in_array($landmarkId, $landmark_id_arr)) {
				$landmarkArray['airportArrayInStock'][$prefectureName][$landmarkId] = $val['Landmark']['name'];
			}
		}

		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $landmarkArray, '10minutes');

		return $landmarkArray;
	}

	//------------------------------------------------------
	// 在庫があるエリアID,ランドマークIDの配列を取得
	//------------------------------------------------------
	public function getLandmarkIdAndAreaIdArrayInStock() {
		// 返り値をキャッシュから取得 ※取り扱い注意
		$cache_name = $this->getCacheKey($this->getLastModified(), __FUNCTION__, func_get_args());
		$cache_ret = $this->readCache($cache_name, '10minutes');
		if ($cache_ret !== false) {
			return $cache_ret;
		}

		$options = array(
			'fields' => array(
				'CarClassStock.stock_group_id',
				'CarClassStock.stock_group_id',
			),
			'conditions' => array(
				'stock_date BETWEEN NOW() AND NOW() + INTERVAL ? MONTH' => $this->periodMonthStock,
				'stock_count >' => 0,
				'suspension' => 0,
				'delete_flg' => 0,
			),
			'group' => array('CarClassStock.stock_group_id'),
			'recursive' => -1,
		);

		$stockGroupIds = $this->getCarClassStock()->findC('list', $options, '10minutes');

		$sql = '
			SELECT DISTINCT
			  o.id
			  , o.area_id
			  , o.airport_id
			  , o.bullet_train_id
			  , osg.stock_group_id
			FROM
			  rentacar.offices AS o
			  INNER JOIN rentacar.office_stock_groups AS osg
			    ON o.id = osg.office_id
			  INNER JOIN rentacar.commodity_rent_offices AS cro
			    ON o.id = cro.office_id
			  INNER JOIN rentacar.clients AS c
			    ON c.id = o.client_id
			  INNER JOIN rentacar.commodity_terms AS ct
			    ON ct.commodity_id = cro.commodity_id
			  INNER JOIN rentacar.commodities AS co
			    ON co.id = ct.commodity_id
			WHERE
			  o.delete_flg = 0
			  AND osg.delete_flg = 0
			  AND cro.delete_flg = 0
			  AND c.delete_flg = 0
			  AND ct.available_to > NOW()
			  AND ct.delete_flg = 0
			  AND co.is_published = 1
			  AND co.delete_flg = 0
		';

		// 明示的にクエリキャッシュ期限を指定
		$data_arr = $this->queryC($sql, array(), '10minutes');

		$landmark_id_arr = array();
		$area_id_arr = array();
		$office_id_arr = array();
		foreach ($data_arr as $v) {
			if (!isset($stockGroupIds[$v['osg']['stock_group_id']])) {
				continue;
			}
			if (!empty($v["o"]["id"])) {
				$office_id_arr[$v["o"]["id"]] = true;
			}
			if (!empty($v["o"]["area_id"])) {
				$area_id_arr[$v["o"]["area_id"]] = true;
			}
			if (!empty($v["o"]["airport_id"])) {
				$landmark_id_arr[$v["o"]["airport_id"]] = true;
			}
			if (!empty($v["o"]["bullet_train_id"])) {
				$landmark_id_arr[$v["o"]["bullet_train_id"]] = true;
			}
		}
		// 重複をマージ
		$landmark_id_arr = array_keys($landmark_id_arr);
		$area_id_arr = array_keys($area_id_arr);
		$office_id_arr = array_keys($office_id_arr);

		$ret = array($area_id_arr, $landmark_id_arr, $office_id_arr);
		// 返り値をキャッシュに設定 ※取り扱い注意
		$this->writeCache($cache_name, $ret, '10minutes');

		return $ret;
	}

	public function getAirportAndBulletTrainArrayListByClientId($clientId) {

		$bullet_trains = $this->find('all', array(
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'Landmark.landmark_category_id' => 2
			),
			'joins' => array(
				array(
					'alias' => 'BulletTrainArea',
					'table' => 'bullet_train_areas',
					'conditions' => array(
						'BulletTrainArea.id = Landmark.bullet_train_area_id',
						'BulletTrainArea.delete_flg = 0'
					)
				),
			),
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.landmark_category_id',
				'BulletTrainArea.name',
			),
			'recursive' => - 1,
			'order' => array(
				'Landmark.sort', 'Landmark.id',
			),
		));
		$prefectures = $this->find('all', array(
			'conditions' => array(
				'Landmark.delete_flg' => 0,
				'Landmark.landmark_category_id' => 1
			),
			'joins' => array(
				array(
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => array(
						'Prefecture.id = Landmark.prefecture_id',
						'Prefecture.delete_flg = 0'
					)
				)
			),
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.landmark_category_id',
				'Prefecture.name'
			),
			'recursive' => - 1,
			'order' => array(
				'Prefecture.sort',
				'Prefecture.id',
				'Landmark.sort',
				'Landmark.id',
			),
		));

		// 在庫があるエリアID,ランドマークIDの配列を取得
		list($area_id_arr, $landmark_id_arr) = $this->getLandmarkIdAndAreaIdArrayInStockByClientId($clientId);

		$landMarkArray = array();
		$landmarkArray['airportArrayInStock'] = array();
		$landmarkArray['bulletTrainArrayInStock'] = array();

		foreach ($bullet_trains as $val) {
			//都道府県がなければcontinue
			if (empty($val['BulletTrainArea']['name'])) {
				continue;
			}
			$bulletTrainAreaName = $val['BulletTrainArea']['name'];
			$landmarkId = $val['Landmark']['id'];
			$landmarkArray['bulletTrainArray'][$bulletTrainAreaName][$landmarkId] = $val['Landmark']['name'];

			// 在庫がある新幹線のみ
			if (in_array($landmarkId, $landmark_id_arr)) {
				$landmarkArray['bulletTrainArrayInStock'][$bulletTrainAreaName][$landmarkId] = $val['Landmark']['name'];
			}
		}

		foreach ($prefectures as $val) {
			//都道府県がなければcontinue
			if (empty($val['Prefecture']['name'])) {
				continue;
			}
			$prefectureName = $val['Prefecture']['name'];
			$landmarkId = $val['Landmark']['id'];
			$landmarkArray['airportArray'][$prefectureName][$landmarkId] = $val['Landmark']['name'];

			// 在庫がある空港のみ
			if (in_array($landmarkId, $landmark_id_arr)) {
				$landmarkArray['airportArrayInStock'][$prefectureName][$landmarkId] = $val['Landmark']['name'];
			}
		}

		return $landmarkArray;
	}

	//------------------------------------------------------
	// 顧客IDに紐付く在庫があるエリアID,ランドマークIDの配列を取得
	//------------------------------------------------------
	public function getLandmarkIdAndAreaIdArrayInStockByClientId($clientId) {
		$sql = '
			SELECT DISTINCT
			  o.area_id
			  , o.airport_id
			  , o.bullet_train_id
			FROM
			  rentacar.offices AS o
			  INNER JOIN rentacar.office_stock_groups AS osg
			    ON o.id = osg.office_id
			  INNER JOIN rentacar.car_class_stocks AS ccs
			    ON osg.stock_group_id = ccs.stock_group_id
			WHERE
			  o.client_id = :clientId
			  AND o.delete_flg = 0
			  AND osg.delete_flg = 0
			  AND ccs.stock_date BETWEEN NOW() AND NOW() + INTERVAL :interval MONTH
			  AND ccs.stock_count > 0
			  AND ccs.suspension = 0
			  AND ccs.delete_flg = 0
		';

		$param_arr = array(
			'clientId' => $clientId,
			'interval' => $this->periodMonthStock,
		);

		// 明示的にクエリキャッシュ期限を指定
		$data_arr = $this->queryC($sql, $param_arr, '10minutes');

		$landmark_id_arr = array();
		$area_id_arr = array();
		foreach ($data_arr as $v) {
			if (!empty($v["o"]["area_id"])) {
				$area_id_arr[] = $v["o"]["area_id"];
			}
			if (!empty($v["o"]["airport_id"])) {
				$landmark_id_arr[] = $v["o"]["airport_id"];
			}
			if (!empty($v["o"]["bullet_train_id"])) {
				$landmark_id_arr[] = $v["o"]["bullet_train_id"];
			}
		}
		// 重複をマージ
		$landmark_id_arr = array_values(array_unique($landmark_id_arr));
		$area_id_arr = array_values(array_unique($area_id_arr));

		return array($area_id_arr, $landmark_id_arr);
	}

	public function getAirportLinkCdList() {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.link_cd',
			),
			'conditions' => array(
				'Landmark.landmark_category_id' => 1,
				'Landmark.link_cd IS NOT NULL',
				'Landmark.delete_flg' => 0,
			),
			'order' => array(
				'Landmark.sort',
				'Landmark.id',
			),
			'recursive' => -1,
		);

		return $this->findC('list', $options);
	}

	public function getFerryTerminalLinkCdList() {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.link_cd',
			),
			'conditions' => array(
				'Landmark.landmark_category_id' => 3,
				'Landmark.link_cd IS NOT NULL',
				'Landmark.delete_flg' => 0,
			),
			'order' => array(
				'Landmark.sort',
				'Landmark.id',
			),
			'recursive' => -1,
		);

		return $this->findC('list', $options);
	}

	public function getAirportLinkCdListByPrefectureId($prefectureId) {
		$options = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.short_name',
				'Landmark.link_cd',
			),
			'conditions' => array(
				'Landmark.prefecture_id' => $prefectureId,
				'Landmark.landmark_category_id' => 1,
				'Landmark.link_cd IS NOT NULL',
				'Landmark.delete_flg' => 0,
			),
			'order' => array(
				'Landmark.sort',
				'Landmark.id',
			),
			'recursive' => -1
		);

		$ret = $this->findC('all', $options);
		
		return Hash::combine($ret, '{n}.Landmark.link_cd', '{n}.Landmark');
	}

	public function getAirportLinkCd() {

		$sql = "
			SELECT
			  l.link_cd
			  , l.short_name
			  , p.region_link_cd
			  , p.link_cd
			FROM
			  rentacar.landmarks AS l
			  INNER JOIN rentacar.prefectures AS p
			    ON p.id = l.prefecture_id
			WHERE
			  l.landmark_category_id = 1
			  AND l.delete_flg = 0
			  AND p.delete_flg = 0
		";

		$data_arr = $this->queryC($sql);

		if (empty($data_arr)) {
			return array();
		}
		
		$combined = array();
		foreach ($data_arr as $v) {
			$regionLinkCd = str_replace('area_', '', $v['p']['region_link_cd']);
			$url = '/rentacar/' . $regionLinkCd . '/';
			if ($regionLinkCd != $v['p']['link_cd']) {
				$url .= $v['p']['link_cd'] . '/';
			}
			$url .= $v['l']['link_cd'] . '/';
			$combined[] = array(
				'name' => $v['l']['short_name'],
				'url' => $url,
				'link_cd' => $v['l']['link_cd'],
				'length' => mb_strlen($v['l']['short_name'])
			);
		}

		return $combined;
	}

	//------------------------------------------------------
	// 新幹線駅IDから駅IDへの変換リスト
	//------------------------------------------------------
	public function getConversionStationList() {
		$ret = $this->findC('list', array(
			'fields' => array(
				'Station.prefecture_id',
				'Station.id',
				'Landmark.id',
			),
			'joins' => array(
				array(
					'alias' => 'Station',
					'table' => 'stations',
					'type' => 'INNER',
					'conditions' => array(
						'Station.id = Landmark.travelko_id',
					),
				),
			),
			'conditions' => array(
				'Landmark.landmark_category_id' => 2,
				'Landmark.delete_flg' => false,
				'Station.delete_flg' => false,
			),
			'recursive' => -1,
		));

		return $ret;
	}

	/**
	 * 座標に最寄りの空港を返す
	 * 地球の丸みを考慮していないのでdistanceをそのまま距離として使ってはいけない
	 *
	 * @param double $lat
	 * @param double $lng
	 * @return array
	 */
	public function getNearestAirport($lat, $lng) {
		if (!is_numeric($lat) || !is_numeric($lng)) {
			return array();
		}

		$params = array(
			'lat' => $lat,
			'lng' => $lng,
		);

		$sql = "
			SELECT
			  l.id
			  , l.name
			  , l.short_name
			  , POWER(ABS(l.latitude - :lat), 2) + POWER(ABS(l.longitude - :lng), 2) distance
			FROM
			  rentacar.landmarks AS l
			WHERE
			  l.landmark_category_id = 1
			  AND l.delete_flg = 0
			  AND l.latitude is not null
			  AND l.longitude is not null
			ORDER BY
			  distance
			LIMIT
			  1
		";

		$ret = $this->queryC($sql, $params);

		if (empty($ret)) {
			return array();
		}

		$ret = $ret[0]['l'];

		// 直近2か月間を対象とする
		$from = date('Y-m-d 00:00:00', strtotime('-61 day'));
		$to = date('Y-m-d 23:59:59', strtotime('-1 day'));

		// 予約実績を追加する
		$options = array(
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => array('Landmark.id = Office.airport_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Reservation',
					'table' => 'reservations',
					'conditions' => array('Office.id = Reservation.return_office_id')
				),
			),
			'conditions' => array(
				'Landmark.id' => $ret['id'],
				'Reservation.reservation_datetime >=' => $from,
				'Reservation.reservation_datetime <=' => $to,
				'Reservation.rent_office_id != Reservation.return_office_id'
			),
			'recursive' => -1,
		);

		$cnt = $this->findC('count', $options);
		$ret['cnt'] = $cnt;
		$ret['category'] = 'airport';

		return $ret;
	}

	/**
	 * 空港IDから都道府県IDを返す
	 * @param int $airport_id
	 * @return int $prefecture_id
	 */
	public function getPrefectureIdByAirportId($airport_id) {

		$option = array(
			'fields' => array(
				'Landmark.prefecture_id',
			),
			'conditions' => array(
				'Landmark.id' => $airport_id,
				'Landmark.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('first', $option);
	}

	/**
	 * 空港IDからランドマーク情報を返す
	 * @param int $airport_id
	 * @return int $prefecture_id
	 */
	public function getLandmarkByAirportId($airport_id) {

		$option = array(
			'fields' => array(
				'Landmark.id',
				'Landmark.name',
				'Landmark.prefecture_id',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'alias' => 'Prefecture',
					'table' => 'prefectures',
					'conditions' => array(
						'Prefecture.id = Landmark.prefecture_id',
						'Prefecture.delete_flg = 0'
					)
				)
			),
			'conditions' => array(
				'Landmark.id' => $airport_id,
				'Landmark.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->findC('first', $option);
	}
}

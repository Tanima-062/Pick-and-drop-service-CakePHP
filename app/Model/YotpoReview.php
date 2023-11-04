<?php
App::uses('AppModel','Model');
App::uses('Office', 'Model');
App::uses('Area', 'Model');
App::uses('City', 'Model');
require_once("encrypt_class.php");

/**
 * YotpoReview Model
 */
class YotpoReview extends AppModel {

	protected $cacheConfig = '1hour';

	protected $limit = 100;

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'review_id'=>array(
			'numeric'=>array(
				'rule'=>array(
					'numeric'
				)
			)
		),
		'title'=>array(
			'notempty'=>array(
				'rule'=>array(
					'notempty'
				)
			)
		),
		'content'=>array(
			'notempty'=>array(
				'rule'=>array(
					'notempty'
				)
			)
		),
		'score'=>array(
			'numeric'=>array(
				'rule'=>array(
					'numeric'
				)
			)
		),
		'votes_up'=>array(
			'numeric'=>array(
				'rule'=>array(
					'numeric'
				)
			)
		),
		'votes_down'=>array(
			'numeric'=>array(
				'rule'=>array(
					'numeric'
				)
			)
		),
		'created_at'=>array(
			'datetime'=>array(
				'rule'=>array(
					'datetime'
				)
			)
		),
		'updated_at'=>array(
			'datetime'=>array(
				'rule'=>array(
					'datetime'
				)
			)
		),
		'sku'=>array(
			'notempty'=>array(
				'rule'=>array(
					'notempty'
				)
			)
		),
		'name_enc'=>array(
			'notempty'=>array(
				'rule'=>array(
					'notempty'
				)
			)
		),
		'email_enc'=>array(
			'notempty'=>array(
				'rule'=>array(
					'notempty'
				)
			)
		),
		'reviewer_type'=>array(
			'notempty'=>array(
				'rule'=>array(
					'notempty'
				)
			)
		),
		'unpublished'=>array(
			'boolean'=>array(
				'rule'=>array(
					'boolean'
				)
			)
		),
		'client_id'=>array(
			'numeric'=>array(
				'rule'=>array(
					'numeric'
				)
			)
		),
		'office_id'=>array(
			'numeric'=>array(
				'rule'=>array(
					'numeric'
				)
			)
		),
		'delete_flg'=>array(
			'boolean'=>array(
				'rule'=>array(
					'boolean'
				)
			)
		)
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
	);

	public function setLimit($limit){
		$this->limit = $limit;
	}

	/**
	 * 会社別レーティング取得
	 */
	public function getRatingsGroupByClientId() {
		$options = array(
			'fields'=>array(
				'YotpoReview.client_id',
				'AVG(YotpoReview.score) AS rating',
				'COUNT(*) AS count',
			),
			'conditions' => array(
				'YotpoReview.unpublished' => 0,
			),
			'group' => array(
				'YotpoReview.client_id'
			),
			'order' => array(
				'count DESC',
				'rating DESC'
			)
		);

		$result = $this->findC('all', $options);
		if (!empty($result)) {
			$result = Hash::combine($result, '{n}.YotpoReview.client_id', '{n}.0');
		}

		return $result;
	}

	/**
	 * 指定地方の店舗に寄せられたレビューを取得
	 */
	public function getReviewsByRegionLinkCd($regionLinkCd) {
		$options = array(
			'fields'=>array(
				'YotpoReview.title',
				'YotpoReview.content',
				'YotpoReview.score',
				"DATE_FORMAT(YotpoReview.created_at, '%Y-%m-%d') as created_at",
				'YotpoReview.reviewer_type',
				'Client.id',
				'Client.name',
				'Client.url',
				'Office.name',
				'Office.url',
			),
			'conditions' => array(
				'Prefecture.region_link_cd' => $regionLinkCd,
				'Prefecture.delete_flg' => 0,
				'Area.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Area',
					'table'=>'areas',
					'conditions'=>'Area.id = Office.area_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Prefecture',
					'table'=>'prefectures',
					'conditions'=>'Prefecture.id = Area.prefecture_id'
				)
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('all', $options);
	}

	/**
	 * 指定地方の店舗に寄せられたレビュー数を取得
	 */
	public function getReviewCountByRegionLinkCd($regionLinkCd) {
		$options = array(
			'conditions' => array(
				'Prefecture.region_link_cd' => $regionLinkCd,
				'Prefecture.delete_flg' => 0,
				'Area.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Area',
					'table'=>'areas',
					'conditions'=>'Area.id = Office.area_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Prefecture',
					'table'=>'prefectures',
					'conditions'=>'Prefecture.id = Area.prefecture_id'
				)
			),
		);

		$result = $this->findC('count', $options);

		return $result;
	}

	/**
	 * 指定都道府県の店舗に寄せられたレビューを取得
	 */
	public function getReviewsByPrefectureId($prefectureId) {
		$options = array(
			'fields'=>array(
				'YotpoReview.title',
				'YotpoReview.content',
				'YotpoReview.score',
				"DATE_FORMAT(YotpoReview.created_at, '%Y-%m-%d') as created_at",
				'YotpoReview.reviewer_type',
				'Client.id',
				'Client.name',
				'Client.url',
				'Office.name',
				'Office.url',
			),
			'conditions' => array(
				'Area.prefecture_id' => $prefectureId,
				'Area.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Area',
					'table'=>'areas',
					'conditions'=>'Area.id = Office.area_id'
				)
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('all', $options);
	}

	/**
	 * 指定都道府県の店舗に寄せられたレビュー数を取得
	 */
	public function getReviewCountByPrefectureId($prefectureId) {
		$options = array(
			'conditions' => array(
				'Area.prefecture_id' => $prefectureId,
				'Area.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Area',
					'table'=>'areas',
					'conditions'=>'Area.id = Office.area_id'
				)
			),
		);

		$result = $this->findC('count', $options);

		return $result;
	}

	/**
	 * エリアに寄せられたレビューを取得
	 */
	public function getReviewsByAreaId($areaId) {
		$options = array(
			'fields'=>array(
				'YotpoReview.title',
				'YotpoReview.content',
				'YotpoReview.score',
				"DATE_FORMAT(YotpoReview.created_at, '%Y-%m-%d') as created_at",
				'YotpoReview.reviewer_type',
				'Client.id',
				'Client.name',
				'Client.url',
				'Office.name',
				'Office.url',
			),
			'conditions' => array(
				'Office.area_id' => $areaId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('all', $options);
	}

	/**
	 * エリアに寄せられたレビュー数を取得
	 */
	public function getReviewsCountByAreaId($areaId) {
		$options = array(
			'conditions' => array(
				'Office.area_id' => $areaId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('count', $options);
	}

	/**
	 * 都市に寄せられたレビューを取得
	 */
	public function getReviewsByCityId($cityId) {
		$options = array(
			'fields'=>array(
				'YotpoReview.title',
				'YotpoReview.content',
				'YotpoReview.score',
				"DATE_FORMAT(YotpoReview.created_at, '%Y-%m-%d') as created_at",
				'YotpoReview.reviewer_type',
				'Client.id',
				'Client.name',
				'Client.url',
				'Office.name',
				'Office.url',
			),
			'conditions' => array(
				'Office.city_id' => $cityId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('all', $options);
	}

	/**
	 * 都市に寄せられたレビュー数を取得
	 */
	public function getReviewsCountByCityId($cityId) {
		$options = array(
			'conditions' => array(
				'Office.city_id' => $cityId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('count', $options);
	}

	/**
	 * 店舗に寄せられたレビューを取得
	 */
	public function getReviewsByOfficeId($officeId) {
		$options = array(
			'fields'=>array(
				'YotpoReview.title',
				'YotpoReview.content',
				'YotpoReview.score',
				"DATE_FORMAT(YotpoReview.created_at, '%Y-%m-%d') as created_at",
				'YotpoReview.reviewer_type',
				'Client.id',
				'Client.name',
				'Client.url',
				'Office.name',
				'Office.url',
			),
			'conditions' => array(
				'Office.id' => $officeId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('all', $options);
	}

	/**
	 * 店舗に寄せられたレビュー数を取得
	 */
	public function getReviewsCountByOfficeId($officeId) {
		$options = array(
			'conditions' => array(
				'Office.id' => $officeId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('count', $options);
	}

	/**
	 * 店舗に寄せられたレビュー数を取得
	 */
	public function getReviewsAvgByOfficeId($officeId) {
		$options = array(
			'fields'=>array(
				'AVG(YotpoReview.score) AS rating',
			),
			'conditions' => array(
				'Office.id' => $officeId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			)
		);
		$result = $this->findC('first', $options);
		if(!empty($result)){
			return $result[0]['rating'];
		}
		return 0;
	}


	/**
	 * 空港に寄せられたレビューを取得
	 */
	public function getReviewsByAirportId($airportId) {
		$options = array(
			'fields'=>array(
				'YotpoReview.title',
				'YotpoReview.content',
				'YotpoReview.score',
				"DATE_FORMAT(YotpoReview.created_at, '%Y-%m-%d') as created_at",
				'YotpoReview.reviewer_type',
				'Client.id',
				'Client.name',
				'Client.url',
				'Office.name',
				'Office.url',
			),
			'conditions' => array(
				'Office.airport_id' => $airportId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('all', $options);
	}

	/**
	 * 空港に寄せられたレビュー数を取得
	 */
	public function getReviewsCountByAirportId($airportId) {
		$options = array(
			'conditions' => array(
				'Office.airport_id' => $airportId,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('count', $options);
	}

	/**
	 * 駅の店舗に寄せられたレビューを取得
	 */
	public function getReviewsByStationId($stationId) {
		$options = array(
			'fields'=>array(
				'YotpoReview.title',
				'YotpoReview.content',
				'YotpoReview.score',
				"DATE_FORMAT(YotpoReview.created_at, '%Y-%m-%d') as created_at",
				'YotpoReview.reviewer_type',
				'Client.id',
				'Client.name',
				'Client.url',
				'Office.name',
				'Office.url',
			),
			'conditions' => array(
				'OfficeStation.station_id' => $stationId,
				'OfficeStation.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'OfficeStation',
					'table'=>'office_stations',
					'conditions'=>'Office.id = OfficeStation.office_id'
				)
			),
			'order' => 'YotpoReview.created_at DESC',
		);

		return $this->findC('all', $options);
	}

	/**
	 * 駅の店舗に寄せられたレビュー数を取得
	 */
	public function getReviewCountByStationId($stationId) {
		$options = array(
			'conditions' => array(
				'OfficeStation.station_id' => $stationId,
				'OfficeStation.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'Office.delete_flg' => 0,
				'YotpoReview.unpublished' => 0,
				'YotpoReview.reviewer_type <>' => 'anonymous_user'
			),
			'joins' => array(
				array(
					'type'=>'INNER',
					'alias'=>'Client',
					'table'=>'clients',
					'conditions'=>'Client.id = YotpoReview.client_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'Office',
					'table'=>'offices',
					'conditions'=>'Office.id = YotpoReview.office_id'
				),
				array(
					'type'=>'INNER',
					'alias'=>'OfficeStation',
					'table'=>'office_stations',
					'conditions'=>'Office.id = OfficeStation.office_id'
				)
			),
		);

		$result = $this->findC('count', $options);

		return $result;
	}

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()) {
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();
		if (!empty($this->data['YotpoReview']['name_enc'])){
			$this->data['YotpoReview']['name_enc'] = $encrypt->encrypt($this->data['YotpoReview']['name_enc']);
		}
		if (!empty($this->data['YotpoReview']['email_enc'])){
			$this->data['YotpoReview']['email_enc'] = $encrypt->encrypt($this->data['YotpoReview']['email_enc']);
		}

		return true;
	}

	/**
	 * 検索前処理
	 */
	public function beforeFind($queryData) {
		// 対象検索条件を暗号化
		$encrypt = new Encrypt();
		if (!empty($queryData['conditions']['YotpoReview.name_enc'])) {
			$queryData['conditions']['YotpoReview.name_enc'] = $encrypt->encrypt($queryData['conditions']['YotpoReview.name_enc']);
		}
		if (!empty($queryData['conditions']['YotpoReview.email_enc'])) {
			$queryData['conditions']['YotpoReview.email_enc'] = $encrypt->encrypt($queryData['conditions']['YotpoReview.email_enc']);
		}

		if (!empty($queryData['conditions']['YotpoReview.name_enc like'])) {
			$val = trim($queryData['conditions']['YotpoReview.name_enc like'], '%');
			$queryData['conditions']['YotpoReview.name_enc like'] = '%' . $encrypt->encrypt($val) . '%';
		}
		if (!empty($queryData['conditions']['YotpoReview.email_enc like'])) {
			$val = trim($queryData['conditions']['YotpoReview.email_enc like'], '%');
			$queryData['conditions']['YotpoReview.email_enc like'] = '%' . $encrypt->encrypt($val) . '%';
		}

		return $queryData;
	}

	/**
	 * 検索後処理
	 */
	public function afterFind($results, $primary = false) {
		// 対象フィールドを復号
		$encrypt = new Encrypt();
		foreach ($results as $key => $val) {
			if (isset($val['YotpoReview']['name_enc'])) {
				$results[$key]['YotpoReview']['name_enc'] = $encrypt->decrypt($val['YotpoReview']['name_enc']);
			}
			if (isset($val['YotpoReview']['email_enc'])) {
				$results[$key]['YotpoReview']['email_enc'] = $encrypt->decrypt($val['YotpoReview']['email_enc']);
			}
		}
		return $results;
	}

}

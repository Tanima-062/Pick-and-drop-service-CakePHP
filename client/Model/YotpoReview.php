<?php
App::uses('AppModel','Model');
require_once("encrypt_class.php");

/**
 * YotpoReview Model
 */
class YotpoReview extends AppModel {

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

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()){
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

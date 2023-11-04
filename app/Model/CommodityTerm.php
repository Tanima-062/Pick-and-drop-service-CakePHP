<?php
App::uses('AppModel', 'Model');
/**
 * CommodityTerm Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property Staff $Staff
 */
class CommodityTerm extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'commodity_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'available_from' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'available_to' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Commodity' => array(
			'className' => 'Commodity',
			'foreignKey' => 'commodity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * @param unknown $commodityId
	 */
	public function getDeadline($commodityId) {
		$options = array(
			'fields' => array(
				'CommodityTerm.deadline_hours',
				'CommodityTerm.deadline_days',
				'CommodityTerm.deadline_time',
			),
			'conditions' => array(
					'CommodityTerm.commodity_id' => $commodityId,
					'CommodityTerm.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		return $this->find('first', $options);
	}

	/**
	 * 受付締切時間
	 */
	public function acceptanceDeadlineTime($commodityId, $rentDate) {

		// 商品対象期間取得
		$term = $this->getDeadline($commodityId);
		$term = $term['CommodityTerm'];
		$rentDatetime = strtotime($rentDate);
		$errorMessage = false;

		if (isset($term['deadline_days']) && !empty($term['deadline_time'])) {
			// 時刻指定の場合
			$rentDate = date('Y-m-d ' . $term['deadline_time'], $rentDatetime);
			$deadline = strtotime($rentDate . ' -' . $term['deadline_days'] . ' day');

			if (time() > $deadline) {
				$errorMessage = "※本プランのお申込みは" . date('Y/m/d H:i', $deadline) . "までとなっております。";
			}
		} else if (isset($term['deadline_hours'])) {
			// 時間指定の場合
			$deadline = strtotime($rentDate . ' -' . $term['deadline_hours'] . ' hour');

			if (time() > $deadline) {
				$errorMessage = "※本プランのお申込みは出発日時の" . $term['deadline_hours'] . "時間前までとなっております。";
			}
		}

		return $errorMessage;
	}
}

<?php
App::uses('AppModel', 'Model');
App::import('Model', 'Landmark');
/**
 * TourSalesStock Model
 */
class TourSalesStock extends AppModel {
	public $useDbConfig = 'common';
	public $useTable = 'cm_tm_rc_tour_sales_stock';

	public $virtualFields = array(
		'key' => 'CONCAT(TourSalesStock.iata_cd, "_", TourSalesStock.stock_date, "_", TourSalesStock.tour_car_type_id)'
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'iata_cd' => array(
			'existsIATA' => array(
				'rule' => array('existsIATA'),
				'required' => true,
				'message' => '利用空港が不正です'
			)
		),
		'stock_date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'required' => true,
				'message' => '在庫日が不正です'
			)
		),
		'tour_car_type_id' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber'),
				'required' => true,
				'message' => '車両クラスIDは整数で入力してください'
			)
		),
		'stock_count' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'required' => true,
				'message' => '在庫数は整数で入力してください'
			)
		),
		'sold_count' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'required' => true,
				'message' => '販売数は整数で入力してください'
			)
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean')
			)
		)
	);

	// 利用空港バリデーション
	public function existsIATA($data) {
		$iataCdList = Hash::extract((new Landmark())->getAirportList(), '{n}.Landmark.iata_cd');
		return in_array($this->data[$this->name]['iata_cd'], array_merge($iataCdList, array('TYO', 'OSA')));
	}

	// 重複チェック
	public function duplicate($data) {
		if ($this->data[$this->name]['delete_flg']) {
			return true;
		}
		$conditions = array(
			'iata_cd' => $this->data[$this->name]['iata_cd'],
			'tour_car_type_id' => $this->data[$this->name]['tour_car_type_id'],
			'stock_date <=' => $this->data[$this->name]['stock_date'],
			'delete_flg' => 0
		);
		if (isset($this->data[$this->name]['id'])) {
			$conditions['id <>'] = $this->data[$this->name]['id'];
		}
		$ret = $this->find('first', array('conditions' => $conditions));
		return empty($ret);
	}
}

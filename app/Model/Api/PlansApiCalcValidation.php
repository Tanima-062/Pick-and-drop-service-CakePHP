<?php
App::uses('AppModel', 'Model');

class PlansApiCalcValidation extends AppModel {

	/**
	 * Use table
	 *
	 * @var mixed False or table name
	 */
	public $useTable = false;

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'fromShopId' => array(
			array(
				'rule' => 'notblank',
				'message' => '出発店舗IDは必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'naturalNumber',
				'message' => '出発店舗IDが正しくありません',
				'last' => false,
			),
		),
		'toShopId' => array(
			array(
				'rule' => 'notblank',
				'message' => '返却店舗IDは必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('naturalNumber', true),
				'message' => '返却店舗IDが正しくありません',
				'last' => false,
			),
		),
		'startDate' => array(
			array(
				'rule' => 'notblank',
				'message' => '出発日は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'date',
				'message' => '出発日が正しくありません',
				'last' => false,
			),
		),
		'startTime' => array(
			array(
				'rule' => 'notblank',
				'message' => '出発時間は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('custom', '/^[0-2][0-9]:[0-5][0-9]$/'),
				'message' => '出発時間が正しくありません',
				'last' => false,
			),
		),
		'endDate' => array(
			array(
				'rule' => 'notblank',
				'message' => '返却日は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'date',
				'message' => '返却日が正しくありません',
				'last' => false,
			),
		),
		'endTime' => array(
			array(
				'rule' => 'notblank',
				'message' => '返却時間は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('custom', '/^[0-2][0-9]:[0-5][0-9]$/'),
				'message' => '返却時間が正しくありません',
				'last' => false,
			),
		),
		'currency' => array(
			'rule' => array('custom', '/^[A-Za-z]+$/'),
			'message' => '通貨が正しくありません',
			'last' => false,
		),
	);

	// 予約時のルールが足りないので追加させる
	public function addReservationRule() {
		$this->validator()->add('planId', array(
			array(
				'rule' => 'notblank',
				'message' => 'プランIDは必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'naturalNumber',
				'message' => 'プランIDが正しくありません',
				'last' => false,
			),
		))->add('basePrice', array(
			array(
				'rule' => 'notblank',
				'message' => '基本料金は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'naturalNumber',
				'message' => '基本料金が正しくありません',
				'last' => false,
			),
		))->add('totalPrice', array(
			array(
				'rule' => 'notblank',
				'message' => '合計料金は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'naturalNumber',
				'message' => '合計料金が正しくありません',
				'last' => false,
			),
		));
	}
}

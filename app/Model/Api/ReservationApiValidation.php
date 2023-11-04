<?php
App::uses('AppModel', 'Model');

/**
 * Class ReservationApiValidation
 */
class ReservationApiValidation extends AppModel {

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
		'userId' => array(
			'rule'    => 'naturalNumber',
			'message' => 'ユーザーIDが正しくありません',
		),
		'cmApplicationId' => array(
			'rule'    => 'naturalNumber',
			'message' => '申込番号が正しくありません',
		),
		'currency' => array(
			'rule'    => array('custom', '/^[A-Za-z]+$/'),
			'message' => '通貨が正しくありません',
		),
		'ipAddress' => array(
			'rule'    => 'ip',
			'message' => 'IPアドレスが正しくありません',
		),
		'advertisingCd' => array(
			'rule'    => array('custom', '/^[0-9A-Za-z_]+$/'),
			'message' => '広告コードが正しくありません',
		),
	);

	/**
	 * User用バリデーションを設定する
	 */
	public function setUserValidate() {

		$this->validate = array(
			'lastName' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '性は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('custom', '/^[ァ-ヶー]+$/u'),
					'message' => '性が正しくありません',
				),
			),
			'firstName' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '名は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('custom', '/^[ァ-ヶー]+$/u'),
					'message' => '名が正しくありません',
				),
			),
			'email' => array(
				array(
					'rule'     => 'notblank',
					'message'  => 'メールアドレスは必須です',
					'required' => true,
				),
				array(
					'rule'    => 'email',
					'message' => 'メールアドレスが正しくありません',
				),
			),
			'tel' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '電話番号は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('custom', '/^[0-9]+$/'),
					'message' => '電話番号が正しくありません',
				),
			),
			'adultCount' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '大人人数は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('naturalNumber', true),
					'message' => '大人人数が正しくありません',
				),
			),
			'childCount' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '子供人数は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('naturalNumber', true),
					'message' => '子供人数が正しくありません',
				),
			),
			'infantCount' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '幼児人数は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('naturalNumber', true),
					'message' => '幼児人数が正しくありません',
				),
			),
		);
	}

	/**
	 * Plan用バリデーションを設定する
	 */
	public function setPlanValidate() {

		$this->validate = array(
			'planId' => array(
				array(
					'rule'     => 'notblank',
					'message'  => 'プランIDは必須です',
					'required' => true,
				),
				array(
					'rule'    => 'naturalNumber',
					'message' => 'プランIDが正しくありません',
				),
			),
			'fromShopId' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '出発店舗IDは必須です',
					'required' => true,
				),
				array(
					'rule'    => 'naturalNumber',
					'message' => '出発店舗IDが正しくありません',
				),
			),
			'toShopId' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '返却店舗IDは必須です',
					'required' => true,
				),
				array(
					'rule'    => array('naturalNumber', true),
					'message' => '返却店舗IDが正しくありません',
				),
			),
			'startDate' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '出発日は必須です',
					'required' => true,
				),
				array(
					'rule'    => 'date',
					'message' => '出発日が正しくありません',
				),
			),
			'startTime' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '出発時間は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('custom', '/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'),
					'message' => '出発時間が正しくありません',
				),
			),
			'endDate' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '返却日は必須です',
					'required' => true,
				),
				array(
					'rule'    => 'date',
					'message' => '返却日が正しくありません',
				),
			),
			'endTime' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '返却時間は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('custom', '/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'),
					'message' => '返却時間が正しくありません',
				),
			),
			'basePrice' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '基本料金は必須です',
					'required' => true,
				),
				array(
					'rule'    => 'naturalNumber',
					'message' => '基本料金が正しくありません',
				),
			),
			'salesPrice' => array(
				array(
					'rule'     => 'notblank',
					'message'  => '販売料金は必須です',
					'required' => true,
				),
				array(
					'rule'    => 'naturalNumber',
					'message' => '販売料金が正しくありません',
				),
			),
		);
	}

}

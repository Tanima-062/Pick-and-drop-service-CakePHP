<?php
App::uses('AppModel', 'Model');
/**
 * Search Model
 *
 */
class SearchesApiValidation extends AppModel {

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
		// bookingInfoの値
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
		'adultCount' => array(
			array(
				'rule' => 'notblank',
				'message' => '大人人数は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'naturalNumber',
				'message' => '大人人数が正しくありません',
				'last' => false,
			),
		),
		'childCount' => array(
			array(
				'rule' => 'notblank',
				'message' => '子供人数は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('naturalNumber', true),
				'message' => '子供人数が正しくありません',
				'last' => false,
			),
		),
		'infantCount' => array(
			array(
				'rule' => 'notblank',
				'message' => '幼児人数は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => array('naturalNumber', true),
				'message' => '幼児人数が正しくありません',
				'last' => false,
			),
		),
		// locationの値
		'latitude' => array(
			array(
				'rule' => 'notblank',
				'message' => '緯度は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'decimal',
				'message' => '緯度が正しくありません',
				'last' => false,
			),
		),
		'longitude' => array(
			array(
				'rule' => 'notblank',
				'message' => '経度は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'decimal',
				'message' => '経度が正しくありません',
				'last' => false,
			),
		),
		'IATACode' => array(
			'rule' => array('custom', '/^[A-Za-z]{3}$/'),
			'message' => '空港コードが正しくありません',
			'last' => false,
		),
		'airportId' => array(
			'rule' => array('naturalNumber', true),
			'message' => '空港・港が正しくありません',
			'last' => false,
		),
		'stationId' => array(
			'rule' => array('naturalNumber', true),
			'message' => '駅が正しくありません',
			'last' => false,
		),
		'areaId' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'エリアが正しくありません',
			'last' => false,
		),
		'returnLatitude' => array(
			array(
				'rule' => 'notblank',
				'message' => '緯度は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'decimal',
				'message' => '緯度が正しくありません',
				'last' => false,
			),
		),
		'returnLongitude' => array(
			array(
				'rule' => 'notblank',
				'message' => '経度は必須です',
				'required' => true,
				'last' => false,
			),
			array(
				'rule' => 'decimal',
				'message' => '経度が正しくありません',
				'last' => false,
			),
		),
		'returnIATACode' => array(
			'rule' => array('custom', '/^[A-Za-z]{3}$/'),
			'message' => '空港コードが正しくありません',
			'last' => false,
		),
		'returnAirportId' => array(
			'rule' => array('naturalNumber', true),
			'message' => '空港・港が正しくありません',
			'last' => false,
		),
		'returnStationId' => array(
			'rule' => array('naturalNumber', true),
			'message' => '駅が正しくありません',
			'last' => false,
		),
		'returnAreaId' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'エリアが正しくありません',
			'last' => false,
		),
		// SearchConditionの値
		'carTypes' => array(
			'rule' => array('intArray'),
			'message' => "車両タイプが正しくありません",
			'last' => false,
		),
		'options' => array(
			'rule' => array('intArray'),
			'message' => "オプションが正しくありません",
			'last' => false,
		),
		'clients' => array(
			'rule' => array('intArray'),
			'message' => "会社指定が正しくありません",
			'last' => false,
		),
		'smokingType' => array(
			'rule' => array('inList', array(0, 1, 2)),
			'message' => '喫煙タイプが正しくありません',
			'last' => false,
		),
		'currency' => array(
			'rule' => array('custom', '/^[A-Za-z]+$/'),
			'message' => '通貨が正しくありません',
			'last' => false,
		),
	);

	/**
	 * 整数の配列になっているか見る
	 *
	 * @param array $check
	 * @return boolean
	 */
	public function intArray($check) {
		$values = $check[key($check)];
		foreach ($values as $v) {
			if (!is_int($v)) {
				return false;
			}
		}
		return true;
	}
}

<?php
App::uses('AppModel', 'Model');

/**
 * Class PlanApiValidation
 */
class PlanApiValidation extends AppModel {

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
		'startDateTime' => array(
			array(
				'rule'     => 'notblank',
				'message'  => '出発日時は必須です',
				'required' => true,
			),
			array(
				'rule'    => 'datetime',
				'message' => '出発日時が正しくありません',
			),
		),
		'endDateTime' => array(
			array(
				'rule'     => 'notblank',
				'message'  => '返却日時は必須です',
				'required' => true,
			),
			array(
				'rule'    => 'datetime',
				'message' => '返却日時が正しくありません',
			),
		),
		'smokingType' => array(
			array(
				'rule'     => 'notblank',
				'message'  => 'タバコは必須です',
				'required' => true,
			),
			array(
				'rule'    => array('inList', array(0, 1, 2)),
				'message' => 'タバコが正しくありません',
			),
		),
		'returnWay' => array(
			array(
				'rule'     => 'notblank',
				'message'  => '出発店舗へ返却は必須です',
				'required' => true,
			),
			array(
				'rule'    => 'boolean',
				'message' => '出発店舗へ返却が正しくありません',
			),
		),
		'place' => array(
			array(
				'rule'     => 'notblank',
				'message'  => '出発場所は必須です',
				'required' => true,
			),
			array(
				'rule'    => array('inList', array(1, 3, 4)),
				'message' => '出発場所が正しくありません',
			),
		),
	);

	/**
	 * 条件付きのバリデーションを設定する
	 */
	public function setComplexValidate() {

		// パラメータ
		$params = $this->data[self::class];

		// 出発場所が存在する場合のみ
		if (isset($params['place'])) {
			// 出発場所によりバリデーションを追加
			switch ($params['place']) {
				// エリア
				case 1:
					$field = 'areaId';
					$name  = 'エリアID';
					break;

				// 空港
				case 3:
					$field = 'airportId';
					$name  = '空港ID';
					break;

				// 駅
				case 4:
					$field = 'stationId';
					$name  = '駅ID';
					break;
			}

			// パラメータが存在する場合のみ
			if (isset($field) && isset($name)) {
				$this->validator()->add($field, array(
					array(
						'rule'     => 'notblank',
						'message'  => "出発場所 - {$name}は必須です",
						'required' => true,
					),
					array(
						'rule'    => 'naturalNumber',
						'message' => "出発場所 - {$name}が正しくありません",
					),
				));
				unset($field);
				unset($name);
			}
		}

		// 出発店舗へ返却が「1: 別店舗に返却」の場合
		if (isset($params['returnWay']) && intval($params['returnWay']) === 1) {
			// 返却場所バリデーション追加
			$this->validator()->add('returnPlace', array(
				array(
					'rule'     => 'notblank',
					'message'  => '返却場所は必須です',
					'required' => true,
				),
				array(
					'rule'    => array('inList', array(1, 3, 4)),
					'message' => '返却場所が正しくありません',
				),
			));

			// 返却場所が存在する場合のみ
			if (isset($params['returnPlace'])) {
				// 返却場所によりバリデーションを追加
				switch ($params['returnPlace']) {
					// エリア
					case 1:
						$field = 'returnAreaId';
						$name  = 'エリアID';
						break;

					// 空港
					case 3:
						$field = 'returnAirportId';
						$name  = '空港ID';
						break;

					// 駅
					case 4:
						$field = 'returnStationId';
						$name  = '駅ID';
						break;
				}

				// パラメータが存在する場合のみ
				if (isset($field) && isset($name)) {
					$this->validator()->add($field, array(
						array(
							'rule'     => 'notblank',
							'message'  => "返却場所 - {$name}は必須です",
							'required' => true,
						),
						array(
							'rule'    => 'naturalNumber',
							'message' => "返却場所 - {$name}が正しくありません",
						),
					));
				}
			}
		}
	}

}

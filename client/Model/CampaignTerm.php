<?php
App::uses('AppModel', 'Model');
/**
 * Campaign Model
 *
 * @property Client $Client
 * @property Campaign $Campaign
 * @property Staff $Staff
 */
class CampaignTerm extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
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
		'start_date' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => '開始日は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'end_date' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => '終了日は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'datecompare' => array(
				'rule' => array('datecompare'),
				'message' => '終了日が開始日より過去です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'end_date' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => '終了日は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'datecompare' => array(
				'rule' => array('datecompare'),
				'message' => '終了日が開始日より過去です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'mon' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
			),
		),
		'tue' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
			),
		),
		'wed' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
			),
		),
		'thu' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
			),
		),
		'fri' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
			),
		),
		'sat' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
			),
		),
		'sun' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
			),
		),
		'hol' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => '不正な値です。',
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
			'numeric' => array(
				'rule' => array('numeric'),
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
		'Campaign' => array(
			'className' => 'Campaign',
			'foreignKey' => 'campaign_id',
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

	
	// 日付比較
	public function dateCompare($data) {
		return (strtotime($data['end_date']) >= strtotime($this->data[$this->name]['start_date']));
	}
	
	public function beforeSave($options = array()){
		parent::beforeSave($options);

		$data = $this->data[$this->name];
		$link_cd = $models[$this->name];

		// 新規登録または主キー単体での更新の場合のみ
		if (isset($data['id']) && is_array($data['id'])) {
			return true;
		}

		// リンクコードが空文字の時、または削除時はnullに更新する
		if ((isset($data[$link_cd]) && $data[$link_cd] == '') || !empty($data['delete_flg'])) {
			$this->data[$this->name][$link_cd] = null;
		}

		return true;
	}

	// 日付重複チェック
	public function dateDuplicate($data) {
		foreach ($data as $k1 => $v1) {
			foreach ($data as $k2 => $v2) {
				if ($k1 != $k2) {
					// 曜日が異なれば重複チェックの対象外になる
					$diff = array_values(array_intersect($v1['week'], $v2['week']));
					if (empty($diff)) {
						continue;
					}
					// 始点2 <= 終点1 && 始点1 <= 終点2
					if ($v2['start_date'] <= $v1['end_date'] && $v1['start_date'] <= $v2['end_date']) {
						return true;
					}
				} else {
					unset($data[$k2]);
				}
			}
		}
		return false;
	}

	// 曜日存在チェック
	public function containsDay($data) {
		foreach ($data as $v) {
			$start = new DateTime($v['start_date']);
			$end = new Datetime($v['end_date']);
			$interval = $start->diff($end)->format('%a');
			if ($interval >= 6) { // 6のとき1週間
				// 1週間あれば曜日を網羅する
				continue;
			} else {
				$days = [];
				for ($i = 0; $i <= $interval; $i++) {
					$time = strtotime($v['start_date'] . ' + ' . $i . ' day');
					$weekDay = date('w', $time);
					// Constant::weekEnが月曜始まりのため調整
					if ($weekDay == 0) {
						$weekDay = 6;
					} else {
						$weekDay -= 1;
					}
					$days[$weekDay] = $weekDay;
				}
				foreach ($v['week'] as $w) {
					if ($w == 7) { // 祝日は見ない
						continue;
					}
					if (!isset($days[$w])) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public function getTermsByCampaignIds($campaignIds) {
		$options = array(
			'fields' => array(
				'CampaignTerm.id',
				'CampaignTerm.campaign_id',
				"DATE_FORMAT(CampaignTerm.start_date, '%Y/%m/%d') AS start_date",
				"DATE_FORMAT(CampaignTerm.end_date, '%Y/%m/%d') AS end_date",
				'ABS(CampaignTerm.mon) AS mon',
				'ABS(CampaignTerm.tue) AS tue',
				'ABS(CampaignTerm.wed) AS wed',
				'ABS(CampaignTerm.thu) AS thu',
				'ABS(CampaignTerm.fri) AS fri',
				'ABS(CampaignTerm.sat) AS sat',
				'ABS(CampaignTerm.sun) AS sun',
				'ABS(CampaignTerm.hol) AS hol',
			),
			'conditions' => array(
				'CampaignTerm.campaign_id' => $campaignIds,
				'CampaignTerm.delete_flg' => 0,
			),
			'order' => array(
				'CampaignTerm.id',
			),
			'recursive' => -1,
		);

		$data = $this->find('all', $options);
		if (!empty($data)) {
			$data = Hash::combine($data, '{n}.CampaignTerm.id', '{n}.0', '{n}.CampaignTerm.campaign_id');
		}

		return $data;
	}

    /**
     * 商品IDから、同一クライアントIDと同一期間で同じ商品IDを設定しているキャンペーンIDを取得する
     *
     * @param string $terms キャンペーン期間、曜日データ
     * @param string $campaignId
     * @param string $clientId
     * @param array $commodityItemIds 選択されたキャンペーンに紐付いている全ての商品アイテムID全て
     *
     * @return array distinctしたキャンペーンID
     */
    public function getCommodityItemIdWithOtherCampaignId($terms, $campaignId, $clientId, $commodityItemIds)
    {
        $options = [];
        $options['joins'] = [
            [
                'table'      => 'commodity_campaign_prices',
                'alias'      => 'CommodityCampaignPrice',
                'type'       => 'INNER',
                'conditions' => [
                    'CommodityCampaignPrice.campaign_id = CampaignTerm.campaign_id'
                ]
            ],
        ];
        $options['fields'] = [
            'DISTINCT CampaignTerm.campaign_id',
        ];
        // 同一の顧客IDのキャンペーンが対象
        $options['conditions'] = [
            'CampaignTerm.client_id'     => $clientId,
            'CampaignTerm.delete_flg'    => 0,
            'CommodityCampaignPrice.delete_flg' => 0,
            'CommodityCampaignPrice.commodity_item_id' => $commodityItemIds
        ];

        // チェック対象のキャンペーン期間開始日、及び終了日が重なるキャンペーンを対象
        // start_date <= {比較対象の終了日} and end_date >= {比較対象の開始日}
        foreach ($terms as $tk => $tv) {
            $stringDayOfWeek = array();
            $conditions = array();

            // 各曜日条件の判定
            if ($tv['mon']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.mon =' => true
                ];
            }
            if ($tv['tue']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.tue =' => true
                ];
            }
            if ($tv['wed']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.wed =' => true
                ];
            }
            if ($tv['thu']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.thu =' => true
                ];
            }
            if ($tv['fri']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.fri =' => true
                ];
            }
            if ($tv['sat']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.sat =' => true
                ];
            }
            if ($tv['sun']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.sun =' => true
                ];
            }
            if ($tv['hol']) {
                $stringDayOfWeek[] = [
                    'CampaignTerm.hol =' => true
                ];
            }
            // 日付条件の格納
            $conditions[] = [
                'CampaignTerm.start_date <=' => $tv['end_date'],
                'CampaignTerm.end_date >=' => $tv['start_date']
            ];
            // 曜日条件を格納
            if ($stringDayOfWeek) {
                $conditions['OR'] = $stringDayOfWeek;
            }
            // $conditionsを追加
            $options['conditions']['OR'][] = $conditions;

        }

        // チェック対象のキャンペーンIDは除外
        $options['conditions']['NOT'] = [
            'CampaignTerm.campaign_id' => $campaignId,
        ];

        $options['recursive'] = -1;
        return $this->find('all', $options);
    }

}

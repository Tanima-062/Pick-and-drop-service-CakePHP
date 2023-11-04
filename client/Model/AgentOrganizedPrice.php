<?php

App::uses('AppModel', 'Model');
App::import('Vendor', 'imageResizeUpLoad');

/**
 * AgentOrganizedPrice Model
 *
 */
class AgentOrganizedPrice extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'start_date' => [
            'dateFormat' => [
                'rule' => ['date', 'ymd'],
                'required' => true,
                'message' => '有効な日付を YY-MM-DD フォーマットで入力してください。'
            ]
        ],
        'end_date' => [
            'dateFormat' => [
                'rule' => ['date', 'ymd'],
                'required' => true,
                'message' => '有効な日付を YY-MM-DD フォーマットで入力してください。'
            ],
            'term' => [
                'rule' => 'checkSeasonalityTerm'
            ]
        ],
        'price_stay_1' => [
            'numeric' => [
                'rule' => 'numeric',
                'required' => true,
                'message' => '料金を数値で入力してください。'
            ]
        ],
        'price_stay_2' => [
            'numeric' => [
                'rule' => 'numeric',
                'required' => true,
                'message' => '料金を数値で入力してください。'
            ]
        ],
        'price_stay_3' => [
            'numeric' => [
                'rule' => 'numeric',
                'required' => true,
                'message' => '料金を数値で入力してください。'
            ]
        ],
        'price_stay_over' => [
            'numeric' => [
                'rule' => 'numeric',
                'required' => true,
                'message' => '料金を数値で入力してください。'
            ]
        ]
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [];

    /**
     * hasMany associations
     *
     * @var array
     *
     */
    public $hasMany = [];

    /**
     * 料金情報を保存する
     *
     * @param array $requests
     * @return bool
     */
    public function savePriceData($requests)
    {
        // 各カラム単体のバリデーション
        $result = $this->saveAll($requests, ['validate' => 'only']);
        if (!$result) {
            return false;
        }

        // 期間重複チェック
        $checkArr = $requests;
        foreach ($requests as $key => $value) {
            $startDate = new DateTime($value['start_date']);
            $endDate = new DateTime($value['end_date']);
            array_shift($checkArr);
            foreach ($checkArr as $checkVal) {
                $checkStartDate = new DateTime($checkVal['start_date']);
                $checkEndDate = new DateTime($checkVal['end_date']);
                if (($startDate <= $checkEndDate) && ($endDate >= $checkStartDate)) {
                    $this->validationErrors[$key]['start_date'] = '期間が重複している料金設定があります。';
                    break;
                }
            }
        }
        if ($this->validationErrors !== []) {
            return false;
        }

        $result = $this->saveAll($requests, ['validate' => false]);
        return $result;
    }

    /**
     * シーズナリティの開始日、終了日の妥当性をチェックする
     *
     * @return string|bool
     */
    public function checkSeasonalityTerm()
    {
        $startDate = new DateTime($this->data['AgentOrganizedPrice']['start_date']);
        $endDate = new DateTime($this->data['AgentOrganizedPrice']['end_date']);

        if ($startDate > $endDate) {
            return '終了日は開始日よりも後の日付を入力してください。';
        }
        return true;
    }

    /**
     * 商品アイテムIDに紐づく料金情報を取得する
     *
     * @param int $commodityItemId
     * @return array
     */
    public function getPricesByCommodityItemId($commodityItemId)
    {
        $results = $this->find('all', [
            'fields' => [
                'id',
                'start_date',
                'end_date',
                'price_stay_1',
                'price_stay_2',
                'price_stay_3',
                'price_stay_over'
            ],
            'conditions' => [
                'commodity_item_id' => $commodityItemId,
                'delete_flg' => 0
            ]
        ]);

        return array_map(
            function ($val) {
                return $val['AgentOrganizedPrice'];
            },
            $results
        );
    }

    /**
     * リクエストされたIDが存在するか調べる
     * 
     * @param int $agentOrganizedPriceId
     * @return bool
     */
    public function existsPriceData($agentOrganizedPriceId)
    {
        $result = $this->find('first', ['conditions' => [
            'id' => $agentOrganizedPriceId,
            'delete_flg' => 0
        ]]);
        return ($result === []) ? false : true;
    }

    /**
     * 指定した料金情報を削除する
     * 
     * @param int $agentOrganizedPriceId
     * @param int $staffId
     * @return bool|array
     */
    public function deletePrice($agentOrganizedPriceId, $staffId)
    {
        $this->id = $agentOrganizedPriceId;
        $now = date_create()->format('Y-m-d H:i:s');
        $data = [
            'staff_id' => $staffId,
            'modified' => $now,
            'delete_flg' => 1,
            'deleted' => $now
        ];
        $result = $this->save($data, true, array_keys($data));
        return $result;
    }
}

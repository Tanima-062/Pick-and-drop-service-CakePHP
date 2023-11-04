<?php
App::uses('AppModel', 'Model');
/**
 * PaymentInfo Model
 */
class PaymentToken extends AppModel
{
    /**
    * Validation rules
    *
    * @var array
    */
    public $validate = [
        'cm_application_id' => [
            'rule1' => [
                'rule' => ['numeric'],
                'message' => 'cm_application_idに数値以外の文字が含まれています。',
            ],
            'rule2' => [
                'rule' => ['notBlank'],
                'message' => 'cm_application_idの値が空です。',
            ]
        ],
        'token' => [
            'rule1' => [
                'rule' => ['maxLength',256],
                'message' => 'tokenの長さが257文字以上です。',
            ],
            'rule2' => [
                'rule' => ['notBlank'],
                'message' => 'tokenの値が空です。',
            ]
        ],
    ];

    public function getTokenByCmApplicationIdAndToken($cmApplicationId, $token)
    {
        $result_token = null;
        $conditions = [
                        'fields' => 'token',
                        'conditions' => ['cm_application_id' => $cmApplicationId, 'token' => $token],
                ];
        $results = $this->find('first', $conditions);
        if (!empty($results)) {
            $result_token = $results['PaymentToken']['token'];
        }
        return $result_token;
    }

    public function getIdByCmApplicationIdAndToken($cmApplicationId, $token)
    {
        $id = null;
        $conditions = [
                        'fields' => 'id',
                        'conditions' => ['cm_application_id' => $cmApplicationId, 'token' => $token],
                ];
        $results = $this->find('first', $conditions);
        if (!empty($results)) {
            $id = $results['PaymentToken']['id'];
        }
        return $id;
    }

    public function getCallBackValuesByCmApplicationId($cmApplicationId, $token)
    {
        $callBackValues = null;
        $conditions = [
                'fields' => 'call_back_values',
                'conditions' => ['cm_application_id' => $cmApplicationId, 'token' => $token],
            ];
        $results = $this->find('first', $conditions);
        if (!empty($results['PaymentToken']['call_back_values'])) {
            $callBackValues = json_decode($results['PaymentToken']['call_back_values'], true);
        }
        return $callBackValues;
    }

    public function saveInsertUpdate($cmApplicationId, $token)
    {
        $param =[
            'cm_application_id' => $cmApplicationId,
            'token' => $token,
        ];

        $id = $this->getIdByCmApplicationIdAndToken($cmApplicationId, $token);
        if (!empty($id)) {
            $param = ['id'=> $id] + $param;
        }

        $results = $this->save($param);
        return $results;
    }

    public function updateCallBackValues($id, $json)
    {
        $param =[
            'id' => $id,
            'call_back_values' => $json,
        ];
        $results = $this->save($param);
        return $results;
    }
}

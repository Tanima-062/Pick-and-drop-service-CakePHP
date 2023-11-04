<?php
App::uses('AppModel', 'Model');
/**
 * BudgetReservationApiFailure Model
 *
 */
class BudgetReservationApiFailure extends AppModel
{
    public $useTable = 'budget_reservation_api_failure';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'reservation_key' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'created' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    );

    /**
     * 10分以内に同一のエラー内容が複数あるかチェック
     *
     * @return bool
     */
    public function checkDuplicateErrorMessage($reservationKey)
    {
        $result = true;

        // 仮想フィールドとして追加
        $this->virtualFields = array(
            'count' => 'count(*)',
        );

        $errorMessages = $this->find(
            'list',
            array(
                'fields' => array(
                    'error_message',
                    'count'
                ),
                'conditions' => array(
                    'created >=' => date("Y-m-d H:i:s", strtotime("-10 minute")),
                    'reservation_key' => $reservationKey
                ),
                'group' => array(
                    'BudgetReservationApiFailure.error_message',
                ),
                'recursive' => -1
            )
        );

        foreach ($errorMessages as $errorMessage) {
            if ($errorMessage >= 2) {
                $result = false;
                break;
            }
        }

        return $result;
    }
}

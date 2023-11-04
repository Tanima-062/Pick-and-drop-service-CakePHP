<?php

App::uses('AppModel', 'Model');

class ReservationStatus extends AppModel
{
    /**
     * 予約ステータスで取得
     * [ステータスID => ステータス名] の配列
     *
     * @return array
     */
    public function getReservationStatuses()
    {
        $params = [
            'conditions' => [
                'ReservationStatus.delete_flg' => 0
            ]
        ];
        $items = $this->find('all', $params);
        $result = [];
        foreach ($items as $item) {
            $result[$item['ReservationStatus']['id']] = $item['ReservationStatus']['name'];
        }
        return $result;
    }
}

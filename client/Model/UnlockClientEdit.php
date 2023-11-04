<?php
App::uses('AppModel', 'Model');

class UnlockClientEdit extends AppModel {

    public $validate = [
        'staff_id' => [
            'numeric' => [
                'rule' => ['numeric']
            ],
        ],
    ];

    public function findBy($staffId, $reservationId)
    {
        return $this->find('first', [
            'conditions' => [
                'UnlockClientEdit.staff_id' => $staffId,
                'UnlockClientEdit.reservation_id' => $reservationId,
            ],
            'recursive' => -1
        ]);
    }
}
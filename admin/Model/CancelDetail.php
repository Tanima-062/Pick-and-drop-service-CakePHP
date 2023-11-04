<?php
App::uses('AppModel', 'Model');
App::uses('Reservation','Model');
App::uses('Client','Model');
App::uses('Office','Model');

/**
 * CancelDetail Model
 */
class CancelDetail extends AppModel {
	public $validate = [
		'account_code' => [
			'rule' => ['custom', '/^[A-Z_]+$/'],
			'message' => '科目名を選択してください。',
			'required' => true
		],
		'amount' => [
			'rule' => ['custom', '/^[-]?[0-9]+$/'],
			'message' => '半角数字0-9までの数値を入力してください。',
			'required' => true
		],
		'count' => [
			'rule' => ['custom', '/^[0-9]+$/'],
			'message' => '半角数字0-9までの数値を入力してください。',
			'required' => true
		],
	];

	public $belongsTo = [
		'Reservation' => [
			'className' => 'Reservation',
			'foreignKey' => 'reservation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		]
	];

	public function getProceeds($year, $month) {
		$this->Client = new Client();
		$clients = $this->Client->getClientByConclusionContractCriteria();

		$result = [];
		if (!empty($clients['rent'])) {
			$conditions = [
				'Reservation.rent_datetime like' => '%' . $year . '-' . $month . '%',
				'Reservation.client_id' => $clients['rent'],
				'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
			];

			$result = $this->find('all', [
				'fields' => [
					'CancelDetail.*',
					'Reservation.*',
					'Office.*',
				],
				'conditions' => $conditions,
				'joins' => [
					[
						'type' => "INNER",
						'alias' => "Office",
						'table' => "offices",
						'conditions' => "Office.id = Reservation.rent_office_id"
					],
					[
						'type' => 'INNER',
						'alias' => 'CommodityItem',
						'table' => 'commodity_items',
						'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
					],
					[
						'type' => 'INNER',
						'alias' => 'Commodity',
						'table' => 'commodities',
						'conditions' => 'Commodity.id = CommodityItem.commodity_id'
					]
				]
			]);
		}

		$result2 = [];
		if (!empty($clients['return'])) {
			$conditions = [
				'Reservation.return_datetime like' => '%' . $year . '-' . $month . '%',
				'Reservation.client_id' => $clients['return'],
				'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED
			];

			$result2 = $this->find('all', [
				'fields' => [
					'CancelDetail.*',
					'Reservation.*',
					'Office.*',
				],
				'conditions' => $conditions,
				'joins' => [
					[
						'type' => "INNER",
						'alias' => "Office",
						'table' => "offices",
						'conditions' => "Office.id = Reservation.rent_office_id"
					],
					[
						'type' => 'INNER',
						'alias' => 'CommodityItem',
						'table' => 'commodity_items',
						'conditions' => 'CommodityItem.id = Reservation.commodity_item_id'
					],
					[
						'type' => 'INNER',
						'alias' => 'Commodity',
						'table' => 'commodities',
						'conditions' => 'Commodity.id = CommodityItem.commodity_id'
					]
				]
			]);
		}

		$allResult = array_merge($result, $result2);
		return $allResult;
	}

	public function getCancelFeesGroupByReservationId($reservationIds) {
		$result = $this->find('all', [
			'fields' => [
				'CancelDetail.reservation_id',
				'SUM(CancelDetail.amount * CancelDetail.count) as cancel_fee'
			],
			'conditions' => [
				'CancelDetail.reservation_id' => $reservationIds,
				// 精算,予約csvではこの条件の金額が不要(function __addCancelDetailFormatの会社別集計に合わせる)
				'CancelDetail.account_code not' => ['ADMINISTRATIVE_FEE','ADVENTURE_FEE']
			],
			'group' => [
				'CancelDetail.reservation_id'
			],
			'recursive' => -1
		]);
		return Hash::combine($result, '{n}.CancelDetail.reservation_id', '{n}.0.cancel_fee');
	}
}

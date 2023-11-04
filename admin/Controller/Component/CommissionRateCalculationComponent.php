<?php
/*
 * コントローラー側で各モデル()を読み込むこと
 */

class CommissionRateCalculationComponent extends Component{


	public function initialize($controller) {
		$this->controller = $controller;
	}

	public function applyAll() {
		$this->controller->Client->find('all');
		// 精算管理会社一覧を取得
		$settlementCompanyList = $this->controller->SettlementCompany->find('all');

		foreach($settlementCompanyList as $settlementCompany) {
			$this->apply($settlementCompany);
		}
	}

	/**
	 * 指定クライアントの販売手数料を適用する
	 * @param $settlementCompany
	 * @param $save
	 * @return int
	 */
	public function apply($settlementCompany, $save=true) {
		// 適用対象を取得
		$applyRecord = $this->__searchApplyRecord($settlementCompany);

		if (count($applyRecord) > 0 && !$save) {
			return $applyRecord['CommissionRate']['commission_rate'];
		}

		if (count($applyRecord) > 0) { 	// 精算管理会社テーブルの各commission_rateカラムを更新する
			$this->controller->SettlementCompany->id = $settlementCompany['SettlementCompany']['id'];
			$this->controller->SettlementCompany->saveField('commission_rate', $applyRecord['CommissionRate']['commission_rate']);
			$this->controller->SettlementCompany->saveField('staff_id', $this->controller->cdata['id']);
		}

		return 0;
	}

	/**
	 * 適用レコードを探す
	 * @param $settlementCompany
	 * @return array
	 */
	private function __searchApplyRecord($settlementCompany) {
		$commissionRates = $this->controller->CommissionRate->find('all', [
			'conditions' => [
				'CommissionRate.client_id' => $settlementCompany['SettlementCompany']['client_id'],
				'CommissionRate.is_published' => 1,
				'CommissionRate.delete_flg' => 0
			]
		]);

		$today = time();
		// 何度もSQL発行するのは無駄なので保持する(プロパティにいれてもいいかも)
		$step_condition_type_num_arr = [];
		foreach($commissionRates as $commissionRate) { // 適用期間外だったらパス
			if (strtotime($commissionRate['CommissionRate']['apply_term_from']) > $today ||
				strtotime($commissionRate['CommissionRate']['apply_term_to']) < $today
			) {
				continue;
			}

			if ($commissionRate['CommissionRate']['accounting_condition'] == 'FIXED_RATE') { // 計上条件(定率)
				return $commissionRate;
			}
			elseif ($commissionRate['CommissionRate']['accounting_condition'] == 'STEP_RATE') { // 計上条件(段階条件定率)
				if (count($step_condition_type_num_arr) == 0) {
					switch ($commissionRate['CommissionRate']['step_condition_type']) { // 仕様上このループ内では同じ値になるはず
						case 'CLOSE_NUM':
							$step_condition_type_num_arr['step_condition_type_num'] = $this->__getCloseNumber($settlementCompany, $commissionRate);
							break;
						case 'RESERVED_NUM':
							$step_condition_type_num_arr['step_condition_type_num'] = $this->__getReservedNumber($settlementCompany, $commissionRate);
							break;
						case 'CLOSE_AMOUNT':
							$step_condition_type_num_arr['step_condition_type_num'] = $this->__getCloseAmount($settlementCompany, $commissionRate);
							break;
						case 'RESERVED_AMOUNT':
							$step_condition_type_num_arr['step_condition_type_num'] = $this->__getReservedAmount($settlementCompany, $commissionRate);
							break;
						default:
							break;
					}
				}

				$this->log("settlementCompanyId:".$settlementCompany['SettlementCompany']['id'].", step_condition_type_num:".$step_condition_type_num_arr['step_condition_type_num'], LOG_DEBUG);
				// 段階条件の範囲に入っているか
				if (empty($commissionRate['CommissionRate']['step_condition_value1']) && !is_numeric($commissionRate['CommissionRate']['step_condition_value1'])) {
					if ($step_condition_type_num_arr['step_condition_type_num'] < $commissionRate['CommissionRate']['step_condition_value2']) {
						return $commissionRate;
					}
				} elseif (empty($commissionRate['CommissionRate']['step_condition_value2']) && !is_numeric($commissionRate['CommissionRate']['step_condition_value2'])) {
					if ($commissionRate['CommissionRate']['step_condition_value1'] <= $step_condition_type_num_arr['step_condition_type_num']) {
						return $commissionRate;
					}
				} else {
					if ($commissionRate['CommissionRate']['step_condition_value1'] <= $step_condition_type_num_arr['step_condition_type_num'] &&
						$step_condition_type_num_arr['step_condition_type_num'] < $commissionRate['CommissionRate']['step_condition_value2']) {
						return $commissionRate;
					}
				}
			}
		}

		return [];
	}

	/**
	 * 条件判定が精算管理会社のときのクエリ条件を追加
	 * @param $settlementCompany
	 * @param $commissionRate
	 * @param $conditions
	 * @return mixed
	 */
	private function __queryJoinSettlementCompany($settlementCompany, $commissionRate, $conditions)
	{
		$conditions['joins'] = [
			[
				'type' => 'INNER',
				'table' => 'commodity_items',
				'alias' => 'CommodityItem',
				'conditions' => 'CommodityItem.id = Reservation.commodity_item_id',
			],
			[
				'type' => 'INNER',
				'table' => 'commodities',
				'alias' => 'Commodity',
				'conditions' => 'Commodity.id = CommodityItem.commodity_id',
			]
		];
		$conditions['conditions']['Commodity.sales_type'] = Constant::SALES_TYPE_ARRANGED;
		// 条件判定が精算管理会社のとき
		if ($commissionRate['CommissionRate']['contract_condition'] == 'SETTLEMENT_COMPANY') {
			$conditions['joins'][] = [
					'type' => 'INNER',
					'table' => 'offices',
					'alias' => 'Offices',
					'conditions' => 'Offices.id = Reservation.rent_office_id',
			];
			$conditions['conditions']['Offices.settlement_company_id'] = $settlementCompany['SettlementCompany']['id'];
		}

		return $conditions;
	}

	/**
	 * 前月成約数取得
	 * @param $settlementCompany
	 * @param $commissionRate
	 * @return int
	 */
	private function __getCloseNumber($settlementCompany, $commissionRate) {
		$client = $this->controller->Client->findById($settlementCompany['SettlementCompany']['client_id']);
		$timing = ($client['Client']['conclusion_contract_criteria']) ? 'return_datetime' : 'rent_datetime';

		$today = time();
		$conditions = [
			'conditions' => [
				'Reservation.client_id' => $settlementCompany['SettlementCompany']['client_id'],
				'Reservation.reservation_status_id' => 2,
				'Reservation.'.$timing.' >=' => date("Y-m-d 00:00:00", strtotime("first day of - 1 month", $today)),
				'Reservation.'.$timing.' <=' => date("Y-m-d 23:59:59", strtotime("last day of - 1 month", $today)),
			],
			'recursive' => -1
		];

		// 条件判定が精算管理会社のとき
		$conditions = $this->__queryJoinSettlementCompany($settlementCompany, $commissionRate, $conditions);

		return $this->controller->Reservation->find('count', $conditions);
	}

	/**
	 * 前月予約獲得数取得
	 * @param $settlementCompany
	 * @param $commissionRate
	 * @return int
	 */
	private function __getReservedNumber($settlementCompany, $commissionRate) {
		$today = time();
		$conditions = [
			'conditions' => [
				'Reservation.client_id' => $settlementCompany['SettlementCompany']['client_id'],
				'Reservation.created >=' => date("Y-m-d 00:00:00", strtotime("first day of - 1 month", $today)),
				'Reservation.created <=' => date("Y-m-d 23:59:59", strtotime("last day of - 1 month", $today))
			],
			'recursive' => -1
		];

		// 条件判定が精算管理会社のとき
		$conditions = $this->__queryJoinSettlementCompany($settlementCompany, $commissionRate, $conditions);

		return $this->controller->Reservation->find('count', $conditions);
	}

	/**
	 * 前月成約金額取得
	 * @param $settlementCompany
	 * @param $commissionRate
	 * @return int
	 */
	private function __getCloseAmount($settlementCompany, $commissionRate) {
		$client = $this->controller->Client->findById($settlementCompany['SettlementCompany']['client_id']);
		$timing = ($client['Client']['conclusion_contract_criteria']) ? 'return_datetime' : 'rent_datetime';

		$today = time();
		$conditions = [
			'conditions' => [
				'Reservation.client_id' => $settlementCompany['SettlementCompany']['client_id'],
				'Reservation.reservation_status_id' => 2,
				'Reservation.'.$timing.' >=' => date("Y-m-d 00:00:00", strtotime("first day of - 1 month", $today)),
				'Reservation.'.$timing.' <=' => date("Y-m-d 23:59:59", strtotime("last day of - 1 month", $today))
			],
			'fields' => [
				'sum(Reservation.amount) as sumAmount'
			],
			'recursive' => -1
		];

		// 条件判定が精算管理会社のとき
		$conditions = $this->__queryJoinSettlementCompany($settlementCompany, $commissionRate, $conditions);

		$sumAmount = $this->controller->Reservation->find('first', $conditions);

		if (count($sumAmount) > 0) {
			return $sumAmount[0]['sumAmount'];
		}

		return 0;
	}

	/**
	 * 前月予約獲得金額取得
	 * @param $settlementCompany
	 * @param $commissionRate
	 * @return int
	 */
	private function __getReservedAmount($settlementCompany, $commissionRate) {
		$today = time();
		$conditions = [
			'conditions' => [
				'Reservation.client_id' => $settlementCompany['SettlementCompany']['client_id'],
				'Reservation.created >=' => date("Y-m-d 00:00:00", strtotime("first day of - 1 month", $today)),
				'Reservation.created <=' => date("Y-m-d 23:59:59", strtotime("last day of - 1 month", $today))
			],
			'fields' => [
				'sum(Reservation.amount) as sumAmount'
			],
			'recursive' => -1
		];

		// 条件判定が精算管理会社のとき
		$conditions = $this->__queryJoinSettlementCompany($settlementCompany, $commissionRate, $conditions);

		$sumAmount = $this->controller->Reservation->find('first', $conditions);

		if (count($sumAmount) > 0) {
			return $sumAmount[0]['sumAmount'];
		}

		return 0;
	}
}

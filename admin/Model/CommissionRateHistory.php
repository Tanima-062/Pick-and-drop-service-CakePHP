<?php
App::uses('AppModel', 'Model');

/**
 * CommissionRateHistory Model
 *
 */
class CommissionRateHistory extends AppModel {

	public function getRateHistoriesBySettlementCompanyIds($settlementCompanyIds) {
		$result = $this->find('all', [
			'fields' => [
				'CommissionRateHistory.rate_ym',
				'CommissionRateHistory.settlement_company_id',
				'CommissionRateHistory.commission_rate'
			],
			'conditions' => [
				'CommissionRateHistory.settlement_company_id' => $settlementCompanyIds
			],
			'recursive' => -1
		]);
		return Hash::combine($result, '{n}.CommissionRateHistory.settlement_company_id', '{n}.CommissionRateHistory.commission_rate', '{n}.CommissionRateHistory.rate_ym');
	}
}

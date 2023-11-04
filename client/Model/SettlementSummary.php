<?php
App::uses('AppModel', 'Model');
/**
 * SettlementSummary Model
 */
class SettlementSummary extends AppModel {

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'SettlementSummarySalesPerformance'=>array(
			'className'=>'SettlementSummarySalesPerformance',
			'foreignKey'=>'settlement_summary_id',
			'dependent'=>false,
			'conditions'=>'',
			'fields'=>'',
			'order'=>'',
			'limit'=>'',
			'offset'=>'',
			'exclusive'=>'',
			'finderQuery'=>'',
			'counterQuery'=>''
		),
		'SettlementSummaryDetail'=>array(
			'className'=>'SettlementSummaryDetail',
			'foreignKey'=>'settlement_summary_id',
			'dependent'=>false,
			'conditions'=>'',
			'fields'=>'',
			'order'=>'',
			'limit'=>'',
			'offset'=>'',
			'exclusive'=>'',
			'finderQuery'=>'',
			'counterQuery'=>''
		),
		'SettlementSummaryNextAdjustment'=>array(
			'className'=>'SettlementSummaryNextAdjustment',
			'foreignKey'=>'settlement_summary_id',
			'dependent'=>false,
			'conditions'=>'',
			'fields'=>'',
			'order'=>'',
			'limit'=>'',
			'offset'=>'',
			'exclusive'=>'',
			'finderQuery'=>'',
			'counterQuery'=>''
		)
	);

	/*
	 * 指定されたスタッフがダウンロード可能か判定して返す
	 */
	public function isAccessibleByThisStaff($settlementSummaryId, $clientId)
	{
		// システム管理者は必ずOK
		$clientData = $this->_getCurrentUser();
		if ($clientData['is_system_admin']) {
			return true;
		}
		$count = $this->find('count', array(
			'conditions' => array(
				'SettlementSummary.id' => $settlementSummaryId,
				'SettlementCompanyStaff.staff_id' => $clientData['id'],
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'settlement_companies',
					'alias' => 'SettlementCompany',
					'conditions' => array(
						'SettlementCompany.accounting_code = SettlementSummary.settlement_company_accounting_code',
					)
				),
				array(
					'type' => 'inner',
					'table' => 'settlement_company_staffs',
					'alias' => 'SettlementCompanyStaff',
					'conditions' => array(
						'SettlementCompany.id = SettlementCompanyStaff.settlement_company_id'
					)
				),
			),
			'recursive' => -1,
		));
		return $count > 0;
	}

}

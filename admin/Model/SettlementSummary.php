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

	/**
	 * 同期時のメール送信に使用するデータ取得
	 *
	 * @param string $id
	 * @return array
	 */
	public function getSynchronizationSettlementCompanyData($id){
		$options = [
			'fields' => array(
				'SettlementCompany.name',
				'CONCAT(
					SettlementCompany.billing_email1,
					IF(SettlementCompany.billing_email2 = "", "", CONCAT(",",SettlementCompany.billing_email2)),
					IF(SettlementCompany.billing_email3 = "", "", CONCAT(",",SettlementCompany.billing_email3)),
					IF(SettlementCompany.billing_email4 = "", "", CONCAT(",",SettlementCompany.billing_email4)),
					IF(SettlementCompany.billing_email5 = "", "", CONCAT(",",SettlementCompany.billing_email5)),
					IF(SettlementCompany.billing_email6 = "", "", CONCAT(",",SettlementCompany.billing_email6)),
					IF(SettlementCompany.billing_email7 = "", "", CONCAT(",",SettlementCompany.billing_email7)),
					IF(SettlementCompany.billing_email7 = "", "", CONCAT(",",SettlementCompany.billing_email8)),
					IF(SettlementCompany.billing_email8 = "", "", CONCAT(",",SettlementCompany.billing_email9)),
					IF(SettlementCompany.billing_email8 = "", "", CONCAT(",",SettlementCompany.billing_email10))
				) as billing_email',
				'SettlementSummary.document_status',
			),
			'joins' => [
				[
					'type' => 'inner',
					'table' => 'settlement_companies',
					'alias' => 'SettlementCompany',
					'conditions' => 'SettlementCompany.accounting_code = SettlementSummary.settlement_company_accounting_code'
				],
			],
			'conditions' => [
				'SettlementSummary.id' => $id,
			],
			'recursive' => -1
		];

		return $this->find('all', $options);
	}
}

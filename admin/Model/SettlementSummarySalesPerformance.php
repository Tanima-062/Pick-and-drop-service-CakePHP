<?php
App::uses('AppModel', 'Model');
/**
 * SettlementSummarySalesPerformance Model
 */
class SettlementSummarySalesPerformance extends AppModel {

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'SettlementSummary'=>array(
			'className'=>'SettlementSummary',
			'foreignKey'=>'settlement_summary_id',
			'conditions'=>'',
			'fields'=>'',
			'order'=>''
		)
	);
}

<?php
App::uses('AppModel', 'Model');
/**
 * SettlementSummaryDetail Model
 */
class SettlementSummaryDetail extends AppModel {

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

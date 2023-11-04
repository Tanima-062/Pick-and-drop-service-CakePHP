<?php
App::uses('AppModel', 'Model');

class SettlementCompanyStaff extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SettlementCompany' => array(
			'className' => 'SettlementCompany',
			'foreignKey' => 'settlement_company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
        );

}

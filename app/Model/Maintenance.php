<?php
App::uses('AppModel', 'Model');
/**
 * Maintenance Model
 *
 * @property Staff $Staff
 */
class Maintenance extends AppModel {

	protected $cacheConfig = '1hour';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	// イーコンメンテ中か？
	public function isEconMaintenance()
	{
		$isEconMaintenance = false;
		$maintenanceInfo = $this->findC('first', array(
			'field' => array(
				'Maintenance.is_under_maintenance',
			),
			'conditions' => array(
				'type' => 'ECON',
			),
			'recursive' => -1,
		));
		if (!empty($maintenanceInfo) && $maintenanceInfo['Maintenance']['is_under_maintenance']) {
			$isEconMaintenance = true;
		}
		return $isEconMaintenance;
	}

	// 新規決済フラグ
	public function isPaymentAPI()
	{
		$isEconMaintenance = false;
		$maintenanceInfo = $this->findC('first', array(
			'field' => array(
				'Maintenance.is_under_maintenance',
			),
			'conditions' => array(
				'type' => 'PaymentAPI',
			),
			'recursive' => -1,
		));
		if (!empty($maintenanceInfo) && $maintenanceInfo['Maintenance']['is_under_maintenance']) {
			$isEconMaintenance = true;
		}
		return $isEconMaintenance;
	}
}

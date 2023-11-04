<?php
App::uses('AppModel','Model');

/**
 * OfficeSupplement Model
 *
 */
class OfficeSupplement extends AppModel {

	public function getOfficeSupplementByOfficeId($officeId) {
		$options = array(
			'fields' => array(
				'office_id',
				'nearest_transport',
				'method_of_transport',
				'required_transport_time',
				'pickup_method',
				'pickup_wait_time',
				'pickup_wait_time_busy',
				'rent_proc_time',
				'rent_proc_time_busy',
			),
			'conditions' => array(
				'OfficeSupplement.office_id' => $officeId,
				'OfficeSupplement.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$ret = $this->findC('all', $options);
		// このままでは使いにくいのでoffice_idをキーにする
		return Hash::combine($ret, '{n}.OfficeSupplement.office_id', '{n}.OfficeSupplement');
	}

}

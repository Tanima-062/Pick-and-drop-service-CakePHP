<?php
App::uses('AppModel', 'Model');
/**
 * CmThApplicationDetail Model
 *
 */
class CmThApplicationDetail extends AppModel {

	public $useDbConfig = 'skyticket';
	public $useTable = 'cm_th_application_detail';

	public function getCmApplicationIdByReservationId($reservationId) {
		$result = $this->find('first', [
			'fields' => [
				'CmThApplicationDetail.cm_application_id'
			],
			'conditions' => [
				'CmThApplicationDetail.application_id' => $reservationId,
				'CmThApplicationDetail.service_cd' => 'rc'
			],
			'recursive' => -1
		]);
		if (empty($result)) {
			return 0;
		}
		return $result['CmThApplicationDetail']['cm_application_id'];
	}

	/**
	 * cm_application_idからapplication_idを取得する
	 */
	public function getApplicationIdByCmApplicationId($cmApplicationId)
    {
		$result = $this->find('first', [
			'fields' => [
				'CmThApplicationDetail.application_id'
			],
			'conditions' => [
				'CmThApplicationDetail.cm_application_id' => $cmApplicationId,
				'CmThApplicationDetail.service_cd' => 'rc'
			],
			'recursive' => -1,
		]);
		if (empty($result)) {
			return 0;
		}
		return $result['CmThApplicationDetail']['application_id'];
    }
}

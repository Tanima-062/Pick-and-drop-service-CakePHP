<?php
App::uses('AppModel', 'Model');
/**
 * OfficeSelectionPermission Model
 *
 */
class OfficeSelectionPermission extends AppModel {

	public function getPermissionOfficeList($staffId) {
		$options = array(
				'fields' => array(
						'OfficeSelectionPermission.id',
						'OfficeSelectionPermission.office_id',
				),
				'conditions' => array(
						'OfficeSelectionPermission.staff_id' => $staffId,
				),
				'recursive' => -1,
		);
		return $this->find('list', $options);
	}

}

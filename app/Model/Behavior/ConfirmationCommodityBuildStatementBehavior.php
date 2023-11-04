<?php
class ConfirmationCommodityBuildStatementBehavior extends ModelBehavior {
	
	/**
	 * 出発営業所サブクエリを生成する
	 * @param Model $model
	 * @param int $officeId
	 * @return string
	 */
	public function getRentOfficeSubQuery(Model $model, $officeId) {
		return $this->_getOfficeSubQuery($model->Office, $officeId, 'rent');
	}

	/**
	 * 返却営業所サブクエリを生成する
	 * @param Model $model
	 * @param int $officeId
	 * @return string
	 */
	public function getReturnOfficeSubQuery(Model $model, $officeId) {
		return $this->_getOfficeSubQuery($model->Office, $officeId, 'return');
	}

	private function _getOfficeSubQuery(Model $model, $officeId, $accept) {
		$db = $model->getDataSource();
		$tname = "commodity_{$accept}_offices";
		
		$sql = $db->buildStatement(array(
			'fields' => array(
				'Office.id AS office_id',
				'Office.name AS office_name',
				'Office.address AS office_address',
				'Office.access AS office_access',
				'Office.tel AS office_tel',
				"Office.{$accept}_meeting_info",
				"{$tname}.commodity_id",
			),
			'table' => $db->fullTableName($model),
			'alias' => 'Office',
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => $tname,
					'conditions' => array(
						"{$tname}.office_id = Office.id",
					),
				),
			),
			'conditions' => array(
				'Office.id' => $officeId,
			)
		), $model);
				
		return $sql;
	}
}
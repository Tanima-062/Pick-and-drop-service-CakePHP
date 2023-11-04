<?php
class CommodityDataBuildStatementBehavior extends ModelBehavior {

	/**
	 * 出発営業所サブクエリを生成する
	 * @param Model $model
	 * @param string $identifier
	 * @param string $previousIdentifier
	 * @param int $time
	 * @param mixed $clientId
	 * @return string
	 */
	public function getRentOfficeSubQuery(Model $model, $identifier, $previousIdentifier, $time, $clientId) {
		return $this->_getOfficeSubQuery($model->Office, $identifier, $previousIdentifier, $time, $clientId, 'rent');
	}

	/**
	 * 返却営業所サブクエリを生成する
	 * @param Model $model
	 * @param string $identifier
	 * @param string $previousIdentifier
	 * @param int $time
	 * @param mixed $clientId
	 * @return string
	 */
	public function getReturnOfficeSubQuery(Model $model, $identifier, $previousIdentifier, $time, $clientId) {
		return $this->_getOfficeSubQuery($model->Office, $identifier, $previousIdentifier, $time, $clientId, 'return');
	}

	private function _getOfficeSubQuery(Model $model, $identifier, $previousIdentifier, $time, $clientId, $accept) {
		$db = $model->getDataSource();

		$conditions = array(
			'fields' => array(
				'Office.id',
				'Office.client_id',
				'Office.sort',
				'Office.name',
				'Office.url',
				'Office.area_id',
				'Office.bullet_train_id',
				'Office.accept_' . $accept,
				'Office.airport_id',
				'Office.tel',
				'Office.address',
				'Office.access_dynamic',
				"CASE"
				. " WHEN office_business_hours.office_id IS NOT NULL"
				. " THEN office_business_hours.{$identifier}_hours_from"
				. " ELSE Office.{$identifier}_hours_from"
				. " END AS office_hours_from",
				"CASE"
				. " WHEN office_business_hours.office_id IS NOT NULL"
				. " THEN office_business_hours.{$identifier}_hours_to"
				. " ELSE Office.{$identifier}_hours_to"
				. " END AS office_hours_to",
				'office_business_hours.start_day',
				'office_business_hours.end_day',
			),
			'table' => $db->fullTableName($model),
			'alias' => 'Office',
			'joins' => array(
				array(
					'type' => 'LEFT',
					'table' => 'office_business_hours',
					'conditions' => array(
						'Office.id = office_business_hours.office_id',
						'office_business_hours.start_day_unixtime <=' => $time,
						'office_business_hours.end_day_unixtime >=' => $time,
						'office_business_hours.delete_flg = 0',
					),
				),
			),
			'conditions' => array(
				'Office.client_id' => $clientId,
				"Office.accept_{$accept} = 1",
				'Office.delete_flg = 0',
			),
		);

		if (!empty($previousIdentifier)) {
			// 前日の営業時間を取得
			$previousDay = strtotime('-1 day', $time);
			$conditions['joins'][] = array(
				'type' => 'LEFT',
				'table' => 'office_business_hours',
				'alias' => 'OfficeBusinessHourPrevious',
				'conditions' => array(
					'Office.id = OfficeBusinessHourPrevious.office_id',
					'OfficeBusinessHourPrevious.start_day_unixtime <=' => $previousDay,
					'OfficeBusinessHourPrevious.end_day_unixtime >=' => $previousDay,
					'OfficeBusinessHourPrevious.delete_flg = 0',
				),
			);
			$conditions['fields'][] =
				"CASE"
				. " WHEN OfficeBusinessHourPrevious.office_id IS NOT NULL"
				. " THEN OfficeBusinessHourPrevious.{$previousIdentifier}_hours_to"
				. " ELSE Office.{$previousIdentifier}_hours_to"
				. " END AS office_hours_to_previous";
		}

		$sql = $db->buildStatement($conditions, $model);

		return $sql;
	}
}
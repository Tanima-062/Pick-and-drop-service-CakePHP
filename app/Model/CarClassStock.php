<?php
App::uses('AppModel', 'Model');
/**
 * CarClassStock Model
 */
class CarClassStock extends AppModel {

	public function getCarClassStock($stockGroupId, $carClassId, $year, $month, $day = 0) {
		$options = array(
			'fields' => array(
				'CarClassStock.id',
				"DATE_FORMAT(CarClassStock.stock_date, '%e') AS day",
				'CarClassStock.stock_count',
			),
			'conditions' => array(
				'CarClassStock.stock_group_id' => $stockGroupId,
				'CarClassStock.car_class_id' => $carClassId,
				'CarClassStock.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		if (empty($day)) {
			$targetMonth = sprintf('%04d-%02d-', $year, $month);
			$options['conditions']['CarClassStock.stock_date BETWEEN ? AND ?'] = array($targetMonth.'01', $targetMonth.'31');
		} else {
			$options['conditions']['CarClassStock.stock_date'] = sprintf('%04d-%02d-%02d', $year, $month, $day);
		}

		return $this->find('all', $options);
	}

	public function getCarClassStockDateRange($stockGroupId, $carClassId, $toDate, $fromDate) {
		$options = array(
			'fields' => array(
				'CarClassStock.id',
				'CarClassStock.stock_date',
				'CarClassStock.stock_count',
				'CarClassStock.suspension',
			),
			'conditions' => array(
				'CarClassStock.stock_group_id' => $stockGroupId,
				'CarClassStock.car_class_id' => $carClassId,
				'CarClassStock.stock_date BETWEEN ? AND ?' => array($toDate, $fromDate),
				'CarClassStock.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

	public function getCarClassStockCount($stockGroupId, $carClassId, $year, $month, $day = 0) {

		$ret = $this->getCarClassStock($stockGroupId, $carClassId, $year, $month, $day);
		if (!empty($ret)) {
			$ret = Hash::combine($ret, '{n}.0.day', '{n}.CarClassStock.stock_count');
		}

		return $ret;
	}

	public function getCarClassStockIdAndCount($stockGroupId, $carClassId, $year, $month, $day = 0) {

		$ret = $this->getCarClassStock($stockGroupId, $carClassId, $year, $month, $day);
		if (!empty($ret)) {
			$ret = Hash::combine($ret, '{n}.0.day', '{n}.CarClassStock');
		}

		return $ret;
	}

	public function bulkUpdate($stocks) {

		$this->query("
			UPDATE
				car_class_stocks
			SET
				stock_count = ".$stocks['stock_count'].",
				staff_id = ".$stocks['staff_id'].",
				modified = ".$stocks['modified']."
			WHERE
				id IN (".implode(',', $stocks['id']).")",
			false);
	}

	public function bulkInsert($stocks) {

		$this->query("
			INSERT INTO
				car_class_stocks(
					client_id,
					stock_group_id,
					car_class_id,
					stock_date,
					stock_count,
					staff_id,
					created,
					modified
				)
			VALUES
				".implode(',', $stocks),
			false);
	}

	// 販売フラグの一括更新
	public function bulkUpdateSuspension($stocks) {

		$this->query("
			UPDATE
				car_class_stocks
			SET
				suspension = ".$stocks['suspension'].",
				modified = ".$stocks['modified']."
			WHERE
				id IN (".implode(',', $stocks['id']).")",
			false);
	}
}

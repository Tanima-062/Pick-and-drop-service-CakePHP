<?php
App::uses('AppModel', 'Model');
/**
 * DropOffAreaRate Model
 */
class DropOffAreaRate extends AppModel {

	protected $cacheConfig = '1hour';

	/**
	 * 乗り捨て料金取得
	 * @param int $fromOfficeId
	 * @param int $returnOfficeId
	 * @param int $carClassId
	 */
	public function getDropOffAreaPrice($fromOfficeId, $returnOfficeId, $carClassId) {
		$options = array(
			'fields' => array(
				'DropOffAreaRate.price',
				'DropOffAreaRate.price2',
				'DropOffAreaRate.price3',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'FromDropOffArea',
					'table' => 'drop_off_areas',
					'conditions' => 'FromDropOffArea.id = DropOffAreaRate.rent_drop_off_area_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'FromOffice',
					'table' => 'offices',
					'conditions' => 'FromDropOffArea.id = FromOffice.area_drop_off_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'ReturnDropOffArea',
					'table' => 'drop_off_areas',
					'conditions' => 'ReturnDropOffArea.id = DropOffAreaRate.return_drop_off_area_id',
				),
				array(
					'type' => 'INNER',
					'alias' => 'ReturnOffice',
					'table' => 'offices',
					'conditions' => 'ReturnDropOffArea.id = ReturnOffice.area_drop_off_id',
				),
			),
			'conditions' => array(
				'FromOffice.id' => $fromOfficeId,
				'ReturnOffice.id' => $returnOfficeId,
				'DropOffAreaRate.delete_flg' => 0,
				'FromDropOffArea.delete_flg' => 0,
				'FromOffice.delete_flg' => 0,
				'ReturnDropOffArea.delete_flg' => 0,
				'ReturnOffice.delete_flg' => 0,
			),
			'recursive' => -1,
		);

		$result = $this->findC('first', $options);

		if (empty($result)) {
			return null;
		}

		// 乗捨料金料金パターン取得
		$this->CarClass = ClassRegistry::init('CarClass');
		$this->setDataSource($this->getDataSource()->configKeyName);

		$pricePattern = $this->CarClass->findC('list', array(
			'fields' => 'CarClass.drop_off_price_pattern',
			'conditions' => array(
				'CarClass.id' => $carClassId,
			),
			'recursive' => -1,
		));

		if (empty($pricePattern)) {
			return null;
		}

		$pricePattern = $pricePattern[key($pricePattern)];
		$price = ($pricePattern > 1) ? 'price' . $pricePattern : 'price';

		return $result['DropOffAreaRate'][$price];
	}

	/**
	 * 乗り捨て料金・深夜手数料を取得
	 * @param int $fromOfficeId
	 * @param int $returnOfficeId
	 * @param int $carClassId
	 * @param string $fromTime
	 * @param string $returnTime
	 * @return array
	 */
	public function dropOffLateNight($fromOfficeId, $returnOfficeId, $carClassId, $fromTime, $returnTime) {
		$Office = ClassRegistry::init('Office');

		// 乗り捨て料金
		$result['dropPrice'] = '';
		if ($fromOfficeId != $returnOfficeId) {
			$dropOffAreaPrice = $this->getDropOffAreaPrice($fromOfficeId, $returnOfficeId, $carClassId);
			if (isset($dropOffAreaPrice)) {
				$result['dropPrice'] = (string) $dropOffAreaPrice;
			}
		}
		// 深夜手数料
		$fromData = array(
			'fromOfficeId' => $fromOfficeId,
			'fromTime' => $fromTime,
		);
		$returnData = array(
			'returnOfficeId' => $returnOfficeId,
			'returnTime' => $returnTime,
		);
		$lateNightFee = $Office->getLateNightFee($fromData, $returnData);
		$result['nightFee'] = '';
		if (!empty($lateNightFee)) {
			$result['nightFee'] = (string) $lateNightFee;
		}
		return $result;
	}

	/**
	 * 指定した空港への乗り捨て料金最安値を取得
	 *
	 * @param int[] $officeIds
	 * @param int[] $airportIds
	 * @return array
	 */
	public function getLowestPriceAirport($officeIds, $airportIds) {
		if (empty($officeIds) || empty($airportIds)) {
			return array();
		}

		// 乗捨料金パターンを考慮した最安値
		$this->virtualFields = array(
			'price' => 'CASE Client.required_drop_off_price_pattern' .
				' WHEN 1 THEN MIN(DropOffAreaRate.price)' .
				' WHEN 2 THEN MIN(LEAST(DropOffAreaRate.price, DropOffAreaRate.price2))' .
				' WHEN 3 THEN MIN(LEAST(DropOffAreaRate.price, DropOffAreaRate.price2, DropOffAreaRate.price2))' .
				' ELSE MIN(DropOffAreaRate.price)' .
				' END'
		);

		$options = array(
			'fields' => array(
				'ReturnOffice.airport_id',
				'price',
				'Client.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RentDropOffArea',
					'table' => 'drop_off_areas',
					'conditions' => array('RentDropOffArea.id = DropOffAreaRate.rent_drop_off_area_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'RentOffice',
					'table' => 'offices',
					'conditions' => array('RentDropOffArea.id = RentOffice.area_drop_off_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'ReturnDropOffArea',
					'table' => 'drop_off_areas',
					'conditions' => array('ReturnDropOffArea.id = DropOffAreaRate.return_drop_off_area_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'ReturnOffice',
					'table' => 'offices',
					'conditions' => array('ReturnDropOffArea.id = ReturnOffice.area_drop_off_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Client.id = RentOffice.client_id')
				),
			),
			'conditions' => array(
				'RentOffice.id' => $officeIds,
				'ReturnOffice.airport_id' => $airportIds,
				'DropOffAreaRate.delete_flg' => 0,
				'RentOffice.delete_flg' => 0,
				'ReturnDropOffArea.delete_flg' => 0,
				'ReturnOffice.delete_flg' => 0,
				'Client.delete_flg' => 0,
			),
			'group' => array(
				'Client.id',
				'ReturnOffice.airport_id',
			),
			'recursive' => -1,
		);

		$ret = $this->find('list', $options);
		$this->virtualFields = null;

		return $ret;
	}

	/**
	 * 指定した駅への乗り捨て料金最安値を取得
	 *
	 * @param int[] $officeIds
	 * @param int[] $stationIds
	 * @return array
	 */
	public function getLowestPriceStation($officeIds, $stationIds) {
		if (empty($officeIds) || empty($stationIds)) {
			return array();
		}

		// 乗捨料金パターンを考慮した最安値
		$this->virtualFields = array(
			'price' => 'CASE Client.required_drop_off_price_pattern' .
				' WHEN 1 THEN MIN(DropOffAreaRate.price)' .
				' WHEN 2 THEN MIN(LEAST(DropOffAreaRate.price, DropOffAreaRate.price2))' .
				' WHEN 3 THEN MIN(LEAST(DropOffAreaRate.price, DropOffAreaRate.price2, DropOffAreaRate.price2))' .
				' ELSE MIN(DropOffAreaRate.price)' .
				' END'
		);

		$options = array(
			'fields' => array(
				'OfficeStation.station_id',
				'price',
				'Client.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'RentDropOffArea',
					'table' => 'drop_off_areas',
					'conditions' => array('RentDropOffArea.id = DropOffAreaRate.rent_drop_off_area_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'RentOffice',
					'table' => 'offices',
					'conditions' => array('RentDropOffArea.id = RentOffice.area_drop_off_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'Client',
					'table' => 'clients',
					'conditions' => array('Client.id = RentOffice.client_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'ReturnDropOffArea',
					'table' => 'drop_off_areas',
					'conditions' => array('ReturnDropOffArea.id = DropOffAreaRate.return_drop_off_area_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'ReturnOffice',
					'table' => 'offices',
					'conditions' => array('ReturnDropOffArea.id = ReturnOffice.area_drop_off_id')
				),
				array(
					'type' => 'INNER',
					'alias' => 'OfficeStation',
					'table' => 'office_stations',
					'conditions' => array('ReturnOffice.id = OfficeStation.office_id')
				),
			),
			'conditions' => array(
				'RentOffice.id' => $officeIds,
				'OfficeStation.station_id' => $stationIds,
				'DropOffAreaRate.delete_flg' => 0,
				'RentDropOffArea.delete_flg' => 0,
				'RentOffice.delete_flg' => 0,
				'Client.delete_flg' => 0,
				'ReturnDropOffArea.delete_flg' => 0,
				'ReturnOffice.delete_flg' => 0,
				'OfficeStation.delete_flg' => 0,
			),
			'group' => array(
				'Client.id',
				'OfficeStation.station_id'
			),
			'recursive' => -1,
		);

		$ret = $this->find('list', $options);
		$this->virtualFields = null;

		return $ret;
	}

}

<?php
App::uses('AppShell', 'Console/Command');

class PrepareSearchHintInfoShell extends AppShell {

	public $uses = array('KeyValue', 'Commodity', 'Office', 'Prefecture', 'Landmark', 'Area', 'Station', 'KeyValue');

	public function startup() {
		parent::startup();
	}

	public function main() {
		$data = array();
		$now = date('Y-m-d');

		// 出発地に紐づく営業所の商品で、一番短い手仕舞い時間データ作成
		$landmarkList = $this->Landmark->getAllLandmarks();
		$airports = $landmarkList['ArrayInStock'];
		$place = 3;
		foreach ($airports as $p) {
			foreach ($p as $airport_id => $airport) {
				$nearOfficesIds = $this->Office->getOfficeIdListByAirportId($airport_id);
				$officeIds = array_keys($nearOfficesIds);
				if (!empty($officeIds)) {
					$minDeadlineHour = $this->Commodity->getMinDeadlineHour($now, $officeIds);
					if (!empty($minDeadlineHour)) {
						$minDeadlineHour = $minDeadlineHour[0]['deadline_hours'];
						if (!empty($minDeadlineHour)) {
							$data[$place.'_'.$airport_id] = intval($minDeadlineHour);
						}
					}
				}
			}
		}

		$stations = $this->Station->getStationAll(false, true);
		$place = 4;
		foreach ($stations as $p) {
			foreach ($p as $s) {
				foreach ($s as $station) {
					$nearOfficesIds = $this->Office->getOfficeIdListByStationId($station['id']);
					$officeIds = array_keys($nearOfficesIds);
					if (!empty($officeIds)) {
						$minDeadlineHour = $this->Commodity->getMinDeadlineHour($now, $officeIds);
						if (!empty($minDeadlineHour)) {
							$minDeadlineHour = $minDeadlineHour[0]['deadline_hours'];
							if (!empty($minDeadlineHour)) {
								$data[$place.'_'.$station['id']] = intval($minDeadlineHour);
							}
						}
					}
				}
			}
		}

		$areas = $this->Area->getAreaAll(true);
		$place = 1;
		foreach ($areas as $prefectureId => $areasGroup) {
			foreach ($areasGroup as $area) {
				$nearOfficesIds = $this->Office->getOfficeIdListByAreaId($area['id']);
				$officeIds = array_keys($nearOfficesIds);
				if (!empty($officeIds)) {
					$minDeadlineHour = $this->Commodity->getMinDeadlineHour($now, $officeIds);
					if (!empty($minDeadlineHour)) {
						$minDeadlineHour = $minDeadlineHour[0]['deadline_hours'];
						if (!empty($minDeadlineHour)) {
							$data[$place.'_'.$area['id']] = intval($minDeadlineHour);
						}
					}
				}
			}
		}

		if (count($data) > 0) {
			$json = json_encode($data);
		} else {
			$json = '';
		}

		$keyValue = $this->KeyValue->find('first', array('conditions' => array('key' => 'front_search_min_deadline_hours_data')));

		if (empty($keyValue)) {
			$record = array();
			$record['id'] = null;
			$record['key'] = 'front_search_min_deadline_hours_data';
		} else {
			$record = $keyValue['KeyValue'];
			$record['modified'] = date('Y-m-d H:i:s');
		}

		$record['value'] = $json;

		$this->KeyValue->save($record);
	}
}

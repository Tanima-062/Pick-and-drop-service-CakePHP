<?php
App::uses('BaseApiPostShell', 'Console/Command');

class BudgetReservationShell extends BaseApiPostShell {

	public $uses = array('Reservation', 'ReservationDetail', 'Equipment', 'Privilege');

	private $clientId = 13;


	// 実行メイン
	protected function executeMain() {

		// 予約情報取得
		list($reservations, $ids, $updateParams) = $this->getReservations();

		if (empty($reservations)) {
			$this->outputLog('処理対象のデータが見つかりませんでした');
			return;
		}
		foreach ($updateParams as $k => $v) {
			$this->outputLog(sprintf('データ (id = %d, reservation_status_id = %d, modified = %s, old_api_status = %d, new_api_status = %d)',
					$k, $v['reservation_status_id'], $v['modified'], $v['old_api_status'], $v['new_api_status']));
		}

		// 送信データ作成
		$data = $this->makePostData($reservations, $ids);

		// データ送信
		$url = (IS_PRODUCTION) ? '' : 'http://jp.skyticket.jp/rentacar/api/budget/v1/reservations';	// TODO 本番用のURL
		try {
			$response = $this->sendPostData($url, $data, array('X-Auth-Key' => 'DcGWXLQGiEDNL2Y_gZJr'));
			if (!$response['response']['result']['status']) {
				// ありえないはずだが
				$this->outputLog(sprintf("予約連携が失敗しました (%s)", $response['response']['result']['message']));
				return;
			}
		} catch (Exception $e) {
			$this->outputLog(sprintf("データ送信で例外が発生しました (%s)", $e->getMessage()));
			return;
		}

		// API処理状態更新
		foreach ($updateParams as $k => $v) {
			$this->Reservation->begin();
			try {
				$this->Reservation->updateApiStatus(array(
					'id'				 => $k,
					'new_api_status'	 => $v['new_api_status'],
					'modified'			 => $v['modified'],
				));
			} catch (Exception $e) {
				$this->Reservation->rollback();
				$this->outputLog(sprintf("id = %d のデータは例外が発生したため、更新されませんでした (%s)", $k, $e->getMessage()));
				continue;
			}
			$this->Reservation->commit();
		}
	}

	// 予約情報取得
	private function getReservations() {
		$reservations = $this->Reservation->getReservationApiPostData($this->clientId);

		$apiStatusApiUpdate = Constant::apiStatusApiUpdate();
		$apiStatusCall = Constant::apiStatusCall();

		$reservationCombined = array();
		$reservationIds = array();
		$updateParams = array();

		foreach ($reservations as $v) {
			$reservationId = $v['Reservation']['id'];

			$apiStatusId = $v['Reservation']['api_status_id'];
			$reservationStatusId = $v['Reservation']['reservation_status_id'];

			$combined = array(
				'reservation_key'			 => $v['Reservation']['reservation_key'],
				'reservation_status_id'		 => $apiStatusCall[$apiStatusId],
				'rent_datetime'				 => $v[0]['rent_datetime'],
				'rent_shop_id'				 => $v['RentOffice']['id'],
				'rent_shop_name'			 => $v['RentOffice']['name'],
				'return_datetime'			 => $v[0]['return_datetime'],
				'return_shop_id'			 => $v['ReturnOffice']['id'],
				'return_shop_name'			 => $v['ReturnOffice']['name'],
				'car_class_id'				 => $v['CarClass']['id'],
				'car_class_name'			 => $v['CarClass']['name'],
				'car_model_id'				 => $v['CarModel']['id'],
				'car_model_name'			 => $v['CarModel']['name'],
				'plan_id'					 => $v['CommodityItem']['id'],
				'plan_name'					 => $v['Commodity']['name'],
				'last_name'					 => $v['Reservation']['last_name'],
				'first_name'				 => $v['Reservation']['first_name'],
				'tel'						 => $v['Reservation']['tel'],
				'email'						 => $v['Reservation']['email'],
				'arrival_flight_number'		 => $v['Reservation']['arrival_flight_number'],
				'departure_flight_number'	 => $v['Reservation']['departure_flight_number'],
				'adults_count'				 => $v['Reservation']['adults_count'],
				'children_count'			 => $v['Reservation']['children_count'],
				'infants_count'				 => $v['Reservation']['infants_count'],
				'total_amount'				 => $v['Reservation']['amount'],
			);
			$reservationCombined[] = $combined;
			$reservationIds[] = $reservationId;

			$updateParams[$reservationId] = array(
				'old_api_status'		 => $apiStatusId,
				'new_api_status'		 => $apiStatusApiUpdate[$apiStatusId],
				'modified'				 => $v['Reservation']['modified'],
				'reservation_status_id'	 => $reservationStatusId,
			);

			// 予約連携前にキャンセルされた場合、キャンセルの連携も行う
			if ($reservationStatusId == Constant::STATUS_CANCEL && $apiStatusId == Constant::API_STATUS_UNDER_RESERVATION) {
				$combined['reservation_status_id'] = $apiStatusCall[Constant::API_STATUS_UNDER_CANCEL];
				$reservationCombined[] = $combined;
				$reservationIds[] = $reservationId;
				$updateParams[$reservationId]['new_api_status'] = $apiStatusApiUpdate[Constant::API_STATUS_UNDER_CANCEL];
			}
		}

		return array($reservationCombined, $reservationIds, $updateParams);
	}

	// 料金明細情報（予約明細）取得
	private function getDetails() {
		$details = $this->ReservationDetail->getDetailApiPostData($this->clientId);

		$detailsCombined = array();
		foreach ($details as $v) {
			$reservationId = $v['Reservation']['id'];
			$typeId = $v['ReservationDetail']['detail_type_id'];
			if (!isset($detailsCombined[$reservationId])) {
				$detailsCombined[$reservationId] = array();
			}
			$detailsCombined[$reservationId][] = array(
				'detail_type_id'	 => $typeId,
				'amount'			 => $v['ReservationDetail']['amount'],
			);
		}

		return $detailsCombined;
	}

	// 標準オプション情報（装備）取得
	private function getNormalOptions() {
		$equipments = $this->Equipment->getEquipmentApiPostData($this->clientId);

		$normalOptions = array();
		foreach ($equipments as $v) {
			$reservationId = $v['Reservation']['id'];
			if (!isset($normalOptions[$reservationId])) {
				$normalOptions[$reservationId] = array();
			}
			$normalOptions[$reservationId][] = array(
				'option_id'		 => $v['Equipment']['id'],
				'option_name'	 => $v['Equipment']['name'],
				'count'			 => 1,
				'amount'		 => 0,
			);
		}

		return $normalOptions;
	}

	// 選択オプション情報（特典）取得
	private function getExtraOptions() {
		$options = $this->Privilege->getPrivilegeApiPostData($this->clientId, 0);
		$sheets = $this->Privilege->getPrivilegeApiPostData($this->clientId, 1);

		$privileges = array_merge($options, $sheets);

		$extraOptions = array();
		foreach ($privileges as $v) {
			$reservationId = $v['Reservation']['id'];
			$alias = $v[0]['alias'];
			if (!isset($extraOptions[$reservationId])) {
				$extraOptions[$reservationId] = array();
			}
			$extraOptions[$reservationId][] = array(
				'option_id'		 => $v['Privilege']['id'],
				'option_name'	 => $v['Privilege']['name'],
				'count'			 => $v[$alias]['count'],
				'amount'		 => ($v[$alias]['price'] > 0) ? ($v[$alias]['price'] / $v[$alias]['count']) : 0,
			);
		}

		return $extraOptions;
	}

	// 送信データ作成
	private function makePostData($reservations, $ids) {
		$details = $this->getDetails();
		$normalOptions = $this->getNormalOptions();
		$extraOptions = $this->getExtraOptions();

		$reservationList = array();
		foreach ($reservations as $k => $v) {
			$reservationId = $ids[$k];

			$object = array(
				'reservation'		 => $v,
				'details'			 => $details[$reservationId],
				'normal_options'	 => isset($normalOptions[$reservationId]) ? $normalOptions[$reservationId] : array(),
				'extra_options'		 => isset($extraOptions[$reservationId]) ? $extraOptions[$reservationId] : array(),
			);

			$reservationList[] = $object;
		}

		return array('request' => array('reservation_list' => $reservationList));
	}

}

<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('ReservationUtilComponent', 'Controller/Component');

class UpdateTourSalesStockShell extends AppShell {

	public $uses = array('TourSalesStock', 'TourReservation', 'Reservation', 'CarClassReservation');
	public $components = array('ReservationAPISelect', 'ReservationUtil');

	public $Components = null;

	public function startup() {

		$collection = new ComponentCollection();
		$this->ReservationUtil = new ReservationUtilComponent($collection);
		$this->ReservationUtil->initialize($this);

		parent::startup();
	}

	/**
	 * 紐付く航空券予約がキャンセルされていたら募集型予約もキャンセルする
	 * 募集型予約を集計して募集型在庫の販売数に反映する
	 */
	public function main() {
		$this->cancelTourReservation();
		$this->updateTourSalesStock();

		$this->cancelTourCreatedByAPI();
	}

	/**
	 * 募集型予約を集計して募集型在庫の販売数に反映する
	 * @return bool
	 */
	private function updateTourSalesStock() {
		$today = date('Y-m-d');

		// 在庫テーブルに存在する空港のみ対応
		$iataCds = $this->TourSalesStock->find('all', [
			'fields' => [
				'DISTINCT iata_cd',
			],
			'conditions' => [
				'stock_date >= ' => $today,
				'delete_flg' => 0
			]
		]);
		$iataCds = Hash::extract($iataCds, '{n}.TourSalesStock.iata_cd');

		if (empty($iataCds)) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour sales stock table future record not exist');
			return true;
		}

		$sqlFormat = "UPDATE common.cm_tm_rc_tour_sales_stock SET sold_count = ELT(FIELD(id,%s),%s) WHERE id IN (%s)";

		foreach ($iataCds as $iataCd) {
			// 空港ごとの在庫レコードを取得
			$stocks = $this->TourSalesStock->find('all', [
				'fields' => [
					'key',
					'id',
					'sold_count'
				],
				'conditions' => [
					'iata_cd' => $iataCd,
					'stock_date >= ' => $today,
					'delete_flg' => 0
				],
				'recursive' => -1
			]);
			$stocks = Hash::combine($stocks, '{n}.TourSalesStock.key', '{n}.TourSalesStock');

			// 空港ごとの予約レコードを取得
			$reservations = $this->TourReservation->find('all', [
				'fields' => [
					'id',
					'iata_cd',
					'arrival_dt',
					'departure_dt',
					'tour_car_type_id'
				],
				'conditions' => [
					'iata_cd' => $iataCd,
					'departure_dt >= ' => $today,
					'reservation_status_id <> ' => 3
				],
				'recursive' => -1
			]);

			// 更新対象の抽出
			$updates = [];
			foreach ($reservations as $reservation) {
				$r = $reservation['TourReservation'];
				$startDay = date('Y-m-d', strtotime($r['arrival_dt']));
				$endDay = date('Y-m-d', strtotime($r['departure_dt']));
				if ($startDay < $today) {
					$startDay = $today;
				}
				$daySub = (strtotime($endDay) - strtotime($startDay)) / (3600 * 24);
				for ($i = 0; $i <= $daySub; $i++) {
					$day = date('Y-m-d', strtotime($startDay.' +'.$i.' day'));
					$key = sprintf('%s_%s_%s', $iataCd, $day, $r['tour_car_type_id']);
					if (!isset($stocks[$key])) {
						continue;
					}
					if (isset($updates[$key])) {
						$updates[$key] += 1;
					} else {
						$updates[$key] = 1;
					}
				}
			}
			$stockIds = [];
			$stockValues = [];
			foreach ($stocks as $key => $stock) {
				if (isset($updates[$key])) {
					if ($stock['sold_count'] != $updates[$key]) {
						$stockIds[] = $stock['id'];
						$stockValues[] = $updates[$key];
					}
				} else {
					if ($stock['sold_count'] > 0) {
						$stockIds[] = $stock['id'];
						$stockValues[] = 0;
					}
				}
			}

			// 一括更新
			// 空港(1) x 車両クラス(4) x 半年分日数(180) = MAX720レコードで、SQL長さは問題にならない想定
			if (count($stockIds) > 0) {
				$sql = sprintf($sqlFormat, implode(',', $stockIds), implode(',', $stockValues), implode(',', $stockIds));
				$result = $this->TourSalesStock->query($sql);
				if ($result === false) {
					$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour sales stock table ('.$iataCd.') update returns false');
				} else {
					$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour sales stock table ('.$iataCd.') update '.$this->TourSalesStock->getAffectedRows().' rows');
				}
			} else {
				$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour sales stock table ('.$iataCd.') update target none');
			}
		}
		return true;
	}

	/**
	 * 紐付く航空券予約がキャンセルされていたら募集型予約もキャンセルする
	 * @return bool
	 */
	private function cancelTourReservation()
	{
		$selectSql = "
			SELECT
				rtr.id,
				rtr.cm_application_id,
				rtr.booking_dt,
				rtr.arrival_dt,
				rtr.departure_dt,
				rtr.reservation_status_id,
				dta.status
			FROM common.cm_th_rc_tour_reservation rtr
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.cm_application_id = rtr.cm_application_id AND ad.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad.application_id AND dta.status NOT IN (100, 150, 200, 300, 400, 600, 700)
			LEFT JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = rtr.cm_application_id AND ad2.service_cd = 'rc'
			WHERE
				rtr.reservation_status_id <> 3 AND ad2.detail_id IS NULL
		";
		$updateSql = "
			UPDATE
				common.cm_th_rc_tour_reservation rtr
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.cm_application_id = rtr.cm_application_id AND ad.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad.application_id AND dta.status NOT IN (100, 150, 200, 300, 400, 600, 700)
			LEFT JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = rtr.cm_application_id AND ad2.service_cd = 'rc'
			SET
				rtr.reservation_status_id = 3,
				rtr.staff_id = 0
			WHERE
				rtr.reservation_status_id <> 3 AND ad2.detail_id IS NULL
		";

		// 更新対象をログに出しておきたい
		$result = $this->TourReservation->query($selectSql);
		if ($result === false) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour reservation table select returns false');
			return false;
		} elseif (empty($result)) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour reservation table select returns none');
			return true;
		}
		foreach ($result as $r) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour reservation table update target '.json_encode($r['rtr']).', da application status ('.$r['dta']['status'].')');
		}

		// 更新
		$result = $this->TourReservation->query($updateSql);
		if ($result === false) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour reservation table update returns false');
			return false;
		}
		$this->out(date('Y/m/d H:i:s ') . $this->name . ': tour reservation table update '.$this->TourReservation->getAffectedRows().' rows');

		return true;
	}

	/**
	 * 紐付く航空券予約がキャンセルされていたら募集型予約もキャンセルする
	 * 2021年12月中旬〜APIによる自動予約対応
	 * @return bool
	 */
	private function cancelTourCreatedByAPI()
	{
		try {
			// コンフィグ違うのでトランザクション別々
			$this->TourReservation->begin();
			$this->Reservation->begin();

			$this->cancelTourReservationAuto();
			$this->cancelCarClassReservation();
			$cancelTarget = $this->cancelReservation();

			$this->TourReservation->commit();
			$this->Reservation->commit();
		} catch (Exception $e) {
			$this->TourReservation->rollback();
			$this->Reservation->rollback();
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': ' . $e->getMessage());
			return false;
		}

		if (!empty($cancelTarget)) {
			$this->cancelApiReservation($cancelTarget);

			// 予約キャンセル通知
			foreach ($cancelTarget as $t) {
				if (!in_array($t['rr']['client_id'], Constant::notSendmailClientIdsWhenAgentOrganized())) {
					$this->ReservationUtil->sendNotificationMail($t['rr']['id'], 'cancel');
				}
			}
		}

		return true;
	}

	private function cancelTourReservationAuto()
	{
		$selectSql = "
			SELECT
				rtr.id,
				rtr.cm_application_id,
				rtr.booking_dt,
				rtr.arrival_dt,
				rtr.departure_dt,
				rtr.reservation_status_id,
				rtr.reservation_key,
				dta.status
			FROM common.cm_th_rc_tour_reservation rtr
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.cm_application_id = rtr.cm_application_id AND ad.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad.application_id AND dta.status IN (500, 600, 700, 1000, 0, 30)
			INNER JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = rtr.cm_application_id AND ad2.service_cd = 'rc'
			WHERE
				rtr.reservation_status_id <> 3
		";
		$updateSql = "
			UPDATE
				common.cm_th_rc_tour_reservation rtr
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.cm_application_id = rtr.cm_application_id AND ad.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad.application_id AND dta.status IN (500, 600, 700, 1000, 0, 30)
			INNER JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = rtr.cm_application_id AND ad2.service_cd = 'rc'
			SET
				rtr.reservation_status_id = 3,
				rtr.staff_id = 0
			WHERE
				rtr.reservation_status_id <> 3
		";

		// 更新対象をログに出しておきたい
		$result = $this->TourReservation->query($selectSql);
		if ($result === false) {
			throw new Exception('(tour) reservation table select returns false');
		} elseif (empty($result)) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': (tour) reservation table select returns none');
			return;
		}
		foreach ($result as $r) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': (tour) reservation table update target '.json_encode($r['rtr']).', da application status ('.$r['dta']['status'].')');
		}

		$result = $this->TourReservation->query($updateSql);
		if ($result === false) {
			throw new Exception('(tour) reservation table update returns false');
		}
		$this->out(date('Y/m/d H:i:s ') . $this->name . ': (tour) reservation table update '.$this->TourReservation->getAffectedRows().' rows');
	}

	private function cancelReservation()
	{
		$date = date('Y-m-d H:i:s');

		$selectSql = "
			SELECT
				rr.id,
				rr.reservation_key,
				rr.reservation_datetime,
				rr.rent_datetime,
				rr.return_datetime,
				rr.reservation_status_id,
				rr.client_id,
				rr.api_status_id,
				rr.control_number,
				dta.status
			FROM rentacar.reservations rr
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.application_id = rr.id AND ad.service_cd = 'rc'    
			INNER JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = ad.cm_application_id AND ad2.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad2.application_id AND dta.status IN (500, 600, 700, 1000, 0, 30)
			WHERE
				rr.reservation_status_id <> 3
		";
		$updateSql = "
			UPDATE
				rentacar.reservations rr
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.application_id = rr.id AND ad.service_cd = 'rc'
			INNER JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = ad.cm_application_id AND ad2.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad2.application_id AND dta.status IN (500, 600, 700, 1000, 0, 30)
			SET
				rr.reservation_status_id = 3,
			    rr.cancel_flg = 1,
			    rr.cancel_datetime = '{$date}',
			    rr.cancel_staff_id = 0,
			    rr.cancel_reason_id = 5
			WHERE
				rr.reservation_status_id <> 3
		";

		// 更新対象をログに出しておきたい
		$target = $this->Reservation->query($selectSql);
		if ($target === false) {
			throw new Exception('reservation table select returns false');
		} elseif (empty($target)) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': reservation table select returns none');
			return;
		}
		foreach ($target as $t) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': reservation table update target '.json_encode($t['rr']).', da application status ('.$t['dta']['status'].')');
		}

		$result = $this->Reservation->query($updateSql);
		if ($result === false) {
			throw new Exception('reservation table update returns false');
		}
		$this->out(date('Y/m/d H:i:s ') . $this->name . ': reservation table update '.$this->Reservation->getAffectedRows().' rows');

		return $target;
	}

	private function cancelCarClassReservation()
	{
		$selectSql = "
			SELECT
				rccr.id,
				rccr.stock_date,
				rccr.reservation_id,
				dta.status
			FROM rentacar.car_class_reservations rccr
			INNER JOIN rentacar.reservations rr
				ON rr.id = rccr.reservation_id
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.application_id = rr.id AND ad.service_cd = 'rc'    
			INNER JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = ad.cm_application_id AND ad2.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad2.application_id AND dta.status IN (500, 600, 700, 1000, 0, 30)
			WHERE
				rr.reservation_status_id <> 3 AND rccr.delete_flg = 0
		";
		$updateSql = "
			UPDATE
				rentacar.car_class_reservations rccr
			INNER JOIN rentacar.reservations rr
				ON rr.id = rccr.reservation_id
			INNER JOIN skyticket.cm_th_application_detail ad
				ON ad.application_id = rr.id AND ad.service_cd = 'rc'
			INNER JOIN skyticket.cm_th_application_detail ad2
				ON ad2.cm_application_id = ad.cm_application_id AND ad2.service_cd = 'da'
			INNER JOIN skyticket.da_th_application dta
				ON dta.application_id = ad2.application_id AND dta.status IN (500, 600, 700, 1000, 0, 30)
			SET
				rccr.delete_flg = 1
			WHERE
				rr.reservation_status_id <> 3 AND rccr.delete_flg = 0
		";

		// 更新対象をログに出しておきたい
		$result = $this->CarClassReservation->query($selectSql);
		if ($result === false) {
			throw new Exception('car class reservation table select returns false');
		} elseif (empty($result)) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': car class reservation table select returns none');
			return;
		}
		foreach ($result as $r) {
			$this->out(date('Y/m/d H:i:s ') . $this->name . ': car class reservation table update target '.json_encode($r['rccr']).', da application status ('.$r['dta']['status'].')');
		}

		$result = $this->CarClassReservation->query($updateSql);
		if ($result === false) {
			throw new Exception('car class reservation table update returns false');
		}
		$this->out(date('Y/m/d H:i:s ') . $this->name . ': car class reservation table update '.$this->CarClassReservation->getAffectedRows().' rows');
	}

	private function cancelApiReservation($target)
	{
		$collection = new ComponentCollection();
		$selectAPI = $collection->load('ReservationAPISelect');
		$selectAPI->startup(new Controller());
		foreach ($target as $t) {
			if ($t['rr']['api_status_id'] != Constant::API_STATUS_EXCLUDED) {
				$componentName = $selectAPI->getApiComponentName($t['rr']['client_id']);
				if (empty($componentName)) {
					continue;
				}
				$reservationAPI = $collection->load($componentName);

				$reservationAPI->setAdminReservationData($t['rr']['id'], Constant::API_STATUS_CANCEL);

				list($success, $result) = $reservationAPI->sendReservationData();
				if ($success) {
					if ($result['status']) {
						continue;
					}
					$errorString = sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
				} else {
					$errorString = 'キャンセル連携中に何らかのエラーが発生しました。';
				}
				$this->out(date('Y/m/d H:i:s ') . $this->name . ': ' . $errorString);
				$reservationAPI->sendAlertFromAdmin($t['rr']['control_number']/*, $_SERVER['HTTP_HOST']*/);
			}
		}
	}
}

<?php
App::uses('BaseRestApiController', 'Controller');
App::uses('ReservationsRetrieveApiController', 'Controller');

class ApplicationsRetrieveApiController extends BaseRestApiController {
	public $uses = array('Reservation');

	private $isEconMaintenance = false;

	public function beforeFilter() {
		parent::beforeFilter();
		$this->ApiCommon->setCorsHeader();
	}

	// 申込確認
	public function view($id) {
		$reservationKeys = $this->Reservation->getReserveKeyByCmApplicationId($id);

		if (empty($reservationKeys)) {
			// 予約なし
			throw new ApiException(ApiException::NO_RESERVATION, 404);
		}

		$reservations = array();

		foreach ($reservationKeys as $key => $val) {
			$reservations[] = $this->requestAction(
				array('controller' => 'reservations_retrieve_api', 'action' => 'view'),
				array('pass' => array('key' => $key), 'data' => array('tel' => $val))
			);
		}

		$this->responseData = $reservations;
	}

}

<?php
App::uses('BaseRestApiController', 'Controller');
App::uses('ApiException', 'Error');

class ReservationsDetailRetrieveApiController extends BaseRestApiController {

	public $uses = array(
		'Reservation',
		'ReservationPrivilege',
		'ReservationChildSheet',
		'CommodityItem',
		'CmThApplication'
	);

	public function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * cm_application_idを引数に予約情報を取得する
	 *
	 * @param string $id
	 * @return json 予約情報(オプション含む)
	 */
	public function index($id) {
		// paymentDetail用予約データ取得
		$paymentDetailData = $this->Reservation->getReservationCarNameByCmApplicationId($id);
		if (empty($paymentDetailData)) {
			// 予約なし
			throw new ApiException(ApiException::NO_RESERVATION, 404);
		}
		$commodityItemId = $paymentDetailData['CommodityItem']['id'];

		//車名生成
		$carInfoList = $this->CommodityItem->getCarInfo($commodityItemId);
		//車両タイプ
		$carType = '';
		if(!empty($carInfoList[$commodityItemId]['CarType']['name'])) {
			$carType = $carInfoList[$commodityItemId]['CarType']['name'];
		}
		//車種
		$carModel = '';
		if(!empty($carInfoList[$commodityItemId]['CarModel'])) {
			$carModeLists = Hash::extract($carInfoList[$commodityItemId]['CarModel'],'{n}.name');
			if(!empty($carModeLists)) {
				$carModel = implode($carModeLists,'・');
			}
		}
		$rentacar_name = $carType .'（'. $carModel;
		( !empty($commodity['CommodityItem']['car_model_id']) ) ? $rentacar_name .= '）' : $rentacar_name .= '他）';

		//予約詳細データ
		$detail = $this->__getpaymentDetaildetail($paymentDetailData['Reservation']['id']);
		// チャイルドシート取得
		$childSheetData = $this->ReservationChildSheet->getReservationChildSheetData($paymentDetailData['Reservation']['id']);
		// その他オプション取得
		$privilegeData = $this->ReservationPrivilege->getReservationPrivilegeData($paymentDetailData['Reservation']['id']);
		// シート/オプションのデータ成形
		$options = $this->__getOptions($childSheetData, $privilegeData, count($detail)+1);

		$paymentDetaildetail = array_merge($detail, $options);

		$paymentDetailRc[] = array(
			'rentacarName' => $rentacar_name,
			'depatureDatetime' => $paymentDetailData['Reservation']['rent_datetime'],
			'arrivalDatetime' => $paymentDetailData['Reservation']['return_datetime'],
			'detail' => $paymentDetaildetail
		);
		$paymentDetail = array(
			'rc' => $paymentDetailRc,
		);
	
	
		// registrationData用予約データ取得
		$reservation_data = $this->Reservation->getReservationInfoByCmApplicationId($id);
		if (empty($reservation_data)) {
			// 予約なし
			throw new ApiException(ApiException::NO_RESERVATION, 404);
		}

		$params = array(
			'fields' => 'user_id',
			'conditions' => array('cm_application_id' => $id)
		);
		$userId = $this->CmThApplication->find('first',$params);

		// シート/オプションのデータ成形
		$options = $this->__getOptions($childSheetData, $privilegeData);

		$jp_detail['rc'] = array(
				'reservationKey' => $reservation_data['Reservation']['reservation_key'],
				'shopName' => $reservation_data['Client']['name'],
				'startSalesOfficeName' => $reservation_data['RentOffice']['name'],
				'endSalesOfficeName' => $reservation_data['ReturnOffice']['name'],
				'startDate' => $reservation_data['Reservation']['rent_datetime'],
				'endDate' => $reservation_data['Reservation']['return_datetime'],
				'totalPrice' => $reservation_data['Reservation']['amount'],
				'tax' => (int)0,
				'option' => $options,
		);
		
		$reservation_detail['jp'] = array(
				'totalPrice' => $reservation_data['Reservation']['amount'],
				'totalOtherPrice' => $reservation_data['Reservation']['amount'],
				'userId' => $userId['CmThApplication']['user_id'],
				'applicantFamilyName' => '',
				'applicantFirstName' => '',
				'applicantFamilyNameKana' => $reservation_data['Reservation']['last_name'],
				'applicantFirstNameKana' => $reservation_data['Reservation']['first_name'],
				'tel' => $reservation_data['Reservation']['tel'],
				'localContact' => null,
				'email' => $reservation_data['Reservation']['email'],
				'birth' => array(),
				'gender' => null,
				'advertisingCode' => $reservation_data['Reservation']['advertising_cd'],
				'paymentLimit' => $reservation_data['Reservation']['payment_limit_datetime'],
				'systemFee' => $reservation_data['Reservation']['administrative_fee'],
				'detail' => $jp_detail
		);

		$reservation['rc'][] = array(
				'cmApplicationId' => $id,
				'currency' => 'JPY',
				'lang' => 'ja',
				'totalPrice' => $reservation_data['Reservation']['amount'],
				'totalOtherPrice' => $reservation_data['Reservation']['amount'],
				'totalOriginalPrice' => $reservation_data['Reservation']['amount'],
				'totalOtherOriginalPrice' => $reservation_data['Reservation']['amount'],
				'localPayment' => false,
				'basicAt' => $reservation_data['Reservation']['rent_datetime'],
				'discountData' => array(),
				'isPoint' => false,
				'pointData' => array(),
				'detail' => $reservation_detail
		);

		$appendix['discountData'] = array();

		$registrationData = array(
				'appendix' => $appendix,
				'reservation' => $reservation
		);
		$this->responseData = array(
			'status_code' => http_response_code(),
			'paymentDetail' => $paymentDetail,
			'registrationData' => $registrationData,
		);
	}

	/**
	 * reservation_idを引数に詳細情報を取得する
	 *
	 * @param string $reservation_id
	 * @return array 予約情報の詳細
	 */
	private function __getpaymentDetaildetail($reservationId) {
		//オプション/シートは別で取得するため、免責補償は基本料金と合算するため出力除外
		$exclusionDetailTypeId = array('2','3','6');

		$details = $this->Reservation->getCommodityPrice($reservationId);
		$cnt = 1;
		foreach($details as $dk => $dv){
			if(in_array($dv['ReservationDetail']['detail_type_id'],$exclusionDetailTypeId)){
				continue;
			}
			if($dv['ReservationDetail']['detail_type_id'] == 1){
				$disclaimerCompensationPrice = Hash::extract($details, '{n}.ReservationDetail[detail_type_id=6].amount');
				if(!empty($disclaimerCompensationPrice)){
					$dv["ReservationDetail"]["amount"] += $disclaimerCompensationPrice[0];
				}
			}
			$detail[] = array(
				'title' => $dv["DetailType"]["name"],
				'titleIndent' => false,
				'subTitle' => '',
				'currency' => 'JPY',
				'price' => $dv["ReservationDetail"]["amount"],
				'quantity' => $dv["ReservationDetail"]["count"],
				'order' => $cnt
			);
			$cnt++;
		}
		return $detail;
	}

	/**
	 * reservation_idを引数にオプション情報を取得する
	 *
	 * @param string $reservation_id
	 * @return array 予約情報のオプション
	 */
	private function __getOptions($childSheets, $privileges, $order = 1) {
		$childSheetOption = array();
		$otherOption = array();
		// チャイルドシート取得
		if (!empty($childSheets)) {
			foreach ((array)$childSheets as $value) {
				$childSheetOption[] = array(
					'title' => $value['Privilege']['name'],
					'titleIndent' => false,
					'subTitle' => '',
					'currency' => 'JPY',
					'price' => $value['ReservationChildSheet']['price'],
					'quantity' => (int)$value['ReservationChildSheet']['count'],
					'order' => (int)$order,
				);
				$order++;
			}
		}
		// その他オプション取得
		if (!empty($privileges)) {
			foreach ((array)$privileges as $value) {
				$otherOption[] = array(
					'title' => $value['Privilege']['name'],
					'titleIndent' => false,
					'subTitle' => '',
					'currency' => 'JPY',
					'price' => $value['ReservationPrivilege']['price'],
					'quantity' => (int)$value['ReservationPrivilege']['count'],
					'order' => (int)$order,
				);
				$order++;
			}
		}
		// 取得したオプション項目を集約
		$options = array_merge($childSheetOption, $otherOption);
		return $options;
	}
}

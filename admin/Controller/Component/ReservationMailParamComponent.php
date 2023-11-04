<?php

App::uses('Component', 'Controller');

class ReservationMailParamComponent extends Component
{

	public function getReservationMailParam($reservation)
	{
		$Equipment = ClassRegistry::init('Equipment');
		$Privilege = ClassRegistry::init('Privilege');
		$Office = ClassRegistry::init('Office');

		// UA取得
		if (strcmp(uaCheck(), Constant::DEVICE_PC) == 0) {
			$ua = 'PC';
		} else if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) {
			$ua = 'スマートフォン';
		}

		// 曜日
		$weekday = array('日', '月', '火', '水', '木', '金', '土');
		$rentWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['rent_datetime']))];
		$returnWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['return_datetime']))];

		// 料金内訳
		$privilegeList = $Privilege->getClientPrivilegeList($reservation['Client']['id']);
		$basicText = '';
		$optionList = '';
		$optionText = '';
		$disclaimerText = '';
		$nightFeeText = '';
		$dropOffText = '';
		if (!empty($reservation['ReservationDetail'])) {
			// 基本料金・免責補償料金・乗り捨て料金・深夜手数料
			foreach ($reservation['ReservationDetail'] as $value) {
				if ($value['detail_type_id'] == Constant::DETAIL_TYPE_BASICPRICE) {
					$basicText = number_format($value['amount']) . '円';
				} else if ($value['detail_type_id'] == Constant::DETAIL_TYPE_DISCLAIMER) {
					$disclaimerText = number_format($value['amount']) . '円';
				} else if ($value['detail_type_id'] == Constant::DETAIL_TYPE_NIGHTFEE) {
					$optionList .= '深夜手数料 ' . number_format($value['amount']) . '円  ';
					$nightFeeText = number_format($value['amount']) . '円';
				} else if ($value['detail_type_id'] == Constant::DETAIL_TYPE_DROPOFFPRICE) {
					$optionList .= '乗り捨て料金 ' . number_format($value['amount']) . '円  ';
					$dropOffText = number_format($value['amount']) . '円';
				}
			}
		}
		// オプション
		if (!empty($reservation['ReservationChildSheet'])) {
			// チャイルドシート
			foreach ($reservation['ReservationChildSheet'] as $value) {
				if (!empty($privilegeList[$value['child_sheet_id']])) {
					$optionList .= $privilegeList[$value['child_sheet_id']] . '×' . $value['count'] . ' ' . number_format($value['price']) . '円  ';
					$optionText .= $privilegeList[$value['child_sheet_id']] . '×' . $value['count'] . ' ' . number_format($value['price']) . '円  ';
				}
			}
		}
		if (!empty($reservation['ReservationPrivilege'])) {
			// 特典
			foreach ($reservation['ReservationPrivilege'] as $value) {
				if (!empty($privilegeList[$value['privilege_id']])) {
					$optionList .= $privilegeList[$value['privilege_id']] . '×' . $value['count'] . ' ' . number_format($value['price']) . '円  ';
					$optionText .= $privilegeList[$value['privilege_id']] . '×' . $value['count'] . ' ' . number_format($value['price']) . '円  ';
				}
			}
		}
		// 装備
		$equipmentList = array(0 => '免責補償') + $Equipment->getCommodityEquipmentList($reservation['Commodity']['id']) + array('transmission_flg' => $reservation['Commodity']['transmission_flg'] ? 'MT車' : 'AT車');
		$equipmentText = implode('、', $equipmentList);

		$rentOfficeTime = $Office->getOfficeBusinessHours($reservation['Reservation']['rent_office_id'], date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime'])));
		$returnOfficeTime = $Office->getOfficeBusinessHours($reservation['Reservation']['return_office_id'], date('Y-m-d', strtotime($reservation['Reservation']['return_datetime'])));

		$rentOfficeStartDay = '';
		if (!empty($rentOfficeTime['start_day'])) {
			$rentOfficeStartDay = date('Y/m/d', strtotime($rentOfficeTime['start_day']));
		}
		$rentOfficeEndDay = '';
		if (!empty($rentOfficeTime['end_day'])) {
			$rentOfficeEndDay = date('Y/m/d', strtotime($rentOfficeTime['end_day']));
		}

		$returnOfficeStartDay = '';
		if (!empty($returnOfficeTime['start_day'])) {
			$returnOfficeStartDay = date('Y/m/d', strtotime($returnOfficeTime['start_day']));
		}
		$returnOfficeEndDay = '';
		if (!empty($returnOfficeTime['start_day'])) {
			$returnOfficeEndDay = date('Y/m/d', strtotime($returnOfficeTime['end_day']));
		}

		$administrativeFee = isset($reservation['Reservation']['administrative_fee']) ? $reservation['Reservation']['administrative_fee'] : 0;
		$amountMinusFee = $reservation['Reservation']['amount'] - $administrativeFee;

		$emailView = array(
			'reservation_id' => $reservation['Reservation']['id'],
			'reservation_key' => $reservation['Reservation']['reservation_key'],
			'reservation_hash' => $reservation['Reservation']['reservation_hash'],
			'rent_date' => date('Y年m月d日', strtotime($reservation['Reservation']['rent_datetime'])),
			'rent_week' => $rentWeekDay,
			'rent_time' => date('H:i', strtotime($reservation['Reservation']['rent_datetime'])),
			'return_date' => date('Y年m月d日', strtotime($reservation['Reservation']['return_datetime'])),
			'return_week' => $returnWeekDay,
			'return_time' => date('H:i', strtotime($reservation['Reservation']['return_datetime'])),
			'last_name' => $reservation['Reservation']['last_name'],
			'first_name' => $reservation['Reservation']['first_name'],
			'email' => $reservation['Reservation']['email'],
			'tel' => $reservation['Reservation']['tel'],
			'amount' => $reservation['Reservation']['amount'],
			'administrative_fee' => $administrativeFee,
			'amount_minus_fee' => $amountMinusFee,
			'arrival_flight_number' => $reservation['Reservation']['arrival_flight_number'],
			'departure_flight_number' => $reservation['Reservation']['departure_flight_number'],
			'reservation_datetime' => $reservation['Reservation']['created'],
			'adults_count' => $reservation['Reservation']['adults_count'],
			'children_count' => $reservation['Reservation']['children_count'],
			'infants_count' => $reservation['Reservation']['infants_count'],
			'client_name' => $reservation['Client']['name'],
			'rent_office_name' => $reservation['RentOffice']['name'],
			'rent_office_tel' => $reservation['RentOffice']['tel'],
			'rent_office_hours_from' => date('H:i', strtotime($rentOfficeTime['start_time'])),
			'rent_office_hours_to' => date('H:i', strtotime($rentOfficeTime['end_time'])),
			'rent_office_start_day' => $rentOfficeStartDay,
			'rent_office_end_day' => $rentOfficeEndDay,
			'rent_office_address' => $reservation['RentOffice']['address'],
			'rent_office_access' => $reservation['RentOffice']['access_dynamic'],
			'rent_meeting_info' => mb_convert_kana($reservation['RentOffice']['rent_meeting_info'], 'KV'),
			'rent_office_notification' => mb_convert_kana($reservation['RentOffice']['notification'], 'KV'),
			'return_office_name' => $reservation['ReturnOffice']['name'],
			'return_office_tel' => $reservation['ReturnOffice']['tel'],
			'return_office_hours_from' => date('H:i', strtotime($returnOfficeTime['start_time'])),
			'return_office_hours_to' => date('H:i', strtotime($returnOfficeTime['end_time'])),
			'return_office_start_day' => $returnOfficeStartDay,
			'return_office_end_day' => $returnOfficeEndDay,
			'return_office_address' => $reservation['ReturnOffice']['address'],
			'return_office_access' => $reservation['ReturnOffice']['access_dynamic'],
			'return_meeting_info' => mb_convert_kana($reservation['ReturnOffice']['return_meeting_info'], 'KV'),
			'commodity_name' => mb_convert_kana($reservation['Commodity']['name'], 'KV'),
			'car_class' => $reservation['CarClass']['name'],
			'car_type' => $reservation['CarType']['name'],
			'car_smoking_flg' => $reservation['Commodity']['smoking_flg'],
			'car_count' => $reservation['Reservation']['cars_count'],
			'ua' => $ua,
			'client_reservation_content' => $reservation['Client']['reservation_content'],
			'client_cancel_policy' => $reservation['Client']['cancel_policy'],
			'reservation_email' => $reservation['ReservationMail']['contents'],
			'basic_price' => $basicText,
			'option_list' => $optionList,
			'option_text' => $optionText,
			'equipment_text' => $equipmentText,
			'disclaimer' => $disclaimerText,
			'night_fee' => $nightFeeText,
			'drop_off' => $dropOffText,
			'advertising_cd' => $reservation['Reservation']['advertising_cd'],
		);

		return $emailView;
	}
}

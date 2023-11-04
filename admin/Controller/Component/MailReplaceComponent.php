<?php
class MailReplaceComponent extends Component{

	public $components = array('CancelPolicy');

	public function initialize($controller) {
		$this->Reservation = ClassRegistry::init('Reservation');
		$this->CarType = ClassRegistry::init('CarType');

		$this->carTypeList = $this->CarType->find('list');
		$this->wday = array('日', '月', '火', '水', '木', '金', '土');
	}

	public function getReplaceList() {
		$replaceArr = array(
			array(
				'name' => '予約番号',
				'search_string' => '@@reservation_id@@',
				'example' => 'AB000000123456',
			),
			array(
				'name' => '氏名カナ',
				'search_string' => '@@user_name@@',
				'example' => 'アドベン　タロウ',
			),
			array(
				'name' => 'レンタカー会社名',
				'search_string' => '@@client_name@@',
				'example' => 'スカチケレンタカー',
			),
			array(
				'name' => '受取日時',
				'search_string' => '@@pickup_date@@',
				'example' => '○○○○年○月○日（曜） ○○:○○',
			),
			array(
				'name' => '返却日時',
				'search_string' => '@@return_date@@',
				'example' => '○○○○年○月○日（曜） ○○:○○',
			),
			array(
				'name' => 'プラン名',
				'search_string' => '@@plan_name@@',
				'example' => 'ベーシックプラン【免責付き】　等',
			),
			array(
				'name' => '車両タイプ',
				'search_string' => '@@car_type@@',
				'example' => '車両タイプ
					禁煙/喫煙',
			),
			array(
				'name' => 'ご利用人数',
				'search_string' => '@@number_of_users@@',
				'example' => '大人○名
					子供○名
					幼児○名',
			),
			array(
				'name' => '受取店舗名',
				'search_string' => '@@pickup_name@@',
				'example' => '恵比寿ガーデンプレイス店',
			),
			array(
				'name' => '受取店舗連絡先',
				'search_string' => '@@pickup_tel@@',
				'example' => '03-6277-0515',
			),
			array(
				'name' => '受取店舗営業時間',
				'search_string' => '@@pickup_business_time@@',
				'example' => '10:00~19:00',
			),
			array(
				'name' => '受取店舗住所',
				'search_string' => '@@pickup_address@@',
				'example' => '東京都渋谷区恵比寿4-20',
			),
			array(
				'name' => '受取店舗アクセス',
				'search_string' => '@@pickup_access@@',
				'example' => '恵比寿駅より徒歩10分',
			),
			array(
				'name' => '送迎や待ち合わせに関する情報（受取）',
				'search_string' => '@@pickup_notice@@',
				'example' => '※予約完了メール
					【受取時の送迎や待ち合わせについて】',
			),
			array(
				'name' => '店舗からのご案内',
				'search_string' => '@@pickup_information@@',
				'example' => '※予約完了メール
					●ご利用の店舗からのご案内',
			),
			array(
				'name' => '返却店舗名',
				'search_string' => '@@return_name@@',
				'example' => '恵比寿ガーデンプレイス店',
			),
			array(
				'name' => '返却店舗連絡先',
				'search_string' => '@@return_tel@@',
				'example' => '03-6277-0515',
			),
			array(
				'name' => '返却店舗営業時間',
				'search_string' => '@@return_business_time@@',
				'example' => '10:00~19:00',
			),
			array(
				'name' => '返却店舗住所',
				'search_string' => '@@return_address@@',
				'example' => '東京都渋谷区恵比寿4-20',
			),
			array(
				'name' => '返却店舗アクセス',
				'search_string' => '@@return_access@@',
				'example' => '恵比寿駅より徒歩10分',
			),
			array(
				'name' => '送迎や待ち合わせに関する情報（返却）',
				'search_string' => '@@return_notice@@',
				'example' => '※予約完了メール
					【返却時の送迎や待ち合わせについて】',
			),
			array(
				'name' => '予約車両台数',
				'search_string' => '@@number_of_cars@@',
				'example' => '○台',
			),
			array(
				'name' => '標準装備',
				'search_string' => '@@equipment@@',
				'example' => '免責補償、カーナビ　等',
			),
			array(
				'name' => 'オプション',
				'search_string' => '@@option@@',
				'example' => '○○シート×1 ○○円  ○○補償×1 ○○円　等',
			),
			array(
				'name' => '合計料金',
				'search_string' => '@@amount@@',
				'example' => '○○（円）',
			),
			array(
				'name' => 'お支払い情報',
				'search_string' => '@@payment@@',
				'example' => '支払い方法：○○
					ご入金状況：○○
					ご入金金額：○○円',
			),
			array(
				'name' => 'ご到着便',
				'search_string' => '@@arrival_flight@@',
				'example' => 'ANA○○○',
			),
			array(
				'name' => 'ご出発便',
				'search_string' => '@@departure_flight@@',
				'example' => 'ANA○○○',
			),
			array(
				'name' => '予約完了メール内定型文',
				'search_string' => '@@client_notice@@',
				'example' => '※予約完了メール
					■○○レンタカーからのご案内',
			),
			array(
				'name' => 'キャンセルポリシー',
				'search_string' => '@@cancel_policy@@',
				'example' => '〈キャンセル料〉
					出発日の ○日前まで・・・・無料
					出発日の ○日前まで・・・・基本料金の○○%
					出発日の ○日前まで・・・・基本料金の○○%
					出発日の 当日まで　　・・・・基本料金の○○%
					出発後　 　　　　・・・・基本料金の○○%

					・キャンセル料は○○円を限度とします。
					・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。
					・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。',
			),
			array(
				'name' => 'キャンセルポリシー・補足',
				'search_string' => '@@cancel_policy_add@@',
				'example' => '※予約完了メール
					■キャンセルポリシーに関するお知らせ',
			),
			array(
				'name' => 'マイページURL',
				'search_string' => '@@mypage_url@@',
				'example' => 'マイページリンク（予約番号自動反映）',
			),
		);
	
		return $replaceArr;
	}

	// array[reservations.id][置換対象文字列] = 置換後文字列 を返す関数
	public function getReplacePattern($reservationIds) {
		// 予約詳細データ取得(client/reservations/edit)
		$reservationDetailData = $this->getReservationDetail($reservationIds);
		// 予約完了メール用データ取得
		$reservationCompletionMailData = $this->getReservationCompletionMail($reservationIds);
		// キーを保持するためHash::mergeで結合する
		$replaceArr = Hash::merge($reservationDetailData, $reservationCompletionMailData);

		return $replaceArr;
	}

	// 置換処理
	public function mailReplace($targetString, $replacePattern) {
		$replaceKey = array_keys($replacePattern);
		$replaceValue = array_values($replacePattern);
		return str_replace($replaceKey, $replaceValue, $targetString);

	}

	public function getReservationDetail($reservationIds) {
		$options = array(
			'fields'=>array(
				'Reservation.*',
				'CommodityItem.*',
				'CarClass.*',
				'Commodity.*',
				'Client.*'
			),
			'joins'=>array(
				array(
					'table'=>'commodity_items',
					'alias'=>'CommodityItem',
					'type'=>'LEFT',
					'conditions'=>array(
						'CommodityItem.id = Reservation.commodity_item_id'
					)
				),
				array(
					'table'=>'car_classes',
					'alias'=>'CarClass',
					'type'=>'LEFT',
					'conditions'=>array(
						'CommodityItem.car_class_id = CarClass.id'
					)
				),
				array(
					'table'=>'commodities',
					'alias'=>'Commodity',
					'type'=>'LEFT',
					'conditions'=>array(
						'Commodity.id = CommodityItem.commodity_id'
					)
				),
				array(
					'table'=>'clients',
					'alias'=>'Client',
					'type'=>'LEFT',
					'conditions'=>array(
						'Reservation.client_id = Client.id'
					)
				)
			),
			'conditions'=>array(
				'Reservation.id'=>$reservationIds,
				'Reservation.delete_flg'=>0
			),
			'recursive'=>- 1
		);
		$result = $this->Reservation->find('all',$options);

		$replaceReservationData = array();
		if (!empty($result)) {
			foreach ($result as $key => $val) {
				$rentTimeStamp = strtotime($val['Reservation']['rent_datetime']);
				$rentDay = h(date('Y年m月d日', $rentTimeStamp));
				$rentWeek = $this->wday[h(date('w', $rentTimeStamp))];
				$rentTime = h(date('H時i分', $rentTimeStamp));
				$retrunTimeStamp = strtotime($val['Reservation']['return_datetime']);
				$returnDay = h(date('Y年m月d日', $retrunTimeStamp));
				$returnWeek = $this->wday[h(date('w', $retrunTimeStamp))];
				$returnTime = h(date('H時i分', $retrunTimeStamp));

				$replaceReservationData[$val['Reservation']['id']]['@@reservation_id@@'] = $val['Reservation']['reservation_key'];
				$replaceReservationData[$val['Reservation']['id']]['@@user_name@@'] = $val['Reservation']['last_name'] . ' ' . $val['Reservation']['first_name'];
				$replaceReservationData[$val['Reservation']['id']]['@@client_name@@'] = $val['Client']['name'];
				$replaceReservationData[$val['Reservation']['id']]['@@pickup_date@@'] = $rentDay . '（' . $rentWeek . '）' . $rentTime;
				$replaceReservationData[$val['Reservation']['id']]['@@return_date@@'] = $returnDay . '（' . $returnWeek . '）' . $returnTime;
				$replaceReservationData[$val['Reservation']['id']]['@@plan_name@@'] = $val['Commodity']['name'];
				$replaceReservationData[$val['Reservation']['id']]['@@car_type@@'] = $this->carTypeList[$val['CarClass']['car_type_id']];
				$replaceReservationData[$val['Reservation']['id']]['@@number_of_users@@'] = "大人 " . $val['Reservation']['adults_count'] . " 名\n"
														. "子供 " . $val['Reservation']['children_count'] . " 名\n"
														. "幼児 " . $val['Reservation']['infants_count'] . " 名\n";
			}
		}
		return $replaceReservationData;
	}

	public function getReservationCompletionMail($reservationIds) {
		$domain = IS_PRODUCTION ? 'skyticket.jp' : 'jp.skyticket.jp';
		$replaceReservationData = array();
		// 予約メール送信内容
		$reservationDataArr = $this->Reservation->getReservationDataForMailMulti($reservationIds);
		if (empty($reservationDataArr)) {
			return $replaceReservationData;
		}
		// 料金内訳
		$clientPrivilegeList = $this->Reservation->getClientPrivilegeListMulti($reservationIds);
		// 装備
		$reservationEquipmentList = $this->Reservation->getCommodityEquipmentListMulti($reservationIds);
		// キャンセルポリシー
		$reservationCancelPolicyArr = $this->CancelPolicy->getTextLinesMulti($reservationIds, false);

		foreach ($reservationDataArr as $reservationId => $reservation) {
			// 曜日
			$weekday = array('日', '月', '火', '水', '木', '金', '土');
			$rentWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['rent_datetime']))];
			$returnWeekDay = $weekday[date('w', strtotime($reservation['Reservation']['return_datetime']))];

			// 料金内訳
			if (!empty($clientPrivilegeList[$reservation['Client']['id']])) {
				$privilegeList = $clientPrivilegeList[$reservation['Client']['id']];
			} else {
				$privilegeList = array();
			}
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
			if (!empty($reservationEquipmentList[$reservation['Reservation']['id']])) {
				$equipmentList = array(0 => '免責補償') + $reservationEquipmentList[$reservation['Reservation']['id']] + array('transmission_flg' => $reservation['Commodity']['transmission_flg'] ? 'MT車' : 'AT車');
			} else {
				$equipmentList = array(0 => '免責補償') + array('transmission_flg' => $reservation['Commodity']['transmission_flg'] ? 'MT車' : 'AT車');
			}
			$equipmentText = implode('、', $equipmentList);


			$rentOfficeStartDay = '';
			if(!empty($reservation['RentOffice']['start_day'])){
				$rentOfficeStartDay = date('Y/m/d', strtotime($reservation['RentOffice']['start_day']));
			}
			$rentOfficeEndDay = '';
			if(!empty($reservation['RentOffice']['end_day'])){
				$rentOfficeEndDay = date('Y/m/d', strtotime($reservation['RentOffice']['end_day']));
			}

			$returnOfficeStartDay = '';
			if(!empty($reservation['ReturnOffice']['start_day'])){
				$returnOfficeStartDay = date('Y/m/d', strtotime($reservation['ReturnOffice']['start_day']));
			}
			$returnOfficeEndDay = '';
			if(!empty($reservation['ReturnOffice']['start_day'])){
				$returnOfficeEndDay = date('Y/m/d', strtotime($reservation['ReturnOffice']['end_day']));
			}

			$administrativeFee = isset($reservation['Reservation']['administrative_fee']) ? $reservation['Reservation']['administrative_fee'] : 0;
			$amountMinusFee = $reservation['Reservation']['amount'] - $administrativeFee;

			// 置換当てはめ
			$replaceReservation['@@pickup_name@@'] = $reservation['RentOffice']['name'];
			$replaceReservation['@@pickup_tel@@'] = $reservation['RentOffice']['tel'];
			$replaceReservation['@@pickup_business_time@@'] = date('G:i',strtotime($reservation['RentOffice']['office_hours_from'])).'~'.date('G:i',strtotime($reservation['RentOffice']['office_hours_to']));
			if (!empty($rentOfficeStartDay) && !empty($rentOfficeEndDay)) {
				$replaceReservation['@@pickup_business_time@@'] .= "\n　※ " . $rentOfficeStartDay . "~" . $rentOfficeEndDay . " は営業時間が通常と異なります。詳細は店舗へお問い合わせください。";
			}
			$replaceReservation['@@pickup_address@@'] = $reservation['RentOffice']['address'];
			$replaceReservation['@@pickup_access@@'] = $reservation['RentOffice']['access_dynamic'];
			if (!empty($reservation['RentOffice']['rent_meeting_info'])) {
				$replaceReservation['@@pickup_notice@@'] = mb_convert_kana($reservation['RentOffice']['rent_meeting_info'], 'KV');
			} else {
				$replaceReservation['@@pickup_notice@@'] = '';
			}
			$replaceReservation['@@pickup_information@@'] = $reservation['RentOffice']['notification'];
			$replaceReservation['@@return_name@@'] = $reservation['ReturnOffice']['name'];
			$replaceReservation['@@return_tel@@'] = $reservation['ReturnOffice']['tel'];
			$replaceReservation['@@return_business_time@@'] = date('G:i',strtotime($reservation['ReturnOffice']['office_hours_from'])).'~'.date('G:i',strtotime($reservation['ReturnOffice']['office_hours_to']));
			if (!empty($returnOfficeStartDay) && !empty($returnOfficeEndDay)) {
				$replaceReservation['@@return_business_time@@'] .= "\n　※ " . $returnOfficeStartDay . "~" . $returnOfficeEndDay . " は営業時間が通常と異なります。詳細は店舗へお問い合わせください。";
			}
			$replaceReservation['@@return_address@@'] = $reservation['ReturnOffice']['address'];
			$replaceReservation['@@return_access@@'] = $reservation['ReturnOffice']['access_dynamic'];
			if (!empty($reservation['ReturnOffice']['return_meeting_info'])) {
				$replaceReservation['@@return_notice@@'] = mb_convert_kana($reservation['ReturnOffice']['return_meeting_info'], 'KV');
			} else {
				$replaceReservation['@@return_notice@@'] = '';
			}
			$replaceReservation['@@number_of_cars@@'] = $reservation['Reservation']['cars_count']. '台';
			$replaceReservation['@@equipment@@'] = html_entity_decode($equipmentText);
			$replaceReservation['@@option@@'] = html_entity_decode($optionList);
			$replaceReservation['@@amount@@'] = number_format($reservation['Reservation']['amount']);
			$replaceReservation['@@payment@@'] = "支払い方法：WEB事前決済\nご入金状況：入金済み\nご入金金額：" . number_format($reservation['Reservation']['amount']) . "円";
			$replaceReservation['@@arrival_flight@@'] = $reservation['Reservation']['arrival_flight_number'];
			$replaceReservation['@@departure_flight@@'] = $reservation['Reservation']['departure_flight_number'];
			$replaceReservation['@@client_notice@@'] = $reservation['Client']['reservation_content'];
			if (!empty($reservationCancelPolicyArr[$reservationId])) {
				$replaceReservation['@@cancel_policy@@'] = "〈キャンセル料〉\n" . $reservationCancelPolicyArr[$reservationId] . "\n・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。\n・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。";
			} else {
				$replaceReservation['@@cancel_policy@@'] = "〈キャンセル料〉\n・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。\n・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。";
			}
			$replaceReservation['@@cancel_policy_add@@'] = $reservation['Client']['cancel_policy'];
			$replaceReservation['@@mypage_url@@'] = 'https://' . $domain . '/rentacar/mypages/login/?hash=' . $reservation['Reservation']['reservation_hash'];

			$replaceReservationData[$reservationId] = $replaceReservation;
		}

		return $replaceReservationData;
	}


}

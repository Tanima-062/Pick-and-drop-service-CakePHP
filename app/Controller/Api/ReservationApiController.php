<?php
App::uses('BaseRestApiController', 'Controller');

require_once("db_class.php");
require_once("user_class.php");
require_once("application_class.php");

/**
 * Class ReservationApiController
 *
 * @property CarClassReservation           CarClassReservation
 * @property Client                        Client
 * @property Commodity                     Commodity
 * @property CommodityItem                 CommodityItem
 * @property CommodityPrivilege            CommodityPrivilege
 * @property CommodityTerm                 CommodityTerm
 * @property DisclaimerCompensation        DisclaimerCompensation
 * @property DropOffAreaRate               DropOffAreaRate
 * @property OfficeStockGroup              OfficeStockGroup
 * @property PlansApiCalcValidation        PlansApiCalcValidation
 * @property Privilege                     Privilege
 * @property Reservation                   Reservation
 * @property ReservationApiValidation      ReservationApiValidation
 * @property ReservationChildSheet         ReservationChildSheet
 * @property ReservationDetail             ReservationDetail
 * @property ReservationMail               ReservationMail
 * @property ReservationPrivilege          ReservationPrivilege
 * @property ReservationUtilComponent      ReservationUtil
 * @property ReservationAPISelectComponent ReservationAPISelect
 */
class ReservationApiController extends BaseRestApiController {

	/**
	 * 使用コンポーネント一覧
	 * @var string[]
	 */
	public $components = array(
		'ReservationUtil',
		'ReservationAPISelect',
	);

	/**
	 * 使用Model一覧
	 * @var string[]
	 */
	public $uses = array(
		'CarClassReservation',
		'Client',
		'Commodity',
		'CommodityItem',
		'CommodityPrivilege',
		'CommodityTerm',
		'DisclaimerCompensation',
		'DropOffAreaRate',
		'OfficeStockGroup',
		'PlansApiCalcValidation',
		'Privilege',
		'Reservation',
		'ReservationApiValidation',
		'ReservationChildSheet',
		'ReservationDetail',
		'ReservationMail',
		'ReservationPrivilege',
	);

	/**
	 * 予約一覧
	 */
	public function index() {

		// レスポンスデータ生成
		$this->responseData = array('予約一覧');
	}

	/**
	 * 予約登録
	 * @throws Exception
	 */
	public function register() {

		try {
			// パラメータ存在チェック
			if (empty($this->request->data)) {
				throw new ApiException(ApiException::NO_PARAM);
			}

			// バリデーションチェック
			$this->ReservationApiValidation->set($this->request->data);
			if (!$this->ReservationApiValidation->validates()) {
				throw new ApiException($this->ReservationApiValidation->validationErrors);
			}

			// バリデーションをUser用に設定
			$this->ReservationApiValidation->setUserValidate();

			// バリデーションチェック
			$this->ReservationApiValidation->set($this->request->data['userInfo']);
			if (!$this->ReservationApiValidation->validates()) {
				throw new ApiException($this->ReservationApiValidation->validationErrors);
			}
		} catch (Exception $e) {
			$format = "RequestData : %s\n%s\n%s";
			$this->log(sprintf($format, json_encode($this->request->data), $e->getMessage(), $e->getTraceAsString()), 'error');
			throw $e;
		}

		// プラン毎のバリデーション
		foreach ($this->request->data['plans'] as $plan) {

			try {
				// バリデーションをPlan用に設定
				$this->ReservationApiValidation->setPlanValidate();

				// バリデーションチェック
				$this->ReservationApiValidation->set($plan);
				if (!$this->ReservationApiValidation->validates()) {
					throw new ApiException($this->ReservationApiValidation->validationErrors);
				}

				// 出発日時と返却日時
				$from = "{$plan['startDate']} {$plan['startTime']}:00";
				$to   = "{$plan['endDate']} {$plan['endTime']}:00";

				// 商品アイテムデータ取得
				if ($plan['salesType'] == Constant::SALES_TYPE_AGENT_ORGANIZED) {
					$price_data = $this->CommodityItem->getCommodityItemPriceDataAgentOrganized($plan['planId'], $plan['startDate']);
				} else {
					$price_data = $this->CommodityItem->getCommodityItemPriceData($plan['planId'], $plan['startDate']);
				}

				// 存在チェック
				if (empty($price_data)) {
					throw new ApiException(ApiException::NO_PLAN);
				}

				// 車種とクラス、参考モデルを取得
				$car_info_list = $this->CommodityItem->getCarInfo($plan['planId']);
				$car_info_list = $car_info_list[$plan['planId']];

				// 乗り捨て料金が設定チェック
				if ($plan['fromShopId'] !== $plan['toShopId']) {
					// 乗り捨て料金取得
					$drop_off_area_price = $this->DropOffAreaRate->getDropOffAreaPrice($plan['fromShopId'], $plan['toShopId'], $price_data['CarClass']['id']);

					// 存在チェック
					if (!isset($drop_off_area_price)) {
						throw new ApiException(ApiException::DO_NOT_DROPOFF);
					}
				}

				// 乗車人数
				$people_count = $this->ReservationUtil->calcPersonCount(
					$this->request->data['userInfo']['adultCount'],
					$this->request->data['userInfo']['childCount'],
					$this->request->data['userInfo']['infantCount']
				);

				// 定員チェック
				$capacity = 999;
				foreach ($car_info_list['CarModel'] as $car_model) {
					if ($car_model['capacity'] < $capacity) {
						$capacity = $car_model['capacity'];
					}
				}

				// 定員オーバーチェック
				if ($people_count > $capacity) {
					throw new ApiException(ApiException::CAPACITY_OVER);
				}

				// シートチェック ※幼児がいる場合は必須
				$sheets = array();
				if (isset($plan['sheets']) && count($plan['sheets']) > 0) {
					$sheets = Hash::combine($plan['sheets'], '{n}.optionId', '{n}.count');
					if (!$this->Privilege->maxLimitCheck($sheets)) {
						throw new ApiException(ApiException::SEAT_MAX_OVER);
					}
				} elseif ($this->request->data['userInfo']['infantCount'] > 0) {
					throw new ApiException(ApiException::SEAT_REQUIRED);
				}

				// 各オプションの最大数チェック
				$options = array();
				if (isset($plan['options']) && count($plan['options']) > 0) {
					$options = Hash::combine($plan['options'], '{n}.optionId', '{n}.count');
					if (!$this->Privilege->maxLimitCheck($options)) {
						throw new ApiException(ApiException::OPTION_MAX_OVER);
					}
				}

				// 商品受付締切時間チェック
				$deadline_time = $this->CommodityTerm->acceptanceDeadlineTime($price_data['CommodityItem']['commodity_id'], $from);

				// 存在チェック
				if ($deadline_time !== false) {
					throw new ApiException($deadline_time);
				}

				// 暦日制か時間制か
				$day_time_flg = $this->Commodity->read('day_time_flg', $price_data['CommodityItem']['commodity_id']);
				$day_time_flg = $day_time_flg['Commodity']['day_time_flg'];

				// 料金計算チェック
				if ($plan['salesType'] == Constant::SALES_TYPE_AGENT_ORGANIZED) {
					$price_check = $this->ReservationUtil->priceCalculationAgentOrganized($plan['planId'], $from, $to, $plan['fromShopId'], $plan['toShopId'], $plan['salesPrice']);
				} else {
					$price_check = $this->ReservationUtil->priceCalculation($plan['planId'], $from, $to, $day_time_flg, $plan['fromShopId'], $plan['toShopId'], $plan['basePrice'], array(), $options);
				}
				// 存在チェック
				if ($price_check === false) {
					throw new ApiException(ApiException::PRICE_DIFFERENCE);
				}

				$remainingStock = $this->CommodityItem->getOfficeStocks($price_data['CarClass']['id'], array(
					'from_office' => $plan['fromShopId'],
					'from'        => $from,
					'to'          => $to,
					'cars_count'  => 1,
				));

				// 在庫チェック
				if (empty($remainingStock) || empty($remainingStock[$plan['fromShopId']])) {
					throw new ApiException(ApiException::STOCK_OUT);
				}
			} catch (Exception $e) {
				$format = "PlanData : %s\n%s\n%s";
				$this->log(sprintf($format, json_encode($plan), $e->getMessage(), $e->getTraceAsString()), 'error');
				throw $e;
			}
		}

		$apiReserved = array();

		try {
			// トランザクション
			$this->Reservation->begin();
			$db = GetDBInstance(DB_MAIN_MASTER);
			$db->beginTransaction();

			$reservations = array();

			// ユーザーID
			$user_id = !empty($this->request->data['userId']) ? $this->request->data['userId'] : null;

			// 申込番号
			$cm_app_id = !empty($this->request->data['cmApplicationId']) ? $this->request->data['cmApplicationId'] : null;

			//広告コード取得
			$advertisingCd = !empty($this->request->data['advertisingCd']) ? $this->request->data['advertisingCd'] : null;

			// ユーザーエージェント
			$agent = !empty($this->request->data['userAgent']) ? $this->request->data['userAgent'] : env('HTTP_USER_AGENT');

			// IPアドレス
			$ip = !empty($this->request->data['ipAddress']) ? $this->request->data['ipAddress'] : $this->request->clientIP();

			// cm_th_application用に上書き
			$_SERVER['HTTP_USER_AGENT'] = $agent;
			$_SERVER["REMOTE_ADDR"] = $ip;

			foreach ($this->request->data['plans'] as $plan) {

				while (1) {
					// ユニークなハッシュキーの生成
					$hashKey = md5(uniqid(rand(), 1));

					// 重複チェック
					if (!$this->Reservation->uniqueCheckHashKey($hashKey)) {
						break;
					}
				}

				// 出発日時と返却日時
				$from = "{$plan['startDate']} {$plan['startTime']}:00";
				$to   = "{$plan['endDate']} {$plan['endTime']}:00";

				// レンタル期間から暦日制と24時間制の日数を算出
				list($day_night, $period, $period24) = $this->ReservationUtil->getPeriodArray($from, $to);

				// 商品アイテムデータ取得
				if ($plan['salesType'] == Constant::SALES_TYPE_AGENT_ORGANIZED) {
					$price_data = $this->CommodityItem->getCommodityItemPriceDataAgentOrganized($plan['planId'], $plan['startDate']);
				} else {
					$price_data = $this->CommodityItem->getCommodityItemPriceData($plan['planId'], $plan['startDate']);
				}

				$client_id    = $price_data['CommodityItem']['client_id'];
				$commodity_id = $price_data['CommodityItem']['commodity_id'];
				$car_class_id = $price_data['CarClass']['id'];
				$sheets       = !empty($plan['sheets']) ? Hash::combine($plan['sheets'], '{n}.optionId', '{n}.count') : array();
				$options      = !empty($plan['options']) ? Hash::combine($plan['options'], '{n}.optionId', '{n}.count') : array();

				// 予約番号の取得
				$client_data = $this->Client->getClientById($client_id);

				// 予約タグ存在チェック
				if (empty($client_data['Client']['reserve_tag'])) {
					throw new ApiException(ApiException::NO_RESERVE_TAG, 500);
				}

				$max_reservation_key    = $this->Reservation->getMaxReservationKey($client_data['Client']['reserve_tag']);
				$result_reservation_key = $this->Reservation->uniqueCheckReservationKey($max_reservation_key);

				// 予約番号重複チェック
				if (!empty($result_reservation_key)) {
					throw new ApiException(ApiException::RESERVE_NO_DUPLICATE, 409);
				}

				// 現在時刻
				$current_time = date('Y-m-d H:i:s');

				// モデルリセット
				$this->Reservation->create();
				$this->ReservationMail->create();
				$this->ReservationChildSheet->create();
				$this->ReservationPrivilege->create();
				$this->ReservationDetail->create();
				$this->CarClassReservation->create();

				// 予約データ
				$reservation_params = array(
					'client_id'               => $client_id,
					'user_session_id'         => $ip,
					'user_agent'              => $agent,
					'reservation_datetime'    => $current_time,
					'reservation_key'         => $max_reservation_key,
					'reservation_hash'        => $hashKey,
					'reservation_status_id'   => Constant::STATUS_RESERVATION,
					'commodity_item_id'       => $plan['planId'],
					'rent_datetime'           => $from,
					'return_datetime'         => $to,
					'rent_office_id'          => $plan['fromShopId'],
					'return_office_id'        => $plan['toShopId'],
					'last_name'               => $this->request->data['userInfo']['lastName'],
					'first_name'              => $this->request->data['userInfo']['firstName'],
					'tel'                     => $this->request->data['userInfo']['tel'],
					'email'                   => $this->request->data['userInfo']['email'],
					'arrival_flight_number'   => $plan['flightNumber']['arrival'],
					'departure_flight_number' => $plan['flightNumber']['departure'],
					'adults_count'            => $this->request->data['userInfo']['adultCount'],
					'children_count'          => $this->request->data['userInfo']['childCount'],
					'infants_count'           => $this->request->data['userInfo']['infantCount'],
					'cars_count'              => 1,
					'amount'                  => $plan['basePrice'],
					'is_send_mail'            => 0,
					// TODO 備考の入力有無で0:未返信 3:設定なし
					'mail_status'    => 3,
					'advertising_cd' => $advertisingCd,
					'api_status_id'  => $this->ReservationAPISelect->apiRequired($client_id) ? Constant::API_STATUS_INCLUDED : Constant::API_STATUS_EXCLUDED,
					'rennavi_status' => $this->ReservationAPISelect->isRennaviApiTarget($client_id) ? Constant::RENNAVI_STATUS_RESERVE : Constant::RENNAVI_STATUS_EXCLUDED,
					'sales_price'    => $plan['salesPrice'],
				);

				// TODO 決済手数料(仮)
				if (isset($plan['fee'])) {
					$reservation_params['amount'] += $plan['fee'];
					$reservation_params['administrative_fee'] = $plan['fee'];
					$reservation_params['payment_status'] = 'PAYED'; // TODO 即時以外の考慮も必要
				}

				// 予約登録
				$reservation_result = $this->Reservation->save($reservation_params);

				// エラーチェック
				if (empty($reservation_result)) {
					// 予約バリデーションエラー
					if (!empty($this->Reservation->validationErrors)) {
						throw new ApiException($this->Reservation->validationErrors);
					}

					// 予約登録失敗
					throw new ApiException(ApiException::RESERVE_INSERT_ERROR, 500);
				}

				// 予約ID
				$reservation_id = $reservation_result['Reservation']['id'];

				// 備考の入力がある場合
				if (!empty($plan['remarks'])) {
					// 備考登録
					$reservation_mail_result = $this->ReservationMail->save(array(
						'reservation_id' => $reservation_id,
						'mail_datetime'  => $current_time,
						'staff_id'       => 0,
						'contents'       => $plan['remarks'],
						'read_flg'       => 0,
					));

					// 備考登録エラーチェック
					if (empty($reservation_mail_result)) {
						throw new ApiException(ApiException::REMARKS_INSERT_ERROR, 500);
					}
				}

				// オプション料金（チャイルドシート・特典）
				// シートの最大数チェック
				// オプションデータ
				$reservation_privilege_data = $this->CommodityPrivilege->getPrivilegeData(array(
					'commodityId' => $commodity_id,
					'period'      => $period,
					'period24'    => $period24,
					'sheet'       => $sheets,
					'privilege'   => $options,
				));

				// 予約チャイルドシートデータ
				$reservation_child_sheet_params = array();
				if (!empty($sheets)) {
					foreach ($sheets as $privilege_id => $count) {
						$reservation_child_sheet_params[] = array(
							'reservation_id' => $reservation_id,
							'child_sheet_id' => $privilege_id,
							'count'          => $count,
							'price'          => $reservation_privilege_data[$privilege_id]['amount'],
						);
					}

					// シート登録
					$reservation_child_sheet_result = $this->ReservationChildSheet->saveMany($reservation_child_sheet_params);

					// 登録エラーチェック
					if (empty($reservation_child_sheet_result)) {
						throw new ApiException(ApiException::SEAT_INSERT_ERROR, 500);
					}
				}

				// 予約特典データ
				$reservation_privilege_params = array();
				if (!empty($options)) {
					foreach ($options as $privilege_id => $count) {
						$reservation_privilege_params[] = array(
							'reservation_id' => $reservation_id,
							'privilege_id'   => $privilege_id,
							'count'          => $count,
							'price'          => $reservation_privilege_data[$privilege_id]['amount'],
						);
					}

					// オプション登録
					$reservation_privilege_result = $this->ReservationPrivilege->saveMany($reservation_privilege_params);

					// 登録エラーチェック
					if (empty($reservation_privilege_result)) {
						throw new ApiException(ApiException::OPTION_INSERT_ERROR, 500);
					}
				}

				// 予約明細データ
				$date_string = "{$from}~{$to}";

				// 乗り捨て料金・深夜手数料
				$drop_off_late_night = $this->DropOffAreaRate->dropOffLateNight($plan['fromShopId'], $plan['toShopId'], $car_class_id, $plan['startTime'], $plan['endTime']);

				$reservation_detail_params = array();

				// 乗り捨て料金
				if (!empty($drop_off_late_night['dropPrice'])) {
					$reservation_detail_params[] = array(
						'reservation_id'     => $reservation_id,
						'detail_type_id'     => Constant::DETAIL_TYPE_DROPOFFPRICE,
						'detail_date_string' => $date_string,
						'count'              => 1,
						'amount'             => $drop_off_late_night['dropPrice'],
					);
				}

				// 深夜手数料
				if (!empty($drop_off_late_night['nightFee'])) {
					$reservation_detail_params[] = array(
						'reservation_id'     => $reservation_id,
						'detail_type_id'     => Constant::DETAIL_TYPE_NIGHTFEE,
						'detail_date_string' => $date_string,
						'count'              => 1,
						'amount'             => $drop_off_late_night['nightFee'],
					);
				}

				$reservation_sheet_amount     = 0;
				$reservation_sheet_count      = 0;
				$reservation_privilege_amount = 0;
				$reservation_privilege_count  = 0;
				foreach ($reservation_privilege_data as $key => $reservation_privilege) {
					if ($reservation_privilege['option_flg'] == 1) {
						// チャイルドシート
						$reservation_sheet_amount += $reservation_privilege['amount'];
						$reservation_sheet_count  += $reservation_privilege['count'];
					} else {
						// オプション（特典）
						$reservation_privilege_amount += $reservation_privilege['amount'];
						$reservation_privilege_count  += $reservation_privilege['count'];
					}
				}

				// チャイルドシート
				if (!empty($reservation_sheet_count)) {
					$reservation_detail_params[] = array(
						'reservation_id'     => $reservation_id,
						'detail_type_id'     => Constant::DETAIL_TYPE_CHILDSHEET,
						'detail_date_string' => $date_string,
						'count'              => $reservation_sheet_count,
						'amount'             => $reservation_sheet_amount
					);
				}

				// オプション（特典）
				if (!empty($reservation_privilege_count)) {
					$reservation_detail_params[] = array(
						'reservation_id'     => $reservation_id,
						'detail_type_id'     => Constant::DETAIL_TYPE_OPTIONPRICE,
						'detail_date_string' => $date_string,
						'count'              => $reservation_privilege_count,
						'amount'             => $reservation_privilege_amount,
					);
				}


				// 免責補償料金取得
				$date_from = date('Y-m-d', strtotime($reservation_params['rent_datetime']));
				$date_to   = date('Y-m-d', strtotime($reservation_params['return_datetime']));

				// レンタル期間から暦日制と24時間制の日数を算出
				list($day_night, $period, $period24) = $this->ReservationUtil->getPeriodArray($reservation_params['rent_datetime'], $reservation_params['return_datetime']);

				// 免責補償料金取得
				$disclaimer_compensation_price = $this->DisclaimerCompensation->getFee($car_class_id, $date_from, $date_to, $period, $period24);

				// 免責補償料金
				$reservation_detail_params[] = array(
					'reservation_id'     => $reservation_id,
					'detail_type_id'     => Constant::DETAIL_TYPE_DISCLAIMER,
					'detail_date_string' => $date_string,
					'count'              => 1,
					'amount'             => $disclaimer_compensation_price,
				);

				// 基本料金からの免責補償料金の減算
				$basicPrice = $plan['basePrice'] - $disclaimer_compensation_price;

				// 基本料金
				$reservation_detail_params[] = array(
					'reservation_id'     => $reservation_id,
					'detail_type_id'     => Constant::DETAIL_TYPE_BASICPRICE,
					'detail_date_string' => $date_string,
					'count'              => 1,
					'amount'             => $basicPrice,
				);

				// 予約明細登録
				$reservationDetailResult = $this->ReservationDetail->saveMany($reservation_detail_params);

				// 登録エラーチェック
				if (empty($reservationDetailResult)) {
					throw new ApiException(ApiException::DETAIL_INSERT_ERROR, 500);
				}

				// 在庫チェック
				$remaining_stock = $this->CommodityItem->getOfficeStocks($price_data['CarClass']['id'], array(
					'from_office' => $plan['fromShopId'],
					'from'        => $from,
					'to'          => $to,
					'cars_count'  => 1,
				));

				// 在庫なし
				if (empty($remaining_stock) || empty($remaining_stock[$plan['fromShopId']])) {
					throw new ApiException(ApiException::STOCK_OUT);
				}

				// 営業所在庫管理地域取得
				$office_stock_group = $this->OfficeStockGroup->getOfficeStockGroupId($plan['fromShopId']);

				$step = 60 * 60 * 24;
				$array_time = range(strtotime($date_from), strtotime($date_to), $step);

				// クラス共有在庫
				$car_class_reservation_params = array();
				foreach ($array_time as $time) {
					$car_class_reservation_params[] = array(
						'client_id'         => $client_id,
						'stock_group_id'    => $office_stock_group['OfficeStockGroup']['stock_group_id'],
						'car_class_id'      => $car_class_id,
						'stock_date'        => date('Y-m-d', $time),
						'reservation_id'    => $reservation_id,
						'reservation_count' => $reservation_result['Reservation']['cars_count'],
					);
				}

				// 在庫引当登録
				$car_class_reservation_result = $this->CarClassReservation->saveMany($car_class_reservation_params);

				// 登録エラーチェック
				if (empty($car_class_reservation_result)) {
					throw new ApiException(ApiException::RESERVE_INSERT_ERROR, 500);
				}

				// ユーザーIDが無ければユーザー登録
				if (empty($user_id)) {
					$cm_tm_user_params = array(
						'family_name'             => $this->request->data['userInfo']['lastName'],
						'first_name'              => $this->request->data['userInfo']['firstName'],
						'tel'                     => $this->request->data['userInfo']['tel'],
						'email'                   => $this->request->data['userInfo']['email'],
						'mailmagazine_recept_flg' => 0,
						'password'                => '',
						'member_status'           => 0,
					);

					// ユーザークラス
					$User = new User($db);

					// ユーザー登録
					$user_id = $User->insertUser($cm_tm_user_params, $db);

					// 登録エラーチェック
					if (!$user_id) {
						throw new ApiException(ApiException::USER_INSERT_ERROR, 500);
					}
				}

				// 会社別APIコンポーネント名取得
				$component_name = $this->ReservationAPISelect->getApiComponentName($client_id);

				// 予約連携API
				if (!empty($component_name)) {
					// 会社別コンポーネントロード
					$ReservationApi = $this->Components->load($component_name);

					$child_sheet_data = isset($reservation_child_sheet_params) ? $reservation_child_sheet_params : array();
					$privilege_data   = isset($reservation_privilege_params) ? $reservation_privilege_params : array();

					// 送信データセット
					$ReservationApi->setFrontReservationData($reservation_params, $reservation_detail_params, $child_sheet_data, $privilege_data);

					// 送信
					list($success, $result) = $ReservationApi->sendReservationData();

					$index = count($apiReserved);
					$apiReserved[$index] = array(
						'client_id' => $client_id,
						'reservation_id' => $reservation_id,
						'reservation_key' => $max_reservation_key,
						'cancel_api_required' => true
					);

					if ($success) {
						// API成功
						if ($result['status']) {
							if (!empty($result['reserveno'])) {
								// 管理番号更新
								$update_result = $this->Reservation->save(array(
									'id'             => $reservation_id,
									'control_number' => $result['reserveno'],
								));

								// 登録エラーチェック
								if (!is_array($update_result)) {
									throw new ApiException(sprintf("管理番号の登録に失敗しました。(%s)", $result['reserveno']), 500);
								}
							}
						} else {
							// 連携先予約NGの場合、キャンセル連携しないのでメール通知必要なし
							$apiReserved[$index]['cancel_api_required'] = false;
							throw new ApiException(sprintf("予約連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : '')), 500);
						}
					} else {
						// API失敗の場合、キャンセル連携必要
						throw new ApiException('予約連携中に何らかのエラーが発生しました。', 500);
					}
				}

				$reservations[] = array(
					'planId'         => $plan['planId'],
					'reservationKey' => $max_reservation_key,
					'reservationId'  => $reservation_id,
				);
			}

			// 全てのプラン予約後の処理
			if (empty($cm_app_id)) {
				// cm_th_application, cm_th_application_detail 新規登録
				$data = array();
				foreach ($reservations as $reservation) {
					$data[] = array(
						'application_id' => $reservation['reservationId'],
						'service_cd'     => "rc",
					);
				}

				// 申込クラス
				$application = new Application($db);

				// 申込登録
				if (!$application->insertApplication(array('user_id' => $user_id), $data, $db)) {
					throw new ApiException(ApiException::APPLICATION_INSERT_ERROR, 500);
				}
			} else {
				foreach ($reservations as $reservation) {
					// 申込詳細登録
					if(!$this->ReservationUtil->insertApplicationDetail($db, $reservation['reservationId'], $cm_app_id)) {
						throw new ApiException(ApiException::APPLICATION_DETAIL_INSERT_ERROR, 500);
					}
				}
			}

			// Commit
			$db->commit();
			$this->Reservation->commit();

		} catch (Exception $e) {
			// 予約失敗時はログを出力する
			$format          = "ReservationId : %s (%s)\n%s\n%s";
			$reservation_id  = isset($reservation_id) ? $reservation_id : '';
			$reservation_key = isset($max_reservation_key) ? $max_reservation_key : '';
			$this->log(sprintf($format, $reservation_id, $reservation_key, $e->getMessage(), $e->getTraceAsString()), 'error');

			foreach ($apiReserved as $apiInfo) {
				if ($apiInfo['cancel_api_required']) {
					// 予約連携APIでエラー or 予約連携API成功後にエラーの場合
					$component_name = $this->ReservationAPISelect->getApiComponentName($apiInfo['client_id']);
					$ReservationApi = $this->Components->load($component_name);

					// キャンセル連携
					$cancelSuccess = false;
					$ReservationApi->setMypageReservationData($apiInfo['reservation_id'], Constant::API_STATUS_CANCEL);

					// エラーメッセージ
					$error_string = '';

					try {
						list($success, $result) = $ReservationApi->sendReservationData();

						if ($success) {
							if ($result['status']) {
								$cancelSuccess = true;
							} else {
								$error_string = sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
							}
						} else {
							$error_string = 'キャンセル連携中に何らかのエラーが発生しました。';
						}
					} catch (Exception $ex) {
						$error_string = sprintf("%s\n%s", $ex->getMessage(), $ex->getTraceAsString());
					}

					// キャンセル連携もエラーの場合、メールで通知
					if (!$cancelSuccess) {
						$format              = "ReservationId : %s (%s)\n%s";
						$reservation_id      = $apiInfo['reservation_id'];
						$max_reservation_key = $apiInfo['reservation_key'];
						$reserve_no          = isset($result['reserveno']) ? $result['reserveno'] : '';

						$this->log(sprintf($format, $reservation_id, $max_reservation_key, $error_string), 'error');
						$ReservationApi->sendAlertFromFront($reserve_no, $this->domain);
					}
				}
			}

			// Rollback
			$db->rollback();
			$this->Reservation->rollback();
			throw $e;
		}

		// 予約メール送信処理
		foreach ($reservations as $reservation) {
			$reservation = $this->Reservation->getReservationData($reservation['reservationId']);
			if (!in_array($reservation['Reservation']['client_id'], Constant::notSendmailClientIdsWhenAgentOrganized())) {
				$this->ReservationUtil->sendReservedMail($reservation, false, false);
			}
		}

		// ステータスコード
		$this->response->statusCode(201);

		foreach ($reservations as $reservation) {
			$this->responseData[] = array(
				'planId'         => $reservation['planId'],
				'reservationKey' => $reservation['reservationKey'],
			);
		}
	}

	/**
	 * 入金状態更新
	 * @param int $reservation_key 予約キー
	 * @throws Exception
	 */
	public function payment($reservation_key) {

		// トランザクション
		$this->Reservation->begin();
		$db = GetDBInstance(DB_MAIN_MASTER);
		$db->beginTransaction();

		$reservation = $this->ReservationUtil->findReservation($db, $reservation_key);

		// エラーおよび空チェック
		if (empty($reservation)) {
			throw new ApiException(ApiException::RESERVE_FIND_ERROR);
		}

		$db->commit();

		$reservation_id = $reservation[0]['id'];
		$payment_status = $reservation[0]['payment_status'];

		if($payment_status !== Constant::PAYMENT_STATUS_PAYED){
			$reservation_params = array(
				'payment_status' => Constant::PAYMENT_STATUS_PAYED
			);
	
			$this->Reservation->id = $reservation_id;
	
			// 予約登録
			$this->Reservation->save($reservation_params);
	
			$this->Reservation->commit();

		}else{
			throw new ApiException(ApiException::RESERVE_UPDATE_ERROR, 500);
		}

		// ステータスコード
		$this->response->statusCode(204);
		$this->responseData = [];
	}

	/**
	 * 予約キャンセル
	 * @param int $reservation_key 予約キー
	 * @throws Exception
	 */
	public function cancel($reservation_key) {

		// トランザクション
		$this->Reservation->begin();
		$db = GetDBInstance(DB_MAIN_MASTER);
		$db->beginTransaction();

		$reservation = $this->ReservationUtil->findReservation($db, $reservation_key);

		// エラーおよび空チェック
		if (empty($reservation)) {
			throw new ApiException(ApiException::RESERVE_FIND_ERROR);
		}

		$db->commit();

		$reservation_id = $reservation[0]['id'];
		$client_id = $reservation[0]['client_id'];
		$cancel_flg = $reservation[0]['cancel_flg'];
		$now = date("Y-m-d H:i:s");

		if ($cancel_flg !== "1") {
			// 会社別APIコンポーネント名取得
			$component_name = $this->ReservationAPISelect->getApiComponentName($client_id);

			// 予約連携API
			if (!empty($component_name)) {
				// 会社別コンポーネントロード
				$ReservationApi = $this->Components->load($component_name);

				// 送信データセット
				$ReservationApi->setMypageReservationData($reservation_id, Constant::API_STATUS_CANCEL);

				// 送信
				list($success, $result) = $ReservationApi->sendReservationData();

				if ($success) {
					if (!$result['status']) {
						throw new ApiException(sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : '')), 500);
					}
				} else {
					throw new ApiException('キャンセル連携中に何らかのエラーが発生しました。', 500);
				}
			}

			$cancel_params = array(
				'cancel_flg' => 1,
				'reservation_status_id' => 3,
				'cancel_datetime' => $now
			);

			$this->Reservation->id = $reservation_id;

			// 予約キャンセル
			$this->Reservation->save($cancel_params);

			$this->Reservation->commit();

			// 予約キャンセル通知
			if (!in_array($client_id, Constant::notSendmailClientIdsWhenAgentOrganized())) {
				$this->ReservationUtil->sendNotificationMail($reservation_id, 'cancel');
			}
		} else {
			throw new ApiException(ApiException::RESERVE_UPDATE_ERROR, 500);
		}

		// ステータスコード
		$this->response->statusCode(204);
		$this->responseData = array($reservation_key);
	}

}

<?php
App::uses('BaseRestApiController', 'Controller');

require_once("log_class.php");
require_once("encrypt_class.php");
require_once("db_class.php");
require_once("user_class.php");
require_once("application_class.php");
require_once("area_class.php");
require_once("classification_class.php");

class ReservationsApiController extends BaseRestApiController {
	public $components = array('Validation', 'ReservationUtil', 'ReservationAPISelect');
	public $uses = array(
		'ReservationsApiValidation',
		'PlansApiCalcValidation',
		'Commodity',
		'CommodityItem',
		'CommodityTerm',
		'CommodityPrivilege',
		'Client',
		'Office',
		'DropOffAreaRate',
		'DisclaimerCompensation',
		'Privilege',
		'Reservation',
		'ReservationMail',
		'ReservationChildSheet',
		'ReservationPrivilege',
		'ReservationDetail',
		'CarClassReservation',
		'OfficeStockGroup',
		'ClientEmail',
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->ApiCommon->setCorsHeader();
		// メール送信で使用するため上書き
		$this->domain = $_SERVER['HTTP_HOST'];
	}

	// 予約処理
	public function add() {
		if (empty($this->request->data)) {
			// パラメータなし
			throw new ApiException(ApiException::NO_PARAM);
		}

		$params = $this->request->data;

		// 不要な文字削除
		if (isset($params['userInfo']['tel'])) {
			$params['userInfo']['tel'] = $this->ReservationUtil->telNormalization($params['userInfo']['tel']);
		}
		if (isset($params['userInfo']['email'])) {
			$params['userInfo']['email'] = $this->ReservationUtil->mailNormalization($params['userInfo']['email']);
		}
		foreach ((array)$params['plans'] as $k => $plan) {
			$arrival = isset($plan['flightNumber']['arrival']) ? $this->Validation->removeControlChars($plan['flightNumber']['arrival']) : '';
			$params['plans'][$k]['flightNumber']['arrival'] = $arrival;

			$departure = isset($plan['flightNumber']['departure']) ? $this->Validation->removeControlChars($plan['flightNumber']['departure']) : '';
			$params['plans'][$k]['flightNumber']['departure'] = $departure;
		}

		// バリデーション
		$this->ReservationsApiValidation->set($params);
		if (!$this->ReservationsApiValidation->validates()) {
			throw new ApiException($this->ReservationsApiValidation->validationErrors);
		}

		$userInfo = $params['userInfo'];

		// planのルールが足りないので追加
		$this->PlansApiCalcValidation->addReservationRule();

		// プラン毎のバリデーション
		foreach ($params['plans'] as $plan) {
			$this->PlansApiCalcValidation->set($plan);
			if (!$this->PlansApiCalcValidation->validates()) {
				throw new ApiException($this->PlansApiCalcValidation->validationErrors);
			}

			$from = $plan['startDate'] . ' ' . $plan['startTime'] . ':00';
			$to = $plan['endDate'] . ' ' . $plan['endTime'] . ':00';

			// 商品アイテムデータ取得
			$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($plan['planId'], $plan['startDate']);

			if (empty($commodityItemPriceData)) {
				// プランなし
				throw new ApiException(ApiException::NO_PLAN, 404);
			}

			$carInfoList = $this->CommodityItem->getCarInfo($plan['planId']);
			$carInfoList = $carInfoList[$plan['planId']];

			// 乗り捨て料金が設定チェック
			if ($plan['fromShopId'] != $plan['toShopId']) {
				$dropOffAreaRateData = $this->DropOffAreaRate->getDropOffAreaPrice(
					$plan['fromShopId'],
					$plan['toShopId'],
					$commodityItemPriceData['CarClass']['id']
				);
				if (!isset($dropOffAreaRateData)) {
					// 乗り捨て不可
					throw new ApiException(ApiException::DO_NOT_DROPOFF);
				}
			}

			// 乗車人数
			$peopleCnt = $this->ReservationUtil->calcPersonCount($userInfo['adultCount'], $userInfo['childCount'], $userInfo['infantCount']);

			// 定員チェック
			$capacity = 999;
			foreach ($carInfoList['CarModel'] as $carModel) {
				if ($carModel['capacity'] < $capacity) {
					$capacity = $carModel['capacity'];
				}
			}
			if ($peopleCnt > $capacity) {
				// 定員オーバー
				throw new ApiException(ApiException::CAPACITY_OVER);
			}

			// シートチェック
			$sheets = array();
			if (!empty($plan['sheets'])) {
				// シートの最大数チェック
				$sheets = Hash::combine($plan['sheets'], '{n}.optionId', '{n}.count');
				$maxLimitCheck = $this->Privilege->maxLimitCheck($sheets);
				if (!$maxLimitCheck) {
					// シート最大数オーバー
					throw new ApiException(ApiException::SEAT_MAX_OVER);
				}
			} else if (!empty($userInfo['infantCount'])) {
					// シート必須
				throw new ApiException(ApiException::SEAT_REQUIRED);
			}

			// 各オプションの最大数チェック
			$options = array();
			if (!empty($plan['options'])) {
				$options = Hash::combine($plan['options'], '{n}.optionId', '{n}.count');
				$maxLimitCheck = $this->Privilege->maxLimitCheck($options);
				if (!$maxLimitCheck) {
					// オプション最大数オーバー
					throw new ApiException(ApiException::OPTION_MAX_OVER);
				}
			}

			// 商品受付締切時間チェック
			$acceptanceDeadlineTime = $this->CommodityTerm->acceptanceDeadlineTime($plan['planId'], $from);
			if (!empty($acceptanceDeadlineTime)) {
				// 締切時間超過
				throw new ApiException($acceptanceDeadlineTime);
			}

			$dayTimeFlg = $this->Commodity->read('day_time_flg', $commodityItemPriceData['CommodityItem']['commodity_id']);
			$dayTimeFlg = $dayTimeFlg['Commodity']['day_time_flg'];

			// 料金計算チェック
			$totalPrice = $this->ReservationUtil->priceCalculation(
				$plan['planId'],
				$from,
				$to,
				$dayTimeFlg,
				$plan['fromShopId'],
				$plan['toShopId'],
				$plan['totalPrice'],
				$sheets,
				$options
			);

			if (empty($totalPrice)) {
				// 料金相違
				throw new ApiException(ApiException::PRICE_DIFFERENCE);
			}

			// 在庫チェック
			$stockCheckParams = array(
				'from_office' => $plan['fromShopId'],
				'from' => $from,
				'to' => $to,
				'cars_count' => 1,
			);
			$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $stockCheckParams);
			if (empty($remainingStock) || empty($remainingStock[$plan['fromShopId']])) {
				// 在庫なし
				throw new ApiException(ApiException::STOCK_OUT, 404);
			}
		}

		// トランザクションは1件ずつと全部どちらがいいのか・・・
		try {
			// トランザクション
			$this->Reservation->begin();
			// skyticketデータベースに対するトランザクション
			$db = GetDBInstance(DB_MAIN_MASTER);   // マスター
			$db->beginTransaction();

			$reservations = array();
			$userId = !empty($params['userId']) ? $params['userId'] : null;
			$cmApplicationId = !empty($params['cmApplicationId']) ? $params['cmApplicationId'] : null;
			//広告コード取得
			$advertisingCd = !empty($params['advertisingCd']) ? $params['advertisingCd'] : null;
			$agent = !empty($params['userAgent']) ? $params['userAgent'] : env('HTTP_USER_AGENT');
			$ip = !empty($params['ipAddress']) ? $params['ipAddress'] : $this->request->clientIP();
			// cm_th_application用に上書き
			$_SERVER['HTTP_USER_AGENT'] = $agent;
			$_SERVER["REMOTE_ADDR"] = $ip;

			$fromStep1 = true; // TODO fromstep1にあたる何かがいる

			foreach ($params['plans'] as $plan) {
				// ユニークなハッシュキーの生成
				while (1) {
					$hashKey = md5(uniqid(rand(), 1));
					if (!$this->Reservation->uniqueCheckHashKey($hashKey)) {
						break;
					}
				}

				$from = $plan['startDate'] . ' ' . $plan['startTime'] . ':00';
				$to = $plan['endDate'] . ' ' . $plan['endTime'] . ':00';
				list($dayNight, $period, $period24) = $this->ReservationUtil->getPeriodArray($from, $to);

				// 商品アイテムデータ取得
				$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($plan['planId'], $plan['startDate']);
				$clientId = $commodityItemPriceData['CommodityItem']['client_id'];
				$commodityId = $commodityItemPriceData['CommodityItem']['commodity_id'];
				$carClassId = $commodityItemPriceData['CarClass']['id'];
				$sheets = !empty($plan['sheets']) ? Hash::combine($plan['sheets'], '{n}.optionId', '{n}.count') : array();
				$options = !empty($plan['options']) ? Hash::combine($plan['options'], '{n}.optionId', '{n}.count') : array();

				// 予約番号の取得
				$clientData = $this->Client->getClientById($clientId);
				$reserveTag = $clientData['Client']['reserve_tag'];
				if (empty($reserveTag)) {
					// 予約タグなし
					throw new ApiException(ApiException::NO_RESERVE_TAG, 500);
				}
				$maxReservationKey = $this->Reservation->getMaxReservationKey($reserveTag);
				$resultReservationKey = $this->Reservation->uniqueCheckReservationKey($maxReservationKey);
				if (!empty($resultReservationKey)) {
					// 予約番号重複
					throw new ApiException(ApiException::RESERVE_NO_DUPLICATE, 409);
				}

				// 現在時刻
				$currentTime = date('Y-m-d H:i:s');

				// モデルリセット
				$this->Reservation->create();
				$this->ReservationMail->create();
				$this->ReservationChildSheet->create();
				$this->ReservationPrivilege->create();
				$this->ReservationDetail->create();
				$this->CarClassReservation->create();

				// 予約データ
				$reservationParams = array(
					'client_id' => $clientId,
					'user_session_id' => $ip,
					'user_agent' => $agent,
					'reservation_datetime' => $currentTime,
					'reservation_key' => $maxReservationKey,
					'reservation_hash' => $hashKey,
					'reservation_status_id' => Constant::STATUS_RESERVATION,
					'commodity_item_id' => $plan['planId'],
					'rent_datetime' => $from,
					'return_datetime' => $to,
					'rent_office_id' => $plan['fromShopId'],
					'return_office_id' => $plan['toShopId'],
					'last_name' => $userInfo['lastName'],
					'first_name' => $userInfo['firstName'],
					'tel' => $userInfo['tel'],
					'email' => $userInfo['email'],
					'arrival_flight_number' => $plan['flightNumber']['arrival'],
					'departure_flight_number' => $plan['flightNumber']['departure'],
					'adults_count' => $userInfo['adultCount'],
					'children_count' => $userInfo['childCount'],
					'infants_count' => $userInfo['infantCount'],
					'cars_count' => 1,
					'amount' => $plan['totalPrice'],
					'is_send_mail' => 1,
					// TODO 備考の入力有無で0:未返信 3:設定なし
					'mail_status' => !empty($plan['remarks']) ? 0 : 3,
					'advertising_cd' => $advertisingCd,
					'api_status_id' => $this->ReservationAPISelect->apiRequired($clientId) ? Constant::API_STATUS_INCLUDED : Constant::API_STATUS_EXCLUDED,
					'rennavi_status' => $this->ReservationAPISelect->isRennaviApiTarget($clientId) ? Constant::RENNAVI_STATUS_RESERVE : Constant::RENNAVI_STATUS_EXCLUDED,
				);

				// TODO 決済手数料(仮)
				// if (!$fromStep1) {
				if (isset($plan['fee'])) {
					$reservationParams['amount'] += $plan['fee'];
					$reservationParams['administrative_fee'] = $plan['fee'];
					$reservationParams['payment_status'] = 'PAYED'; // TODO 即時以外の考慮も必要
				}

				$reservationResult = $this->Reservation->save($reservationParams);
				if (empty($reservationResult)) {
					if (!empty($this->Reservation->validationErrors)) {
						// 予約バリデーションエラー
						throw new ApiException($this->Reservation->validationErrors);
					}
					// 予約登録失敗
					throw new ApiException(ApiException::RESERVE_INSERT_ERROR, 500);
				}

				$reservationId = $reservationResult['Reservation']['id'];

				// 備考の入力がある場合
				if (!empty($plan['remarks'])) {
					// 予約メールデータ（備考）
					$reservationMailParams = array(
						'reservation_id' => $reservationId,
						'mail_datetime' => $currentTime,
						'staff_id' => 0,
						'contents' => $plan['remarks'],
						'read_flg' => 0,
					);
					$reservationMailResult = $this->ReservationMail->save($reservationMailParams);
					if (empty($reservationMailResult)) {
						// 備考登録失敗
						throw new ApiException(ApiException::REMARKS_INSERT_ERROR, 500);
					}
				}

				// オプション料金（チャイルドシート・特典）
				// シートの最大数チェック
				$optionParams = array(
					'commodityId' => $commodityId,
					'period' => $period,
					'period24' => $period24,
					'sheet' => $sheets,
					'privilege' => $options,
				);
				$reservationPrivilegeData = $this->CommodityPrivilege->getPrivilegeData($optionParams);

				// 予約チャイルドシートデータ
				$reservationChildSheetParams = array();
				if (!empty($sheets)) {
					foreach ($sheets as $privilegeId => $sheetCount) {
						$reservationChildSheetParams[] = array(
							'reservation_id' => $reservationId,
							'child_sheet_id' => $privilegeId,
							'count' => $sheetCount,
							'price' => $reservationPrivilegeData[$privilegeId]['amount'],
						);
					}
					$reservationChildSheetResult = $this->ReservationChildSheet->saveMany($reservationChildSheetParams);
					if (empty($reservationChildSheetResult)) {
						// シート登録失敗
						throw new ApiException(ApiException::SEAT_INSERT_ERROR, 500);
					}
				}

				// 予約特典データ
				$reservationChildSheetParams = array();
				if (!empty($options)) {
					foreach ($options as $privilegeId => $privilegeCount) {
						$reservationPrivilegeParams[] = array(
							'reservation_id' => $reservationId,
							'privilege_id' => $privilegeId,
							'count' => $privilegeCount,
							'price' => $reservationPrivilegeData[$privilegeId]['amount'],
						);
					}
					$reservationPrivilegeResult = $this->ReservationPrivilege->saveMany($reservationPrivilegeParams);
					if (empty($reservationPrivilegeResult)) {
						// オプション登録失敗
						throw new ApiException(ApiException::OPTION_INSERT_ERROR, 500);
					}
				}

				// 予約明細データ
				$dateString = $from . '~' . $to;
				// 乗り捨て料金・深夜手数料
				$dropOffLateNight = $this->DropOffAreaRate->dropOffLateNight(
					$plan['fromShopId'],
					$plan['toShopId'],
					$carClassId,
					$plan['startTime'],
					$plan['endTime']
				);

				$reservationDetailParams = array();
				if (!empty($dropOffLateNight['dropPrice'])) {
					// 乗り捨て料金
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationId,
						'detail_type_id' => Constant::DETAIL_TYPE_DROPOFFPRICE,
						'detail_date_string' => $dateString,
						'count' => 1,
						'amount' => $dropOffLateNight['dropPrice'],
					);
				}
				if (!empty($dropOffLateNight['nightFee'])) {
					// 深夜手数料
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationId,
						'detail_type_id' => Constant::DETAIL_TYPE_NIGHTFEE,
						'detail_date_string' => $dateString,
						'count' => 1,
						'amount' => $dropOffLateNight['nightFee'],
					);
				}

				$reservationSheetAmount = 0;
				$reservationSheetCount = 0;
				$reservationPrivilegeAmount = 0;
				$reservationPrivilegeCount = 0;
				foreach ($reservationPrivilegeData as $key => $reservationPrivilege) {
					if ($reservationPrivilege['option_flg'] == 1) {
						// チャイルドシート
						$reservationSheetAmount += $reservationPrivilege['amount'];
						$reservationSheetCount += $reservationPrivilege['count'];
					} else {
						// オプション（特典）
						$reservationPrivilegeAmount += $reservationPrivilege['amount'];
						$reservationPrivilegeCount += $reservationPrivilege['count'];
					}
				}

				if (!empty($reservationSheetCount)) {
					// チャイルドシート
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationId,
						'detail_type_id' => Constant::DETAIL_TYPE_CHILDSHEET,
						'detail_date_string' => $dateString,
						'count' => $reservationSheetCount,
						'amount' => $reservationSheetAmount
					);
				}

				if (!empty($reservationPrivilegeCount)) {
					// オプション（特典）
					$reservationDetailParams[] = array(
						'reservation_id' => $reservationId,
						'detail_type_id' => Constant::DETAIL_TYPE_OPTIONPRICE,
						'detail_date_string' => $dateString,
						'count' => $reservationPrivilegeCount,
						'amount' => $reservationPrivilegeAmount,
					);
				}

				// 免責補償料金取得
				$dateFrom = date('Y-m-d', strtotime($reservationParams['rent_datetime']));
				$dateTo = date('Y-m-d', strtotime($reservationParams['return_datetime']));

				list($dayNight, $period, $period24) = $this->ReservationUtil->getPeriodArray($reservationParams['rent_datetime'], $reservationParams['return_datetime']);

				$disclaimerCompensationPrice = $this->DisclaimerCompensation->getFee(
					$carClassId,
					$dateFrom,
					$dateTo,
					$period,
					$period24
				);

				// 免責補償料金
				$reservationDetailParams[] = array(
					'reservation_id' => $reservationId,
					'detail_type_id' => Constant::DETAIL_TYPE_DISCLAIMER,
					'detail_date_string' => $dateString,
					'count' => 1,
					'amount' => $disclaimerCompensationPrice,
				);

				// 基本料金からの免責補償料金の減算
				$basicPrice = $plan['basePrice'] - $disclaimerCompensationPrice;
				// 基本料金
				$reservationDetailParams[] = array(
					'reservation_id' => $reservationId,
					'detail_type_id' => Constant::DETAIL_TYPE_BASICPRICE,
					'detail_date_string' => $dateString,
					'count' => 1,
					'amount' => $basicPrice,
				);
				$reservationDetailResult = $this->ReservationDetail->saveMany($reservationDetailParams);
				if (empty($reservationDetailResult)) {
					// 予約明細登録失敗
					throw new ApiException(ApiException::DETAIL_INSERT_ERROR, 500);
				}

				// 在庫チェック
				$stockCheckParams = array(
					'from_office' => $plan['fromShopId'],
					'from' => $from,
					'to' => $to,
					'cars_count' => 1,
				);
				$remainingStock = $this->CommodityItem->getOfficeStocks($commodityItemPriceData['CarClass']['id'], $stockCheckParams);
				if (empty($remainingStock) || empty($remainingStock[$plan['fromShopId']])) {
					// 在庫なし
					throw new ApiException(ApiException::STOCK_OUT, 404);
				}

				// 営業所在庫管理地域取得
				$officeStockGroup = $this->OfficeStockGroup->getOfficeStockGroupId($plan['fromShopId']);

				// クラス共有在庫
				$step = 60 * 60 * 24;
				$arrayTime = range(strtotime(date('Y-m-d', $from)), strtotime(date('Y-m-d', $to)), $step);
				foreach ($arrayTime as $time) {
					$carClassReservationParams[] = array(
						'client_id' => $clientId,
						'stock_group_id' => $officeStockGroup['OfficeStockGroup']['stock_group_id'],
						'car_class_id' => $carClassId,
						'stock_date' => date('Y-m-d', $time),
						'reservation_id' => $reservationId,
						'reservation_count' => 1,
					);
				}

				$carClassReservationResult = $this->CarClassReservation->saveMany($carClassReservationParams);
				if (empty($carClassReservationResult)) {
					// 在庫引当登録失敗
					throw new ApiException(ApiException::RESERVE_INSERT_ERROR, 500);
				}

				// ユーザーIDが無ければユーザー登録
				if (empty($userId)) {
					// common.cm_m_user
					$cmTmUserParams = array(
						'family_name' => $userInfo['lastName'],
						'first_name' => $userInfo['firstName'],
						'tel' => $userInfo['tel'],
						'email' => $userInfo['email'],
						'mailmagazine_recept_flg' => 0,
						'password' => '',
						'member_status' => 0,
					);

					$userId = (new User($db))->insertUser($cmTmUserParams, $db);
					if (!$userId) {
						// ユーザー登録失敗
						throw new ApiException(ApiException::USER_INSERT_ERROR, 500);
					}
				}

				// 予約連携API
				$componentName = $this->ReservationAPISelect->getApiComponentName($clientId);
				if (!empty($componentName)) {
					// 会社別コンポーネントロード
					$reservationAPI = $this->Components->load($componentName);

					$childSheetData = isset($reservationChildSheetParams) ? $reservationChildSheetParams : array();
					$privilegeData = isset($reservationPrivilegeParams) ? $reservationPrivilegeParams : array();

					// 送信データセット
					$reservationAPI->setFrontReservationData($reservationParams, $reservationDetailParams, $childSheetData, $privilegeData);

					// 送信
					list($success, $result) = $reservationAPI->sendReservationData();
					if ($success) {	// API成功
						if ($result['status']) {
							// コミットされるまでにエラー発生したら、キャンセル連携必要
							$cancelApiRequired = true;

							if (!empty($result['reserveno'])) {
								$controlNumber = $result['reserveno'];
								// 管理番号更新
								$updateResult = $this->Reservation->save(array(
									'id' => $reservationId,
									'control_number' => $controlNumber,
								));
								if (!is_array($updateResult)) {
									$saveFlg = false;
									$errorString = sprintf("管理番号の登録に失敗しました。(%s)", $result['reserveno']);
									throw new ApiException($errorString, 500);
								}
							}
						} else {
							// 連携先予約NGの場合、キャンセル連携しないのでメール通知必要なし
							$saveFlg = false;
							$errorString = sprintf("予約連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
							throw new ApiException($errorString, 500);
						}
					} else {
						// API失敗の場合、キャンセル連携必要
						$cancelApiRequired = true;
						$saveFlg = false;
						$errorString = '予約連携中に何らかのエラーが発生しました。';
						throw new ApiException($errorString, 500);
					}
				}

				$reservations[] = array(
					'planId' => $plan['planId'],
					'reservationKey' => $maxReservationKey,
					'reservationId' => $reservationId,
				);
			}

			// 全てのプラン予約後の処理
			if (empty($cmApplicationId)) {
				// 新規登録 cm_th_application, cm_th_application_detail
				$application_data = array(
					'user_id' => $userId,
				);
				$data = array();
				foreach ($reservations as $reservation) {
					$data[] = array(
						'application_id' => $reservation['reservationId'],
						'service_cd' => "rc",
					);
				}
				$application = new Application($db);
				if (!$application->insertApplication($application_data, $data, $db)) {
					// 申込登録エラー
					throw new ApiException(ApiException::APPLICATION_INSERT_ERROR, 500);
				}
			} else {
				foreach ($reservations as $reservation) {
					if(!$this->ReservationUtil->insertApplicationDetail($db, $reservation['reservationId'], $cmApplicationId)) {
						// 申込詳細登録エラー
						throw new ApiException(ApiException::APPLICATION_DETAIL_INSERT_ERROR, 500);
					}
				}
			}

			$db->commit();
			$this->Reservation->commit();
			$cancelApiRequired = false;

		} catch (Exception $e) {
			// 予約失敗時はログを出力する
			$reservationKey = isset($maxReservationKey) ? $maxReservationKey : '';
			$this->log(sprintf("ReservationId : %s (%s)\n%s\n%s", $reservationId, $reservationKey, $e->getMessage(), $e->getTraceAsString()), 'error');

			if ($cancelApiRequired) {
				// 予約連携APIでエラー or 予約連携API成功後にエラーの場合
				// キャンセル連携
				$cancelSuccess = false;
				$reservationAPI->changeApiStatus(Constant::API_STATUS_CANCEL);
				try {
					list($success, $result) = $reservationAPI->sendReservationData();
					if ($success) {
						if ($result['status']) {
							$cancelSuccess = true;
						} else {
							$errorString = sprintf("キャンセル連携が失敗しました。(%s)", (!empty($result['message']) ? $result['message'] : ''));
						}
					} else {
						$errorString = 'キャンセル連携中に何らかのエラーが発生しました。';
					}
				} catch (Exception $ex) {
					$errorString = sprintf("%s\n%s", $ex->getMessage(), $ex->getTraceAsString());
				}
				if (!$cancelSuccess) {
					// キャンセル連携もエラーの場合、メールで通知
					$this->log(sprintf("ReservationId : %s (%s)\n%s", $reservationResult['Reservation']['id'], $maxReservationKey, $errorString), 'error');
					$reservationAPI->sendAlertFromFront($controlNumber, $this->domain);
				}
			}

			// if (!$fromStep1) {
			// 	$this->PaymentEcon->cancelReservationFail();
			// }

			throw $e;
		}

		// if (!$fromStep1) {
		// 	// 与信 -> 計上にする
		// 	if (!$this->PaymentEcon->cardCapture($reservationResult['Reservation']['id'], $reservationParams['amount'])) {
		// 		$this->PaymentEcon->notice(sprintf("予約番号:%s", $reservationResult['Reservation']['reservation_key']), "ECON与信->計上失敗");
		// 	}

		// 	// 予約に成功したら決済結果をDBに登録する
		// 	$this->PaymentEcon->saveResultData($reservationResult['Reservation']['reservation_key']);
		// }

		// 予約メール送信処理
		foreach ($reservations as $reservation) {
			$reservation = $this->Reservation->getReservationData($reservation['reservationId']);
			$this->ReservationUtil->sendReservedMail($reservation, true, empty($cmApplicationId));

			Configure::load('YotpoConfig', 'default');
			$yotpoConfig = Configure::read('Yotpo');
			if ($yotpoConfig['is_active']) {
				//BOC send MAP data to yotpo
				$office_url = $reservation['Client']['url'] . '/' . $reservation['RentOffice']['url'] . '/';
				$yotpo_order_info = array(
					'ordered_at' => date('Y-m-d', strtotime($reservation['Reservation']['return_datetime'])),
					'email' => $reservation['Reservation']['email'],
					'lastname' => $reservation['Reservation']['last_name'],
					'firstname' => $reservation['Reservation']['first_name'],
					'order_number' => $reservation['Reservation']['id'],
				);
				$yotpo_items = array(
					array(
						'item_code' => $reservation['RentOffice']['id'],
						'url' => $office_url,
						'name' => $reservation['Client']['name'] . '　|　' . $reservation['RentOffice']['name'],
						'group_name' => $reservation['Client']['id'],
						'clientProductName' => $reservation['Client']['name'],
						'clientProductURL' => $reservation['Client']['url'] . '/'
					)
				);
				if ($reservation['Client']['sp_logo_image']) {
					$yotpo_items[0]['clientImageURL'] = '/rentacar/img/logo/square/' . $reservation['Client']['id'] . '/' . $reservation['Client']['sp_logo_image'];
				}
				$json_order_info = json_encode($yotpo_order_info);
				$json_items = json_encode($yotpo_items);
				exec("php /var/www/skyticket.com/rentacar/app/Console/cake.php YotpoReview postOrderWrapper '$json_order_info' '$json_items' -app /var/www/skyticket.com/rentacar/app/ > /dev/null 2>&1 &");
				//EOC send MAP data to yotpo
			}
		}

		$this->response->statusCode(201);
		$this->responseData = $reservations;
	}
}

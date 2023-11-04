<?php
App::uses('AppController','Controller');
App::uses('Sanitize','Utility');

/**
 * Reservations Controller
 *
 * @property Reservation $Reservation
 */
class SelectMailTargetsController extends AppController {
	public $reservationDetail = array();
	public $rankCalendar = array();
	public $basicPrice;
	public $dayCount;
	public $uses = array(
		'Reservation',
		'ReservationStatus',
		'ReservationMail',
		'CancelReason',
		'CarType',
		'MailSendHistory',
		'MailSendTarget'
	);
	public $components = array('TaxRate');

	function beforeFilter() {
		parent::beforeFilter();

		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		$this->set('isClientAdmin', $this->cdata['username']);

		$paymentMethod = array(
			1 => 'WEB事前決済',
			0 => '現地精算',
		);
		$this->set('paymentMethod', $paymentMethod);

		// キャンセル処理がどこで行われたかの出し分け(本来0はありえないが念の為)
		$this->cancelType = array(
			0 => 'マイページ',
			1 => 'マイページ',
			2 => 'マイページ',
			3 => 'マイページ',
			4 => 'マイページ',
			5 => '管理ツール',
			6 => '管理ツール',
			7 => '管理ツール',
		);
		$this->set('cancelType', $this->cancelType);
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$viewData = '';
		if (!empty($this->request->query)) {
			$conditions['conditions'] = array();

			// 販売方法
			if (!empty($this->request->query['SalesType'])) {
				$conditions['conditions']['Commodity.sales_type'] = $this->request->query['SalesType'];
			}

			// ステータス
			if (!empty($this->request->query['ReservationStatus'])) {
				if (is_numeric($this->request->query['ReservationStatus'])) {
					$conditions['conditions']['Reservation.reservation_status_id'] = $this->request->query['ReservationStatus'];
				}
			} else {
				// $this->request->query['ReservationStatus'] = $conditions['conditions']['Reservation.reservation_status_id'] = 1;
			}

			// 利用開始日
			if (!empty($this->request->query['ReservationRentDateFrom']['year'])) {

				$rentDateFrom = $this->request->query['ReservationRentDateFrom']['year'];
				if (!empty($this->request->query['ReservationRentDateFrom']['month'])) {
					$rentDateFrom .= '-' . $this->request->query['ReservationRentDateFrom']['month'];
				} else {
					$rentDateFrom .= '-' . '01';
				}
				if (!empty($this->request->query['ReservationRentDateFrom']['day'])) {
					$rentDateFrom .= '-' . $this->request->query['ReservationRentDateFrom']['day'];
				} else {
					$rentDateFrom .= '-' . '01';
				}
				$rentDateFrom .= ' ' . '00:00:00';

				$conditions['conditions']['Reservation.rent_datetime >='] = $rentDateFrom;
				$this->request->data['ReservationRentDateFrom']['ReservationRentDate'] = $this->request->query['ReservationRentDateFrom'];
			} else {
				// デフォルトの現在年月が画面に表示されてしまうため、空文字を設定
				$this->request->data['ReservationRentDateFrom']['ReservationRentDate'] = '';
			}
			if (!empty($this->request->query['ReservationRentDateTo']['year'])) {

				$rentDateTo = $this->request->query['ReservationRentDateTo']['year'];
				if (!empty($this->request->query['ReservationRentDateTo']['month'])) {
					$rentDateTo .= '-' . $this->request->query['ReservationRentDateTo']['month'];
				} else {
					$rentDateTo .= '-' . '12';
				}
				if (!empty($this->request->query['ReservationRentDateTo']['day'])) {
					$rentDateTo .= '-' . $this->request->query['ReservationRentDateTo']['day'];
				} else {
					$rentDateTo .= '-' . '31';
				}
				$rentDateTo .= ' ' . '23:59:59';

				$conditions['conditions']['Reservation.rent_datetime <='] = $rentDateTo;
				$this->request->data['ReservationRentDateTo']['ReservationRentDate'] = $this->request->query['ReservationRentDateTo'];
			} else {
				// デフォルトの現在年月が画面に表示されてしまうため、空文字を設定
				$this->request->data['ReservationRentDateTo']['ReservationRentDate'] = '';
			}
			if (strlen($rentDateFrom) > 0 || strlen($rentDateTo) > 0) {
				if (!(strlen($rentDateFrom) > 0 && strlen($rentDateTo) > 0)) {
					$dateDesignationErr[] = '利用開始日';
				} else {
					// 日付範囲カウント
					$day1 = new DateTime($rentDateFrom);
					$day2 = new DateTime($rentDateTo);
					$interval = $day1->diff($day2);
					if ($interval->days > 30) {
						$dateOverErr[] = '利用開始日';
					}
				}
			}

			// 利用終了日
			if(!empty($this->request->query['ReservationReturnDateFrom']['year'])) {

				$returnDateFrom = $this->request->query['ReservationReturnDateFrom']['year'];
				if (!empty($this->request->query['ReservationReturnDateFrom']['month'])) {
					$returnDateFrom .= '-' . $this->request->query['ReservationReturnDateFrom']['month'];
				} else {
					$returnDateFrom .= '-' . '01';
				}
				if (!empty($this->request->query['ReservationReturnDateFrom']['day'])) {
					$returnDateFrom .= '-' . $this->request->query['ReservationReturnDateFrom']['day'];
				} else {
					$returnDateFrom .= '-' . '01';
				}
				$returnDateFrom .= ' ' . '00:00:00';

				$conditions['conditions']['Reservation.return_datetime >='] = $returnDateFrom;
				$this->request->data['ReservationReturnDateFrom']['ReservationReturnDate'] = $this->request->query['ReservationReturnDateFrom'];
			}
			if(!empty($this->request->query['ReservationReturnDateTo']['year'])) {

				$returnDateTo = $this->request->query['ReservationReturnDateTo']['year'];
				if (!empty($this->request->query['ReservationReturnDateTo']['month'])) {
					$returnDateTo .= '-' . $this->request->query['ReservationReturnDateTo']['month'];
				} else {
					$returnDateTo .= '-' . '12';
				}
				if (!empty($this->request->query['ReservationReturnDateTo']['day'])) {
					$returnDateTo .= '-' . $this->request->query['ReservationReturnDateTo']['day'];
				} else {
					$returnDateTo .= '-' . '31';
				}
				$returnDateTo .= ' ' . '23:59:59';

				$conditions['conditions']['Reservation.return_datetime <='] = $returnDateTo;
				$this->request->data['ReservationReturnDateTo']['ReservationReturnDate'] = $this->request->query['ReservationReturnDateTo'];
			}
			if (strlen($returnDateFrom) > 0 || strlen($returnDateTo) > 0) {
				if (!(strlen($returnDateFrom) > 0 && strlen($returnDateTo) > 0)) {
					$dateDesignationErr[] = '利用終了日';
				} else {
					// 日付範囲カウント
					$day1 = new DateTime($returnDateFrom);
					$day2 = new DateTime($returnDateTo);
					$interval = $day1->diff($day2);
					if ($interval->days > 30) {
						$dateOverErr[] = '利用終了日';
					}
				}
			}

			// 申込日時
			if (!empty($this->request->query['ReservationCreatedDateFrom']['year'])) {

				$createdDateFrom = $this->request->query['ReservationCreatedDateFrom']['year'];
				if (!empty($this->request->query['ReservationCreatedDateFrom']['month'])) {
					$createdDateFrom .= '-' . $this->request->query['ReservationCreatedDateFrom']['month'];
				} else {
					$createdDateFrom .= '-' . '01';
				}
				if (!empty($this->request->query['ReservationCreatedDateFrom']['day'])) {
					$createdDateFrom .= '-' . $this->request->query['ReservationCreatedDateFrom']['day'];
				} else {
					$createdDateFrom .= '-' . '01';
				}
				$createdDateFrom .= ' ' . '00:00:00';

				$conditions['conditions']['Reservation.created >='] = $createdDateFrom;
				$this->request->data['ReservationCreatedDateFrom']['Created'] = $this->request->query['ReservationCreatedDateFrom'];
			}
			if (!empty($this->request->query['ReservationCreatedDateTo']['year'])) {

				$createdDateTo = $this->request->query['ReservationCreatedDateTo']['year'];
				if (!empty($this->request->query['ReservationCreatedDateTo']['month'])) {
					$createdDateTo .= '-' . $this->request->query['ReservationCreatedDateTo']['month'];
				} else {
					$createdDateTo .= '-' . '12';
				}
				if (!empty($this->request->query['ReservationCreatedDateTo']['day'])) {
					$createdDateTo .= '-' . $this->request->query['ReservationCreatedDateTo']['day'];
				} else {
					$createdDateTo .= '-' . '31';
				}
				$createdDateTo .= ' ' . '23:59:59';

				$conditions['conditions']['Reservation.created <='] = $createdDateTo;
				$this->request->data['ReservationCreatedDateTo']['Created'] = $this->request->query['ReservationCreatedDateTo'];
			}
			if (strlen($createdDateFrom) > 0 || strlen($createdDateTo) > 0) {
				if (!(strlen($createdDateFrom) > 0 && strlen($createdDateTo) > 0)) {
					$dateDesignationErr[] = '申込み日時';
				} else {
					// 日付範囲カウント
					$day1 = new DateTime($createdDateFrom);
					$day2 = new DateTime($createdDateTo);
					$interval = $day1->diff($day2);
					if ($interval->days > 30) {
						$dateOverErr[] = '申込み日時';
					}
				}
			}

			// キャンセル日時
			if (!empty($this->request->query['ReservationCancelDateFrom']['year'])) {

				$cancelDateFrom = $this->request->query['ReservationCancelDateFrom']['year'];
				if (!empty($this->request->query['ReservationCancelDateFrom']['month'])) {
					$cancelDateFrom .= '-' . $this->request->query['ReservationCancelDateFrom']['month'];
				} else {
					$cancelDateFrom .= '-' . '01';
				}
				if (!empty($this->request->query['ReservationCancelDateFrom']['day'])) {
					$cancelDateFrom .= '-' . $this->request->query['ReservationCancelDateFrom']['day'];
				} else {
					$cancelDateFrom .= '-' . '01';
				}
				$cancelDateFrom .= ' ' . '00:00:00';

				$conditions['conditions']['Reservation.cancel_datetime >='] = $cancelDateFrom;
				$this->request->data['ReservationCancelDateFrom']['Cancel'] = $this->request->query['ReservationCancelDateFrom'];
			}
			if (!empty($this->request->query['ReservationCancelDateTo']['year'])) {

				$cancelDateTo = $this->request->query['ReservationCancelDateTo']['year'];
				if (!empty($this->request->query['ReservationCancelDateTo']['month'])) {
					$cancelDateTo .= '-' . $this->request->query['ReservationCancelDateTo']['month'];
				} else {
					$cancelDateTo .= '-' . '12';
				}
				if (!empty($this->request->query['ReservationCancelDateTo']['day'])) {
					$cancelDateTo .= '-' . $this->request->query['ReservationCancelDateTo']['day'];
				} else {
					$cancelDateTo .= '-' . '31';
				}
				$cancelDateTo .= ' ' . '23:59:59';

				$conditions['conditions']['Reservation.cancel_datetime <='] = $cancelDateTo;
				$this->request->data['ReservationCancelDateTo']['Cancel'] = $this->request->query['ReservationCancelDateTo'];
			}
			if (strlen($cancelDateFrom) > 0 || strlen($cancelDateTo) > 0) {
				if (!(strlen($cancelDateFrom) > 0 && strlen($cancelDateTo) > 0)) {
					$dateDesignationErr[] = 'キャンセル日時';
				} else {
					// 日付範囲カウント
					$day1 = new DateTime($cancelDateFrom);
					$day2 = new DateTime($cancelDateTo);
					$interval = $day1->diff($day2);
					if ($interval->days > 30) {
						$dateOverErr[] = 'キャンセル日時';
					}
				}
			}

			// skyticket申込番号
			if (!empty($this->request->query['CmApplicationId'])) {
				$ids = array_diff(explode("\n", str_replace("\r", "", $this->request->query['CmApplicationId'])), array(''));
				foreach ($ids as $key => $inputId) {
					// 前後の全半角スペース除去
					$ids[$key] = preg_replace("/(^\s+)|(\s+$)/u", "", $inputId);
				}
				if (count($ids) > 1) {
					$conditions['conditions']['CmThApplicationDetail.cm_application_id'] = $ids;
				} elseif (count($ids) > 0) {
					$conditions['conditions']['CmThApplicationDetail.cm_application_id'] = current($ids);
				}
				$this->request->query['CmApplicationId'] = implode("\n", $ids);
			}

			// 予約番号
			if (!empty($this->request->query['ReservationKeyId'])) {
				$ids = array_diff(explode("\n", str_replace("\r", "", $this->request->query['ReservationKeyId'])), array(''));
				foreach ($ids as $key => $inputId) {
					// 前後の全半角スペース除去
					$ids[$key] = preg_replace("/(^\s+)|(\s+$)/u", "", $inputId);
				}
				if (count($ids) > 1) {
					$conditions['conditions']['Reservation.reservation_key'] = $ids;
				} elseif (count($ids) > 0) {
					$conditions['conditions']['Reservation.reservation_key like'] = '%' . current($ids) . '%';
				}
				$this->request->query['ReservationKeyId'] = implode("\n", $ids);
			}

			// 会社名
			if (!empty($this->request->query['Client'])) {
				$conditions['conditions']['Client.id'] = $this->request->query['Client'];
			}

			if (isset($this->request->query['Recommend']) && is_numeric($this->request->query['Recommend'])) {
				if ($this->request->query['Recommend'] == 1) {
					$conditions['conditions']['Reservation.recommend_id >'] = 0;
				} else {
					$conditions['conditions']['Reservation.recommend_id'] = 0;
				}
			}

			// 車両タイプ
			if (!empty($this->request->query['CarType'])) {
				$conditions['conditions']['CarType.id'] = $this->request->query['CarType'];
			}

			// 広告コード
			if (!empty($this->request->query['AdvertisingCd'])) {
				$conditions['conditions']['Reservation.advertising_cd like'] = '%' . $this->request->query['AdvertisingCd'] . '%';
			}

			// 支払方法
			if (isset($this->request->query['PaymentMethod']) && is_numeric($this->request->query['PaymentMethod'])) {
				if ($this->request->query['PaymentMethod'] == 1) {
					$conditions['conditions']['OR'] = array(
						array(
							'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
							'Reservation.payment_status IS NOT NULL'
						),
						array(
							'Commodity.sales_type' => Constant::SALES_TYPE_AGENT_ORGANIZED,
							'Reservation.sales_price > 0'
						)
					);
				} else {
					$conditions['conditions']['OR'] = array(
						array(
							'Commodity.sales_type' => Constant::SALES_TYPE_ARRANGED,
							'Reservation.payment_status IS NULL'
						),
						array(
							'Commodity.sales_type' => Constant::SALES_TYPE_AGENT_ORGANIZED,
							'Reservation.sales_price' => 0
						)
					);
				}
			}

			// 入金ステータス
			if (!empty($this->request->query['PaymentStatus'])) {
				$conditions['conditions']['Reservation.payment_status'] = $this->request->query['PaymentStatus'];
			}

			$conditions['order'] = 'Reservation.id desc';
			$this->Reservation->unbindModel(array(
				'belongsTo' => array(
					'CommodityItem'
				)
			), false);
			if (!empty($dateDesignationErr)) {
				// 日付の開始と終了がセットじゃない
				$this->Session->setFlash(implode('、', $dateDesignationErr) . 'に開始日と終了日を1ヶ月以内の範囲で指定してください。','default',array('class'=>'alert alert-danger'));
			} elseif (!empty($dateOverErr)) {
				// 31日以上の日付範囲が指定されている
				$this->Session->setFlash(implode('、', $dateOverErr) . 'は1ヶ月以内の範囲で指定してください。','default',array('class'=>'alert alert-danger'));
			} elseif (empty($conditions['conditions'])) {
				// 検索条件がなにもない
				$this->Session->setFlash('検索条件を1つ以上指定してください。','default',array('class'=>'alert alert-danger'));
			} else {
				/**
				 * 送信対象reservations.id保存
				 */
				if (!empty($this->request->query['registerReservationId'])) {
					$this->__registerReservationIds($conditions);
				}

				$this->paginate = $this->Reservation->getReservationData($conditions);
				$data = $this->paginate();

				/**
				 * 予約IDとキーIDを取得
				 * 　ADD: 入金ステータス
				 */
				foreach ($data as $key => $val) {
					$viewData['reservation_id'][] = $val['Reservation']['id'];
					$viewData['reservation_key'][] = $val['Reservation']['reservation_key'];
					
					$payment_status = !empty($val['Reservation']['payment_status']) ? $val['Reservation']['payment_status'] : '';
					$payment_status = Constant::paymentStatus()[$payment_status];
					$viewData['reservation_payment_status'][] = $payment_status;
					$data[$key]['Reservation']['payment_status_jp'] = $payment_status;
				}

				$this->set('reservations', $data);
				$this->set('count', $this->Reservation->getReservationCount($conditions));
			}
			$this->request->data['Reservation'] = $this->request->query;
		}

		$this->__setViewVars($viewData);
	}

	function __setViewVars($data = '') {

		// 利用開始日オプション
		$this->set('datetimeRentFromOptions', array(
			'formName' => 'ReservationRentDateFrom',
			'fieldName' => 'ReservationRentDate',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => true
		));
		$this->set('datetimeRentToOptions', array(
			'formName' => 'ReservationRentDateTo',
			'fieldName' => 'ReservationRentDate',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => true
		));

		// 利用終了日オプション
		$this->set('datetimeReturnFromOptions', array(
			'formName' => 'ReservationReturnDateFrom',
			'fieldName' => 'ReservationReturnDate',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => false
		));
		$this->set('datetimeReturnToOptions', array(
			'formName' => 'ReservationReturnDateTo',
			'fieldName' => 'ReservationReturnDate',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => false
		));

		// 申込日時オプション
		$this->set('datetimeBookingOptions', array(
			'formName' => 'ReservationCreatedDate',
			'fieldName' => 'Created',
			'dateFormat' => 'YMD',
			'class' => 'form',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => false
		));
		$this->set('datetimeBookingFromOptions', array(
			'formName' => 'ReservationCreatedDateFrom',
			'fieldName' => 'Created',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => false
		));
		$this->set('datetimeBookingToOptions', array(
			'formName' => 'ReservationCreatedDateTo',
			'fieldName' => 'Created',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => false
		));

		// キャンセル日時オプション
		$this->set('datetimeCancelFromOptions', array(
			'formName' => 'ReservationCancelDateFrom',
			'fieldName' => 'Cancel',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => false
		));
		$this->set('datetimeCancelToOptions', array(
			'formName' => 'ReservationCancelDateTo',
			'fieldName' => 'Cancel',
			'dateFormat' => 'YMD',
			'class' => 'form span3',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => false
		));

		$reservationStatus = $this->ReservationStatus->find('list', array(
			'fields' => array(
				'ReservationStatus.name'
			)
		));

		if (!empty($data['reservation_id'])) {
			$reservationMail = $this->ReservationMail->find('list', array(
				'fields' => array(
					'ReservationMail.reservation_id',
					'ReservationMail.contents'
				),
				'conditions' => array(
					'reservation_id' => $data['reservation_id']
				)
			));
			$reservationMailDate = $this->ReservationMail->find('list', array(
				'fields' => array(
					'ReservationMail.reservation_id',
					'mail_datetime'
				),
				'conditions' => array(
					'reservation_id' => $data['reservation_id']
				)
			));
		}

		$carTypeList = $this->CarType->find('list', array(
			'conditions' => array(
				'CarType.delete_flg' => 0,
			),
			'order' => array(
				'CarType.id' => 'asc',
			),
		));

		$clientList = $this->Client->find('list');

		$cancelReasonList = $this->CancelReason->find('list', array(
			'fields' => 'id,reason'
		));

		$conditions['conditions']['Reservation.client_id'] = $this->clientData['client_id'];
		$conditions['order'] = array(
			'Reservation.rent_datetime' => 'asc'
		);

		$this->set(compact('reservationStatus', 'reservationMail', 'reservationMailDate', 'cancelReasonList', 'clientList', 'carTypeList'));
	}

	/**
	 * reservations.idを登録する
	 */
	private function __registerReservationIds($conditions) {
		/**
		 * データ取得
		 */
		$this->Reservation->unbindModel(array(
			'belongsTo' => array(
				'CommodityItem',
				'UserSession',
			),
			'hasMany' => array(
				'Contract',
				'ReservationChildSheet',
				'ReservationDetail',
				'ReservationMail',
				'ReservationPrivilege',
			),
		), false);


		$conditions = $this->Reservation->getReservationData($conditions);
		$count = $this->Reservation->find('count', $conditions);
		$limit = 500;
		$loop  = ceil($count / $limit);

		if ($count > 0) {
			try {
				$this->MailSendHistory->begin();
				//履歴テーブルに空データを作る MailSendHistory
				$insertMailSendHistoryData['create_datetime'] = date('Y-m-d H:i:s');
				$insertMailSendHistoryData['create_staff_id'] = $this->cdata['id'];
				$insertMailSendHistoryData['update_datetime'] = date('Y-m-d H:i:s');
				$insertMailSendHistoryData['update_staff_id'] = $this->cdata['id'];
				$this->MailSendHistory->create();
				if (!$this->MailSendHistory->save($insertMailSendHistoryData)) {
					throw new Exception('送信マスタ保存エラー');
				}
				$mailSendHistoryId = $this->MailSendHistory->getLastInsertID();

				for ($i = 0; $i < $loop; $i++){
					$insertMailSendTargetDataArr = array();
					$conditions['limit'] = $limit;
					$conditions['offset'] = $limit * $i;

					$reservationData = $this->Reservation->find('all', $conditions);
					$reservationIds = Hash::extract($reservationData, '{n}.Reservation.id');

					//履歴テーブルの空データのIDをもとに対象者データをinsertしていく MailSendTarget
					foreach ($reservationIds as $key => $reservationId) {
						$insertMailSendTargetData['mail_send_history_id'] = $mailSendHistoryId;
						$insertMailSendTargetData['reservation_id'] = $reservationId;
						$insertMailSendTargetData['create_datetime'] = date('Y-m-d H:i:s');
						$insertMailSendTargetData['create_staff_id'] = $this->cdata['id'];
						$insertMailSendTargetDataArr[] = $insertMailSendTargetData;
					}
				}
				$this->MailSendTarget->create();
				if (!$this->MailSendTarget->saveAll($insertMailSendTargetDataArr)) {
					throw new Exception('送信対象保存エラー');
				}
			} catch (Exception $e) {
				///登録失敗
				$this->MailSendHistory->rollback();
				$this->Session->setFlash('宛先の登録に失敗しました。','default',array('class'=>'alert alert-danger'));
			}
			// 送信マスタ、送信対象が保存できれば成功
			$this->MailSendHistory->commit();
			$this->Session->setFlash('宛先の登録が完了しました。','default',array('class'=>'alert alert-success'));
		} else {
			$this->Session->setFlash('宛先が抽出できないため、登録に失敗しました。','default',array('class'=>'alert alert-danger'));
		}
	}


}

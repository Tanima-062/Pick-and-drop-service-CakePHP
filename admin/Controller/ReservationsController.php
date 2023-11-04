<?php
App::uses('AppController','Controller');
App::uses('Sanitize','Utility');

/**
 * Reservations Controller
 *
 * @property Reservation $Reservation
 */
class ReservationsController extends AppController {
	public $reservationDetail = array();
	public $rankCalendar = array();
	public $basicPrice;
	public $dayCount;
	public $uses = array(
		'Reservation',
		'ReservationStatus',
		'Office',
		'ReservationMail',
		'CancelReason',
		'Prefecture',
		'Plan',
		'Commodity',
		'CarClass',
		'DeliveryMail',
		'CommodityPrivilege',
		'CarType',
		'ReservationPrivilege',
		'Privilege',
		'ChildSheetPrice',
		'CommodityPrice',
		'CommodityFreeChildSheet',
		'ReservationDetail',
		'ReservationChildSheet',
		'PriceRankCalendar',
		'CarClassReservation',
		'CommodityItemReservation',
		'CommodityRentOffice',
		'CommodityReturnOffice',
		'CommodityItem',
		'Message',
		'ReservationStatus',
		'CancelDetail',
		'CommissionRateHistory',
		'Recommend',
		'CmThApplicationDetail'
	);
	public $components = array('TaxRate');

	function beforeFilter() {
		parent::beforeFilter();

		set_time_limit(0);
		ini_set('memory_limit', '1024M');

		$mailStatus = array(
			0 => '未返信',
			1 => '返信済み',
			2 => '対応完了',
			3 => '設定なし'
		);

		$this->set('mailStatus', $mailStatus);
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

			// セイ
			if (!empty($this->request->query['ReservationLastName'])) {
				$conditions['conditions']['Reservation.last_name like'] = '%' . $this->request->query['ReservationLastName'] . '%';
			}

			// メイ
			if (!empty($this->request->query['ReservationFirstName'])) {
				$conditions['conditions']['Reservation.first_name like'] = '%' . $this->request->query['ReservationFirstName'] . '%';
			}

			// メールアドレス
			if (!empty($this->request->query['Email'])) {
				$conditions['conditions']['Reservation.email like'] = '%' . $this->request->query['Email'] . '%';
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

			// 電話番号
			if (!empty($this->request->query['ReservationTel'])) {
				$conditions['conditions']['Reservation.tel like'] = '%' . $this->request->query['ReservationTel'] . '%';
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

			/**
			 * CSV出力
			 */
			if (!empty($this->request->query['getCsv'])) {
				$this->__downloadCsvData($conditions);
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

			$this->request->data['Reservation'] = $this->request->query;
		}

		$this->__setViewVars($viewData);

		$now = date("Y-m-d H:i:s");
		$conditions = array('Message.ui_admin_flg' => 1,
			'Message.delete_flg' => 0,
			'Message.from_time <=' => $now,
			'Message.to_time >=' => $now
		);
		$order = array('Message.modified DESC');
		$messages = $this->Message->find('all', array('conditions' => $conditions, 'order' => $order));

		$this->set('messages', $messages);
	}

	public function edit($id = null) {
		$this->view($id);
		$this->render('view');
	}

	/**
	 * cancel method
	 *
	 * @return void
	 */
	public function cancel() {
		$conditions['conditions'] = array();
		// ステータス
		$this->request->query['ReservationStatus'] = $conditions['conditions']['Reservation.reservation_status_id'] = 3;

		// クライアント
		if (!empty($this->request->query['client_id'])) {
			$conditions['conditions']['Reservation.client_id'] = $this->request->query['client_id'];
		}

		// キャンセル理由
		if (!empty($this->request->query['cancel_reason_id'])) {
			$conditions['conditions']['Reservation.cancel_reason_id'] = $this->request->query['cancel_reason_id'];
		}

		// 申込日時
		if (!empty($this->request->query['ReservationCreatedDate'])) {

			// 年
			if (!empty($this->request->query['ReservationCreatedDate']['year'])) {
				$createdDate = $this->request->query['ReservationCreatedDate']['year'];

				// 月
				if (!empty($this->request->query['ReservationCreatedDate']['month'])) {
					$createdDate .= '-' . $this->request->query['ReservationCreatedDate']['month'];

					// 日
					if (!empty($this->request->query['ReservationCreatedDate']['day'])) {
						$createdDate .= '-' . $this->request->query['ReservationCreatedDate']['day'];
					}
				}

				$conditions['conditions']['Reservation.created LIKE'] = $createdDate . '%';
				$this->request->data['ReservationCreatedDate']['Created'] = $this->request->query['ReservationCreatedDate'];
			}
		} else {
			$currentYear = date('Y');
			$currentMonth = date('m');
			$conditions['conditions']['Reservation.created LIKE'] = $currentYear . '-' . $currentMonth . '%';
			$this->request->query['ReservationCreatedDate']['year'] = $currentYear;
			$this->request->query['ReservationCreatedDate']['month'] = $currentMonth;
			$this->request->query['ReservationCreatedDate']['day'] = '';
		}

		// キャンセル日時
		if (!empty($this->request->query['ReservationCancelDate'])) {

			// 年
			if (!empty($this->request->query['ReservationCancelDate']['year'])) {
				$cancelDate = $this->request->query['ReservationCancelDate']['year'];

				// 月
				if (!empty($this->request->query['ReservationCancelDate']['month'])) {
					$cancelDate .= '-' . $this->request->query['ReservationCancelDate']['month'];

					// 日
					if (!empty($this->request->query['ReservationCancelDate']['day'])) {
						$cancelDate .= '-' . $this->request->query['ReservationCancelDate']['day'];
					}
				}

				$conditions['conditions']['Reservation.cancel_datetime LIKE'] = $cancelDate . '%';
				$this->request->data['ReservationCancelDate']['Cancel'] = $this->request->query['ReservationCancelDate'];
			} else {
				// デフォルトの現在年月が画面に表示されてしまうため、空文字を設定
				$this->request->data['ReservationCancelDate']['Cancel'] = '';
			}
		} else {
			$currentYear = date('Y');
			$currentMonth = date('m');
			$conditions['conditions']['Reservation.cancel_datetime LIKE'] = $currentYear . '-' . $currentMonth . '%';
			$this->request->query['ReservationCancelDate']['year'] = $currentYear;
			$this->request->query['ReservationCancelDate']['month'] = $currentMonth;
			$this->request->query['ReservationCancelDate']['day'] = '';
		}

		$conditions['order'] = 'Reservation.id desc';
		$this->Reservation->unbindModel(array(
			'hasMany' => array(
				'CommodityItem'
			),
			'belongsTo' => array(
				'CommodityItem'
			)
		), false);

		/**
		 * CSV出力
		 */
		if (!empty($this->request->query['getCsv'])) {
			$this->__downloadCsvCancelData($conditions);
		}

		$this->paginate = $this->Reservation->getReservationData($conditions);
		$data = $this->paginate();
		$this->set('reservations', $data);

		$this->set('count', $this->Reservation->getReservationCount($conditions));

		$this->__setViewVars();

		$this->request->data['Reservation'] = $this->request->query;
		$this->request->data['ReservationRentDate'] = $this->request->query;
		$this->request->data['ReservationReturnDate'] = $this->request->query;
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$data = $this->Reservation->find('all',array(
				'conditions'=>array(
						'Reservation.id'=>$id
				)
		));

		$this->Commodity->recursive = - 1;
		$data[0] += $this->Commodity->find('first',array(
				'conditions'=>array(
						'id'=>$data[0]['CommodityItem']['commodity_id']
				)
		));

		$rentOfficeHours = $this->Office->getOfficeBusinessHours($data[0]['Reservation']['rent_office_id'],$data[0]['Reservation']['rent_datetime']);
		$data[0]['RentOfficeHours'] = $rentOfficeHours;

		$returnOfficeHours = $this->Office->getOfficeBusinessHours($data[0]['Reservation']['return_office_id'],$data[0]['Reservation']['return_datetime']);
		$data[0]['ReturnOfficeHours'] = $returnOfficeHours;

		$this->request['data'] = $data[0];


		// 予約クライアントID
		$clientId = $this->request['data']['Client']['id'];
		// オプション（特典・シート）リスト
		$privilegeList = $this->Privilege->getPrivilegeList($clientId);
		// 予約チャイルドシート取得
		$reservationChildSheet = $this->ReservationChildSheet->getReservationChildSheet($id);
		// 予約特典取得
		$reservationPrivilege = $this->ReservationPrivilege->getReservationPrivilege($id);

		$officeList = $this->Office->getAllList($clientId);

		// 車両クラス
		$this->CarClass->recursive = - 1;
		$carClass = $this->CarClass->find('first',array(
				'conditions'=>array(
						'CarClass.id'=>$this->request['data']['CommodityItem']['car_class_id']
				)
		));

		// 車両タイプ
		$this->CarType->recursive = - 1;
		$carType = $this->CarType->find('list');

		// メール
		$this->ReservationMail->recursive = - 1;
		$mails = $this->ReservationMail->find('all',array(
				'conditions'=>array(
						'reservation_id'=>$id
				),
				'order'=>array(
						'id'=>'desc'
				)
		));
		// $mails = $this->ReservationMail->find('first',array('order'=>'id desc'));

		// メール配信チェック
		if($this->DeliveryMail->checkReturnMail($this->request['data']['Reservation'])) {
			$this->set('mailError','1');
		}

		// スタッフ
		$staffList = $this->Staff->find('list');

		// 予約状況
		$statusList = $this->ReservationStatus->find('list');

		// キャンセル理由マスタ
		$cancelReason = $this->CancelReason->find('list',array(
				'fields'=>array(
						'id',
						'reason'
				),
				'conditions'=>array(
						'delete_flg'=>0
				),
				'recursive'=>- 1
		));

		$this->set(compact('officeList','carClass','carType','mails','staffList','statusList','cancelReason','privilegeList','reservationChildSheet','reservationPrivilege'));
	}

	/**
	 * CSVインポート画面
	 * CSVに記述された予約データを一括で修正する機能
	 *
	 * @return void
	 */
	public function import()
	{
		// post以外は画面出すだけ
		if (!$this->request->is('post')) {
			return;
		}

		try {
			// CSVの文字コードをUTF-8に変換
			$tmpFileName = $this->request->data['Reservation']['import_csv']['tmp_name'];
			$text = file_get_contents($tmpFileName);
			setlocale(LC_ALL, 'ja_JP.UTF-8');
			$encoding = mb_detect_encoding($text, 'ASCII,JIS,UTF-8,CP51932,SJIS-win', true);
			if ($encoding === false) {
				throw new Exception('CSVの文字コードが判別できませんでした。');
			}
			if ($encoding !== 'UTF-8') {
				file_put_contents($tmpFileName, mb_convert_encoding($text, 'UTF-8', $encoding));
			}
			unset($text);

			// インポート実行
			list($success, $alert, $errList) = $this->__updateReservationFromCsv($tmpFileName);
		} catch (Exception $e) {
			$alert['message'] = $e->getMessage();
			$alert['class'] = 'alert alert-error';
			$success = false;
		}

		$this->Session->setFlash($alert['message'], 'default', array('class' => $alert['class']));
		if ($success) {
			$this->redirect(array('action' => 'index'));
		}
		$this->set('errList', $errList);
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
		$this->set('datetimeCancelOptions', array(
			'formName' => 'ReservationCancelDate',
			'fieldName' => 'Cancel',
			'dateFormat' => 'YMD',
			'class' => 'form',
			'minYear' => '2013',
			'empty' => '---',
			'setCurrentMonth' => true
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

	protected function _convertDatetime($dateArray) {
		$result = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $dateArray['day'] . ' ' . $dateArray['hour'] . ':' . $dateArray['min'] . ':' . '00';

		return $result;
	}

	/**
	 * 予約一覧csvを出力
	 */
	private function __downloadCsvData($conditions) {
		Configure::write('debug', 0); // debugコードを出さない
		$this->autoRender = false; // Viewを使わない

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

//		// クエリをslaveに向ける
//		foreach ((array)$this->uses as $model) {
//			$this->$model->setDataSource('default_slave');
//		}

		$conditions = $this->Reservation->getReservationData($conditions);
		$count = $this->Reservation->find('count', $conditions);
		$limit = 5000;
		$loop  = ceil($count / $limit);

		if ($count > 0) {
			// キャンセル理由マスタ
			$cancelReason = $this->CancelReason->find('list',array(
					'fields'=>array(
							'id',
							'reason'
					),
					'conditions'=>array(
							'delete_flg'=>0
					),
					'recursive'=>- 1
			));

			$fileName = date('YmdHis') . '.csv';
			$pathFile = TMP.$fileName;

			$csvFile = fopen(TMP.$fileName, "w") or die("Unable to open file!");

			stream_filter_prepend($csvFile, 'convert.iconv.utf-8/cp932//TRANSLIT');

			// ヘッダーを書き込む
			$csvData = 'skyticket申込番号,予約番号,販売方法,レコメンド,レコメンドID,レコメンド手数料率(%),氏名カナ,メールアドレス,ご利用人数(大人),ご利用人数(子供),ご利用人数(幼児),ご利用期間,ステータス,合計金額,コミッション率(%),コミッション料(円),事前決済手数料率(%),事前決済手数料(円),事前決済キャンセル料,お申込み商品,車両クラス,車両タイプ,車両台数,貸出店舗,返却店舗,返信状況,会社名,精算管理会社,IPアドレス,キャリア,申込日時,キャンセル日付,支払方法,入金ステータス,キャンセルタイプ,キャンセル理由,reservation_id' . "\r\n";
			fwrite($csvFile, $csvData);

			for ($i = 0; $i < $loop; $i++){

				$conditions['limit'] = $limit;
				$conditions['offset'] = $limit * $i;

				$reservationData = $this->Reservation->find('all', $conditions);

				$reservationIds = Hash::extract($reservationData, '{n}.Reservation.id');
				$settlementCompanyIds = Hash::extract($reservationData, '{n}.SettlementCompany.id');
				$recommendIds = Hash::extract($reservationData, '{n}.Reservation.recommend_id');

				$cancelDetails = $this->CancelDetail->getCancelFeesGroupByReservationId($reservationIds);
				$rateHistories = $this->CommissionRateHistory->getRateHistoriesBySettlementCompanyIds($settlementCompanyIds);
				$recommends = $this->Recommend->find('list', array(
					'fields' => array('Recommend.id', 'Recommend.recommend_fee'),
					'conditions' => array('Recommend.id' => $recommendIds),
					'recursive' => -1
				));

				foreach ($reservationData as $key => $val) {
					if ($val['Reservation']['mail_status'] == 0) {
						$mailStatus = '未返信';
					} else if($val['Reservation']['mail_status'] == 1) {
						$mailStatus = '返信済み';
					} else if($val['Reservation']['mail_status'] == 2) {
						$mailStatus = '対応完了';
					} else if($val['Reservation']['mail_status'] == 3) {
						$mailStatus = '設定なし';
					}

					$payment_status = !empty($val['Reservation']['payment_status']) ? $val['Reservation']['payment_status'] : '';
					$payment_status = Constant::paymentStatus()[$payment_status];

					if ($val['Commodity']['sales_type'] == Constant::SALES_TYPE_ARRANGED) {
						$paymentMethod = isset($val['Reservation']['payment_status']) ? 'WEB事前決済' : '現地精算';
					} else {
						$paymentMethod = $val['Reservation']['sales_price'] > 0 ? 'WEB事前決済' : '現地精算';
					}

					$year = substr($val[0]['contract_ym'], 0, 4);
					$month = substr($val[0]['contract_ym'], 4);
					$taxRate = $this->TaxRate->getConsumptionTaxRate($year, (int)$month);

					$feeRate = '';
					$fee = '';
					if ($paymentMethod == 'WEB事前決済') {
						$feeRate = $val['SettlementCompany']['fee_rate'];
						$tmp = floor($val['Reservation']['amount'] * $feeRate / 100);
						$fee = $tmp + floor($tmp * ($taxRate - 1.0));
					}

					if (isset($rateHistories[$val[0]['contract_ym']][$val['SettlementCompany']['id']])) {
						$commissionRate = $rateHistories[$val[0]['contract_ym']][$val['SettlementCompany']['id']];
					} else {
						$commissionRate = $val['SettlementCompany']['commission_rate'];
					}
					$commission = floor($val['Reservation']['amount'] * $commissionRate / 100);
					if (!$val['SettlementCompany']['is_internal_tax']) {
						$commission = $commission + floor($commission * ($taxRate - 1.0));
					}

					$csvData = $val['CmThApplicationDetail']['cm_application_id'] . ',' . $val['Reservation']['reservation_key'] . ',' .

						Constant::salesType()[$val['Commodity']['sales_type']] . ',' .

						// 手数料率のカラムは定額の場合の金額も保存可能だが、今後定額レコメンドの予定はないそう
						($val['Reservation']['recommend_id'] ? '対象,' . $val['Reservation']['recommend_id'] . ',' . $recommends[$val['Reservation']['recommend_id']] : '対象外,,') . ',' .

						$val['Reservation']['last_name'] . ' ' . $val['Reservation']['first_name'] . ',' .

						$val['Reservation']['email'] . ',' .

						$val['Reservation']['adults_count'] . ',' . $val['Reservation']['children_count'] . ',' . $val['Reservation']['infants_count'] . ',' .

						$val['Reservation']['rent_datetime'] . '～' . $val['Reservation']['return_datetime'] . ',' .

						$val['ReservationStatus']['name'] . ',' .

						$val['Reservation']['amount'] . ',' .

						$commissionRate . ',' .
						$commission . ',' .

						$feeRate . ',' .
						$fee . ',' .

						(isset($cancelDetails[$val['Reservation']['id']]) ? $cancelDetails[$val['Reservation']['id']] : '') . ',' .

						str_replace(',', '', $val['Commodity']['name']) . ',' .
						str_replace(',', '', $val['CarClasses']['name']) . ',' . $val['CarType']['name'] . ',' .

						$val['Reservation']['cars_count'] . ',' .

						$val['RentOffices']['name'] . ',' . $val['ReturnOffices']['name'] . ',' .

						$mailStatus . ',' . $val['Client']['name'] . ',' . $val['SettlementCompany']['name'] . ',' . $val['Reservation']['user_session_id'] . ',' . uaCheck($val['Reservation']['user_agent']) . ',' . $val['Reservation']['created'] . ',' . $val['Reservation']['cancel_datetime'] . ',' .

						$paymentMethod . ',' .
						
						$payment_status . ',' .

						($val['Reservation']['cancel_flg'] ? $this->cancelType[$val['Reservation']['cancel_reason_id']] : '') . ',' .

						($val['Reservation']['cancel_flg'] ? $cancelReason[$val['Reservation']['cancel_reason_id']] : '') . ',' .

						$val['Reservation']['id'] . ',' . "\r\n";

					fwrite($csvFile, $csvData);
				}
			}

			fclose($csvFile);

			header("Content-disposition: attachment; filename=" . $fileName);
			header("Content-type: application/octet-stream; name=" . $fileName);
			readfile($pathFile);
			unlink ($pathFile);
		}

		exit();
	}

	/**
	 * キャンセル一覧csvを出力
	 */
	private function __downloadCsvCancelData($conditions) {
		Configure::write('debug', 0); // debugコードを出さない
		$this->autoRender = false; // Viewを使わない

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

//		// クエリをslaveに向ける
//		foreach ((array)$this->uses as $model) {
//			$this->$model->setDataSource('default_slave');
//		}
		$reservationData = $this->Reservation->find('all', $this->Reservation->getReservationData($conditions));
		foreach ((array)$this->uses as $model) {
			$this->$model->setDataSource('default');
		}
		
		/**
		 * CSVの処理
		 */

		$csvFile = date('YmdHis') . '.csv';

		// ヘッダ出力
		header("Content-disposition: attachment; filename=" . $csvFile);
		header("Content-type: application/octet-stream; name=" . $csvFile);
		
		// ストリーム出力
		$fp = @fopen('php://output', 'w');
		if (!$fp) {
			exit;
		}
		
		// SJIS指定
		stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

		$csvData = '予約番号,会社名,プラン,車両クラス,車両タイプ,出発店舗,返却店舗,予約台数,合計金額,ご利用期間,申込日時,' . 'キャンセル日時,キャンセル理由,キャンセル理由詳細' . "\r\n";
		fwrite($fp, $csvData);
		
		foreach($reservationData as $key => $val) {

			$csvData = $val['Reservation']['reservation_key'] . ',' .

			$val['Client']['name'] . ',' .

			$val['Commodity']['name'] . ',' .

			str_replace(',', '', $val['CarClasses']['name']) . ',' . $val['CarType']['name'] . ',' .

			$val['RentOffices']['name'] . ',' .

			$val['ReturnOffices']['name'] . ',' .

			$val['Reservation']['cars_count'] . ',' .

			$val['Reservation']['amount'] . ',' .

			$val['Reservation']['rent_datetime'] . '～' . $val['Reservation']['return_datetime'] . ',' .

			$val['Reservation']['created'] . ',' .

			$val['Reservation']['cancel_datetime'] . ',' .

			$val['CancelReason']['reason'] . ',' . str_replace(array(
				"\r\n",
				"\r",
				"\n"
			), '', $val['Reservation']['cancel_remark']) . ',' . "\r\n";

			fwrite($fp, $csvData);
		}

		fclose($fp);
		exit();
	}

	public function getPrivilegeArray() {
		$this->CommodityPrivilege->recursive = - 1;
		$privilege = $this->CommodityPrivilege->getCommodityPrivilege($this->request['data']['Reservation']['commodity_item_id']);

		$privileges = array();
		if (!empty($privilege)) {
			foreach ($privilege as $key => $pVal) {

				$max = array();
				for ($i = 0; $i <= $pVal['Privilege']['maximum']; $i ++) {
					$max[$i] = $i;
				}
				$privileges[$key]['max'] = $max;
				$privileges[$key]['id'] = $pVal['Privilege']['id'];

				if (!empty($this->request['data']['ReservationPrivilege'])) {
					foreach ($this->request['data']['ReservationPrivilege'] as $val) {

						if ($val['privilege_id'] == $pVal['Privilege']['id']) {
							$privileges[$key]['name'] = $pVal['Privilege']['name'];
							$privileges[$key]['count'] = $val['count'];
						}
					}
				}

				if (empty($privileges[$key]['name'])) {
					$privileges[$key]['name'] = $pVal['Privilege']['name'];
					$privileges[$key]['count'] = 0;
				}
			}
		}

		return $privileges;
	}

	/**
	 * CSVを読み込んでRservationsテーブルを更新する
	 *
	 * @param string $tmpFileName
	 * @return array
	 */
	private function __updateReservationFromCsv($tmpFileName)
	{
		$lineNo   = 0;
		$errList  = [];

		$file = fopen($tmpFileName, 'r');
		while ($line = fgetcsv($file, 0, ',')) {
			$lineNo++;
			if ($lineNo === 1) continue; // ヘッダ行ではDB更新に入らない
			if ($line === [null]) continue; // 空行はスキップ

			try {
				/* CSVから取得した値のバリデーション (save()で拾えないもの) */
				// カラム数のチェック
				if (count($line) !== 3) {
					throw new Exception('項目が足りません。');
				}
				// 予約ステータスのチェック
				$line['reservation_status_id'] = array_search($line[1], $this->ReservationStatus->getReservationStatuses());
				if ($line['reservation_status_id'] === false) {
					throw new Exception('予約ステータスが無効な値です。');
				}
				/* 更新対象がテーブルに存在するか */
				$reservation = $this->Reservation->find('first', array('conditions' => array('reservation_key' => $line[0])));
				if ($reservation === []) {
					throw new Exception('該当するデータが存在しません。予約番号を確認してください。');
				}

				/* 更新が必要なデータか */
				if (
					(string)$line['reservation_status_id'] === $reservation['Reservation']['reservation_status_id']
					&& $line[2] === $reservation['Reservation']['amount']
				) {
					throw new Exception('登録されているデータと差分がないため更新しませんでした。');
				}

				/* 更新フィールドの値セット */
				$data = array(
					'id' => $reservation['Reservation']['id'],
					'staff_id' => $this->cdata['id']
				);

				// 金額修正の場合
				if ($line[2] !== (string)$reservation['amount']) {
					$data['amount'] = $line[2];
				}

				// 予約ステータス更新の場合
				if (
					($line['reservation_status_id'] == 3)
					&& ($line['reservation_status_id'] !== $reservation['Reservation']['reservation_status_id'])
				) {
					// キャンセルに変更
					$data['reservation_status_id'] = $line['reservation_status_id'];
					$data['cancel_flg'] = 1;
					$data['cancel_datetime'] = DboSource::expression('NOW()');
					$data['cancel_staff_id'] = $this->cdata['id'];
					$data['cancel_reason_id'] = 5;
					// webから事前決済した予約の時は支払いステータスも変更
					if (!is_null($reservation['Reservation']['payment_status'])) {
						$data['payment_status'] = 'REFUND_REQUEST';
					}
				} elseif (
					// キャンセル取り消し
					($reservation['Reservation']['reservation_status_id'] == 3)
					&& ($line['reservation_status_id'] !== $reservation['Reservation']['reservation_status_id'])
				) {
					$data['reservation_status_id'] = $line['reservation_status_id'];
					$data['cancel_flg'] = 0;
					$data['cancel_datetime'] = '0000-00-00 00:00:00';
					$data['cancel_staff_id'] = 0;
					$data['cancel_reason_id'] = 0;
					$data['cancel_remark'] = '';
					// webから事前決済した予約の時は支払いステータスも変更
					if (!is_null($reservation['Reservation']['payment_status'])) {
						$data['payment_status'] = 'PAYED';
					}
				}

				/* テーブル更新 */
				unset($this->Reservation->validate['cancel_datetime']);
				unset($this->Reservation->validate['cancel_remark']);
				if (!$this->Reservation->save($data, true)) {
					throw new Exception(implode('<br>', array_unique(Hash::extract($this->Reservation->validationErrors, '{s}.{n}'))));
				}
			} catch (Exception $e) {
				$line['no'] = $lineNo;
				$line['errors'] = $e->getMessage();
				$errList[] = $line;
			}
		}
		fclose($file);

		// アラートの表示内容を設定
		$success = false;
		$alert = array();
		if ($lineNo <= 1) {
			$alert['message'] = 'CSVファイルが空です。';
			$alert['class'] = 'alert alert-warning';
		} elseif ($errList === []) {
			$success = true;
			$alert['message'] = 'インポートが完了しました。';
			$alert['class'] = 'alert alert-success';
		} else {
			$alert['message'] = '取り込めなかったデータがあります。内容をご確認ください。';
			$alert['class'] = 'alert alert-error';
		}

		return array($success, $alert, $errList);
	}

	/**
     * cm_application_idから該当する予約IDを取得し、予約詳細画面にリダイレクトする
	 * 
     * @return void
     */
    public function redirectEditForm($cmApplicationId)
    {
		$reservationId = $this->CmThApplicationDetail->getApplicationIdByCmApplicationId($cmApplicationId);
        if ($reservationId) {
            $clientId = $this->Reservation->getClientId($reservationId);
            $this->redirect("../../client/reservations/edit/$reservationId?cid=$clientId");
        } else {
            $this->Session->setFlash('予約内容が見つかりませんでした', 'default', array('class' => 'alert alert-error'));
            $this->redirect('../../client/reservations/');
        }
    }
}

<?php
ini_set("max_execution_time",60);
App::uses('AppController', 'Controller');
App::uses('SkyticketCakeEmail', 'Vendor');
require_once("mailsend_class.php");
/**
 * SettlementSummary Controller
 *
 * @property SettlementSummary $SettlementSummary
 */
class SettlementSummaryController extends AppController {

	public $components = array('SettlementData');
	public $uses = array('SettlementSummary', 'Client', 'SettlementCompany', 'SettlementSummaryNextAdjustment', 'PublicHoliday', 'Reservation', 'CommissionRateHistory', 'Recommend', 'CancelDetail', 'SettlementSummarySalesPerformance', 'SettlementSummaryDetail', 'SettlementSummaryCloseData', 'SettlementSummaryCancelData');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->request->data['SettlementSummary'] = $this->request->query;

		// 計上月セレクトボックス(年)の選択肢を作成
		$oldestSettlementSummary = $this->SettlementSummary->find('first', [
			'recursive' => -1,
			'conditions' => array('latest_flg' => 1),
			'order' => 'settlement_month asc'
		]);

		$minYear = date('Y');
		if (!empty($oldestSettlementSummary)) {
			$oldestSettlementMonth = str_split($oldestSettlementSummary['SettlementSummary']['settlement_month'], 4);
			$minYear = $oldestSettlementMonth[0];
		}

		$this->set('settlementYear', array(
			'formName' => 'SettlementSummary',
			'fieldName' => 'settlement_year',
			'dateFormat' => 'Y',
			'class' => 'span4',
			'minYear' => $minYear,
			'maxYear' => date('Y'),
			'empty' => '---',
			'setCurrentMonth'=>false
		));

		$this->set('settlementMonth', array(
			'formName' => 'SettlementSummary',
			'fieldName' => 'settlement_month',
			'dateFormat' => 'M',
			'class' => 'span3',
			'empty' => '---',
			'setCurrentMonth'=>false
		));

		$conditions['SettlementSummary.latest_flg'] = true;

		// 経理用管理コード
		if (isset($this->request->query['accounting_code']) && $this->request->query['accounting_code'] != '') {
			$conditions['SettlementCompany.accounting_code'] = $this->request->query['accounting_code'];
		}
		// 精算管理会社
		if (!empty($this->request->query['settlement_company_id'])) {
			$conditions['SettlementCompany.id'] = $this->request->query['settlement_company_id'];
		}
		// クライアント
		if (!empty($this->request->query['client_id'])) {
			// クライアントIDが一致する経理用管理コードを条件にする
			$settlementCompany = $this->SettlementCompany->find('list', [
				'fields' => array(
					'SettlementCompany.accounting_code',
				),
				'recursive' => -1,
				'conditions' => array('SettlementCompany.client_id' => $this->request->query['client_id']),
			]);
			if (!empty($settlementCompany)) {
				if (count($settlementCompany) >= 2) {
					$conditions['SettlementCompany.accounting_code in'] = $settlementCompany;
				} else {
					$conditions['SettlementCompany.accounting_code'] = $settlementCompany;
				}
			} else {
				$conditions['SettlementCompany.accounting_code'] = null;
			}
		}
		// 計上月
		if (!empty($this->request->query['settlement_year']) && !empty($this->request->query['settlement_month']) && $this->request->query['settlement_year']['year'] != '' && $this->request->query['settlement_month']['month'] != '') {
			$conditions['SettlementSummary.settlement_month'] = implode('', [$this->request->query['settlement_year']['year'], $this->request->query['settlement_month']['month']]);
		} else {
			// 計上年
			if (!empty($this->request->query['settlement_year']) && $this->request->query['settlement_year']['year'] != '') {
				$conditions['SettlementSummary.settlement_month like'] = $this->request->query['settlement_year']['year'].'%';
			}
			// 計上月
			if (!empty($this->request->query['settlement_month']) && $this->request->query['settlement_month']['month'] != '') {
				$conditions['SettlementSummary.settlement_month like'] = '%'.$this->request->query['settlement_month']['month'];
			}
		}

		// 初期アクセス時のパラメータセット
		if (count($this->request->query) == 0) { 

			// 最新PDFを取得
			$latestSettlementSummary = $this->SettlementSummary->find('first', [
				'recursive' => -1,
				'conditions' => array('latest_flg' => 1),
				'order' => 'settlement_month desc'
			]);

			if (!empty($latestSettlementSummary)) {
				$conditions['SettlementSummary.settlement_month'] = $latestSettlementSummary['SettlementSummary']['settlement_month'];
				$settlementMonth = str_split($latestSettlementSummary['SettlementSummary']['settlement_month'], 4);
				$this->request->data['SettlementSummary']['settlement_year']['year'] = $settlementMonth[0];
				$this->request->data['SettlementSummary']['settlement_month']['month'] = $settlementMonth[1];
			}
		}

		$this->Paginator->settings = ['SettlementCompany' =>
			[
				'recursive' => -1,
				'fields' => array(
					'SettlementSummary.*',
					'SettlementCompany.*',
					"SUBSTRING_INDEX(GROUP_CONCAT(SettlementCompany.name order by SettlementCompany.id), ',', 1) as settlementCompanyName",
					'GROUP_CONCAT(distinct(Client.name) order by Client.id) as clientName'
				),
				'joins'=>array(
					array(
						'type'=>'LEFT',
						'alias'=>'SettlementSummary',
						'table'=>'settlement_summaries',
						'conditions'=>'SettlementCompany.accounting_code = SettlementSummary.settlement_company_accounting_code',
					),
					array(
						'type'=>'LEFT',
						'alias'=>'Client',
						'table'=>'clients',
						'conditions'=>'SettlementCompany.client_id = Client.id',
					)
				),
				'group' => 'SettlementSummary.id',
				'conditions' => $conditions,
				'order' => 'SettlementSummary.synchronization_status asc, SettlementSummary.create_datetime desc',
			]
		];

		$this->set('settlementSummaries', $this->Paginator->paginate('SettlementCompany'));

		$settlementCompanyList = $this->SettlementCompany->find('list');
		$clientList = $this->Client->find('list');
		$this->set(compact('settlementCompanyList', 'clientList'));
	}

	/**
	 * detail method
	 *
	 * @return void
	 */
	public function detail($settlementCompanyAccountingCode) {
		// 最新精算書の年月度取得
		$todayBusinessDay = $this->PublicHoliday->getBusinessDay();
		if ($todayBusinessDay < Constant::SETTLEMENT_SUMMARY_CREATE_DATE) {
			// 2か月前
			$latestDate = date('Ym', strtotime('first day of -2 month'));
		} else {
			// 1か月前
			$latestDate = date('Ym', strtotime('first day of previous month'));
		}

		if ($this->request->is('post')) {
			$requestData = $this->request->data['SettlementSummary'];
			if ($requestData['action'] == 'NextAdjustmentAdd') {
				// 次月調整追加
				// 余計なデータをinsertさせない
				unset($requestData['action']);

				$this->SettlementSummaryNextAdjustment->create();
				$this->request->data['SettlementSummaryNextAdjustment'] = $requestData;
				$this->request->data['SettlementSummaryNextAdjustment']['settlement_company_accounting_code'] = $settlementCompanyAccountingCode;
				$this->request->data['SettlementSummaryNextAdjustment']['status'] = 'NEW';
				$this->request->data['SettlementSummaryNextAdjustment']['settlement_month'] = $this->request->data['SettlementSummary']['settlement_year'].$this->request->data['SettlementSummary']['settlement_month'];
				$this->request->data['SettlementSummaryNextAdjustment']['create_datetime'] = date("Y-m-d H:i:s");
				$this->request->data['SettlementSummaryNextAdjustment']['create_staff_id'] = $this->cdata['id'];
				$this->request->data['SettlementSummaryNextAdjustment']['update_datetime'] = date("Y-m-d H:i:s");
				$this->request->data['SettlementSummaryNextAdjustment']['update_staff_id'] = $this->cdata['id'];

				if ($this->SettlementSummaryNextAdjustment->save($this->request->data)) {
					$this->Session->setFlash('次月調整を登録しました','default',array('class'=>'alert alert-success'));
					$this->redirect(array('action' => 'detail/'.$settlementCompanyAccountingCode));
				} else {
					$err_msg = '次月調整の登録に失敗しました';
					if (isset($this->SettlementSummaryNextAdjustment->validationErrors)) {
						$errors = $this->SettlementSummaryNextAdjustment->validationErrors;
						foreach ($errors as $error) {
							$err_msg .= '<br>'.$error[0];
						}
					}
					$this->Session->setFlash($err_msg,'default',array('class'=>'alert alert-error'));
				}
			} elseif ($this->request->data['SettlementSummary']['action'] == 'Recreate') {
				// 再発行
				$this->recreate($settlementCompanyAccountingCode, $requestData['latestId'], $latestDate, $requestData['payment_limit_datetime']);
				$paymentLimitDatetime = $requestData['payment_limit_datetime'];
			}
		}

		$settlementCompany = $this->SettlementCompany->find('all', [
			'fields' => [
				'Client.name',
				'recreate_limit_flg'
			],
			'recursive' => 1,
			'conditions' => array('SettlementCompany.accounting_code' => $settlementCompanyAccountingCode),
			'order' => 'SettlementCompany.client_id'
		]);

		// 表示に使用するデータ整形
		$clientName = [];
		$isUnlimited = false;
		foreach ($settlementCompany as $row) {
			$clientName[] = $row['Client']['name'];
			if ($row['SettlementCompany']['recreate_limit_flg']) {
				$isUnlimited = true;
			}
		}
		// 再発行可能フラグ
		$isVisible = true;
		if (!$isUnlimited) {
			if (!($todayBusinessDay >= Constant::SETTLEMENT_SUMMARY_CREATE_DATE && $todayBusinessDay < Constant::SETTLEMENT_CLOSING_DATE)) {
				$isVisible = false;
			}
		}

		$settlementCompanyName = $this->SettlementCompany->find('first', [
			'fields' => [
				'SettlementCompany.name',
			],
			'recursive' => -1,
			'conditions' => array('SettlementCompany.accounting_code' => $settlementCompanyAccountingCode),
			'order' => 'SettlementCompany.id'
		]);

		// 最新精算書IDの取得(再発行対象)
		$options = array(
			'fields' => array('id'),
			'conditions' => array(
				'settlement_company_accounting_code' => $settlementCompanyAccountingCode,
				'settlement_month =' =>  $latestDate,
				'latest_flg' => true,
				'delete_flg' => false,
			),
			'recursive' => -1,
		);
		$latestData = $this->SettlementSummary->find('first',$options);
		if (!empty($latestData)) {
			$latestId = $latestData['SettlementSummary']['id'];
		} else {
			$latestId = '';
		}

		// 差込年月のoption用配列作成
		$settlementYear = [];
		$year = substr($latestDate, 0, 4);
		for ($i=1; $i <= 3; $i++) {
			$settlementYear[$year] = $year;
			$year++;
		}
		$settlementMonth = [];
		for ($month=1; $month <= 12; $month++) { 
			$month = sprintf('%02d',$month);
			$settlementMonth[$month] = $month;
		}

		// 次月調整取得
		if ($isVisible) {
			// 未使用のもの、今月使ったもの、今月以降差し込み予定だったが削除したもの
			$nextAdjustmentsConditions = array(
				'SettlementSummaryNextAdjustment.settlement_company_accounting_code' => $settlementCompanyAccountingCode,
				'SettlementSummaryNextAdjustment.status' => array('NEW', 'USED'),
				'NOT' => array(
					'AND' => array(
						'SettlementSummaryNextAdjustment.status' => 'NEW',
						'SettlementSummaryNextAdjustment.delete_flg' => '1',
						'SettlementSummaryNextAdjustment.settlement_month < ' => $latestDate
					)
				)
			);
		} else {
			// 未使用のもの、来月以降使用予定だったが削除したもの
			$nextAdjustmentsConditions = array(
				'SettlementSummaryNextAdjustment.settlement_company_accounting_code' => $settlementCompanyAccountingCode,
				'SettlementSummaryNextAdjustment.status' => 'NEW',
				'NOT' => array(
					'AND' => array(
						'SettlementSummaryNextAdjustment.status' => 'NEW',
						'SettlementSummaryNextAdjustment.delete_flg' => '1',
						'SettlementSummaryNextAdjustment.settlement_month <= ' => $latestDate
					)
				)
			);
		}
		$settlementSummaryNextAdjustments = $this->SettlementSummaryNextAdjustment->find('all', [
			'recursive' => 1,
			'conditions' => $nextAdjustmentsConditions,
			'order' => ['SettlementSummaryNextAdjustment.settlement_month' => 'asc', 'SettlementSummaryNextAdjustment.id' => 'asc']
		]);

		// 生成済PDF一覧取得
		$this->Paginator->settings = [
			'SettlementSummary' =>
			[
				'recursive' => 0,
				'fields' => [
					'SettlementSummary.*',
				],
				'conditions' => [
					'SettlementSummary.latest_flg' => true,
					'SettlementSummary.settlement_company_accounting_code' => $settlementCompanyAccountingCode
				],
				'order' => 'settlement_month desc',
				'limit' => 20,
				'paramType' => 'querystring'
			]
		];

		$data = [
			'settlementCompanyName' => $settlementCompanyName['SettlementCompany']['name'],
			'clientName' => implode('/', $clientName),
			'isVisible' => $isVisible,
			'latestId' => $latestId,
			'settlementMonth' => $settlementMonth,
			'settlementYear' => $settlementYear,
			'paymentLimitDatetime' => $paymentLimitDatetime,
			'latestDate' => $latestDate
		];

		$this->set('settlementSummaries', $this->Paginator->paginate('SettlementSummary'));

		$this->set(compact('data', 'settlementCompany', 'settlementSummaryNextAdjustments'));
	}

	/**
	 * 登録済み次月調整編集
	 * 
	 * @return void
	 */
	public function edit() {
		$this->request->data['SettlementSummaryNextAdjustment']['update_datetime'] = date("Y-m-d H:i:s");
		$this->request->data['SettlementSummaryNextAdjustment']['update_staff_id'] = $this->cdata['id'];
		if ($this->SettlementSummaryNextAdjustment->save($this->request->data['SettlementSummaryNextAdjustment'])) {
			$this->Session->setFlash('次月調整を更新しました','default',array('class'=>'alert alert-success'));
		} else {
			$err_msg = '次月調整の更新に失敗しました';
			if (isset($this->SettlementSummaryNextAdjustment->validationErrors)) {
				$errors = $this->SettlementSummaryNextAdjustment->validationErrors;
				foreach ($errors as $error) {
					$err_msg .= '<br>'.$error[0];
				}
			}
			$this->Session->setFlash($err_msg,'default',array('class'=>'alert alert-error'));
		}

		$settlementSummaryNextAdjustment = $this->SettlementSummaryNextAdjustment->find('first', [
			'recursive' => -1,
			'conditions' => [
				'SettlementSummaryNextAdjustment.id' => $this->request->data['SettlementSummaryNextAdjustment']['id'],
			],
		]);
		$this->redirect('/SettlementSummary/detail/'.$settlementSummaryNextAdjustment['SettlementSummaryNextAdjustment']['settlement_company_accounting_code']);
	}

	/**
	 * 登録済み次月調整削除(論理削除)
	 * 
	 * @return void
	 */
	public function delete($id) {
		$deleteParams = [
			'id' => $id,
			'delete_flg' => 1,
			'update_datetime' => date("Y-m-d H:i:s"),
			'update_staff_id' => $this->cdata['id']
		];

		if ($this->SettlementSummaryNextAdjustment->save($deleteParams)) {
			$this->Session->setFlash('次月調整を削除しました','default',array('class'=>'alert alert-success'));
		} else {
			$this->Session->setFlash('次月調整の削除に失敗しました','default',array('class'=>'alert alert-error'));
		}

		$settlementSummaryNextAdjustment = $this->SettlementSummaryNextAdjustment->find('first', [
			'recursive' => -1,
			'conditions' => [
				'SettlementSummaryNextAdjustment.id' => $id,
			],
		]);
		$this->redirect('/SettlementSummary/detail/'.$settlementSummaryNextAdjustment['SettlementSummaryNextAdjustment']['settlement_company_accounting_code']);
	}

	/**
	 * 同期処理
	 *
	 * @param string $id
	 * @return void
	 */
	public function synchronization($id) {
		$synchronizationParams = [
			'id' => $id,
			'synchronization_status' => 'SYNCHRONIZED',
			'synchronization_datetime' => date("Y-m-d H:i:s"),
			'update_datetime' => date("Y-m-d H:i:s"),
			'update_staff_id' => $this->cdata['id']
		];

		try {
			// 同期する対象を取得
			$settlementSummary = $this->SettlementSummary->find('first', [
				'recursive' => -1,
				'conditions' => [
					'SettlementSummary.id' => $id,
				],
			]);
			$options = array(
				'fields' => array('SettlementCompanyStaffs.staff_id'),
				'joins' => array(
					array(
						'type' => 'LEFT',
						'alias' => 'SettlementCompanyStaffs',
						'table' => 'settlement_company_staffs',
						'conditions' => 'SettlementCompany.id = SettlementCompanyStaffs.settlement_company_id',
					),

				),
				'conditions' => array('accounting_code' => $settlementSummary['SettlementSummary']['settlement_company_accounting_code']),
				'recursive' => -1
			);
			$settlementCompanyStaffIdList = $this->SettlementCompany->find('list', $options);

			if (in_array(null, $settlementCompanyStaffIdList, true)) {
				$this->Session->setFlash('担当者が設定されていない精算管理会社が存在するため同期に失敗しました', 'default', array('class' => 'alert alert-error'));
				$this->redirect($this->referer());
			}

			$this->SettlementSummary->begin();

			if ($this->SettlementSummary->save($synchronizationParams)) {

				// 同経理用管理コードで同精算年月度のID取得
				$settlementSummaryIdList = $this->SettlementSummary->find('list', [
					'fields' => [
						'SettlementSummary.id',
					],
					'recursive' => -1,
					'conditions' => [
						'SettlementSummary.id !=' => $id,
						'settlement_month' => $settlementSummary['SettlementSummary']['settlement_month'],
						'settlement_company_accounting_code' => $settlementSummary['SettlementSummary']['settlement_company_accounting_code'],
					],
				]);

				if (!empty($settlementSummaryIdList)) {
					// 同経理用管理コードで同精算年月度のデータを論理削除する
					$this->SettlementSummary->updateAll([
						'delete_flg' => true,
						'update_datetime' => "'" . date("Y-m-d H:i:s") . "'",
						'update_staff_id' => $this->cdata['id'],
					], [
						'id' => $settlementSummaryIdList
					]);

					// 精算書成約データ物理削除
					$this->SettlementSummaryCloseData->deleteAll([
						'SettlementSummaryCloseData.settlement_summary_id' => $settlementSummaryIdList,
					]);

					// 精算書キャンセルデータ物理削除
					$this->SettlementSummaryCancelData->deleteAll([
						'SettlementSummaryCancelData.settlement_summary_id' => $settlementSummaryIdList
					]);
				}

				$this->SettlementSummary->commit();

				// メール送信
				$this->sendSynchronizationMail($id);
				$this->Session->setFlash('精算書を同期しました', 'default', array('class' => 'alert alert-success'));
			} else {
				$this->Session->setFlash('精算書の同期に失敗しました', 'default', array('class' => 'alert alert-error'));
			}
		} catch (Exception $e) {
			$this->SettlementSummary->rollback();
			$this->Session->setFlash('精算書の同期に失敗しました', 'default', array('class' => 'alert alert-error'));
		}
		$this->redirect($this->referer());
	}

	/**
	 * 同期完了メール送信
	 *
	 * @param string $settlementSummaryId
	 * @return void
	 */
	private function sendSynchronizationMail($settlementSummaryId)
	{
		$subject = 'skyticketご精算書につきまして';
		$attachment_arr = array();
		$add_header_arr['charset'] = 'ISO-2022-JP';
		$add_header_arr['From'] = EMAIL_ADDRESS_RENTACAR;
		$add_header_arr['Cc'] = EMAIL_ADDRESS_RENT_A_CAR . ',' . EMAIL_ADDRESS_KEIRI;
		$domain = IS_PRODUCTION ? 'skyticket.jp' : 'jp.skyticket.jp';

		$settlementCompanies = $this->SettlementSummary->getSynchronizationSettlementCompanyData($settlementSummaryId);
		foreach ($settlementCompanies as $settlementCompany) {
			$to = $settlementCompany[0]['billing_email'];

			// 請求と支払いで文言を変える
			$message = '内容に問題がなければ記載の期日までに';
			if ($settlementCompany['SettlementSummary']['document_status'] == 'INVOICE') {
				$message .= '弊社振込先へご入金頂き、';
			} else {
				$message .= '指定のお振込先へご入金させて頂きますので、';
			}
			$message .= '相違がございます場合はお手数ですが以下のメールアドレスまでご連絡下さい。';

			// メール本文
			$body = '';
			$body .= $settlementCompany['SettlementCompany']['name'] . PHP_EOL;
			$body .= '精算ご担当者様' . PHP_EOL;
			$body .= '' . PHP_EOL;
			$body .= 'いつもお世話になっております。' . PHP_EOL;
			$body .= 'スカイチケットレンタカーサポートセンターでございます。' . PHP_EOL;
			$body .= '' . PHP_EOL;
			$body .= '先月返却分の精算書が発行されましたので、以下のURLよりご確認をお願いいたします。' . PHP_EOL;
			$body .= '管理画面URL: https://' . $domain . '/rentacar/client/users/login' . PHP_EOL;
			$body .= '' . PHP_EOL;
			$body .= $message . PHP_EOL;
			$body .= EMAIL_ADDRESS_RENT_A_CAR . PHP_EOL;
			$body .= '※第4営業日以内のご連絡:当月分にて再発行処理' . PHP_EOL;
			$body .= '※第5営業日以降のご連絡:次月分にて調整処理' . PHP_EOL;
			$body .= '' . PHP_EOL;
			$body .= '今後とも何卒、宜しくお願い申し上げます。' . PHP_EOL;
			$body .= '' . PHP_EOL;
			$body .= '※このメールは送信専用です。返信いただきましても当社には届きません。' . PHP_EOL;
			$body .= '' . PHP_EOL;
			$body .= '----------------------------------------------------------------------------------------------------' . PHP_EOL;
			$body .= '株式会社アドベンチャー' . PHP_EOL;
			$body .= 'スカイチケットレンタカーサポートセンター' . PHP_EOL;
			$body .= 'https://' . $domain . PHP_EOL;
			$body .= COMPANY_ADDRESS . PHP_EOL;
			$body .= 'TEL:'. ADV_SETTLEMENT_TEL . ' 受付時間 10:00〜19:00（土日祝祭日を除く）' . PHP_EOL;
			$body .= 'MAIL: ' . EMAIL_ADDRESS_RENT_A_CAR . PHP_EOL;
			$body .= '----------------------------------------------------------------------------------------------------' . PHP_EOL;

			$mailsend = new MailSend();
			$mailsend->sendAndSave($to, $subject, $body, $attachment_arr, $add_header_arr, array(), array());
		}
	}

	/**
	 * preview method
	 *
	 * @return void
	 */
	public function preview($settlementSummaryId) {
		$this->download($settlementSummaryId, true);
	}

	/**
	 * download method
	 *
	 * @return void
	 */
	public function download($settlementSummaryId, $previewFlg=false) {
		$this->set('previewFlg', $previewFlg);
		// 精算会社、精算書の日付など
		$options = array(
			'fields' => array(
				'SettlementSummary.settlement_month',
				'SettlementSummary.notification_datetime',
				'SettlementSummary.document_status',
				'SettlementSummary.payment_limit_datetime',
				'SettlementSummary.amount',
				'SettlementSummary.id',
				'SettlementSummary.to_name',
				'SettlementSummary.settlement_company_accounting_code',
				'SettlementSummary.settlement_company_is_internal_tax',
				'SettlementSummary.settlement_company_bank_name',
				'SettlementSummary.settlement_company_bank_branch_name',
				'SettlementSummary.settlement_company_account_type',
				'SettlementSummary.settlement_company_account_number',
				'SettlementSummary.settlement_company_account_holder',
				'SettlementSummary.adv_company_zip',
				'SettlementSummary.adv_company_address',
				'SettlementSummary.adv_company_address_other',
				'SettlementSummary.adv_company_name_japanese',
				'SettlementSummary.adv_display_fax',
				'SettlementSummary.adv_settlement_tel',
				'SettlementSummary.adv_bank_name',
				'SettlementSummary.adv_bank_branch_name',
				'SettlementSummary.adv_account_type',
				'SettlementSummary.adv_account_number',
				'SettlementSummary.adv_account_holder',
			),
			'conditions' => array(
				'SettlementSummary.id' => $settlementSummaryId,
			),
			'recursive' => -1,
		);
		$settlementTopData = $this->SettlementSummary->find('first', $options);
		$this->set('settlementTopData', $settlementTopData);

		// 内税外税取得
		$isInternalTax = $settlementTopData['SettlementSummary']['settlement_company_is_internal_tax'];
		$this->set('isInternalTax', $isInternalTax);

		// 販売実績部分
		$options = array(
			'fields' => array(
				'item_code',
				'item_name',
				'count',
				'amount',
			),
			'conditions' => array(
				'settlement_summary_id' => $settlementSummaryId,
			),
			'order' => array('sort_no' => 'ASC'),
			'recursive' => -1,
		);
		$settlementSalesPerformanceData = $this->SettlementSummarySalesPerformance->find('all', $options);
		$this->set('settlementSalesPerformanceData', $settlementSalesPerformanceData);

		// 精算内容詳細部分
		$options = array(
			'fields' => array(
				'item_code',
				'item_name',
				'count',
				'commission_rate',
				'payment_amount',
				'billing_amount',
			),
			'conditions' => array(
				'settlement_summary_id' => $settlementSummaryId,
			),
			'order' => array('sort_no' => 'ASC'),
			'recursive' => -1,
		);
		$settlementDetailData = $this->SettlementSummaryDetail->find('all', $options);
		$this->set('settlementDetailData', $settlementDetailData);

		// 住所とか
		$this->set('COMPANY_ZIP', $settlementTopData['SettlementSummary']['adv_company_zip']);
		$this->set('COMPANY_ADDRESS', $settlementTopData['SettlementSummary']['adv_company_address']);
		$this->set('COMPANY_ADDRESS_OTHER', $settlementTopData['SettlementSummary']['adv_company_address_other']);
		$this->set('ADV_COMPANY_NAME_JAPANESE', $settlementTopData['SettlementSummary']['adv_company_name_japanese']);
		$this->set('ADV_SETTLEMENT_TEL', $settlementTopData['SettlementSummary']['adv_settlement_tel']);
		$this->set('ADV_DISPLAY_FAX', $settlementTopData['SettlementSummary']['adv_display_fax']);

		// 請求/支払で分岐する文言,項目名など
		$toName = $settlementTopData['SettlementSummary']['to_name'];
		$this->set('toName', $toName);

		if($settlementTopData['SettlementSummary']['document_status'] == 'INVOICE'){
			$documentName = '請求';
			$documentString = 'ご請求';
			$amountName = '請求';
			$limitName = 'お支払い期限';
			$notificationName = '請求';
			$bankName = $settlementTopData['SettlementSummary']['adv_bank_name'].'　'.$settlementTopData['SettlementSummary']['adv_bank_branch_name'];
			$bankNumber = $settlementTopData['SettlementSummary']['adv_account_type'].'　'.$settlementTopData['SettlementSummary']['adv_account_number'];
			$bankHolder = $settlementTopData['SettlementSummary']['adv_account_holder'];
			$transferMessage = '※振込手数料は貴社でご負担願います';
		}else{
			$accountType = Constant::accountType();
			$documentName = '支払通知';
			$documentString = 'お支払い';
			$amountName = '支払';
			$limitName = '支払期限日';
			$notificationName = '通知';
			$bankName = $settlementTopData['SettlementSummary']['settlement_company_bank_name'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . $settlementTopData['SettlementSummary']['settlement_company_bank_branch_name'];
			$bankNumber = $accountType[$settlementTopData['SettlementSummary']['settlement_company_account_type']] . '&nbsp;&nbsp;&nbsp;&nbsp;' . $settlementTopData['SettlementSummary']['settlement_company_account_number'];
			$bankHolder = $settlementTopData['SettlementSummary']['settlement_company_account_holder'];
			$transferMessage = '';
		}
		$this->set('documentName', $documentName);
		$this->set('documentString', $documentString);
		$this->set('amountName', $amountName);
		$this->set('limitName', $limitName);
		$this->set('notificationName', $notificationName);
		$this->set('bankName', $bankName);
		$this->set('bankNumber', $bankNumber);
		$this->set('bankHolder', $bankHolder);
		$this->set('transferMessage', $transferMessage);
		$this->set('taxName', Constant::TAX_NAME);

		// キャンセルデータ
		$options = array(
			'fields' => array(
				'reservation_key',
				'client_name',
				'return_office_name',
				'name',
				'amount',
			),
			'conditions' => array(
				'settlement_summary_id' => $settlementSummaryId,
			),
			'order' => array('id' => 'ASC'),
			'recursive' => -1,
		);
		$settlementCancelData = $this->SettlementSummaryCancelData->find('all', $options);
		$this->set('settlementCancelData', $settlementCancelData);

		// TCPDF設定部分
		App::import('Vendor','TCPDF/tcpdf');
		$tcpdf = new TCPDF();
		$textfont = 'freesans';

		$tcpdf->SetAuthor("");
		$tcpdf->SetAutoPageBreak( false );
		$tcpdf->setHeaderFont(array($textfont,'',10));
		$tcpdf->xheadercolor = array(255,255,255);

		$this->set('tcpdf', $tcpdf);
		$this->render('settlement');
	}

	/**
	 * recreate method
	 *
	 * @return void
	 */
	public function recreate($settlementCompanyAccountingCode, $settlementSummaryId, $latestDate, $paymentLimitDatetime) {
		try {
			if ($paymentLimitDatetime != '') {
				$paymentLimitDatetimeArr = explode('/', $paymentLimitDatetime);
				if (checkdate($paymentLimitDatetimeArr[1], $paymentLimitDatetimeArr[2], $paymentLimitDatetimeArr[0])) {
					if (strtotime(date('Y/m/d')) <= strtotime($paymentLimitDatetime)) {
						$paymentLimitDate = date('Y-m-d 00:00:00', strtotime($paymentLimitDatetime));
					} else {
						throw new Exception('入金締切日に過去を設定できません。');
					}
				} else {
					throw new Exception('入金締切日に不正な値が入力されています。');
				}
				
			} else {
				throw new Exception('入金締切日を入力してください。');
			}

			// 生成対象年月度
			if (!empty($settlementSummaryId)) {
				$options = array(
					'conditions' => array('id' => $settlementSummaryId),
					'recursive' => -1
				);
				$SettlementSummaryData = $this->SettlementSummary->find('first', $options);
				$settlementDate = $SettlementSummaryData['SettlementSummary']['settlement_month'];
			} else {
				$settlementDate = $latestDate;
			}
			preg_match('/^(\d{4})(\d{2})$/', $settlementDate, $settlementMonth);
			$year = $settlementMonth[1];
			$month = $settlementMonth[2];

			// 予約データ取得
			$this->Reservation->recursive = -1;
			$proceeds = $this->Reservation->getProceedsSettlement($year, '', array(), true);
			// 清算管理会社が設定されていない予約(営業店)のチェック
			$checkSettlementCompanyId = $this->SettlementData->__checkSettlementCompanyId($proceeds, $year, $month, true);
			if($checkSettlementCompanyId !== true){
				throw new Exception($checkSettlementCompanyId);
			}
			$commissionRates = $this->SettlementData->getCommissionRates($year);

			// レコメンド
			$recommendSettlement = $this->SettlementData->__getRecommendSettlement($year, $month);

			// 対象の精算管理会社を抽出する
			$options = array(
				'fields' => array('id'),
				'conditions' => array('accounting_code' => $settlementCompanyAccountingCode),
				'recursive' => -1
			);
			$targetSettlementcompanyId = $this->SettlementCompany->find('list',$options);

			// 精算管理会社単位で使う
			$period = array('year'=> $year, 'month' => '', 'day'=> '');
			$dataSettles = $this->SettlementData->__formatDataSettlement($proceeds, $period, $commissionRates, $recommendSettlement, (int)$month, $targetSettlementcompanyId);

			// 合計額を精算管理会社単位から計算する
			$data = $this->SettlementData->__aggregateDataSettlement($dataSettles);

			$this->SettlementData->__addSettlementFormat($data, $dataSettles, (int)$month);
			$this->SettlementData->__addCancelDetailFormat($data, $dataSettles, $year, $month);

			// 次月調整データ取得
			$options = array(
				'fields' => array(
					'id',
					'settlement_company_accounting_code',
					'settlement_month',
					'item_name',
					'count',
					'commission_rate',
					'payment_amount',
					'billing_amount',
				),
				'conditions' => array(
					'delete_flg' => 0,
					'settlement_month <=' => $year. $month,
					'settlement_month !=' => '',
					'settlement_company_accounting_code' => $settlementCompanyAccountingCode,
					'status' => array('NEW', 'USED'),
				),
				'order'=>'id asc',
				'recursive' => -1
			);
			$nextAdjustmentData = $this->SettlementSummaryNextAdjustment->find('all', $options);
			$nextAdjustmentArr = Hash::combine($nextAdjustmentData, '{n}.SettlementSummaryNextAdjustment.id', '{n}.SettlementSummaryNextAdjustment', '{n}.SettlementSummaryNextAdjustment.settlement_company_accounting_code');

			// 精算マスタデータ作成
			$settlementSummaryData = $this->SettlementData->getSettlementSummaryData($dataSettles, $year, (int)$month, $nextAdjustmentArr, $settlementCompanyAccountingCode);
			if (empty($settlementSummaryData)) {
				throw new Exception('計上するデータがありません。');
			}
			// 販売実績,詳細データに必要なクライアント(精算マスタで使用した分)を抽出する
			$accountingCodeArr = $this->SettlementData->getAccountingCodeAllData($settlementSummaryData);
			$clientList = $this->Client->find('list');
			// 販売実績データ作成
			$salesPerformanceData = $this->SettlementData->getSalesPerformanceData($accountingCodeArr, $dataSettles, $clientList, $year, (int)$month);
			// 精算詳細データ作成
			$summaryDetailData = $this->SettlementData->getSummaryDetailData($accountingCodeArr, $dataSettles, $clientList, $year, (int)$month, $period, $commissionRates);

			// 精算書マスタ/販売実績/精算詳細データ保存
			$result = $this->SettlementData->saveSettlementSummaryData($year, $month, $settlementSummaryData, $salesPerformanceData, $summaryDetailData, $nextAdjustmentArr, $this->cdata['id'], $settlementSummaryId, $paymentLimitDatetime);
			if ($result !== true) {
				throw new Exception($result);
			}
			$message = '再発行が完了しました。';
		}  catch (Exception $e) {
			$message = str_replace("\n","<br>",$e->getMessage());
		}
		$this->Session->setFlash($message, 'default', array('class' => 'alert alert-info'));
	}

	/**
	 * closingDownload method
	 *
	 * @return void
	 */
	public function closingDownload($settlementSummaryId) {
		// 精算会社、精算書の日付など
		$options = array(
			'fields' => array(
				'SettlementSummary.to_name',
				'SettlementSummary.settlement_company_accounting_code',
			),
			'conditions' => array(
				'SettlementSummary.id' => $settlementSummaryId,
			),
			'recursive' => -1,
		);
		$settlementTopData = $this->SettlementSummary->find('first', $options);
		$toName = $settlementTopData['SettlementSummary']['to_name'];
		$this->autoRender = false; // Viewを使わない

		// 成約データ
		$options = array(
			'fields' => array(
				'payment_method',
				'reservation_key',
				'client_name',
				'return_office_name',
				'name',
				'amount',
			),
			'conditions' => array(
				'settlement_summary_id' => $settlementSummaryId,
			),
			'order' => array('id' => 'ASC'),
			'recursive' => -1,
		);
		$count = $this->SettlementSummaryCloseData->find('count', $options);
		$limit = 5000;
		$loop  = ceil($count / $limit);

		if ($count > 0) {
			$fileName = $settlementTopData['SettlementSummary']['settlement_company_accounting_code'].$toName.'様成約明細書.csv';

			$pathFile = TMP.$fileName;
			$csvFile = fopen(TMP.$fileName, "w") or die("Unable to open file!");

			stream_filter_prepend($csvFile, 'convert.iconv.utf-8/cp932//TRANSLIT');

			// ヘッダーを書き込む
			$csvData = '支払方法,予約番号,クライアント名,返却店舗名,氏名,合計金額' . "\r\n";
			fwrite($csvFile, $csvData);

			for ($i = 0; $i < $loop; $i++){

				$options['limit'] = $limit;
				$options['offset'] = $limit * $i;

				$settlementCloseData = $this->SettlementSummaryCloseData->find('all', $options);

				foreach ($settlementCloseData as $sk => $sv) {
					$csvData = $sv['SettlementSummaryCloseData']['payment_method'] . ',' .
						$sv['SettlementSummaryCloseData']['reservation_key'] . ',' .
						$sv['SettlementSummaryCloseData']['client_name'] . ',' .
						$sv['SettlementSummaryCloseData']['return_office_name'] . ',' .
						$sv['SettlementSummaryCloseData']['name'] . ',' .
						$sv['SettlementSummaryCloseData']['amount'] . ',' . "\r\n";

					fwrite($csvFile, $csvData);
				}
			}

			fclose($csvFile);

			header("Content-disposition: attachment; filename=" . $fileName);
			header("Content-type: application/octet-stream; name=" . $fileName);
			readfile($pathFile);
			unlink ($pathFile);
			exit();
		}
		$this->Session->setFlash('成約データがありません。', 'default', array('class' => 'alert alert-error'));
		$this->redirect($this->referer());
	}
}

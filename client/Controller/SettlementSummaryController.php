<?php
App::uses('AppController', 'Controller');
/**
 * SettlementSummary Controller
 *
 * @property SettlementSummary $SettlementSummary
 */
class SettlementSummaryController extends AppController {

	public $uses = ['SettlementSummary', 'SettlementCompany', 'SettlementSummarySalesPerformance', 'SettlementSummaryDetail', 'SettlementSummaryCloseData', 'SettlementSummaryCancelData', 'SettlementCompanyStaff', 'CommissionRateHistory'];

	public function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->Paginator->settings = [
			'SettlementSummary' =>
			[
				'recursive' => 0,
				'fields' => [
					'SettlementSummary.*',
				],
				'joins' => [
					[
						'type' => 'inner',
						'table' => 'settlement_companies',
						'alias' => 'SettlementCompany',
						'conditions' => ['SettlementSummary.settlement_company_accounting_code = SettlementCompany.accounting_code']
					],
				],
				'conditions' => [
					'SettlementSummary.delete_flg' => 0,
					'SettlementSummary.synchronization_status' => 'SYNCHRONIZED',
				],
				'order' => 'settlement_month desc',
				'limit' => 20,
				'paramType' => 'querystring'
			]
		];

		if ($this->clientData['is_system_admin'] == 1) {
			// adminユーザ: セレクトボックスで選択された精算管理会社で絞り込み
			$this->set('settlementCompanies', $this->SettlementCompany->find('list', array(
				'conditions' => array('client_id' => $this->clientData['client_id'])
			)));
			$this->Paginator->settings['SettlementSummary']['conditions']['SettlementCompany.id'] = $this->request->query['settlement_company_id'];
		} else {
			// 紐づいていない場合はclient/topにリダイレクト
			$settlementCompanies = $this->SettlementCompanyStaff->find('list', array(
				'fields' => array(
					'SettlementCompany.id',
					'SettlementCompany.name',
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'alias' => 'SettlementCompany',
						'table' => 'settlement_companies',
						'conditions' => 'SettlementCompanyStaff.settlement_company_id = SettlementCompany.id',
					),
				),
				'conditions' => array('SettlementCompanyStaff.staff_id' => $this->clientData['staff_id']),
				'recursive' => -1,
			));
			if (empty($settlementCompanies)) {
				$this->redirect('/Tops/');
			} elseif (count($settlementCompanies) > 1) {
				// 1ユーザに複数の精算管理会社が紐づいている場合もselectボックスで絞り込みさせる
				$this->set('settlementCompanies', $settlementCompanies);
				$this->Paginator->settings['SettlementSummary']['conditions']['SettlementCompany.id'] = $this->request->query['settlement_company_id'];
			} else {
				// 1つしか紐づいていない場合は直接そのデータを出す
				$settlementCompanyKeys = array_keys($settlementCompanies);
				$this->Paginator->settings['SettlementSummary']['conditions']['SettlementCompany.id'] = $settlementCompanyKeys[0];
			}
		}
		$this->set('settlementCompanyId', $this->request->query['settlement_company_id']);
		$this->set('settlementSummaries', $this->Paginator->paginate('SettlementSummary'));
	}

	/**
	 * 精算PDFダウンロード
	 */
	public function download($settlementSummaryId)
	{
		// アクセス権限のないPDFのダウンロードを弾く
		$isAccessible = $this->SettlementSummary->isAccessibleByThisStaff($settlementSummaryId, $this->clientData['client_id']);
		if (!$isAccessible) {
			$this->redirect(array("controller" => "users", "action" => "logout"));
		}
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
				'SettlementSummary.synchronization_status' => 'SYNCHRONIZED',
				'SettlementSummary.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$settlementTopData = $this->SettlementSummary->find('first', $options);
		if (empty($settlementTopData)) {
			$this->Session->setFlash('精算書が見つかりませんでした。', 'default', array('class' => 'alert alert-info'));
			$this->redirect(array("controller" => "SettlementSummary", "action" => "index"));
		}
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
	 * 成約明細csvダウンロード
	 */
	public function closingDownload($settlementSummaryId)
	{
		// アクセス権限のないPDFのダウンロードを弾く
		$isAccessible = $this->SettlementSummary->isAccessibleByThisStaff($settlementSummaryId, $this->clientData['client_id']);
		if (!$isAccessible) {
			$this->redirect(array("controller" => "users", "action" => "logout"));
		}

		// 精算会社、精算書の日付など
		$options = array(
			'fields' => array(
				'SettlementSummary.to_name',
				'SettlementSummary.settlement_company_accounting_code',
			),
			'conditions' => array(
				'SettlementSummary.id' => $settlementSummaryId,
				'SettlementSummary.synchronization_status' => 'SYNCHRONIZED',
				'SettlementSummary.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$settlementTopData = $this->SettlementSummary->find('first', $options);
		if (empty($settlementTopData)) {
			$this->Session->setFlash('成約明細が見つかりませんでした。', 'default', array('class' => 'alert alert-info'));
			$this->redirect(array("controller" => "SettlementSummary", "action" => "index"));
		}
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

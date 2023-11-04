<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('SettlementDataComponent', 'Controller/Component');
App::uses('TaxRateComponent', 'Controller/Component');
require_once("notice_class.php");

class CreateSettlementSummaryShell extends AppShell {

	public $components = array('SettlementData', 'TaxRate');

	public $uses = array('PublicHoliday', 'Client', 'Reservation', 'CancelDetail', 'SettlementCompany', 'SettlementSummary', 'SettlementSummarySalesPerformance', 'SettlementSummaryDetail', 'SettlementSummaryNextAdjustment', 'SettlementSummaryCloseData', 'SettlementSummaryCancelData', 'SettlementHistory', 'CommissionRateHistory', 'Recommend');

	public function startup() {
		$collection = new ComponentCollection();
		$this->SettlementData = new SettlementDataComponent($collection);
		$this->SettlementData->initialize($this);

		parent::startup();
	}

	public function main() {
		// 第3営業日にPDF生成処理を行う/次月調整の過去移行を行う
		$businessDay = $this->PublicHoliday->getBusinessDay();

		if ($businessDay == '3') {
			$this->log('start '. get_class(), LOG_INFO);
			// 年月度取得,重複チェック
			$settlementDate = explode('-', date('Y-m', strtotime('first day of previous month')));
			$year = $settlementDate[0];
			$month = $settlementDate[1];

			$existSettlement = $this->SettlementSummary->find('count',array('conditions' => array('settlement_month' => $year.$month)));
			if ($existSettlement > 0) {
				// 生成予定の年月度のデータがあるとデータが重複するため終了する
				$this->log('exists settlement data is ' . $year . '/' . $month, LOG_INFO);
				exit;
			}

			// 使用済み次月調整の過去化
			$this->nextAdjustmentArchive();
			$this->log('COMPLETE nextAdjustmentArchive', LOG_INFO);

			// 精算書用データ生成
			$this->settlementSummary($year, $month);
			$this->log('COMPLETE settlementSummary', LOG_INFO);
			$this->log('end '. get_class(), LOG_INFO);
		}

	}

	// 使用済み次月調整の過去化
	public function nextAdjustmentArchive() {
		// 過去分の次月調整はステータスを更新する
		$setData = array('status' => "'PAST_USED'");
		$conditions = array('status' => 'USED');
		$this->SettlementSummaryNextAdjustment->updateAll($setData, $conditions);

	}

	// 精算額集計
	public function settlementSummary($year, $month) {
		$this->log('target date '.$year.'/'.$month, LOG_INFO);
		// 予約データ取得
		$this->Reservation->recursive = -1;
		$proceeds = $this->Reservation->getProceedsSettlement($year, '', array(), true);
		$this->log('COMPLETE getProceedsSettlement count('.count($proceeds).')', LOG_INFO);
		// 精算管理会社が設定されていない予約(営業店)のチェック
		$checkSettlementCompanyId = $this->SettlementData->__checkSettlementCompanyId($proceeds, $year, $month);
		$commissionRates = $this->SettlementData->getCommissionRates($year);
		$this->log('COMPLETE getCommissionRates count('.count($commissionRates).')', LOG_INFO);

		// レコメンド
		$recommendSettlement = $this->SettlementData->__getRecommendSettlement($year, $month);
		$this->log('COMPLETE __getRecommendSettlement count('.count($recommendSettlement).')', LOG_INFO);

		// 精算管理会社単位で使う
		$period = array('year'=> $year, 'month' => '', 'day'=> '');
		$dataSettles = $this->SettlementData->__formatDataSettlement($proceeds, $period, $commissionRates, $recommendSettlement, (int)$month);
		$this->log('COMPLETE __formatDataSettlement count('.count($dataSettles).')', LOG_INFO);

		// 合計額を精算管理会社単位から計算する
		$data = $this->SettlementData->__aggregateDataSettlement($dataSettles);
		$this->log('COMPLETE __aggregateDataSettlement count('.count($data).')', LOG_INFO);

		$this->SettlementData->__addSettlementFormat($data, $dataSettles, (int)$month);
		$this->SettlementData->__addCancelDetailFormat($data, $dataSettles, $year, $month);
		$this->log('COMPLETE settlementDataFormat count('.count($dataSettles).')', LOG_INFO);


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
				'status' => 'NEW',
			),
			'order'=>'settlement_company_accounting_code asc, id asc',
			'recursive' => -1
		);
		$nextAdjustmentData = $this->SettlementSummaryNextAdjustment->find('all', $options);
		$nextAdjustmentArr = Hash::combine($nextAdjustmentData, '{n}.SettlementSummaryNextAdjustment.id', '{n}.SettlementSummaryNextAdjustment', '{n}.SettlementSummaryNextAdjustment.settlement_company_accounting_code');
		$this->log('GET nextAdjustmentData count('.count($nextAdjustmentData).')', LOG_INFO);

		// 精算マスタデータ作成
		$settlementSummaryData = $this->SettlementData->getSettlementSummaryData($dataSettles, $year, (int)$month, $nextAdjustmentArr);
		$this->log('COMPLETE getSettlementSummaryData count('.count($settlementSummaryData).')', LOG_INFO);

		// 販売実績,詳細データに必要なクライアント(精算マスタで使用した分)を抽出する
		$accountingCodeArr = $this->SettlementData->getAccountingCodeAllData($settlementSummaryData);
		$clientList = $this->Client->find('list');
		// 販売実績データ作成
		$salesPerformanceData = $this->SettlementData->getSalesPerformanceData($accountingCodeArr, $dataSettles, $clientList, $year, (int)$month);
		$this->log('COMPLETE getSalesPerformanceData count('.count($salesPerformanceData).')', LOG_INFO);
		// 精算詳細データ作成
		$summaryDetailData = $this->SettlementData->getSummaryDetailData($accountingCodeArr, $dataSettles, $clientList, $year, (int)$month, $period, $commissionRates);
		$this->log('COMPLETE getSummaryDetailData count('.count($summaryDetailData).')', LOG_INFO);

		// 精算書マスタ/販売実績/精算詳細データ保存
		$this->SettlementData->saveSettlementSummaryData($year, $month, $settlementSummaryData, $salesPerformanceData, $summaryDetailData, $nextAdjustmentArr);
	}

}

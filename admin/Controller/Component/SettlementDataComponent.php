<?php
/*
 * コントローラー側で各モデルを読み込むこと
 * CancelDetail,CommissionRateHistory,Recommend,Reservation,SettlementCompany,SettlementHistory
 */

require_once("notice_class.php");

class SettlementDataComponent extends Component{

	public $components = array('TaxRate');

	public function initialize($controller) {
		$this->controller = $controller;
	}

	public function __getSettlementFinished()
	{
		$top = $this->controller->SettlementHistory->find('first', array('order' => array('settlement_month' => 'DESC', 'created' => 'DESC')));

		if (!empty($top) && $top['SettlementHistory']['settlement_month'] >= '202004') {
			return array($top['SettlementHistory']['settlement_month'], $top['SettlementHistory']['created']);
		} else {
			// 2020年4月までは完了しているものとして扱う
			return array('202004', '2020-05-08 09:39:00');
		}
	}

	public function getCommissionRates($year,$month = ''){

		$commissionRates = array();
		$settlementCompanyList = $this->controller->SettlementCompany->find('all');
		foreach($settlementCompanyList as $settlementCompany){
			$commissionRates[$settlementCompany['SettlementCompany']['client_id']][$settlementCompany['SettlementCompany']['id']]['current'] =
				$settlementCompany['SettlementCompany']['commission_rate'];
		}

		if($month == ''){
			$conditions = array('rate_ym LIKE' => "$year%");
		} else {
			$conditions = array('rate_ym' => $year.$month );
		}

		$fields = array('client_id', 'settlement_company_id', 'rate_ym','commission_rate');
		$this->controller->CommissionRateHistory->recursive = -1;
		$commissionRateHistories = $this->controller->CommissionRateHistory->find('all', array('fields'=> $fields,'conditions' => $conditions));

		foreach($commissionRateHistories as $commisionRateHistory){
			$clientId = $commisionRateHistory['CommissionRateHistory']['client_id'];
			$settlementCompanyId = $commisionRateHistory['CommissionRateHistory']['settlement_company_id'];
			$rateYm = $commisionRateHistory['CommissionRateHistory']['rate_ym'];
			$commissionRates[$clientId][$settlementCompanyId][$rateYm]
				= $commisionRateHistory['CommissionRateHistory']['commission_rate'];
		}

		return $commissionRates;
	}
	
	public function __getRecommendSettlement($year, $month)
	{
		$taxRate = $this->TaxRate->getConsumptionTaxRate($year, $month);
		$recommendData = $this->controller->Recommend->getSettlementTarget($year, $month);

		$tmp = array();
		$statusAllRecommendIds = array();
		$statusReserveRecommendIds = array();
		$statusContractRecommendIds = array();
		foreach ($recommendData as $rd) {
			$tmp[$rd['Recommend']['id']] = $rd['Recommend'];
			if ($rd['Recommend']['recommend_fee_unit']) {
				if ($rd['Recommend']['settlement_timing']) {
					// 申込月に請求（変数名に反してステータス＝予約とは限らないが）
					$statusReserveRecommendIds[] = $rd['Recommend']['id'];
				} else {
					// 成約月に請求
					$statusContractRecommendIds[] = $rd['Recommend']['id'];
				}
			} else {
				// レコメンド期間の終了日を含む月に請求
				$statusAllRecommendIds[] = $rd['Recommend']['id'];
			}
		}
		$recommendData = $tmp;

		// 種別ごとに予約データ取得する
		$allData = array();
		$reserveData = array();
		$contractData = array();
		if (!empty($statusAllRecommendIds)) {
			$allData = $this->controller->Reservation->getRecommendedData($statusAllRecommendIds);
		}
		if (!empty($statusReserveRecommendIds)) {
			$reserveData = $this->controller->Reservation->getRecommendedData(
				$statusReserveRecommendIds,
				null,
				array(
					"DATE_FORMAT(Reservation.reservation_datetime, '%Y%m')" => $year.$month,
				)
			);
		}
		if (!empty($statusContractRecommendIds)) {
			$contractData = $this->controller->Reservation->getRecommendedData(
				$statusContractRecommendIds,
				array(
					array(
						'type' => 'INNER',
						'alias' => 'Client',
						'table' => 'clients',
						'conditions' => 'Client.id = Reservation.client_id'
					)
				),
				array(
					'Reservation.reservation_status_id' => 2,
					'OR' => array(
						array(
							'Client.conclusion_contract_criteria' => 0,
							"DATE_FORMAT(Reservation.rent_datetime, '%Y%m')" => $year.$month,
						),
						array(
							'Client.conclusion_contract_criteria' => 1,
							"DATE_FORMAT(Reservation.return_datetime, '%Y%m')" => $year.$month
						)
					)
				)
			);
		}
		$mixedData = $allData + $reserveData + $contractData;

		// （精算管理会社ごとに）手数料計算する
		$recommendSettlement = array();
		foreach ($mixedData as $recommendId => $md) {
			$rec = $recommendData[$recommendId];
			foreach ($md as $settlementCompanyId => $d) {
				if (!isset($recommendSettlement[$settlementCompanyId])) {
					$recommendSettlement[$settlementCompanyId] = array(
						'recommend_price' => 0,
						'recommend_count' => 0,
						'recommend_without_tax' => 0,
						'recommend_tax' => 0,
						'recommend_with_tax' => 0
					);
				}
				$recommendSettlement[$settlementCompanyId]['recommend_price'] += $d['sum'];
				$recommendSettlement[$settlementCompanyId]['recommend_count'] += $d['count'];
				if ($rec['is_internal_tax']) {// 内税
					if ($rec['recommend_fee_unit'] == 0) {// 定額
						$withTax = $rec['recommend_fee'];
					} else {
						$withTax = $d['sum'] * ($rec['recommend_fee'] * 10) / 1000;
					}
					$tax = $withTax / $taxRate * ($taxRate - 1);

					$withTax = (int)floor($withTax);
					$tax = (int)floor($tax);
					$withoutTax = $withTax - $tax;
				} else {// 外税
					if ($rec['recommend_fee_unit'] == 0) {// 定額
						$withoutTax = $rec['recommend_fee'];
					} else {
						$withoutTax = $d['sum'] * ($rec['recommend_fee'] * 10) / 1000;
					}
					$tax = $withoutTax * ($taxRate - 1);

					$withoutTax = (int)floor($withoutTax);
					$tax = (int)floor($tax);
					$withTax = $withoutTax + $tax;
				}
				$recommendSettlement[$settlementCompanyId]['recommend_without_tax'] += $withoutTax;
				$recommendSettlement[$settlementCompanyId]['recommend_tax'] += $tax;
				$recommendSettlement[$settlementCompanyId]['recommend_with_tax'] += $withTax;
			}
		}

		return $recommendSettlement;
	}

	public function __formatDataSettlement($proceeds, $period, $commissionRates, $recommendSettlement, $month, $targetSettlementcompanyId=[]) {
		$data = array();

		foreach ($proceeds as $val) {
			$clientId = $val['Reservation']['client_id'];
			$settlementCompanyId = $val['Office']['settlement_company_id'];

			if (!$settlementCompanyId) {
				continue;
			}
			// 抽出対象のIDがセットされている時だけ、その清算管理会社IDが対象かチェックする
			if (!empty($targetSettlementcompanyId) && !array_key_exists($settlementCompanyId, $targetSettlementcompanyId)) {
				continue;
			}

			$date = $val[0]['date'];
			// 請求内容は変わらないはずだが、見た目変わるインパクト大きいので躊躇
			/*if ($date <> $month) {
				continue;
			}*/

			// 予約
			if ($val['Reservation']['reservation_status_id'] == 1) {
				$status = 'booking';
			// 成約
			} else if ($val['Reservation']['reservation_status_id'] == 2) {
				$status = 'agreement';
			// キャンセル
			} else if ($val['Reservation']['reservation_status_id'] == 3) {
				$status = 'cancel';
			} else {
				continue;
			}

			$data[$clientId][$settlementCompanyId][$date][$status]['price'] = $val[0]['price'];
			$data[$clientId][$settlementCompanyId][$date][$status]['count'] = $val[0]['count'];

			$rate = $this->__getCommissionRate($clientId, $settlementCompanyId, $commissionRates, $period, $date);
			$rate = $rate / 100;
			$commission = floor($val[0]['price'] * $rate);

			/**
			 * 予約ID　確認用
			 */
			if (isset($val[0]['reservation_ids'])) {
				$data[$clientId][$settlementCompanyId][$date][$status]['reservation_ids'] = $val[0]['reservation_ids'];
			}

			// 現地精算 - 成約件数(allではなく、dateにするか?)
			$data[$clientId][$settlementCompanyId][$date][$status]['local_pay'] = (isset($val[0]['local_pay'])) ? $val[0]['local_pay'] : 0;

			// 現地精算 - 成約金額
			$data[$clientId][$settlementCompanyId][$date][$status]['local_amount'] = (isset($val[0]['local_amount'])) ? $val[0]['local_amount'] : 0;

			// 販売手数料
			$data[$clientId][$settlementCompanyId][$date][$status]['commission'] = $commission;

			// 販売手数料 - 料率
			$data[$clientId][$settlementCompanyId][$date][$status]['commission_rate'] = $rate;
		}

		if (!empty($recommendSettlement)) {
			$settlementCompanyMap = $this->controller->SettlementCompany->find('list', array('fields' => array('id', 'client_id')));
			foreach ($recommendSettlement as $settlementCompanyId => $rs) {
				$clientId = $settlementCompanyMap[$settlementCompanyId];
				$data[$clientId][$settlementCompanyId][$month]['recommend'] = $rs;
			}
		}

		return $data;
	}

	public function __aggregateDataSettlement($dataSettles)
	{
		$data = array();
		$template = array(
			'booking' => array(
				'count' => 0,
				'price' => 0,
				'local_pay' => 0,
				'local_amount' => 0,
				'commission' => 0
			),
			'agreement' => array(
				'count' => 0,
				'price' => 0,
				'local_pay' => 0,
				'local_amount' => 0,
				'commission' => 0
			),
			'cancel' => array(
				'count' => 0,
				'price' => 0,
				'local_pay' => 0,
				'local_amount' => 0,
				'commission' => 0
			),
			'recommend' => array(
				'recommend_without_tax' => 0,
				'recommend_with_tax' => 0
			),
		);

		foreach ($dataSettles as $clientId => $d1) {
			foreach ($d1 as $settlementCompanyId => $d2) {
				foreach ($d2 as $month => $d3) {
					if (!isset($data[$month])) {
						$data[$month] = $template;
					}
					foreach ($d3 as $status => $d4) {
						if ($status == 'recommend') {
							$data[$month][$status]['recommend_without_tax'] += $d4['recommend_without_tax'];
							$data[$month][$status]['recommend_with_tax'] += $d4['recommend_with_tax'];
						} else {
							$data[$month][$status]['count'] += $d4['count'];
							$data[$month][$status]['price'] += $d4['price'];
							$data[$month][$status]['local_pay'] += $d4['local_pay'];
							$data[$month][$status]['local_amount'] += $d4['local_amount'];
							$data[$month][$status]['commission'] += $d4['commission'];
						}
					}
				}
			}
		}

		return array('0' => $data);
	}

	public function __addSettlementFormat(&$data, &$dataSettles, $date)
	{
		$this->controller->SettlementCompany->recursive = -1;
		$settlementCompanies = $this->controller->SettlementCompany->find('all');
		foreach ($settlementCompanies as $settlementCompany) {
			foreach ($dataSettles as $clientId => &$settlementData) {
				foreach ($settlementData as $settlementCompanyId => &$d) {
					if ($settlementCompany['SettlementCompany']['id'] == $settlementCompanyId) {
						$d[$date]['accounting_code'] = $settlementCompany['SettlementCompany']['accounting_code'];
						$d[$date]['settlement_company_id'] = $settlementCompany['SettlementCompany']['id'];
						$d[$date]['settlement_company_name'] = $settlementCompany['SettlementCompany']['name'];
						$d[$date]['fee_rate'] = $settlementCompany['SettlementCompany']['fee_rate'];
						$d[$date]['bank_name'] = $settlementCompany['SettlementCompany']['bank_name'];
						$d[$date]['bank_branch_name'] = $settlementCompany['SettlementCompany']['bank_branch_name'];
						$d[$date]['account_type'] = $settlementCompany['SettlementCompany']['account_type'];
						$d[$date]['account_number'] = $settlementCompany['SettlementCompany']['account_number'];
						$d[$date]['account_holder'] = $settlementCompany['SettlementCompany']['account_holder'];
						$d[$date]['billing_email1'] = $settlementCompany['SettlementCompany']['billing_email1'];
						$d[$date]['billing_email2'] = $settlementCompany['SettlementCompany']['billing_email2'];
						$d[$date]['billing_email3'] = $settlementCompany['SettlementCompany']['billing_email3'];
						$d[$date]['billing_email4'] = $settlementCompany['SettlementCompany']['billing_email4'];
						$d[$date]['billing_email5'] = $settlementCompany['SettlementCompany']['billing_email5'];
						$d[$date]['billing_email6'] = $settlementCompany['SettlementCompany']['billing_email6'];
						$d[$date]['billing_email7'] = $settlementCompany['SettlementCompany']['billing_email7'];
						$d[$date]['billing_email8'] = $settlementCompany['SettlementCompany']['billing_email8'];
						$d[$date]['billing_email9'] = $settlementCompany['SettlementCompany']['billing_email9'];
						$d[$date]['billing_email10'] = $settlementCompany['SettlementCompany']['billing_email10'];
						$d[$date]['is_internal_tax'] = $settlementCompany['SettlementCompany']['is_internal_tax'];
					}
				}
			}
		}

		$data['0'][$date]['agreement']['settlement_fee'] = 0;
		$data['0'][$date]['cancel']['settlement_fee'] = 0;
		foreach ($dataSettles as $clientId => &$settlementData) {
			foreach ($settlementData as $settlementCompanyId => &$d) {
				$d[$date]['agreement']['settlement_fee'] = isset($d[$date]['agreement']) ? floor(($d[$date]['agreement']['price'] - $d[$date]['agreement']['local_amount']) * $d[$date]['fee_rate']/100) : 0;
				$d[$date]['cancel']['settlement_fee'] = isset($d[$date]['cancel']) ? floor(($d[$date]['cancel']['price'] - $d[$date]['cancel']['local_amount']) * $d[$date]['fee_rate']/100) : 0;
				$data['0'][$date]['agreement']['settlement_fee'] += $d[$date]['agreement']['settlement_fee'];
				$data['0'][$date]['cancel']['settlement_fee'] += $d[$date]['cancel']['settlement_fee'];
			}
		}
	}

	public function __addCancelDetailFormat(&$data, &$dataSettles, $year, $month)
	{
		$cancelDetails = $this->controller->CancelDetail->getProceeds($year, $month);

		$month = (int)$month;
		foreach ($cancelDetails as $cancelDetail) {
			if ($cancelDetail['CancelDetail']['account_code'] == 'ADMINISTRATIVE_FEE') {
				continue;
			}

			// 全体集計用
			if ($cancelDetail['CancelDetail']['account_code'] == 'ADVENTURE_FEE') {
				if (isset($data['0'][$month]['cancel']['adventure_fee'])) {
					$data['0'][$month]['cancel']['adventure_fee'] += $cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'];
				} else {
					$data['0'][$month]['cancel']['adventure_fee'] = $cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'];
				}
				continue;
			}

			// 全体集計用
			if (isset($data['0'][$month]['cancel']['cancel_detail_amount'])) {
				$data['0'][$month]['cancel']['cancel_detail_amount'] += $cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'];
			} else {
				$data['0'][$month]['cancel']['cancel_detail_amount'] = $cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'];
			}

			// 会社別集計
			foreach ($dataSettles as $clientId => &$dataSettle) {
				foreach ($dataSettle as $settlementCompanyId => &$d) {
					if ($cancelDetail['Office']['settlement_company_id'] == $settlementCompanyId) {
						if (isset($d[$month]['cancel']['cancel_detail_amount'])) {
							$d[$month]['cancel']['cancel_detail_amount'] += $cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'];
						} else {
							$d[$month]['cancel']['cancel_detail_amount'] = $cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'];
						}
					}
				}
			}
		}
	}

	//手数料率を取得
	public function __getCommissionRate($clientId,$settlementCompanyId,$commissionRates,$period,$date)
	{
		$rate = 0;

		if (empty($commissionRates[$clientId][$settlementCompanyId]))
		{
			return $rate;
		}

		$clientCommissionRates = $commissionRates[$clientId][$settlementCompanyId];

		if(!empty($period['month'])){
			$strDate = $period['year'].$period['month'];
		} else {
			$unit = str_pad($date, 2, "0", STR_PAD_LEFT);
			$strDate = $period['year'].$unit;
		}

		if(array_key_exists($strDate,$clientCommissionRates)){
			$rate = $clientCommissionRates[$strDate];
		} else {
			if($strDate >= date('Ym')){
				$rate = $clientCommissionRates['current'];
			}
		}

		return $rate;
	}

	// 清算管理会社が設定されていない営業店があるか判定
	public function __checkSettlementCompanyId($proceeds, $year, $month, $manualFlg = false)
	{
		$ngReservationId = array();
		$ngReservation = false;
		if(isset($proceeds)){
			foreach ($proceeds as $val) {
				// 今作ろうとしている精算年月度以外は無視する
				$date = $val[0]['date'];
				if ($date <> $month) {
					continue;
				}
				$settlementCompanyId = $val['Office']['settlement_company_id'];
				// 精算管理会社が登録されていないデータはNG(アラートの通知だけ飛ばす(slack))
				if (!$settlementCompanyId) {
					$ngReservation = true;
				}
			}
			if ($ngReservation) {
				$ngReservationData = $this->controller->Reservation->getProceedsSettlementSummary($year, $month);
				$ngReservationId = Hash::extract($ngReservationData, '{n}.Reservation.id');
			}
		}

		if (!empty($ngReservation)) {
			$errList = array();
			$officeList = $this->controller->Reservation->getOfficesByReservationId($ngReservationId);
			foreach($officeList as $ok => $officeData){
				$errList[] = $officeData['Client']['name'] . ':' . $officeData['Office']['name'];
			}
			$message = implode("\n", $errList) . "\nの精算管理会社が設定されていません。\n設定後、精算書の再発行を実行してください。";
			$title = '精算書生成バッチ';
			if ($manualFlg) {
				// 再発行の時は処理を止めるためデータを返さずにエラーを返す
				return $message;
			} else {
				$this->notice($message, $title);
			}
		}

		return true;
	}

	// 精算額集計(精算額マスタデータ取得)
	public function getSettlementSummaryData($data, $year, $month, $nextAdjustmentArr, $targetAccountingCode = '')
	{
		$taxRate = $this->TaxRate->getConsumptionTaxRate($year, $month);
		$body = array();
		$returnBody = array();
		$settlementCompanyIds = array();
		foreach ($data as $clientId => $dataSettle) {
			foreach ($dataSettle as $settlementCompanyId => $d) {
				// 精算会社が設定されていない(営業所の)予約も処理しない(精算先がないため集計しても無意味)
				if (!isset($d[$month]['settlement_company_id'])) {
					continue;
				}

				// 成約件数を保持しておく。後で経理用管理コードごとに0件かチェックする
				$body[$settlementCompanyId]['count'] = isset($d[$month]['agreement']['count']) ? $d[$month]['agreement']['count'] : 0;

				// 精算金額
				$commissionTax = ($d[$month]['is_internal_tax'] || !isset($d[$month]['agreement']['commission'])) ? 0 : floor(($taxRate - 1.0) * (int)$d[$month]['agreement']['commission']);
				$settlementTax = ($d[$month]['is_internal_tax']) ? 0 : floor($d[$month]['agreement']['settlement_fee'] * ($taxRate - 1.0));
				$settlementPrice = ((isset($d[$month]['agreement']['price']) ? $d[$month]['agreement']['price'] : 0) - (isset($d[$month]['agreement']['local_amount']) ? (int)$d[$month]['agreement']['local_amount'] : 0) + (isset($d[$month]['cancel']['cancel_detail_amount']) ? (int)$d[$month]['cancel']['cancel_detail_amount'] : 0)) -
					((isset($d[$month]['agreement']['commission']) ? (int)$d[$month]['agreement']['commission'] : 0) + $commissionTax + $d[$month]['agreement']['settlement_fee'] + $settlementTax + (isset($d[$month]['recommend']['recommend_with_tax']) ? (int)$d[$month]['recommend']['recommend_with_tax'] : 0));
				if (!isset($body[$settlementCompanyId]['amount'])) {
					$body[$settlementCompanyId]['amount'] = $settlementPrice;
				} else {
					$body[$settlementCompanyId]['amount'] += $settlementPrice;
				}
			}
		}
		// 清算管理コードごとに精算書を作るので抽出する
		if ($targetAccountingCode == '') {
			$condition = array('accounting_code <>' => '');
		} else {
			$condition = array('accounting_code' => $targetAccountingCode);
		}
		$options = array(
			'fields' => array(
				'SettlementCompany.id',
				'SettlementCompany.name',
				'SettlementCompany.accounting_code',
				'SettlementCompany.account_holder',
				'SettlementCompany.account_number',
				'SettlementCompany.account_type',
				'SettlementCompany.bank_branch_name',
				'SettlementCompany.bank_name',
				'(case when CommissionRateHistory.is_internal_tax is not null then CommissionRateHistory.is_internal_tax else SettlementCompany.is_internal_tax end) as is_internal_tax',

			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'CommissionRateHistory',
					'table' => 'commission_rate_histories',
					'conditions' => array(
						'SettlementCompany.id = CommissionRateHistory.settlement_company_id',
						'CommissionRateHistory.rate_ym' => $year.sprintf('%02d', $month)
					),
				),
			),
			'conditions' => $condition,
			'order'=>'accounting_code asc, id asc',
			'recursive' => -1
		);
		$SettlementCompanyData = $this->controller->SettlementCompany->find('all',$options);
		if (!empty($SettlementCompanyData)) {
			foreach ($SettlementCompanyData as $k => $v) {
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['id'] = $v['SettlementCompany']['id'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['name'] = $v['SettlementCompany']['name'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['accounting_code'] = $v['SettlementCompany']['accounting_code'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['account_holder'] = $v['SettlementCompany']['account_holder'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['account_number'] = $v['SettlementCompany']['account_number'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['account_type'] = $v['SettlementCompany']['account_type'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['bank_branch_name'] = $v['SettlementCompany']['bank_branch_name'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['bank_name'] = $v['SettlementCompany']['bank_name'];
				$SettlementCompanyArr[$v['SettlementCompany']['accounting_code']][$v['SettlementCompany']['id']]['is_internal_tax'] = $v[0]['is_internal_tax'];
			}
		}
		foreach($SettlementCompanyArr as $accountingCode => $settlementCompanyIds){
			foreach(array_keys($settlementCompanyIds) as $settlementCompanyId){
				if (isset($returnBody[$accountingCode])) {
					$returnBody[$accountingCode]['amount'] += isset($body[$settlementCompanyId]['amount']) ? $body[$settlementCompanyId]['amount'] : '0';
					$returnBody[$accountingCode]['count'] += isset($body[$settlementCompanyId]['count']) ? $body[$settlementCompanyId]['count'] : '0';
				} else {
					$returnBody[$accountingCode]['amount'] = isset($body[$settlementCompanyId]['amount']) ? $body[$settlementCompanyId]['amount'] : '0';
					$returnBody[$accountingCode]['count'] = isset($body[$settlementCompanyId]['count']) ? $body[$settlementCompanyId]['count'] : '0';
				}

				// 以下項目は同じ経理管理コードが登録されていても値はほぼ同じなので1度通ればいい
				if (empty($returnBody[$accountingCode]['name'])) {
					// 精算会社名
					$returnBody[$accountingCode]['name'] = $settlementCompanyIds[$settlementCompanyId]['name'];
					// 内税/外税
					$returnBody[$accountingCode]['is_internal_tax'] = $settlementCompanyIds[$settlementCompanyId]['is_internal_tax'];
					// 銀行名
					$returnBody[$accountingCode]['bank_name'] = $settlementCompanyIds[$settlementCompanyId]['bank_name'];
					// 銀行支店名
					$returnBody[$accountingCode]['bank_branch_name'] = $settlementCompanyIds[$settlementCompanyId]['bank_branch_name'];
					// 口座種別
					$returnBody[$accountingCode]['account_type'] = $settlementCompanyIds[$settlementCompanyId]['account_type'];
					// 口座番号
					$returnBody[$accountingCode]['account_number'] = $settlementCompanyIds[$settlementCompanyId]['account_number'];
					// 口座名義カナ
					$returnBody[$accountingCode]['account_holder'] = $settlementCompanyIds[$settlementCompanyId]['account_holder'];
				}
			}
			// 再発行の時だけ次月調整だけでもあれば精算書データを作る
			if($returnBody[$accountingCode]['count'] < 1 && !($targetAccountingCode != '' && !empty($nextAdjustmentArr[$accountingCode]))){
				unset($returnBody[$accountingCode]);
				continue;
			}

			// 次月調整分の金額を合算する
			if (isset($nextAdjustmentArr[$accountingCode])) {
				foreach ($nextAdjustmentArr[$accountingCode] as $nextAdjustmentId => $nextAdjustmentData) {
					if (isset($nextAdjustmentData['payment_amount'])) {
						// 支払額
						$returnBody[$accountingCode]['amount'] += $nextAdjustmentData['payment_amount'];
					}
					if (isset($nextAdjustmentData['billing_amount'])) {
						// 請求額
						$returnBody[$accountingCode]['amount'] -= $nextAdjustmentData['billing_amount'];
					}
				}
			}

			// 支払い/請求 Constant::settlementDocumentStatus
			$returnBody[$accountingCode]['document_status'] = ($returnBody[$accountingCode]['amount'] > 0) ? 'PAYMENT' : 'INVOICE';
			// 表示のために金額の絶対値をとる
			$returnBody[$accountingCode]['amount'] = abs($returnBody[$accountingCode]['amount']);
			// 清算管理会社ID
			$returnBody[$accountingCode]['settlement_company_id'] = array_keys($settlementCompanyIds);
		}

		return $returnBody;
	}

	// 販売実績,詳細データに必要なクライアント(精算マスタで使用した分)の抽出
	public function getAccountingCodeAllData($settlementSummaryData)
	{
		$accountingCodeArr = array();
		if (!empty($settlementSummaryData)) {
			$accountingCodes = array_keys($settlementSummaryData);
			$options = array(
				'fields' => array(
					'id',
					'accounting_code',
					'client_id',
					'fee_rate',
				),
				'conditions' => array(
					'accounting_code' => $accountingCodes,
				),
				'order'=>'id asc',
				'recursive' => -1
			);
			$targetAccountingCodeData = $this->controller->SettlementCompany->find('all', $options);
			// dataSettlesにないデータを補完するために
			// dataSettlesと同じように[accounting_code][client_id][settlement_company_id]の配列を作る
			// データがないと手数料が取れないので値は手数料を入れておく
			if (!empty($targetAccountingCodeData)) {
				foreach ($accountingCodes as $accountingCode) {
					$accountingCodeData = Hash::extract($targetAccountingCodeData, '{n}.SettlementCompany[accounting_code='.$accountingCode.']');
					$accountingCodeArr[$accountingCode] = Hash::combine($accountingCodeData, '{n}.id', '{n}.fee_rate', '{n}.client_id');
				}
			}
		}
		return $accountingCodeArr;
	}

	// 精算額集計(販売実績データ取得)
	public function getSalesPerformanceData($accountingCodeArr, $data, $clientList, $year, $month)
	{
		$body = array();

		foreach ($accountingCodeArr as $accountingCode => $clientData) {
			foreach ($clientData as $clientId => $dataSettle) {
				foreach ($dataSettle as $settlementCompanyId => $d) {
					//クライアント名
					$body[$settlementCompanyId][$clientId]['client_name'] = $clientList[$clientId];

					// クライアント - 成約件数
					$body[$settlementCompanyId][$clientId]['client_count'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['count'])) ? $data[$clientId][$settlementCompanyId][$month]['agreement']['count'] : '0';
					// クライアント - 成約金額
					$body[$settlementCompanyId][$clientId]['client_price'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['price'])) ? $data[$clientId][$settlementCompanyId][$month]['agreement']['price'] : '0';

					// (内訳)現地精算 - 成約件数
					$body[$settlementCompanyId][$clientId]['local_count'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['local_pay'])) ? $data[$clientId][$settlementCompanyId][$month]['agreement']['local_pay'] : '0';
					// (内訳)現地精算 - 成約金額
					$body[$settlementCompanyId][$clientId]['local_price'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['local_amount'])) ? $data[$clientId][$settlementCompanyId][$month]['agreement']['local_amount'] : '0';

					// (内訳)WEB事前決済 - 必要かどうか
					if (isset($d)) {
						$body[$settlementCompanyId][$clientId]['web_flg'] = '1';
					} else {
						$body[$settlementCompanyId][$clientId]['web_flg'] = '0';
					}
					// (内訳)WEB事前決済 - 成約件数
					$body[$settlementCompanyId][$clientId]['web_count'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['local_pay'])) ? ($data[$clientId][$settlementCompanyId][$month]['agreement']['count'] - $data[$clientId][$settlementCompanyId][$month]['agreement']['local_pay']) : '0';
					// (内訳)WEB事前決済 - 成約金額
					$body[$settlementCompanyId][$clientId]['web_price'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['local_amount'])) ? ($data[$clientId][$settlementCompanyId][$month]['agreement']['price'] - $data[$clientId][$settlementCompanyId][$month]['agreement']['local_amount']) : '0';
				}
			}
		}
		return $body;
	}

	// 精算額集計(精算額詳細データ取得)
	public function getSummaryDetailData($accountingCodeArr, $data, $clientList, $year, $month, $period, $commissionRates)
	{
		$taxRate = $this->TaxRate->getConsumptionTaxRate($year, $month);
		$body = array();

		foreach ($accountingCodeArr as $accountingCode => $clientData) {
			foreach ($clientData as $clientId => $dataSettle) {
				foreach ($dataSettle as $settlementCompanyId => $d) {
					//クライアント名
					$body[$settlementCompanyId][$clientId]['client_name'] = $clientList[$clientId];

					// 決済手数料の有無で事前決済の表示を切り替える
					if(isset($d)){
						// WEB事前決済　前受金(件数)
						$body[$settlementCompanyId][$clientId]['web_count'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['local_pay'])) ? ($data[$clientId][$settlementCompanyId][$month]['agreement']['count'] - $data[$clientId][$settlementCompanyId][$month]['agreement']['local_pay']) : '0';
						// WEB事前決済　前受金(金額)
						$body[$settlementCompanyId][$clientId]['web_price'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['local_amount'])) ? ($data[$clientId][$settlementCompanyId][$month]['agreement']['price'] - $data[$clientId][$settlementCompanyId][$month]['agreement']['local_amount']) : '0';
						// WEB事前決済　キャンセル料　預り金(件数)
						if (isset($data[$clientId][$settlementCompanyId][$month]['cancel']['count']) && isset($data[$clientId][$settlementCompanyId][$month]['cancel']['local_pay'])) {
							$body[$settlementCompanyId][$clientId]['web_cancel_count'] = $data[$clientId][$settlementCompanyId][$month]['cancel']['count'] - $data[$clientId][$settlementCompanyId][$month]['cancel']['local_pay'];
						} else {
							$body[$settlementCompanyId][$clientId]['web_cancel_count'] = '0';
						}
						// WEB事前決済　キャンセル料　預り金(金額)
						$body[$settlementCompanyId][$clientId]['web_cancel_price'] = (isset($data[$clientId][$settlementCompanyId][$month]['cancel']['cancel_detail_amount'])) ? ($data[$clientId][$settlementCompanyId][$month]['cancel']['cancel_detail_amount']) : '0';
						// WEB事前決済　決済手数料(料率)
						$body[$settlementCompanyId][$clientId]['web_rate'] = (isset($d)) ? $d : '0';
						// WEB事前決済　決済手数料(金額)
						$body[$settlementCompanyId][$clientId]['web_settlement'] = (!empty($data[$clientId][$settlementCompanyId][$month]['agreement']['settlement_fee'])) ? $data[$clientId][$settlementCompanyId][$month]['agreement']['settlement_fee'] : '0';
						// WEB事前決済 - 消費税
						if (!empty($data[$clientId][$settlementCompanyId][$month]['agreement']['settlement_fee'])) {
							$body[$settlementCompanyId][$clientId]['web_tax'] = floor($data[$clientId][$settlementCompanyId][$month]['agreement']['settlement_fee'] * ($taxRate - 1.0));
						} else {
							$body[$settlementCompanyId][$clientId]['web_tax'] = '0';
						}
					}

					// 販売手数料(料率)
					$rate = $this->__getCommissionRate($clientId, $settlementCompanyId, $commissionRates, $period, $month);
					$body[$settlementCompanyId][$clientId]['commission_rate'] = (isset($rate)) ? ($rate) : '0';
					// 販売手数料(金額)
					$body[$settlementCompanyId][$clientId]['commission_price'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['commission'])) ? $data[$clientId][$settlementCompanyId][$month]['agreement']['commission'] : '0';
					// 販売手数料 - 消費税
					$body[$settlementCompanyId][$clientId]['commission_tax'] = (isset($data[$clientId][$settlementCompanyId][$month]['agreement']['commission'])) ? (floor(($taxRate - 1.0) * $data[$clientId][$settlementCompanyId][$month]['agreement']['commission'])) : '0';

					// レコメンド掲載費用
					if(isset($data[$clientId][$settlementCompanyId][$month]['recommend']['recommend_with_tax'])){
						$body[$settlementCompanyId][$clientId]['recommend_price'] = (!empty($data[$clientId][$settlementCompanyId][$month]['recommend']['recommend_with_tax'])) ? $data[$clientId][$settlementCompanyId][$month]['recommend']['recommend_with_tax'] : '0';
					}
					// レコメンド掲載費用(件数)
					if (isset($data[$clientId][$settlementCompanyId][$month]['recommend']['recommend_count'])) {
						$body[$settlementCompanyId][$clientId]['recommend_count'] = (!empty($data[$clientId][$settlementCompanyId][$month]['recommend']['recommend_count'])) ? $data[$clientId][$settlementCompanyId][$month]['recommend']['recommend_count'] : '0';
					}
				}
			}
		}
		return $body;
	}

	// 精算書データ保存処理
	public function saveSettlementSummaryData($year, $month, $settlementSummaryData, $salesPerformanceData, $summaryDetailData, $nextAdjustmentArr, $userId = '', $oldSettlementSummaryId = '', $paymentLimitDatetime = '')
	{
		$errMsg = [];
		// 販売実績データの保存
		if (count($settlementSummaryData) > 0) {
			$createDateTime = date('Y-m-d H:i:s');
			$taxRate = $this->TaxRate->getConsumptionTaxRate($year, $month);
			$taxRate = number_format((($taxRate * 100) - 100), 1);
			if ($userId == '') {
				$userId = '0';
			}

			foreach ($settlementSummaryData as $accountingCode => $settlementData) {
				try {
					$this->controller->SettlementSummary->begin();
					$sort = 1;
					$detailSort = 0;
					$itemCode = 'A';
					$clientCount = 0;
					$clientPrice = 0;
					$insertCloseDataArr = array();
					$insertCancelDataArr = array();
					$insertSummaryData = array();
					$insertSalesPerformanceData = array();
					$insertDetailData = array();
					$usedNextAdjustmentId = array();

					$insertSummaryData['amount'] = $settlementData['amount'];
					// オールレンタカーだけ名前をデータから取らずに「オールレンタカー」とする
					// オールレンタカーの精算会社IDが含まれているかの判定なので[0]だけ見れば問題なし
					if (array_search($settlementData['settlement_company_id'][0], Constant::allrentacarId()) !== FALSE) {
						$insertSummaryData['to_name'] = Constant::ALLRENTACAR_NAME;
					} else {
						$insertSummaryData['to_name'] = $settlementData['name'];
					}
					$insertSummaryData['settlement_month'] = $year . $month;
					$insertSummaryData['notification_datetime'] = $createDateTime;
					$insertSummaryData['document_status'] = $settlementData['document_status'];
					if (!empty($paymentLimitDatetime)) {
						$insertSummaryData['payment_limit_datetime'] = date('Y-m-d 00:00:00', strtotime($paymentLimitDatetime));
					} else {
						$insertSummaryData['payment_limit_datetime'] = date('Y-m-t 00:00:00');
					}
					$insertSummaryData['latest_flg'] = '1';
					$insertSummaryData['settlement_company_accounting_code'] = $accountingCode;

					$insertSummaryData['settlement_company_is_internal_tax'] = $settlementData['is_internal_tax'];
					$insertSummaryData['settlement_company_bank_name'] = $settlementData['bank_name'];
					$insertSummaryData['settlement_company_bank_branch_name'] = $settlementData['bank_branch_name'];
					$insertSummaryData['settlement_company_account_type'] = $settlementData['account_type'];
					$insertSummaryData['settlement_company_account_number'] = $settlementData['account_number'];
					$insertSummaryData['settlement_company_account_holder'] = $settlementData['account_holder'];
					$insertSummaryData['synchronization_status'] = 'CREATED';
					$insertSummaryData['synchronization_datetime'] = '0000-00-00 00:00:00';
					$insertSummaryData['adv_company_zip'] = COMPANY_ZIP;
					$insertSummaryData['adv_company_address'] = COMPANY_ADDRESS1.COMPANY_ADDRESS2.COMPANY_ADDRESS3;
					$insertSummaryData['adv_company_address_other'] = COMPANY_ADDRESS4;
					$insertSummaryData['adv_company_name_japanese'] = ADV_COMPANY_NAME_JAPANESE;
					$insertSummaryData['adv_display_fax'] = ADV_DISPLAY_FAX;
					$insertSummaryData['adv_settlement_tel'] = ADV_SETTLEMENT_TEL;
					$insertSummaryData['adv_bank_name'] = BANK_NAME;
					$insertSummaryData['adv_bank_branch_name'] = BANK_BRANCH_NAME;
					$insertSummaryData['adv_account_type'] = ACCOUNT_TYPE;
					$insertSummaryData['adv_account_number'] = ACCOUNT_NUMBER;
					$insertSummaryData['adv_account_holder'] = ACCOUNT_HOLDER;
					$insertSummaryData['create_datetime'] = $createDateTime;
					$insertSummaryData['create_staff_id'] = $userId;
					$insertSummaryData['update_datetime'] = $createDateTime;
					$insertSummaryData['update_staff_id'] = $userId;

					$this->controller->SettlementSummary->create();
					if (!$this->controller->SettlementSummary->save($insertSummaryData)) {
						throw new Exception('マスタ保存エラー');
					}
					$settlementSummaryId = $this->controller->SettlementSummary->getLastInsertID();

					foreach ($settlementData['settlement_company_id'] as $settlementCompanyId) {
						// 0円キャンセルをカウントする
						$cancelCount[$settlementCompanyId] = 0;

						// 成約/キャンセルデータ
						// 精算管理会社単位で成約データを出す
						$settlementReservationData = $this->controller->Reservation->getProceedsSettlementSummary($year, $month, $settlementCompanyId);
						if (isset($settlementReservationData)) {
							// 精算管理会社単位でキャンセルデータを出す
							$reservationIds = Hash::extract($settlementReservationData, '{n}.Reservation.id');
							$settlementCancelData = $this->controller->CancelDetail->getCancelFeesGroupByReservationId($reservationIds);
							foreach ($settlementReservationData as $sk => $sv) {
								if ($sv['Reservation']['reservation_status_id'] == '2') {
									// 成約データ
									$insertCloseData['settlement_summary_id'] = $settlementSummaryId;
									if (isset($sv['Reservation']['payment_status'])) {
										$insertCloseData['payment_method'] = 'WEB事前決済';
									} else {
										$insertCloseData['payment_method'] = '現地精算';
									}
									$insertCloseData['reservation_key'] = $sv['Reservation']['reservation_key'];
									$insertCloseData['client_name'] = $sv['Client']['name'];
									$insertCloseData['return_office_name'] = $sv['ReturnOffices']['name'];
									$insertCloseData['name'] = $sv['Reservation']['last_name'] . ' ' . $sv['Reservation']['first_name'];
									$insertCloseData['amount'] = $sv['Reservation']['amount'];
									$insertCloseDataArr[$sv['Reservation']['id']] = $insertCloseData;
								} elseif ($sv['Reservation']['reservation_status_id'] == '3') {
									// キャンセルデータ
									// キャンセル料金が0円より大きいもののみ集計対象になる
									if (isset($settlementCancelData[$sv['Reservation']['id']]) && $settlementCancelData[$sv['Reservation']['id']] > 0) {
										$insertCancelData['settlement_summary_id'] = $settlementSummaryId;
										$insertCancelData['reservation_key'] = $sv['Reservation']['reservation_key'];
										$insertCancelData['client_name'] = $sv['Client']['name'];
										$insertCancelData['return_office_name'] = $sv['ReturnOffices']['name'];
										$insertCancelData['name'] = $sv['Reservation']['last_name'] . ' ' . $sv['Reservation']['first_name'];
										$insertCancelData['amount'] = isset($settlementCancelData[$sv['Reservation']['id']]) ? $settlementCancelData[$sv['Reservation']['id']] : 0;
										$insertCancelDataArr[$sv['Reservation']['id']] = $insertCancelData;
										$cancelCount[$settlementCompanyId]++;
									}
								} else {
									// 予約データ(精算を締めているので本来ならここは通らないはず)
									continue;
								}
							}
						}

						// 販売実績
						foreach ($salesPerformanceData[$settlementCompanyId] as $clientId => $clientData) {
							// クライアントデータ
							$insertSalesPerformanceData[] = $this->__formatSaveSalesPerformance($settlementSummaryId, $itemCode, $clientData['client_name'], $clientData['client_count'], $clientData['client_price'], $sort);
							$clientCount += $clientData['client_count'];
							$clientPrice += $clientData['client_price'];
							$clientCode[$settlementCompanyId] = $itemCode;
							$itemCode++;
							$sort++;

							// 内訳 現地精算
							$insertSalesPerformanceData[] = $this->__formatSaveSalesPerformance($settlementSummaryId, null, '(内訳)　現地精算', $clientData['local_count'], $clientData['local_price'], $sort);
							$sort++;

							// 内訳 WEB精算
							if($clientData['web_flg']){
								$item_name = '(内訳)　WEB事前決済';
							} else {
								$item_name = '-';
							}
							$insertSalesPerformanceData[] = $this->__formatSaveSalesPerformance($settlementSummaryId, null, $item_name, $clientData['web_count'], $clientData['web_price'], $sort);
							$sort++;
						}
					}
					// 精算データ
					$insertSalesPerformanceData[] = $this->__formatSaveSalesPerformance($settlementSummaryId, null, 'スカイチケット　'. $year .'年'. $month .'月　成約', $clientCount, $clientPrice, '0');

					foreach ($settlementData['settlement_company_id'] as $settlementCompanyId) {
						// 精算詳細/次月調整データの保存
						if (count($summaryDetailData) > 0) {
							foreach ($summaryDetailData[$settlementCompanyId] as $clientId => $clientData) {
								// クライアント名
								$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, null, $clientData['client_name'], null, null, null, null, $detailSort);
								$detailSort++;

								if (isset($clientData['web_rate'])) {
									// WEB事前決済　前受金
									$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, 'WEB事前決済　前受金', $clientData['web_count'], null, $clientData['web_price'], '0', $detailSort);
									$webCode = $itemCode;
									$itemCode++;
									$detailSort++;

									// WEB事前決済　キャンセル料　預り金
									$clientData['web_cancel_count'] = $cancelCount[$settlementCompanyId];
									$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, 'WEB事前決済　キャンセル料　預り金', $clientData['web_cancel_count'], null, $clientData['web_cancel_price'], '0', $detailSort);
									$itemCode++;
									$detailSort++;

									// WEB事前決済　決済手数料
									$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, 'WEB事前決済　決済手数料('. $webCode .'*手数料)', null, $clientData['web_rate'], '0', $clientData['web_settlement'], $detailSort);
									$detailSort++;

									// WEB事前決済　消費税
									$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, Constant::TAX_NAME, null, $taxRate, '0', $clientData['web_tax'], $detailSort);
									$itemCode++;
									$detailSort++;
								}

								// 販売手数料
								$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, '販売手数料('. $clientCode[$settlementCompanyId] .'*手数料)', null, $clientData['commission_rate'], '0', $clientData['commission_price'], $detailSort);
								$detailSort++;

								// 販売手数料　消費税
								$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, Constant::TAX_NAME, null, $taxRate, '0', $clientData['commission_tax'], $detailSort);
								$itemCode++;
								$detailSort++;

								// レコメンド掲載費用
								if(isset($clientData['recommend_price'])){
									$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, 'レコメンド掲載費用', $clientData['recommend_count'], null, '0', $clientData['recommend_price'], $detailSort);
									$itemCode++;
									$detailSort++;
								}
							}
						}
					}

					// 次月調整
					if (isset($nextAdjustmentArr[$accountingCode])) {
						// PDFに出力する際、次月調整の上を開けたいため空白行を差し込むことで誤魔化す
						$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, null, null, null, null, null, null, $detailSort);
						$detailSort++;
						foreach ($nextAdjustmentArr[$accountingCode] as $nk => $nv) {
							if ((isset($nv['payment_amount']) && $nv['payment_amount'] > 0) || (isset($nv['billing_amount']) && $nv['billing_amount'] > 0)) {
								$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, $itemCode, $nv['item_name'], $nv['count'], $nv['commission_rate'], $nv['payment_amount'], $nv['billing_amount'], $detailSort);
								$itemCode++;
							} else {
								$insertDetailData[] = $this->__formatSaveDetail($settlementSummaryId, null, $nv['item_name'], $nv['count'], $nv['commission_rate'], $nv['payment_amount'], $nv['billing_amount'], $detailSort);
							}
							$usedNextAdjustmentId[] = $nv['id'];
							$detailSort++;
						}
					}

					if (!empty($insertCloseDataArr)) {
						$this->controller->SettlementSummaryCloseData->create();
						if (!$this->controller->SettlementSummaryCloseData->saveAll($insertCloseDataArr)) {
							throw new Exception('成約データ保存エラー');
						}
					}
					if (!empty($insertCancelDataArr)) {
						$this->controller->SettlementSummaryCancelData->create();
						if (!$this->controller->SettlementSummaryCancelData->saveAll($insertCancelDataArr)) {
							throw new Exception('キャンセルデータ保存エラー');
						}
					}

					$this->controller->SettlementSummarySalesPerformance->create();
					if (!$this->controller->SettlementSummarySalesPerformance->saveAll($insertSalesPerformanceData)) {
						throw new Exception('販売実績保存エラー');
					}
					$this->controller->SettlementSummaryDetail->create();
					if (!$this->controller->SettlementSummaryDetail->saveAll($insertDetailData)) {
						throw new Exception('内容詳細保存エラー');
					}

					if(count($usedNextAdjustmentId) > 0){
						// 使用した次月調整はステータスを更新する
						$setData = array('SettlementSummaryNextAdjustment.status' => "'USED'", 'SettlementSummaryNextAdjustment.settlement_summary_id' => $settlementSummaryId);
						$conditions = array('SettlementSummaryNextAdjustment.id' => $usedNextAdjustmentId);
						if (!$this->controller->SettlementSummaryNextAdjustment->updateAll($setData, $conditions)) {
							throw new Exception('次月調整更新エラー');
						}
					}

					if (!empty($oldSettlementSummaryId)) {
						$conditions = array(
							'id' => $oldSettlementSummaryId,
							'latest_flg' => '1',
						);
						$oldSettlementSummary = $this->controller->SettlementSummary->find('count', array('conditions' => $conditions, 'recursive' => -1));
						if ($oldSettlementSummary != 1) {
							// リロードされた時に同じものを作ろうとするので弾くためにチェック
							throw new Exception('更新用旧マスタがありません。');
						}

						// 既存精算書の過去化
						$conditions = array(
							'id' => $oldSettlementSummaryId,
							'latest_flg' => '0',
							'update_datetime' => date('Y-m-d H:i:s'),
							'update_staff_id' => $userId
						);
						if (!$this->controller->SettlementSummary->save($conditions)) {
							throw new Exception('マスタlatest更新エラー');
						}
					}
					// 精算書単位でcommitしていく
					$this->controller->SettlementSummary->commit();

				} catch (Exception $e) {
					$this->controller->log($e->getMessage(), LOG_ERROR);
					$this->controller->log($e->getTraceAsString(), LOG_ERROR);
					$this->controller->log('error settlement_company_accounting_code:' . $accountingCode, LOG_ERROR);
					$this->controller->SettlementSummary->rollback();
					$errMsg[] = '経理用管理コード:'.$accountingCode.' にて保存時にエラーが発生しました。';
				}
			}
		} else {
			$errMsg[] = '登録用データが見つかりませんでした。';
		}
		if (!empty($errMsg)) {
			return implode("\n", $errMsg);
		} else {
			return true;
		}
	}

	// 販売実績保存用データ成形
	public function __formatSaveSalesPerformance($settlementSummaryId, $itemCode, $itemName, $count, $amount, $sortNo) {
		$data = array();
		$data['settlement_summary_id'] = $settlementSummaryId;
		$data['item_code'] = $itemCode;
		$data['item_name'] = $itemName;
		$data['count'] = $count;
		$data['amount'] = $amount;
		$data['sort_no'] = $sortNo;
		return $data;
	}

	// 精算詳細保存用データ成形
	public function __formatSaveDetail($settlementSummaryId, $itemCode, $itemName, $count, $commissionRate, $paymentAmount, $billingAmount, $sortNo) {
		$data = array();
		$data['settlement_summary_id'] = $settlementSummaryId;
		$data['item_code'] = $itemCode;
		$data['item_name'] = $itemName;
		$data['count'] = $count;
		$data['commission_rate'] = $commissionRate;
		$data['payment_amount'] = $paymentAmount;
		$data['billing_amount'] = $billingAmount;
		$data['sort_no'] = $sortNo;
		return $data;
	}

	private function notice($message, $subject) {
		$room_id = 41382981; // レンタカー開発チーム(社内)

		$notice = new Notice($room_id);
		$ret = $notice->exec_notice($message, $subject);
		if (!$ret) {
			$this->log(sprintf("通知エラー([%s]%s)", $subject, $message), LOG_ERROR);
		}
	}

}

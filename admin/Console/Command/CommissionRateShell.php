<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('CommissionRateCalculationComponent', 'Controller/Component');

class CommissionRateShell extends AppShell {

	public $components = array('CommissionRateCalculation');

	public $uses = array('PublicHoliday', 'CommissionRateHistory', 'Client', 'SettlementCompany', 'CommissionRate', 'Reservation');

	public $cdata = array('id' => 0);

	public function startup() {
		$collection = new ComponentCollection();
		$this->CommissionRateCalculation = new CommissionRateCalculationComponent($collection);
		$this->CommissionRateCalculation->initialize($this);

		parent::startup();
	}

	/**
	 * 販売手数料を更新し、履歴を作成する(バッチ処理)
	 */
	public function main() {
		if ($this->canUpdate('now')) {
			// 販売手数料を更新
			$this->CommissionRateCalculation->applyAll();
		}

		// 履歴を作成
		$this->createCommissionRateHistory();
	}

	/**
	 * 土日祝日かどうか判定し、今日が第3営業日ならtrueを返す
	 * @param $timeStr
	 * @return bool
	 */
	private function canUpdate($timeStr) {
		$currentTime = strtotime($timeStr);
		$today = date('j', $currentTime);
		$currentYear = date('Y', $currentTime);
		$currentMonth = date('n', $currentTime);

		$canUpdate = false;
		if ($today >= 3) {
			$holidays = $this->PublicHoliday->getHolidaysByMonth(date('Y-m-d'));
			if ($currentMonth == 1) {
				// 1/2 & 1/3 が休日になるが、祝日マスタには登録できない
				// （祝日とすると、店舗の営業時間も祝日仕様になってしまう）
				$holidays[2] = $currentYear.'-01-02'; // emptyでなければ何でも良い
				$holidays[3] = $currentYear.'-01-03';
			}

			$businessDayCount = 0;
			for ($i = 1; $i <= $today; $i++) {
				$timeStamp = mktime(0, 0, 0, $currentMonth, $i, $currentYear);
				$dayOfWeek = date('w', $timeStamp);
				if ($dayOfWeek == 0 || $dayOfWeek == 6) {
					continue;
				} else if (!empty($holidays[$i])) {
					continue;
				}
				// 第3営業日が今日のとき販売手数料を更新
				if (++$businessDayCount == 3 && $i == $today) {
					$canUpdate = true;
					break;
				}
			}
		}

		return $canUpdate;
	}

	/**
	 * 先月分の販売手数料履歴を作成or更新する
	 * @return bool
	 */
	private function createCommissionRateHistory() {
		// 先月取得
		$beforeYm = date('Ym', strtotime(date('Y-m-1') . '-1 month'));

		// 精算管理会社の販売手数料を一覧で取得する
		$commissionRateArr = $this->SettlementCompany->find('all', [
			'fields' => [
				'id',
				'client_id',
				'commission_rate',
				'is_internal_tax'
			]
		]);

		$data = [];
		foreach($commissionRateArr as $key => $value) {
			$clientId = $value['SettlementCompany']['client_id'];
			$settlementCompanyId = $value['SettlementCompany']['id'];
			$commissionRate = $value['SettlementCompany']['commission_rate'];
			$isInternalTax = $value['SettlementCompany']['is_internal_tax'];

			if (empty($commissionRate)) {
				continue;
			}

			$history = $this->CommissionRateHistory->find('first', [
				'conditions' => [
					'client_id' => $clientId,
					'settlement_company_id' => $settlementCompanyId,
					'rate_ym' => (int)$beforeYm
				]
			]);

			if (empty($history)) {
				$data['CommissionRateHistory'][$key] = [
					'client_id' => $clientId,
					'settlement_company_id' => $settlementCompanyId,
					'commission_rate' => $commissionRate,
					'is_internal_tax' => $isInternalTax,
					'rate_ym' => (int)$beforeYm
				];
			} else {
				if ($history['CommissionRateHistory']['commission_rate'] != $commissionRate) {
					$data['CommissionRateHistory'][$key] = [
						'id' => $history['CommissionRateHistory']['id'],
						'commission_rate' => $commissionRate,
						'is_internal_tax' => $isInternalTax,
					];
				}
			}
		}

		if (!empty($data['CommissionRateHistory'])) {
			if (!$this->CommissionRateHistory->saveAll($data['CommissionRateHistory'])) {
				$this->log(sprintf("販売手数料履歴の更新に失敗しました。(%s)", json_encode($data['CommissionRateHistory'])), LOG_ERROR);
				return false;
			}
		}

		return true;
	}

	/**
	 * 過去の履歴のsettlement_company_idを更新する。0を適当なsettlement_company_idに置き換え、足りないデータは追加する。1度だけ利用するもの
	 */
	public function pastUpdateHistory()
	{
		$histories = $this->CommissionRateHistory->find('all', [
			'conditions' => [
				'settlement_company_id' => 0
			]
		]);

		$settlementCompanies = $this->SettlementCompany->find('all', [
			'fields' => [
				'id',
				'client_id'
			]
		]);

		$data = [];
		foreach ($histories as $history) {
			$historySettlements = Hash::extract($settlementCompanies, '{n}.SettlementCompany[client_id='.$history['CommissionRateHistory']['client_id'].']');
			$count = count($historySettlements);
			if ($count == 0) {
				continue;
			}

			// 0を上書き
			$history['CommissionRateHistory']['settlement_company_id'] = $historySettlements[0]['id'];
			array_push($data, $history);

			// 残りはinsert
			for ($i = 1; $i < $count; $i++) {
				$copy = $history;
				unset($copy['CommissionRateHistory']['id']);
				$copy['CommissionRateHistory']['settlement_company_id'] = $historySettlements[$i]['id'];
				$copy['CommissionRateHistory']['created'] = date('Y-m-d H:i:s');
				array_push($data, $copy);
			}
		}

		// bulkInsert,updateしたほうがいいけど、1回しかテスト出来ないのと、めったに書き込まない履歴なのでこれで
		$this->CommissionRateHistory->saveAll($data);
	}
}

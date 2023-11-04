<?php
App::uses('AppController', 'Controller');
/**
 * SettlementCompanies Controller
 *
 * @property SettlementCompany $SettlementCompany
 */
class SettlementCompaniesController extends AppController {

	public $components = array('CommissionRateCalculation');

	public $uses = array('Client', 'CommissionRate', 'Reservation', 'SettlementCompany', 'Staff', 'SettlementCompanyStaff');

	public $paginate = array(
		'contain' => array('SettlementCompany')
	);

	public function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		if ($this->request->is('post')) {
			if (!empty($this->request->data['CommissionRate']['CommissionRate'])) {
				$this->CommissionRateCalculation->applyAll();
				$this->redirect($this->referer());
			}
		}

		$conditions = array();

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
			$conditions['SettlementCompany.client_id'] = $this->request->query['client_id'];
		}

		$this->request->data['SettlementCompany'] = $this->request->query;
		$this->Paginator->settings = array('conditions' => $conditions, 'order' => 'SettlementCompany.id asc');

		$this->set('SettlementCompanies', $this->Paginator->paginate('SettlementCompany'));

		$settlementCompanyList = $this->SettlementCompany->find('list');
		$clientList = $this->Client->find('list');

		$this->set(compact('settlementCompanyList', 'clientList'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		if (!$this->SettlementCompany->exists($id)) {
			throw new NotFoundException(__('Invalid settlement company'));
		}

		if ($this->SettlementCompany->delete($id)) {
			$this->Session->setFlash('削除しました', 'default', array('class' => 'alert alert-success'));
		} else {
			$this->Session->setFlash('削除に失敗しました', 'default', array('class' => 'alert alert-error'));
		}

		$this->redirect($this->referer());
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		$this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
		$this->set('staffList', $this->Staff->getStaffListWithClientId());

		if ($this->request->is('post')) {
			try {
				$this->SettlementCompany->begin();
				$this->SettlementCompany->create();
				$accountingCode = Hash::get($this->SettlementCompany->find('first', ['fields' => ['max(cast(accounting_code as signed)) as accounting_code']]), '0.accounting_code');
				if (empty($accountingCode)) {
					// この処理を入れる時点ですでにデータが入っているため通らないはずだが念のため。
					$accountingCode = 0;
				}
				// 経理用管理コードは自動発番するようにする
				$this->request->data['SettlementCompany']['accounting_code'] = $accountingCode + 1;
				if (!$this->SettlementCompany->save($this->request->data)) {
					throw new Exception('追加エラー');
				}
				// 担当者登録
				$id = $this->SettlementCompany->getLastInsertID();
				$SettlementCompanyStaffData = [];
				foreach ($this->request->data['settlement_staff_id'] as $staffId) {
					// 空白や重複スタッフIDは登録しないようにする
					if (!empty($staffId) && count(Hash::extract($SettlementCompanyStaffData, '{n}[staff_id='.$staffId.'].staff_id')) < 1) {
						$tmpSettlementCompanyStaff['settlement_company_id'] = $id;
						$tmpSettlementCompanyStaff['staff_id'] = $staffId;
						$SettlementCompanyStaffData[] = $tmpSettlementCompanyStaff;
					}
				}
				if (!$this->SettlementCompanyStaff->saveAll($SettlementCompanyStaffData)) {
					throw new Exception('担当者登録エラー');
				}
				$this->SettlementCompany->commit();
			
				$this->Session->setFlash('精算管理会社を登録しました', 'default', array('class' => 'alert alert-success'));
				$this->redirectReferer();
			} catch (Exception $e) {
				$this->SettlementCompany->rollback();
				$this->Session->setFlash('精算管理会社の登録に失敗しました', 'default', array('class' => 'alert alert-error'));
			}
		}
		// 初期表示用に1~10のどこまでメールが登録されているか保持する
		for ($i = 10; $i > 0; $i--) {
			if (!empty($this->request->data['SettlementCompany']['billing_email'.$i])) {
				$max_billing_email = $i;
				break;
			}
		}
		$this->set('max_billing_email', $max_billing_email);
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->set('clientList', $this->Client->find('list', ['conditions' => ['delete_flg' => 0]]));
		$this->set('staffList', $this->Staff->getStaffListWithClientId());

		if (!$this->SettlementCompany->exists($id)) {
			throw new NotFoundException(__('Invalid settlement company'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			try {
				$this->SettlementCompany->begin();
				// accounting_codeごとに精算書を作るため精算書用の項目を合わせる
				$setData = array(
					'recreate_limit_flg' => $this->request->data['SettlementCompany']['recreate_limit_flg'],
					'is_internal_tax' => $this->request->data['SettlementCompany']['is_internal_tax'],
					'bank_name' => "'".$this->request->data['SettlementCompany']['bank_name']."'",
					'bank_branch_name' => "'".$this->request->data['SettlementCompany']['bank_branch_name']."'",
					'account_type' => $this->request->data['SettlementCompany']['account_type'],
					'account_number' => "'".$this->request->data['SettlementCompany']['account_number']."'",
					'account_holder' => "'".$this->request->data['SettlementCompany']['account_holder']."'",
					'invoice_number' => "'".$this->request->data['SettlementCompany']['invoice_number']."'"
				);
				$conditions = array('accounting_code' => $this->request->data['SettlementCompany']['accounting_code']);
				if (!$this->SettlementCompany->updateAll($setData, $conditions)) {
					throw new Exception('連動エラー');
				}
				if (!$this->SettlementCompany->save($this->request->data)) {
					throw new Exception('更新エラー');
				}
				// 担当者元データ物理削除
				if (!$this->SettlementCompanyStaff->deleteAll(['settlement_company_id' => $id])){
					throw new Exception('担当者データ削除エラー');
				}
				// 担当者登録
				$SettlementCompanyStaffData = [];
				foreach ($this->request->data['settlement_staff_id'] as $staffId) {
					// 空白や重複スタッフIDは登録しないようにする
					if (!empty($staffId) && count(Hash::extract($SettlementCompanyStaffData, '{n}[staff_id='.$staffId.'].staff_id')) < 1) {
						$tmpSettlementCompanyStaff['settlement_company_id'] = $id;
						$tmpSettlementCompanyStaff['staff_id'] = $staffId;
						$SettlementCompanyStaffData[] = $tmpSettlementCompanyStaff;
					}
				}
				if (!$this->SettlementCompanyStaff->saveAll($SettlementCompanyStaffData)) {
					throw new Exception('担当者登録エラー');
				}
				$this->SettlementCompany->commit();

				$this->Session->setFlash('精算管理会社を編集しました', 'default', array('class' => 'alert alert-success'));
				$this->redirectReferer();
			} catch (Exception $e) {
				$this->SettlementCompany->rollback();
				$this->Session->setFlash('精算管理会社の編集に失敗しました', 'default', array('class' => 'alert alert-error'));
			}
		} else {
			$options = array('conditions' => array('SettlementCompany.' . $this->SettlementCompany->primaryKey => $id));
			$this->request->data = $this->SettlementCompany->find('first', $options);
			// 精算書が作られたことがあるか確認
			$options = array(
				'fields' => array(
					'SettlementSummary.id'
				),
				'joins'=>array(
					array(
						'type'=>'INNER',
						'alias'=>'SettlementSummary',
						'table'=>'settlement_summaries',
						'conditions'=>'SettlementCompany.accounting_code = SettlementSummary.settlement_company_accounting_code',
					),
				),
				'conditions' => array(
					'SettlementCompany.id' => $id,
				),
				'order' => array('SettlementSummary.id' => 'ASC'),
				'recursive' => -1,
			);
			$settlementSummaryCnt = $this->SettlementCompany->find('count', $options);
			$this->set('settlement_summary_cnt', $settlementSummaryCnt);
		}
		// 初期表示用に1~10のどこまでメールが登録されているか保持する
		for ($i = 10; $i >0; $i--) {
			if (!empty($this->request->data['SettlementCompany']['billing_email'.$i])) {
				$max_billing_email = $i;
				break;
			}
		}
		$this->set('max_billing_email', $max_billing_email);
		$SettlementCompanyStaffList = $this->SettlementCompanyStaff->find('all', ['fields' => ['staff_id'], 'conditions' => ['settlement_company_id' => $id], 'order' => 'id', 'recursive' => -1]);
		$SettlementCompanyStaff = Hash::extract($SettlementCompanyStaffList, '{n}.SettlementCompanyStaff.staff_id');
		$this->set('settlement_company_staff', $SettlementCompanyStaff);
	}

	/**
	 * 元画面へ遷移
	 * ※元画面情報がなければ初期画面へ
	 *
	 * @return void
	 */
	private function redirectReferer()
	{
		// refererがあれば戻す
		if (!empty($this->request->data['Custom']['referer'])) {
			$this->redirect($this->request->data['Custom']['referer']);
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}
}

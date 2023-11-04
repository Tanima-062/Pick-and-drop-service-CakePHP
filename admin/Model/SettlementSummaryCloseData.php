<?php
App::uses('AppModel', 'Model');
require_once("encrypt_class.php");
/**
 * SettlementSummaryCloseData Model
 */
class SettlementSummaryCloseData extends AppModel {

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'SettlementSummary'=>array(
			'className'=>'SettlementSummary',
			'foreignKey'=>'settlement_summary_id',
			'conditions'=>'',
			'fields'=>'',
			'order'=>''
		)
	);

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()){
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();
		if (!empty($this->data['SettlementSummaryCloseData']['name'])){
			$this->data['SettlementSummaryCloseData']['name'] = $encrypt->encrypt($this->data['SettlementSummaryCloseData']['name']);
		}

		return true;
	}

	/**
	 * 検索前処理
	 */
	public function beforeFind($queryData) {
		// 対象検索条件を暗号化
		$encrypt = new Encrypt();
		if (!empty($queryData['conditions']['SettlementSummaryCloseData.name'])) {
			$queryData['conditions']['SettlementSummaryCloseData.name'] = $encrypt->encrypt($queryData['conditions']['SettlementSummaryCloseData.name']);
		}

		if (!empty($queryData['conditions']['SettlementSummaryCloseData.name like'])) {
			$val = trim($queryData['conditions']['SettlementSummaryCloseData.name like'], '%');
			$queryData['conditions']['SettlementSummaryCloseData.name like'] = '%' . $encrypt->encrypt($val) . '%';
		}

		return $queryData;
	}

	/**
	 * 検索後処理
	 */
	public function afterFind($results, $primary = false) {
		// 対象フィールドを複合化
		$encrypt = new Encrypt();
		foreach ($results as $key => $val) {
			if (isset($val['SettlementSummaryCloseData']['name'])) {
				$results[$key]['SettlementSummaryCloseData']['name'] = $encrypt->decrypt($val['SettlementSummaryCloseData']['name']);
			}
		}
		return $results;
	}

}

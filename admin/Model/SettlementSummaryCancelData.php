<?php
App::uses('AppModel', 'Model');
require_once("encrypt_class.php");
/**
 * SettlementSummaryCancelData Model
 */
class SettlementSummaryCancelData extends AppModel {

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
		if (!empty($this->data['SettlementSummaryCancelData']['name'])){
			$this->data['SettlementSummaryCancelData']['name'] = $encrypt->encrypt($this->data['SettlementSummaryCancelData']['name']);
		}

		return true;
	}

	/**
	 * 検索前処理
	 */
	public function beforeFind($queryData) {
		// 対象検索条件を暗号化
		$encrypt = new Encrypt();
		if (!empty($queryData['conditions']['SettlementSummaryCancelData.name'])) {
			$queryData['conditions']['SettlementSummaryCancelData.name'] = $encrypt->encrypt($queryData['conditions']['SettlementSummaryCancelData.name']);
		}

		if (!empty($queryData['conditions']['SettlementSummaryCancelData.name like'])) {
			$val = trim($queryData['conditions']['SettlementSummaryCancelData.name like'], '%');
			$queryData['conditions']['SettlementSummaryCancelData.name like'] = '%' . $encrypt->encrypt($val) . '%';
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
			if (isset($val['SettlementSummaryCancelData']['name'])) {
				$results[$key]['SettlementSummaryCancelData']['name'] = $encrypt->decrypt($val['SettlementSummaryCancelData']['name']);
			}
		}
		return $results;
	}

}

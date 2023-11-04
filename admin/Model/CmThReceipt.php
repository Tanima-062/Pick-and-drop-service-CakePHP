<?php
App::uses('AppModel', 'Model');
require_once("encrypt_class.php");
/**
 * ReservationStatus Model
 *
 */
class CmThReceipt extends AppModel {

	public $useDbConfig = 'skyticket';
	public $useTable = 'cm_th_receipt';
	public $primaryKey = 'receipt_id';

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()){
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();
		if (!empty($this->data['CmThReceipt']['receipt_name_enc'])){
			$this->data['CmThReceipt']['receipt_name_enc'] = $encrypt->encrypt($this->data['CmThReceipt']['receipt_name_enc']);
		}
		if (!empty($this->data['CmThReceipt']['send_address1_enc'])){
			$this->data['CmThReceipt']['send_address1_enc'] = $encrypt->encrypt($this->data['CmThReceipt']['send_address1_enc']);
		}
		if (!empty($this->data['CmThReceipt']['send_address2_enc'])){
			$this->data['CmThReceipt']['send_address2_enc'] = $encrypt->encrypt($this->data['CmThReceipt']['send_address2_enc']);
		}
		if (!empty($this->data['CmThReceipt']['send_address3_enc'])){
			$this->data['CmThReceipt']['send_address3_enc'] = $encrypt->encrypt($this->data['CmThReceipt']['send_address3_enc']);
		}
		if (!empty($this->data['CmThReceipt']['send_name_enc'])){
			$this->data['CmThReceipt']['send_name_enc'] = $encrypt->encrypt($this->data['CmThReceipt']['send_name_enc']);
		}

		return true;
	}

	/**
	 * 検索後処理
	 */
	public function afterFind($results, $primary = false) {
		// 対象フィールドを復号
		$encrypt = new Encrypt();
		foreach ($results as $key => $val) {
			if (isset($val['CmThReceipt']['receipt_name_enc'])) {
				$results[$key]['CmThReceipt']['receipt_name_enc'] = $encrypt->decrypt($val['CmThReceipt']['receipt_name_enc']);
			}
			if (isset($val['CmThReceipt']['send_address1_enc'])) {
				$results[$key]['CmThReceipt']['send_address1_enc'] = $encrypt->decrypt($val['CmThReceipt']['send_address1_enc']);
			}
			if (isset($val['CmThReceipt']['send_address2_enc'])) {
				$results[$key]['CmThReceipt']['send_address2_enc'] = $encrypt->decrypt($val['CmThReceipt']['send_address2_enc']);
			}
			if (isset($val['CmThReceipt']['send_address3_enc'])) {
				$results[$key]['CmThReceipt']['send_address3_enc'] = $encrypt->decrypt($val['CmThReceipt']['send_address3_enc']);
			}
			if (isset($val['CmThReceipt']['send_name_enc'])) {
				$results[$key]['CmThReceipt']['send_name_enc'] = $encrypt->decrypt($val['CmThReceipt']['send_name_enc']);
			}
		}

		return $results;
	}
}

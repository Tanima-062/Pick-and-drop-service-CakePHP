<?php

App::uses('AppModel', 'Model');
require_once("encrypt_class.php");
/**
 * MailSendTarget Model
 *
 * @property MailSendTarget $MailSendTarget
 */
class MailSendTarget extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array();


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array();

	/**
	 * hasMany associations
	 *
	 * @var array
	 *
	 */
	public $hasMany = array();

	/**
	 * 登録前処理
	 */
	public function beforeSave($options = array()){
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();
		if (!empty($this->data['MailSendTarget']['last_name'])){
			$this->data['MailSendTarget']['last_name'] = $encrypt->encrypt($this->data['MailSendTarget']['last_name']);
		}
		if (!empty($this->data['MailSendTarget']['first_name'])){
			$this->data['MailSendTarget']['first_name'] = $encrypt->encrypt($this->data['MailSendTarget']['first_name']);
		}
		if (!empty($this->data['MailSendTarget']['email'])){
			$this->data['MailSendTarget']['email'] = $encrypt->encrypt($this->data['MailSendTarget']['email']);
		}

		return true;
	}

	/**
	 * 検索前処理
	 */
	public function beforeFind($queryData) {
		// 対象検索条件を暗号化
		$encrypt = new Encrypt();
		if (!empty($queryData['conditions']['MailSendTarget.last_name'])) {
			$queryData['conditions']['MailSendTarget.last_name'] = $encrypt->encrypt($queryData['conditions']['MailSendTarget.last_name']);
		}
		if (!empty($queryData['conditions']['MailSendTarget.first_name'])) {
			$queryData['conditions']['MailSendTarget.first_name'] = $encrypt->encrypt($queryData['conditions']['MailSendTarget.first_name']);
		}
		if (!empty($queryData['conditions']['MailSendTarget.email'])) {
			$queryData['conditions']['MailSendTarget.email'] = $encrypt->encrypt($queryData['conditions']['MailSendTarget.email']);
		}

		if (!empty($queryData['conditions']['MailSendTarget.last_name like'])) {
			$val = trim($queryData['conditions']['MailSendTarget.last_name like'], '%');
			$queryData['conditions']['MailSendTarget.last_name like'] = '%' . $encrypt->encrypt($val) . '%';
		}
		if (!empty($queryData['conditions']['MailSendTarget.first_name like'])) {
			$val = trim($queryData['conditions']['MailSendTarget.first_name like'], '%');
			$queryData['conditions']['MailSendTarget.first_name like'] = '%' . $encrypt->encrypt($val) . '%';
		}
		if (!empty($queryData['conditions']['MailSendTarget.email like'])) {
			$val = trim($queryData['conditions']['MailSendTarget.email like'], '%');
			$queryData['conditions']['MailSendTarget.email like'] = '%' . $encrypt->encrypt($val) . '%';
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
			if (isset($val['MailSendTarget']['last_name'])) {
				$results[$key]['MailSendTarget']['last_name'] = $encrypt->decrypt($val['MailSendTarget']['last_name']);
			}
			if (isset($val['MailSendTarget']['first_name'])) {
				$results[$key]['MailSendTarget']['first_name'] = $encrypt->decrypt($val['MailSendTarget']['first_name']);
			}
			if (isset($val['MailSendTarget']['email'])) {
				$results[$key]['MailSendTarget']['email'] = $encrypt->decrypt($val['MailSendTarget']['email']);
			}
		}
		return $results;
	}


}

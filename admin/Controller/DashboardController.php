<?php
App::uses('AppController','Controller');
App::uses('Sanitize','Utility');

/**
 * Reservations Controller
 *
 * @property Reservation $Reservation
 */
class DashboardController extends AppController {

	public $uses = array('Reservation','KeyValue');

	private $rank = 5; // 上位何件まで出すか

	function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$week = array('日','月','火','水','木','金','土');

		// 日別予約獲得(件数)

		$tables = array(
			array('日別予約獲得 <件数>', '日別予約獲得 <見込売上>'),
			array('月別予約獲得 <件数>', '月別予約獲得 <見込売上>'),
			array('日別売上 <成約数>', '日別売上 <確定売上>'),
			array('月別売上 <成約数>', '月別売上 <確定売上>'),
		);

		// ダッシュボードのデータを取得
		$keyValue = $this->KeyValue->findC('first', array('conditions' => array('key'=> 'admin_dashboard_data')));
		
		$modified = '';
		$summary = array();
		if(!empty($keyValue)){
			$json = $keyValue['KeyValue']['value'];
			$modified = $keyValue['KeyValue']['modified'];
			$summary = json_decode($json,true);
		}

		// エリア別売上
		$this->set('rank', $this->rank);
		$this->set(compact('week', 'tables', 'summary','modified'));
	}
}

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppModel', 'Model');
class ClientCampaign extends AppModel {
	public $validate = array(
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'タイトルは必須です',	
			),
		),
		'client_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'クライアントは必須です',	
			),
		),
		'list_explanation' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '一覧用説明文は必須です',	
			),
		),
		'overview' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '概要は必須です',	
			),
		),
		'vehicle_fee_example' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '車両クラス・料金例は必須です',	
			),
		),
		'rank' => array(
			'notempty' => array(
				'rule' => array('numeric', 0),
				'message' => '優先順位は必須です',	
			),
		),

		'period_start' => array(
			'rule1' => array(
//				'on' => 'create',
				'rule' => 'checkPeriodStartEnd',
				'allowEmpty' => true,
				'last' => false,
				'message' => '開始日と終了日をご確認ください'
			),
			'rule2' => array(
				'rule' => 'checkPeriodTodayCompareStart',
//				'on' => 'create',
				'allowEmpty' => true,
				'last' => true,
				'message' => '本日以降の日付をご指定ください'
				)
		),
		'period_end' => array(
			'rule1' => array(
//				'on' => 'create',
				'rule' => 'checkPeriodStartEnd',
				'allowEmpty' => true,
				'last' => false,
				'message' => '開始日と終了日をご確認ください'
			),
			'rule2' => array(
//				'on' => 'create',
				'rule' => 'checkPeriodTodayCompareEnd',
				'allowEmpty' => true,
				'last' => true,
				'message' => '本日以降の日付をご指定ください'
			)
		),
		'booking_start' => array(
			'rule1' => array(
//				'on' => 'create',
				'rule' => 'checkBookingStartEnd',
				'allowEmpty' => true,
				'last' => false,
				'message' => '開始日と終了日をご確認ください'
			),
			'rule2' => array(
//				'on' => 'create',
				'rule' => 'checkBookingTodayCompareStart',
				'allowEmpty' => true,
				'last' => true,
				'message' => '本日以降の日付をご指定ください'
			)
		),
		'booking_end' => array(
			'rule1' => array(
//				'on' => 'create',
				'rule' => 'checkBookingStartEnd',
				'allowEmpty' => true,
				'last' => false,
				'message' => '開始日と終了日をご確認ください'
			),
			'rule2' => array(
//				'on' => 'create',
				'rule' => 'checkBookingTodayCompareEnd',
				'allowEmpty' => true,
				'last' => true,
				'message' => '本日以降の日付をご指定ください'
			)
		)
	);


		//キャンペーン対象期間
        public function checkPeriodStartEnd(){
            $dtStart = $this->data['ClientCampaign']['period_start'];
            $dtEnd = $this->data['ClientCampaign']['period_end'];
            if($dtStart > $dtEnd ){
                return false;
            } else {
                return true;
            }
        }
        
        public function checkPeriodTodayCompareStart(){
            $dtStart = $this->data['ClientCampaign']['period_start'];
            $dtToday = date('Y-m-d');
            if($dtStart < $dtToday){
                return false;
            } else {
                return true;
            }
        }
        
        public function checkPeriodTodayCompareEnd(){
            $dtEnd = $this->data['ClientCampaign']['period_end'];
            $dtToday = date('Y-m-d');
            if($dtEnd < $dtToday){
                return false;
            } else {
                return true;
            }
        }
        
        //ご予約可能期間
        public function checkBookingStartEnd(){
            $dtStart = $this->data['ClientCampaign']['booking_start'];
            $dtEnd = $this->data['ClientCampaign']['booking_end'];
            if($dtStart > $dtEnd ){
                return false;
            } else {
                return true;
            }
        }
        
        public function checkBookingTodayCompareStart(){
            $dtStart = $this->data['ClientCampaign']['booking_start'];
            $dtToday = date('Y-m-d');
            if($dtStart < $dtToday){
                return false;
            } else {
                return true;
            }
        }
        
        public function checkBookingTodayCompareEnd(){
            $dtEnd = $this->data['ClientCampaign']['booking_end'];
            $dtToday = date('Y-m-d');
            if($dtEnd < $dtToday){
                return false;
            } else {
                return true;
            }
        }

	public function unsetVal($ColName){
            unset($this->validate[$ColName]);
	}
        
        public function getPagenate($clientId){
            $option = array( 'conditions' => array( 'client_id' => $clientId ) );
            return $option;
        }
}
?>
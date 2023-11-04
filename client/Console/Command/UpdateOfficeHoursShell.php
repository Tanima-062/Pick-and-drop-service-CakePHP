<?php
App::uses('AppShell', 'Console/Command');

class UpdateOfficeHoursShell extends AppShell {

	public $uses = array('Office');

	public function main() {

	  //営業所を取得
	  $offices = $this->Office->find('all',array('conditions'=>array('office_hours_from <>'=>null,'office_hours_to <>'=>null), 'recursive'=>-1));
	  if(!empty($offices)) {
	    $officeHourArray = array();
	    foreach($offices as $office) {

	      //開始時間か終了時間が入力されていない営業所はcontinue
	      if(empty($office['Office']['id']) || empty($office['Office']['office_hours_from']) | empty($office['Office']['office_hours_to'])) {
	        continue;
	      }

	      $officeId = $office['Office']['id'];
	      $officeHourFrom = $office['Office']['office_hours_from'];
	      $officeHourTo = $office['Office']['office_hours_to'];

	      $officeHourArray = array(
	          'id' =>$office['Office']['id'],
	          'mon_hours_from'=>$officeHourFrom,
	          'tue_hours_from'=>$officeHourFrom,
	          'wed_hours_from'=>$officeHourFrom,
	          'thu_hours_from'=>$officeHourFrom,
	          'fri_hours_from'=>$officeHourFrom,
	          'sat_hours_from'=>$officeHourFrom,
	          'sun_hours_from'=>$officeHourFrom,
	          'hol_hours_from'=>$officeHourFrom,
	          'mon_hours_to'=>$officeHourTo,
	          'tue_hours_to'=>$officeHourTo,
	          'wed_hours_to'=>$officeHourTo,
	          'thu_hours_to'=>$officeHourTo,
	          'fri_hours_to'=>$officeHourTo,
	          'sat_hours_to'=>$officeHourTo,
	          'sun_hours_to'=>$officeHourTo,
	          'hol_hours_to'=>$officeHourTo
	      );

	      $this->Office->save($officeHourArray);
	      unset($officeHourArray);

	    }
	  }
	}
}
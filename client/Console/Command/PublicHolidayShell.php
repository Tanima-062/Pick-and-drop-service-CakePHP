<?php
App::uses('AppShell','Console/Command');
App::import('Vendor','PEAR/File/IMC');

class PublicHolidayShell extends AppShell {
  public $uses = array(
	  'PublicHoliday'
  );

  /**
   * google カレンダーから日本の祝日を前後1年分取得
   */
  public function main() {

	// include_path に pearのライブラリパスを追加
	set_include_path(get_include_path() . ':' . PEAR);

	$parse = File_IMC::parse('vCalendar');

	// カレンダーID
	$calendar_id = urlencode('japanese__ja@holiday.calendar.google.com');
	 $url = 'https://calendar.google.com/calendar/ical/' . $calendar_id . '/public/full.ics';
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	$result = curl_exec($ch);
	$parse->fromText($result);
	$events = $parse->getEvents();

	while($events->valid()) {
	  // File_IMC_Parse_Vcalendar_Event
	  $event = $events->current();

	  $data[] = array(
		  'start'=>$event->getStart(),
		  'end'=>$event->getEnd(),
		  'summary'=>$event->getSummary(),
		  'description'=>$event->getDescription()
	  );
	  $events->next();
	}

	$holidays = Hash::sort($data,'{n}.start','asc');

	$saveData = array();
	if(! empty($holidays)) {
	  foreach($holidays as $holiday) {

		$date = date('Y-m-d',strtotime($holiday['start']));
		//祝日が既に登録されていた場合continue
		$exists = $this->PublicHoliday->checkRegistPublicHoliday($date);
		if($exists) {
		  continue;
		}

		$saveData[] = array(
			'name'=>$holiday['summary'],
			'date'=>$date,
			'delete_flg'=>0
		);
	  }
	} else {
	  $this->log('祝日の取得に失敗しました。');
	}

	if(!empty($saveData)) {
	  $this->PublicHoliday->saveAll($saveData);
	}
  }
}
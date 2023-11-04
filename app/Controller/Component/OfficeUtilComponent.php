<?php

class OfficeUtilComponent extends Component {

	private function groupDayWithSameBusinessHours($businessHours){
		
		$sameDayBusinessHours = array();
		$indexes = array();
		$c = 0;

		foreach($businessHours as $businessHour){
			if(empty($businessHour['from']) || empty($businessHour['to'])){
				continue;
			}
			$key = $businessHour['from'].'-'.$businessHour['to'];
			
			if(array_key_exists($key, $sameDayBusinessHours)){

				$indexes = $sameDayBusinessHours[$key]['indexes'];
				$sc = -1;
				foreach($indexes as $c => $consecutiveIndexes){
					foreach($consecutiveIndexes as $i => $index){

						if($index == ($businessHour['index']-1)){
							
							$sc = $c;
							break;
						}
					}
				}
				
				if($sc != -1){
					
					$sameDayBusinessHours[$key]['indexes'][$sc][] = $businessHour['index'];
				} else {
					$sameDayBusinessHours[$key]['indexes'][] = array($businessHour['index']);
				}
				
			} else {
				
				$sameDayBusinessHours[$key]['indexes'][] = array($businessHour['index']);
				$sameDayBusinessHours[$key]['from'] =  $businessHour['from'];
				$sameDayBusinessHours[$key]['to'] =  $businessHour['to'];
				
			}
			
		}

		return $sameDayBusinessHours;
	}

	public function checkSameHours($businessHours,$businessHours2){
		$allBussinessHours = array_merge($businessHours,$businessHours2);
		$first = true;
		$saveHour = '';
  		foreach($allBussinessHours as $businessHour){
  			if($businessHour['index'] == 0){
  				$saveHour = $businessHour['from'] . $businessHour['to'];
  			} else {
  				if($saveHour != ($businessHour['from'] . $businessHour['to'])){
  					return false;
  				}
  			}
  		}
  		return true;
	}

	public function formatOfficeBusinessHours($office) {
		if (empty($office)) {
			return '';
		}
		$kanjis = array(0 => '月', 1 => '火', 2 => '水', 3 => '木', 4 => '金', 5 => '土', 6 => '日', 7 => '祝');

  		$businessHours = array();
  		$businessHours['mon'] = array('index' => 0, 'from' => $office['mon_hours_from'], 'to' => $office['mon_hours_to']);
  		$businessHours['tue'] = array('index' => 1, 'from' => $office['tue_hours_from'], 'to' => $office['tue_hours_to']);
  		$businessHours['wed'] = array('index' => 2, 'from' => $office['wed_hours_from'], 'to' => $office['wed_hours_to']);
  		$businessHours['thu'] = array('index' => 3, 'from' => $office['thu_hours_from'], 'to' => $office['thu_hours_to']);
  		$businessHours['fri'] = array('index' => 4, 'from' => $office['fri_hours_from'], 'to' => $office['fri_hours_to']);

  		$businessHours2 = array();
  		$businessHours2['sat'] = array('index' => 5, 'from' => $office['sat_hours_from'], 'to' => $office['sat_hours_to']);
  		$businessHours2['sun'] = array('index' => 6, 'from' => $office['sun_hours_from'], 'to' => $office['sun_hours_to']);
  		$businessHours2['hol'] = array('index' => 7, 'from' => $office['hol_hours_from'], 'to' => $office['hol_hours_to']);

  		if($this->checkSameHours($businessHours,$businessHours2)){
  			$str = '毎日 '. date('H:i', strtotime($office['mon_hours_from'])) .' ～ '. date('H:i', strtotime($office['mon_hours_to']));
  			return $str;
  		}

  		$groupDayBusinessHours = $this->groupDayWithSameBusinessHours($businessHours);
  		
  		$str = '';
  		foreach($groupDayBusinessHours as $groupDayBusinessHour){

  			$days = '';
  			if(count($groupDayBusinessHour['indexes']) == 1){
  				$indexes = $groupDayBusinessHour['indexes'][0];
  				$isConsecutive = true;
  				$sep = '〜';
  				$count = count($indexes);
  				if($count > 1){
	  				$first = $indexes[0];
	  				$last = $indexes[$count-1];
	  				$days = $kanjis[$first].'〜'.$kanjis[$last];
  				} else {
  					$first = $indexes[0];
  					$days = $kanjis[$first];
  				}
  			} else {
  				foreach($groupDayBusinessHour['indexes'] as $indexes){
  					
  					$daysIndexes = array();
  					foreach($indexes as $i => $index){
  						$daysIndexes[] = $kanjis[$index];
  					}

  					$strDaysIndexes = implode('・',$daysIndexes);
  					if(!empty($days)){
  						$days = $days. '・' . $strDaysIndexes;
  					} else {
  						$days = $strDaysIndexes;
  					}
  				}
  			}
  			
  			$strDays = '('.$days.') '. date('H:i', strtotime($groupDayBusinessHour['from'])). ' ～ ' . date('H:i', strtotime($groupDayBusinessHour['to']));

  			if(!empty($str)){
  				$str = $str . ' / ' . $strDays;
  			} else {
  				$str = $strDays;
  			}
  		}

  		$groupDayBusinessHours2 = $this->groupDayWithSameBusinessHours($businessHours2);

  		foreach($groupDayBusinessHours2 as $groupDayBusinessHour){

  			$days = '';
  			
			foreach($groupDayBusinessHour['indexes'] as $indexes){
				
				$daysIndexes = array();
				foreach($indexes as $i => $index){
					$daysIndexes[] = $kanjis[$index];
				}

				$strDaysIndexes = implode('・',$daysIndexes);
				if(!empty($days)){
					$days = $days. '・' . $strDaysIndexes;
				} else {
					$days = $strDaysIndexes;
				}
			}
  			
  			
  			$strDays = '('.$days.') '. date('H:i', strtotime($groupDayBusinessHour['from'])). ' ～ ' . date('H:i', strtotime($groupDayBusinessHour['to']));

  			if(!empty($str)){
  				$str = $str . ' / ' . $strDays;
  			} else {
  				$str = $strDays;
  			}
  		}

		return $str;
	}
}

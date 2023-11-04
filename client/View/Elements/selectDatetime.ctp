<?php
	// init
	if (empty($formName)) {
		$formName = 'Search';
	}
	if (empty($fieldName)) {
		$fieldName = 'datetime';
	}
	if (empty($dateFormat)) {
		$dateFormat = 'YMDH';
	}

	if(empty($minYear)) {
		$minYear = date('Y', strtotime('-1 year'));
	}

	if(empty($maxYear)) {
		$maxYear = date('Y', strtotime('+1 year'));
	}

	$default = array(
		'monthNames' => false,
	);

	if(empty($class)) {
		$class = 'span4';
	}

	if(empty($empty)) {
		$empty = false;
	}

	$default = array_merge($default,array('empty'=>$empty));
	$default = array_merge($default,array('class'=>$class));


	if (!empty($datetimeOption)) {
		$datetimeOption = array_merge($default, $datetimeOption);
	} else {
		$datetimeOption = $default;
	}

	$yearOption = $monthOption = $dayOption = $hourOption = $minuteOption = $datetimeOption;

	if(isset($setCurrentMonth)){
		if($setCurrentMonth) {
			if(!isset($this->request->data[$formName])) {
				$yearOption['value'] = date('Y');
				$monthOption['value'] = date('m');
			}
		}
	}

	if(isset($setCurrentDay)){
		if($setCurrentDay){
			if(!isset($this->request->data[$formName])) {
				$yearOption['value'] = date('Y');
				$monthOption['value'] = date('m');
				$dayOption['value'] = date('d'); 
			}
		}	
	}

	// form create
	foreach (preg_split('//', $dateFormat, -1, PREG_SPLIT_NO_EMPTY) as $char) {
		switch ($char) {
			case 'Y':
				echo $this->Form->year("{$formName}.{$fieldName}", $minYear, $maxYear, $yearOption) . '年';
				break;
			case 'M':
				echo $this->Form->month("{$formName}.{$fieldName}", $monthOption) . '月';
				break;
			case 'D':
				echo $this->Form->day("{$formName}.{$fieldName}", $dayOption) . '日';
				break;
			case 'H':
				echo $this->Form->hour("{$formName}.{$fieldName}", true, $hourOption). '時';
				break;
			case 'I':
				echo $this->Form->minute("{$formName}.{$fieldName}", $minuteOption). '分';
				break;
		}
	}
?>

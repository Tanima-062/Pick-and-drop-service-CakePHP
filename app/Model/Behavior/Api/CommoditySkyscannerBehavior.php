<?php
class CommoditySkyscannerBehavior extends ModelBehavior {
	
	private $fuelTypes = array(
 		'R' => 'Unspecified',
		'D' => 'Diesel',
		'H' => 'Hybrid',
		'E' => 'Electric',
		'V' => 'Petrol',
	);
	
	private $optionNames = array(
 		1	 => 'Smoking',
		2	 => 'Non-smoking',
		4	 => 'GPS',
		5	 => 'ETC',
		6	 => 'Studless tire',
		7	 => 'Tire chain',
		8	 => '4WD',
		9	 => 'Junior seat',
		10	 => 'Child seat',
		11	 => 'Baby Seat',
		12	 => 'ETC card',
		13	 => 'NOC',
	);
	
	// SIPPコードから車両タイプを取得
	public function getCarType(Model $model, $letters, $sippCode) {
		if (empty($letters[0]) || empty($sippCode[0])) {
			return '';
		}
		
		return $letters[0][$sippCode[0]];
	}
	
	// トランスミッションを取得
	public function getTransmission(Model $model, $value) {
		return empty($value) ? 'Auto' : 'Manual';
	}
	
	// SIPPコードから燃料タイプを取得
	public function getFuelType(Model $model, $sippCode) {
		if (empty($sippCode) || strlen($sippCode) < 4) {
			return '';
		}
		
		if (!isset($this->fuelTypes[$sippCode[3]])) {
			return 'Unspecified';
		}

		return $this->fuelTypes[$sippCode[3]];
	}
	
	// オプションIDからオプション名を取得
	public function getOptionName(Model $model, $optionId) {
		if (empty($optionId)) {
			return '';
		}
		
		if (!isset($this->optionNames[$optionId])) {
			return '';
		}

		return $this->optionNames[$optionId];
	}
	
	// 送迎方法を取得
	public function getShuttleService(Model $model, $value) {
		// 現状の仕様では判定が難しいので一旦全てカウンターからの送迎にしておく
		return empty($value) ? '' : 'desk in terminal';
	}
	

}
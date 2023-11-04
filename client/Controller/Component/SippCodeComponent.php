<?php
class SippCodeComponent extends Component {

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}
	
	// 車種の情報を取得する
	private function getCarModel($carClassId, $carModelId) {
		return $this->controller->CarModel->getCarModelForSippCode($carClassId, $carModelId);
	}
	
	// 商品の情報を取得する
	private function getCommodity($commodityId) {
		return $this->controller->Commodity->getCommodityForSippCode($commodityId);
	}

	// SIPPコードの一覧を取得する
	private function getAllLetters() {
		return $this->controller->SippCodeLetter->getAllLetters();
	}
	
	// SIPPコードを検証する
	public function validate($sippCode, $commodityId, $carClassId, $carModelId = null) {
		if (empty($sippCode) || empty($commodityId) || empty($carClassId)) {
			return false;
		}
		
		if (count($sippCode) != 4) {
			return false;
		}
		
		$carModel = $this->getCarModel($carClassId, $carModelId);
		$commodity = $this->getCommodity($commodityId);
		
		foreach($sippCode as $k => $letter) {
			$validation = 'validationLetter' . ($k + 1);
			
			// 表示条件を満たしているか検証する
			if (!$this->$validation($letter, $carModel, $commodity)) {
				return false;
			}
		}
		
		return true;
	}
	
	public function getSippCodeList($commodityId, $carClassId, $carModelId = null) {
		if (empty($commodityId) || empty($carClassId)) {
			return false;
		}
		
		$carModel = $this->getCarModel($carClassId, $carModelId);
		$commodity = $this->getCommodity($commodityId);
		$letters = $this->getAllLetters();
		
		$ret = array();
		foreach($letters as $letter) {
			$letter = $letter['SippCodeLetter'];
			$validation = 'validationLetter' . $letter['letter_number'];
			
			// 表示条件を満たしているか検証する
			if ($this->$validation($letter['letter'], $carModel, $commodity)) {
				$ret[$letter['letter_number'] - 1][$letter['letter']] = $letter['letter'] . ' ' . $letter['description'];
			}
		}
		
		return $ret;
	}
	
	// 1文字目のバリデーション
	public function validationLetter1($letter, $carModel, $commodity = null) {
		$capacity = $carModel['CarModel']['capacity'];
		$car_type_id = $carModel['CarType']['id'];
		
		switch ($letter) {
			case 'M': // 4席以下
				if ($capacity <= 4) {
					return true;
				}
				break;
			case 'E': // 5席以下の軽自動車、コンパクト
				if ($capacity <= 5 && ($car_type_id == 1 || $car_type_id == 2)) {
					return true;
				}
				break;
			case 'C': // 5席以下の軽自動車以外
				if ($capacity <= 5 && $car_type_id != 1) {
					return true;
				}
				break;
			case 'I': // ミドル・セダンor6席
				if ($capacity == 6 || $car_type_id == 3) {
					return true;
				}
				break;
			case 'S': // ミドル・セダンor7席
				if ($capacity == 7 || $car_type_id == 3) {
					return true;
				}
				break;
			case 'F': // ミドル・セダンor7席
				if ($capacity == 7 || $car_type_id == 3) {
					return true;
				}
				break;
			case 'P': // 8席
				if ($capacity == 8) {
					return true;
				}
				break;
			case 'L': // 9席以上15席未満
				if ($capacity >= 9 && $capacity < 15) {
					return true;
				}
				break;
			case 'O': // 15席以上
				if ($capacity >= 15) {
					return true;
				}
				break;
		}
		return false;
	}
	
	// 2文字目のバリデーション
	public function validationLetter2($letter, $carModel, $commodity = null) {
		$capacity = $carModel['CarModel']['capacity'];
		$door = $carModel['CarModel']['door'];
		$car_type_id = $carModel['CarType']['id'];
		
		switch ($letter) {
			case 'B': // ドア数3以下
				if ($door <= 3) {
					return true;
				}
				break;
			case 'D': // ドア数5以上
				if ($door <= 5) {
					return true;
				}
				break;
			case 'W': // 5席以下
				if ($capacity <= 5) {
					return true;
				}
				break;
			case 'V': // 6席以上
				if ($capacity >= 6) {
					return true;
				}
				break;
			case 'H': // キャンピングカー
				if ($car_type_id == 18) {
					return true;
				}
				break;
		}
		return false;
	}
	
	// 3文字目のバリデーション
	public function validationLetter3($letter, $carModel = null, $commodity) {
		$name = $commodity['Commodity']['name'];
		$transmission_flg = $commodity['Commodity']['transmission_flg'];
		$has4WD = $commodity['CommodityPrivilege']['commodity_id'];
		
		switch ($letter) {
			case 'M': // MT
				if (!empty($transmission_flg)) {
					return true;
				}
				break;
			case 'N': // MT、商品名またはオプションカテゴリに4WDを含む
				if (!empty($transmission_flg) &&
					($has4WD || strpos($name,'4WD') !== false || strpos($name,'４ＷＤ') !== false)) {
					return true;
				}
				break;
			case 'A': // AT
				if (empty($transmission_flg)) {
					return true;
				}
				break;
			case 'B': // AT、商品名またはオプションカテゴリに4WDを含む
				if (empty($transmission_flg) &&
					($has4WD || strpos($name,'4WD') !== false || strpos($name,'４ＷＤ') !== false)) {
					return true;
				}
				break;
		}
		return false;
	}
	
	// 4文字目のバリデーション
	public function validationLetter4($letter, $carModel, $commodity = null) {
		$car_type_id = $carModel['CarType']['id'];
		
		switch ($letter) {
			case 'R':
				return true;
				break;
			case 'H':
			case 'E':
				// ハイブリッドまたはエコカー
				if ($car_type_id == 6 || $car_type_id == 15) {
					return true;
				}
				break;
		}
		return false;
	}
	
}

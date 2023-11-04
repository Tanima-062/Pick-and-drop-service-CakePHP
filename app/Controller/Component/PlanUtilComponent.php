<?php

App::uses('Component', 'Controller');

class PlanUtilComponent extends Component {

	public function checktime($hour, $min, $sec) {
		if ($hour < 0 || $hour > 23 || !is_numeric($hour)) {
			return false;
		}
		if ($min < 0 || $min > 59 || !is_numeric($min)) {
			return false;
		}
		if ($sec < 0 || $sec > 59 || !is_numeric($sec)) {
			return false;
		}
		return true;
	}

	// レンタル期間から暦日制と24時間制の日数を算出する
	public function getPeriodArray($from, $to) {
		$dateFrom = date('Y-m-d', strtotime($from));
		$dateTo = date('Y-m-d', strtotime($to));
		$dayNight = floor(abs((strtotime($dateFrom) - strtotime($dateTo)) / (60 * 60 * 24)));
		$period = $dayNight + 1;

		$diffTime = abs(strtotime($to) - strtotime($from));
		$count24 = $diffTime / 3600 / 24;
		$period24 = ceil($count24);

		return array($dayNight, $period, $period24);
	}

	/*
	 * メールアドレスからハイフンを半角にする処理
	 */
	public function mailNormalization($mailAddress) {
		$vowels = array("ー", "‐", "－", "―", "ー");
		$mailAddress = $this->basicNormalization($mailAddress);
		$mailAddress = str_replace($vowels, "-", $mailAddress);
		$vowels = array("＠");
		$mailAddress = str_replace($vowels, "@", $mailAddress);
		return $mailAddress;
	}

	/**
	 * 電話番号から半角全角スペースを削除する処理
	 */
	public function telNormalization($tel) {
		$tel = $this->basicNormalization($tel);
		$vowels = array("-", "－", "ー", "―");
		$tel = str_replace($vowels, "", $tel);
		return $tel;
	}

	/**
	 * 半角全角スペースを削除する処理
	 * 全角英数字を半角に変換
	 * @param unknown $str
	 */
	public function basicNormalization($str) {
		$vowels = array(" ", "　");
		$str = str_replace($vowels, "", $str);
		$str = mb_convert_kana($str, "a", "utf-8");
		return $str;
	}

	/**
	 * 基本料金を計算する
	 *
	 * @param array $commodityPrice
	 * @param int $dayTimeFlg
	 * @param string $from
	 * @param string $to
	 * @param int $period
	 * @return int
	 */
	public function calcBasicPrice($commodityPrice, $dayTimeFlg, $from, $to, $period) {
		$price = 0;
		$afterPrice = 0;

		if ($dayTimeFlg == 1) {
			// 時間制
			$rentalTime = ceil(abs((strtotime($from) - strtotime($to)) / (60 * 60)));

			if ($rentalTime <= 24) {
				// 24時間以内
				$price = $commodityPrice[$rentalTime]['price'];
			} else {
				// 24時間以降
				$ceilRentalTime = ceil($rentalTime);
				$overDay1 = floor(($ceilRentalTime - 24) / 24);
				$overDay2 = ceil(($ceilRentalTime - 24) / 24);
				$remainPrice = ($ceilRentalTime % 24);
				$price = $commodityPrice[24]['price'];
				$afterPrice1 = ($commodityPrice[0]['price'] * $overDay1) + ($commodityPrice[25]['price'] * $remainPrice);
				$afterPrice2 = ($commodityPrice[0]['price'] * $overDay2);
				// 料金比較
				$afterPrice = min($afterPrice1, $afterPrice2);
			}
		} else {
			// 日泊制
			if ($period <= 5) {
				// 4泊5日以内
				$price = $commodityPrice[$period]['price'];
			} else {
				// 5泊6日以上
				$diffDay = $period - 5;
				$price = $commodityPrice[5]['price'];
				$afterPrice = $commodityPrice[0]['price'] * $diffDay;
			}
		}

		return $price + $afterPrice;
	}

	/**
	 * 基本料金を計算する（募集型）
	 *
	 * @param array $commodityPrice
	 * @param int $period
	 * @return int
	 */
	public function calcBasicPriceAgentOrganized($commodityPrice, $period) {
		$over = 3;
		$span = $period - 1;
		if ($span == 0) {
			return 0;
		}
		if ($span <= $over) {
			$price = intval($commodityPrice['price_stay_' . $span]);
		} else {
			$price = intval($commodityPrice['price_stay_' . $over]) + (intval($commodityPrice['price_stay_over']) * ($span - $over));
		}
		return $price;
	}

	/**
	 * 乗車人数を計算する。
	 * (子供、幼児は大人2/3として扱う)
	 *
	 * @param int $adultCount
	 * @param int $childCount
	 * @param int $infantCount
	 * @return float
	 */
	public function calcPersonCount($adultCount, $childCount = 0, $infantCount = 0) {
		return intval($adultCount) + intval($childCount) + intval($infantCount);
	}
}

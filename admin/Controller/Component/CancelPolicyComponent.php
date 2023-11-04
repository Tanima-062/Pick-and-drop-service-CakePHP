<?php

App::uses('Component', 'Controller');

// app/clientで内容揃えること
class CancelPolicyComponent extends Component
{

	private $advCancelFee = 0;

	public function initialize(Controller $controller)
	{
		$this->controller = $controller;
	}

	public function getTextLines($clientId, $departureDatetime, $isHtml = true)
	{
		$CancelFee = ClassRegistry::init('CancelFee');
		$now = date('Y-m-d H:i:s');

		if ($isHtml) {
			$narrow = '&ensp;';
			$wide = '&emsp;';
			$newLine = '<br>';
		} else {
			$narrow = ' ';
			$wide = '　';
			$newLine = "\n";
		}

		$policy = array();
		$cancelFees = array();

		$tempCancelFees = $CancelFee->getCancelFees($clientId);
		foreach ($tempCancelFees as $temp) {
			$applyTermPoint = $temp['CancelFee']['apply_term_point'] ? $departureDatetime : $now;
			if (strtotime($applyTermPoint) >= strtotime($temp['CancelFee']['apply_term_from'])) {
				$cancelFees[] = $temp;
			}
		}
		unset($tempCancelFees);

		foreach ($cancelFees as $cancelFee) {
			$str = '';
			if ($cancelFee['CancelFee']['is_after_departure'] == 0) {
				$unitIsDay = $cancelFee['CancelFee']['from_cancel_limit_unit'] == 'DAY' ? true : false;
				$str .= '出発日の';
				if ($cancelFee['CancelFee']['from_cancel_limit'] > 0) {
					$str .= sprintf('%2s%s前〜', $cancelFee['CancelFee']['from_cancel_limit'], $unitIsDay ? '日' : '時間');
					if ($isHtml) {
						$str = str_replace(' ', $narrow, $str);
					}
				}
				$unitIsDay = $cancelFee['CancelFee']['cancel_limit_unit'] == 'DAY' ? true : false;
				if ($cancelFee['CancelFee']['cancel_limit'] > 0) {
					$str .= sprintf('%2s%s前まで', $cancelFee['CancelFee']['cancel_limit'], $unitIsDay ? '日' : '時間');
					if ($isHtml) {
						$str = str_replace(' ', $narrow, $str);
					}
				} else {
					if ($unitIsDay) {
						$str .= $narrow . $narrow . '当日まで' . $wide . $wide;
					} else {
						$str = '出発時刻まで' . $wide . $wide . $wide;
					}
				}
			} else {
				$str .= '出発後' . $wide . $narrow . $narrow . $wide . $wide . $wide . $wide;
			}

			$str .= '・・・・';

			if ($cancelFee['CancelFee']['cancel_fee'] > 0) {
				if ($cancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_BASIC_RATE') {
					$str .= '基本料金の' . $cancelFee['CancelFee']['cancel_fee'] . '%';
				} elseif ($cancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_FIXED_RATE') {
					$str .= '合計金額の' . $cancelFee['CancelFee']['cancel_fee'] . '%';
				} else {
					$str .= number_format($cancelFee['CancelFee']['cancel_fee']) . '円';
				}
			} else {
				$str .= '無料';
			}
			$policy[] = $str;

			// INCIDENT-3044 取消手続料の徴収を廃止する
			//$this->advCancelFee = $cancelFee['CancelFee']['adv_cancel_fee'];
		}
		if (!empty($cancelFees)) {
			$cancelFeeMax = (int)max(Hash::extract($cancelFees, '{n}.CancelFee.cancel_fee_max'));
			if ($cancelFeeMax > 0) {
				$policy[] = '';
				$policy[] = '・キャンセル料は' . number_format($cancelFeeMax) . '円を限度とします。';
			}
		}

		return implode($newLine, $policy);
	}

	// INCIDENT-3044 取消手続料の徴収を廃止する
	// getTextLines()の後に呼ぶこと
	/*public function getAdvCancelFee()
	{
		return $this->advCancelFee;
	}*/

	public function getTextLinesMulti($reservationIds, $isHtml = true)
	{
		$reservationCancelPolicyArr = array();
		$CancelFee = ClassRegistry::init('CancelFee');
		$now = date('Y-m-d H:i:s');

		if ($isHtml) {
			$narrow = '&ensp;';
			$wide = '&emsp;';
			$newLine = '<br>';
		} else {
			$narrow = ' ';
			$wide = '　';
			$newLine = "\n";
		}

		$reservationTempCancelFees = $CancelFee->getCancelFeesMulti($reservationIds);
		if (!empty($reservationTempCancelFees)) {
			foreach ($reservationTempCancelFees as $reservationId => $departureDatetimeArr) {
				$policy = array();
				$cancelFees = array();
				foreach ($departureDatetimeArr as $departureDatetime => $tempCancelFees) {
					foreach ($tempCancelFees as $temp) {
						$applyTermPoint = $temp['CancelFee']['apply_term_point'] ? $departureDatetime : $now;
						if (strtotime($applyTermPoint) >= strtotime($temp['CancelFee']['apply_term_from'])) {
							$cancelFees[] = $temp;
						}
					}
				}
				unset($tempCancelFees);

				foreach ($cancelFees as $cancelFee) {
					$str = '';
					if ($cancelFee['CancelFee']['is_after_departure'] == 0) {
						$unitIsDay = $cancelFee['CancelFee']['from_cancel_limit_unit'] == 'DAY' ? true : false;
						$str .= '出発日の';
						if ($cancelFee['CancelFee']['from_cancel_limit'] > 0) {
							$str .= sprintf('%2s%s前〜', $cancelFee['CancelFee']['from_cancel_limit'], $unitIsDay ? '日' : '時間');
							if ($isHtml) {
								$str = str_replace(' ', $narrow, $str);
							}
						}
						$unitIsDay = $cancelFee['CancelFee']['cancel_limit_unit'] == 'DAY' ? true : false;
						if ($cancelFee['CancelFee']['cancel_limit'] > 0) {
							$str .= sprintf('%2s%s前まで', $cancelFee['CancelFee']['cancel_limit'], $unitIsDay ? '日' : '時間');
							if ($isHtml) {
								$str = str_replace(' ', $narrow, $str);
							}
						} else {
							if ($unitIsDay) {
								$str .= $narrow . $narrow . '当日まで' . $wide . $wide;
							} else {
								$str = '出発時刻まで' . $wide . $wide . $wide;
							}
						}
					} else {
						$str .= '出発後' . $wide . $narrow . $narrow . $wide . $wide . $wide . $wide;
					}

					$str .= '・・・・';

					if ($cancelFee['CancelFee']['cancel_fee'] > 0) {
						if ($cancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_BASIC_RATE') {
							$str .= '基本料金の' . $cancelFee['CancelFee']['cancel_fee'] . '%';
						} elseif ($cancelFee['CancelFee']['cancel_fee_unit'] == 'RESERVE_FIXED_RATE') {
							$str .= '合計金額の' . $cancelFee['CancelFee']['cancel_fee'] . '%';
						} else {
							$str .= number_format($cancelFee['CancelFee']['cancel_fee']) . '円';
						}
					} else {
						$str .= '無料';
					}
					$policy[] = $str;

					// INCIDENT-3044 取消手続料の徴収を廃止する
					//$this->advCancelFee = $cancelFee['CancelFee']['adv_cancel_fee'];
				}
				if (!empty($cancelFees)) {
					$cancelFeeMax = (int)max(Hash::extract($cancelFees, '{n}.CancelFee.cancel_fee_max'));
					if ($cancelFeeMax > 0) {
						$policy[] = '';
						$policy[] = '・キャンセル料は' . number_format($cancelFeeMax) . '円を限度とします。';
					}
				}

				$reservationCancelPolicyArr[$reservationId] = implode($newLine, $policy);
			}
		}
		return $reservationCancelPolicyArr;
	}
}

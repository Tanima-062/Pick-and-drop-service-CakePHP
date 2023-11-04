<?php
App::uses('BaseRestApiController', 'Controller');

class BudgetStocksController extends BaseRestApiController {
	// バジェット
	protected $clientId = Constant::BUDGET_CLIENT_ID;

	public $uses = array('CarClass', 'CarClassStock', 'CarClassReservation');

	// 在庫検索
	public function index() {
		$params = $this->request->query;

		// パラメータチェック
		if (!$this->searchParamCheck($params)) {
			$this->response->statusCode(400);
			return;
		}

		$year = intval($params['year']);
		$month = intval($params['month']);
		$stock_group_id = intval($params['stock_group_id']);

		$day = isset($params['day']) ? intval($params['day']) : 0;
		$car_class_id = isset($params['car_class_id']) ? intval($params['car_class_id']) : 0;

		// 車両クラスを取得
		$car_class_list = $this->CarClass->getAvailableCarClassList($this->clientId, $stock_group_id, $car_class_id);

		if (empty($car_class_list)) {
			$this->response->statusCode(400);
			return;
		}

		if (empty($day)) {
			$first_day = 1;
			$last_day = date('t', mktime(0, 0, 0, $month + 1, 0, $year));
		} else {
			$first_day = $day;
			$last_day = $day;
		}

		$stock_list = array();

		foreach ($car_class_list as $v) {
			$c_id = intval($v['CarClass']['id']);

			// 枠数を取得
			$cs = $this->CarClassStock->getCarClassStockCount($stock_group_id, $c_id, $year, $month, $day);
			// 予約数を取得
			$cr = $this->CarClassReservation->getCarClassReservationCount($stock_group_id, $c_id, $year, $month, $day);

			for ($i = $first_day; $i <= $last_day; ++$i) {
				$max_count = isset($cs[$i]) ? intval($cs[$i]) : 0;
				$reservation_count = isset($cr[$i]) ? intval($cr[$i]) : 0;

				$stock_info = array(
					'stock_group_id'	 => $stock_group_id,
					'car_class_id'		 => $c_id,
					'stock_date'		 => sprintf('%04d%02d%02d', $year, $month, $i),
					'max_count'			 => $max_count,
					'reservation_count'	 => $reservation_count,
					'stock_count'		 => ($max_count > $reservation_count) ? $max_count - $reservation_count : 0,
				);

				$stock_list[] = array('stock_info' => $stock_info);
			}
		}

		$this->responseData['response'] = array(
			'stock_list' => $stock_list,
		);
	}

	// 在庫更新
	public function edit() {
		$params = $this->request->data;

		// パラメータチェック
		list($result, $error_message) = $this->updateParamCheck($params);
		if ($result === false) {
			$this->response->statusCode(400);
			$this->responseData['response'] = array(
				'result' => false,
				'message' => $error_message,
			);
			$this->log(sprintf("在庫更新パラメータエラー(%s)\n%s", $error_message, json_encode($params)), 'error');
			return;
		}

		$update_stocks = array();
		$insert_stocks = array();

		foreach ($result as $id_key => $v) {
			list($s_id, $c_id) = explode('-', $id_key);
			foreach ($v as $month_key => $stock_list) {
				list($year, $month) = explode('-', $month_key);
				// 現在の枠数を取得
				$cs = $this->CarClassStock->getCarClassStockIdAndCount($s_id, $c_id, $year, $month);
				if ($stock_list['has_stock_upd_day']) {
					// 現在の予約数を取得
					$cr = $this->CarClassReservation->getCarClassReservationCount($s_id, $c_id, $year, $month);
				}

				foreach ($stock_list as $day => $update_info) {
					if ($update_info['stock_upd_day']) {
						$stock_count = $update_info['count'];
						$reservation_count = isset($cr[$day]) ? $cr[$day] : 0;
						$max_count = $stock_count + $reservation_count;
					} else {
						$max_count = $update_info['count'];
					}
					if (isset($cs[$day])) {
						if ($max_count != $cs[$day]['stock_count']) {
							// 更新対象
							if (!isset($update_stocks[$max_count])) {
								$update_stocks[$max_count] = array(
									'id' => array(),
									'stock_count' => $max_count,
									'staff_id' => 0,
									'modified' => 'NOW()',
								);
							}
							$update_stocks[$max_count]['id'][] = $cs[$day]['id'];
						}
					} else {
						if ($max_count != 0) {
							// 追加対象
							$insert_stocks[] = sprintf("(%d,%d,%d,'%04d-%02d-%02d',%d,0,NOW(),NOW())", $this->clientId, $s_id, $c_id, $year, $month, $day, $max_count);
						}
					}
				}
			}
		}

		if (empty($update_stocks) && empty($insert_stocks)) {
			$this->responseData['response'] = array(
				'result' => true,
				'message' => '更新対象の在庫情報がありませんでした。',
			);
			$this->log(sprintf("在庫更新対象データなし\n%s", json_encode($params)), 'debug');
			return;
		}

		// 更新
		$this->CarClassStock->begin();

		$error = false;
		$ex_message = '';
		try {
			if (!empty($update_stocks)) {
				foreach ($update_stocks as $stocks) {
					$ret = $this->CarClassStock->bulkUpdate($stocks);
					if ($ret === false) {
						$error = true;
						$this->log(sprintf("在庫更新bulkUpdate失敗\n%s", json_encode($stocks)), 'error');
						break;
					}
				}
			}

			if (!$error && !empty($insert_stocks)) {
				$ret = $this->CarClassStock->bulkInsert($insert_stocks);
				if ($ret === false) {
					$error = true;
					$this->log(sprintf("在庫更新bulkInsert失敗\n%s", json_encode($insert_stocks)), 'error');
				}
			}
		} catch (Exception $ex) {
			$error = true;
			$ex_message = $ex->getMessage();
			$this->log(sprintf("在庫更新DB例外発生(%s)\n%s", $ex_message, $ex->getTraceAsString()), 'error');
		}

		if ($error) {
			$this->CarClassStock->rollback();
			$this->response->statusCode(500);
			$this->responseData['response'] = array(
				'result' => false,
				'message' => '在庫情報の更新に失敗しました。'.(!empty($ex_message) ? '('.$ex_message.')' : ''),
			);
			$this->log(sprintf("在庫更新失敗\n%s", json_encode($params)), 'error');
			return;
		}

		$this->CarClassStock->commit();

		$this->responseData['response'] = array(
			'result' => true,
		);
	}

	// 検索パラメータチェック
	private function searchParamCheck($params) {
		if (!(isset($params['year']) && isset($params['month']) && isset($params['stock_group_id']))) {
			return false;
		}

		if (!preg_match('/^20[1-9][0-9]$/', $params['year'])) {
			return false;
		}

		if (!preg_match('/^(0?[1-9]|1[012])$/', $params['month'])) {
			return false;
		}

		if (!preg_match('/^[1-9][0-9]*$/', $params['stock_group_id'])) {
			return false;
		}

		if (isset($params['day'])) {
			if (!preg_match('/^(0?[1-9]|[12][0-9]|3[01])$/', $params['day'])) {
				return false;
			}
		}

		if (isset($params['car_class_id'])) {
			if (!preg_match('/^[1-9][0-9]*$/', $params['car_class_id'])) {
				return false;
			}
		}

		return true;
	}

	// 更新パラメータチェック
	private function updateParamCheck($params) {
		// パラメータが存在するか、形式は正しいか？
		// ID（の組み合わせ）がバジェットに存在するか？
		// 在庫情報に重複がないか？

		if (!(isset($params['request']['stock_list']) && is_array($params['request']['stock_list']))) {
			return array(false, '在庫リストを正しく設定してください。');
		}

		// パーティションが存在する上限年月(2年以内)
		$partition_max = intval(date('Ym', strtotime('+23 month')));

		$renew_list = array();

		foreach ($params['request']['stock_list'] as $v) {
			if (!(isset($v['stock_info']) && is_array($v['stock_info']))) {
				return array(false, '在庫情報を正しく設定してください。');
			}

			$stock_info = $v['stock_info'];

			$stock_group_id = isset($stock_info['stock_group_id']) ? $stock_info['stock_group_id'] : '';
			$car_class_id = isset($stock_info['car_class_id']) ? $stock_info['car_class_id'] : '';
			$stock_date = isset($stock_info['stock_date']) ? $stock_info['stock_date'] : '';
			$max_count = isset($stock_info['max_count']) ? $stock_info['max_count'] : '';
			$stock_count = isset($stock_info['stock_count']) ? $stock_info['stock_count'] : '';

			$param_str = sprintf('(stock_group_id = [%s], car_class_id = [%s], stock_date = [%s], max_count = [%s], stock_count = [%s])', $stock_group_id, $car_class_id, $stock_date, $max_count, $stock_count);

			if (!preg_match('/^[1-9][0-9]*$/', $stock_group_id)) {
				return array(false, '在庫管理地域IDを正しく設定してください。'.$param_str);
			}
			if (!preg_match('/^[1-9][0-9]*$/', $car_class_id)) {
				return array(false, '車両クラスIDを正しく設定してください。'.$param_str);
			}

			if (!preg_match('/^(20[1-9][0-9])(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])$/', $stock_date, $date_matches)) {
				return array(false, '在庫日を正しく設定してください。'.$param_str);
			}

			$year = intval($date_matches[1]);
			$month = intval($date_matches[2]);
			$day = intval($date_matches[3]);

			$last_day = date('t', mktime(0, 0, 0, $month + 1, 0, $year));
			if ($day > $last_day) {
				return array(false, '在庫日に存在しない日付が設定されています。'.$param_str);
			}

			if ($year * 100 + $month > $partition_max) {
				return array(false, '在庫日は当月月初から2年以内の日付を設定してください。'.$param_str);
			}

			$input_stock = false;
			$count = $max_count;
			if ($max_count === '' && $stock_count === '') {
				return array(false, '枠数と在庫数のどちらかを設定してください。'.$param_str);
			}
			if ($stock_count !== '') {
				if (!preg_match('/^([1-9][0-9]*|0)$/', $stock_count)) {
					return array(false, '在庫数を正しく設定してください。'.$param_str);
				}
				$input_stock = true;
				$count = $stock_count;
			} else {
				if ($max_count !== '') {
					if (!preg_match('/^([1-9][0-9]*|0)$/', $max_count)) {
						return array(false, '枠数を正しく設定してください。'.$param_str);
					}
				}
			}

			// 在庫リストを組み直す
			$month_key = $year.'-'.$month;
			$id_key = $stock_group_id.'-'.$car_class_id;
			if (!isset($renew_list[$id_key])) {
				$ret = $this->CarClass->getAvailableCarClassList($this->clientId, $stock_group_id, $car_class_id);
				if (empty($ret)) {
					return array(false, '在庫管理地域IDと車両クラスIDの組み合わせが不正です。'.$param_str);
				}
				$renew_list[$id_key] = array($month_key => array('has_stock_upd_day' => false));
			} else {
				if (isset($renew_list[$id_key][$month_key])) {
					if (isset($renew_list[$id_key][$month_key][$day])) {
						return array(false, '在庫情報が重複しています。'.$param_str);
					}
				} else {
					$renew_list[$id_key][$month_key] = array('has_stock_upd_day' => false);
				}
			}
			if ($input_stock) {
				$renew_list[$id_key][$month_key]['has_stock_upd_day'] = true;
			}
			$renew_list[$id_key][$month_key][$day] = array('count' => $count, 'stock_upd_day' => $input_stock);
		}

		return array($renew_list, '');
	}
}

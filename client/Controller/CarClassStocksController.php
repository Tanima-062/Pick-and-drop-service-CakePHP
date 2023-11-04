<?php
App::uses('AppController', 'Controller');
//Configure::write ('debug', 2);
//ini_set('xdebug.var_display_max_children', -1);
//ini_set('xdebug.var_display_max_data', -1);
//ini_set('xdebug.var_display_max_depth', -1);

/**
 * CarClassStocks Controller
 *
 * @property CarClassStock $CarClassStock
 */
class CarClassStocksController extends AppController {

	public $yearArray;
	public $monthArray;
	public $dayArray;

	public $components = array('ReservationAPISelect');

	public $uses = array('CarClassStock', 'CarClass', 'StockGroup', 'Reservation','Area','UpdatedTable','CommodityGroup','CarClassReservation','Operation','Prefecture', 'OfficeSelectionPermission', 'OfficeStockGroup');

	public function beforeFilter() {

		parent::beforeFilter();

		set_time_limit(0);

		if (!empty($this->request->query)) {
			$this->request->data['CarClassStock'] = $this->request->query;
			$this->request->data['CarClassStock']['year'] = $this->request->data['CarClassStock']['year']['year'];
			$this->request->data['CarClassStock']['month'] = $this->request->data['CarClassStock']['month']['month'];
		}
		$this->set('is_check_user', true);
	}

	public function index() {

		// 権限営業所取得（ログインユーザー）
		$permissionOfficeList = empty($this->clientData['is_system_admin']) ? $this->OfficeSelectionPermission->getPermissionOfficeList($this->clientData['id']) : null;
		// 営業在庫地域の取得（権限営業所対応）
		$officeStockGroupData = $this->OfficeStockGroup->getStockGroups($permissionOfficeList);
		// 在庫地域の取得
		$stockGroupIdList = array();
		foreach ($officeStockGroupData as $k => $v) {
			$v = $v['OfficeStockGroup'];
			if ($v['client_id'] == $this->clientData['client_id']) {
				$stockGroupIdList[$k] = $v['stock_group_id'];
			}
		}
		$stockGroupIds = array_unique($stockGroupIdList);
		unset($officeStockGroupData);
		unset($stockGroupIdList);

		$postPrefectureId = '';
		if (!empty($this->data['CarClassStock']['prefecture_id'])) {
			$postPrefectureId = $this->data['CarClassStock']['prefecture_id'];
		}
		$stockGroupData = $this->StockGroup->getAllFindStockGroup($stockGroupIds, $postPrefectureId);
		$stockGroups = Hash::combine($stockGroupData, '{n}.StockGroup.id', '{n}.StockGroup.name'); // 地域リスト
		// 権限営業所の都道府県取得
		$prefectureStockGroupData = $this->StockGroup->getAllFindStockGroup($stockGroupIds);
		$prefectureIdList = Hash::extract($prefectureStockGroupData, '{n}.StockGroup.prefecture_id');
		$prefectureIds = array_unique($prefectureIdList);
		$prefectureData = $this->Prefecture->getAllFindPrefecture($prefectureIds);
		$prefectureList = Hash::combine($prefectureData, '{n}.Prefecture.id', '{n}.Prefecture.name'); // 都道府県リスト

		if (!empty($this->data['CarClassStock']['stock_group_id'])) {
			if (!in_array($this->data['CarClassStock']['stock_group_id'], $stockGroupIds)) {
				$this->Session->setFlash( '不正なアクセスです。', 'default', array('class' => 'alert alert-error'));
				$this->redirect(array('action'=>'index'));
			}
		}

	    $prefectureId = 0;
		if (!isset($this->data['CarClassStock'])) {

			$this->request->data['CarClassStock'] = array();
			$this->request->data['CarClassStock']['year'] = date('Y');
			$this->request->data['CarClassStock']['month'] = date('m');
			$this->request->data['searchDate'] = date('Y-m');
			$this->request->data['CarClassStock']['commodity_group_id'] = false;
		} else {

			$this->request->data['searchDate'] = $this->data['CarClassStock']['year'].'-'.$this->data['CarClassStock']['month'];
			$prefectureId = $this->request->data['CarClassStock']['prefecture_id'];
		}


		if (isset($this->data['save'])) {
			$this->_carClassStockSave();
		} else if (isset($this->data['delete'])) {
			$this->_carClassStockDelete();
		} else if(isset($this->data['all_full_car'])) {
			$this->_carClassAllFullCar();
		} else if(isset($this->data['remaining_amount'])) {
			$this->_saveRemainingAmount();
		} else if(isset($this->data['CarClassStock']['get_csv'])) {
			$this->_downloadCsvData();
		}else if(!empty($this->request->data['CarClassStock']['stock_group_id'])){
			$carClassStock = $this->CarClassStock->getCarClassStockSearch($this->data['CarClassStock'], $this->clientData['Client']['id']);
		}

		$this->set('result', $carClassStock);

		$time = mktime(0,0,0,$this->data['CarClassStock']['month'], 1, $this->data['CarClassStock']['year']);
		$lastDay = date('t', $time);
		$this->set('lastDay', $lastDay);

		$w = date('w', $time);
		$this->set('w', $w);

		// 見込み売上取得
		$expected = $this->Reservation->getExpectedValue($this->data['CarClassStock'], $this->clientData['client_id']);
		$this->set('expected', $expected);

		$carClassLists = $this->CarClass->getCarClassLists($this->clientData['client_id']);
		$this->set('carClassLists', $carClassLists);

		if (empty($stockGroups)) {
			$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id'],$prefectureId);
		}
		$this->set('stockGroups', $stockGroups);

		$commodityGroupLists = $this->CommodityGroup->getList($this->clientData['Client']['id']);
		$this->set('commodityGroupLists', $commodityGroupLists);

		$this->set('carTypeLists',$this->CarClass->getCarTypeByClientID($this->clientData['Client']['id']));

		if (empty($prefectureList)) {
			$prefectureList = $this->Prefecture->getPrefectureList();
		}

		$this->set('yearArray', $this->yearArray);
		$this->set('monthArray', $this->monthArray);
		$this->set('dayArray', $this->dayArray);
		$this->set('wday', $this->wday);
		$this->set('sundayColor', '#ffe4e1');
		$this->set('saturdayColor', '#e0ffff');
		$this->set('pastColor', '#D1D1D1');
		$this->set('suspensionColor', '#cc0000;color:#fff');
		$this->set('prefectureList',$prefectureList);
		$this->set('isRennaviApiTarget', $this->ReservationAPISelect->isRennaviApiTarget($this->clientData['client_id']));	// レンナビAPI使用のclientのみ販売停止状態を表示する
		$this->set('isJnet', (Constant::JNET_CLIENT_ID == $this->clientData['client_id']));									// Jnetへの在庫手仕舞API提供はまだなので、制御用変数を用意
	}

	//先月の在庫データを引き継ぐ
	public function takingOverData() {

		$data = $this->data;

		if(!empty($data['takingOverData'])) {

			//前月を取得
			$lastMonthData = $data;
			$year = $data['CarClassStock']['year'];
			$month = $data['CarClassStock']['month'];
			$lastMonthData['CarClassStock']['year'] = date('Y', strtotime(date($year. '-'. $month .'-1').' -1 month'));
			$lastMonthData['CarClassStock']['month'] = date('m', strtotime(date($year. '-'. $month .'-1').' -1 month'));

			//前月のデータを取得
			$lastMonthCarClassStock = $this->CarClassStock->getCarClassStockSearch($lastMonthData['CarClassStock'], $this->clientData['client_id']);

			//今月のデータを取得
			$monthCarClassStock = $this->CarClassStock->getCarClassStockSearch($data['CarClassStock'], $this->clientData['client_id']);

			foreach($monthCarClassStock as $key => $val) {
				$stockGroup = $val['StockGroup']['id'];
				$carClass = $val['CarClass']['id'];

				$monthDataStocks[$stockGroup][$carClass] = $val;
			}

			//今月の最後の日
			$lastDay = date('t',mktime(0,0,0,$month,1,$year));

			//前月の最後の日
			$lastMonthLastDay = date('t',mktime(0,0,0,$lastMonthData['CarClassStock']['month'],1,$lastMonthData['CarClassStock']['year']));

			$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
			$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);

			$i = 0;
			$values = array();
			foreach($lastMonthCarClassStock as $key => $val) {

				$stockGroupId = $val['StockGroup']['id'];
				$carClassId = $val['CarClass']['id'];

				//前月の最終日の在庫数
				if(!empty($val['CarClassStock'][$lastMonthLastDay])) {
					$max = $val['CarClassStock'][$lastMonthLastDay];
					$lastMonthStock = $max['stock_count'];
				} else {
					$lastMonthStock = 0;
				}

				for($j = 1; $j <= $lastDay; $j++) {

					$day = date('d', strtotime(date($year. '-'. $month .'-' . $j)));

					//アップデート
					if(!empty($monthDataStocks[$stockGroupId][$carClassId]['CarClassStock'][$day]['id'])) {

						$updateData[$i]['stock_count'] = $lastMonthStock;

						if(empty($updateData[$i]['staff_id'])) {
							$updateData[$i]['staff_id'] = $this->clientData['id'];
						}

						$updateData[$i]['id'][] = $monthDataStocks[$stockGroupId][$carClassId]['CarClassStock'][$day]['id'];
						$content[$stockGroupId]['area'] = $stockGroups[$stockGroupId];
						$content[$stockGroupId]['content'][$carClassId]= $carClassList[$carClassId];
					//インサート
					} else {
						if($lastMonthStock > 0){
							$values[] = "(".
								$this->clientData['client_id']."," .
								$stockGroupId."," .
								$carClassId.",".
								"'".$data['CarClassStock']['year'].'-'.$data['CarClassStock']['month'].'-'. $day. "'," .
								$lastMonthStock."," .
								$this->clientData['id']."," .
								"now()," .
								"now()
							)";
							$content[$stockGroupId]['area'] = $stockGroups[$stockGroupId];
							$content[$stockGroupId]['content'][$carClassId]= $carClassList[$carClassId];
						}
					}
				}

				$i++;
			}

			$saved = false;
			// INSERT
			if (!empty($values)) {
				$this->CarClassStock->bulkInsert($values);
				$saved = true;
			}

			// UPDATE
			if (!empty($updateData)) {
				foreach ($updateData as $val) {
					if (!empty($val['id'])) {
						$this->CarClassStock->bulkUpdate($val);
						$saved = true;
					}
				}
			}

			if ($saved) {
				$this->__updateTableSave($content,'taking_over');
			}
		}

		$redirectUrl = $this->referer();
		$this->redirect($redirectUrl);
	}

	// 満車処理
	public function clientfullCar() {

		$this->autoLayout = false;
		$this->autoRender = false;

		// ajaxでアクセスが来た場合
		if ($this->request->isAjax()) {

			$data = $this->request->data;
			$date = $data['year'] . "-" . $data['month'];

			$this->request->data['CarClassStock']['year'] = $data['year'];
			$this->request->data['CarClassStock']['month'] = $data['month'];
			list($stockGroupId,$carClassId) = explode("-", $data['name']);

			$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
			$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);

			// 更新用のデータ
			$content[$stockGroupId]['area'] = $stockGroups[$stockGroupId];

			$reserveData['year'] = $data['year'];
			$reserveData['month'] = $data['month'];
			$reserveData['stock_group_id'] = $stockGroupId;
			$reserveData['car_class_id'] = $carClassId;

			// 予約数取得
			$reservetionList = $this->CarClassReservation->getCarClassReservationCount($reserveData, $this->clientData['client_id']);

			// 満車の場合
			if (!empty($data['full_car_flg']) && !empty($this->clientData['client_id'])) {
				$stockCheck = explode(',', $data['stock_check']);
				$saveData = array();
				$i = 0;

				foreach ($stockCheck as $stock) {
					if (preg_match("/Check\[([0-9]*)\]/", $stock, $m)) {

						// 更新テーブル用データ
						if (empty($content[$stockGroupId]['content'][$carClassId])) {
							$content[$stockGroupId]['content'][$carClassId] = '';
						}
						$content[$stockGroupId]['content'][$carClassId] = $carClassList[$carClassId];

						$this->CarClassStock->recursive = -1;
						$carClassStock = $this->CarClassStock->find('first', array(
								'conditions'=>array(
									'car_class_id' => $carClassId,
									'client_id' => $this->clientData['client_id'],
									'stock_group_id' => $stockGroupId,
									'stock_date' => $date . '-' .$m[1]
								)
							)
						);

						if (empty($carClassStock)) {
							continue;
						}

						if (!empty($reservetionList[$m[1]])) {
							$stockCount = $reservetionList[$m[1]];
						} else {
							$stockCount = 0;
						}

						$saveData[$i]['id'] = $carClassStock['CarClassStock']['id'];
						$saveData[$i]['stock_count'] = $stockCount;
						$saveData[$i]['staff_id'] = $this->clientData['id'];

						$i++;
					}
				}

				if (!empty($saveData)) {
					if ($this->CarClassStock->saveAll($saveData)) {
						$this->__updateTableSave($content, 'full_car');
						echo '満車処理を実行しました。';
					} else {
						echo '満車処理に失敗しました';
					}
				}
			} else {
				echo '満車処理に失敗しました';
			}
		}
	}

	protected function _carClassStockSave() {

		$data = $this->data;

		if (!empty($this->data['CarClassStock']['min_date']) && !empty($this->data['CarClassStock']['max_date'])) {
			$results = $this->CarClassStock->getCarClassStockSearch($this->data['CarClassStock'], $this->clientData['Client']['id']);
		} else {
			$results = json_decode($data['CarClassStock']['result'], true);
		}

		$carClassStockUpdateStack = array(array('id' => ''));
		$carClassStockInsertStack = array();
		$idStacks = array();
		$index = 0;

		$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
		$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);

		// 期間が選択されておらず一つもﾁｪｯｸがされていないとき
		if (!isset($data['Check']) && empty($this->data['CarClassStock']['min_date']) &&
				empty($this->data['CarClassStock']['max_date'])) {

			//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
			$redirectUrl = $this->referer();
			$this->redirect($redirectUrl);
		}

		foreach ($data['StockCount'] as $stockCountKey => $stockCountVal) {

			$insertStockVal = $stockCountVal;

			// 在庫数が入力されているか？
			if (strlen($stockCountVal)) {

				$tmp = explode('-', $stockCountKey);
				$stockGroupId = $tmp[0];
				$carClassId = $tmp[1];

				//updateTable 用のデータ
				$content[$stockGroupId]['area'] = $stockGroups[$stockGroupId];

				foreach ($results as $result) {

					// 在庫管理地域と車両ｸﾗｽが一致すれば
					if ($result['StockGroup']['id'] == $stockGroupId && $result['CarClass']['id'] == $carClassId) {

						if (!empty($data['Check'])) {

							foreach ($data['Check'] as $checkKey => $checkVal) {

								// ﾁｪｯｸﾎﾞｯｸｽがﾁｪｯｸされているか？
								if ($checkVal) {

									// すでに登録されていたらUPDATE
									if (isset($result['CarClassStock'][$checkKey])) {

										if ($result['CarClassStock'][$checkKey]['stock_count'] == $stockCountVal) {
											continue;
										}

										if (isset($beforeStockCountVal) && $beforeStockCountVal != $stockCountVal) {
											$idStacks = array();
											$index++;
										}

										// idのスタック
										array_push($idStacks, $result['CarClassStock'][$checkKey]['id']);
										$carClassStockUpdateStack[$index]['id'] = $idStacks;
										$carClassStockUpdateStack[$index]['stock_count'] = $stockCountVal;
										$carClassStockUpdateStack[$index]['staff_id'] = $this->clientData['id'];
										$beforeStockCountVal = $stockCountVal;

									} else {
										if($stockCountVal > 0){
											$values = "(".$this->clientData['client_id'].",";
											$values .= $stockGroupId.",";
											$values .= $carClassId.",";
											$values .= "'".$data['CarClassStock']['year'].'-'.$data['CarClassStock']['month'].'-'.$checkKey."',";
											$values .= $stockCountVal.",";
											$values .= $this->clientData['id'].",";
											$values .= "now(),";
											$values .= "now())";
											array_push($carClassStockInsertStack, $values);
										}



									}

									//更新テーブル用データ
									if(empty($content[$stockGroupId]['content'][$carClassId])) {
										$content[$stockGroupId]['content'][$carClassId] = '';
									}
									$content[$stockGroupId]['content'] [$carClassId]= $carClassList[$carClassId];

								}
							}
						} else if (!empty($this->data['CarClassStock']['min_date']) &&
								!empty($this->data['CarClassStock']['max_date'])) {

							// トランザクション開始
							$this->CarClassStock->begin();

							$this->CarClassStock->spanStockSave($stockGroupId,$carClassId,$insertStockVal,$this->clientData,
									$this->data['CarClassStock']['min_date'],$this->data['CarClassStock']['max_date']);

							$this->CarClassStock->commit();

							//更新テーブル用データ
							if(empty($content[$stockGroupId]['content'][$carClassId])) {
								$content[$stockGroupId]['content'][$carClassId] = '';
							}
							$content[$stockGroupId]['content'] [$carClassId]= $carClassList[$carClassId];

						}
					}
				}
			}
		}

		// INSERT
		if (!empty($carClassStockInsertStack)) {
			$this->CarClassStock->bulkInsert($carClassStockInsertStack);
		}

		// UPDATE
		if (!empty($carClassStockUpdateStack)) {

			foreach ($carClassStockUpdateStack as $val) {
				if (!empty($val['id'])) {
					$this->CarClassStock->bulkUpdate($val);
				}
			}
		}

		if(!empty($content)) {
			$this->__updateTableSave($content);
		}

		//$redirectUrl = $this->data['CarClassStock']['url'];
		$redirectUrl = $this->referer();
		$this->redirect($redirectUrl);
	}


	//一括売り止め
	protected function _carClassStockDelete() {

		$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
		$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);

		$data = $this->data;
		$results = json_decode($data['CarClassStock']['result'], true);
		$i = 0;
		$carClassStockUpdateStack[$i] = array(
				'id' => array(),
				'stock_count' => 0,
				'staff_id' => $this->clientData['id']);
		$carClassStockInsertStack = array();

		// 一つもﾁｪｯｸがされていないとき
		if (!isset($data['Check'])) {
			return true;
		}

		foreach ($data['StockCount'] as $stockCountKey => $stockCountVal) {

			$tmp = explode('-', $stockCountKey);
			$stockGroupId = $tmp[0];
			$carClassId = $tmp[1];

			//updateTable 用のデータ
			$content[$stockGroupId]['area'] = $stockGroups[$stockGroupId];

			//更新テーブル用データ
			if(isset($content[$stockGroupId]['content'][$carClassId])) {
				$content[$stockGroupId]['content'][$carClassId] = '';
			}
			$content[$stockGroupId]['content'][$carClassId] = $carClassList[$carClassId];

			foreach ($results as $result) {

				// 在庫管理地域と車両ｸﾗｽが一致すれば
				if ($result['StockGroup']['id'] == $stockGroupId && $result['CarClass']['id'] == $carClassId) {

					foreach ($data['Check'] as $checkKey => $checkVal) {

						// ﾁｪｯｸﾎﾞｯｸｽがﾁｪｯｸされているか？
						if ($checkVal) {

							// すでに登録されていて在庫数が変わるならUPDATE
							if (isset($result['CarClassStock'][$checkKey])) {

								if ($result['CarClassStock'][$checkKey]['stock_count'] == 0) {
									continue;

								} else {

									if (isset($lastStockCount) && $lastStockCount != 0) {
										$i++;
									}
									$carClassStockUpdateStack[$i]['stock_count'] = 0;
									$carClassStockUpdateStack[$i]['staff_id'] = $this->clientData['id'];
									if (!isset($carClassStockUpdateStack[$i]['id'])) {
										$carClassStockUpdateStack[$i]['id'] = array();
									}
									$lastStockCount = 0;
								}
								array_push($carClassStockUpdateStack[$i]['id'], $result['CarClassStock'][$checkKey]['id']);

							} else {
								//在庫は存在していない時、insertしない
								/*$values = "(".$this->clientData['client_id'].",";
								$values .= $stockGroupId.",";
								$values .= $carClassId.",";
								$values .= "'".$data['CarClassStock']['year'].'-'.$data['CarClassStock']['month'].'-'.$checkKey."',";
								$values .= "0,";
								$values .= $this->clientData['id'].",";
								$values .= "now(),";
								$values .= "now())";

								array_push($carClassStockInsertStack, $values);*/

							}

						}
					}
				}
			}
		}
		//在庫は存在していない時、insertしない
		// INSERT
		/*if (!empty($carClassStockInsertStack)) {
			$this->CarClassStock->bulkInsert($carClassStockInsertStack);
		}*/
		// UPDATE
		if (!empty($carClassStockUpdateStack)) {
			foreach ($carClassStockUpdateStack as $val) {
				if (!empty($val['id'])) {
					$this->CarClassStock->bulkUpdate($val);
				}
			}
		}

		$this->__updateTableSave($content,'delete_car');

		//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
		$redirectUrl = $this->referer();
		$this->redirect($redirectUrl);

	}



	//一括満車
	protected function _carClassAllFullCar() {

		$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
		$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);

		$data = $this->data;
		$results = json_decode($data['CarClassStock']['result'], true);
		$i = 0;
		$carClassStockUpdateStack[$i] = array(
				'id' => array(),
				'stock_count' => 0,
				'staff_id' => $this->clientData['id']);

		// 一つもﾁｪｯｸがされていないとき
		if (!isset($data['Check'])) {
			$this->Session->setFlash('エラー:一括満車処理に失敗しました。日付をチェックしてください。','customFlashError');
			//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
			$redirectUrl = $this->referer();
			$this->redirect($redirectUrl);
		}

		//予約数取得
		$reserveData['from_date'] = $data['CarClassStock']['year'] . '-' .$data['CarClassStock']['month'] .'-' . '01';
		$reserveData['to_date'] = $data['CarClassStock']['year'] . '-' .$data['CarClassStock']['month'] .'-' . '31';
		$reservetionList = $this->CarClassReservation->getCarClassReservationCountAll($reserveData,$this->clientData['client_id']);

		$i = 0;
		foreach ($data['StockCount'] as $stockCountKey => $stockCountVal) {

			$tmp = explode('-', $stockCountKey);
			$stockGroupId = $tmp[0];
			$carClassId = $tmp[1];


			//updateTable 用のデータ
			$content[$stockGroupId]['area'] = $stockGroups[$stockGroupId];

			//更新テーブル用データ
			if(isset($content[$stockGroupId]['content'][$carClassId])) {
				$content[$stockGroupId]['content'][$carClassId] = '';
			}
			$content[$stockGroupId]['content'] [$carClassId]= $carClassList[$carClassId];


			foreach ($results as $result) {

				// 在庫管理地域と車両ｸﾗｽが一致すれば
				if ($result['StockGroup']['id'] == $stockGroupId && $result['CarClass']['id'] == $carClassId) {

					foreach ($data['Check'] as $checkKey => $checkVal) {

						// ﾁｪｯｸﾎﾞｯｸｽがﾁｪｯｸされているか？
						if ($checkVal) {

							if(empty($result['CarClassStock'][$checkKey]['id']) || is_array($result['CarClassStock'][$checkKey]['id'])) {
								continue;
							}

							// すでに登録されていて在庫数が変わるならUPDATE
							if (isset($result['CarClassStock'][$checkKey])) {

								if(!empty($reservetionList[$data['CarClassStock']['year']][$data['CarClassStock']['month']][$checkKey][$stockGroupId][$carClassId])) {
									$reserveCount = $reservetionList[$data['CarClassStock']['year']][$data['CarClassStock']['month']][$checkKey][$stockGroupId][$carClassId];
								} else {
									$reserveCount = 0;
								}

								if ($result['CarClassStock'][$checkKey]['stock_count'] == $reserveCount) {
									continue;

								} else {

									if(!empty($reserveCount)) {
										$carClassStockUpdateStack[$i]['stock_count'] = $reserveCount;
									} else {
										$carClassStockUpdateStack[$i]['stock_count'] = 0;
									}

									$carClassStockUpdateStack[$i]['staff_id'] = $this->clientData['id'];

									$carClassStockUpdateStack[$i]['id'] = $result['CarClassStock'][$checkKey]['id'];
								}
							}
						}

						$i++;
					}
				}

			}
		}


		// UPDATE
		if (!empty($carClassStockUpdateStack) && !empty($carClassStockUpdateStack[0]['id'] )) {

			if($this->CarClassStock->saveAll($carClassStockUpdateStack)) {
				$this->__updateTableSave($content,'full_car');
			}
		}
		//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
		$redirectUrl = $this->referer();
		$this->redirect($redirectUrl);

	}

	/**
	 * 残数設定
	 */
	protected function _saveRemainingAmount() {

		$data = $this->data;

		//予約数取得
		if(!empty($data['CarClassStock']['min_date']) && !empty($data['CarClassStock']['max_date'])) {
			$reserveData['from_date'] = $data['CarClassStock']['min_date'];
			$reserveData['to_date'] = $data['CarClassStock']['max_date'];
		} else {
			if(!empty($data['CarClassStock']['year']) && !empty($data['CarClassStock']['month'])) {
				$reserveData['from_date'] = $data['CarClassStock']['year'] . "-" . $data['CarClassStock']['month'] . "-" . "01";
				$reserveData['to_date'] = $data['CarClassStock']['year'] . "-" . $data['CarClassStock']['month'] . "-" . "31";
			}
		}

		//日付が無い場合はリダイレクト
		if(empty($reserveData['from_date']) || empty($reserveData['to_date'])) {
			$this->Session->setFlash('エラー:日付を選択してください。','customFlashError');
			//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
			$redirectUrl = $this->referer();
			$this->redirect($redirectUrl);
		}

		$reservetionList = $this->CarClassReservation->getCarClassReservationCountAll($reserveData,$this->clientData['client_id']);

		if (
				(!empty($this->data['CarClassStock']['min_date']) && !empty($this->data['CarClassStock']['max_date'])) ||
				(!empty($this->data['CarClassStock']['year']) && !empty($this->data['CarClassStock']['month']) && !empty($this->data['Check']))
				) {
			$results = $this->CarClassStock->getCarClassStockSearch($this->data['CarClassStock'], $this->clientData['Client']['id']);
		} else {
			$this->Session->setFlash('エラー:残数設定に失敗しました。日付を入力してください。','customFlashError');
			//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
			$redirectUrl = $this->referer();
			$this->redirect($redirectUrl);
		}

		$carClassStockUpdateStack = array(array('id' => ''));
		$carClassStockInsertStack = array();
		$idStacks = array();
		$index = 0;

		$stockGroups = $this->StockGroup->getStockGroupList($this->clientData['client_id']);
		$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);

		// 期間が選択されておらず一つもﾁｪｯｸがされていないとき
		if (!isset($data['Check']) && empty($this->data['CarClassStock']['min_date']) &&
				empty($this->data['CarClassStock']['max_date'])) {

				//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
				$redirectUrl = $this->referer();
				$this->redirect($redirectUrl);
		}

		foreach ($data['StockCount'] as $stockCountKey => $stockCountVal) {

			$insertStockVal = $stockCountVal;

			// 残数が入力されているか？
			if (strlen($stockCountVal)) {

				$tmp = explode('-', $stockCountKey);
				$stockGroupId = $tmp[0];
				$carClassId = $tmp[1];

				//updateTable 用のデータ
				$content[$stockGroupId]['area'] = $stockGroups[$stockGroupId];

				foreach ($results as $result) {

					// 在庫管理地域と車両ｸﾗｽが一致すれば
					if ($result['StockGroup']['id'] == $stockGroupId && $result['CarClass']['id'] == $carClassId) {

						//対象期間在庫設定の場合
						if (!empty($this->data['CarClassStock']['min_date']) &&
								!empty($this->data['CarClassStock']['max_date'])) {

							$minDate = $this->data['CarClassStock']['min_date'];
							$maxDate = $this->data['CarClassStock']['max_date'];

							$this->CarClassStock->RemainingAmounSave($stockGroupId,$carClassId,$this->clientData,$minDate,$maxDate,$reservetionList,$stockCountVal);

							//更新テーブル用データ
							if(empty($content[$stockGroupId]['content'][$carClassId])) {
								$content[$stockGroupId]['content'][$carClassId] = '';
							}
							$content[$stockGroupId]['content'] [$carClassId]= $carClassList[$carClassId];

						} else {
						//日付チェックで残数更新を押した場合
							if (!empty($data['Check'])) {

								$stockYear = $data['CarClassStock']['year'];
								$stockMonth = $data['CarClassStock']['month'];

								foreach ($data['Check'] as $checkKey => $checkVal) {

									// ﾁｪｯｸﾎﾞｯｸｽがﾁｪｯｸされているか？
									if ($checkVal) {

										if(!empty($reservetionList[$stockYear][$stockMonth][$checkKey][$stockGroupId][$carClassId])) {
											$reserveNum = $reservetionList[$stockYear][$stockMonth][$checkKey][$stockGroupId][$carClassId];
										} else {
											$reserveNum = 0;
										}

										$stockValue = $stockCountVal + $reserveNum;

										// すでに登録されていたらUPDATE
										if (isset($result['CarClassStock'][$checkKey])) {

											if ($result['CarClassStock'][$checkKey]['stock_count'] == $stockValue) {
												continue;
											}

											if (isset($beforeStockCountVal) && $beforeStockCountVal != $stockValue) {
												$idStacks = array();
												$index++;
											}

											// idのスタック
											array_push($idStacks, $result['CarClassStock'][$checkKey]['id']);
											$carClassStockUpdateStack[$index]['id'] = $idStacks;
											$carClassStockUpdateStack[$index]['stock_count'] = $stockValue;
											$carClassStockUpdateStack[$index]['staff_id'] = $this->clientData['id'];
											$beforeStockCountVal = $stockValue;

										} else {
											if($stockValue > 0){
												$values = "(".$this->clientData['client_id'].",";
												$values .= $stockGroupId.",";
												$values .= $carClassId.",";
												$values .= "'".$data['CarClassStock']['year'].'-'.$data['CarClassStock']['month'].'-'.$checkKey."',";
												$values .= $stockValue.",";
												$values .= $this->clientData['id'].",";
												$values .= "now(),";
												$values .= "now())";

												$carClassStockInsertStack[] = $values;
											}

										}

										//更新テーブル用データ
										if(empty($content[$stockGroupId]['content'][$carClassId])) {
											$content[$stockGroupId]['content'][$carClassId] = '';
										}
										$content[$stockGroupId]['content'] [$carClassId]= $carClassList[$carClassId];

									}
								}

								// UPDATE
								if (!empty($carClassStockUpdateStack)) {
									foreach ($carClassStockUpdateStack as $val) {
										if (!empty($val['id'])) {
											$this->CarClassStock->bulkUpdate($val);
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// INSERT
		if (!empty($carClassStockInsertStack)) {
			$this->CarClassStock->bulkInsert($carClassStockInsertStack);
		}

		if(!empty($content)) {
			$this->__updateTableSave($content);
		}

		//$redirectUrl = str_replace('/client','',$this->data['CarClassStock']['url']);
		$redirectUrl = $this->referer();
		$this->redirect($redirectUrl);
	}

	/**
	 * csvを出力
	 */
	protected function _downloadCsvData() {

		Configure::write('debug', 0); // debugコードを出さない
		$this->autoRender = false; // Viewを使わない

		// 車両在庫情報を取得
		$carClassStock = $this->CarClassStock->getCarClassStockSearch($this->data['CarClassStock'], $this->clientData['Client']['id']);
		// 指定された年月の最終日を取得
		$time = mktime(0,0,0,$this->data['CarClassStock']['month'], 1, $this->data['CarClassStock']['year']);
		$lastDay = date('t', $time);

		//// CSVの処理
		// ファイル名
		$csvFile = date('YmdHis'). '.csv';
		// ヘッダ出力
		header ("Content-disposition: attachment; filename=" . $csvFile);
		header ("Content-type: application/octet-stream; name=" . $csvFile);
		// ストリーム出力
		$fp = @fopen('php://output', 'w');
		if (!$fp) {
			exit;
		}
		// SJIS指定
		stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932//TRANSLIT');

		// ヘッダー行出力
		$csvData = '会社,地域,車両クラス,,';
		for ($day=1; $day <= $lastDay; $day++) {
			$csvData .= date('Y/m/', $time).$day.',';
		}
		$csvData .= '合計';
		$csvData .= "\r\n";
		fwrite($fp, $csvData);

		$isRennaviApiTarget = $this->ReservationAPISelect->isRennaviApiTarget($this->clientData['client_id']);	// レンナビAPI使用のclientのみ販売停止状態を表示する
		$isJnet = (Constant::JNET_CLIENT_ID == $this->clientData['client_id'])? true : false;					// Jnetへの在庫手仕舞API提供はまだなので、制御用変数を用意

		// 車両在庫情報出力
		foreach($carClassStock as $key => $val) {
			// 1車両クラスにつき3行（枠、予約、残）出力する
			for ($i=0; $i < 3; $i++) {
				// 各車両クラスの1行目のみ、会社名、地域名、車両クラス名を出力
				if ($i == 0) {
					$csvData = $this->clientData['Client']['name'].','.$val['StockGroup']['name'].','.$val['CarClass']['name'].',';
				} else {
					$csvData = ',,,';
				}
				$total = 0;
				if ($i == 0) {
					// 各車両クラスの1行目（枠）を出力
					$csvData .= '枠,';
					for ($day=1; $day <= $lastDay; $day++) {
						$index = sprintf("%02d", $day);
						if (!empty($val['CarClassStock'][$index]['stock_count'])) {
							// レンナビAPI使用のclientのみ販売停止状態を表示する（Jnetへの在庫手仕舞API提供はまだなので表示しない）
							if ($isRennaviApiTarget && !$isJnet && isset($val['CarClassStock'][$index]['suspension']) && $val['CarClassStock'][$index]['suspension'] == 1) {
								$csvData .= '停 ';
							}
							$csvData .= $val['CarClassStock'][$index]['stock_count'].',';
							$total += $val['CarClassStock'][$index]['stock_count'];
						} else if (isset($val['CarClassStock'][$index]['stock_count'])) {
							$csvData .= '止,';
						} else {
							$csvData .= '0,';
						}
					}
				} else if ($i == 1) {
					// 各車両クラスの2行目（予約）を出力
					$csvData .= '予約,';
					for ($day=1; $day <= $lastDay; $day++) {
						$index = sprintf("%02d", $day);
						if (!empty($val['CarClassReservation'][$index])) {
							$csvData .= $val['CarClassReservation'][$index].',';
							$total += $val['CarClassReservation'][$index];
						} else {
							$csvData .= '0,';
						}
					}
				} else if ($i == 2) {
					// 各車両クラスの3行目（残）を出力
					$csvData .= '残,';
					for ($day=1; $day <= $lastDay; $day++) {
						$index = sprintf("%02d", $day);
						if (!empty($val['CarClassStock'][$index]['stock_count'])) {
							$diff = $val['CarClassStock'][$index]['stock_count'];
							if (!empty($val['CarClassReservation'][$index])) {
								$diff -= $val['CarClassReservation'][$index];
								$diff = max(0, $diff);
							}
							$total += $diff;
							$csvData .= $diff.',';
						} else {
							$csvData .= '0,';
						}
					}
				}

				$csvData .= $total;
				$csvData .= "\r\n";
				fwrite($fp, $csvData);
			}
		}

		fclose($fp);
		exit;
	}

	//更新テーブルに値を挿入
	private function __updateTableSave($content,$str = '') {

		$data = $this->data;
		$i = 0;

		if(!empty($str)) {
			$operation = $this->Operation->getOperationList($str);
			$category = '在庫管理【'. $operation['name'] . '】';
			$operationId = $operation['id'];
		} else {
			$category ='在庫管理';
		}

		if(is_array($content) && !empty($content)) {

			if (empty($data['CarClassStock']['min_date']) || empty($data['CarClassStock']['max_date'])) {
				$updateDate = $data['CarClassStock']['year'] . "年" . $data['CarClassStock']['month'] . "月" ;
			} else {
				$updateDate = $data['CarClassStock']['min_date'] . "~" . $data['CarClassStock']['max_date'];
			}

			foreach($content as $key => $val) {

				if(!empty($operationId)) {
					$saveData[$i]['operation_id'] = $operationId;
				}
				$saveData[$i]['category'] = $category;
				$saveData[$i]['client_id'] = $this->clientData['client_id'];
				$saveData[$i]['staff_id'] = $this->clientData['id'];
				$saveData[$i]['content'] = '在庫データ (' .$updateDate . "・" .$val['area'] . "・";
				if(!empty($val['content'])) {
					foreach($val['content'] as $key => $carClassName) {
						$saveData[$i]['content'] .= $carClassName . ',';
					}
				}

				$saveData[$i]['content'] = rtrim($saveData[$i]['content'],",");

				$saveData[$i]['content'] .= ') を更新しました。';
				$i++;
			}

			$this->UpdatedTable->saveMany($saveData);
		}
	}

}

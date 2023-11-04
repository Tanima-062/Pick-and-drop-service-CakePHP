<?php
App::uses('AppController', 'Controller');

/**
 * Plan Controller
 *
 * @property Plan $Plan
 */
class PlanController extends AppController {

	public $components = array('BreadCrumb', 'PlanUtil', 'Cookie');
	public $uses = array(
		'CommodityItem', 'Commodity', 'CommodityPrivilege', 'CommodityEquipment', 'CommodityImage',
		'DisclaimerCompensation', 'Equipment', 'ClientCard', 'DropOffAreaRate', 'Office', 'CancelFee',
		'Maintenance'
	);
	// 車両年式0
	public $newCarRegistration = array(
		1 => '新車登録1年以内',
		2 => '新車登録2年以内',
		3 => '新車登録3年以内',
		4 => '新車登録4年以内',
		5 => '新車登録5年以上'
	);
	// 喫煙・禁煙
	public $smokingCarList = array(
		0 => '禁煙車',
		1 => '喫煙車',
		2 => '指定なし',
	);
	// planのパラメータエラー時のView
	private $planErrorView = '/Errors/error404';

	function beforeFilter() {
		parent::beforeFilter();

		// 検索で保持したPRを削除しておく
		$this->Cookie->delete(Constant::PR_MAP_COOKIE_NAME);
		$this->Cookie->delete(Constant::PR_PLAN_COOKIE_NAME);

		// Ajaxの場合はbeforeFilterの処理を通さない
		if ($this->request->is('ajax') && $this->action != 'ajaxAction' && $this->action != 'getPlanInfo') {
			exit;
		}

		foreach ((array)$this->uses as $model) {
			$this->$model->setDataSource('default_slave');
		}

		// robots noindex
		$this->set('meta_robots', 'noindex');

		$this->set('smokingCarList', $this->smokingCarList);
	}

	/**
	 * プラン詳細・見積もり
	 * @return void
	 */
	public function index() {
		$this->loadComponent('OfficeUtil');
		$this->log($this->Session->id().'[plan/index]', LOG_DEBUG);
		$expiredFlg = false;
		$nowDateTime = date('Y-m-d H:i:s');
		if (!empty($this->request->query) || isset($this->params->itemId)) {
			$this->request->data = $this->request->query;
		}

		if (empty($this->params['commodityItemId']) || !is_numeric($this->params['commodityItemId'])) {
			// commodityItemIdが数字じゃなかったらパラメータエラー
			$this->render($this->planErrorView);
			return;
		}

		if ($this->Session->check('reservation.plan')) {
			$postData = json_decode($this->Session->read('reservation.plan'), true);
			if (!empty($postData['sheet'])) {
				$this->request->data['sheet'] = $postData['sheet'];
			}
			if (!empty($postData['privilege'])) {
				$this->request->data['privilege'] = $postData['privilege'];
			}
		}

		// パンくず用のリファラーを保持する(何もない場合は何もない検索ページに飛ばす)
		// ここより上位からパンくずを辿ってきた時に上書きさせない
		if (!preg_match('/\/(plan|reservations)/', $this->referer(null, true))) {
			$this->Session->write('BreadCrumb.search', $this->referer(array('controller' => 'searches', 'action' => 'index')));
		}

		// 検索結果のパラメータを取得
		if (preg_match('/searches?/', $this->referer())) {
			// リファラを優先的に使う
			$backSearch = explode('searches', $_SERVER["HTTP_REFERER"])[1];
		} else if ($this->Session->check('reservation.backSearch')) {
			// セッションの値があれば使う
			$backSearch = $this->Session->read('reservation.backSearch');
		} else {
			$backSearch = '';
		}

		// セッション削除
		if ($this->Session->check('message')) {
			$sessionMessage = $this->Session->read('message');
			$this->set('sessionMessage', $sessionMessage);
			$this->Session->delete('message');
			$this->log($this->Session->id().'[plan/index] session message delete', LOG_DEBUG);
		}
		if ($this->Session->check('reservation')) {
			$this->Session->delete('reservation');
			$this->log($this->Session->id().'[plan/index] session reservation delete', LOG_DEBUG);
		}
		if ($this->Session->check('referer')) {
			$this->Session->delete('referer');
			$this->log($this->Session->id().'[plan/index] session referer delete', LOG_DEBUG);
		}

		$fromDateTime = null;
		$toDateTime = null;

		if (empty($this->request->query['from']) || empty($this->request->query['time']) ||
				empty($this->request->query['to']) || empty($this->request->query['return_time'])) {
			$this->render($this->planErrorView);
			return;
		} else {
			// 貸出日時
			list($fromY, $fromM, $fromD) = explode('-', $this->request->query['from']);
			list($fromH, $fromI) = explode('-', $this->request->query['time']);
			$fromS = '00';
			$fromDateTime = date('Y-m-d H:i:s', mktime($fromH, $fromI, $fromS, $fromM, $fromD, $fromY));
			$fromDate = date('Y-m-d', mktime(0, 0, 0, $fromM, $fromD, $fromY));
			// 返却日時
			list($toY, $toM, $toD) = explode('-', $this->request->query['to']);
			list($toH, $toI) = explode('-', $this->request->query['return_time']);
			$toS = '00';
			$toDateTime = date('Y-m-d H:i:s', mktime($toH, $toI, $toS, $toM, $toD, $toY));

			// 貸出日時チェック
			if (!checkdate($fromM, $fromD, $fromY) || !$this->PlanUtil->checktime($fromH, $fromI, $fromS)) {
				$this->render($this->planErrorView);
				return;
			}
			// 返却日時チェック
			if (!checkdate($toM, $toD, $toY) || !$this->PlanUtil->checktime($toH, $toI, $toS)) {
				$this->render($this->planErrorView);
				return;
			}
			// 貸出返却期間チェック
			if (strtotime($fromDateTime) > strtotime($toDateTime)) {
				$this->render($this->planErrorView);
				return;
			}
		}

		$requestData = array();
		// 貸出日と返却日がなかったらリダイレクト
		if (empty($fromDateTime) || empty($toDateTime) || strtotime($nowDateTime) >= strtotime($fromDateTime)) {
			$expiredFlg = true;
		}

		// 貸出日時
		$requestData['from'] = $fromDateTime;
		// 返却日時
		$requestData['to'] = $toDateTime;
		// 大人人数
		if (!empty($postData['Reservation']['adults'])) {
			$requestData['adults'] = $postData['Reservation']['adults'];
		} else {
			if (!empty($this->request->query['adults_count'])) {
				$requestData['adults'] = $this->request->query['adults_count'];
			} else {
				$requestData['adults'] = 1;
			}
		}
		// 子供人数
		if (!empty($postData['Reservation']['children'])) {
			$requestData['children'] = $postData['Reservation']['children'];
		} else {
			if (!empty($this->request->query['children_count'])) {
				$requestData['children'] = $this->request->query['children_count'];
			} else {
				$requestData['children'] = 0;
			}
		}
		// 幼児人数
		if (!empty($postData['Reservation']['infants'])) {
			$requestData['infants'] = $postData['Reservation']['infants'];
		} else {
			if (!empty($this->request->query['infants_count'])) {
				$requestData['infants'] = $this->request->query['infants_count'];
			} else {
				$requestData['infants'] = 0;
			}
		}
		// 貸出営業所(エリアID)
		if ($this->data['place'] == 1 && !empty($this->request->query['area_id'])) {
			$requestData['area_id'] = $this->request->query['area_id'];
		}
		// 貸出営業所(新幹線ID)
		if ($this->data['place'] == 2 && !empty($this->request->query['bullet_train_id'])) {
			$requestData['bullet_train_id'] = $this->request->query['bullet_train_id'];
		}
		// 貸出営業所(空港ID)
		if ($this->data['place'] == 3 && !empty($this->request->query['airport_id'])) {
			$requestData['airport_id'] = $this->request->query['airport_id'];
		}
		// 貸出営業所(ローカル駅ID)
		if ($this->data['place'] == 4 && !empty($this->request->query['station_id'])) {
			$requestData['station_id'] = $this->request->query['station_id'];
		}
		// 貸出営業所(営業所ID)
		if (!empty($this->request->query['office_id'])) {
			$requestData['office_id'] = $this->request->query['office_id'];
		}

		// 返却営業所(エリアID)
		if (!empty($this->data['return_place']) && $this->data['return_place'] == 1 && !empty($this->request->query['return_area_id'])) {
			$requestData['return_area_id'] = $this->request->query['return_area_id'];
		} else if (empty($this->data['return_place']) && $this->data['place'] == 1) {
			$requestData['return_area_id'] = $this->request->query['area_id'];
		}
		// 返却営業所(新幹線ID)
		if (!empty($this->data['return_place']) && $this->data['return_place'] == 2 && !empty($this->request->query['return_bullet_train_id'])) {
			$requestData['return_bullet_train_id'] = $this->request->query['return_bullet_train_id'];
		} else if (empty($this->data['return_place']) && $this->data['place'] == 2) {
			$requestData['return_bullet_train_id'] = $this->request->query['bullet_train_id'];
		}
		// 返却営業所(空港ID)
		if (!empty($this->data['return_place']) && $this->data['return_place'] == 3 && !empty($this->request->query['return_airport_id'])) {
			$requestData['return_airport_id'] = $this->request->query['return_airport_id'];
		} else if (empty($this->data['return_place']) && $this->data['place'] == 3) {
			$requestData['return_airport_id'] = $this->request->query['airport_id'];
		}
		// 返却営業所(ローカル駅ID)
		if (!empty($this->data['return_place']) && $this->data['return_place'] == 4 && !empty($this->request->query['return_station_id'])) {
			$requestData['return_station_id'] = $this->request->query['return_station_id'];
		} else if (empty($this->data['return_place']) && $this->data['place'] == 4) {
			$requestData['return_station_id'] = $this->request->query['station_id'];
		}
		// 返却営業所(営業所ID)
		if (!empty($this->request->query['return_office_id'])) {
			$requestData['return_office_id'] = $this->request->query['return_office_id'];
		}
		// PR商品を経由された場合のみフラグを保持し、それ以外は削除
		if (!empty($this->data['recommend_flg']) && $this->data['recommend_flg'] == '1') {
			$this->Session->write('recommend_flg', '1');
		} else {
			$this->Session->delete('recommend_flg');
		}
		// 支払い方法
		if (!is_null($postData['Reservation']['payment_method'])) {
			$defaultPaymentMethod = $postData['Reservation']['payment_method'];
		} else {
			$defaultPaymentMethod = 1;
		}


		// 商品アイテムデータ取得
		$commodityItemPriceData = $this->CommodityItem->getCommodityItemPriceData($this->params['commodityItemId'], $fromDate);
		if (!empty($commodityItemPriceData)) {
			// クライアントID
			$requestData['client_id'] = $commodityItemPriceData['CommodityItem']['client_id'];
			// 商品データ取得
			$commodityData = $this->Commodity->getCommodityData($commodityItemPriceData['CommodityItem']['commodity_id'], $requestData);
		}

		if (empty($commodityItemPriceData) || empty($commodityData)) {
			// 商品が見つからない時はパラメータエラー
			$this->render($this->planErrorView);
			return;
		} else {

			// 商品情報のマージ
			$commodityInfo = array_merge($commodityItemPriceData, $commodityData);

			// 貸出営業所一覧データ取得
			$officeDatas = $commodityData['RentOffice'];

			// 貸出営業所セレクト
			$fromOfficeList = array();
			$fromOfficeOptionList = array();
			$fromOfficeStockCheckCnt = 0;
			$fromOfficeDefault = 0;
			$fromOfficeSelectErrorFlg = false;
			$stockCheckParams = array(
				'from_office' => Hash::extract($officeDatas, '{n}.id'),
				'from' => $fromDateTime,
				'to' => $toDateTime,
				'cars_count' => 1,
			);
			$remainingStock = 0;

			// 営業所毎の在庫数取得
			$officeStocks = $this->CommodityItem->getOfficeStocks($commodityInfo['CarClass']['id'], $stockCheckParams);

			foreach ($officeDatas as $key => $officeData) {
				$officeId = $officeData['id'];
				$officeName = $officeData['name'];

				if (!empty($officeStocks[$officeData['id']])) {
					$officeStock = $officeStocks[$officeId];
					$fromOfficeList[$officeId] = $officeName;
					$fromOfficeOptionList[$officeId] = array('value' => $officeId, 'name' => $officeName);
					$fromOfficeStockCheckCnt++;

					if (empty($fromOfficeDefault)) {
						$fromOfficeDefault = $officeId;
						$remainingStock = $officeStock;
					} else if ($remainingStock > $officeStock) {
						$remainingStock = $officeStock;
					}

				} else {
					$_brackets = (!$expiredFlg) ? '（在庫なし）' : '';
					$fromOfficeList[$officeId] = $officeName . $_brackets;
					$fromOfficeOptionList[$officeId] = array('value' => $officeId, 'name' => $officeName . $_brackets, 'disabled' => TRUE);

					if (!empty($postData['Reservation']['from_office'])) {
						if ($officeId == $postData['Reservation']['from_office']) {
							$fromOfficeSelectErrorFlg = true;
						}
					}
				}
			}
			if (empty($fromOfficeStockCheckCnt) || $fromOfficeSelectErrorFlg) {
				// 選択できない状況回避時は $fromOfficeOptionList リセット
				$fromOfficeOptionList = array();
			}
			if (!empty($postData['Reservation']['from_office'])) {
				$this->request->data['from_office'] = $postData['Reservation']['from_office'];
			} else if (!empty($fromOfficeDefault)) {
				$this->request->data['from_office'] = $fromOfficeDefault;
			} else {
				$this->request->data['from_office'] = !empty($officeDatas[0]['id']) ? $officeDatas[0]['id'] : 0;
			}

			// 返却営業所一覧データ取得
			$returnOfficeDatas = $commodityData['ReturnOffice'];

			// 返却営業所セレクト
			$returnOfficeList = array();
			$returnOfficeDefault = 0;
			foreach ($returnOfficeDatas as $key => $returnOffice) {
				$returnOfficeList[$returnOffice['id']] = $returnOffice['name'];
				if (!empty($fromOfficeDefault)) {
					// 出発デフォルト店舗が返却店舗もあればそれを選択
					if ($returnOffice['id'] == $fromOfficeDefault) {
						$returnOfficeDefault = $returnOffice['id'];
					}
				}
			}
			if (!empty($postData['Reservation']['return_office'])) {
				$this->request->data['return_office'] = $postData['Reservation']['return_office'];
			} else if (!empty($returnOfficeDefault)) {
				$this->request->data['return_office'] = $returnOfficeDefault;
			} else {
				$this->request->data['return_office'] = !empty($returnOfficeDatas[0]['id']) ? $returnOfficeDatas[0]['id'] : 0;
			}

			list($dayNight, $period, $period24) = $this->PlanUtil->getPeriodArray($fromDateTime, $toDateTime);

			// レンタル期間
			if ($commodityData['Commodity']['day_time_flg'] == 1) {
				$rentalTime = abs((strtotime($fromDateTime) - strtotime($toDateTime)) / (60 * 60));
				$rentalPeriod = $rentalTime . '時間';
			} else if ($dayNight > 0) {
				$rentalPeriod = $dayNight . '泊' . $period . '日';
			} else {
				$rentalPeriod = '日帰り';
			}

			// 基本料金
			$price = $this->PlanUtil->calcBasicPrice(
				$commodityItemPriceData['CommodityPrice'],
				$commodityData['Commodity']['day_time_flg'],
				$fromDateTime,
				$toDateTime,
				$period
			);

			// 免責補償料金
			$disclaimerCompensationPrice = $this->DisclaimerCompensation->getFee(
				$commodityItemPriceData['CarClass']['id'],
				$this->request->query['from'],
				$this->request->query['to'],
				$period,
				$period24
			);

			$basicCharge = $price + $disclaimerCompensationPrice;

			// オプション(特典)データ取得
			$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($commodityItemPriceData['CommodityItem']['commodity_id'], $period, $period24);

			// オプション数セレクト
			$sheetOptions = array();
			$privilegeOptions = array();
			foreach ($commodityPrivilegeData as $key => $commodityPrivilege) {
				if ($commodityPrivilege['Privilege']['option_flg'] == 1) {
					// シートオプション
					$sheetOptions[$commodityPrivilege['Privilege']['id']] = array_combine(
							range(1, $commodityPrivilege['Privilege']['maximum']),
							range(1, $commodityPrivilege['Privilege']['maximum'])
					);
				} else {
					// 特典オプション
					$privilegeOptions[$commodityPrivilege['Privilege']['id']] = array_combine(
							range(1, $commodityPrivilege['Privilege']['maximum']),
							range(1, $commodityPrivilege['Privilege']['maximum'])
					);
				}
			}

			// 装備セット
			$equipmentList = $this->Equipment->getEquipment();
			$commodityEquipment = $this->CommodityEquipment->getEquipmentData($commodityData['Commodity']['id']);

			// 乗車人数セレクト
			$adultPassengers = array();
			for ($i = 1; $i <= 10; $i++) {
				$adultPassengers[$i] = $i;
			}
			$passengers = array();
			for ($i = 0; $i <= 10; $i++) {
				$passengers[$i] = $i;
			}

			// 車両年式
			$newCarRegistration = $this->newCarRegistration;

			// 支払方法（カード取得）
			if (!empty($commodityInfo['Client']['accept_card'])) {
				$clientCards = $this->ClientCard->getCardByClientId($commodityInfo['Client']['id']);
				$commodityInfo['Cards'] = $clientCards[$commodityInfo['Client']['id']];
			}

			// キャンセル料無料期限
			$cancelFreeLimit = $this->CancelFee->getCancelFreeLimit($commodityInfo['Client']['id']);
			if(!empty($cancelFreeLimit)) {
				$cancelFreeLimitDay = preg_replace("/[^0-9]*/s", "", $cancelFreeLimit);
				$today = date("Y-m-d");
				$cancelFreeDate = date("Y-m-d", strtotime($requestData['from'] . "-" . $cancelFreeLimitDay . " day"));
				$isExpiredCancelLimit = strtotime($cancelFreeDate) < strtotime($today) ? true : false;
			} else { // キャンセル料無料期限がない場合、このプランではキャンセル料はかかりません。
				$isExpiredCancelLimit = false;
			}

			// イーコンメンテモード
			$econMaintenance = $this->Maintenance->isEconMaintenance();

			$_session['requestData'] = json_encode($requestData);
			$_session['basicCharge'] = json_encode($basicCharge);

			$sessionUniqId = md5(uniqid(rand(), 1));
			$_session['uniqId'] = $sessionUniqId;
			$redirectPlan = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			$_session['redirectPlan'] = $redirectPlan;
			$_session['backSearch'] = $backSearch;

			$this->Session->write('reservation', $_session);

			$this->set(compact(
					'basicCharge', 'commodityInfo', 'officeDatas', 'fromOfficeList', 'fromOfficeOptionList', 'returnOfficeDatas', 'returnOfficeList',
					'commodityPrivilegeData', 'requestData', 'adultPassengers', 'passengers', 'sheetOptions', 'privilegeOptions',
					'newCarRegistration', 'rentalPeriod', 'equipmentList', 'commodityEquipment', 'sessionUniqId','remainingStock',
					'cancelFreeLimit', 'econMaintenance', 'isExpiredCancelLimit', 'defaultPaymentMethod'
			));
		}

		$this->set('backSearch', $backSearch);
		$this->set('expiredFlg', $expiredFlg);

		$this->set('title_for_layout', 'お見積り');
		$this->set('h1_for_layout', 'お見積り');
		$this->set('top_txt', 'プランの見積もりができます。');
		$this->set('description_for_layout', 'プランの見積もりができます。');

		//  パンくずリスト設定
		$progressArr = $this->BreadCrumb->setPlan($this->action);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {
		$this->index();
	}

	/**
	 * プラン詳細にてajaxでの在庫チェック、乗り捨てエリア料金・深夜手数料の取得
	 */
	public function ajaxAction() {
		if (!$this->request->is('ajax')) {
			return;
		}

		$this->autoRender = false;

		// 在庫チェック
		if (!empty($this->data['ajaxAction']) && $this->data['ajaxAction'] == 'stockCheck') {
			$stockCheckParams = array(
				'from_office' => $this->data['fromOffice'],
				'from' => $this->data['from'],
				'to' => $this->data['to'],
				'cars_count' => 1,
			);
			$remainingStock = $this->CommodityItem->getOfficeStocks($this->data['carClassId'], $stockCheckParams);
			if (empty($remainingStock) || empty($remainingStock[$this->data['fromOffice']])) {
				$isStock = false;
			} else {
				$isStock = true;
			}
			echo $isStock;
		}

		// 乗り捨てエリア料金・深夜手数料取得
		if (!empty($this->data['ajaxAction']) && $this->data['ajaxAction'] == 'dropOffLateNight') {
			if (empty($this->data['fromOfficeId']) || empty($this->data['returnOfficeId'])) {
				return false;
			}
			// 乗り捨てエリア料金
			$result['dropPrice'] = '';
			$dropOffAreaPrice = $this->DropOffAreaRate->getDropOffAreaPrice($this->data['fromOfficeId'], $this->data['returnOfficeId'], $this->data['carClassId']);
			if (isset($dropOffAreaPrice)) {
				$result['dropPrice'] = (string) $dropOffAreaPrice;
			}
			// 深夜手数料
			$fromData = array(
				'fromOfficeId' => $this->data['fromOfficeId'],
				'fromTime' => date('H:i', strtotime($this->data['from'])),
			);
			$returnData = array(
				'returnOfficeId' => $this->data['returnOfficeId'],
				'returnTime' => date('H:i', strtotime($this->data['to'])),
			);
			$lateNightFee = $this->Office->getLateNightFee($fromData, $returnData);
			$result['nightFee'] = '';
			if (!empty($lateNightFee)) {
				$result['nightFee'] = (string) $lateNightFee;
			}
			echo json_encode($result);
		}
	}

}

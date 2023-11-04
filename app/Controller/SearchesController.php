<?php
App::uses('AppController','Controller');
App::uses('Sanitize','Utility');

/**
 * Searches Controller
 *
 * @property Searches $Searches
 */
class SearchesController extends AppController {

	public $uses = array(
		'Commodity',
		'Prefecture','Landmark',
		'Area',
		'Prefecture',
		'Office',
		'CarType', 'Equipment',
		'Client', 'CommodityRentOffice',
		'CommodityItem', 'CommodityEquipment',
		'Search' // validationのみ
		,'Station','OfficeStation'
		,'PublicHoliday','KeyValue'
	);

	public $use_searchbox = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $components = array('BreadCrumb', 'Cookie');

	//仮で、option_manage.jsを指定
	public $new_js = true;


	// 車両年式
	public $newCarRegistrationList = array(
		1 =>'新車登録1年以内',
		2 =>'新車登録2年以内',
		3 =>'新車登録3年以内',
		4 =>'新車登録4年以内',
		5 =>'新車登録5年以上'
	);

	// 喫煙・禁煙
	public $smokingCarList = array(
		0 =>'禁煙車',
		1 =>'喫煙車',
		2 =>'指定なし',
	);

	// ソート順cookie名
	private $sortCookieName = 'rc_sort';
	// ソート順cookieの有効期限(1ヶ月)
	private $sortCookieDuration = 2592000;
	// 地図PRcookie名
	private $prMapCookieName = Constant::PR_MAP_COOKIE_NAME;
	// プランPRcookie名
	private $prPlanCookieName = Constant::PR_PLAN_COOKIE_NAME;
	// PRcookieの有効期限
	private $prCookieDuration = Constant::PR_COOKIE_DURATION;

	public function beforeFilter() {
		parent::beforeFilter();

		// F5対策で明示的にセッション閉じる
		session_write_close();

		// Ajaxの場合はbeforeFilterの処理を通さない
		if ($this ->request->is( 'ajax' )) {
			exit;
		}

		foreach ((array)$this->uses as $model) {
			$this->$model->setDataSource('default_slave');
		}

		// 日時関連パラメータが揃って無い場合はマーケの施策
		// それ以外はこれまで通りバリデーションエラーとする
		$_2dayslater = strtotime('+2 day');
		if (strcmp(uaCheck(), Constant::DEVICE_SMART_PHONE) == 0) { // SP（出来ればPCとパラメータ同じが良い）
			if (!isset($this->request->query['date']) && !isset($this->request->query['time']) &&
				!isset($this->request->query['return_date']) && !isset($this->request->query['return_time'])) {
				$this->request->query['date'] = date('Y/m/d', $_2dayslater);
				$this->request->query['time'] = '11-00';
				$this->request->query['return_date'] = $this->request->query['date'];
				$this->request->query['return_time'] = '17-00';
			}
		} else { // PC
			if (!isset($this->request->query['year']) && !isset($this->request->query['month']) && !isset($this->request->query['day']) && !isset($this->request->query['time']) &&
				!isset($this->request->query['return_year']) && !isset($this->request->query['return_month']) && !isset($this->request->query['return_day']) && !isset($this->request->query['return_time'])) {
				$this->request->query['year'] = date('Y', $_2dayslater);
				$this->request->query['month'] = date('m', $_2dayslater);
				$this->request->query['day'] = date('d', $_2dayslater);
				$this->request->query['time'] = '11-00';
				$this->request->query['return_year'] = $this->request->query['year'] ;
				$this->request->query['return_month'] = $this->request->query['month'];
				$this->request->query['return_day'] = $this->request->query['day'];
				$this->request->query['return_time'] = '17-00';
			}
		}

		// robots noindex
		if (!$this->request->params['is_move_search']) {
			// サーチ直下
			$this->set('meta_robots', 'noindex');
		} elseif (empty($this->request->query)) {
			// /rentacar/〇〇〇/searchesはパラメーターがない場合
			$this->set('meta_robots', 'noindex');
		}

		// searchから
		if (!$this->request->params['is_move_search'] && !empty($this->request->query)) {
			// 地方を取得するモデルを提議
			App::uses('Prefecture','Model');
			$prefecture = new Prefecture;
			
			// エリア
			if ($this->request->query['place'] === '1') {
				App::uses('Area','Model');
				$area = new Area;
				$area_arr = $area->getAreaById($this->request->query['area_id']);

				if (!empty($area_arr[0])) {
					$prefecture_arr = $prefecture->getLinkCdAndRegionLinkCdById($area_arr[0]['Area']['prefecture_id']);
					$query_string = http_build_query($this->request->query);

					if ($area_arr[0]['Area']['prefecture_id'] == '1' || $area_arr[0]['Area']['prefecture_id'] == '47') {
						// 北海道と沖縄
						return $this->redirect('/'.$prefecture_arr['Prefecture']['link_cd'].'/'.$area_arr[0]['Area']['area_link_cd'].'/?'.$query_string, 301);
					} else {
						// その他
						return $this->redirect('/'.str_replace('area_', '', $prefecture_arr['Prefecture']['region_link_cd']).'/'.$prefecture_arr['Prefecture']['link_cd'].'/'.$area_arr[0]['Area']['area_link_cd'].'/?'.$query_string, 301);
					}
				}
			}

			// 空港
			if ($this->request->query['place'] === '3') {
				App::uses('Landmark', 'Model');
				$landmark = new Landmark;
				$port_arr = $landmark->getAirportById($this->request->query['airport_id']);
				if (empty($port_arr)) {
					// 空港データがなければ、港の方を取得
					$port_arr = $landmark->getFerryTerminalById($this->request->query['airport_id']);
				}

				// 空港・港があればエリア情報を取得して移動
				if (!empty($port_arr)) {
					$prefecture_arr = $prefecture->getLinkCdAndRegionLinkCdById($port_arr['Landmark']['prefecture_id']);
					$query_string = http_build_query($this->request->query);

					if ($port_arr['Landmark']['prefecture_id'] == '1' || $port_arr['Landmark']['prefecture_id'] == '47') {
						// 北海道と沖縄
						return $this->redirect('/'.$prefecture_arr['Prefecture']['link_cd'].'/'.$port_arr['Landmark']['link_cd'].'/?'.$query_string, 301);
					} else {
						// その他
						return $this->redirect('/'.str_replace('area_', '', $prefecture_arr['Prefecture']['region_link_cd']).'/'.$prefecture_arr['Prefecture']['link_cd'].'/'.$port_arr['Landmark']['link_cd'].'/?'.$query_string, 301);
					}
				}
			}
			// 駅
			if ($this->request->query['place'] === '4') {
				App::uses('Station', 'Model');
				$station = new Station;
				$station_arr = $station->getStationById($this->request->query['station_id']);
				
				if (!empty($station_arr)) {
					$prefecture_arr = $prefecture->getLinkCdAndRegionLinkCdById($station_arr['Station']['prefecture_id']);
					$query_string = http_build_query($this->request->query);

					if ($station_arr['Station']['prefecture_id'] == '1' || $station_arr['Station']['prefecture_id'] == '47') {
						// 北海道と沖縄
						return $this->redirect('/'.$prefecture_arr['Prefecture']['link_cd'].'/'.$station_arr['Station']['url'].'/?'.$query_string, 301);
					} else {
						// その他
						return $this->redirect('/'.str_replace('area_', '', $prefecture_arr['Prefecture']['region_link_cd']).'/'.$prefecture_arr['Prefecture']['link_cd'].'/'.$station_arr['Station']['url'].'/?'.$query_string, 301);
					}
				}
			}


		}

		$this->set('newCarRegistrationList', $this->newCarRegistrationList);
		$this->set('smokingCarList', $this->smokingCarList);
	}

	/**
	 * index method
	 */
	public function index() {

		$params = '';
		if (!empty($this->request->query)) {
			$params = $this->request->query;

			/**
			* バリデーションチェック
			*/
			$this->Search->set($params);
			if (!$this->Search->validates()) {
				$params = '';
				$this->set('validationErrors', $this->Search->validationErrors);
			} else {
				// 新幹線駅は廃止のためパラメータ変換する
				if ((!empty($params['place']) && $params['place'] == 2) || (!empty($params['return_place']) && $params['return_place'] == 2)) {
					$params = $this->OptionsManage->convertBulletTrainParamsToStationParams($params);
				}

				// パラメータに不備が無ければメイン処理
				$this->_searchMain($params);
			}
		}

		// 装備セット
		$equipmentList = $this->Equipment->getEquipment();
		$this->set('equipmentList', $equipmentList);

		// 車両年式セット
		$this->set('newCarRegistration', $this->newCarRegistration);

		// クライアントリストセット
		$this->set('clientList', $this->Client->getClientInfoList());

		// ソート順セット
		$searchSortList = Hash::combine(Constant::searchSortOrders(), '{n}.id', '{n}.name');

		// 空港以外は近い順を表示させない
		if (empty($params['place']) || $params['place'] != 3) {
			unset($searchSortList[5]);
		}

		$this->set(compact('searchSortList'));

		/**
		 * 検索オプションセット
		 * OptionsManageコンポーネント使用
		 */
		$this->OptionsManage->setSearchOptions($params);
		$this->OptionsManage->saveHistoryCookie();

		// 車両タイプ別の最安値を取得
		$lowestPriceOfCarType = $this->Commodity->getLowestPriceOfCarType();

		if (!empty($lowestPriceOfCarType)) {
			// 既にset済みの値に最安値を追加する
			$this->set('carTypeInfo', call_user_func(function ($values) {
				$carTypeInfo = $this->viewVars['carTypeInfo'];

				foreach ($carTypeInfo as $k => $carType) {
					$carTypeId = $carType['CarType']['id'];

					if (!empty($values[$carTypeId])) {
						$carTypeInfo[$k]['CarType']['lowestPrice'] = $values[$carTypeId];
					}
				}
				return $carTypeInfo;

			}, $lowestPriceOfCarType));
		}

		$this->set('title_for_layout', $this->viewVars['searchPlace'].'の格安レンタカー比較予約｜スカイチケット');
		$this->set('description_for_layout', $this->viewVars['searchPlace'].'の格安レンタカーが最大70%オフ！'.$this->viewVars['searchPlace'].'で最安値のレンタカーを比較・予約。当日予約や乗り捨て、24時間営業など幅広いプランとキャンペーンを掲載中。口コミやおすすめ検索からかんたんに予約ができます。');

		if ($this->viewVars['searchPlace'] == $this->viewVars['prefectureName']) {
			$this->set('keywords', $this->viewVars['searchPlace'].",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");
		} else {
			$this->set('keywords', $this->viewVars['searchPlace'].','. $this->viewVars['prefectureName'].",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");
		}

		// パンくずリスト設定
		$progressArr = $this->BreadCrumb->setSearches($this->action);
		$this->set('progress_arr', $progressArr);
	}

	// スマホ用のindex
	public function sp_index() {

		$query = '';
		if (!empty($this->request->query)) {
			$query = http_build_query($this->request->query);
		}

		// 出発日を分割
		if (!empty($this->request->query['date'])) {
			$date = explode('/', $this->request->query['date']);

			$this->request->query['year'] = $date[0];
			$this->request->query['month'] = $date[1];
			$this->request->query['day'] = $date[2];
		}

		// 返却日を分割
		if (!empty($this->request->query['return_date'])) {
			$date = explode('/', $this->request->query['return_date']);

			$this->request->query['return_year'] = $date[0];
			$this->request->query['return_month'] = $date[1];
			$this->request->query['return_day'] = $date[2];
		}

		// 返却方法を代入（PCとSPでは解釈が違う PC=0:出発店舗に返却 1:乗り捨て利用 / SP=0:出発店舗に返却 1:乗り捨て(都道府県) 2:乗り捨て(新幹線) 3:乗り捨て(空港)
		$spReturnPlace = $this->request->query['return_way'];
		if (!empty($this->request->query['return_way'])) {

			$this->request->query['return_place'] = $this->request->query['return_way'];
			$spReturnPlace = $this->request->query['return_place'];

			// 内部で検索する際には0か1でなければならない
			if ($this->request->query['return_way'] > 0) {
				$this->request->query['return_way'] = '1';
			}
		}
		$this->set('spReturnPlace', $spReturnPlace);
		$this->set('query', $query);

		$abTestIDList = explode('!', $this->Cookie->read('_gaexp'));

		$testPatternNum = '0';
		foreach($abTestIDList as $abTestID) {
				if(strpos($abTestID, 'c9taX5U7RQqZU3ebntZtqQ') !== false) {
						$testPatternNum = substr($abTestID, -1);
				}
		}

		$this->set('testPatternNum', $testPatternNum);


		$this->index();
	}

	public function search_form() {
		$this->redirect('/searches');
	}

	public function sp_search_form() {

		$params = '';
		$query = '';
		if (!empty($this->request->query)) {
			$params = $this->request->query;
			$query = http_build_query($params);
		}

		/**
		 * 検索オプションセット
		 * OptionsManageコンポーネント使用
		 */
		$this->OptionsManage->setSearchOptions($params);
		$this->OptionsManage->saveHistoryCookie();
		$this->set('query', $query);

		$this->set('title_for_layout', 'レンタカーの詳細検索');
		$this->set('description_for_layout', '国内レンタカーの予約・比較するならskyticket（スカイチケット）。国内線飛行機チケットも予約できるスカイチケットで旅先の移動のためのレンタカーも予約できます。');
		$this->set('keywords', 'レンタカー,skyticket,スカイチケット,レンタカー予約,航空券');
	}

	/**
	 * 検索メイン処理
	 */
	private function _searchMain($params) {

		// 年月日を連結
		$params['from'] = $params['year'] . '-' . $params['month'] . '-' . $params['day'];
		$params['to'] = $params['return_year'] . '-' . $params['return_month'] . '-' . $params['return_day'];

		$fromDatetime = $params['from'] . ' ' . str_replace('-',':',$params['time']);
		$this->set('fromDatetime', $fromDatetime);
		$toDatetime = $params['to'] . ' ' . str_replace('-',':',$params['return_time']);

		$rentalTime = abs((strtotime($fromDatetime) - strtotime($toDatetime)) / (60 * 60));
		$this->set('rentalTime', $rentalTime);

		/**
		 * 新幹線駅、空港、ローカル駅の場合クエリからエリアIDを取得
		 */
		// 都道府県の場合
		if ($params['place'] == 1) {
			if (isset($params['area_id'])) {
				if (!filter_var($params['area_id'], FILTER_VALIDATE_INT)) {
					$areaList = $this->Area->getAreaListByPrefectureId($params['prefecture']);
					if (!empty($areaList)) {
						$params['area_id'] = array_keys($areaList);
					} else {
						$params['area_id'] = 0;
					}
				}
			}
			// 新幹線駅の場合
		} else if ($params['place'] == 2) {
			$params['area_id'] = $this->Office->getOfficeAreaIdList(array(
				'bullet_train_id' => $params['bullet_train_id']
			));

			// 空港の場合
		} else if ($params['place'] == 3) {
			$params['area_id'] = $this->Office->getOfficeAreaIdList(array(
				'airport_id' => $params['airport_id']
			));

			// ローカル駅の場合
		} else if ($params['place'] == 4) {
			if (isset($params['station_id'])) {
				$params['area_id'] = $this->OfficeStation->getAreaIdListByStationId($params['station_id']);
			}
		}

		if (isset($params['area_id'])) {
			$viewNumber = $this->Area->getViews($params['area_id']);
			$this->set('viewNumber', $viewNumber);
		}

		// ソート順取得
		// GETパラメータを優先する
		$sort = !empty($this->request->query['sort']) ? $this->request->query['sort'] : filter_input(INPUT_COOKIE, $this->sortCookieName, FILTER_VALIDATE_INT);

		if (!empty($sort)) {
			$params['sort'] = $sort;
			// cookieに保存する
			setcookie($this->sortCookieName, $sort, time() + $this->sortCookieDuration, '/', '', true, true);
		}

		// 出発日と返却日が正しく設定されているかチェック
		if ($this->_checkDate($fromDatetime,$toDatetime)) {

			// ページ取得
			$page = 1;
			if (!empty($params['page']) && ctype_digit($params['page'])) {
				$page = (int)$params['page'];
			} else if (!empty($this->params['named']['page']) && ctype_digit($this->params['named']['page'])) {
				$page = (int)$this->params['named']['page'];
			}
			unset($params['page']);

			// 検索条件からpaginaterに渡すためのクエリを生成
			if (!empty($params['type']) && $params['type'] == 'map') { // 地図から探す
				$isList = false;
				$query = $this->Commodity->getCommodityQuery($params, $page, false);
			} else { // プラン一覧（パラメータなし or map以外はこちら）
				$isList = true;
				$query = $this->Commodity->getCommodityQuery($params, $page);
			}
			$this->set('isList', $isList);

			if (!empty($query)) {

				if ($isList) { // プラン一覧
					// 地図から遷移した場合のみPRを引き継ぐ
					$this->Commodity->inheritPR = $this->Cookie->read($this->prMapCookieName);
					// ページリロードされたら引き継ぎたくないのですぐに消す
					$this->Cookie->delete($this->prMapCookieName);
					// 商品情報を取得
					$this->paginate = $query;
					$commodities = $this->paginate();

					// 商品IDと商品アイテムIDを取得
					$commodityId = array();
					$commodityItemId = array();
					foreach ($commodities as $val) {
						$commodityId[] = $val['Commodity']['id'];
						$commodityItemId[] = $val['CommodityItem']['id'];
						if ($val['pr']) {
							$prPlanClientIds[] = $val['Commodity']['client_id'];
						}
					}
					if (!empty($prPlanClientIds)) {
						$this->Cookie->write($this->prPlanCookieName, array_unique($prPlanClientIds), true, $this->prCookieDuration);
					}

					/**
					 * 商品の関連情報取得
					 */
					// 出発店舗取得
					if ($params['place'] == 2) {
						// 新幹線の場合
						$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityId, $params['bullet_train_id'], 2, $params['from'], $fromDatetime, $params['to']);
					} else if ($params['place'] == 3) {
						// 空港の場合
						$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityId, $params['airport_id'], 3, $params['from'], $fromDatetime, $params['to']);
					} else if ($params['place'] == 4) {
						// ローカル駅の場合
						$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityId, $params['station_id'], 4, $params['from'], $fromDatetime, $params['to']);
					} else {
						// 通常は都道府県
						$rentOfficeList = $this->CommodityRentOffice->getRentOfficeListByPlaceAndId($commodityId, $params['area_id'], 1, $params['from'], $fromDatetime, $params['to']);
					}
					// 今見ているページを地図一覧から戻ってきても再表示できるようにパラメータで渡す
					$this->set('refPage', $page);

				} else { // 地図から探す
					// プランから遷移した場合のみPRを引き継ぐ
					$this->Commodity->inheritPR = $this->Cookie->read($this->prPlanCookieName);
					// ページリロードされたら引き継ぎたくないのですぐに消す
					$this->Cookie->delete($this->prPlanCookieName);
					// 店舗取得もモデルで行い、プランを店舗ごとにまとめる
					list($commodities, $rentOfficeList, $commodityId, $commodityItemId) = $this->Commodity->getPlansForMap($query, $params);
					foreach ($rentOfficeList as $officeId => $officeInfo){
						if ($officeInfo['pr']) {
							$prMapClientIds[] = $officeInfo['client_id'];
						}
					}
					if (!empty($prMapClientIds)) {
						$this->Cookie->write($this->prMapCookieName, array_unique($prMapClientIds), true, $this->prCookieDuration);
					}
					// 直前に開いていたプランのページを再表示するようにパラメータで渡す
					$this->set('page', $params['ref_page']);
				}

				$this->set('rentOfficeList', $rentOfficeList);

				// 車種取得
				$carInfoList = $this->CommodityItem->getCarInfo($commodityItemId);
				$this->set('carInfoList', $carInfoList);

				// 装備取得
				$commodityEquipment = $this->CommodityEquipment->getCommodityEquipment($commodityId);
				$this->set('commodityEquipment', $commodityEquipment);

				// 「この会社のプラン一覧へ」のリンクを作成するために配列をgetパラメータに変換
				$clientPlanParams = $params;
				unset($clientPlanParams['client_id'], $clientPlanParams['from'], $clientPlanParams['to']);
				$clientPlanLink = http_build_query($clientPlanParams);
				$this->set('clientPlanLink', $clientPlanLink);

				// 「予約する」のリンクを生成
				$reserveUrl = $this->Commodity->getPlanQueryString();
				$this->set('reserveUrl', $reserveUrl);
        
				// 北海道キャンペーン
				$hokkaidoCampaignFlg = false;
				$hokkaidoCampaignTargetClientIds = [55, 4, 75, 46, 33, 115, 25, 111, 13, 43, 5, 108, 26, 142];
				if (strtotime($fromDatetime) >= strtotime('2022-09-01 00:00:00') && strtotime($fromDatetime) <= strtotime('2022-11-30 23:59:59') ) {
					$pathArr = preg_split("#/#", $this->request->url);
					if (is_array($pathArr) && $pathArr[0] == 'hokkaido') {
						$hokkaidoCampaignFlg = true;
					}
				}
				$this->set('hokkaidoCampaignFlg', $hokkaidoCampaignFlg);
				$this->set('hokkaidoCampaignTargetClientIds', $hokkaidoCampaignTargetClientIds);
				
			} else {

				// 検索結果がない場合
				// ヒント用の情報を取得
				$datetimeFrom = $params['year'] . '-' . $params['month'] . '-' . $params['day'] . ' ' . str_replace('-', ':', $params['time']) . ':00';
				$datetimeTo = $params['return_year'] . '-' . $params['return_month'] . '-' . $params['return_day'] . ' ' . str_replace('-', ':', $params['return_time']) . ':00';

				// 指定都道府県取得
				$tmpArea = $this->Area->getPrefectureIdByAreaId($params['area_id']);
				$prefectureId = $tmpArea['Area']['prefecture_id'];
				// 都道府県のエリアリスト取得
				$areaList = $this->Area->getAreaInfoByPrefectureId($prefectureId);

				// 都道府県のリンク作成
				$prefLink = $this->Prefecture->getUrlById($prefectureId);

				$tmpParams = $params;
				$tmpParams['return_way'] = 0;
				$tmpParams['place'] = 1;
				 // 検索パラメータではなくarea_idに紐づくprefecture_idをセットする
				$tmpParams['prefecture'] = $prefectureId;

				$otherAreas = array();
				// 都道府県のエリアリストを回す
				foreach ($areaList as $area) {
					if ($area['Area']['id'] == $params['area_id']) {
						continue;
					}
					$areaCommodities = $this->Commodity->getPriceByAreaId($area['Area']['id'], $datetimeFrom, $datetimeTo);

					$bestPrice = 0;
					foreach ($areaCommodities as $commodity) {
						if (empty($bestPrice)) {
							$bestPrice = $commodity['price'];
						} else {
							if ($commodity['price'] < $bestPrice) {
								$bestPrice = $commodity['price'];
							}
						}
					}

					if (!empty($bestPrice)) {
						$tmpParams['area_id'] = $area['Area']['id'];
						$otherAreas[$area['Area']['id']] = array('name' => $area['Area']['name'], 'query' => http_build_query($tmpParams) , 'price' => $bestPrice);
					}

					// エリア3件取得したら、停止
					if (count($otherAreas) >= 3) {
						break;
					}
				}

				$fromDayInfo = $this->PublicHoliday->getDayInfo($fromDatetime);
				$identifier = $fromDayInfo['identifier'];

				$earliestOffice = null;
				$latestOffice = null;
				$departureLink = '';

				if ($params['place'] == 2) {
					// 新幹線の場合
					$nearOfficesList = $this->Office->getOfficeNearListByAirportId($params['bullet_train_id']);
					$keyPlaceDepartId = $params['place'].'_'.$params['bullet_train_id'];
				} else if ($params['place'] == 3) {
					// 空港の場合
					$nearOfficesList = $this->Office->getOfficeNearListByAirportId($params['airport_id']);
					$this->Landmark->recursive = -1;
					$departure = $this->Landmark->findC('first', array('conditions' => array('Landmark.id' => $params['airport_id'])), '1day');
					$departureLink = $prefLink . $departure['Landmark']['link_cd'] . '/';
					$keyPlaceDepartId = $params['place'].'_'.$params['airport_id'];
				} else if ($params['place'] == 4) {
					// ローカル駅の場合
					$nearOfficesList = $this->Office->getOfficeNearListByStationId($params['station_id']);
					$this->Station->recursive = -1;
					$departure = $this->Station->findC('first', array('conditions' => array('Station.id' => $params['station_id'])), '1day');
					$departureLink = $prefLink . $departure['Station']['url'] . '/';
					$keyPlaceDepartId = $params['place'].'_'.$params['station_id'];
				} else if ($params['place'] == 1) {
					// 通常は都道府県
					$nearOfficesList = $this->Office->getOfficeNearListByAreaId($params['area_id']);
					$departure = $this->Area->findC('first', array('conditions' => array('Area.id' => $params['area_id'])), '1day');
					$departureLink = $prefLink . $departure['Area']['area_link_cd'] . '/';
					$keyPlaceDepartId = $params['place'].'_'.$params['area_id'];

				}

				foreach ($nearOfficesList as $rentOffice) {
					if (empty($earliestOffice)) {
						$earliestOffice = $rentOffice['Office'];
					} else {
						if (strtotime($rentOffice['Office'][$identifier.'_hours_from']) < strtotime($earliestOffice[$identifier.'_hours_from'])) {
							// 一番早い店舗
							$earliestOffice = $rentOffice['Office'];
						}
					}

					if (empty($latestOffice)) {
						$latestOffice = $rentOffice['Office'];
					} else {
						if (strtotime($rentOffice['Office'][$identifier.'_hours_to']) > strtotime($latestOffice[$identifier.'_hours_to'])) {
							// 一番遅い店舗
							$latestOffice = $rentOffice['Office'];
						}
					}
				}

				// 最短手仕舞い時間
				$minDeadlineHour = '';
				$minDeadlineHoursData = $this->KeyValue->findC('first', array('conditions' => array('key'=> 'front_search_min_deadline_hours_data')), '1day');
				if (!empty($minDeadlineHoursData)) {
					if (!empty($minDeadlineHoursData['KeyValue']['value'])) {
						$arr = json_decode($minDeadlineHoursData['KeyValue']['value'], true);
						if (array_key_exists($keyPlaceDepartId, $arr)) {
							$minDeadlineHour = $arr[$keyPlaceDepartId];
						}
					}
				}

				$this->set('departureLink', $departureLink);
				$this->set('minDeadlineHour', $minDeadlineHour);
				$this->set('earliestOffice', $earliestOffice);
				$this->set('latestOffice', $latestOffice);
				$this->set('otherAreas', $otherAreas);
				$this->set('identifier', $identifier);
				$this->set('keyPlaceDepartId', $keyPlaceDepartId);
			}

			// （検索結果有無に関わらず）「絞り込み条件解除」のリンクを作成
			$resetCondParams = $params;
			if (!$this->viewVars['fromRentacarClient'] &&
				((isset($params['smoking_flg']) && $params['smoking_flg'] !='2') ||
				  isset($params['car_type']) || isset($params['option']) || !empty($params['client_id']))) {
				$resetCondParams['smoking_flg'] = '2';
				unset($resetCondParams['car_type'], $resetCondParams['option'], $resetCondParams['client_id'], $resetCondParams['from'], $resetCondParams['to']);
				$resetCondLink = http_build_query($resetCondParams);
				$this->set('resetCondLink', $resetCondLink);
			}

		}

		if (empty($query)) {
			// 地図から探すの場合も空のarray返せば問題ない
			$this->paginate = array(
				'conditions' => array('id' => 0)
			);
			$commodities = $this->paginate();
		}

		// _submitは不要
		if (!empty($this->request->query['_submit'])) {
			unset($this->request->query['_submit']);
		}

		// 戻るリンク用に検索パラメータを保存する
		$backSearch = Router::queryString($this->request->query);
		if (!empty($backSearch)) {
			$this->Session->write('reservation.backSearch', $backSearch);
			$this->set('backSearch', $backSearch);
		}

		// pageは不要
		if (!empty($this->request->query['page'])) {
			unset($this->request->query['page']);
		}

		// 現在のソート順
		if (!empty($sort)) {
			$this->set('current_sort', $sort);
		} else {
			$this->set('current_sort', current(Constant::searchSortOrders())['id']);
		}

		$commodityClientIds = array();
		foreach ($commodities as $commodity){
			if (!in_array($commodity['Commodity']['client_id'], $commodityClientIds)) {
				$commodityClientIds[] = $commodity['Commodity']['client_id'];
			}
		}
		$this->set('commodityClientIds', $commodityClientIds);
		$this->set('commodities', $commodities);
	}

	/**
	 * 出発日と返却日が正常か確認
	 */
	private function _checkDate($fromDatetime, $toDatetime) {
		$checkFlg = true;

		// 出発日が今より大きいかチェック
		if (strtotime($fromDatetime) <= strtotime('+1 hour')) {
			$checkFlg = false;
			$this->Session->setFlash('出発日時は現在の日時より1時間後以降に設定してください。');
		}

		// 返却日が出発日より大きいかチェック
		if (strtotime($fromDatetime) >= strtotime($toDatetime)) {
			$checkFlg = false;
			$this->Session->setFlash('返却日時は出発日時より後に設定してください。');
		}

		return $checkFlg;
	}

}

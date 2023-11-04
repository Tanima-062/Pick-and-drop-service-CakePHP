<?php

App::uses('AppController', 'Controller');

class CompanyController extends AppController {

	public $uses = array('Prefecture', 'Landmark', 'Area', 'CarType', 'Equipment', 'Client', 'Office', 'Landmark', 'ClientCampaign', 'RcPostmeta', 'OfficeStation', 'Station', 'KeyValue');
	public $use_searchbox = true;
	public $use_yotpo = true;
	public $use_yotpo_rating = true;
	public $new_js = true;
	public $components = array('BreadCrumb', 'CompanyContents');

	public function beforeFilter() {
		parent::beforeFilter();

		foreach ((array)$this->uses as $model) {
			$this->$model->setDataSource('default_slave');
		}
	}

	// 正しいURLに301転送
	public function moved_url() {
		if (empty($this->request->params['link_cd'])) {
			throw new NotFoundException();
		}

		$this->redirect('/company/' . $this->request->params['link_cd'] . DS, 301);
	}

	public function index() {

		if (empty($this->request->params['company_id'])) {

			$params = $this->request->params;

			if (empty($params['link_cd'])) {
				throw new NotFoundException();
			}

			$link_cd = $params['link_cd'];
			$client_arr = $this->Client->getClientByLinkCd($link_cd);

			if (empty($client_arr)) {
				throw new NotFoundException();
			}

			$this->request->query['company_id'] = key($client_arr);
		} else {
			$this->request->query['company_id'] = $this->request->params['company_id'];
		}

		$this->set('regions', Constant::regions());

		$prefectureList = $this->Prefecture->getAllLinkCdAndRegionLinkCd();
		// 地方別都道府県
		$prefectureListGroupByRegion = Hash::combine($prefectureList, '{n}.Prefecture.link_cd', '{n}.Prefecture.name', '{n}.Prefecture.region_link_cd');
		$this->set('prefectureListGroupByRegion', $prefectureListGroupByRegion);
		unset($prefectureList);

		$defaultDate = explode('-', date('Y-m-d', strtotime('+7 days')));

		$companyId = $this->request->query['company_id'];

		$fromRentacarClient = false;
		if (!empty($this->from_client_id) && $this->from_client_id == $companyId) {
			$fromRentacarClient = true;
		}
		$this->set('fromRentacarClient', $fromRentacarClient);

		$clientInfo = $this->Client->getClientById($companyId);
		$clientName = $clientInfo['Client']['name'];
		$prefectureInfo = $this->Office->getOfficePrefectureIdList($clientInfo['Client']['id']);

		$allOfficeInfo = $this->Office->getOfficeClientListByClientId($clientInfo['Client']['id']);
		$allOfficeInfo = Hash::combine($allOfficeInfo, '{n}.Office.sort', '{n}');
		ksort($allOfficeInfo);
		$officeInfo = array_shift($allOfficeInfo);
		$stationId = $this->OfficeStation->getNearestStation($officeInfo['Office']['id']);

		// 検索パラメータ設定
		$params = array(
			'year' => $defaultDate[0],
			'month' => $defaultDate[1],
			'day' => $defaultDate[2],
			'time' => '11-00',
			'return_way' => '0',
			'return_year' => $defaultDate[0],
			'return_month' => $defaultDate[1],
			'return_day' => $defaultDate[2],
			'return_time' => '17-00',
			'client_id' => $clientInfo['Client']['id']
		);
		if (!empty($officeInfo['Office']['airport_id'])) {
			$params['place'] = 3;
			$params['airport_id'] = $officeInfo['Office']['airport_id'];
		} elseif (!empty($stationId)) {
			$params['place'] = 4;
			$params['prefecture'] = $officeInfo['Area']['prefecture_id'];
			$params['station_id'] = $stationId;
		} else {
			$params['place'] = 1;
			$params['prefecture'] = $officeInfo['Area']['prefecture_id'];
			$params['area_id'] = $officeInfo['Area']['id'];
		}
		$this->OptionsManage->setSearchOptions($params);

		// 車両タイプ別最安値（人気空港(港)・駅別）
		// SPでの日付はdate,return_dateのためパラメータに追加する
		$rankingDate = array(
			'date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
			'return_date' => $defaultDate[0] . '/' . $defaultDate[1] . '/' . $defaultDate[2],
		);
		$params = array_merge($params, $rankingDate);

		$landmarkRanking = $this->CompanyContents->getPopularLandmarkRanking($companyId, $params);
		$this->set('landmarkRanking', $landmarkRanking);
		$typeCapacityList = $this->CompanyContents->getCarTypeCapacityList();
		$this->set('typeCapacityList', $typeCapacityList);

		if (!$fromRentacarClient) {
			// 在庫があるもののみを取得・表示
			$prefectureList = $this->Prefecture->getPrefectureListInStock();

			// clientIdの条件付
			$landmarkList = $this->Landmark->getAirportAndBulletTrainArrayListByClientId($companyId);
			if (!empty($officeInfo['Office']['airport_id'])) {
				$landmarkInfo = $this->Landmark->getLandmarkByAirportId($officeInfo['Office']['airport_id']);
				$prefectureName = $landmarkInfo['Prefecture']['name'];
				$landmarkId = $landmarkInfo['Landmark']['id'];
				$landmarkName = $landmarkInfo['Landmark']['name'];
				if (!isset($landmarkList['airportArrayInStock'][$prefectureName][$landmarkId])) {
					$landmarkList['airportArrayInStock'][$prefectureName][$landmarkId] = $landmarkName;
				}
			}

			// 空港(在庫があるもののみ、ただし店舗の最寄り空港は必ず設定する)
			$airportSelectHeader = array('空港を選択してください');
			$airportInStockOptions = array(
				'type' => 'select',
				'options' => array_merge($airportSelectHeader, $landmarkList['airportArrayInStock']),
				'default' => $officeInfo['Office']['airport_id']
			);
			// OptionsManageでセットした値を上書き
			$this->set('airportInStockOptions', $airportInStockOptions);

			// 店舗の最寄り駅も必ず設定する
			if (!empty($stationId)) {
				$stations = json_decode($this->viewVars['station_arr'], true);
				$prefectureId = $officeInfo['Area']['prefecture_id'];
				$areaId = $officeInfo['Area']['id'];
				$areaStations = isset($stations[$prefectureId][$areaId]) ? $stations[$prefectureId][$areaId] : array();
				$stationIds = Hash::extract($areaStations, '{n}.id');
				if (!in_array($stationId, $stationIds)) {
					$nearestStation = $this->Station->find('first', array(
						'fields' => array('Station.id', 'Station.name', 'Station.major_flg', 'Area.name'),
						'joins' => array(
							array('type' => 'INNER', 'alias' => 'City', 'table' => 'cities', 'conditions' => 'City.id = Station.city_id'),
							array('type' => 'INNER', 'alias' => 'Area', 'table' => 'areas', 'conditions' => 'Area.id = City.area_id'),
						),
						'conditions' => array('Station.id' => $stationId, 'Station.delete_flg' => 0, 'City.delete_flg' > 0, 'Area.delete_flg' => 0),
						'recursive' => -1
					));
					if (!empty($nearestStation)) {
						$areaStations[] = array(
							'id' => $nearestStation['Station']['id'],
							'name' => $nearestStation['Station']['name'],
							'major' => $nearestStation['Station']['major_flg'],
							'area_name' => $nearestStation['Area']['name']
						);
						$stations[$prefectureId][$areaId] = array_values($areaStations);
						$this->set('station_arr', json_encode($stations));
					}
				}
			}

			$prefectureInfoList = array();
			foreach ($prefectureInfo as $k => $v) {
				if (!array_key_exists($k, $prefectureInfoList) && array_key_exists($k, $prefectureList)) {
					$prefectureInfoList = $prefectureInfoList + array($k => $v);
				}
			}
		} else {
			$prefectureInfoList = $prefectureInfo;
		}

		$prefectureInfoList = array('都道府県を選択してください') + $prefectureInfoList;
		$this->set('clientInfo', $clientInfo);
		$this->set('prefectureInfoList', $prefectureInfoList);
		// 初期値(取得したairportIdの1行目)事業所取得
		$officeInfo = array();
		$this->set('officeInfo', $officeInfo);

		// リンク用日付配列
		$link_date_arr = date_parse(date('Ymd', strtotime('+7 day')));	    // 7日後の日付
		$link_date_arr2 = date_parse(date('Ymd', strtotime('+11 day')));	 // 11日後の日付

		$this->set(compact('link_date_arr', 'link_date_arr2'));

		// 都道府県検索用リンク生成
		$prefectureAreaInfo = array();
		$prefectureOfficeInfo = array();

		$officeResult = $this->Office->getOfficeClientListByClientId($clientInfo['Client']['id']);

		if (isset($prefectureInfoList[0])) {
			$prefectureAreaInfo[0] = $prefectureInfoList[0];
			$prefectureOfficeInfo[0] = array();
		}

		foreach ($officeResult as $k1 => $v1) {
			if ($v1['Office']['id'] == 6935) {
				// 2020/07/08 日産レンタカー りんくうタウン店は表示したくない
				continue;
			}
			$_id = $v1['Area']['prefecture_id'];
			if (isset($prefectureInfoList[$_id])) {
				$prefectureAreaInfo[$_id] = $prefectureInfoList[$_id];
				unset($v1['Area']);
				$prefectureOfficeInfo[$_id][] = $v1;
			}
		}
		unset($officeResult);

		$this->set('prefectureAreaInfo', $prefectureAreaInfo);
		$this->set('prefectureOfficeInfo', $prefectureOfficeInfo);

		// キャンペーン用データを取得
		$this->paginate = $this->ClientCampaign->getPagenate($clientInfo['Client']['id']); // paginateプロパティ
		$campaignData = $this->paginate('ClientCampaign'); // こっちはpaginateメソッド
		$this->set('campaignData', $campaignData);

		// 会社の特徴を取得する
		$companyCharacterContents = $this->RcPostmeta->getCompanyPostmetaData($companyId, $clientInfo['Client']['url'], $fromRentacarClient);

		// 画像処理
		if (!empty($companyCharacterContents['company-outline-list'])) {
			foreach ($companyCharacterContents['company-outline-list'] as $key => $val) {
				if (!empty($val['img'])) {
					$params = array(
						'conditions' => array(
							"RcPostmeta.post_id" => $val['img'],
							"RcPostmeta.meta_key" => "_wp_attached_file"
						)
					);
					$imgPathData = $this->RcPostmeta->find('first', $params);
					$companyCharacterContents['company-outline-list'][$key]['photo-guid'] = $imgPathData["RcPostmeta"]["meta_value"];
				}
			}
		}
		if (!empty($companyCharacterContents['company-fee-contents-list'])) {
			foreach ($companyCharacterContents['company-fee-contents-list'] as $key => $val) {
				if (!empty($val['img'])) {
					$params = array(
						'conditions' => array(
							"RcPostmeta.post_id" => $val['img'],
							"RcPostmeta.meta_key" => "_wp_attached_file"
						)
					);
					$imgPathData = $this->RcPostmeta->find('first', $params);
					$companyCharacterContents['company-fee-contents-list'][$key]['photo-guid'] = $imgPathData["RcPostmeta"]["meta_value"];
				}
			}
		}
		if (!empty($companyCharacterContents['company-insurance-contents-list'])) {
			foreach ($companyCharacterContents['company-insurance-contents-list'] as $key => $val) {
				if (!empty($val['img'])) {
					$params = array(
						'conditions' => array(
							"RcPostmeta.post_id" => $val['img'],
							"RcPostmeta.meta_key" => "_wp_attached_file"
						)
					);
					$imgPathData = $this->RcPostmeta->find('first', $params);
					$companyCharacterContents['company-insurance-contents-list'][$key]['photo-guid'] = $imgPathData["RcPostmeta"]["meta_value"];
				}
			}
		}
		if (!empty($companyCharacterContents['company-today-contents-list'])) {
			foreach ($companyCharacterContents['company-today-contents-list'] as $key => $val) {
				if (!empty($val['img'])) {
					$params = array(
						'conditions' => array(
							"RcPostmeta.post_id" => $val['img'],
							"RcPostmeta.meta_key" => "_wp_attached_file"
						)
					);
					$imgPathData = $this->RcPostmeta->find('first', $params);
					$companyCharacterContents['company-today-contents-list'][$key]['photo-guid'] = $imgPathData["RcPostmeta"]["meta_value"];
				}
			}
		}
		if (!empty($companyCharacterContents['rentacar-body-list'])) {
			foreach ($companyCharacterContents['rentacar-body-list'] as $key => $val) {
				if (!empty($val['img'])) {
					$params = array(
						'conditions' => array(
							"RcPostmeta.post_id" => $val['img'],
							"RcPostmeta.meta_key" => "_wp_attached_file"
						)
					);
					$imgPathData = $this->RcPostmeta->find('first', $params);
					$companyCharacterContents['rentacar-body-list'][$key]['photo-guid'] = $imgPathData["RcPostmeta"]["meta_value"];
				}
			}
		}

		// キャッシュされたYOTPOのjsonをDBから取得
		$main_widget = '';
		$jsonKeyValue = $this->KeyValue->find('first', array('conditions' => array('key'=> 'yotpo_json_company_'.$clientInfo['Client']['id'].'cl')));
		if ($jsonKeyValue) {
			$review = json_decode($jsonKeyValue['KeyValue']['value']);
			$main_widget = $review[0]->result;
		}

		$this->set('main_widget', $main_widget);
		$this->set('companyCharacterContents', $companyCharacterContents);
		$this->set('clientList', $this->Client->getClientList());

		$this->set('title_for_layout', $clientName . 'の格安レンタカー予約・口コミ・料金比較｜スカイチケット');
		$this->set('description_for_layout', $clientName .'のレンタカーを今すぐ予約！気になる口コミや保険補償制度、料金形態を詳しく解説。'. $clientName .'で借りられるレンタカー車両や営業所一覧からかんたんに予約ができます。おすすめプランやキャンペーン料金も掲載中。');
		$this->set('keywords', $clientName . ",レンタカー,格安,比較,予約,乗り捨て,スカイチケット");

		// パンくずリスト設定
		$progressArr = $this->BreadCrumb->setCompany($this->action, $link_cd, $clientName);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_index() {
		$this->new_js = true;
		$this->index();
	}

	public function reviews() {

		if (empty($this->request->params['company_id'])) {

			$params = $this->request->params;

			if (empty($params['link_cd'])) {
				throw new NotFoundException();
			}

			$link_cd = $params['link_cd'];
			$client_arr = $this->Client->getClientByLinkCd($link_cd);

			if (empty($client_arr)) {
				throw new NotFoundException();
			}

			$this->request->query['company_id'] = key($client_arr);
		} else {
			$this->request->query['company_id'] = $this->request->params['company_id'];
		}
		$companyId = $this->request->query['company_id'];
		$clientInfo = $this->Client->getClientById($companyId);
		$clientName = $clientInfo['Client']['name'];
		$this->set('clientInfo', $clientInfo);
		$this->set('clientList', $this->Client->getClientList());
		
		$defaultDate = explode('-', date('Y-m-d', strtotime('+7 days')));

		$allOfficeInfo = $this->Office->getOfficeClientListByClientId($clientInfo['Client']['id']);
		$allOfficeInfo = Hash::combine($allOfficeInfo, '{n}.Office.sort', '{n}');
		ksort($allOfficeInfo);
		$officeInfo = array_shift($allOfficeInfo);
		$stationId = $this->OfficeStation->getNearestStation($officeInfo['Office']['id']);

		// 検索パラメータ設定
		$params = array(
			'year' => $defaultDate[0],
			'month' => $defaultDate[1],
			'day' => $defaultDate[2],
			'time' => '11-00',
			'return_way' => '0',
			'return_year' => $defaultDate[0],
			'return_month' => $defaultDate[1],
			'return_day' => $defaultDate[2],
			'return_time' => '17-00',
		);
		if (!empty($officeInfo['Office']['airport_id'])) {
			$params['place'] = 3;
			$params['airport_id'] = $officeInfo['Office']['airport_id'];
		} elseif (!empty($stationId)) {
			$params['place'] = 4;
			$params['prefecture'] = $officeInfo['Area']['prefecture_id'];
			$params['station_id'] = $stationId;
		} else {
			$params['place'] = 1;
			$params['prefecture'] = $officeInfo['Area']['prefecture_id'];
			$params['area_id'] = $officeInfo['Area']['id'];
		}
		$this->OptionsManage->setSearchOptions($params);

		// clientIdの条件付
		$landmarkList = $this->Landmark->getAirportAndBulletTrainArrayListByClientId($companyId);
		if (empty($landmarkList['airportArrayInStock']) && !empty($officeInfo['Office']['airport_id'])) {
			$landmarkInfo = $this->Landmark->getLandmarkByAirportId($officeInfo['Office']['airport_id']);
			$prefectureName = $landmarkInfo['Prefecture']['name'];
			$landmarkId = $landmarkInfo['Landmark']['id'];
			$landmarkName = $landmarkInfo['Landmark']['name'];
			$landmarkList['airportArrayInStock'][$prefectureName][$landmarkId] = $landmarkName;
		}

		// 空港(在庫があるもののみ、ただし在庫が一つもない場合は最寄り空港を設定する)
		$airportSelectHeader = array('空港を選択してください');
		$airportInStockOptions = array(
			'type' => 'select',
			'options' => array_merge($airportSelectHeader, $landmarkList['airportArrayInStock']),
			'default' => $officeInfo['Office']['airport_id']
		);
		// OptionsManageでセットした値を上書き
		$this->set('airportInStockOptions', $airportInStockOptions);

		// キャッシュされたYOTPOのjsonをDBから取得
		$main_widget = '';
		$jsonKeyValue = $this->KeyValue->find('first', array('conditions' => array('key'=> 'yotpo_json_company_'.$clientInfo['Client']['id'].'cl')));
		if ($jsonKeyValue) {
			$review = json_decode($jsonKeyValue['KeyValue']['value']);
			$main_widget = $review[0]->result;
		}

		$this->set('main_widget', $main_widget);

		// パンくずリスト設定
		$progressArr = $this->BreadCrumb->setCompany($this->action, $link_cd, $clientName);
		$this->set('progress_arr', $progressArr);
	}

	public function sp_reviews() {
		$this->reviews();
	}

	public function ajaxAction() {
		if ($this->request->is('ajax')) {

			$this->autoRender = false;

			if (empty($this->data['clientId']) || empty($this->data['type']) || empty($this->data['conditionId'])) {
				return false;
			} else {
				$officeInfo = $this->Office->getOfficeClientListByCondition($this->data['clientId'], $this->data['type'], $this->data['conditionId']);
				echo json_encode($officeInfo);
			}
		}
	}

	private function cutBeforeText($text, $num) {
		$replace = substr($text, $num, strlen($text) - $num);
		return $replace;
	}

	private function cutAfterText($text, $num) {
		$replace = substr($text, 0, strlen($text) - $num);
		return $replace;
	}

}

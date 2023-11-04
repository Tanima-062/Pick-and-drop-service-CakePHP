<?php

App::uses('Component', 'Controller');

class AreamapComponent extends Component {

	// 近隣の都道府県
	private $neighbors = array(
		1  => array(2),								// 北海道→青森
		2  => array(1, 3, 5),						// 青森　→北海道、岩手、秋田
		3  => array(2, 4, 5),						// 岩手　→青森、宮城、秋田
		4  => array(3, 5, 6, 7),					// 宮城　→岩手、秋田、山形、福島
		5  => array(2, 3, 4, 6),					// 秋田　→青森、岩手、宮城、山形
		6  => array(4, 5, 7, 15),					// 山形　→宮城、秋田、福島、新潟
		7  => array(4, 6, 8, 9, 10, 15),			// 福島　→宮城、山形、茨城、栃木、群馬、新潟
		8  => array(7, 9, 11, 12),					// 茨城　→福島、栃木、埼玉、千葉
		9  => array(7, 8, 10, 11),					// 栃木　→福島、茨城、群馬、埼玉
		10 => array(7, 9, 11, 15, 20),				// 群馬　→福島、栃木、埼玉、新潟、長野
		11 => array(8, 9, 10, 12, 13, 19),			// 埼玉　→茨城、栃木、群馬、千葉、東京、山梨
		12 => array(8, 11, 13),						// 千葉　→茨城、埼玉、東京
		13 => array(11, 12, 14, 19),				// 東京　→埼玉、千葉、神奈川、山梨
		14 => array(13, 19, 22),					// 神奈川→東京、山梨、静岡
		15 => array(6, 7, 10, 16, 20),				// 新潟　→山形、福島、群馬、富山、長野
		16 => array(15, 17, 20, 21),				// 富山　→新潟、石川、長野、岐阜
		17 => array(16, 18, 21),					// 石川　→富山、福井、岐阜
		18 => array(17, 21, 25, 26),				// 福井　→石川、岐阜、滋賀、京都
		19 => array(11, 13, 14, 20, 22),			// 山梨　→埼玉、東京、神奈川、長野、静岡
		20 => array(10, 15, 16, 19, 21, 22, 23),	// 長野　→群馬、新潟、富山、山梨、岐阜、静岡、愛知
		21 => array(16, 17, 18, 20, 23, 25),		// 岐阜　→富山、石川、福井、長野、愛知、滋賀
		22 => array(14, 19, 20, 23),				// 静岡　→神奈川、山梨、長野、愛知
		23 => array(20, 21, 22, 24),				// 愛知　→長野、岐阜、静岡、三重
		24 => array(23, 25, 26, 29, 30),			// 三重　→愛知、滋賀、京都、奈良、和歌山
		25 => array(18, 21, 24, 26),				// 滋賀　→福井、岐阜、三重、京都
		26 => array(18, 24, 25, 27, 28, 29),		// 京都　→福井、三重、滋賀、大阪、兵庫、奈良
		27 => array(26, 28, 29, 30),				// 大阪　→京都、兵庫、奈良、和歌山
		28 => array(26, 27, 31, 33),				// 兵庫　→京都、大阪、鳥取、岡山
		29 => array(24, 26, 27, 30),				// 奈良　→三重、京都、大阪、和歌山
		30 => array(24, 27, 29),					// 和歌山→三重、大阪、奈良
		31 => array(28, 32, 33, 34),				// 鳥取　→兵庫、島根、岡山、広島
		32 => array(31, 33, 34, 35),				// 島根　→鳥取、岡山、広島、山口
		33 => array(28, 31, 34),					// 岡山　→兵庫、鳥取、広島
		34 => array(31, 32, 33, 35),				// 広島　→鳥取、島根、岡山、山口
		35 => array(32, 34, 40),					// 山口　→島根、広島、福岡
		36 => array(37, 38, 39),					// 徳島　→香川、愛媛、高知
		37 => array(33, 36, 38),					// 香川　→岡山、徳島、愛媛
		38 => array(36, 37, 39),					// 愛媛　→徳島、香川、高知
		39 => array(36, 38),						// 高知　→徳島、愛媛
		40 => array(35, 41, 43, 44),				// 福岡　→山口、佐賀、熊本、大分
		41 => array(40, 42, 43),					// 佐賀　→福岡、長崎、熊本
		42 => array(40, 41, 43),					// 長崎　→福岡、佐賀、熊本
		43 => array(40, 41, 42, 44, 45, 46),		// 熊本　→福岡、佐賀、長崎、大分、宮崎、鹿児島
		44 => array(40, 43, 45),					// 大分　→福岡、熊本、宮崎
		45 => array(43, 44, 46),					// 宮崎　→熊本、大分、鹿児島
		46 => array(43, 45),						// 鹿児島→熊本、宮崎
		47 => array()								// 沖縄　→なし
	);

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function setAreamapViewVars($prefectureId, $areamapId, $areamapName) {
		$Landmark = ClassRegistry::init('Landmark');
		$Area = ClassRegistry::init('Area');
		$Station = ClassRegistry::init('Station');
		$Prefecture = ClassRegistry::init('Prefecture');

		//対象都道府県の空港一覧
		$airportLinkCdList = $Landmark->getAirportLinkCdListByPrefectureId($prefectureId);

		// エリアリスト
		$areaInfo = $Area->getAreaInfoByPrefectureId($prefectureId);
		$areaList = Hash::combine($areaInfo, '{n}.Area.id', '{n}.Area');
		unset($areaInfo);
		$this->controller->set(compact('areamapId', 'areamapName', 'airportLinkCdList', 'areaList'));

		$prefectureStationList = $Station->getStationListWithAreaByPrefectureId($prefectureId);

		$stationTypes = Constant::stationTypes();
		$stationListGroupByArea = $majorStationList = $mapStationList = array();
		if (!empty($prefectureStationList)) {
			// エリア別駅リスト
			$stationListGroupByArea['areas'] = array();
			foreach ($prefectureStationList as $k => $v) {
				if (!isset($stationListGroupByArea['areas'][$v['Area']['id']])) {
					$stationListGroupByArea['areas'][$v['Area']['id']] = $v['Area'];
					$stationListGroupByArea['areas'][$v['Area']['id']]['stations'] = array();
				}
				$stationListGroupByArea['areas'][$v['Area']['id']]['stations'][$v['Station']['id']] = $v['Station'];
				if (isset($stationTypes[$v['Station']['type']])) {
					$type = $stationTypes[$v['Station']['type']];
				} else {
					$type = '駅';
				}
				$stationListGroupByArea['areas'][$v['Area']['id']]['stations'][$v['Station']['id']]['type'] = $type;
				$prefectureStationList[$k]['Station']['type'] = $type;
			}
			// 主要駅リスト
			$majorStationList = Hash::extract($prefectureStationList, '{n}.Station[major_flg=1]');
			// 地図表示駅リスト
			$mapStationList = Hash::extract($prefectureStationList, '{n}.Station[pref_map_flg=1]');
		}
		unset($prefectureStationList);
		$this->controller->set(compact('stationListGroupByArea', 'majorStationList', 'mapStationList'));

		//// 都道府県関係 ////
		$prefectureList = $Prefecture->getAllLinkCdAndRegionLinkCd();

		$neighborhoodPrefectureList = $prefectureListGroupByRegion = array();
		if (!empty($prefectureList)) {
			// 近隣の都道府県
			$neighborhoodPrefectureList = $this->getNeighborhood($prefectureId, $prefectureList);
			// 地方別都道府県
			$prefectureListGroupByRegion = Hash::combine($prefectureList, '{n}.Prefecture.link_cd', '{n}.Prefecture.name', '{n}.Prefecture.region_link_cd');
		}

		unset($prefectureList);
		$this->controller->set(compact('neighborhoodPrefectureList', 'prefectureListGroupByRegion'));
	}

	private function getNeighborhood($prefectureId, $prefectureList) {
		$neighbors  = $this->neighbors[$prefectureId];
		$result = array();
		if (!empty($neighbors)) {
			$matcher = '(^'. implode('$|^', $neighbors) .'$)';
			$result = Hash::extract($prefectureList, "{n}.Prefecture[id=/$matcher/]");
		}
		return $result;
	}

}

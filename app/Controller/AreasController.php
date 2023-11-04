<?php
App::uses('AppController', 'Controller');

class AreasController extends AppController {

	public $uses = array('Area');

	/**
	 * ajax用 都道府県に応じたエリアをsetする
	 * @param string $prefectureId
	 * @param number $rentFlg
	 */
	public function set_area($prefectureId = '',$rentFlg = 1) {
		$this->autoLayout = false;

		if(!empty($prefectureId) && $this ->request->is( 'ajax' )) {
			$stockFlg = 0;
			if ($rentFlg == 1){
				$stockFlg = 1;
			}

			$default = null;
			if(!empty($this->request->query['default']) AND is_numeric($this->request->query['default'])){
				$default = $this->request->query['default'];
			}

			$areaList = $this->Area->getAreaListByPrefectureId($prefectureId, $stockFlg);
			$this->set('rentFlg',$rentFlg);
			$this->set('areaList',$areaList);
			$this->set('default',$default);

		}
	}

		//クライアントidで絞り込み取得
		public function set_area_byclientid($prefectureId = '',$rentFlg = 1,$clientId = '') {
		$this->autoLayout = false;
		if(!empty($prefectureId) && $this ->request->is( 'ajax' )) {
			$stockFlg = 0;
			if ($rentFlg == 1){
				$stockFlg = 1;
			}
			$default = null;
			if(!empty($this->request->query['default']) AND is_numeric($this->request->query['default'])){
				$default = $this->request->query['default'];
			}

			$areaList = $this->Area->getAreaListByPrefectureClientId($prefectureId, $stockFlg, $clientId);
			$this->set('rentFlg',$rentFlg);
			$this->set('areaList',$areaList);
			$this->set('default',$default);
		}
	}

		public function get_topareakey_byclientid($prefectureId = '',$clientId = '') {
		$this->autoLayout = false;
		if(!empty($prefectureId) && $this ->request->is( 'ajax' )) {
			$areaList = $this->Area->getAreaListByPrefectureClientId($prefectureId, '1', $clientId);
						$this->set('areaList',$areaList);
		}
	}

}
?>
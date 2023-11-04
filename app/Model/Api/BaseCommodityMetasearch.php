<?php

App::uses('Commodity', 'Model');
App::uses('CommodityItem', 'Model');
App::uses('CommodityEquipment', 'Model');

abstract class BaseCommodityMetasearch extends Commodity {
	// functionを上書きさせたいので、CommodityMetasearchSubQuery,CommoditySubQueryの順でロードする
	public $actsAs = array('CommodityCommon', 'CommodityMetasearchCommon', 'CommodityMetasearchSubQuery', 'CommoditySubQuery');

	public $useTable = 'commodities';
	public $alias = 'Commodity';

	// paginateCountをオーバーライド
	// ページャー処理は$this->_commoditiesを使ってphp側で制御しているのでクエリでは処理しない。
	public function paginateCount() {
		if (empty($this->_commodities)) {
			return 0;
		}
		return count($this->_commodities);
	}

}

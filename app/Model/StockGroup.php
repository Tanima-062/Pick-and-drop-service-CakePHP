<?php
App::uses('AppModel', 'Model');
/**
 * StockGroup Model
 */
class StockGroup extends AppModel {

	protected $cacheConfig = '1hour';

	public function getStockGroupListWithPrefecture($clientId) {
		$options = array(
			'fields' => array(
				'StockGroup.id',
				'StockGroup.name',
				'Prefecture.id',
				'Prefecture.name',
			),
			'joins' => array(
				array(
					'table' => 'prefectures',
					'alias' => 'Prefecture',
					'type' => 'INNER',
					'conditions' => array(
						'Prefecture.id = StockGroup.prefecture_id',
						'Prefecture.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'StockGroup.client_id' => $clientId,
				'StockGroup.delete_flg' => 0,
			),
			'order' => array('StockGroup.sort'),
			'recursive' => -1,
		);

		return $this->findC('all', $options);
	}
}

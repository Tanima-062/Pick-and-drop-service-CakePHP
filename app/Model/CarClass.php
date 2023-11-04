<?php
App::uses('AppModel', 'Model');
/**
 * CarClass Model
 */
class CarClass extends AppModel {

	protected $cacheConfig = '1hour';

	public function getCarClassListWithCarType($clientId) {
		$options = array(
			'fields' => array(
				'CarClass.id',
				'CarClass.name',
				'CarType.id',
				'CarType.name',
			),
			'joins' => array(
				array(
					'table' => 'car_types',
					'alias' => 'CarType',
					'type' => 'INNER',
					'conditions' => array(
						'CarType.id = CarClass.car_type_id',
						'CarType.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'CarClass.client_id' => $clientId,
				'CarClass.delete_flg' => 0,
			),
			'order' => 'CarClass.sort',
			'recursive' => -1,
		);

		return $this->findC('all', $options);
	}

	public function getAvailableCarClassList($clientId, $stockGroupId, $carClassId = 0) {
		$options = array(
			'fields' => array(
				'CarClass.id',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'stock_groups',
					'alias' => 'StockGroup',
					'conditions' => array(
						'StockGroup.id' => $stockGroupId,
						'StockGroup.client_id' => $clientId,
						'StockGroup.delete_flg' => 0,
					),
				),
				array(
					'type' => 'INNER',
					'table' => 'car_class_stock_groups',
					'alias' => 'CarClassStockGroup',
					'conditions' => array(
						'CarClassStockGroup.car_class_id = CarClass.id',
						'CarClassStockGroup.stock_group_id = StockGroup.id',
						'CarClassStockGroup.delete_flg' => 0,
					),
				),
			),
			'conditions' => array(
				'CarClass.client_id' => $clientId,
				'CarClass.delete_flg' => 0,
			),
			'order' => 'CarClass.sort',
			'recursive' => -1,
		);
		if (!empty($carClassId)) {
			$options['conditions']['CarClass.id'] = $carClassId;
		}

		return $this->findC('all', $options);
	}

	public function belongToClient($carClassId, $clientId) {
		$count = $this->findC('count', array(
			'conditions' => array(
				'id' => $carClassId,
				'client_id' => $clientId,
			),
			'recursive' => -1,
		));

		return ($count == 1);
	}

	public function isAvailable($carClassId)
	{
		$count = $this->findC('count', array(
			'conditions' => array(
				'id' => $carClassId,
				'delete_flg' => 0,
			),
			'recursive' => -1,
		));

		return ($count == 1);
	}
}

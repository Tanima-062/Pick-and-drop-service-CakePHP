<?php

App::uses('AppModel', 'Model');

/**
 * CommodityEquipment Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property Equipment $Equipment
 * @property Staff $Staff
 */
class CommodityEquipment extends AppModel {

	protected $cacheConfig = '1hour';

	public function getCommodityEquipment($commodityId) {

		$commodityEquipments = $this->findC('all', array(
			'conditions' => array(
				'CommodityEquipment.commodity_id' => $commodityId,
				'CommodityEquipment.delete_flg' => 0,
				'Equipment.is_published' => 1,
				'Equipment.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'Equipment',
					'table' => 'equipments',
					'conditions' => 'CommodityEquipment.equipment_id = Equipment.id',
				)
			),
			'fields' => array(
				'CommodityEquipment.commodity_id',
				'Equipment.id',
			),
			'recursive' => -1
				)
		);

		$equipment = array();
		foreach ($commodityEquipments as $commodityEquipment) {
			$key = $commodityEquipment['CommodityEquipment']['commodity_id'];
			$equipmentId = $commodityEquipment['Equipment']['id'];
			$equipment[$key][$equipmentId] = $equipmentId;
		}

		return $equipment;
	}

	public function getEquipmentData($commodityId) {
		$options = array(
			'fields' => array(
				'CommodityEquipment.equipment_id',
				'CommodityEquipment.id',
			),
			'conditions' => array(
				'CommodityEquipment.commodity_id' => $commodityId,
				'CommodityEquipment.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$result = $this->find('list', $options);
		return $result;
	}

	public function getEquipmentDataWithName($commodityId) {
		$options = array(
			'fields' => array(
				'Equipment.id',
				'Equipment.name',
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'equipments',
					'alias' => 'Equipment',
					'conditions' => array(
						'Equipment.id = CommodityEquipment.equipment_id',
						'Equipment.delete_flg' => 0,
					),
				)
			),
			'conditions' => array(
				'CommodityEquipment.commodity_id' => $commodityId,
				'CommodityEquipment.delete_flg' => 0,
			),
			'order' => array(
				'Equipment.sort'
			),
			'recursive' => -1,
		);
		return $this->findC('all', $options);
	}

	public function getCommodityEquipmentByCommodityIds($commodityIds) {

		return $this->findC('all', array(
			'conditions' => array(
				'CommodityEquipment.commodity_id' => $commodityIds,
				'CommodityEquipment.delete_flg' => 0,
			),
			'fields' => array(
				'CommodityEquipment.commodity_id',
				'CommodityEquipment.equipment_id',
			),
			'recursive' => -1
		));
	}

	/**
	 * @param array      $commodityIds 商品ID一覧
	 * @param array|null $fields       取得フィールド一覧
	 * @return array|null
	 */
	public function getEquipmentListByCommodityId($commodityIds, $fields = NULL) {

		if (is_null($fields)) {
			$fields = array(
				'CommodityEquipment.*',
				'Equipment.*',
			);
		}

		$options = array(
			'fields' => $fields,
			'conditions' => array(
				'CommodityEquipment.commodity_id' => $commodityIds,
				'CommodityEquipment.delete_flg' => 0,
			),
			'joins' => array(
				array(
					"table" => "equipments",
					"alias" => "Equipment",
					'type' => 'inner',
					'conditions' => array(
						'CommodityEquipment.equipment_id = Equipment.id',
						'Equipment.delete_flg' => 0,
					),
				),
			),
			'recursive' => -1,
		);

		return $this->find('all', $options);
	}

}

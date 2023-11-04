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
		return $this->find('all', $options);
	}
}

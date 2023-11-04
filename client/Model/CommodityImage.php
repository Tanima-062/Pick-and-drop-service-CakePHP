<?php
App::uses('AppModel', 'Model');
class CommodityImage extends AppModel {

	public function getImageByCommodityId($commodityId) {

		$options = array(
				'conditions' => array(
						'CommodityImage.delete_flg' => 0,
						'CommodityImage.commodity_id' => $commodityId
				),
				'order' => array(
						'CommodityImage.id' => 'ASC'
				),
				'recursive' => -1
		);

		$images = $this->find('all', $options);

		if (!empty($images)) {

			$image = array();
			foreach ($images as $key => $val) {
				$image[$key]['image_relative_url'] = $val['CommodityImage']['image_relative_url'];
				$image[$key]['remark'] = $val['CommodityImage']['remark'];
			}

			return $image;
		}

		return false;

	}

	public function getFirstImageByCommodityIds($commodityIds) {
		$options = array(
			'fields' => array(
				'CommodityImage.id',
				'CommodityImage.image_relative_url',
				'CommodityImage.commodity_id',
			),
			'conditions' => array(
				'CommodityImage.commodity_id' => $commodityIds,
				"CommodityImage.image_relative_url != ''",
				'CommodityImage.delete_flg' => 0,
			),
			'order' => array(
					'CommodityImage.id'
			),
			'recursive' => -1,
		);
		
		$ret = $this->find('list', $options);
		
		foreach ($ret as $commodityId => $images) {
			// 最初の画像を取得する
			$ret[$commodityId] = array_values($images)[0];
		}
		
		return $ret;
	}
}

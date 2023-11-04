<?php
App::uses('AppModel', 'Model');
/**
 * CommodityImage Model
 *
 * @property Client $Client
 * @property Commodity $Commodity
 * @property Staff $Staff
 */
class CommodityImage extends AppModel {

	protected $cacheConfig = '1hour';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'commodity_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'image_relative_url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'remark' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Commodity' => array(
			'className' => 'Commodity',
			'foreignKey' => 'commodity_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

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

		$images = $this->findC('all', $options);

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

	public function getImageByClientId($clientId) {
		$options = array(
			'fields' => array(
				'CommodityImage.image_relative_url'
			),
			'conditions' => array(
				'CommodityImage.image_relative_url <>' => '',
				'CommodityImage.delete_flg' => 0,
			),
			'joins' => array(
				array(
					'type' => 'INNER',
					'table' => 'commodities',
					'alias' => 'Commodity',
					'conditions' => array(
						'Commodity.client_id' => $clientId,
						'Commodity.is_published' => 1,
						'Commodity.delete_flg' => 0,
						'Commodity.id = CommodityImage.commodity_id'
					)
				)
			),
			'order' => array(
				'CommodityImage.id' => 'ASC'
			),
			'recursive' => -1
		);

		$image = $this->find('first', $options);

		return $image;
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
		
		$ret = $this->findC('list', $options);
		
		foreach ($ret as $commodityId => $images) {
			// 最初の画像を取得する
			$ret[$commodityId] = array_values($images)[0];
		}
		
		return $ret;
	}

}

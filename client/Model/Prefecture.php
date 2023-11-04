<?php
App::uses('AppModel', 'Model');
/**
 * Prefecture Model
 *
 * @property Staff $Staff
 * @property Reservation $Reservation
 */
class Prefecture extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
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
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	public function getPrefectureList() {
		return $this->find('list',array('conditions'=>array('delete_flg'=>0)));
	}

	public function getAllFindPrefecture($prefectureId) {
		$options = array(
				'conditions' => array(
						'Prefecture.id' => $prefectureId,
						'Prefecture.delete_flg' => 0,
				),
				'recursive' => -1,
		);
		return $this->find('all', $options);
	}

	public function getPrefectureListExistStockGroupByClientID($clientId) {
		$options = array(
				'conditions' => array(
						'StockGroup.client_id'=>$clientId,
						'StockGroup.delete_flg' => 0,
						'Prefecture.delete_flg' => 0
				),
				'joins'=>array(
						array(
							'type'=>'INNER',
							'alias'=>'StockGroup',
							'table'=>'stock_groups',
							'conditions'=>'Prefecture.id = StockGroup.prefecture_id'
						)
				),
				'order'=>array(
					'Prefecture.sort',
					'StockGroup.sort',
				),
		);

		return $this->find('list', $options);
	}

}

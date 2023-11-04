<?php
App::uses('AppModel', 'Model');
/**
 * BulletTrainArea Model
 *
 */
class BulletTrainArea extends AppModel {

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
	);

	public function getBulletTrainAreaList() {
		return $this->find('list',array('conditions'=>array('delete_flg'=>0)));
	}

}

<?php
App::uses('AppModel', 'Model');
/**
 * ClientCarModelSort Model
 *
 * @property Client $Client
 * @property CarModel $CarModel
 */
class ClientCarModelSort  extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */


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
		'CarModel' => array(
			'className' => 'CarModel',
			'foreignKey' => 'car_model_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}

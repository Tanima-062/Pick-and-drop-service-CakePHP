<?php

App::uses('AppModel', 'Model');

/**
 * Commodity Model
 *
 * @property Client $Client
 */
class Commodity extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array();


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array();

	/**
	 * hasMany associations
	 *
	 * @var array
	 *
	 */
	public $hasMany = array();

}

<?php
App::uses('AppModel', 'Model');
/**
 * Search Model
 *
 */
class SearchTravelko extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = false;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'rental_area' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => 'numeric',
				'message' => false,
				'last' => true,
			),
		),
		'rental_area_type' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 0, 4),
				'message' => false,
				'last' => true,
			),
		),
		'return_area' => array(
			array(
				'rule' => 'numeric',
				'message' => false,
				'allowEmpty' => true,
				'last' => true,
			),
		),
		'return_area_type' => array(
			array(
				'rule' => array('range', 0, 4),
				'message' => false,
				'allowEmpty' => true,
				'last' => true,
			),
		),
		'rental_time' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('custom', '/^2[0-9]{3}[0-1][0-9][0-3][0-9][0-2][0-9](00|30)$/'),
				'message' => false,
				'last' => true,
			),
		),
		'return_time' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('custom', '/^2[0-9]{3}[0-1][0-9][0-3][0-9][0-2][0-9](00|30)$/'),
				'message' => false,
				'last' => true,
			),
		),
		'shop' => array(
			array(
				'rule' => array('custom', '/^\d+(-\d+)*$/'),
				'message' => false,
				'last' => true,
			),
		),
		'limit' => array(
			array(
				'rule' => 'numeric',
				'message' => false,
				'allowEmpty' => true,
				'last' => true,
			),
		),
		'offset' => array(
			array(
				'rule' => 'numeric',
				'message' => false,
				'allowEmpty' => true,
				'last' => true,
			),
		),
		'sort' => array(
			array(
				'rule' => array('range', 0, 4),
				'message' => false,
				'allowEmpty' => true,
				'last' => true,
			),
		),
	);
}

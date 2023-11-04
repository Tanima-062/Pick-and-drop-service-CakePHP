<?php
App::uses('AppModel', 'Model');
/**
 * Search Model
 *
 */
class Search extends AppModel {

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
		'place' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 0, 5),
				'message' => false,
				'last' => true,
			),
		),
		'year' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 1999, 2100),
				'message' => false,
				'last' => true,
			),
		),
		'month' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 0, 13),
				'message' => false,
				'last' => true,
			),
		),
		'day' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 0, 32),
				'message' => false,
				'last' => true,
			),
		),
		'time' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('custom', '/^[0-2][0-9]-(00|30)$/'),
				'message' => false,
				'last' => true,
			),
		),
		'return_way' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', -1, 2),
				'message' => false,
				'last' => true,
			),
		),
		'return_year' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 1999, 2100),
				'message' => false,
				'last' => true,
			),
		),
		'return_month' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 0, 13),
				'message' => false,
				'last' => true,
			),
		),
		'return_day' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('range', 0, 32),
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
				'rule' => array('custom', '/^[0-2][0-9]-(00|30)$/'),
				'message' => false,
				'last' => true,
			),
		),
		'date' => array(
			array(
				'rule' => array('custom', '/^2[0-9]{3}\/[0-1][0-9]\/[0-3][0-9]$/'),
				'message' => false,
				'last' => true,
			),
		),
		'return_date' => array(
			array(
				'rule' => array('custom', '/^2[0-9]{3}\/[0-1][0-9]\/[0-3][0-9]$/'),
				'message' => false,
				'last' => true,
			),
		),
	);
}

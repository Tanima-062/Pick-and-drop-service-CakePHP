<?php
App::uses('AppModel', 'Model');
/**
 * Search Model
 *
 */
class SearchSkyscanner extends AppModel {

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
		'rental_type' => array(
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
		'rental_point' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('custom', '/^([0-9]+|[A-Za-z]{3})$/'),
				'message' => false,
				'last' => true,
			),
		),
		'return_type' => array(
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
		'return_point' => array(
			array(
				'rule' => 'notblank',
				'message' => false,
				'required' => true,
				'last' => true,
			),
			array(
				'rule' => array('custom', '/^([0-9]+|[A-Za-z]{3})$/'),
				'message' => false,
				'last' => true,
			),
		),
		'rental_datetime' => array(
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
		'return_datetime' => array(
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
	);
}

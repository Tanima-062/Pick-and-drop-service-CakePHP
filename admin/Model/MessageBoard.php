<?php
App::uses('AppModel', 'Model');
/**
 * MessageBoard Model
 */
class MessageBoard extends AppModel {

	public $belongsTo = [
		'Staff' => [
			'className' => 'Staff',
			'fields' => [
				'Staff.name'
			]
		]
	];
}

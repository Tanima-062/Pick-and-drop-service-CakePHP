<?php

App::uses('AppModel', 'Model');

/**
 * MailTemplateCategory Model
 *
 * @property MailTemplateCategory $MailTemplateCategory
 */
class MailTemplateCategory extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'rule' => array('maxLength', '128'),
			'message' => 'カテゴリ名は%s文字以内で入力してください',
			'allowEmpty' => false
		),
	);


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

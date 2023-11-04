<?php

App::uses('AppModel', 'Model');

/**
 * MailTemplate Model
 *
 * @property MailTemplate $MailTemplate
 */
class MailTemplate extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'rule' => array('maxLength', '128'),
			'message' => 'テンプレート名は%s文字以内で入力してください',
			'allowEmpty' => false
		),
		'mail_template_category_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'カテゴリを選択してください。',
			),
			'numeric' => array(
				'rule' => 'numeric',
				'message' => '不正なカテゴリが選択されました。',
			),
			'allowEmpty' => false
		),
		'mail_from' => array(
			'email' => array(
				'rule' => 'email',  
				'message' => 'メールFromにはメールアドレスを入力してください',
			),
			'maxlength' => array(
				'rule' => array('maxLength', '1024'),  
				'message' => 'メールFromは%s文字以内で入力してください'
			),
			'allowEmpty' => false
		),
		'mail_subject' => array(
			'rule' => array('maxLength', '128'),
			'message' => 'メール件名は%s文字以内で入力してください',
			'allowEmpty' => false
		),
		'mail_content' => array(
			'rule' => array('maxLength', '65535'),
			'message' => 'メール本文は%s文字以内で入力してください',
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

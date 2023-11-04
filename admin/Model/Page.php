<?php
App::uses('AppModel', 'Model');
/**
 * Page Model
 *
 * @property PageCategory $PageCategory
 * @property PageViewingPermission $PageViewingPermission
 */
class Page extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'page_category_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'name' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'ページ名は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'url' => array(
			'notblank' => array(
				'rule' => array('notblank'),
				'message' => 'URLは必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'new_tab_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
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
		'PageCategory' => array(
			'className' => 'PageCategory',
			'foreignKey' => 'page_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'PageViewingPermission' => array(
			'className' => 'PageViewingPermission',
			'foreignKey' => 'page_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	//最後のソート番号にプラス1した値を返却する
	public function getSortNum($options = array()) {
		$this->recursive = -1;
		$sortArray = $this->find('first',array('conditions'=>array('delete_flg'=>0),'order'=>'sort desc'));
		$sort = $sortArray['Page']['sort'];
		if(empty($sort)) {
			$sort = 0;
		}
		return ++$sort;
	}

}

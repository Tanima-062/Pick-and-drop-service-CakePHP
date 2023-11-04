<?php
App::uses('AppModel', 'Model');
/**
 * PageCategory Model
 *
 * @property Staff $Staff
 * @property Page $Page
 */
class PageCategory extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => '値を入力してください。',
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
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Page' => array(
			'className' => 'Page',
			'foreignKey' => 'page_category_id',
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
	public function getSortNum() {
		$this->recursive = -1;
		$sortArray = $this->find('first',array('conditions'=>array('delete_flg'=>0),'order'=>'sort desc'));
		$sort = $sortArray['PageCategory']['sort'];
		if(empty($sort)) {
			$sort = 0;
		}
		return ++$sort;
	}

	public function getPageCategoryData() {
		$this->hasMany['Page']['conditions'] = array('delete_flg' => 0);
		$options = array(
				'conditions' => array(
						'PageCategory.delete_flg' => 0
				),
				'order' => array(
						'PageCategory.sort' => 'asc',
				),
				'recursive' => 1,
		);
		$result = $this->find('all', $options);
		$this->hasMany['Page']['conditions'] = '';

		return $result;
	}

}

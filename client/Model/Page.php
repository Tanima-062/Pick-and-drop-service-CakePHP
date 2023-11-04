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
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'ページ名は必須です',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
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

	/**
	 * URLが登録されているかチェックする
	 * 登録されている場合は配列を返却する
	 *
	 * @param string $url
	 * @return array
	 */
	public function getPageInfo($url) {

		return $this->find('first', array(
				'conditions' => array(
					'Page.url like' => $url,
					'Page.delete_flg' => 0,
				)
			)
		);
	}

	/**
	 * アクセスできるページを取得する
	 * @param unknown $staffId
	 * @return array
	 */
	public function getAllPages() {

		$pageList = $this->find('all', array(
				'conditions' => array(
					'Page.delete_flg' => 0
				),
				'joins' => array(
					array(
						'type' => 'INNER',
						'table' => 'page_categories',
						'alias' => 'PageCategory',
						'conditions' => 'PageCategory.id = Page.page_category_id'
					),
				),
				'fields' => array(
					'Page.id',
					'Page.page_category_id',
					'Page.name',
					'Page.url',
					'Page.new_tab_flg',
					'PageCategory.*'
				),
				'order' => 'PageCategory.sort asc,Page.sort asc',
				'recursive' => -1
			)
		);

		$pages = array();
		foreach ($pageList as $page) {
			$pageCategoryId = $page['PageCategory']['id'];
			$pages[$pageCategoryId]['category_name'] = $page['PageCategory']['name'];
			$pages[$pageCategoryId]['page'][] = $page['Page'];
		}

		return $pages;

	}

}

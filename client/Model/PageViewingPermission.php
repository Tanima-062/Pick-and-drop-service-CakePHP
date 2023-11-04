<?php
App::uses('AppModel', 'Model');
/**
 * PageViewingPermission Model
 *
 */
class PageViewingPermission extends AppModel {

	/**
	 * 該当スタッフがページへのアクセス権限を持っているかチェックする
	 *
	 * @param unknown $pageId
	 * @param unknown $staffId
	 * @return bool
	 */
	public function checkPermission($pageId,$staffId) {

		return (bool)$this->find('count',array(
				'conditions'=>array(
						'page_id'=>$pageId,
						'staff_id'=>$staffId
					)
				)
			);

	}

	/**
	 * スタッフIDからアクセスできるページを取得する
	 * @param unknown $staffId
	 * @return array
	 */
	public function getPages($staffId) {

		$pageList = $this->find('all',array(
				'conditions'=>array(
						'PageViewingPermission.staff_id'=>$staffId,
						'Page.delete_flg'=>0
				),
				'joins'=>array(
						array(
								'type'=>'INNER',
								'table'=>'pages',
								'alias'=>'Page',
								'conditions'=>'Page.id = PageViewingPermission.page_id'
						),
						array(
								'type'=>'INNER',
								'table'=>'page_categories',
								'alias'=>'PageCategory',
								'conditions'=>'PageCategory.id = Page.page_category_id'
						),
				),
				'fields'=>array(
						'Page.id',
						'Page.page_category_id',
						'Page.name',
						'Page.url',
						'Page.new_tab_flg',
						'PageCategory.*'
				),
				'order'=>'PageCategory.sort asc,Page.sort asc'
			)
		);

		$pages = array();
		foreach($pageList as $page) {
			$pageCategoryId = $page['PageCategory']['id'];
			$pages[$pageCategoryId]['category_name'] = $page['PageCategory']['name'];
			$pages[$pageCategoryId]['page'][] = $page['Page'];
		}

		return $pages;

	}

}

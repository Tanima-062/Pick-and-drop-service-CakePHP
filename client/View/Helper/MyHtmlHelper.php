<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class MyHtmlHelper extends Helper {

	public function h($string) {

		preg_match_all("/&lt;a href=.*&lt;\/a&gt;/iu",$string,$pattarn);
		//<a>タグにマッチする文字列を$pattarn[0][0]から順番に格納した

		if (!empty($pattarn[0])) {
			foreach ($pattarn[0] as $key=>$val){
				if(preg_match("/(http|https):\/\/[-\w\.]+(:\d+)?(\/[^\s]*)?$/",$pattarn[0][$key])){
					//urlとして正しいものが含まれている場合は処理をする
					$replace[] = htmlspecialchars_decode($pattarn[0][$key]);
					//htmlspecialchars_decodeでエスケープ処理を解除した
				}else{
					$replace[] = $pattarn[0][$key];
					//urlとして正しいものが含まれていない場合は処理しない
				}
			}
			$string = str_replace($pattarn[0],$replace,$string);
			//パターンにマッチした部分(<a>タグ)だけを元にもどした
		}

		return $string;

	}
}

<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	// デフォルトキャッシュ有効期限
	protected $cacheConfig = '3minutes';
	// クエリキャッシュ使用フラグ
	// 対象はfindC、queryCのみ find、queryは影響しない
	protected $useQueryCache = true;

	// ランドマークのイベント系(1~12月)のカテゴリID
	public $eventCategory = array(
			4,5,6,7,8,9,10,11,12,13,14,15
	);

	/**
	 * slave接続エラーでmasterに向ける
	 *
	 * @param string $dataSource
	 * @return void
	 * @throws MissingConnectionException
	 */
	public function setDataSource($dataSource = null) {
		try {
			parent::setDataSource($dataSource);
		} catch (MissingConnectionException $e) {
			if ($dataSource !== 'default_slave') {
				throw $e;
			}
			$this->useDbConfig = 'default';
			parent::setDataSource();
		}
	}

	function begin() {
		$db = $this->getDataSource($this->useDbConfig);
		$db->begin($this);
	}
	function commit() {
		$db = $this->getDataSource($this->useDbConfig);
		$db->commit($this);
	}
	function rollback() {
		$db = $this->getDataSource($this->useDbConfig);
		$db->rollback($this);
	}

	/**
	 * プレースホルダのためのバインド配列を生成する
	 * @param string $parameter パラメータ名
	 * @param mixed $value バインドする値
	 * @return array パラメータ文字列, バインド値
	 * @throws Exception
	 */
	public function createBindArray($parameter, $value) {
		if (empty($parameter) || empty($value)) {
			throw new Exception(__FUNCTION__);
		}

		if ($parameter[0] == ':') {
			$parameter = substr($parameter, 1);
		}

		// $valueが配列でない場合
		if (!is_array($value)) {
			return array(
				':' . $parameter,
				array($parameter => $value),
			);
		}

		// $valueが配列の場合は値毎にキーを生成
		$keys = array();
		$values = array();
		foreach ($value as $k => $v) {
			$k = "{$parameter}_{$k}";
			$keys[] = ':' . $k;
			$values[$k] = $v;
		}

		return array(
			implode(',', $keys),
			$values,
		);
	}

	// キャッシュ化対応のfind関数
	public function findC($type = 'first', $query = array(), $config = '') {
		if (!$this->useQueryCache) {
			return parent::find($type, $query);
		}
		$cache_name = $this->getCacheKey($this->getLastModified(), $type, $query);
		$cache_ret = $this->readCache($cache_name, $config);
		if ($cache_ret !== false) {
			return $cache_ret;
		}
		$ret = parent::find($type, $query);
		$this->writeCache($cache_name, $ret, $config);
		return $ret;
	}

	// キャッシュ化対応のquery関数
	public function queryC($query, $params = array(), $config = '') {
		if (!$this->useQueryCache) {
			return parent::query($query, $params);
		}
		$cache_name = $this->getCacheKey($this->getLastModified(), $query, $params);
		$cache_ret = $this->readCache($cache_name, $config);
		if ($cache_ret !== false) {
			return $cache_ret;
		}
		$ret = parent::query($query, $params);
		$this->writeCache($cache_name, $ret, $config);
		return $ret;
	}

	//ページネーションでGroup By使ったときのページ数
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$parameters = compact('conditions');
		$this->recursive = $recursive;
		$count = $this->find('count', array_merge($parameters, $extra));
		if (isset($extra['group'])) {
			$count = $this->getAffectedRows();
		}
		return $count;
	}

	public function getCookie($cookieName) {
		$ret = '';
		if (isset($_COOKIE[$cookieName])) {
			$ret = $_COOKIE[$cookieName];
		} else {
			$value  = md5(date('U') + microtime() + mb_ereg_replace("\.", "", $_SERVER['REMOTE_ADDR']));
			$expire = time()+ (60 * 60 * 24 * 365);   //
			setcookie($cookieName, $value, $expire, '/');
			$ret = $value;
		}
		return $ret;
	}

	/**
	 * 営業所名に"店"を付与する
	 * @param string $name
	 * @return string
	 */
	protected function addSuffixOfOffice($name) {
		if (empty($name)) {
			return '';
		}

		// トライレンタカー、宮古島レンタカージオ、レンタカー金助は"本店"を返す
		$words = array('トライ', 'ジオ', '金助');

		str_replace($words, '', $name, $count);

		if ($count != 0) {
			return $name . '本店';
		}

		// "店"が含まれず、"○○所"でない場合は"店"を付与
		if (strpos($name, '店') === false && !preg_match('/.*所$/', $name)) {
			// 括弧付き対応マッチパターン
			$pattern = '/^(.*?)([\(|（].*?[\)|）])?$/u';
			$replacement = '$1店$2';

			return preg_replace($pattern, $replacement, $name);
		}

		return $name;
	}

	/**
	 * WordPressの画像パスを置換する
	 * @param string $name
	 * @return string
	 */
	protected function replaceWpImagePath($path) {
		if (empty($path)) {
			return '';
		}
		return str_replace('http://160.16.81.254/wp/rentacar/wp-content/uploads/', '/rentacar/wp/img/', $path);
	}

	/**
	 * Cacheから指定した値を取得
	 * @param string $key
	 * @param string $config
	 * @return array
	 */
	protected function readCache($key, $config = '') {
		$ret = Cache::read($key, empty($config) ? $this->cacheConfig : $config);

		if (empty($ret)) {
			return $ret;
		}

		return json_decode(gzuncompress($ret), true);	// 解凍しjsonを配列に変換
	}

	/**
	 * 指定した値をCacheに保存
	 * @param string $key
	 * @param array  $value
	 * @param string $config
	 * @return boolean
	 */
	protected function writeCache($key, $value, $config = '') {
		// falseになるのはvalueが未定義の場合のみ
		$obj = (isset($value) || is_null($value)) ? gzcompress(json_encode($value), 1) : array();	// json化し圧縮
		$ret = Cache::write($key, $obj, empty($config) ? $this->cacheConfig : $config);

		if ($ret === false) {
			$this->log('cache write error: ' . Debugger::trace());
		}

		return $ret;
	}

	/**
	 * キャッシュのキーを生成する
	 * @return string
	 */
	protected function getCacheKey() {
		return hash('sha256', json_encode(func_get_args()), false);
	}

	/**
	 * テーブルの更新タイムスタンプをキャッシュから取得する
	 * 存在しなければ現在のタイムスタンプ
	 * @return string
	 */
	protected function getLastModified() {
		$ret = Cache::read($this->useTable, '1day');
		if ($ret !== false) {
			return $ret;
		}
		$ret = time();
		Cache::write($this->useTable, $ret, '1day');
		return $ret;
	}

	/**
	 * キャッシュのテーブル更新タイムスタンプを初期化する
	 * @return boolean
	 */
	protected function resetLastModified() {
		$ret = time();
		Cache::write($this->useTable, $ret, '1day');
		return $ret;
	}

	public function loadComponent($componentClass, $settings = array()) {
		if (!isset($this->{$componentClass})) {
			if (!isset($this->Components)) {
				$this->Components = new ComponentCollection();
			}
			App::uses($componentClass, 'Controller/Component');
			$this->{$componentClass} = $this->Components->load($componentClass, $settings);
		}
	}
}

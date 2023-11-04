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

	public function beforeSave($options = array()){
		parent::beforeSave($options);

		// 対象のモデルのみ処理させたい
		$models = array(
			'Prefecture'	 => 'link_cd',
			'Area'			 => 'area_link_cd',
			'Client'		 => 'url',
			'Landmark'		 => 'link_cd',
		);

		if (!isset($models[$this->name])) {
			return true;
		}

		$data = $this->data[$this->name];
		$link_cd = $models[$this->name];

		// 新規登録または主キー単体での更新の場合のみ
		if (isset($data['id']) && is_array($data['id'])) {
			return true;
		}

		// リンクコードが空文字の時、または削除時はnullに更新する
		if ((isset($data[$link_cd]) && $data[$link_cd] == '') || !empty($data['delete_flg'])) {
			$this->data[$this->name][$link_cd] = null;
		}
		// 都道府県のみ
		if ($this->name == 'Prefecture') {
			if ((isset($data['region_link_cd']) && $data['region_link_cd'] == '') || !empty($data['delete_flg'])) {
				$this->data[$this->name]['region_link_cd'] = null;
			}
		}

		return true;
	}

	function afterSave($created) {

		parent::afterSave($created);

		// テーブルの更新タイムスタンプをキャッシュに保存する
		switch ($this->alias) {
			case 'UpdatedTable':
				break;
			default:
				Cache::write($this->useTable, time(), '1day');
				break;
		}

		// コンテンツページのキャッシュをクリアする
		switch ($this->alias) {
			case 'Prefecture':
			case 'Landmark':
			case 'Area':
			case 'Client':
			case 'City':
			case 'Station':
				$configs = Cache::groupConfigs('anchortext');
				foreach ($configs['anchortext'] as $config) {
					Cache::clearGroup('anchortext', $config);
				}
				break;
			default:
				break;
		}

		$UpdatedTable = ClassRegistry::init('UpdatedTable');

		if($this->alias == 'Commodity') {

			if(!empty($this->data[$this->alias]['is_published']) && $this->data[$this->alias]['is_published']) {
				$saveContents['category'] =  '商品管理';

				$saveContents['content'] = "商品情報（商品管理番号：" . $this->data[$this->alias]['commodity_key'] . "）を【公開】更新しました。";

				$saveContents['url'] = '/client/commodities/edit/' . $this->data[$this->alias]['id'];

				$saveContents['client_id'] = $this->data[$this->alias]['client_id'];
				$saveContents['staff_id'] = $this->data[$this->alias]['staff_id'];
				$UpdatedTable->save($saveContents);
			}
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

}

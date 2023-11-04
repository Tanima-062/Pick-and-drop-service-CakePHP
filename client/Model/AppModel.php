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
			'Office'		 => 'url',
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

		return true;
	}

	function afterSave($created) {

		parent::afterSave($created);

		// テーブルの更新タイムスタンプをキャッシュに保存する
		switch ($this->alias) {
			case 'UpdatedTable':
				break;
			case 'DropOffAreaRate':
				Cache::write('commodities', time(), '1day');
			default:
				Cache::write($this->useTable, time(), '1day');
				break;
		}

		// コンテンツページのキャッシュをクリアする
		switch ($this->alias) {
			case 'Client':
			case 'Office':
				$configs = Cache::groupConfigs('anchortext');
				foreach ($configs['anchortext'] as $config) {
					Cache::clearGroup('anchortext', $config);
				}
				break;
			default:
				break;
		}

		$data = null;
		if (empty($this->data[$this->alias])) {
			// データが無ければ何もしない
			return;
		} else {
			$data = $this->data[$this->alias];
		}
		// 変数取得
		$id = !empty($data['id']) ? $data['id'] : '';
		$name = !empty($data['name']) ? $data['name'] : '';
		$delete_flg = !empty($data['delete_flg']) ? $data['delete_flg'] : null;
		$process = null;

		// 処理内容を設定
		if($created) {
			$process = '新規登録';
		} else {
			$process = empty($delete_flg) ? '更新' : '削除';
		}

		$UpdatedTable = ClassRegistry::init('UpdatedTable');

		//お問い合わせに返信を行いました。
		if($this->alias == 'ReservationMail') {

			$saveContents['id'] ='';
			$saveContents['category'] =  '顧客管理';
			$saveContents['content'] = 'お問い合わせに返信を行いました。';

		//顧客データを更新しました。
		} else if($this->alias == 'Reservation') {

			//予約番号を取得
			$Reservation = ClassRegistry::init('Reservation');
			$updatedReservationKey = $Reservation->find('first',array(
					'conditions'=>array(
							'Reservation.id'=>$id
					),
					'fields'=>array(
							'Reservation.reservation_key'
					),
					'recursive' => -1,
			));

			$saveContents['id'] ='';
			$saveContents['category'] =  '顧客管理';
			$saveContents['content'] = '顧客データ (予約番号:'. $updatedReservationKey['Reservation']['reservation_key'].') を更新しました。';

		//商品データを更新しました。
		} else if($this->alias == 'Commodity' && !isset($data['not_update_table'])) {

			$saveContents['category'] =  '商品一覧';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "商品情報 (商品ID：{$id}) を{$process}しました。";

		//商品グループを更新しました。
		} else if($this->alias == 'CommodityGroup') {

			if (isset($data['available_from'])) {
				$saveContents['url'] = Router::url('detail_edit') . '/' . $id;
			} else {
				$saveContents['url'] = Router::url('edit') . '/' . $id;
			}

			$saveContents['category'] = '商品グループ管理';
			$saveContents['content'] = "商品グループ ({$name}) を{$process}しました。";

		//キャンペーン期間を更新しました。
		} else if($this->alias == 'Campaign') {

			$saveContents['category'] = 'キャンペーン期間管理';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "キャンペーン期間（{$name}）を{$process}しました。";

		//基本情報を更新しました。
		} else if($this->alias == 'Client') {

			$saveContents['category'] =  '基本情報管理';
			$saveContents['content'] = '基本情報を更新しました。';

		//営業所情報を更新しました
		} else if($this->alias == 'Office' && isset($name)) {

			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['category'] = '営業所一覧';
			$saveContents['content'] = "営業所情報 ({$name}) を{$process}しました。";

		//特別営業時間
		} else if($this->alias == 'OfficeBusinessHour') {
			$saveContents['category'] =  '特別営業時間';
			$saveContents['content'] = "特別営業時間 ({$id}) を{$process}しました。";
		//在庫管理地域を更新しました。
		} else if($this->alias == 'StockGroup') {
			$saveContents['category'] =  '在庫管理地域一覧';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "在庫管理地域 ({$name}) を{$process}しました。";

		//車種情報を更新しました。
		} else if($this->alias == 'ClientCarModel' && !isset($data['not_update_table'])) {

			$saveContents['category'] =  '車種一覧';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "車種情報 (ID：{$id}) を{$process}しました。";

		//車両クラスを更新しました。
		} else if($this->alias == 'CarClass') {

			$saveContents['category'] =  '車両クラス管理';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "車両クラス ({$name}) を{$process}しました。";

		//免責補償料金を更新しました。
		} else if($this->alias == 'DisclaimerCompensation') {

			$saveContents['category'] =  '免責補償料金設定';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "免責補償料金 (ID：{$id}) を{$process}しました。";

		//乗捨エリアを更新しました。
		} else if($this->alias == 'DropOffArea') {

			$saveContents['category'] =  '乗捨エリア一覧';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "乗捨エリア (ID：{$id}) を{$process}しました。";

		//乗捨料金を更新しました。
		} else if($this->alias == 'DropOffAreaRate') {

			$saveContents['category'] =  '乗捨料金一覧';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "乗捨料金 (ID：{$id}) を{$process}しました。";

		//深夜手数料を更新しました。
		} else if($this->alias == 'LateNightFee') {

			$saveContents['category'] =  '深夜手数料';
			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['content'] = "深夜手数料 (ID：{$id}) を{$process}しました。";

		//パスワードを更新しました。
		} else if($this->alias == 'Staff') {

			$saveContents['category'] =  'パスワード変更';

			if(!$created) {
				$saveContents['content'] = 'パスワード変更を行いました。';
			}

		//特典を更新しました
		} else if($this->alias == 'Privilege') {

			$saveContents['url'] = Router::url('edit') . '/' . $id;
			$saveContents['category'] =  'オプション管理';
			$saveContents['content'] = "オプション ({$name}) を{$process}しました。";
		}

		//insert
		if(!empty($saveContents) && !empty($saveContents['content'])) {


			$saveContents['client_id'] = CakeSession::read("clientData.Client.id");
			if(empty($saveContents['client_id'])) {
				$saveContents['client_id'] = CakeSession::read("Auth.User.client_id");
			}

			$saveContents['staff_id'] = CakeSession::read("Auth.User.id");

			if(empty($saveContents['url']) && $this->alias != 'Staff') {
				$saveContents['url'] = Router::url();
			}

			$UpdatedTable->save($saveContents);
		}

	}

	//画像をアップロードする関数
	public function saveImage($file,$uploadPath = 'commodity_img', $fileName = '') {

		 $uploadPath =  ROOT .DS . WEBROOT_DIR . DS . "img" . DS . $uploadPath;
		$result = false;

		if (is_uploaded_file($file["tmp_name"])) {

			//拡張子判別
			$imgInfo = getimagesize($file["tmp_name"]);

			$extension = $this->getExtension($imgInfo[2]);

			if ($extension) {

				if(!empty($fileName)) {
					$fileName = $fileName . $extension;
				} else {
					$fileName = $this->getNewFileName().$extension;
				}

				if(!file_exists($uploadPath)) {
					mkdir($uploadPath,0777);
				}
				if( move_uploaded_file($file["tmp_name"], $uploadPath .DS. $fileName) ){
					chmod( $uploadPath . $fileName, 0644);
					$result = $fileName;
				} else {
					$this->validationErrors['file'] = __('ファイルの保存に失敗しました');
				}
			} else {
				$this->validationErrors['file'] = __('不正なファイル形式です');
			}

		} else {
			$this->validationErrors['file'] = __('アップロードに失敗しました。');
		}

		return $result;
	}

	public function savePdf($file,$uploadPath,$fileName = ''){

		 $uploadPath =  ROOT .DS . WEBROOT_DIR . DS . "files" . DS . $uploadPath;

		if(!empty($file["tmp_name"]) && $file["size"]>0){
			$nameArray = explode(".",$file["name"]);
			$extention = $nameArray[count($nameArray)-1];
			if($extention == "pdf"){

				if(empty($fileName)) {
					$fileName = $this->getNewFileName(). '.pdf';
				}

				if(!file_exists($uploadPath)) {
					mkdir($uploadPath,'0777');
				}

				if(move_uploaded_file($file["tmp_name"],$uploadPath.DS . $fileName)){
					return $fileName;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * 画像の拡張子を取得
	 */
	private function getExtension($image) {
		switch ($image) {
			case IMAGETYPE_GIF : // gif
				$extension = '.gif';
				break;
			case IMAGETYPE_JPEG: // jpeg
				$extension = '.jpg';
				break;
			case IMAGETYPE_PNG : // png
				$extension = '.png';
				break;
			default:
				$extension = false;
		}

		return $extension;

	}

	/**
	 * 画像名をランダムで生成
	 */
	private function getNewFileName() {

		$newTime = time();
		$ranNum = rand()*99;

		return $newTime.$ranNum;

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
	 * 全モデルのアンバインド
	 *
	 * 引数にアンバインドしたくないモデルを指定出来ます
	 *
	 * @param unknown_type $bindModel
	 */
	function unbindFully($bindModel,$reset = false) {

		$bindModel = array_merge(array(
				'belongsTo'=>array(),
				'hasOne'=>array(),
				'hasMany'=>array(),
				'hasAndBelongsToMany'=>array()), $bindModel);

		$unbind = array();
		foreach ($this->belongsTo as $model=>$info) {

			if (!in_array($model,$bindModel['belongsTo'])) {
				$unbind['belongsTo'][] = $model;
			}
		}
		foreach ($this->hasOne as $model=>$info) {

			if (!in_array($model,$bindModel['hasOne'])) {
				$unbind['hasOne'][] = $model;
			}
		}
		foreach ($this->hasMany as $model=>$info) {

			if (!in_array($model,$bindModel['hasMany'])) {
				$unbind['hasMany'][] = $model;
			}
		}
		foreach ($this->hasAndBelongsToMany as $model=>$info) {

			if (!in_array($model,$bindModel['hasAndBelongsToMany'])) {
				$unbind['hasAndBelongsToMany'][] = $model;
			}
		}
		$this->unbindModel($unbind,$reset);
	}

	/**
	 * ユーザー情報を取得する関数
	 */
	protected function _getCurrentUser() {
		App::uses('AuthComponent',  'Controller/Component');
		return AuthComponent::user();
	}

	/**
	 * validationErrorの配列を結合した文字列にして返す
	 */
	public function getValidationErrorsString() {
		$value = '';
		foreach ((array)$this->validationErrors as $error) {
			if (!empty($error[0])) {
				$value .=  "<br>\n" . $error[0];
			}
		}
		return $value;
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

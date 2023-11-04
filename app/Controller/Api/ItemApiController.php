<?php
App::uses('BaseRestApiController', 'Controller');

/**
 * Class ItemApiController
 * @property Area Area
 * @property CarType CarType
 * @property Client Client
 * @property Maintenance Maintenance
 * @property OptionsManageComponent OptionsManage
 */
class ItemApiController extends BaseRestApiController {

	/**
	 * 使用Model一覧
	 * @var string[]
	 */
	public $uses = array(
		'Area',
		'CarType',
		'Client',
		'Maintenance',
	);

	/**
	 * エリア一覧取得
	 * @param int $prefecture_id
	 */
	public function area($prefecture_id) {

		// slave接続エラーでmasterに向ける
		$this->Area->setDataSource('default_slave');

		// 対象都道府県のエリア一覧を取得
		$areas = $this->Area->getAreaListByPrefectureId($prefecture_id, true);

		// レスポンスデータ生成
		foreach ($areas as $id => $name) {
			$this->responseData[] = array(
				'areaId'   => $id,
				'areaName' => $name,
			);
		}
	}

	/**
	 * 車両タイプ一覧取得
	 */
	public function carType() {

		// slave接続エラーでmasterに向ける
		$this->CarType->setDataSource('default_slave');

		// 車両タイプ一覧取得
		$car_type_list = $this->CarType->getCarTypeInfo();

		// レスポンスデータ生成
		foreach ($car_type_list as $car_type) {
			$car_type = $car_type['CarType'];
			$this->responseData[] = array(
				'carTypeId'   => $car_type['id'],
				'carTypeName' => $car_type['name'],
				'description' => $car_type['description'],
				'image'       => sprintf('/rentacar/img/car_type_%02d.png', $car_type['id']),
			);
		}
	}

	/**
	 * レンタカー事業者一覧取得
	 */
	public function client() {

		// パラメータ - クライアントID
		$client_ids = isset($this->request->query['clientIds'])
			? explode(',', trim($this->request->query['clientIds']))
			: array();

		// クライアント一覧取得
		$client_list = $this->Client->getClientListWithAreaType($client_ids);

		// レスポンスデータ生成
		foreach ($client_list as $client) {
			$client = $client['Client'];
			$this->responseData[] = array(
				'clientId'   => $client['id'],
				'clientName' => $client['name'],
			);
		}
	}

	/**
	 * 装備一覧取得
	 */
	public function equipment() {

		// コンポーネント呼び出し
		$this->loadComponent('OptionsManage');

		// 装備一覧取得
		$options = $this->OptionsManage->getOptions();

		// イーコンメンテモード時、「WEB決済可能」除去
		if ($this->Maintenance->isEconMaintenance()) {
			unset($options[99]);
		}

		// レスポンスデータ生成
		foreach ($options as $id => $name) {
			$this->responseData[] = array(
				'equipmentId'   => $id,
				'equipmentName' => $name,
				'description'   => NULL,
			);
		}
	}

}

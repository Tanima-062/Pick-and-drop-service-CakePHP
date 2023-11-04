<?php

App::uses('AppController', 'Controller');


class TopsController extends AppController {

	public $uses = array('UpdatedTable', 'Staff', 'CarClassStock', 'PriceRankCalendar', 'Reservation', 'CarClass', 'Prefecture', 'OfficeStockGroup', 'StockGroup', 'OfficeSelectionPermission', 'Message');

	public function index() {
		$staffId = $this->clientData['id'];
		$staff = $this->Staff->findById($staffId);

		$prefectureId = null;
		if (isset($this->request->query['prefecture_id']) && !empty($this->request->query['prefecture_id'])) {
			$prefectureId = $this->request->query['prefecture_id'];
		}
		$stockGroupId = null;
		if (isset($this->request->query['stock_group_id']) && !empty($this->request->query['stock_group_id'])) {
			$stockGroupId = $this->request->query['stock_group_id'];
		}
		$carTypeId = null;
		if (isset($this->request->query['car_type_id']) && !empty($this->request->query['car_type_id'])) {
			$carTypeId = $this->request->query['car_type_id'];
		}
		$carClassId = null;
		if (isset($this->request->query['car_class_id']) && !empty($this->request->query['car_class_id'])) {
			$carClassId = $this->request->query['car_class_id'];
		}

		// 権限営業所取得（ログインユーザー）
		$permissionOfficeList = empty($this->clientData['is_system_admin']) ? $this->OfficeSelectionPermission->getPermissionOfficeList($this->clientData['id']) : null;
		// 営業在庫地域の取得（権限営業所対応）
		$officeStockGroupData = $this->OfficeStockGroup->getStockGroups($permissionOfficeList);
		// 在庫地域の取得
		$stockGroupIdList = array();
		foreach ($officeStockGroupData as $k => $v) {
			$v = $v['OfficeStockGroup'];
			if ($v['client_id'] == $this->clientData['client_id']) {
				$stockGroupIdList[$k] = $v['stock_group_id'];
			}
		}
		$stockGroupIds = array_unique($stockGroupIdList);
		unset($officeStockGroupData);
		unset($stockGroupIdList);

		$stockGroupData = $this->StockGroup->getAllFindStockGroup($stockGroupIds, $prefectureId);
		$stockGroups = Hash::combine($stockGroupData, '{n}.StockGroup.id', '{n}.StockGroup.name');
		$prefectureStockGroupData = $this->StockGroup->getAllFindStockGroup($stockGroupIds);
		$prefectureIdList = Hash::extract($prefectureStockGroupData, '{n}.StockGroup.prefecture_id');
		$prefectureIds = array_unique($prefectureIdList);
		$prefectureData = $this->Prefecture->getAllFindPrefecture($prefectureIds);
		$prefectureList = Hash::combine($prefectureData, '{n}.Prefecture.id', '{n}.Prefecture.name');

		$this->UpdatedTable->recursive = -1;

		if ($staffId == 1) {
			$updateTables = $this->UpdatedTable->find('all', array('conditions' => array('client_id' => $this->clientData['client_id'], 'delete_flg <>' => 1), 'limit' => '20', 'order' => array('id' => 'desc')));
		} else {
			$conditions = array(
				'client_id' => $this->clientData['client_id'],
				'delete_flg <>' => 1
			);

			if (!$staff['Staff']['is_client_admin']){
				$conditions['staff_id'] = array($staffId);
			}

			$updateTables = $this->UpdatedTable->find('all', array('conditions' => $conditions, 'limit' => '20', 'order' => array('id' => 'desc')));
		}

		$this->set('updateTables', $updateTables);
		$this->set('staffList', $this->Staff->find('list', array('conditions' => array('delete_flg <>' => 1))));


		// 在庫切れ確認範囲
		$data['start_date'] = date('Y-m-d');
		$data['end_date'] = date('Y-m-d', strtotime("+3 month"));
		$data['client_id'] = $this->clientData['client_id'];
		$data['staff_id'] = $staffId;
		$data['is_client_admin'] = $this->clientData['is_client_admin'];
		if (!empty($prefectureId)){
			$data['prefecture_id'] = $prefectureId;
		}
		if (!empty($stockGroupId)){
			$data['stock_group_id'] = $stockGroupId;
		}
		if (!empty($carTypeId)){
			$data['car_type_id'] = $carTypeId;
		}
		if (!empty($carClassId)){
			$data['car_class_id'] = $carClassId;
		}
		if (!empty($stockGroupIds)){
			$data['stockGroupIds'] = $stockGroupIds;
		}

		$outOfStockArray = $this->CarClassStock->getOutOfStock($data);
		$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);
		$carTypeList = $this->CarClass->getCarTypeByClientID($this->clientData['Client']['id']);

		$this->set('outOfStockArray', $outOfStockArray);
		$this->set('carClassList', $carClassList);
		$this->set('carTypeList', $carTypeList);
		$this->set('prefectureList', $prefectureList);
		$this->set('stockGroups', $stockGroups);

		// 直近2週間の予約状況
		$fromDate = date('Y-m-d', strtotime('-2 week'));
		$toDate = date('Y-m-d');

		$reserveArrays = array();
		foreach (Constant::salesType() as $salesType => $typeName) {
			$reserves = $this->Reservation->getReservedOperand($fromDate, $toDate, $salesType, array(1, 2));
			$reserveArray = array('sum' => 0);
			foreach ($reserves as $reserve) {
				$reserveArray['sum'] += $reserve[0]['count'];

				$year = $reserve[0]['year'];
				$month = $reserve[0]['month'];
				$reserveArray['count'][$year][$month]['count'] = $reserve[0]['count'];
			}
			$reserveArrays[$salesType] = $reserveArray;
		}

		$this->set('reserveArrays', $reserveArrays);
		$this->set('isManagedPackage', $this->clientData['Client']['is_managed_package']);

		$this->request->data['Tops'] = $this->request->query;

		$now = date("Y-m-d H:i:s");
		$conditions = array(
			'Message.ui_client_flg' => 1,
			'Message.delete_flg' => 0,
			'Message.from_time <=' => $now,
			'Message.to_time >=' => $now
		);
		$order = array('Message.from_time DESC');
		$messages = $this->Message->find('all', array('conditions' => $conditions, 'order' => $order));

		$this->set('messages', $messages);

	}
}
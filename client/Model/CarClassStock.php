<?php
App::uses('AppModel', 'Model');
App::uses('OfficeStockGroup', 'Model');
App::uses('CarClassReservation', 'Model');

/**
 * CarClassStock Model
 *
 * @property Client $Client
 * @property StockGroup $StockGroup
 * @property CarClass $CarClass
 * @property Staff $Staff
 */
class CarClassStock extends AppModel {

	public $OfficeStockGroup;
	public $CarClassReservation;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'client_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'stock_group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'car_class_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'stock_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		//'stock_count' => array(
// 			'numeric' => array(
// 				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
// 			),
// 		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'StockGroup' => array(
			'className' => 'StockGroup',
			'foreignKey' => 'stock_group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CarClass' => array(
			'className' => 'CarClass',
			'foreignKey' => 'car_class_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);


	public function getCarClassStockSearch($data, $clientId) {

		$this->OfficeStockGroup = new OfficeStockGroup();
		$this->CarClassReservation = new CarClassReservation();

		$options = array(
			'conditions' => array(
				'client_id' => $clientId,
			),
			'order' => array(
				'stock_date' => 'ASC',
			),
			'recursive' => -1
		);

		// 在庫を取得する期間の条件をセット
		if (!empty($data['min_date']) && !empty($data['max_date'])) {
			$options['conditions']['stock_date BETWEEN ? AND ?'] = array($data['min_date'],$data['max_date']);
		} else {
			$options['conditions']['stock_date BETWEEN ? AND ?'] = array($data['year'].'-'.$data['month'].'-01',$data['year'].'-'.$data['month'].'-31');
		}

		// 在庫管理地域と車両クラスの設定
		if (!empty($data['stock_group_id'])) {
			$options['conditions']['stock_group_id'] = $data['stock_group_id'];
		} else if (!empty($data['commodity_group_id'])) {
			$stockGroups = $this->StockGroup->getStockGroupByClientId($clientId,$data['commodity_group_id']);
		} else {
			$stockGroups = $this->StockGroup->getStockGroupByClientId($clientId);
		}

		$carTypeId = '';
		if(!empty($data['car_type_id'])) {
			$carTypeId = $data['car_type_id'];
		}

		if (!empty($data['car_class_id']) && !empty($data['commodity_group_id'])) {
			$carClasses = $this->CarClass->getCarClassAndStockGroup($clientId,$data['commodity_group_id'],$data['car_class_id'],$carTypeId,$data);
		} else if (!empty($data['car_class_id'])) {
			$carClasses = $this->CarClass->getCarClassAndStockGroup($clientId,null,$data['car_class_id'],$carTypeId,$data);
		} else if (!empty($data['commodity_group_id'])) {
			$carClasses = $this->CarClass->getCarClassAndStockGroup($clientId,$data['commodity_group_id'],null,$carTypeId,$data);
		} else {
			$carClasses = $this->CarClass->getCarClassAndStockGroup($clientId,null,null,$carTypeId,$data);
		}

		// 地域指定なし
		if (isset($stockGroups)) {

			$result = array();
			$cnt = 0;
			foreach ($stockGroups as $stockGroup) {
				foreach ($carClasses as $carClass) {

					$options['conditions']['car_class_id'] = $carClass['CarClass']['id'];
					$options['conditions']['stock_group_id'] = $stockGroup['StockGroup']['id'];

					$resultTmp = $this->find('all', $options);
					if (!empty($resultTmp)) {
						$result[$cnt]['CarClassStock'] = $this->carClassStockDateCount($resultTmp);
					} else {
						$result[$cnt]['CarClassStock'] = array();
					}

					// 在庫管理地域マージ
					$result[$cnt]['StockGroup'] = $stockGroup['StockGroup'];

					// 車両クラスマージ
					$result[$cnt]['CarClass'] = $carClass['CarClass'];

					// 車両クラス対応在庫管理地域IDマージ
					$result[$cnt]['CarClassStockGroup'] = $carClass[0]['stock_group_id'];

					// 成約数マージ
					$data['car_class_id'] = $carClass['CarClass']['id'];
					$data['stock_group_id'] = $stockGroup['StockGroup']['id'];
					$result[$cnt]['CarClassReservation'] = $this->CarClassReservation->getCarClassReservationCount($data, $clientId);

					$cnt++;
				}
			}

		// 地域指定あり
		} else {

			$result = array();
			foreach ($carClasses as $key => $val) {
				$options['conditions']['car_class_id'] = $val['CarClass']['id'];

				$carsTmp = $this->find('all', $options);
				if (!empty($carsTmp)) {
					$result[$key]['CarClassStock'] = $this->carClassStockDateCount($carsTmp);
				} else {
					$result[$key]['CarClassStock'] = array();
				}

				// 在庫管理地域マージ
				$stockGroup = $this->StockGroup->getStockGroup($data['stock_group_id']);
				$result[$key]['StockGroup'] = $stockGroup['StockGroup'];

				// 車両クラスマージ
				$result[$key]['CarClass'] = $val['CarClass'];

				// 車両クラス対応在庫管理地域IDマージ
				$result[$key]['CarClassStockGroup'] = $val[0]['stock_group_id'];

				// 成約数マージ
				$data['car_class_id'] = $val['CarClass']['id'];
				$result[$key]['CarClassReservation'] = $this->CarClassReservation->getCarClassReservationCount($data, $clientId);
			}

		}

		return $result;

	}

	public function getOutOfStock($data){

		$this->CarClassReservation = new CarClassReservation();

		$startDate = $data['start_date'];
		$endDate = $data['end_date'];

		$fields = array('CarClassReservation.client_id',
						'StockGroup.prefecture_id',
						'StockGroup.id',
						'StockGroup.name',
						'CarClass.car_type_id',
						'CarClassReservation.car_class_id',
						'CarClassReservation.stock_date',
						'CarClassStock.stock_count as stocks',
						'sum(CarClassReservation.reservation_count) as reservations'
		);

		$join_conditions1 = "CarClassReservation.client_id = CarClassStock.client_id ";
		$join_conditions1 .= "AND CarClassReservation.stock_group_id = CarClassStock.stock_group_id ";
		$join_conditions1 .= "AND CarClassReservation.car_class_id = CarClassStock.car_class_id ";
		$join_conditions1 .= "AND CarClassReservation.stock_date = CarClassStock.stock_date";

		$joins = array(
		array('type'=> 'INNER',
			  'alias'=> 'CarClassStock',
			  'table'=> 'car_class_stocks',
			  'conditions'=> $join_conditions1),
		array('type'=> 'INNER',
			  'alias'=> 'StockGroup',
			  'table'=> 'stock_groups',
			  'conditions'=> "CarClassReservation.stock_group_id = StockGroup.id"),
		array('type'=> 'INNER',
			  'alias'=> 'CarClass',
			  'table'=> 'car_classes',
			  'conditions'=> "CarClassReservation.car_class_id = CarClass.id"),
		array('type'=> 'INNER',
			  'alias'=> 'CarType',
			  'table'=> 'car_types',
			  'conditions'=> "CarClass.car_type_id = CarType.id"),
		);
		$group = array('CarClassReservation.client_id',
					   'CarClassReservation.stock_group_id',
			           'CarClassReservation.car_class_id',
			           'CarClassReservation.stock_date HAVING (stocks<=reservations)'
		);

		$conditions = array(
			'CarClass.client_id' => $data['client_id'],
			'CarClass.delete_flg' => 0,
			'StockGroup.client_id' => $data['client_id'],
			'StockGroup.delete_flg' => 0,
			'CarType.delete_flg' => 0,
			'CarClassReservation.stock_date >=' => $startDate,
			'CarClassReservation.stock_date <=' => $endDate,
			'CarClassReservation.delete_flg' => 0,
			'CarClassStock.delete_flg' => 0,
			'CarClassStock.stock_count >' => 0,
		);

		if(!$data['is_client_admin']){
			$conditions[] = array('OR' => array(
					array('CarClass.scope' => 0),
					array('CarClass.scope' => $data['staff_id'])
				)
			);
		}

		if(isset($data['prefecture_id'])){
			$conditions['StockGroup.prefecture_id'] = $data['prefecture_id'];
		}

		if(isset($data['stock_group_id'])){
			$conditions['CarClassReservation.stock_group_id'] = $data['stock_group_id'];
		}

		if(isset($data['car_class_id'])){
			$conditions['CarClassReservation.car_class_id'] = $data['car_class_id'];
		}

		if(isset($data['car_type_id'])){
			$conditions['CarClass.car_type_id'] = $data['car_type_id'];
		}

		$recursive = -1;
		$order = array(
			    'StockGroup.prefecture_id' => 'ASC',
			    'StockGroup.id' => 'ASC',
				'CarClassReservation.stock_date' => 'ASC',
		);
		$options = compact('fields','joins','conditions','group','order','recursive');
		$records = $this->CarClassReservation->find('all',$options);

		$results = array();
		foreach($records as $record){
			if(isset($data['stockGroupIds'])){
				if(!in_array($record['StockGroup']['id'],$data['stockGroupIds'])){
					continue;
				}
			}
			$prefectureId=$record['StockGroup']['prefecture_id'];
			$stockGroupId=$record['StockGroup']['id'];
			$carClassId=$record['CarClassReservation']['car_class_id'];

			$key = "$prefectureId-$stockGroupId-$carClassId";

			$results[$key][] = $record;

		}

		return $results;
	}

	//在庫切れを取得
	/*public function getOutOfStock($data, $clientId) {

		$this->OfficeStockGroup = new OfficeStockGroup();
		$this->CarClassReservation = new CarClassReservation();

		$options = array(
				'conditions' => array(
						'client_id' => $clientId,
				),
				'order' => array(
						'stock_date' => 'ASC',
				),
				'recursive' => -1
		);

		$options['conditions']['stock_count <>'] = 0;

		// 在庫を取得する期間の条件をセット
		if (!empty($data['min_date']) && !empty($data['max_date'])) {
			$options['conditions']['stock_date BETWEEN ? AND ?'] = array($data['min_date'],$data['max_date']);
		} else {
			$options['conditions']['stock_date BETWEEN ? AND ?'] = array($data['year'].'-'.$data['month'].'-01',$data['year'].'-'.$data['month'].'-31');
		}

		// 在庫管理地域と車両クラスの設定
		$stockGroups = $this->StockGroup->getStockGroupByClientId($clientId);
		$carClasses = $this->CarClass->getCarClassAndStockGroup($clientId);

		//在庫地域と車両クラスごとのアラートリスト取得
		$stockAlertCountArray = $this->CarClass->getStockAlertCountList($clientId);

		// 地域・車両クラス指定なし
		if (isset($stockGroups) && isset($carClasses)) {

			$result = array();
			$cnt = 0;
			foreach ($stockGroups as $stockGroup) {
				foreach ($carClasses as $carClass) {

					$options['conditions']['car_class_id'] = $carClass['CarClass']['id'];
					$options['conditions']['stock_group_id'] = $stockGroup['StockGroup']['id'];

					$resultTmp = $this->find('all', $options);
					if (!empty($resultTmp)) {

						$result[$cnt]['CarClassStock'] = $this->carClassStockYmdCount($resultTmp);

						// 在庫管理地域マージ
						$result[$cnt]['StockGroup'] = $stockGroup['StockGroup'];

						// 車両クラスマージ
						$result[$cnt]['CarClass'] = $carClass['CarClass'];

						// 車両クラス対応在庫管理地域IDマージ
						$result[$cnt]['CarClassStockGroup']['stock_group_id'] = $carClass[0]['stock_group_id'];

						// 成約数マージ
						$data['car_class_id'] = $carClass['CarClass']['id'];
						$data['stock_group_id'] = $stockGroup['StockGroup']['id'];
						$result[$cnt]['CarClassReservation'] = $this->CarClassReservation->getCarClassReservationByDateTimeCount($data, $clientId);

					}

					$cnt++;
				}
			}
		}



		//ストックと予約数を同じ配列にまとめる
		$alertArray = array();
		foreach($result as $key => $carClass) {
			$i = 0;
			foreach($carClass['CarClassStock'] as $key2 => $carClassStock) {

				if(!empty($carClass['CarClassReservation'][$key2])) {

					$rest = $carClassStock['stock_count'] - $carClass['CarClassReservation'][$key2];

					$stockGroupId = $carClass['StockGroup']['id'];
					$carClassId = $carClass['CarClass']['id'];

					$stockGroupName = $carClass['StockGroup']['name'];
					$carClassName = $carClass['CarClass']['name'];

					$subscript = '';
					if($rest > 0) {
						 if(!empty($stockAlertCountArray[$stockGroupId][$carClassId]) && $stockAlertCountArray[$stockGroupId][$carClassId] >= $rest) {
						 	$subscript = 'alert';
						 }
					} else {
						$subscript = 'outOfStock';
					}

					if(!empty($subscript)) {
						$alertArray[$subscript][$stockGroupName][$carClassName]['stock_count'] = $carClass['CarClassStock'][$key2]['stock_count'];
						$alertArray[$subscript][$stockGroupName][$carClassName]['stock_group_name'] = $carClass['StockGroup']['name'];
						$alertArray[$subscript][$stockGroupName][$carClassName]['stock_group_id'] = $carClass['StockGroup']['id'];
						$alertArray[$subscript][$stockGroupName][$carClassName]['car_class_name'] = $carClass['CarClass']['name'];
						$alertArray[$subscript][$stockGroupName][$carClassName]['car_class_id'] = $carClass['CarClass']['id'];
						$alertArray[$subscript][$stockGroupName][$carClassName]['reserve'] = $carClass['CarClassReservation'][$key2];
						$alertArray[$subscript][$stockGroupName][$carClassName]['rest'] = $rest;
						$alertArray[$subscript][$stockGroupName][$carClassName]['day'][] = $key2;
					}
				}
			}
		}
		return $alertArray;
	}*/

	// 日付がｷｰになった在庫数が格納された配列を返す
	public function carClassStockDateCount($carClassStocks) {

		$result = array();
		foreach ($carClassStocks as $val) {
			$index = substr($val['CarClassStock']['stock_date'], 8, 2);
			$result[$index]['stock_count'] = $val['CarClassStock']['stock_count'];
			$result[$index]['suspension'] = $val['CarClassStock']['suspension'];
			$result[$index]['id'] = $val['CarClassStock']['id'];
		}

		return $result;
	}

	public function carClassStockYmdCount($carClassStocks) {

		$result = array();
		foreach ($carClassStocks as $val) {
			$index = $val['CarClassStock']['stock_date'];
			$result[$index]['stock_count'] = $val['CarClassStock']['stock_count'];
			$result[$index]['id'] = $val['CarClassStock']['id'];
		}

		return $result;

	}

	public function bulkUpdate($data) {

		$this->query("
				UPDATE
					car_class_stocks
				SET
					stock_count = ".$data['stock_count'].",
					staff_id = ".$data['staff_id'].",
					modified = NOW()
				WHERE
					id IN (".implode(',', $data['id']).")",false);
	}

	public function bulkInsert($data) {

		$this->query("
				INSERT INTO
					car_class_stocks(
						client_id,
						stock_group_id,
						car_class_id,
						stock_date,
						stock_count,
						staff_id,
						created,
						modified
					)
				VALUES
					".implode(',', $data),
		false);
	}

	public function spanStockSave($stockGroupId,$carClassId,$stockValue,$clientData,$minDate,$maxDate) {

		$carClassStockInsertStack = array();

		$this->query("
				DELETE FROM
					car_class_stocks
				WHERE
					car_class_stocks.stock_group_id = ".$stockGroupId."
					AND car_class_id = ".$carClassId."
					AND client_id = ".$clientData['client_id']."
					AND stock_date >= '".$minDate."'
					AND stock_date <= '".$maxDate."'",false);

		$minSec  = strtotime($minDate);
		$maxSec  = strtotime($maxDate);
		for ($date = $minSec; $date <= $maxSec; $date += (3600*24)) {

			$stockDate = date('Y-m-d',$date);
			//在庫が0の時、insertしない
			if($stockValue>0){
				$values = "(".$clientData['client_id'].",";
				$values .= $stockGroupId.",";
				$values .= $carClassId.",";
				$values .= "'".$stockDate."',";
				$values .= $stockValue.",";
				$values .= $clientData['id'].",";
				$values .= "now(),";
				$values .= "now())";

				array_push($carClassStockInsertStack, $values);
			}

		}
		//在庫が0の時、insertしない
		if(!empty($carClassStockInsertStack)){
			$this->query("
					INSERT INTO
						car_class_stocks(
							client_id,
							stock_group_id,
							car_class_id,
							stock_date,
							stock_count,
							staff_id,
							created,
							modified
						)
					VALUES
						".implode(',', $carClassStockInsertStack),
			false);
		}
	}


	//残数
	public function RemainingAmounSave($stockGroupId,$carClassId,$clientData,$minDate,$maxDate,$reservetionList,$stockCountVal) {


		$carClassStockInsertStack = array();

		$this->query("
				DELETE FROM
				car_class_stocks
				WHERE
				car_class_stocks.stock_group_id = ".$stockGroupId."
				AND car_class_id = ".$carClassId."
				AND client_id = ".$clientData['client_id']."
				AND stock_date >= '".$minDate."'
				AND stock_date <= '".$maxDate."'",false);

		$minSec  = strtotime($minDate);
		$maxSec  = strtotime($maxDate);
		for ($date = $minSec; $date <= $maxSec; $date += (3600*24)) {

			$stockDate = date('Y-m-d',$date);

			$stockYear = date('Y',$date);
			$stockMonth = date('m',$date);
			$stockDay = date('d',$date);

			if(!empty($reservetionList[$stockYear][$stockMonth][$stockDay][$stockGroupId][$carClassId])) {
				$reserveNum = $reservetionList[$stockYear][$stockMonth][$stockDay][$stockGroupId][$carClassId];
			} else {
				$reserveNum = 0;
			}
			$stockValue = $stockCountVal + $reserveNum;

			//在庫が0の時、insertしない
			if($stockValue > 0){
				$values = "(".$clientData['client_id'].",";
				$values .= $stockGroupId.",";
				$values .= $carClassId.",";
				$values .= "'".$stockDate."',";
				$values .= $stockValue.",";
				$values .= $clientData['id'].",";
				$values .= "now(),";
				$values .= "now())";

				array_push($carClassStockInsertStack, $values);
			}

		}

		//在庫が0の時、insertしない
		if(!empty($carClassStockInsertStack)){
			$this->query("
					INSERT INTO
					car_class_stocks(
					client_id,
					stock_group_id,
					car_class_id,
					stock_date,
					stock_count,
					staff_id,
					created,
					modified
			)
					VALUES
					".implode(',', $carClassStockInsertStack),
					false);
		}

	}

	//地域、車両別ごとの最後の在庫日時
	public function getLastCarClassStock($clientId) {

		return $this->find('all',array(
				'conditions'=>array(
						'CarClassStock.client_id'=>$clientId,
						'CarClassStock.stock_count <>'=>0,
						'CarClassStock.delete_flg'=>0,
						'StockGroup.delete_flg'=>0,
						'CarClass.delete_flg'=>0
				),
				'fields'=>array(
						'CarClassStock.*',
						'StockGroup.*',
						'CarClass.*',
						'max(CarClassStock.stock_date) as last_stock_date'
				),
				'order'=>'StockGroup.id,CarClass.sort ASC,last_stock_date desc',
				//'order'=>'CarClassStock.stock_date asc ,StockGroup.id',
				'group'=>array('stock_group_id','car_class_id')
		)
		);

	}
}

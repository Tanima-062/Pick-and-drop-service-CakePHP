<?php
App::uses('AppModel', 'Model');
/**
 * DropOffAreaRate Model
 *
 * @property RentDropOffArea $RentDropOffArea
 * @property ReturnDropOffArea $ReturnDropOffArea
 */
class DropOffAreaRate extends AppModel {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
			'price' => array(
					'notempty' => array(
							'rule' => array('notempty'),
							'message' => '乗捨料金は必須です',
							//'allowEmpty' => false,
							//'required' => false,
							//'last' => false, // Stop validation after this rule
							//'on' => 'create', // Limit validation to 'create' or 'update' operations
					),
			),
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
	);

	/**
	 *  出発日と返却地が登録されているかチェック
	 *
	 * @param numeric $rentDropOffAreaId
	 * @param numeric $returnDropOffAreaId
	 *
	 * @return boolean 登録されていない場合=> true 登録されている場合false
	 */

	public function uniqueAreaCheck($rentDropOffAreaId,$returnDropOffAreaId,$clientId,$id = null) {

		$conditions= array(
								'rent_drop_off_area_id'=>$rentDropOffAreaId,
								'return_drop_off_area_id'=>$returnDropOffAreaId,
								'client_id'=>$clientId,
								'delete_flg'=>array(0,1)
							);

		if(!empty($id)) {
			$conditions += array('id <>' => $id);
		}

		$checkData = $this->find('first',array(
											'conditions'=> $conditions,
											'recursive'=>-1
								)
		);

		if(empty($checkData)) {
			return true;
		}

		return false;
	}


	/**
	 * 編集・削除対象のデータが該当クライアントのものかチェックする
	 *
	 */
	public function clientCheck($id,$clientId) {

		$count = $this->find('count',array('conditions'=>array('id'=>$id,'client_id'=>$clientId,'delete_flg'=>array(0,1))));
		if(empty($count)) {
			return false;
		}

		return true;

	}


}

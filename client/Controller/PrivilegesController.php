<?php
App::uses('AppController', 'Controller');
/**
 * Privileges Controller
 *
 * @property Privilege $Privilege
 */
class PrivilegesController extends AppController {

	public $uses = array('Privilege', 'PrivilegePrice', 'Staff', 'CommodityPrivilege', 'Commodity', 'CommodityItem');

	public function beforeFilter() {
		parent::beforeFilter();
		$staffId = $this->clientData['id'];
		$isClientAdmin = $this->clientData['is_client_admin'];
		/**
		 * 編集・削除対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit', 'sheet_edit'),$this->action)) {
			// 編集・削除対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集・削除対象ID、クライアントID、スタッフIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->Privilege->clientCheck($this->passedArgs[0], $this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}
		$shapeList = array(
				0 => '1レンタル当たりの金額 ',
				1 => '1日当たりの金額',
		);
		$this->set(compact('shapeList'));
		$matcher = (strpos($this->action, 'sheet') !== false) ? '{n}[name=/シート$/]' : '{n}';
		$this->set('optionCategories', Hash::combine(Constant::optionCategories(), $matcher . '.id', $matcher . '.name'));
		// 公開範囲
		$scopeList = array(0 => '共通');
		if ($isClientAdmin) {
			$scopeList += $this->Staff->getStaffList($this->clientData['client_id']);
		} else {
			$scopeList[$staffId] = $this->clientData['name'];
		}
		$this->set(compact('scopeList', 'isClientAdmin', 'staffId'));
		$this->set('is_check_user', true);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		if ($this->action == 'sheet_index') {
			$optionName = 'シート';
			$addLink = 'sheet_add';
			$editLink = 'sheet_edit';
			$conditions['conditions']['Privilege.option_flg'] = 1;
		} else {
			$optionName = 'オプション';
			$addLink = 'add';
			$editLink = 'edit';
			$conditions['conditions']['Privilege.option_flg'] = 0;
		}

		$conditions['conditions']['Privilege.delete_flg'] = 0;
		$conditions['conditions']['Privilege.client_id'] = $this->clientData['client_id'];

		if (!$this->clientData['is_client_admin']) {
			$conditions['conditions']['OR'] = array(
				array('Privilege.scope' => 0),
				array('Privilege.scope' => $this->clientData['id'])
			);
		}

		$this->paginate = $conditions;

		$this->Privilege->recursive = -1;
		$privileges = $this->paginate();

		$privilegeIds = array();
		foreach ($privileges as $privilege) {
			$privilegeIds[] = $privilege['Privilege']['id'];
		}
		// クライアントごとのオプション料金を1日取得
		$privilegePriceFirstDayList = $this->PrivilegePrice->getPrivilegePriceFirstDayList($privilegeIds);

		$this->set(compact('optionName', 'addLink', 'editLink', 'privileges', 'privilegePriceFirstDayList'));
	}
	/**
	 * シート一覧
	 */
	public function sheet_index() {
		$this->index();
		$this->render('index');
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {

		$clientId = $this->clientData['client_id'];

		$staffId = $this->clientData['id'];

		if ($this->action == 'sheet_add') {
			$optionName = 'シート';
			$optionFlg = 1;
			$indexLink = 'sheet_index';
		} else {
			$optionName = 'オプション';
			$optionFlg = 0;
			$indexLink = 'index';
		}

		if ($this->request->is('post')) {
			$this->request->data['Privilege']['staff_id'] = $staffId;
			$saveFlg = true;
			//オプション料金はマイナスOK,シート料金はマイナスNG
			if($this->action == 'sheet_add' && $this->request->data['Privilege']['price'] < 0){
				$saveFlg = false;
			}
			$this->Privilege->begin();
			$this->Privilege->create();
			// オプションをセーブ
			if ($saveFlg && $this->Privilege->save($this->request->data['Privilege'])) {

				if ($this->data['Privilege']['shape_flg'] == 0) {
					// 固定料金
					$this->request->data['PrivilegePrice'][0]['price'] = $this->data['Privilege']['price'];
					$this->request->data['PrivilegePrice'][0]['span_count'] = 1;
					$this->request->data['PrivilegePrice'][0]['privilege_id'] = $this->Privilege->getLastInsertID();
					$this->request->data['PrivilegePrice'][0]['client_id'] = $this->data['Privilege']['client_id'];
					$this->request->data['PrivilegePrice'][0]['staff_id'] = $staffId;

					// オプション料金をセーブ
					if ($this->PrivilegePrice->saveMany($this->request->data['PrivilegePrice'])) {
						$saveFlg = true;
					} else {
						$saveFlg = false;
					}
				} else {
					// 変動料金
					for ($i=1; $i<=32; $i++) {
						if ($i != 32) {
							// 1~31
							$this->request->data['PrivilegePrice'][$i]['price'] = $this->data['Privilege']['price'] * $i;
							$this->request->data['PrivilegePrice'][$i]['span_count'] = $i;
						} else {
							// 以後1日
							$this->request->data['PrivilegePrice'][$i]['price'] = $this->data['Privilege']['price'];
							$this->request->data['PrivilegePrice'][$i]['span_count'] = 0;
						}
						$this->request->data['PrivilegePrice'][$i]['privilege_id'] = $this->Privilege->getLastInsertID();
						$this->request->data['PrivilegePrice'][$i]['client_id'] = $this->data['Privilege']['client_id'];
						$this->request->data['PrivilegePrice'][$i]['staff_id'] = $staffId;
					}
					// オプション料金をセーブ
					if ($this->PrivilegePrice->saveMany($this->request->data['PrivilegePrice'])) {
						$saveFlg = true;
					} else {
						$saveFlg = false;
					}
				}
			} else {
				$saveFlg = false;
			}

			// INSERT判定
			if ($saveFlg) {
				$this->Privilege->commit();
				$this->Session->setFlash('登録が正しく完了しました。', 'default', array('class'=>'alert alert-success'));
				$this->redirect(array('action' => $indexLink));
			} else {
				$this->Privilege->rollback();
				$this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。', 'default', array('class'=>'alert alert-error'));
			}

		}

		$this->set(compact('clientId', 'staffId', 'clients', 'staffs', 'optionName', 'optionFlg', 'indexLink'));
	}
	/**
	 * シート新規追加
	 */
	public function sheet_add() {
		$this->add();
		$this->render('add');
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {

		$clientId = $this->clientData['client_id'];

		$staffId = $this->clientData['id'];

		if ($this->action == 'sheet_edit') {
			$optionName = 'シート';
			$optionFlg = 1;
			$indexLink = 'sheet_index';
		} else {
			$optionName = 'オプション';
			$optionFlg = 0;
			$indexLink = 'index';
		}

		// 1日の料金を取得
		$privilegePriceFirstDay = $this->PrivilegePrice->getPrivilegePriceFirstDay($id);
		// 詳細料金を取得
		$privilegePriceData = $this->PrivilegePrice->getPrivilegePriceData($id);

		if (!$this->Privilege->exists($id)) {
			throw new NotFoundException(__('Invalid privilege'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			$saveFlg = false;
			$updateFlg = true;
			$saveDeleteFlg = false;
			$deleteFlg = false;
			$sheetMinusFlg = false;
			$optionMinusFlg = false;
			$this->Privilege->begin();

			// オプション更新データ
			$updatePrivilege['Privilege']['id'] = $this->data['Privilege']['id'];

			if ($this->clientData['is_client_admin']) {
				$updatePrivilege['Privilege']['option_category_id'] = $this->data['Privilege']['option_category_id'];
			}

			$updatePrivilege['Privilege']['name'] = $this->data['Privilege']['name'];
			$updatePrivilege['Privilege']['shape_flg'] = $this->data['Privilege']['shape_flg'];
			$updatePrivilege['Privilege']['period_flg'] = $this->data['Privilege']['period_flg'];
			$updatePrivilege['Privilege']['scope'] = $this->data['Privilege']['scope'];
			$updatePrivilege['Privilege']['maximum'] = $this->data['Privilege']['maximum'];
			$updatePrivilege['Privilege']['unit_name'] = $this->data['Privilege']['unit_name'];
			$updatePrivilege['Privilege']['staff_id'] = $staffId;
			$updatePrivilege['Privilege']['delete_flg'] = $this->data['Privilege']['delete_flg'];

			// 削除フラグ
			if ($this->data['Privilege']['delete_flg'] == 1) {
				$updatePrivilege['Privilege']['deleted'] = date("Y-m-d H:i:s", time());
				$deleteFlg = true;
			}

			//シート料金はマイナスNG,オプション料金は合算してマイナスだとNG
			if ($this->action == 'sheet_edit'){
				if($this->data['Privilege']['price'] < 0) {
					$sheetMinusFlg = true;
				}
			//オプション料金更新時にチェック
			}else{
				//料金にマイナスを設定した時だけチェック
				if($this->data['Privilege']['price'] < 0){
					$optionMinusFlg = $this->__checkMinusPrice($id, $this->data['Privilege']['price'], $this->data['Privilege']['maximum']);
				}
			}

			// 料金形態を変更したら過去料金設定を物理削除
			if (($this->data['Privilege']['shape_flg'] != $this->data['Privilege']['default_shape_flg'] || !empty($deleteFlg)) && !$sheetMinusFlg && !$optionMinusFlg) {
				$updateFlg = false;
				$deletePrivilegePrice['PrivilegePrice.privilege_id'] = $id;
				$this->PrivilegePrice->recursive = -1;
				if ($this->PrivilegePrice->deleteAll($deletePrivilegePrice, false)) {
					$saveDeleteFlg = true;
				} else {
					$saveDeleteFlg = false;
				}
			} else {
				$saveDeleteFlg = true;
			}

			// オプション料金更新データ
			if (empty($deleteFlg)) {
				if ($this->data['Privilege']['shape_flg'] == 1) {
					// 変動料金
					if ($updateFlg) {
						// 料金データ更新
						foreach ($privilegePriceData as $key => $privilegePrice) {
							$savePrivilegePrice[$key]['id'] = $privilegePrice['PrivilegePrice']['id'];
							$savePrivilegePrice[$key]['staff_id'] = $staffId;
							if ($privilegePrice['PrivilegePrice']['span_count'] != 0) {
								// 乗算
								$savePrivilegePrice[$key]['price'] = $this->data['Privilege']['price'] * $privilegePrice['PrivilegePrice']['span_count'];
							} else {
								$savePrivilegePrice[$key]['price'] = $this->data['Privilege']['price'];
							}
						}
					} else {
						// 料金データ新規追加
						for ($i=1; $i<=32; $i++) {
							if ($i != 32) {
								// 1~31
								$savePrivilegePrice[$i]['price'] = $this->data['Privilege']['price'] * $i;
								$savePrivilegePrice[$i]['span_count'] = $i;
							} else {
								// 以後1日
								$savePrivilegePrice[$i]['price'] = $this->data['Privilege']['price'];
								$savePrivilegePrice[$i]['span_count'] = 0;
							}
							$savePrivilegePrice[$i]['privilege_id'] = $this->data['Privilege']['id'];
							$savePrivilegePrice[$i]['client_id'] = $this->data['Privilege']['client_id'];
							$savePrivilegePrice[$i]['staff_id'] = $staffId;
						}
					}
				} else {
					// 固定料金
					if ($updateFlg) {
						// 料金データ更新
						foreach ($privilegePriceData as $key => $privilegePrice) {
							$savePrivilegePrice[$key]['id'] = $privilegePrice['PrivilegePrice']['id'];
							$savePrivilegePrice[$key]['price'] = $this->data['Privilege']['price'];
							$savePrivilegePrice[$key]['staff_id'] = $staffId;
						}
					} else {
						// 料金データ新規追加
						$savePrivilegePrice[0]['price'] = $this->data['Privilege']['price'];
						$savePrivilegePrice[0]['span_count'] = 1;
						$savePrivilegePrice[0]['privilege_id'] = $this->data['Privilege']['id'];
						$savePrivilegePrice[0]['client_id'] = $this->data['Privilege']['client_id'];
						$savePrivilegePrice[0]['staff_id'] = $staffId;
						$savePrivilegePrice[0]['shape_flg'] = $this->data['Privilege']['shape_flg'];
					}
				}
			}

			if(!$sheetMinusFlg && !$optionMinusFlg){
				// オプションをセーブ
				if ($this->Privilege->save($updatePrivilege)) {
					if (!$deleteFlg) {
						$this->PrivilegePrice->create();
						// オプション料金をセーブ
						if ($this->PrivilegePrice->saveMany($savePrivilegePrice)) {
							$saveFlg = true;
						} else {
							$saveFlg = false;
						}
					} else {
						$saveFlg = true;
					}
				} else {
					$saveFlg = false;
				}
			}

			// INSERT判定
			if ($saveFlg && $saveDeleteFlg) {
				$this->Privilege->commit();
				$this->Session->setFlash('登録が正しく完了しました。', 'default', array('class'=>'alert alert-success'));
				$this->redirect(array('action' => $indexLink));
			} elseif ($optionMinusFlg) {
				$this->Session->setFlash('料金設定がマイナスになる可能性があります、各項目を見直して下さい。', 'default', array('class'=>'alert alert-error'));
			} else {
				$this->Privilege->rollback();
				$this->Session->setFlash('入力に失敗しました、各項目を見直して下さい。', 'default', array('class'=>'alert alert-error'));
			}


		} else {
			$options = array('conditions' => array('Privilege.' . $this->Privilege->primaryKey => $id));
			$this->request->data = $this->Privilege->find('first', $options);
		}

		$this->set(compact('clientId', 'clients', 'staffId', 'staffs', 'privilegePriceFirstDay', 'privilegePriceData', 'optionName', 'optionFlg'));
	}
	public function sheet_edit($id = null) {
		$this->edit($id);
		$this->render('edit');
	}

	// 詳細設定
	public function detail_edit($id) {

		$privilegeData = $this->Privilege->getPrivilegeFirstData($id);

		// 詳細料金を取得
		$priceData = $this->PrivilegePrice->getPrivilegePriceData($id);

		$this->set(compact('priceData'));

		if ($this->request->is('post')) {
			//シートはマイナス設定がNGなので先にチェック
			if ($privilegeData['Privilege']['option_flg']) {
				foreach ($this->data['PrivilegePrice'] as $k => $v) {
					if($v['price'] < 0){
						$this->Session->setFlash(__('保存に失敗しました'));
						$this->redirect('sheet_edit/'.$id);
					}
				}
			}
			//1日の料金をもとに料金がマイナスになるかチェックする
			//span_countがpostで連携されないため詳細料金から1日のidを抽出しておく
			$firstData = Hash::extract($priceData, '{n}.PrivilegePrice[span_count=1]');
			$firstId = $firstData[0]['id'];
			$checkPrice = $this->data['PrivilegePrice'][$firstId]['price'];
			if($checkPrice < 0){
				$optionMinusFlg = $this->__checkMinusPrice($id, $checkPrice, $privilegeData['Privilege']['maximum']);
				if($optionMinusFlg){
					//料金がマイナスになる可能性がある場合NG
					$this->Session->setFlash(__('料金設定がマイナスになる可能性があります、各項目を見直して下さい。'));
					$this->redirect('edit/'.$id);
				}
			}
			//正常保存処理
			foreach ($this->data['PrivilegePrice'] as $k => $v) {
				$this->data['PrivilegePrice'][$k]['staff_id'] = $this->clientData['id'];
			}
			$this->PrivilegePrice->begin();
			if ($this->PrivilegePrice->saveMany($this->data['PrivilegePrice'])) {
				$this->PrivilegePrice->commit();
				$this->Session->setFlash(__('保存に成功しました'));
			} else {
				$this->PrivilegePrice->rollback();
				$this->Session->setFlash(__('保存に失敗しました'));
			}
			if ($privilegeData['Privilege']['option_flg']) {
				$this->redirect('sheet_edit/'.$id);
			} else {
				$this->redirect('edit/'.$id);
			}

		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Privilege->id = $id;
		if (!$this->Privilege->exists()) {
			throw new NotFoundException(__('Invalid privilege'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Privilege->delete()) {
			$this->Session->setFlash(__('Privilege deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Privilege was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * minuscheck method
 *
 * @param string $id オプションID
 * @param int $price オプション料金
 * @param int $maximum オプション最大設定値
 * @return boolean
 */
	private function __checkMinusPrice($id, $price, $maximum) {
		$optionMinusFlg = false;
		//修正したオプションを設定している商品を抽出
		$options = array(
			'fields' => array(
				'CommodityPrivilege.commodity_id'
			),
			'conditions' => array(
				'CommodityPrivilege.privilege_id' => $id,
				'CommodityPrivilege.delete_flg' => 0,
			),
			'recursive' => -1
		);
		$commodityIds = Hash::extract($this->CommodityPrivilege->find('all', $options),'{n}.CommodityPrivilege.commodity_id');
		//どの商品にも紐づいてないならマイナスチェックをしない
		if(!empty($commodityIds)){
			foreach($commodityIds as $ck => $cv){
				//商品IDごとに編集したオプションの金額を保持
				$commodityOptionPrice[$cv] = $price * $maximum;
			}
			//抽出した商品に紐づいている他のマイナス金額のオプションを抽出
			$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($commodityIds, $id);
			//各商品の金額を出し、マイナスオプションを合算してマイナスになるか判定
			if(!empty($commodityPrivilegeData)){
				//商品IDごとにマイナスオプションを合算する
				foreach($commodityPrivilegeData as $ck => $cv){
					$commodityId = $cv['CommodityPrivilege']['commodity_id'];
					$commodityOptionPrice[$commodityId] += $cv['PrivilegePrice']['price'] * $cv['Privilege']['maximum'];
				}
			}
			//各商品ごとに1日分の基本料金+免責補償料金を出す
			//基本料金
			$commodityItemPrice = $this->Commodity->getPriceDataMulti(array_keys($commodityOptionPrice));
			//免責補償料金
			$disclaimerCompensationPriceArr = $this->CommodityItem->getDisclaimerCompensationPrice(array_keys($commodityOptionPrice));
			if(is_array($disclaimerCompensationPriceArr)){
				$disclaimerCompensationPrice = Hash::combine($disclaimerCompensationPriceArr, '{n}.CommodityItem.commodity_id', '{n}.0.price');
			}
			//商品ごとにマイナスチェック
			foreach($commodityItemPrice as $ck => $cv){
				$price = $cv + $disclaimerCompensationPrice[$ck] + $commodityOptionPrice[$ck];
				if($price < 0){
					//料金がマイナスになる可能性がある場合NG
					$optionMinusFlg = true;
				}
			}
		}
		return $optionMinusFlg;

	}
}

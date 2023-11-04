<?php

App::uses('AppController', 'Controller');

/**
 * Commodities Controller
 *
 * @property Commodity $Commodity
 * @property Area $Area
 * @property Prefecture $Prefecture
 */
class CommoditiesController extends AppController {

	public $uses = array(
		'Commodity',
		'CarClass',
		'Office',
		'Equipment',
		'Privilege',
		'ChildSheet',
		'CommodityItem',
		'CommodityPrice',
		'CommodityImage',
		'CommodityEquipment',
		'CommodityPrivilege',
		'StockGroup',
		'CommodityGroup',
		'Area',
		'Prefecture',
		'CarModel',
		'SippCodeLetter',
		'DisclaimerCompensation',
		'Campaign',
		'CampaignTerm',
		'CommodityCampaignPrice',
		'AgentOrganizedPrice'
	);
	public $components = array(
		'SippCode'
	);
	// 喫煙・禁煙
	public $smokingCarList = array(
		0 => '禁煙車',
		1 => '喫煙車',
		2 => '指定なし',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		set_time_limit(500);
		$this->set('is_check_user', true);
	}

	/**
	 * commodity method
	 *
	 * @return void
	 */
	public function index() {

		//不要なjoinを解除
		$this->Commodity->recursive = 0;
		$this->Commodity->unbindModel(array('belongsTo' => array('Staff', 'Language')));
		$this->Commodity->unbindModel(array('hasMany' => array(
				'CommodityEquipment',
				'CommodityImage',
				'CommodityItem',
				'CommodityPrivilege',
				'CommodityRentOffice',
				'CommodityReturnOffice',
				'CommodityTerm',
				'CommodityPrice',
				)
			)
		);

		//getで来た場合 検索フォームの値を保持
		if ($this->request->is('get')) {
			if (!empty($this->request->params['named'])) {
				$this->request->data['Commodities'] = array_diff_key($this->request->params['named'], array('sort' => 0, 'direction' => 0, 'page' => 0));
			} else {
				$this->request->data['Commodities'] = $this->request->query;
			}
		}

		$sql = $this->Commodity->getCommonditiesConditions2($this->request->data['Commodities'], $this->clientData['client_id']);

		$this->paginate = $sql;

		$this->set('count', $this->Commodity->getCommonditiesCount($sql));

		if ($this->paginate) {
			$commodities = $this->paginate();
			
			$commodityImages = $this->CommodityImage->getFirstImageByCommodityIds(Hash::extract($commodities, '{n}.Commodity.id'));

			$commodity = array();
			for ($i = 0; $i < count($commodities); $i++) {
				$index = $commodities[$i]['Commodity']['id'];
				$commodity[$index]['CommodityItem']['id'] = $commodities[$i]['CommodityItem']['id'];
				$commodity[$index]['CommodityItem']['sipp_code'] = $commodities[$i]['CommodityItem']['sipp_code'];
				$commodity[$index]['CommodityTerm']['available_from'] = $commodities[$i]['CommodityTerm']['available_from'];
				$commodity[$index]['CommodityTerm']['available_to'] = $commodities[$i]['CommodityTerm']['available_to'];
				$commodity[$index]['Commodity']['id'] = $commodities[$i]['Commodity']['id'];
				$commodity[$index]['Commodity']['name'] = $commodities[$i]['Commodity']['name'];
				$commodity[$index]['Commodity']['public_request'] = $commodities[$i]['Commodity']['public_request'];
				$commodity[$index]['Commodity']['is_published'] = $commodities[$i]['Commodity']['is_published'];
				$commodity[$index]['Commodity']['image_relative_url'] = $commodities[$i]['Commodity']['image_relative_url'];
				$commodity[$index]['Commodity']['day_time_flg'] = $commodities[$i]['Commodity']['day_time_flg'];
				$commodity[$index]['StockGroup']['name'] = $commodities[$i]['StockGroup']['name'];
				$commodity[$index]['CommodityImage']['image_relative_url'] = $commodityImages[$commodities[$i]['Commodity']['id']];
				$commodity[$index]['Commodity']['sales_type'] = $commodities[$i]['Commodity']['sales_type'];
			}

			//車両クラス取得
			foreach ($commodity as $key => $val) {
				$i = 0;
				$isAgentOrganizedCommodity = Constant::isAgentOrganizedCommodity(
					$this->clientData['Client']['is_managed_package'],
					$val['Commodity']['sales_type']
				);
				$prices = $this->getPrices($isAgentOrganizedCommodity, $val['Commodity']['id']);
				$commodity[$key]['CarClasses'][$i] = array('id' => '', 'name' => '');
				foreach ($prices as $price) {
					$commodity[$key]['CarClasses'][$i]['id'] = $price['CarClasses']['id'];
					$commodity[$key]['CarClasses'][$i]['name'] = $price['CarClasses']['name'];
					$commodity[$key]['CarClasses'][$i]['priceSystem'] = $this->getPriceSystemTemplate(
						(int)$price['Commodity']['day_time_flg'],
						$isAgentOrganizedCommodity
					);
					$i++;
				}
			}

			$publish = array('published' => 0, 'non_published' => 0);
			foreach ($commodity as $commodityVal) {
				if ($commodityVal['Commodity']['is_published']) {
					$publish['published'] ++;
				} else {
					$publish['non_published'] ++;
				}
			}

			// 料金取得
			foreach ($commodity as $commodityId => $value) {
				if ($value['Commodity']['day_time_flg'] == 1) {
					// 24H料金
					$spanCount = 24;
				} else {
					// 日帰り料金
					$spanCount = 1;
				}
				$spanPrice = $this->CommodityPrice->getSpanCountPrice($this->clientData['client_id'], $spanCount, $value['CommodityItem']['id']);
				$commodity[$commodityId]['CommodityPrice'] = $spanPrice['CommodityPrice'];
			}

			// 募集型料金の期間取得
			$aoPrices = $this->AgentOrganizedPrice->find('all', array(
				'fields' => array(
					'AgentOrganizedPrice.id',
					'AgentOrganizedPrice.commodity_item_id',
					'AgentOrganizedPrice.start_date',
					'AgentOrganizedPrice.end_date'
				),
				'conditions' => array(
					'AgentOrganizedPrice.commodity_item_id' => Hash::extract($commodities, '{n}.CommodityItem.id'),
					'AgentOrganizedPrice.delete_flg' => 0
				),
				'order' => 'AgentOrganizedPrice.id',
				'recursive' => -1
			));
			$aoTerms = Hash::combine($aoPrices, '{n}.AgentOrganizedPrice.id', '{n}.AgentOrganizedPrice', '{n}.AgentOrganizedPrice.commodity_item_id');

			$this->set('publish', $publish);
			$this->set('commodities', $commodity);
			$this->set('aoTerms', $aoTerms);
		}

		//フォームオプションをセット
		$this->__setViewVars($this->clientData['client_id']);
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {

		$this->set('deadLine', array(1 => '受付締切時間を設定する', 0 => '受付締切日時を設定する'));
		if ($this->request->is('get')) {
			//初期値

			$this->request->data['CommodityTerm']['available_from']['year'] = date('Y');
			$this->request->data['CommodityTerm']['available_from']['month'] = date('m');
			$this->request->data['CommodityTerm']['available_from']['day'] = date('d');
			$this->request->data['CommodityTerm']['available_from']['hour'] = "00";
			$this->request->data['CommodityTerm']['available_from']['min'] = "00";


			$this->request->data['CommodityTerm']['available_to']['year'] = date('Y');
			$this->request->data['CommodityTerm']['available_to']['month'] = date('m');
			$this->request->data['CommodityTerm']['available_to']['day'] = date('d');
			$this->request->data['CommodityTerm']['available_to']['hour'] = "00";
			$this->request->data['CommodityTerm']['available_to']['min'] = "00";

			$this->request->data['CommodityTerm']['consider_opening_hours'] = 0;
		} else if ($this->request->is('post')) {
			$salesType = (!empty($this->request->data['Commodity']['sales_type']))?$this->request->data['Commodity']['sales_type']:'ARRANGED';

			// オプションとチャイルドシートの構成を統合
			if (!empty($this->data['CommodityPrivilege']['sheet_privilege_id'])) {
				if (empty($this->data['CommodityPrivilege']['privilege_id'])) {
					$this->request->data['CommodityPrivilege']['privilege_id'] = $this->data['CommodityPrivilege']['sheet_privilege_id'];
				} else {
					$this->request->data['CommodityPrivilege']['privilege_id'] = array_merge($this->data['CommodityPrivilege']['privilege_id'], $this->data['CommodityPrivilege']['sheet_privilege_id']);
				}
			}

			try {
				$this->Commodity->begin();

				$error = $this->__checkDedlineDate($this->request->data['CommodityTerm']);

				if (!empty($error)) {
					throw new Exception($error);
				}

				if ($salesType == 'ARRANGED') {
					$error = $this->__checkInsertImages($this->request->data['CommodityImage']);
	
					if (!empty($error)) {
						throw new Exception($error);
					}
				}else {
					unset($this->Commodity->validate["description"]["notempty"]);
					// 募集型ON×WEB事前決済不可のクライアントでも募集型商品を作成できるようにする(CAR-341)
					if ($this->clientData['Client']['accept_prepay'] == 0) {
						$this->request->data['Commodity']['payment_method'] = 1;
					}
				}

				$isAgentOrganizedCommodity = Constant::isAgentOrganizedCommodity(
					$this->clientData['Client']['is_managed_package'],
					$this->request->data['Commodity']['sales_type']
				);
				// 販売方法で募集型企画を選択した場合、設定に制限あり
				if ($isAgentOrganizedCommodity) {
					$error = $this->checkAgentOrganizedSettings();
					if (!empty($error)) {
						throw new Exception($error);
					}
				}

				$commodityData = $this->request->data['Commodity'];

				$this->Commodity->create();
				if (!$this->Commodity->saveMethod($commodityData, $this->clientData)) {
					throw new Exception($this->Commodity->getValidationErrorsString());
				}

				//最後に挿入されたデータ
				$id = $this->Commodity->getLastInsertID();

				//関連テーブルへのデータの挿入
				if (!$this->Commodity->relativeModelSaveMethod($id, $this->request->data, $this->clientData)) {
					throw new Exception($this->Commodity->getValidationErrorsString());
				}
				$priceSystem = $this->getPriceSystemTemplate((int)$this->request->data['Commodity']['day_time_flg'], $isAgentOrganizedCommodity);
				if ($priceSystem === '') {
					throw new Exception('料金形態の値が不正です。');
				}
				$this->Commodity->commit();

				$this->Session->setFlash('商品を登録しました。', 'default', array('class' => 'alert alert-success'));
				// 価格設定に飛ばす
				$this->redirect(array('action' => $priceSystem, $id));
			} catch (Exception $e) {
				$this->Commodity->rollback();
				$this->Session->setFlash('商品の登録に失敗しました。' . $e->getMessage(), 'default', array('class' => 'alert alert-error'));
			}
		}

		if (empty($this->request->data['radioHoursOrDays'])) {
			$this->request->data['radioHoursOrDays'] = 0;
		}
		$this->set('hoursOrDays', $this->request->data['radioHoursOrDays']);

		$this->__setViewVarsAdd($this->clientData['client_id']);
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {

		$referer = $this->referer();
		if (preg_match('/client\/commodities\?current_client_id=\d+&name/', $referer)) {
			$this->Session->delete('clientReferer');
			$this->Session->write('clientReferer', $referer);
		} elseif (preg_match('/client\/commodities\/index\/name/i', $referer)) {
			$this->Session->delete('clientReferer');
			$this->Session->write('clientReferer', $referer);
		}

		$this->set('deadLine', array(1 => '受付締切時間を設定する', 0 => '受付締切日時を設定する'));

		//IDがなかったらindexにリダイレクト
		$this->Commodity->id = $id;
		if (!$this->Commodity->exists()) {
			$this->redirect(array('action' => 'index'));
		}

		//編集不可だったらログアウト
		$isEditable = $this->Commodity->isEditableByThisStaff($id, $this->clientData['client_id']);
		if (!$isEditable) {
			$this->redirect(array("controller" => "users", "action" => "logout"));
		}

		$oldCommodity = $this->formatEditData($this->Commodity->read(null, $id));

		//POSTされたとき
		if ($this->request->is('post') || $this->request->is('put')) {
			$salesType = (!empty($oldCommodity['Commodity']['sales_type']))?$oldCommodity['Commodity']['sales_type']:'ARRANGED';

			// オプションとチャイルドシートの構成を統合
			if (!empty($this->data['CommodityPrivilege']['sheet_privilege_id'])) {
				if (empty($this->data['CommodityPrivilege']['privilege_id'])) {
					$this->request->data['CommodityPrivilege']['privilege_id'] = $this->data['CommodityPrivilege']['sheet_privilege_id'];
				} else {
					$this->request->data['CommodityPrivilege']['privilege_id'] = array_merge($this->data['CommodityPrivilege']['privilege_id'], $this->data['CommodityPrivilege']['sheet_privilege_id']);
				}
			}

			try {

				//オプションが設定されていたらprivilege_idから金額と最大個数を取得
				//マイナスを抽出して基本金額,免責補償料金と合算する
				if(!empty($this->request->data['CommodityPrivilege']['privilege_id'])){
					//設定しているオプションの金額抽出
					$privilegePriceData = $this->Privilege->getPrivilegeMaxPriceData($this->request->data['CommodityPrivilege']['privilege_id']);
					if(!empty($privilegePriceData)){
						$option_price = 0;
						foreach($privilegePriceData as $pk => $pv){
							if($pv['PrivilegePrice']['price'] < 0){
								$option_price += $pv['PrivilegePrice']['price'] * $pv['Privilege']['maximum'];
							}
						}

						//基本料金
						$commodityItemPrice = $this->Commodity->getPriceDataMulti($oldCommodity['Commodity']['id']);
						//免責補償料金
						$disclaimerCompensationPriceArr = $this->CommodityItem->getDisclaimerCompensationPrice($oldCommodity['Commodity']['id']);
						if(is_array($disclaimerCompensationPriceArr)){
							$disclaimerCompensationPrice = $disclaimerCompensationPriceArr[0][0]['price'];
						}else{
							$disclaimerCompensationPrice = 0;
						}

						$price = $commodityItemPrice[$oldCommodity['Commodity']['id']] + $disclaimerCompensationPrice;

						//料金とマイナスオプションを合算してマイナスになったらNG
						$check_price = $price + $option_price;
						if($check_price < 0){
							throw new Exception('オプションの設定により料金設定がマイナスになる可能性があります。');
						}
					}
				}
				
				$error = $this->__checkDedlineDate($this->request->data['CommodityTerm']);

				if (!empty($error)) {
					throw new Exception($error);
				}

				if ($salesType == 'ARRANGED') {
					$error = $this->__checkUpdateImages($this->request->data['CommodityImage'],$oldCommodity['CommodityImage']); 

					if (!empty($error)) {
						throw new Exception($error);
					}
				}else {
					unset($this->Commodity->validate["description"]["notempty"]);
					// 募集型ON×WEB事前決済不可のクライアントでも募集型商品を作成できるようにする(CAR-341)
					if ($this->clientData['Client']['accept_prepay'] == 0) {
						$this->request->data['Commodity']['payment_method'] = 1;
					}
				}
				
				$isAgentOrganizedCommodity = Constant::isAgentOrganizedCommodity(
					$this->clientData['Client']['is_managed_package'],
					$oldCommodity['Commodity']['sales_type']
				);
				// 販売方法で募集型企画を選択した場合、設定に制限あり
				if ($isAgentOrganizedCommodity) {
					$error = $this->checkAgentOrganizedSettings();
					if (!empty($error)) {
						throw new Exception($error);
					}
				}

				$commodityData = $this->request->data['Commodity'];

				$this->Commodity->create();
				if (!$this->Commodity->saveMethod($commodityData, $this->clientData)) {
					throw new Exception($this->Commodity->getValidationErrorsString());
				}
				//関連テーブルへのデータの挿入
				if (!$this->Commodity->relativeModelSaveMethod($id, $this->request->data, $this->clientData)) {
					throw new Exception($this->Commodity->getValidationErrorsString());
				}

				$this->Session->setFlash(__('データを編集しました。'));

				//indexに飛ばす
				$redirectUrl = $this->Session->read('clientReferer');
				if (!empty($redirectUrl)) {
					$this->redirect($redirectUrl);
				} else {
					$this->redirect(array('action' => 'index'));
				}
			} catch (Exception $e) {
				$this->Session->setFlash('商品の更新に失敗しました。' . $e->getMessage(), 'default', array('class' => 'alert alert-error'));
				$this->request->data['CommodityImage'] = $oldCommodity['CommodityImage'];
			}

			$commodityData = $this->formatEditData($this->Commodity->read(null, $id));
			$this->request->data['Commodity']['day_time_flg'] = $commodityData['Commodity']['day_time_flg'];
			$this->request->data['Commodity']['sales_type'] = $commodityData['Commodity']['sales_type'];
		} else {
			$this->request->data = $oldCommodity;
		}

		$isAgentOrganizedCommodity = Constant::isAgentOrganizedCommodity(
			$this->clientData['Client']['is_managed_package'],
			$this->request->data['Commodity']['sales_type']
		);

		//商品IDに紐づく車両クラスと値段の組み合わせを取得
		$registCarClass = $this->getPrices($isAgentOrganizedCommodity, $id);
		$this->set('registCarClass', $registCarClass);

		if (empty($this->request->data['radioHoursOrDays'])) {
			if (isset($this->request->data['CommodityTerm']['deadline_days']) && !empty($this->request->data['CommodityTerm']['deadline_time'])) {
				$this->request->data['radioHoursOrDays'] = 1;
			} else {
				$this->request->data['radioHoursOrDays'] = 0;
			}
		}
		$this->set('hoursOrDays', $this->request->data['radioHoursOrDays']);

		// 料金設定画面
		$template = $this->getPriceSystemTemplate((int)$this->request->data['Commodity']['day_time_flg'], $isAgentOrganizedCommodity);
		$this->set('template', $template);

		//商品ID
		$this->set('id', $id);
		//スタッフID
		$this->set('staffId', $this->clientData['id']);

		$this->__setViewVarsAdd($this->clientData['client_id']);
	}

	/**
	 * copy method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function copy($id) {

		$this->set('deadLine', array(1 => '受付締切時間を設定する', 0 => '受付締切日時を設定する'));

		//IDがなかったらindexにリダイレクト
		$this->Commodity->id = $id;
		if (!$this->Commodity->exists()) {
			$this->redirect(array('action' => 'index'));
		}

		//編集不可だったらログアウト
		$isEditable = $this->Commodity->isEditableByThisStaff($id, $this->clientData['client_id']);
		if (!$isEditable) {
			$this->redirect(array("controller" => "users", "action" => "logout"));
		}

		$oldCommodity = $this->formatEditData($this->Commodity->read(null, $id));

		//POSTされたとき
		if ($this->request->is('post') || $this->request->is('put')) {
			$salesType = (!empty($this->request->data['Commodity']['sales_type']))?$this->request->data['Commodity']['sales_type']:'ARRANGED';
			
			// オプションとチャイルドシートの構成を統合
			if (!empty($this->data['CommodityPrivilege']['sheet_privilege_id'])) {
				if (empty($this->data['CommodityPrivilege']['privilege_id'])) {
					$this->request->data['CommodityPrivilege']['privilege_id'] = $this->data['CommodityPrivilege']['sheet_privilege_id'];
				} else {
					$this->request->data['CommodityPrivilege']['privilege_id'] = array_merge($this->data['CommodityPrivilege']['privilege_id'], $this->data['CommodityPrivilege']['sheet_privilege_id']);
				}
			}

			try {
				$this->Commodity->begin();

				$error = $this->__checkDedlineDate($this->request->data['CommodityTerm']);

				if (!empty($error)) {
					throw new Exception($error);
				}

				if ($salesType == 'ARRANGED') {
					$error = $this->__checkCopyImages($this->request->data['CommodityImage']); 

					if (!empty($error)) {
						throw new Exception($error);
					}
				}else {
					unset($this->Commodity->validate["description"]["notempty"]);
					// 募集型ON×WEB事前決済不可のクライアントでも募集型商品を作成できるようにする(CAR-341)
					if ($this->clientData['Client']['accept_prepay'] == 0) {
						$this->request->data['Commodity']['payment_method'] = 1;
					}
				}

				$isAgentOrganizedCommodity = Constant::isAgentOrganizedCommodity(
					$this->clientData['Client']['is_managed_package'],
					$this->request->data['Commodity']['sales_type']
				);
				// 販売方法で募集型企画を選択した場合、設定に制限あり
				if ($isAgentOrganizedCommodity) {
					$error = $this->checkAgentOrganizedSettings();
					if (!empty($error)) {
						throw new Exception($error);
					}
				}

				$commodityData = $this->data['Commodity'];

				// 通常のコピーなら画像を削除
				if (isset($this->data['nomal'])) {
					$commodityData['image_relative_url'] = null;

					foreach ($this->data['CommodityImage'] as $key => $val) {
						$this->request->data['CommodityImage'][$key]['default_image'] = null;

						if (!strcmp($val['default_remark'], $val['remark'])) {
							$this->request->data['CommodityImage'][$key]['remark'] = null;
							$this->request->data['CommodityImage'][$key]['default_remark'] = null;
						}
					}
				}

				$this->Commodity->create();
				if (!$this->Commodity->saveMethod($commodityData, $this->clientData)) {
					throw new Exception($this->Commodity->getValidationErrorsString());
				}

				//最後に挿入されたデータ
				$addId = $this->Commodity->getLastInsertID();

				//関連テーブルへのデータの挿入
				if (!$this->Commodity->relativeModelSaveMethod($addId, $this->data, $this->clientData)) {
					throw new Exception($this->Commodity->getValidationErrorsString());
				}

				$priceSystem = $this->getPriceSystemTemplate((int)$this->request->data['Commodity']['day_time_flg'], $isAgentOrganizedCommodity);
				if ($priceSystem === '') {
					throw new Exception('料金形態の値が不正です。');
				}
				$this->Session->setFlash(__('商品をコピーしました。'), 'default', array('class' => 'alert alert-success'));
				$this->Commodity->commit();
				// 価格設定に飛ばす
				$this->redirect(array('action' => $priceSystem, $addId));
			} catch (Exception $e) {
				$this->Session->setFlash('商品のコピーに失敗しました。' . $e->getMessage(), '', array('class' => 'alert alert-error'), 'auth');
				$this->Commodity->rollback();
				$this->request->data['Client'] = $oldCommodity['Client'];
				$this->request->data['CommodityImage'] = $oldCommodity['CommodityImage'];
				$this->request->data = $this->__idDelete($this->request->data);
			}
		} else {
			$data = $oldCommodity;
			$this->request->data = $this->__idDelete($data);
			$this->request->data['Commodity']['name'] = "【コピー】" . $this->request->data['Commodity']['name'];
		}

		//商品IDに紐づく車両クラスと値段の組み合わせを取得
		$this->Commodity->recursive = 0;
		$registCarClass = $this->Commodity->getPlace($id, $this->clientData['client_id']);
		$this->set('registCarClass', $registCarClass);

		if (empty($this->request->data['radioHoursOrDays'])) {
			if (isset($this->request->data['CommodityTerm']['deadline_days']) && !empty($this->request->data['CommodityTerm']['deadline_time'])) {
				$this->request->data['radioHoursOrDays'] = 1;
			} else {
				$this->request->data['radioHoursOrDays'] = 0;
			}
		}
		$this->set('hoursOrDays', $this->request->data['radioHoursOrDays']);

		//スタッフID
		$this->set('staffId', $this->clientData['id']);

		$this->__setViewVarsAdd($this->clientData['client_id']);
	}

	/**
	 * 画像を削除するページ
	 */
	public function del_img($imgId) {

		if (!empty($imgId)) {

			$this->CommodityImage->recursive = -1;
			$count = $this->CommodityImage->find('count', array('conditions' => array('id' => $imgId, 'client_id' => $this->clientData['client_id'])));
			if ($count == 0) {
				$this->redirect(array("controller" => "Users", "action" => "logout"));
			}

			$this->CommodityImage->unbindModel(array('belongsTo' => array('Commodity')));
			$this->CommodityImage->deleteAll(array('CommodityImage.id' => $imgId), false);

			$this->redirect($this->referer());
		} else {
			$this->redirect(array("controller" => "Users", "action" => "logout"));
		}
	}

	/**
	 * 商品を削除
	 */
	public function commodityDelete() {
		$data = $this->data;
		$data['Commodity']['staff_id'] = $this->clientData['id'];
		$data['Commodity']['delete_flg'] = 1;
		$data['Commodity']['deleted'] = date("Y-m-d H:i:s", time());

		$this->Commodity->save($data);

		$this->redirect(array('controller' => 'Commodities', 'action' => 'index'));
	}

	/**
	 * 暦日制料金設定
	 * @param unknown $commodityId
	 * @param unknown $carClassId
	 */
	public function daySystem($commodityId, $carClassId = null) {

		// 共通処理
		$this->__commonProcessing($commodityId, $carClassId);

		// 暦日制配列
		$commodityScheduleArray = array(1, 2, 3, 4, 5, 0);

		$this->set(compact('commodityScheduleArray', 'commodityId'));
	}

	/**
	 * 時間制料金設定
	 * @param unknown $commodityId
	 * @param unknown $carClassId
	 */
	public function timeSystem($commodityId, $carClassId = null) {

		// 共通処理
		$this->__commonProcessing($commodityId, $carClassId);

		// 詳細時間設定リンク
		if (!empty($carClassId)) {
			$detailLink = $commodityId . '/' . $carClassId;
		} else {
			$detailLink = $commodityId . '/';
		}

		// 時間制配列
		$commodityScheduleArray = array(6, 12, 24, 0, 25);

		// 詳細時間制配列
		for ($i = 1; $i <= 24; $i++) {
			$detailCommodityScheduleArray[] = $i;
		}
		array_push($detailCommodityScheduleArray, 0, 25);

		$this->set(compact('commodityScheduleArray', 'detailCommodityScheduleArray', 'detailLink', 'commodityId'));
	}

	/**
	 * 詳細時間制料金設定
	 * @param unknown $commodityId
	 * @param unknown $carClassId
	 */
	public function detailTimeSystem($commodityId, $carClassId = null) {

		// 共通処理
		$this->__commonProcessing($commodityId, $carClassId);

		// 詳細時間制配列
		for ($i = 1; $i <= 24; $i++) {
			$detailCommodityScheduleArray[] = $i;
		}
		array_push($detailCommodityScheduleArray, 0, 25);

		$this->set(compact('detailCommodityScheduleArray'));
	}

	/**
	 * 詳細時間制料金設定（キャンペーン）
	 * @param unknown $commodityId
	 * @param unknown $carClassId
	 */
	public function detailTimeSystemCampaign($commodityId, $carClassId = null, $campaignId = null) {

		$this->detailTimeSystem($commodityId, $carClassId);
		$this->set('campaignId', $campaignId);
	}

	public function preview($id = null, $carClassId) {
		// クライアント画面のレイアウトオフ
		$this->autoLayout = false;

		// 編集不可だったらログアウト
		$isEditable = $this->Commodity->isEditableByThisStaff($id, $this->clientData['client_id']);
		if (!$isEditable) {
			$this->redirect(array("controller" => "users", "action" => "logout"));
		}

		// 商品アイテムデータ取得
		$commodityItem = $this->CommodityItem->getCommodityPreview($id, $carClassId);
		// 商品アイテム料金取得
		$commodityItemPrice = $this->CommodityItem->getCommodityItemPriceData($commodityItem['CommodityItem']['id']);
		$commodityInfo = array_merge($commodityItem, $commodityItemPrice);
		// 免責補償料金取得
		$disclaimerCompensationOption = array(
			'car_class_id' => $commodityItemPrice['CarClass']['id'],
			'from' => date('Y-m-d'),
			'to' => date('Y-m-d'),
		);
		$disclaimerCompensation = $this->DisclaimerCompensation->getDisclaimerCompensation($disclaimerCompensationOption);

		if ($commodityInfo['Commodity']['day_time_flg'] == 1) {
			$rentalPeriod = '6時間';
			$price = $commodityItemPrice['CommodityPrice'][6]['price'];
		} else {
			$rentalPeriod = '日帰り';
			$price = $commodityItemPrice['CommodityPrice'][1]['price'];
		}
		// 免責補償料金
		$disclaimerCompensationPrice = $disclaimerCompensation['price'] * 1;
		$basicCharge = $price + $disclaimerCompensationPrice;

		// 装備セット
		$equipmentList = $this->Equipment->getEquipment();
		$commodityEquipment = $this->CommodityEquipment->getEquipmentData($commodityInfo['Commodity']['id']);

		$smokingCarList = $this->smokingCarList;

		$this->set(compact('commodityInfo', 'rentalPeriod', 'basicCharge', 'equipmentList', 'commodityEquipment', 'smokingCarList'));
	}

	/**
	 * 募集型企画商品 暦日制料金設定
	 * 
	 * 初回登録時: /client/commodities/packageSystem/{commodity_id}
	 * 編集時:    /client/commodities/packageSystem/{commodity_id}/{car_class_id}
	 *
	 * @param string $commodityId
	 * @param string $carClassId
	 * @return void
	 */
	public function packageSystem($commodityId, $carClassId = null)
	{
		/* 編集権限チェック */
		// ログインユーザー
		$this->set('loginUserId', $this->clientData['id']);
		// 編集不可だったらログアウト
		$isEditable = $this->Commodity->isEditableByThisStaff($commodityId, $this->clientData['client_id']);
		if (!$isEditable) {
			$this->redirect(array("controller" => "users", "action" => "logout"));
		}
		$this->set('isSystemAdmin', $this->clientData['is_system_admin']);

		$commodityItemId = (int)$this->CommodityItem->field(
			'id',
			[
				'commodity_id' => $commodityId,
				'car_class_id' => $carClassId,
				'delete_flg'   => 0
			]
		);
		/* 保存処理 */
		if (
			isset($this->request->data['saveAll']) ||
			isset($this->request->data['saveCarInfo']) ||
			isset($this->request->data['savePriceInfo'])
		) {
			$this->savePackageSystem($commodityId, $this->request->data, $commodityItemId);
		}

		/* 表示用データ取得 */
		$this->setPackageSystemViewVars($commodityId, $carClassId, $commodityItemId);

	}

	/**
	 * 価格設定一括削除
	 *
	 * @param string $commodityItemId
	 * @return void
	 */
	public function deleteAll($commodityItemId)
	{
		if ((int)$commodityItemId === 0) {
			$this->redirect(array('controller' => 'Users', 'action' => 'logout'));
		}

		$salesType = $this->Commodity->getSalesTypeByCommodityItemId($commodityItemId);
		$isAgentOrganizedCommodity = Constant::isAgentOrganizedCommodity(
			$this->clientData['Client']['is_managed_package'],
			$salesType
		);
		if ($isAgentOrganizedCommodity) {
			$redirectUrl = $this->deleteAgentOrganizedCommodityItem($commodityItemId);
			$this->redirect($redirectUrl);
		} else {
			$this->del_place($commodityItemId);
		}
	}

	/**
	 * 共通処理
	 * @param unknown $commodityId
	 */
	private function __commonProcessing($commodityId, $carClassId) {
		// ログインユーザー
		$this->set('loginUserId', $this->clientData['id']);

		// 編集不可だったらログアウト
		$isEditable = $this->Commodity->isEditableByThisStaff($commodityId, $this->clientData['client_id']);
		if (!$isEditable) {
			$this->redirect(array("controller" => "users", "action" => "logout"));
		}

		$this->set('isSystemAdmin', $this->clientData['is_system_admin']);

		// 設定保存ボタンが押された時
		if (isset($this->data['save']) || isset($this->data['save2']) || isset($this->data['save3']) || isset($this->data['save4'])) {

			$data = $this->request->data;

			$saveData['commodity_id'] = $commodityId;
			if (!empty($data['CommodityItem']['car_class_id'])) {
				$saveData['car_class_id'] = $data['CommodityItem']['car_class_id'];
			} else if (!empty($carClassId)) {
				$saveData['car_class_id'] = $carClassId;
			}

			if (!empty($data['CommodityItem']['car_model_id'])) {
				$saveData['car_model_id'] = $data['CommodityItem']['car_model_id'];
			} else {
				$saveData['car_model_id'] = null;
			}

			// デフォルト値
			$saveData['id'] = $data['CommodityItem']['id'];
			$saveData['client_id'] = $this->clientData['client_id'];
			$saveData['staff_id'] = $this->clientData['id'];

			$checkCommodityItem = $this->CommodityItem->find('first', array(
				'conditions' => array(
					'CommodityItem.client_id' => $saveData['client_id'],
					'CommodityItem.commodity_id' => $saveData['commodity_id'],
					'CommodityItem.delete_flg' => 0,
				),
				'recursive' => -1,
			));
			if (!empty($checkCommodityItem)) {
				if (empty($saveData['id'])) {
					$saveData['id'] = $checkCommodityItem['CommodityItem']['id'];
				}
				// 車両クラス・車種に変更がある時はSIPPコードをリセットする
				if ($saveData['car_class_id'] != $checkCommodityItem['CommodityItem']['car_class_id'] ||
						$saveData['car_model_id'] != $checkCommodityItem['CommodityItem']['car_model_id']) {
					$saveData['sipp_code'] = null;
				}
			}

			$saveFlg = true;

			// 車両情報のみ保存 or 全て保存
			if (isset($this->data['save']) || isset($this->data['save2'])) {
				// 管理者のみSIPPコードが必要
				$correctSippCode = true;
				if ($this->clientData['is_system_admin'] && $this->action != 'detailTimeSystem') {
					$correctSippCode = $this->SippCode->validate(
						$data['CommodityItem']['sipp_code'], $saveData['commodity_id'], $saveData['car_class_id'], $saveData['car_model_id']
					);
					if ($correctSippCode) {
						$saveData['sipp_code'] = implode($data['CommodityItem']['sipp_code']);
					} else {
						$this->Session->setFlash('SIPPコードを選択してください。', 'default', array('class' => 'alert alert-error'));
					}
				}
				if ($correctSippCode) {
					// 商品アイテムマスタにデータ登録
					if (!$this->CommodityItem->save($saveData)) {
						$this->Session->setFlash($this->CommodityItem->getValidationErrorsString(), 'default', array('class' => 'alert alert-error'));
						$saveFlg = false;
					} else if (isset($this->data['save2'])) {
						// 車両情報のみ保存ボタンが押された場合
						$this->Session->setFlash('車両情報を設定しました。', 'default', array('class' => 'alert alert-success'));
						$this->redirect(array('action' => $this->action, $commodityId, $saveData['car_class_id']));
					}
				} else {
					$saveFlg = false;
				}
			}
			// 基本料金のみ保存 or 全て保存
			if ($saveFlg && (isset($this->data['save']) || isset($this->data['save3']))) {
				// 基本料金を登録
				if (empty($saveData['id'])) {
					// 最後に挿入されたデータを取得
					$commodityItemId = $this->CommodityItem->getLastInsertID();
				} else {
					$commodityItemId = $saveData['id'];
				}
				$commodityPriceSaveFlg = false;

				// 入力値のチェック
				if (!empty($this->data['system']) && $this->data['system'] == 'timeSystem') {
					// 時間制入力チェック
					$checkArray = $this->__checkTimeEmptyArray($data['CommodityPrice']);
					if (!empty($checkArray)) {
						$data['CommodityPrice'] += array_fill(1,5,$data['CommodityPrice'][6]);
						$data['CommodityPrice'] += array_fill(7,5,$data['CommodityPrice'][12]);
						$data['CommodityPrice'] += array_fill(13,11,$data['CommodityPrice'][24]);
						//超過時間計算金額チェック
						$tmpReferencePrice = $data['CommodityPrice'][6]['commodity_price']; //基準料金
						$tmpOverRagePrice = $data['CommodityPrice'][25]['commodity_price']; //1時間超過料金
						$tmpOverTime = 1; //超過時間
						for($i = 7; $i <= 23; $i++){
							if($i == 12) {
								$tmpReferencePrice = $data['CommodityPrice'][$i]['commodity_price'];
								$tmpOverTime = 1;
							}else{
								$tmpNewPrice = $tmpReferencePrice + ($tmpOverTime * $tmpOverRagePrice);
								$data['CommodityPrice'][$i]['commodity_price'] = ( $tmpNewPrice < $data['CommodityPrice'][$i]['commodity_price'] ) ? $tmpNewPrice : $data['CommodityPrice'][$i]['commodity_price'];
								$tmpOverTime++;
							}
						}
					}
				} else {
					// 入力チェック
					$checkArray = $this->__checkEmptyAllArray($data['CommodityPrice']);
				}

				if (!empty($checkArray)) {
					$i = 0;
					foreach ($data['CommodityPrice'] as $spanCount => $val) {
						$priceCheck = true;
						$commodityPriceSaveFlg = false;

						$price = $val['commodity_price'];

						$commodityItemSaveData['CommodityPrice'][$i]['commodity_item_id'] = $commodityItemId;
						$commodityItemSaveData['CommodityPrice'][$i]['client_id'] = $this->clientData['client_id'];
						$commodityItemSaveData['CommodityPrice'][$i]['span_count'] = $spanCount;

						$uniqCommodityPriceCheck = $this->CommodityPrice->find('first', array('conditions' => $commodityItemSaveData['CommodityPrice'][$i], 'recursive' => -1));
						if (!empty($uniqCommodityPriceCheck)) {
							$commodityItemSaveData['CommodityPrice'][$i]['id'] = $uniqCommodityPriceCheck['CommodityPrice']['id'];
						}
						if (empty($price)) {
							$commodityItemSaveData['CommodityPrice'][$i]['price'] = 0;
						} else {
							$commodityItemSaveData['CommodityPrice'][$i]['price'] = $price;
						}
						$commodityItemSaveData['CommodityPrice'][$i]['staff_id'] = $this->clientData['id'];
						$i++;
						if (!$priceCheck) {
							break;
						} else {
							$commodityPriceSaveFlg = true;
						}
					}
				}

				//オプションが設定されていたら料金がマイナスになるかチェックする
				//これ以前にNGだとチェックもしない
				if($commodityPriceSaveFlg){
					$minusCheck = true;
					//マイナス料金を抽出して合算する
					$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($commodityId);
					$option_price = 0;
					if(!empty($commodityPrivilegeData)){
						foreach($commodityPrivilegeData as $ck => $cv){
							//最大個数設定された時にマイナスにならないようチェックするためmaximumを乗算する
							$option_price += $cv['PrivilegePrice']['price'] * $cv['Privilege']['maximum'];
						}
					}
					//免責補償料金
					$disclaimerCompensationPriceArr = $this->CommodityItem->getDisclaimerCompensationPrice($commodityId);
					if(is_array($disclaimerCompensationPriceArr)){
						$disclaimerCompensationPrice = $disclaimerCompensationPriceArr[0][0]['price'];
					}else{
						$disclaimerCompensationPrice = 0;
					}
					//時間か暦日か判定し6時間/日帰りの料金と合算
					if((!empty($this->data['system']) && $this->data['system'] == 'timeSystem') || $this->action == 'detailTimeSystem'){
						$check_price = $data['CommodityPrice'][6]['commodity_price'] + $disclaimerCompensationPrice + $option_price;
					}else{
						$check_price = $data['CommodityPrice'][1]['commodity_price'] + $disclaimerCompensationPrice + $option_price;
					}
					//料金がマイナスになった場合NG
					if($check_price < 0){
						$commodityPriceSaveFlg = false;
						$minusCheck = false;
					}
				}

				// 商品料金マスタにデータ登録
				if ($commodityPriceSaveFlg) {
					// すでに料金設定がされているかチェック
					if ($this->CommodityPrice->saveAll($commodityItemSaveData['CommodityPrice'])) {
						$this->CommodityPrice->checkHavePrice($commodityItemId);
						if (isset($this->data['save3'])) {
							// 更新の場合担当者へメール送信
							$this->Session->setFlash('基本料金を設定しました。', 'default', array('class' => 'alert alert-success'));
							$this->redirect(array('action' => $this->action, $commodityId, $saveData['car_class_id']));
						} else if ($this->action == 'detailTimeSystem') {
							$this->Session->setFlash('基本料金を設定しました。', 'default', array('class' => 'alert alert-success'));
							$this->redirect(array('action' => 'timeSystem', $commodityId, $saveData['car_class_id']));
						}
					} else {
						$this->Session->setFlash(__('料金が空の項目があります。'), 'default', array('class' => 'alert alert-error'));
						$saveFlg = false;
					}
				} else if (!isset($priceCheck) || !$priceCheck) {
					$this->Session->setFlash(__('『料金設定が間違っているか、空欄の状態』となっております。</br>&nbsp;再度、料金をご確認ください。'), 'default', array('class' => 'alert alert-error'));
					$saveFlg = false;
				} else if (!isset($minusCheck) || !$minusCheck) {
					$this->Session->setFlash(__('オプションの設定により料金設定がマイナスになる可能性があります。</br>&nbsp;再度、料金をご確認ください。'), 'default', array('class' => 'alert alert-error'));
					$saveFlg = false;
				} else {
					$this->Session->setFlash(__('料金が空の項目があります。'), 'default', array('class' => 'alert alert-error'));
					$saveFlg = false;
				}
			}
			// 全て保存
			if ($saveFlg && (isset($this->data['save']) || isset($this->data['save4']))) {
				if (!empty($data['CommodityCampaignPrice'])) {
					// キャンペーン料金を登録
					if (empty($saveData['id'])) {
						// 最後に挿入されたデータを取得
						$commodityItemId = $this->CommodityItem->getLastInsertID();
					} else {
						$commodityItemId = $saveData['id'];
					}

					$dbUpdateDate = $this->CommodityCampaignPrice->getUpdateDate($commodityItemId);
					if (!empty($dbUpdateDate)) {
						if (strtotime($dbUpdateDate) > strtotime($data['display_time'])) {
							$this->Session->setFlash(__('他の担当者により更新されていたため、キャンペーン料金は登録されませんでした。'), 'default', array('class' => 'alert alert-error'));
							$saveFlg = false;
						}
					}

					if ($saveFlg) {
						foreach ($data['CommodityCampaignPrice'] as $campaignId => $campaignPrice) {
							// 入力値のチェック
							if (!empty($this->data['system']) && $this->data['system'] == 'timeSystem') {
								// 時間制入力チェック
								$isNotEmpty = $this->__checkTimeSetArray($campaignPrice);
								if ($isNotEmpty) {
									$data['CommodityCampaignPrice'][$campaignId] += array_fill(1, 5, $data['CommodityCampaignPrice'][$campaignId][6]);
									$data['CommodityCampaignPrice'][$campaignId] += array_fill(7, 5, $data['CommodityCampaignPrice'][$campaignId][12]);
									$data['CommodityCampaignPrice'][$campaignId] += array_fill(13, 11, $data['CommodityCampaignPrice'][$campaignId][24]);
									// 超過時間計算金額チェック
									$tmpReferencePrice = $data['CommodityCampaignPrice'][$campaignId][6]['commodity_price']; //基準料金
									$tmpOverRagePrice = $data['CommodityCampaignPrice'][$campaignId][25]['commodity_price']; //1時間超過料金
									$tmpOverTime = 1; //超過時間
									for($i = 7; $i <= 23; $i++){
										if($i == 12) {
											$tmpReferencePrice = $data['CommodityCampaignPrice'][$campaignId][$i]['commodity_price'];
											$tmpOverTime = 1;
										}else{
											$tmpNewPrice = $tmpReferencePrice + ($tmpOverTime * $tmpOverRagePrice);
											$data['CommodityCampaignPrice'][$campaignId][$i]['commodity_price'] = ( $tmpNewPrice < $data['CommodityCampaignPrice'][$campaignId][$i]['commodity_price'] ) ? $tmpNewPrice : $data['CommodityCampaignPrice'][$campaignId][$i]['commodity_price'];
											$tmpOverTime++;
										}
									}
								}
							} else {
								// 入力チェック
								$isNotEmpty = $this->__checkSetAllArray($campaignPrice);
							}
						}

						if ($isNotEmpty) {
							$dbCampaignPrice = $this->Campaign->getCommodityCampaignPrice($commodityItemId);
							$i = 0;
							foreach ($data['CommodityCampaignPrice'] as $campaignId => $campaignPrice) {
								$dbDeleteFlg = 0;
								$dbSpanToId = array();
								$dbSpanToPrice = array();
								if (!empty($dbCampaignPrice[$campaignId])) {
									$dbDeleteFlg = current($dbCampaignPrice[$campaignId])['delete_flg'];
									$dbSpanToId = Hash::combine($dbCampaignPrice[$campaignId], '{n}.span_count', '{n}.id');
									$dbSpanToPrice = Hash::combine($dbCampaignPrice[$campaignId], '{n}.span_count', '{n}.price');
								}
								$deleteFlg = !empty($campaignPrice['delete_flg']) ? $campaignPrice['delete_flg'] : 0;
								unset($campaignPrice['delete_flg']);

								foreach ($campaignPrice as $spanCount => $val) {
									$dbPrice = !empty($dbSpanToPrice[$spanCount]) ? $dbSpanToPrice[$spanCount] : -1;
									if ($dbDeleteFlg != $deleteFlg || $dbPrice != $val['commodity_price']) {
										if (!empty($dbSpanToId)) {
											$campaignPriceSaveData['CommodityCampaignPrice'][$i]['id'] = $dbSpanToId[$spanCount];
										}
										$campaignPriceSaveData['CommodityCampaignPrice'][$i]['client_id'] = $this->clientData['client_id'];
										$campaignPriceSaveData['CommodityCampaignPrice'][$i]['campaign_id'] = $campaignId;
										$campaignPriceSaveData['CommodityCampaignPrice'][$i]['span_count'] = $spanCount;
										$campaignPriceSaveData['CommodityCampaignPrice'][$i]['price'] = $val['commodity_price'];
										$campaignPriceSaveData['CommodityCampaignPrice'][$i]['commodity_item_id'] = $commodityItemId;
										$campaignPriceSaveData['CommodityCampaignPrice'][$i]['staff_id'] = $this->clientData['id'];
										$campaignPriceSaveData['CommodityCampaignPrice'][$i]['delete_flg'] = $deleteFlg;
										if ($deleteFlg == 1 && $dbDeleteFlg == 0) {
											$campaignPriceSaveData['CommodityCampaignPrice'][$i]['deleted'] = date('Y-m-d H:i:s');
										}
										$i++;
									}
								}
							}
						}

						//オプションが設定されていたら料金がマイナスになるかチェックする
						$minusCheck = true;
						//これ以前にNGだとチェックもしない
						if(!empty($campaignPriceSaveData)){
							//既に取得していたら飛ばす
							if(empty($commodityPrivilegeData)){
								//マイナス料金を抽出して合算する
								$commodityPrivilegeData = $this->CommodityPrivilege->getCommodityPrivilegeData($commodityId);
							}
							$option_price = 0;
							if(!empty($commodityPrivilegeData)){
								foreach($commodityPrivilegeData as $cpk => $cpv){
									//最大個数設定された時にマイナスにならないようチェックするためmaximumを乗算する
									$option_price += $cpv['PrivilegePrice']['price'] * $cpv['Privilege']['maximum'];
								}
							}
							//免責補償料金
							$disclaimerCompensationPriceArr = $this->CommodityItem->getDisclaimerCompensationPrice($commodityId);
							if(is_array($disclaimerCompensationPriceArr)){
								$disclaimerCompensationPrice = $disclaimerCompensationPriceArr[0][0]['price'];
							}else{
								$disclaimerCompensationPrice = 0;
							}
							foreach($campaignPriceSaveData['CommodityCampaignPrice'] as $ck => $cv){
								//時間(詳細設定)か暦日か判定し6時間/日帰りの料金と合算
								if((!empty($this->data['system']) && $this->data['system'] == 'timeSystem') || isset($this->data['save4'])){
									if($cv['span_count'] != '6'){
										continue;
									}
									$check_price = $cv['price'] + $disclaimerCompensationPrice + $option_price;
								}else{
									if($cv['span_count'] != '1'){
										continue;
									}
									$check_price = $cv['price'] + $disclaimerCompensationPrice + $option_price;
								}
								//料金がマイナスになった場合NG
								if($check_price < 0){
									//saveall回避のため保存用データ削除
									$campaignPriceSaveData = array();
									$minusCheck = false;
								}
							}
						}

						if (!empty($campaignPriceSaveData)) {
							// キャンペーン料金マスタにデータ登録
							if (!$this->CommodityCampaignPrice->saveAll($campaignPriceSaveData['CommodityCampaignPrice'])) {
								$this->Session->setFlash(__('キャンペーン料金に0円を登録する場合は全ての項目を0円にしてください。'), 'default', array('class' => 'alert alert-error'));
								$saveFlg = false;
							}
						} else if (!$isNotEmpty) {
							$this->Session->setFlash(__('キャンペーン料金に0円を登録する場合は全ての項目を0円にしてください。'), 'default', array('class' => 'alert alert-error'));
							$saveFlg = false;
						} else if (!$minusCheck) {
							$this->Session->setFlash(__('オプションの設定により料金設定がマイナスになる可能性があります。'), 'default', array('class' => 'alert alert-error'));
							$saveFlg = false;
						}
					}
				}

				if ($saveFlg) {
					$this->Session->setFlash('車両情報・料金を設定しました。', 'default', array('class' => 'alert alert-success'));
					if (isset($this->data['save4'])) {
						$this->redirect(array('action' => 'timeSystem', $commodityId, $saveData['car_class_id']));
					} else {
						$this->redirect(array('action' => $this->action, $commodityId, $saveData['car_class_id']));
					}
				}
			}
		}

		// 車両クラス
		$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);

		if (!empty($carClassId)) {

			$this->Commodity->recursive = -1;
			$place = $this->Commodity->getPlace($commodityId, $this->clientData['client_id'], $carClassId);
			foreach ($place as $key => $val) {
				if (!isset($data['CommodityItem'])) {
					$data['CommodityItem']['id'] = $val['CommodityItem']['id'];
					$data['CommodityItem']['car_class_id'] = $val['CommodityItem']['car_class_id'];
					$data['CommodityItem']['car_model_id'] = $val['CommodityItem']['car_model_id'];

					if (!empty($val['CommodityItem']['sipp_code'])) {
						$data['CommodityItem']['sipp_code'] = str_split($val['CommodityItem']['sipp_code']);
					}
				}
				$priceSpanId = $val['CommodityPrice']['span_count'];
				$data['CommodityPrice'][$priceSpanId]['id'] = $val['CommodityPrice']['id'];
				$data['CommodityPrice'][$priceSpanId]['commodity_price'] = $val['CommodityPrice']['price'];
				$data['CommodityPrice'][$priceSpanId]['delete_flg'] = $val['CommodityPrice']['delete_flg'];
				$data['CommodityPriceDeleteFlg']['delete_flg'] = $val['CommodityPrice']['delete_flg'];
			}
			if (!empty($data['CommodityItem']['id'])) {
				$campaignPrice = $this->Campaign->getCommodityCampaignPrice($data['CommodityItem']['id']);
				if (!empty($campaignPrice)) {
					$this->set('campaignIds', array_keys($campaignPrice));
					foreach ($campaignPrice as $campaignId => $prices) {
						// 公開/非公開切り替えはキャンペーン単位
						$data['CommodityCampaignPrice'][$campaignId]['delete_flg'] = current($prices)['delete_flg'];
						foreach ($prices as $price) {
							$data['CommodityCampaignPrice'][$campaignId][$price['span_count']] = array(
								'commodity_price' => $price['price'],
							);
						}
					}
				}
			}
			$this->request->data = $data;
			$this->set('CommodityItemId', $data['CommodityItem']['id']);

			$carModelList = $this->CarModel->getCarModelListByCarClassId($carClassId);

			// マスタと異なるパラメータの車両クラスでアクセスされた場合に検知する
			if (!isset($this->data['save']) && !isset($this->data['save2']) && (!isset($this->data['save4'])) && $carClassId != $data['CommodityItem']['car_class_id']) {
				$this->Session->setFlash('車両クラスの登録が変更されています。最新の画面からアクセスしてください。', 'default', array('class' => 'alert alert-error'));
				throw new NotFoundException();
			}

			$carModelId = $data['CommodityItem']['car_model_id'];

			// 画面表示中にキャンペーン更新されたかチェック
			$this->set('DisplayTime', $this->CommodityCampaignPrice->getUpdateDate($data['CommodityItem']['id']));

			// 公開範囲外のキャンペーンの期間もチェック
			$campaignOutOfScopeList = $this->Campaign->getCommodityCampaignIdOutOfScope($data['CommodityItem']['id']);
		} else {
			$carClassId = key($carClassList);
			$carModelList = $this->CarModel->getCarModelListByCarClassId($carClassId);
			$carModelId = key($carModelList);
		}

		// SIPPコード
		$sippCodeList = $this->SippCode->getSippCodeList($commodityId, $carClassId, $carModelId);

		// キャンペーン
		$campaignList = $this->Campaign->getCampaignList($this->clientData['client_id']);
		$allCampaignList = isset($campaignOutOfScopeList) ? array_merge(array_keys($campaignList), $campaignOutOfScopeList) : array_keys($campaignList);
		$campaignTermList = $this->CampaignTerm->getTermsByCampaignIds($allCampaignList);
		$campaignTermJson = json_encode($campaignTermList);
		$campaignOutOfScopeJson = json_encode(isset($campaignOutOfScopeList) ? $campaignOutOfScopeList : array());

		// 料金入力オプション
		$priceInputType = array(
			0 => '基本料金基準',
			1 => 'カスタム料金入力'
		);
		$priceCalcUnit = array(
			0 => '円',
			1 => '％'
		);
		$priceCalcType = array(
			0 => '加算',
			1 => '減算'
		);

		// キャンペーン未登録のとき、更新チェック用の日時＝現在日時
		if (!isset($this->viewVars['DisplayTime'])) {
			$this->set('DisplayTime', date('Y-m-d H:i:s'));
		}

		$this->set(compact(
			'carModelList', 'carClassList', 'sippCodeList', 'campaignList', 'campaignTermList', 'campaignTermJson', 'campaignOutOfScopeJson', 'priceInputType', 'priceCalcUnit', 'priceCalcType'
		));
	}

	/**
	 * 配列全てに料金が入力されているか
	 * @param unknown $array
	 */
	private function __checkEmptyAllArray($array) {
		foreach ($array as $key => $value) {
			if ($key == 'delete_flg') {
				continue;
			}
			if (!isset($value['commodity_price']) || empty($value['commodity_price'])) {
				return false;
			}
		}
		return true;
	}

	private function __checkSetAllArray($array) {
		$sum = 0;
		$containsZero = false;
		foreach ($array as $key => $value) {
			if ($key == 'delete_flg') {
				continue;
			}
			if (!isset($value['commodity_price'])) {
				return false;
			}
			if (empty($value['commodity_price'])) {
				$containsZero = true;
			}
			$sum += $value['commodity_price'];
		}
		if ($containsZero && $sum > 0) {
			return false;
		}
		return true;
	}

	/**
	 * 必須項目の料金が設定されているか
	 * 6時間(6)・12時間(12)・24時間(24)・以後1日(0)・超過1時間(25)
	 * @param unknown $array
	 */
	private function __checkTimeEmptyArray($array) {
		if (!empty($array[6]['commodity_price']) && !empty($array[12]['commodity_price']) && !empty($array[24]['commodity_price']) && !empty($array[0]['commodity_price']) && !empty($array[25]['commodity_price'])) {
			return true;
		} else {
			return false;
		}
	}

	private function __checkTimeSetArray($array) {
		if (!isset($array[6]['commodity_price']) || !isset($array[12]['commodity_price']) || !isset($array[24]['commodity_price']) || !isset($array[0]['commodity_price']) || !isset($array[25]['commodity_price'])) {
			return false;
		}
		$sum = 0;
		$containsZero = false;
		foreach ($array as $key => $value) {
			if ($key == 'delete_flg') {
				continue;
			}
			if (!isset($value['commodity_price'])) {
				return false;
			}
			if (empty($value['commodity_price'])) {
				$containsZero = true;
			}
			$sum += $value['commodity_price'];
		}
		if ($containsZero && $sum > 0) {
			return false;
		}
		return true;
	}

	// 手配旅行の価格設定一括削除処理
	public function del_place($commondityItemId) {

		if (!empty($commondityItemId)) {

			$this->CommodityPrice->recursive = -1;
			$count = $this->CommodityPrice->find('count', array(
				'conditions' => array(
					'commodity_item_id' => $commondityItemId,
					'client_id' => $this->clientData['client_id']
				)
			));
			if ($count == 0) {
				$this->redirect(array("controller" => "Users", "action" => "logout"));
			}

			$this->CommodityItem->recursive = -1;
			$this->CommodityItem->unbindModel(array('belongsTo' => array('Client', 'Commodity', 'CarClass', 'Staff')));

			$save['id'] = $commondityItemId;
			$save['staff_id'] = $this->clientData['id'];
			$save['delete_flg'] = 1;
			$save['deleted'] = date("Y-m-d H:i:s", time());

			$this->CommodityItem->save($save);

			$this->CommodityPrice->recursive = -1;
			$this->CommodityPrice->unbindModel(array('belongsTo' => array('Commodity', 'PriceRank', 'PriceSpan', 'CommodityItem', 'Staff')));
			$this->CommodityPrice->updateAll(
				array(
					'staff_id' => $this->clientData['id'],
					'delete_flg' => 1,
					'deleted' => date("'Y-m-d H:i:s'", time()),
				), array(
					'commodity_item_id' => $commondityItemId,
					'client_id' => $this->clientData['client_id'],
				)
			);

			$this->CommodityCampaignPrice->recursive = -1;
			$this->CommodityCampaignPrice->updateAll(
				array(
					'staff_id' => $this->clientData['id'],
					'delete_flg' => 1,
					'deleted' => date("'Y-m-d H:i:s'", time()),
				), array(
					'commodity_item_id' => $commondityItemId,
				)
			);

			$this->redirect(array("action" => "index"));
		} else {
			$this->redirect(array("controller" => "Users", "action" => "logout"));
		}
	}

	/**
	 * 募集型企画商品の価格設定一括削除処理
	 *
	 * @param stirng $commodityItemId
	 * @return string|array
	 * @throws Exception
	 */
	private function deleteAgentOrganizedCommodityItem($commodityItemId)
	{
		$count = $this->AgentOrganizedPrice->find('count', [
			'conditions' => [
				'commodity_item_id' => $commodityItemId,
				'client_id' => $this->clientData['client_id'],
				'delete_flg' => 0
			]
		]);
		if ($count === 0) {
			return ['controller' => 'Users', 'action' => 'logout'];
		}

		try {
			$this->CommodityItem->begin();

			// 商品アイテム情報削除
			$this->CommodityItem->recursive = -1;
			$this->CommodityItem->unbindModel(array('belongsTo' => array('Client', 'Commodity', 'CarClass', 'Staff')));

			$save['id'] = $commodityItemId;
			$save['staff_id'] = $this->clientData['id'];
			$save['delete_flg'] = 1;
			$save['deleted'] = date("Y-m-d H:i:s", time());

			$result = $this->CommodityItem->save($save);
			if (!$result) {
				throw new Exception('商品情報の削除に失敗しました。');
			}

			// 価格削除
			$result = $this->AgentOrganizedPrice->updateAll(
				[
					'staff_id' => $this->clientData['id'],
					'delete_flg' => 1,
					'deleted' => date("'Y-m-d H:i:s'", time()),
				], [
					'commodity_item_id' => $commodityItemId,
				]
			);
			if (!$result) {
				throw new Exception('料金設定の削除に失敗しました。');
			}

			$this->CommodityItem->commit();
			return ['action' => 'index'];
		} catch(Exception $e) {
			$this->Session->setFlash($e->getMessage(), 'default', ['class' => 'alert alert-error']);
			$this->CommodityItem->rollback();
			return $this->referer();
		}
	}

	/**
	 * 募集型企画商品の料金設定を登録する
	 *
	 * @param int   $commodityId
	 * @param array $requestData
	 * @param int   $commodityItemId
	 * @return void
	 */
	private function savePackageSystem($commodityId, $requestData, $commodityItemId)
	{
		try {
			$this->CommodityItem->begin();

			if (isset($requestData['saveAll']) || isset($requestData['saveCarInfo'])) {
				$commodityItemData = [
					'client_id'    => $this->clientData['client_id'],
					'commodity_id' => $commodityId,
					'car_class_id' => $requestData['CommodityItem']['car_class_id'],
					'car_model_id' => $requestData['CommodityItem']['car_model_id'],
					'staff_id'     => $this->clientData['id']
				];
				if ($this->clientData['is_system_admin']) {
					$correctSippCode = $this->SippCode->validate(
						$requestData['CommodityItem']['sipp_code'], $commodityItemData['commodity_id'], $commodityItemData['car_class_id'], $commodityItemData['car_model_id']
					);
					if ($correctSippCode) {
						$commodityItemData['sipp_code'] = implode($requestData['CommodityItem']['sipp_code']);
					} else {
						throw new RuntimeException('SIPPコードを選択してください。');
					}
				}
				if ($commodityItemId !== 0) {
					$this->CommodityItem->id = $commodityItemId;
				}

				$result = $this->CommodityItem->save($commodityItemData);
				if (!$result) {
					throw new RuntimeException('車両情報を確認してください。');
				}
				if ($commodityItemId === 0) {
					$commodityItemId = $result['CommodityItem']['id'];
				}
			}

			if (isset($requestData['saveAll']) || isset($requestData['savePriceInfo'])) {
				if (empty($requestData['AgentOrganizedPrice'])) {
					throw new RuntimeException('料金は最低1件の登録が必須です。');
				}
				$agentOrganizedPriceData = [];
				foreach ($requestData['AgentOrganizedPrice'] as $key => $val) {
					$agentOrganizedPriceData[$key] = [
						'client_id'         => $this->clientData['client_id'],
						'commodity_item_id' => $commodityItemId,
						'start_date'        => $val['start_date'],
						'end_date'          => $val['end_date'],
						'price_stay_1'      => $val['price_stay_1'],
						'price_stay_2'      => $val['price_stay_2'],
						'price_stay_3'      => $val['price_stay_3'],
						'price_stay_over'   => $val['price_stay_over'],
						'staff_id'          => $this->clientData['id']
					];
					if (isset($val['id'])) {
						$agentOrganizedPriceData[$key]['id'] = $val['id'];
					}
				}
				$result = $this->AgentOrganizedPrice->savePriceData($agentOrganizedPriceData);
				if (!$result) {
					throw new RuntimeException('料金設定を確認してください。');
				}
			}

			$this->CommodityItem->commit();
			$this->Session->setFlash('車両情報・料金を設定しました。', 'default', array('class' => 'alert alert-success'));
			$carClassId = $this->CommodityItem->field('car_class_id', ['id' => $commodityItemId, 'delete_flg' => 0]);
			$this->redirect(array('action' => 'packageSystem', $commodityId, $carClassId));
		} catch (Exception $e) {
			$this->Session->setFlash('保存に失敗しました。' . $e->getMessage(), 'default', ['class' => 'alert alert-error']);
			$this->CommodityItem->rollback();
		}
	}

	//編集用に登録データをフォーマット
	private function formatEditData($data) {

		$editData = $data;

		$i = 0;
		unset($editData['CommodityRentOffice']);
		foreach ($data['CommodityRentOffice'] as $key => $val) {
			$editData['CommodityRentOffice']['commodity_id'][$i] = $val['office_id'];
			$i++;
		}

		$i = 0;
		unset($editData['CommodityReturnOffice']);
		foreach ($data['CommodityReturnOffice'] as $key => $val) {
			$editData['CommodityReturnOffice']['commodity_id'][$i] = $val['office_id'];
			$i++;
		}

		$editData['CommodityTerm']['available_from'] = $this->splitDateTime($editData['CommodityTerm'][0]['available_from']); // 年を取り出す
		$editData['CommodityTerm']['available_to'] = $this->splitDateTime($editData['CommodityTerm'][0]['available_to']); // 年を取り出す
		$editData['CommodityTerm']['deadline_hours'] = $editData['CommodityTerm'][0]['deadline_hours'];
		$editData['CommodityTerm']['consider_opening_hours'] = $editData['CommodityTerm'][0]['consider_opening_hours'];
		$editData['CommodityTerm']['deadline_days'] = $editData['CommodityTerm'][0]['deadline_days'];

		if (!empty($editData['CommodityTerm'][0]['deadline_time'])) {
			$editData['CommodityTerm']['deadline_time'] = $this->splitDateTime($editData['CommodityTerm'][0]['deadline_time']); // 年を取り出す
		}

		$editData['CommodityTerm']['bookable_days'] = $editData['CommodityTerm'][0]['bookable_days'];

		$i = 0;
		unset($editData['CommodityEquipment']);
		foreach ($data['CommodityEquipment'] as $key => $val) {
			$editData['CommodityEquipment']['equipment_id'][$i] = $val['equipment_id'];
			$i++;
		}

		$i = 0;
		unset($editData['CommodityPrivilege']);
		// オプションとシートの分割
		$commodityPrivilegeIdList = array();
		foreach ($data['CommodityPrivilege'] as $key => $val) {
			$commodityPrivilegeIdList[] = $val['privilege_id'];
		}
		$privilegeData = $this->Privilege->getPrivilegeData($commodityPrivilegeIdList);
		foreach ($privilegeData as $key => $value) {
			if ($value['Privilege']['option_flg'] == 0) {
				// オプション
				$editData['CommodityPrivilege']['privilege_id'][$i] = $value['Privilege']['id'];
			} else if ($value['Privilege']['option_flg'] == 1) {
				// シート
				$editData['CommodityPrivilege']['sheet_privilege_id'][$i] = $value['Privilege']['id'];
			}
			$i++;
		}

		return $editData;
	}

	//データを年月日ごとの配列にわける
	private function splitDateTime($date) {

		$formatDate['year'] = date('Y', strtotime($date));
		$formatDate['month'] = date('m', strtotime($date));
		$formatDate['day'] = date('d', strtotime($date));
		$formatDate['hour'] = date('H', strtotime($date));
		$formatDate['min'] = date('i', strtotime($date));

		return $formatDate;
	}

	/**
	 * 価格設定の遷移先テンプレート名を取得する
	 *
	 * @param  int  $dayTimeFlg
	 * @param  bool $isAgentOrganizedCommodity
	 * @return string
	 */
	private function getPriceSystemTemplate($dayTimeFlg, $isAgentOrganizedCommodity)
	{
		$template = '';
		if ($dayTimeFlg === 0) {
			$template = $isAgentOrganizedCommodity ? 'packageSystem' : 'daySystem';
		} elseif ($dayTimeFlg === 1) {
			$template = 'timeSystem';
		}
		return $template;
	}

	//配列に値が一つでも入っているかチェック
	private function checkEmptyArray($data) {

		foreach ($data as $val) {
			if (!empty($val['commodity_price'])) {

				return true;
			}
		}

		return false;
	}

	private function __checkInsertImages($commodityImage) {

		$error = '';

		if(empty($commodityImage[0]['image_relative_url']['name'])){
			$error = '画像1が登録されていません。';
		}


		return $error;
	}

	private function __checkCopyImages($commodityImage) {

		$error = '';

		if(empty($commodityImage[0]['default_image'])&&
		   empty($commodityImage[0]['image_relative_url']['name'])){
			$error = '画像1が登録されていません。';
		}


		return $error;
	}

	private function __checkUpdateImages($commodityImage,$oldCommodityImage) {

		$error = '';

		if(empty($commodityImage[0]['image_relative_url']['name'])&&
		   empty($oldCommodityImage[0]['image_relative_url'])){
			$error = '画像1が登録されていません。';
		}


		return $error;
	}

	private function __checkDedlineDate($data) {

		$error = '';
		if ($data['deadline_hours'] == '' && $data['deadline_days'] == '') {
			$error = '受付締切時間または受付締切時刻が入力されていません。';
		} else if ($data['deadline_days'] != '' && (empty($data['deadline_time']['hour']) || empty($data['deadline_time']['min']))) {
			$error = '受付締切時刻が入力されていません。';
		}
		if (!checkdate($data['available_from']['month'], $data['available_from']['day'], $data['available_from']['year'])) {
			$error = '提供開始日時の日付が正しくありません。';
		}
		if (!checkdate($data['available_to']['month'], $data['available_to']['day'], $data['available_to']['year'])) {
			$error = '提供終了日時の日付が正しくありません。';
		}
		if (empty($error)){
			$from = date("Ymd",mktime( 0, 0, 0, $data['available_from']['month'], $data['available_from']['day'], $data['available_from']['year']));
			$to = date("Ymd",mktime( 0, 0, 0, $data['available_to']['month'], $data['available_to']['day'], $data['available_to']['year']));
			if($from > $to){
				$error = '提供終了日が提供開始日より前です。';
			}
		}

		return $error;
	}

	/**
	 * 募集型企画商品関連設定の制限が守られているかチェックする
	 *
	 * @return string
	 */
	private function checkAgentOrganizedSettings()
	{
		if ((int)$this->request->data['Commodity']['day_time_flg'] !== 0) {
			return '募集型企画商品の料金形態は暦日制のみ選択可能です。';
		}
		if ((int)$this->request->data['Commodity']['payment_method'] !== 1) {
			return '募集型企画商品のお支払い方法はWEB事前決済のみ選択可能です。';
		}
		return '';
	}

	/**
	 * ビュー変数セット
	 */
	private function __setViewVars($clientId) {

		// 日付fromオプション
		$this->set('datetimeFromOptions', array(
			'formName' => 'Commodities',
			'fieldName' => 'datetimeFrom',
			'dateFormat' => 'YMD',
			'minYear' => '2016',
			'class' => 'span3',
			'empty' => '---'
		));

		//車両クラス
		$this->set('carClassesFromOptions', $this->CarClass->getCarClassLists(
				$this->clientData['client_id']
			), array(
				'empty' => '',
				'label' => false,
				'div' => false,
			)
		);

		//営業所別
		$this->set('officeFromOptions', $this->Office->find('list', array('conditions' => array('client_id' => $clientId, 'delete_flg' => 0), array('fields' => array('id', 'name')))), array(
			'empty' => '',
			'label' => false,
			'div' => false,
				)
		);

		//営業所在庫管理地域マスタ
		$staffId = '';
		if (!$this->clientData['is_system_admin']) {
			$staffId = $this->clientData['id'];
		}
		$this->set('stockGroupFromOptions', $this->StockGroup->getStockGroupList($clientId, '', $staffId), array(
			'empty' => '',
			'label' => false,
			'div' => false,
				)
		);

		//商品グループマスタ
		$this->set('commodityGroupFromOptions', $this->CommodityGroup->getList(
				$clientId
			), array(
				'empty' => '',
				'label' => false,
				'div' => false,
			)
		);

		$this->set('postConditions', $this->request->data['Commodities']);

		$this->set('carTypeLists', $this->CarClass->getCarTypeByClientID($clientId));

		$this->set('clientId', $clientId);
	}

	private function __setViewVarsAdd($clientId) {

		// 販売方法
		$this->set('salesType', [
			'options' => Constant::salesType(),
			'empty'   => false,
			'label'   => false,
			'div'     => false
		]);
		//グループフォームオプション
		$this->set('groupFormOptions', array(
			'options' => $this->CommodityGroup->getList($clientId),
			'empty' => '',
			'label' => false,
			'div' => false,
			'style' => 'width:80%;',
		));

		//受け取り可能開始時間
		$this->set('rentTimeFromOptions', array(
			'formName' => 'Commodity',
			'fieldName' => 'rent_time_from',
			'dateFormat' => 'HI',
			'class' => 'span2',
			'empty' => '---'
		));

		//受け取り開始終了時間
		$this->set('rentTimeToOptions', array(
			'formName' => 'Commodity',
			'fieldName' => 'rent_time_to',
			'dateFormat' => 'HI',
			'class' => 'span2',
			'empty' => '---'
		));

		//返車可能開始時間
		$this->set('returnTimeFromOptions', array(
			'formName' => 'Commodity',
			'fieldName' => 'return_time_from',
			'dateFormat' => 'HI',
			'class' => 'span2',
			'empty' => '---'
		));

		//返車開始終了時間
		$this->set('returnTimeToOptions', array(
			'formName' => 'Commodity',
			'fieldName' => 'return_time_to',
			'dateFormat' => 'HI',
			'class' => 'span2',
			'empty' => '---'
		));

		//提供開始日時フォームオプション
		$this->set('availableFromOptions', array(
			'formName' => 'CommodityTerm',
			'fieldName' => 'available_from',
			'dateFormat' => 'YMDHI',
			'maxYear' => date('Y') + 1,
			'class' => 'span2',
			'empty' => false
		));

		//提供開始日時フォームオプション
		$this->set('availableToOptions', array(
			'formName' => 'CommodityTerm',
			'fieldName' => 'available_to',
			'dateFormat' => 'YMDHI',
			'maxYear' => date('Y') + 1,
			'class' => 'span2',
			'empty' => false
		));

		//受付締切時刻フォームオプション
		$this->set('deadlineTimeOptions', array(
			'class' => 'span1',
			'empty' => '---',
			'label' => false,
		));

		//商品装備情報
		$this->set('equipmentFormOptions', array(
			'type' => 'select',
			'multiple' => 'checkbox',
			'options' => $this->Equipment->find('list', array('conditions' => array('delete_flg' => 0, 'is_published' => 1), array('fields' => array('id', 'name')))),
			'empty' => '',
			'label' => false,
			'div' => false,
		));


		###############################################################################################################################
		// 都道府県フォームオプション(受取)
		$options = array(
			'recursive' => -1,
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.prefecture_id = Prefecture.id',
				),
				array(
					'type' => 'LEFT',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => 'Office.area_id = Area.id',
				),
			),
			'conditions' => array(
				'Office.accept_rent' => 1,
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0
			),
		);
		$this->set('prefectureRentFormOptions', array(
			'type' => 'select',
			'options' => $this->Prefecture->find('list', $options),
			'empty' => 'すべて',
			'label' => false,
			'div' => false,
		));
		// 都道府県フォームオプション(返却)
		$options = array(
			'recursive' => -1,
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.prefecture_id = Prefecture.id',
				),
				array(
					'type' => 'LEFT',
					'alias' => 'Office',
					'table' => 'offices',
					'conditions' => 'Office.area_id = Area.id',
				),
			),
			'conditions' => array(
				'Office.accept_return' => 1,
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0
			),
		);
		$this->set('prefectureReturnFormOptions', array(
			'type' => 'select',
			'options' => $this->Prefecture->find('list', $options),
			'empty' => 'すべて',
			'label' => false,
			'div' => false,
		));

		// 受取営業所フォームオプション
		$this->Office->virtualFields = array('value' => '', 'name' => '', 'class' => '');
		$options = array(
			'recursive' => -1,
			'fields' => array(
				'Office.id as Office__value',
				'Office.name as Office__name',
				'concat("pref-class_", Area.prefecture_id) as Office__class',
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.id = Office.area_id',
				),
			),
			'conditions' => array(
				'Office.accept_rent' => 1,
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0,
			),
		);
		$response = $this->Office->find('all', $options);
		$response = Hash::extract($response, '{n}.' . $this->Office->name);

		$this->set('officeRentFormOptions', array(
			'type' => 'select',
			'multiple' => 'checkbox',
			'options' => $response,
			'empty' => '',
			'label' => false,
			'div' => false,
		));

		// 返却営業所フォームオプション
		$this->Office->virtualFields = array('value' => '', 'name' => '', 'class' => '');
		$options = array(
			'recursive' => -1,
			'fields' => array(
				'Office.id as Office__value',
				'Office.name as Office__name',
				'concat("pref-class_", Area.prefecture_id) as Office__class',
			),
			'joins' => array(
				array(
					'type' => 'LEFT',
					'alias' => 'Area',
					'table' => 'areas',
					'conditions' => 'Area.id = Office.area_id',
				),
			),
			'conditions' => array(
				'Office.accept_return' => 1,
				'Office.client_id' => $clientId,
				'Office.delete_flg' => 0,
			),
		);
		$response = $this->Office->find('all', $options);
		$response = Hash::extract($response, '{n}.' . $this->Office->name);

		$this->set('officeReturnFormOptions', array(
			'type' => 'select',
			'multiple' => 'checkbox',
			'options' => $response,
			'empty' => '',
			'label' => false,
			'div' => false,
		));

		$this->set('paymentMethodOptions', array(
			0 => '現地精算のみ',
			1 => 'WEB事前決済のみ',
			2 => 'どちらでも可',
		));

		###############################################################################################################################
		// オプションリスト情報
		$privilegeFormOptions = $this->Privilege->getPrivilegeDataList($clientId, 0);
		// チャイルドシートリスト情報
		$sheetFormOptions = $this->Privilege->getPrivilegeDataList($clientId, 1);
		$this->set(compact('privilegeFormOptions', 'sheetFormOptions', 'clientId'));
	}

	/**
	 * 募集型企画商品 料金設定画面のviewVarsをセット
	 *
	 * @param  string      $commodityId
	 * @param  string|null $carClassId
	 * @param  int         $commodityItemId
	 * @return void
	 */
	private function setPackageSystemViewVars($commodityId, $carClassId, $commodityItemId)
	{
		if (!is_null($carClassId)) {
			$commodityItem = $this->CommodityItem->find('first', [
					'fields' => ['car_model_id', 'sipp_code'],
					'conditions' => [
						'CommodityItem.id' => $commodityItemId,
						'CommodityItem.delete_flg'   => 0
					],
					'recursive' => -1
				]
			);

			if ($this->request->data === []) {
				$this->request->data['CommodityItem']['car_model_id'] = $commodityItem['CommodityItem']['car_model_id'];
				$this->request->data['CommodityItem']['sipp_code'] = str_split($commodityItem['CommodityItem']['sipp_code']);
				$this->request->data['AgentOrganizedPrice'] = $this->AgentOrganizedPrice->getPricesByCommodityItemId($commodityItemId);
			}
		}

		$carClassList = $this->CarClass->getCarClassLists($this->clientData['client_id']);
		$carClassId = is_null($carClassId) ? key($carClassList) : $carClassId;
		$carModelList = $this->CarModel->getCarModelListByCarClassId($carClassId);
		$sippCodeList = $this->SippCode->getSippCodeList($commodityId, $carClassId, $this->request->data['CommodityItem']['car_model_id']);
		$agentOrganizedPriceList = $this->request->data['AgentOrganizedPrice'];

		$inputOption['date'] = [
			'type' => 'text',
			'class' => 'datepicker',
			'required' => true,
		];
		$inputOption['price'] = [
			'type' => 'number',
			'min' => 0,
			'required' => true,
		];

		$this->request->data['CommodityItem']['car_class_id'] = $carClassId;
		$this->set(compact(
			'commodityId', 'commodityItemId', 'carModelList', 'carClassList', 'sippCodeList', 'agentOrganizedPriceList', 'inputOption'
		));
}

	private function __idDelete($data) {

		foreach ($data as $key => $val) {
			if (strcmp($key, 'Commodity') == 0) {

				unset($data[$key]['id']);
			} else if (strcmp($key, 'CommodityImage') == 0) {
				foreach ($val as $imgKey => $img) {
					unset($data[$key][$imgKey]['id']);
				}
			} else if (strcmp($key, 'CommodityItem') == 0) {
				foreach ($val as $itemKey => $item) {
					unset($data[$key][$itemKey]['id']);
				}
			} else if (strcmp($key, 'CommodityTerm') == 0) {
				foreach ($val as $termKey => $term) {
					if ($termKey == 0) {
						unset($data[$key][$termKey]['id']);
						break;
					}
				}
			} else if (strcmp($key, 'CommodityTerm') == 0) {
				foreach ($val as $termKey => $term) {
					if ($termKey == 0) {
						unset($data[$key][$termKey]['id']);
						break;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * ajax用 車両クラスに応じた車種をgetする
	 * @param string $carClassId
	 */
	public function get_car_model_list($carClassId = '') {
		$this->autoRender = false;
		if (!empty($carClassId) && $this->request->is('ajax')) {
			$carModelList = $this->CarModel->getCarModelListByCarClassId($carClassId);
			return json_encode($carModelList);
		}
	}

	/**
	 * ajax用 sippコードのリストをgetする
	 * @param string $commodityId
	 * @param string $carClassId
	 * @param string $carModelId
	 */
	public function get_sipp_code_list($commodityId, $carClassId, $carModelId = null) {
		$this->autoRender = false;

		if (!$this->request->is('ajax')) {
			return;
		}

		if (empty($commodityId) || empty($carClassId)) {
			return;
		}

		$sippCodeList = $this->SippCode->getSippCodeList($commodityId, $carClassId, $carModelId);

		return json_encode($sippCodeList);
	}

	/**
	 * ajax用 車両クラスに応じた免責補償料金をgetする
	 * @param string $carClassId
	 */
	public function get_disclaimer_list($carClassId = '') {
		$this->autoRender = false;
		if (!empty($carClassId) && $this->request->is('ajax')) {
			$disclaimerList = $this->DisclaimerCompensation->getAllByCarClassId($carClassId);
			$disclaimerList = Hash::extract($disclaimerList, '{n}.DisclaimerCompensation');
			return json_encode($disclaimerList);
		}
	}

	// フロント側プラン詳細同名メソッドのコピー
	// TASK-9016 本家は app/Controller/Api/AjaxPlanInfoController.php に移動した
	public function getPlanInfo() {
		//define('MAX_AGE', 3600);

		Configure::write('debug', 0);
		$this->autoRender = false;
		$this->response->type('json');

		if ($this->request->is('ajax')) {
			if (empty($this->request->query['id'])) {
				throw new NotFoundException;
			} else {
				$commodityInfo = $this->Commodity->getCommodityInfoByCommodityItemId($this->request->query['id']);
				if (empty($commodityInfo)) {
					throw new NotFoundException;
				}
				$commodityImages = array();
				$tmp = $this->CommodityImage->getImageByCommodityId($commodityInfo['Commodity']['id']);
				if (!empty($tmp)) {
					foreach ($tmp as $v) {
						if (!empty($v['image_relative_url'])) {
							$commodityImages[] = [
								'url' => $v['image_relative_url'],
								'remark' => $v['remark']
							];
						}
					}
				}
				$carModels = array();
				$tmp = $this->CarModel->getCarModelListByClientIdAndCarClassId($commodityInfo['Commodity']['client_id'], $commodityInfo['CommodityItem']['car_class_id']);
				if (!empty($tmp)) {
					foreach ($tmp as $v) {
						$carModels[] = $v['CarModel']['name'];
					}
				}
				//$this->response->header($this->HttpHeader->getPublicCacheConfig(MAX_AGE));
				return json_encode(array(
					'id' => $this->request->query['id'],
					'client_id' => $commodityInfo['Commodity']['client_id'],
					'description' => $commodityInfo['Commodity']['description'],
					'remark' => $commodityInfo['Commodity']['remark'],
					'plan_name' => $commodityInfo['Commodity']['name'],
					'images' => $commodityImages,
					'models' => $carModels,
					'car_type_name' => $commodityInfo['CarType']['name'],
					'flg_model_select' => !empty($commodityInfo['CommodityItem']['car_model_id']),
				));
			}
		} else {
			throw new NotFoundException;
		}
	}

	/**
	 * 車両クラスの価格を取得する
	 *
	 * @param  bool   $isAgentOrganizedCommodity
	 * @param  string $commodityId
	 * @return array
	 */
	private function getPrices($isAgentOrganizedCommodity, $commodityId)
	{
		$prices = [];
		if ($isAgentOrganizedCommodity) {
			$prices = $this->Commodity->getAgentOrganizedCommodityItemData($commodityId, $this->clientData['client_id']);
		} else {
			$prices = $this->Commodity->getPlace($commodityId, $this->clientData['client_id']);
		}
		return $prices;
	}

	/**
	 * 募集型企画商品向け 料金情報削除（1行だけ削除）
	 *
	 * @return string
	 */
	public function deletePrice()
	{
		$this->autoRender = false;
		$this->response->type('json');
		$commodityId = (int)$this->request->data['commodityId'];
		$agentOrganizedPriceId = (int)$this->request->data['agentOrganizedPriceId'];
		$response = [
			'result' => '',
			'message' => ''
		];

		// HTTPメソッドチェック
		if (!$this->request->is('ajax') || !$this->request->is('post')) {
			$response['result'] = 'error';
			$response['message'] = '不正なリクエストです。';
			return json_encode($response);
		}

		// 編集権限チェック
		$isEditable = $this->Commodity->isEditableByThisStaff($commodityId, $this->clientData['client_id']);
		if (!$isEditable) {
			$response['result'] = 'error';
			$response['message'] = '編集権限がありません。';
			return json_encode($response);
		}

		// 存在チェック
		if (!$this->AgentOrganizedPrice->existsPriceData($agentOrganizedPriceId)) {
			$response['result'] = 'error';
			$response['message'] = '指定された料金情報は存在しません。';
			return json_encode($response);
		}

		$result = $this->AgentOrganizedPrice->deletePrice($agentOrganizedPriceId, $this->clientData['id']);
		if (!$result) {
			$response['result'] = 'error';
			$response['message'] = '削除できませんでした。';
		} else {
			$response['result'] = 'success';
		}
		return json_encode($response);
	}
}

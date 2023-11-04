<?php
App::uses('AppController', 'Controller');
/**
 * Campaigns Controller
 *
 * @property Campaign $Campaign
 */
class CampaignTermsController extends AppController {

	public $uses = array('Campaign', 'CampaignTerm', 'Staff', 'Commodity', 'CommodityCampaignPrice');
	const DEFAULT_TERM_COUNT = 20; // 期間初期表示数
	public function beforeFilter() {
		parent::beforeFilter();

		/**
		 * 編集・削除対象のデータが該当クライアントのデータかチェックする
		 */
		if(array_keys(array('edit'),$this->action)) {
			//編集・削除対象IDが存在するかチェック
			if(!empty($this->passedArgs[0])) {
				/**
				 * 編集・削除対象IDとクライアントIDで検索
				 * データが存在しない場合一覧へリダイレクト
				 */
				if(!$this->Campaign->clientCheck($this->passedArgs[0],$this->clientData['Client']['id'])) {
					$this->Session->setFlash( '不正なアクセスです。', 'default', array( 'class' => 'alert alert-error'));
					$this->redirect(array('action'=>'index'));
				}
			}
		}

		// 公開範囲
		$scopeList = array(0 => '共通');
		if ($this->clientData['is_client_admin']) {
			$scopeList += $this->Staff->getStaffList($this->clientData['client_id']);
		} else {
			$scopeList[$this->clientData['id']] = $this->clientData['name'];
		}

		$this->set(compact('scopeList'));
		$this->set('is_check_user', true);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$clientId = $this->clientData['Client']['id'];
		$date = '';

		if (!empty($this->request->query)) {
			if (!empty($this->request->query['date'])) {
				$date = $this->request->query['date']['year'] . '/' . $this->request->query['date']['month'] . '/' . $this->request->query['date']['day'];
			}
			$this->request->data['search'] = $this->request->query;
		}

		$campaigns = $this->Campaign->getAllCampaignAndTerms($clientId, $this->request->query['name'], $date, $this->request->query['week']);

		// 対象期間オプション
		$targetDateOptions = array(
			'fieldName' => 'date',
			'dateFormat' => 'YMD',
			'minYear' => '2016',
			'class' => 'span3',
			'datetimeOption' => array(
				'value' => !empty($date) ? $date : date('Ymd')
			)
		);

		$this->set(compact('campaigns', 'targetDateOptions'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$scopeOption = array('empty' => false, 'required');
		if (!$this->clientData['is_client_admin']) {
			unset($this->viewVars['scopeList'][0]);
		}
		$this->set('scopeOption', $scopeOption);

		// 新規登録時の初期期間表示件数を設定
		$this->request->data['Campaign']['termDefaultCount'] = self::DEFAULT_TERM_COUNT;

		if (!$this->request->is('post')) {
			return;
		}

		$campaign = $this->request->data['Campaign'];
		$terms = $this->request->data['CampaignTerm'];
		$clientId = $this->clientData['Client']['id'];
		$staffId = $this->clientData['id'];

		$campaign['client_id'] = $clientId;
		$campaign['staff_id'] = $staffId;

		// 入力の無い期間は破棄
		foreach ($terms as $k => $v) {
			if (empty($v['start_date']) && empty($v['end_date'])) {
				unset($terms[$k]);
			}
			if (!empty($v['week'])) {
				$weekSaveData = $this->getSaveWeekData($v['week']);
				$terms[$k] = array_merge($v, $weekSaveData);
			}
		}
		array_merge($terms);

		// 期間データの表示数設定
		if (count($terms) <= self::DEFAULT_TERM_COUNT) {
			$this->request->data['Campaign']['termCount'] = self::DEFAULT_TERM_COUNT;
		} else {
			$this->request->data['Campaign']['termCount'] = count($terms);
		}

		// 期間の登録が1つもない場合は登録させない
		if (empty($terms)) {
			$this->Session->setFlash(__('期間が登録されていません。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		// 1期間に曜日の登録が1つもない場合は登録させない
		foreach ($terms as $tk => $tv) {
			if (empty($tv['week'])) {
				$this->Session->setFlash(__('曜日が登録されていない期間があります。'), 'default', array('class' => 'alert alert-error'));
				return;
			}
		}

		// validationのみ
		if (!$this->CampaignTerm->saveAll($terms, array('validate' => 'only'))) {
			$this->Session->setFlash(__('登録に失敗しました。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		// 期間重複チェック
		if ($this->CampaignTerm->dateDuplicate($terms)) {
			$this->Session->setFlash(__('曜日が重複して、同じ期間の登録は出来ません。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		// 曜日存在チェック
		if ($this->CampaignTerm->containsDay($terms)) {
			$this->Session->setFlash(__('指定期間に指定曜日が存在しません。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		$this->Campaign->begin();

		if (!$this->Campaign->save($campaign)) {
			$this->Session->setFlash(__('登録に失敗しました。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		// 必要な項目を追加
		foreach ($terms as $k => $v) {
			$terms[$k]['client_id'] = $clientId;
			$terms[$k]['campaign_id'] = $this->Campaign->id;
			$terms[$k]['staff_id'] = $staffId;
		}

		if (!$this->CampaignTerm->saveAll($terms, array('atomic' => true))) {
			$this->Session->setFlash(__('登録に失敗しました。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		$this->Campaign->commit();
		$this->Session->setFlash(__('登録しました。'), 'default', array('class' => 'alert alert-success'));
		if (!empty($this->request->data['Custom']['referer'])) {
			$this->redirect($this->request->data['Custom']['referer']);
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!($this->request->is('post') || $this->request->is('put'))) {
			$data = $this->Campaign->getCampaignAndTermsByCampaignId($id);
			if (empty($data)) {
				throw new NotFoundException(__('Invalid campaign'));
			}
			$weekEn = Constant::weekEn();
			foreach ($data['CampaignTerm'] as $key => $term) {
				$week = [];
				foreach ($weekEn as $val => $dayInfo) {
					if ($term[$dayInfo]) {
						$week[] = $val;
					}
				}
				$data['CampaignTerm'][$key]['week'] = $week;
			}

			// 期間データの初期表示数設定
			if (count($data['CampaignTerm']) <= self::DEFAULT_TERM_COUNT) {
				$data['Campaign']['termCount'] = self::DEFAULT_TERM_COUNT;
			} else {
				$data['Campaign']['termCount'] = count($data['CampaignTerm']);
			}

			$this->request->data = $data;
			return;
		}

		$clientId = $this->clientData['Client']['id'];
		$staffId = $this->clientData['id'];
		$now = date('Y-m-d H:i:s');

		$campaign = $this->request->data['Campaign'];
		$terms = $this->request->data['CampaignTerm'];

		// 期間データの表示数設定
		if (count($terms) <= self::DEFAULT_TERM_COUNT) {
			$this->request->data['Campaign']['termCount'] = self::DEFAULT_TERM_COUNT;
		} else {
			$this->request->data['Campaign']['termCount'] = count($terms);
		}

		$campaign['staff_id'] = $staffId;
		if (!empty($campaign['delete_flg'])) {
			if (!empty($this->Commodity->getCampaignCount($campaign['id'], $clientId))) {
				$this->Session->setFlash(__('登録済み商品に該当のキャンペーンが登録されているため削除できません。'), '', array(), 'auth');
			}
			$campaign['deleted'] = $now;
		}

        $campaignId = $campaign['id'];

		$deleteTerms = array();
		foreach ($terms as $k => $v) {
			$weekSaveData = $this->getSaveWeekData($v['week']);
			$terms[$k] = array_merge($v, $weekSaveData);

			if (!empty($campaign['delete_flg'])) {
				// キャンペーン本体を削除する場合、紐づく期間も削除
				if (!empty($v['id'])) {
					$deleteTerms[] = array('id' => $v['id'], 'delete_flg' => 1, 'deleted' => $now);
				}
				unset($terms[$k]);
			} else {
				if (empty($v['id']) && empty($v['start_date']) && empty($v['end_date'])) {
					// 入力の無い期間は破棄
					unset($terms[$k]);
				} else {
					if (empty($v['start_date']) && empty($v['end_date'])) {
						// 登録済みで、日時を消された期間は削除
						$deleteTerms[] = array('id' => $v['id'], 'delete_flg' => 1, 'deleted' => $now);
						unset($terms[$k]);
					} else if (empty($v['id'])) {
						// 未登録は新規追加
						$terms[$k]['client_id'] = $clientId;
						$terms[$k]['campaign_id'] = $campaign['id'];
						$terms[$k]['staff_id'] = $staffId;
					} else {
						// 登録済みは更新
						$terms[$k]['staff_id'] = $staffId;
					}
				}
			}
		}
		array_merge($terms);

		// キャンペーン削除以外は期間をチェック
		if (empty($campaign['delete_flg'])) {
			// 期間の登録が1つもない場合は登録させない
			if (empty($terms)) {
				$this->Session->setFlash(__('期間が登録されていません。'), 'default', array('class' => 'alert alert-error'));
				return;
			}

			// 1期間に曜日の登録が1つもない場合は登録させない
			foreach ($terms as $tk => $tv) {
				if (empty($tv['week'])) {
					$this->Session->setFlash(__('曜日が登録されていない期間があります。'), 'default', array('class' => 'alert alert-error'));
					return;
				}
			}

			// validationのみ
			if (!$this->CampaignTerm->saveAll($terms, array('validate' => 'only'))) {
				$this->Session->setFlash(__('更新に失敗しました。'), 'default', array('class' => 'alert alert-error'));
				return;
			}

			// 期間重複チェック
			if ($this->CampaignTerm->dateDuplicate($terms)) {
				$this->Session->setFlash(__('曜日が重複して、同じ期間の登録は出来ません。'), 'default', array('class' => 'alert alert-error'));
				return;
			}

			// 曜日存在チェック
			if ($this->CampaignTerm->containsDay($terms)) {
				$this->Session->setFlash(__('指定期間に指定曜日が存在しません。'), 'default', array('class' => 'alert alert-error'));
				return;
			}

            // campaignId, $clientIdに紐づく 商品ID:commodity_item_id の取得
            $commodityItemIds = array();
            $commodityItemData = $this->CommodityCampaignPrice->getCommodityItemId($campaignId, $clientId);
            if (empty($commodityItemData)) {
                $commodityItemIds = NULL;
            } else {
                $commodityItemIds = Hash::extract($commodityItemData, '{n}.CommodityCampaignPrice.commodity_item_id');
            }

            // 更新しようとしているキャンペーンの期間とクライアントIDにて同一商品IDを設定しているキャンペーンが他にあった場合、エラーメッセージを出力
            if ($commodityItemIds) {
                $campaignIds = $this->CampaignTerm->getCommodityItemIdWithOtherCampaignId($terms, $campaignId, $clientId, $commodityItemIds);
                if (!empty($campaignIds)) {
                    $this->Session->setFlash(__('登録済み商品と期間が重なるキャンペーンが存在する為、更新できません。'), 'default', array('class' => 'alert alert-error'));
                    return;
                }
            }
		}

		$this->Campaign->begin();

		if (!$this->Campaign->save($campaign)) {
			$this->Session->setFlash(__('更新に失敗しました。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		$terms = array_merge($terms, $deleteTerms);
		if (!$this->CampaignTerm->saveAll($terms, array('atomic' => true))) {
			$this->Session->setFlash(__('更新に失敗しました。'), 'default', array('class' => 'alert alert-error'));
			return;
		}

		$this->Campaign->commit();
		$this->Session->setFlash(__('更新しました。'), 'default', array('class' => 'alert alert-success'));
		if (!empty($this->request->data['Custom']['referer'])) {
			$this->redirect($this->request->data['Custom']['referer']);
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

	private function getSaveWeekData($week) {
		$res = [];
		$weekEn = Constant::weekEn();
		foreach ($weekEn as $dayInfo) {
			$res[$dayInfo] = 0;
		}
		if (empty($week)) {
			return $res;
		}
		foreach ($week as $day) {
			if (!empty($weekEn[$day])) {
				$res[$weekEn[$day]] = 1;
			}
		}
		return $res;
	}
}

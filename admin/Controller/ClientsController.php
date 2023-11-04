<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'imageResizeUpLoad');

/**
 * Clients Controller
 *
 * @property Client $Client
 */
class ClientsController extends AppController {

	public $uses = array('Client', 'ClientCard', 'ClientEmail', 'CreditCard', 'SettlementCompany');

	public function beforeFilter() {
		parent::beforeFilter();

		$adminFlgArray = array('0' => 'クライアント', '1' => '管理者');
		$this->set('adminFlgArray', $adminFlgArray);

		// 成約の基準
		$conclusionContractCriteria = array(
			0 => '出発日で成約にする',
			1 => '返却日で成約にする'
		);

		// 地域タイプ
		$areaType = array(
			0 => '',
			1 => '全国',
			2 => '地域',
		);

		// 可・不可
		$acceptOrNot = array(
			0 => '不可',
			1 => '可',
		);

		// 検索公開・非公開
		$searchable = array(
			'1' => '公開',
			'' => '非公開'
		);

		// 締め切り時刻
		$deadlineTimeOptions = array(
			'class' => 'span1',
			'empty' => '---',
			'label' => false,
		);

		// 包括販売商品
		$managedPackage = [
			0 => 'OFF',
			1 => 'ON'
		];

		$this->set(compact(array('conclusionContractCriteria', 'areaType', 'acceptOrNot', 'searchable', 'deadlineTimeOptions', 'managedPackage')));
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		//並び順を保存するを押されたとき
		if ($this->request->is('post')) {
			if (!empty($this->request->data['Sort']['sort'])) {
				$order = $this->request->data['Sort']['sort'];
				$orderArray = explode(',', $order);
				$saveData = array();
				$i = 1;
				foreach ($orderArray as $val) {

					$saveData[$i]['id'] = $val;
					$saveData[$i]['sort'] = $i;
					$i++;
				}

				if ($this->Client->saveAll($saveData, array( 'validate' => false))) {
					$this->Session->setFlash('並び順を保存しました。', 'default', array('class' => 'alert alert-success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('エラー:並び順の保存に失敗しました。', 'default', array('class' => 'alert alert-error'));
				}
			}
		}

		$conditions = array(
			'fields' => array(
				'Client.id',
				'Client.name',
				'Client.sort',
				'Client.url',
				'Client.reserve_tag',
				'Client.commission_rate',
				'Client.area_type',
				'Client.accept_prepay',
				'Client.created',
				'Client.modified',
				'Client.is_searchable',
				'Client.delete_flg',
				'Client.is_managed_package'
			),
			'order' => array(
				'Client.sort',
				'Client.id',
			),
		);
		$this->set('clients', $this->Client->find('all', $conditions));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->request->data['Client'] = $this->correctDeadline($this->request->data['Client']);
			$message = $this->checkDeadline($this->request->data['Client']);
			if (!empty($message)) {
				$this->Session->setFlash($message, 'default', array('class' => 'alert alert-error'));
			} else {
				$this->request->data['Client']['staff_id'] = $this->cdata['id'];
				$this->Client->create();
				$saveData = array();
				if ($this->Client->save($this->request->data)) {

					//save ClientLogo
					/*if (!empty($this->request->data['Client']['logo_image_tmp'])) {

						$id = $this->Client->getLastInsertID();
						// 画像リサイズアップロード
						$this->ImageResize = new ImageResizeUpLoad();
						$upLoadDir = 'logo' . DS . 'oblong' . DS . $id . DS;
						$width = '190';
						$height = '30';
						$imgName = $this->ImageResize->resizeUpLoad($this->request->data['Client']['logo_image_tmp'], $upLoadDir, null, array($width, $height));

						if ($imgName) {
							$saveData['Client']['logo_image'] = $imgName;
							$saveData['Client']['id'] = $id;
						}
					}*/

					if (!empty($this->request->data['Client']['sp_logo_image_tmp'])) {

						$id = $this->Client->getLastInsertID();
						// 画像リサイズアップロード
						$this->ImageResize = new ImageResizeUpLoad();
						$upLoadDir = 'logo' . DS . 'square' . DS . $id . DS;
						$width = '120';
						$height = '120';
						$imgName = $this->ImageResize->resizeUpLoad($this->request->data['Client']['sp_logo_image_tmp'], $upLoadDir, null, array($width, $height));

						if ($imgName) {
							$saveData['Client']['sp_logo_image'] = $imgName;
							$saveData['Client']['id'] = $id;
						}
					}

					if (!empty($saveData)) {
						$saveData['Client']['staff_id'] = $this->cdata['id'];
						$this->Client->save($saveData);
					}

					// save ClientCard
					if (!empty($this->request->data['ClientCard']['credit_card_id'])) {
						foreach ($this->request->data['ClientCard']['credit_card_id'] as $key => $val) {
							$clientCard[$key]['ClientCard']['staff_id'] = $this->cdata['id'];
							$clientCard[$key]['ClientCard']['client_id'] = $id;
							$clientCard[$key]['ClientCard']['credit_card_id'] = $val;
						}

						$this->ClientCard->recursive = -1;
						$this->ClientCard->saveall($clientCard);
					}

					$this->Session->setFlash('クライアントを追加しました', 'default', array('class' => 'alert alert-success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('クライアントの追加に失敗しました', 'default', array('class' => 'alert alert-error'));
				}
			}
		}

		if (empty($this->request->data['radioHoursOrDays'])) {
			$this->request->data['radioHoursOrDays'] = 0;
		}
		$this->set('hoursOrDays', $this->request->data['radioHoursOrDays']);

		$creditCardList = $this->CreditCard->getCreditCard();

		$this->set(compact(array('creditCardList')));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {

		if (!$this->Client->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Client'] = $this->correctDeadline($this->request->data['Client']);
			$message = $this->checkDeadline($this->request->data['Client']);
			if (!empty($message)) {
				$this->Session->setFlash($message, 'default', array('class' => 'alert alert-error'));
			} else {
				//save ClientLogo
				/*if (!empty($this->request->data['Client']['logo_image_tmp'])) {

					// 画像リサイズアップロード
					$this->ImageResize = new ImageResizeUpLoad();
					$upLoadDir = 'logo' . DS . 'oblong' . DS . $id . DS;
					$width = '190';
					$height = '30';
					$imgName = $this->ImageResize->resizeUpLoad($this->request->data['Client']['logo_image_tmp'], $upLoadDir, null, array($width, $height));

					if ($imgName) {
						$this->request->data['Client']['logo_image'] = $imgName;
					}
				}*/

				if (!empty($this->request->data['Client']['sp_logo_image_tmp'])) {

					// 画像リサイズアップロード
					$this->ImageResize = new ImageResizeUpLoad();
					$upLoadDir = 'logo' . DS . 'square' . DS . $id . DS;
					$width = '120';
					$height = '120';
					$imgName = $this->ImageResize->resizeUpLoad($this->request->data['Client']['sp_logo_image_tmp'], $upLoadDir, null, array($width, $height));

					if ($imgName) {
						$this->request->data['Client']['sp_logo_image'] = $imgName;
					}
				}

				$this->request->data['Client']['staff_id'] = $this->cdata['id'];
				if ($this->request->data['Client']['delete_flg'] == 1) {
					$this->Client->id = $id;
					if ($this->Client->field('delete_flg') == 0) {
						$this->request->data['Client']['deleted'] = date("Y-m-d H:i:s", time());
					}
				} else {
					$this->request->data['Client']['deleted'] = null;
				}

				if ($this->Client->save($this->request->data['Client'])) {

					// save ClientCard
					$this->ClientCard->deleteall(array('ClientCard.client_id' => $id), false);
					if (!empty($this->request->data['ClientCard']['credit_card_id'])) {
						foreach ($this->request->data['ClientCard']['credit_card_id'] as $key => $val) {
							$clientCard[$key]['ClientCard']['staff_id'] = $this->cdata['id'];
							$clientCard[$key]['ClientCard']['client_id'] = $id;
							$clientCard[$key]['ClientCard']['credit_card_id'] = $val;
						}

						$this->ClientCard->recursive = -1;
						$this->ClientCard->saveall($clientCard);
					}

					$this->Session->setFlash('クライアントを編集しました', 'default', array('class' => 'alert alert-success'));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('クライアントの編集に失敗しました', 'default', array('class' => 'alert alert-error'));
				}
			}
		} else {
			$options = array('conditions' => array('Client.' . $this->Client->primaryKey => $id));
			$this->request->data = $this->Client->find('first', $options);
		}

		if (empty($this->request->data['radioHoursOrDays'])) {
			if (isset($this->request->data['Client']['cancel_deadline_days']) && !empty($this->request->data['Client']['cancel_deadline_time'])) {
				$this->request->data['radioHoursOrDays'] = 1;
			} else {
				$this->request->data['radioHoursOrDays'] = 0;
			}
		}
		$this->set('hoursOrDays', $this->request->data['radioHoursOrDays']);

		$clientCards = $this->ClientCard->getClientCard($id);
		$clientEmails = $this->ClientEmail->getClientEmail($id);
		$creditCardList = $this->CreditCard->getCreditCard();

		$this->set(compact(array('clientEmails', 'clientCards', 'creditCardList')));
	}

	private function correctDeadline($data) {
		$ret = $data;
		if (!isset($ret['cancel_deadline_hours'])) {
			$ret['cancel_deadline_hours'] = '';
		}
		if (!isset($ret['cancel_deadline_days'])) {
			$ret['cancel_deadline_days'] = '';
		}
		if (!isset($ret['cancel_deadline_time']['hour'])) {
			$ret['cancel_deadline_time']['hour'] = '';
		}
		if (!isset($ret['cancel_deadline_time']['min'])) {
			$ret['cancel_deadline_time']['min'] = '';
		}
		return $ret;
	}

	private function checkDeadline($data) {

		$error = '';
		if ($data['cancel_deadline_hours'] === '' && $data['cancel_deadline_days'] === '') {
			$error = 'キャンセル手仕舞い時間またはキャンセル手仕舞い時刻が入力されていません。';
		} else if ($data['cancel_deadline_days'] !== '' && (empty($data['cancel_deadline_time']['hour']) || empty($data['cancel_deadline_time']['min']))) {
			$error = 'キャンセル手仕舞い時刻が入力されていません。';
		}

		return $error;
	}
}

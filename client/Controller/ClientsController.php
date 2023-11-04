<?php
App::uses('AppController', 'Controller');
/**
 * Clients Controller
 *
 * @property Client $CarClassStock
 */
class ClientsController extends AppController {

	public $uses = array('Client', 'ClientEmail', 'ClientCard', 'CreditCard');

	public $components = array('PdfFileUp', 'CancelPolicy');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('is_check_user', true);
	}

	public function index() {
		$clients		 = $this->Client->getClient($this->clientData['client_id']);
		$clientCards	 = $this->ClientCard->getClientCard($this->clientData['client_id']);
		$clientEmails	 = $this->ClientEmail->getClientEmail($this->clientData['client_id']);
		$creditCardList	 = $this->CreditCard->getCreditCard();
		$cancelPolicy	 = $this->CancelPolicy->getTextLines($this->clientData['client_id'], date('Y-m-d H:i:s'));

		$this->set(compact(array(
			'clients',
			'clientEmails',
			'clientCards',
			'creditCardList',
			'cancelPolicy'
		)));
	}

	public function edit() {
		$this->_saveClient();
		$this->index();
	}

	function _saveClient() {

		if ($this->request->is('post') || $this->request->is('put')) {
			// save Client
			if (!empty($this->request->data['Client'])) {
				$this->request->data['Client']['id']       = $this->clientData['client_id'];
				$this->request->data['Client']['staff_id'] = $this->clientData['id'];

				// PDFファイルのアップロード

				if(!empty($this->request->data['Client']['clause_pdf']['tmp_name'])) {
					$file = $this->request->data['Client']['clause_pdf'];
					$this->request->data['Client']['clause_pdf'] = $this->Client->savePdf($file,'clause_pdf');
					$error = '';
					if(empty($this->request->data['Client']['clause_pdf'])) {
					  $error = 'PDFの保存に失敗しました。拡張子がpdfのファイルを選択してください';
					}
				} else {
					unset($this->request->data['Client']['clause_pdf']);
				}

				$this->Client->save($this->request->data['Client']);

			}


			// save ClientEmail
			if (!empty($this->request->data['ClientEmail'])) {
				foreach ($this->request->data['ClientEmail'] as $key => $val) {
					$this->request->data['ClientEmail'][$key]['client_id'] = $this->clientData['client_id'];
					$this->request->data['ClientEmail'][$key]['staff_id'] = $this->clientData['id'];
				}
				if(!$this->ClientEmail->saveall($this->request->data['ClientEmail'])) {
					$error .= ' 予約通知先メールアドレスの保存に失敗しました メールアドレスの形式を確認してください';
				}
			}
			// save ClientCard
			$this->ClientCard->deleteall(array ('ClientCard.client_id' => $this->clientData['client_id']), false);
			if (!empty($this->request->data['ClientCard']['credit_card_id'])) {
				foreach ($this->request->data['ClientCard']['credit_card_id'] as $key => $val) {
					$clientCard[$key]['ClientCard']['staff_id']       = $this->clientData['id'];
					$clientCard[$key]['ClientCard']['client_id']      = $this->clientData['client_id'];
					$clientCard[$key]['ClientCard']['credit_card_id'] = $val;
				}
				$this->ClientCard->saveall($clientCard);
			}

			if(empty($error)) {
				$this->Session->setFlash('クライアント情報を編集しました。', 'default', array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash($error, 'default', array('class'=>'alert alert-error'));
			}
		}
	}
}
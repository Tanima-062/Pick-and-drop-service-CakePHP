<?php

App::uses('AppController', 'Controller');

class MailTemplatesController extends AppController {

	public $components = array('MailReplace');
	public $uses = array('MailTemplate', 'MailTemplateCategory', 'MailReplaceString', 'MailSendHistory', 'Reservation');

	public $unsentMailcount = 0;

	public function beforeFilter() {
		parent::beforeFilter();

		// カテゴリ
		$options = array(
			'fields' => array(
				'id',
				'name',
			),
			'conditions' => array(
				'delete_flg' => 0,
			),
			'order' => array('sort_no' => 'ASC', 'id' => 'ASC'),
			'recursive' => -1,
		);
		$mailCategoryList = $this->MailTemplateCategory->find('list', $options);
		$this->set('mailCategoryList', $mailCategoryList);

		// 送信メール編集中はテンプレート更新不可
		$options = array(
			'fields' => array(
				'id',
			),
			'conditions' => array(
				// Constant::bulkMailStatusの送信完了以外を取ってくる
				'send_status_id != ' => 3,
				'delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$this->unsentMailcount = $this->MailSendHistory->find('count', $options);
		$this->set('unsentMailcount', $this->unsentMailcount);
	}

	public function index() {
		$mailTemplateCategoryId = '';
		// 親テーブル以外のカラムは直接ソートできないのでvirtualFieldsで設定する
		$this->MailTemplate->virtualFields = array(
			'mail_template_category_sort_no' => 'MailTemplateCategory.sort_no',
			'mail_template_category_id' => 'MailTemplateCategory.id',
		);
		// テンプレート
		$conditions = array(
			'MailTemplate.delete_flg' => 0,
			'MailTemplateCategory.delete_flg' => 0,
		);
		if (!empty($this->request->query['mail_template_category_id'])) {
			$conditions['MailTemplate.mail_template_category_id'] = $this->request->query['mail_template_category_id'];
			$mailTemplateCategoryId = $this->request->query['mail_template_category_id'];
		}
		$this->set('mailTemplateCategoryId', $mailTemplateCategoryId);

		$this->paginate = array(
			'fields' => array(
				'MailTemplate.id',
				'MailTemplate.name',
				'MailTemplate.mail_from',
				'MailTemplate.mail_subject',
				'MailTemplate.mail_content',
				'MailTemplateCategory.name',
			),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'type' => 'INNER',
					'alias' => 'MailTemplateCategory',
					'table' => 'mail_template_categories',
					'conditions' => 'MailTemplate.mail_template_category_id = MailTemplateCategory.id',
				),
			),
			'order' => array(
				'mail_template_category_sort_no' => 'ASC',
				'mail_template_category_id' => 'ASC',
				'sort_no' => 'ASC',
				'id' => 'ASC',
			),
			'recursive' => -1,
		);
		$this->set('mailTemplateList', $this->paginate());
	}

	public function category_add() {
		if ($this->request->is('post')) {
			$this->MailTemplateCategory->create();
			$this->request->data['MailTemplateCategory']['create_staff_id'] = $this->cdata['id'];
			$this->request->data['MailTemplateCategory']['update_staff_id'] = $this->cdata['id'];
			$this->request->data['MailTemplateCategory']['create_datetime'] = date('Y-m-d H:i:s');
			$this->request->data['MailTemplateCategory']['update_datetime'] = date('Y-m-d H:i:s');
			if ($this->MailTemplateCategory->save($this->request->data)) {
				$this->Session->setFlash('カテゴリを追加しました。','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('カテゴリ登録に失敗しました。','default',array('class'=>'alert alert-error'));
			}
		}
	}

	public function mail_replace_string_list() {
		// 置換文字
		$MailReplaceStrings = $this->MailReplace->getReplaceList();
		if ($this->request->is('post') && !empty($this->request->data['MailTemplates']['reservationKey'])) {
			$reservationKey = $this->request->data['MailTemplates']['reservationKey'];
			$options = array(
				'fields' => array('Reservation.id'),
				'conditions' => array('reservation_key' => $reservationKey),
				'order' => array('Reservation.id'),
				'recursive' => -1,
			);
			$options['conditions'] = array('reservation_key' => $reservationKey);
			$reservationData = $this->Reservation->find('first', $options);
			$reservationId = $reservationData['Reservation']['id'];
			$replacePattern = $this->MailReplace->getReplacePattern($reservationId);
			// 取得できないパターンでも回避はせずエラーであることがわかるように空欄で表示する。
			foreach ($MailReplaceStrings as $key => $pattern) {
				$MailReplaceStrings[$key]['example'] = $replacePattern[$reservationId][$pattern['search_string']];
			}
		}
		$this->set('MailReplaceStrings', $MailReplaceStrings);
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->MailTemplate->create();
			$this->request->data['MailTemplate']['create_staff_id'] = $this->cdata['id'];
			$this->request->data['MailTemplate']['update_staff_id'] = $this->cdata['id'];
			$this->request->data['MailTemplate']['create_datetime'] = date('Y-m-d H:i:s');
			$this->request->data['MailTemplate']['update_datetime'] = date('Y-m-d H:i:s');
			if ($this->MailTemplate->save($this->request->data)) {
				$this->Session->setFlash('テンプレートを追加しました。','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('テンプレート登録に失敗しました。','default',array('class'=>'alert alert-error'));
			}
		}
	}

	public function edit($id=null) {
		if (empty($id)) {
			$this->redirect(array('action' => 'index'));
		}
		$options = array(
			'fields' => array(
				'MailTemplate.id',
				'MailTemplate.name',
				'MailTemplate.mail_from',
				'MailTemplate.mail_subject',
				'MailTemplate.mail_content',
				'MailTemplate.mail_template_category_id',
			),
			'conditions' => array(
				'MailTemplate.id' => $id,
				'MailTemplate.delete_flg' => 0,
			),
			'order' => array(
				'id' => 'ASC',
			),
			'recursive' => -1,
		);
		$mailTemplate = $this->MailTemplate->find('first', $options);
		if (empty($mailTemplate)) {
			$this->redirect(array('action' => 'index'));
		}
		$this->set('mailTemplate', $mailTemplate);

		if ($this->request->is('post')) {
			// 未送信メールのテンプレートを編集されると想定外の内容を送信する可能性があるため弾く
			if ($this->unsentMailcount > 0) {
				$this->Session->setFlash('テンプレート編集に失敗しました。<br>未送信メールがあるためテンプレート編集はできません。','default',array('class'=>'alert alert-error'));
				$this->redirect(array('action' => 'index'));
			}
			$this->MailTemplate->create();
			$this->MailTemplate->id = $id;
			$this->request->data['MailTemplate']['update_staff_id'] = $this->cdata['id'];
			$this->request->data['MailTemplate']['update_datetime'] = date('Y-m-d H:i:s');
			if ($this->MailTemplate->save($this->request->data)) {
				$this->Session->setFlash('テンプレートを編集しました。','default',array('class'=>'alert alert-success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('テンプレート編集に失敗しました。','default',array('class'=>'alert alert-error'));
			}
		}
	}

}

<?php
App::uses('AppController', 'Controller');
/**
 * MailSendHistories Controller
 *
 * @property MailSendHistory $MailSendHistory
 */
class MailSendHistoriesController extends AppController {

	public $components = array('MailReplace');
	public $uses = array('MailSendHistory', 'MailTemplateCategory', 'MailTemplate', 'MailReplaceString', 'MailSendTarget', 'Staff');

	function beforeFilter()
	{
		parent::beforeFilter();

		// 送信メールステータス
		$mailStatusList = Constant::bulkMailStatus();
		$this->set('mailStatusList', $mailStatusList);

		// 送信メール宛先ステータス
		$this->targetStatusList = Constant::targetMailStatus();

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
		$categoryList = $this->MailTemplateCategory->find('list', $options);
		$this->set('categoryList', $categoryList);

		// テンプレート
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
		$templateList = $this->MailTemplate->find('list', $options);
		$this->set('templateList', $templateList);

		$this->set('staffList', $this->Staff->find('list'));
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		if ($this->request->is('get')) {
			//getで来た場合 検索フォームの値を保持(ページャやソート)
			$this->request->data['MailSendHistories'] = array_diff_key($this->request->params['named'], array('sort' => 0, 'direction' => 0, 'page' => 0));
			$data = $this->request->params['named'];
		} else {
			$data = $this->request->data['MailSendHistories'];
		}
		// デフォルトの抽出条件は未送信とする
		if (empty($data)) {
			$data['send_status_id'] = '0';
			$this->request->data['MailSendHistories']['send_status_id'] = $data['send_status_id'];
		}

		$options['fields'] = array(
			'MailSendHistory.id',
			'MailSendHistory.mail_template_name',
			'MailSendHistory.send_status_id',
			'MailSendHistory.create_staff_id',
			'MailSendHistory.create_datetime',
			'MailSendHistory.update_staff_id',
			'MailSendHistory.send_start_datetime',
		);
		$options['order'] = 'MailSendHistory.id desc';

		$options['conditions'] = array('delete_flg' => '0');
		if (!empty($data['staff_id'])) {
			$options['conditions'] = array('MailSendHistory.staff_id' => $data['staff_id']);
		}
		if (!empty($data['from']['year']) && !empty($data['from']['month'])) {
			if (!empty($data['from']['day'])) {
				$options['conditions'] += array('MailSendHistory.send_start_datetime >=' => $data['from']['year'] . "-" . $data['from']['month'] . "-" . $data['from']['day'] . " 00:00:00");
			} else {
				$options['conditions'] += array('MailSendHistory.send_start_datetime >=' => $data['from']['year'] . "-" . $data['from']['month'] . "-01 00:00:00");
			}
		}

		if (!empty($data['to']['year']) && !empty($data['to']['month'])) {
			if (!empty($data['to']['day'])) {
				$options['conditions'] += array('MailSendHistory.send_start_datetime <=' => $data['to']['year'] . "-" . $data['to']['month'] . "-" . $data['to']['day'] . " 23:59:59");
			} else {
				$lastDay = date('t', strtotime($data['to']['year'] . '-' . $data['to']['month'] . '-01'));
				$options['conditions'] += array('MailSendHistory.send_start_datetime <=' => $data['to']['year'] . "-" . $data['to']['month'] . "-" . $lastDay . " 23:59:59");
			}
		}
		// ステータスだけ0が入る可能性あるため回避
		if (!empty($data['send_status_id']) || $data['send_status_id'] == '0') {
			$options['conditions'] += array('MailSendHistory.send_status_id' => $data['send_status_id']);
		}

		if (!empty($data['mail_template_name'])) {
			$options['conditions'] += array('MailSendHistory.mail_template_name like ' => '%' . $data['mail_template_name'] . '%');
		}

		$this->paginate = $options;

		$this->set('mailSendHistories', $this->paginate());
		$this->set('postConditions', $this->request->data['MailSendHistories']);
	}

	/**
	 * detail method
	 *
	 * @return void
	 */
	public function detail($id=null) {
		if (empty($id)) {
			$this->redirect(array('action' => 'index'));
		}
		$options = array(
			'fields' => array(
				'MailSendHistory.id',
				'MailSendHistory.mail_template_category_name',
				'MailSendHistory.mail_template_id',
				'MailSendHistory.mail_template_name',
				'MailSendHistory.mail_template_from',
				'MailSendHistory.mail_template_subject',
				'MailSendHistory.mail_template_content',
				'MailSendHistory.send_status_id',
				'MailSendHistory.send_start_datetime',
				'MailSendHistory.send_end_datetime',
				'MailSendHistory.create_datetime',
				'MailSendHistory.create_staff_id',
				'MailSendHistory.update_datetime',
				'MailSendHistory.update_staff_id',
			),
			'conditions' => array(
				'MailSendHistory.id' => $id,
				'MailSendHistory.delete_flg' => 0,
			),
			'order' => array(
				'MailSendHistory.id' => 'ASC',
			),
			'recursive' => -1,
		);
		$mailSendHistory = $this->MailSendHistory->find('first', $options);
		if (empty($mailSendHistory)) {
			$this->redirect(array('action' => 'index'));
		}
		$this->set('mailSendHistory', $mailSendHistory);

		$options = array(
			'fields' => array(
				'MailSendHistory.id',
			),
			'conditions' => array(
				'MailSendHistory.id !=' => $id,
				'MailSendHistory.send_status_id' => '1',
				'MailSendHistory.delete_flg' => 0,
			),
			'recursive' => -1,
		);
		$waitMailSendCount = $this->MailSendHistory->find('count', $options);
		$this->set('waitMailSendCount', $waitMailSendCount);

                if ($this->request->is('post')) {
			// 入力不備系はreturn,処理エラー系はredirect
			// 送信対象DL
			if (!empty($this->request->data['getTargetCsv']) && $this->request->data['getTargetCsv'] == '1') {
				$this->autoRender = false; // Viewを使わない
				$options = array(
					'fields' => array(
						'Reservation.reservation_key',
						'MailSendTarget.last_name',
						'MailSendTarget.first_name',
						'MailSendTarget.email',
						'MailSendTarget.send_flg',
					),
					'conditions' => array(
						'MailSendTarget.mail_send_history_id' => $id,
					),
					'joins' => array(
						array(
							'type' => 'LEFT',
							'alias' => 'Reservation',
							'table' => 'reservations',
							'conditions' => 'MailSendTarget.reservation_id = Reservation.id',
						),
					),
					'order' => array(
						'MailSendTarget.id' => 'ASC',
					),
					'recursive' => -1,
				);

                		$count = $this->MailSendTarget->find('count', $options);
                		$limit = 5000;
                		$loop  = ceil($count / $limit);

                		if ($count > 0) {
					$fileName = $id . '_' . date('YmdHis') . '.csv';
					$pathFile = TMP.$fileName;
					$csvFile = fopen(TMP.$fileName, "w") or die("Unable to open file!");
					stream_filter_prepend($csvFile, 'convert.iconv.utf-8/cp932//TRANSLIT');
                		        // ヘッダーを書き込む
                		        $csvData = '予約番号,氏名カナ,メールアドレス,送信状況' . "\r\n";
                		        fwrite($csvFile, $csvData);

					for ($i = 0; $i < $loop; $i++){
						$options['limit'] = $limit;
						$options['offset'] = $limit * $i;
						$mailSendTargetData = $this->MailSendTarget->find('all', $options);
						foreach ($mailSendTargetData as $key => $val) {
							$csvData = $val['Reservation']['reservation_key'] . ','
								. $val['MailSendTarget']['last_name'] . ' ' . $val['MailSendTarget']['first_name'] . ','
								. $val['MailSendTarget']['email'] . ','
								. $this->targetStatusList[$val['MailSendTarget']['send_flg']] . ',' . "\r\n";

							fwrite($csvFile, $csvData);
						}

					}
					fclose($csvFile);
					header("Content-disposition: attachment; filename=" . $fileName);
					header("Content-type: application/octet-stream; name=" . $fileName);
					readfile($pathFile);
					unlink ($pathFile);
				}
				exit();

			// メール送信
			} elseif (!empty($this->request->data['submitHistory']) && $this->request->data['submitHistory'] == '1') {
				if (!empty($waitMailSendCount) && $waitMailSendCount > 0) {
					$this->Session->setFlash('別のメールが送信中のため、送信できません。','default',array('class'=>'alert alert-error'));
					return false;
				}
				$mailTemplateId = $this->request->data['MailSendHistory']['mail_template_id'];
				if (empty($mailTemplateId)) {
					$this->Session->setFlash('送信内容を選択してください。','default',array('class'=>'alert alert-error'));
					return false;
				} else {
					$options = array(
						'fields' => array(
							'MailTemplate.id',
							'MailTemplate.name',
							'MailTemplate.mail_from',
							'MailTemplate.mail_subject',
							'MailTemplate.mail_content',
							'MailTemplateCategory.name',
						),
						'conditions' => array(
							'MailTemplate.id' => $mailTemplateId,
							'MailTemplate.delete_flg' => 0,
						),
						'joins' => array(
							array(
								'type' => 'INNER',
								'alias' => 'MailTemplateCategory',
								'table' => 'mail_template_categories',
								'conditions' => 'MailTemplate.mail_template_category_id = MailTemplateCategory.id',
							),
						),
						'order' => array(
							'MailTemplate.id' => 'ASC',
						),
						'recursive' => -1,
					);
					$templateData = $this->MailTemplate->find('first', $options);
					if (empty($templateData)) {
						$this->Session->setFlash('選択された送信内容に不備がありました。<br>テンプレートが削除されていないかご確認ください。','default',array('class'=>'alert alert-error'));
						return false;
					}
				}
				// 履歴を「メール送信中」に更新
				$this->MailSendHistory->create();
				$this->MailSendHistory->id = $id;
				$this->request->data['MailSendHistory']['mail_template_category_name'] = $templateData['MailTemplateCategory']['name'];
				$this->request->data['MailSendHistory']['mail_template_id'] = $templateData['MailTemplate']['id'];
				$this->request->data['MailSendHistory']['mail_template_name'] = $templateData['MailTemplate']['name'];
				$this->request->data['MailSendHistory']['mail_template_from'] = $templateData['MailTemplate']['mail_from'];
				$this->request->data['MailSendHistory']['mail_template_subject'] = $templateData['MailTemplate']['mail_subject'];
				$this->request->data['MailSendHistory']['mail_template_content'] = $templateData['MailTemplate']['mail_content'];
				$this->request->data['MailSendHistory']['send_status_id'] = '1';
				$this->request->data['MailSendHistory']['send_start_datetime'] = date('Y-m-d H:i:s');
				$this->request->data['MailSendHistory']['update_staff_id'] = $this->cdata['id'];
				$this->request->data['MailSendHistory']['update_datetime'] = date('Y-m-d H:i:s');
				if ($this->MailSendHistory->save($this->request->data)) {
					exec("php /var/www/skyticket.com/rentacar/admin/Console/cake.php SendBulkMail main '". $id ."' -app /var/www/skyticket.com/rentacar/admin/ > /dev/null 2>&1 &");
					$this->Session->setFlash('メール送信処理を開始しました。','default',array('class'=>'alert alert-success'));
				} else {
					$this->Session->setFlash('メール送信処理に失敗しました。','default',array('class'=>'alert alert-error'));
				}
			// 宛先削除
			} elseif (!empty($this->request->data['deleteHistory']) && $this->request->data['deleteHistory'] == '1') {
				$this->MailSendHistory->create();
				$this->MailSendHistory->id = $id;
				$this->request->data['MailSendHistory']['delete_flg'] = '1';
				$this->request->data['MailSendHistory']['update_staff_id'] = $this->cdata['id'];
				$this->request->data['MailSendHistory']['update_datetime'] = date('Y-m-d H:i:s');
				if ($this->MailSendHistory->save($this->request->data)) {
					$this->Session->setFlash('宛先を削除しました。','default',array('class'=>'alert alert-success'));
				} else {
					$this->Session->setFlash('宛先の削除に失敗しました。','default',array('class'=>'alert alert-error'));
				}
			}
			$this->redirect(array('action' => 'index'));
		}
	}

	/**
	 * getTemplateList method
	 *
	 * @return json
	 */
	public function getTemplateList($templateId=null) {
		$this->autoRender = false;
		$templateList = '<option value="">---</option>';

		if (empty($templateId)) {
			$conditions = array('delete_flg' => 0);
		} else {
			$conditions = array(
				'mail_template_category_id' => $templateId,
				'delete_flg' => 0
			);
		}
		$options = array(
			'fields' => array(
				'id',
				'name',
			),
			'conditions' => $conditions,
			'order' => array('sort_no' => 'ASC', 'id' => 'ASC'),
			'recursive' => -1,
		);
		$result = $this->MailTemplate->find('list', $options);
		if (!empty($result)) {
			foreach ($result as $id => $name) {
				$templateList .= '<option value="' . $id .'">' . htmlspecialchars($name) . '</option>';
			}
		}

		return $templateList;
	}

	/**
	 * getTemplate method
	 *
	 * @return json
	 */
	public function getTemplate($templateId=null, $id=null) {
		$this->autoRender = false;
		$options = array(
			'fields' => array(
				'mail_from',
				'mail_subject',
				'mail_content',
			),
			'conditions' => array(
				'id' => $templateId,
				'delete_flg' => 0,
			),
			'order' => array('id' => 'ASC'),
			'recursive' => -1,
		);
		$result = $this->MailTemplate->find('first', $options);

		$options = array(
			'fields' => array(
				'MailSendTarget.reservation_id',
			),
			'conditions' => array(
				'MailSendTarget.mail_send_history_id' => $id,
			),
			'order' => array(
				'MailSendTarget.id' => 'ASC',
			),
			'recursive' => -1,
		);

                $reservationId = $this->MailSendTarget->find('first', $options);
		if (!empty($result['MailTemplate'])) {
			$templateData['mail_from'] = $result['MailTemplate']['mail_from'];
			// 置換パターン呼び出し
			$replacePattern = $this->MailReplace->getReplacePattern($reservationId['MailSendTarget']['reservation_id']);
			$targetReplacePattern = $replacePattern[$reservationId['MailSendTarget']['reservation_id']];
			if (!empty($targetReplacePattern)) {
				$templateData['mail_subject'] = $this->MailReplace->mailReplace($result['MailTemplate']['mail_subject'], $targetReplacePattern);
				$result['MailTemplate']['mail_content'] = $this->MailReplace->mailReplace($result['MailTemplate']['mail_content'], $targetReplacePattern);
				$templateData['mail_content'] = str_replace("\n", "<br/>", $result['MailTemplate']['mail_content']);
			} else {
				$templateData['mail_subject'] = $result['MailTemplate']['mail_subject'];
				$templateData['mail_content'] = str_replace("\n", "<br/>", $result['MailTemplate']['mail_content']);
			}
		}

		return json_encode($templateData);
	}
}

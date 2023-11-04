<?php
App::uses('AppShell', 'Console/Command');
App::uses('ComponentCollection', 'Controller');
App::uses('MailReplaceComponent', 'Controller/Component');
App::uses('SkyticketCakeEmail', 'Vendor');
require_once("notice_class.php");
require_once("encrypt_class.php");

class SendBulkMailShell extends AppShell {

	public $components = array('MailReplace');

	public $uses = array('MailSendHistory', 'MailSendTarget', 'Reservation', 'MessageBoard');

	public function startup() {
		$collection = new ComponentCollection();
		$this->MailReplace = new MailReplaceComponent($collection);
		$this->MailReplace->initialize($this);
		parent::startup();
	}

	public function main() {
		// 対象フィールドを暗号化
		$encrypt = new Encrypt();

		// 一度に処理する件数
		// 予約ごとに置換文字を作るためメモリ節約用の制限
		$limit = '100';
		try {
			$argCount = count($this->args);
			if ($argCount != 1) {
				throw new Exception("送信処理パラメータ数エラー (count = $argCount)");
			}
			$id = $this->args[0];
			// 送信内容抽出
			$options = array(
				'fields' => array(
					'id',
					'mail_template_category_name',
					'mail_template_name',
					'mail_template_from',
					'mail_template_subject',
					'mail_template_content',
					'update_staff_id',
				),
				'conditions' => array(
					'id' => $id,
					'send_status_id' => array('1', '2'), // bulkMailStatusの未送信と完了以外が対象
					'delete_flg' => 0,
				),
				'order' => array(
					'id' => 'ASC',
				),
				'recursive' => -1,
			);
			$sendMailData = $this->MailSendHistory->find('first', $options);
			if (empty($sendMailData)) {
				throw new Exception("送信テンプレートが見つかりませんでした。");
			}

			// 送信対象件数確認
			$options = array(
				'fields' => array(
					'id',
					'reservation_id',
				),
				'conditions' => array(
					'mail_send_history_id' => $id,
					'send_flg' => 0,
				),
				'order' => array(
					'id' => 'ASC',
				),
				'recursive' => -1,
			);
			$total = $this->MailSendTarget->find('count', $options);
			if (empty($total)) {
				throw new Exception("送信対象が0件です。");
			}
			$lastPage = max((int) ceil($total / $limit), 1);

			for ($i = 1; $i <= $lastPage; $i++) {
				// 送信対象抽出
				$options['limit'] = $limit;
				$sendMailTargets = $this->MailSendTarget->find('list', $options);
				if (empty($sendMailTargets)) {
					throw new Exception("送信対象が見つかりませんでした。");
				}

				// 送信対象宛先抽出
				$targetOptions = array(
					'fields' => array(
						'id',
						'last_name',
						'first_name',
						'email',
					),
					'conditions' => array(
						'id' => $sendMailTargets,
					),
					'order' => array(
						'id' => 'ASC',
					),
					'recursive' => -1,
				);
				$sendMailTargetData = $this->Reservation->find('all', $targetOptions);
				if (empty($sendMailTargetData)) {
					throw new Exception("送信対象の情報が取得できませんでした。");
				}

				// 置換パターン呼び出し
				$targetReservationIds = Hash::extract($sendMailTargetData, '{n}.Reservation.id');
				$replacePattern = $this->MailReplace->getReplacePattern($targetReservationIds);

				// 送信処理
				foreach ($sendMailTargetData as $key => $reservation) {
					// 置換処理
					$targetReplacePattern = $replacePattern[$reservation['Reservation']['id']];

					if (!empty($targetReplacePattern)) {
						$subject = $this->MailReplace->mailReplace($sendMailData['MailSendHistory']['mail_template_subject'], $targetReplacePattern);
						$content = $this->MailReplace->mailReplace($sendMailData['MailSendHistory']['mail_template_content'], $targetReplacePattern);
					} else {
						$subject = $sendMailData['MailSendHistory']['mail_template_subject'];
						$content = $sendMailData['MailSendHistory']['mail_template_content'];
					}
					// 継承してメールクラスを利用
					$email = new SkyticketCakeEmail('smtp');
					$email
						->from($sendMailData['MailSendHistory']['mail_template_from'])
						->subject($subject)
						->to(trim($reservation['Reservation']['email']))
						->send($content);

					// メール送信完了として更新する
					$setData = array(
						'MailSendTarget.last_name' => "'" . $encrypt->encrypt($reservation['Reservation']['last_name']) . "'",
						'MailSendTarget.first_name' => "'" . $encrypt->encrypt($reservation['Reservation']['first_name']) . "'",
						'MailSendTarget.email' => "'" . $encrypt->encrypt($reservation['Reservation']['email']) . "'",
						'MailSendTarget.send_flg' => "'1'",
						'MailSendTarget.send_datetime' => "'" .date('Y-m-d H:i:s') . "'",
					);
					$conditions = array('MailSendTarget.reservation_id' => $reservation['Reservation']['id'], 'MailSendTarget.mail_send_history_id' => $id);
					// 送信した事実は変更できないためトランザクションしない
					if (!$this->MailSendTarget->updateAll($setData, $conditions)) {
						$this->log("update send_mail_targets error " . print_r($conditions,true), LOG_INFO);
					}

					$this->MessageBoard->create();
					$this->MessageBoard->save(array(
						'reservation_id' => $reservation['Reservation']['id'],
						'category_cd' => 'RESERVATION_DETAIL',
						'message' => $sendMailData['MailSendHistory']['mail_template_category_name'] . 'の' . $sendMailData['MailSendHistory']['mail_template_name'] . 'で一斉メール送信',
						'staff_id' => $sendMailData['MailSendHistory']['update_staff_id']
					));
				}
			}
		} catch (Exception $e) {
			// 送信処理自体は実行してしまっているのでrollbackはしない
			$this->log($e->getMessage(), LOG_ERROR);
			$this->log($e->getTraceAsString(), LOG_ERROR);
			$errMsg[] = $e->getMessage();
		}

		// 送信状況カウント
		// cakephpではbooleanが「0」と表示されないため無理やり「0」出力する
		$this->MailSendTarget->virtualFields = array("send_flg" => "CASE send_flg WHEN '0' THEN '0' ELSE '1' END", "count" => "0");
		$options = array(
			'fields' => array(
				'send_flg',
				'count(id) as MailSendTarget__count',
			),
			'conditions' => array(
				'mail_send_history_id' => $id,
			),
			'group' => array(
				'send_flg',
			),
			'recursive' => -1,
		);
		$result = $this->MailSendTarget->find('all', $options);
		$sendMailCount = Hash::combine($result, '{n}.MailSendTarget.send_flg', '{n}.MailSendTarget.count');
		if (empty($sendMailCount[0])) {
			// 全部正常に送信完了
			$sendStatusId = '3';
		} else {
			// 正常に送信完了できなかった
			$sendStatusId = '2';
		}
		// 履歴更新
		$setData = array(
			'MailSendHistory.send_status_id' => "'" . $sendStatusId . "'",
			'MailSendHistory.send_end_datetime' => "'" .date('Y-m-d H:i:s') . "'",
		);
		$conditions = array('MailSendHistory.id' => $id);
		if (!$this->MailSendHistory->updateAll($setData, $conditions)) {
			$errMsg[] = "送信履歴の更新に失敗しました。ID：$id";
		}

		// slackに処理終了の通知をする(成否問わず)
		$title = '一斉メール送信';
		$message = "履歴ID：" . $id . " の送信処理が完了しました。"
			. "\n送信成功：" . (!empty($sendMailCount[1]) ? $sendMailCount[1] : '0') . " 件"
			. "\n送信失敗：" . (!empty($sendMailCount[0]) ? $sendMailCount[0] : '0') . " 件";

		if ($sendStatusId == '2') {
			$message .= "\n全てのメールが正常に送信できていないため、管理画面より再送信を実行してください。";
		}
		if (!empty($errMsg)) {
			$message .= "\n" . implode("\n", $errMsg);
		}
		$this->notice($message, $title);
	}

	private function notice($message, $subject) {
		$room_id = 41382981; // レンタカー開発チーム(社内)

		$notice = new Notice($room_id);
		$ret = $notice->exec_notice($message, $subject);
		if (!$ret) {
			$this->log(sprintf("通知エラー([%s]%s)", $subject, $message), LOG_ERROR);
		}
	}

}

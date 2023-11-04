<?php

require_once('const/common_const.php');
require_once('config/mailsend_conf.php');

class DeliveryMail extends AppModel {

	//var $mailbox  = '{'.MAILSEND_HOST.':110/pop3/notls}Maildir';
	var $host = 'mail.skyticket.jp';
	var $username = 'info@skyticket.jp';
	var $password = '3ha5ezb';
	var $subject = "/Returned/";

	function __construct() {
		parent::__construct();

		$mail_conf = new MailSendConf();
		$mail_conf->load();
		$this->host = $mail_conf->getMailSendHost();
		$this->username = $mail_conf->getMailSendAuthUser();
		$this->password = $mail_conf->getMailSendAuthPass();
	}
	
	function saveReturnMailInfo() {

		$this->mailbox  = '{'.$this->host.':110/pop3/notls}Maildir';

		$maxMsgno = $this->getMaxMsgno();

		set_time_limit(0);
		$mbox = imap_open($this->mailbox, $this->username, $this->password);

		$mailCheck = imap_check($mbox);

		$result = array();
		for($i=$maxMsgno+1;$i<=$mailCheck->Nmsgs;$i++) {
			$headinfo  = imap_headerinfo($mbox, $i);

			$bodyinfo  = imap_body($mbox, $i);
			if (isset($headinfo->Subject) && preg_match($this->subject,$headinfo->Subject)) {

				if (preg_match('/(\w+[-+\w.]+@[-\w.]+\.\w{2,5})/', $bodyinfo, $rslt)) {

					array_push($result,array(
							'send_datetime'=>date('Y-m-d H:i:s',strtotime($headinfo->date)),
							'email'=>$rslt[0],
							'msg_no'=>$headinfo->Msgno));
				}
			}
		}
		imap_close($mbox);

		if (!empty($result)) {
			$this->saveMany($result);
		}
	}

	public function getMaxMsgno() {

		$options = array(
				'fields' => array(
						'MAX(msg_no) as max_no'
				),
				'recursive' => -1
		);

		$mails = $this->find('first',$options);

		if (empty($mails)) {
			return 0;
		} else {
			return $mails[0]['max_no'];
		}
	}

	public function checkReturnMail($data) {

		$maxSendDatetime = "(
				SELECT
					id,
					MAX(send_datetime) as max_send_datetime
				FROM
					delivery_mails
				GROUP BY
					email)";

		$options = array(
				'fields' => array(
						'*'
				),
				'joins' => array(
						array(
								'type' => 'INNER',
								'alias' => 'MaxTbl',
								'table' => $maxSendDatetime,
								'conditions' => 'MaxTbl.id = DeliveryMail.id'
						)
				),
				'conditions' => array(
						'email' => $data['email'],
						'max_send_datetime >' => $data['created']
				),
				'group' => 'DeliveryMail.email',
				'recursive' => -1
		);

		$result = $this->find('all',$options);
		if (!empty($result)) {
			return true;
		} else {
			return false;
		}
	}
}
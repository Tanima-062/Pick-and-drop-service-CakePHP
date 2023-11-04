<?php

class DeliveryMail extends AppModel {

	var $mailbox  = "";
	var $username = '';
	var $password = '';
	var $subject = "Returned mail: see transcript for details";

	public function getAll() {

		$options = array(
				'fields' => array(
						'MAX(send_datetime) as send_datetime',
						'*'
				),
				'group' => 'email',
				'recursive' => -1
		);

		$mails = $this->find('all',$options);

		if (empty($mails)) {
			return false;
		} else {
			return $mails;
		}
	}

	public function getPaginateOptions($query) {

		$this->virtualFields['max_send_datetime'] = 'MAX(send_datetime)';
		$options = array(
				'fields' => array(
						'*'
				),
				'group' => 'email',
				'order' => 'DeliveryMail__max_send_datetime DESC',
				'limit' => 50,
				'recursive' => -1
		);

		if (!empty($query['email'])) {
			$options['conditions']['email LIKE'] = "%".$query['email']."%";
		}

		return $options;
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
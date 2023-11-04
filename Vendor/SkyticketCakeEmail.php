<?php
App::uses('CakeEmail', 'Network/Email');
require_once("mailsend_class.php");

class SkyticketCakeEmail extends CakeEmail {
	public $cm_application_id = 0;
	public $reservation_id = 0;
	public $user_id = 0;
	public $service_cd = SERVICE_CD_RC;
	public $send_flg = MailSend::SEND_FLG_SUCCESS_CD;
	public $mail_status = MailSend::AUTO_MAIL_STATUS;
	public $non_show_user_flg = 0;
	
	public function setMailLogDataForSkyticket($reservation_id=0) {
		if (empty($reservation_id)) {
			return false;
		}
		$this->reservation_id = $reservation_id;
		
		$sql = "
			SELECT
				cta.user_id,
				ctad.cm_application_id,
				ctad.application_id
			FROM skyticket.cm_th_application_detail AS ctad
			LEFT JOIN skyticket.cm_th_application AS cta
			ON cta.cm_application_id = ctad.cm_application_id
			WHERE ctad.application_id = :_application_id AND ctad.service_cd = :_service_cd
		";
		$param_arr = array(
			':_application_id'	=> $reservation_id,
			':_service_cd'		=> SERVICE_CD_RC,
		);
		$db = GetDBInstance(DB_MAIN_MASTER);    // master DB
		$data_list = $db->executeFetchAll($sql, $param_arr);
		if (empty($data_list[0])) {
			return false;
		}
		$db = null;
		$this->cm_application_id = !empty($data_list[0]['cm_application_id']) ? $data_list[0]['cm_application_id'] : 0;
		$this->user_id = !empty($data_list[0]['user_id']) ? $data_list[0]['user_id'] : 0;
		return true;
	}
	
	public function send($content = null) {
//		$contents = parent::send($content);
		$contents = $this->getContents($content);

		try {
			$message = "";
			if (!empty($contents['message'])) {
				$message = $contents['message'];
			}
			list($to, $subject, $from) = $this->_get_mail_params();
			$add_header_arr['From'] = $from;
			$add_header_arr['charset'] = 'ISO-2022-JP';

			$mail_log_data = array(
				'cm_application_id'	=> $this->cm_application_id,
				'application_id'	=> $this->reservation_id,
				'user_id'			=> $this->user_id, 
				'service_cd'		=> $this->service_cd,
//				'send_flg'			=> $this->send_flg,
				'mail_status'		=> $this->mail_status,
				'non_show_user_flg'	=> $this->non_show_user_flg,
			);
			$attachment_arr = array();
			$mailsend = new MailSend();
			$mailsend->sendAndSave($to, $subject, $message, $attachment_arr, $add_header_arr, array(), $mail_log_data);
			$mailsend = null;
		} catch (Exception $e) {
			CakeLog::write(LOG_ERROR, "Failed insert cm_th_mail_log\r\n".$e->getMessage());
		}
		return $contents;
	}

	// CakeEmail sendメソッドのコピー
	// $this->transportClass()->send($this)はコードから直接メール発信しているため予期せぬ不具合が生じる
	// そのためここではメールのコンテンツを取得するだけにして、skyticketと同じ送信方式で送信する
	public function getContents($content = null) {
		if (empty($this->_from)) {
			throw new SocketException(__d('cake_dev', 'From is not specified.'));
		}
		if (empty($this->_to) && empty($this->_cc) && empty($this->_bcc)) {
			throw new SocketException(__d('cake_dev', 'You need to specify at least one destination for to, cc or bcc.'));
		}

		if (is_array($content)) {
			$content = implode("\n", $content) . "\n";
		}

		$this->_textMessage = $this->_htmlMessage = '';
		$this->_createBoundary();
		$this->_message = $this->_render($this->_wrap($content));

		$contents = $this->transportClass()->getContents($this);
		return $contents;
	}

	private function _get_mail_params() {
		$reservation_id = 0;
		$resavation_data_array = $this->_get_member_variable('viewVars');
		if (!empty($resavation_data_array['reservation_id'])) {
			$reservation_id = $resavation_data_array['reservation_id'];
			$this->setMailLogDataForSkyticket($reservation_id);
		}
		
		$to = $this->_get_first_key_member_variable('to');
		$from = $this->_get_first_key_member_variable('from');
		$subject = mb_decode_mimeheader($this->_get_member_variable('subject'));
		return array($to, $subject, $from);
	}
	
	private function _get_member_variable($variable_name) {
		if (!method_exists(parent::class, $variable_name)) {
			return false;
		}
		return parent::$variable_name();
	}
	
	private function _get_first_key_or_value($array, $is_value=true) {
		if(!is_array($array) || empty($array)) {
			return false;
		}
		foreach ($array AS $k => $v) {
			if ($is_value) {
				return $v;
			}
			return $k;
			break;
		}
		return false;
	}
	
	private function _get_first_key_member_variable($variable_name) {
		$array = $this->_get_member_variable($variable_name);
		return $this->_get_first_key_or_value($array, false);
	}
}

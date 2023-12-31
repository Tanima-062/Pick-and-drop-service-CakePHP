<?php
/**
 * This is email configuration file.
 *
 * Use it to configure email transports of Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * In this file you set up your send email details.
 *
 * @package       cake.config
 */
/**
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *		Mail 		- Send using PHP mail function
 *		Smtp		- Send using SMTP
 *		Debug		- Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email.  Transports should be named 'YourTransport.php',
 * where 'Your' is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 *
 */

require_once('const/common_const.php');
require_once('config/mailsend_conf.php');

class EmailConfig {

	public $default = array(
		'transport' => 'Mail',
		'from' => EMAIL_ADDRESS_RENTACAR,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	public $smtp = array(
			'protocol'=>'SMTP_AUTH',
			'from' => array(EMAIL_ADDRESS_RENTACAR => 'スカイチケット'),
			//'additionalParameters'=> Constant::EMAIL_ADDITIONALPARAMETERS,
			'subject' => '予約完了のお知らせ',
			'timeout' => 30,
			'log' => true,
			'charset' => 'UTF-8',
			'headerCharset' => 'UTF-8',
	);

	public $fast = array(
		'from' => 'you@localhost',
		'sender' => null,
		'to' => null,
		'cc' => null,
		'bcc' => null,
		'replyTo' => null,
		'readReceipt' => null,
		'returnPath' => null,
		'messageId' => true,
		'subject' => null,
		'message' => null,
		'headers' => null,
		'viewRender' => null,
		'template' => false,
		'layout' => false,
		'viewVars' => null,
		'attachments' => null,
		'emailFormat' => null,
		'transport' => 'Smtp',
		'host' => 'localhost',
		'port' => 25,
		'timeout' => 30,
		'username' => 'user',
		'password' => 'secret',
		'client' => null,
		'log' => true,
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);

	function __construct() {
		$mail_conf = new MailSendConf();
		$mail_conf->load();
		$this->smtp['host'] = $mail_conf->getMailSendHost();
		$this->smtp['port'] = $mail_conf->getMailSendPort();
		$this->smtp['username'] = $mail_conf->getMailSendAuthUser();
		$this->smtp['password'] = $mail_conf->getMailSendAuthPass();
	}
}

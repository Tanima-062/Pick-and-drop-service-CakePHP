<?php

App::uses('Component', 'Controller');

class GoogleRecaptchaComponent extends Component {

	private $url = 'https://www.google.com/recaptcha/api/siteverify';
	private $secretKey = '';

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->secretKey = IS_PRODUCTION ? constant::RECAPTCHA_SECRET_KEY_PROD : constant::RECAPTCHA_SECRET_KEY_DEV;
	}

	public function verifyToken($token)
	{
		list($json, $errno) = $this->callRecaptchaApi($token);
		if ($errno !== CURLE_OK) {
			return true;
		}

		$response = json_decode($json, true);
		if ($response['success']) {
			if ($response['score'] >= constant::RECAPTCHA_BOT_THRESHOLD) {
				return true;
			}
		}
		return false;
	}

	private function callRecaptchaApi($token) {
		$ch = curl_init($this->url);

		curl_setopt_array($ch, array(
			CURLOPT_POST			 => true,
			CURLOPT_POSTFIELDS		 => http_build_query(array('secret' => $this->secretKey, 'response' => $token)),
			CURLOPT_RETURNTRANSFER	 => true,
			CURLOPT_FAILONERROR		 => true,
			CURLOPT_TIMEOUT			 => 1,
		));

		$json = curl_exec($ch);
		$errno = curl_errno($ch);

		curl_close($ch);

		return array($json, $errno);
	}
}

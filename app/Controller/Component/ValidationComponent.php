<?php

App::uses('Component', 'Controller');

class ValidationComponent extends Component {

	private $controller = null;

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	// 個人情報
	public function validatePersonalInfo($data) {
		$result = true;

		// 氏名
		$nameError = false;
		$last_name = $data['last_name'];
		if (empty($last_name) || !preg_match("/^[ァ-ヶー]+$/u", $last_name)) {
			$result = false;
			$nameError = true;
		}
		$first_name = $data['first_name'];
		if (empty($first_name) || !preg_match("/^[ァ-ヶー]+$/u", $first_name)) {
			$result = false;
			$nameError = true;
		}
		if ($nameError) {
			$this->setSession('message.first_name', '氏名（カナ）は全角カタカナで入力してください。');
		}

		// 電話番号
		$tel = $data['tel'];
		if (empty($tel) || !ctype_digit($tel)) {
			$result = false;
			$this->setSession('message.tel', '電話番号を入力してください。');
		}

		// メール
		$email = $data['email'];
		if (empty($email)) {
			$result = false;
			$this->setSession('message.email', 'メールアドレスを入力してください。');
		} else if (!Validation::email($email)) {
			$result = false;
			$this->setSession('message.email', 'メールアドレスの入力形式が間違っています。<br>「..」、「.@」、「スペース（空白）」が含まれるアドレスは使用できません。<br>');
		}

		return $result;
	}

	// 制御文字除去
	public function removeControlChars($text) {
		return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
	}

	// セッション書込
	private function setSession($key, $value) {
		if (isset($this->controller->Session)) {
			$this->controller->Session->write($key, $value);
		}
	}
}

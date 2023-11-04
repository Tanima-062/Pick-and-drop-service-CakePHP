<?php

App::uses('Component', 'Controller');

class BotMeasuresComponent extends Component {

	public $components = array('GoogleRecaptcha');
	private $controller = null;
	private $sessKey = 'jsc5';
	private $siteKey = '';

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->GoogleRecaptcha->initialize($controller);
		$this->siteKey = IS_PRODUCTION ? constant::RECAPTCHA_SITE_KEY_PROD : constant::RECAPTCHA_SITE_KEY_DEV;
	}

	// JSチャレンジを実行する
	public function jsChallenge($session, $request) {
		// 一度NGになったセッションは通さない
		if ($session->check($this->sessKey . '.block')) {
			session_write_close();
			usleep(100000);
			exit;
		}

		// 認証済みの場合は処理しない
		if ($session->check($this->sessKey . '.challenged')) {
			$ngCnt = $session->read($this->sessKey . '.cnt');

			if ($request->clientIp() != $session->read($this->sessKey . '.ip')) {
				$session->write($this->sessKey . '.ip', $request->clientIp());
				$ngCnt++;
			}

			if (env('HTTP_USER_AGENT') != $session->read($this->sessKey . '.ua')) {
				$session->write($this->sessKey . '.ua', env('HTTP_USER_AGENT'));
				$ngCnt++;
			}

			$session->write($this->sessKey . '.cnt', $ngCnt);

			// IP、UAコロコロ変わってるやつ暫定ブロック
			if ($ngCnt > 3) {
				// $session->write($this->sessKey . '.block', true);
				// ログを取る
				$session->write($this->sessKey . '.cnt', 0);
				//error_log(print_r($session->read($this->sessKey), true), 3, LOGS . date('Ymd') . '_block.log');
			}

			return true;
		}

		// パラメータ名を取得出来ない場合は動的に生成する
		$key = ($session->check($this->sessKey . '.key')) ? $session->read($this->sessKey . '.key') : md5(uniqid('', true));

		// トークンが送信された場合は検証する
		if ($session->check($this->sessKey . '.token') && !empty($request->data[$key])) {
			$token = $session->read($this->sessKey . '.token');

			// 正しいトークンの場合は認証済みにしてリダイレクトする
			$tmpToken = (int)($token . $this->luhn($token));
			$tmpToken = (int)($tmpToken . $this->luhn($tmpToken));
			if (($tmpToken . $this->luhn($tmpToken)) == $request->data[$key]) {
				$session->write($this->sessKey . '.challenged', true);
				$session->write($this->sessKey . '.ip', $request->clientIp());
				$session->write($this->sessKey . '.ua', env('HTTP_USER_AGENT'));
				$session->write($this->sessKey . '.cnt', 0);
				$session->renew();
				$this->controller->redirect(str_replace('/rentacar', '', $request->here()), 303);
			}
		}

		// 未認証の場合はチャレンジページを表示する
		$token = mt_rand();
		$session->write($this->sessKey, array(
			'key' => $key,
			'token' => $token,
		));

		$this->renderChallenge($key, $token);
	}

	// luhnアルゴリズムのチェックディジットを求める
	private function luhn($number) {
		if (!is_numeric($number)) {
			return null;
		}

		$arr = array_reverse(str_split(strval($number)));
		$sum = 0;

		foreach ($arr as $k => $v) {
			$v = (int)$v;
			if (($k % 2) == 0) {
				$sum += ($v >= 5) ? $v * 2 - 9 : $v * 2;
			} else {
				$sum += $v;
			}
		}

		$mod = $sum % 10;
		return ($mod > 0) ? 10 - $mod : 0;
	}

	private function renderChallenge($key, $token) {
		// 偽キー
		$keys = array(
			md5(uniqid('', true)),
			md5(uniqid('', true)),
			md5(uniqid('', true)),
		);
		// 偽トークン
		$values = array(
			mt_rand(),
			mt_rand(),
			mt_rand(),
		);
		// ランダムで本物キーをセット
		$rnd = mt_rand(0, 2);
		// $keys[$rnd] = $key;
		// $values[$rnd] = $token;

		$res = <<<EOF
<html>
	<head>
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
		<meta name="robots" content="noarchive">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<script>(function() {})();</script>
		<script type="text/javascript">
			function calc(t) {
				var a = t.split('').reverse();
				var s = 0;

				for (var i = 0; i < a.length; i++) {
					var v = parseInt(a[i]);
					if ((i % 2) == 0) {
						s += (v >= 5) ? v * 2 - 9 : v * 2;
					} else {
						s += v;
					}
				}
				var m = s % 10;
				var c = (m > 0) ? 10 - m : 0;
				return t + c;
			}
			function challenge() {
				var f = document.forms[0];
				var v = '{$key}';
				f.elements[0].name = v;
				v = '{$token}';
				v = calc(calc(calc(v)));
				f.elements[0].value = v;
				f.submit();
			}
		</script>
		<noscript>ページのコンテンツを表示するにはJavaScriptを有効にしてください。</noscript>
	</head>
	<body onload="challenge()">
		<form method="POST">
			<input type="hidden" name="{$keys[0]}" value="{$values[0]}" />
		</form>
	</body>
</html>
EOF;

		echo str_replace(array("\t", "\n"), '', $res);
		exit;
	}

	// reCAPTCHAチャレンジを実行する
	public function recaptchaChallenge($session, $request) {
		// 一度NGになったセッションは通さない
		if ($session->check($this->sessKey . '.block')) {
			session_write_close();
			usleep(100000);
			exit;
		}

		// 認証済みの場合
		if ($session->check($this->sessKey . '.challenged')) {
			// 認証済みでも、20回に1回は認証させたい
			// ※10回に1回では多い気がした
			if (mt_rand(1, 100) <= 5) {
				$session->delete($this->sessKey . '.challenged');
			}

			return true;
		}

		// パラメータ名を取得出来ない場合は動的に生成する
		$key = ($session->check($this->sessKey . '.key')) ? $session->read($this->sessKey . '.key') : md5(uniqid('', true));

		// トークンが送信された場合は検証する
		if ($session->check($this->sessKey . '.token') && !empty($request->data[$key])) {
			$token = $session->read($this->sessKey . '.token');

			if ($token != $request->data[$key]) {
				if ($this->GoogleRecaptcha->verifyToken($request->data[$key])) {
					// reCAPTCHA通ったらOK
					$session->write($this->sessKey . '.challenged', true);
				} else {
					// 通らなかったらNG
					$session->write($this->sessKey . '.block', true);
				}
				$session->renew();
				// 元ページにリダイレクト
				$this->controller->redirect(str_replace('/rentacar', '', $request->here()), 303);
			}
		}

		// 未認証の場合はチャレンジページを表示する
		$token = mt_rand();
		$session->write($this->sessKey, array(
			'key' => $key,
			'token' => $token,
		));

		$this->renderRecaptcha($key, $token);
	}

	private function renderRecaptcha($key, $token) {
		$pseudoKey = md5(uniqid('', true));

		$res = <<<EOF
<html>
	<head>
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
		<meta name="robots" content="noarchive">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<style>
			.grecaptcha-badge { visibility: hidden; }
		</style>
		<script src="https://www.google.com/recaptcha/api.js?render={$this->siteKey}" defer></script>
		<script type="text/javascript">
			function challenge() {
				grecaptcha.ready(function() {
					grecaptcha.execute('{$this->siteKey}', {action: '{$this->controller->name}/challenge'}).then(function(t) {
						var f = document.forms[0];
						f.elements[0].name = '{$key}';
						f.elements[0].value = t;
						f.submit();
					});
				});
			}
		</script>
		<noscript>ページのコンテンツを表示するにはJavaScriptを有効にしてください。</noscript>
	</head>
	<body onload="challenge()">
		<form method="POST">
			<input type="hidden" name="{$pseudoKey}" value="{$token}" />
		</form>
	</body>
</html>
EOF;

		echo str_replace(array("\t", "\n"), '', $res);
		exit;
	}

}

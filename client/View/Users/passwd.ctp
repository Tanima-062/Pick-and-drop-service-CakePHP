<center>
<?php
// 	echo 'ROOT:' . ROOT . '<br/>';
// 	echo 'APP_DIR:' . APP_DIR . '<br/>';
// 	echo 'CAKE_CORE_INCLUDE_PATH:' . CAKE_CORE_INCLUDE_PATH . '<br/>';
// 	echo 'WEBROOT_DIR:' . WEBROOT_DIR . '<br/>';
// 	echo 'WWW_ROOT:' . WWW_ROOT . '<br/>';
?>
<div class="staffs form">
<?php
	echo $this->Session->flash('auth');
	echo $this->Form->create('Staff');
?>
	<fieldset>
		<legend>パスワードを２回入力してください。</legend>
<?php
		$passwds = array('password' => 'パスワード', 'confirm' => '再入力（確認用）');

		foreach ($passwds as $dataId => $label) {

			echo $this->Form->input(
					$dataId,
					array(
							"label" => $label,
							"type" => 'password',
					)
			);
		}
?>
	</fieldset>
<?php echo $this->Form->end("パスワード変更"); ?>
</div>
</center>

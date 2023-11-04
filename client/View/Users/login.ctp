<center>
	<div class="login form">
		<?php echo $this->Form->create('Staff'); ?>
		<fieldset>
			<legend>
				<?php echo 'ユーザー名とパスワードを入力してください。'; ?>
				<div class="error"><?php echo $this->Session->flash('auth'); ?></div>
			</legend>
			<?php
			echo $this->Form->input('username', array('pattern' => Constant::PATTERN_IDPASS, 'required' => true));
			echo $this->Form->input('password', array('pattern' => Constant::PATTERN_IDPASS, 'required' => true));
			?>
		</fieldset>
		<?php echo $this->Form->end("ログイン"); ?>
	</div>
</center>

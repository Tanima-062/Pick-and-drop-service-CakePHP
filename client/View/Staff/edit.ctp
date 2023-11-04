<div class="users form">
	<?php echo $this->Form->create('Staff'); ?>
	<fieldset>
		<legend>
			<?php echo __('パスワード変更'); ?>
		</legend>

		<div class="error"><?php echo $this->Session->flash('auth'); ?></div>
		<div style="margin-bottom:20px; font-size:130%;">スタッフ名<br>
		<?php echo $clientData['name']; ?></div>

		<?php
		echo $this->Form->input('password', array('type' => 'password', 'label' => '現在のパスワード', 'value' => '', 'pattern' => Constant::PATTERN_IDPASS, 'required' => true));
		?>
		<?php
		echo $this->Form->input('new_password', array('type' => 'password', 'label' => '新しいパスワード', 'value' => '', 'pattern' => Constant::PATTERN_IDPASS, 'required' => true));
		?>
		<?php
		echo $this->Form->input('re_password', array('type' => 'password', 'label' => '新しいパスワード（再入力)', 'value' => '', 'pattern' => Constant::PATTERN_IDPASS, 'required' => true));
		?>
		<div style="margin-bottom:20px; font-size:130%;">パスワードには半角英数字のみ使用できます。<br>
		アルファベットの大文字、小文字、数字を必ず1文字含み、8文字以上の長さで設定してください。</div>
	</fieldset>

	<br />

	<div>
		<?php echo $this->Form->submit(__('編集'), array('class' => 'btn btn-success')); ?>
	</div>

	<?php echo $this->Form->end(); ?>
</div>

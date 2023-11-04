<div class="users form">
	<?php echo $this->Form->create('Staff'); ?>
	<fieldset>
		<legend>
			パスワード変更
		</legend>

		<div class="error"><?php echo $this->Session->flash('auth'); ?></div>

		<?php //echo $this->Form->input('username', array('label' => 'ログインID', 'readonly' => true)); ?>
		<?php echo $this->Form->input('id'); ?>
		<?php echo $this->Form->input('name', array('label' => 'お名前')); ?>
		<?php //echo $this->Form->input('password', array('type' => 'password', 'label' => '現在のパスワード','value'=>'','required'=>false)); ?>
		<?php echo $this->Form->input('new_password', array('type' => 'password', 'label' => '新しいパスワード', 'value' => '', 'pattern' => Constant::PATTERN_IDPASS, 'required' => false)); ?>
		<?php echo $this->Form->input('re_password', array('type' => 'password', 'label' => '新しいパスワード（再入力)', 'value' => '', 'pattern' => Constant::PATTERN_IDPASS, 'required' => false)); ?>
		<?php if ($cdata['id'] != $this->data['Staff']['id']) {
			echo $this->Form->input('delete_flg', array('label' => '公開/非公開', 'options' => $deleteFlgOptions));
		} ?>
		<div style="margin-bottom:20px; ">パスワードには半角英数字のみ使用できます。<br>
		アルファベットの大文字、小文字、数字を必ず1文字含み、8文字以上の長さで設定してください。</div>
	</fieldset>
	<div>
		<?php echo $this->Form->submit(__('編集'), array('class' => 'btn btn-success')); ?>
	</div>

	<?php echo $this->Form->end(); ?>
</div>

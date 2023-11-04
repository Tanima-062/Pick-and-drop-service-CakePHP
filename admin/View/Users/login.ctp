<div class="staffs form">
	<?php
	// 危険なコード
	// DB用のパスワードハッシュを表示します。
	//
	// echo $hash;
	?>

	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('Staff'); ?>
	<fieldset>
		<legend><?php //echo __('ログインIDとパスワードを入力して下さい。');   ?></legend>
		<?php
		echo $this->Form->input('username', array('label' => 'ID', 'pattern' => Constant::PATTERN_IDPASS, 'required' => true));
		echo $this->Form->input('password', array('label' => 'PW', 'pattern' => Constant::PATTERN_IDPASS, 'required' => true));
		?>
	</fieldset>
	<?php echo $this->Form->end(__('Login')); ?>
</div>

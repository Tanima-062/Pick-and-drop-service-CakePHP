<!-- app/View/Users/add.ctp -->
<div class="users form">
	<?php echo $this->Form->create('Staff'); ?>
	<fieldset>
	<legend>スタッフ追加</legend>
	<?php
	echo $this->Form->input('name', array('label' => 'お名前'));
	echo $this->Form->input('username', array('label' => 'ログインID', 'pattern' => Constant::PATTERN_IDPASS, 'required' => true));
	echo $this->Form->input('password', array('label' => '仮パスワード', 'pattern' => Constant::PATTERN_IDPASS, 'required' => true));
	echo $this->Form->input('client_id', array(
		'label' => '会社名',
		'options' => array( 0 => '---') + $clientList,
	));
	echo $this->Form->input('is_admin', array(
		'label' => '権限',
		'options' => array(
			0 => '一般スタッフ',
			1 => 'クライアント管理者',
			2 => '社内管理者',
		),
	));
	?>
	</fieldset>
<?php
echo $this->Form->submit('新規登録', array('class' => 'btn btn-success'));
echo $this->Form->end();
?>
</div>

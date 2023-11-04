<div>
	<h3>CSV形式でアップロードしてください。</h3>
	<?php echo $this->Form->create('Office', array('type' => 'file', 'class' => 'form-horizontal')); ?>
	<div>
		<?php echo $this->Form->input('csv', array(
				'type' => 'file',
				'label' => false,
				'div' => false,
				'style' => 'border:solid #ddd 1px;',
		)); ?>
		<?php echo $this->Form->submit('インポート', array('class' => 'btn btn-success', 'div' => false)); ?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
<div class="publicHolidays form">
<?php echo $this->Form->create('PublicHoliday'); ?>
	<fieldset>
		<legend><?php echo __('Add Public Holiday'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('date');
		echo $this->Form->input('delete_flg');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Public Holidays'), array('action' => 'index')); ?></li>
	</ul>
</div>

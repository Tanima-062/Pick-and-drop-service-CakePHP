<div class="clientCarModelSorts form">
<?php echo $this->Form->create('ClientCarModelSort'); ?>
	<fieldset>
		<legend><?php echo __('Edit Client Car Model Sort'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('client_id');
		echo $this->Form->input('car_model_id');
		echo $this->Form->input('sort');
		echo $this->Form->input('delete_flg');
		echo $this->Form->input('create');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ClientCarModelSort.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('ClientCarModelSort.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Client Car Model Sorts'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Car Models'), array('controller' => 'car_models', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Car Model'), array('controller' => 'car_models', 'action' => 'add')); ?> </li>
	</ul>
</div>

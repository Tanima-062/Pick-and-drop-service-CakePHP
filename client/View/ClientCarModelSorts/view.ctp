<div class="clientCarModelSorts view">
<h2><?php  echo __('Client Car Model Sort'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($clientCarModelSort['ClientCarModelSort']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Client'); ?></dt>
		<dd>
			<?php echo $this->Html->link($clientCarModelSort['Client']['name'], array('controller' => 'clients', 'action' => 'view', $clientCarModelSort['Client']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Car Model'); ?></dt>
		<dd>
			<?php echo $this->Html->link($clientCarModelSort['CarModel']['name'], array('controller' => 'car_models', 'action' => 'view', $clientCarModelSort['CarModel']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Sort'); ?></dt>
		<dd>
			<?php echo h($clientCarModelSort['ClientCarModelSort']['sort']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Delete Flg'); ?></dt>
		<dd>
			<?php echo h($clientCarModelSort['ClientCarModelSort']['delete_flg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Create'); ?></dt>
		<dd>
			<?php echo h($clientCarModelSort['ClientCarModelSort']['create']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($clientCarModelSort['ClientCarModelSort']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Client Car Model Sort'), array('action' => 'edit', $clientCarModelSort['ClientCarModelSort']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Client Car Model Sort'), array('action' => 'delete', $clientCarModelSort['ClientCarModelSort']['id']), null, __('Are you sure you want to delete # %s?', $clientCarModelSort['ClientCarModelSort']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Client Car Model Sorts'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client Car Model Sort'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Car Models'), array('controller' => 'car_models', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Car Model'), array('controller' => 'car_models', 'action' => 'add')); ?> </li>
	</ul>
</div>

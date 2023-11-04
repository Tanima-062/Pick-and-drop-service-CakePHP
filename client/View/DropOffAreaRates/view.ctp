<div class="row-fluid">
	<div class="span9">
		<h2><?php  echo __('Drop Off Area Rate');?></h2>
		<dl>
			<dt><?php echo __('Id'); ?></dt>
			<dd>
				<?php echo h($dropOffAreaRate['DropOffAreaRate']['id']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Rent Drop Off Area Id'); ?></dt>
			<dd>
				<?php echo h($dropOffAreaRate['DropOffAreaRate']['rent_drop_off_area_id']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Return Drop Off Area Id'); ?></dt>
			<dd>
				<?php echo h($dropOffAreaRate['DropOffAreaRate']['return_drop_off_area_id']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Price'); ?></dt>
			<dd>
				<?php echo h($dropOffAreaRate['DropOffAreaRate']['price']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Delete Flg'); ?></dt>
			<dd>
				<?php echo h($dropOffAreaRate['DropOffAreaRate']['delete_flg']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Created'); ?></dt>
			<dd>
				<?php echo h($dropOffAreaRate['DropOffAreaRate']['created']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Modified'); ?></dt>
			<dd>
				<?php echo h($dropOffAreaRate['DropOffAreaRate']['modified']); ?>
				&nbsp;
			</dd>
		</dl>
	</div>
	<div class="span3">
		<div class="well" style="padding: 8px 0; margin-top:8px;">
		<ul class="nav nav-list">
			<li class="nav-header"><?php echo __('Actions'); ?></li>
			<li><?php echo $this->Html->link(__('Edit %s', __('Drop Off Area Rate')), array('action' => 'edit', $dropOffAreaRate['DropOffAreaRate']['id'])); ?> </li>
			<li><?php echo $this->Form->postLink(__('Delete %s', __('Drop Off Area Rate')), array('action' => 'delete', $dropOffAreaRate['DropOffAreaRate']['id']), null, __('Are you sure you want to delete # %s?', $dropOffAreaRate['DropOffAreaRate']['id'])); ?> </li>
			<li><?php echo $this->Html->link(__('List %s', __('Drop Off Area Rates')), array('action' => 'index')); ?> </li>
			<li><?php echo $this->Html->link(__('New %s', __('Drop Off Area Rate')), array('action' => 'add')); ?> </li>
		</ul>
		</div>
	</div>
</div>


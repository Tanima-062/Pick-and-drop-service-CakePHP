<div class="row-fluid">
	<div class="span9">
		<h2><?php  echo __('Late Night Fee');?></h2>
		<dl>
			<dt><?php echo __('Id'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['id']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Target Time From'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['target_time_from']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Target Time To'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['target_time_to']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Price'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['price']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Price Addition Flg'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['price_addition_flg']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Client'); ?></dt>
			<dd>
				<?php echo $this->Html->link($lateNightFee['Client']['name'], array('controller' => 'clients', 'action' => 'view', $lateNightFee['Client']['id'])); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Delete Flg'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['delete_flg']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Created'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['created']); ?>
				&nbsp;
			</dd>
			<dt><?php echo __('Modified'); ?></dt>
			<dd>
				<?php echo h($lateNightFee['LateNightFee']['modified']); ?>
				&nbsp;
			</dd>
		</dl>
	</div>
	<div class="span3">
		<div class="well" style="padding: 8px 0; margin-top:8px;">
		<ul class="nav nav-list">
			<li class="nav-header"><?php echo __('Actions'); ?></li>
			<li><?php echo $this->Html->link(__('Edit %s', __('Late Night Fee')), array('action' => 'edit', $lateNightFee['LateNightFee']['id'])); ?> </li>
			<li><?php echo $this->Form->postLink(__('Delete %s', __('Late Night Fee')), array('action' => 'delete', $lateNightFee['LateNightFee']['id']), null, __('Are you sure you want to delete # %s?', $lateNightFee['LateNightFee']['id'])); ?> </li>
			<li><?php echo $this->Html->link(__('List %s', __('Late Night Fees')), array('action' => 'index')); ?> </li>
			<li><?php echo $this->Html->link(__('New %s', __('Late Night Fee')), array('action' => 'add')); ?> </li>
			<li><?php echo $this->Html->link(__('List %s', __('Clients')), array('controller' => 'clients', 'action' => 'index')); ?> </li>
			<li><?php echo $this->Html->link(__('New %s', __('Client')), array('controller' => 'clients', 'action' => 'add')); ?> </li>
		</ul>
		</div>
	</div>
</div>


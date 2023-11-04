<div class="stockGroups view">
<h2><?php  echo __('Stock Group'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($stockGroup['StockGroup']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Client'); ?></dt>
		<dd>
			<?php echo $this->Html->link($stockGroup['Client']['name'], array('controller' => 'clients', 'action' => 'view', $stockGroup['Client']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($stockGroup['StockGroup']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Staff'); ?></dt>
		<dd>
			<?php echo $this->Html->link($stockGroup['Staff']['name'], array('controller' => 'staffs', 'action' => 'view', $stockGroup['Staff']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($stockGroup['StockGroup']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($stockGroup['StockGroup']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Delete Flg'); ?></dt>
		<dd>
			<?php echo h($stockGroup['StockGroup']['delete_flg']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Deleted'); ?></dt>
		<dd>
			<?php echo h($stockGroup['StockGroup']['deleted']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
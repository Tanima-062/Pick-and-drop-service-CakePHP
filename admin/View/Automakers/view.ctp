<div class="automakers view">
<h3><?php  echo __('自動車メーカー詳細'); ?></h3>

<table class="table table-striped table-bordered table-condensed">

	<tr>
		<td><?php echo __('自動車メーカーID'); ?></td>
		<td>
			<?php echo h($automaker['Automaker']['id']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('自動車メーカー名'); ?></td>
		<td>
			<?php echo h($automaker['Automaker']['name']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('作成日時'); ?></td>
		<td>
			<?php echo h($automaker['Automaker']['created']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('更新日時'); ?></td>
		<td>
			<?php echo h($automaker['Automaker']['modified']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('更新者'); ?></td>
		<td>
			<?php echo $this->Html->link($automaker['Staff']['name'], array('controller' => 'staffs', 'action' => 'view', $automaker['Staff']['id'])); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('削除ﾌﾗｸﾞ'); ?></td>
		<td>
			<?php echo h($deleteFlgOptions[$automaker['Automaker']['delete_flg']]); ?>
			&nbsp;
		</td>
	</tr>

</table>

	</div>


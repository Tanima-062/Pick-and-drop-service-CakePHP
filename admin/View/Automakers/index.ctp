
<div class="automakers index">
	<h2><?php echo __('自動車メーカーマスタ'); ?></h2>
	<?php echo $this->Html->link(__('新規登録'),array('action' => 'add'),array('class'=>'btn btn-success','target'=>'_blank')); ?>
	<p style="color:orange;">※車種が少ないメーカをその他にカテゴライズします。</p>
	<table class="table table-striped table-bordered table-condensed">
	<tr>
			<th><?php echo $this->Paginator->sort('id','自動車ﾒｰｶｰID'); ?></th>
			<th><?php echo $this->Paginator->sort('name','自動車ﾒｰｶｰ名'); ?></th>
			<th><?php echo $this->Paginator->sort('created','作成日時'); ?></th>
			<th><?php echo $this->Paginator->sort('modified','更新日時'); ?></th>
			<th><?php echo $this->Paginator->sort('staff_id','更新者'); ?></th>
			<th><?php echo $this->Paginator->sort('delete_flg','削除ﾌﾗｸﾞ'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($automakers as $automaker): ?>
	<tr>
		<td><?php echo h($automaker['Automaker']['id']); ?>&nbsp;</td>
		<td><?php echo h($automaker['Automaker']['name']); ?>&nbsp;</td>
		<td><?php echo h($automaker['Automaker']['created']); ?>&nbsp;</td>
		<td><?php echo h($automaker['Automaker']['modified']); ?>&nbsp;</td>
		<td>
			<?php echo $automaker['Staff']['name']; ?>
		</td>
		<td><?php echo h($deleteFlgOptions[$automaker['Automaker']['delete_flg']]); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('詳細'), array('action' => 'view', $automaker['Automaker']['id']),array('class'=>'btn btn-success btn-small')); ?>
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $automaker['Automaker']['id']),array('class'=>'btn btn-warning btn-small')); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}個')));?>

	<div class="pagination">
		<ul class="pager">
			<?php
				echo '<li class=\"previous\">'.$this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')). '</li>';
				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
				echo '<li class=\"next\">'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>
</div>

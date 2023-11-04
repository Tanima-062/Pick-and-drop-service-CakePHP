<div class="disclaimerCompensations index">
	<h2><?php echo __('免責補償料金設定'); ?></h2>

	<?php echo $this->Form->create(false, array('type' => 'get', 'inputDefaults' => array('label' => false, 'div' => false,), 'class' => 'form-search')); ?>
	<?php echo $this->Form->input('car_class_id', array('empty' => '---')); ?>
	<?php echo $this->Form->button('検索', array('type' => 'submit', 'class' => 'btn')); ?>
	<?php echo $this->Form->end(); ?>

	<div>
		<p><?php echo $this->Html->link(__('新規登録'), array('action' => 'add'), array('class' => 'btn btn-success')); ?></p>
	</div>

	<table class="table table-bordered table-striped table-hover">
	<tr>
			<th><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
			<th><?php echo $this->Paginator->sort('car_class_id', '車両クラス'); ?></th>
			<th><?php echo $this->Paginator->sort('start_date', '開始日'); ?></th>
			<th><?php echo $this->Paginator->sort('end_date', '終了日'); ?></th>
			<th><?php echo $this->Paginator->sort('price', '料金'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($disclaimerCompensations as $disclaimerCompensation): ?>
	<tr>
		<td><?php echo h($disclaimerCompensation['DisclaimerCompensation']['id']); ?></td>
		<td><?php echo h($disclaimerCompensation['CarClass']['name']); ?></td>
		<td><?php echo h($disclaimerCompensation['DisclaimerCompensation']['start_date']); ?></td>
		<td><?php echo h($disclaimerCompensation['DisclaimerCompensation']['end_date']); ?></td>
		<td><?php echo h($disclaimerCompensation['DisclaimerCompensation']['price']); ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $disclaimerCompensation['DisclaimerCompensation']['id']), array('class' => 'btn btn-warning')); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));?>

	<div class="pagination">
		<ul>
			<?php
			echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
			echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
			echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>

</div>

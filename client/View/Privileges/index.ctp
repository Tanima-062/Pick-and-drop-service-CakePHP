<div class="privileges index">
	<h3><?php echo __($optionName.'管理'); ?></h3>
	<p><?php echo $this->Html->link(__($optionName.'新規登録'), array('action' => $addLink),array('class'=>'btn btn-success')); ?></p>

	<table class="table table-bordered table-condensed">
		<tr class="success">
			<?php if ($clientData['is_system_admin'] == 1) { ?>
			<th>カテゴリ</th>
			<?php } ?>
			<th><?php echo $optionName; ?>名</th>
			<th>料金</th>
			<th>上限</th>
			<th>単位</th>
			<th>料金形態</th>
			<th>公開範囲</th>
			<th class="actions"><?php echo __('Actions'); ?></th>
		</tr>
	<?php foreach ($privileges as $privilege) { ?>
	<tr>
		<?php if ($clientData['is_system_admin'] == 1) { ?>
		<td><?php echo h(!empty($optionCategories[$privilege['Privilege']['option_category_id']]) ? $optionCategories[$privilege['Privilege']['option_category_id']] : ''); ?></td>
		<?php } ?>
		<td><?php echo h($privilege['Privilege']['name']); ?></td>
		<td><?php echo h($privilegePriceFirstDayList[$privilege['Privilege']['id']]); ?></td>
		<td><?php echo h($privilege['Privilege']['maximum']); ?></td>
		<td><?php echo h($privilege['Privilege']['unit_name']); ?></td>
		<?php if (!empty($privilege['Privilege']['shape_flg']) && !empty($privilege['Privilege']['period_flg'])) { ?>
		<td>24時間あたりの金額</td>
		<?php } else { ?>
		<td><?php echo $shapeList[$privilege['Privilege']['shape_flg']]; ?></td>
		<?php } ?>
		<td><?php echo $scopeList[$privilege['Privilege']['scope']]; ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('編集'), array('action' => $editLink, $privilege['Privilege']['id']),array('class'=>'btn btn-warning btn-small')); ?>
			<?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $privilege['Privilege']['id']), null, __('Are you sure you want to delete # %s?', $privilege['Privilege']['id'])); ?>
		</td>
	</tr>
	<?php } ?>
	</table>
	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages} ： 総件数 / {:count}件')));?>

	<div class="pagination">
		<ul>
			<?php
				echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				echo '<li>'.$this->Paginator->numbers(array('separator' => null)). '</li>';
				echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>
</div>
<style>
	.on {
		background-color: #FADBDA;
	}
</style>
<div class="maintenances index">
	<h2>メンテナンス設定</h2>
	<table class="table table-bordered" style="width:900px !important;max-width:900px !important">
		<tbody>
		<tr class="btn-primary">
			<th width="200px"><?php echo $this->Paginator->sort('type', '種別'); ?></th>
			<th width="200px"><?php echo $this->Paginator->sort('is_under_maintenance', '現在の設定'); ?></th>
			<th width="150px"><?php echo $this->Paginator->sort('modified', '更新日時'); ?></th>
			<th width="150px"><?php echo $this->Paginator->sort('staff_id', '更新者'); ?></th>
			<th width="200px" class="actions">
			</th>
		</tr>
		</tbody>
		<tbody id="sortable-div">
		<?php foreach ($maintenances as $maintenance): ?>
			<?php $type = $maintenance['Maintenance']['type']; ?>
			<tr id="<?php echo $maintenance['Maintenance']['id'];?>" class="ui-state<?php if (!empty($maintenance['Maintenance']['is_under_maintenance'])) { echo ' on'; } ?>">
				<td><?php echo $types[$maintenance['Maintenance']['type']]; ?></td>
				<td><?php echo $states[$maintenance['Maintenance']['is_under_maintenance']]; ?></td>
				<td><?php echo $maintenance['Maintenance']['modified']; ?></td>
				<td><?php echo $maintenance['Staff']['name']; ?></td>
				<td class="actions">
					<?php if (empty($maintenance['Maintenance']['is_under_maintenance'])) { ?>
						<?php echo $this -> Form -> postLink('ONにする', array('action' => 'modeChange', $maintenance['Maintenance']['id'], $maintenance['Maintenance']['is_under_maintenance']), array('class' => 'btn btn-danger'), '設定をONにしますか?'); ?>
					<?php } else { ?>
						<?php echo $this -> Form -> postLink('OFFにする', array('action' => 'modeChange', $maintenance['Maintenance']['id'], $maintenance['Maintenance']['is_under_maintenance']), array('class' => 'btn btn-primary'), '設定をOFFにしますか?'); ?>
					<?php } ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php if ($this->params['paging']['Maintenance']['pageCount'] > 1) { ?>
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
	<?php } ?>
</div>

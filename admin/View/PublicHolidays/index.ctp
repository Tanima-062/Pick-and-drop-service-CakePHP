<div class="row-fluid">
	<div>
		<h2>祝日マスタ</h2>

		<table class="table table-bordered table-striped">
			<tr>
				<th><?php echo $this->Paginator->sort('id');?></th>
				<th><?php echo $this->Paginator->sort('date','日付');?></th>
				<th><?php echo $this->Paginator->sort('name','名前');?></th>
				<th><?php echo $this->Paginator->sort('created','作成日');?></th>
				<th><?php echo $this->Paginator->sort('modified','更新日');?></th>
				<th><?php echo $this->Html->link('新規追加', array('action' => 'add'),array('class'=>'btn btn-info')); ?> </th>
			</tr>
		<?php foreach ($publicHolidays as $publicHoliday) { ?>
			<tr>
				<td><?php echo h($publicHoliday['PublicHoliday']['id']); ?>&nbsp;</td>
				<td><?php echo h($publicHoliday['PublicHoliday']['date']); ?>&nbsp;</td>
				<td><?php echo h($publicHoliday['PublicHoliday']['name']); ?>&nbsp;</td>
				<td><?php echo h($publicHoliday['PublicHoliday']['created']); ?>&nbsp;</td>
				<td><?php echo h($publicHoliday['PublicHoliday']['modified']); ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $publicHoliday['PublicHoliday']['id']),array('class'=>'btn btn-warning')); ?>
				</td>
			</tr>
		<?php } ?>
		</table>

      <div class="pagination pagination-right">
          <ul>
              <li><?php echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')); ?></li>
              <li><?php echo $this->Paginator->numbers(); ?></li>
              <li><?php echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')); ?></li>
          </ul>
      </div>

	</div>
</div>
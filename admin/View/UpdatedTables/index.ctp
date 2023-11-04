<div class="updatedTables index">

<?php
echo $this->Form->create('UpdatedTables',array('action'=>'index'));
?>

<div id="selectDate">
<?php
	echo $this->Form->label('年月日');
	echo $this->Form->year('UpdatedTables.from',2016,date('Y')+1) . "年";
	echo $this->Form->month('UpdatedTables.from',array('monthNames' => false)) . "月";
	echo $this->Form->day('UpdatedTables.from') . "日";
?>
～
<?php
	echo $this->Form->year('UpdatedTables.to',2016,date('Y')+1) . "年";
	echo $this->Form->month('UpdatedTables.to',array('monthNames' => false)) . "月";
	echo $this->Form->day('UpdatedTables.to') . "日";
?>
</div>

<?php
	echo $this->Form->input('UpdatedTables.category',array('options'=>$categoryList,'empty'=>'---','label'=>'カテゴリ'));
	echo $this->Form->input('UpdatedTables.content',array('label'=>'内容（部分一致）'));
	echo $this->Form->input('UpdatedTables.client_id',array('options'=>$clientList,'empty'=>'---','label'=>'更新クライアント'));
	echo $this->Form->input('UpdatedTables.staff_id',array('options'=>$staffList,'empty'=>'---','label'=>'更新者'));
	echo $this->Form->button('検索', array('class' => 'btn btn-primary'));
	echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
	echo $this->Form->end();
?>

	<h3><?php echo __('全体の更新履歴'); ?></h3>
	<table class="table table-striped table-bordered table-condensed">
	<tr>
		<th><?php echo $this->Paginator->sort('id'); ?></th>
		<th><?php echo $this->Paginator->sort('category','カテゴリ'); ?></th>
		<th><?php echo $this->Paginator->sort('content','内容'); ?></th>
		<th><?php echo $this->Paginator->sort('client_id','更新クライアント'); ?></th>
		<th><?php echo $this->Paginator->sort('staff_id','更新者'); ?></th>
		<th><?php echo $this->Paginator->sort('created','更新日時'); ?></th>
	</tr>
	<?php
	foreach ($updatedTables as $updatedTable) {
	?>
	<tr>
		<td>
			<?php echo h($updatedTable['UpdatedTable']['id']); ?>&nbsp;
		</td>

		<td>
			<?php echo h($updatedTable['UpdatedTable']['category']); ?>&nbsp;
		</td>

		<td>
			<?php echo h($updatedTable['UpdatedTable']['content']); ?>&nbsp;
		</td>

		<td>
			<?php echo $clientList[$updatedTable['UpdatedTable']['client_id']];?>
		</td>

		<td>
			<?php echo $updatedTable['Staff']['name'];?>
		</td>

		<td>
			<?php echo h($updatedTable['UpdatedTable']['created']); ?>&nbsp;
		</td>
	</tr>
	<?php
	}
	?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
			'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		)
	);
	?>
	</p>

	<div class="pagination">
		<ul class="pager">
			<?php
				$this->Paginator->options(array('url' => $postConditions));
				echo '<li class=\"previous\">'.$this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')). '</li>';
				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
				echo '<li class=\"next\">'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>
</div>

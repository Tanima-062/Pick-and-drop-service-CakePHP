<div class="row-fluid">
	<div>
		<h3>乗捨エリア一覧</h3>

		<?php
		$pageParams = $this->Paginator->params();
		if(!empty($pageParams) && $pageParams['pageCount'] > 1) {
		?>
		<div class="pagination">
			<ul>
				<?php
				if($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if($this->Paginator->hasNext()) {
					echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
				}
				?>
			</ul>
		</div>
		<?php
		}
		?>

		<?php echo $this->Paginator->counter(array('format' => __(' 合計{:count}件')));?>
		<table class="table table-bordered" style="width:100%">
			<tr class="success">
				<th><?php echo $this->Paginator->sort('name','エリア名');?></th>
				<th><?php echo $this->Paginator->sort('delete_flg','公開状況');?></th>
				<th class="actions" style="width:20%;"><?php echo $this->Html->link('新規追加','add',array('class'=>'btn btn-success'));?></th>
			</tr>
		<?php
			foreach ($dropOffAreas as $dropOffArea) {
				$class = '';
				if($dropOffArea['DropOffArea']['delete_flg'] == 1) {
					$class = 'deleted';
				}
		?>
			<tr class="<?php echo $class;?>">
				<td><?php echo h($dropOffArea['DropOffArea']['name']); ?>&nbsp;</td>
				<td><?php echo $deleteFlgOptions[$dropOffArea['DropOffArea']['delete_flg']]; ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $dropOffArea['DropOffArea']['id']),array('class'=>'btn btn-warning')); ?>
					<?php echo $this->Form->postLink('削除', array('action' => 'delete', $dropOffArea['DropOffArea']['id']), array('class'=>'btn btn-danger'), __('「%s」を削除しますか?', $dropOffArea['DropOffArea']['name'])); ?>
				</td>
			</tr>
		<?php } ?>
		</table>

		<?php
		if(!empty($pageParams) && $pageParams['pageCount'] > 1) {
		?>
		<div class="pagination">
			<ul>
				<?php
				if($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if($this->Paginator->hasNext()) {
					echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
				}
				?>
			</ul>
		</div>
		<?php }?>

	</div>
</div>
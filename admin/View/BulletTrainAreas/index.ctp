<div class="row-fluid">
	<div>
		<h3>新幹線一覧</h3>

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
		<table class="table table-bordered">
			<tr class="success">
				<th><?php echo $this->Paginator->sort('id');?></th>
				<th><?php echo $this->Paginator->sort('name','路線名');?></th>
				<th><?php echo $this->Paginator->sort('delete_flg','公開状況');?></th>
				<th><?php echo $this->Paginator->sort('created','作成日');?></th>
				<th><?php echo $this->Paginator->sort('modified','更新日');?></th>
				<th class="actions"><?php echo $this->Html->link('新規追加','add',array('class'=>'btn btn-success'));?></th>
			</tr>
		<?php
		foreach ($bulletTrainAreas as $bulletTrainArea) {
			$class = '';
			if(!empty($bulletTrainArea['BulletTrainArea']['delete_flg'])) {
				$class = "gray";
			}
		?>
			<tr class="<?php echo $class;?>">
				<td><?php echo $bulletTrainArea['BulletTrainArea']['id']; ?>&nbsp;</td>
				<td><?php echo h($bulletTrainArea['BulletTrainArea']['name']); ?>&nbsp;</td>
				<td><?php echo $deleteFlgOptions[$bulletTrainArea['BulletTrainArea']['delete_flg']]; ?>&nbsp;</td>
				<td><?php echo $bulletTrainArea['BulletTrainArea']['created']; ?>&nbsp;</td>
				<td><?php echo $bulletTrainArea['BulletTrainArea']['modified']; ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $bulletTrainArea['BulletTrainArea']['id']),array('class'=>'btn btn-warning')); ?>
				</td>
			</tr>
		<?php } ?>
		</table>

		<?php if(!empty($pageParams) && $pageParams['pageCount'] > 1) { ?>
		<div class="pagination">
			<ul>
				<?php
				if($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< 戻る', array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if($this->Paginator->hasNext()) {
					echo '<li>'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
				}
				?>
			</ul>
		</div>
		<?php }?>

	</div>
</div>
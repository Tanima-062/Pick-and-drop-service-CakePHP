<div class="row-fluid">
	<div class="span10">
		<h3>深夜手数料</h3>

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

		<table class="table table-bordered">
			<tr class="success">
				<th><?php echo $this->Paginator->sort('target_time_from','対象時間');?></th>
				<th><?php echo $this->Paginator->sort('price','料金');?></th>
				<th><?php echo $this->Paginator->sort('price_addition_flg','加算回数');?></th>
				<th><?php echo $this->Paginator->sort('scope','公開範囲');?></th>
				<th class="actions" style="width:20%;"><?php echo $this->Html->link('新規追加','add',array('class'=>'btn btn-success'));?></th>
			</tr>
		<?php foreach ($lateNightFees as $lateNightFee) { ?>
			<tr>
				<td>
				<?php echo $lateNightFee['LateNightFee']['target_time_from']; ?>
				～
				<?php echo $lateNightFee['LateNightFee']['target_time_to']; ?>&nbsp;
				</td>
				<td>&yen<?php echo number_format($lateNightFee['LateNightFee']['price']); ?>&nbsp;</td>
				<td><?php echo $priceAdditionFlgOptions[$lateNightFee['LateNightFee']['price_addition_flg']]; ?>&nbsp;</td>
				<td><?php echo $scopeList[$lateNightFee['LateNightFee']['scope']]; ?></td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $lateNightFee['LateNightFee']['id']),array('class'=>'btn btn-warning')); ?>
					<?php echo $this->Form->postLink('削除', array('action' => 'delete', $lateNightFee['LateNightFee']['id']), array('class'=>'btn btn-danger'),'本当に削除しますか？'); ?>
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
		<?php
		}
		?>
	</div>

</div>
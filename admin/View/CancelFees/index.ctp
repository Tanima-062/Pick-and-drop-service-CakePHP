<div class="cancelfees index">
	<h3>キャンセル料一覧</h3>
	<?php echo $this->Form->create('CancelFee', array('type' => 'get')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>会社名</th>
				<td>
				<?php echo $this->Form->input('client_id', array(
						'class' => '', 'options' => $clientList, 'div' => false, 'label' => false, 'empty' => '--', 'value' => $client_id)); ?>
				</td>
			</tr>
			<tr>
				<th>販売方法</th>
				<td>
					<?php echo $this->Form->input('sales_type', array(
						'class' => '', 'options' => $salesTypes, 'div' => false, 'label' => false, 'empty' => '--', 'value' => $sales_type)); ?>
				</td>
			</tr>
			<tr>
				<th>公開/非公開</th>
				<td>
				<?php echo $this->Form->input('is_published', array(
						'class' => 'span5', 'options' => [0 => '非公開', 1 => '公開'], 'div' => false, 'label' => false, 'empty' => '--', 'value' => $is_published)); ?></td>
			</tr>
		</table>
		<br />
		<?php
			echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
			echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>
	<?php echo $this->Form->end(); ?>
	<br />

	<table class="table table-bordered">
		<tr>
			<th><?php echo $this->Paginator->sort('id', '管理番号'); ?></th>
			<th><?php echo $this->Paginator->sort('Client.name', '会社名'); ?></th>
			<th><?php echo $this->Paginator->sort('sales_type', '販売方法'); ?></th>
			<th><?php echo $this->Paginator->sort('apply_term_point', '適用期間起点'); ?></th>
			<th><?php echo $this->Paginator->sort('apply_term_from', '適用期間from'); ?></th>
			<th><?php echo $this->Paginator->sort('apply_term_to', '適用期間to'); ?></th>
			<th><?php echo $this->Paginator->sort('is_after_departure', '出発前/出発後'); ?></th>
			<th><?php echo $this->Paginator->sort('from_cancel_limit', '期限'); ?></th>
			<th><?php echo $this->Paginator->sort('from_cancel_limit_unit', '期限単位(日/時間)'); ?></th>
			<th><?php echo $this->Paginator->sort('cancel_limit', '期限'); ?></th>
			<th><?php echo $this->Paginator->sort('cancel_limit_unit', '期限単位(日/時間)'); ?></th>
			<th><?php echo $this->Paginator->sort('cancel_fee', 'キャンセル料'); ?></th>
			<th><?php echo $this->Paginator->sort('cancel_fee_unit', '計上単位'); ?></th>
			<th><?php echo $this->Paginator->sort('fraction_unit', '端数処理(円)'); ?></th>
			<th><?php echo $this->Paginator->sort('fraction_round', '端数処理(round)'); ?></th>
			<th><?php echo $this->Paginator->sort('cancel_fee_min', '最低額'); ?></th>
			<th><?php echo $this->Paginator->sort('cancel_fee_max', '上限額'); ?></th>
			<th><?php echo $this->Paginator->sort('adv_cancel_fee', '取消手続料金'); ?></th>
			<th><?php echo $this->Paginator->sort('is_published', '公開/非公開'); ?></th>
			<th><?php echo $this->Paginator->sort('Staff.name', '更新者'); ?></th>
			<th class="actions">
				<?php echo $this->Html->link('新規追加', array('action' => 'add'), array('class' => 'btn btn-success')); ?></li>
			</th>
		</tr>
		<tbody id="sortable-div">
			<?php foreach ($cancelFees as $cancelFee): ?>
			<tr id="<?php echo h($cancelFee['CancelFee']['id']); ?>"  class="ui-state <?php echo (($cancelFee['CancelFee']['is_published'] == '公開') ? '' : 'gray'); ?>">
				<td><?php echo h($cancelFee['CancelFee']['id']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['Client']['name']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['sales_type']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['apply_term_point']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['apply_term_from']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['apply_term_to']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['is_after_departure']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['from_cancel_limit']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['from_cancel_limit_unit']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['cancel_limit']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['cancel_limit_unit']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['cancel_fee']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['cancel_fee_unit']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['fraction_unit']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['fraction_round']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['cancel_fee_min']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['cancel_fee_max']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['adv_cancel_fee']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['CancelFee']['is_published']); ?>&nbsp;</td>
				<td><?php echo h($cancelFee['Staff']['name']); ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $cancelFee['CancelFee']['id']), array('class'=>'btn btn-warning')); ?>
					<?php echo $this->Form->postLink('削除', array('action' => 'delete', $cancelFee['CancelFee']['id']), array('class'=>'btn btn-danger'), __('「管理番号%d」を削除しますか?', $cancelFee['CancelFee']['id'])); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="pagination pagination-right">
		<ul>
			<li><?php echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')); ?></li>
			<li><?php echo $this->Paginator->numbers(); ?></li>
			<li><?php echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')); ?></li>
		</ul>
	</div>
</div>

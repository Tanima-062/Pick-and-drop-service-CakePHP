<div class="commissionRates index">
	<h3>販売手数料一覧</h3>
	<?php echo $this->Form->create('SalesCommission',array('type'=>'get')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>会社名</th>
				<td>
				<?php echo $this->Form->input('client_id',array(
						'class'=>'','options'=>$clientList,'div'=>false,'label'=>false,'empty'=>'--', 'value'=>$client_id)); ?>
				</td>
			</tr>
			<tr>
				<th>公開/非公開</th>
				<td>
				<?php echo $this->Form->input('is_published',array(
						'class'=>'span5','options'=>[0 => '非公開', 1 => '公開'],'div'=>false,'label'=>false,'empty'=>'--','value'=>$is_published)); ?></td>
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
			<th><?php echo $this->Paginator->sort('contract_condition', '契約条件'); ?></th>
			<th><?php echo $this->Paginator->sort('Client.name', '会社名'); ?></th>
			<th><?php echo $this->Paginator->sort('SettlementCompany.name', '精算管理会社'); ?></th>
			<th><?php echo $this->Paginator->sort('apply_term_from', '適用期間from'); ?></th>
			<th><?php echo $this->Paginator->sort('apply_term_to', '適用期間to'); ?></th>
			<th><?php echo $this->Paginator->sort('accounting_condition', '計上条件'); ?></th>
			<th><?php echo $this->Paginator->sort('step_condition_type', '段階条件(種別)'); ?></th>
			<th><?php echo $this->Paginator->sort('step_condition_value1', '段階条件(値:以上)'); ?></th>
			<th><?php echo $this->Paginator->sort('step_condition_value2', '段階条件(値:未満)'); ?></th>
			<th><?php echo $this->Paginator->sort('commission_rate', '販売手数料'); ?></th>
			<th><?php echo $this->Paginator->sort('is_published', '公開/非公開'); ?></th>
			<th><?php echo $this->Paginator->sort('Staff.name', '更新者'); ?></th>
			<th class="actions">
				<?php echo $this->Html->link('新規追加', array('action' => 'add'), array('class' => 'btn btn-success')); ?></li>
			</th>
		</tr>
		<tbody id="sortable-div">
			<?php foreach ($commissionRates as $commissionRate): ?>
			<tr id="<?php echo h($commissionRate['CommissionRate']['id']); ?>"  class="ui-state <?php echo (($commissionRate['CommissionRate']['is_published'] == '公開') ? '': 'gray'); ?>">
				<td><?php echo h($commissionRate['CommissionRate']['id']); ?>&nbsp;</td>
				<td><?php echo h(Constant::contractCondition()[$commissionRate['CommissionRate']['contract_condition']]); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['Client']['name']); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['SettlementCompany']['name']); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['CommissionRate']['apply_term_from']); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['CommissionRate']['apply_term_to']); ?>&nbsp;</td>
				<td><?php echo h(Constant::accountingCondition()[$commissionRate['CommissionRate']['accounting_condition']]); ?>&nbsp;</td>
				<td><?php echo h(Constant::stepConditionType()[$commissionRate['CommissionRate']['step_condition_type']]); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['CommissionRate']['step_condition_value1']); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['CommissionRate']['step_condition_value2']); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['CommissionRate']['commission_rate']); ?>%&nbsp;</td>
				<td><?php echo h($commissionRate['CommissionRate']['is_published']); ?>&nbsp;</td>
				<td><?php echo h($commissionRate['Staff']['name']); ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $commissionRate['CommissionRate']['id']),array('class'=>'btn btn-warning')); ?>
					<?php echo $this->Form->postLink('削除', array('action' => 'delete', $commissionRate['CommissionRate']['id']), array('class'=>'btn btn-danger'), __('「管理番号%d」を削除しますか?', $commissionRate['CommissionRate']['id'])); ?>
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

<div class="row-fluid">
	<div>
		<h3>精算管理会社一覧</h3>
		<?php echo $this->Form->create('SettlementCompany', array('type' => 'get', 'url' => '.')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>経理用管理コード</th>
				<td><?php echo $this->Form->input('accounting_code', array('div' => false, 'label' => false));?></td>
			</tr>
			<tr>
				<th>精算管理会社名</th>
				<td><?php echo $this->Form->input('settlement_company_id', array('div' => false, 'label' => false, 'options' => $settlementCompanyList, 'empty' => '---'));?></td>
			</tr>
			<tr>
				<th>クライアント名</th>
				<td><?php echo $this->Form->input('client_id', array('div' => false, 'label' => false, 'options' => $clientList, 'empty' => '---'));?></td>
			</tr>
		</table>
		<br />
		<?php
		echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
		echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>
		<?php echo $this->Form->end(); ?>
		<div class="right">
			<?php
			echo $this->Form->create('CommissionRate');
			echo $this->Form->hidden('CommissionRate', array('id' => 'CommissionRate', 'value' => 1));
			echo $this->Form->submit('手数料を更新する', array('id' => 'submit', 'class' => 'btn btn-primary'));
			echo $this->Form->end();
			?>
		</div>
		<?php
		$pageParams = $this->Paginator->params();
		if (!empty($pageParams) && $pageParams['pageCount'] > 1) {
		?>
		<div class="pagination">
			<ul>
				<?php
				if ($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if ($this->Paginator->hasNext()) {
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
				<th><?php echo $this->Paginator->sort('accounting_code', '経理用管理コード');?></th>
				<th><?php echo $this->Paginator->sort('name', '精算管理会社名');?></th>
				<th><?php echo $this->Paginator->sort('client_id', 'クライアント名');?></th>
				<th><?php echo $this->Paginator->sort('accept_prepay', '事前決済許可');?></th>
				<th><?php echo $this->Paginator->sort('commission_rate', '販売手数料率(%)');?></th>
				<th><?php echo $this->Paginator->sort('fee_rate', '決済手数料率(%)');?></th>
				<th><?php echo $this->Paginator->sort('payment_cycle', '支払いサイクル');?></th>
				<th><?php echo $this->Paginator->sort('amount_include_tax', '成果基準額');?></th>
				<th><?php echo $this->Paginator->sort('is_internal_tax', '消費税計算');?></th>
				<th><?php echo $this->Paginator->sort('modified', '更新日');?></th>
				<th class="actions"><?php echo $this->Html->link('新規追加', 'add', array('class' => 'btn btn-success'));?></th>
			</tr>
			<?php
			foreach ($SettlementCompanies as $SettlementCompany) {
			?>
			<tr>
				<td><?php echo $SettlementCompany['SettlementCompany']['id']; ?>&nbsp;</td>
				<td><?php echo $SettlementCompany['SettlementCompany']['accounting_code']; ?>&nbsp;</td>
				<td><?php echo h($SettlementCompany['SettlementCompany']['name']); ?>&nbsp;</td>
				<td><?php echo h($SettlementCompany['Client']['name']); ?>&nbsp;</td>
				<td><?php echo ($SettlementCompany['Client']['accept_prepay']) ? '可': '不可'; ?>&nbsp;</td>
				<td><?php echo $SettlementCompany['SettlementCompany']['commission_rate']; ?>&nbsp;</td>
				<td><?php echo $SettlementCompany['SettlementCompany']['fee_rate']; ?>&nbsp;</td>
				<td><?php echo $SettlementCompany['SettlementCompany']['payment_cycle']; ?>&nbsp;</td>
				<td><?php echo (!$SettlementCompany['SettlementCompany']['amount_include_tax']) ? '税込': '税抜'; ?>&nbsp;</td>
				<td><?php echo (!$SettlementCompany['SettlementCompany']['is_internal_tax']) ? '外税' : '内税'; ?>&nbsp;</td>
				<td><?php echo $SettlementCompany['SettlementCompany']['modified']; ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('編集', array('action' => 'edit', $SettlementCompany['SettlementCompany']['id']), array('class' => 'btn btn-warning')); ?>
					<?php echo $this->Html->link('削除', array('action' => 'delete', $SettlementCompany['SettlementCompany']['id']), array('class' => 'btn btn-danger')); ?>
					<?php
					if (!empty($SettlementCompany['SettlementCompany']['accounting_code'])) {
						echo $this->Html->link('精算書詳細', array('controller' => 'SettlementSummary', 'action' => 'detail', $SettlementCompany['SettlementCompany']['accounting_code']), array('class' => 'btn btn-info'));
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>

		<?php if (!empty($pageParams) && $pageParams['pageCount'] > 1) { ?>
		<div class="pagination">
			<ul>
				<?php
				if ($this->Paginator->hasPrev()) {
					echo '<li>'.$this->Paginator->prev('< 戻る', array(), null, array('class' => 'prev disabled')). '</li>';
				}

				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

				if ($this->Paginator->hasNext()) {
					echo '<li>'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
				}
				?>
			</ul>
		</div>
		<?php }?>

	</div>
</div>

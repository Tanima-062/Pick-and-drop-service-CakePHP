<div class="row-fluid">
	<div>
		<h3>精算書一覧</h3>
		<?php echo $this->Form->create('SettlementSummary', array('type' => 'get', 'url' => '.')); ?>
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
			<tr>
				<th>計上月</th>
				<td>
					<div>
						<?php
							echo $this->element('selectDatetime', $settlementYear);
							echo $this->element('selectDatetime', $settlementMonth);
						?>
					</div>
				</td>

			</tr>
		</table>
		<br />
		<?php
		echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
		echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		echo $this->Form->end();
		?>
		<table class="table table-bordered">
			<tr class="success">
				<th>Id</th>
				<th>経理用管理コード</th>
				<th>精算管理会社名</th>
				<th>クライアント名</th>
				<th>計上月</th>
				<th>作成日</th>
				<th>同期日時</th>
				<th>精算書プレビュー</th>
				<th>精算書ダウンロード</th>
				<th>成約明細ダウンロード</th>
				<th>同期</th>
				<th>詳細</th>

			</tr>
			<?php
				foreach ($settlementSummaries as $settlementSummary) {
			?>
			<tr>
				<td><?php echo $settlementSummary['SettlementSummary']['id']; ?>&nbsp;</td>
				<td><?php echo $settlementSummary['SettlementSummary']['settlement_company_accounting_code']; ?>&nbsp;</td>
				<td><?php echo h($settlementSummary[0]['settlementCompanyName']); ?>&nbsp;</td>
				<td><?php echo nl2br(h(implode("\n", explode(',', $settlementSummary[0]['clientName'])))); ?>&nbsp;</td>
				<td style="width: auto;"><?php echo h(implode('/', str_split($settlementSummary['SettlementSummary']['settlement_month'], 4))); ?>&nbsp;</td>
				<td>
					<?php
						echo date("Y/m/d",strtotime($settlementSummary['SettlementSummary']['create_datetime'])).'<br>'.date("H:i:s",strtotime($settlementSummary['SettlementSummary']['create_datetime']));
					?>&nbsp;
				</td>
				<td><?php
				if ($settlementSummary['SettlementSummary']['synchronization_status'] == 'SYNCHRONIZED') {
					echo date("Y/m/d",strtotime($settlementSummary['SettlementSummary']['synchronization_datetime'])).'<br>'.date("H:i:s",strtotime($settlementSummary['SettlementSummary']['synchronization_datetime']));
				} else {
					echo '---';
				}
				?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('プレビュー', array('action' => 'preview', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-warning', 'target' => '_blank')); ?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link('精算書DL', array('action' => 'download', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-warning')); ?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link('成約明細DL', array('action' => 'closingDownload', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-warning')); ?>
				</td>
				<td class="actions">
					<?php
					if ($settlementSummary['SettlementSummary']['synchronization_status'] == 'CREATED') {
						echo $this->Html->link('同期', array('action' => 'synchronization', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-danger'));
					}
					?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link('詳細', array('action' => 'detail', $settlementSummary['SettlementSummary']['settlement_company_accounting_code']), array('class' => 'btn btn-info')); ?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>

		<?php 
			$pageParams = $this->Paginator->params();
			if (!empty($pageParams) && $pageParams['pageCount'] > 1) {
		?>
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

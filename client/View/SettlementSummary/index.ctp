<div class="row-fluid">
	<div>
		<h3>精算書ダウンロード</h3>
		<?php
		if(!empty($settlementCompanies)) {
			echo $this->Form->create('settlementCompany', array('inputDefaults' => array('label' => false), 'type' => 'get'));
		?>
		<table class="table table-bordered form-inline" style="width: auto;">
			<tr>
				<th class="alert-success">精算管理会社名</th>
				<td>
				<?php echo $this->Form->input('settlement_company_id',
					[
						'label' => false,
						'options' => $settlementCompanies,
						'required' => true,
						'div' => false,
						'value' => $settlementCompanyId,
						'style' => 'width:100%',
						'empty' => '---'
					]);
				?>
				</td>
			</tr>
		</table>
		<?php echo $this->Form->end();}?>

		<table class="table table-bordered" style="width:100%">
			<tr class="success">
				<th>年月区分</th>
				<th>作成日</th>
				<th>精算書ダウンロード</th>
				<th>成約明細ダウンロード</th>
			</tr>
		<?php
			foreach ($settlementSummaries as $settlementSummary) {
		?>
			<tr>
				<td>
					<?php echo h(implode('/', str_split($settlementSummary['SettlementSummary']['settlement_month'], 4))); ?>&nbsp;
				</td>
				<td>
					<?php echo h(date("Y/m/d H:i:s",strtotime($settlementSummary['SettlementSummary']['notification_datetime']))); ?>&nbsp;
				</td>
				<td class="actions">
					<?php echo $this->Html->link('精算書DL', array('action' => 'download', $settlementSummary['SettlementSummary']['id']),array('class'=>'btn btn-warning')); ?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link('成約明細DL', array('action' => 'closingDownload', $settlementSummary['SettlementSummary']['id']),array('class'=>'btn btn-warning')); ?>
				</td>
			</tr>
		<?php } ?>
		</table>

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
		<?php }?>
	</div>
</div>

<script>
	$('#settlementCompanySettlementCompanyId').change(function () {
		$('#settlementCompanySettlementCompanyId').prop('readonly', true);

		$(this).parents('form').submit();
	});
</script>

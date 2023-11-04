<?php echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/themes/redmond/jquery-ui.min.css"); ?>
<script>

	$(document).on('change', '#CommissionRateStepConditionType', function(){
		const value = $(this).val();
		if (value.endsWith('_NUM')) {
			$('#step_condition_value1_suffix').html('件');
			$('#step_condition_value2_suffix').html('件');
		}
		else if (value.endsWith('_AMOUNT')) {
			$('#step_condition_value1_suffix').html('円');
			$('#step_condition_value2_suffix').html('円');
		}
	});

	$(document).on('change', 'input[name="data[CommissionRate][accounting_condition]"]', function(){
		if ($(this).val() == 'STEP_RATE') {
			$('#step_condition').show();
		}
		else if ($(this).val() == 'FIXED_RATE') {
			$('#step_condition').hide();
		}
	});

	$(document).on('change', 'input[name="data[CommissionRate][contract_condition]"]', function(){
		$('#CommissionRateSettlementCompanyId > option').remove();

		if ($('input[name="data[CommissionRate][contract_condition]"]:checked').val() === 'SETTLEMENT_COMPANY') {
			$('#CommissionRateClientId').change();
		}
	});

	// レンタカー会社が選択されたら、紐づく精算管理会社の一覧を取得する
	$(document).on('change', '#CommissionRateClientId', function(){
		$('#CommissionRateSettlementCompanyId > option').remove();

		if ($('input[name="data[CommissionRate][contract_condition]"]:checked').val() === 'CLIENT') {
			return;
		}

		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/CommissionRates/ajaxGetSettlementCompany',
			data: {
				client_id: $(this).val()
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				$('#CommissionRateSettlementCompanyId').append($('<option>').html('').val(0));
				$.each(res.message, function(index, value) {
					$('#CommissionRateSettlementCompanyId').append($('<option>').html(value).val(index));
				})
			}
			else {
				alert('Error:精算管理会社の取得に失敗しました。' + res.message);
			}
		})
		.fail(function(){
			alert('Fail:システムエラー');
		})
	});

	/*
	 * 初期化
	 */
	$(document).on('ready', function(){
		$('#step_condition').hide();
	});

</script>
<div class="commissionRates form span8">
<?php
echo $this->Form->create('CommissionRate', array('inputDefaults' => array('label' => false)));
?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>販売手数料追加</h3>
	<table class="table table-bordered form-inline">
		<tr>
			<th>条件判定</th>
			<td><?php echo $this->Form->input('contract_condition',[
				'type' => 'radio',
				'options' => Constant::contractCondition(),
				'value' => 'CLIENT',
				'div' => false
				]);
				?>
			</td>
		</tr>
		<tr>
			<th class="span3">クライアント</th>
			<td><?php echo $this->Form->input('client_id',[
					'options' => $clientList,
					'empty' => true,
					'required' => true,
					'div' => false,
					'value' => ''
				]);
				?>
			</td>
		</tr>
		<tr id="settlement_company">
			<th class="span3">精算管理会社</th>
			<td><?php echo $this->Form->input('settlement_company_id',[
				'options' => '',
				'empty' => true,
				'div' => false,
				'value' => ''
				]);
				?>
			</td>
		</tr>
		<tr>
			<th>適用期間</th>
			<td>
				開始
				<?php
					echo $this->Form->input('apply_term_from', array(
					'type' => 'date',
					'dateFormat' => 'YM',
					'div' => false,
					'class' => 'span2',
					'monthNames' => false,
					'empty' => '--',
					'separator' => ['年','月']
				));
				?>&nbsp;&nbsp;
				終了
				<?php
				echo $this->Form->input('apply_term_to', array(
					'type' => 'date',
					'dateFormat' => 'YM',
					'div' => false,
					'class' => 'span2',
					'monthNames' => false,
					'empty' => '--',
					'separator' => ['年','月']
				));
				?>
			</td>
		</tr>
		<tr>
			<th>計上条件</th>
			<td><?php echo $this->Form->input('accounting_condition',[
					'type' => 'radio',
					'options' => Constant::accountingCondition(),
					'value' => 'FIXED_RATE',
					'div' => false
				]);
				?>
			</td>
		</tr>
		<tr id="step_condition">
			<th>段階条件指標</th>
			<td>
				<?php echo $this->Form->input('step_condition_type', [
					'options' => Constant::stepConditionType(),
					'class' => 'span3',
					'div' => false,
					'empty' => '--',
				]); ?>&nbsp;&nbsp;
				<?php echo $this->Form->input('step_condition_value1', [
					'type' => 'text',
					'class' => 'text-right span2',
					'after' => ' <span id="step_condition_value1_suffix">件</span>',
					'maxlength' => 10,
					'div' => false
				]); ?>
				 以上 &nbsp;-&nbsp;
				<?php echo $this->Form->input('step_condition_value2', [
					'type' => 'text',
					'class' => 'text-right span2',
					'after' => ' <span id="step_condition_value2_suffix">件</span>',
					'maxlength' => 10,
					'div' => false
				]); ?>
				 未満
			</td>
		</tr>
		<tr>
			<th>販売手数料</th>
			<td>
				<?php echo $this->Form->input('commission_rate', [
					'type' => 'text',
					'required' => true,
					'value' => '',
					'class' => 'text-right span2',
					'after' => ' <span id="commission_rate_suffix">%</span>',
					'maxlength' => 10
				]); ?>
			</td>
		</tr>
		<tr>
		  <th>公開/非公開</th>
		  <td>
		    <?php echo $this->Form->input('is_published', ['options'=>$isPublishedOptions, 'div' => false, 'class' => 'span2']);?>
		  </td>
		</tr>
	</table>
	<div class="right">
	<?php
	echo $this->Form->submit('追加',array('class'=>'btn btn-success'));
	echo $this->Form->end();
	?>
	</div>
</div>

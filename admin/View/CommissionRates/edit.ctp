<?php echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/themes/redmond/jquery-ui.min.css"); ?>
<script>
	$(document).on('change', '#CommissionRateStepConditionType', function(){
		changeStepConditionValueSuffix($(this));
	});

	$(document).on('change', 'input[name="data[CommissionRate][accounting_condition]"]', function(){
		displayStepCondition($(this));
	});

	function displayStepCondition(obj) {
		if (obj.val() == 'STEP_RATE') {
			$('#step_condition').show();
		}
		else if (obj.val() == 'FIXED_RATE') {
			$('#step_condition').hide();
		}
	}

	function changeStepConditionValueSuffix(obj) {
		const value = obj.val();
		if (value.endsWith('_NUM')) {
			$('#step_condition_value1_suffix').html('件');
			$('#step_condition_value2_suffix').html('件');
		}
		else if (value.endsWith('_AMOUNT')) {
			$('#step_condition_value1_suffix').html('円');
			$('#step_condition_value2_suffix').html('円');
		}
	}

	/*
	 * 初期化
	 */
	$(document).on('ready', function(){
		displayStepCondition($('input[name="data[CommissionRate][accounting_condition]"]:checked'));

		changeStepConditionValueSuffix($('#CommissionRateStepConditionType'));
	});

</script>
<div class="commissionRates span8">
<?php
echo $this->Form->create('CommissionRate', array('inputDefaults' => array('label' => false)));
echo $this->Form->input('id');
echo $this->Form->input('client_id', ['type' => 'hidden', 'value' => $this->data['CommissionRate']['client_id']]);
?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>販売手数料編集</h3>
	<table class="table table-bordered form-inline">
		<tr>
			<th class="span3">管理番号</th>
			<td> <?php echo $this->data['CommissionRate']['id']; ?> </td>
		</tr>
		<tr>
			<th>条件判定</th>
			<td><?php echo $this->Form->input('contract_condition',[
				'type' => 'radio',
				'options' => Constant::contractCondition(),
				'value' => $this->data['CommissionRate']['contract_condition'],
				'div' => false
				]);
				?>
			</td>
		</tr>
		<tr>
			<th>クライアント</th>
			<td><?php echo $this->data['Client']['name']; ?></td>
		</tr>
		<tr id="settlement_company">
			<th class="span3">精算管理会社</th>
			<td><?php echo $this->Form->input('settlement_company_id',[
				'options' => $settlementCompanies,
				'empty' => true,
				'div' => false,
				'value' => $this->data['CommissionRate']['settlement_company_id']
				]);
				?>
			</td>
		</tr>
		<tr>
			<th>適用期間</th>
			<td>
				開始:
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
				終了:
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
					'value' => $this->data['CommissionRate']['commission_rate'],
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
		<tr>
			<th>更新者</th>
			<td> <?php echo $this->data['Staff']['name']; ?> </td>
		</tr>
		<tr>
			<th>更新日時</th>
			<td> <?php echo $this->data['CommissionRate']['modified']; ?> </td>
		</tr>
		<tr>
			<th>作成日時</th>
			<td> <?php echo $this->data['CommissionRate']['created']; ?> </td>
		</tr>
	</table>
	<div class="right">
	<?php
	echo $this->Form->submit('保存',array('class'=>'btn btn-success'));
	echo $this->Form->end();
	?>
	</div>
</div>

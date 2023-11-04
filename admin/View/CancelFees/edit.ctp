<?php echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/themes/redmond/jquery-ui.min.css"); ?>
<script>
	/*
	 * 出発前が選択されたら「期限」の項目を表示する
	 */
	$(document).on('change', 'input[name="data[CancelFee][is_after_departure]"]', function(){
		if ($(this).val() == 0) { // 出発前
			$('#cancel_limit_show').show();
		}
		else {// 出発後
			$('#cancel_limit_show').hide();
		}
	});

	/*
	 * 1.計上単位の定率が選択されたら「端数処理」「最低額」「上限額」の項目を表示する(それ以外は非表示)
	 * 2.計上単位の定率が選択されたらキャンセル料の「円」を「%」に変更する(それ以外は円)
	 */
	$(document).on('change', 'input[name="data[CancelFee][cancel_fee_unit]"]', function(){
		if ($(this).val() == 'RESERVE_FIXED_RATE' || $(this).val() == 'RESERVE_BASIC_RATE') {
			$('#fraction_show').show();
			$('#cancel_fee_min_show').show();
			$('#cancel_fee_max_show').show();

			$('#cancel_fee_suffix').html('%');
		}
		else {
			$('#fraction_show').hide();
			$('#cancel_fee_min_show').hide();
			$('#cancel_fee_max_show').hide();

			$('#cancel_fee_suffix').html('円');
		}
	});

	/*
	 * 販売方法の選択によって取消手続料金のreadonly属性を切り替える
	 * 募集型企画の場合は編集不可
	 */
	$(document).on('change', 'input:radio[name="data[CancelFee][sales_type]"]', function() {
		addReadOnlyAdvCancelFee($(this).val());
	});

	/*
	 * 初期化
	 */
	$(document).on('ready', function(){
		if ($('input[name="data[CancelFee][is_after_departure]"]:eq(0)').prop('checked')) {
			$('#cancel_limit_show').show();
		}
		else {
			$('#cancel_limit_show').hide();
		}

		if ($('input[name="data[CancelFee][cancel_fee_unit]"]:eq(0)').prop('checked')) {
			$('#fraction_show').hide();
			$('#cancel_fee_min_show').hide();
			$('#cancel_fee_max_show').hide();
			$('#cancel_fee_suffix').html('円');
		}
		else {
			$('#fraction_show').show();
			$('#cancel_fee_min_show').show();
			$('#cancel_fee_max_show').show();
			$('#cancel_fee_suffix').html('%');
		}

		addReadOnlyAdvCancelFee($('input:radio[name="data[CancelFee][sales_type]"]:checked').val());

		const pickeroption = {
			dateFormat: 'yy-mm-dd',
			monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			dayNames: [ '日' , '月', '火', '水', '木', '金', '土'],
			dayNamesShort: [ '日' , '月', '火', '水', '木', '金', '土'],
			dayNamesMin: [ '日' , '月', '火', '水', '木', '金', '土'],
			prevText: '前へ',
			nextText: '次へ',
		};

		$('#CancelFeeApplyTermFrom').datepicker(
			pickeroption
		);

		$('#CancelFeeApplyTermTo').datepicker(
			pickeroption
		);

	});

	/* 取消手続料金のreadonly付加判定 */
	function addReadOnlyAdvCancelFee(salesType) {
		$('#CancelFeeAdvCancelFee').prop(
			'readonly',
			(salesType === 'AGENT-ORGANIZED') ? true : false
		);
	}
</script>
<div class="cancelFees span8">
<?php
echo $this->Form->create('CancelFee', array('inputDefaults' => array('label' => false)));
echo $this->Form->input('id');
echo $this->Form->input('client_id', ['type' => 'hidden', 'value' => $this->data['CancelFee']['client_id']]);
?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>キャンセル料編集</h3>
	<table class="table table-bordered form-inline">
		<tr>
			<th class="span3">管理番号</th>
			<td> <?php echo $this->data['CancelFee']['id']; ?> </td>
		</tr>
		<tr>
			<th>レンタカー会社</th>
			<td><?php echo $this->data['Client']['name']; ?></td>
		</tr>
		<tr>
			<th>販売方法</th>
			<td><?php echo $this->Form->input('sales_type',[
					'type' => 'radio',
					'options' => Constant::salesType(),
					'value' => $this->data['CancelFee']['sales_type'],
					'div' => false
				]);
				?>
			</td>
		</tr>
		<tr>
			<th>適用期間起点</th>
			<td><?php echo $this->Form->input('apply_term_point',[
						'type' => 'radio',
						'options' => [
							'0' => 'キャンセル日',
							'1' => '出発日'
						],
						'value' => $this->data['CancelFee']['apply_term_point'],
						'div' => false
					]);
				?>
			</td>
		</tr>
		<tr>
			<th>適用期間</th>
			<td><div>
				<span class='input-prepend'><?php echo $this->Form->input('apply_term_from', ['type' => 'text', 'required' => true]); ?></span>&nbsp;〜&nbsp;
				<span class='input-append add-on'><?php echo $this->Form->input('apply_term_to', ['type' => 'text', 'required' => true]); ?></span>
				</div>
			</td>
		</tr>
		<tr>
			<th>出発前・出発後</th>
			<td><?php echo $this->Form->input('is_after_departure',[
						'type' => 'radio',
						'options' => [
							'0' => '出発前',
							'1' => '出発後'
						],
						'value' => $this->data['CancelFee']['is_after_departure'],
						'div' => false
					]);
				?>
			</td>
		</tr>
		<tr id='cancel_limit_show'>
			<th>期限</th>
			<td>
				<?php echo $this->Form->input('from_cancel_limit', [
					'type' => 'text',
					'div' => false,
					'class' => 'span1 pagination-centered',
					'maxlength' => 3
				]); ?>
				<?php echo $this->Form->input('from_cancel_limit_unit',[
						'options' => Constant::cancelLimitUnit(),
						'selected' => $this->data['CancelFee']['from_cancel_limit_unit'],
						'div' => false,
						'class' => 'span2'
				]);
				?>
				<span style='margin-right:10px;margin-left:10px'>〜</span>
				<?php echo $this->Form->input('cancel_limit', [
					'type' => 'text',
					'div' => false,
					'class' => 'span1 pagination-centered',
					'maxlength' => 3
				]); ?>
				<?php echo $this->Form->input('cancel_limit_unit',[
						'options' => Constant::cancelLimitUnit(),
						'selected' => $this->data['CancelFee']['cancel_limit_unit'],
						'div' => false,
						'class' => 'span2'
				]);
				?>
			</td>
		</tr>
		<tr>
			<th>計上単位</th>
			<td><?php echo $this->Form->input('cancel_fee_unit',[
						'type' => 'radio',
						'options' => Constant::cancelFeeUnit(),
						'value' => $this->data['CancelFee']['cancel_fee_unit'],
						'div' => false
					]);
				?>
			</td>
		</tr>
		<tr>
			<th>キャンセル料</th>
			<td>
				<?php echo $this->Form->input('cancel_fee', [
						'type' => 'text',
						'required' => true,
						'class' => 'text-right span2',
						'after' => ' <span id="cancel_fee_suffix">円</span>',
						'maxlength' => 10
					]); ?>
			</td>
		</tr>
		<tr id='fraction_show'>
			<th>端数処理</th>
			<td><?php echo $this->Form->input('fraction_unit',[
						'options' => Constant::fractionUnit(),
						'selected' => $this->data['CancelFee']['fraction_unit'],
						'div' => false,
						'class' => 'text-right span2'
					]);
				?> 円単位 
				<?php echo $this->Form->input('fraction_round',[
						'options' => Constant::fractionRound(),
						'selected' => $this->data['CancelFee']['fraction_round'],
						'div' => false,
						'class' => 'span2'
					]);
				?>
			</td>
		</tr>
		<tr id='cancel_fee_min_show'>
			<th>最低額</th>
			<td>
				<?php echo $this->Form->input('cancel_fee_min', [
					'type' => 'text',
					'class' => 'text-right span2',
					'maxlength' => 10,
					'after' => ' 円'
				]); ?>
			</td>
		</tr>
		<tr id='cancel_fee_max_show'>
			<th>上限額</th>
			<td>
				<?php echo $this->Form->input('cancel_fee_max', [
					'type' => 'text',
					'class' => 'text-right span2',
					'maxlenght' => 10,
					'after' => ' 円'
				]); ?>
			</td>
		</tr>
		<tr>
			<th>取消手続料金</th>
			<td>
				<?php echo $this->Form->input('adv_cancel_fee', [
					'type' => 'text',
					'class' => 'text-right span2',
					'maxlength' => 10,
					'after' => ' 円'
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
			<td> <?php echo $this->data['CancelFee']['modified']; ?> </td>
		</tr>
		<tr>
			<th>作成日時</th>
			<td> <?php echo $this->data['CancelFee']['created']; ?> </td>
		</tr>
	</table>
	<div class="right">
	<?php
	echo $this->Form->submit('保存',array('class'=>'btn btn-success'));
	echo $this->Form->end();
	?>
	</div>
</div>

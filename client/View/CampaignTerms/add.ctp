<script>
$(function() {
	$.datepicker.setDefaults($.datepicker.regional["ja"]);
	var startOption = {
			numberOfMonths: 1,
			changeMonth: true,
			changeYear: true,
			monthNames: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
			monthNamesShort: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
			dayNamesShort: ['日','月','火','水','木','金','土'],
			dayNamesMin: ['日','月','火','水','木','金','土'],
			showMonthAfterYear: true,
			dateFormat: 'yy-mm-dd',
	};
	$(".startDatePicker").datepicker(startOption);
	$(".endDatePicker").datepicker(startOption);
	$(document).on('click', '.add_term_button', function(){
		term_count = $('.term_new').length;
		var termDOM = $(this).parents('tr');
		str = termDOM.prop('outerHTML');
		str = str.replace(new RegExp(term_count,'g'), term_count+1);
		str = str.replace(new RegExp(term_count-1,'g'), term_count);
		termDOM.after(str);
		termDOM.next('tr').find('select').removeAttr('required');
		// 2個目以降ボタン削除必須
		$(this).remove();
		term_data = $('.add_term_button').parents('tr');
		// コピー後のデータ値を空にする
		term_data.find('[type="hidden"]').each(function(){
			$(this).val('');
		})
		term_data.find('[type="text"]').each(function(){
			$(this).val('');
		})
		term_data.find('[type="checkbox"]').each(function(){
			$(this).prop('checked', false);
		})
		term_data.find('[class*="hasDatepicker"]').each(function(){
			$(this).removeClass('hasDatepicker');
			$(this).datepicker(startOption);
		})
	});
});
</script>
<style>
table {
	font-size: 14px;
}
.checkbox {
	float:left;
	width:22px;
	margin-left:2px;
}
.error-message {
	color:red;
}
.float-left {
	float:left;
}
.mr-15 {
	margin-right:15px;
}
</style>
<?php echo $this->Form->create('Campaign', array('inputDefaults' => array('label' => false, 'div' => false,),)); ?>
<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
<?php $term_count = ($this->request->data['Campaign']['termCount'] ? $this->request->data['Campaign']['termCount'] : $this->request->data['Campaign']['termDefaultCount']); ?>
<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
<h3><?php echo __('キャンペーン期間登録'); ?></h3>
<table class="table table-bordered table-striped table-hover">
	<tbody>
		<tr>
			<th>キャンペーン期間名</th>
			<td><?php echo $this->Form->input('name', array('required')); ?></td>
		</tr>
<?php for ($i = 0; $i < $term_count; $i++) { ?>
	<?php
	if ($i == $term_count - 1) {
		$term_btn = "<span class='add_term_button'>+</span>";
	} else {
		$term_btn = "";
	}
	?>
		<tr class="term_new">
			<th style="position:relative;">対象期間<?=$i + 1?><?php echo $term_btn; ?></th>
			<td>
				<div class="float-left mr-15">
					<?php echo $this->Form->input("CampaignTerm.{$i}.start_date", array('type' => 'text', 'class' => 'startDatePicker', 'required' => false)); ?> ～ 
					<?php echo $this->Form->input("CampaignTerm.{$i}.end_date", array('type' => 'text', 'class' => 'endDatePicker', 'required' => false)); ?>
				</div>
				<div>
					<?php echo $this->Form->input("CampaignTerm.{$i}.week", [
						'type' => 'select', 
						'multiple'=> 'checkbox',
						'options' => Constant::weekJp(),
						'div' => false
					]); 
					?>	
				</div>
			</td>
		</tr>
<?php } ?>
		<tr>
			<th>公開範囲</th>
			<td><?php echo $this->Form->select('scope', $scopeList, $scopeOption); ?></td>
		</tr>
	</tbody>
</table>
<?php echo $this->Form->button('登録', array('type' => 'submit', 'class' => 'btn btn-success')); ?>
<?php echo $this->Form->end(); ?>
<?php echo $this->Html->link(__('戻る'), $referer, array('class' => 'btn btn-warning')); ?>

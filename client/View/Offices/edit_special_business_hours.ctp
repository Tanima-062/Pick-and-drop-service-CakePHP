<script>
$(function() {
	$("#start_day").datepicker({
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 3,
		showButtonPanel: true,
		monthNames: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
		altField: "#end_day, .start_day",
		onClose: function(){
			var value = $('#start_day').val();
			$("#end_day").datepicker('option', 'minDate', value);
		}
	});
	$("#end_day").datepicker({
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 3,
		monthNames: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
		minDate: 'd',
		altField: ".end_day",
	});
});
</script>

<div>
	<h3><?php echo $officeData['Office']['name']; ?>（特別営業時間・編集）</h3>
	<?php echo $this->Form->create(false, array(
			'inputDefaults' => array(
					'label' => false,
					'div' => false,
			),
	)); ?>

	<div class="well">
		<p>期間設定</p>

		<div>
			<?php echo $this->Form->input('start_day', array('type' => 'text', 'value' => $officeBusinessHour['start_day'], 'required' => true, 'placeholder' => '開始日', 'style' => 'padding: 4px 6px;')); ?>
			&emsp;～&emsp;
			<?php echo $this->Form->input('end_day', array('type' => 'text', 'value' => $officeBusinessHour['end_day'], 'required' => true, 'placeholder' => '終了日', 'style' => 'padding: 4px 6px;')); ?>
		</div>
		<div class="clearfix">
			<label for="delete_flg" class="checkbox" style="float:right;"><?php echo $this->Form->checkbox('delete_flg', array('hiddenField' => false)); ?>削除</label>
		</div>
	</div>

	<table class="table table-bordered table-striped table-condensed">
		<?php
		foreach ($weekArray as $key => $week) {
		?>
		<tr>
			<th style="<?php echo $key == 'hol' ? 'background:#fddfe2;' : ''; ?>"><?php echo $week; ?></th>
			<td style="<?php echo $key == 'hol' ? 'background:#fddfe2;' : ''; ?>">
			<?php echo $this->Form->hidden('id', array('value' => $officeBusinessHour['id'])); ?>
			<?php echo $this->Form->hidden('start_day', array('class' => 'start_day', 'value' => $officeBusinessHour['start_day'])); ?>
			<?php echo $this->Form->hidden('end_day', array('class' => 'end_day', 'value' => $officeBusinessHour['end_day'])); ?>
			<?php echo $this->Form->hour('week_day.' . $key . '.' .$key.'_hours_from', true, array('empty' => '---', 'class' => 'span2','value'=>$officeBusinessHour["{$key}".'_hours_from'])); ?>時
			<?php echo $this->Form->minute('week_day.'. $key . '.' .$key.'_hours_from', array('empty' => '---', 'class' => 'span2','value'=>$officeBusinessHour["{$key}".'_hours_from'])); ?>分
			&emsp;～&emsp;
			<?php echo $this->Form->hour('week_day.'. $key . '.' . $key.'_hours_to', true, array('empty' => '---', 'class' => 'span2','value'=>$officeBusinessHour["{$key}".'_hours_to'])); ?>時
			<?php echo $this->Form->minute('week_day.'. $key .'.'.$key.'_hours_to', array('empty' => '---', 'class' => 'span2','value'=>$officeBusinessHour["{$key}".'_hours_to'])); ?>分
			</td>
		</tr>
		<?php } ?>
	</table>

	<p><?php echo $this->Form->button('編集登録', array('type' => 'submit', 'class' => 'btn btn-warning')); ?></p>

	<?php echo $this->Form->end(); ?>
</div>
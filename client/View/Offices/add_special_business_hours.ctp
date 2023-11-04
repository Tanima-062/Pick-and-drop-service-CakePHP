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
	<h3><?php echo $officeData['Office']['name']; ?>（特別営業時間・新規登録）</h3>
	<?php echo $this->Form->create(false, array(
			'inputDefaults' => array(
					'label' => false,
					'div' => false,
			),
	)); ?>

	<div class="well">
		<p>期間設定</p>
		<?php echo $this->Form->input('start_day', array('type' => 'text', 'required' => true, 'placeholder' => '開始日', 'style' => 'padding: 4px 6px;')); ?>
		&emsp;～&emsp;
		<?php echo $this->Form->input('end_day', array('type' => 'text', 'required' => true, 'placeholder' => '終了日', 'style' => 'padding: 4px 6px;')); ?>
	</div>

	<table class="table table-bordered table-striped table-condensed">

		<?php foreach ($weekArray as $key => $week) { ?>
		<tr>
			<th style="<?php echo $key == 'hol' ? 'background:#fddfe2;' : ''; ?>"><?php echo $week; ?></th>
			<td style="<?php echo $key == 'hol' ? 'background:#fddfe2;' : ''; ?>">
			<?php echo $this->Form->hour('week_day.' . $key . '.' .$key.'_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
			<?php echo $this->Form->minute('week_day.'. $key . '.' .$key.'_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
			&emsp;～&emsp;
			<?php echo $this->Form->hour('week_day.'. $key . '.' . $key.'_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
			<?php echo $this->Form->minute('week_day.'. $key .'.'.$key.'_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
			</td>
		</tr>
		<?php } ?>
	</table>

	<p><?php echo $this->Form->button('登録', array('type' => 'submit', 'class' => 'btn btn-success')); ?></p>

	<?php echo $this->Form->end(); ?>
</div>
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
	$("#startDatePicker").datepicker(startOption);
	$("#endDatePicker").datepicker(startOption);
});
</script>
<?php echo $this->Form->create('DisclaimerCompensation', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => true,),)); ?>
<h3><?php echo __('新規登録 免責補償料金'); ?></h3>
<table class="table table-bordered table-striped table-hover">
	<tbody>
		<tr>
			<th>車両クラス</th>
			<td><?php echo $this->Form->input('car_class_id'); ?></td>
		</tr>
		<tr>
			<th>開始日</th>
			<td><?php echo $this->Form->input('start_date', array('type' => 'text', 'id' => 'startDatePicker')); ?></td>
		</tr>
		<tr>
			<th>終了日</th>
			<td><?php echo $this->Form->input('end_date', array('type' => 'text', 'id' => 'endDatePicker')); ?></td>
		</tr>
		<tr>
			<th>料金</th>
			<td>
				<?php echo $this->Form->input('price', array('min' => 0, 'step' => 1)); ?> 円
				<?php echo $this->Form->input('period_flg', array('type' => 'radio', 'options' => $periodList, 'legend' => false, 'label' => true, 'div' => true)); ?>
			</td>
		</tr>
		<tr>
			<th>料金上限日数</th>
			<td>
			<?php echo $this->Form->input('period_limit', array('required' => false, 'min' => 0, 'max' => 31, 'step' => 1)); ?> 日<br>
			<span class="red">※上限を設定しない場合は、0日で登録してください。</span>
			</td>
		</tr>
	</tbody>
</table>
<?php echo $this->Form->button('新規登録', array('type' => 'submit', 'class' => 'btn btn-success')); ?>
<?php echo $this->Form->end(); ?>
<?php echo $this->Html->link(__('一覧'), array('action' => 'index'), array('class' => 'btn btn-primary')); ?>
<style>
input[type="text"],
input[type="number"]{
	width: 100%;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}
.table {
	table-layout:fixed;
}
.table tr th,
.table tr td {
	text-align: center;
	border: solid #ddd 1px;
}
</style>

<h3>時間制料金詳細設定</h3>
<div style="margin-bottom:10px; color: #f00; font-weight: bold; font-size: 150%;">
※免責補償抜きの価格を設定ください。
</div>
<?php echo $this->Form->create('CommodityItem',array('novalidate'=>false, 'inputDefaults' => array('label' => false, 'div' => false)));?>
<table class="table table-condensed">
	<?php
	$arrayChunk = array_chunk($detailCommodityScheduleArray, 6);
	foreach ($arrayChunk as $chunkKey => $detailCommoditySchedule) { ?>
	<tr class="success">
		<?php foreach ($detailCommoditySchedule as $thKey => $value) {
			if ($value == 0) {
				$value = '以後一日';
			} else if ($value == 25) {
				$value = '超過1時間';
			} else {
				$value = $value.'時間';
			}
		?>
		<th><?php echo $value; ?></th>
		<?php } ?>
	</tr>
	<tr>
		<?php foreach ($detailCommoditySchedule as $key => $value) { ?>
		<td>
		<?php echo $this->Form->input("CommodityPrice.{$value}.commodity_price", array(
				'type' => 'number',
				'label' => false,
				'min' => 0,
				'required' => true,
		)); ?>
		</td>
		<?php } ?>
	</tr>
	<?php } ?>
</table>
<div>
	<?php echo $this->Form->hidden('CommodityItem.id'); ?>
	<?php echo $this->Form->submit('設定保存',array('name'=>'save','class'=>'btn btn-success'));?>
</div>
<?php echo $this->Form->end(); ?>


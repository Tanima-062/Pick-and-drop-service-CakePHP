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
<table class="table-bordered table-striped table-condensed">
	<tr>
		<th class="span4">キャンペーン期間名</th>
		<td style="width:250px;"><?php echo $campaignList[$campaignId]; ?></td>
	</tr>
	<tr>
		<th class="span4">対象期間</th>
		<td style="width:250px;">
		<?php
			if (!empty($campaignTermList[$campaignId])) {
				$terms = $campaignTermList[$campaignId];
				$termCount = count($terms);
				$i = 0;
				foreach ($terms as $term) {
					echo $term['start_date'].'～'.$term['end_date'];
					if (++$i < $termCount) {
						echo '<br>';
					}
				}
			}
		?>
		</td>
	</tr>
</table>
<div style="margin-top:10px;margin-bottom:10px; color: #f00; font-weight: bold; font-size: 150%;">
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
		<?php echo $this->Form->input("CommodityCampaignPrice.{$campaignId}.{$value}.commodity_price", array(
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
	<?php echo $this->Form->hidden("CommodityCampaignPrice.{$campaignId}.delete_flg"); ?>
	<?php echo $this->Form->hidden('display_time', array('value' => $DisplayTime, 'name' => 'display_time')); ?>
	<?php echo $this->Form->submit('設定保存',array('name'=>'save4','class'=>'btn btn-success'));?>
</div>
<?php echo $this->Form->end(); ?>


<h3>月別商品成約状況</h3>

<h4>検索条件</h4>
<?php echo $this->Form->create('Statistic'); ?>
	<table class="table table-bordered table-condensed">
		<tr>
			<th>年月</th>
			<td colspan="3"><?php echo $this->Form->select('year', $yearArray); ?>年&emsp;<?php echo $this->Form->select('month', $monthArray); ?>月<span style="color: red;">※必須</span>
			<?php if (isset($error)) { ?>
			<div style="color:red;"><?php echo $error; ?></div>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<th>管理番号</th>
			<td colspan="3">
			<?php echo $this->Form->input('commodity_key', array(
					'style' => 'height: 30px;line-height: 30px;width: 50%;',
					'placeholder' => '000000',
					'label' => false)); ?>
			</td>
		</tr>
		<tr>
			<th>地域</th>
			<td><?php echo $this->Form->select('stock_group_id', $stockGroups); ?></td>
			<th>車両クラス</th>
			<td><?php echo $this->Form->select('car_class_id', $carClassLists); ?></td>
		</tr>
	</table>
	<?php echo $this->Form->submit('検索', array('name' => 'search', 'class' => 'btn btn-primary')); ?>
<?php echo $this->Form->end(); ?>


<h4>統計情報</h4>
<?php if (isset($statistics) && !empty($statistics)) { ?>
<table id="statistic" class="table table-striped table-bordered table-condensed" style="font-size:12px;">
	<tr>
		<th>管理番号</th>
		<th style="width:40%;">商品名</th>
		<th>地域</th>
		<th>車両クラス</th>
		<th>成約数</th>
		<th>売上</th>
		<th>客単価</th>
	</tr>
	<?php foreach ($statistics as $key => $statistic) { ?>
	<tr>
		<td><?php echo $statistic['commodity_items']['commodity_key']; ?></td>
		<td style="text-align:left;"><?php echo $statistic['commodity_items']['commodity_name']; ?></td>
		<td style="text-align:left;"><?php echo $statistic['commodity_items']['stock_group_name']; ?></td>
		<td style="text-align:left;"><?php echo $statistic['commodity_items']['car_class_name']; ?></td>
		<td><?php echo $statistic[0]['reservation_count']; ?></td>
		<td>￥<?php echo number_format($statistic[0]['price']); ?></td>
		<td>￥<?php echo number_format($statistic[0]['price'] / $statistic[0]['reservation_count']); ?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>
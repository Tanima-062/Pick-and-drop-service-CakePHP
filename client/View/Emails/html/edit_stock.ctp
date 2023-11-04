<!DOCTYPE html>
<html>
<head>
</head>
<body>
<p style="margin:5px;">
<b><?php echo $clientName; ?></b>様の在庫数が変更されました。<br/>
</p>

<a href="<?php echo $url; ?>"><?php echo $url; ?></a><br/><br/>

<table>

	<tr style="background-color:black;color:white;">
		<th colspan=<?php echo $lastDay+4; ?>><?php echo $date; ?></th>
	</tr>

	<tr style="background-color:lightseagreen;">
		<th>地域</th>
		<th>車両クラス</th>
		<th style="width:50px;">&nbsp;</th>
		<?php for ($i = 1; $i <= $lastDay; $i++) { ?>
			<td><?php echo sprintf("%02d",$i); ?></td>
		<?php } ?>
		<th>合計</th>
	</tr>

	<?php foreach($newData as $key => $val) { ?>

		<?php if (($key % 2) == 0) { ?>
		<tr style="background-color:lightsteelblue;">
		<?php } else { ?>
		<tr style="background-color:lightgoldenrodyellow;">
		<?php } ?>
			<td rowspan=2><?php echo $val['StockGroup']['name']; ?></td>
			<td rowspan=2><?php echo $val['CarClass']['name']; ?></td>
			<td>変更前</td>
			<?php $oldTotal = 0; ?>
			<?php foreach ($oldData[$key]['CarClassStock'] as $oldStock) { ?>
				<td><?php echo $oldStock['stock_count']; ?></td>
				<?php $oldTotal += $oldStock['stock_count']; ?>
			<?php } ?>
			<td><?php echo $oldTotal; ?></td>
		</tr>

		<?php if (($key % 2) == 0) { ?>
		<tr style="background-color:lightsteelblue;">
		<?php } else { ?>
		<tr style="background-color:lightgoldenrodyellow;">
		<?php } ?>
			<td>変更後</td>
			<?php $total = 0; ?>
			<?php foreach ($val['CarClassStock'] as $day => $stock) { ?>
				<?php $diff = $oldData[$key]['CarClassStock'][$day]['stock_count'] - $stock['stock_count']; ?>

				<?php if ($diff != 0) { ?>
					<td style="background-color: #FFCAF8;"><?php echo $stock['stock_count']; ?></td>
				<?php } else { ?>
					<td><?php echo $stock['stock_count']; ?></td>
				<?php } ?>
				<?php $total += $stock['stock_count']; ?>
			<?php } ?>
			<td><?php echo $total; ?></td>
		</tr>
	<?php } ?>
</table>
</body>
</html>
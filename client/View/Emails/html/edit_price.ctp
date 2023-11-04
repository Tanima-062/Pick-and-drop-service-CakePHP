<!DOCTYPE html>
<html>
<head>
</head>
<body>
<p style="margin:5px;">
<b><?php echo $clientName; ?></b>様の料金設定が変更されました。<br/>
商品名：<?php echo $commodityName; ?><br/>
車両クラス：<?php echo $carClassName; ?><br/>
</p>

<a href="<?php echo $url; ?>"><?php echo $url; ?></a><br/><br/>

<table style="width:200px;">
<?php
foreach($newData as $key => $data) {
	foreach($data as $key2 => $newVal) {
		$price = array();
		$price[$key2] = $newVal['commodity_price'];
?>

	<tr style="background-color:black;color:white;">
		<th colspan="2">
		<?php
		if(!empty($priceRank['nomal'][$key])) {
			 echo $priceRank['nomal'][$key];
		} else if(!empty($priceRank['campaign'][$key])) {
			 echo $priceRank['campaign'][$key];
		}
		 ?>
		</th>
	</tr>

	<tr style="background-color:lightseagreen;">
		<th>変更前</th>
		<th>変更後</th>
	</tr>

	<?php
	 foreach ($price as $day => $val) {
	 	if(empty($val)) {
	 		$val = 0;
	 	}
	 	 ?>

		<tr style="background-color: #D4F8F8;">
			<?php if (isset($oldData[$key][$day])) { ?>
				<td>
					<?php echo number_format($oldData[$key][$day]['commodity_price']); ?>
				</td>
				<?php
				$diff = $oldData[$key][$day]['commodity_price'] - $val;
				?>
			<?php } else { ?>

				<td>0</td>
				<?php $diff = 0 - $val; ?>

			<?php } ?>

			<?php if ($diff != 0) { ?>
				<td style="background-color: #FFCAF8;">
					<?php echo number_format($val); ?>
				</td>
			<?php } else { ?>
				<td><?php echo number_format($val); ?></td>
			<?php } ?>
		</tr>
	<?php } ?>

<?php
	}
	}
 ?>
</table>
</body>
</html>
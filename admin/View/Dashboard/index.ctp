<style>
table {
	font-size: 120%;
}
th, td {
	padding: 5px 10px;
}
th {
	text-align: left;
	padding-top: 10px;
}
td {
	text-align: right;
}
</style>

<h4>過去最高売上</h4>
<span style="float:right">更新日時：<?= $modified; ?></span>
<table>
<?php
if(!empty($summary)){
	foreach ($tables as $k => $v) {
?>
	<tr>
		<th colspan="4"><?=$v[0]?></th>
		<th></th>
		<th colspan="4"><?=$v[1]?></th>
	</tr>
	<?php
		$summary_left = $summary[$k * 2];
		$summary_right = $summary[$k * 2 + 1];
		
		for ($i = 0; $i < $rank; $i++) {
			$left = !empty($summary_left[$i]) ? $summary_left[$i][0] : array('record' => '-', 'cnt' => 0, 'total' => 0);
			$right = !empty($summary_right[$i]) ? $summary_right[$i][0] : array('record' => '-', 'cnt' => 0, 'total' => 0);
	?>
	<tr>
		<td><?=$i + 1?>位</td>
		<?php
			if (strpos($v[0], '月別') !== false || $left['record'] == '-') {
		?>
		<td><?=$left['record']?></td>
		<?php
			} else {
		?>
		<td><?=$left['record']?> (<?=$week[date('w', strtotime($left['record']))]?>)</td>
		<?php
			}
		?>
		<td><?=number_format($left['cnt'])?>件</td>
		<td><?=number_format($left['total'])?>円</td>
		<td></td>
		<td><?=$i + 1?>位</td>
		<?php
			if (strpos($v[1], '月別') !== false || $right['record'] == '-') {
		?>
		<td><?=$right['record']?></td>
		<?php
			} else {
		?>
		<td><?=$right['record']?> (<?=$week[date('w', strtotime($right['record']))]?>)</td>
		<?php
			}
		?>
		<td><?=number_format($right['cnt'])?>件</td>
		<td><?=number_format($right['total'])?>円</td>
	</tr>
	<?php
		}
	?>
<?php
	}
}
?>
</table>

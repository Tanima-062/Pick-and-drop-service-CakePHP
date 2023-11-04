<table class="table table-bordered">
	<tr>
		<th></th>
		<?php
			$monthArray = array();
			for ($i = 0; $i < 12; $i++) {
				$key = date('Y',strtotime(date('Y-n-01')."+{$i}month"));
				if (empty($monthArray[$key])) {
					$monthArray[$key] = 0;
				}
				$monthArray[$key]++;
			}

			foreach ($monthArray as $key => $val) {
		?>
				<th colspan="<?php echo $val; ?>">
					<?php echo $key;?>
			</th>
		<?php
			}
		?>
	</tr>

	<tr>
		<td>予約月</td>

		<?php
		$date = date('n');
			for ($i = 0; $i < 12;$i++) {
		?>
				<td>
					<?php
						echo date('n',strtotime(date('Y-n-01') . "+{$i}month")) . '月';
					?>
				</td>
		<?php
			}
		?>
	</tr>
	<tr>
		<td>割合</td>

		<?php
			for ($i = 0; $i < 12; $i++) {
				$year = date('Y',strtotime(date('Y-n-01') ."+{$i}month"));
				$month = date('n',strtotime(date('Y-n-01') . "+{$i}month"));
		?>
				<td>
					<?php
						if (!empty($reserveArray['count'][$year][$month]['count'])) {
							echo round(($reserveArray['count'][$year][$month]['count'] / $reserveArray['sum']) * 100,1);
						} else {
							echo '0';
						}
					?>
					%
				</td>
		<?php
			}
		?>

	</tr>
</table>
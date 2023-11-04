<?php
$year = $this->request->data['Reservation']['date']['year'];
$month = $this->request->data['Reservation']['date']['month'];

//指定年月の最終日
$lastDay = date("t", mktime(0, 0, 0, $month, 1, $year));

$week = array("日", "月", "火", "水", "木", "金", "土");
?>

<div>
	<h3>
		<?php echo __('日別キャンセル数'); ?>
	</h3>


	<table class="table table-bordered table-condensed">
		<!-- 合計start -->
			<tr>
				<th rowspan="2">会社名&nbsp;</th>
				<th rowspan="2" style="min-width:60px;">項目&nbsp;</th>
				<th colspan="<?php echo ($lastDay+1); ?>"><?php echo $year; ?>年<?php echo $month; ?>月&nbsp;</th>
			</tr>
			<tr>
				<th class="">合計&nbsp;</th>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {
					$date = $year . "-" . $month . "-" . $day;
					$time = strtotime($date);
					$w = date("w", $time);

					$class = '';
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<th class="<?php echo $class; ?>"><?php echo $day; ?><br /><?php echo $week[$w]; ?>&nbsp;</th>
				<?php } ?>
			</tr>
			<tr>
				<td rowspan="6">全体&nbsp;</td>
				<td>成約数&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['agreement']['count'])) {
						echo number_format($data['0']['all']['agreement']['count']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data['0'][$day]['agreement']['count'])) {
							echo number_format($data['0'][$day]['agreement']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ数&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['count'])) {
						echo number_format($data['0']['all']['cancel']['count']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data['0'][$day]['cancel']['count'])) {
							echo number_format($data['0'][$day]['cancel']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ率&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['rate_cancel'])) {
						echo number_format($data['0']['all']['cancel']['rate_cancel'],3).'％';
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data['0'][$day]['cancel']['rate_cancel'])) {
							echo number_format($data['0'][$day]['cancel']['rate_cancel'],3).'％';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分見込売上&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['price'])) {
						echo number_format($data['0']['all']['cancel']['price']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data['0'][$day]['cancel']['price'])) {
							echo number_format($data['0'][$day]['cancel']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分予約単価&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['avg_price_count'])) {
						echo number_format($data['0']['all']['cancel']['avg_price_count']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data['0'][$day]['cancel']['avg_price_count'])) {
							echo number_format($data['0'][$day]['cancel']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分粗利&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['avg_price_count'])) {
						echo number_format($data['0']['all']['cancel']['avg_price_count']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data['0'][$day]['cancel']['commission'])) {
							echo number_format($data['0'][$day]['cancel']['commission']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
		<!-- 合計end -->
		<!-- 会社毎start -->
		<?php
		foreach ($clientList as $key => $clientData) {
			$clientId = $clientData['Client']['id'];
			$clientName = $clientData['Client']['name'];
			$commissionRate = $clientData['Client']['commission_rate'];

			$style = '';
			if ($clientId == 1) {
				continue;
				$clientName = '合計';
				$style = 'background-color:#F9F9F9;';
			}
			?>
			<tr>
				<th rowspan="2">会社名&nbsp;</th>
				<th rowspan="2" style="min-width:60px;">項目&nbsp;</th>
				<th colspan="<?php echo ($lastDay+1); ?>"><?php echo $year; ?>年<?php echo $month; ?>月&nbsp;</th>
			</tr>
			<tr>
				<th>合計&nbsp;</th>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {
					$date = $year . "-" . $month . "-" . $day;
					$time = strtotime($date);
					$w = date("w", $time);

					$class = '';
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<th class="<?php echo $class; ?>"><?php echo $day; ?><br /><?php echo $week[$w]; ?>&nbsp;</th>
				<?php } ?>
			</tr>

			<tr>
				<td rowspan="6" style="<?php echo $style; ?>"><?php echo $clientName; ?>&nbsp;</td>
				<td>成約数&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['agreement']['count'])) {
						echo number_format($data[$clientId]['all']['agreement']['count']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data[$clientId][$day]['agreement']['count'])) {
							echo number_format($data[$clientId][$day]['agreement']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ数&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['cancel']['count'])) {
						echo number_format($data[$clientId]['all']['cancel']['count']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data[$clientId][$day]['cancel']['count'])) {
							echo number_format($data[$clientId][$day]['cancel']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ率&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['cancel']['rate_cancel'])) {
						echo number_format($data[$clientId]['all']['cancel']['rate_cancel'],3).'％';
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data[$clientId][$day]['cancel']['rate_cancel'])) {
							echo number_format($data[$clientId][$day]['cancel']['rate_cancel'],3).'％';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分見込売上&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['cancel']['price'])) {
						echo number_format($data[$clientId]['all']['cancel']['price']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data[$clientId][$day]['cancel']['price'])) {
							echo number_format($data[$clientId][$day]['cancel']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分予約単価&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['cancel']['avg_price_count'])) {
						echo number_format($data[$clientId]['all']['cancel']['avg_price_count']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data[$clientId][$day]['cancel']['avg_price_count'])) {
							echo number_format($data[$clientId][$day]['cancel']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分粗利&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['cancel']['commission'])) {
						echo number_format($data[$clientId]['all']['cancel']['commission']);
					}
					?>
				</td>
				<?php
				for ($day = 1; $day <= $lastDay; $day++) {

					/** 土日を赤くする * */
					$class = '';
					$w = date("w", strtotime($year . "-" . $month . "-" . $day));
					if ($w == 0)
						$class = 'error';
					if ($w == 6)
						$class = 'info';
					?>
					<td class="<?php echo $class; ?>">
						<?php
						if (!empty($data[$clientId][$day]['cancel']['commission'])) {
							echo number_format($data[$clientId][$day]['cancel']['commission']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<!-- 会社毎end  -->

		<?php } ?>
	</table>
</div>

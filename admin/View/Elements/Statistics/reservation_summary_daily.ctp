<?php
$year = $this->request->data['Reservation']['date']['year'];
$month = $this->request->data['Reservation']['date']['month'];

//指定年月の最終日
$lastDay = date("t", mktime(0, 0, 0, $month, 1, $year));

$week = array("日", "月", "火", "水", "木", "金", "土");
?>

<div>
	<h3><?php echo __('日別予約獲得数'); ?></h3>


	<table class="table table-bordered table-condensed">
		<!-- 合計start -->
			<tr>
				<th rowspan="2">会社名&nbsp;</th>
				<th rowspan="2" style="min-width:70px;">項目&nbsp;</th>
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
				<td rowspan="6">
					全体&nbsp;
				</td>
				<td>予約獲得数&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['booking']['count'])) {
						echo number_format($data['0']['all']['booking']['count']);
					} else {
						echo "　";
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
						if (!empty($data['0'][$day]['booking']['count'])) {
							echo number_format($data['0'][$day]['booking']['count']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>件数シェア&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['booking']['rate_count'])) {
						echo number_format($data['0']['all']['booking']['rate_count'],3).'%';
					} else {
						echo "　";
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
						if (!empty($data['0'][$day]['booking']['rate_count'])) {
							echo number_format($data['0'][$day]['booking']['rate_count'],3).'%';
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込売上&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['booking']['price'])) {
						echo number_format($data['0']['all']['booking']['price']);
					} else {
						echo "　";
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
						if (!empty($data['0'][$day]['booking']['price'])) {
							echo number_format($data['0'][$day]['booking']['price']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>売上シェア&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['booking']['rate_price'])) {
						echo number_format($data['0']['all']['booking']['rate_price'],3).'%';
					} else {
						echo "　";
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
						if (!empty($data['0'][$day]['booking']['rate_price'])) {
							echo number_format($data['0'][$day]['booking']['rate_price'],3).'%';
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>予約単価&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['booking']['avg_price_count'])) {
						echo number_format($data['0']['all']['booking']['avg_price_count']);
					} else {
						echo "　";
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
						if (!empty($data['0'][$day]['booking']['avg_price_count'])) {
							echo number_format($data['0'][$day]['booking']['avg_price_count']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込粗利&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['booking']['commission'])) {
						echo number_format($data['0']['all']['booking']['commission']);
					} else {
						echo "　";
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
						if (!empty($data['0'][$day]['booking']['commission'])) {
							echo number_format($data['0'][$day]['booking']['commission']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
		<!-- 合計end -->	
		<?php
		foreach ($clientList as $key => $clientData) {
			$clientId = $clientData['Client']['id'];
			$clientName = $clientData['Client']['name'];
			$commissionRate = $clientData['Client']['commission_rate'];

			$style = "";
			if ($clientId == 1) {
				continue;
				$clientName = '合計';
				$style = 'background-color:#F9F9F9;';
			}
			?>
			<tr>
				<th rowspan="2">会社名&nbsp;</th>
				<th rowspan="2" style="min-width:70px;">項目&nbsp;</th>
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

			<!-- 会社毎start -->
			<tr>
				<td rowspan="6" style="<?php echo $style; ?>">
					<?php echo $clientName; ?>&nbsp;
				</td>
				<td>予約獲得数&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['booking']['count'])) {
						echo number_format($data[$clientId]['all']['booking']['count']);
					} else {
						echo "　";
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
						if (!empty($data[$clientId][$day]['booking']['count'])) {
							echo number_format($data[$clientId][$day]['booking']['count']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>件数シェア&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['booking']['rate_count'])) {
						echo number_format($data[$clientId]['all']['booking']['rate_count'],3).'%';
					} else {
						echo "　";
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
						if (!empty($data[$clientId][$day]['booking']['rate_count'])) {
							echo number_format($data[$clientId][$day]['booking']['rate_count'],3).'%';
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込売上&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['booking']['price'])) {
						echo number_format($data[$clientId]['all']['booking']['price']);
					} else {
						echo "　";
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
						if (!empty($data[$clientId][$day]['booking']['price'])) {
							echo number_format($data[$clientId][$day]['booking']['price']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>売上シェア&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['booking']['rate_price'])) {
						echo number_format($data[$clientId]['all']['booking']['rate_price'],3).'%';
					} else {
						echo "　";
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
						if (!empty($data[$clientId][$day]['booking']['rate_price'])) {
							echo number_format($data[$clientId][$day]['booking']['rate_price'],3).'%';
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>予約単価&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['booking']['avg_price_count'])) {
						echo number_format($data[$clientId]['all']['booking']['avg_price_count']);
					} else {
						echo "　";
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
						if (!empty($data[$clientId][$day]['booking']['avg_price_count'])) {
							echo number_format($data[$clientId][$day]['booking']['avg_price_count']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込粗利&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['booking']['commission'])) {
						echo number_format($data[$clientId]['all']['booking']['commission']);
					} else {
						echo "　";
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
						if (!empty($data[$clientId][$day]['booking']['commission'])) {
							echo number_format($data[$clientId][$day]['booking']['commission']);
						} else {
							echo "　";
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<!-- 会社毎end  -->

		<?php } ?>
	</table>
</div>
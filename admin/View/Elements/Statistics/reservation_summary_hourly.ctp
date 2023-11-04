<div>
	<h3>
		<?php echo __('時間別予約獲得数'); ?>
	</h3>


	<table class="table  table-bordered table-condensed ">
		<!-- 合計start -->	
			<tr>
				<th rowspan="2">会社名&nbsp;</th>
				<th rowspan="2" style="width:40px;">項目&nbsp;</th>
				<th colspan="25">
					<?php echo $this->request->data['Reservation']['date']['year']; ?>年&nbsp;
					<?php echo $this->request->data['Reservation']['date']['month']; ?>月&nbsp;
					<?php echo $this->request->data['Reservation']['date']['day']; ?>日&nbsp;
				</th>
			</tr>
			<tr>
				<th>合計&nbsp;</th>
				<th>0時&nbsp;</th>
				<th>1時&nbsp;</th>
				<th>2時&nbsp;</th>
				<th>3時&nbsp;</th>
				<th>4時&nbsp;</th>
				<th>5時&nbsp;</th>
				<th>6時&nbsp;</th>
				<th>7時&nbsp;</th>
				<th>8時&nbsp;</th>
				<th>9時&nbsp;</th>
				<th>10時&nbsp;</th>
				<th>11時&nbsp;</th>
				<th>12時&nbsp;</th>
				<th>13時&nbsp;</th>
				<th>14時&nbsp;</th>
				<th>15時&nbsp;</th>
				<th>16時&nbsp;</th>
				<th>17時&nbsp;</th>
				<th>18時&nbsp;</th>
				<th>19時&nbsp;</th>
				<th>20時&nbsp;</th>
				<th>21時&nbsp;</th>
				<th>22時&nbsp;</th>
				<th>23時&nbsp;</th>
			</tr>
			<tr>
				<td rowspan="6">
					全体&nbsp;
				</td>
				<td>予約獲得数&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['booking']['count'])) {
							echo number_format($data['0']['all']['booking']['count']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data['0'][$hour]['booking']['count'])) {
							echo number_format($data['0'][$hour]['booking']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>件数シェア&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['booking']['rate_count'])) {
							echo number_format($data['0']['all']['booking']['rate_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data['0'][$hour]['booking']['rate_count'])) {
							echo number_format($data['0'][$hour]['booking']['rate_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込売上&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['booking']['price'])) {
							echo number_format($data['0']['all']['booking']['price']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data['0'][$hour]['booking']['price'])) {
							echo number_format($data['0'][$hour]['booking']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>売上シェア&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['booking']['rate_price'])) {
							echo number_format($data['0']['all']['booking']['rate_price'],3).'%';
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data['0'][$hour]['booking']['rate_price'])) {
							echo number_format($data['0'][$hour]['booking']['rate_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>予約単価&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['booking']['avg_price_count'])) {
							echo number_format($data['0']['all']['booking']['avg_price_count']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data['0'][$hour]['booking']['avg_price_count'])) {
							echo number_format($data['0'][$hour]['booking']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込粗利&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['booking']['commission'])) {
							echo number_format($data['0']['all']['booking']['commission']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data['0'][$hour]['booking']['commission'])) {
							echo number_format($data['0'][$hour]['booking']['commission']);
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
				<th rowspan="2" style="width:40px;">項目&nbsp;</th>
				<th colspan="25">
					<?php echo $this->request->data['Reservation']['date']['year']; ?>年&nbsp;
					<?php echo $this->request->data['Reservation']['date']['month']; ?>月&nbsp;
					<?php echo $this->request->data['Reservation']['date']['day']; ?>日&nbsp;

				</th>
			</tr>
			<tr>
				<th>合計&nbsp;</th>
				<th>0時&nbsp;</th>
				<th>1時&nbsp;</th>
				<th>2時&nbsp;</th>
				<th>3時&nbsp;</th>
				<th>4時&nbsp;</th>
				<th>5時&nbsp;</th>
				<th>6時&nbsp;</th>
				<th>7時&nbsp;</th>
				<th>8時&nbsp;</th>
				<th>9時&nbsp;</th>
				<th>10時&nbsp;</th>
				<th>11時&nbsp;</th>
				<th>12時&nbsp;</th>
				<th>13時&nbsp;</th>
				<th>14時&nbsp;</th>
				<th>15時&nbsp;</th>
				<th>16時&nbsp;</th>
				<th>17時&nbsp;</th>
				<th>18時&nbsp;</th>
				<th>19時&nbsp;</th>
				<th>20時&nbsp;</th>
				<th>21時&nbsp;</th>
				<th>22時&nbsp;</th>
				<th>23時&nbsp;</th>
			</tr>

			<tr>
				<td rowspan="6" <?php echo $style; ?>>
					<?php echo $clientName; ?>&nbsp;
				</td>
				<td>予約獲得数&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['count'])) {
							echo number_format($data[$clientId]['all']['booking']['count']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$hour]['booking']['count'])) {
							echo number_format($data[$clientId][$hour]['booking']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>件数シェア&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['rate_count'])) {
							echo number_format($data[$clientId]['all']['booking']['rate_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$hour]['booking']['rate_count'])) {
							echo number_format($data[$clientId][$hour]['booking']['rate_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込売上&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['price'])) {
							echo number_format($data[$clientId]['all']['booking']['price']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$hour]['booking']['price'])) {
							echo number_format($data[$clientId][$hour]['booking']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>売上シェア&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['rate_price'])) {
							echo number_format($data[$clientId]['all']['booking']['rate_price'],3).'%';
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$hour]['booking']['rate_price'])) {
							echo number_format($data[$clientId][$hour]['booking']['rate_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>予約単価&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['avg_price_count'])) {
							echo number_format($data[$clientId]['all']['booking']['avg_price_count']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$hour]['booking']['avg_price_count'])) {
							echo number_format($data[$clientId][$hour]['booking']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>見込粗利&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['commission'])) {
							echo number_format($data[$clientId]['all']['booking']['commission']);
						}
						?>
				</td>
				<?php
				for ($hour = 0; $hour <= 23; $hour++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$hour]['booking']['commission'])) {
							echo number_format($data[$clientId][$hour]['booking']['commission']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<!-- 会社毎end  -->

		<?php } ?>
	</table>
</div>

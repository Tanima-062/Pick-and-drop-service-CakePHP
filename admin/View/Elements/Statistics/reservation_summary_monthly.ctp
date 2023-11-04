<div>
	<h3>
		<?php echo __('月別予約獲得数'); ?>
	</h3>


	<table class="table  table-bordered table-condensed">
		<!-- 合計start -->
			<tr>
				<th rowspan="2">会社名&nbsp;</th>
				<th rowspan="2">項目&nbsp;</th>
				<th colspan="13"><?php echo $this->request->data['Reservation']['date']['year']; ?>年&nbsp;</th>
			</tr>
			<tr>
				<th>合計&nbsp;</th>
				<th>1月&nbsp;</th>
				<th>2月&nbsp;</th>
				<th>3月&nbsp;</th>
				<th>4月&nbsp;</th>
				<th>5月&nbsp;</th>
				<th>6月&nbsp;</th>
				<th>7月&nbsp;</th>
				<th>8月&nbsp;</th>
				<th>9月&nbsp;</th>
				<th>10月&nbsp;</th>
				<th>11月&nbsp;</th>
				<th>12月&nbsp;</th>
			</tr>
			<tr>
				<td rowspan="14" style="">
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['count'])) {
							echo number_format($data['0'][$month]['booking']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['year_count'])) {
						echo number_format($data['0']['all']['booking']['year_count'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['year_count'])) {
							echo number_format($data['0'][$month]['booking']['year_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['month_count'])) {
						echo number_format($data['0']['all']['booking']['month_count'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['month_count'])) {
							echo number_format($data['0'][$month]['booking']['month_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['rate_count'])) {
							echo number_format($data['0'][$month]['booking']['rate_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['price'])) {
							echo number_format($data['0'][$month]['booking']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['year_price'])) {
						echo number_format($data['0']['all']['booking']['year_price'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['year_price'])) {
							echo number_format($data['0'][$month]['booking']['year_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['month_price'])) {
						echo number_format($data['0']['all']['booking']['month_price'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['month_price'])) {
							echo number_format($data['0'][$month]['booking']['month_price'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['rate_price'])) {
							echo number_format($data['0'][$month]['booking']['rate_price'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['avg_price_count'])) {
							echo number_format($data['0'][$month]['booking']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['year_avg_price_count'])) {
						echo number_format($data['0']['all']['booking']['year_avg_price_count'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['year_avg_price_count'])) {
							echo number_format($data['0'][$month]['booking']['year_avg_price_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['month_avg_price_count'])) {
						echo number_format($data['0']['all']['booking']['month_avg_price_count'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['booking']['month_avg_price_count'])) {
							echo number_format($data['0'][$month]['booking']['month_avg_price_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['booking']['commission'])) {
							echo number_format($data['0'][$month]['booking']['commission']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['year_commission'])) {
						echo number_format($data['0']['all']['booking']['year_commission'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['booking']['year_commission'])) {
							echo number_format($data['0'][$month]['booking']['year_commission'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
					if (!empty($data['0']['all']['booking']['month_commission'])) {
						echo number_format($data['0']['all']['booking']['month_commission'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['booking']['month_commission'])) {
							echo number_format($data['0'][$month]['booking']['month_commission'],3).'%';
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
				<th rowspan="2">項目&nbsp;</th>
				<th colspan="13"><?php echo $this->request->data['Reservation']['date']['year']; ?>年&nbsp;</th>
			</tr>
			<tr>
				<th>合計&nbsp;</th>
				<th>1月&nbsp;</th>
				<th>2月&nbsp;</th>
				<th>3月&nbsp;</th>
				<th>4月&nbsp;</th>
				<th>5月&nbsp;</th>
				<th>6月&nbsp;</th>
				<th>7月&nbsp;</th>
				<th>8月&nbsp;</th>
				<th>9月&nbsp;</th>
				<th>10月&nbsp;</th>
				<th>11月&nbsp;</th>
				<th>12月&nbsp;</th>
			</tr>

			<tr>
				<td rowspan="18" style="<?php echo $style; ?>">
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['count'])) {
							echo number_format($data[$clientId][$month]['booking']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['year_count'])) {
							echo number_format($data[$clientId]['all']['booking']['year_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['year_count'])) {
							echo number_format($data[$clientId][$month]['booking']['year_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['month_count'])) {
							echo number_format($data[$clientId]['all']['booking']['month_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['month_count'])) {
							echo number_format($data[$clientId][$month]['booking']['month_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['rate_count'])) {
							echo number_format($data[$clientId][$month]['booking']['rate_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(pt)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['year_rate_count'])) {
							echo number_format($data[$clientId]['all']['booking']['year_rate_count'],3).'pt';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['year_rate_count'])) {
							echo number_format($data[$clientId][$month]['booking']['year_rate_count'],3).'pt';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(pt)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['month_rate_count'])) {
							echo number_format($data[$clientId]['all']['booking']['month_rate_count'],3).'pt';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['month_rate_count'])) {
							echo number_format($data[$clientId][$month]['booking']['month_rate_count'],3).'pt';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['price'])) {
							echo number_format($data[$clientId][$month]['booking']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['year_price'])) {
							echo number_format($data[$clientId]['all']['booking']['year_price'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['year_price'])) {
							echo number_format($data[$clientId][$month]['booking']['year_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['month_price'])) {
							echo number_format($data[$clientId]['all']['booking']['month_price'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['month_price'])) {
							echo number_format($data[$clientId][$month]['booking']['month_price'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['rate_price'])) {
							echo number_format($data[$clientId][$month]['booking']['rate_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(pt)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['year_rate_price'])) {
							echo number_format($data[$clientId]['all']['booking']['year_rate_price'],3).'pt';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['year_rate_price'])) {
							echo number_format($data[$clientId][$month]['booking']['year_rate_price'],3).'pt';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(pt)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['month_rate_price'])) {
							echo number_format($data[$clientId]['all']['booking']['month_rate_price'],3).'pt';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['month_rate_price'])) {
							echo number_format($data[$clientId][$month]['booking']['month_rate_price'],3).'pt';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['avg_price_count'])) {
							echo number_format($data[$clientId][$month]['booking']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['year_avg_price_count'])) {
							echo number_format($data[$clientId]['all']['booking']['year_avg_price_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['year_avg_price_count'])) {
							echo number_format($data[$clientId][$month]['booking']['year_avg_price_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['month_avg_price_count'])) {
							echo number_format($data[$clientId]['all']['booking']['month_avg_price_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['booking']['month_avg_price_count'])) {
							echo number_format($data[$clientId][$month]['booking']['month_avg_price_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['booking']['commission'])) {
							echo number_format($data[$clientId][$month]['booking']['commission']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['year_commission'])) {
							echo number_format($data[$clientId]['all']['booking']['year_commission'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['booking']['year_commission'])) {
							echo number_format($data[$clientId][$month]['booking']['year_commission'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['booking']['month_commission'])) {
							echo number_format($data[$clientId]['all']['booking']['month_commission'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['booking']['month_commission'])) {
							echo number_format($data[$clientId][$month]['booking']['month_commission'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<!-- 会社毎end  -->

		<?php } ?>
	</table>
</div>

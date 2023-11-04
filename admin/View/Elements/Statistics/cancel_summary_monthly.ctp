<div>
	<h3><?php echo __('月別キャンセル数'); ?></h3>


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
				<td rowspan="16">全体&nbsp;</td>
				<td>成約数&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['agreement']['count'])) {
							echo number_format($data['0']['all']['agreement']['count']);
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['agreement']['count'])) {
							echo number_format($data['0'][$month]['agreement']['count']);
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['count'])) {
							echo number_format($data['0'][$month]['cancel']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['year_count'])) {
						echo number_format($data['0']['all']['cancel']['year_count'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['year_count'])) {
							echo number_format($data['0'][$month]['cancel']['year_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['month_count'])) {
						echo number_format($data['0']['all']['cancel']['month_count'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['month_count'])) {
							echo number_format($data['0'][$month]['cancel']['month_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['rate_cancel'])) {
							echo number_format($data['0'][$month]['cancel']['rate_cancel'],3).'％';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(pt)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['year_rate_cancel'])) {
						echo number_format($data['0']['all']['cancel']['year_rate_cancel'],3).'pt';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['year_rate_cancel'])) {
							echo number_format($data['0'][$month]['cancel']['year_rate_cancel'],3).'pt';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(pt)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['month_rate_cancel'])) {
						echo number_format($data['0']['all']['cancel']['month_rate_cancel'],3).'pt';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['month_rate_cancel'])) {
							echo number_format($data['0'][$month]['cancel']['month_rate_cancel'],3).'pt';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['price'])) {
							echo number_format($data['0'][$month]['cancel']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['year_price'])) {
						echo number_format($data['0']['all']['cancel']['year_price'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['year_price'])) {
							echo number_format($data['0'][$month]['cancel']['year_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['month_price'])) {
						echo number_format($data['0']['all']['cancel']['month_price'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['month_price'])) {
							echo number_format($data['0'][$month]['cancel']['month_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分予約単価&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['cancel']['avg_price_count'])) {
							echo number_format($data['0']['all']['cancel']['avg_price_count']);
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['cancel']['avg_price_count'])) {
							echo number_format($data['0'][$month]['cancel']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['cancel']['year_avg_price_count'])) {
							echo number_format($data['0']['all']['cancel']['year_avg_price_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['cancel']['year_avg_price_count'])) {
							echo number_format($data['0'][$month]['cancel']['year_avg_price_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td><?php
						if (!empty($data['0']['all']['cancel']['month_avg_price_count'])) {
							echo number_format($data['0']['all']['cancel']['month_avg_price_count'],3).'%';
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data['0'][$month]['cancel']['month_avg_price_count'])) {
							echo number_format($data['0'][$month]['cancel']['month_avg_price_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>ｷｬﾝｾﾙ分粗利&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['commission'])) {
						echo number_format($data['0']['all']['cancel']['commission']);
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['commission'])) {
							echo number_format($data['0'][$month]['cancel']['commission']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['year_commission'])) {
						echo number_format($data['0']['all']['cancel']['year_commission'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['year_commission'])) {
							echo number_format($data['0'][$month]['cancel']['year_commission'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td>
					<?php
					if (!empty($data['0']['all']['cancel']['month_commission'])) {
						echo number_format($data['0']['all']['cancel']['month_commission'],3).'%';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data['0'][$month]['cancel']['month_commission'])) {
							echo number_format($data['0'][$month]['cancel']['month_commission'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
		<!-- 合計start -->
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
				$style = 'background-color:#F9F9F9';
			}
			?>
			<tr>
				<th rowspan="2">会社名&nbsp;</th>
				<th rowspan="2">項目&nbsp;</th>
				<th colspan="13"><?php echo $this->request->data['Reservation']['date']['year']; ?>年&nbsp;</th>
			</tr>
			<tr>
				<th>合計</th>
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
				<td rowspan="16" style="<?php echo $style; ?>"><?php echo $clientName; ?>&nbsp;</td>
				<td>成約数&nbsp;</td>
				<td><?php
						if (!empty($data[$clientId]['all']['agreement']['count'])) {
							echo number_format($data[$clientId]['all']['agreement']['count']);
						}
						?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['agreement']['count'])) {
							echo number_format($data[$clientId][$month]['agreement']['count']);
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['count'])) {
							echo number_format($data[$clientId][$month]['cancel']['count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td>
					<?php
						if (!empty($data[$clientId]['all']['cancel']['year_count'])) {
							echo number_format($data[$clientId]['all']['cancel']['year_count'],3).'%';
						}
					?>
				</td>	
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['year_count'])) {
							echo number_format($data[$clientId][$month]['cancel']['year_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td>
					<?php
						if (!empty($data[$clientId]['all']['cancel']['month_count'])) {
							echo number_format($data[$clientId]['all']['cancel']['month_count'],3).'%';
						}
					?>
				</td>	
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['month_count'])) {
							echo number_format($data[$clientId][$month]['cancel']['month_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['rate_cancel'])) {
							echo number_format($data[$clientId][$month]['cancel']['rate_cancel'],3).'％';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(pt)&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['cancel']['year_rate_cancel'])) {
						echo number_format($data[$clientId]['all']['cancel']['year_rate_cancel'],3).'pt';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['year_rate_cancel'])) {
							echo number_format($data[$clientId][$month]['cancel']['year_rate_cancel'],3).'pt';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(pt)&nbsp;</td>
				<td>
					<?php
					if (!empty($data[$clientId]['all']['cancel']['month_rate_cancel'])) {
						echo number_format($data[$clientId]['all']['cancel']['month_rate_cancel'],3).'pt';
					}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['month_rate_cancel'])) {
							echo number_format($data[$clientId][$month]['cancel']['month_rate_cancel'],3).'pt';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['price'])) {
							echo number_format($data[$clientId][$month]['cancel']['price']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td>
					<?php
						if (!empty($data[$clientId]['all']['cancel']['year_price'])) {
							echo number_format($data[$clientId]['all']['cancel']['year_price'],3).'%';
						}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['year_price'])) {
							echo number_format($data[$clientId][$month]['cancel']['year_price'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td>
					<?php
						if (!empty($data[$clientId]['all']['cancel']['month_price'])) {
							echo number_format($data[$clientId]['all']['cancel']['month_price'],3).'%';
						}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['month_price'])) {
							echo number_format($data[$clientId][$month]['cancel']['month_price'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['cancel']['avg_price_count'])) {
							echo number_format($data[$clientId][$month]['cancel']['avg_price_count']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td>
					<?php
						if (!empty($data[$clientId]['all']['cancel']['year_avg_price_count'])) {
							echo number_format($data[$clientId]['all']['cancel']['year_avg_price_count'],3).'%';
						}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['cancel']['year_avg_price_count'])) {
							echo number_format($data[$clientId][$month]['cancel']['year_avg_price_count'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td>
					<?php
						if (!empty($data[$clientId]['all']['cancel']['month_avg_price_count'])) {
							echo number_format($data[$clientId]['all']['cancel']['month_avg_price_count'],3).'%';
						}
					?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td><?php
						if (!empty($data[$clientId][$month]['cancel']['month_avg_price_count'])) {
							echo number_format($data[$clientId][$month]['cancel']['month_avg_price_count'],3).'%';
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
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['commission'])) {
							echo number_format($data[$clientId][$month]['cancel']['commission']);
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前年比(%)&nbsp;</td>
				<td>
				<?php
				if (!empty($data[$clientId]['all']['cancel']['year_commission'])) {
					echo number_format($data[$clientId]['all']['cancel']['year_commission'],3).'%';
				}
				?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['year_commission'])) {
							echo number_format($data[$clientId][$month]['cancel']['year_commission'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td>-前月比(%)&nbsp;</td>
				<td>
				<?php
				if (!empty($data[$clientId]['all']['cancel']['month_commission'])) {
					echo number_format($data[$clientId]['all']['cancel']['month_commission'],3).'%';
				}
				?>
				</td>
				<?php
				for ($month = 1; $month <= 12; $month++) {
					?>
					<td>
						<?php
						if (!empty($data[$clientId][$month]['cancel']['month_commission'])) {
							echo number_format($data[$clientId][$month]['cancel']['month_commission'],3).'%';
						}
						?>
					</td>
				<?php } ?>
			</tr>
			<!-- 会社毎end  -->
		<?php } ?>
	</table>
</div>

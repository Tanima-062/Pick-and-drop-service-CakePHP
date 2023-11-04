<script>
// リセット追加処理
$(document).on('click', '.btn-reset', function() {
	let $yearSelect = $('#ReservationYearYear');
	// 後処理
	setTimeout(function() {
		$yearSelect.prop("selectedIndex", 1);
	}, 1);
});
</script>
<div>
	<h3>
		月別売上（<?php echo ($salesType == Constant::SALES_TYPE_ARRANGED) ? '手配' : '募集';?>）
	</h3>

<?php echo $this->Form->create('Reservation', array('type' => 'get'));?>
<table class="table table-bordered">
	<tr>
		<th class="alert-info">年</th>
		<td colspan="3"><?php echo @$this->Form->year('Reservation.year' ,2013, date('Y', strtotime("1 years")), array('empty' => false)); ?>年</td>
	</tr>
	<tr>
		<th class="alert-info">エリア別</th>
		<td><?php echo $this->Form->select('region_link_cd', $regionList); ?></td>
		<th class="alert-info">営業所別</th>
		<td><?php echo $this->Form->select('office_id', $officeLists); ?></td>
	</tr>
	<tr>
		<th class="alert-info">車両タイプ</th>
		<td><?php echo $this->Form->select('car_type_id', $carTypeLists); ?></td>
		<th class="alert-info">車両クラス</th>
		<td><?php echo $this->Form->select('car_class_id', $carClassLists); ?></td>
	</tr>
</table>

<div style="float:right;">
<?php
	echo $this->Form->button('検索', array('class' => 'btn btn-info'));
	echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
?>
</div>
<?php
	echo $this->Form->end();
?>

<br />
<br />


	<table class="table table-bordered table-condensed">

		<!-- 会社毎start -->
		<tr class="info">
			<th colspan="2" rowspan="2">項目&nbsp;</th>
			<th colspan="13">
				<?php echo $this->request->data['Reservation']['year']['year'];?>
				年&nbsp;
			</th>
		</tr>
		<tr class="info">
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
			<td rowspan="4">売上&nbsp;</td>
			<td>予約数&nbsp;</td>

			<td>
			<?php
			if(!empty($data['sum']['booking']['count'])) {
				echo $data['sum']['booking']['count'];
			} else {
				echo 0;
			}
			?>
			</td>
			<?php
			for($month = 1;$month <= 12;$month++) {
				$monthVal = str_pad($month, 2, "0", STR_PAD_LEFT);
				$tmpDate = $this->request->data['Reservation']['year'].'-'.$monthVal.'-01';
				$lastDayVal = date('t',strtotime($tmpDate));
				?>
			<td>
				<?php
				if(!empty($data[$month]['booking']['count'])) {

					if($dayStandardClientFlg) {

						echo $this->Html->link(
								number_format($data[$month]['booking']['count']),
								'/reservations/?ReservationStatus=1
								&ReservationReturnDate[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationReturnDate[month]=' . $monthVal .
								'&ReservationReturnDate[day]=01'.
								'&ReservationReturnDate2[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationReturnDate2[month]=' . $monthVal .
								'&ReservationReturnDate2[day]='. $lastDayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);

					} else {

						echo $this->Html->link(
								number_format($data[$month]['booking']['count']),
								'/reservations/?ReservationStatus=1
								&ReservationRentDate[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationRentDate[month]=' . $monthVal .
								'&ReservationRentDate[day]=01'. 
								'&ReservationRentDate2[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationRentDate2[month]=' . $monthVal .
								'&ReservationRentDate2[day]='. $lastDayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);
					}
				} else {
					echo '0';
				}
				?>
			</td>
			<?php }?>
		</tr>
		<tr>
			<td>見込売上&nbsp;</td>

			<td>
			<?php
			if(!empty($data['sum']['expected_revenue']['price'])) {
				echo number_format($data['sum']['expected_revenue']['price']);
			} else {
				echo 0;
			}
			?>
			</td>

			<?php
			for($month = 1;$month <= 12;$month++) {
			?>
			<td>
				<?php
				if(!empty($data[$month]['expected_revenue']['price'])) {
					echo number_format($data[$month]['expected_revenue']['price']);
				} else {
					echo '0';
				}
				?>
			</td>
			<?php }?>
		</tr>

		<tr>
			<td>成約数&nbsp;</td>
			<td>
			<?php
			if(!empty($data['sum']['agreement']['count'])) {
				echo $data['sum']['agreement']['count'];
			} else {
				echo 0;
			}
			?>

			</td>
			<?php
			for($month = 1;$month <= 12;$month++) {
				$monthVal = str_pad($month, 2, "0", STR_PAD_LEFT);
				$tmpDate = $this->request->data['Reservation']['year'].'-'.$monthVal.'-01';
				$lastDayVal = date('t',strtotime($tmpDate));
				?>
			<td>
				<?php

				if(!empty($data[$month]['agreement']['count'])) {

					if($dayStandardClientFlg) {

						echo $this->Html->link(
								number_format($data[$month]['agreement']['count']),
								'/reservations/?ReservationStatus=2
								&ReservationReturnDate[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationReturnDate[month]=' . $monthVal .
								'&ReservationReturnDate[day]=01'.
								'&ReservationReturnDate2[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationReturnDate2[month]=' . $monthVal .
								'&ReservationReturnDate2[day]='. $lastDayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);

					} else {

						echo $this->Html->link(
								number_format($data[$month]['agreement']['count']),
								'/reservations/?ReservationStatus=2
								&ReservationRentDate[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationRentDate[month]=' . $monthVal .
								'&ReservationRentDate[day]=01'.
								'&ReservationRentDate2[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationRentDate2[month]=' . $monthVal .
								'&ReservationRentDate2[day]='. $lastDayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);
					}

				} else {
					echo 0;
				}
				?>
			</td>
			<?php }?>
		</tr>
		<tr>
			<td>確定売上&nbsp;</td>
			<td>
				<?php
				if(!empty($data['sum']['agreement']['price'])) {
					echo number_format($data['sum']['agreement']['price']);
				} else {
					echo '0';
				}
				?>
			</td>
			<?php
			for($month = 1;$month <= 12;$month++) {
				?>
			<td>
				<?php
				if(!empty($data[$month]['agreement']['price'])) {
					echo number_format($data[$month]['agreement']['price']);
				} else {
					echo '0';
				}
				?>
			</td>
			<?php }?>
		</tr>


		<tr>
			<td rowspan="2">キャンセル&nbsp;</td>
			<td>キャンセル数&nbsp;</td>
			<td>

			<?php
				if(!empty($data['sum']['cancel']['count'])) {
					echo $data['sum']['cancel']['count'];
				} else {
					echo 0;
				}
			?>

			</td>
			<?php
			for($month = 1;$month <= 12;$month++) {
				$monthVal = str_pad($month, 2, "0", STR_PAD_LEFT);
				$tmpDate = $this->request->data['Reservation']['year'].'-'.$monthVal.'-01';
				$lastDayVal = date('t',strtotime($tmpDate));
				?>
			<td>
				<?php
				if(!empty($data[$month]['cancel']['count'])) {
					//echo number_format($data[$month]['cancel']['count']);
					if($dayStandardClientFlg) {

						echo $this->Html->link(
								number_format($data[$month]['cancel']['count']),
								'/reservations/?ReservationStatus=3
								&ReservationReturnDate[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationReturnDate[month]=' . $monthVal .
								'&ReservationReturnDate[day]=01' .
								'&ReservationReturnDate2[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationReturnDate2[month]=' . $monthVal .
								'&ReservationReturnDate2[day]=' . $lastDayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);

					} else {
						
						echo $this->Html->link(
								number_format($data[$month]['cancel']['count']),
								'/reservations/?ReservationStatus=3
								&ReservationRentDate[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationRentDate[month]=' . $monthVal .
								'&ReservationRentDate[day]=01' .
								'&ReservationRentDate2[year]=' . $this->request->data['Reservation']['year'] .
								'&ReservationRentDate2[month]=' . $monthVal .
								'&ReservationRentDate2[day]=' . $lastDayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);
					}

				} else {
					echo "0";
				}
				?>
			</td>
			<?php }?>
		</tr>
		<tr>
			<td>キャンセル料金&nbsp;</td>

			<td>
				<?php
				if(!empty($data['sum']['cancel']['price'])) {
					echo number_format($data['sum']['cancel']['price']);
				} else {
					echo "0";
				}
				?>
			</td>
			<?php
			for($month = 1;$month <= 12;$month++) {
				?>
			<td>
				<?php
				if(!empty($data[$month]['cancel']['price'])) {
					echo number_format($data[$month]['cancel']['price']);
				} else {
					echo "0";
				}
				?>
			</td>
			<?php }?>
		</tr>





		<tr>
			<td rowspan="4">予約獲得&nbsp;</td>
			<td>予約獲得数&nbsp;</td>
			<td>
			<?php
			if(!empty($data2['sum']['booking']['count'])) {
				echo $data2['sum']['booking']['count'];
			} else {
				echo 0;
			}
			?>
			</td>
			<?php
			for($month = 1;$month <= 12;$month++) {
				$monthVal = str_pad($month, 2, "0", STR_PAD_LEFT);
				$tmpDate = $this->request->data['Reservation']['year'].'-'.$monthVal.'-01';
				$lastDayVal = date('t',strtotime($tmpDate));
				?>
			<td>
				<?php
				if(!empty($data2[$month]['booking']['count'])) {
					//echo number_format($data2[$month]['booking']['count']);
					echo $this->Html->link(
							number_format($data2[$month]['booking']['count']),
							'/reservations/?ReservationStatus=
							&ReservationCreatedDate[year]=' . $this->request->data['Reservation']['year'] .
							'&ReservationCreatedDate[month]=' . $monthVal .
							'&ReservationCreatedDate[day]=01'.
							'&ReservationCreatedDate2[year]=' . $this->request->data['Reservation']['year'] .
							'&ReservationCreatedDate2[month]=' . $monthVal .
							'&ReservationCreatedDate2[day]='. $lastDayVal .
							'&ReservationOfficeName=' . $searchData['office_id'] .
							'&ReservationCarClassName='.  $searchData['car_class_id']
							,
							array('target'=>'_blank')
					);

				} else {
					echo '0';
				}
				?>
			</td>
			<?php }?>
		</tr>
		<tr>
			<td>予約獲得分の見込売上&nbsp;</td>

			<td>
			<?php
				if(!empty($data2['sum']['expected_revenue']['price'])) {
					echo number_format($data2['sum']['expected_revenue']['price']);
				} else {
					echo '0';
				}
			?>

			</td>
			<?php
			for($month = 1;$month <= 12;$month++) {
				?>
			<td>
				<?php
				if(!empty($data2[$month]['expected_revenue']['price'])) {
					echo number_format($data2[$month]['expected_revenue']['price']);
				} else {
					echo '0';
			}
			?>
			</td>
			<?php }?>
		</tr>

		<!-- 会社毎end  -->

	</table>
</div>

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
<h3>日別売上（<?php echo ($salesType == Constant::SALES_TYPE_ARRANGED) ? '手配' : '募集';?>）</h3>

<?php echo $this->Form->create('Reservation', array('type' => 'get')); ?>

<table class="table table-bordered">
	<tr>
		<th class="alert-info">年月</th>
		<td colspan="3">
		<?php echo @$this->Form->year('Reservation.year', 2013, date('Y') + 1, array('empty' => false)) . "年"; ?>
		<?php echo $this->Form->month('Reservation.month', array('empty' => false, 'monthNames' => false)) . "月"; ?>
		</td>
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

<?php
//指定年月の最終日
$lastDay = date("t", mktime(0, 0, 0, $month, 1, $year));

$week = array("日", "月", "火", "水", "木", "金", "土");

?>


<br />
<div>

	<div style="float:right;">
		<h4 style="color:#000;">
			見込売上合計: <span style="color:blue;"><?php echo @number_format($data['sum']['booking']['price']);?></span>円　
			確定売上合計: <span style="color:blue;"><?php  echo @number_format($data['sum']['agreement']['price']);?></span>円　
			予約獲得分の見込売上合計:<span style="color:blue"><?php echo @number_format($data2['sum']['booking']['price']);?></span>円
		</h4>
	</div>
	<br />

		<table class="table table-bordered table-condensed">

			<tr class="info">
				<th  style="min-width:150px" colspan="2" rowspan="2">項目&nbsp;</th>
				<th colspan="<?php echo $lastDay + 1;?>"><?php echo $year;?>年<?php echo $month;?>月&nbsp;</th>
			</tr>
			<tr>
				<th>
					合
					<br />
					計
				</th>
				<?php
				for($day = 1; $day <= $lastDay; $day++) {
					$date = $year ."-". $month ."-".$day;
					$time = strtotime($date);
					$w = date("w", $time);

					$class = '';
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<th class="<?php echo $class;?>">
					<?php echo $day;?><br /><?php echo $week[$w];?>&nbsp;
				</th>
				<?php }?>
			</tr>

		<!-- 会社毎start -->
			<tr>
				<td rowspan="4" >
					売上&nbsp;
				</td>
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
				for($day = 1;$day <= $lastDay;$day++) {

					$dayVal = str_pad($day, 2, "0", STR_PAD_LEFT);

					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
				if(!empty($data[$day]['booking']['count'])) {
					if($dayStandardClientFlg) {

						echo $this->Html->link(
								number_format($data[$day]['booking']['count']),
								'/reservations/?ReservationStatus=1
								&ReservationReturnDate[year]=' . $year.
								'&ReservationReturnDate[month]=' . $month.
								'&ReservationReturnDate[day]=' . $dayVal .
								'&ReservationReturnDate2[year]=' . $year.
								'&ReservationReturnDate2[month]=' . $month.
								'&ReservationReturnDate2[day]=' . $dayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);

					} else {

						echo $this->Html->link(
								number_format($data[$day]['booking']['count']),
								'/reservations/?ReservationStatus=1
								&ReservationRentDate[year]=' .$year.
								'&ReservationRentDate[month]=' . $month .
								'&ReservationRentDate[day]=' . $dayVal .
								'&ReservationRentDate2[year]=' .$year.
								'&ReservationRentDate2[month]=' . $month .
								'&ReservationRentDate2[day]=' . $dayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id'],
								array('target'=>'_blank')
						);
					}
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
				for($day = 1;$day <= $lastDay;$day++) {

					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
					if(!empty($data[$day]['expected_revenue']['price'])) {
						echo number_format($data[$day]['expected_revenue']['price']);
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
				for($day = 1;$day <= $lastDay;$day++) {

					$dayVal = str_pad($day, 2, "0", STR_PAD_LEFT);

					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
					if(!empty($data[$day]['agreement']['count'])) {
						//echo number_format($data[$day]['agreement']['count']);

						if($dayStandardClientFlg) {

							echo $this->Html->link(
									number_format($data[$day]['agreement']['count']),
									'/reservations/?ReservationStatus=2
									&ReservationReturnDate[year]=' . $year .
									'&ReservationReturnDate[month]=' . $month.
									'&ReservationReturnDate[day]=' . $dayVal .
									'&ReservationReturnDate2[year]=' . $year .
									'&ReservationReturnDate2[month]=' . $month.
									'&ReservationReturnDate2[day]=' . $dayVal .
									'&ReservationOfficeName=' . $searchData['office_id'] .
									'&ReservationCarClassName='.  $searchData['car_class_id']
									,
									array('target'=>'_blank')
							);

						} else {

							echo $this->Html->link(
									number_format($data[$day]['agreement']['count']),
									'/reservations/?ReservationStatus=2
									&ReservationRentDate[year]=' . $year .
									'&ReservationRentDate[month]=' . $month .
									'&ReservationRentDate[day]=' . $dayVal .
									'&ReservationRentDate2[year]=' . $year .
									'&ReservationRentDate2[month]=' . $month .
									'&ReservationRentDate2[day]=' . $dayVal .
									'&ReservationOfficeName=' . $searchData['office_id'] .
									'&ReservationCarClassName='.  $searchData['car_class_id']
									,
									array('target'=>'_blank')
							);
						}

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
					echo 0;
				}
				?>
				</td>
				<?php
				for($day = 1;$day <= $lastDay;$day++) {

					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
					if(!empty($data[$day]['agreement']['price'])) {
						echo number_format($data[$day]['agreement']['price']);
					}
				?>
				</td>
				<?php }?>
			</tr>

			<tr>
				<td rowspan="2" style="width:35px">
					キャン&nbsp;<br />セル&nbsp;
				</td>
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
				for($day = 1;$day <= $lastDay;$day++) {
					$dayVal = str_pad($day, 2, "0", STR_PAD_LEFT);

					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
					if(!empty($data[$day]['cancel']['count'])) {
						//echo number_format($data[$day]['cancel']['count']);
						if($dayStandardClientFlg) {

							echo $this->Html->link(
									number_format($data[$day]['cancel']['count']),
									'/reservations/?ReservationStatus=3
									&ReservationReturnDate[year]=' . $year.
									'&ReservationReturnDate[month]=' . $month .
									'&ReservationReturnDate[day]='. $dayVal .
									'&ReservationReturnDate2[year]=' . $year.
									'&ReservationReturnDate2[month]=' . $month .
									'&ReservationReturnDate2[day]='. $dayVal .
									'&ReservationOfficeName=' . $searchData['office_id'] .
									'&ReservationCarClassName='.  $searchData['car_class_id']

									,
									array('target'=>'_blank')
							);

						} else {

							echo $this->Html->link(
									number_format($data[$day]['cancel']['count']),
									'/reservations/?ReservationStatus=3
									&ReservationRentDate[year]=' . $year.
									'&ReservationRentDate[month]=' . $month .
									'&ReservationRentDate[day]='. $dayVal .
									'&ReservationRentDate2[year]=' . $year.
									'&ReservationRentDate2[month]=' . $month .
									'&ReservationRentDate2[day]='. $dayVal .
									'&ReservationOfficeName=' . $searchData['office_id'] .
									'&ReservationCarClassName='.  $searchData['car_class_id']

									,
									array('target'=>'_blank')
							);
						}

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
						echo 0;
					}
				?>
				</td>
				<?php
				for($day = 1;$day <= $lastDay;$day++) {
					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
					if(!empty($data[$day]['cancel']['price'])) {
						echo number_format($data[$day]['cancel']['price']);
					}
				?>
				</td>
				<?php }?>
			</tr>

			<tr>
				<td rowspan="2" >
					予約<br />獲得&nbsp;
				</td>
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
				for($day = 1;$day <= $lastDay;$day++) {

					$dayVal = str_pad($day, 2, "0", STR_PAD_LEFT);

					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
					if(!empty($data2[$day]['booking']['count'])) {
						//echo number_format($data2[$day]['booking']['count']);

						echo $this->Html->link(
								number_format($data2[$day]['booking']['count']),
								'/reservations/?ReservationStatus=
								&ReservationCreatedDate[year]=' . $year .
								'&ReservationCreatedDate[month]=' . $month .
								'&ReservationCreatedDate[day]=' . $dayVal .
								'&ReservationCreatedDate2[year]=' . $year .
								'&ReservationCreatedDate2[month]=' . $month .
								'&ReservationCreatedDate2[day]=' . $dayVal .
								'&ReservationOfficeName=' . $searchData['office_id'] .
								'&ReservationCarClassName='.  $searchData['car_class_id']
								,
								array('target'=>'_blank')
						);

					}
				?>
				</td>
				<?php }?>
			</tr>
			<tr>
				<td>予約獲得分の<br />見込売上&nbsp;</td>

				<td>
					<?php
					if(!empty($data2['sum']['expected_revenue']['price'])) {
						echo number_format($data2['sum']['expected_revenue']['price']);
					} else {
						echo 0;
					}
					?>
				</td>
				<?php
				for($day = 1;$day <= $lastDay;$day++) {

					/** 土日をあかくする **/
					$class = '';
					$w = date("w",strtotime($year ."-". $month ."-".$day));
					if($w == 0) $class = 'error';
					if($w == 6) $class = 'info';
				?>
				<td class="<?php echo $class;?>">
				<?php
					if(!empty($data2[$day]['expected_revenue']['price'])) {
						echo number_format($data2[$day]['expected_revenue']['price']);
					}
				?>
				</td>
				<?php }?>
			</tr>

		<!-- 会社毎end  -->


</table>

</div>
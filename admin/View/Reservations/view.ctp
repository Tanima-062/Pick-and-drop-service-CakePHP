<div class="reservations form">
<?php echo $this->Form->create('Reservation'); ?>

	<fieldset>
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<td>会社名</td>
					<td><?php echo $this->request['data']['Client']['name'];?></td>
				<tr>
					<td>予約番号</td>
					<td><?php echo $this->request['data']['Reservation']['reservation_key'];?></td>
				</tr>
				<tr>
					<td>氏名カナ</td>
					<td>
						<?php echo $this->request['data']['Reservation']['last_name'];?>
						<?php echo $this->request['data']['Reservation']['first_name'];?>
					</td>
				</tr>
				<tr>
					<td>ご利用人数</td>
					<td>
						大人 <?php echo $this->request['data']['Reservation']['adults_count'];?>名
						<?php if (!empty($this->request['data']['Reservation']['children_count'])) { ?>
						／
						子供 <?php echo $this->request['data']['Reservation']['children_count'];?>名
						<?php } ?>
						<?php if (!empty($this->request['data']['Reservation']['infants_count'])) { ?>
						／
						幼児 <?php echo $this->request['data']['Reservation']['infants_count'];?>名
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td>ご予約日時</td>
					<td>
						<?php echo date('Y年m月d日H時i分',strtotime($this->request['data']['Reservation']['created'])); ?>
					</td>
				</tr>
				<tr>
					<td>ご利用期間</td>
					<td>
					<?php echo date('Y年m月d日H時i分',strtotime($this->request['data']['Reservation']['rent_datetime'])); ?>
					～
					<?php echo date('Y年m月d日H時i分',strtotime($this->request['data']['Reservation']['return_datetime'])); ?>
					</td>
				</tr>
				<tr>
					<td>到着便名</td>
					<td>
						 <?php echo $this->request['data']['Reservation']['arrival_flight_number'];?>
					 </td>
				</tr>
				<tr>
					<td>出発便名</td>
					<td>
						 <?php echo $this->request['data']['Reservation']['departure_flight_number'];?>
					 </td>
				</tr>
				<tr>
					<td>キャリア</td>
					<td>
						<?php echo uaCheck($this->request['data']['Reservation']['user_agent']);?>
					</td>
				</tr>
				<tr>
					<td>メールアドレス</td>
					<td>
						<?php echo $this->request['data']['Reservation']['email'];?>
						<?php  if (!empty($mailError)) { ?>
							<p style="font-size:15px;color:red;">
								※お客様のメール環境またはメールアドレス不一致などの理由によりメッセージが届いておりません。<br>
								※お客様へのご連絡や確認事項は、お手数ですが「電話番号」へお願いします。
							</p>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td>電話番号</td>
					<td>
						<?php echo $this->request['data']['Reservation']['tel'];?>
					</td>
				</tr>

				<?php if ($this->request['data']['Reservation']['reservation_status_id'] == 3) { ?>
					<tr>
						<td rowspan=2 >キャンセル理由</td>
						<td><?php echo $cancelReason[$this->request['data']['Reservation']['cancel_reason_id']]; ?></td>
					</tr>
					<tr><td><?php echo h($this->request['data']['Reservation']['cancel_remark']); ?></td></tr>
				<?php } ?>

				<tr>
					<td>合計金額</td>
					<td>
						<?php echo number_format($this->request['data']['Reservation']['amount']);?>円
					</td>
				</tr>
				<tr>
					<td>お申込みプラン</td>
					<td><?php echo $this->request['data']['Commodity']['name']?></td>
				</tr>

				<tr>
					<td>車両タイプ</td>
					<td><?php echo $carType[$carClass['CarClass']['car_type_id']];?></td>
				</tr>
				<tr>
					<td>車両クラス</td>
					<td><?php echo $carClass['CarClass']['name'];?></td>
				</tr>
				<tr>
					<td>広告コード</td>
					<td>
						<?php echo $this->request['data']['Reservation']['advertising_cd'];?>
					</td>
				</tr>
				<tr>
					<td>貸出店舗</td>
					<td>

					<?php $rentOfficeId = $this->request['data']['Reservation']['rent_office_id']; ?>
						<?php echo $officeList[$rentOfficeId]['name'];?>
						(<?php echo $officeList[$rentOfficeId]['tel'];?>/ <?php echo $officeList[$rentOfficeId]['address'];?>/
						営業時間：<?php echo date('H:i',strtotime($this->request['data']['RentOfficeHours']['office_hours_from']));?> ~
						<?php echo date('H:i',strtotime($this->request['data']['RentOfficeHours']['office_hours_to']));?>)
					</td>
				</tr>
				<tr>
					<td>返却店舗</td>
					<td>
						<?php $returnOfficeId = $this->request['data']['Reservation']['return_office_id']; ?>
						<?php echo $officeList[$returnOfficeId]['name'];?>
						(<?php echo $officeList[$returnOfficeId]['tel'];?>/ <?php echo $officeList[$returnOfficeId]['address'];?>/
						営業時間：<?php echo date('H:i',strtotime($this->request['data']['ReturnOfficeHours']['office_hours_from']));?> ~
						<?php echo date('H:i',strtotime($this->request['data']['ReturnOfficeHours']['office_hours_to']));?>)
					</td>
				</tr>
				<?php if (!empty($reservationChildSheet)) { ?>
					<tr>
						<th colspan=2 style="background-color: #CCD4DD;">シート</th>
					</tr>
					<?php foreach ($reservationChildSheet as $key => $sheet) { ?>
					<tr>
						<td><?php echo $privilegeList[$sheet['child_sheet_id']]['name']; ?><?php if (!empty($privilegeList[$sheet['child_sheet_id']]['delete_flg'])) { echo ' (削除済)'; } ?></td>
						<td><?php echo $sheet['count']; ?></td>
					</tr>
					<?php } ?>
				<?php } ?>
				<?php if (!empty($reservationPrivilege)) { ?>
					<tr>
						<th colspan=2 style="background-color: #CCD4DD;">特典オプション</th>
					</tr>
					<?php foreach ($reservationPrivilege as $key => $privilege) { ?>
					<tr>
						<td><?php echo $privilegeList[$privilege['privilege_id']]['name']; ?><?php if (!empty($privilegeList[$privilege['privilege_id']]['delete_flg'])) { echo ' (削除済)'; } ?></td>
						<td><?php echo $privilege['count']; ?></td>
					</tr>
					<?php } ?>
				<?php } ?>
				<tr>
					<th colspan=2 style="background-color: #CCD4DD;">ステータス</th>
				</tr>
				<tr>
					<td>予約状況</td>
					<td><?php echo $statusList[$this->request['data']['Reservation']['reservation_status_id']];?></td>
				</tr>
		</table>
	</fieldset>


	<?php if(!empty($mails)){ ?>
	<fieldset>
		<legend id="inquiry">
			<?php echo __('お問い合わせへの回答'); ?>
		</legend>
		<?php
		if (isset($sendError)) {
			echo $sendError;
		}
		?>
	</fieldset>
	<dl style="padding:10px;border:solid #ddd 1px;box-sizing:border-box;">
		<?php
			foreach($mails as $key => $mail){
				$staffId = $mail['ReservationMail']['staff_id'];
				if($key == 0) echo "<span class=\"badge badge-success\">New</span>";
				?>
			<?php if( $mail['ReservationMail']['staff_id'] == 0){ ?>
				<dt style="color:#145612;">
					<?php echo date('Y年m月d日 H時i分s秒',strtotime($mail['ReservationMail']['mail_datetime']));?>
					(お客様)
				</dt>
				<dd style="color:#145612;">
					<p style="word-wrap: break-word;">
						<?php echo nl2br(h($mail['ReservationMail']['contents']));?>
					</p>
				</dd>
			<?php } else { ?>
				<dt style="color:#000;">
					<?php echo date('Y年m月d日 H時i分s秒',strtotime($mail['ReservationMail']['mail_datetime']));?>
					(<?php echo $staffList[$staffId]; ?>)
					<?php if ($mail['ReservationMail']['read_flg']) { ?>
						<span style="color:blue;">既読</span>
					<?php } else { ?>
						<span style="color:red;">未読</span>
					<?php } ?>
				</dt>
				<dd style="color:#000;">
					<p style="word-wrap: break-word;">
						<?php echo nl2br(h($mail['ReservationMail']['contents']));?>
					</p>
				</dd>
			<?php } ?>
		<?php } ?>
	</dl>
	<?php } ?>
</div>

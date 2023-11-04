<div class="reservations form">
<?php echo $this->Form->create('Reservation'); ?>
	<fieldset>
		<legend id="title">
			<?php echo __('skyticket　顧客情報'); ?>
		</legend>
	</fieldset>
	<fieldset>
		<?php echo $this->Form->input('id',array('label'=>'ID','div'=>false));?>
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<td>予約番号</td>
					<td><?php echo $this->request['data']['Reservation']['reservation_key'];?></td>
				</tr>
				<tr>
					<td>申込み日時</td>
					<?php
					$date = date('Y年m月d日', strtotime($this->request['data']['Reservation']['created']));
					$week = $wday[date('w', strtotime($this->request['data']['Reservation']['created']))];
					$time = date('H時i分', strtotime($this->request['data']['Reservation']['created']));
					?>
					<td><?php echo $date; ?>（<?php echo $week; ?>）<?php echo $time; ?></td>
				</tr>
				<tr>
					<td>キャンセル日時</td>
					<?php
					$date = date('Y年m月d日', strtotime($this->request['data']['Reservation']['cancel_datetime']));
					$week = $wday[date('w', strtotime($this->request['data']['Reservation']['cancel_datetime']))];
					$time = date('H時i分', strtotime($this->request['data']['Reservation']['cancel_datetime']));
					?>
					<td>
						<?php if(!empty($this->request['data']['Reservation']['cancel_flg'])): ?>
						<?php echo $date; ?>（<?php echo $week; ?>）<?php echo $time; ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>管理番号</td>
					<td><?php echo $this->request['data']['Reservation']['control_number'];?></td>
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
						大人 <?php echo $this->request['data']['Reservation']['adults_count'];?>名 ／
						子供 <?php echo $this->request['data']['Reservation']['children_count'];?>名 ／
						幼児 <?php echo $this->request['data']['Reservation']['infants_count'];?>名
					</td>
				</tr>
				<tr>
					<td>ご利用期間</td>
					<td>
					<?php echo date('Y年m月d日H時i分',strtotime($this->request['data']['Reservation']['rent_datetime'])); ?>
					(<?php echo $fromWeek; ?>)
					～
					<?php echo date('Y年m月d日H時i分',strtotime($this->request['data']['Reservation']['return_datetime'])); ?>
					(<?php echo $toWeek; ?>)
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
					<td><?php echo $this->request['data']['Reservation']['departure_flight_number'];?></td>
				</tr>
				<tr>
					<td>メールアドレス</td>
					<td>
						<?php echo $this->request['data']['Reservation']['email'];?>
					</td>
				</tr>
				<tr>
					<td>電話番号</td>
					<td>
						<?php echo $this->request['data']['Reservation']['tel'];?>
					</td>
				</tr>
				<tr>
					<td>ステータス</td>
					<td>
						<?php echo $reservationStatus[$this->data['Reservation']['reservation_status_id']];?>
					</td>
				</tr>
				<?php
				if ($acceptPrepay && $this->data['Commodity']['sales_type'] != Constant::SALES_TYPE_AGENT_ORGANIZED) {
				?>
				<tr>
					<td>支払方法</td>
					<td>
						<?php echo $paymentMethod[$isPaidInAdvance]; ?>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td>合計金額</td>
					<td>
						<?php echo number_format($this->request['data']['Reservation']['amount']);?>円
						&nbsp;&nbsp;&nbsp;（ 予約時内容 ：
						<?php foreach ($breakDownContent as $key => $breakDown) { ?>
							<?php echo $breakDown['name']; ?>
							<?php echo number_format($breakDown['sum']); ?>円
							<?php if ($breakDown != end($breakDownContent)) { ?>
								<?php echo '、'; ?>
							<?php } ?>
						<?php } ?>）
					</td>
				</tr>
				<tr>
					<td>お申込みプラン</td>
					<td><?php echo h($this->data['Commodity']['name']); ?></td>
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
					<td>車両台数</td>
					<td>
						<?php echo $this->request['data']['Reservation']['cars_count'];?>台
					</td>
				</tr>
				<tr>
					<td>貸出店舗</td>
					<td>
						<?php $rentOfficeId = $this->request['data']['Reservation']['rent_office_id']; ?>
						<?php echo $rentOfficeMap[$rentOfficeId]['Office']['name'];?>
						<?php 
						if(!empty($rentOfficeMap[$rentOfficeId]['Office']['office_code'])){
							echo "(".$rentOfficeMap[$rentOfficeId]['Office']['office_code'].")";
						}
						?>
					</td>
				</tr>
				<tr>
					<td>返却店舗</td>
					<td>
						<?php $returnOfficeId = $this->request['data']['Reservation']['return_office_id']; ?>
						<?php echo $returnOfficeMap[$returnOfficeId]['Office']['name'];?>
						<?php 
						if(!empty($returnOfficeMap[$returnOfficeId]['Office']['office_code'])){
							echo "(".$returnOfficeMap[$returnOfficeId]['Office']['office_code'].")";
						}
						?>
					</td>
				</tr>
				<tr>
					<th colspan=2 style="background-color: #CCD4DD;">シート</th>
				</tr>

				<?php foreach ($privilegeSheet as $key => $sheet) { ?>
					<?php if (!empty($reservationChildSheetData[$sheet['Privilege']['id']]) && $reservationChildSheetData[$sheet['Privilege']['id']]['count'] > 0) { ?>
					<tr>
						<td><?php echo $sheet['Privilege']['name']; ?></td>
						<td><?php echo $reservationChildSheetData[$sheet['Privilege']['id']]['count']; ?><?php echo $sheet['Privilege']['unit_name']; ?></td>
					</tr>
					<?php } ?>
				<?php } ?>
				<tr>
					<th colspan=2 style="background-color: #CCD4DD;">特典オプション</th>
				</tr>
				<?php foreach ($privilegeData as $key => $privilege) { ?>
					<?php if (!empty($reservationPrivilegeData[$privilege['Privilege']['id']]) && $reservationPrivilegeData[$privilege['Privilege']['id']]['count'] > 0) { ?>
					<tr>
						<td><?php echo $privilege['Privilege']['name']; ?></td>
						<td><?php echo $reservationPrivilegeData[$privilege['Privilege']['id']]['count']; ?><?php echo $privilege['Privilege']['unit_name']; ?></td>
					</tr>
					<?php } ?>
				<?php } ?>
		</table>
	</fieldset>



	<fieldset>
		<legend id="inquiry">
			<?php echo __('お問い合わせ履歴'); ?>
		</legend>
	</fieldset>
	<?php if(!empty($this->request->data['ReservationMail'])){?>
	<dl style="border:solid #ddd 1px;padding:10px;box-sizing:border-box;">
		<?php foreach($this->request->data['ReservationMail'] as $key => $mail){?>
			<?php if($key == 0) echo "<span class=\"badge badge-success\">New</span>";?>
			<?php if($mail['ReservationMail']['staff_id'] == 0){ ?>
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
					(<?php echo $mail['Staff']['name']; ?>)
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
		<?php }?>
	</dl>
	<?php }?>

</div>

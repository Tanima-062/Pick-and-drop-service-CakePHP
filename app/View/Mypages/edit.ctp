<div id="renewal" class="login-edit wrap contents clearfix mypages-edit_page">



<?php echo $this->Form->create('Reservation', array(
		'url' => '/mypages/edit/'.$this->params['content'].'/check/',
		'name' => 'reserve',
		'type' => 'post',
		'inputDefaults' => $inputDefaults)); ?>

<?php if ($this->params['content'] == 'airport') { ?>
	<div class="edit-title">
		<h2 class="h-type03">到着便/出発便の登録・変更</h2>
		<p class="ttxt topbtm30 complete_panel_message margin-btm30 text_center">
			ご利用の到着便/出発便を入力して<br>
			「送信内容を確認する」ボタンを押して下さい。
		</p>
	</div>
	<div class="step-box">
		<h3 class="h-type04">到着便/出発便を登録</h3>
		<div class="box-outer">
			<div class="box-inner">
				<table class="pc-form-date airport-table box-outer">
					<?php if (!empty($arrivalAirport)){
						$display = "";
					} else {
						$display = "display:none";
					}
					?>
					<tr style="<?php echo $display ?>">
						<th>到着便名</th>
						<td>
						<?php echo $this->Form->hidden('defaultFlightNumber', array('value' => $result['Reservation']['arrival_flight_number'])); ?>
						<?php
						echo $this->Form->input('Reservation.arrival_flight_number', array(
								'type' => 'text',
								'default' => $result['Reservation']['arrival_flight_number'],
						)); ?>
						</td>
					</tr>
					<?php if (!empty($departureAirport)){
						$display = "";
					} else {
						$display = "display:none";
					}
					?>
					<tr style="<?php echo $display ?>">
						<th>出発便名</th>
						<td>
						<?php echo $this->Form->hidden('defaultDepartureFlightNumber', array('value' => $result['Reservation']['departure_flight_number'])); ?>
						<?php
						echo $this->Form->input('Reservation.departure_flight_number', array(
								'type' => 'text',
								'default' => $result['Reservation']['departure_flight_number'],
						)); ?>
						</td>
					</tr>

				</table><!-- end .pc-form-date -->
			</div><!-- end .box-inner -->
		</div><!-- end .box-outer -->
	</div><!-- end .step-box -->
<?php } ?>





<!-- メールアドレス変更 -->
<?php if ($this->params['content'] == 'mail') { ?>
	<div class="edit-title">
		<h2 class="h-type03">メールアドレスの変更</h2>
		<p class="ttxt topbtm30 complete_panel_message margin-btm30 text_center">
			変更後のメールアドレスを入力して「送信内容を確認する」ボタンを押して下さい。
		</p>
	</div>
	<div class="changeUserdata">

	<table class="">
		<th>メールアドレス</th>
		<td>
			<?php echo $this->Form->hidden('defaultEmail', array('value' => $result['Reservation']['email'])); ?>
			<?php echo $this->Form->input('email',array('value'=>$result['Reservation']['email'],'required' => true,'class'=>'textInput _mail')); ?>
			<p class="suggestion"></p>
		</td>
	</tr>
	</table>
</div>

<?php } ?>



<!-- 電話番号変更 -->
<?php if ($this->params['content'] == 'tel') { ?>
	<div class="edit-title">
		<h2 class="h-type03">電話番号の変更</h2>
		<p class="ttxt topbtm30 complete_panel_message margin-btm30 text_center">
			変更後の電話番号を入力して「送信内容を確認する」ボタンを押して下さい。
		</p>
	</div>
	<div class="changeUserdata">
		<table>
			<tr>
				<th>電話番号</th>
				<td>
					<?php echo $this->Form->hidden('defaultTel', array('value' => $result['Reservation']['tel'])); ?>
					<input id="ReservationMobile" name="tel" required="required" type="text" class="textInput _tel"
							value="<?php echo $result['Reservation']['tel']; ?>"
							onblur="if(this.value==''){this.value='<?php echo $result['Reservation']['tel']; ?>';}"
							maxlength="11" pattern="^[0-9]+$">
				</td>
			</tr>
		</table>
	</div>
<?php } ?>


<!-- ご利用人数変更 -->

<?php
 if ($this->params['content'] == 'count') { ?>
	<div class="edit-title">
		<h2 class="h-type03">ご利用人数の変更</h2>
		<p class="ttxt topbtm30 complete_panel_message margin-btm30 text_center">
			変更後の人数を入力して「送信内容を確認する」ボタンを押して下さい。
		</p>
	</div>
	<div class="changeUserdata">
		<table class="">
			<tr>
				<th>ご利用人数</th>
				<td>
					<div class="-item">
						大人（12歳以上）：
						<div class="-select">
						<?php echo $this->Form->hidden('defaultAdults_count', array('value' => $result['Reservation']['adults_count'])); ?>
						<?php
							echo $this->Form->input('adults_count', array(
								'type' => 'select',
								'options' => $adultPassengers,
								'default' => intval($result['Reservation']['adults_count'])
							));
						?>
						</div> 名
					</div>
					<div class="-item">
						子供（6〜11歳）：
						<div class="-select">
						<?php echo $this->Form->hidden('defaultChildren_count', array('value' => $result['Reservation']['children_count'])); ?>
						<?php
							echo $this->Form->input('children_count', array(
								'type' => 'select',
								'options' => $passengers,
								'default' => intval($result['Reservation']['children_count'])
							));
						?>
						</div> 名
					</div>
					<div class="-item">
						幼児（6歳未満）：
						<div class="-select">
						<?php echo $this->Form->hidden('defaultInfants_count', array('value' => $result['Reservation']['infants_count'])); ?>
						<?php
							echo $this->Form->input('infants_count', array(
								'type' => 'select',
								'options' => $passengers,
								'default' => intval($result['Reservation']['infants_count']),
								'readonly' => true
							));
						?>
						</div> 名
					</div>
					<div class="infants_comment">
						幼児の乗車にはチャイルドシート等が必要なため、予約確認ページからは変更できません。<br>
						変更が必要な場合は、ご利用の店舗までお問合せください。
					</div>
					<?php echo $this->Form->hidden('capacity', array('value' => $capacity)); ?>
				</td>
			</tr>
		</table>
	</div>
<?php } ?>



<?php
// お問い合わせ
if ($this->params['content'] == 'contents') { ?>
<div class="edit-title">
	<h2 class="h-type03">お問い合わせ</h2>
	<p class="ttxt topbtm30 complete_panel_message margin-btm30 text_center">
		お問い合わせの内容を入力して<br>
		「送信内容を確認する」ボタンを押して下さい。
	</p>
</div>
<div class="step-box">
	<h3 class="h-type04">お問い合わせ</h3>
	<div class="box-outer">
		<p class="input-text">
		<?php echo $this->Form->textarea('contents', array('required' => true, 'style' => array('width:100%;box-sizing:border-box;min-height:200px;'))); ?>
		</p>
	</div>
</div>
<?php }  ?>
<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
?>

	<div class="wid95 submit">
		<p class="btn-submit">
			<?php
			echo $this->Form->submit('送信内容を確認する', array(
			 		'name' => 'reserveBtn',
			 		'class' => 'btn btn btn_submit rent-margin-bottom-important',
					'value' => 'reserve',
					'div' => false
			)); ?>
		</p>
	</div>
	<?php echo $this->Form->hidden('Reservation.reservation_key', array('value' => h($result['Reservation']['reservation_key']))); ?>
	<?php echo $this->Form->hidden('Reservation.tel', array('value' => h($this->data['Reservation']['tel']))); ?>
<?php echo $this->Form->end(); ?>
	<div class="rent-margin-bottom btn btn_cancel">
<?php echo $this->Html->link('戻る', '/mypages/'); ?>
	</div>
</div>

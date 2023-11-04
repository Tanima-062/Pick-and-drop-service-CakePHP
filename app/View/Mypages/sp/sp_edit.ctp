	<!-- コンテンツ -->
	<div id="js-content" class="sp_mypages-edit_page">
		<?php
			if (!empty($sessionMessage)) {
				echo $this->element('session_message');
			}
		?>

		<?php echo $this->Form->create('Reservation', array(
				'url' => '/mypages/edit/'.$this->params['content'].'/check/',
				'name' => 'reserve',
				'type' => 'post',
				'inputDefaults' => $inputDefaults)); ?>

<?php if ($this->params['content'] == 'airport') { ?>
			<h2 class="title_blue_line"><span>到着便/出発便の登録・変更</span></h2>
			<section class="">
                <div class="inner">
					<p class="ttxt">
						ご利用の到着便/出発便を入力して<br>
						「送信内容を確認する」ボタンを押して下さい。
					</p>
                </div>
            </section>
            <section class="plan_form">
                <h3 class="plan_form_title">到着便/出発便を登録</h3>
                <section class="inner">
					<table>
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
					</table>
				</section>
			</section>
<?php } ?>


<!-- メールアドレス・電話番号変更 -->
<?php if ($this->params['content'] == 'mail') { ?>
	<h2 class="title_blue_line"><span>メールアドレスの変更</span></h2>
	<section>
		<div class="inner">
			<p class="ttxt">
			変更後のメールアドレスを入力して「送信内容を確認する」ボタンを押して下さい。
			</p>
		</div>
	</section>
<div class="changeUserdata">

			<div class="-title">メールアドレス</div>
			<div>
				<?php echo $this->Form->hidden('defaultEmail', array('value' => $result['Reservation']['email'])); ?>
				<?php echo $this->Form->input('email',array('value'=>$result['Reservation']['email'],'required' => true,'class'=>'textInput _mail')); ?>
				<p class="suggestion"></p>
			</div>
</div>
<?php } ?>


<!-- メールアドレス・電話番号変更 -->
<?php if ($this->params['content'] == 'tel') { ?>
	<h2 class="title_blue_line"><span>電話番号の変更</span></h2>
	<section>
		<div class="inner">
			<p class="ttxt">
			変更後の電話番号を入力して「送信内容を確認する」ボタンを押して下さい。
			</p>
		</div>
	</section>
<div class="changeUserdata">

			<div class="-title">電話番号</div>
			<div>
				<?php echo $this->Form->hidden('defaultTel', array('value' => $result['Reservation']['tel'])); ?>
				<input id="ReservationMobile" name="tel" required="required" type="text" class="textInput _tel"
						value="<?php echo $result['Reservation']['tel']; ?>"
						onblur="if(this.value==''){this.value='<?php echo $result['Reservation']['tel']; ?>';}"
						maxlength="11" pattern="^[0-9]+$">
			</div>
</div>
<?php } ?>


<?php if ($this->params['content'] == 'count') { ?>
<h2 class="title_blue_line"><span>ご利用人数の変更</span></h2>
<section>
	<div class="inner">
		<p class="ttxt">
		変更後の人数を入力して「送信内容を確認する」ボタンを押して下さい。
		</p>
	</div>
</section>
<div class="changeUserdata">

		<div class="-title">ご利用人数</div>
		<div class="-item">
			<table>
				<tr>
					<td>
						大人（12歳以上）：
					</td>
					<td>
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
					</td>
			</tr>
			<tr>
				<td>
					子供（6〜11歳）：
				</td>
				<td>
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
				</td>
			</tr>
			<tr>
				<td>
					幼児（6歳未満）：
				</td>
				<td>
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
				</td>
			</tr>
			</table>
			<div class="infants_comment">
				幼児の乗車にはチャイルドシート等が必要なため、予約確認ページからは変更できません。<br>
				変更が必要な場合は、ご利用の店舗までお問合せください。
			</div>
			<?php echo $this->Form->hidden('capacity', array('value' => $capacity)); ?>
		</div>
</div>
<?php } ?>


<!-- お問い合わせ -->
<?php if ($this->params['content'] == 'contents') { ?>
			<h2 class="title_blue_line"><span>お問い合わせ</span></h2>
			<section class="">
                <div class="inner">
					<p class="ttxt">
						お問い合わせの内容を入力して<br>
						「送信内容を確認する」ボタンを押して下さい。
					</p>
                </div>
            </section>
            <section class="plan_form">
                <h3 class="plan_form_title">お問い合わせ</h3>
                <section>
					<div class="box-outer">
						<p class="input-text p_10px">
							<?php echo $this->Form->textarea('contents', array('required' => true)); ?>
						</p>
					</div>
				</section>
			</section>
<?php }  ?>

			<div class="wid95 submit">
				<p class="btn-submit">
					<?php
					echo $this->Form->submit('送信内容を確認する', array(
							'name' => 'reserveBtn',
							'class' => 'btn bg_orange btn_bg_important',
							'value' => 'reserve',
							'div' => false
					)); ?>
				</p>
			</div>
			<?php echo $this->Form->hidden('Reservation.reservation_key', array('value' => h($result['Reservation']['reservation_key']))); ?>
			<?php echo $this->Form->hidden('Reservation.tel', array('value' => h($this->data['Reservation']['tel']))); ?>
		<?php echo $this->Form->end(); ?>
		<div class="ac inner mb20px">
	<?php echo $this->Html->link('戻る', '/mypages/'); ?>
		</div>
	</div><!-- end #js-content -->

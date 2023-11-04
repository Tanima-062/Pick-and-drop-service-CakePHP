<div id="js-content" class="sp_mypages-check_page">
<?php if (!empty($this->data['Reservation']['contents'])) { ?>
	<h2 class="title_blue_line"><span>お問い合わせ内容</span></h2>
<?php } else { ?>
	<h2 class="title_blue_line"><span>変更内容</span></h2>
<?php } ?>

<?php echo $this->Form->create('Reservation', array(
		'url' => '/mypages/edit/'.$this->params['content'].'/check/',
		'name' => 'reserve',
		'type' => 'post',
		'inputDefaults' => $inputDefaults)); ?>


		<?php
			$editflag = FALSE;
		?>
	<section class="plan_form changeUserdata" >
		<table>
		<!-- メールアドレス・電話番号の確認 -->
		<?php if (!empty($this->data['Reservation']['defaultEmail']) && !empty($this->data['Reservation']['email']) && $this->data['Reservation']['email'] != $this->data['Reservation']['defaultEmail']) {
			$editflag = TRUE;
		?>
			<tr>
				<th>メールアドレス</th>
				<td>
					<span class="cancellation"><?php echo $this->data['Reservation']['defaultEmail']; ?></span>
					<br>
					<?php echo $this->data['Reservation']['email']; ?>
					<?php echo $this->Form->hidden('email', array('value' => h($this->data['Reservation']['email']))); ?>
				</td>
			</tr>
		<?php } ?>
		<?php if (!empty($this->data['Reservation']['defaultTel']) && !empty($this->data['tel']) && $this->data['tel'] != $this->data['Reservation']['defaultTel']) {
			$editflag = TRUE;
		?>
			<tr>
				<th>電話番号</th>
				<td>
					<span class="cancellation"><?php echo $this->data['Reservation']['defaultTel']; ?></span>
					<br>
					<?php echo $this->data['tel']; ?>
					<?php echo $this->Form->hidden('edit_tel', array('value' => $this->data['tel'])); ?>
				</td>
			</tr>
		<?php } ?>

		<?php if ($this->data['Reservation']['adults_count'] != $this->data['Reservation']['defaultAdults_count'] || $this->data['Reservation']['children_count'] != $this->data['Reservation']['defaultChildren_count']) {
			$editflag = TRUE;
		?>
<tr>
	<th>ご利用人数</th>
	<td>
	<?php if ($this->data['Reservation']['adults_count'] != $this->data['Reservation']['defaultAdults_count'] ) { ?>
		<div>
			大人　<span class="cancellation"><?php echo $this->data['Reservation']['defaultAdults_count']; ?>名</span>　→　
			<b><?php echo $this->data['Reservation']['adults_count']; ?>
				<?php echo $this->Form->hidden('adults_count', array('value' => $this->data['Reservation']['adults_count'])); ?></b>名
		</div>
	<?php } ?>
	<?php if ($this->data['Reservation']['children_count'] != $this->data['Reservation']['defaultChildren_count']) { ?>
		<div>
			子供　<span class="cancellation"><?php echo $this->data['Reservation']['defaultChildren_count']; ?>名</span>　→　
			<b><?php echo $this->data['Reservation']['children_count']; ?>
			<?php echo $this->Form->hidden('children_count', array('value' => $this->data['Reservation']['children_count'])); ?></b>名
		</div>
	<?php } ?>
	</td>
</tr>
<?php } ?>


		<?php if (array_search(1, $reserveChangeFlg) == 'airline') {
			$editflag = TRUE;
?>
		<?php
		if(empty($this->data['Reservation']['defaultFlightNumber']) && empty($this->data['Reservation']['arrival_flight_number'])){
			$display = "display:none";
		} else {
			$display = "";
		}
		?>
		<tr style="<?php echo $display ?>">
			<th>到着便</th>
			<td>
				<?php if (!empty($this->data['Reservation']['defaultFlightNumber']) && $this->data['Reservation']['defaultFlightNumber'] !== $this->data['Reservation']['arrival_flight_number']) { ?>
				<span class="cancellation"><?php echo $this->data['Reservation']['defaultFlightNumber']; ?></span>
				<br>
				<?php } ?>
				<?php echo $this->data['Reservation']['arrival_flight_number']; ?>
				<?php echo $this->Form->hidden('arrival_flight_number', array('value' => $this->data['Reservation']['arrival_flight_number'])); ?>
			</td>
		</tr>
		<?php
		if(empty($this->data['Reservation']['defaultDepartureFlightNumber']) && empty($this->data['Reservation']['departure_flight_number'])){
			$display = "display:none";
		} else {
			$display = "";
		}
		?>
		<tr style="<?php echo $display ?>">
			<th>出発便</th>
			<td>
				<?php if (!empty($this->data['Reservation']['defaultDepartureFlightNumber']) && $this->data['Reservation']['defaultDepartureFlightNumber'] !== $this->data['Reservation']['departure_flight_number']) { ?>
				<span class="cancellation"><?php echo $this->data['Reservation']['defaultDepartureFlightNumber']; ?></span>
				<br>
				<?php } ?>
				<?php echo $this->data['Reservation']['departure_flight_number']; ?>
				<?php echo $this->Form->hidden('departure_flight_number', array('value' => $this->data['Reservation']['departure_flight_number'])); ?>
			</td>
		</tr>
		<?php } ?>

		<!-- お問い合わせ -->
		<?php if (!empty($this->data['Reservation']['contents'])) { ?>
			<tr>
				<th>内容</th>
				<td>
					<?php echo nl2br($this->data['Reservation']['contents']); ?>
					<?php echo $this->Form->hidden('contents', array('value' => $this->data['Reservation']['contents'])); ?>
				</td>
			</tr>
		<?php } ?>
		</table>
	</section>

	<?php	if (!empty($this->data['Reservation']['contents']) || $editflag){ ?>
	<p class="btn-submit"><?php echo $this->Form->submit('送信', array(
						'id' => 'rewrite',
				 		'name' => 'rewriteBtn',
				 		'class' => 'btn btn_bg_important bg_orange',
				 		'value' => 'rewrite',
						'div' => false,)); ?>
	</p>
	<?php } else { ?>
		<div class="changeUserdata">

		<p>変更内容がありません。</p>
		<div class="back_mypage">
			<a href="/rentacar/mypages/">
				マイページにもどる
			</a>
		</div>

		</div>
	<?php } ?>



	<?php echo $this->Form->hidden('reservation_key', array('value' => h($this->data['Reservation']['reservation_key']))); ?>
	<?php echo $this->Form->hidden('tel', array('value' => h($this->data['Reservation']['tel']))); ?>
	<?php echo $this->Form->hidden('changeFlag', array('value' => $changeFlag)); ?>
	<?php echo $this->Form->hidden('reserveChangeFlg', array('value' => json_encode($reserveChangeFlg))); ?>
<?php echo $this->Form->end(); ?>

</div>

<script>
$(function() {
	// 二重送信防止
	$('form').on('submit', function(event){
		// ボタンをdisabledスタイルに上書き
		$('#rewrite').addClass("btn_mypage_disabled");
		// 2回目以降はクラスが付与されfalseが返るようになる
		if ($('.mypage_disabled_flg').size() > 0) {
			return false;
		}
		$(this).addClass("mypage_disabled_flg");
	});
});
</script>

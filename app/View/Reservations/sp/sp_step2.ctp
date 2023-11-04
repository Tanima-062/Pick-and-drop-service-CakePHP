<main class="step2-contents">

	<!-- コンテンツ -->
	<div id="js-content">

		<div id='sessionMessage'>
<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
?>
		</div>

		<?php echo $this->element('sp_reservation_steps'); ?>
		
		<?php echo $this->element('sp_reservation_timer'); ?>

		<?php echo $this->element('sp_plan_view'); ?>
		
<?php
	echo $this->Form->create('Reservation', array(
		'type' => 'post',
		'url' => 'completion/',
		'class' => 'st-table rent-margin-bottom-l',
		'inputDefaults' => array(
			'div' => false,
			'label' => false,
			'legend' => false,
		),
	));
?>
		<section class="plan_form" id="plan_form">
			<h3 class="plan_form_title">お客様情報の確認</h3>
			<section class="inner mb10px">
				<dl class="plan_form_dl">
					<dt class="plan_form_dt">氏名</dt>
					<dd class="plan_form_dd"><?= $confirmation['last_name']; ?> <?= $confirmation['first_name']; ?></dd>
					<dt class="plan_form_dt">携帯電話番号</dt>
					<dd class="plan_form_dd"><?= $confirmation['tel']; ?></dd>
					<dt class="plan_form_dt">メールアドレス</dt>
					<dd class="plan_form_dd"><?= $confirmation['email']; ?></dd>
<?php
	if (!empty($confirmation['remark'])) {
?>
					<dt class="plan_form_dt">備考欄</dt>
					<dd class="plan_form_dd"><?= nl2br($confirmation['remark']); ?></dd>
<?php
	}
?>
				</dl>
<?php
	if (!$paymentApi) { //決済APIがfalseのとき
?>
				<dl class="plan_form_dl">
					<dt class="plan_form_dt">カード番号</dt>
					<dd class="plan_form_dd"><span id="cardNumber"></span></dd>
					<dt class="plan_form_dt">カード名義</dt>
					<dd class="plan_form_dd"><span id="cardOwner"></span></dd>
					<dt class="plan_form_dt">セキュリティコード</dt>
					<dd class="plan_form_dd"><span>****</span></dd>
					<dt class="plan_form_dt">カード有効期限</dt>
					<dd class="plan_form_dd"><span id="cardExpMonth"></span> / <span id="cardExpYear"></span></dd>
				</dl>
<?php
	}
?>
				<div>
					<?php echo $this->Html->link('お客様情報を変更する', '/reservations/step1/'.$this->data['Reservation']['uniqId'].'/'.($fromRentacarClient ? '?from_rentacar_client=true' : ''), array('class' => 'btn-type-sub')); ?>
				</div>
			</section>

			<section>
				<h3 class="plan_form_title">予約条件の確認</h3>
				<h4 class="title_blue">貸出期間</h4>
				<div class="inner">
					<table>
						<tr>
							<th>受取日</th>
						</tr>
						<tr>
							<td>
								<div class="date-input"><?= $confirmation['from']; ?></div>
							</td>
						</tr>
						<tr>
							<th>返却日</th>
						</tr>
						<tr>
							<td>
								<div class="date-input"><?= $confirmation['to']; ?></div>
							</td>
						</tr>
					</table>
				</div>
			</section>
			<section>
				<h4 class="title_blue">予約詳細</h4>
				<div class="inner">
					<table>
						<tr>
							<th>受取営業所</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?= $confirmation['rentOfficeName']; ?>
								</div>
							</td>
						</tr>
<?php
	if (!empty($confirmation['rentOfficeMeetingInfo'])) {
?>
						<tr>
							<th>受取方法</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?php echo $confirmation['rentOfficeMeetingInfo']; ?>
								</div>
							</td>
						</tr>
<?php
	}
?>
					</table>

<?php
	if (!empty($confirmation['arrival'])) {
?>
					<table>
						<tr>
							<th>到着便</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?php echo $confirmation['arrival']; ?>
								</div>
							</td>
						</tr>
					</table>
<?php
	}
?>

<?php
	if (!empty($arrivalAirport) && empty($confirmation['arrival'])) {
?>
					<div class="caution_blue -detail">
						<i class="icm-warning"></i>
						<div>
							<p class="title">空港到着便はまだ未入力です</p>
							<p class="detail">予約完了後、予約の照会・変更・取消へログインしてご登録ください。<br>※受取日の前日までにご登録がない場合、空港送迎バスをご利用いただけない場合がございます。</p>
						</div>
					</div>
<?php
	}
?>
					<table>
						<tr>
							<th>返却営業所</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?= $confirmation['returnOfficeName']; ?>
								</div>
							</td>
						</tr>
<?php
	if (!empty($confirmation['returnOfficeMeetingInfo'])) {
?>
						<tr>
							<th>返却方法</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?= $confirmation['returnOfficeMeetingInfo']; ?>
								</div>
							</td>
						</tr>
<?php
	}
?>
					</table>
<?php
	if (!empty($confirmation['departure'])) {
?>
					<table>
						<tr>
							<th>出発便</th>
						</tr>
						<tr>
							<td class="select_type_line1">
								<div class="search_select">
									<?= $confirmation['departure']; ?>
								</div>
							</td>
						</tr>
					</table>
<?php
	}
?>

<?php
	if (!empty($departureAirport) && empty($confirmation['departure'])) {
?>
					<div class="caution_blue -detail">
						<i class="icm-warning"></i>
						<div>
							<p class="title">空港出発便はまだ未入力です</p>
							<p class="detail">予約完了後、予約の照会・変更・取消へログインしてご登録ください。<br>※返却日の前日までにご登録がない場合、空港送迎バスをご利用いただけない場合がございます。</p>
						</div>
					</div>
<?php
	}
?>
				</div>
			</section>
		</section>

		<section class="plan_comfirmation">
			<section>
				<h3 class="plan_form_title">お支払い料金</h3>
				<h4 class="title_blue">ご利用人数</h4>
				<table class="people_num">
					<tr>
						<td>
							<span>大人</span><?php echo $confirmation['adults']; ?>人
						</td>
						<td>
							<span>子供</span><?php echo $confirmation['children']; ?>人
						</td>
						<td>
							<span>幼児</span><?php echo $confirmation['infants']; ?>人
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<th>ご利用日数</th>
						<td><?php echo $rentalPeriod; ?></td>
					</tr>
					<tr>
						<th>基本料金</th>
						<td><?php echo number_format($basicCharge); ?>円</td>
					</tr>
				</table>
				<table class="price_table mb10px">
<?php
	if (!empty($confirmation['dropOffLateNight']['dropPrice'])) {
?>
					<tr>
						<th>乗り捨て料金</th>
						<td><?php echo number_format($confirmation['dropOffLateNight']['dropPrice']); ?>円</td>
					</tr>
<?php
	}
?>

<?php
	if (!empty($confirmation['dropOffLateNight']['nightFee'])) {
?>
					<tr>
						<th>深夜手数料</th>
						<td><?= number_format($confirmation['dropOffLateNight']['nightFee']); ?>円</td>
					</tr>
<?php
	}
?>

<?php
	if (!empty($confirmation['privilegeOption'])) {
		foreach ($confirmation['privilegeOption'] as $key => $value) { 
?>
					<tr>
						<th><?php echo $value[0]; ?></th>
						<td><?php echo $value[1]; ?>円</td>
					</tr>
<?php 
		}
	}
?>
				</table>
				<div class="inner">
					<div class="plan_info_body_price price_block clearfix">
						<div class="fl">
							<p class="price_block_title">合計料金<span>（免責補償・税込）</span></p>
							<p class="caption_gray"><?php echo $rentalPeriod; ?>料金</p>
						</div>
						<div class="fr">
							<p class="price_block_price"><span><?php echo number_format($confirmation['estimationTotalPrice']); ?>円</span></p>
						</div>
					</div>

					<p class="plan_warning_red">料金は変動します。今日のお得な料金で予約を確定！</p>

					<?php echo $this->element('sp_reservation_note_memo_reminder'); // 予約番号メモリマインダー ?>

				</div>
			</section>
		</section>
		<?php echo $this->Form->hidden('uniqId', array('value' => $this->data['Reservation']['uniqId'])); ?>
		<?php echo $this->Form->hidden('isStep1', array('value' => false)); ?>
		<?php echo $this->Form->end(); ?>

		<div class="mb20px">
			<dl class="accordion">
				<dt class="trigger">
					注意事項<span class="open-close">open</span>
				</dt>
				<dd class="acordion_tree">
					<?php echo nl2br(h($confirmation['precautions'])); ?>
				</dd>
				<dt class="trigger">
					キャンセルポリシー<span class="open-close">open</span>
				</dt>
				<dd class="acordion_tree">
					ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
					<br>
					〈キャンセル料〉<br>
					<?php echo $confirmation['cancelPolicy']; ?><br>
					・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
					・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。<br>
					<br>
					■キャンセルポリシーに関するお知らせ<br>
					<?php echo nl2br(h($confirmation['clientCancelPolicy'])); ?>
				</dd>
			</dl>
		</div>

<?php
	echo $this->element('reservation_note');
?>

		<div class="inner">
<?php
	if ($paymentApi) { //決済APIがtrueのとき
		echo $this->Form->button('お支払いへ', array('class' => 'btn-type-primary', 'div' => false, 'id' => 'submitReserve', 'type' => 'button'));
		echo $this->Form->hidden('ReservationRedirectUrlEcon' ,array('value' => $paymentRedirectUrl));
	} else {
		echo $this->Form->button('上記の内容で予約する', array('class' => 'btn-type-primary', 'div' => false, 'id' => 'submitReserve', 'type' => 'button'));
	}
?>
		</div>
		<div class="inner mb20px">
			<?php echo $this->Html->link('戻る', '/reservations/step1/'.$this->data['Reservation']['uniqId'].'/'.($fromRentacarClient ? '?from_rentacar_client=true' : ''), array('class' => 'btn-type-sub')); ?>
		</div>
	</div><!-- js-content End -->

<?php echo $this->element('loading_indicator_earth'); ?>
<?php
	if (!$paymentApi) {
		echo $this->Form->hidden('session_token', array('value' => $econ_token));
		echo $this->Html->script($econ_jsf_url);
	} else {
		echo $this->Html->script('jquery-1.9.1.js');
	}
	echo $this->Html->script('input_confirm_econ.js');
?>

<?php
	$paymentApiFlgToJs = $paymentApi ? true : false ;
?>
</main>

<script>
var paymentApiFlg = "<?= $paymentApiFlgToJs ?>";

$(function() {
	// 二重送信防止
	$('form').on('submit', function(event){
		$('#submitReserve').attr('disabled', true);
	});
});
</script>

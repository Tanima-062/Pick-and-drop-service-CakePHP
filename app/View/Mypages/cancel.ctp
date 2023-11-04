<div id="renewal" class="login-edit login wrap contents clearfix mypages-cancel_page">
<?php
	echo $this->element('progress_bar');
?>
	<h2 class="h-type03">予約のキャンセル</h2>
	<div class="step-box">
		<p class="ttxt">
			下記の予約を取り消します。<br>
よろしければキャンセル理由を記入の上、「キャンセル内容の確認」ボタンを押してください。
		</p>
		<?php echo $this->Form->create('Cancel', array('url' => '/mypages/cancel_check/', 'name' => 'reserve', 'type' => 'post', 'inputDefaults' => $inputDefaults)); ?>
		<div class="box-outer">
			<h3 class="h-type04">予約状況の確認</h3>
			<div class="box-inner">
				<table class="pc-form-date center">
					<tr>
						<th>予約状況</th>
						<th>予約番号</th>
						<th>予約申し込み日</th>
					</tr>
					<tr>
						<td><?php echo $reservationStatus[$result['Reservation']['reservation_status_id']]; ?></td>
						<td><?php echo $result['Reservation']['reservation_key']; ?></td>
						<td>
						<?php
						$w = $week[date('w', strtotime($result['Reservation']['created']))];
						echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['created'])); ?>
						</td>
					</tr>
				</table>
			</div><!-- end .box-inner -->
		</div><!-- end .box-outer -->

		<div class="box-outer">
			<div class="box-inner">
				<div class="float-box clearfix">
					<table class="pc-form-date">
						<tr>
							<th class="cancel_th">事業者名</th>
							<td><?php echo $client['Client']['name']; ?></td>
						</tr>
						<tr>
							<th class="cancel_th">車両タイプ</th>
							<td><?php echo $result['CarClass']['name']; ?></td>
						</tr>
						<tr>
							<th class="cancel_th">予約期間</th>
							<td>
							<?php
							$w = $week[date('w', strtotime($result['Reservation']['rent_datetime']))];
							echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['rent_datetime'])); ?>
							～<br>
							<?php
							$w = $week[date('w', strtotime($result['Reservation']['return_datetime']))];
							echo date('Y年m月d日 ('.$w.') H:i', strtotime($result['Reservation']['return_datetime'])); ?>
							</td>
						</tr>
					</table>
				</div>
			</div><!-- end .box-inner -->
		</div><!-- end .box-outer -->


		<div class="box-outer rent-margin-bottom">
			<h3 class="h-type04">キャンセルポリシー</h3>
			<div class="frame">


<?php
	//事前決済済みかどうかで表示を切り替える
			if (!empty($isPaidInAdvance) && $isPaidInAdvance == true) {
?>
					ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
					<br>
					〈キャンセル料〉<br>
					<?php echo $result['CancelPolicy']; ?><br>
					・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
					・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。<br>
					<br>
					■キャンセルポリシーに関するお知らせ<br>
					<?php echo nl2br($result['Client']['cancel_policy']);?>
<?php
						} else {
?>
				ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
<br>
						<?php echo $result['CancelPolicy']; ?><br>
						・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
						<?php echo nl2br($result['Client']['cancel_policy']);?>
<?php
						}
?>

			</div>


			<h3 class="h-type04">キャンセルに関する注意事項</h3>
			<div class="frame">
				<?php
					//事前決済済みかどうかで表示を切り替える
							if (!empty($isPaidInAdvance) && $isPaidInAdvance == true) {
				?>
									<ul>
										<li>● ご入金後のキャンセル<br>
											このままキャンセル手続きをお願いします。<br>
											以下のキャンセル料等を頂戴し、差額をご返金いたします。<br>
											・レンタカー会社ごとのキャンセルポリシーに基づくキャンセル料</li>

										<li>● 航空便の欠航・運休等によるキャンセルの場合<br>
											天候不良による航空便の欠航・運休等、お客様の事由以外でキャンセルされる場合、レンタカー会社既定のキャンセル料が免除となる場合がございます。<br>
											ご返金手続きは弊社にて行いますので、航空会社発行の欠航・運休証明書を添付の上、<a href="mailto:rentacar@skyticket.com">メール（rentacar@skyticket.com）</a>にてご連絡ください。<br>
											※このまま予約確認ページよりキャンセル手続きをされますと、キャンセル料を頂戴してご返金となりますのでご注意ください。<br>
											</li>
									</ul>
				<?php
										} else {
				?>
				キャンセル料が発生する場合は、レンタカー会社より別途ご連絡いたします。<br>
キャンセル理由によっては、キャンセル料が免除される場合もございますので、詳細はご利用予定のレンタカー店へご確認ください。

				<?php
										}
				?>

			</div>



				<?php
					//事前決済済みかどうかで表示を切り替える
							if (!empty($isPaidInAdvance) && $isPaidInAdvance == true) {
				?>
				<h3 class="h-type04">ご返金につきまして</h3>
				<div class="frame">
					キャンセルのお手続き後、ご返金がある場合は以下のとおり返金の処理をいたします。<br>
<br>
● キャンセル手続き後、約1か月前後でご利用いただいたクレジットカードを通じてご返金いたします。<br>
● クレジットカード会社の締日により、返金情報がお客様のクレジットカード明細へ反映されるまでにお時間がかかることがございますのでご了承ください。（ご利用のクレジットカード会社によっては一度ご請求後、翌月以降に返金となる場合がございます。）<br>
● 弊社処理後の返金方法や返金のお日にちにつきましては、クレジットカード会社によって異なりますので弊社ではわかりかねます。ご利用のカード会社へお問い合わせください。<br>
					</div>
				<?php
										}
				?>






			<div class="box-inner">
				<?php echo $this->Form->hidden('Reservation.cancel_reason_id', array('value' => 1));?>

				<table class="pc-form-date center">

					<tr>
						<th class="cancel_th required">キャンセル理由詳細</th>
						<td>
						<?php echo $this->Session->flash('cancelError'); ?>
						<?php echo $this->Form->input('Reservation.cancel_remark',
								array(
									'cols' => 34,
									'required' => true,
									'maxlength' => 200,
									'placeholder' => '具体的なキャンセル理由をご入力ください。')); ?>
						<?php if (isset($validationMsg['cancel_remark'][0])) {
							echo '<br/><span style="color:red;">'.$validationMsg['cancel_remark'][0].'</span>';
						} ?>
						</td>
					</tr>
				</table>
			</div><!-- end .box-inner -->
		</div><!-- end .box-outer -->
		<p class="btn-submit">
			<?php echo $this->Form->submit('キャンセル内容の確認',
					 array('id' => 'cancel',
					 		'name' => 'cancelBtn',
					 		'class' => 'btn btn_submit rent-margin-bottom-important',
					 		'value' => 'cancel',
							'div' => false,)); ?>
			<?php echo $this->Form->hidden('Reservation.reservation_key', array('value' => h($result['Reservation']['reservation_key']))); ?>
			<?php echo $this->Form->hidden('Reservation.tel', array('value' => h($result['Reservation']['tel']))); ?>
			</p>
		<?php echo $this->Form->end(); ?>
	</div>
	<div class="rent-margin-bottom btn btn_cancel">
	<?php echo $this->Html->link('戻る', '/mypages/'); ?>
	</div>
</div>
<?php $this->Html->script(array('mypages_cancel.js'), array('inline' => false)); ?>

<style>
	/* 広告用　：completionページ上部にスペースが生まれる件 */
	img[height='1'][width='1'] {
		display: none;
	}
</style>
<div style="height: 0;overflow: hidden;visibility: hidden;">
	<!--afb成果URL-->
	<script>
	if (!window.afblpcvCvConf) {
	  window.afblpcvCvConf = [];
	}
	window.afblpcvCvConf.push({
	  siteId: "b1bfefa9",
	  commitData: {
		pid: "O9518A",
		u: "<?= $reservation['Reservation']['reservation_key']; ?>",
		  amount:"<?= $reservation['Commodity']['id']; ?>.1.<?= $reservation['Reservation']['amount']; ?>"
	  }
	});
	</script>
	<script src="https://t.afi-b.com/jslib/lpcv.js?cid=b1bfefa9&pid=O9518A" async="async"></script>
	<!--A8成果URL-->
	<span id="a8sales"></span>
	<script src="//statics.a8.net/a8sales/a8sales.js"></script>
	<script>
	a8sales({
		"pid": "s00000001783019",
		"order_number": "<?= $reservation['Reservation']['reservation_key']; ?>",
		"currency": "JPY",
		"items": [
			{
				"code": "a8",
				"price": <?= $reservation['Reservation']['amount']; ?>,
				"quantity": 1
			}, ],
			"total_price": <?= $reservation['Reservation']['amount']; ?>
	});
	</script>

	<!-- Skyscanner Analytics Tag -->
	<script>
	// This line imports the Skyscanner Tag code
	(function(s,k,y,t,a,g){s['SkyscannerAnalyticsTag']=t;s [t]=s[t]||function(){ (s[t].buffer=s[t].buffer||[]).push(arguments)};s[t].u=y;
	var l=k.createElement("script"); l.src=y+"/tag.js";l.async=1;var h=k.getElementsByTagName("head")[0];h.appendChild(l); })(window, document, 'https://analytics.skyscanner.net', 'sat');
	</script>
	<script>
	function fireSkyscannerTag() {
	<?php
		if(isset($_SESSION["advertising_cd"])){
			if($_SESSION["advertising_cd"] == "skyscanner_rc") {
	?>
		sat('init', 'SAT-560516-1');
		console.log('firing tag');
		sat('send', 'conversion', {
			bookingReference:"<?= $reservation['Reservation']['reservation_key']; ?>"
		});
	<?php
			}
		}
	?>
	}
	</script>
	<!-- /Skyscanner Analytics Tag -->

	<!--JANet成果URL-->
	<img src="https://ec.j-a-net.jp///<?= rawurlencode($reservation['Reservation']['reservation_key']); ?>/405204/<?= rawurlencode($reservation['Commodity']['id']); ?>/1/<?= rawurlencode($reservation['Reservation']['amount']); ?>" width="1" height="1">
	<!--SmartC成果URL-->
	<img src="https://ec.smart-c.jp///<?= $reservation['Reservation']['smartc_reservation_key']; ?>/31183000/<?= $reservation['Commodity']['id']; ?>/1/<?= $reservation['Reservation']['amount_without_tax']; ?>" width="1" height="1">
	<!--アクセストレード成果URL-->
	<img src="https://is.accesstrade.net/cgi-bin/isatV2/sky-rentacar/isatWeaselV2.cgi?result_id=101&verify=<?= $reservation['Reservation']['reservation_key']; ?>&value=<?= $reservation['Reservation']['amount_without_tax']; ?>" width="1" height="1">
	<script>
	var __atw = __atw || [];
	__atw.push({ "merchant" : "sky-rentacar", "param" : {
		"result_id" : "101",
		"verify" : "<?= $reservation['Reservation']['reservation_key']; ?>",
		"value" : "<?= $reservation['Reservation']['amount_without_tax']; ?>"
	}});
	(function(a){var b=a.createElement("script");b.src="https://h.accesstrade.net/js/nct/cv.min.js";b.async=!0;
	a=a.getElementsByTagName("script")[0];a.parentNode.insertBefore(b,a)})(document);
	</script>

	<!----- ValueCommerce iTAG ----->
	<div
	data-vc-ec-id="2333182"
	data-vc-sales-number="1"
	data-vc-sales-amount="<?= $reservation['Reservation']['amount']; ?>"
	data-vc-order-id="<?= $reservation['Reservation']['reservation_key']; ?>"
	data-vc-prgrid="rentacar"
	>
	</div>
	<script type="text/javascript" src="//cv.valuecommerce.com/vccv.min.js"></script>
	<noscript>
	<img src="https://itrack2.valuecommerce.ne.jp/cgi-bin/2333182/vc_itag.cgi?type=img
	&sales_number=1
	&sales_amount=<?= $reservation['Reservation']['amount']; ?>
	&order_id=<?= $reservation['Reservation']['reservation_key']; ?>
	&prgrid=rentacar"
	width="1" height="1">
	</noscript>

	<div
	data-vc-ec-id="3380060"
	data-vc-sales-number="1"
	data-vc-sales-amount="<?= $reservation['Reservation']['amount']; ?>"
	data-vc-order-id="<?= $reservation['Reservation']['reservation_key']; ?>"
	>
	</div>
	<script type="text/javascript" src="//cv.valuecommerce.com/vccv.min.js"></script>
	<noscript>
	<img src="https://itrack2.valuecommerce.ne.jp/cgi-bin/3380060/vc_itag.cgi?type=img
	&sales_number=1
	&sales_amount=<?= $reservation['Reservation']['amount']; ?>
	&order_id=<?= $reservation['Reservation']['reservation_key']; ?>"
	width="1" height="1">
	</noscript>
	<!----- /ValueCommerce iTAG ---->
	<!--エムフロ成果URL-->
	<img src="https://admin.mtrf.net/ac/action.php?cid=AD000144&uid=<?= $reservation['Reservation']['reservation_key']; ?>&pid=3&amount=<?= $reservation['Reservation']['amount']; ?>" height="1" width="1">
</div>

	<!-- AppsFlyer CVタグ -->
<?php
	if( USER_TERMINAL_SOFT>=TERMINAL_SOFT_APP ){
		$format_rent_date = substr($reservation['Reservation']['rent_datetime'], 0, 10);
		$format_return_date = substr($reservation['Reservation']['return_datetime'], 0, 10);
?>
	<script type="text/javascript">
		function trackEvent(){
			var eventName = "af_rentacar_booking"
			var eventParams = "{\"af_content_type\":\"rentacar\",\"af_revenue\":\"<?= $reservation['Reservation']['amount']; ?>\",\"af_currency\":\"JPY\",\"af_date_a\":\"<?=$format_rent_date;?>\",\"af_date_b\":\"<?=$format_return_date;?>\",\"af_receipt_id\":\"<?=$reservation['Reservation']['reservation_key']; ?>\"}";
			var iframe = document.createElement("IFRAME");
			iframe.setAttribute("src", "af-event://inappevent?eventName="+eventName+"&eventValue="+eventParams);
			document.documentElement.appendChild(iframe);
			iframe.parentNode.removeChild(iframe);
			iframe = null;
		}
		trackEvent();
	</script>
	<!-- FireBase CVタグ -->
	<script>
		function sendEvent() {
			var name = "rentacar_cv";
			var params = {
				"transaction_id": "<?=$reservation['Reservation']['reservation_key']; ?>",
				"value": <?= $reservation['Reservation']['profit']; ?>,
				"currency": "JPY"
			};
			logEvent(name,params);
		}
		sendEvent();

		function logEvent(name, params) {
			if (!name) {
				return;
			}

			if (window.AnalyticsWebInterface) {
				// Call Android interface
				window.AnalyticsWebInterface.logEvent(name, JSON.stringify(params));
			} else if (window.webkit
				&& window.webkit.messageHandlers
				&& window.webkit.messageHandlers.firebase) {
				// Call iOS interface
				var message = {
				command: 'logEvent',
				name: name,
				parameters: params
				};
				window.webkit.messageHandlers.firebase.postMessage(message);
			} else {
				// No Android or iOS interface found
				console.log("No native APIs found.");
			}
		}

		function setUserProperty(name, value) {
		if (!name || !value) {
			return;
		}

		if (window.AnalyticsWebInterface) {
			// Call Android interface
			window.AnalyticsWebInterface.setUserProperty(name, value);
		} else if (window.webkit
			&& window.webkit.messageHandlers
			&& window.webkit.messageHandlers.firebase) {
			// Call iOS interface
			var message = {
			command: 'setUserProperty',
			name: name,
			value: value
		};
			window.webkit.messageHandlers.firebase.postMessage(message);
		} else {
			// No Android or iOS interface found
			console.log("No native APIs found.");
		}
		}
	</script>
<?php
	}
?>
<!-- // AppsFlyer CVタグ -->


<main class="completion-contents">
	<!-- コンテンツ -->
	<div id="js-content">
		<!-- 予約番号 -->
		<section class="reserve_completed">
			<!-- 予約ステップ -->
			<?php echo $this->element('sp_reservation_steps'); ?>
			<div class="completion_contents_body">
				<h3 class="color_blue mb10px">予約を受け付けました<br>予約番号を必ずお控えください</h3>
				<div class="reserve_completed_num-wrap">
					<p>お客様の予約番号は下記になります</p>
				 	<div class="reserve_completed_num">
				 		<p class="color_red"><?php echo $reservation['Reservation']['reservation_key']; ?></p>
				 	</div>
				</div>

				<?php // セルフチェックイン案内
					echo $this->element('sp_note_self-checkin', [
						'planname' => $reservation['Commodity']['name'], 
						'clientid' => $reservation['Client']['id']
					]);
				?>

				<div class="mb20px">
					<?= $this->Html->link('予約内容を確認する', '/mypages/login/?hash='.$reservation['Reservation']['reservation_hash'], array('class' => 'btn-type-primary')); ?>
				</div>
			</div>
		</section>
		<!-- /予約番号 -->

		<div class="registration_notice">
			会員登録すると次回から入力の手間が省けます。<br>
			<a href="/user/user_edit.php">会員登録はこちら</a>
		</div>

		<section class="inquiry" id="">
			<h3 class="plan_form_title">お問い合わせ窓口</h3>
			<section class="inner">
				<div class="inquiry_body">
					<div class="clearfix mb20px">
						<div class="inquiry_body_photo fl">
<?php
	if (!empty($reservation['Client']['sp_logo_image'])) {
		echo $this->Html->image('logo/square/'.$reservation['Client']['id'].'/'.$reservation['Client']['sp_logo_image'], array(
				'alt' => $reservation['Client']['name'],
				'class' => 'contents_result_list_body_detail_img st-table_cell va-middle'
		));
	}
?>
						</div>
						<div class="inquiry_body_text fl">
							<ul>
								<li>受取店舗</li>
								<li><?= $reservation['Client']['name']; ?><br><?= $reservation['RentOffice']['name']; ?></li>
								<li><p class="font_b color_red"><?= $reservation['RentOffice']['tel']; ?></p></li>
								<li>
									営業時間 <?php echo date('H:i', strtotime($reservation['RentOffice']['office_hours_from'])); ?>〜<?= date('H:i', strtotime($reservation['RentOffice']['office_hours_to'])); ?>
									<?php if(!empty($reservation['RentOffice']['start_day']) && !empty($reservation['RentOffice']['end_day'])): ?>
										<p class="note_irregular-business-hours"><i class="icm-info-button-fill" aria-hidden="true"></i> <?php echo date('Y/m/d', strtotime($reservation['RentOffice']['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($reservation['RentOffice']['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。</p>
									<?php endif; ?>
								</li>
							</ul>
							<ul>
								<li>返却店舗</li>
								<li><?= $reservation['Client']['name']; ?><br><?= $reservation['ReturnOffice']['name']; ?></li>
								<li><p class="font_b color_red"><?= $reservation['ReturnOffice']['tel']; ?></p></li>
								<li>
									営業時間 <?php echo date('H:i', strtotime($reservation['ReturnOffice']['office_hours_from'])); ?>〜<?= date('H:i', strtotime($reservation['ReturnOffice']['office_hours_to'])); ?>
									<?php if(!empty($reservation['ReturnOffice']['start_day']) && !empty($reservation['ReturnOffice']['end_day'])): ?>
										<p class="note_irregular-business-hours"><i class="icm-info-button-fill" aria-hidden="true"></i> <?php echo date('Y/m/d', strtotime($reservation['ReturnOffice']['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($reservation['ReturnOffice']['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。</p>
									<?php endif; ?>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="caution_gray">
					<i class="icm-info-button-fill"></i>
					<p>お問い合わせの際は「スカイチケットで予約した」と伝えるとスムーズです</p>
				</div>
				<div class="maildomain_reminder">
					<dl>
						<dt>携帯メールアドレスをご利用のお客様へ</dt>
						<dd>
							<i class="icm-mail"></i>
							<div class="-text">
								あらかじめ<span class="color_red">@<?= MAIL_DOMAIN; ?></span>からのメールを受信できるように設定をお願いします。
							</div>
						</dd>
					</dl>
				</div>
			</section>
			<section class="mail_error">
				<h4 class="title_blue">メールが届かないとき</h4>
				<div class="inner">
					<p class="text">予約確認メールが受信できない場合は、以下の手順でご確認ください。</p>
					<ol>
						<li>予約の照会・変更・取消にログインして、登録したメールアドレスに間違いがないかご確認ください。登録メールアドレスの変更が行えます。</li>
						<li>迷惑メールフォルダを確認してください。</li>
						<li>携帯メールアドレスを入力して受信できていない場合は、<span>「@<?= MAIL_DOMAIN; ?>」</span>のドメインから受信ができるように設定をお願いします。</li>
						<li>予約完了メールの再送は下記メールアドレスへお問い合わせください。</li>
					</ol>
					<p class="text"><?= $this->Html->link(EMAIL_ADDRESS_RENTACAR, 'mailto:' . EMAIL_ADDRESS_RENTACAR . '?subject=' . urlencode('予約完了メール再送のお願い')); ?></p>
				</div>
				<div class="inner mb20px">
					<?= $this->Html->link('予約内容を確認する', '/mypages/login/?hash='.$reservation['Reservation']['reservation_hash'], array('class' => 'btn-type-primary')); ?>
				</div>
			</section>
		</section>
	</div><!-- js-content End -->
</main>

<?php 
	if (!empty($reservation['Reservation']['travelko_code'])) { 
?>
	<script>
		(function(w, d, s, t){
			w['TravelkoTag']=t; w[t]=w[t]||function(){(w[t].buffer=w[t].buffer||[]).push(arguments)};
			var f = d.getElementsByTagName(s)[0], j = d.createElement(s);
			j.async = true; j.src="https://www.tour.ne.jp/element/tracking/script_conversion.min.js"; var h=d.getElementsByTagName("head")[0];
			h.appendChild(j);
		})(window, document, 'script', 'TTT');

		TTT('send', 'request', {
			menu_code: "<?= Constant::TRAVELKO_MENU_CODE; ?>",
			agent_code: "<?= Constant::TRAVELKO_AGENT_CODE; ?>",
			tracking_code: "<?= $reservation['Reservation']['travelko_code']; ?>", //1トラッキングコード
			affiliate_code: "travelko_rc",
			reservation_id: "<?= $reservation['Reservation']['reservation_key']; ?>", //申込番号
			currency_code: "JPY",
			total_price: "<?= number_format($reservation['Reservation']['amount']); ?>", //申込金額
			profit_price: "<?= number_format($reservation['Reservation']['amount']); ?>", //申込金額
			rental_date: "<?= date('Y-m-d', strtotime($reservation['Reservation']['rent_datetime'])); ?>", //貸出日
			rental_return_date: "<?= date('Y-m-d', strtotime($reservation['Reservation']['return_datetime'])); ?>", //返却日
			dept_pref_code: "<?= $reservation['RentOffice']['pref_link_cd']; ?>", //出発都道府県コード
			dest_pref_code: "<?= $reservation['ReturnOffice']['pref_link_cd']; ?>" //到着都道府県コード
		});
	</script>
<?php 
	} 
?>

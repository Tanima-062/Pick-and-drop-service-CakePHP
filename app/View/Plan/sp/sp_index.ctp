<?php
	echo $this->Html->script(['sp/jquery-ui.min', 'plan', '/js/modal_float'], ['inline' => false, 'defer' => true]);
	echo $this->Html->css(['sp/jquery-ui'], null, ['inline' => false]);
?>

<!-- コンテンツ -->
<div id="js-content">
<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
?>
	<?php echo $this->element('sp_plan_view'); ?>

	<?php
		echo $this->Form->create('Reservation', array(
			'type' => 'post',
			'id' => 'ReservationPlanForm',
			'url' => '/reservations/step1/',
			'inputDefaults' => array(
				'div' => false,
				'label' => false,
				'legend' => false,
			),
		));
	?>

		<!--/プラン詳細表示-->
		<?php echo $this->element('sp_modal-plan'); ?>

		<section class="plan_form" id="plan_form">
			<section>
				<h4>貸出期間</h4>
				<div class="inner">
					<?php echo date('Y年m月d日 H時i分', strtotime($requestData['from']));; ?> ～ <?php echo date('Y年m月d日 H時i分', strtotime($requestData['to'])); ?>
					<?php echo ($expiredFlg) ? '<div style="color:red;">貸出期間が過去の日付です。</div>' : ''; ?>
				</div>
			</section>

			<section>
				<h4>車両の受取・返却</h4>
<?php
	$rentalOfficeClass = "";
	$fromOfficeTitle = "";
	if($this->request->data['from_office'] == $this->request->data['return_office']) {
		$rentalOfficeClass = "same_store";
		$fromOfficeTitle = "受取/返却営業所";
	}
?>
				<div id="fromReturnOffice" class="inner <?php echo $rentalOfficeClass; ?>">
					<div id="fromOffice">
						<table class="fromOffice">
							<tr>
								<th>
									<p class="fromOffice_title">受取営業所</p>
									<p class="fromOfficeTitle"><?php echo $fromOfficeTitle; ?></p>
								</th>
							</tr>
							<tr>
								<td class="select_type_line1 transparent">
									<div id="officeStock" class="select-form">
										<div class="field-wrap">
											<?php
												echo $this->Form->input('from_office', array(
													'type' => 'select',
													'class' => 'field',
													'options' => !empty($fromOfficeOptionList) ? $fromOfficeOptionList : $fromOfficeList,
													'default' => $this->request->data['from_office']
												));
											?>
											<i class="icm-right-arrow"></i>
										</div>
									</div>
								</td>
							</tr>
						</table>
						<div>
							<dl>
								<dd>
<?php
	foreach ($officeDatas as $key => $officeData) {
?>
									<div id="fromOfficeId<?php echo $officeData['id']; ?>" class="from-office office-data">
										<p>
											営業時間<span><?php echo date('H:i', strtotime($officeData['office_hours_from'])); ?>～<?php echo date('H:i', strtotime($officeData['office_hours_to'])); ?></span>
										</p>
<?php 
		if(!empty($officeData['start_day']) && !empty($officeData['end_day'])){ 
?>
										<p class="note_irregular-business-hours">
											<i class="icm-info-button-fill" aria-hidden="true"></i>
											<?php echo date('Y/m/d', strtotime($officeData['start_day'])); ?>
											～
											<?php echo date('Y/m/d', strtotime($officeData['end_day'])); ?> 
											は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
										</p>
<?php 
		}
?>
										<p>
											アクセス<span><?php echo $officeData['access_dynamic']; ?></span>
										</p>
									</div>
<?php
	}
?>
								</dd>
							</dl>
						</div>
					</div><!-- /fromOffice -->

					<hr class="plan_hr">
					
					<div id="returnOfficeBox">
						<table class="returnOffice">
							<tr>
								<th>
									<p class="returnOffice_title">返却営業所</p>
								</th>
							</tr>
							<tr>
								<td class="select_type_line1 transparent">
									<div class="select-form">
										<div class="field-wrap">
											<?php
												echo $this->Form->input('return_office', array(
													'type' => 'select',
													'class' => 'field',
													'options' => $returnOfficeList,
													'default' => $this->request->data['return_office']
												));
											?>
											<i class="icm-right-arrow"></i>
										</div>
									</div>
								</td>
							</tr>
						</table>
						<div>
							<dl>
								<dd>
<?php
	foreach ($returnOfficeDatas as $key => $returnOfficeData) {
?>
									<div id="returnOfficeId<?php echo $returnOfficeData['id']; ?>" class="return-office office-data">
										<p>
											営業時間<span><?php echo date('H:i', strtotime($returnOfficeData['office_hours_from'])); ?>～<?php echo date('H:i', strtotime($returnOfficeData['office_hours_to'])); ?></span>
										</p>
<?php 
		if(!empty($returnOfficeData['start_day']) && !empty($returnOfficeData['end_day'])){
?>
										<p class="note_irregular-business-hours">
											<i class="icm-info-button-fill" aria-hidden="true"></i>
											<?php echo date('Y/m/d', strtotime($returnOfficeData['start_day'])); ?>～<?php echo date('Y/m/d', strtotime($returnOfficeData['end_day'])); ?> は営業時間が通常と異なります。詳細は店舗へお問い合わせください。
										</p>
<?php 
		} 
?>
										<p>
											アクセス<span><?php echo $returnOfficeData['access_dynamic']; ?></span>
										</p>
									</div>
<?php
	}
?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</section>

			<section class="select_option">
				<h4>利用人数<span class="label-require">必須</span></h4>
				<div class="inner">
					<table class="colspan2 people_num">
						<tr>
							<td>
								<span>大人（12歳以上）</span>
							</td>
							<td>
								<div class="select-form">
									<div class="field-wrap">
										<?php
											echo $this->Form->input('adults', array(
												'type' => 'select',
												'class' => 'field',
												'options' => $adultPassengers,
												'default' => $requestData['adults'],
											));
										?>
										<i class="icm-right-arrow"></i>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<span>子供（6〜11歳）</span>
							</td>
							<td>
								<div class="select-form">
									<div class="field-wrap">
										<?php
											echo $this->Form->input('children', array(
												'type' => 'select',
												'class' => 'field',
												'options' => $passengers,
												'default' => $requestData['children'],
											));
										?>
										<i class="icm-right-arrow"></i>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<span>幼児（6歳未満）</span>
							</td>
							<td>
								<div class="select-form">
									<div class="field-wrap">
										<?php
											echo $this->Form->input('infants', array(
												'type' => 'select',
												'class' => 'field',
												'options' => $passengers,
												'default' => $requestData['infants'],
											));
										?>
										<i class="icm-right-arrow"></i>
									</div>
								</div>
							</td>
						</tr>
					</table>
					<div class="caution_blue">
						<i class="icm-warning"></i>
						<p>利用人数は選択頂いた車両クラスの乗車定員をご確認の上、ご登録ください。</p>
					</div>
				</div>
			</section>

			<section class="select_option">
				<h4>オプション</h4>
				<div id="sheet-option" class="inner">
					<div id="js-sheet-note" class="caution_blue" style="display: none;">
							<i class="icm-warning"></i>
							<p>6歳未満の幼児を同乗させる場合、チャイルドシートの使用が法令により義務付けられています。</p>
					</div>
					<table>
<?php
	$privilege_option_flg_zero_cnt = 0; // 下のリクエスト表示・非表示のためのカウント用
	foreach ($commodityPrivilegeData as $key => $commodityPrivilege) {
		if ($commodityPrivilege['Privilege']['option_flg'] == 1) {
?>
						<tr>
							<th><?php echo $commodityPrivilege['Privilege']['name']; ?></th>
							<td>
								<span class="font-size-large" id="option-price<?php echo $commodityPrivilege['Privilege']['id']; ?>">0円</span>
							</td>
							<td>
								<div class="select-form">
									<div class="field-wrap">
										<?php
											echo $this->Form->input('sheet.'.$commodityPrivilege['Privilege']['id'], array(
												'type' => 'select',
												'class' => 'field',
												'options' => $sheetOptions[$commodityPrivilege['Privilege']['id']],
												'empty' => '---',
												'data-id' => $commodityPrivilege['Privilege']['id'],
												'data-price' => $commodityPrivilege[0]['Sum'],
												'max' => $commodityPrivilege['Privilege']['maximum'],
											));
										?>
										<i class="icm-right-arrow"></i>
									</div>
								</div>
							</td>
						</tr>
<?php
		} elseif ($commodityPrivilege['Privilege']['option_flg'] == 0) {
			$privilege_option_flg_zero_cnt++;
		}
	}
?>
					</table>
				</div>
<?php
	if(!empty($privilege_option_flg_zero_cnt)) {
?>
				<div id="privilege-option" class="inner">
					<table>
<?php
		foreach ($commodityPrivilegeData as $key => $commodityPrivilege) {
			if ($commodityPrivilege['Privilege']['option_flg'] == 0) {
?>
						<tr>
							<th><?php echo $commodityPrivilege['Privilege']['name']; ?></th>
							<td>
								<span class="font-size-large" id="option-price<?php echo $commodityPrivilege['Privilege']['id']; ?>">0円</span>
							</td>
							<td>
								<div class="select-form">
									<div class="field-wrap">
										<?php
											echo $this->Form->input('privilege.'.$commodityPrivilege['Privilege']['id'], array(
												'type' => 'select',
												'class' => 'field',
												'options' => $privilegeOptions[$commodityPrivilege['Privilege']['id']],
												'empty' => '---',
												'data-id' => $commodityPrivilege['Privilege']['id'],
												'data-price' => $commodityPrivilege[0]['Sum'],
												'max' => $commodityPrivilege['Privilege']['maximum'],
											));
										?>
										<i class="icm-right-arrow"></i>
									</div>
								</div>
							</td>
						</tr>
<?php
			}
		}
?>
					</table>
				</div>
<?php
	}
?>
			</section>
		</section><!-- /plan_form -->

		<section class="plan_comfirmation">
			<section>
				<h3 class="plan_form_title">料金の確認</h3>
				<ul class="plan_price_list">
					<li>
						<div class="plan_price_list_th">ご利用日数</div>
						<div class="plan_price_list_td"><?php echo $rentalPeriod; ?></div>
					</li>
					<li>
						<div class="plan_price_list_th">基本料金</div>
						<div class="plan_price_list_td"><?php echo number_format($basicCharge); ?>円</div>
						<div class="opt_dbox_wrap">
							<span class="opt_price_title">スカイチケット限定コミコミ価格</span><br />
							レンタカー基本料金&nbsp;<i class="fa fa-plus-circle"></i>&nbsp;免責補償&nbsp;<i class="fa fa-plus-circle"></i>&nbsp;消費税
						</div>
					</li>
				</ul>
				<ul id="other-price" class="plan_price_list">
					<li id="drop">
						<div class="plan_price_list_th">乗り捨て料金</div>
						<div class="price plan_price_list_td no-padding">0円</div>
					</li>
					<li id="nightfee">
						<div class="plan_price_list_th">深夜手数料</div>
						<div class="price plan_price_list_td no-padding">0円</div>
					</li>
<?php
	foreach ($commodityPrivilegeData as $key => $commodityPrivilege) {
?>
					<li id="privilege<?php echo $commodityPrivilege['Privilege']['id']; ?>">
						<div class="plan_price_list_th"><?php echo $commodityPrivilege['Privilege']['name']; ?></div>
						<div class="price plan_price_list_td no-padding">0円</div>
					</li>
<?php
	}
?>
				</ul>
				<div class="plan_info_total_price price_block">
					<div class="plan_info_left">
						<p class="price_block_title">合計料金<span>（税込）</span></p>
						<p class="caption_gray"><?php echo $rentalPeriod; ?>料金</p>
					</div>
					<div class="plan_info_right">
						<p id="total-place" class="price_block_price"><span>0円</span></p>
					</div>
				</div>
			</section>
		</section><!-- /plan_comfirmation -->

		<section class="plan_comfirmation">
			<section>
				<h3 class="plan_form_title">お支払い方法</h3>
<?php
	switch($commodityInfo['Commodity']['payment_method']){
		case 0:
?>
				<div class="inner">
					<?php echo $this->element('sp_note_payment_onsite'); // 現地決済の注意事項 ?>
				</div>
<?php
			echo $this->Form->hidden('payment_method', array('value' => 0));
			break;

		case 1:
?>
				<div class="inner">
					クレジットカードでの事前決済<?php echo $econMaintenance ? '（メンテナンス中）' : ''; ?>
				</div>
				<?php echo $this->element('modal_about-card'); // デビット・プリペイドカードをご使用時の注意事項 ?>
<?php
			echo $this->Form->hidden('payment_method', array('value' => 1));
			break;

		case 2:
			$defaultMethod = $econMaintenance ? 0 : $defaultPaymentMethod;
			$commonOptions = array('id'=>'paymentMethod','hiddenField'=>false, 'label'=>false, 'default' => $defaultMethod, 'class' => 'js-radio-payment');
			$creditOptions = $econMaintenance ? array_merge($commonOptions, array('disabled')) : $commonOptions;
?>
				<div class="radio-form -btn">
					<?= $this->Form->radio('payment_method', array('0' => ''), $commonOptions); ?>
					<label for="paymentMethod0" class="label"><p>現地で決済</p></label>
				</div>
				<div class="radio-form -btn">
					<?= $this->Form->radio('payment_method', array('1' => ''), $creditOptions); ?>
					<label for="paymentMethod1" class="label"><p>クレジットカードで事前決済</p><?php echo $econMaintenance ? '（メンテナンス中）' : ''; ?></label>
				</div>

				<div id="js_select_onsite">
					<div class="inner">
						<?php echo $this->element('sp_note_payment_onsite'); // 現地決済の注意事項 ?>
					</div>
				</div><!-- /notes_onsite_wrap -->

				<div id="js_select_credit" class="notes_credit_wrap">
					<?php echo $this->element('modal_about-card'); // デビット・プリペイドカードをご使用時の注意事項 ?>
				</div>
<?php
			break;

		default:
			break;
	}
?>

<?php
	if (!$expiredFlg && !($econMaintenance && $commodityInfo['Commodity']['payment_method'] == 1)){
?>
				<div class="inner">
					<div>
<?php
		if(!empty($cancelFreeMessage) && !$isExpiredCancelLimit) {
?>
						<p class="notes_nobg">
							<span>
								<i class="icm-clock"></i> <em>突然の予定変更でも安心。</em>
								<span class="notes_aside"><?php echo $cancelFreeMessage; ?></span>
							</span>
							<input type="hidden" id="cancelFreeLimit" value="<?= $cancelFreeLimit; ?>"></input>
						</p>
<?php
		}
?>
						<?php echo $this->Form->submit('次へ進む', array('id' => 'btn_submit', 'class' => 'btn-type-primary')); ?>

						<p class="plan_warning">人気の車種はすぐに予約が埋まる可能性があります。<br />お早めに予約ください。</p>
					</div>
				</div>
<?php
	}
?>
			</section>
		</section><!-- /plan_comfirmation -->

		<?php
			echo $this->Form->hidden('uniqId', array('value' => $sessionUniqId));
			echo $this->Form->hidden('basicPrice', array('value' => $basicCharge));
			echo $this->Form->hidden('from', array('value' => $requestData['from']));
			echo $this->Form->hidden('to', array('value' => $requestData['to']));
			echo $this->Form->hidden('carClassId', array('value' => $commodityInfo['CarClass']['id']));
			echo $this->Form->hidden('commodityItemId', array('value' => $commodityInfo['CommodityItem']['id']));
			echo $this->Form->hidden('commodityId', array('value' => $commodityInfo['Commodity']['id']));
			echo $this->Form->hidden('clientId', array('value' => $commodityInfo['Client']['id']));
			echo $this->Form->hidden('estimationTotalPrice', array('value' => '0'));
			echo $this->Form->hidden('dayTimeFlg', array('value' => $commodityInfo['Commodity']['day_time_flg']));
			echo $this->Form->hidden('submitFlg', array('value' => 1));
			if ($fromRentacarClient) {
				echo $this->Form->hidden('from_rentacar_client', array('value' => 'true'));
			}
		?>
	<?php
		echo $this->Form->end();
	?>

<?php 
	if (!empty($backSearch)) { 
?>
	<div class="inner backsearch-btn-wrap">
		<?php echo $this->Html->link('<span>検索結果一覧に戻る</span>', '/searches' . $backSearch, array('class' => 'btn-type-sub','escape'=>false)); ?>
	</div>
<?php
	} 
?>
</div><!-- js-content End -->

<div id="notifier" class="popup_body popup_plan">
	<div class="popup_wrap">
		<div class="popup_cell">
			<img src="/rentacar/img/icon_speaker.png" width="20" height="auto" />
		</div>
		<div class="popup_cell">
			<p class="popup_main_message">
				<span id="js_booking_text">当プランは今日<span id="js_booking_num"></span>件予約されました</span>
				<span id="js_visitor_text" class="displayn">現在<span id="js_visitor_num"></span>名が当プランを検討中です</span>
			</p>
			<span class="popup_notes">人気のプランは売り切れる可能性があります</span>
		</div>
		<div class="popup_cell">
			<i id="notifierClose" class="fa fa-close" aria-hidden="true"></i>
		</div>
	</div>
</div>

<script>
$(function(){
	// 使ってなさそう
	// プランの詳細を開いておく
	// if( !$(".plan_detail .plan_detail_list").is(':visible') ){
	// 	$(".plan_detail .trigger").first().click();
	// }

	// 使ってなさそう
	// $(".notes_slide_p").delay(300).slideDown();
<?php
	/* 車両イメージ削除
	var swiper = $('.swiper-container').swiper({
		nextButton: '.swiper-button-next',
		prevButton: '.swiper-button-prev',
		parallax: true,
		// loop: true,
	}); */
?>
	// ページ内リンク
	$("a[href^=#]").on("click", function() {
		var href= $(this).attr("href");
		var target = $(href == "#" || href == "" ? "html" : href);
		var position = target.offset().top;
		$("html, body").animate({scrollTop:position}, 400);
		return false;
	});

	$(".js-radio-payment").on("change", function(){
		if( $("#paymentMethod0").prop("checked") ){
			$("#js_select_onsite").show();
			$("#js_select_credit").hide();
		}else{
			$("#js_select_onsite").hide();
			$("#js_select_credit").show();
		}
	});

	// ポップアップ
<?php
	$booking_num = mt_rand(0, 5);
	$visitor_num = mt_rand(1, 5);
?>
	if(<?=$booking_num;?> > 0) {
		$("#js_booking_num").text( <?=$booking_num;?> );
		$("#js_visitor_num").text( <?=$visitor_num;?> );
		$("#js_visitor_text").hide();

		var promise = showNotifier()
		.then( switchShow )
		.then( hideNotifier );

		$("#notifierClose").on("click", function() {
			$("#notifier").removeClass("fadeInBottom");
		});
	}
	function showNotifier(){
		var d = new $.Deferred;
		$("#notifier").css({"display":"block"});
		setTimeout(function(){
			$("#notifier").addClass("fadeInBottom");
			d.resolve();
		}, 1000);
		return d.promise();
	}
	function switchShow(){
		var d = new $.Deferred;
		setTimeout(function(){
			$("#js_booking_text").slideUp();
			$("#notifier").delay(500).queue(function(){
				$("#js_visitor_text").slideDown();
			});
			d.resolve();
		}, 5000);
		return d.promise();
	}
	function hideNotifier(){
		var d = new $.Deferred;
		setTimeout(function(){
			$("#notifier").removeClass("fadeInBottom");
			d.resolve();
		}, 5000);
		return d.promise();
	}
	function getVisitorNum(min, max) {
		return Math.floor( Math.random() * (max - min + 1) ) + min;
	}

	if($('#ReservationFromOffice').children().length === 1){
		$('#ReservationFromOffice').addClass('-one');
		$('#ReservationFromOffice').next('.icm-right-arrow').hide();
		$('.fromOffice_title').text('受取営業所');
		$('#fromOffice').addClass('fromOffice-no_choice');
	}else{
		$('.fromOffice_title').text('受取営業所(選択可)');
	}

	if($('#ReservationReturnOffice').children().length === 1){
		$('#ReservationReturnOffice').addClass('-one');
		$('#ReservationReturnOffice').next('.icm-right-arrow').hide();
		$('.returnOffice_title').text('返却営業所');
		$('#returnOfficeBox').addClass('returnOffice-no_choice');
	}else{
		$('.returnOffice_title').text('返却営業所(選択可)');
	}

	if($('#fromReturnOffice').hasClass('same_store') && $('#fromOffice').hasClass('fromOffice-no_choice') && $('#returnOfficeBox').hasClass('returnOffice-no_choice')) {
		$('.fromOffice_title').text('受取/返却営業所');
		$('#returnOfficeBox').hide();
		$('.plan_hr').hide();
	}

	$(".js-radio-payment").trigger("change");
});
</script>

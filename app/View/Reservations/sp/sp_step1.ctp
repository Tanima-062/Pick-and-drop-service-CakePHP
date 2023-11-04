<?php
	echo $this->Html->script(['/js/modal_float'], ['inline' => false, 'defer' => true]);
?>
<main class="step1-contents">

	<!-- コンテンツ -->
	<div id="js-content">
		<?php
			echo "<div id='sessionMessage'>";
			if (!empty($sessionMessage)) {
				echo $this->element('session_message');
			}
			echo "</div>";
		?>

		<?php echo $this->element('sp_reservation_steps'); ?>
		
		<?php echo $this->element('sp_reservation_timer'); ?>

		<?php echo $this->element('sp_plan_view'); ?>

<?php
	// 未ログインの場合
	if (!$login_flg) {
?>
		<div class="login-form">
<?php
		if ($login_error_flg){
?>
			<div id="js-login_error" class="login_error-wrap">
				<p class="login_error"><?=__('ログインに失敗しました。');?><br /><?=__('メールアドレスまたはパスワードが登録情報と異なっています。');?></p>
			</div><!-- error End -->
			<ul class="aside_login_error">
				<li><a href="/user/remind.php" target="_blank"><?=__('パスワードを忘れた場合');?></a></li>
				<li class="st-margin-small-top"><a href="javascript:void(0);" id="btn_scroll_form"><?=__('ログインせずに予約する場合');?></a></li>
			</ul>
<?php
		} else {
?>
			<div class="login-form close">
				<input type="button" name="" id="show_login_button" class="btn-type-link is-login" value="ログインして予約する方はこちら" />
			</div><!-- form End -->
<?php
		}
?>
			<div id="login_area" class="login-form">
<?php
		echo $this->Form->create('Login', array(
			'type' => 'post',
			'url' => HTTPS_ROOT_URL . 'rentacar/reservations/step1/',
			'inputDefaults' => array(
				'div' => false,
				'label' => false,
				'legend' => false,
			),
		));
?>
				<div class="form">
					<div class="input-form">
						<label class="label">登録メールアドレス（会員ID用）<span class="note">※半角英数字</span><span class="required"></span></label>
						<input id="loginMail" class="field" type="email" name="email" value="" placeholder="登録メールアドレス（会員ID用）">
						<p id="js-err_mail"></p>
					</div>
					<div class="input-form">
						<label class="label">「パスワード」 を入力してください<span class="note">※半角英数字</span><span class="required"></span></label>
						<div class="field-wrap icon-right">
							<input id="loginPassWord" class="field" type="password" name="password" value="" placeholder="パスワード">
							<i id="js-toggle_pass" class="-icon icm-eye-blocked"></i>
						</div>
						<p id="js-err_pass"></p>
					</div>
					<div class="checkbox-form -default">
						<input type="checkbox" id="ckbRememberLogin" name="ckbRememberLogin" value="on" >
						<label for="ckbRememberLogin" class="label">
							<?=__('次回から自動的にログイン');?>
						</label>
					</div>
					<div class="login-form">
						<input type="submit" id="js-btnLogin" value="ログイン" class="btn-type-secondary" />
						<input type="hidden" name="passenger_form_chk" value="1">
					</div>
					<a class="reset-password" href="/user/remind.php" target="_blank">パスワードを忘れた場合</a>
				</div><!-- form End -->
<?php
		echo $this->Form->hidden('uniqId', array('value' => $this->data['Reservation']['uniqId']));
		if ($fromRentacarClient) {
			echo $this->Form->hidden('from_rentacar_client', array('value' => 'true'));
		}
		echo $this->Form->end();
?>
			</div><!-- login_area End -->
		</div><!--End login-form-->
<?php
	}
?>

<?php
	$url = 'completion/';
	if ($paymentMethod == 1) {
		$url = 'step2/';
	}
	echo $this->Form->create('Reservation', array(
		'id' => 'application_form',
		'type' => 'post',
		'url' => $url,
		'inputDefaults' => array(
			'div' => false,
			'label' => false,
			'legend' => false,
		),
	));
?>

		<section>
			<h3 class="plan_form_title">予約の詳細情報を入力</h3>
			<section class="table-form">
				<h4 class="title_blue">お客様情報の入力</h4>
				<ul class="inner">
					<li class="plan-form">
						<div class="label -bold">運転者名 <span class="required"></span></div>
						<div class="decide-width -half">
							<fieldset class="input-form -stack">
								<label class="label" for="js-lastName">姓（カナ）</label>
								<?php
									echo $this->Form->input('last_name', array(
										'type' => 'text',
										'required' => true,
										'placeholder' => '例：ヤマダ',
										'class' => 'field',
										'id' => 'js-lastName',
										'maxlength' => 20,
										'default' => !empty($application_user['family_name']) ? $application_user['family_name'] : '',
									));
								?>
							</fieldset>
							<fieldset class="input-form -stack">
								<label class="label" for="js-givenName">名（カナ）</label>
								<?php
									echo $this->Form->input('first_name', array(
										'type' => 'text',
										'required' => true,
										'placeholder' => '例：タロウ',
										'class' => 'field',
										'id' => 'js-givenName',
										'maxlength' => 20,
										'default' => !empty($application_user['first_name']) ? $application_user['first_name'] : '',
									));
								?>
							</fieldset>
						</div>
					</li>
					<li class="plan-form">
						<div class="label -bold">当日のご連絡先（半角数字）<span class="required"></span></div>
						<fieldset class="input-form">
							<?php
								echo $this->Form->input('tel', array(
									'type' => 'tel',
									'required' => true,
									'placeholder' => '例：09012341234',
									'class' => 'field',
									'id' => 'js-tel',
									'maxlength' => 32,
									'default' => !empty($application_user['tel']) ? $application_user['tel'] : '',
								));
							?>
						</fieldset>
					</li>
					<li class="plan-form">
						<div class="label -bold">メールアドレス（半角英数）<span class="required"></span></div>
						<fieldset class="input-form">
							<?php
								echo $this->Form->input('email', array(
									'type' => 'email',
									'required' => true,
									'placeholder' => '例：skyticket123@'.MAIL_DOMAIN,
									'class' => 'field',
									'id' => 'js-email',
									'maxlength' => 256,
									'default' => !empty($application_user['email']) ? $application_user['email'] : '',
									'autocomplete' => 'off',
								));
							?>
						</fieldset>
						<div class="autocomplete-email"></div>
						<div class="-notes">
							<i class="icm-mail"></i>
							<p class="-notes_text">
								<span>メール受信制限をされている方は @<?= MAIL_DOMAIN; ?> からのメール受信を許可してください。<br /></span>
								<a href="/info/error_mail" target="_blank">メールがとどかない場合の対処方法</a>
							</p>
						</div>
					</li>
<?php
	if (!empty($arrivalAirport) || !empty($departureAirport)) {
?>
					<li class="plan-form">
						<div class="label -bold">航空便情報</div>
<?php
		if (!empty($arrivalAirport)) {
?>
						<div class="decide-width -full">
							<fieldset class="input-form -stack">
								<label class="label" for="js-arrival">到着便</label>
								<?php
									echo $this->Form->input('arrival', array(
										'type' => 'text',
										'placeholder' => '例：ANA777',
										'class' => 'field',
										'id' => 'js-arrival',
										'maxlength' => 10,
									));
								?>
<?php
		}
?>
							</fieldset>

<?php		
		if (!empty($departureAirport)) {
?>
							<fieldset class="input-form -stack">
								<label class="label" for="js-departure">出発便</label>
								<?php
									echo $this->Form->input('departure', array(
										'type' => 'text',
										'placeholder' => '例：ANA777',
										'class' => 'field',
										'id' => 'js-departure',
										'maxlength' => 10,
									));
								}
								?>
							</fieldset>
						</div>
						<div class="-notes">
							<ul>
								<li class="-notes_li">※返却日の前日までにご登録がない場合、空港送迎バスをご利用いただけない場合がございます。</li>
								<li class="-notes_li">※予約完了後の登録も可能です。予約の照会・変更・取消へログインしてご登録ください。</li>
							</ul>
						</div>
					</li>
<?php
	}

	if (!empty($commodityInfo['Client']['need_remark'])) {
?>
					<li class="plan-form">
						<div class="label -bold"><label>備考欄</label></div>
						<fieldset class="textarea-form">
							<?php
								echo $this->Form->textarea('remark', array(
									'placeholder' => 'ご予約のレンタカー営業所に、プランに関する連絡・質問・ご要望などあればご記入ください。',
									'rows' => 3,
									'class' => 'field'
								));
							?>
						</fieldset>
					</li>
<?php
	}
?>
				</ul>
			</section>
<?php
	if (!$paymentApi) { //決済APIがfalseのとき
		if ($paymentMethod == 1) {
?>
			<section class="table-form">
				<h4 class="title_blue">クレジットカード情報を入力</h4>
				<ul class="inner">
					<li class="plan-form">
						<div class="label -bold">カード番号<span class="note">※ハイフンなし</span><span class="required"></span></div>
						<fieldset class="input-form">
							<?php
								echo $this->Form->input('cardNumber', array(
									'type' => 'text',
									//'required' => true,
									'placeholder' => '例：1111222233334444',
									'class' => 'field',
									'id' => 'js-cardNumber',
									'maxlength' => 19,
									'default' => !empty($inputed['card']['card_number']) ? $inputed['card']['card_number'] :'',
								));
							?>
						</fieldset>
					</li>
					<li class="plan-form">
						<div class="label -bold">カード名義<span class="note">※英大文字</span><span class="required"></span></div>
						<fieldset class="input-form">
							<?php
								echo $this->Form->input('cardOwner', array(
									'type' => 'text',
									//'required' => true,
									'placeholder' => '例：TARO YAMADA',
									'class' => 'field',
									'id' => 'js-cardOwner',
									'maxlength' => 40,
									'default' => !empty($inputed['card']['owner']) ? $inputed['card']['owner'] :'',
									'onblur' => 'this.value=this.value.toUpperCase()'
								));
							?>
						</fieldset>
					</li>
					<li class="plan-form">
						<div class="label -bold">セキュリティコード<span class="required"></span></div>
						<fieldset class="input-form">
							<?php
								echo $this->Form->input('cardCvc', array(
									'type' => 'password',
									//'required' => true,
									'placeholder' => '***',
									'class' => 'field',
									'id' => 'js-cvc',
									'maxlength' => 4,
									'autocomplete' => 'new-password',
									'default' => !empty($inputed['card']['sec_code']) ? $inputed['card']['sec_code'] :'',
								));
							?>
						</fieldset>
						<div class="-notes">
							<?php echo $this->element('sp_modal_credit_code'); ?>
						</div>
					</li>
					<li class="plan-form">
						<div class="label -bold">カード有効期限<span class="required"></span></div>
						<div class="decide-width -half">
							<fieldset class="select-form -stack">
								<label class="label">月(Month)</label>
								<div class="field-wrap">
<?php
	echo $this->Form->input('cardExpiration', array(
		'type' => 'date',
		'dateFormat' => 'M',
		'monthNames' => false,
		'class' => 'field',
		'selected' => array('month' => !empty($inputed['card']['credit_expiration']['month']) ? $inputed['card']['credit_expiration']['month'] : date('m')),
	));
?>
									<i class="icm-right-arrow"></i>
								</div>
							</fieldset>
							<fieldset class="select-form -stack">
								<label class="label">年(Year)</label>
								<div class="field-wrap">
<?php
	echo $this->Form->input('cardExpiration', array(
		'type' => 'date',
		'dateFormat' => 'Y',
		'maxYear' => date('Y') + 50,
		'minYear' => date('Y'),
		'class' => 'field',
		'selected' => array('year' => !empty($inputed['card']['credit_expiration']['year']) ? $inputed['card']['credit_expiration']['year'] : date('Y')),
	));
?>
									<i class="icm-right-arrow"></i>
								</div>
							</fieldset>
						</div>
					</li>
				</ul>
			</section>
<?php
		}
	} // -----決済APIがfalseのとき
?>

<?php
	if ($paymentMethod != 1) {
?>
			<section class="office_day plan_form">
				<h3 class="plan_form_title">予約条件の確認</h3>
				<h4 class="title_blue">貸出期間</h4>
				<div class="inner">
					<table>
						<tr>
							<th class="from_office_day">受取日</th>
						</tr>
						<tr>
							<td>
								<div class="date-input from_office_date"><?=$confirmation['from']; ?></div>
							</td>
						</tr>
						<tr>
							<th class="return_office_day">返却日</th>
						</tr>
						<tr>
							<td>
								<div class="date-input return_office_date"><?=$confirmation['to']; ?></div>
							</td>
						</tr>
					</table>
				</div>
			</section>
			<section class="office_place plan_form">
				<h4 class="title_blue">予約詳細</h4>
				<div class="inner">
					<table>
						<tr>
							<th class="from_office_place">受取営業所</th>
						</tr>
						<tr>
							<td class="select_type_line1 from_office_name">
								<div class="search_select"><?=$confirmation['rentOfficeName']; ?></div>
							</td>
						</tr>
<?php
		if (!empty($confirmation['rentOfficeMeetingInfo'])) {
?>
						<tr>
							<th class="from_office_howto">受取方法</th>
						</tr>
						<tr>
							<td class="select_type_line1 from_office_howto_inner">
								<div id="js_rent_info" class="search_select"><?=$confirmation['rentOfficeMeetingInfo']; ?></div>
							</td>
						</tr>
<?php
		}
?>
					</table>
					<table>
						<tr>
							<th class="return_office_place">返却営業所</th>
						</tr>
						<tr>
							<td class="select_type_line1 return_office_name">
								<div class="search_select"><?=$confirmation['returnOfficeName'];?></div>
							</td>
						</tr>
<?php
		if (!empty($confirmation['returnOfficeMeetingInfo'])) {
?>
						<tr>
							<th class="return_office_howto">返却方法</th>
						</tr>
						<tr>
							<td class="select_type_line1 return_office_howto_inner">
								<div id="js_return_info" class="search_select"><?=$confirmation['returnOfficeMeetingInfo'];?></div>
							</td>
						</tr>
<?php
		}
?>
					</table>
				</div>
			</section>
<?php
	}
?>
		</section>
<?php
	if ($paymentMethod != 1) {
?>
		<section>
			<section class="plan_comfirmation">
				<h3 class="plan_form_title">お支払い料金</h3>
				<h4 class="title_blue">ご利用人数</h4>
				<table class="people_num">
					<tr>
						<td>
							<span>大人</span><?=$confirmation['adults']; ?>人
						</td>
						<td>
							<span>子供</span><?=$confirmation['children']; ?>人
						</td>
						<td>
							<span>幼児</span><?=$confirmation['infants']; ?>人
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<th>ご利用日数</th>
						<td><?=$rentalPeriod; ?></td>
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
						<td><?php echo number_format($confirmation['dropOffLateNight']['nightFee']); ?>円</td>
					</tr>
<?php
		}
?>
<?php
		if (!empty($confirmation['privilegeOption'])) {
			foreach ($confirmation['privilegeOption'] as $key => $value) {
?>
					<tr>
						<th><?=$value[0]; ?></th>
						<td><?=$value[1]; ?>円</td>
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
							<p class="caption_gray"><?= $rentalPeriod; ?>料金</p>
						</div>
						<div class="fr">
							<p class="price_block_price"><span><?php echo number_format($confirmation['estimationTotalPrice']); ?>円</span></p>
						</div>
					</div>
					<?php echo $this->element('sp_note_payment_onsite'); // 現地決済の注意事項 ?>

					<?php echo $this->element('sp_reservation_note_memo_reminder'); // 予約番号メモリマインダー ?>

				</div>

				<div class="mb20px">
					<dl class="accordion">
						<dt class="trigger">
							注意事項<span class="open-close">open</span>
						</dt>
						<dd class="acordion_tree">
							<?= nl2br(h($confirmation['precautions'])); ?>
						</dd>
						<dt class="trigger">
							キャンセルポリシー<span class="open-close">open</span>
						</dt>
						<dd class="acordion_tree">
							ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
							<br>
							<?php
								echo $confirmation['cancelPolicy'];
							?>
							<br>
							・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
							<?php
								echo nl2br(h($confirmation['clientCancelPolicy']));
							?>
						</dd>
					</dl>
				</div>

<?php
		if ($paymentMethod != 1) {
			echo $this->element('reservation_note');
		}
?>

				<div class="inner">
					<div class="ac">
						<p class="caution">
							<span>お急ぎください！</span>人気のレンタカーはすぐに予約が埋まります。
						</p>
<?php
		echo $this->Form->submit('次へ（予約を確定）', array('class' => 'btn-type-primary', 'id' => 'btn_submit', 'div' => false));
?>
					</div>
				</div>
			</section>
		</section>
<?php
	} else {
?>
		<div class="inner">
			<div>
				<p class="caution">
					<span>お急ぎください！</span>人気のレンタカーはすぐに予約が埋まります。
				</p>
<?php
		echo $this->Form->submit('次へ（確認画面）', array('class' => 'btn-type-primary', 'id' => 'btn_submit_payment', 'div' => false, 'type' => 'button'));
?>
			</div>
		</div>
<?php
	}
?>

<?php
	echo $this->Form->hidden('is_send_mail', array('value' => 1));
	echo $this->Form->hidden('uniqId', array('value' => $this->data['Reservation']['uniqId']));
	echo $this->Form->hidden('isStep1', array('value' => true));
	echo $this->Form->hidden('from_rentacar_client', array('value' => ($fromRentacarClient ? 'true' : 'false')));
	echo $this->Form->end();
?>

<?php
	if (!empty($refererPlan)) {
?>
		<div class="inner mb20px">
			<?php echo $this->Html->link('戻る', $refererPlan, array('class' => 'btn-type-sub')); ?>
		</div>
<?php
	}
?>
	</div><!-- js-content End -->
<?php
	if ($paymentMethod == 1) {
		echo $this->element('loading_indicator_earth');

		if (!$paymentApi) {
			echo $this->Html->script($econ_jsf_url);
		} else {
			echo $this->Html->script('jquery-1.9.1.js');
		}
		echo $this->Html->script('input_econ.js');
	}
?>

<?php
	$paymentApiFlgToJs = $paymentApi ? true : false ;
?>
</main>

<script>
var paymentApiFlg = "<?= $paymentApiFlgToJs ?>";

$(function() {

<?php
	if (!$login_flg){
?>
	// ログイン入力欄を出現させるクリックイベント
	$("#show_login_button").click( function(){
		$("#login_area").slideToggle();
	});

	// submit可能フラグ
	var btnLogin = $('#js-btnLogin'),
		isEmailError = false,
		isPassError  = false,
		ERROR_REQUIRE = '必須';

	$('#loginMail').on('blur keyup', function(event) {
		var mailVal = $(this).val();

		if(mailVal.length <= 0) {
			$('#js-err_mail').html(ERROR_REQUIRE).addClass('error-message');
			$(this).addClass('-error');
			btnLogin.attr('disabled', true);

		} else if (!mailVal.match(/([a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-])+@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/)) {
			$('#js-err_mail').html('メールアドレスを入力してください').addClass('error-message');
			$(this).addClass('-error');
			btnLogin.attr('disabled', true);

		} else {
			$('#js-err_mail').html('').removeClass('error-message');
			$(this).removeClass('-error');
			if(!isPassError){
				btnLogin.attr('disabled', false);
			}
		}
		isEmailError = $('#js-err_mail').html();
	});

	$('#loginPassWord').on('blur keyup', function(event) {
		var passVal = $(this).val();

		if(passVal.length <= 0) {
			$('#js-err_pass').html(ERROR_REQUIRE).addClass('error-message');
			$(this).addClass('-error');
			btnLogin.attr('disabled', true);

		} else if (!passVal.match(/^[0-9a-zA-Z -/:-@\[-\`\{-\~]+$/)) {
			$('#js-err_pass').html('半角英数字で入力して下さい').addClass('error-message');
			$(this).addClass('-error');
			btnLogin.attr('disabled', true);

		} else if (passVal.length < 6 || passVal.length > 32) {
			$('#js-err_pass').html('6文字～32文字の半角英数字').addClass('error-message');
			$(this).addClass('-error');
			btnLogin.attr('disabled', true);

		} else {
			$('#js-err_pass').html('').removeClass('error-message');
			$(this).removeClass('-error');
			if(!isEmailError){
				btnLogin.attr('disabled', false);
			}
		}

		isPassError = $('#js-err_pass').html();
	});
	
	$('#js-toggle_pass').on('click', function() {
		$(this).toggleClass('icm-eye icm-eye-blocked');
		var input = $(this).prev('input');
		if (input.attr('type') == 'text') {
			input.attr('type','password');
		} else {
			input.attr('type','text');
		}
	});
	
<?php
		if ($login_error_flg){
?>
	location.href="#js-login_error";
	$('#login_area').show();
	$('#btn_scroll_form').on('click', function() {
		var target_top = $("#application_form").offset().top;
		$('body,html').animate({
			scrollTop: target_top
		}, 500);
	});
<?php
		}
?>
<?php
	}
?>

<?php
	// 受け取り情報が存在する場合
	if (!empty($confirmation['rentOfficeMeetingInfo'])) {
?>
	var rent_info = $("#js_rent_info").text();
	if( rent_info.length > 100 ){
		var rent_info_limit = rent_info.substr(0, 100);

		$("#js_rent_info").html( '<div class="shoten_cont">' + rent_info_limit + "…" + '<div><div class="js-btnMore btn-more -hide"><p class="readmore-toggler">もっと読む</p><i class="icm-right-arrow icon-right-arrow_down"></i></div></div></div>' );
		$("#js_rent_info").append( '<div class="shoten_cont hidden">' + rent_info + '<div><div class="js-btnMore btn-more -open"><p class="readmore-toggler">閉じる</p><i class="icm-right-arrow"></i></div></div></div>' );
	}
<?php
	}
?>

<?php	
	// 返却情報が存在する場合
	if (!empty($confirmation['returnOfficeMeetingInfo'])) {
?>
	var return_info = $("#js_return_info").text();
	if( return_info.length > 100 ){
		var return_info_limit = return_info.substr(0, 100);

		$("#js_return_info").html( '<div class="shoten_cont">' + return_info_limit + "…" + '<div><div class="js-btnMore btn-more -hide"><p class="readmore-toggler">もっと読む</p><i class="icm-right-arrow icon-right-arrow_down"></i></div></div></div>' );
		$("#js_return_info").append( '<div class="shoten_cont hidden">' + return_info + '<div><div class="js-btnMore btn-more -open"><p class="readmore-toggler">閉じる</p><i class="icm-right-arrow"></i></div></div></div>' );
	}
<?php
	}
?>
	$('.js-btnMore').on("click", function(){
		$(".shoten_cont").toggleClass( "hidden" );
	});


<?php
	if ($paymentMethod != 1) {
?>
	// 使ってなさそう
	// 現地支払い案内を上部に表示
	// $("#js_prepaid_free").show();
<?php
	}
?>

	// 二重送信防止
	$('form').on('submit', function(event){
		$('#submitReserve').attr('disabled', true);
	});

	// 使ってなさそう
	// $(".notes_slide_p").delay(300).slideDown();

	// ひらがな to カタカナ
	function hiraganaToKatakana(str) {
		return str.replace(/[ぁ-ん]/g, function(s) {
			return String.fromCharCode(s.charCodeAt(0) + 0x60);
		});
	}

	function changeSubmitButton() {
<?php
	if ($paymentMethod == 1) {
		if ($paymentApi) { //決済APIがtrueとき
?>
			if ($('#js-lastName').hasClass('-error') || $('#js-givenName').hasClass('-error') ||
			$('#js-tel').hasClass('-error') || $('#js-email').hasClass('-error')) {
				$('#btn_submit_payment').attr('disabled', true);
			} else {
				$('#btn_submit_payment').attr('disabled', false);
			}
<?php
		} else { //決済APIがfalseのとき
?>
			if ($('#js-lastName').hasClass('-error') || $('#js-givenName').hasClass('-error') ||
			$('#js-tel').hasClass('-error') || $('#js-email').hasClass('-error') ||
			$('#js-cardNumber').hasClass('-error') || $('#js-cardOwner').hasClass('-error') ||
			$('#js-cvc').hasClass('-error')) {
				$('#btn_submit_payment').attr('disabled', true);
			} else {
				$('#btn_submit_payment').attr('disabled', false);
			}
<?php
		}
	} else {
?>
		if ($('#js-lastName').hasClass('-error') || $('#js-givenName').hasClass('-error') ||
			$('#js-tel').hasClass('-error') || $('#js-email').hasClass('-error')) {
			$('#btn_submit').attr('disabled', true);
		} else {
			$('#btn_submit').attr('disabled', false);
		}
<?php
	}
?>
	}

	// 姓は全角カタカナ
	$('#js-lastName').on('blur', function() {
		var inputValue = $(this).val();
		$(this).val(hiraganaToKatakana(inputValue));
		if(!$('#js-lastName').val().match(/^[ァ-ンー]+$/)) {
			$('#js-lastName').addClass('-error');
			$(this).siblings('.js-err-message').remove();
			$(this).parent().append('<p class="js-err-message error-message">カタカナを入力してください</p>');
		} else {
			$('#js-lastName').removeClass('-error');
			$(this).siblings('.js-err-message').remove();
		}
		changeSubmitButton();
	});

	// 名は全角カタカナ
	$('#js-givenName').on('blur', function() {
		var inputValue = $(this).val();
		$(this).val(hiraganaToKatakana(inputValue));
		if(!$('#js-givenName').val().match(/^[ァ-ンー]+$/)) {
			$('#js-givenName').addClass('-error');
			$(this).siblings('.js-err-message').remove();
			$(this).parent().append('<p class="js-err-message error-message">カタカナを入力してください</p>');
		} else {
			$('#js-givenName').removeClass('-error');
			$(this).siblings('.js-err-message').remove();
		}
		changeSubmitButton();
	});

	// 電話番号
	$('#js-tel').on('blur', function() {
		var inputValue = $(this).val();
		if(!inputValue.match(/^\d{7,13}$/)) {
			$(this).addClass('-error');
			$(this).next('.js-err-message').remove();
			$(this).after('<p class="js-err-message error-message">電話番号を入力してください</p>');
		} else {
			$(this).removeClass('-error');
			$(this).next('.js-err-message').remove();
		}
		changeSubmitButton();
	});

	// メールアドレス
	$('#js-email').on('blur', function() {
		var inputValue = $(this).val();
		if(!inputValue.match(/^([a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-])+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) {
			$(this).addClass('-error');
			$(this).next('.js-err-message').remove();
			$(this).after('<p class="js-err-message error-message">メールアドレスは必須です（半角英数以外の大文字/スペース/使用できない文字等が含まれていないかご確認ください）</p>');
		} else {
			$(this).removeClass('-error');
			$(this).next('.js-err-message').remove();
		}
		changeSubmitButton();
	});

<?php
	if (!$paymentApi) { //決済APIがfalseのとき
?>
	// カード番号
	$('#js-cardNumber').on('blur', function() {
		var inputValue = $(this).val();
		if (!inputValue.match(/^4\d{12}(\d{3})?$/) && // visa
			!inputValue.match(/^5[1-5]\d{14}$/) && // mc
			!inputValue.match(/^(3\d{4}|2100|1800)\d{11}$/) && // jcb
			!inputValue.match(/^3[4|7]\d{13}$/) && // amex
			!inputValue.match(/^(?:3(0[0-5]|[68]\d)\d{11})|(?:5[1-5]\d{14})$/) ){ // diners
			$(this).addClass('-error');
			$(this).next('.js-err-message').remove();
			$(this).after('<p class="js-err-message error-message">カード番号を入力してください</p>');
		} else {
			$(this).removeClass('-error');
			$(this).next('.js-err-message').remove();
		}
		changeSubmitButton();
	});

	// カード名義
	$('#js-cardOwner').on('blur', function() {
		var inputValue = $(this).val();
		if (!inputValue.match(/^([A-Z]|\s)+$/)){
			$(this).addClass('-error');
			$(this).next('.js-err-message').remove();
			$(this).after('<p class="js-err-message error-message">カード名義を入力してください</p>');
		} else {
			$(this).removeClass('-error');
			$(this).next('.js-err-message').remove();
		}
		changeSubmitButton();
	});

	// セキュリティコード
	$('#js-cvc').on('blur', function() {
		var inputValue = $(this).val();
		if (!inputValue.match(/^(\d{3}|\d{4})$/)){
			$(this).addClass('-error');
			$(this).next('.js-err-message').remove();
			$(this).after('<p class="js-err-message error-message">セキュリティコードを入力してください</p>');
		} else {
			$(this).removeClass('-error');
			$(this).next('.js-err-message').remove();
		}
		changeSubmitButton();
	});

<?php
	}
?>
});
</script>
<script>
// メアド自動完成
if(document.getElementById("js-email") && document.querySelector('.autocomplete-email')) {
	autocomplete(document.getElementById("js-email"));
}
function autocomplete(inp) {
	var currentFocus;
	// 入力時メアドを自動完成して表示
	inp.addEventListener("input", function(e) {
		var val = this.value;
		if (!val) { return false;}
		currentFocus = -1;

		if(val.indexOf('@') != -1) {
			// ドメインリスト取得
			$.ajax({
				url: "https://skyticket.jp/hotel/api/v3/mail/domain/suggest?domain=@"+val.split('@')[1],
				type: "GET",
				dataType: "json",
			}).done(function(domainList){
				// 検索結果一致
				if(domainList.length == 1 && '@'+val.split('@')[1] === domainList[0]) {
					closeAllLists();
				} else {
					// 検索結果のリストを作成
					const autocompleteEmailList = document.createElement("ul");
					autocompleteEmailList.setAttribute('class', 'autocomplete-email-list');
					autocompleteEmailList.setAttribute('id', 'autocomplete-email-list');
					document.querySelector('.autocomplete-email').appendChild(autocompleteEmailList);

					domainList.forEach(domain => {
						const autocompleteEmailItem = document.createElement("li");
						autocompleteEmailItem.setAttribute('class', 'autocomplete-email-item');
						autocompleteEmailItem.innerHTML = val.split('@')[0] + domain
						autocompleteEmailItem.innerHTML += "<input type='hidden' value='" + val.split('@')[0] + domain + "'>";
						autocompleteEmailItem.addEventListener("mousedown", function(e) {
							inp.value = this.getElementsByTagName("input")[0].value;
							closeAllLists();
						});
						autocompleteEmailList.appendChild(autocompleteEmailItem);
					});
					closeAllLists(autocompleteEmailList);
				}
			}).fail(function(a,b,c){
				console.log("エラーが発生しました。"+b);
			});
		} else {
			closeAllLists();
		}
	});
	// 入力欄以外をクリック時、選択時リストを全部閉じる
	function closeAllLists(elmnt) {
		var x = document.getElementsByClassName('autocomplete-email-list');
		for (var i = 0; i < x.length; i++) {
			if (elmnt != x[i] && elmnt != inp) {
				x[i].parentNode.removeChild(x[i]);
			}
		}
	}
	document.addEventListener("click", function (e) {
		closeAllLists(e.target);
	});
}
</script>

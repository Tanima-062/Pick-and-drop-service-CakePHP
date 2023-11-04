<main class="wrap contents step1-contents clearfix">
	<?php echo $this->element('reservation_steps'); // 予約ステップ ?>

	<div id='sessionMessage'>
<?php
	if (!empty($sessionMessage)) {
		echo $this->element('session_message');
	}
?>
	</div>

<?php echo $this->element('plan_view'); ?>

<?php
	// 未ログインの場合-----
	if (!$login_flg) {
		if ($login_error_flg){
?>
	<div id="js-login_error">
		<p class="login_error">ログインに失敗しました。<br />メールアドレスまたはパスワードが登録情報と異なっています。</p>
		<p class="text_right">
			<a href="/user/remind.php" target="_blank">パスワードを忘れた場合</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a href="javascript:void(0);" id="btn_scroll_form">ログインせずに予約する場合</a>
		</p>
	</div><!-- error End -->
<?php
		} else {
?>
	<div class="login-form close">
		<input type="button" name="" id="show_login_button" class="btn-type-link is-login" value="ログインして予約する方はこちら" />
	</div><!-- btn End -->
<?php
		}
?>
	<div class="login-form" id="login_area" style="display:none">
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
				<label class="label">登録メールアドレス（会員ID用）<span class="note">※半角英数字</span></label>
				<input id="loginMail" class="field" type="email" name="email" value="" placeholder="登録メールアドレス（会員ID用）">
				<p id="js-err_mail"></p>
			</div>
			<div class="input-form">
				<label class="label">「パスワード」 を入力してください<span class="note">※半角英数字</span></label>
				<div class="icon-right field-wrap">
					<input id="loginPassWord" class="field" type="password" name="password" value="" placeholder="パスワード">
					<i id="js-toggle_pass" class="-icon icm-eye-blocked"></i>
				</div>
				<p id="js-err_pass"></p>
			</div>
			<div class="input-form">
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
			</div>
		</div><!-- form End -->
<?php
		echo $this->Form->hidden('uniqId', array('value' => $this->data['Reservation']['uniqId']));
		if ($fromRentacarClient) {
			echo $this->Form->hidden('from_rentacar_client', array('value' => 'true'));
		}
		echo $this->Form->end();
?>
	</div><!-- login_area End -->
<?php
	} 
	// -----未ログインの場合
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

	<h3 class="heading -big">運転者情報を入力</h3>
	<table class="contents_detail_tbl table-form">
		<tr>
			<th>
				<span class="va-middle">氏名（カナ）</span>
				<span class="label-require va-middle">必須</span>
			</th>
			<td>
				<p class="explain -top">運転する方のお名前で予約してください</p>
				<div class="decide-width -name">
					<fieldset class="input-form">
						<label class="label">姓</label>
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
					<fieldset class="input-form">
						<label class="label">名</label>
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
			</td>
		</tr>
		<tr>
			<th>
				<span class="va-middle">携帯番号</span>
				<span class="label-require va-middle">必須</span>
			</th>
			<td>
				<div>
					<fieldset class="input-form">
						<?php
							echo $this->Form->input('tel', array(
								'type' => 'tel',
								'required' => true,
								'placeholder' => '例：09012341234',
								'class' => 'field -half',
								'id' => 'js-tel',
								'maxlength' => 32,
								'default' => !empty($application_user['tel']) ? $application_user['tel'] : '',
							));
						?>
					</fieldset>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<span class="va-middle">メールアドレス</span>
				<span class="label-require va-middle">必須</span>
			</th>
			<td class="of-visible">
				<div>
					<fieldset class="input-form">
						<?php
							echo $this->Form->input('email', array(
								'type' => 'email',
								'required' => true,
								'placeholder' => '例：123abcde@'.MAIL_DOMAIN,
								'class' => 'field -half',
								'id' => 'js-email',
								'maxlength' => 256,
								'default' => !empty($application_user['email']) ? $application_user['email'] : '',
								'autocomplete' => 'off',
							));
						?>
					</fieldset>
					<div class="autocomplete-email"></div>
				</div>
			</td>
		</tr>
<?php
	if (!empty($arrivalAirport)) {
?>
		<tr>
			<th>
				<span>到着便情報</span>
			</th>
			<td>
				<div>
					<fieldset class="input-form">
						<label class="label">便名</label>
						<?php
							echo $this->Form->input('arrival', array(
								'type' => 'text',
								'placeholder' => '例：ANA777',
								'class' => 'field -quarter',
								'maxlength' => 10,
							));
						?>
					</fieldset>
				</div>
				<span class="explain -bottom">※到着便は後日登録も可能です</span>
			</td>
		</tr>
<?php
	}
	if (!empty($departureAirport)) {
?>
		<tr>
			<th>
				<span>出発便情報</span>
			</th>
			<td>
				<div>
					<fieldset class="input-form">
						<label class="label">便名</label>
						<?php
							echo $this->Form->input('departure', array(
								'type' => 'text',
								'placeholder' => '例：ANA777',
								'class' => 'field -quarter',
								'maxlength' => 10,
							));
						?>
					</fieldset>
				</div>
				<span class="explain -bottom">※出発便は後日登録も可能です</span>
			</td>
		</tr>
<?php
	}
	if (!empty($commodityInfo['Client']['need_remark'])) {
?>
		<tr>
			<th>
				<span>備考欄</span>
			</th>
			<td>
				<div>
					<fieldset class="textarea-form">
						<?php
							echo $this->Form->textarea('remark', array(
								'placeholder' => 'ご予約のレンタカー営業所に、プランに関する連絡・質問・ご要望などあればご記入ください。',
								'rows' => 4,
								'class' => 'field',
							));
						?>
					</fieldset>
				</div>
			</td>
		</tr>
<?php
	}
?>
	</table>

<?php
	// 決済APIがfalseのとき-----
	if (!$paymentApi) {

		if ($paymentMethod == 1) {
?>
	<h3 class="heading -big">クレジットカード情報を入力</h3>
	<table class="contents_detail_tbl table-form">
		<tr>
			<th>
				<span>利用可能な</br>クレジットカード</span>
			</th>
			<td>
				<?php
					echo $this->Form->input('cardType', array(
						'type' => 'radio',
						'hiddenField'=>false,
						'label' => true,
						'options' => array(
							'visa' => $this->Html->image("/img/cards/visa.jpg", array(
								'alt' => 'VISA'
							)),
							'mastercard' => $this->Html->image("/img/cards/master.jpg", array(
								'alt' => 'MasterCard'
							)),
							'jcb' => $this->Html->image("/img/cards/jcb.jpg", array(
								'alt' => 'JCB'	
							)),
							'amex' => $this->Html->image("/img/cards/american.jpg", array(
								'alt' => 'American Express'
							)),
							'dinersclub' => $this->Html->image("/img/cards/dinas.jpg", array(
								'alt' => 'Diners'
							))
						),
						'value' => !empty($inputed['card']['card']) ? $inputed['card']['card'] : '',
						'disabled' => true,
						'class' => 'input_credit_type'
					));
				?>
			</td>
		</tr>
		<tr>
			<th>
				<span>カード番号</span>
			</th>
			<td>
				<div>
					<fieldset class="input-form">
						<?php
							echo $this->Form->input('cardNumber', array(
								'type' => 'text',
								//'required' => true,
								'placeholder' => '**** **** **** ****',
								'id' => 'js-cardNumber',
								'class' => 'field -half',
								'maxlength' => 19,
								'default' => !empty($inputed['card']['card_number']) ? $inputed['card']['card_number'] :'',
							));
						?>
					</fieldset>
				</div>
				<span class="explain -bottom">※ハイフンなし 例)1111222233334444</span>
			</td>
		</tr>
		<tr>
			<th>
				<span>カード名義</span>
			</th>
			<td>
				<div>
					<fieldset class="input-form">
						<?php
							echo $this->Form->input('cardOwner', array(
								'type' => 'text',
								//'required' => true,
								'id' => 'js-cardOwner',
								'class' => 'field -half',
								'maxlength' => 40,
								'default' => !empty($inputed['card']['owner']) ? $inputed['card']['owner'] :'',
								'onkeyup' => 'this.value=this.value.toUpperCase()'
							));
						?>
					</fieldset>
				</div>
				<span class="explain -bottom">※英大文字 例)TARO YAMADA</span>
			</td>
		</tr>
		<tr>
			<th>
				<span>セキュリティコード</span>
			</th>
			<td>
				<div>
					<fieldset class="input-form">
						<?php
							echo $this->Form->input('cardCvc', array(
								'type' => 'password',
								//'required' => true,
								'placeholder' => '***',
								'class' => 'cvc',
								'id' => 'js-cvc',
								'class' => 'field -quarter',
								'maxlength' => 4,
								'autocomplete' => 'new-password',
								'default' => !empty($inputed['card']['sec_code']) ? $inputed['card']['sec_code'] :'',
							));
						?>
					</fieldset>
				</div>
				<span class="explain -bottom">※カード裏面のご署名欄にある３桁の番号、または表面にある４桁の番号となります</span>
			</td>
		</tr>
		<tr>
			<th>
				<span>カード有効期限</br>(MONTH/YEAR)</span>
			</th>
			<td>
				<div class="decide-width -limit">
					<div class="select-form">
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
					</div>
					<div class="-slash">/</div>
					<div class="select-form">
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
					</div>
				</div>
			</td>
		</tr>
	</table>
<?php
		}
	} // -----決済APIがfalseのとき
?>


<?php
	if ($paymentMethod != 1) {
?>

	<h3 class="heading -big">レンタカー予約内容の確認</h3>
	<h4 class="heading -x-large">予約詳細</h4>
	<table class="contents_confirm_tbl table-form">
		<tr>
			<th>レンタカー事業者</th>
			<td>
<?php
		if(!empty($commodityInfo['Client']['sp_logo_image'])){
			echo $this->Html->image('logo/square/'.$commodityInfo['Client']['id'].'/'.$commodityInfo['Client']['sp_logo_image'], array('alt' => $commodityInfo['Client']['name'],'width' => 64));
			echo '<br>';
		}
?>
				<div class="contents_complete_tbl_shop">
				<span class="contents_complete_tbl_shopName"><?php echo $confirmation['clientName']; ?></span>
				</div>
			</td>
		</tr>
		<tr class="">
			<th>受取日時</th>
			<td class="text_danger"><?php echo $confirmation['from']; ?></td>
		</tr>
<?php
		if (!empty($confirmation['arrival'])) {
?>
		<tr>
			<th>到着便名</th>
			<td><?php echo $confirmation['arrival']; ?></td>
		</tr>
<?php
		}
?>
		<tr class="">
			<th>受取店舗</th>
			<td><span><?php echo $confirmation['rentOfficeName']; ?></span><span>（<?php echo trim($confirmation['rentOfficeTel']); ?>）</span></td>
		</tr>
<?php
		if (!empty($confirmation['rentOfficeMeetingInfo'])) {
?>
		<tr class="">
			<th>受取方法</th>
			<td><span><?php echo nl2br($confirmation['rentOfficeMeetingInfo']); ?></span></td>
		</tr>
<?php
		}
?>
		<tr class="">
			<th>返却日時</th>
			<td class="text_danger"><?php echo $confirmation['to']; ?></td>
		</tr>
<?php
		if (!empty($confirmation['departure'])) {
?>
		<tr>
			<th>出発便名</th>
			<td><?php echo $confirmation['departure']; ?></td>
		</tr>
<?php
		}
?>
		<tr class="">
			<th>返却店舗</th>
			<td><span><?php echo $confirmation['returnOfficeName']; ?></span><span>（<?php echo trim($confirmation['returnOfficeTel']); ?>）</span></td>
		</tr>
<?php
		if (!empty($confirmation['returnOfficeMeetingInfo'])) {
?>
		<tr class="">
			<th>返却方法</th>
			<td><span><?php echo nl2br($confirmation['returnOfficeMeetingInfo']); ?></span></td>
		</tr>
<?php
		}
?>
	</table>


	<h4 class="heading -x-large">お支払金額</h4>
	<table class="contents_confirm_tbl table-form">
		<tr class="">
			<th>ご利用人数</th>
			<td>
				<span>大人（12歳以上）</span><span><?php echo $confirmation['adults']; ?>名</span>
<?php
		if (!empty($confirmation['children'])) {
?>
				/ <span>子供（6〜11歳）</span><span><?php echo $confirmation['children']; ?>名</span>
<?php
		}
		if (!empty($confirmation['infants'])) {
?>
				/ <span>幼児（5歳以下）</span><span><?php echo $confirmation['infants']; ?>名</span>
<?php
		}
?>
			</td>
		</tr>
		<tr class="clearfix">
			<th>基本料金</th>
			<td>
				<span class="text_bold" style="float:right;">&yen; <?php echo number_format($basicCharge) ?></span>
			</td>
		</tr>
		<tr class="clearfix">
			<th>オプション料金</th>
			<td>
<?php
		if (!empty($confirmation['dropOffLateNight']['dropPrice'])) {
?>
				<div>
					<span class="text_bold" >乗り捨て料金</span>
					<span class="text_bold" style="float:right;" >&yen; <?php echo number_format($confirmation['dropOffLateNight']['dropPrice']); ?></span>
				</div>
<?php
		}
		if (!empty($confirmation['dropOffLateNight']['nightFee'])) {
?>
				<div>
					<span class="text_bold" >深夜手数料</span>
					<span class="text_bold" style="float:right;" >&yen; <?php echo number_format($confirmation['dropOffLateNight']['nightFee']); ?></span>
				</div>
<?php
		}
		if (!empty($confirmation['privilegeOption'])) {
			foreach ($confirmation['privilegeOption'] as $key => $value) {
?>
				<div>
					<span><?php echo $value[0]; ?></span>
					<span class="text_bold" style="float:right;" >&yen; <?php echo $value[1]; ?></span>
				</div>
<?php
			}
		}
?>
			</td>
		</tr>
		<tr>
			<th>お支払合計金額</th>
			<td class="contents_result_detail_amount">
				<div class="text_right rent-padding">
					<span class="bubble bubble-right">税込価格</span>
					<span class="contents_result_detail_amount_price">&yen; <?php echo number_format($confirmation['estimationTotalPrice']); ?></span>
				</div>

				<?php echo $this->element('pc_note_payment_onsite', [
						'acceptCash' => $confirmation['acceptCash'], 
						'acceptCard' => $confirmation['acceptCard']
					]); // 現地決済の注意事項 ?>

<?php
		if (!empty($confirmation['Cards'])) {
			echo "・ご利用頂けるカード";
			foreach ($confirmation['Cards']['url'] as $key => $card) {
				echo $this->Html->image($card, array('alt' => $confirmation['Cards']['name'][$key]));
			}
		}
?>
			</td>
		</tr>
		<tr>
			<th>キャンセルポリシー</th>
			<td>
				ご予約をキャンセルされる場合、下記のキャンセル料を申し受けます。<br>
				<br>
				<?php echo $confirmation['cancelPolicy']; ?>
				<br>
				・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
				<?php echo nl2br(h($confirmation['clientCancelPolicy'])); ?>
			</td>
		</tr>
	</table>
<?php
	}
?>

<?php
	if ($paymentMethod != 1) {
		echo $this->element('reservation_note');
	}
?>

	<section class="result-btn-wrap">
<?php
	echo $this->Form->hidden('is_send_mail', array('value' => 1));
	echo $this->Form->hidden('uniqId', array('value' => $this->data['Reservation']['uniqId']));
	echo $this->Form->hidden('isStep1', array('value' => true));
	echo $this->Form->hidden('from_rentacar_client', array('value' => ($fromRentacarClient ? 'true' : 'false')));
	if (!empty($refererPlan)) {
		echo $this->Html->link('詳細選択に戻る', $refererPlan, array('class' => 'btn-type-cancel left-btn'));
	}
	if ($paymentMethod == 1) {
		echo $this->Form->submit('次へ（確認画面）', array('type' => 'button', 'id' => 'btn_submit_payment', 'class' => 'btn-type-primary right-btn', 'div' => false));
	} else {
		echo $this->Form->submit('次へ（予約を確定）', array('type' => 'button', 'id' => 'btn_submit', 'class' => 'btn-type-primary right-btn', 'div' => false));
	}

?>
	</section>
<?php
	echo $this->Form->end();
?>

<?php
	if ($paymentMethod != 1) {
?>
	<section class="panel panel_green">
		予約完了後に表示される予約番号のメモをお願いいたします。予約の確認や取消に必要な情報ですので、当日まで大切にお控えください。
	</section>

	<section class="panel panel_note">
		<h5 class="panel_note_title">注意事項</h5>
		<?php echo nl2br(h($confirmation['precautions'])); ?>
	</section>

<?php
	}
?>
</main>
<!-- wrap -->

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
<script>
var paymentApiFlg = "<?= $paymentApiFlgToJs ?>";

// ひらがな to カタカナ
function hiraganaToKatakana(str) {
	return str.replace(/[ぁ-ん]/g, function(s) {
		return String.fromCharCode(s.charCodeAt(0) + 0x60);
	});
}

function checkForm() {
	$("#application_form input").each(function () {
		$(this).blur();
	});

	var is_error = false;
	$(".js-err-message").each(function () {
		$("html,body").animate({ scrollTop: $('.contents_detail_tbl').offset().top - 30 });
		is_error = true;
		return false;
	});
	return is_error
}

$(function() {

	// 合計金額を取得
	var total_price = $(".contents_result_detail_amount_price").text();
	$("#js_btm_total_place").html(total_price);

	$("#btn_submit").on("click", function(){
		// gaイベント
		ga('send', 'event', 'pc_step1', 'click', '次へボタン', {
			hitCallback: createFunctionWithTimeout(function(){
				if (checkForm()) {
					return;
				}
				$("#application_form").submit();
			})
		});
	});
<?php
	if ($paymentMethod != 1) {
?>
	$("#btn_submit_bottom").on("click", function(){
		ga('send', 'event', 'pc_step1', 'click', 'フローティングボタン', {
			hitCallback: createFunctionWithTimeout(function(){
				if (checkForm()) {
					return;
				}
				$("#application_form").submit();
			})
		});
	});
<?php
	}
?>
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
			$('#btn_submit_bottom').attr('disabled', true);
		} else {
			$('#btn_submit').attr('disabled', false);
			$('#btn_submit_bottom').attr('disabled', false);
		}
<?php
	}
?>
	}

	// Form Validation-----
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
			$(this).after('<p class="js-err_message error-message">セキュリティコードを入力してください</p>');
		} else {
			$(this).removeClass('-error');
			$(this).next('.js-err-message').remove();
		}
		changeSubmitButton();
	});
<?php
	}
?>
	// -----Form Validation


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
	// キー操作
	inp.addEventListener("keydown", function(e) {
		var x = document.getElementById('autocomplete-email-list');
		if (x) x = x.getElementsByTagName("li");
		if (e.keyCode == 40) { //down
			e.preventDefault();
			currentFocus++;
			addActive(x);
		} else if (e.keyCode == 38) { //up
			e.preventDefault();
			currentFocus--;
			addActive(x);
		} else if (e.keyCode == 13) { // enter
			e.preventDefault();
			if (currentFocus > -1) {
				if (x) x[currentFocus].click();
			}
		}
	});
	// 活性表示
	function addActive(x) {
		if (!x) return false;
		removeActive(x);
		if (currentFocus >= x.length) currentFocus = 0;
		if (currentFocus < 0) currentFocus = (x.length - 1);
		x[currentFocus].classList.add("autocomplete-active");
		inp.value = x[currentFocus].getElementsByTagName("input")[0].value;
	}
	// 活性表示除去
	function removeActive(x) {
		for (var i = 0; i < x.length; i++) {
			x[i].classList.remove("autocomplete-active");
		}
	}
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

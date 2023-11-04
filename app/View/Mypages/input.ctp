<div id="renewal" class="login wrap contents clearfix mypages-input_page">
<?php
	echo $this->element('progress_bar'); 
?>
	<h2 class="h-type03">お支払い情報入力</h2>
	<div id="sessionMessage">
		<?php if (!empty($sessionMessage)) { ?>
			<?php echo $this->element('session_message'); ?>
		<?php } ?>
	</div>
	<div class="step-box">
		<p class="ttxt">下記のフォームにクレジットカード情報を入力して、「お支払い」ボタンを押してください。</p>
		<?php echo $this->Form->create('Reservation', array('url' => '/mypages/input_confirm/', 'name' => 'reserve', 'type' => 'post', 'inputDefaults' => $inputDefaults)); ?>
		<div class="box-outer rent-margin-bottom">
			<h3 class="h-type04">未払金額</h3>

			<div class="box-inner">
				<table class="pc-form-date center">
					<tr>
						<th>未払金額</th>
						<td class="price">
							<span>&yen;<?php echo number_format($unpaidAmount); ?></span>
						</td>
					</tr>
				</table>
			</div><!-- end .box-inner -->
		</div><!-- end .box-outer -->
		<div class="box-outer rent-margin-bottom">
			<?php echo $this->Form->create('Reservation', array(
				'type' => 'post',
				'url' => 'input_confirm/',
				'inputDefaults' => array(
					'div' => false,
					'label' => false,
					'legend' => false,
				),
				'class' => 'customer-input',
			)); ?>

			<h3 class="h-type04">クレジットカード情報のご入力</h3>
			<table class="tbl_credit rent-margin-bottom">
				<tr>
					<th class="tbl_credit_th">利用可能なクレジットカード</th>
					<td>
<?php
	echo $this->Form->input('card.card', array(
		'type' => 'radio',
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
					<th class="tbl_credit_th">カード番号</br>
						<span class="tbl_credit_aside">※ハイフンなし<br />
					例）1111222233334444</span>
					</th>
					<td>
<?php
	echo $this->Form->input('card.card_number', array(
		'type' => 'text',
		'placeholder' => '**** **** **** ****',
		'id' => 'js-cardNumber',
		'class' => 'rent-input width_half',
		'maxlength' => 19,
		'autocomplete' => 'cc-number',
		'default' => !empty($inputed['card']['card_number']) ? $inputed['card']['card_number'] :'',
	));
?>
					</td>
				</tr>
				<tr>
					<th class="tbl_credit_th">カード名義</br>
						<span class="tbl_credit_aside">※英大文字<br />例）TARO YAMADA</span>
					</th>
					<td>
<?php
	echo $this->Form->input('card.owner', array(
		'type' => 'text',
		'id' => 'js-cardOwner',
		'class' => 'rent-input width_half',
		'maxlength' => 40,
		'autocomplete' => 'cc-name',
		'autocapitalize' => 'off',
		'default' => !empty($inputed['card']['owner']) ? $inputed['card']['owner'] :'',
		'onkeyup' => 'this.value=this.value.toUpperCase()'
	));
?>
					</td>
				</tr>
				<tr>
					<th class="tbl_credit_th">セキュリティコード</br>
						<span class="tbl_credit_aside">※カード裏面のご署名欄にある３桁の番号、</br>
							または表面にある４桁の番号となります</span>
					</th>
					<td>
<?php
	echo $this->Form->input('card.sec_code', array(
		'type' => 'password',
		'placeholder' => '***',
		'class' => 'rent-input',
		'id' => 'js-cvc',
		'maxlength' => 4,
		'autocomplete' => 'new-password',
		'default' => !empty($inputed['card']['sec_code']) ? $inputed['card']['sec_code'] :'',
	));
?>
					</td>
				</tr>
				<tr>
					<th class="tbl_credit_th">カード有効期限（MONTH/YEAR）</th>
					<td>
						<div class="credit_term_wrap">
<?php
	echo $this->Form->input('card.credit_expiration', array(
		'type' => 'date',
		'dateFormat' => 'M',
		'monthNames' => false,
		'class' => 'credit_term',
		'autocomplete' => 'cc-exp-month',
		'selected' => array('month' => !empty($inputed['card']['credit_expiration']['month']) ? $inputed['card']['credit_expiration']['month'] : date('m')),
	));
?>
							<i class="icm-right-arrow icon-right-arrow_down"></i>
						</div>
						/
						<div class="credit_term_wrap">
<?php
	echo $this->Form->input('card.credit_expiration', array(
		'type' => 'date',
		'dateFormat' => 'Y',
		'maxYear' => date('Y') + 50,
		'minYear' => date('Y'),
		'class' => 'credit_term',
		'autocomplete' => 'cc-exp-year',
		'selected' => array('year' => !empty($inputed['card']['credit_expiration']['year']) ? $inputed['card']['credit_expiration']['year'] : date('Y')),
	));
?>
							<i class="icm-right-arrow icon-right-arrow_down"></i>
						</div>
					</td>
				</tr>
			</table>
		</div><!-- end .box-outer -->
		<div class="contents_result_list_btnGroup rent-margin-bottom">
			<?php echo $this->Html->link('前の画面に戻る', '/mypages/',array('class' => 'btn btn_plain')); ?>
			<button type="button" class="contents_result_list_btnGroup_next btn btn_submit bg_orange" id="submitButton">お支払い</button>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>

<?php echo $this->element('loading_indicator_earth'); ?>

<?php $this->Html->script(array($econ_jsf_url, 'mypages_input'), array('inline' => false)); ?>
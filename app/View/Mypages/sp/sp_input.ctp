<?php
	echo $this->Html->script(['/js/modal_float'], ['inline' => false, 'defer' => true]);
?>
<div id="js-content" class="sp_mypages-input_page">
	<div id="sessionMessage">
		<?php if (!empty($sessionMessage)) { ?>
			<?php echo $this->element('session_message'); ?>
		<?php } ?>
	</div>
	<h2 class="title_blue_line"><span>お支払い情報入力</span></h2>
	<section class="">
		<div class="inner">
			<p class="ttxt">
				<em>下記の金額で決済します。</em>フォームにクレジットカード情報を入力して、「お支払い」ボタンを押してください。
			</p>
		</div>
	</section>
	<section class="plan_form">
		<h3 class="plan_form_title">未払金額</h3>
		<section class="plan_info_body_price price_block">
			<div class="plan_info_left">
				<p class="price_block_title">未払金額(税込)</p>
			</div>
			<div class="plan_info_right">
				<p class="price_block_price">&yen;<?php echo number_format($unpaidAmount); ?></p>
			</div>
		</section>

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


		<h3 class="plan_form_title">クレジットカード情報のご入力</h3>
		<section>
			<ul class="inner">
				<li class="plan_form_li">
					<h5><label class="input__head">利用可能なクレジットカード</label></h5>
					<?php echo $this->Form->input('card.card', array(
						'type' => 'radio',
						'label' => array(
							'class' => 'card--label'
						),
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
						'style' => 'display: none;'
					));?>
				</li>
				<li class="plan_form_li">
					<h5><label for="js-cardNumber">カード番号</label></h5>
					<span>※ハイフンなし</span>
					<?php echo $this->Form->input('card.card_number', array(
						'type' => 'tel',
						'required' => true,
						'id' => 'js-cardNumber',
						'class' => 'plan_form_input',
						'placeholder' => '例）1111222233334444',
						'maxlength' => 19,
						'autocomplete' => 'cc-number',
						'default' => !empty($inputed['card']['card_number']) ? $inputed['card']['card_number'] :'',
					)); ?>
				</li>
				<li class="plan_form_li">
					<h5><label class="input__head">カード名義<span>※英大文字</span></label></h5>
					<?php echo $this->Form->input('card.owner', array(
						'type' => 'text',
						'required' => true,
						'id' => 'js-cardOwner',
						'class' => 'plan_form_input',
						'placeholder' => '例）TARO YAMADA',
						'maxlength' => 40,
						'autocomplete' => 'cc-name',
						'default' => !empty($inputed['card']['owner']) ? $inputed['card']['owner'] :'',
						'onblur' => 'this.value=this.value.toUpperCase()'
					)); ?>
				</li>
				<li class="plan_form_li">
					<h5><label class="input__head">セキュリティコード</label></h5>
					<?php echo $this->Form->input('card.sec_code', array(
						'type' => 'password',
						'required' => true,
						'placeholder' => '***',
						'class' => 'plan_form_input',
						'id' => 'js-cvc',
						'maxlength' => 4,
						'autocomplete' => 'new-password',
						'default' => !empty($inputed['card']['sec_code']) ? $inputed['card']['sec_code'] :'',
					)); ?>
					<div class="-notes">
						<?php echo $this->element('sp_modal_credit_code'); ?>
					</div>
				</li>
				<li class="plan_form_li">
					<h5><label class="input__head">カード有効期限（MONTH/YEAR）</label></h5>
					<div class="plan_form_col2">月(Month)
							<?php echo $this->Form->input('card.credit_expiration', array(
								'type' => 'date',
								'dateFormat' => 'M',
								'monthNames' => false,
								'class' => 'plan_form_input',
								'autocomplete' => 'cc-exp-month',
								'selected' => array('month' => !empty($inputed['card']['credit_expiration']['month']) ? $inputed['card']['credit_expiration']['month'] : date('m')),
							));
							?>
					</div>
					<div class="plan_form_col2">年(Year)
							<?php echo $this->Form->input('card.credit_expiration', array(
								'type' => 'date',
								'dateFormat' => 'Y',
								'maxYear' => date('Y') + 50,
								'minYear' => date('Y'),
								'class' => 'plan_form_input',
								'autocomplete' => 'cc-exp-year',
								'selected' => array('year' => !empty($inputed['card']['credit_expiration']['year']) ? $inputed['card']['credit_expiration']['year'] : date('Y')),
							));
							?>
					</div>
				</li>
			</ul>
		</section>

		<div class="ac inner mb20px">
			<p class="btn-submit mb20px">
				<button type="button" id="submitButton" class="btn-type-primary" style="width: 100%;">お支払い</button>
			</p>
			<?php echo $this->Html->link('戻る', '/mypages/', array('class'=>'btn-type-sub')); ?>
		</div>
	</section>

	<?php echo $this->Form->end(); ?>
</div><!-- end #js-content -->

<?php echo $this->element('loading_indicator_earth'); ?>

<?php $this->Html->script(array($econ_jsf_url, 'mypages_input'), array('inline' => false)); ?>


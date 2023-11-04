<?php $this->Html->script(array('reservationLogin'), array('inline' => false)); ?>

<div id="js-content" class="mypage sp_mypages-login_page">

<?php 
	if (!empty($errorTxt)) {
?>
	<div class="session_message_wrap">
		<div class="session_message">
			<i class="icm-warning"></i>
			<div class="session-message-text"><?php echo $errorTxt; ?></div>
		</div>
	</div>
<?php
	}
?>

	<section class="-inner">
		<h2 class="headline-large-sp"><span>レンタカーの予約確認</span></h2>
		<div class="content_section">
			日本国内のレンタカーの予約状況の照会・変更・取消ができます。<br>
			予約番号および、予約時に登録した電話番号を入力し、「予約確認」ボタンを押してください。
<?php 
	if(!IS_PRODUCTION) { // 海外検索導入
?>
			<div class="notification">
				<div class="-icon-wrapper">
					<i class="icm-info-button-fill"></i>
				</div>
				<div class="-text-wrapper">
					海外のレンタカーをご予約のお客様は
					<a href="/car-rental/mypage/login">海外レンタカー予約確認</a>
					へお進みください。
				</div>
			</div>
<?php
	} // !IS_PRODUCTION
?>
		</div>

		<div class="content_section">
			<?php echo $this->Form->create('Reservation', array(
				'url' => '/mypages/login/',
				'name' => 'quote',
				'type' => 'post',
				'novalidate' => true,
				'inputDefaults' => $inputDefaults)); ?>

				<div class="input-form">
					<p class="input-form-label -required">お客様の予約番号（半角英数字のみ）</p>
					<?php echo $this->Form->input('reservation_key', array('id' => 'js-rsv-num','class' => 'field','placeholder' => '例）RC00000012345')); ?>
				</div>

				<div class="input-form">
					<p class="input-form-label -required">登録した電話番号（半角数字のみ、ハイフン不要）</p>
					<?php echo $this->Form->input('tel', array('id' => 'js-tel','class' => 'field','placeholder' => '例）09012345678')); ?>
				</div>

				<div id="loginBox">
					<?php echo $this->Form->submit('予約確認', array(
						'name' => 'login',
						'accesskey' => 1,
						'div' => false,
						'class' => 'btn-type-primary',
						'id' => 'js-btn-see-this-rsv')); ?>
				</div>
			<?php echo $this->Form->end(); ?>
		</div>

<?php 
	if(!IS_PRODUCTION) { // 海外検索導入
?>
		<div class="content_section">
			<p class="button-label">海外のレンタカーをご予約のお客様</p>
			<span class="button-label-sub">海外のレンタカーの予約状況を確認する方はこちら</span>
			<a href="/car-rental/mypage/login" class="btn-type-link">海外レンタカー予約確認</a>
		</div>
<?php
	} // !IS_PRODUCTION
?>
		<div class="content_section">
			<p class="button-label">マイページへログイン</p>
			<span class="button-label-sub">skyticket会員のマイページからはすべての予約をご確認いただけます。</span>
			<a href="/user/login.php" class="btn-type-link">ログイン</a>
		</div>
	</section>
</div>

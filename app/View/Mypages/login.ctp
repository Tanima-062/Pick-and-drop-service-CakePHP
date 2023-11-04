<?php $this->Html->script(array('reservationLogin'), array('inline' => false)); ?>

<?php
	echo $this->Html->script(['/js/modal_float'], ['inline' => false, 'defer' => true]);
?>

<div id="renewal" class="login-edit login wrap contents clearfix mypages-login_page">

	<div class="topicpath">
		<ul>
			<li><a href="/"><?=__('トップ');?></a></li>
			<li><a href="" class="current"><?=__('予約確認');?></a></li>
		</ul>
	</div><!-- topicpath End -->

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

	<section>
		<h2 class="maintitle">skyticket - 予約確認</h2>
		<p class="subtext">ご利用のサービスをお選びください。<br />
		会員・ゲストを問わず全てのご予約内容の確認が可能です。</p>
	</section>
	<section>
		<?php echo $this->Form->create('Reservation', array(
			'url' => '/mypages/login/',
			'name' => 'quote',
			'type' => 'post',
			'novalidate' => true,
			'inputDefaults' => $inputDefaults)); ?>

		<div class="bookingLogin">
			<div class="bookingLogin_header">
				<ul>
					<li><a href="/user/search.php#flight" class="flight"><span><?=__('国内航空券');?><br>/<?=__('海外航空券');?></span></a></li>
					<li><a href="/user/search.php#hotel" class="hotel"><span>ホテル</span></a></li>
					<li><a href="/user/search.php#domestic_tour" class="domestic_tour"><span>国内ツアー</span></a></li>
					<li><a href="/user/search.php#dp" class="dp"><span>国内/海外<br>航空券+ホテル</span></a></li>
					<li class="on"><a href="javascript:void(0)" class="rentacar"><span>レンタカー</span></a></li>
					<li><a href="/bus/mypage/login" class="bus"><span>高速バス</span></a></li>
					<li><a href="/ferry/mypages/login/" class="ferry"><span>フェリー</span></a></li>
					<li><a href="/gourmet/mypage/login/" class="gorumet"><span>グルメ</span></a></li>
					<li>
						<a href="javascript:void(0);" class="js-modalf_open overseas_tour"><span>海外ツアー</span></a>
						<?php echo $this->element('modal_mypages_overseas_tour'); ?>
					</li>
				</ul>
			</div>

			<div class="bookingLogin_body">
				<div class="bookingLogin_form">
					<div class="form-title-wrap">
						<h3 class="form-title"><?=__('レンタカーの予約確認');?></h3>
						<span>（<?=__('予約番号で確認');?>&nbsp;-&nbsp;<?=__('会員/非会員共通');?>）</span>
					</div>
					<div class="input-form">
						<p class="label">お客様の予約番号（半角英数字のみ）</p>
						<?php echo $this->Form->input('reservation_key', array('class' => 'field','id' => 'js-rsv-num','placeholder' => '例）RC00000012345')); ?>
					</div>
					<div class="input-form">
						<p class="label">登録した電話番号（半角数字のみ、ハイフン不要）</p>
						<?php echo $this->Form->input('tel', array('class' => 'field','id' => 'js-tel','placeholder' => '例）09012345678')); ?>
					</div>

					<?php echo $this->Form->submit('予約確認', array(
						'name' => 'login',
						'accesskey' => 1,
						'div' => false,
						'class' => 'btn-type-primary',
						'id' => 'js-btn-see-this-rsv' )); ?>
				</div>

				<div class="bookingLogin_toMember">
					<h3><?=__('マイページ');?><?=__('ログイン');?></h3>
					<p><?=__('ログインして予約状況を確認する方はこちら');?></p>
					<a href="/user/login.php">
						<span>
							<i class="icm-user-shape" aria-hidden="true"></i>
							<?=__('ログイン');?>
						</span>
						<i class="icm-right-arrow"></i>
					</a>
				</div>
			</div>

			<div class="bookingLogin_notice">
				<p>
					※「海外WiFiレンタル」「旅行保険」 「プレミアム」各サービスのご予約確認・変更及びキャンセルは、こちらからでは行えません。<br>各提携サービスサイトまたは予約完了メールよりお願いいたします。
				</p>
			</div>
		</div> <!-- bookingLogin END -->

		<?php echo $this->Form->end(); ?>
	</section>

	<section class="link_list">	
		<ul>
			<li>
				<a href="https://support.skyticket.jp/hc/ja/categories/4403654208025">
					<span>
						<i class="icm-question-fill" aria-hidden="true"></i>
						<?=__('よくあるご質問');?>
					</span>
					<i class="icm-right-arrow"></i>
				</a>
			</li>
			<li>
				<a href="https://support.skyticket.jp/hc/ja/sections/4406204444697">
					<span>
						<i class="icm-blocked" aria-hidden="true"></i>
						<?=__('キャンセルについて');?>
					</span>
					<i class="icm-right-arrow"></i>
				</a>
			</li>
			<li>
				<a href="https://support.skyticket.jp/hc/ja/articles/4405210627993">
					<span>
						<i class="icm-info-button-fill" aria-hidden="true"></i>
						<?=__('ご利用方法');?>
					</span>
					<i class="icm-right-arrow"></i>
				</a>
			</li>
		</ul>
	</section>

</div><!-- /#renewal -->

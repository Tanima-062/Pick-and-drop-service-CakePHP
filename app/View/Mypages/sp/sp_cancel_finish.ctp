<div id="js-content" class="sp_mypages-cancel_finish_page">
	<h2 class="title_blue_line"><span>予約キャンセルの完了</span></h2>



	<section class="plan_form">
		<div class="inner">
			<p class="ttxt">予約のキャンセルを受け付けました。</p>
		</div>
		<div class="inner">
			<p class="ttxt">
			お客様のメールアドレスに、予約キャンセルメールを自動送信いたしましたのでご確認をお願いします。<br>
				またのご利用をお持ちしております。
			</p>
		</div>
		<div class="inner">
			<div class="reserve_completed_num-wrap">
				<p>取消済みの予約番号</p>
			 	<div class="reserve_completed_num">
			 		<p class="color_red"><?php echo $this->data['Reservation']['reservation_key']; ?></p>
		</div>
		</div>
		</div>
	</section>

	<p class="btn-submit ac"><?php echo $this->Html->link('レンタカー予約TOPへ戻る', '/'); ?></p>
</div>

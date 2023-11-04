<div id="renewal" class="login-edit login wrap contents clearfix mypages-cancel_finish_page">
<?php
	echo $this->element('progress_bar'); 
?>
	<h2 class="h-type03">予約キャンセルの完了</h2>
	<div class="step-box">
		<div class="check-blue margin-btm30">
			<p>ご利用ありがとうございました</p>
		</div>
		<p class="ttxt topbtm30 complete_panel_message margin-btm30 text_center">
			予約のキャンセルを受け付けました。<br>
			またのご利用をお持ちしております。
		</p>
		<div class="box-outer text_center">
			<div class="">
				<p class="blue-txt">メールをご確認ください</p>
				<p class="txt">
					この度はご利用いただき誠にありがとうございます。<br>
					お客様のメールアドレスに、予約キャンセルメールを自動送信いたしましたのでご確認をお願いします。
				</p>
			</div>
		</div>
		<div class="frame complete_panel_reserve text_center">
			<p>取消済みの予約番号</p>
			<p class="complete_panel_reserve_number rent-margin-bottom"><?php echo $this->data['Reservation']['reservation_key']; ?></p>
			<p>上記の予約を取り消しました。</p>
		</div>
	</div><!-- end .step-box -->

	<div class="rent-margin-bottom btn btn_cancel">
		<?php echo $this->Html->link('レンタカー予約TOPへ戻る', '/'); ?>
	</div>
</div>
<div class="mypage mypage__completion">
	<h2 class="title_blue_line"><span>お支払い完了</span></h2>
	<article class="inner">
		<p class="mb20px">お支払いを受け付けました。</p>

		<p class="ac mb20px">
			<?php echo $this->Html->link('予約内容を確認する', '/mypages/login/?hash='.$result['Reservation']['reservation_hash'], array('class' => 'btn bg_orange')); ?>
		</p>
	</article>
</div>
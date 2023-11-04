<div class="login wrap clearfix mypages-completion_page">
<?php
	echo $this->element('progress_bar'); 
?>
	<h2 class="h-type03">お支払い完了</h2>
	<article>
		<p>お支払いを受け付けました。</p>

		<div class="unpaid_btn_wrap">
			<?php echo $this->Html->link('予約内容を確認する', '/mypages/login/?hash='.$result['Reservation']['reservation_hash'], array('class' => 'btn btn_submit rent-margin-bottom-important')); ?>
		</div>
	</article>
</div>
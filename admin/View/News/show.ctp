<div class="news">
	<h3>お知らせ 詳細</h3>
	<hr>
	<?php if(!empty($message)): ?>
	<div style="border:1px solid #ccc;padding:20px">
		<h4><?php echo $message['Message']['title'] ?></h4>
		<div style="float:right">更新日時 : <?php echo date("Y年m月d日 H:i",strtotime($message['Message']['modified'])) ?></div><br>
		<hr>
		<p>
		<?php /*echo h(date("Y年m月d日 H:i",strtotime($message['Message']['from_time']))); ?> 〜 <?php echo h(date("Y年m月d日 H:i",strtotime($message['Message']['to_time'])));*/ ?>
		</p>
		<p>
		<?php echo nl2br($message['Message']['message']); ?>
		</p>
	</div>
	<?php endif; ?>
</div>
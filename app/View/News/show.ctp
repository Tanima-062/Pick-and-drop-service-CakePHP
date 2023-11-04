<?php if(!empty($message)): ?>
<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
	<section class="contents-wrap">
		<h2 class="news-ttl"><?php echo h($message['Message']['title']) ?></h2>
		<hr>
		<p><?php echo date("Y/m/d",strtotime($message['Message']['modified'])) ?></p>
		<hr>
		<?php echo nl2br($message['Message']['message']); ?>
	</section>
</div>
<?php endif; ?>

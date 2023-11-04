<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
<section class="contents-wrap">
	<h2>レンタカーのお知らせ バックナンバー</h2>
	<hr>
	<?php if(!empty($messages)): ?>
	<ul class="st-list">
		<?php foreach($messages as $message): ?>
		<li>
			<?php //echo h(date("Y年m月d日 H:i",strtotime($message['Message']['modified']))); ?>
			<?php echo $this->Html->link($message['Message']['title'],'/news/show/'.$message['Message']['id']);?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</section>
</div>

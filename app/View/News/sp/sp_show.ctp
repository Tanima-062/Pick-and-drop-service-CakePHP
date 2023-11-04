<?php
echo $this->Html->css(array('sp/jquery-ui'),null,array('inline'=>false));
echo $this->Html->script(array('sp/jquery-ui.min', 'sp/jquery.ui.datepicker-ja.min'),array('inline'=>false));
?>
<section class="contents-wrap">
	<h2 class="news-ttl">お知らせ 詳細</h2>
	<h3 class="news-ttl"><?php echo h($message['Message']['title']) ?></h3>
	<div style="float:right">更新日時 : <?php echo date("Y年m月d日 H:i",strtotime($message['Message']['modified'])) ?></div><br>
	<hr>
	<p>
	<?php /*echo h(date("Y年m月d日 H:i",strtotime($message['Message']['from_time']))); ?> 〜 <?php echo h(date("Y年m月d日 H:i",strtotime($message['Message']['to_time']))); */?>
	</p>
	<p>
	<?php echo nl2br($message['Message']['message']); ?>
	</p>
</section>

<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery-ui.css">
<script type="text/javascript" src="/rentacar/js/sp/jquery.ui.datepicker-ja.min.js" async></script>

<section class="contents-wrap">
	<h2 class="news-ttl">レンタカーのお知らせ バックナンバー</h2>
	<hr />
<?php 
	if(!empty($messages)){ 
?>
	<ul class="st-list">
<?php
		foreach($messages as $message){
?>
		<li>
			<?php //echo h(date("Y年m月d日 H:i",strtotime($message['Message']['modified']))); ?>
			<?php echo $this->Html->link($message['Message']['title'],'/news/show/'.$message['Message']['id']); ?>
		</li>
<?php 
		} 
?>
	</ul>
<?php 
	}
?>
</section>

<div class="news">
	<h3>お知らせ バックナンバー</h3>
	<hr>
	<?php if(!empty($messages)): ?>
		<table class="table table-bordered">
		<tr class="success">
			<th style="width: 15%">日時</th>
			<th>内容</th>
		</tr>
		
		<?php foreach($messages as $message): ?>
		<tr>
			<td>
			<?php echo h(date("Y年m月d日 H:i",strtotime($message['Message']['modified']))); ?>
			</td>
			<td>
			<?php 
			echo $this->Html->link($message['Message']['title'],'/news/show/'.$message['Message']['id']);
			?>
			</td>
		<tr>
		<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>
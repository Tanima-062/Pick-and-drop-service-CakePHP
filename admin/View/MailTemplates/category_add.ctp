<div class="areas form">
	<h3> <?php echo __('メールテンプレートカテゴリ 新規追加'); ?> </h3>
	<?php echo $this->Form->create('MailTemplateCategory'); ?>
	<table class="table table-bordered">
		<tr>
			<th>カテゴリ名</th>
			<td>
				<?php echo $this->Form->input('name',array('label'=>false, 'required' => true)); ?>
			</td>
		</tr>
	</table>
	<?php echo $this->Html->link('メールテンプレート一覧へ戻る', array('action' => 'index'), array('class' => 'btn btn-info'));?>
	<div class="right">
	<?php
	echo $this->Form->submit('登録',array('class'=>'btn btn-success'));
	echo $this->Form->end();
	?>
	</div>
</div>
<script>
$(function(){
	$("input").on("keydown", function(e) {
		if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
			return false;
		} else {
			return true;
		}
	});
});
</script>

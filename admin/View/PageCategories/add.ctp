<div class="pageCategories form">
	<?php echo $this->Form->create('PageCategory'); ?>
	<h3>ページカテゴリー追加</h3>
	<table class="table table-bordered">
		<tr>
			<th class="span3">ページカテゴリー名</th>
			<td>
				<?php
				echo $this->Form->input('name',array(
						'label'=>false
				));
				?>
			</td>
		</tr>
	</table>
	<div class="right">
		<?php echo $this->Form->submit('登録する',array('class'=>'btn btn-success')); ?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>


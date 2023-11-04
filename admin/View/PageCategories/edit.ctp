<div class="pageCategories form">
<?php echo $this->Form->create('PageCategory'); ?>
	<h3>ページカテゴリー編集</h3>
	<table class="table table-bordered">
	<?php echo $this->Form->input('id',array('label'=>false)); ?>
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
<?php echo $this->Form->submit('編集',array('class'=>'btn btn-success')); ?>
<?php echo $this->Form->end(); ?>
	</div>
</div>


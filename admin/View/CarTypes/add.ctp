<div class="carTypes form">
<?php echo $this->Form->create('CarType',array('inputDefaults'=>array('label'=>false))); ?>
	<h3>車両タイプ追加</h3>
	<table class="table table-bordered">
		<tr>
			<th>車両タイプ</th>
			<td> <?php echo $this->Form->input('name'); ?> </td>
		</tr>

		<tr>
			<th>法定定員</th>
			<td> <?php echo $this->Form->input('capacity'); ?> </td>
		</tr>

		<tr>
			<th>説明</th>
			<td>
			<?php echo $this->Form->input('description'); ?>
			</td>
		</tr>

		<tr>
			<th>トラベルコID</th>
			<td><?php echo $this->Form->input('travelko_id', array('type' => 'text')); ?></td>
		</tr>
	</table>
	<div class="right">
	<?php
	echo $this->Form->submit('登録',array('class'=>'btn btn-success'));
	echo $this->Form->end();
	?>
	</div>
</div>

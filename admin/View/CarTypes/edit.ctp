<div class="carTypes form">
<?php
echo $this->Form->create('CarType',array('inputDefaults'=>array('label'=>false)));
echo $this->Form->input('id');
?>
	<h3>車両タイプ修正</h3>
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
		<tr>
		  <th>公開/非公開</th>
		  <td>
		    <?php echo $this->Form->input('delete_flg',array('options'=>$deleteFlgOptions));?>
		  </td>
		</tr>
	</table>
	<div class="right">
	<?php
	echo $this->Form->submit('修正',array('class'=>'btn btn-success'));
	echo $this->Form->end();
	?>
	</div>
</div>

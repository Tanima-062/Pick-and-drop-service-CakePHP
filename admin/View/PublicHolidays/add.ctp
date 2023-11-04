<div class="">
	<div class="span6">
		<?php echo $this->Form->create('PublicHoliday', array('class' => 'form-horizontal'));?>
				<h3>祝日追加</h3>
			<table class="table table-bordered">
			  <tr>
			    <th>祝日名</th>
			    <td>
				<?php echo $this->Form->input('name',array('label'=>false,'div'=>false)); ?>
				  </td>
				</tr>
				<tr>
				    <th>日にち</th>
				    <td>
				    <?php echo $this->Form->input('date',array('label'=>false,'div'=>false, 'dateFormat' => 'YMD', 'monthNames' => false,'class'=>'span3')); ?>
				    </td>
				</tr>
			</table>
		<?php echo $this->Form->submit('登録',array('class'=>'btn btn-warning','div'=>false));?>
		<?php echo $this->Form->end();?>
	</div>

</div>
<div class="row-fluid span8">
	<h3>乗捨エリア編集</h3>
	<?php echo $this->Form->create('DropOffArea', array('class' => 'form-horizontal','inputDefaults'=>array('label'=>false)));?>
	<table class="table table-bordered control-group">
		<tr>
			<th class="alert-success">乗捨エリア名</th>
			<td>
			<?php
				echo $this->Form->hidden('id');
				echo $this->Form->input('name', array(
					'required' => 'required',
					'helpInline' => '<span class="label label-important">' . __('Required') . '</span>&nbsp;')
				);
			?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">公開・非公開</th>
			<td>
				<?php
				echo $this->Form->input('delete_flg',array('type'=>'select','options'=>$deleteFlgOptions));
				?>
			</td>
		</tr>
	</table>

	<div class="left">
		<?php echo $this->Html->link('乗捨てエリア一覧へ戻る','/drop_off_areas/',array('class'=>'btn btn-warning'));?>
	</div>
	<div class="right">
		<?php echo $this->Form->submit('更新',array('class'=>'btn btn-success right'));?>
	</div>
	<?php echo $this->Form->end();?>

</div>
<div class="row-fluid span8">
	<h3>新幹線編集</h3>
	<?php echo $this->Form->create('BulletTrainArea', array('class' => 'form-horizontal','inputDefaults'=>array('label'=>false,'div'=>false)));?>
	<table class="table table-bordered">
		<tr>
			<th class="alert-success">路線名</th>
			<td>
			<?php
				echo $this->Form->hidden('id');
				echo $this->Form->input('name', array( 'required' => 'required','div'=>false));
			?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">公開・非公開</th>
			<td>
				<?php
				echo $this->Form->input('delete_flg',array('type'=>'select','options'=>$deleteFlgOptions,'div'=>false));
				?>
			</td>
		</tr>
	</table>

	<span class="left">
		<?php echo $this->Html->link('新幹線一覧へ戻る','/bullet_train_areas/',array('class'=>'btn btn-info'));?>
	</span>
	<span class="right">
		<?php echo $this->Form->submit('編集する',array('class'=>'btn btn-success right','div'=>false));?>
	</span>
	<?php echo $this->Form->end();?>

</div>
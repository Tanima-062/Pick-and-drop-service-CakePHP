<div class="automakers form">
<?php echo $this->Form->create('Automaker',array('inputDefaults'=>array('label'=>false))); ?>
		<h3>自動車メーカー編集</h3>
		<?php echo $this->Form->input('id');?>
		<table class="table table-bordered">
			<tr>
				<th class="span3">自動車メーカー名</th>
				<td>
						<?php echo $this->Form->input('name',array('required' => true)); ?>
				</td>
			</tr>
			<tr>
				<th>削除フラグ</th>
				<td><?php echo $this->Form->input('delete_flg'); ?></td>
			</tr>
		</table>
		<?php echo $this->Form->submit('変更を保存する',array('class'=>'btn btn-success'))?>
		<?php echo $this->Form->end(); ?>
</div>
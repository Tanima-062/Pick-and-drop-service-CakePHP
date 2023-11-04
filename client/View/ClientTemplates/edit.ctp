<div class="ClientTemplates form">
<?php echo $this->Form->create('ClientTemplate'); ?>
		<h3>返信テンプレート編集</h3>
		<?php echo $this->Form->input('id',array('label'=>'ID','div'=>false));?>
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<td class="span2">テンプレート名</td>
				<td class="span10"><?php echo $this->Form->input('name',
						array('label'=>false,'div'=>false,'style'=>'width:95%;'));?></td>
			</tr>
			<tr>
				<td>内容</td>
				<td><?php echo $this->Form->input('template',
						array('label'=>false,'div'=>false,'style'=>'width:95%;height:500px;'));?></td>
			</tr>

			<tr>
				<td class="span2">削除</td>
				<td class="span10">
					<?php echo $this->Form->input('delete_flg', array('label'=>false,'div'=>false,'style'=>'width:95%;'));?>
				</td>
			</tr>
			<?php echo $this->Form->input('id'); ?>
		</table>
		<?php echo $this->Form->submit('変更を保存する',array('class'=>'btn btn-success'))?>
		<?php echo $this->Form->end(); ?>
		<?php echo $this->Html->link(__('戻る'), array('action' => 'index'),array('class'=>'btn btn-warning')); ?>

</div>


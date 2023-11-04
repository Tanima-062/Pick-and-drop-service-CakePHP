<div class="areas form">
	<h3> エリア編集</h3>
	<?php
	echo $this->Form->create('Area',array('inputDefaults'=>array('label'=>false)));
	echo $this->Form->hidden('id');
	?>
	<table class="table table-bordered">
		<tr>
			<th>エリア名</th>
			<td>
				<?php echo $this->Form->input('name',array('label'=>false, 'required' => true)); ?>
			</td>
		</tr>
		<tr>
			<th>都道府県</th>
			<td>
				<?php echo $this->Form->input('prefecture_id',array('label'=>false,'options'=>$prefectureList)); ?>
			</td>
		</tr>
		<tr>
			<th>リンク用URL</th>
			<td>
				<?php echo $this->Form->input('area_link_cd',array('label'=>false, 'pattern' => Constant::PATTERN_LINKCD, 'required' => true)); ?>
			</td>
		</tr>
		<tr>
			<th>ソート</th>
			<td>
			<?php
				echo $this->Form->input('sort',array('label'=>false));
				?>
			</td>
		</tr>
	</table>
	<div class="right">
	<?php
	echo $this->Form->submit('保存',array('class'=>'btn btn-success'));
	echo $this->Form->end();
	?>
	</div>
</div>
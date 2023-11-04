<div class="equipment form">
<?php echo $this->Form->create('Equipment', array('enctype' => 'multipart/form-data', 'inputDefaults' => array('label' => false))); ?>
	<h3>装備マスタ編集</h3>
	<table class="table table-bordered">
		<?php echo $this->Form->input('id'); ?>
		<tr>
			<th class="span3">カテゴリ</th>
			<td>
				<?php echo $this->Form->input('option_category_id', array('empty' => '---', 'required' => true)); ?>
			</td>
		</tr>
		<tr>
			<th class="span3">装備名</th>
			<td>
				<?php echo $this->Form->input('name', array('required' => true)); ?>
			</td>
		</tr>
		<tr>
			<th>説明</th>
			<td>
				<?php echo $this->Form->input('description', array('class' => 'span10')); ?>
			</td>
		</tr>
		<tr>
			<th>公開フラグ</th>
			<td>
				<?php echo $this->Form->input('is_published', array('type' => 'select', 'options' => $isPublishedOptions)); ?>
			</td>
		</tr>
		<tr>
			<th>削除フラグ</th>
			<td>
				<?php echo $this->Form->input('delete_flg'); ?>
			</td>
		</tr>
	</table>
<?php echo $this->Form->submit('変更を保存する', array('class' => 'btn btn-success')) ?>
<?php echo $this->Form->end(); ?>
</div>
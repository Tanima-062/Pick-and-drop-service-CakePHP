<div class="equipment form">
<?php echo $this->Form->create('Equipment', array('enctype' => 'multipart/form-data', 'inputDefaults' => array('label' => false))); ?>
	<h3>装備マスタ新規追加</h3>
	<table class="table table-bordered">
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
				<?php echo $this->Form->input('description', array('class' => 'span9')); ?>
			</td>
		</tr>
	</table>
<?php echo $this->Form->submit('新規登録', array('class' => 'btn btn-success')) ?>
<?php echo $this->Form->end(); ?>
</div>
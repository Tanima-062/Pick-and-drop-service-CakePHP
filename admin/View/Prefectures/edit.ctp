<div class="prefectures form span8">
	<h3>都道府県編集</h3>
	<?php
	echo $this->Form->create('Prefecture',array('inputDefaults'=>array('label'=>false)));
	echo $this->Form->input('id');
	?>
	<table class="table table-bordered">
		<tr>
			<th>都道府県名</th>
			<td>
				<?php echo $this->Form->input('name', array('required' => true)); ?>
			</td>
		</tr>
		<tr>
			<th>リンク用URL(地方)</th>
			<td>
				<?php echo $this->Form->input('region_link_cd', array('pattern' => Constant::PATTERN_IDPASS, 'required' => false)); ?>
			</td>
		</tr>
		<tr>
			<th>リンク用URL(都道府県)</th>
			<td>
				<?php echo $this->Form->input('link_cd', array('pattern' => Constant::PATTERN_IDPASS, 'required' => false)); ?>
			</td>
		</tr>
		<tr>
			<th>レコメンドランダムフラグ</th>
			<td>
				<?php echo $this->Form->input('recommend_random_flg', array('type' => 'checkbox', 'required' => false)); ?>
				<code>※チェックをつけるとその都道府県のレコメンドがランダム表示になります。</code>
			</td>
		</tr>
	</table>
	<div class="right">
	<?php echo $this->Form->submit('編集する',array('class'=>'btn btn-success'));?>
	<?php echo $this->Form->end(); ?>
	</div>
</div>




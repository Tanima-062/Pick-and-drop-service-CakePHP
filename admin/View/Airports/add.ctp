<div class="landmarks">
	<?php echo $this->Form->create('Airport', array('inputDefaults' => array('label' => false))); ?>
	<h3>空港追加</h3>
	<table class="table table-bordered">
		<tr>
			<th>都道府県</th>
			<td><?php echo $this->Form->input('prefecture_id', array('options' => $prefectureList)); ?></td>
		</tr>
		<tr>
			<th>空港名</th>
			<td>
				<?php echo $this->Form->input('name', array('required' => true)); ?>
			</td>
		</tr>
		<tr>
			<th>略称</th>
			<td>
				<?php echo $this->Form->input('short_name', array('required' => true)); ?>
				<span class="text-error">※都道府県ページで使用されます。</span>
			</td>
		</tr>
		<tr>
			<th>トラベルコID</th>
			<td><?php echo $this->Form->input('travelko_id', array('type' => 'text')); ?></td>
		</tr>
		<tr>
			<th>公開/非公開</th>
			<td>
				<?php echo $this->Form->input('delete_flg', array('type' => 'input', 'default' => 0, 'options' => $deleteFlgOptions)); ?>
			</td>
		</tr>
		<tr>
			<th>ソート</th>
			<td>
				<?php echo $this->Form->input('sort', array('required' => false)); ?>
			</td>
		</tr>
	</table>
	<?php echo $this->Form->submit('新規登録', array('class' => 'btn btn-success ')); ?>
	<?php echo $this->Form->end(); ?>
</div>
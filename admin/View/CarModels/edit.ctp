<div class="carModels form">
    <?php echo $this->Form->create('CarModel', array('enctype' => 'multipart/form-data', 'inputDefaults' => array('label' => false))); ?>
    <?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
		<h3>車種マスタ編集</h3>
		<table class="table table-bordered">
			<tr>
				<th class="span3">自動車メーカーID</th>
				<td><?php echo $this->Form->input('automaker_id',array('options' => $automakerList)); ?> </td>
		</tr>
		<tr>
			<th>車種名</th>
			<td>
			<?php echo $this->Form->input('name',array('required' => true)); ?>
			</td>
	</tr>
	<tr>
		<th>スーツケース</th>
		<td>
		<?php echo $this->Form->input('trunk_space',array('required' => true, 'min' => 0, 'max' => 5)); ?>
		</td>
	</tr>
	<tr>
		<th>ゴルフバッグ</th>
		<td>
		<?php echo $this->Form->input('golf_bag',array('required' => true, 'min' => 0, 'max' => 5)); ?>
		</td>
	</tr>
	<tr>
		<th>排気量</th>
		<td>
			<?php echo $this->Form->input('displacement',array('required' => true)); ?>
		</td>
	</tr>
	<tr>
		<th>法定定員</th>
		<td>
			<?php echo $this->Form->input('capacity',array('required' => true)); ?>
		</td>
	</tr>
	<tr>
		<th>推奨人員</th>
		<td>
		<?php echo $this->Form->input('recommended_capacity',array('required' => true)); ?>
		</td>
	</tr>
	<tr>
		<th>推奨荷物の数</th>
		<td>
	<?php
		echo $this->Form->input('package_num',array( 'required' => true));
		?>
		</td>
	</tr>
	<tr>
		<th>燃費</th>
		<td>
		<?php
		echo $this->Form->input('mileage',array('required' => true));
		?>
		</td>
	</tr>
	<tr>
		<th>ドア数</th>
		<td>
		<?php
		echo $this->Form->input('door',array('required' => true));
		?>
		</td>
	</tr>
	<tr>
		<th>
			画像　例：横幅300pxに変換
		</th>
		<td>
		<?php echo $this->Form->file('image_relative_url',array('label'=>'画像相対URL')); ?>
		</td>
	</tr>
	<tr>
		<th>説明</th>
		<td>
		<?php echo $this->Form->input('description',array('class' => 'span4', 'required' => true)); ?>
		</td>
	</tr>
	<tr>
		<th>削除フラグ</th>
		<td>
		<?php echo $this->Form->input('delete_flg'); ?>
		</td>
	</tr>
	</table>
	<?php echo $this->Form->submit('変更を保存する',array('class'=>'btn btn-success'))?>
	<?php echo $this->Form->end(); ?>
</div>

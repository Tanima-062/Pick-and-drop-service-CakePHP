<div class="row-fluid span8">
	<h3>乗捨料金追加</h3>
	<?php echo $this->Form->create('DropOffAreaRate', array('class' => 'form-horizontal','inputDefaults'=>array('label'=>false,'div'=>false)));?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<table class="table table-bordered control-group">
		<tr>
			<th class="alert-success">出発エリア</th>
			<td>
			<?php
				echo $this->Form->input('rent_drop_off_area_id',array('options'=>$dropOfAreaList));
			?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">返却エリア</th>
			<td>
			<?php
			echo $this->Form->input('return_drop_off_area_id',array('options'=>$dropOfAreaList));
			?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">逆方向のエリアの乗捨料金も同時に登録する</th>
			<td>
			<?php
			echo $this->Form->input('with_reverse',array('type'=>'checkbox','checked'=>true));
			?>
			</td>
		</tr>
		<?php foreach ($dropOffPricePatternList as $k => $v) {
			$price = ($k > 1) ? 'price' . $k : 'price';
		?>
		<tr>
			<th class="alert-success"><?=$v?></th>
			<td>
			<?php echo $this->Form->input($price, array('empty' => false, 'min' => 0, 'required')); ?> 円
			</td>
		</tr>
		<?php } ?>
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
		<?php echo $this->Html->link('エリア区間ごとの乗捨料金一覧へ戻る',$referer,array('class'=>'btn  btn-warning'));?>
	</div>
	<div class="right">
		<?php echo $this->Form->submit('登録',array('class'=>'btn btn-success right'));?>
	</div>
	<?php echo $this->Form->end();?>

</div>
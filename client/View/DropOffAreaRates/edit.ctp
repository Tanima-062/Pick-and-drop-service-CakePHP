<div class="row-fluid span8">
	<h3>乗捨料金編集</h3>
	<?php echo $this->Form->create('DropOffAreaRates', array( 'class' => 'form-horizontal','inputDefaults'=>array('label'=>false,'div'=>false)));?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<table class="table table-bordered control-group">
		<tr>
			<th class="alert-success">出発エリア</th>
			<td>
			<?php
			 echo $dropOfAreaList[$this->request->data['DropOffAreaRate']['rent_drop_off_area_id']];
			?>
			</td>
		</tr>
		<tr>
			<th class="alert-success">返却エリア</th>
			<td>
			<?php
			 echo $dropOfAreaList[$this->request->data['DropOffAreaRate']['return_drop_off_area_id']];
			?>
			</td>
		</tr>
		<?php foreach ($dropOffPricePatternList as $k => $v) {
			$price = ($k > 1) ? 'price' . $k : 'price';
		?>
		<tr>
			<th class="alert-success"><?=$v?></th>
			<td>
			<?php echo $this->Form->input('DropOffAreaRate.' . $price, array('empty' => false, 'min' => 0, 'required' => true)); ?> 円
			</td>
		</tr>
		<?php } ?>
		<tr>
			<th class="alert-success">公開・非公開</th>
			<td>
				<?php
				echo $this->Form->input('DropOffAreaRate.delete_flg',array('type'=>'select','options'=>$deleteFlgOptions));
				?>
			</td>
		</tr>
	</table>

	<div class="left">
		<?php echo $this->Html->link('乗捨料金一覧へ戻る',$referer,array('class'=>'btn btn-warning'));?>
	</div>
	<div class="right">
		<?php echo $this->Form->submit('更新',array('class'=>'btn btn-success right'));?>
	</div>
	<?php echo $this->Form->end();?>

</div>
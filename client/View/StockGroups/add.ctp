<div class="stockGroups form">
	<?php echo $this -> Form -> create('StockGroup'); ?>
	<h3>在庫管理地域登録</h3>
	<?php echo $this -> Form -> input('id'); ?>

	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<td>クライアント名</td>
			<td>
			<?php
			echo $clientData['Client']['name'];
			echo $this -> Form -> hidden('client_id', array('value' => $clientData['Client']['id']));
			?>
			</td>
		</tr>
		<tr>
			<td>在庫管理地域名</td>
			<td><?php echo $this -> Form -> input('name', array('label' => false, 'div' => false)); ?></td>
		</tr>
		<tr>
			<td>都道府県</td>
			<td><?php echo $this->Form->input('prefecture_id',array('label'=>false,'div'=>false,'options'=>$prefectureList));?></td>
		</tr>
		<?php
		echo $this -> Form -> hidden('staff_id', array('label' => false, 'div' => false, 'value' => $clientData['Client']['staff_id']));
		echo $this -> Form -> hidden('delete_flg', array('label' => false, 'div' => false, 'value' => 0));
		echo $this -> Form -> hidden('deleted', array('label' => false, 'div' => false));
		?>
	</table>

	<?php echo $this->Form->submit('登録',array('class'=>'btn btn-success'))?>
	<?php echo $this->Form->end();?>
	<?php echo $this->Html->link(__('戻る'), array('action' => 'index'), array('class' => 'btn btn-warning')); ?>
</div>
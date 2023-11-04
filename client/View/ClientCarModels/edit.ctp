<h3>車種管理（クラス設定）</h3>

<?php echo $this->Form->create('ClientCarModel', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
<table class="table-striped table-condensed">
	<tr>
		<td>メーカー名</td>
		<td><?php echo $clientCarModelDetail['Automaker']['name']; ?></td>
	</tr>
	<tr>
		<td>車種名</td>
		<td><?php echo $clientCarModelDetail['CarModel']['name']; ?></td>
	</tr>
	<tr>
		<td>排気量</td>
		<td><?php echo $clientCarModelDetail['CarModel']['displacement']; ?></td>
	</tr>
	<tr>
		<td>車両クラス</td>
		<td><?php echo $this->Form->select('car_class_id', $carClassLists, array ('value' => $clientCarModelDetail['ClientCarModel']['car_class_id'], 'empty' => false)); ?></td>
	</tr>
</table>
<div>
	<?php echo $this->Form->hidden('id', array ('value' => $clientCarModelDetail['ClientCarModel']['id'])); ?>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/ClientCarModels/', array ('escape' => false)); ?>
	<?php echo $this->Form->submit('更新', array ('class' => 'btn btn-success', 'div' => false)); ?>
</div>
<?php echo $this->Form->end(); ?>
<h3>車両クラス編集</h3>

<p>
	<?php echo $this->Html->link('車種一覧', '/ClientCarModels/', array ('escape' => false)); ?>
	　＞　<?php echo $this->Html->link('クラス管理', '/CarClasses/', array ('escape' => false)); ?>
	　＞　クラス編集
</p>

<?php if(!empty($error)) {?>
<div class="alert alert-error"><?php echo $this->Session->flash('auth'); ?></div>
<?php }?>

<?php $this->Form->data = $carClassDetail;?>
<?php echo $this->Form->create('CarClass', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
<table class="table table-striped table-condensed">
	<tr>
		<td width="20%">車両クラス名</td>
		<td><?php echo $this->Form->input('name', array ('value' => $carClassDetail['CarClass']['name'],
				'required','style' => 'width:50%;')); ?></td>
	</tr>
	<tr>
		<td>車両タイプ</td>
		<td><?php echo $this->Form->select('car_type_id', $carTypeLists, array(
				 'required','value' => $carClassDetail['CarClass']['car_type_id'])); ?></td>
	</tr>
	<tr>
		<td>乗捨料金パターン</td>
		<td><?php echo $this->Form->select('drop_off_price_pattern', $dropOffPricePatternList, array(
			'empty' => false, 'value' => $carClassDetail['CarClass']['drop_off_price_pattern'], 'required')); ?></td>
	</tr>
	<tr>
<?php
	$selected = $carClassDetail['CarClass']['scope'];
	$scopeOption = array('empty' => false, 'required', 'value' => $selected);
	if (!$isClientAdmin) {
		$scopeListEdit[$selected] = $scopeList[$selected];
	} else {
		$scopeListEdit = $scopeList;
	}
?>
		<td>公開範囲</td>
		<td><?php echo $this->Form->select('scope', $scopeListEdit, $scopeOption); ?></td>
	</tr>
	<tr>
		<td>在庫管理地域</td>
		<td>

			<div class="checkbox" style="margin-bottom:20px;">
				<?php echo $this->Form->input('CarClassEdit.all_stock_edit', array('type' => 'checkbox', 'label' => 'すべてにチェックする'));?>
			</div>

			<?php echo $this->Form->input('CarClassStockGroup.stock_group_id', array('type' => 'select', 'multiple' => 'checkbox', 'options' => $stockGroupList, 'selected' => $stockGroupArray));?>

		</td>

	</tr>
	<tr>
		<td>車種</td>
		<td><?php
		foreach ($clientCarModelLists as $key => $carModel) {

			if (isset($tmp) && $tmp != $carModel['CarModel']['automaker_id']) {
				echo "<hr>";
			}

			if (isset($carClassDetail['CarModel']['car_model_id'][$carModel['CarModel']['id']])) {
				echo $this->Form->input('ClientCarModel.car_model_id.'.$carModel['CarModel']['id'],
						array (
								'type'      => 'select' ,
								'multiple'  => 'checkbox',
								'options'   => array($carModel['CarModel']['id'] => $autoMaker[$carModel['CarModel']['automaker_id']].' ・ '.$carModel['CarModel']['name']),
								'selected' => $carClassDetail['CarModel']['car_model_id'][$carModel['CarModel']['id']],
						));
				echo $this->Form->hidden('ClientCarModel.id.'.$carModel['CarModel']['id'],
						array (
								'value' => $carClassDetail['CarModel']['id'][$carModel['CarModel']['id']],
						));
			} else {
				echo $this->Form->input('ClientCarModel.car_model_id.'.$carModel['CarModel']['id'],
						array (
								'type'      => 'select' ,
								'multiple'  => 'checkbox',
								'options'   => array($carModel['CarModel']['id'] => $autoMaker[$carModel['CarModel']['automaker_id']].' ・ '.$carModel['CarModel']['name']),
						));
			}

			$tmp = $carModel['CarModel']['automaker_id'];
		} ?></td>
	</tr>
	<tr>
		<td>削除</td>
		<td><?php echo $this->Form->checkbox('delete_flg', array('value' => 1));?></td>
	</tr>
</table>
<div>
	<?php echo $this->Form->hidden('id', array ('value' => $carClassDetail['CarClass']['id'])); ?>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/CarClasses/', array ('escape' => false)); ?>
	<?php echo $this->Form->submit('更新', array ('class' => 'btn btn-success', 'div' => false)); ?>
</div>
<?php echo $this->Form->end(); ?>
<h3>車両クラス登録</h3>

<p>
	<?php echo $this->Html->link('車種一覧', '/ClientCarModels/', array ('escape' => false)); ?>
	　＞　<?php echo $this->Html->link('クラス管理', '/CarClasses/', array ('escape' => false)); ?>
	　＞　新規クラス登録
</p>

<?php echo $this->Form->create('CarClass', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
<table class="table table-striped table-condensed">
	<tr>
		<td width="20%">車両クラス名</td>
		<td><?php echo $this->Form->input('name', array ('style' => 'width:50%;','required')); ?></td>
	</tr>
	<tr>
		<td>車両タイプ</td>
		<td><?php echo $this->Form->select('car_type_id', $carTypeLists, array('required')); ?></td>
	</tr>
	<tr>
		<td>乗捨料金パターン</td>
		<td><?php echo $this->Form->select('drop_off_price_pattern', $dropOffPricePatternList, array('empty' => false, 'default' => 1, 'required')); ?></td>
	</tr>
	<tr>
<?php
	$scopeOption = array('empty' => false, 'required');
	if (!$isClientAdmin) {
		unset($scopeList[0]);
	}
?>
		<td>公開範囲</td>
		<td><?php echo $this->Form->select('scope', $scopeList, $scopeOption); ?></td>
	</tr>
	<tr>
		<td>在庫管理地域</td>
		<td>
			<div class="checkbox" style="margin-bottom:20px;">
				<?php echo $this->Form->input('CarClassEdit.all_stock_edit', array('type' => 'checkbox', 'label' => 'すべてにチェックする'));?>
			</div>
			<?php
			if(!empty($stockGroupList)) {
				echo $this->Form->input('CarClassStockGroup.stock_group_id',
						array (
								'type'      => 'select' ,
								'multiple'  => 'checkbox',
								'options'   => $stockGroupList,
								'required',
							)
					);
			} else {
			?>
			<p class="alert alert-error">
			<?php echo $this->Html->link('※先に在庫管理地域をご登録ください。','/StockGroups/',array('style'=>'color:red;'));?>
			</p>
			<?php
			}
			?>
			</td>
	</tr>
	<tr>
		<td>車種</td>
		<td>
		<?php
		if(!empty($clientCarModelLists)) {
			foreach($clientCarModelLists as $key => $carModel) {
				if (isset($tmp) && $tmp != $carModel['CarModel']['automaker_id']) {
					echo "<hr>";
				}
				echo $this->Form->input('ClientCarModel.car_model_id.'.$key,
					array (
						'type'      => 'select' ,
						'multiple'  => 'checkbox',
						'options'   => array($carModel['CarModel']['id'] => $autoMaker[$carModel['CarModel']['automaker_id']].' ・ '.$carModel['CarModel']['name']),
					));

				$tmp = $carModel['CarModel']['automaker_id'];
			}
		} else {
		?>
		<p class="alert alert-error">
		<?php echo $this->Html->link('※先に車種をご登録ください。','/ClientCarModels',array('style'=>'color:red;'));?>
		</p>

		<?php
		}
		?>
		</td>
	</tr>
</table>
<div>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/CarClasses/', array ('escape' => false)); ?>
	<?php if (!empty($clientCarModelLists) && !empty($stockGroupList)) { ?>
	<?php echo $this->Form->submit('登録', array ('class' => 'btn btn-success', 'div' => false)); ?>
	<?php } ?>
</div>
<?php echo $this->Form->end(); ?>
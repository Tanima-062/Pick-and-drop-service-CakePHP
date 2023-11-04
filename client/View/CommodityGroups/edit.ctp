<h3>商品グループ編集</h3>

<p>
	<?php echo $this->Html->link('商品グループ管理', '/CommodityGroups/', array ('escape' => false)); ?>
	　＞　商品グループ編集
</p>

<?php echo $this->Form->create('CommodityGroup', array (
		'inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),'required',)); ?>
<table  class="table table-bordered" style="width:60%;">
	<tr>
		<td class="alert-success">グループ名</td>
		<td><?php
		echo $this->Form->hidden('id');
		echo $this->Form->input('name',array('style'=>'width:100%;','required')); ?>
		</td>
	</tr>
	<tr>
<?php
	$selected = $this->data['CommodityGroup']['scope'];
	$scopeOption = array('empty'=>false,'style'=>'width:100%;','required');
	if (!$isClientAdmin) {
		$scopeListEdit[$selected] = $scopeList[$selected];
	} else {
		$scopeListEdit = $scopeList;
	}
?>
		<td class="alert-success">公開範囲</td>
		<td><?php
		echo $this->Form->select('scope', $scopeListEdit, $scopeOption); ?>
		</td>
	</tr>
</table>

<table class="table table-striped table-condensed">
	<tr>
		<td colspan = "3">該当する商品を選択して下さい</td>
	</tr>
	<tr>
		<td colspan = "3"><?php
		foreach ($commodities as $key => $commodity) { ?>
		<?php echo $this->Form->checkbox('Commodity.'.$key.'.id',
				array('value'=>$key,'div'=>false)); ?>
		<label for="Commodity<?php echo $key; ?>Id" style='display:inline;'><?php echo $commodity; ?></label><br/>
		<?php } ?>
		</td>

	</tr>

</table>

<table class="table">
	<tr>
		<td><?php echo $this->Form->input('delete_flg',array('label'=>false)); ?><span> 削除フラグ</span></td>
	</tr>
</table>

<br />

<div>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/CommodityGroups/', array ('escape' => false)); ?>
	<?php echo $this->Form->submit('編集', array ('class' => 'btn btn-success', 'div' => false)); ?>
</div>

<?php echo $this->Form->end(); ?>
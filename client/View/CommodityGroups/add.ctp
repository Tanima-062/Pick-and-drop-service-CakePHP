<h3>商品グループ登録</h3>

<p>
	<?php echo $this->Html->link('商品グループ管理', '/CommodityGroups/', array ('escape' => false)); ?>
	　＞　商品グループ登録
</p>

<?php echo $this->Form->create('CommodityGroup', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
<table class="table table-bordered" style="width:60%;">
	<tr>
		<td class="alert-success">グループ名</td>
		<td><?php echo $this->Form->input('name',array('style'=>'width:100%;','required')); ?></td>
	</tr>
	<tr>
<?php
	$scopeOption = array('empty'=>false,'style'=>'width:100%;','required');
	if (!$isClientAdmin) {
		unset($scopeList[0]);
	}
?>
		<td class="alert-success">公開範囲</td>
		<td><?php echo $this->Form->select('scope', $scopeList, $scopeOption); ?></td>
	</tr>
</table>
<div>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/CommodityGroups/', array ('escape' => false)); ?>
	<?php echo $this->Form->submit('登録', array ('class' => 'btn btn-success', 'div' => false)); ?>
</div>
<?php echo $this->Form->end(); ?>
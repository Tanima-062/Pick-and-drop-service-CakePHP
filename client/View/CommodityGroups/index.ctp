<?php echo $this->Html->script('jquery-ui.min.js');?>

<script>
<?php
	if ($isClientAdmin) {
?>
$(function(){
	$('#sortable-div').sortable();
});


jQuery(function($) {
$("#sortable-div").sortable({
	items: "tr",
	opacity: 1.5,
	revert: false,
	forcePlaceholderSize: false,
	placeholder: "alert-info",
	stop : function(){
		var data=[];
		$(".ui-state").each(function(i,v){
			data.push(v.id);
		});
		$('#CastOrder').val(data.toString());
	},
	update : function(){
		$('#submit').removeAttr('disabled');
	},
	cancel:'.stop'
});
<?php
	}
?>

//$('#sortable-div td').sortable({cancel : '.stop'});

});
</script>

<h3>商品グループ管理</h3>

<span>
	<?php echo $this->Html->link('<span class="btn btn-success">新規商品グループ登録</span>', '/CommodityGroups/add/', array ('escape' => false)); ?>
</span>

<span style="float:right;<?php echo ($isClientAdmin) ? '' : 'visibility:hidden;'; ?>">
	<?php echo $this->Form->create('Client');?>
	<?php echo $this->Form->hidden('sort',array('id'=>'CastOrder'));?>
	<?php echo $this->Form->submit('並び順を保存する',array('id'=>'submit', 'class'=>'btn btn-primary','disabled'=>'disabled'));?>
	<?php echo $this->Form->end();?>
</span>

<br clear="all" />
<table style="width:50%;" class="table table-striped table-bordered table-condensed">
	<thead>
			<tr class="alert-info">
			<th>順番</th>
			<th>グループ名</th>
			<th>公開範囲</th>
			</tr>
	</thead>
	<tbody id="sortable-div">
	<?php
	$i = 1;
	foreach ($commodityGroups as $commodityGroup) {
	?>
		<tr  id="<?php echo $commodityGroup['CommodityGroup']['id'];?>" class="ui-state">
			<td><?php echo $i;?></td>
			<td><?php echo $this->Html->link($commodityGroup['CommodityGroup']['name'], '/CommodityGroups/edit/' . $commodityGroup['CommodityGroup']['id'] . '/', array()); ?></td>
			<td><?php echo $scopeList[$commodityGroup['CommodityGroup']['scope']]; ?></td>
		</tr>
	<?php
		$i++;
	}
	 ?>
	</tbody>
</table>

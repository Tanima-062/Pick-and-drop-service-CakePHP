<?php echo $this->Html->script('jquery-ui.min.js');?>

<script>

<?php
	if ($isClientAdmin) {
?>
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

<h3>車両クラス管理</h3>

<p>
	<?php echo $this->Html->link('車種一覧', '/ClientCarModels/', array ('escape' => false)); ?>
	　＞　クラス管理
</p>

<div class="left">
	<?php echo $this->Html->link('<span class="btn btn-success">新規クラス登録</span>', '/CarClasses/add/', array ('escape' => false)); ?>
</div>

<div class="right"<?php echo ($isClientAdmin) ? '' : ' style="visibility:hidden"';?>>
	<?php echo $this->Form->create('Client');?>
	<?php echo $this->Form->hidden('order',array('id'=>'CastOrder'));?>
	<?php echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));?>
	<?php echo $this->Form->end();?>
</div>

<table class="table table-bordered table-striped">
	<thead>
		<tr class="alert-info">
			<th>車両クラス名</th>
			<th>車両タイプ</th>
			<th>車種名</th>
			<th>乗捨料金</th>
			<th>公開範囲</th>
		</tr>
	</thead>
	<tbody id="sortable-div">
		<?php
		if (!empty($carClassLists)) {
			foreach ($carClassLists as $carClassList) {
				//車種
				$carModel = '';
				if(!empty($clientCarModel[$carClassList['CarClass']['id']])) {
					$carModel = $clientCarModel[$carClassList['CarClass']['id']]['name'];
				}

		?>
			<tr id="<?php echo $carClassList['CarClass']['id'];?>" class="ui-state" >
				<td><?php echo $this->Html->link($carClassList['CarClass']['name'], '/CarClasses/edit/' . $carClassList['CarClass']['id'] . '/'); ?></td>
				<td><?php echo $carTypeLists[$carClassList['CarClass']['car_type_id']]; ?></td>
				<td><?php echo $carModel;?></td>
				<td><?php echo $dropOffPricePatternList[$carClassList['CarClass']['drop_off_price_pattern']];?></td>
				<td><?php echo $scopeList[$carClassList['CarClass']['scope']];?></td>
			</tr>
		<?php
			}
		}
		?>
	</tbody>
</table>

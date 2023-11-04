<?php echo $this->Html->script('jquery-ui.min.js');?>

<script>
$(function(){
	$('#sortable-div').sortable();
});


jQuery(function($) {
$("#sortable-div").sortable({
	items: "tr",
	opacity: 1.5,
	revert: false,
	forcePlaceholderSize: false,
	placeholder: "btn-info",
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


//$('#sortable-div td').sortable({cancel : '.stop'});

});
</script>

<div class="stockGroups index">
	<h3><?php echo __('在庫管理地域一覧'); ?></h3>
	<span>
			<?php echo $this->Html->link(__('新規登録'), array('action' => 'add'),array('class'=>'btn btn-success')); ?>
	</span>

	<span style="float:right;">
	<?php echo $this->Form->create('StockGroups');?>
	<?php echo $this->Form->hidden('sort',array('id'=>'CastOrder'));?>
	<?php echo $this->Form->submit('並び順を保存する',array('id'=>'submit', 'class'=>'btn btn-primary','disabled'=>'disabled'));?>
	<?php echo $this->Form->end();?>
	</span>

	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th style="width:7%;">順番</th>
				<th>地域名</th>
			</tr>
		</thead>
		<tbody id="sortable-div">
			<?php
			$i = 1;
			foreach ($stockGroups as $stockGroup):
			?>
				<tr id="<?php echo $stockGroup['StockGroup']['id'];?>" class="ui-state">
					<td><?php echo $i;?></td>
					<td><?php echo $this->Html->link(h($stockGroup['StockGroup']['name']), array('action' => 'edit', $stockGroup['StockGroup']['id'])); ?>&nbsp;</td>
				</tr>
			<?php
			$i++;
			endforeach;
			?>
			</tbody>
	</table>

		<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));?>

	<div class="pagination">
		<ul>
			<?php
				echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
				echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>

</div>
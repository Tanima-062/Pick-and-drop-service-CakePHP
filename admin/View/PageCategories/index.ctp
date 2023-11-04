<script>
$(function() {
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
			$('#sort').val(data.toString());
		},
		update : function(){
			$('#submit').removeAttr('disabled');
		},
		cancel:'.stop'
	});

//$('#sortable-div td').sortable({cancel : '.stop'});

});
</script>

<div class="pageCategories index">
	<h3>ページカテゴリーマスタ</h3>
	<div class="right">
		<?php
			echo $this->Form->create('PageCategory');
			echo $this->Form->hidden('sort',array('id'=>'sort'));
			echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));
			echo $this->Form->end();
		?>
	</div>
	<table class="table table-bordered">
		<thead>
			<tr class="btn-primary">
				<th>順番</th>
				<th>id</th>
				<th>カテゴリー名</th>
				<th class="actions"><?php echo $this->Html->link('新規追加','add',array('class'=>'btn btn-success'));?></th>
			</tr>
		</thead>
		<tbody id="sortable-div">
	<?php
	foreach ($pageCategories as $pageCategory): ?>
	<tr id="<?php echo $pageCategory['PageCategory']['id'];?>" class="ui-state" >

		<td class="span2"><?php echo h($pageCategory['PageCategory']['sort']); ?>&nbsp;</td>
		<td><?php echo h($pageCategory['PageCategory']['id']); ?>&nbsp;</td>
		<td><?php echo h($pageCategory['PageCategory']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link('編集', array('action' => 'edit', $pageCategory['PageCategory']['id']),array('class'=>'btn btn-warning')); ?>
			<?php echo $this->Form->postLink('削除', array( 'action' => 'delete', $pageCategory['PageCategory']['id']),array('class'=>'btn btn-danger'), __('「%s」を削除しますか?', $pageCategory['PageCategory']['name'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
		</tbody>
	</table>

</div>


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

<div class="pages index">
	<h3>ページマスタ</h3>
		<div class="right">
		<?php
			echo $this->Form->create('Page');
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
					<th>ページカテゴリー</th>
					<th>ページ名</th>
					<th>ページURL</th>
					<th class="actions"><?php echo $this->Html->link('新規追加', array('action' => 'add'),array('class'=>'btn btn-success')); ?></th>
			</tr>
		</thead>
		<tbody id="sortable-div">
		<?php
		foreach ($pages as $page):
		?>
		<tr id="<?php echo $page['Page']['id'];?>" class="ui-state" >
			<td><?php echo h($page['Page']['sort']); ?>&nbsp;</td>
			<td><?php echo h($page['Page']['id']); ?>&nbsp;</td>
			<td>
				<?php echo $page['PageCategory']['name']; ?>
			</td>
			<td><?php echo h($page['Page']['name']); ?>&nbsp;</td>
			<td><?php echo h($page['Page']['url']); ?>&nbsp;</td>
			<td class="actions">
				<?php echo $this->Html->link('編集', array('action' => 'edit', $page['Page']['id']),array('class'=>'btn btn-warning')); ?>
				<?php echo $this->Form->postLink('削除', array( 'action' => 'delete', $page['Page']['id']),array('class'=>'btn btn-danger'), __('「%s」を削除しますか?', $page['Page']['name'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
</div>


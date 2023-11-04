<?php /* 都道府県の順番を変更することはほぼ無いのでコメントアウト
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
*/ ?>
<div class="prefectures index">
	<h2>都道府県マスタ</h2>
		<div class="right">
		<?php
			echo $this->Form->create('Prefecture');
			echo $this->Form->hidden('sort',array('id'=>'sort'));
// 都道府県の順番を変更することはほぼ無いのでコメントアウト
//			echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));
			echo $this->Form->end();
		?>
	</div>
	<table class="table table-bordered">
		<thead>
			<tr class="btn-primary">
					<th>id</th>
					<th>都道府県名</th>
					<th>リンク用URL(地方)</th>
					<th>リンク用URL(都道府県)</th>
					<th>作成日</th>
					<th>更新日</th>
					<th>更新者</th>
					<th class="actions"><?php echo $this->Html->link('新規追加','add/',array('class'=>'btn btn-success')); ?></th>
			</tr>
		</thead>
		<tbody id="sortable-div">
		<?php
		foreach ($prefectures as $prefecture): ?>
		<tr id="<?php echo $prefecture['Prefecture']['id'];?>" class="ui-state" >
			<td><?php echo h($prefecture['Prefecture']['id']); ?>&nbsp;</td>
			<td><?php echo h($prefecture['Prefecture']['name']); ?>&nbsp;</td>
			<td><?php echo h($prefecture['Prefecture']['region_link_cd']); ?>&nbsp;</td>
			<td><?php echo h($prefecture['Prefecture']['link_cd']); ?>&nbsp;</td>
			<td><?php echo h($prefecture['Prefecture']['created']); ?>&nbsp;</td>
			<td><?php echo h($prefecture['Prefecture']['modified']); ?>&nbsp;</td>
			<td>
				<?php echo $prefecture['Staff']['name']; ?>
			</td>
			<td class="actions">
				<?php echo $this->Html->link('編集', array('action' => 'edit', $prefecture['Prefecture']['id']),array('class'=>'btn btn-warning')); ?>
				<?php echo $this->Form->postLink('削除', array('action' => 'delete', $prefecture['Prefecture']['id']), array('class'=>'btn btn-danger'), __('「%s」を削除しますか?', $prefecture['Prefecture']['name'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
</div>
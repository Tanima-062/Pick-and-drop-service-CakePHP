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
<div class="landmarks index">
	<h2>ランドマーク一覧</h2>
	<div class="right">
		<?php
			echo $this->Form->create('Landmark');
			echo $this->Form->hidden('sort',array('id'=>'sort'));
			echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));
			echo $this->Form->end();
		?>
	</div>
	<table class="table table-bordered">
	<thead>
		<tr class="btn-primary">
				<th>Id</th>
				<th>都道府県</th>
				<th>カテゴリ</th>
				<th>ランドマーク名</th>
				<th>略称</th>
				<th>リンク用URL</th>
				<th>緯度</th>
				<th>経度</th>
				<th>トラベルコID</th>
				<th>公開</th>
				<th class="actions"><?php echo $this->Html->link('新規登録','add',array('class'=>'btn btn-success'));?></th>
		</tr>
	</thead>
	<tbody id="sortable-div">
	<?php
	foreach ($landmarks as $landmark):
		$class = '';
		if(!empty($landmark['Landmark']['delete_flg'])) {
			$class = 'gray';
		}
		if(empty($linkcds[$landmark['Landmark']['id']])){
			$linkcds[$landmark['Landmark']['id']] = '';
		}
	?>
	<tr id="<?php echo $landmark['Landmark']['id'];?>" class="ui-state <?php echo $class ?>">
		<td><?php echo h($landmark['Landmark']['id']); ?></td>
		<td><?php echo h($prefectureList[$landmark['Landmark']['prefecture_id']]); ?></td>
		<td><?php echo h($landmarkCategoryList[$landmark['Landmark']['landmark_category_id']]); ?></td>
		<td><?php echo h($landmark['Landmark']['name']); ?></td>
		<td><?php echo h($landmark['Landmark']['short_name']); ?></td>
		<td><?php echo h($landmark['Landmark']['link_cd']); ?></td>
		<td><?php echo h($landmark['Landmark']['latitude']); ?></td>
		<td><?php echo h($landmark['Landmark']['longitude']); ?></td>
		<td><?php echo h($landmark['Landmark']['travelko_id']); ?></td>
		<td><?php echo h($deleteFlgOptions[$landmark['Landmark']['delete_flg']]); ?></td>
		<td class="actions">
			<?php //echo $this->Html->link(__('詳細'), array('action' => 'view', $landmark['Landmark']['id']),array('class'=>'btn btn-success btn-small')); ?>
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $landmark['Landmark']['id']),array('class'=>'btn btn-warning btn-small')); ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
</div>
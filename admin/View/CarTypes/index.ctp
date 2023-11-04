<script>
	$(function () {
		$("#sortable-div").sortable({
			items: "tr",
			opacity: 1.5,
			revert: false,
			forcePlaceholderSize: false,
			placeholder: "alert-info",
			stop: function () {
				var data = [];
				$(".ui-state").each(function (i, v) {
					data.push(v.id);
				});
				$('#sort').val(data.toString());
			},
			update: function () {
				$('#submit').removeAttr('disabled');
			},
			cancel: '.stop'
		});

//$('#sortable-div td').sortable({cancel : '.stop'});

	});
</script>
<div class="carTypes index">
	<h3>車両タイプマスタ</h3>
	<div class="right">
		<?php
		echo $this->Form->create('Sort');
		echo $this->Form->hidden('sort', array('id' => 'sort'));
		echo $this->Form->submit('並び順を保存する', array('id' => 'submit', 'class' => 'btn btn-primary', 'disabled' => 'disabled'));
		echo $this->Form->end();
		?>
	</div>
	<table class="table table-bordered">
		<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('name', '車両タイプ'); ?></th>
			<th><?php echo $this->Paginator->sort('capacity', '法定定員'); ?></th>
			<th><?php echo $this->Paginator->sort('description', '説明'); ?></th>
			<th><?php echo $this->Paginator->sort('travelko_id', 'トラベルコID'); ?></th>
			<th class="actions">
				<?php echo $this->Html->link('新規追加', array('action' => 'add'), array('class' => 'btn btn-success')); ?></li>
			</th>
		</tr>
		<tbody id="sortable-div">
			<?php foreach ($carTypes as $carType): ?>
				<tr id="<?php echo h($carType['CarType']['id']); ?>" class="ui-state">
					<td><?php echo h($carType['CarType']['id']); ?>&nbsp;</td>
					<td><?php echo h($carType['CarType']['name']); ?>&nbsp;</td>
					<td><?php echo h($carType['CarType']['capacity']); ?>&nbsp;</td>
					<td><?php echo h($carType['CarType']['description']); ?>&nbsp;</td>
					<td><?php echo h($carType['CarType']['travelko_id']); ?>&nbsp;</td>
					<td class="actions">
						<?php echo $this->Html->link('編集', array('action' => 'edit', $carType['CarType']['id']), array('class' => 'btn btn-warning')); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>


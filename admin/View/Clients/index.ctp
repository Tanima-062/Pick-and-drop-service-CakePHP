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
<div class="clients index">
	<h3>クライアント一覧</h3>
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
			<th><?php echo $this->Paginator->sort('name', 'クライアント名'); ?></th>
			<th><?php echo $this->Paginator->sort('area_type', '地域タイプ'); ?></th>
			<th><?php echo $this->Paginator->sort('reserve_tag', '予約タグ'); ?></th>
			<th><?php echo $this->Paginator->sort('commission_rate', '手数料率(%)'); ?></th>
			<th><?php echo $this->Paginator->sort('url', 'リンク用URL'); ?></th>
			<th><?php echo $this->Paginator->sort('accept_prepay', '事前決済許可'); ?></th>
			<th><?php echo $this->Paginator->sort('is_managed_package', '包括販売商品'); ?></th>
			<th><?php echo $this->Paginator->sort('created', '作成日'); ?></th>
			<th><?php echo $this->Paginator->sort('modified', '更新日'); ?></th>
			<th class="actions">
				<?php echo $this->Html->link('新規追加', array('action' => 'add'), array('class' => 'btn btn-success')); ?>
			</th>
		</tr>
		<tbody id="sortable-div">
			<?php
			foreach ($clients as $client) {
				$class = !empty($client['Client']['delete_flg']) ? 'gray' : (empty($client['Client']['is_searchable']) ? 'sunday' : '');
				?>
				<tr id="<?php echo h($client['Client']['id']); ?>" class="ui-state <?php echo $class; ?>">
					<td><?php echo h($client['Client']['id']); ?>&nbsp;</td>
					<td><?php echo h($client['Client']['name']); ?>&nbsp;</td>
					<td><?php echo h($areaType[$client['Client']['area_type']]); ?>&nbsp;</td>
					<td><?php echo h($client['Client']['reserve_tag']); ?>&nbsp;</td>
					<td>
						<?php
							$rates = Hash::extract($client, 'SettlementCompany.{n}.commission_rate');
						?>
						max: <?php echo !empty($rates) ? h(max($rates)) : ''; ?>,
						min: <?php echo !empty($rates) ? h(min($rates)) : ''; ?>
					</td>
					<td><?php echo h($client['Client']['url']); ?>&nbsp;</td>
					<td><?php echo h($acceptOrNot[$client['Client']['accept_prepay']]); ?></td>
					<td><?php echo h($managedPackage[$client['Client']['is_managed_package']]); ?>&nbsp;</td>
					<td><?php echo h($client['Client']['created']); ?>&nbsp;</td>
					<td><?php echo h($client['Client']['modified']); ?>&nbsp;</td>
					<td class="actions">
						<?php echo $this->Html->link('編集', array('action' => 'edit', $client['Client']['id']), array('class' => 'btn btn-warning')); ?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>

<script>
    $(function() {
        $("#sortable-div").sortable({
            items : "tr",
            opacity : 1.5,
            revert : false,
            forcePlaceholderSize : false,
            placeholder : "alert-info",
            stop : function() {
                var data = [];
                $(".ui-state").each(function(i, v) {
                    data.push(v.id);
                });
                $('#sort').val(data.toString());
            },
            update : function() {
                $('#submit').removeAttr('disabled');
            },
            cancel : '.stop'
        });

        //$('#sortable-div td').sortable({cancel : '.stop'});

    });
</script>

<div class="equipment index">
	<h3>装備一覧</h3>
	<div class="left">
		<?php echo $this->Html->link(__('新規登録'), array('action' => 'add'), array('class' => 'btn btn-success')); ?>
	</div>
	<div class="right">
	<?php
	echo $this->Form->create('Equipment');
	echo $this->Form->hidden('sort', array('id' => 'sort'));
	echo $this->Form->submit('並び順を保存する', array('id' => 'submit', 'class' => 'btn btn-primary', 'disabled' => 'disabled'));
	echo $this->Form->end();
	?>
	</div>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr class="alert-info">
				<th>装備ID</th>
				<th>カテゴリ</th>
				<th>装備名</th>
				<th>説明</th>
				<th>更新者</th>
				<th>公開</th>
				<th class="actions"><?php echo __('Actions'); ?></th>
			</tr>
		</thead>
		<tbody id="sortable-div">
			<?php
			foreach ($equipment as $equipment) {
			?>
			<tr id="<?php echo $equipment['Equipment']['id']; ?>" class="ui-state" >
				<td><?php echo h($equipment['Equipment']['id']); ?>&nbsp;</td>
				<td><?php echo h(!empty($optionCategories[$equipment['Equipment']['option_category_id']]) ? $optionCategories[$equipment['Equipment']['option_category_id']] : ''); ?>&nbsp;</td>
				<td><?php echo h($equipment['Equipment']['name']); ?>&nbsp;</td>
				<td><?php echo h($equipment['Equipment']['description']); ?>&nbsp;</td>
				<td>
					<?php echo $equipment['Staff']['name']; ?>
				</td>
				<td><?php echo h($isPublishedOptions[$equipment['Equipment']['is_published']]); ?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link(__('詳細'), array('action' => 'view', $equipment['Equipment']['id']), array('class' => 'btn btn-success btn-small')); ?>
					<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $equipment['Equipment']['id']), array('class' => 'btn btn-warning btn-small')); ?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>

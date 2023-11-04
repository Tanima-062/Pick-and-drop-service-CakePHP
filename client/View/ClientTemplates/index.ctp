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
			$('#order').val(data.toString());
		},
		update : function(){
			$('#submit').removeAttr('disabled');
		},
		cancel:'.stop'
	});
//$('#sortable-div td').sortable({cancel : '.stop'});
});
</script>

<div class="clientTemplates index">
	<h3>返信テンプレート</h3>

	<div class="right">
		<?php
			echo $this->Form->create('ClientTemplate');
			echo $this->Form->hidden('order',array('id'=>'order'));
			echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));
			echo $this->Form->end();
		?>
	</div>

	<?php echo $this->Form->create('ClientTemplate',array('action'=>'sortedit')); ?>

	<p>
		<?php echo $this->Html->link(__('新規登録'), array('action' => 'add'),array('class'=>'btn btn-success')); ?>
	</p>

	<?php
	if (isset($clientTemplates) && !empty($clientTemplates)) {
	?>
		<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr class="alert alert-info">
							<th class="span1">順番</th>
							<th class="span3">テンプレ名</th>
							<th class="span6">テンプレート</th>
							<th class="span2">作成者</th>
					</tr>
				</thead>
			<tbody id="sortable-div">
				<?php
				foreach ($clientTemplates as $key => $clientTemplate) {
				?>
				<tr id="<?php echo $clientTemplate['ClientTemplate']['id'];?>" class="ui-state">
					<td>
						<?php echo ++$key;?>
					</td>
					<td >
						<?php
							echo $this->Html->link($clientTemplate['ClientTemplate']['name'],
								array('action' => 'edit', $clientTemplate['ClientTemplate']['id'])); ?>
					</td>

					<td class="actions">
						<?php echo mb_strimwidth(h($clientTemplate['ClientTemplate']['template']),0,60,'...'); ?>
					</td>
					<td><?php echo $staffList[$clientTemplate['ClientTemplate']['login_staff_id']]; ?></td>
				</tr>
				<?php } ?>
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
	<?php } ?>
	<?php echo $this->Form->end(); ?>
</div>
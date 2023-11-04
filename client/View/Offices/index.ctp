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
		$('#office_order').val(data.toString());
	},
	update : function(){
		$('#submit').removeAttr('disabled');
	},
	cancel:'.stop'
});

//$('#sortable-div td').sortable({cancel : '.stop'});

});
</script>

<div class="index">
	<h3>営業所一覧</h3>

	<?php
	echo $this->Form->create('Office',array('type'=>'get','inputDefaults'=>array('label'=>false)));
	?>
	<table class="table table-bordered">
		<tr>
			<th class="span2">都道府県</th>
			<td class="span4"><?php echo $this->Form->input('prefecture_id',array('options'=>$prefectureList,'empty'=>'---'));?></td>
			<td>
			<?php
            echo $this->Form->submit('絞り込み',array('class'=>'btn btn-info'));
            ?>
            </td>
		</tr>
	</table>
	<?php
	echo $this->Form->end();
	?>

	<div class="right">
		<?php
			echo $this->Form->create('Office');
			echo $this->Form->hidden('order',array('id'=>'office_order'));
			echo $this->Form->submit('並び順を保存する',array('id'=>'submit','class'=>'btn btn-primary','disabled'=>'disabled'));
			echo $this->Form->end();
		?>
	</div>

	<p>
	<?php echo $this->Html->link(__('新規登録'), array('action' => 'add'),array('class'=>'btn btn-success')); ?>
	</p>
	<table class="table table-bordered table-striped">
		<thead>
			<tr class="alert-info">
				<th>順番</th>
				<th>在庫管理地域</th>
				<th>営業所名</th>
				<th>営業時間</th>
				<th>出発/返却</th>
<?php	if ($is_system_admin == 1){	?>
				<th>リンク用URL</th>
<?php	}	?>
				<th>電話番号</th>
				<th>住所</th>
			</tr>
		</thead>
	<tbody id="sortable-div">
	<?php
		foreach ($offices as $key => $office) {
	?>
		<tr id="<?php echo $office['Office']['id'];?>" class="ui-state" >
			<td>
				<span class="sort-now">
					<?php echo $office['Office']['sort']; ?>
				</span>
				<span class="sort-form" style="display:none;">
					<?php
						echo $this->Form->inpu('Office.'.$key.'.sort',array(
								'label'=>false,
								'div'=>false,
								'style'=>'width:30px;',
								'value'=>$office['Office']['sort'])
						);
						echo $this->Form->hidden('Office.'.$key.'.id',array(
								'value'=>$office['Office']['id']
							)
						);
						?>
				</span>
			</td>
			<td>
			<?php
			if (!empty($stockGroupList[$office['OfficeStockGroup']['stock_group_id']])) {
				echo $stockGroupList[$office['OfficeStockGroup']['stock_group_id']];
			}
			?>
			</td>
			<td>
				<?php
				echo $this->Html->link(h($office['Office']['name']), array('action' => 'edit', $office['Office']['id']));
				?>
			</td>
			<td>
			<?php if (!empty($office['Office']['businessHours'])) { ?>
			<?php echo $office['Office']['businessHours']; ?><br>
			<?php } ?>
			<?php echo $this->Html->link('特別営業時間設定', '/Offices/special_business_hours/'.$office['Office']['id'].'/'); ?>
			</td>
			<td>
				<?php
					if(!empty($office['Office']['accept_rent'])) {
						echo "出発";
					}

					if(!empty($office['Office']['accept_rent']) && !empty($office['Office']['accept_return'])) {
						echo "/";
					}

					if(!empty($office['Office']['accept_return'])) {
						echo "返却";
					}
				?>
			</td>
<?php	if ($is_system_admin == 1){	?>
			<td>
			<?php echo h($office['Office']['url']); ?>
			</td>
<?php	}	?>
			<td>
			<?php echo h($office['Office']['tel']); ?>
			</td>
			<td>
				<?php echo h($office['Office']['address']); ?>
			</td>
		</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php
	echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));
	?>

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
<div class="zipcodes index">
	<h2>お知らせ一覧</h2>
	<?php echo $this->Form->create('Message',array('type'=>'get','url'=>'.')); ?>
		<table class="table-bordered table-condensed" style="width:600px">
			<tr>
			<th>掲載期間</th>
			<td>
				掲載開始日時<br>
				<?php echo $this->element('selectDatetime',$fromTimeOptions);?><br>
				掲載終了日時<br>
				<?php echo $this->element('selectDatetime',$toTimeOptions);?>
			</td>
			</tr>
			<?php if($is_system_admin): ?>
			<tr>
				<th>更新者</th>
				<td><?php echo $this->Form->input('Staff', array('div' => false, 'label' => false, 'options' => $staffList, 'empty' => false));?></td>
			</tr>
			<?php endif; ?>
		</table>
		<br />
		<div>
			<?php
				echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
				echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
			?>
		</div>
	<?php echo $this->Form->end(); ?>


	<table class="table table-bordered">
	<thead>
	<tr class="btn-primary">
			<th>Id</th>
			<th>タイトル</th>
			<th>表示箇所</th>
			<th>掲載期間</th>
			<th>公開</th>
			<th>作成者</th>
			<th>更新者</th>
			<th class="actions"><?php echo $this->Html->link('新規登録','add',array('class'=>'btn btn-success'));?></th>
	</tr>
	</thead>
	<tbody id="sortable-div">
	<?php
	foreach ($messages as $message):
		$class = '';
		if(!empty($message['Message']['delete_flg'])) {
			$class = 'gray';
		}
	?>
	<tr id="<?php echo $message['Message']['id'];?>" class="ui-state <?php echo $class;?>">
		<td><?php echo h($message['Message']['id']); ?></td>
		<td><?php echo h($message['Message']['title']); ?></td>
		<td>
			<?php if(!empty($message['Message']['ui_website_flg'])){
				echo "<div>レンタカーサービストップ</div>";
			}
			?>
			<?php if(!empty($message['Message']['ui_client_flg'])){
				echo "<div>クライアント管理画面トップ</div>";
			}
			?>
			<?php if(!empty($message['Message']['ui_admin_flg'])){
				echo "<div>社内管理画面トップ</div>";
			}
			?>
		</td>
		<td>
			<?php echo h(date("Y-m-d H:i",strtotime($message['Message']['from_time']))); ?><br>
			〜<br>
			<?php echo h(date("Y-m-d H:i",strtotime($message['Message']['to_time']))); ?>
		</td>
		<td>
			<?php if(empty($message['Message']['delete_flg'])){
				echo "<div>公開</div>";
			} else {
				echo "<div>非公開</div>";
			}
			?>
		</td>
		<td>
			<?php if(array_key_exists($message['Message']['staff_id'], $staffList)): ?>
				<?php echo h($staffList[$message['Message']['staff_id']]); ?><br>
			<?php endif; ?>
			<?php echo h(date("Y-m-d H:i",strtotime($message['Message']['created']))); ?>
		</td>
		<td>
			<?php if(array_key_exists($message['Message']['modified_staff_id'], $staffList)): ?>
				<?php echo h($staffList[$message['Message']['modified_staff_id']]); ?><br>
			<?php endif; ?>
			<?php echo h(date("Y-m-d H:i",strtotime($message['Message']['modified']))); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $message['Message']['id']),array('class'=>'btn btn-warning')); ?>
		</td>
	</tr>
<?php endforeach; ?>
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
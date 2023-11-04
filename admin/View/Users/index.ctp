<div class="staffs index">
	<h3>スタッフ一覧</h3>
	<?php echo $this->Form->create('User',array('type'=>'get','inputDefaults'=>array('label'=>false))); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>クライアント名</th>
				<td><?php echo $this->Form->input('client_id', array('div' => false, 'label' => false, 'options' => $clientList, 'empty'=>'---'));?></td>
			</tr>
			<tr>
				<th>権限</th>
				<td><?php echo $this->Form->input('role', array('div' => false, 'label' => false, 'options' => $roleList, 'empty'=>'---'));?></td>
			</tr>
		</table>
		<br />
		<div style="float:left;padding:0px 20px 10px 0px;">
		<?php echo $this->Form->submit('検索する',array('class'=>'btn btn-primary'))?>
		</div>
	<?php echo $this->Form->end(); ?>
  <div class="pagination pagination-right">
    <ul>
      <li><?php echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')); ?></li>
      <li><?php echo $this->Paginator->numbers(); ?></li>
      <li><?php echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')); ?></li>
    </ul>
  </div>
	<table class="table table-bordered">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('client_id','クライアント名'); ?></th>
			<th><?php echo $this->Paginator->sort('name','担当者名'); ?></th>
			<th><?php echo $this->Paginator->sort('is_admin','権限'); ?></th>
			<th><?php echo $this->Paginator->sort('username','ユーザーID'); ?></th>
			<th class="actions">
			<?php echo $this->Html->link('スタッフ追加', array('action' => 'add'),array('class'=>'btn btn-success')); ?>
			</th>
	</tr>
	<?php
	foreach ($staffs as $staff):
		$class = '';
		if(!empty($staff['Staff']['delete_flg'])) {
			$class = 'gray';
		}

		$is_admin = '一般スタッフ';
		if (!empty($staff['Staff']['is_system_admin']) && !empty($staff['Staff']['is_client_admin'])) {
			$is_admin = '社内管理者';
		} else if (empty($staff['Staff']['is_system_admin']) && !empty($staff['Staff']['is_client_admin'])) {
			$is_admin = 'クライアント管理者';
		}
	?>
	<tr class="<?php echo $class;?>">
		<td><?php echo h($staff['Staff']['id']); ?></td>
		<td>
			<?php echo h($staff['Client']['name']); ?>
		</td>
		<td><?php echo h($staff['Staff']['name']); ?></td>
		<td><?php echo h($is_admin); ?></td>
		<td><?php echo h($staff['Staff']['username']); ?></td>
		<td class="actions">
			<?php echo $this->Html->link('編集', array('action' => 'edit', $staff['Staff']['id']), array('class' => 'btn btn-warning')); ?>
			<?php if (empty($staff['Staff']['is_system_admin'])) { ?>
			<?php echo $this->Html->link('営業所設定', array('action' => 'office_authority_management', $staff['Staff']['id']), array('class' => 'btn btn-info')); ?>
			<?php echo $this->Html->link('メニュー設定', array('action' => 'page_authority_management', $staff['Staff']['id']), array('class' => 'btn btn-primary')); ?>
			<?php } ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	/*
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	*/
	?>	</p>

	<div class="pagination pagination-right">
		<ul>
			<li><?php echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')); ?></li>
			<li><?php echo $this->Paginator->numbers(); ?></li>
			<li><?php echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')); ?></li>
		</ul>
	</div>
</div>
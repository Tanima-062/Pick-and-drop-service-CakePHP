<div class="messages">
	<?php echo $this->Form->create('Message', array('inputDefaults' => array('label' => false))); ?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>お知らせ追加</h3>
	<table class="table table-bordered">
		<tr>
			<th>タイトル</th>
			<td><?php echo $this->Form->input('title'); ?></td>
		</tr>
		<tr>
			<th>本文</th>
			<td><?php echo $this->Form->input('message', array('style'=>'width:95%')); ?></td>
		</tr>
		<tr>
			<th>掲載期間</th>
			<td>
				掲載開始日時<br>
				<?php echo $this->element('selectDatetime',$fromTimeOptions);?><br>
				掲載終了日時<br>
				<?php echo $this->element('selectDatetime',$toTimeOptions);?>
			</td>
		</tr>
		<tr>
			<th>表示箇所</th>
			<td>
				レンタカーサービストップ <?php echo $this->Form->input('ui_website_flg');?>
				クライアント管理画面トップ <?php echo $this->Form->input('ui_client_flg');?>
				社内管理画面トップ <?php echo $this->Form->input('ui_admin_flg');?>
			</td>
		</tr>
		<tr>
			<th>公開・非公開</th>
			<td><?php echo $this->Form->input('delete_flg', array('type' => 'input', 'default' => 0, 'options' => $deleteFlgOptions)); ?></td>
		</tr>
	</table>
	<?php echo $this -> Form -> submit('新規登録', array('class' => 'btn btn-success ')); ?>
	<?php echo $this -> Form -> end(); ?>
</div>

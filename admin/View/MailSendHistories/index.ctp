<div class="updatedTables index">

<?php
echo $this->Form->create('MailSendHistories',array('action'=>'index'));
?>

<div id="selectDate">
<?php
	echo $this->Form->label('送信開始日時');
	echo $this->Form->year('MailSendHistories.from', 2023, date('Y')+1) . "年";
	echo $this->Form->month('MailSendHistories.from', array('monthNames' => false)) . "月";
	echo $this->Form->day('MailSendHistories.from') . "日";
?>
～
<?php
	echo $this->Form->year('MailSendHistories.to', 2023, date('Y')+1) . "年";
	echo $this->Form->month('MailSendHistories.to', array('monthNames' => false)) . "月";
	echo $this->Form->day('MailSendHistories.to') . "日";
?>
</div>

<?php
	echo $this->Form->input('MailSendHistories.send_status_id', array('options' => $mailStatusList, 'empty' => '---', 'label' => '送信ステータス'));
	echo $this->Form->input('MailSendHistories.mail_template_name', array('label' => '送信内容(テンプレート名)', 'maxlength' => '128'));
	echo $this->Form->button('検索', array('class' => 'btn btn-primary'));
	echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
	echo $this->Form->end();
?>

	<h3><?php echo __('一斉メール履歴'); ?></h3>
	<table class="table table-striped table-bordered table-condensed">
	<tr>
		<th><?php echo $this->Paginator->sort('id', 'Id', array('url'=>$postConditions)); ?></th>
		<th><?php echo $this->Paginator->sort('mail_template_id', '送信内容(テンプレート名)', array('url'=>$postConditions)); ?></th>
		<th><?php echo $this->Paginator->sort('send_status_id', '送信ステータス', array('url'=>$postConditions)); ?></th>
		<th><?php echo $this->Paginator->sort('create_staff_id', '登録者', array('url'=>$postConditions)); ?></th>
		<th><?php echo $this->Paginator->sort('create_datetime', '登録日時', array('url'=>$postConditions)); ?></th>
		<th><?php echo $this->Paginator->sort('update_staff_id', '更新者', array('url'=>$postConditions)); ?></th>
		<th><?php echo $this->Paginator->sort('send_start_datetime', '送信開始日時', array('url'=>$postConditions)); ?></th>
		<th></th>
	</tr>
	<?php
	foreach ($mailSendHistories as $mailSendHistory) {
	?>
	<tr>
		<td><?php echo h($mailSendHistory['MailSendHistory']['id']); ?>&nbsp;</td>
		<td><?php echo h($mailSendHistory['MailSendHistory']['mail_template_name']); ?>&nbsp;</td>
		<td><?php echo h($mailStatusList[$mailSendHistory['MailSendHistory']['send_status_id']]); ?>&nbsp;</td>
		<td><?php echo h($staffList[$mailSendHistory['MailSendHistory']['create_staff_id']]); ?>&nbsp;</td>
		<td><?php echo h($mailSendHistory['MailSendHistory']['create_datetime']); ?>&nbsp;</td>
		<td><?php echo h($staffList[$mailSendHistory['MailSendHistory']['update_staff_id']]); ?>&nbsp;</td>
		<td><?php echo h($mailSendHistory['MailSendHistory']['send_start_datetime']); ?>&nbsp;</td>
		<td>
			<?php 
			echo $this->Html->link('詳細', array('action' => 'detail', $mailSendHistory['MailSendHistory']['id']), array('class' => 'btn btn-info'));
			?>
		</td>
	</tr>
	<?php
	}
	?>
	</table>
	<?php
	$pageParams = $this->Paginator->params();
	if (!empty($pageParams) && $pageParams['pageCount'] > 1) {
	?>
	<p>
	<?php
		echo $this->Paginator->counter(array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
			)
		);
	?>
	</p>

	<div class="pagination">
		<ul class="pager">
			<?php
				$this->Paginator->options(array('url' => $postConditions));
				echo '<li class=\"previous\">'.$this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')). '</li>';
				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
				echo '<li class=\"next\">'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>
	<?php
	}
	?>
</div>

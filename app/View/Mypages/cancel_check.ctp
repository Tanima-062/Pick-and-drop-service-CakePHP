<div id="renewal" class="login-edit login wrap contents clearfix mypages-cancel_check_page">
<?php
	echo $this->element('progress_bar'); 
?>
	<h2 class="h-type03">キャンセル同意確認</h2>

<?php echo $this->Form->create('Reservation', array(
		'url' => '/mypages/cancel_finish/',
		'name' => 'reserve',
		'type' => 'post',
		'inputDefaults' => $inputDefaults)); ?>

	<!-- メールアドレス・電話番号の確認 -->
	<table class="pc-form-date margin-btm30">
		<!-- <tr>
			<th>キャンセル理由</th>
			<td>-->
			<?php //echo $reason[$this->data['Reservation']['cancel_reason_id']];?>
		<!-- </td>
		</tr>-->
		<tr>
			<th>キャンセル理由詳細</th>
			<td><?php echo str_replace(array("\r\n", "\n", "\r"), '<br>', $this->data['Reservation']['cancel_remark']); ?></td>
		</tr>
	</table>

	<p class="btn-submit rent-margin"><?php echo $this->Form->submit('予約をキャンセルする',
				 array('id' => 'rewrite',
				 		'name' => 'rewriteBtn',
				 		'class' => 'btn btn_bg_important btn_submit',
				 		'value' => 'rewrite',
						'div' => false,)); ?>
	</p>

	<?php echo $this->Form->hidden('cancel_reason_id', array('value' => h($this->data['Reservation']['cancel_reason_id']))); ?>
	<?php echo $this->Form->hidden('cancel_remark', array('value' => h($this->data['Reservation']['cancel_remark']))); ?>
	<?php echo $this->Form->hidden('reservation_key', array('value' => h($this->data['Reservation']['reservation_key']))); ?>
	<?php echo $this->Form->hidden('tel', array('value' => h($this->data['Reservation']['tel']))); ?>
<?php echo $this->Form->end(); ?>

<?php if (!empty($referer)) { ?>
	<div class="rent-margin-bottom btn btn_cancel">
	<?php echo $this->Html->link('戻る', $referer); ?>
	</div>
<?php } ?>

</div>

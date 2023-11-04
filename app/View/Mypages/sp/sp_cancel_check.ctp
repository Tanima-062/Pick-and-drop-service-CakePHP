

<div id="js-content">
	<h2 class="title_blue_line"><span>キャンセル同意確認</span></h2>

<?php echo $this->Form->create('Reservation', array(
		'url' => '/mypages/cancel_finish/',
		'name' => 'reserve',
		'type' => 'post',
		'inputDefaults' => $inputDefaults)); ?>

	<section class="plan_form inner">
		<table>
			<tr>
				<th>キャンセル理由詳細</th>
				<td><?php echo str_replace(array("\r\n", "\n", "\r"), '<br>',$this->data['Reservation']['cancel_remark']); ?></td>
			</tr>
		</table>
	</section>

	<p class="btn-submit"><?php echo $this->Form->submit('予約をキャンセルする',
				 array('id' => 'rewrite',
				 		'name' => 'rewriteBtn',
				 		'class' => 'btn bg_orange btn_bg_important',
				 		'value' => 'rewrite',
						'div' => false,)); ?>
	</p>

	<?php echo $this->Form->hidden('cancel_reason_id', array('value' => h($this->data['Reservation']['cancel_reason_id']))); ?>
	<?php echo $this->Form->hidden('cancel_remark', array('value' => h($this->data['Reservation']['cancel_remark']))); ?>
	<?php echo $this->Form->hidden('reservation_key', array('value' => h($this->data['Reservation']['reservation_key']))); ?>
	<?php echo $this->Form->hidden('tel', array('value' => h($this->data['Reservation']['tel']))); ?>
<?php echo $this->Form->end(); ?>

<?php if (!empty($referer)) { ?>
	<div class="ac inner mb20px">
	<?php echo $this->Html->link('戻る', $referer); ?>
	</div>
<?php } ?>

</div>

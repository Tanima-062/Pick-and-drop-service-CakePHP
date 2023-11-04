<div class="contents">
	<h3>メール置換文字一覧</h3>
	<?php echo $this->Form->create('MailTemplates', array('url' => array('action' => 'mail_replace_string_list'), 'type' => 'post')); ?>
	<table class="table-bordered table-condensed">
		<tr>
			<th>サンプル用予約番号</th>
			<td><?php echo $this->Form->input('reservationKey', array('div' => false, 'label' => false));?></td>
		</tr>
	</table>
	<br>
	<?php
	echo $this->Form->submit('具体例置き換え', array('class' => 'btn btn-primary', 'div' => false));
	echo $this->Form->end();
	?>
	<?php 
		if (!empty($MailReplaceStrings) && count($MailReplaceStrings) > 0) {
	?>
	<table class="table table-bordered">
		<tr>
			<th>名前</th>
			<th>置換文字</th>
			<th>具体例</th>
		</tr>
		<?php 
			foreach ($MailReplaceStrings as $key => $MailReplaceString) {
		?>
		<tr>
			<td><?php echo $MailReplaceString['name']; ?></td>
			<td><?php echo $MailReplaceString['search_string']; ?></td>
			<td><?php echo str_replace("\n", "<br>", $MailReplaceString['example']); ?></td>
		</tr>
		<?php 
			}
		?>
	</table>
	<?php 
		}
	?>
</div>

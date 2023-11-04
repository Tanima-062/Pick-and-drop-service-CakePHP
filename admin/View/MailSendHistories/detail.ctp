<div class="clients form">
	<?php
	echo $this->Form->create('MailSendHistory', array('inputDefaults' => array('label' => false), 'enctype' => 'multipart/form-data'));
	?>

	<h3>一斉メール履歴詳細</h3>
	<table class="table table-bordered">
		<tr>
			<th class="span3">Id</th>
			<td><?php echo h($mailSendHistory['MailSendHistory']['id']); ?></td>
		</tr>
		<tr>
			<th>宛先一覧</th>
			<td><?php echo $this->Form->submit('宛先csvダウンロード', array('class' => 'btn btn-warning', 'name' => 'getTargetCsv', 'value' => '1', 'div' => false)); ?></td>
		</tr>
		<tr>
			<th>送信内容(テンプレートカテゴリ名)</th>
			<td>
				<?php
					if ($mailSendHistory['MailSendHistory']['send_status_id'] == '0') {
						echo $this->Form->input('mail_template_category_id', array('options' => $categoryList, 'empty'=>'---'));
					} else {
						echo h($mailSendHistory['MailSendHistory']['mail_template_category_name']);
					}
				?>
			</td>
		</tr>
		<tr>
			<th>送信内容(テンプレート名)</th>
			<td>
				<?php
					if ($mailSendHistory['MailSendHistory']['send_status_id'] == '0') {
						echo $this->Form->input('mail_template_id', array('options' => $templateList, 'empty'=>'---'));
					} elseif ($mailSendHistory['MailSendHistory']['send_status_id'] == '2') {
						echo $this->Form->hidden('mail_template_id', array('value' => $mailSendHistory['MailSendHistory']['mail_template_id']));
						echo h($mailSendHistory['MailSendHistory']['mail_template_name']);
					} else {
						echo h($mailSendHistory['MailSendHistory']['mail_template_name']);
					}
				?>
			</td>
		</tr>
		<tr>
			<th>送信内容(From)</th>
			<td id="mailFrom">
				<?php
					if ($mailSendHistory['MailSendHistory']['send_status_id'] != '0') {
						echo h($mailSendHistory['MailSendHistory']['mail_template_from']);
					}
				?>
			</td>
		</tr>
		<tr>
			<th>送信内容(件名)</th>
			<td id="mailSubject">
				<?php
					if ($mailSendHistory['MailSendHistory']['send_status_id'] != '0') {
						echo h($mailSendHistory['MailSendHistory']['mail_template_subject']);
					}
				?>
			</td>
		</tr>
		<tr>
			<th>送信内容(本文)</th>
			<td id="mailContent">
				<?php
					if ($mailSendHistory['MailSendHistory']['send_status_id'] != '0') {
						echo str_replace("\n", "<br/>", $mailSendHistory['MailSendHistory']['mail_template_content']);
					}
				?>
			</td>
		</tr>
		<tr>
			<th>送信ステータス</th>
			<td><?php echo h($mailStatusList[$mailSendHistory['MailSendHistory']['send_status_id']]); ?></td>
		</tr>
		<tr>
			<th>登録者</th>
			<td><?php echo h($staffList[$mailSendHistory['MailSendHistory']['create_staff_id']]); ?></td>
		</tr>
		<tr>
			<th>登録日時</th>
			<td><?php echo h($mailSendHistory['MailSendHistory']['create_datetime']); ?></td>
		</tr>
		<tr>
			<th>更新者</th>
			<td><?php echo h($staffList[$mailSendHistory['MailSendHistory']['update_staff_id']]); ?></td>
		</tr>
		<tr>
			<th>送信開始日時</th>
			<td><?php echo h($mailSendHistory['MailSendHistory']['send_start_datetime']); ?></td>
		</tr>
		<tr>
			<th>送信終了日時</th>
			<td><?php echo h($mailSendHistory['MailSendHistory']['send_end_datetime']); ?></td>
		</tr>

	</table>
        <span class="left">
                <?php echo $this->Html->link('一斉メール履歴一覧へ戻る', array('action' => 'index'), array('class' => 'btn btn-info'));?>
        </span>
	<?php if ($waitMailSendCount > 0) { ?>
		<?php if ($mailSendHistory['MailSendHistory']['send_status_id'] == '0' || $mailSendHistory['MailSendHistory']['send_status_id'] == '2') { ?>
		<div class="right">別のメールが送信中のため、送信できません。</div>
		<?php } ?>
	<?php } else { ?>
		<?php if ($mailSendHistory['MailSendHistory']['send_status_id'] == '0') { ?>
		<div class="right">
			<?php echo $this->Form->submit('メール送信', array('class' => 'btn btn-success', 'name' => 'submitHistory', 'value' => '1')); ?>
			<?php echo $this->Form->submit('宛先削除', array('class' => 'btn btn-danger', 'name' => 'deleteHistory', 'value' => '1')); ?>
		</div>
		<?php } elseif ($mailSendHistory['MailSendHistory']['send_status_id'] == '2') { ?>
		<div class="right">
			<?php echo $this->Form->submit('メール再送信', array('class' => 'btn btn-success', 'name' => 'submitHistory', 'value' => '1')); ?>
		</div>
		<?php } ?>
	<?php } ?>
	<?php echo $this->Form->end(); ?>
</div>

<script>
	$('#MailSendHistoryMailTemplateCategoryId').on('change', function () {
		$('#MailSendHistoryMailTemplateId').html('');
		$('#mailFrom').html('');
		$('#mailSubject').html('');
		$('#mailContent').html('');

		categoryId = $(this).val();
		$.ajax({
			type: "GET",
			url: "/rentacar/admin/MailSendHistories/getTemplateList/" + categoryId + "/",
			success: function(templateList) {
				$('#MailSendHistoryMailTemplateId').html(templateList);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("テンプレート一覧を取得できませんでした。\nもう一度カテゴリを選び直してください");
			}
		});

	});
	$('#MailSendHistoryMailTemplateId').on('change', function () {
		$('#mailFrom').html('');
		$('#mailSubject').html('');
		$('#mailContent').html('');

		templateId = $(this).val();
		if (templateId == '') {
			return false;
		}
		$.ajax({
			type: "GET",
			url: "/rentacar/admin/MailSendHistories/getTemplate/" + templateId + "/" + <?php echo $mailSendHistory['MailSendHistory']['id']; ?> + "/",
			success: function(templateData) {
				var templateArr = JSON.parse(templateData);
				$('#mailFrom').html(templateArr['mail_from']);
				$('#mailSubject').html(templateArr['mail_subject']);
				$('#mailContent').html(templateArr['mail_content']);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert("テンプレート情報を取得できませんでした。\nもう一度テンプレートを選び直してください");
			}
		});

	});
</script>

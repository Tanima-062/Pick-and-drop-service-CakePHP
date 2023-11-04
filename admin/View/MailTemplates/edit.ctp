<div class="contents">
	<?php echo $this->Form->create('MailTemplate', array('inputDefaults' => array('label' => false), 'enctype' => 'multipart/form-data')); ?>
		<h3>メールテンプレート変更</h3>
		<table class="table table-bordered">
			<tr>
				<th>Id</th>
				<td>
					<?php echo $mailTemplate['MailTemplate']['id']; ?>
				</td>
			</tr>
			<tr>
				<th>カテゴリ</th>
				<td>
					<?php
					if ($unsentMailcount > 0) {
						echo h($mailCategoryList[$mailTemplate['MailTemplate']['mail_template_category_id']]);
					} else {
						echo $this->Form->input(
						        'mail_template_category_id',
						        array(
						                'div' => false,
						                'style' => 'margin-bottom:0px;',
						                'label' => false,
						                'options' => array('' => '---') + $mailCategoryList,
						                'default' => $mailTemplate['MailTemplate']['mail_template_category_id'],
						                'empty' => false,
						        )
						);
					}
					?>
				</td>
			</tr>
			<tr>
				<th>テンプレート名</th>
				<td>
					<?php
					if ($unsentMailcount > 0) {
						echo h($mailTemplate['MailTemplate']['name']);
					} else {
						echo $this->Form->input('name', array('required' => true, 'div' => false, 'style' => 'width: 30%', 'default' => $mailTemplate['MailTemplate']['name']));
					}
					?>
				</td>
			</tr>
			<tr>
				<th>メールFrom</th>
				<td>
					<?php
					if ($unsentMailcount > 0) {
						echo h($mailTemplate['MailTemplate']['mail_from']);
					} else {
						echo $this->Form->input('mail_from', array('required' => true, 'div' => false, 'style' => 'width: 30%', 'default' => $mailTemplate['MailTemplate']['mail_from']));
					}
					?>
				</td>
			</tr>
			<tr>
				<th>メール件名</th>
				<td>
					<?php
					if ($unsentMailcount > 0) {
						echo h($mailTemplate['MailTemplate']['mail_subject']);
					} else {
						echo $this->Form->input('mail_subject', array('required' => true, 'div' => false, 'style' => 'width: 30%', 'default' => $mailTemplate['MailTemplate']['mail_subject']));
					}
					?>
				</td>
			</tr>
			<tr>
				<th>メール本文</th>
				<td>
					<?php
					if ($unsentMailcount > 0) {
						echo str_replace("\n", "<br/>", $mailTemplate['MailTemplate']['mail_content']);
					} else {
						echo $this->Form->input('mail_content', array('type' => 'textarea', 'required' => true, 'div' => false, 'style' => 'width: 50%', 'default' => $mailTemplate['MailTemplate']['mail_content']));
					}
					?>
					<br />
					<br />
					<?php 
					echo $this->Html->link('置換文字一覧', array('action' => 'mail_replace_string_list'), array('class' => 'btn btn-primary', 'target' => '_blank'));
					?>
				</td>
			</tr>
		</table>
		<span class="left">
			<?php echo $this->Html->link('メールテンプレート一覧へ戻る', array('action' => 'index'), array('class' => 'btn btn-info'));?>
		</span>
		<div class="right">
		<?php
		if ($unsentMailcount > 0) {
			echo $this->Form->submit('編集不可', array('class' => 'btn btn-danger', 'disabled'));
		} else {
			echo $this->Form->submit('保存する', array('class' => 'btn btn-success '));
		}
		?>
		</div>
	<?php echo $this->Form->end(); ?>
</div>

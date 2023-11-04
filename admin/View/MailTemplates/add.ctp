<div class="contents">
	<?php echo $this->Form->create('MailTemplate', array('inputDefaults' => array('label' => false), 'enctype' => 'multipart/form-data')); ?>
		<h3>メールテンプレート追加</h3>
		<table class="table table-bordered">
			<tr>
				<th>カテゴリ</th>
				<td>
					<?php
					echo $this->Form->input(
					        'mail_template_category_id',
					        array(
					                'div' => false,
					                'style' => 'margin-bottom:0px;',
					                'label' => false,
					                'options' => array('' => '---') + $mailCategoryList,
					                'empty' => false,
					        )
					);
					?>
				</td>
			</tr>
			<tr>
				<th>テンプレート名</th>
				<td>
					<?php echo $this->Form->input('name', array('required' => true, 'div' => false, 'style' => 'width: 30%')); ?>
				</td>
			</tr>
			<tr>
				<th>メールFrom</th>
				<td>
					<?php echo $this->Form->input('mail_from', array('required' => true, 'div' => false, 'style' => 'width: 30%')); ?>
				</td>
			</tr>
			<tr>
				<th>メール件名</th>
				<td>
					<?php echo $this->Form->input('mail_subject', array('required' => true, 'div' => false, 'style' => 'width: 30%')); ?>
				</td>
			</tr>
			<tr>
				<th>メール本文</th>
				<td>
					<?php echo $this->Form->input('mail_content', array('type' => 'textarea', 'required' => true, 'div' => false, 'style' => 'width: 50%')); ?>
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
			<?php echo $this->Form->submit('登録', array('class' => 'btn btn-success ')); ?>
		</div>
	<?php echo $this->Form->end(); ?>
</div>

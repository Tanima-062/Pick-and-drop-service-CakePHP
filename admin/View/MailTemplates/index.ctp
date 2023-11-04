<div class="contents index">
	<h2>メールテンプレートマスタ</h2>
	<?php echo $this->Form->create('MailTemplate', array('type' => 'get', 'url' => '.')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>カテゴリ</th>
				<td>
					<?php 
					echo $this->Form->input(
						'mail_template_category_id',
						array(
							'div' => false,
							'style' => 'margin-top:10px;',
							'label' => false,
							'options' => array('' => '---') + $mailCategoryList,
							'empty' => false,
							'default' => $mailTemplateCategoryId
						)
					);
					?>
				</td>
				<td><?php echo $this->Html->link('カテゴリ新規登録', 'category_add', array('class' => 'btn btn-success')); ?></td>
			</tr>
		</table>
	<?php echo $this->Form->end(); ?>
	<table class="table table-bordered">
		<thead>
			<tr class="btn-primary">
				<th>Id</th>
				<th>カテゴリ</th>
				<th>テンプレート名</th>
				<th>From</th>
				<th>メール件名</th>
				<th>メール本文</th>
				<th class="actions"><?php echo $this->Html->link('新規登録', 'add', array('class' => 'btn btn-success')); ?></th>
			</tr>
		</thead>
		<tbody id="sortable-div">
		<?php
			foreach ($mailTemplateList as $mailTemplate):
		?>
			<tr id="<?php echo $mailTemplate['MailTemplate']['id']; ?>" class="ui-state">
				<td><?php echo $mailTemplate['MailTemplate']['id']; ?></td>
				<td><?php echo h($mailTemplate['MailTemplateCategory']['name']); ?></td>
				<td><?php echo h($mailTemplate['MailTemplate']['name']); ?></td>
				<td><?php echo h($mailTemplate['MailTemplate']['mail_from']); ?></td>
				<td><?php echo h($mailTemplate['MailTemplate']['mail_subject']); ?></td>
				<td><?php echo str_replace("\n", "<br>", h($mailTemplate['MailTemplate']['mail_content'])); ?></td>
				<td><?php echo $this->Html->link(__('編集'), array('action' => 'edit', $mailTemplate['MailTemplate']['id']), array('class' => 'btn btn-warning'));?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件'))); ?>
<?php
$pageParams = $this->Paginator->params();
if (!empty($pageParams) && $pageParams['pageCount'] > 1) {
?>
	<div class="pagination">
		<ul>
		<?php
			echo $this->Paginator->prev('< ' . __('戻る'), array('tag' => 'li'), null, array('class' => 'prev disabled'));
			echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li'));
			echo $this->Paginator->next(__('次へ') . ' >', array('tag' => 'li'), null, array('class' => 'next disabled'));
		?>
		</ul>
	</div>
<?php
}
?>

</div>
<script>
	$('#MailTemplateMailTemplateCategoryId').change(function () {
		$('#MailTemplateMailTemplateCategoryId').prop('readonly', true);
		$(this).parents('form').submit();
	});
	// 今いるページをクリックしても移動させない
	$('.pagination').find('.disabled').children().click(function () {
		return false;
	});
</script>

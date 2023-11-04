<div class="contents index">
	<h2>トップコンテンツマスタ</h2>
	<?php echo $this->Form->create('Content', array('type' => 'get', 'url' => '.')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>カテゴリ</th>
				<td><?php echo $this->Form->input('contents_category_id', array('div' => false, 'label' => false, 'options' => array('' => '---') + $categoryList, 'empty' => false)); ?></td>
			</tr>
		</table>
		<br />
		<div style="float:left;padding:0px 20px 10px 0px;">
			<?php echo $this->Form->submit('検索する', array('class' => 'btn btn-primary')); ?>
		</div>
	<?php echo $this->Form->end(); ?>

	<table class="table table-bordered">
		<thead>
			<tr class="btn-primary">
				<th>Id</th>
				<th>画像</th>
				<th>カテゴリ</th>
				<th>記事タイトル</th>
				<th>記事説明</th>
				<th>URL</th>
				<th class="actions"><?php echo $this->Html->link('新規登録', 'add', array('class' => 'btn btn-success')); ?></th>
			</tr>
		</thead>
		<tbody id="sortable-div">
		<?php
			foreach ($contents as $content):
				$class = '';
				if(empty($content['Content']['is_published'])) {
					$class = 'gray';
				}
		?>
			<tr id="<?php echo $content['Content']['id']; ?>" class="ui-state <?php echo $class; ?>">
				<td><?php echo h($content['Content']['id']); ?></td>
				<td>
				<?php if (!empty($content['Content']['image'])) {
					echo $this->Html->image('../../img/contents/top/' . $content['Content']['image']);
				} ?>
				</td>
				<td><?php echo h($categoryList[$content['Content']['contents_category_id']]); ?></td>
				<td><?php echo h($content['Content']['title']); ?></td>
				<td><?php echo nl2br(h($content['Content']['description'])); ?></td>
				<td><?php echo h($content['Content']['url']); ?></td>
				<td class="actions">
					<?php echo $this->Html->link(__('編集'), array('action' => 'edit', $content['Content']['id']), array('class' => 'btn btn-warning')); ?>
					<?php echo $this->Form->postLink('削除', array('action' => 'delete', $content['Content']['id']), array('class'=>'btn btn-danger'), __('「%s」を削除しますか?', $content['Content']['title'])); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件'))); ?>

	<div class="pagination">
		<ul>
		<?php
			echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
			echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
			echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
		?>
		</ul>
	</div>

</div>
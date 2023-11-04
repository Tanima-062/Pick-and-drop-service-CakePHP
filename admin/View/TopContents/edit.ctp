<div class="contents">
	<?php echo $this->Form->create('Content', array('inputDefaults' => array('label' => false), 'enctype' => 'multipart/form-data')); ?>
		<h3>トップコンテンツ編集</h3>
		<?php echo $this->Form->hidden('id'); ?>
		<table class="table table-bordered">
			<tr>
				<th>カテゴリ</th>
				<td><?php echo $this->Form->input('contents_category_id', array('options' => $categoryList)); ?></td>
			</tr>
			<tr>
				<th>記事タイトル (20字以内)</th>
				<td>
					<?php echo $this->Form->input('title', array('required' => true, 'div' => false, 'maxlength' => 20, 'onkeyup' => 'strCount(value, "title_count");', 'style' => 'width: 30%')); ?>
					&nbsp<span id="title_count"><?php echo mb_strlen($this->request->data['Content']['title']); ?>字</span>
				</td>
			</tr>
			<tr>
				<th>記事説明 (70字以内)</th>
				<td>
					<?php echo $this->Form->input('description', array('type' => 'textarea', 'required' => true, 'div' => false, 'maxlength' => '70', 'onkeyup' => 'strCount(value, "description_count");', 'style' => 'width: 50%')); ?>
					&nbsp<span id="description_count" style="vertical-align: bottom"><?php echo mb_strlen($this->request->data['Content']['description']); ?>字</span>
				</td>
			</tr>
			<tr>
				<th>画像 (2MB未満)</th>
				<td>
				<?php
					if (!empty($this->request->data['Content']['image'])) {
						echo $this->Html->image('../../img/contents/top/' . $this->request->data['Content']['image']) . '<br />';
					}
					echo $this->Form->File('image_tmp');
				?>
				</td>
			</tr>
			<tr>
				<th>URL</th>
				<td>
				<?php
					echo $urlPrefix.'&nbsp';
					echo $this->Form->input('url', array('required' => true, 'div' => false, 'maxlength' => '128'));
				?>
				</td>
			</tr>
			<tr>
				<th>公開/非公開</th>
				<td><?php echo $this->Form->input('is_published', array('type' => 'input', 'default' => 0, 'options' => $isPublishedOptions)); ?></td>
			</tr>
		</table>
		<?php echo $this->Form->submit('編集する', array('class' => 'btn btn-success ')); ?>
	<?php echo $this->Form->end(); ?>
</div>
<script>
	function strCount(str, id) {
		document.getElementById(id,).innerHTML = str.length + "文字";
	}
</script>
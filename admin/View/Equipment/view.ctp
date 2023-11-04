<div class="equipment view">
<h3><?php  echo __('装備マスタ詳細'); ?></h3>
<table class="table table-striped table-bordered table-condensed">
	<tr>
		<td><?php echo __('装備ID'); ?></td>
		<td>
			<?php echo h($equipment['Equipment']['id']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('カテゴリ'); ?></td>
		<td>
			<?php echo h(!empty($optionCategories[$equipment['Equipment']['option_category_id']]) ? $optionCategories[$equipment['Equipment']['option_category_id']] : ''); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('装備名'); ?></td>
		<td>
			<?php echo h($equipment['Equipment']['name']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('画像'); ?></td>
		<td>
			<?php echo h($equipment['Equipment']['image_relative_url']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('説明'); ?></td>
		<td>
			<?php echo h($equipment['Equipment']['description']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('公開'); ?></td>
		<td>
			<?php echo h($isPublishedOptions[$equipment['Equipment']['is_published']]); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('作成日時'); ?></td>
		<td>
			<?php echo h($equipment['Equipment']['created']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('更新日時'); ?></td>
		<td>
			<?php echo h($equipment['Equipment']['modified']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('更新者'); ?></td>
		<td>
			<?php echo $equipment['Staff']['name'];?>
			&nbsp;
		</td>
	</tr>


</table>
</div>
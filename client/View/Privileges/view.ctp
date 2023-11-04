<div class="privileges view">
<h3><?php  echo '特典の詳細'; ?></h3>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<td><?php echo __('特典名'); ?></td>
			<td><?php echo h($privilege['Privilege']['name']); ?>&nbsp;</td>
		</tr>

		<!--
		<tr>
			<td><?php echo __('画像'); ?></td>
			<td>
				<?php if(!empty($privilege['Privilege']['image_relative_url'])){?>
					<img src="<?php echo '/img/privilege_img/'.$privilege['Privilege']['image_relative_url'];?>" width="200px">
				<?php }?>&nbsp;
			</td>
		</tr>
		<tr>
			<td><?php echo __('特典説明'); ?></td>
			<td><?php echo h($privilege['Privilege']['description']); ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo __('税抜料金'); ?></td>
			<td><?php echo h($privilege['Privilege']['excluding_tax']); ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo __('消費税'); ?></td>
			<td><?php echo h($privilege['Privilege']['tax']); ?>&nbsp;</td>
		</tr>
		-->

		<tr>
			<td><?php echo __('料金'); ?></td>
			<td><?php echo h($privilege['Privilege']['price']); ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo __('上限'); ?></td>
			<td><?php echo h($privilege['Privilege']['maximum']); ?>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo __('単位'); ?></td>
			<td><?php echo h($privilege['Privilege']['unit_name']); ?>&nbsp;</td>
		</tr>
	</table>
</div>
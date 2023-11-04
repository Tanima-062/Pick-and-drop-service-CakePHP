<div class="carModels view">
<h3><?php  echo __('車種マスタ詳細'); ?></h3>
<table class="table table-striped table-bordered table-condensed">
	<tr>
		<td class="span2"><?php echo __('ID'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['id']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('自動車メーカーID'); ?></td>
		<td>
			<?php echo h($automakerList[$carModel['CarModel']['automaker_id']]); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('車種名'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['name']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('スーツケース'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['trunk_space']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('ゴルフバッグ'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['golf_bag']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('排気量'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['displacement']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('法定定員'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['capacity']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('推奨定員'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['recommended_capacity']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('燃費'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['mileage']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('ドア数'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['door']); ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td><?php echo __('画像'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['image_relative_url']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>
		<td><?php echo __('説明'); ?></td>
		<td>
			<?php echo h($carModel['CarModel']['description']); ?>
			&nbsp;
		</td>
	</tr>

	<tr>

		<td><?php echo __('更新者'); ?></td>
		<td>
			<?php echo $carModel['Staff']['name']; ?>
			&nbsp;
		</td>
	</tr>

	<tr>

		<td><?php echo __('削除ﾌﾗｸﾞ'); ?></td>
		<td>
			<?php echo h($deleteFlgOptions[$carModel['CarModel']['delete_flg']]); ?>
			&nbsp;
		</td>
	</tr>

</table>
</div>
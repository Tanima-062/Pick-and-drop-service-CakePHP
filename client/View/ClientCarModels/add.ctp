<h3>車種登録</h3>

<?php echo $this->Form->create('CarModel', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
<table class="table-striped table-condensed">
	<tr>
		<td>メーカー名</td>
		<td><?php echo $this->Form->select('automaker_id', $automakerLists, array ('empty' => false)); ?></td>
	</tr>
</table>
<div>
	<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/ClientCarModels/', array ('escape' => false)); ?>
	<?php echo $this->Form->submit('検索', array ('class' => 'btn btn-success', 'div' => false)); ?>
</div>
<?php echo $this->Form->end(); ?>

<?php if (!empty($automaker_id)) { ?>
	<?php echo $this->Form->create('ClientCarModel', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th>選択</th>
				<th>車種名</th>
				<th>メーカー</th>
				<th>排気量(cc)</th>
				<th>乗車定員(人)</th>
				<th>スーツケース(個)</th>
				<th>ゴルフバッグ(個)</th>
				<th>燃費(km/L)</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if (!empty($carModelLists)) {
				$i = 0;
				foreach ($carModelLists as $carModelList) {
				?>
					<tr>
						<td>
							<?php echo $this->Form->checkbox($i . '.add_flg', array('value' => 1));?>
							<?php echo $this->Form->hidden($i . '.car_model_id', array ('value' => $carModelList['CarModel']['id'])); ?>
							<?php echo $this->Form->hidden('CarModel.automaker_id', array ('value' => $automaker_id)); ?>
						</td>
						<td><?php echo $carModelList['CarModel']['name']; ?></td>
						<td><?php echo $automakerLists[$carModelList['CarModel']['automaker_id']]; ?></td>
						<td><?php echo $carModelList['CarModel']['displacement']; ?></td>
						<td><?php echo $carModelList['CarModel']['capacity']; ?></td>
						<td><?php echo $carModelList['CarModel']['trunk_space']; ?></td>
						<td><?php echo $carModelList['CarModel']['golf_bag']; ?></td>
						<td><?php echo $carModelList['CarModel']['mileage']; ?></td>
						<td></td>
					</tr>
				<?php
					$i ++;
				}
			}
			?>
		</tbody>
	</table>
	<div>
		<?php echo $this->Html->link('<span class="btn btn-warning">戻る</span>', '/ClientCarModels/', array ('escape' => false)); ?>
		<?php echo $this->Form->submit('更新', array ('class' => 'btn btn-success', 'div' => false)); ?>
	</div>
	<?php echo $this->Form->end(); ?>
<?php } ?>
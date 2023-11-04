<h3>車種一覧</h3>

<?php echo $this->Session->flash('error'); ?>

<?php echo $this->Form->create('ClientCarModel', array ('inputDefaults' => array('label' => false, 'div' => false, 'legend' => false),)); ?>
<p>
	<?php echo $this->Html->link('<span class="btn btn-success">新規車種登録</span>', '/ClientCarModels/add/', array ('escape' => false)); ?>
	<?php echo $this->Form->submit('削除', array ('class' => 'btn btn-danger', 'div' => false)); ?>
</p>

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
		if (!empty($clientCarModelLists)) {
			$i = 0 ;
			foreach ($clientCarModelLists as $clientCarModelList) {
			?>
				<tr>
					<td>
						<?php echo $this->Form->checkbox($i . '.delete_flg', array('value' => 1));?>
						<?php echo $this->Form->hidden($i . '.id', array ('value' => $clientCarModelList['ClientCarModel']['id'])); ?>
						<?php echo $this->Form->hidden($i . '.car_model_id', array ('value' => $clientCarModelList['ClientCarModel']['car_model_id'])); ?>
					</td>
					<td><?php echo $clientCarModelList['CarModel']['name']; ?></td>
					<td><?php echo $clientCarModelList['Automaker']['name']; ?></td>
					<td><?php echo $clientCarModelList['CarModel']['displacement']; ?></td>
					<td><?php echo $clientCarModelList['CarModel']['capacity']; ?></td>
					<td><?php echo $clientCarModelList['CarModel']['trunk_space']; ?></td>
					<td><?php echo $clientCarModelList['CarModel']['golf_bag']; ?></td>
					<td><?php echo $clientCarModelList['CarModel']['mileage']; ?></td>
					<td></td>
				</tr>
			<?php
				$i ++;
			}
		}
		?>
	</tbody>
</table>
<?php echo $this->Form->end(); ?>
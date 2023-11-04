<div class="landmarks">
	<?php echo $this->Form->create('Landmark', array('inputDefaults' => array('label' => false))); ?>
	<h3>ランドマーク追加</h3>
	<table class="table table-bordered">
		<tr>
			<th>都道府県</th>
			<td><?php echo $this->Form->input('prefecture_id', array('options' => $prefectureList)); ?></td>
		</tr>
		<tr>
			<th>ランドマークカテゴリ</th>
			<td>
				<?php echo $this->Form->input('landmark_category_id', array('options' => $landmarkCategoryList)); ?>
			</td>
		</tr>
		<tr>
			<th>ランドマーク名</th>
			<td>
				<?php echo $this->Form->input('name', array('required' => true, 'style' => 'width: 50%;')); ?>
			</td>
		</tr>
		<tr>
			<th>略称</th>
			<td>
				<?php echo $this->Form->input('short_name', array('required' => true)); ?>
				<span class="text-error">※都道府県ページで使用されます。</span>
			</td>
		</tr>
		<tr>
			<th>リンク用URL</th>
			<td><?php echo $this->Form->input('link_cd', array('pattern' => Constant::PATTERN_LINKCD, 'required' => false)); ?></td>
		</tr>
		<tr>
			<th>緯度</th>
			<td><?php echo $this->Form->input('latitude', array('pattern' => Constant::PATTERN_GEOCODE)); ?></td>
		</tr>
		<tr>
			<th>経度</th>
			<td><?php echo $this->Form->input('longitude', array('pattern' => Constant::PATTERN_GEOCODE)); ?></td>
		</tr>
		<tr class="airportItem">
			<th>空港ID</th>
			<td><?php echo $this->Form->input('airport_id', array('type' => 'text', 'pattern' => '\d+')); ?></td>
		</tr>
		<tr class="airportItem">
			<th>IATAコード</th>
			<td><?php echo $this->Form->input('iata_cd', array('type' => 'text', 'pattern' => '^[A-Z]+$')); ?></td>
		</tr>
		<tr class="airportItem">
			<th>トラベルコID</th>
			<td><?php echo $this->Form->input('travelko_id', array('type' => 'text', 'pattern' => '\d+')); ?></td>
		</tr>
		<tr>
			<th>公開/非公開</th>
			<td>
				<?php echo $this->Form->input('delete_flg', array('default' => 0, 'options' => $deleteFlgOptions)); ?>
			</td>
		</tr>
		<tr>
			<th>ソート</th>
			<td>
				<?php echo $this->Form->input('sort', array('required' => false)); ?>
			</td>
		</tr>
	</table>
	<?php echo $this->Form->submit('新規登録', array('class' => 'btn btn-success ')); ?>
	<?php echo $this->Form->end(); ?>
</div>
<script>
$('#LandmarkLandmarkCategoryId').change(function() {
	if ($(this).val() == '1') {
		$('.airportItem').show();
	} else {
		$('.airportItem').hide();
	}
});
$('#LandmarkLandmarkCategoryId').trigger('change');
</script>
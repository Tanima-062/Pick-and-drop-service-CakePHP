<div class="landmarks">
	<?php echo $this->Form->create('Station', array('inputDefaults' => array('label' => false))); ?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>駅編集</h3>
	<?php echo $this->Form->hidden('id'); ?>
	<table class="table table-bordered">
		<tr>
			<th>駅名</th>
			<td><?php echo $this->Form->input('name', array('required' => true)); ?></td>
		</tr>
		<tr>
			<th>都道府県</th>
			<td><?php echo $this->Form->input('prefecture_id', array('options' => $prefectureList)); ?></td>
		</tr>
		<tr>
			<th>市区町村名</th>
			<td><?php echo $this->Form->input('city_id', array('options' => $cityList)); ?></td>
		</tr>
		<tr>
			<th>リンク用URL</th>
			<td><?php echo $this->Form->input('url', array('pattern' => Constant::PATTERN_IDPASS, 'required' => false)); ?></td>
		</tr>
		<tr>
			<th>緯度</th>
			<td><?php echo $this->Form->input('latitude', array('pattern' => Constant::PATTERN_GEOCODE, 'required' => true)); ?></td>
		</tr>
		<tr>
			<th>経度</th>
			<td><?php echo $this->Form->input('longitude', array('pattern' => Constant::PATTERN_GEOCODE, 'required' => true)); ?></td>
		</tr>
		<tr>
			<th>主要駅</th>
			<td><?php echo $this->Form->input('major_flg', array('type' => 'checkbox', 'label' => '主要駅')); ?></td>
		</tr>
		<tr>
			<th>地図</th>
			<td><?php echo $this->Form->input('pref_map_flg', array('type' => 'checkbox', 'label' => '表示する')); ?></td>
		</tr>
		<tr>
			<th>駅タイプ</th>
			<td><?php echo $this->Form->input('type', array('options' => $stationTypes)); ?></td>
		</tr>
		<tr>
			<th>トラベルコID</th>
			<td><?php echo $this->Form->input('travelko_id', array('type' => 'text')); ?></td>
		</tr>
		<tr>
			<th>公開・非公開</th>
			<td><?php echo $this->Form->input('delete_flg', array('type' => 'input', 'default' => 0, 'options' => $deleteFlgOptions)); ?></td>
		</tr>
		<tr>
			<th>ソート</th>
			<td>
				<?php echo $this->Form->input('sort', array('required' => false)); ?>
			</td>
		</tr>
	</table>
	<?php echo $this->Form->submit('編集する', array('class' => 'btn btn-success ')); ?>
	<?php echo $this->Form->end(); ?>
</div>
<script>
$(function(){
	// 都道府県セレクトボックス
	$('#StationPrefectureId').change(function() {
		var prefectureId = $(this).val();
		setCityList(prefectureId);
	});
	// 都道府県に応じて市区町村セレクトボックスを設定する
 	function setCityList(prefecture) {
 		$('#StationCityId').empty();
		$.ajax({
			type: "GET",
			url: "/rentacar/admin/stations/get_city_list/" + prefecture + "/",
			success: function(city) {
				var cityList = JSON.parse(city);
				var options = new Array();
				for (key in cityList) {
					options.push(new Option(cityList[key], key));
				}
				$('#StationCityId').append(options);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				//alert(XMLHttpRequest.status);
				//alert(textStatus);
				//alert(errorThrown.message);
			}
		});
    }
});
</script>

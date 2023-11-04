<div class="cities">
	<?php echo $this -> Form -> create('City', array('inputDefaults' => array('label' => false))); ?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>市区町村編集</h3>
	<?php echo $this -> Form -> hidden('id'); ?>
	<table class="table table-bordered">
		<tr>
			<th>都道府県</th>
			<td><?php echo $this -> Form -> input('prefecture_id', array('options' => $prefectureList)); ?></td>
		</tr>
		<tr>
			<th>エリア名</th>
			<td><?php echo $this -> Form -> input('area_id', array('options' => $areaList)); ?></td>
		</tr>
		<tr>
			<th>市区町村名</th>
			<td><?php echo $this -> Form -> input('name', array('required' => true)); ?></td>
		</tr>
		<tr>
			<th>リンク用URL</th>
			<td><?php echo $this -> Form -> input('link_cd', array('pattern' => Constant::PATTERN_IDPASS, 'required' => false)); ?></td>
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
			<th>トラベルコID</th>
			<td><?php echo $this -> Form -> input('travelko_city_id', array('type' => 'text', 'maxlength' => '10', 'pattern' => '\d+')); ?></td>
		</tr>
		<tr>
			<th>公開・非公開</th>
			<td><?php echo $this -> Form -> input('delete_flg', array('type' => 'input', 'default' => 0, 'options' => $deleteFlgOptions)); ?></td>
		</tr>
	</table>
	<?php echo $this -> Form -> submit('編集する', array('class' => 'btn btn-success ')); ?>
	<?php echo $this -> Form -> end(); ?>
</div>
<script>
$(function(){
	// 都道府県セレクトボックス
	$('#CityPrefectureId').change(function() {
		var prefectureId = $(this).val();
		setAreaList(prefectureId);
	});
	// 都道府県に応じてエリアセレクトボックスを設定する
 	function setAreaList(prefecture) {
 		$('#CityAreaId').empty();
		$.ajax({
			type: "GET",
			url: "/rentacar/admin/cities/get_area_list/" + prefecture + "/",
			success: function(area) {
				var areaList = JSON.parse(area);
				var options = new Array();
				for (key in areaList) {
					options.push(new Option(areaList[key], key));
				}
				$('#CityAreaId').append(options);
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

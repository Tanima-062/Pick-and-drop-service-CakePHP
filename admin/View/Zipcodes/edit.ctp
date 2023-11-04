<div class="zipcodes">
	<?php echo $this->Form->create('Zipcode', array('inputDefaults' => array('label' => false))); ?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>郵便番号編集</h3>
	<?php echo $this->Form->hidden('id'); ?>
	<table class="table table-bordered">
		<tr>
			<th>郵便番号</th>
			<td><?php echo $this->Form->input('zipcode', array('required' => true, 'maxlength' => '7', 'pattern' => '\d{7}')); ?></td>
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
			<th>公開・非公開</th>
			<td><?php echo $this->Form->input('delete_flg', array('type' => 'input', 'default' => 0, 'options' => $deleteFlgOptions)); ?></td>
		</tr>
	</table>
	<?php echo $this->Form->submit('編集する', array('class' => 'btn btn-success ')); ?>
	<?php echo $this->Form->end(); ?>
</div>
<script>
$(function(){
	// 都道府県セレクトボックス
	$('#ZipcodePrefectureId').change(function() {
		var prefectureId = $(this).val();
		setCityList(prefectureId);
	});
	// 都道府県に応じて市区町村セレクトボックスを設定する
 	function setCityList(prefecture) {
 		$('#ZipcodeCityId').empty();
		$.ajax({
			type: "GET",
			url: "/rentacar/admin/zipcodes/get_city_list/" + prefecture + "/",
			success: function(city) {
				var cityList = JSON.parse(city);
				var options = new Array();
				for (key in cityList) {
					options.push(new Option(cityList[key], key));
				}
				$('#ZipcodeCityId').append(options);
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

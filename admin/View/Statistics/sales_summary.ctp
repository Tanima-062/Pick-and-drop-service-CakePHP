<?php echo $this->Form->create('Reservation'); ?>
期間　<?php echo $this->Form->select('segment', array('月別', '日別'), array('default' => '0')); ?>
<span id="selectYearArea">
	<?php echo $this->Form->year('date', 2016, date('Y') + 1, array('empty' => false)); ?>年
</span>
<span id="selectMonthArea">
	<?php echo $this->Form->month('date', array('empty' => false, 'monthNames' => false)); ?>月
</span>
<span id="selectDayArea">
	<?php echo $this->Form->day('date', array('empty' => false)); ?>日
</span>
<div>
エリア 
<span>	
<?php echo $this->Form->select('areaTypeList',$areaTypeList, array('default' => '0')); ?>
</span>

<span class="prefecture">
<?php echo $this->Form->select('prefectureList',$prefectureList, array('default' => '1')); ?>
</span>
<span class="prefecture">
<?php echo $this->Form->select('areaList',$areaList, array('default' => '0')); ?>
</span>

<span class="region">
<?php echo $this->Form->select('regionList',$regionList, array('default' => '')); ?>
</span>

<span class="airport">
<?php echo $this->Form->select('airportList',$airportList, array('default' => '0')); ?>
</span>

<span class="station">
<?php echo $this->Form->select('stationList',$stationList, array('default' => '0')); ?>
</span>
</div>
<div>
ソート 
<span>	
<?php echo $this->Form->select('sortList',$sortList, array('default' => '0')); ?>
</span>
<span>
<?php echo $this->Form->select('sortType', array('','昇順', '降順'), array('default' => '0')); ?>
</span>
</div>
<div class="control-group">
<?php
	echo $this->Form->button('絞り込む', array('class' => 'btn btn-primary'));
	echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset', 'style' => 'margin-right: 20px;'));
	echo $this->Form->submit('csv出力', array('class' => 'btn btn-warning', 'name' => 'getCsv', 'value' => '1', 'div' => false));
?>
</div>
<?php echo $this->Form->end(); ?>
<style>
	tr:nth-child(even) {
		background-color:#F9F9F9;
	}

	td.info {
		background-color: #E0FFFF;
	}

	td.error {
		background-color: #FFE4E1
	}

	select {
		width: 100px;
	}

	#selectMonthArea, #selectDayArea {
		display: none;
	}
</style>
<script>
	$('#ReservationSegment').change(function () {
		if ($(this).val() == 1) {
			$("#selectMonthArea").show();
		} else {
			$("#selectMonthArea").hide();
		}
	});
	$('#ReservationSegment').trigger('change');

	var area_arr = JSON.parse('<?php echo $area_arr ?>');

	$('#ReservationAreaTypeList').change(function () {
		$('.prefecture').hide();
		$('.region').hide();
		$('.airport').hide();
		$('.station').hide();

		if ($(this).val() == 1) {
			$('.prefecture').show();
		} else if($(this).val() == 2) {
			$('.region').show();
		} else if($(this).val() == 3) {
			$('.airport').show();
		} else if($(this).val() == 4) {
			$('.station').show();
		}
	});

	$('#ReservationAreaTypeList').trigger('change');

	$('#ReservationPrefectureList').change(function () {
		$('#ReservationAreaList').empty();
		$('#ReservationAreaList').append('<option value="0">すべてのエリア</option>');
		var prefecture_id = $(this).val();
		var areas = area_arr[prefecture_id];
		for (var area_id in areas) {
			var option = $("<option>").val(area_id).text(areas[area_id]);
			$('#ReservationAreaList').append(option);
		}
	});

	// リセット追加処理
	$(document).on('click', '.btn-reset', function() {
		$('#selectMonthArea').hide();
		$('#selectDayArea').hide();
		$('.prefecture').hide();
		$('.region').hide();
		$('.airport').hide();
		$('.station').hide();
		// 後処理
		setTimeout(function() {
			$('#selectYearArea').find('select').prop("selectedIndex", 1);
		}, 1);
	});
</script>
<?php
if (empty($this->request->data['Reservation']['segment'])) {
	// 月別
	echo $this->element('Statistics/sales_summary_monthly');
} else if ($this->request->data['Reservation']['segment'] == '1') {
	// 日別
	echo $this->element('Statistics/sales_summary_daily');
}
?>
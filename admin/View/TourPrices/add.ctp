<div class="tourprices add">
	<?php echo $this->Form->create('TourPrice', array('inputDefaults' => array('label' => false))); ?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
		<h3>募集型料金追加</h3>
		<span id="carTypeJson" style="display: none"><?php echo json_encode($carTypes); ?></span>
		<?php echo $this->Form->hidden('tour_car_type_id'); ?>
		<?php echo $this->Form->hidden('tour_car_type_name'); ?>
		<?php echo $this->Form->hidden('tour_car_example'); ?>
		<table class="table table-bordered">
			<tr>
				<th>利用空港</th>
				<td><?php echo $this->Form->input('iata_cd', array('div' => false, 'label' => false, 'options' => $airportList, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>販売開始</th>
				<td><?php echo $this->element('selectDatetime', $dateFromOptions); ?></td>
			</tr>
			<tr>
				<th>販売終了</th>
				<td><?php echo $this->element('selectDatetime', $dateToOptions); ?></td>
			</tr>
			<tr>
				<th>営業開始</th>
				<td><?php echo $this->element('selectDatetime', $timeStartOptions); ?></td>
			</tr>
			<tr>
				<th>営業終了</th>
				<td><?php echo $this->element('selectDatetime', $timeEndOptions); ?></td>
			</tr>
			<tr>
				<th>乗車人数</th>
				<td><?php echo $this->Form->input('passenger_count', array('div' => false, 'label' => false, 'options' => $passengerOptions, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>車両クラス名</th>
				<td id="dispCarTypeName"><?php echo !empty($this->request->data['TourPrice']) ? $this->request->data['TourPrice']['tour_car_type_name'] : ''; ?></td>
			</tr>
			<tr>
				<th>車種（例）</th>
				<td id="dispCarExample"><?php echo !empty($this->request->data['TourPrice']) ? $this->request->data['TourPrice']['tour_car_example'] : ''; ?></td>
			</tr>
			<tr>
				<th>販売単価</th>
				<td><?php echo $this->Form->input('price', array('type' => 'text', 'div' => false, 'label' => false)); ?> 円</td>
			</tr>
			<tr>
				<th>公開・非公開</th>
				<td><?php echo $this->Form->input('delete_flg', array('type' => 'input', 'default' => 0, 'options' => $deleteFlgOptions)); ?></td>
			</tr>
		</table>
		<?php echo $this->Form->submit('新規登録', array('class' => 'btn btn-success ')); ?>
	<?php echo $this->Form->end(); ?>
</div>
<script>
    $(function() {
        var carTypeInfos = JSON.parse($('#carTypeJson').text());
        // 乗車人数セレクトボックス
        $('#TourPricePassengerCount').change(function() {
            var passengerCount = $(this).val();
            if (passengerCount != '') {
                setCarTypeItems(passengerCount);
            } else {
                $('#TourPriceTourCarTypeId').val('');
                $('#TourPriceTourCarTypeName').val('');
                $('#TourPriceTourCarExample').val('');
                $('#dispCarTypeName').text('');
                $('#dispCarExample').text('');
            }
        });
        // 乗車人数に応じて車両クラスを設定する
        function setCarTypeItems(passengerCount) {
            var hit = false;
            for (var i = 0; i < carTypeInfos.length; i++) {
                for (var j = 0; j < carTypeInfos[i]['passenger'].length; j++) {
                    if (passengerCount == carTypeInfos[i]['passenger'][j]) {
                        hit = true;
                        $('#TourPriceTourCarTypeId').val(carTypeInfos[i]['id']);
                        $('#TourPriceTourCarTypeName').val(carTypeInfos[i]['name']);
                        $('#TourPriceTourCarExample').val(carTypeInfos[i]['example']);
                        $('#dispCarTypeName').text(carTypeInfos[i]['name']);
                        $('#dispCarExample').text(carTypeInfos[i]['example']);
                        break;
                    }
                }
                if (hit) {
                    break;
                }
            }
        }
        //$('#TourPricePassengerCount').trigger('change');
    });
</script>

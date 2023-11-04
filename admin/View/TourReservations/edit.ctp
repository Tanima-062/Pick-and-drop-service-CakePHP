<div class="tourreservations edit">
	<?php echo $this->Form->create('TourReservation', array('inputDefaults' => array('label' => false))); ?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<h3>募集型予約詳細</h3>
    <p style="text-align: right;"><?php echo $this->Html->link(__('予約一覧へ戻る'), $referer, array('class' => 'btn btn-warning')); ?></p>
	<?php echo $this->Form->hidden('id'); ?>
	<table class="table table-bordered">
		<tr>
			<th>ツアー予約番号</th>
			<td>
				<?php echo $this->request->data['TourReservation']['cm_application_id']; ?>
				<?php echo $this->Form->hidden('cm_application_id'); ?>
			</td>
		</tr>
		<tr>
			<th>申込日時</th>
			<?php
				$timestamp = strtotime($this->request->data['TourReservation']['booking_dt']);
				$date = date('Y年m月d日', $timestamp);
				$w = $wday[date('w', $timestamp)];
				$time = date('H時i分', $timestamp);
			?>
			<td>
				<?php echo sprintf('%s (%s) %s', $date, $w, $time); ?>
				<?php echo $this->Form->hidden('booking_dt'); ?>
			</td>
		</tr>
	<?php if (!empty($this->request->data['TourReservation']['cancel_dt'])) { ?>
		<tr>
			<th>キャンセル日時</th>
			<?php
				$timestamp = strtotime($this->request->data['TourReservation']['cancel_dt']);
				$date = date('Y年m月d日', $timestamp);
				$w = $wday[date('w', $timestamp)];
				$time = date('H時i分', $timestamp);
			?>
			<td>
				<?php echo sprintf('%s (%s) %s', $date, $w, $time); ?>
				<?php echo $this->Form->hidden('cancel_dt'); ?>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<th>氏名</th>
			<td>
				<?php echo '姓 '.$this->request->data['TourReservation']['last_name'].'　名 '.$this->request->data['TourReservation']['first_name']; ?>
				<?php echo $this->Form->hidden('last_name'); ?>
				<?php echo $this->Form->hidden('first_name'); ?>
			</td>
		</tr>
		<tr>
			<th>ご利用人数</th>
			<td>
				<?php echo '大人 '.$this->request->data['TourReservation']['adults_count'].'名 子供 '.$this->request->data['TourReservation']['children_count'].'名 幼児 '.$this->request->data['TourReservation']['infants_count'].'名'; ?>
				<?php echo $this->Form->hidden('adults_count'); ?>
				<?php echo $this->Form->hidden('children_count'); ?>
				<?php echo $this->Form->hidden('infants_count'); ?>
			</td>
		</tr>
		<tr>
			<th>メールアドレス</th>
			<td>
				<?php echo $this->request->data['TourReservation']['email']; ?>
				<?php echo $this->Form->hidden('email'); ?>
			</td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td>
				<?php echo $this->request->data['TourReservation']['tel']; ?>
				<?php echo $this->Form->hidden('tel'); ?>
			</td>
		</tr>
		<tr>
			<th>到着日時</th>
			<?php
				$timestamp = strtotime($this->request->data['TourReservation']['arrival_dt']);
				$date = date('Y年m月d日', $timestamp);
				$w = $wday[date('w', $timestamp)];
				$time = date('H時i分', $timestamp);
			?>
			<td>
				<?php echo sprintf('%s (%s) %s', $date, $w, $time); ?>
				<?php echo $this->Form->hidden('arrival_dt'); ?>
			</td>
		</tr>
		<tr>
			<th>出発日時</th>
			<?php
				$timestamp = strtotime($this->request->data['TourReservation']['departure_dt']);
				$date = date('Y年m月d日', $timestamp);
				$w = $wday[date('w', $timestamp)];
				$time = date('H時i分', $timestamp);
			?>
			<td>
				<?php echo sprintf('%s (%s) %s', $date, $w, $time); ?>
				<?php echo $this->Form->hidden('departure_dt'); ?>
			</td>
		</tr>
		<tr>
			<th>ステータス</th>
			<td><?php echo $this->Form->input('reservation_status_id', array('div' => false, 'label' => false, 'options' => $statusList, 'empty' => false)); ?></td>
		</tr>
		<tr>
			<th>RC会社予約番号</th>
			<td><?php echo $this->Form->input('reservation_key', array('div' => false, 'label' => false)); ?></td>
		</tr>
		<tr>
			<th>利用期間</th>
			<td><?php echo $this->element('selectDatetime', $rentDtOptions); ?> <br> 〜 <br> <?php echo $this->element('selectDatetime', $returnDtOptions); ?></td>
		</tr>
		<tr>
			<th>選択車両クラス</th>
			<td>
				<?php echo $this->request->data['TourReservation']['tour_car_type_name']; ?>
				<?php echo $this->Form->hidden('tour_car_type_name'); ?>
			</td>
		</tr>
		<tr>
			<th>選択車種(例)</th>
			<td>
				<?php echo $this->request->data['TourReservation']['tour_car_example']; ?>
				<?php echo $this->Form->hidden('tour_car_example'); ?>
			</td>
		</tr>
		<tr>
			<th>利用空港</th>
			<td id="IataCd">
				<?php echo $this->request->data['TourReservation']['iata_cd']; ?>
				<?php echo $this->Form->hidden('iata_cd'); ?>
			</td>
		</tr>
		<tr>
			<th>利用会社</th>
			<td><?php echo $this->Form->input('client_id', array('div' => false, 'label' => false, 'options' => $clientListXiata, 'empty' => '---')); ?></td>
			<?php echo $this->Form->hidden('client_name'); ?>
		</tr>
		<tr>
			<th>実手配車両クラス</th>
			<td>
				<?php echo $this->Form->input('car_type_id', array('div' => false, 'label' => false, 'options' => $carTypeList, 'empty' => '---')); ?>
				<?php echo $this->Form->hidden('car_type_name'); ?>
			</td>
		</tr>
		<tr>
			<th>受取店舗</th>
			<td>
				<?php echo $this->Form->input('rent_office_id', array('div' => false, 'label' => false, 'options' => $officeList, 'empty' => '---')); ?>
				<?php echo $this->Form->hidden('rent_office_name'); ?>
			</td>
		</tr>
		<tr>
			<th>受取店舗電話番号</th>
			<td>
				<span id="dispRentTel"><?php echo $this->request->data['TourReservation']['rent_office_tel']; ?></span>
				<?php echo $this->Form->hidden('rent_office_tel'); ?>
			</td>
		</tr>
		<tr>
			<th>受取店舗URL</th>
			<td>
				<span id="dispRentUrl"><?php echo $this->request->data['TourReservation']['rent_office_url']; ?></span>
				<?php echo $this->Form->hidden('rent_office_url'); ?>
			</td>
		</tr>
		<tr>
			<th>返却店舗</th>
			<td>
				<?php echo $this->Form->input('return_office_id', array('div' => false, 'label' => false, 'options' => $officeList, 'empty' => '---')); ?>
				<?php echo $this->Form->hidden('return_office_name'); ?>
			</td>
		</tr>
		<tr>
			<th>返却店舗電話番号</th>
			<td>
				<span id="dispReturnTel"><?php echo $this->request->data['TourReservation']['return_office_tel']; ?></span>
				<?php echo $this->Form->hidden('return_office_tel'); ?>
			</td>
		</tr>
		<tr>
			<th>返却店舗URL</th>
			<td>
				<span id="dispReturnUrl"><?php echo $this->request->data['TourReservation']['return_office_url']; ?></span>
				<?php echo $this->Form->hidden('return_office_url'); ?>
			</td>
		</tr>
		<tr>
			<th>販売価格</th>
			<td>
				<?php echo number_format($this->request->data['TourReservation']['price']); ?> 円
				<?php echo $this->Form->hidden('price'); ?>
			</td>
		</tr>
		<tr>
			<th>仕入価格</th>
			<td><?php echo $this->Form->input('net_price', array('type' => 'text', 'div' => false, 'label' => false)); ?> 円</td>
		</tr>
		<tr>
			<th colspan="2" style="background-color: #3a87ad;">シート</th>
		</tr>
		<tr>
			<th>ベビーシート</th>
			<td>
				<?php echo $this->request->data['TourReservation']['baby_sheets_count']; ?> 台
				<?php echo $this->Form->hidden('baby_sheets_count'); ?>
			</td>
		</tr>
		<tr>
			<th>チャイルドシート</th>
			<td>
				<?php echo $this->request->data['TourReservation']['child_sheets_count']; ?> 台
				<?php echo $this->Form->hidden('child_sheets_count'); ?>
			</td>
		</tr>
		<tr>
			<th>ジュニアシート</th>
			<td>
				<?php echo $this->request->data['TourReservation']['junior_sheets_count']; ?> 台
				<?php echo $this->Form->hidden('junior_sheets_count'); ?>
			</td>
		</tr>
		<tr>
			<th colspan="2" style="background-color: #3a87ad;">その他</th>
		</tr>
		<tr>
			<th>メモ</th>
			<td>
				<?php echo $this->Form->input('remarks' ,array('type' => 'textarea', 'class' => 'span8', 'div' => false, 'label' => false)); ?>
			</td>
		</tr>
	</table>
	<?php echo $this->Form->submit('編集', array('class' => 'btn btn-success ')); ?>
	<?php echo $this->Form->end(); ?>
</div>
<script>
    $(function() {
        // RC会社予約番号テキストボックス
        $('#TourReservationReservationKey').change(function() {
            var status = $('#TourReservationReservationStatusId');
            if ($(this).val() != '' && status.val() == '0') {
                status.val('1');
            }
        });
        // 利用会社セレクトボックス
        $('#TourReservationClientId').change(function() {
            var clientId = $(this).val();
            setOfficeList(clientId, $('#IataCd').text());
            if (clientId != '') {
                $('#TourReservationClientName').val($(this).children(':selected').text());
            } else {
                $('#TourReservationClientName').val('');
            }
        });
        // 会社と空港に応じて店舗セレクトボックスを設定する
        function setOfficeList(clientId, iataCd) {
            $('#TourReservationRentOfficeId').empty();
            $('#TourReservationReturnOfficeId').empty();
            $('#TourReservationRentOfficeName').val('');
            $('#TourReservationReturnOfficeName').val('');
            if (clientId == '') {
                clientId = 4294967295;
            }
            $.ajax({
                type: 'GET',
                url: '/rentacar/admin/TourReservations/get_office_list/' + clientId + '/' + iataCd + '/',
                success: function(office) {
                    var officeList = JSON.parse(office);
                    var rentOptions = new Array();
                    var returnOptions = new Array();
                    rentOptions.push(new Option('---', ''));
                    returnOptions.push(new Option('---', ''));
                    for (key in officeList) {
                        rentOptions.push(new Option(officeList[key], key));
                        returnOptions.push(new Option(officeList[key], key));
                    }
                    $('#TourReservationRentOfficeId').append(rentOptions);
                    $('#TourReservationReturnOfficeId').append(returnOptions);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //alert(XMLHttpRequest.status);
                    //alert(textStatus);
                    //alert(errorThrown.message);
                }
            });
        }
        // 実手配車両クラスセレクトボックス
        $('#TourReservationCarTypeId').change(function() {
            var carTypeId = $(this).val();
            if (carTypeId != '') {
                $('#TourReservationCarTypeName').val($(this).children(':selected').text());
            } else {
                $('#TourReservationCarTypeName').val('');
            }
        });
        // 受取店舗セレクトボックス
        $('#TourReservationRentOfficeId').change(function() {
            var rentOfficeId = $(this).val();
            if (rentOfficeId != '') {
                $('#TourReservationRentOfficeName').val($(this).children(':selected').text());
                $.ajax({
                    type: 'GET',
                    url: '/rentacar/admin/TourReservations/get_office_info/' + rentOfficeId + '/',
                    success: function(office) {
                        var officeInfo = JSON.parse(office);
                        $('#TourReservationRentOfficeTel').val(officeInfo.tel);
                        $('#dispRentTel').text(officeInfo.tel);
                        $('#TourReservationRentOfficeUrl').val(officeInfo.url);
                        $('#dispRentUrl').text(officeInfo.url);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        //alert(XMLHttpRequest.status);
                        //alert(textStatus);
                        //alert(errorThrown.message);
                    }
                });
            } else {
                $('#TourReservationRentOfficeName').val('');
                $('#TourReservationRentOfficeTel').val('');
                $('#dispRentTel').text('');
                $('#TourReservationRentOfficeUrl').val('');
                $('#dispRentUrl').text('');
            }
        });
        // 返却店舗セレクトボックス
        $('#TourReservationReturnOfficeId').change(function() {
            var returnOfficeId = $(this).val();
            if (returnOfficeId != '') {
                $('#TourReservationReturnOfficeName').val($(this).children(':selected').text());
                $.ajax({
                    type: 'GET',
                    url: '/rentacar/admin/TourReservations/get_office_info/' + returnOfficeId + '/',
                    success: function(office) {
                        var officeInfo = JSON.parse(office);
                        $('#TourReservationReturnOfficeTel').val(officeInfo.tel);
                        $('#dispReturnTel').text(officeInfo.tel);
                        $('#TourReservationReturnOfficeUrl').val(officeInfo.url);
                        $('#dispReturnUrl').text(officeInfo.url);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        //alert(XMLHttpRequest.status);
                        //alert(textStatus);
                        //alert(errorThrown.message);
                    }
                });
            } else {
                $('#TourReservationReturnOfficeName').val('');
                $('#TourReservationReturnOfficeTel').val('');
                $('#dispReturnTel').text('');
                $('#TourReservationReturnOfficeUrl').val('');
                $('#dispReturnUrl').text('');
            }
        });
    });
</script>

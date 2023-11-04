<style>
select {
	margin: 0 auto;
}
input[type="number"],
input[type="tel"],
input[type="email"] {
	margin: 0 auto;
}

.ft {
    float: left;
}
</style>

<script>

$(document).ready(function(){
	$('#messageBoardMessage').val('');

	$('#TemplateName').change(function(){

		var name = "<?php echo $this->request->data['Reservation']['last_name'].'　'.$this->request->data['Reservation']['first_name'].' 様\n\n'; ?>";
		var template = <?php echo json_encode($clientTemplate); ?>;
		var string = template[$(this).val()];

		if (string === undefined) {
			$("#ReservationMailContents").val(name);
		} else {
			$("#ReservationMailContents").val(name+string);
		}
	});

	$('.TemplateClass').click(function(){

		var name = "<?php echo $this->request->data['Reservation']['last_name'].'　'.$this->request->data['Reservation']['first_name'].' 様\n\n'; ?>";
		var template = <?php echo json_encode($clientTemplate); ?>;
		var string = template[$(this).attr('val')];

		if (string === undefined) {
			$("#ReservationMailContents").val(name);
		} else {
			$("#ReservationMailContents").val(name+string);
		}
	});

	$(".submit").on("click",function() {
		var rent_year = $('#ReservationRentDatetimeYear').val();
		var rent_month = $('#ReservationRentDatetimeMonth').val();
		var rent_day = $('#ReservationRentDatetimeDay').val();

		var return_year = $('#ReservationReturnDatetimeYear').val();
		var return_month = $('#ReservationReturnDatetimeMonth').val();
		var return_day = $('#ReservationReturnDatetimeDay').val();

		var dateDiff = compareDate(rent_year,rent_month,rent_day,return_year,return_month,return_day);

		if (dateDiff >= 30) {
			if (!confirm("期間設定が、29泊30日以上となっております。\n 日付設定にお間違いがないかご確認ください。")) {
				return false;
			}
		}

		var cancel_fee = $('#CancelDetailAmount').val();
		if (cancel_fee && parseInt(cancel_fee) > parseInt($('#ReservationAmount').val())) {
			alert('キャンセル料は合計金額以下にしてください。');
			return false;
		}
	});

	<?php if ($isPaidInAdvance && $this->request->data['Reservation']['reservation_status_id'] <> Constant::STATUS_CANCEL) { ?>
	$('#ReservationReservationStatusId').change(function(){
		if ($(this).val() == 3) {
			$('#reservation_status').after('<tr class="cancel_input"><th>キャンセル料</th><td><input name="data[CancelDetail][amount]" required="required" type="number" id="CancelDetailAmount"/>円<code>※必須</code></td></tr><tr class="cancel_input"><th>キャンセル理由詳細</th><td><textarea name="data[Reservation][cancel_remark]" class="span4" cols="30" rows="6" id="ReservationCancelRemark" required="required"></textarea><code>※必須</code></td></tr>');
		} else {
			$('.cancel_input').remove();
		}
	});
	<?php } ?>

	<?php if (isset($responseHistories)) { ?>
	$(document).on('click', '#saveResponseHistory', function () {
		const messageBoardMessage = $.trim($('#messageBoardMessage').val());
		if (messageBoardMessage.length === 0) {
			return;
		}

		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/client/Reservations/saveResponseHistory',
			data: {
				reservation_id: $('#ReservationId').val(),
				message: messageBoardMessage
			}
		})
		.done(function (res) {
			if (res && res.ret === 'ok') {
				alert('登録しました');
				location.reload(true);
			} else {
				alert('Error:登録に失敗しました。' + res.message);
			}
		})
		.fail(function () {
			alert('Fail:登録に失敗しました');
		});
	});
	<?php } ?>

	function compareDate(year1, month1, day1, year2, month2, day2) {
			var dt1 = new Date(year1, month1 - 1, day1);
			var dt2 = new Date(year2, month2 - 1, day2);
			var diff = dt1 - dt2;
			var diffDay = diff / 86400000;//1日は86400000ミリ秒
			return Math.abs(diffDay);
	}
});

</script>

<div class="reservations form">

	<?php echo $this->Form->create('Reservation'); ?>
		<div style="text-align:right">
		<?php
		echo $this->Html->link('予約完了メールの再送','/Reservations/retransmission/'. $this->request->data['Reservation']['id'], array('class' => 'btn btn-success'));

		echo $this->Html->link('印刷ページを開く','/Reservations/printData/'. $this->request->data['Reservation']['id'], array('class' => 'btn btn-primary'));
		?>
		</div>
	<?php echo $this->Form->end(); ?>

	<?php echo $this->Session->flash(); ?>

	<?php echo $this->Form->create('Reservation'); ?>
	<fieldset class="client-edit">
		<legend><?php echo __('顧客編集画面'); ?></legend>
		<?php if (isset($responseHistories)) { ?>
			<div style="overflow: hidden;width: 75%">
				<h4>対応履歴</h4>
				<table class="table table-bordered table-condensed" style="margin-bottom: 5px">
					<thead>
						<tr  style="background-color: #dff0d8">
							<th style="width: 20%">担当者</th>
							<th style="width: 20%">入力日時</th>
							<th>対応内容</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($responseHistories as $history) { ?>
						<tr>
							<td><?php echo $history['Staff']['name']; ?></td>
							<td><?php echo $history['MessageBoard']['created']; ?></td>
							<td><?php echo nl2br($history['MessageBoard']['message']); ?></td>
						</tr>
					<?php } ?>
						<tr>
							<td></td>
							<td></td>
							<td><textarea id="messageBoardMessage" name="messageBoardMessage" rows="2" style="width: 95%"></textarea></td>
						</tr>
					</tbody>
				</table>
				<div style="float: right">
					<button type="button" id="saveResponseHistory" class="btn btn-success">対応内容入力</button>
				</div>
			</div>
		<?php } ?>
		<?php if ($showDescUnlockBtn) {?>
			<?php echo $this->Html->link(__('ロック解除(5分)'), '/Reservations/unlocked/'. $this->request->data['Reservation']['id'], array('class' => 'btn btn-primary ft')); ?>
		<?php } ?>
		<?php if ($this->Session->check('clientReferer')) { ?>
			<p style="text-align: right;"><?php echo $this->Html->link(__('顧客一覧へ戻る'), $this->Session->read('clientReferer'), array('class' => 'btn btn-warning')); ?></p>
		<?php } else { ?>
			<p style="text-align: right;"><?php echo $this->Html->link(__('顧客一覧へ戻る'), array('action' => 'index'), array('class' => 'btn btn-warning')); ?></p>
		<?php } ?>

		<?php echo $this->Form->input('id', array('label' => 'ID', 'div' => false));?>
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>予約番号</th>
				<td>
					<?php 
						echo $this->data['Reservation']['reservation_key'];
						if (!empty($cm_application_id)) {
							if ($this->data['Commodity']['sales_type'] != Constant::SALES_TYPE_AGENT_ORGANIZED && !is_null($this->data['Reservation']['payment_status'])) {
								echo '（skyticket申込番号: <a href="/rentacar/admin/PaymentDetails/index/reservation_id:'.$this->data['Reservation']['id'].'" target="_blank">'.$cm_application_id.'</a>）';
							} else {
								echo '（skyticket申込番号:' . $cm_application_id . '）';
							}
						}
					?>
				</td>
			</tr>
			<?php if ($clientData['Client']['is_managed_package']) : ?>
			<tr>
				<th>販売方法</th>
				<td>
					<?php 
						echo Constant::salesType()[$this->data['Commodity']['sales_type']];
					?>
				</td>
			</tr>
				<?php endif; ?>
			<tr>
				<th>申込み日時</th>
				<?php
				$date = date('Y年m月d日', strtotime($this->request['data']['Reservation']['created']));
				$week = $wday[date('w', strtotime($this->request['data']['Reservation']['created']))];
				$time = date('H時i分', strtotime($this->request['data']['Reservation']['created']));
				?>
				<td><?php echo $date; ?>（<?php echo $week; ?>）<?php echo $time; ?></td>
			</tr>
			<tr>
				<th>キャンセル日時</th>
				<?php
				$date = date('Y年m月d日', strtotime($this->request['data']['Reservation']['cancel_datetime']));
				$week = $wday[date('w', strtotime($this->request['data']['Reservation']['cancel_datetime']))];
				$time = date('H時i分', strtotime($this->request['data']['Reservation']['cancel_datetime']));
				?>
				<td>
					<?php if(!empty($this->request['data']['Reservation']['cancel_flg'])): ?>
					<?php echo $date; ?>（<?php echo $week; ?>）<?php echo $time; ?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th>管理番号</th>
				<td><?php echo $this->Form->input('control_number', array('label' => false, 'div' => false))?><code>※任意入力</code></td>
			</tr>
			<tr>
				<th>氏名カナ</th>
				<td>姓
<?php
	if ($canEdit) {
					echo $this->Form->input('last_name', array('label' => false, 'div' => false, 'class' => 'span4'));
	} else {
					echo '&nbsp;';
					echo $this->data['Reservation']['last_name'];
					echo $this->Form->hidden('last_name');
					echo '&nbsp;&nbsp;&nbsp;';
	}
?>
					名
<?php
	if ($canEdit) {
					echo $this->Form->input('first_name', array('label' => false, 'div' => false, 'class' => 'span4'));
	} else {
					echo '&nbsp;';
					echo $this->data['Reservation']['first_name'];
					echo $this->Form->hidden('first_name');
	}
?>
				</td>
			</tr>
			<tr>
				<th>ご利用人数</th>
				<td>大人
<?php
	if ($canEdit) {
					echo $this->Form->input('adults_count', array('label' => false, 'div' => false, 'class' => 'span2'));
	} else {
					echo '&nbsp;';
					echo $this->data['Reservation']['adults_count'];
					echo $this->Form->hidden('adults_count');
					echo '&nbsp;名、';
	}
?>
					子供
<?php
	if ($canEdit) {
					echo $this->Form->input('children_count', array('label' => false, 'div' => false, 'class' => 'span2'));
	} else {
					echo '&nbsp;';
					echo $this->data['Reservation']['children_count'];
					echo $this->Form->hidden('children_count');
					echo '&nbsp;名、';
	}
?>
					幼児
<?php
	if ($canEdit) {
					echo $this->Form->input('infants_count', array('label' => false, 'div' => false, 'class' => 'span2'));
	} else {
					echo '&nbsp;';
					echo $this->data['Reservation']['infants_count'];
					echo $this->Form->hidden('infants_count');
					echo '&nbsp;名';
	}
?>
				</td>
			</tr>
			<tr>
				<th>ご利用期間</th>
				<td>
<?php
	if ($canEdit) {
?>
					<?php echo $this->Form->year('rent_datetime', date('Y') + 2, 2016, array('empty' => false, 'style' => 'width:80px;'));?>年
					<?php echo $this->Form->month('rent_datetime', array('monthNames' => false, 'empty' => false, 'style' => 'width:60px;'));?>月
					<?php echo $this->Form->day('rent_datetime', array('empty' => false, 'style' => 'width:60px;'));?>日
					<?php echo $this->Form->hour('rent_datetime', 24, array('empty' => false, 'style' => 'width:60px;'));?>時
					<?php echo $this->Form->minute('rent_datetime', array('empty' => false, 'style' => 'width:60px;'));?>分
					 ～ <?php echo $this->Form->year('return_datetime', date('Y') + 2, 2016, array('empty' => false, 'style' => 'width:80px;'));?>年
					<?php echo $this->Form->month('return_datetime', array('monthNames' => false, 'empty' => false, 'style' => 'width:60px;'));?>月
					<?php echo $this->Form->day('return_datetime', array('empty' => false, 'style' => 'width:60px;'));?>日
					<?php echo $this->Form->hour('return_datetime', 24, array('empty' => false, 'style' => 'width:60px;'));?>時
					<?php echo $this->Form->minute('return_datetime', array('empty' => false, 'style' => 'width:60px;'));?>分
<?php
	} else {
					 echo $this->Form->hidden('rent_datetime');
					 echo $this->Form->hidden('return_datetime');
	}
?>
					<p style="margin:0;">
					<?php
					// 借日
					$rent = strtotime(date('Y-m-d', strtotime($this->request['data']['Reservation']['rent_datetime'])));
					$rentTimeStamp = strtotime($this->request['data']['Reservation']['rent_datetime']);
					$rentDay = h(date('Y年m月d日', $rentTimeStamp));
					$rentWeek = $wday[h(date('w', $rentTimeStamp))];
					$rentTime = h(date('H時i分', $rentTimeStamp));

					// 返却日
					$return = strtotime(date('Y-m-d', strtotime($this->request['data']['Reservation']['return_datetime'])));
					$retrunTimeStamp = strtotime($this->request['data']['Reservation']['return_datetime']);
					$returnDay = h(date('Y年m月d日', $retrunTimeStamp));
					$returnWeek = $wday[h(date('w', $retrunTimeStamp))];
					$returnTime = h(date('H時i分', $retrunTimeStamp));
					?>
					<?php echo $rentDay; ?>（<?php echo $rentWeek; ?>）<?php echo $rentTime; ?>
					&nbsp;～&nbsp;
					<?php echo $returnDay; ?>（<?php echo $returnWeek; ?>）<?php echo $returnTime; ?>
					 </p>
				</td>
			</tr>
			<tr>
				<th>到着便名</th>
				<td>
				<?php echo $this->Form->input('arrival_flight_number', array('label' => false, 'div' => false));?>
				</td>
			</tr>
			<tr>
				<th>出発便名</th>
				<td>
				<?php echo $this->Form->input('departure_flight_number', array('label' => false, 'div' => false));?>
				</td>
			</tr>
			<tr>
				<th>メールアドレス</th>
				<td>
<?php
	if ($canEdit) {
					echo $this->Form->input('email', array('div' => false, 'label' => false));
	} else {
					echo $this->data['Reservation']['email'];
					echo $this->Form->hidden('email');
	}
?>
					<?php if (!empty($mailError)) { ?>
					<p style="font-size:15px;color:red;">
					※お客様のメール環境またはメールアドレス不一致などの理由によりメッセージが届いておりません。<br>
					※お客様へのご連絡や確認事項は、お手数ですが「電話番号」へお願いします。
					</p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>電話番号</th>
				<td>
<?php
	if ($canEdit) {
					echo $this->Form->input('tel', array('label' => false, 'div' => false, 'class' => 'span4'));
	} else {
					echo $this->data['Reservation']['tel'];
					echo $this->Form->hidden('tel');
	}
?>
				</td>
			</tr>
			<tr id="reservation_status">
				<th>ステータス</th>
				<td>
<?php
	if ($canStatusEdit) {
					echo $this->Form->input('reservation_status_id',
						array('options' => $reservationStatus, 'label' => false, 'div' => false, 'class' => 'span2'));
	} else {
					echo $reservationStatus[$this->data['Reservation']['reservation_status_id']];
					echo $this->Form->hidden('reservation_status_id');
	}
?>
				</td>
			</tr>
<?php
	if ($isRennaviApiTarget) {
?>
			<tr>
				<th style='width: 100px;'>レンナビ予約<br/>ステータス</th>
				<td><?php echo Constant::rennaviStatusNames()[$this->request['data']['Reservation']['rennavi_status']];?></td>
			</tr>
<?php
	}
?>
			<?php if ($this->data['Reservation']['reservation_status_id'] == 3) { ?>
			<?php if (!is_null($cancelFee)) { ?>
			<tr>
				<th>キャンセル料</th>
				<td><?php echo number_format($cancelFee); ?>円</td>
			</tr>
			<?php } ?>
			<tr>
				<th rowspan=2 >キャンセル理由</th>
				<td><?php echo $cancelReason[$this->data['Reservation']['cancel_reason_id']]; ?></td>
			</tr>
			<tr><td><?php echo h($this->data['Reservation']['cancel_remark']); ?></td></tr>
			<?php } ?>
<?php
	if ($acceptPrepay && $this->data['Commodity']['sales_type'] != Constant::SALES_TYPE_AGENT_ORGANIZED) {
?>
				<tr>
					<th>支払方法</th>
					<td>
						<?php
						echo $paymentMethod[$isPaidInAdvance];
						?>
					</td>
				</tr>
<?php
	}
?>
			<tr>
				<th>合計金額</th>
				<td>
<?php
	if ($canEdit) {
					echo $this->Form->input('amount', array('label' => false, 'div' => false, 'class' => 'span2'));
	} else {
					echo number_format($this->data['Reservation']['amount']);
					echo $this->Form->hidden('amount');
	}
?>
					円
					&emsp;(予約時内容 ：
					<?php foreach ($breakDownContent as $key => $breakDown) { ?>
						<?php echo $breakDown['name']; ?>
						<?php echo number_format($breakDown['sum']); ?>円
						<?php if ($breakDown['data_type_id'] == 1) { ?>
						× <?php echo $breakDown['cars_count']; ?>
						<?php } ?>
						<?php if ($breakDown != end($breakDownContent)) { ?>
							<?php echo '、'; ?>
						<?php } ?>
					<?php } ?>)
					<?php if (!$canEdit) { ?>
							<br/><span>※クライアント様画面で合計金額の変更は出来ません。弊社クライアントサポートへご連絡ください。</span>
					<?php } ?>
				</td>
			</tr>
			<?php if (($this->data['Commodity']['sales_type'] === Constant::SALES_TYPE_AGENT_ORGANIZED) && (bool)$clientData['is_system_admin']) : ?>
			<tr>
				<th>販売価格</th>
				<td>
				<?php
					echo $this->Form->input('sales_price', array('label' => false, 'div' => false, 'class' => 'span2'));
				?>
					円
				</td>
			</tr>
			<tr>
				<th>販売利益</th>
				<td>
				<?php
					echo number_format($this->data['Reservation']['sales_price'] - $this->data['Reservation']['amount']);
				?>
					円
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th>お申込みプラン</th>
				<td><?php echo $this->data['Commodity']['name']?></td>
			</tr>
			<tr>
				<th>車両タイプ</th>
				<td><?php echo $carType[$carClass['CarClass']['car_type_id']];?></td>
			</tr>
			<tr>
				<th>車両クラス</th>
				<td><?php echo $carClass['CarClass']['name'];?></td>
			</tr>
			<tr>
				<th>車両台数</th>
				<td>
				<?php echo $this->data['Reservation']['cars_count']; ?>台
				</td>
			</tr>
			<tr>
				<th>受取店舗</th>
				<td>
<?php
	if ($canEdit) {
					echo $this->Form->input('rent_office_id',
						array('options' => $rentOfficeName, 'label' => false, 'div' => false, 'class' => 'span8'));
	} else {
					echo $rentOfficeName[$this->data['Reservation']['rent_office_id']];
					echo $this->Form->hidden('rent_office_id');
	}
?>
				</td>
			</tr>
			<tr>
				<th>返却店舗</th>
				<td>
<?php
	if ($canEdit) {
					echo $this->Form->input('return_office_id',
						array('options' => $returnOfficeName, 'label' => false, 'div' => false, 'class' => 'span8'));
	} else {
					echo $returnOfficeName[$this->data['Reservation']['return_office_id']];
					echo $this->Form->hidden('return_office_id');
	}
?>
				</td>
			</tr>
			<tr>
				<th colspan=2 style="background-color: #dff0d8;">シート</th>
			</tr>
			<?php
			foreach ($privilegeSheet as $key => $sheet) {
					$childSeetId = '';
					$childSeetCount = '';
					$childSeetPrice = '';
					if (!empty($sheet['Privilege']['id'])) {
						if (!empty($reservationChildSheetData[$sheet['Privilege']['id']]['id'])) {
							$childSeetId = $reservationChildSheetData[$sheet['Privilege']['id']]['id'];
						}
						if (!empty($reservationChildSheetData[$sheet['Privilege']['id']]['count'])) {
							$childSeetCount = $reservationChildSheetData[$sheet['Privilege']['id']]['count'];
						}
						if (!empty($reservationChildSheetData[$sheet['Privilege']['id']]['price'])) {
							$childSeetPrice = $reservationChildSheetData[$sheet['Privilege']['id']]['price'];
						}
					}

			?>
			<tr>
				<th><?php echo $sheet['Privilege']['name']; ?></th>
				<td>
<?php
	if ($canEdit) {
					echo $this->Form->input('ReservationChildSheet.'.$sheet['Privilege']['id'].'.count', array(
						'type' => 'number',
						'max' => $sheet['Privilege']['maximum'],
						'min' => 0,
						'value' => $childSeetCount,
						'label' => false,
						'div' => false,
						'required' => false,
						'class' => 'span2',
					));
					echo $sheet['Privilege']['unit_name'];
					echo $this->Form->input('ReservationChildSheet.'.$sheet['Privilege']['id'].'.price', array(
						'type' => 'number',
						'min' => 0,
						'value' => $childSeetPrice,
						'label' => false,
						'div' => false,
						'required' => false,
						'class' => 'span2',
					));
					echo '円';
	} else {
		if ($childSeetCount > 0) {
					echo $childSeetCount;
					echo $sheet['Privilege']['unit_name'];
			if ($childSeetPrice !== '') {
					echo ' : ';
					echo number_format($childSeetPrice);
					echo '円';
			}
		}
					echo $this->Form->hidden('ReservationChildSheet.'.$sheet['Privilege']['id'].'.count', array('value' => $childSeetCount));
					echo $this->Form->hidden('ReservationChildSheet.'.$sheet['Privilege']['id'].'.price', array('value' => $childSeetPrice));
	}
					echo $this->Form->hidden('ReservationChildSheet.'.$sheet['Privilege']['id'].'.id', array('value' => $childSeetId));

					echo $this->Form->hidden('ReservationChildSheetDefault.'.$sheet['Privilege']['id'].'.count', array('value' => $childSeetCount));
					echo $this->Form->hidden('ReservationChildSheetDefault.'.$sheet['Privilege']['id'].'.price', array('value' => $childSeetPrice));
?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<th colspan=2 style="background-color: #dff0d8;">オプション</th>
			</tr>
			<?php
			foreach ($privilegeData as $key => $privilege) {
				$privilegeId = '';
				$privilegeCount = '';
				$privilegePrice = '';
				if(!empty($privilege['Privilege']['id'])) {
					if(!empty($reservationPrivilegeData[$privilege['Privilege']['id']]['id'])) {
						$privilegeId = $reservationPrivilegeData[$privilege['Privilege']['id']]['id'];
					}
					if(!empty($reservationPrivilegeData[$privilege['Privilege']['id']]['count'])) {
						$privilegeCount = $reservationPrivilegeData[$privilege['Privilege']['id']]['count'];
					}
					if(!empty($reservationPrivilegeData[$privilege['Privilege']['id']]['price'])) {
						$privilegePrice = $reservationPrivilegeData[$privilege['Privilege']['id']]['price'];
					}
				}
			?>
			<tr>
				<th><?php echo $privilege['Privilege']['name']; ?></th>
				<td>
<?php
	if ($canEdit) {
					echo $this->Form->input('ReservationPrivilege.'.$privilege['Privilege']['id'].'.count', array(
						'type' => 'number',
						'max' => $privilege['Privilege']['maximum'],
						'min' => 0,
						'value' => $privilegeCount,
						'label' => false,
						'div' => false,
						'required' => false,
						'class' => 'span2',
					));
					echo $privilege['Privilege']['unit_name'];
					echo $this->Form->input('ReservationPrivilege.'.$privilege['Privilege']['id'].'.price', array(
						'type' => 'number',
						'value' => $privilegePrice,
						'label' => false,
						'div' => false,
						'required' => false,
						'class' => 'span2',
					));
					echo '円';
	} else {
		if ($privilegeCount > 0) {
					echo $privilegeCount;
					echo $privilege['Privilege']['unit_name'];
			if ($privilegePrice !== '') {
					echo ' : ';
					echo number_format($privilegePrice);
					echo '円';
			}
		}
					echo $this->Form->hidden('ReservationPrivilege.'.$privilege['Privilege']['id'].'.count', array('value' => $privilegeCount));
					echo $this->Form->hidden('ReservationPrivilege.'.$privilege['Privilege']['id'].'.price', array('value' => $privilegePrice));
	}
					echo $this->Form->hidden('ReservationPrivilege.'.$privilege['Privilege']['id'].'.id', array('value' => $privilegeId));

					echo $this->Form->hidden('ReservationPrivilegeDefault.'.$privilege['Privilege']['id'].'.count', array('value' => $privilegeCount));
					echo $this->Form->hidden('ReservationPrivilegeDefault.'.$privilege['Privilege']['id'].'.price', array('value' => $privilegePrice));
?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<th colspan=2 style="background-color: #dff0d8;"></th>
			</tr>
			<tr>
				<th style='width: 100px;'>返信状況</th>
				<td><?php echo $this->Form->input('mail_status', array('options' => $mailStatus, 'label' => false, 'div' => false));?>
				</td>
			</tr>
			<tr>
				<th style='width: 100px;'>登録処理</th>
				<td><?php echo $this->Form->input('registered_flg', array('options' => $registeredFlgArray, 'label' => false, 'div' => false));?>
				</td>
			</tr>

			<tr>
				<td colspan=2 style='text-align: center;'>
					<?php echo $this->Form->input('commodity_item_id', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['commodity_item_id'])); ?>
					<?php echo $this->Form->input('default_status', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['reservation_status_id'])); ?>
					<?php echo $this->Form->input('default_cars_count', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['cars_count'])); ?>
					<?php echo $this->Form->input('default_cancel_datetime', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['cancel_datetime'])); ?>
					<?php echo $this->Form->input('default_modified', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['modified'])); ?>
					<?php echo $this->Form->input('can_edit', array('type' => 'hidden', 'value' => $canEdit)); ?>

					<?php echo $this->Form->input('ReservationDefault.last_name', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['last_name'])); ?>
					<?php echo $this->Form->input('ReservationDefault.first_name', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['first_name'])); ?>
					<?php echo $this->Form->input('ReservationDefault.adults_count', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['adults_count'])); ?>
					<?php echo $this->Form->input('ReservationDefault.children_count', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['children_count'])); ?>
					<?php echo $this->Form->input('ReservationDefault.infants_count', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['infants_count'])); ?>
					<?php echo $this->Form->input('ReservationDefault.rent_datetime', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['rent_datetime'])); ?>
					<?php echo $this->Form->input('ReservationDefault.return_datetime', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['return_datetime'])); ?>
					<?php echo $this->Form->input('ReservationDefault.arrival_flight_number', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['arrival_flight_number'])); ?>
					<?php echo $this->Form->input('ReservationDefault.departure_flight_number', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['departure_flight_number'])); ?>
					<?php echo $this->Form->input('ReservationDefault.email', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['email'])); ?>
					<?php echo $this->Form->input('ReservationDefault.tel', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['tel'])); ?>
					<?php echo $this->Form->input('ReservationDefault.amount', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['amount'])); ?>
					<?php echo $this->Form->input('ReservationDefault.rent_office_id', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['rent_office_id'])); ?>
					<?php echo $this->Form->input('ReservationDefault.return_office_id', array('type' => 'hidden', 'value' => $this->request['data']['Reservation']['return_office_id'])); ?>

					<?php echo $this->Form->submit('変更を保存する', array('name' => 'reservation', 'class' => 'btn btn-success')); ?>
					<?php echo $this->Form->end(); ?>
				</td>
			</tr>
		</table>
	</fieldset>


	<fieldset>
		<legend id="inquiry">
			<?php echo __('お問い合わせへの回答'); ?>
		</legend>
		<table class="table table-striped table-bordered table-condensed">
			<?php echo $this->Form->create('ReservationMail'); ?>
			<?php if (isset($sendError)) {
				echo $sendError;
			} ?>
			<tr>
				<td class="span10"><?php echo $this->Form->input('ReservationMail.contents',
						array('style' => 'width: 98%;height:350px;',
								'label' => false, 'div' => false,
								'value' => $this->request->data['Reservation']['last_name'].'　'.$this->request->data['Reservation']['first_name'].' 様'));?>
					<?php echo $this->Form->hidden('ReservationMail.reservation_id', array('value' => $this->request->data['Reservation']['id'], 'label' => false, 'div' => false));?>
					<?php echo $this->Form->hidden('ReservationMail.staff_id', array('value' => $clientId, 'label' => false, 'div' => false));?>
					<?php echo $this->Form->hidden('Reservation.id', array('value' => $this->request->data['Reservation']['id'], 'label' => false, 'div' => false));?>
					<?php echo $this->Form->hidden('Reservation.mail_status', array('value' => 1, 'label' => false, 'div' => false));?>
					<?php echo $this->Form->hidden('Reservation.staff_id', array('value' => $clientId, 'label' => false, 'div' => false));?>
					<?php echo $this->Form->hidden('E.mail', array('value' => $this->request->data['Reservation']['email'], 'label' => false, 'div' => false));?>
					<?php echo $this->Form->hidden('E.hash', array('value' => $this->request->data['Reservation']['reservation_hash'], 'label' => false, 'div' => false));?>
				</td>
				<td class="span2">
					回答内容<br/>
					<!--
					案１<br/>
					<?php echo $this->Form->input('Template.name',
						array('options' => $clientTemplateList, 'div' => false, 'label' => false, 'empty' => 'テンプレート未使用')); ?><br/>
					<br/> -->
					<br/>
					<span class="TemplateClass btn btn-warning" val="0"
							style="width:200px;margin-bottom:5px;">
						リセット
					</span>
					<?php foreach ($clientTemplateList as $tempId => $tempVal) { ?>
						<span class="TemplateClass btn btn-success"
								style="width:200px;margin-bottom:5px;" val="<?php echo $tempId?>">
							<?php echo $tempVal; ?>
						</span>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td colspan=2 style='text-align: center;'><?php echo $this->Form->submit('送信する', array('name' => 'Mail', 'class'=>'btn btn-success'))?>
				</td>
			</tr>
		</table>
	</fieldset>

	<?php if (!empty($this->request->data['ReservationMail'])) {?>
	<dl style="height:500px;overflow:scroll;border:solid #ddd 1px;box-sizing:border-box;padding:10px;">
		<?php foreach ($this->request->data['ReservationMail'] as $key => $mail) {?>
		<?php if ($key == 0) echo "<span class=\"badge badge-success\">New</span>";?>

			<?php if ($mail['ReservationMail']['staff_id'] == 0) { ?>
				<dt style="color:#145612;">
					<?php echo date('Y年m月d日 H時i分s秒', strtotime($mail['ReservationMail']['mail_datetime']));?>
					(お客様)
				</dt>
				<dd style="color:#145612;">
					<p style="word-wrap: break-word;">
						<?php echo nl2br(h($mail['ReservationMail']['contents']));?>
					</p>
				</dd>
			<?php } else { ?>
				<dt style="color:#000;">
					<?php echo date('Y年m月d日 H時i分s秒', strtotime($mail['ReservationMail']['mail_datetime']));?>
					(<?php echo $mail['Staff']['name']; ?>)
					<?php if ($mail['ReservationMail']['read_flg']) { ?>
						<span style="color:blue;">既読</span>
					<?php } else { ?>
						<span style="color:red;">未読</span>
					<?php } ?>
				</dt>
				<dd style="color:#000;">
					<p style="word-wrap: break-word;">
						<?php echo nl2br(h($mail['ReservationMail']['contents']));?>
					</p>
				</dd>
			<?php } ?>
		<?php }?>
	</dl>
	<?php }?>

	<?php echo $this->Form->end(); ?>
	<?php if ($this->Session->check('clientReferer')) { ?>
		<p><?php echo $this->Html->link(__('顧客一覧へ戻る'), $this->Session->read('clientReferer'), array('class' => 'btn btn-warning')); ?></p>
	<?php } else { ?>
		<p><?php echo $this->Html->link(__('顧客一覧へ戻る'), array('action' => 'index'), array('class' => 'btn btn-warning')); ?></p>
	<?php } ?>
</div>

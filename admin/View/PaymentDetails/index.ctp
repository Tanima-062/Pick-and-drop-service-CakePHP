<?php echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/themes/redmond/jquery-ui.min.css"); ?>
<script>
$(function() {
	$(document).on('ready', function() {
		// 返金実行ボタンの制御（返金予定額が¥0の場合はdisabled）
		if ($('#RefundSchedulRefundAmount').val() == 0) {
			$('#execRefund').prop('disabled', true);
		}
	});

	function disableFormBtn() {
		$('#saveMessageBoard').prop('disabled', true);
		$('#saveReservation').prop('disabled', true);
		$('#savePaymentDetail').prop('disabled', true);
		$('#saveCancelDetail').prop('disabled', true);
		$('#execRefund').prop('disabled', true);
		$('#paymentInfo').prop('disabled', true);
	}

	function releaseFormBtn() {
		$('#saveMessageBoard').prop('disabled', false);
		$('#saveReservation').prop('disabled', false);
		$('#savePaymentDetail').prop('disabled', false);
		$('#saveCancelDetail').prop('disabled', false);
		$('#execRefund').prop('disabled', false);
		$('#paymentInfo').prop('disabled', false);
	}

	$('#receiptModal').dialog({
		autoOpen: false,
		modal: true,
		height: 580,
		width: 540,
		position: {of: 'body', at: 'top', my: 'center'},
		buttons: [
			{
				text: 'プレビュー',
				class: 'receipt-modal-button',
				click: function () {
					previewReceipt();
				}
			},
			{
				text: '保存',
				class: 'receipt-modal-button',
				click: function () {
					saveReceipt();
				}
			},
			{
				text: '閉じる',
				class: 'receipt-modal-button',
				click: function () {
					$(this).dialog('close');
				}
			}
		],
		open: function () {
			$('body').css('overflow', 'hidden');
		},
		close: function () {
			$('body').css('overflow', 'auto');
		}
	});

	/**
	 * 入金情報表示
	 */
	$(document).on('click', '#paymentInfo', function(){
		disableFormBtn();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxPaymentInfo',
			data: {
				cm_application_id: $('#cm_application_id').val()
			}
		})
		.done(function(res){
			var msg = '';

			if (!res) {
				return;
			}

			res.detail.forEach(r => {
			if (r.inout === 'in') {
				tmp = '支払日時: ' + r.paymentDt + '\n' +
				'入金日時: ' + r.receiveDt
			} else {
				tmp = '返金日時: ' + r.refundDt
			}

			msg = msg + 
			'skyticket申込番号: ' + $('#cm_application_id').val() + '\n' + 
			'決済ID: ' + r.paymentId + '\n' +
			'カートID: ' + r.cartId + '\n' +
			'ユーザーID: ' + r.userId + '\n' +
			'決済処理状況: ' + r.progressName + '\n' +
			'決済方法: ' + r.paymentMethodName + '\n' +
			'支払金額: ' + parseInt(r.price) + '\n' +
			tmp + '\n' + '\n'
		})
			alert(msg);
		})
		.fail(function(){
			alert('入金情報が取得できませんでした');
		})
		.always(function(){
			releaseFormBtn();
		});
	})
	/*
	 * 伝言板 - 追加
	 */
	$(document).on('click', '#saveMessageBoard', function(){
		const messageBoardMessage = $.trim($('#MessageBoardMessage').val());
		if (messageBoardMessage.length === 0) {
			return;
		}

		disableFormBtn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxSaveMessageBoard',
			data: {
				reservation_id: $('#reservation_id').val(),
				message: messageBoardMessage
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				$('#MessageBoardMessage').val('');
				alert('登録しました');
				location.reload();
			}
			else {
				alert('Error:登録に失敗しました。' + res.message);
			}
		})
		.fail(function(){
			alert('Fail:登録に失敗しました');
		})
		.always(function(){
			releaseFormBtn();
		});
	});

	/*
	 * 予約情報 - 入金ステータス、入金期限変更
	 */
	$(document).on('click', '#saveReservation', function(){
		disableFormBtn();
		var data = {
			id: $('#reservation_id').val(),
			payment_status: $('#ReservationPaymentStatus').val()
		};

		if ($('#ReservationPaymentLimitDatetime').val() != '0000-00-00 00:00:00') {
			data.payment_limit_datetime = $('#ReservationPaymentLimitDatetime').val();
		}

		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxSaveReservation',
			data: data
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				alert('登録しました');
				location.reload();
			}
			else {
				alert('Error:登録に失敗しました。' + res.message);
			}
		})
		.fail(function(){
			alert('Fail:登録に失敗しました');
		})
		.always(function(){
			releaseFormBtn();
		});
	});

	/*
	 * 入金明細 - 科目入力
	 */
	$(document).on('keyup', '#PaymentDetailAmount, #PaymentDetailCount', function(){
		const payment_detail_amount = $('#PaymentDetailAmount').val();
		const payment_detail_count = $('#PaymentDetailCount').val();
		if ((payment_detail_amount.length > 0 && !$.isNumeric(payment_detail_amount)) || (payment_detail_count.length > 0 && !$.isNumeric(payment_detail_count))) {
			console.log('数値を入力してください');
			return;
		}

		const adjust_difference = payment_detail_amount * payment_detail_count;
		const total_amount = adjust_difference + parseInt($('#totalAmount').html().replace(/,/g, ''));
		$('#PaymentDetailSum').html(Number(adjust_difference).toLocaleString(undefined, {maximunFractionDigits: 20 }));

		if (adjust_difference === 0) {
			$('#totalAmountRemarks').html('');
		}
		else {
			$('#totalAmountRemarks').html('※調整後 ' + Number(total_amount).toLocaleString(undefined, {maximunFractionDigits: 20 }));
		}
	});

	/*
	 * 入金明細 - 科目保存
	 */
	$(document).on('click', '#savePaymentDetail', function(){

		var account_code = $('#PaymentDetailAccountCode').val();
		var amount = $('#PaymentDetailAmount').val();
		if (account_code != '' && $.isNumeric(amount)) {
			var flag = false;
			if (account_code == 'ADJUST_DIFFERENCE' && amount > 0) {
				if (confirm('登録すると、差分が未入金としてユーザのマイページへ表示されます。登録しますか?') == false) {
					return;
				}
			} else if (account_code == 'ADJUST_ADDITION' && amount > 0) {
				if (confirm('登録すると、差分が未入金としてユーザのマイページへ表示されます。登録しますか?') == false) {
					return;
				}
			} else if (account_code == 'ADJUST_ADDITION' && amount <= 0) {
				alert('追加調整の単価は1以上の数値を入力してください。');
				return;
			} else if (account_code == 'ADJUST_REDUCTION' && amount >= 0) {
				alert('減額調整の単価は0未満の数値を入力してください。');
				return;
			}
		}

		disableFormBtn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxSavePaymentDetail',
			data: {
				reservation_id: $('#reservation_id').val(),
				account_code: $('#PaymentDetailAccountCode').val(),
				amount: $('#PaymentDetailAmount').val(),
				count: $('#PaymentDetailCount').val(),
				remarks: $('#PaymentDetailRemarks').val(),
				account_and_amount_check: 1
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				$('#PaymentDetailAccountCode').val('--');
				$('#PaymentDetailAmount').val('');
				$('#PaymentDetailCount').val('');
				$('#PaymentDetailRemarks').val('');
				alert('登録しました');
				location.reload();
			}
			else {
				alert('Error:登録に失敗しました。' + res.message);
			}
		})
		.fail(function(){
			alert('Fail:登録に失敗しました');
		})
		.always(function(){
			releaseFormBtn();
		});
	});

	/*
	* キャンセル明細 - 科目入力
	*/
	$(document).on('keyup', '#CancelDetailAmount, #CancelDetailCount', function(){
		const cancel_detail_amount = $('#CancelDetailAmount').val();
		const cancel_detail_count = $('#CancelDetailCount').val();
		if ((cancel_detail_amount.length > 0 && !$.isNumeric(cancel_detail_amount)) || (cancel_detail_count.length > 0 && !$.isNumeric(cancel_detail_count))) {
			console.log('数値を入力してください');
			return;
		}

		const adjust_difference = cancel_detail_amount * cancel_detail_count;
		const cancel_detail_amount_sum = adjust_difference + parseInt($('#cancelDetailAmountSum').html().replace(/,/g, ''));
		$('#cancelAmountSum').html(Number(adjust_difference).toLocaleString(undefined, {maximunFractionDigits: 20 }));

		if (adjust_difference === 0) {
			$('#cancelDetailSumRemarks').html('');
		}
		else {
			$('#cancelDetailSumRemarks').html('※調整後 ' + Number(cancel_detail_amount_sum).toLocaleString(undefined, {maximunFractionDigits: 20 }));
		}

	});

	/*
	 * キャンセル明細 - 科目保存
	 */
	$(document).on('click', '#saveCancelDetail', function(){
		disableFormBtn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxSaveCancelDetail',
			data: {
				reservation_id: $('#reservation_id').val(),
				//account_type: 'CANCEL',
				account_code: $('#CancelDetailAccountCode').val(),
				amount: $('#CancelDetailAmount').val(),
				count: $('#CancelDetailCount').val(),
				remarks: $('#CancelDetailRemarks').val()
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				$('#CancelDetailAccountCode').val('--');
				$('#CancelDetailAmount').val('');
				$('#CancelDetailCount').val('');
				$('#CancelDetailRemarks').val('');
				alert('登録しました');
				location.reload();
			}
			else {
				alert('Error:登録に失敗しました。' + res.message);
			}
		})
		.fail(function(){
			alert('Fail:登録に失敗しました');
		})
		.always(function(){
			releaseFormBtn();
		});
	});

	/*
	 * 決済状況 - 返金実行
	 */
	$(document).on('click', '#execRefund', function(){
		if (!confirm('返金処理を実行しますか?')) {
			return false;
		}

		disableFormBtn();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxExecRefund',
			data: {
				id: $('#reservation_id').val(),
				remaining_amount: $('#RefundRemainingAmount').val(),
				cm_application_id: $('#cm_application_id').val()
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				alert('返金要求しました');
				location.reload();
			}
			else {
				alert('Error:返金に失敗しました。' + res.msg);
			}
		})
		.fail(function(){
			alert('Fail:返金に失敗しました');
		})
		.always(function(){
			releaseFormBtn();
		});
	});

	/**
	 * 領収書編集モーダル
	 */
	$(document).on('click', '.edit-receipt', function () {

		const receipt_id = $(this).text();

		$.ajax({
			type: 'GET',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxGetReceiptDetail?receipt_id=' + receipt_id
		})
			.done(function (res) {

				$('.ui-dialog').css('z-index', '9999');

				$('#receiptModal_name').val(res.receipt_name_enc);
				$('#receiptModal_receiptTitle').val(res.receipt_title);
				$('#receiptModal_price').val(res.receipt_price);
				$('#receiptModal_mailFlg').val(res.mail_flg).change();
				$('#receiptModal_receiptStatus').val(res.receipt_status);
				$('#receiptModal_language').val(res.lang_id);
				$('#receiptModal_zip1').val(res.send_zip_code1);
				$('#receiptModal_zip2').val(res.send_zip_code2);
				$('#receiptModal_address1').val(res.send_address1_enc);
				$('#receiptModal_address2').val(res.send_address2_enc);
				$('#receiptModal_address3').val(res.send_address3_enc);
				$('#receiptModal_sendName').val(res.send_name_enc);
				$('#receiptModal_ignoreFlg').prop('checked', (res.ignore_flg == 1));
				$('#receiptModal_reissueFlg').prop('checked', (res.reissue_flg == 1));
				$('#receiptModal_deleteFlg').prop('checked', (res.delete_flg == 1));
				$('#receiptModal_receiptId').val(receipt_id);

				$('#receiptModal').dialog({
					title: '領収書編集 (No.' + receipt_id + ')',
				});
				$('#receiptModal').dialog('open');
			})
			.fail(function () {
				alert('Fail:領収書情報の取得に失敗しました');
			})
	});

	/**
	 * 領収書作成モーダル
	 */
	$(document).on('click', '#createReceipt', function () {
		// 領収書発行可能額を取得し、入力欄に設定
		$.ajax({
			type: 'GET',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxGetReceiptIssuableAmount?reservation_id=' + $('#reservation_id').val()
		})
			.done(function (res) {
				$('#receiptModal_price').val(res);
			})

		// それ以外の項目の初期値を設定
		$('#receiptModal').find('input[type="text"]').val('');
		$('#receiptModal_mailFlg').val(0).change();
		$('#receiptModal_receiptStatus').val(0);
		$('#receiptModal_language').val(1);
		$('#receiptModal_address1').val('');
		$('#receiptModal_ignoreFlg').prop('checked', false);
		$('#receiptModal_reissueFlg').prop('checked', false);
		$('#receiptModal_deleteFlg').prop('checked', false);
		$('#receiptModal_receiptId').val('');

		$('.ui-dialog').css('z-index', '9999');
		$('#receiptModal').dialog({
			title: '領収書作成',
		});
		$('#receiptModal').dialog('open');
	});

	/**
	 * 領収書モーダルで郵送の場合のみ表示する項目の表示・非表示を制御
	 */
	$(document).on('change', '#receiptModal_mailFlg', function () {
		if ($(this).val() == 1) {
			$('.receipt-mail-only').show();
		} else {
			$('.receipt-mail-only').hide();
		}
	});

	/**
	 * 領収書モーダルで保存ボタンを押下した際の処理
	 */
	function saveReceipt() {

		$('.receipt-modal-button').prop('disabled', true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/PaymentDetails/ajaxSaveReceipt',
			data: {
				receipt_name_enc: $('#receiptModal_name').val(),
				receipt_title: $('#receiptModal_receiptTitle').val(),
				receipt_price: $('#receiptModal_price').val(),
				mail_flg: $('#receiptModal_mailFlg').val(),
				receipt_status: $('#receiptModal_receiptStatus').val(),
				lang_id: $('#receiptModal_language').val(),
				send_zip_code1: $('#receiptModal_zip1').val(),
				send_zip_code2: $('#receiptModal_zip2').val(),
				send_address1_enc: $('#receiptModal_address1').val(),
				send_address2_enc: $('#receiptModal_address2').val(),
				send_address3_enc: $('#receiptModal_address3').val(),
				send_name_enc: $('#receiptModal_sendName').val(),
				ignore_flg: $('#receiptModal_ignoreFlg').prop('checked') ? 1 : 0,
				reissue_flg: $('#receiptModal_reissueFlg').prop('checked') ? 1 : 0,
				delete_flg: $('#receiptModal_deleteFlg').prop('checked') ? 1 : 0,
				receipt_id: $('#receiptModal_receiptId').val(),
				cm_application_id: $('#cm_application_id').val(),
				reservation_id: $('#reservation_id').val(),
			}
		})
			.done(function (res) {
				if (res && res.ret.ret === 'ok') {
					alert('領収書情報を保存しました');
					location.reload();
				}
				else {
					alert('Error:領収書情報の保存に失敗しました\n\n' + res.ret.error?.join('\n'));
				}
			})
			.fail(function () {
				alert('Fail:領収書情報の保存に失敗しました');
			})
			.always(function () {
				$('.receipt-modal-button').prop('disabled', false);
			});
	}

	/**
	 * 領収書モーダルでプレビューボタンを押下した際の処理
	 */
	function previewReceipt() {

		window.open('', 'preview');
		var postData = {
			receipt_name_enc: $('#receiptModal_name').val(),
			receipt_title: $('#receiptModal_receiptTitle').val(),
			receipt_price: $('#receiptModal_price').val(),
			lang_id: $('#receiptModal_language').val(),
			receipt_id: $('#receiptModal_receiptId').val(),
			cm_application_id: $('#cm_application_id').val(),
			reservation_id: $('#reservation_id').val(),
		};
		for (key in postData) {
			var inputData = $('<input />', {
				type: 'hidden',
				name: key,
				value: postData[key]
			});
			$('#receiptPreviewForm').append(inputData);
		}
		$('#receiptPreviewForm').attr('target', 'preview').submit();
	}
});
</script>
<div class="paymentDetail index">
	<h3>入金詳細</h3>
	<?php echo $this->Form->input('reservation_id', ['type' => 'hidden', 'value' => $this->params['named']['reservation_id']]); ?>

	<div class="span11" style="border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
		<?php echo $this->Form->create('MessageBoard', [
			'default' => false
		]);?>
		<legend>伝言板</legend>
		<table class="table table-bordered">
			<tr>
				<th class="span4" style="text-align: center">担当者</th>
				<th style="text-align: center">伝言</th>
				<th class="span3" style="text-align: center">更新日時</th>
			</tr>
			<tbody id="sortable-div">
			<?php foreach ($MessageBoards as $messageBoard): ?>
			<tr>
				<td style="text-align: center"><?php echo h($messageBoard['Staff']['name']); ?></td>
				<td><?php echo h($messageBoard['MessageBoard']['message']); ?></td>
				<td style="text-align: center"><?php echo h($messageBoard['MessageBoard']['created']); ?></td>
			</tr>
			<?php endforeach;?>
			<tr>
				<td></td>
				<td><?php echo $this->Form->input('message',[
						'type' => 'textarea',
						'div' => false,
						'label' => false,
						'class' => 'span12'
					]); ?></td>
				<td></td>
			</tr>
			</tbody>
		</table>
		<div class="right">
			<?php echo $this->Form->submit('伝言板追加', [
				'id' => 'saveMessageBoard',
				'div' => false,
				'label' => false,
				'class'=>'btn btn-primary',
				'style' => 'border-radius: 5px;'
			]);?>
		</div>
		<?php echo $this->Form->end();?>
	</div>

	<div class="span11" style="margin-top: 10px;border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
		<?php echo $this->Form->create('Reservation', [
			'default' => false
		]);?>
		<legend>予約情報</legend>
		<table class="table table-bordered">
			<tr>
				<th style="text-align: center">予約番号</th>
				<th style="text-align: center">予約ステータス</th>
				<th style="text-align: center">入金ステータス</th>
				<th style="text-align: center">入金期限</th>
				<th style="text-align: center">会社名</th>
				<th style="text-align: center">申込日時</th>
			</tr>
			<tbody id="sortable-div">
				<tr>
					<td style="text-align: center"><a href="/rentacar/client/Reservations/edit/<?=$PR['Reservation']['id']?>?cid=<?=$PR['Reservation']['client_id']?>" target="_blank"><?php echo h($PR['Reservation']['reservation_key']); ?></a></td>
					<td style="text-align: center"><?php echo h($PR['ReservationStatus']['name']); ?></td>
					<td style="text-align: center"><?php echo $this->Form->input('payment_status',[
								'options' => $paymentStatusSelect,
								'div' => false,
								'label' => false,
								'value' => $defaultPaymentStatus
						]); ?>
					</td>
					<td style="text-align: center"><?php echo $this->Form->input('payment_limit_datetime', [
							'div' => false,
							'label' => false,
							'type' => 'text',
							'value' => h($PR['Reservation']['payment_limit_datetime'])
						]); ?>
					</td>
					<td style="text-align: center"><?php echo h($PR['Client']['name']); ?></td>
					<td style="text-align: center"><?php echo h($PR['Reservation']['created']); ?></td>
				</tr>
			</tbody>
		</table>
		<div class="right">
			<?php echo $this->Form->submit('予約情報保存', [
				'id' => 'saveReservation',
				'div' => false,
				'label' => false,
				'class'=>'btn btn-primary',
				'style' => 'border-radius: 5px;'
			]);?>
		</div>
		<?php echo $this->Form->end();?>
	</div>

	<div class="span11" style="margin-top: 10px;border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
		<legend>キャンセル情報</legend>
		<table class="table table-bordered">
			<tr>
				<th style="text-align: center">キャンセル日時</th>
				<th style="text-align: center">キャンセル理由</th>
				<th style="text-align: center">キャンセル理由詳細</th>
			</tr>
			<tbody id="sortable-div">
			<tr>
				<td style="text-align: center"><?php echo h($PR['Reservation']['cancel_datetime']); ?></td>
				<td><?php echo h($PR['CancelReason']['reason']); ?></td>
				<td><?php echo h($PR['Reservation']['cancel_remark']); ?></td>
			</tr>
			</tbody>
		</table>
	</div>

	<div class="span2" style="margin-top: 10px;border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
		<legend>入金情報</legend>
		<button id="paymentInfo" class="btn btn-large btn-primary">入金情報</button>
		<?php echo $this->Form->input('cm_application_id', ['type' => 'hidden', 'value' => $cmApplicationId]); ?>
	</div>

	<div class="span11" style="margin-top: 10px;border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
		<legend>econ入金情報</legend>
		<table class="table table-bordered">
			<tr>
				<th style="text-align: center">skyticket申込番号</th>
				<th style="text-align: center">econ注文番号</th>
				<th style="text-align: center">与信/計上</th>
				<th style="text-align: center">econ会員/非会員</th>
				<th style="text-align: center">決済開始日</th>
				<th style="text-align: center">決済金額</th>
				<th style="text-align: center">応答ステータス</th>
				<th style="text-align: center">決済処理結果</th>
			</tr>
			<tbody id="sortable-div">
			<?php foreach($Payments as $payment):?>
			<tr>
				<td style="text-align: center"><?php echo h($payment['Payment']['cm_application_id']); ?></td>
				<td id="order_id" style="text-align: center"><?php echo h($payment['Payment']['order_id']); ?></td>
				<td style="text-align: center"><?php echo h($payment['Payment']['keijou']); ?></td>
				<td style="text-align: center"><?php echo h($payment['Payment']['is_member']); ?></td>
				<td style="text-align: center"><?php echo h($payment['Payment']['create_dt']); ?></td>
				<td style="text-align: center"><?php echo h($payment['Payment']['price']); ?></td>
				<td style="text-align: center"><?php echo h($payment['Payment']['info']); ?></td>
				<td style="text-align: center"><?php echo h($payment['Payment']['status_str']); ?></td>
			</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>

	<div class="span11" style="margin-top: 10px;border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
		<?php echo $this->Form->create('CmThReceipt', [
			'default' => false
		]);?>
		<legend>領収書情報</legend>
		<table class="table table-bordered">
			<tr>
				<th style="text-align: center">領収書番号</th>
				<th style="text-align: center">宛名</th>
				<th style="text-align: center">但し書き</th>
				<th style="text-align: center">作成日時</th>
				<th style="text-align: center">発行種別</th>
				<th style="text-align: center">送付状態</th>
				<th style="text-align: center">金額</th>
				<th style="text-align: center">有効/無効</th>
				<th style="text-align: center">ＤＬ</th>
				<th></th>
			</tr>
			<tbody>
			<?php foreach($Receipts as $receipt):?>
				<tr>
					<td style="text-align: center"><a href="javascript:void(0)" class="edit-receipt"><?php echo h($receipt['CmThReceipt']['receipt_id']); ?></a></td>
					<td style="text-align: center"><?php echo h($receipt['CmThReceipt']['receipt_name_enc']); ?></td>
					<td style="text-align: center"><?php echo h($receipt['CmThReceipt']['receipt_title']); ?></td>
					<td style="text-align: center"><?php echo h($receipt['CmThReceipt']['create_dt']); ?></td>
					<td style="text-align: center"><?php echo $receiptMailFlg[$receipt['CmThReceipt']['mail_flg']]; ?></td>
					<td style="text-align: center"><?php echo $receiptStatus[$receipt['CmThReceipt']['receipt_status']]; ?></td>
					<td style="text-align: center"><?php echo h(number_format($receipt['CmThReceipt']['receipt_price'])); ?></td>
					<td style="text-align: center"><?php echo $receiptDeleteFlg[$receipt['CmThReceipt']['delete_flg']]; ?></td>
					<td style="text-align: center"><?php echo (empty($receipt['CmThReceipt']['receipt_dt']) || $receipt['CmThReceipt']['receipt_dt'] == '0000-00-00 00:00:00') ? '未' : '済'; ?></td>
					<td style="text-align: center"><a class="btn btn-primary" href="<?php echo '../downloadReceipt?receipt_id='.$receipt['CmThReceipt']['receipt_id']; ?>" target="_blank">ＤＬ</a></td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
		<div class="right">
			<?php echo $this->Form->submit('領収書作成', [
				'id' => 'createReceipt',
				'div' => false,
				'label' => false,
				'class'=>'btn btn-primary',
				'style' => 'border-radius: 5px;'
			]);?>
		</div>
		<?php echo $this->Form->end();?>
		<div id="receiptModal" style="display:none">
			<table class="table table-borderless table-vcenter small">
				<tr>
					<th>領収書宛名</th>
					<td>
						<input id="receiptModal_name" class="span2" type="text" placeholder="田中 花子" />
					</td>
				</tr>
				<tr>
					<th>但し書き</th>
					<td>
						<input id="receiptModal_receiptTitle" class="span2" type="text" placeholder="旅行代金として" />
					</td>
				</tr>
				<tr>
					<th>金額</th>
					<td>
						<input id="receiptModal_price" class="span2" type="text" placeholder="10000" />
					</td>
				</tr>
				<tr>
					<th>金額上限無視</th>
					<td>
						<input id="receiptModal_ignoreFlg" type="checkbox" value="1" />
						<span>※金額が発行可能額を超えても保存できます。</span>
					</td>
				</tr>
				<tr>
					<th>発行種別</th>
					<td>
						<select id="receiptModal_mailFlg" class="span2">
							<?php foreach ($receiptMailFlg as $code => $name) { ?>
                                <option value="<?=$code?>"><?=$name?></option>
							<?php } ?>
						/select>
					</td>
				</tr>
				<tr>
					<th>送付状態</th>
					<td>
						<select id="receiptModal_receiptStatus" class="span2">
							<?php foreach ($receiptStatus as $code => $name) { ?>
                                <option value="<?=$code?>"><?=$name?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>言語</th>
					<td>
						<select id="receiptModal_language" class="span2">
							<?php foreach ($receiptLangId as $code => $name) { ?>
                                <option value="<?=$code?>"><?=$name?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>再発行</th>
					<td>
						<input id="receiptModal_reissueFlg" type="checkbox" value="1" />
						<span>※領収書に再発行印が入ります（プレビューには入りません）。</span>
					</td>
				</tr>
				<tr>
					<th>無効</th>
					<td>
						<input id="receiptModal_deleteFlg" type="checkbox" value="1" />
						<span>※領収書が無効になります。</span>
					</td>
				</tr>
				<tr class="receipt-mail-only">
					<th>郵便番号</th>
					<td>
						<input id="receiptModal_zip1" class="span1" type="text" maxlength="3" placeholder="123" />-
						<input id="receiptModal_zip2" class="span1" type="text" maxlength="4" placeholder="4567" />
					</td>
				</tr>
				<tr class="receipt-mail-only">
					<th>住所</th>
					<td>
						<ul style="list-style: none; margin-left: 0">
							<li>
								<select id="receiptModal_address1" class="span2">
									<option value="">都道府県</option>
									<?php foreach ($prefectures as $id => $name) { ?>
										<option value="<?=$id?>"><?=$name?></option>
									<?php } ?>
								</select>
							</li>
							<li><input id="receiptModal_address2" class="span3" type="text" placeholder="渋谷区恵比寿4-20-3" /></li>
							<li><input id="receiptModal_address3" class="span3" type="text" placeholder="恵比寿ガーデンプレイスタワー24F" /></li>
						</ul>
					</td>
				</tr>
				<tr class="receipt-mail-only">
					<th>送付先宛名</th>
					<td>
						<input id="receiptModal_sendName" class="span2" type="text" placeholder="鈴木 次郎" />
					</td>
				</tr>
			</table>
			<input id="receiptModal_receiptId" type="hidden" />
			<form id="receiptPreviewForm" action="../previewReceipt" method="post"></form>
		</div>
	</div>

	<div class="span11" style="margin-top: 10px">
		<div class="span6" style="border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
			<legend>入金明細</legend>
			<?php echo $this->Form->create('PaymentDetail', [
				'default' => false
			]);?>
			<table class="table table-bordered">
				<tr>
					<th class="span3" style="text-align: center">科目名</th>
					<th class="span2" style="text-align: center">単価</th>
					<th class="span1" style="text-align: center">数量</th>
					<th class="span2" style="text-align: center">合計</th>
					<th class="span2" style="text-align: center">備考</th>
				</tr>
				<tbody id="sortable-div">
					<?php foreach ($reservationDetails as $reservationDetail): ?>
					<tr>
						<td><?php echo h($reservationDetail['DetailType']['name']); ?></td>
						<td style="text-align: right"><?php echo h(number_format($reservationDetail['ReservationDetail']['amount'] / $reservationDetail['ReservationDetail']['count'])); ?> </td>
						<td style="text-align: center"><?php echo h(number_format($reservationDetail['ReservationDetail']['count'])); ?></td>
						<td style="text-align: right"><?php echo h(number_format($reservationDetail['ReservationDetail']['amount'])); ?> </td>
						<td></td>
					</tr>
					<?php endforeach; ?>
					<tr>
						<td>決済手数料</td>
						<td style="text-align: right"><?php echo h(number_format($administrative_fee)); ?> </td>
						<td style="text-align: center">1</td>
						<td style="text-align: right"><?php echo h(number_format($administrative_fee)); ?> </td>
						<td></td>
					</tr>
					<tr style="background-color: #FFCCCC;">
						<td>予約時入金 合計金額</td>
						<td></td>
						<td></td>
						<td style="text-align: right"><?php echo h(number_format($reservationDetailAmountSum)); ?> </td>
						<td></td>
					</tr>
					<?php foreach ((array)$paymentDetails as $paymentDetail): ?>
					<tr>
						<td><?php echo Constant::paymentAccountCode()[$paymentDetail['PaymentDetail']['account_code']]; ?></td>
						<td style="text-align: right"><?php echo h(number_format($paymentDetail['PaymentDetail']['amount'])); ?> </td>
						<td style="text-align: center"><?php echo h(number_format($paymentDetail['PaymentDetail']['count'])); ?></td>
						<td style="text-align: right"><?php echo h(number_format($paymentDetail['PaymentDetail']['amount'] * $paymentDetail['PaymentDetail']['count'])); ?> </td>
						<td><?php echo h($paymentDetail['PaymentDetail']['remarks']); ?></td>
					</tr>
					<?php endforeach; ?>
					<tr>
						<td><?php echo $this->Form->input('account_code', [
								'empty' => '--',
								'options' => Constant::paymentAccountCode(),
								'div' => false,
								'label' => false,
								'class' => 'span10',
								'require' => true,
							]); ?>
						</td>
						<td style="text-align: right"><?php echo $this->Form->input('amount', [
								'type' => 'text',
								'div' => false,
								'label' => false,
								'maxlength' => 10,
								'class' => 'span10',
								'style' => 'text-align: right',
								'require' => true,
							]); ?></td>
						<td style="text-align: center"><?php echo $this->Form->input('count', [
								'type' => 'text',
								'div' => false,
								'label' => false,
								'maxlength' => 3,
								'class' => 'span12',
								'style' => 'text-align: right',
								'require' => true,
							]); ?></td>
						<td style="text-align: right"><span id='PaymentDetailSum'></span></td>
						<td>
							<?php echo $this->Form->input('remarks', [
								'type' => 'text',
								'div' => false,
								'label' => false,
								'class' => 'span12',
								'maxlength' => 30
							]); ?>
						</td>
					</tr>
					<tr style="background-color: #FFCCCC;">
						<td>調整後 合計金額</td>
						<td></td>
						<td></td>
						<td style="text-align: right"><span id='totalAmount'><?php echo h(number_format($totalAmount)); ?></span></td>
						<td><span id="totalAmountRemarks"></span></td>
					</tr>
				</tbody>
			</table>
			<div class="right">
				<?php echo $this->Form->submit('入金明細保存', [
					'id' => 'savePaymentDetail',
					'div' => false,
					'label' => false,
					'class'=>'btn btn-primary',
					'style' => 'border-radius: 5px;'
				]);?>
			</div>
			<?php echo $this->Form->end();?>
		</div>
		<div class="span6" style="border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
			<legend>キャンセル料明細</legend>
			<?php echo $this->Form->create('CancelDetail', [
				'default' => false
			]);?>
			<table class="table table-bordered">
				<tr>
					<th class="span3" style="text-align: center">科目名</th>
					<th class="span2" style="text-align: center">単価</th>
					<th class="span1" style="text-align: center">数量</th>
					<th class="span2" style="text-align: center">合計</th>
					<th class="span2" style="text-align: center">備考</th>
				</tr>
				<tbody id="sortable-div">
				<?php foreach ((array)$cancelDetails as $cancelDetail): ?>
				<tr>
					<td><?php echo Constant::cancelAccountCode()[$cancelDetail['CancelDetail']['account_code']]; ?></td>
					<td style="text-align: right"><?php echo h(number_format($cancelDetail['CancelDetail']['amount'])); ?> </td>
					<td style="text-align: center"><?php echo h(number_format($cancelDetail['CancelDetail']['count'])); ?></td>
					<td style="text-align: right"><?php echo h(number_format($cancelDetail['CancelDetail']['amount'] * $cancelDetail['CancelDetail']['count'])); ?> </td>
					<td><?php echo h($cancelDetail['CancelDetail']['remarks']); ?></td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td><?php echo $this->Form->input('account_code', [
							'empty' => '--',
							'options' => [
									'ADVENTURE_FEE' => '取消手続料',
									'ADMINISTRATIVE_FEE' => '決済手数料',
									'OTHER' => '【キャンセル料】その他'
							], // constant.phpに項目リストの定義あるけど、ここでは1つしか表示しないのでハードコーディングする
							'div' => false,
							'label' => false,
							'class' => 'span12',
						]); ?>
					</td>
					<td style="text-align: right"><?php echo $this->Form->input('amount', [
							'type' => 'text',
							'div' => false,
							'label' => false,
							'maxlength' => 10,
							'class' => 'span10',
							'style' => 'text-align: right'
						]); ?></td>
					<td style="text-align: center"><?php echo $this->Form->input('count', [
							'type' => 'text',
							'div' => false,
							'label' => false,
							'maxlength' => 3,
							'class' => 'span12',
							'style' => 'text-align: right'
						]); ?></td>
					<td style="text-align: right"><span id='cancelAmountSum'></span></td>
					<td>
						<?php echo $this->Form->input('remarks', [
							'type' => 'text',
							'div' => false,
							'label' => false,
							'class' => 'span12',
							'maxlength' => 30
						]); ?>
					</td>
				</tr>
				<tr style="background-color: #FFCCCC;">
					<td>合計金額</td>
					<td></td>
					<td></td>
					<td style="text-align: right"><span id='cancelDetailAmountSum'><?php echo h(number_format($cancelDetailAmountSum)); ?></span></td>
					<td><span id="cancelDetailSumRemarks"></span></td>
				</tr>
				<tr style="color: darkgrey;">
					<td>今キャンセルした場合の金額(※入金済みのとき)</td>
					<td></td>
					<td></td>
					<td style="text-align: right"><?php echo h(number_format($testCancelFeeData['sum'])); ?></td>
					<td>適用ID:<?=$testCancelFeeData['id'];?></td>
				</tr>
				</tbody>
			</table>
            <?php if ($canEdit) { ?>
			<div class="right">
				<?php echo $this->Form->submit('キャンセル明細保存', [
					'id' => 'saveCancelDetail',
					'div' => false,
					'label' => false,
					'class'=>'btn btn-primary',
					'style' => 'border-radius: 5px;'
				]);?>
			</div>
            <?php } ?>
			<?php echo $this->Form->end();?>
		</div>
	</div>

	<div class="span11" style="margin-top: 10px;">
		<div class="span5" style="border-style:solid;border-width: 1px;border-color: lightgrey;border-radius: 10px;padding: 5px;">
			<legend>決済状況</legend>
			<?php echo $this->Form->create('Refund', [
				'default' => false
			]);?>
			<table class="table table-bordered">
				<tr>
					<th class="span2">入金済み</th>
					<td style="text-align: right"><?php echo h(number_format($payedAmount)); ?></td>
				</tr>
				<tr>
					<th style="border-bottom: 1px solid gray;"> − キャンセル料</th>
					<td style="text-align: right; border-bottom: 1px solid gray;"><?php echo h(number_format($cancelDetailAmountSum)); ?></td>
				</tr>
				<tr>
					<th style="border-top: 1px solid gray">返金予定</th>
					<td style="text-align: right;border-top: 1px solid gray"><?php echo h(number_format($schedulRefundAmount)); ?></td>
				</tr>
				<tr>
					<th>返金依頼中</th>
					<td style="text-align: right"><?php echo h(number_format($refundingAmount)); ?></td>
				</tr>
				<tr>
					<th style="border-bottom: 1px solid gray;">返金済</th>
					<td style="text-align: right; border-bottom: 1px solid gray;"><?php echo h(number_format($refundedAmount)); ?></td>
				</tr>
				<tr>
					<th style="border-top: 1px solid gray">残金</th>
					<td style="text-align: right; border-top: 1px solid gray"><?php echo h(number_format($remainingAmount)); ?></td>
				</tr>
			</table>
			<?php echo $this->Form->input('schedulRefundAmount', ['type' => 'hidden', 'value' => $schedulRefundAmount]);?>
			<?php echo $this->Form->input('refundingAmount', ['type' => 'hidden', 'value' => $refundingAmount]);?>
			<?php echo $this->Form->input('refundedAmount', ['type' => 'hidden', 'value' => $refundedAmount]);?>
			<?php echo $this->Form->input('remainingAmount', ['type' => 'hidden', 'value' => $remainingAmount]);?>
			<div class="right">
				<?php echo $this->Form->submit('返金実行', [
					'id' => 'execRefund',
					'div' => false,
					'label' => false,
					'class'=>'btn btn-large btn-danger',
					'style' => 'border-radius: 10px;'
				]);?>
			</div>
			<?php echo $this->Form->end();?>
		</div>
	</div>
</div>

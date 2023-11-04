<?php echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/themes/redmond/jquery-ui.min.css"); ?>
<script>
	/*
	 * 初期化
	 */
	$(document).on('ready', function(){
		const pickeroption = {
			dateFormat: 'yy-mm-dd',
			monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			dayNames: [ '日' , '月', '火', '水', '木', '金', '土'],
			dayNamesShort: [ '日' , '月', '火', '水', '木', '金', '土'],
			dayNamesMin: [ '日' , '月', '火', '水', '木', '金', '土'],
			prevText: '前へ',
			nextText: '次へ',
		};

		$('input[name="new_created_at_start"], input[name="new_created_at_end"], #PaymentReserveCreatedStart, #PaymentReserveCreatedEnd, #PaymentReserveCanceledStart, #PaymentReserveCanceledEnd').datepicker(
			pickeroption
		);
	});

	function yoshinCancel($orderCode, $target) {
		if (!confirm('与信を取消します。よろしいですか')) {
			return false;
		}
		$target.disabled = true;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/Payments/ajaxYoshinCancelForAPI',
			data: {
				orderCode: $orderCode
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				alert('取消しました');
				location.reload();
			}
			else {
				alert('Error:失敗しました。' + res.message);
			}
		}).
		fail(function(){
			alert('Fail:失敗しました');
		})
		.always(function(){
			$target.disabled = false;
		});
	}

	function cardCapture($orderCode, $target, $reservationId) {
		if (!confirm('与信を計上します。よろしいですか')) {
			return false;
		}
		$target.disabled = true;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/Payments/ajaxCardCaptureForAPI',
			data: {
				orderCode: $orderCode,
				reservationId: $reservationId
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				alert('計上にしました');
				location.reload();
			}
			else {
				alert('Error:失敗しました。' + res.message);
			}
		}).
		fail(function(){
			alert('Fail:失敗しました');
		})
		.always(function(){
			$target.disabled = false;
		});
	}

</script>
<div class="payments index">
	<h3>入金一覧(新)</h3>
	<?php echo $this->Form->create('Payment/new_index',['type'=>'get']); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>skyticket申込番号</th>
				<td><?php echo $this->Form->input('new_cm_application_id', ['type' => 'text','div'=>false,'label'=>false, 'value'=>$new_cm_application_id]); ?>
				</td>
			</tr>
			<tr>
				<th>カート番号</th>
				<td><?php echo $this->Form->input('new_cart_id', ['type' => 'text','div'=>false,'label'=>false, 'value'=>$new_cart_id]); ?>
				</td>
			</tr>
			<tr>
				<th>注文番号</th>
				<td><?php echo $this->Form->input('new_order_code', ['type' => 'text','div'=>false,'label'=>false, 'value'=>$new_order_code]); ?>
				</td>
			</tr>
			<tr>
				<th>決済開始日</th>
				<td><?php echo $this->Form->input('new_created_at_start', [
						'type' => 'text','div'=>false,'label'=>false, 'value'=>$new_created_at_start
						]); ?> ~ 
					<?php echo $this->Form->input('new_created_at_end', [
						'type' => 'text','div'=>false,'label'=>false, 'value'=>$new_created_at_end
						]); ?>
				</td>
			</tr>
			<tr>
				<th>決済処理結果</th>
				<td><?php echo $this->Form->input('progress', [
						'empty' => '--',
						'options' => [
							0  => '決済開始前', // BEFORE_START 1時間後にソフトデリート・24時間後に完全削除
                            1  => '決済開始', // START
                            2  => '与信', // AUTH
                            3  => '計上', // CAPTURE
                            4  => 'キャンセル', // CANCEL
                            5  => '返金要求', // REFUND_REQ
                            6  => '決済代行へ返金リクエスト送信済', // SENT_REFUND
                            7  => '決済代行側で返金処理済', // REFUNDED
                            98 => '返金エラー', // REFUND_ERROR
                            99 => '決済処理中断' // ABORT
						],
						'div' => false,
						'label' => false,
						'value' => $progress
				]);?>
				</td>
            </tr>
		</table>
		<br />

	<?php
		echo $this->Form->submit('検索する', ['class' => 'btn btn-primary', 'div' => false]);
		echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		echo $this->Form->submit('csv出力', array('class' => 'btn btn-warning', 'name' => 'getCsv', 'value' => '1', 'div' => false, 'style' => 'margin-left:20px'));
	?>
	<?php echo $this->Form->end(); ?>
	<br />

	<table class="table table-bordered">
		<tr>
			<td colspan='7' style='background-color: #3A87AD; color:#FFF; text-align:center; font-weight: bold;'>入金情報</td>
			<td colspan='10' style='background-color: #3A87AD; color:#FFF; text-align:center; font-weight: bold;'>レンタカー予約情報</td>
		</tr>
		<tr>
			<th>skyticket申込番号</th>
            <th>カート番号</th>
            <th>注文番号</th>
			<th>決済開始日</th>
			<th>決済金額</th>
			<th>決済処理結果</th>
			<th>アクション</th>
			<th>予約番号</th>
			<th>予約ステータス</th>
			<th>入金ステータス</th>
			<th>会社名</th>
			<th>申込日時</th>
			<th>キャンセル申込日時</th>
			<th>キャンセル理由</th>
		</tr>
		<tbody id="sortable-div">
			<?php foreach ($payments as $payment): ?>
			<tr>
				<td><?php echo h($payment['cm_application_id']); ?></td>
                <td><?php echo h($payment['cart_id']); ?></td>
                <td><?php echo h($payment['order_code']); ?></td>
				<td><?php echo h($payment['created_at']); ?></td>
                <td><?php echo h($payment['price']); ?></td>
				<td><?php echo h($payment['progress_name']); ?></td>
				<td><?php
					if (($payment['Reservation']['reservation_status_id'] == 1 // 予約
					|| $payment['Reservation']['reservation_status_id'] == 2) // 成約
					&& $payment['progress_name'] === '与信'
					&& !empty($payment['order_code'])
					) {
						echo $this->Form->button(
							'計上',
							['type'    => 'button',
							'class'   => 'btn btn-danger',
							'onclick' => "cardCapture('".$payment['order_code']."', this, '" .$payment['Reservation']['id']. "')",
						]
						);
					}
					if ($payment['progress_name'] === '与信'
					&& !empty($payment['order_code'])) {
						echo $this->Form->button(
							'取消',
							['type'    => 'button',
							'class'   => 'btn btn-danger',
							'onclick' => "yoshinCancel('".$payment['order_code']."', this)",
							]
						);
					}
					?>
				</td>
				<td class='saturday'><a href="../PaymentDetails/index/reservation_id:<?=$payment['Reservation']['id']?>"><?php echo h($payment['Reservation']['reservation_key']); ?></a></td>
				<td class='saturday'><?php echo h($payment['ReservationStatus']['name']); ?></td>
				<td class='saturday'><?php echo h($payment['Reservation']['payment_status']); ?></td>
				<td class='saturday'><?php echo h($payment['Client']['name']); ?></td>
				<td class='saturday'><?php echo h($payment['Reservation']['created']); ?></td>
				<td class='saturday'><?php echo h($payment['Reservation']['cancel_datetime']); ?></td>
				<td class='saturday'><?php echo h($payment['CancelReason']['reason']); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
    <?php
		if ($is_pagenate) {
            echo "ページ {$current_page} / {$last_page}  :  総レコード/ {$total}件";
	?>
	<div class="pagination pagination-right">
		<ul>
        <?php echo $paging; ?>
		</ul>
	</div>
	<?php } ?>
</div>

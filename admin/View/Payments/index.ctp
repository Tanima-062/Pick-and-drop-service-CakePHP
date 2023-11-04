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

		$('#PaymentCreateDtStart, #PaymentCreateDtEnd, #PaymentReserveCreatedStart, #PaymentReserveCreatedEnd, #PaymentReserveCanceledStart, #PaymentReserveCanceledEnd').datepicker(
			pickeroption
		);
	});

	function yoshinCancel($orderId, $target) {
		$target.disabled = true;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/Payments/ajaxYoshinCancel',
			data: {
				order_id: $orderId
			}
		})
		.done(function(res){
			if (res && res.ret === 'ok') {
				alert('与信をキャンセルしました');
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

	function cardCapture($orderId, $target) {
		$target.disabled = true;
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/Payments/ajaxCardCapture',
			data: {
				order_id: $orderId
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
	<h3>入金一覧</h3>
	<?php echo $this->Form->create('Payment',['type'=>'get']); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>skyticket申込番号</th>
				<td><?php echo $this->Form->input('cm_application_id', ['type' => 'text','div'=>false,'label'=>false, 'value'=>$cm_application_id]); ?>
				</td>
			</tr>
			<tr>
				<th>econ注文番号</th>
				<td><?php echo $this->Form->input('order_id', ['type' => 'text','div'=>false,'label'=>false, 'value'=>$order_id]); ?>
				</td>
			</tr>
			<tr>
				<th>決済開始日</th>
				<td><?php echo $this->Form->input('create_dt_start', [
						'type' => 'text','div'=>false,'label'=>false, 'value'=>$create_dt_start
						]); ?> ~ 
					<?php echo $this->Form->input('create_dt_end', [
						'type' => 'text','div'=>false,'label'=>false, 'value'=>$create_dt_end
						]); ?>
				</td>
			</tr>
			<tr>
				<th>決済処理結果</th>
				<td><?php echo $this->Form->input('payment_result', [
						'empty' => '--',
						'options' => [
							'success' => '決済正常完了',
							'hold' => '決済通知待ち',
							'error' => '決済失敗',
							'cancel' => '決済キャンセル',
						],
						'div' => false,
						'label' => false,
						'value' => $payment_result
				]);?>
				</td>
			</tr>
			<tr>
				<th>予約番号</th>
				<td><?php echo $this->Form->input('reservation_key_compress', [
					'type' => 'text','div'=>false,'label'=>false, 'value'=>$reservation_key_compress
					]); ?>
				</td>
			</tr>
			<tr>
				<th>予約ステータス</th>
				<td><?php echo $this->Form->input('reservation_status', [
						'empty' => '--',
						'options' => $reservation_status_arr,
						'div' => false,
						'label' => false,
						'value' => $reservation_status
				]);?>
				</td>
			</tr>
			<tr>
				<th>入金ステータス</th>
				<td><?php echo $this->Form->input('payment_status', [
						'empty' => '--',
						'options' => Constant::paymentStatus(),
						'div' => false,
						'label' => false,
						'value' => $payment_status
				]);?>
				</td>
			</tr>
			<tr>
				<th>会社名</th>
				<td><?php echo $this->Form->input('client_id', [
						'empty' => '--',
						'options' => $clientList,
						'div' => false,
						'label' => false,
						'value' => $client_id
				]);?>
				</td>
			</tr>
			<tr>
				<th>申込日時</th>
				<td><?php echo $this->Form->input('reserve_created_start', [
					'type' => 'text','div'=>false,'label'=>false,'value'=>$reserve_created_start
					]); ?> ~ 
					<?php echo $this->Form->input('reserve_created_end', [
					'type' => 'text','div'=>false,'label'=>false,'value'=>$reserve_created_end
					]); ?>
				</td>
			</tr>
			<tr>
				<th>キャンセル申込日時</th>
				<td><?php echo $this->Form->input('reserve_canceled_start', [
					'type' => 'text','div'=>false,'label'=>false,'value'=>$reserve_canceled_start
					]); ?> ~ 
					<?php echo $this->Form->input('reserve_canceled_end', [
					'type' => 'text','div'=>false,'label'=>false,'value'=>$reserve_canceled_end
					]); ?>
				</td>
			</tr>
			<tr>
				<th>キャンセル理由</th>
				<td><?php echo $this->Form->input('cancel_reason_id', [
						'empty' => '--',
						'options' => $cancelReasons,
						'div' => false,
						'label' => false,
						'value' => $cancel_reason_id
					]); ?>
				</td>
			</tr>
		</table>
		<br />

	<?php echo $this->Form->submit('検索する', ['class'=>'btn btn-primary', 'div' => false])?>
	<?php echo $this->Form->submit('csv出力',array('class'=>'btn btn-warning','name'=>'getCsv','value'=>'1', 'div' => false, 'style' => 'margin-left:20px'))?>
	<?php echo $this->Form->end(); ?>
	<br />

	<table class="table table-bordered">
		<tr>
			<td colspan='9' style='background-color: #3A87AD; color:#FFF; text-align:center; font-weight: bold;'>econ入金情報</td>
			<td colspan='7' style='background-color: #3A87AD; color:#FFF; text-align:center; font-weight: bold;'>フェリー予約情報</td>
		</tr>
		<tr>
			<th>skyticket申込番号</th>
			<th>econ注文番号</th>
			<th>与信/計上</th>
			<th>econ会員/非会員</th>
			<th>決済開始日</th>
			<th>決済金額</th>
			<th>応答ステータス</th>
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
				<td><?php echo h($payment['Payment']['cm_application_id']); ?></td>
				<td><?php echo h($payment['Payment']['order_id']); ?></td>
				<td><?php echo h($payment['Payment']['keijou']); ?></td>
				<td><?php echo h($payment['Payment']['is_member']); ?></td>
				<td><?php echo h($payment['Payment']['create_dt']); ?></td>
				<td><?php echo h($payment['Payment']['price']); ?></td>
				<td><?php echo h($payment['Payment']['info']); ?></td>
				<td><?php echo h($payment['Payment']['status_str']); ?></td>
				<td><?php if ($paymentComponent->isYoshinCancel($payment)) {
						      echo $this->Form->button(
								  '取消',
								  ['type'    => 'button',
								   'class'   => 'btn btn-danger',
								   'onclick' => "yoshinCancel('".$payment['Payment']['order_id']."', this)",
								  ]
							  );
						  } elseif ($paymentComponent->canCardCapture($payment)) {
							echo $this->Form->button(
								'計上',
								['type'    => 'button',
								'class'   => 'btn btn-danger',
								'onclick' => "cardCapture('".$payment['Payment']['order_id']."', this)",
							   ]
							);
						  }
					?>
				</td>
				<td class='saturday'><a href="PaymentDetails/index/reservation_id:<?=$payment['Reservation']['id']?>"><?php echo h($payment['Reservation']['reservation_key']); ?></a></td>
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
			echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));
	?>
	<div class="pagination pagination-right">
		<ul>
			<li><?php echo $this->Paginator->prev('< 前へ', [], null, ['class' => 'prev disabled']); ?></li>
			<li><?php echo $this->Paginator->numbers(); ?></li>
			<li><?php echo $this->Paginator->next('次へ >', [], null, ['class' => 'next disabled']); ?></li>
		</ul>
	</div>
	<?php } ?>
</div>

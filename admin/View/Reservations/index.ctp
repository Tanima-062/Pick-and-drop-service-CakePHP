<style>
* {
font-size: 11px;

}

</style>
<div class="reservations index">

	<?php if (!empty($messages)) : ?>
	<div class="news">
		<h3>お知らせ</h3>
		<table class="table table-bordered">
		<tr class="success">
			<th style="width: 15%">日時</th>
			<th>内容</th>
		</tr>

		<?php foreach ($messages as $message): ?>
		<tr>
			<td>
			<?php echo h(date("Y年m月d日 H:i", strtotime($message['Message']['modified']))); ?>
			</td>
			<td>
			<?php echo $this->Html->link($message['Message']['title'], '/news/show/'.$message['Message']['id']); ?>
			</td>
		<tr>
		<?php endforeach; ?>
		</table>
	</div>
	<?php endif; ?>

	<h3><?php echo __('予約一覧'); ?></h3>
	<?php echo $this->Form->create('Reservation', array('type' => 'get', 'url' => '.')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>販売方法</th>
				<td><?php echo $this->Form->input('SalesType', array('div' => false, 'label' => false, 'options' => Constant::salesType(), 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>ステータス</th>
				<td><?php echo $this->Form->input('ReservationStatus', array('div' => false, 'label' => false, 'options' => array('empty' => false, $reservationStatus))); ?></td>
			</tr>
<?php /*
			<!--
			<tr>
				<th>返信状況</th>
				<td><?php echo $this->Form->input('mail_status', array('options' => array('empty' => false, $mailStatus),'label'=>false,'div'=>false));?></td>
			</tr>
			-->
*/ ?>
			<tr>
				<th>skyticket申込番号</th>
				<td><?php echo $this->Form->input('CmApplicationId', array('type' => 'textarea', 'class' => 'span4', 'div' => false, 'label' => false)); ?></td>
			</tr>
			<tr>
				<th>予約番号</th>
				<td><?php echo $this->Form->input('ReservationKeyId', array('type' => 'textarea', 'class' => 'span4', 'div' => false, 'label' => false)); ?></td>
			</tr>
			<tr>
				<th>氏名カナ</th>
				<td>(姓)　<?php echo $this->Form->input('ReservationLastName', array('div' => false, 'label' => false)); ?>　(名)　<?php echo $this->Form->input('ReservationFirstName', array('div' => false, 'label' => false)); ?></td>
			</tr>

			<tr>
				<th>メールアドレス</th>
				<td><?php echo $this->Form->input('Email', array('div' => false, 'label' => false)); ?></td>
			</tr>
			<tr>
				<th>電話番号</th>
				<td><?php echo $this->Form->input('ReservationTel', array('div' => false, 'label' => false)); ?></td>
			</tr>

			<tr>
				<th>利用開始日</th>
				<td><?php echo $this->element('selectDatetime', $datetimeRentFromOptions); ?><br>〜<?php echo $this->element('selectDatetime', $datetimeRentToOptions); ?></td>
			</tr>
			<tr>
				<th>利用終了日</th>
				<td><?php echo $this->element('selectDatetime', $datetimeReturnFromOptions); ?><br>〜<?php echo $this->element('selectDatetime', $datetimeReturnToOptions); ?></td>
			</tr>
			<tr>
				<th>申込み日時</th>
				<td><?php echo $this->element('selectDatetime', $datetimeBookingFromOptions); ?><br>〜<?php echo $this->element('selectDatetime', $datetimeBookingToOptions); ?></td>
			</tr>
			<tr>
				<th>会社名</th>
				<td><?php echo $this->Form->input('Client', array('div' => false, 'label' => false, 'options' => $clientList, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>レコメンド</th>
				<td><?php echo $this->Form->input('Recommend', array('div' => false, 'label' => false, 'options' => array(1 => '対象', 0 => '対象外'), 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>車両タイプ</th>
				<td><?php echo $this->Form->input('CarType', array('div' => false, 'label' => false, 'options' => $carTypeList, 'empty' => '---')) ?></td>
			</tr>
			<tr>
				<th>広告コード</th>
				<td><?php echo $this->Form->input('AdvertisingCd', array('div' => false, 'label' => false)); ?></td>
			</tr>
			<tr>
				<th>支払方法</th>
				<td><?php echo $this->Form->input('PaymentMethod', array('div' => false, 'label' => false, 'options' => $paymentMethod, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>入金ステータス</th>
				<td><?php echo $this->Form->input('PaymentStatus', ['div' => false, 'label' => false, 'options' => Constant::paymentStatus(), 'empty' => '---']); ?></td>
			</tr>
		</table>
		<br />
	<div style="float:left;padding:0px 20px 0px 0px;">
	<?php
		echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
		echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
	?>
	</div>
	<?php echo $this->Form->submit('csv出力', array('class' => 'btn btn-warning', 'name' => 'getCsv', 'value' => '1', 'div' => false)); ?>
	<?php echo $this->Html->link('インポート', array('action' => 'import'), array('class' => 'btn btn-success', 'style' => 'margin-left: 10px')); ?>
	<?php echo $this->Form->end(); ?>

	<?php if (!empty($reservations)) { ?>

	<p>
		PC:<?php echo $count['pc_count']; ?>件　スマホ:<?php echo $count['sp_count']; ?>件
	</p>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>skyticket申込番号</th>
			<th>予約番号</th>
			<th>販売方法</th>
			<th>ｽﾃｰﾀｽ</th>
			<th>キャンセルタイプ</th>
			<th>予約代表者名</th>
			<th>商品名</th>
			<th>車両クラス</th>
			<th>料金</th>
			<th>利用時間</th>
			<th>受取店舗</th>
			<th>返却店舗</th>
			<th>会社名</th>
			<th>支払方法</th>
			<th>入金ステータス</th>
			<th>申込み日時</th>
			<th>キャンセル日時</th>
		</tr>

	<?php foreach ($reservations as $reservation) { ?>
		<tr>
			<td><?php echo h($reservation['CmThApplicationDetail']['cm_application_id']);?></td>
			<td><a href="/rentacar/client/Reservations/edit/<?=$reservation['Reservation']['id']?>?cid=<?=$reservation['Reservation']['client_id']?>" target="_blank"><?=$reservation['Reservation']['reservation_key']?></a></td>
			<td><?php echo h(Constant::salesType()[$reservation['Commodity']['sales_type']]); ?>&nbsp;</td>
			<td><?php echo h($reservationStatus[$reservation['Reservation']['reservation_status_id']]); ?>&nbsp;</td>
			<td><?php echo $reservation['Reservation']['cancel_flg'] ? ($cancelType[$reservation['Reservation']['cancel_reason_id']]): ''; ?></td>
			<td><?php echo h($reservation['Reservation']['last_name'] . ' ' . $reservation['Reservation']['first_name']); ?>&nbsp;</td>
			<td><?php echo h($reservation['Commodity']['name']); ?>&nbsp;</td>
			<td><?php echo h($reservation['CarClasses']['name']); ?>&nbsp;</td>
			<td><?php echo h(number_format($reservation['Reservation']['amount'])); ?>円&nbsp;</td>
			<td><?php echo h($reservation['Reservation']['rent_datetime']);  echo "<br>-<br>" . h($reservation['Reservation']['return_datetime']); ?>&nbsp;</td>
			<td><?php echo h($reservation['RentOffices']['name']); ?>&nbsp;</td>
			<td><?php echo h($reservation['ReturnOffices']['name']); ?>&nbsp;</td>
			<td><?php echo h($reservation['Client']['name']); ?></td>
			<td><?php echo $paymentMethod[$reservation['Commodity']['sales_type'] == Constant::SALES_TYPE_ARRANGED ? isset($reservation['Reservation']['payment_status']) : ($reservation['Reservation']['sales_price'] > 0)]; ?></td>
			<td><?php echo $reservation['Reservation']['payment_status_jp']; ?></td>
			<td><?php echo $reservation['Reservation']['created']; ?></td>
			<td><?php echo $reservation['Reservation']['cancel_flg'] ? $reservation['Reservation']['cancel_datetime'] : ''; ?></td>
		</tr>
	<?php } ?>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));?>

	<div class="pagination">
		<ul>
			<?php
				echo '<li>'.$this->Paginator->prev('< ' . __('戻る'), array(), null, array('class' => 'prev disabled')). '</li>';
				echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';
				echo '<li>'.$this->Paginator->next(__('次へ') . ' >', array(), null, array('class' => 'next disabled')). '</li>';
			?>
		</ul>
	</div>

	<?php } ?>
</div>

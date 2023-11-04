<div class="tourreservations index">
	<h3>募集型予約一覧</h3>
	<?php echo $this->Form->create('TourReservation', array('type' => 'get', 'inputDefaults' => array('label' => false))); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>ツアー予約番号</th>
				<td><?php echo $this->Form->input('cm_application_id' ,array('type' => 'textarea', 'class' => 'span4', 'div' => false, 'label' => false)); ?></td>
			</tr>
			<tr>
				<th>RC予約番号</th>
				<td><?php echo $this->Form->input('reservation_key' ,array('type' => 'textarea', 'class' => 'span4', 'div' => false, 'label' => false)); ?></td>
			</tr>
			<tr>
				<th>ステータス</th>
				<td><?php echo $this->Form->input('reservation_status_id', array('div' => false, 'label' => false, 'options' => $statusList, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>利用開始日</th>
				<td><?php echo $this->element('selectDatetime', $rentDtOptions); ?></td>
			</tr>
			<tr>
				<th>利用終了日</th>
				<td><?php echo $this->element('selectDatetime', $returnDtOptions); ?></td>
			</tr>
			<tr>
				<th>予約者氏名</th>
				<td>(姓)　<?php echo $this->Form->input('last_name', array('div' => false, 'label' => false)); ?>　(名)　<?php echo $this->Form->input('first_name', array('div' => false, 'label' => false)); ?></td>
			</tr>
			<tr>
				<th>利用会社</th>
				<td><?php echo $this->Form->input('client_id', array('div' => false, 'label' => false, 'options' => $clientList, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>利用空港</th>
				<td><?php echo $this->Form->input('iata_cd', array('div' => false, 'label' => false, 'options' => $airportList, 'empty' => '---')); ?></td>
			</tr>
			<tr>
				<th>申込日</th>
				<td><?php echo $this->element('selectDatetime', $bookingDtOptions); ?></td>
			</tr>
		</table>
		<br />
		<div style="float:left;padding:0px 20px 10px 0px;">
			<?php
				echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
				echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
			?>
		</div>
		<?php echo $this->Form->submit('csv出力', array('class' => 'btn btn-warning', 'name' => 'getCsv', 'value' => '1')); ?>
	<?php echo $this->Form->end(); ?>

	<table class="table table-bordered">
		<tr>
			<th><?php echo $this->Paginator->sort('cm_application_id', 'ツアー予約番号'); ?></th>
			<th><?php echo $this->Paginator->sort('reservation_key', 'RC予約番号'); ?></th>
			<th><?php echo $this->Paginator->sort('reservation_status_id', 'ステータス'); ?></th>
			<th><?php echo $this->Paginator->sort('rent_dt', '利用期間'); ?></th>
			<th><?php echo $this->Paginator->sort('car_type_id', '車両タイプ'); ?></th>
			<th><?php echo $this->Paginator->sort('last_name_enc', '予約者氏名'); ?></th>
			<th><?php echo $this->Paginator->sort('client_id', '利用会社'); ?></th>
			<th><?php echo $this->Paginator->sort('rent_office_id', '受取店舗'); ?></th>
			<th><?php echo $this->Paginator->sort('return_office_id', '返却店舗'); ?></th>
			<th><?php echo $this->Paginator->sort('iata_cd', '利用空港'); ?></th>
			<th><?php echo $this->Paginator->sort('price', '販売額'); ?></th>
			<th><?php echo $this->Paginator->sort('net_price', '仕入額'); ?></th>
			<th>利益</th>
			<th><?php echo $this->Paginator->sort('booking_dt', '申込日時'); ?></th>
		</tr>
	<?php
		foreach ($reservations as $r):
			$class = 'no-style';
			if ($r['TourReservation']['reservation_status_id'] == 0) {
				$class = 'attention';
			} elseif ($r['TourReservation']['reservation_status_id'] == 4) {
				$class = 'warning';
			} elseif ($r['TourReservation']['reservation_status_id'] == 5) {
				$class = 'error';
			}
	?>
		<tr class="<?php echo $class; ?>">
			<td><?php echo $this->Html->link($r['TourReservation']['cm_application_id'], array('action' => 'edit', $r['TourReservation']['cm_application_id'])); ?></td>
			<td><?php echo h($r['TourReservation']['reservation_key']); ?></td>
			<td><?php echo h($statusList[$r['TourReservation']['reservation_status_id']]); ?></td>
			<td><?php echo !empty($r['TourReservation']['rent_dt']) ? (h($r['TourReservation']['rent_dt']) . "<br>-<br>" . h($r['TourReservation']['return_dt'])) : ''; ?></td>
			<td><?php echo h($r['TourReservation']['car_type_name']); ?></td>
			<td><?php echo !empty($r['TourReservation']['last_name']) ? (h($r['TourReservation']['last_name']) . "<br>" . h($r['TourReservation']['first_name'])) : ''; ?></td>
			<td><?php echo h($r['TourReservation']['client_name']); ?></td>
			<td><?php echo h($r['TourReservation']['rent_office_name']); ?></td>
			<td><?php echo h($r['TourReservation']['return_office_name']); ?></td>
			<td><?php echo h($r['TourReservation']['iata_cd']); ?></td>
			<td><?php echo h(number_format($r['TourReservation']['price'])); ?>円</td>
			<td><?php echo (isset($r['TourReservation']['net_price']) && is_numeric($r['TourReservation']['net_price'])) ? h(number_format($r['TourReservation']['net_price'])).'円' : ''; ?></td>
			<td><?php echo (isset($r['TourReservation']['net_price']) && is_numeric($r['TourReservation']['net_price'])) ? h(number_format($r['TourReservation']['price'] - $r['TourReservation']['net_price'])).'円' : ''; ?></td>
			<td><?php echo h($r['TourReservation']['booking_dt']); ?></td>
		</tr>
	<?php endforeach; ?>
	</table>

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件'))); ?>

	<div class="pagination pagination-right">
		<ul>
			<li><?php echo $this->Paginator->prev('< 前へ', array(), null, array('class' => 'prev disabled')); ?></li>
			<li><?php echo $this->Paginator->numbers(); ?></li>
			<li><?php echo $this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')); ?></li>
		</ul>
	</div>
</div>
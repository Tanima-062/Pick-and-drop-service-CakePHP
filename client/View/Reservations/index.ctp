<style>
select {
	margin: 0 auto;
}
input[type="text"] {
	padding: 3px 0;
}
</style>


<div class="reservations index">
	<h3><?php echo __('顧客一覧'); ?></h3>
	<?php echo $this->Form->create('Reservation',array('type'=>'get','url'=>'.')); ?>
		<table class="table table-bordered table-condensed kensaku">
			<tr>
				<th class="alert-info">ステータス</th>
				<td><?php echo $this->Form->input('ReservationStatus',array('div'=>false,'label'=>false,'options'=>array('empty'=>'---',$reservationStatus)));?></td>
				<th class="alert-info">返信状況</th>
				<td><?php echo $this->Form->input('mail_status', array('options' => array('empty' => '---', $mailStatus),'label'=>false,'div'=>false));?></td>
			</tr>
			<tr>
				<th class="alert-info">予約番号</th>
				<td><?php echo $this->Form->input('ReservationKeyId',array('div'=>false,'label'=>false));?></td>
				<th class="alert-info">登録処理</th>
				<td><?php echo $this->Form->input('RegisteredFlg',array('div'=>false,'label'=>false,'options'=>$registeredFlgArray,'empty'=>'---'));?></td>
			</tr>
			<tr>
				<th class="alert-info">氏名カナ</th>
				<td>
				(姓) <?php echo $this->Form->input('ReservationLastName',array('div'=>false,'label'=>false, 'style' => 'width:100px;'));?>
				(名) <?php echo $this->Form->input('ReservationFirstName',array('div'=>false,'label'=>false, 'style' => 'width:100px;'));?>
				</td>

				<th class="alert-info">管理番号</th>
				<td><?php echo $this->Form->input('control_number',array('div'=>false,'label'=>false));?></td>
			</tr>
			<tr>
				<th class="alert-info" >電話番号</th>
				<td class="w30"><?php echo $this->Form->input('ReservationTel',array('div'=>false,'label'=>false));?></td>
				<th class="alert-info">商品グループ</th>
				<td class="w30"><?php echo $this->Form->input('ReservationCommodityGroupName',array('div'=>false,'label'=>false,'options'=>array('empty'=>'---',$commodityGroupName)));?></td>
			</tr>
			<tr>
				<th class="alert-info">営業所別</th>
				<td><?php echo $this->Form->input('ReservationOfficeName',array('div'=>false,'label'=>false,'options'=>array('empty'=>'---',$officeName)));?></td>
				<th class="alert-info">車両クラス</th>
				<td><?php echo $this->Form->input('ReservationCarClassName',array('div'=>false,'label'=>false,'options'=>array('empty'=>'---',$carClassName)));?></td>
			</tr>
			<?php if ($clientData['Client']['is_managed_package']) : ?>
			<tr>
				<th class="alert-info">販売方法</th>
				<td colspan="3">
					<?php echo $this->Form->input('SalesType', ['div' => false, 'label' => false, 'options' => ['empty' => '---', $salesType]]); ?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th class="alert-info">利用開始日</th>
				<td colspan="3">
				<div style="float:left;width:560px;margin-right:-100px"><?php echo $this->element('selectDatetime',$datetimeRentOptions);?></div>
				<div style="float:left"> 〜 </div>
				<div style="float:left;width:560px;margin-left:40px"><?php echo $this->element('selectDatetime',$datetimeRentOptions2);?></div>
				</td>
			</tr>
			<tr>
				<th class="alert-info">利用終了日</th>
				<td colspan="3">
				<div style="float:left;width:560px;margin-right:-100px"><?php echo $this->element('selectDatetime',$datetimeReturnOptions);?></div>
				<div style="float:left"> 〜 </div>
				<div style="float:left;width:560px;margin-left:40px"><?php echo $this->element('selectDatetime',$datetimeReturnOptions2);?></div>
				</td>
			</tr>
			<tr>
				<th class="alert-info">申込み日時</th>
				<td colspan="3">
				<div style="float:left;width:560px;margin-right:-100px"><?php echo $this->element('selectDatetime',$datetimeBookingOptions);?></div>
				<div style="float:left"> 〜 </div>
				<div style="float:left;width:560px;margin-left:40px"><?php echo $this->element('selectDatetime',$datetimeBookingOptions2);?></div>
				</div>
			</tr>
			<tr>
				<th class="alert-info">キャンセル日時</th>
				<td colspan="3">
				<div style="float:left;width:560px;margin-right:-100px"><?php echo $this->element('selectDatetime',$datetimeCancelOptions);?></div>
				<div style="float:left"> 〜 </div>
				<div style="float:left;width:560px;margin-left:40px"><?php echo $this->element('selectDatetime',$datetimeCancelOptions2);?></div>
				</td>
			</tr>
		<?php if ($acceptPrepay) { ?>
			<tr>
				<th class="alert-info">支払方法</th>
				<td><?php echo $this->Form->input('PaymentMethod', array('options' => array('empty' => '---', $paymentMethod),'label'=>false,'div'=>false));?></td>
			</tr>
		<?php } ?>
		</table>
		<br />

		<div class="btn-list">
			<div class="left">
			<?php
				echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
				echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
			?>
			</div>

			<div class="right">
			<?php
				 echo $this->Form->submit('csv出力',array('class'=>'btn btn-warning','name'=>'getCsv', 'div' => false));
			 ?>
			</div>

		</div>

	</div>

	<?php echo $this->Form->end(); ?>

	<br clear="all" />

	<?php echo $this->Paginator->counter(array('format' => __('ページ {:page} / {:pages}　：　総レコード/ {:count}件')));?>
	<table class="table table-bordered table-condensed">
		<tr  class="alert-info">
			<th width="8%;">予約番号</th>
		<?php if ($clientData['Client']['is_managed_package']) : ?>
			<th width="8%;">販売方法</th>
		<?php endif; ?>
			<th width="8%;">管理番号</th>
			<th width="5%;">ｽﾃｰﾀｽ</th>
			<th width="8%;">氏名カナ</th>
			<th width="10%"><?php echo $this->Paginator->sort('rent_datetime','利用時間'); ?></th>
			<th width="8%">受取店舗</th>
			<th width="8%">返却店舗</th>
			<th width="7%">到着便</th>
			<th width="8%">備考</th>
			<th width="8%;">返信状況</th>
		<?php if ($acceptPrepay) { ?>
			<th width="6%">支払方法</th>
		<?php } ?>
			<th width="8%"><?php echo $this->Paginator->sort('created','申込み<br/>日時',array('escape'=>false)); ?></th>
		</tr>
	<?php
		foreach ($reservations as $reservation) {
			$class = 'no-style';
			if($reservation['Reservation']['mail_status'] == 0) {
				$class = 'unsent_a_reply';
			}
			$rent_office_code = $clientOfficeAll[$reservation['Reservation']['rent_office_id']]['office_code'];
			$rent_office_name = !empty($clientOfficeAll[$reservation['Reservation']['rent_office_id']]) ? $clientOfficeAll[$reservation['Reservation']['rent_office_id']]['name'] : '(削除店舗)';
			$return_office_code = $clientOfficeAll[$reservation['Reservation']['return_office_id']]['office_code'];
			$return_office_name = !empty($clientOfficeAll[$reservation['Reservation']['return_office_id']]) ? $clientOfficeAll[$reservation['Reservation']['return_office_id']]['name'] : '(削除店舗)';
	?>
		<tr class="<?php echo $class;?>">
			<td><?php echo $this->Html->link($reservation['Reservation']['reservation_key'], array('action' => 'edit', $reservation['Reservation']['id'])); ?></td>
			<?php if ($clientData['Client']['is_managed_package']) : ?>
			<td><?php echo h($salesType[$reservation['Commodity']['sales_type']]); ?></td>
			<?php endif; ?>
			<td><?php echo h($reservation['Reservation']['control_number']); ?></td>
			<td><?php echo h($reservationStatus[$reservation['Reservation']['reservation_status_id']]); ?></td>
			<td><?php echo h($reservation['Reservation']['last_name']); ?><br><?php echo h($reservation['Reservation']['first_name']); ?></td>
			<td><?php echo h(date('Y-m-d H:i', strtotime($reservation['Reservation']['rent_datetime'])));  echo "<br>～<br>" . h(date('Y-m-d H:i', strtotime($reservation['Reservation']['return_datetime']))); ?></td>
			<td>
				<?php
					if(!empty($rent_office_code)) {
						echo h($rent_office_code);
						echo "<br>";
					}
				?>
				<?php echo h($rent_office_name); ?>
			</td>
			<td>
				<?php
					if(!empty($return_office_code)) {
						echo h($return_office_code);
						echo "<br>";
					}
				?>
				<?php echo h($return_office_name); ?>
			</td>
			<td><?php echo h($reservation['Reservation']['arrival_flight_number']); ?></td>
			<td>
				<?php
				if(!empty($reservationMail[$reservation['Reservation']['id']])) {
					echo h(mb_strimwidth($reservationMail[$reservation['Reservation']['id']], 0, 42, "..."));
				} else {
					echo "-";
				}
				?>
			</td>
			<td><?php echo h($mailStatus[$reservation['Reservation']['mail_status']]); ?></td>
		<?php if ($acceptPrepay) { ?>
			<td><?php echo $paymentMethod[$reservation['Commodity']['sales_type'] == Constant::SALES_TYPE_ARRANGED ? isset($reservation['Reservation']['payment_status']) : ($reservation['Reservation']['sales_price'] > 0)]; ?></td>
		<?php } ?>
			<td><?php echo $reservation['Reservation']['created'];?></td>
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
</div>
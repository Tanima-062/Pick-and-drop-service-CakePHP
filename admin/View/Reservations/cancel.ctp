<div class="reservations index">
	<h3><?php echo __('キャンセル一覧'); ?></h3>
	<?php echo $this->Form->create('Reservation',array('type'=>'get')); ?>
		<table class="table-bordered table-condensed">
			<tr>
				<th>会社名</th>
				<td>
				<?php echo $this->Form->input('client_id',array(
						'class'=>'span5','options'=>$clientList,'div'=>false,'label'=>false,'empty'=>'--')); ?>
				</td>
			</tr>

			<tr>
				<th>キャンセル理由</th>
				<td>
				<?php echo $this->Form->input('cancel_reason_id',array(
						'class'=>'span7','options'=>$cancelReasonList,'div'=>false,'label'=>false,'empty'=>'--')); ?>
				</td>
			</tr>

			<tr>
				<th>申込み日時</th>
				<td><?php echo $this->element('selectDatetime',$datetimeBookingOptions);?></td>
			</tr>

			<tr>
				<th>キャンセル日時</th>
				<td><?php echo $this->element('selectDatetime',$datetimeCancelOptions);?></td>
			</tr>
		</table>
		<br />
		<?php
			echo $this->Form->submit('検索する', array('class' => 'btn btn-primary', 'div' => false));
			echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>

	<?php echo $this->Form->end(); ?>

	<?php if (!empty($this->data['Reservation']['ReservationCreatedDate']['year'])) { ?>
	<table class="table table-striped table-bordered table-condensed" style="width:40%;clear:both;">
		<tr>
			<th>キャンセル理由</th>
			<th>件数</th>
		</tr>
		<tr>
			<td>キャンセル理由未取得</td>
			<td><?php echo $count['cancel0_count']; ?>件</td>
		</tr>
		<tr>
			<td>お客様都合によるキャンセル</td>
			<td><?php echo $count['cancel1_count']; ?>件</td>
		</tr>
		<tr>
			<td>交通手段の欠航・運休等によるキャンセル</td>
			<td><?php echo $count['cancel2_count']; ?>件</td>
		</tr>
		<tr>
			<td>予約取り直しによるキャンセル</td>
			<td><?php echo $count['cancel3_count']; ?>件</td>
		</tr>
		<tr>
			<td>その他</td>
			<td><?php echo $count['cancel4_count']; ?>件</td>
		</tr>
		<tr>
			<td>クライアント管理ツールでのキャンセル</td>
			<td><?php echo $count['cancel5_count']; ?>件</td>
		</tr>
		<tr>
			<td>【社内管理者】クライアント管理ツールでのキャンセル</td>
			<td><?php echo $count['cancel6_count']; ?>件</td>
		</tr>
		<tr>
			<td>【クライアント】クライアント管理ツールでのキャンセル</td>
			<td><?php echo $count['cancel7_count']; ?>件</td>
		</tr>
		<tr>
			<th colspan="2">合計</th>
		</tr>
		<tr>
			<td>キャンセル合計件数</td>
			<td><?php echo $count['total_count']; ?>件</td>
		</tr>
		<tr>
			<td>キャンセル合計料金</td>
			<td>&yen;<?php echo number_format($count['total_price']); ?></td>
		</tr>
	</table>
	<?php } ?>
	<p>PC:<?php echo $count['pc_count']; ?>件　スマホ:<?php echo $count['sp_count']; ?>件</p>

	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>予約番号</th>
			<th>会社名</th>
			<th>車両クラス</th>
			<th>料金</th>
			<th>利用時間</th>
			<th>申込み日時</th>
			<th>キャンセル日時</th>
			<th>キャンセル理由</th>
			<th>キャンセル理由詳細</th>
		</tr>
	<?php
	foreach ($reservations as $reservation): ?>
		<tr>
			<td><a href="/rentacar/client/Reservations/edit/<?=$reservation['Reservation']['id']?>?cid=<?=$reservation['Reservation']['client_id']?>" target="_blank"><?=$reservation['Reservation']['reservation_key']?></a></td>
			<td><?php echo h($reservation['Client']['name']);?></td>
			<td><?php echo h($reservation['CarClasses']['name']); ?>&nbsp;</td>
			<td><?php echo h(number_format($reservation['Reservation']['amount'])); ?>円&nbsp;</td>
			<td><?php echo h($reservation['Reservation']['rent_datetime']);  echo "<br>-<br>" . h($reservation['Reservation']['return_datetime']); ?>&nbsp;</td>
			<td><?php echo $reservation['Reservation']['created'];?></td>
			<td><?php echo $reservation['Reservation']['cancel_datetime'];?></td>
			<td><?php
			if (isset($cancelReasonList[$reservation['Reservation']['cancel_reason_id']])) {
				echo $cancelReasonList[$reservation['Reservation']['cancel_reason_id']];
			}?></td>
			<td><?php echo mb_strimwidth($reservation['Reservation']['cancel_remark'],0,40,'...');?></td>
		</tr>
	<?php endforeach; ?>
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

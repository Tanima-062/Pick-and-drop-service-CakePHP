<h3>精算後調整データ</h3>
<h4>　精算完了：<?=substr($finishedMonth, 0, 4)?>年<?=substr($finishedMonth, 4)?>月計上分まで（<?=$finishedAt?>）</h4>
<br>
<?php echo $this->Form->create('Reservation'); ?>
変更月
<span>
	<?php echo $this->Form->year('date', 2016, date('Y'), array('empty' => false)); ?>年
</span>
<span>
	<?php echo $this->Form->month('date', array('empty' => false, 'monthNames' => false, 'value' => $month)); ?>月
</span>
<div class="control-group">
	<?php echo $this->Form->button('検索',array('class'=>'btn btn-primary','id'=>'searchButton')); ?>
</div>
<div class="control-group">
	<?php echo $this->Form->submit('csv出力',array('class'=>'btn btn-warning','name'=>'getCsv','value'=>'1'))?>
</div>
<?php echo $this->Form->hidden('settlement', array('value' => '1')); ?>
<?php echo $this->Form->end(); ?>
<style>
	tr:nth-child(even) {
		background-color:#F9F9F9;
	}
	td.info {
		background-color: #E0FFFF;
	}
	td.error {
		background-color: #FFE4E1
	}
	select {
		width: 100px;
	}
</style>

<p>
	<?php echo empty($data) ? '精算後調整データはありません' : ''; ?>
</p>
<table class="table table-bordered" style="width: 30%">
	<tr>
		<th>予約番号</th>
		<th>変更日時</th>
	</tr>
	<tbody>
	<?php foreach ($data as $key => $value) { ?>
	<tr>
		<td>
			<a href="/rentacar/client/Reservations/edit/<?=$value['id']?>?cid=<?=$value['client_id']?>" target="_blank"><?=$key?></a>
		</td>
		<td>
			<?=$value['updated_at']?>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<script>
	$(function() {
		$('#searchButton').on("click", function(e) {
			var year = $("#ReservationDateYear").val();
			var month = $("#ReservationDateMonth").val();

			var now = new Date();
			var nowYear = now.getFullYear().toString();
			var nowMonth = ("00" + (now.getMonth() + 1)).slice(-2);
			if (year + month > nowYear + nowMonth) {
				alert(year + '年' + month + '月は未来の年月です。');
				e.preventDefault();
			}
		});
	});
</script>
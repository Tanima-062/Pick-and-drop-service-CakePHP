<script type="text/JavaScript">
<!--
$(function(){
	$('#diff-post').click(function(){
		var oReq = new XMLHttpRequest();
		var hrefStr = location.host;
		oReq.open("POST", "//"+hrefStr+"/rentacar/client/diff-update/");
		oReq.onreadystatechange = function(){
			// 本番用
			if (oReq.readyState === 4 && oReq.status === 200){
				location.reload();
			}
		};
		oReq.send();
	});
});
-->
</script>

<h3>月別売上情報</h3>

<h4>検索条件</h4>
<?php echo $this->Form->create('Statistic'); ?>
<table class="table table-bordered table-condensed">
	<tr>
		<th>年月</th>
		<td>
			<?php echo $this->Form->select('year', $yearArray); ?>
			年
		</td>
		<?php if (isset($error)) { ?>
		<td style="color: red;">
			<?php echo $error; ?>
		</td>
		<?php } ?>
		<th>商品名</th>
		<td><?php echo $this->Form->select('commodity_id', $commodityLists); ?></td>
	</tr>
	<tr>
		<th>車両クラス</th>
		<td><?php echo $this->Form->select('car_class_id', $carClassLists); ?></td>
		<th>営業所名</th>
		<td><?php echo $this->Form->select('office_id', $officeLists); ?></td>
	</tr>
</table>
<?php echo $this->Form->submit('検索', array('name' => 'search', 'class' => 'btn btn-primary')); ?>
<?php echo $this->Form->end(); ?>

<h4>統計情報</h4>
<div style="float:right;margin:0 0 10px;">
	<?php echo $this->Form->submit('最新の内容に更新', array(
			'id'=>'diff-post',
			'name' => 'search',
			'class' => 'btn btn-success',
			'style'=>''
	)); ?>
</div>
<?php if (isset($statistics) && !empty($statistics)) { ?>
<table id="statistic" class="table table-striped table-bordered table-condensed" style="font-size: 12px;clear:both;">
	<tr>
		<th>年月</th>
		<th>売上</th>
		<th>広告手数料(<?php echo $client['Client']['commission_rate']; ?>%)</th>
		<th>成約数</th>
		<th>キャンセル数</th>
		<th>キャンセル料金</th>
		<th>顧客単価</th>
		<th>成約者リスト（通常）</th>
	</tr>
	<tr>
		<td colspan="9" style="height: 1px; background-color: #111436; padding: 0;"></td>
	</tr>
	<?php $total = array('price' => 0, 'reservation_count' => 0,'cancel_count' => 0,'cancel_price'=>0,'commission'=>0); ?>
	<?php
	foreach ($statistics as $key => $statistic) {

		if(empty($statistic[0]['price'])) $statistic[0]['price'] = 0;
		if(empty($statistic[0]['reservation_count'])) $statistic[0]['reservation_count'] = 0;
		if(empty($statistic[0]['cancel_count'])) $statistic[0]['cancel_count'] = 0;
		if(empty($statistic[0]['cancel_price'])) $statistic[0]['cancel_price'] = 0;

		 $total['price'] += $statistic[0]['price'];
		 $total['reservation_count'] += $statistic[0]['reservation_count'];
		 $total['cancel_price'] += $statistic[0]['cancel_price'];
		 $total['cancel_count'] += $statistic[0]['cancel_count'];
		 $total['commission'] += floor($statistic[0]['price'] * ($client['Client']['commission_rate'] /100));

	?>
	<tr>
		<td>
			<?php
			echo date('Y年m月', strtotime($statistic['reservations']['statistic_date']));
			?>
		</td>
		<td>&yen;<?php echo number_format($statistic[0]['price']); ?></td>
		<td>&yen;<?php echo  number_format(floor($statistic[0]['price'] * ($client['Client']['commission_rate'] /100)));?></td>
		<td><?php echo number_format($statistic[0]['reservation_count']); ?></td>
		<td><?php echo number_format($statistic[0]['cancel_count']); ?></td>
		<td>&yen;<?php echo number_format($statistic[0]['cancel_price']); ?></td>
		<td>
			&yen;
			<?php
			if(!empty($statistic[0]['price'])  && !empty($statistic[0]['reservation_count'])) {
				echo number_format(floor($statistic[0]['price'] / $statistic[0]['reservation_count']));
			} else {
				echo 0;
			}
			?>
		</td>
		<td>
		<?php if($statistic[0]['reservation_count'] != 0) {?>
			<?php echo $this->Html->link('CSV出力','/statistics/sales?csv=1&month=' . $key . '&' . $url,array('class'=>'btn btn-warning'));?>
			<?php }?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="9" style="height: 1px; background-color: #111436; padding: 0;"></td>
	</tr>
	<tr>
		<td>計</td>
		<td>&yen;<?php echo number_format($total['price']); ?></td>
		<td>&yen;<?php echo number_format($total['commission']);?></td>
		<td><?php echo number_format($total['reservation_count']); ?></td>
		<td><?php echo $total['cancel_count']; ?></td>
		<td>&yen;<?php echo  number_format($total['cancel_price']); ?></td>
		<td>&yen;
			<?php
			if(empty($total['price']) || empty($total['reservation_count'])) {
				echo 0;
			} else {
				echo number_format(floor($total['price'] / $total['reservation_count']));
			}
		?>
		</td>
		<td></td>
	</tr>
</table>
<?php } ?>

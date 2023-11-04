<script>

$(function(){
	$('#accordion_ul table').hide();
		$('#accordion_ul h4').click(function(e){
		$(this).toggleClass("active").next("table").toggle();
	});

	$('#TopsPrefectureId').on('change',function() {
		changeStockGroup();
	});

	function changeStockGroup() {
		var prefecture_id = $('#TopsPrefectureId').val();
		if(prefecture_id==''){
			$('#TopsStockGroupId').html('');
		} else {
			$.ajax({
				type:"GET",
				url:"/rentacar/client/StockGroups/getStockGroupByPrefecture/" + prefecture_id + '/',
				success:function(data){
					$('#TopsStockGroupId').html(data);
				}
			});
		}
	}
});

</script>
<div class="error"><?php echo $this->Session->flash('auth'); ?></div>
<!-- サイト全体の直近2週間のご予約状況ここから -->
<h3>
	サイト全体の直近2週間のご予約状況 (
	<?php echo date('Y/m/d',strtotime('-2week'));?>
	～
	<?php echo date('Y/m/d');?>
	）
</h3>
<?php if ($isManagedPackage) { ?>
	<h4>【単体予約】</h4>
	<?php echo $this->element('reservationTable', array('reserveArray' => $reserveArrays[Constant::SALES_TYPE_ARRANGED])); ?>
	<h4>【包括予約】</h4>
	<?php echo $this->element('reservationTable', array('reserveArray' => $reserveArrays[Constant::SALES_TYPE_AGENT_ORGANIZED])); ?>
<?php } else { ?>
	<?php echo $this->element('reservationTable', array('reserveArray' => $reserveArrays[Constant::SALES_TYPE_ARRANGED])); ?>
<?php } ?>

<?php if(!empty($messages)): ?>
<div class="news">
	<h3>お知らせ</h3>
	<table class="table table-bordered">
	<tr class="success">
		<th style="width: 15%">日時</th>
		<th>内容</th>
	</tr>
	
	<?php foreach($messages as $message): ?>
	<tr>
		<td>
		<?php echo h(date("Y年m月d日 H:i",strtotime($message['Message']['from_time']))); ?>
		</td>
		<td>
		<?php 
		echo $this->Html->link($message['Message']['title'],'/news/show/'.$message['Message']['id']);
		?>
		</td>
	<tr>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>

<!-- 直近2週間のご予約状況ここまで -->
<h3>在庫状況</h3>
<?php
$this->Form->data = $this->data;
echo $this->Form->create('Tops',array('type'=>'get','action'=>'index','controller'=>'Tops')); 
?>
<table class="table table-bordered">
	<tr>
		<th>地域</th>
		<th>車両タイプ</th>
		<th>車両クラス</th>
		<th></th>
	</tr>
	<tr>
		<td>
<?php
echo $this->Form->select('prefecture_id', $prefectureList,array('class'=>'span3'));
echo $this->Form->select('stock_group_id', $stockGroups);
?>
		</td>
		<td>
<?php
echo $this->Form->select('car_type_id', $carTypeList);
?>
		</td>
		<td>
<?php
echo $this->Form->select('car_class_id', $carClassList);
?>
		</td>
		<td>
<?php
echo $this->Form->submit('検索', array('name' => 'search', 'class' => 'btn btn-primary'));
?>
		</td>
	</tr>
</table>
<?php 
echo $this->Form->end();
?>
<?php if(!empty($outOfStockArray)): ?>
<h4 style="color: red;">在庫切れ ※以下の車両クラスで在庫切れが発生しています。</h4>
<table class="table table-bordered">
	<tr class="success">
		<th>地域</th>
		<th>車両タイプ</th>
		<th>車両クラス</th>
		<th>在庫切れ日</th>
	</tr>
	<?php foreach($outOfStockArray as $outOfStockData): ?>
		<tr>
			<td>
				<?php 
				$prefectureId = $outOfStockData[0]['StockGroup']['prefecture_id']; 
				?>
				<?php echo $prefectureList[$prefectureId]; ?>・<?php echo $outOfStockData[0]['StockGroup']['name']; ?>
			</td>
			<td>
				<?php 
				$carTypeId = $outOfStockData[0]['CarClass']['car_type_id'];
				echo $carTypeList[$carTypeId];
				?>
			</td>
			<td>
				<?php 
				$carClassId = $outOfStockData[0]['CarClassReservation']['car_class_id'];
				echo $carClassList[$carClassId];
				?>
			</td>
			<td>
				<?php foreach($outOfStockData as $i => $day){
					if($i <> 0) {
						echo '、';
					}
					$date = strtotime($day['CarClassReservation']['stock_date']);
					echo $this->Html->link(date('n/j',$date),'/car_class_stocks/?year[year]='.date('Y',$date) . '&month[month]='.date('m',$date) . '&car_class_id=' . $day['CarClassReservation']['car_class_id'] . '&prefecture_id=' .$prefectureId. '&stock_group_id=' . $day['StockGroup']['id'].'&car_type_id='.$day['CarClass']['car_type_id'],array('target'=>'_blank','style'=>'display:inline-block;'));
				}
				?>
			</td>
		</tr>	
	<?php endforeach; ?>
</table>
<?php else: ?>
<p>在庫切れの情報はありません</p>
<?php endif; ?>

<br />
<br />

<h3>更新履歴</h3>

<table class="table table-bordered">
	<tr class="success">

		<th style="width: 15%">日時</th>
		<th style="width: 10%">項目</th>
		<th style="width: 55%">内容</th>
		<th style="width: 20%">担当</th>
	</tr>
	<?php
	if(is_array($updateTables)) {
		foreach ($updateTables as $updateTable) {
	?>
	<tr>
		<td>
			<?php echo $updateTable['UpdatedTable']['created'];?>
		</td>
		<td>
			<?php echo $updateTable['UpdatedTable']['category'];?>
		</td>
		<td>
			<?php echo $updateTable['UpdatedTable']['content'];?>
		</td>
		<td>
			<?php
			$staffId = $updateTable['UpdatedTable']['staff_id'];
			if($staffId == 1) {
				$staff = $staffList[$staffId];
			} else {
				$staff = $staffList[$staffId] . '様';
			}
			?>

			<?php echo $staff;?>
		</td>

	</tr>
	<?php
		}
	}
	?>
</table>

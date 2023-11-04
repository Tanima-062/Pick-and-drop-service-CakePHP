<?php
$flashError = $this->Session->flash();
?>
<?php
if(!empty($flashError)) {
?>
<div id=’flashMessage’ class=’message’>
	<?php echo $flashError;?>
</div>
<?php
}
?>

<h3>車両クラス別在庫管理</h3>

<h4>検索条件</h4>
<?php
$this->Form->data = $this->data;
echo $this->Form->create('CarClassStock',array('type'=>'get','action'=>'index','controller'=>'CarClassStocks')); ?>
<table class="table table-bordered table-condensed">
	<tr>
		<th>年月</th>
		<td colspan="3">
		<?php echo $this->Form->select('year', $yearArray,array('multiple' => false)); ?>年&emsp;<?php echo $this->Form->select('month', $monthArray,array('multiple' => false)); ?>月
		<?php if (isset($error)) { ?>
			<span style="color: red;"><?php echo $error; ?></span>
		<?php } ?>
		</td>
	</tr>
	<tr>
		<th>地域<span class="red"> （必須）</span></th>
		<td>
			<?php
			echo $this->Form->select('prefecture_id', $prefectureList,array('class'=>'span3'));
			echo $this->Form->select('stock_group_id', $stockGroups);
			?>
		</td>
		<th>商品グループ</th>
		<td><?php echo $this->Form->select('commodity_group_id', $commodityGroupLists); ?></td>
	</tr>
	<tr>
		<th>車両クラス</th>
		<td><?php echo $this->Form->select('car_class_id', $carClassLists, array('required' => false)); ?></td>
		<th>車両タイプ</th>
		<td><?php echo $this->Form->select('car_type_id', $carTypeLists); ?></td>
	</tr>
</table>

<div style="width:100%;height:38px">
	<div class="left">
		<?php
			echo $this->Form->submit('検索', array('name' => 'search', 'class' => 'btn btn-primary', 'div' => false));
			echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset'));
		?>
	</div>
	<div class="right">
		<?php echo $this->Form->submit('csv出力',array('name'=>'get_csv', 'class'=>'btn btn-warning', 'div' => false)); ?>
	</div>
</div>

<?php echo $this->Form->end(); ?>

<h4>申込状況</h4>
<?php if (!isset($error)) { ?>

<div style="float: right;">
	<?php echo $this->Form->create('CarClassStock', array('action'=>'takingOverData','controller' => 'CarClassStocks')); ?>
	<?php echo $this->Form->hidden('url',array('value'=>$_SERVER['REQUEST_URI'])); ?>
	<?php echo $this->Form->hidden('year');?>
	<?php echo $this->Form->hidden('month');?>
	<?php echo $this->Form->hidden('stock_group_id');?>
	<?php echo $this->Form->hidden('commodity_group_id');?>
	<?php echo $this->Form->hidden('car_class_id');?>
	<?php echo $this->Form->hidden('car_type_id');?>
	<?php echo $this->Form->hidden('hikitugi');?>
	<?php echo $this->Form->hidden('oldData',array('value'=>json_encode($result))); ?>
	<?php echo $this->Form->submit('前月の在庫設定を引き継ぐ', array('name' => 'save', 'class' => 'btn btn-warning taking_over_data', 'div' => false,'name'=>'takingOverData')); ?>
	<?php echo $this->Form->end(); ?>
</div>

<?php echo $this->Form->create('CarClassStock', array('action'=>'index','controller'=>'CarClassStocks','name' => 'editForm')); ?>
<?php echo $this->Form->hidden('url',array('value'=>$_SERVER['REQUEST_URI'])); ?>
<p>
<div style="float: left;border-style: solid ; border-width: 1px;padding:8px;">
	対象期間在庫設定&nbsp;	<?php echo $this->Form->input('min_date', array ('id' => 'jquery-ui-datepicker-from', 'div' => false, 'label' => false)); ?>
			　～　<?php echo $this->Form->input('max_date', array ('id' => 'jquery-ui-datepicker-to', 'div' => false, 'label' => false)); ?> ＞＞
	<?php echo $this->Form->submit('枠数設定', array('name' => 'save', 'class' => 'btn btn-warning', 'div' => false)); ?>
	<?php echo $this->Form->submit('残数設定', array('name' => 'remaining_amount', 'class' => 'btn btn-success', 'div' => false)); ?>
</div>
</p>
<br />
<br clear="all" />
<br clear="all" />

<div style="float:left;">
	<?php echo $this->Form->submit('枠数更新', array('name' => 'save', 'class' => 'btn btn-warning','div'=>false)); ?>
	<?php echo $this->Form->submit('残数更新', array('name' => 'remaining_amount', 'class' => 'btn btn-success','div'=>false)); ?>
</div>
<br clear="all" />
<br clear="all" />

<table id="stock"
	class="table table-striped table-bordered table-condensed"
	style="font-size: 12px;">
	<tr>
		<td style="font-size: 15px;" colspan=<?php echo $lastDay+5; ?>>
		<span style="float: right; margin-right: 10px;">
		見込み件数 :<?php echo number_format($expected[0]['count']); ?>件
		見込み売上 :￥<?php echo number_format($expected[0]['amount']); ?>
		</span>
		</td>
	</tr>
	<tr>
		<td style="font-size: 15px;" colspan=<?php echo $lastDay+5; ?>><?php echo $this->data['CarClassStock']['year']; ?>年
			<?php echo $this->data['CarClassStock']['month']; ?>月</td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: right"><label
			style="display: inline;">全ﾁｪｯｸ&nbsp<input id="CarClassStockAll"
				type="checkbox"
				value="all" name="data[CarClassStock][all]">
		</label>&nbsp</td>
		<td></td>
		<?php $tmpW = $w; ?>
		<?php foreach ($dayArray as $val) { ?>
			<?php if ($lastDay < $val) { ?>
				<?php break; ?>
			<?php } else if (strtotime($this->data['searchDate'].'-'.$val) < strtotime(date('Y-m-d'))) { ?>

		<td style="background-color:<?php echo $pastColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>">
			<input
				id="CarClassStockStock"
				class="stock-check"
				type="checkbox"
				value="1"
				name="Check[<?php echo $val; ?>]">
		</td>

			<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>

		<td class="date-cell" data-date="<?php echo $val; ?>">
			<input
				id="CarClassStockStock"
				class="stock-check"
				type="checkbox"
				value="1"
				name="Check[<?php echo $val; ?>]">
		</td>

			<?php } else if ($tmpW == 0) { ?>

		<td style="background-color:<?php echo $sundayColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>">
			<input
				id="CarClassStockStock"
				class="stock-check"
				type="checkbox"
				value="1"
				name="Check[<?php echo $val; ?>]">
		</td>

			<?php } else if ($tmpW == 6) { ?>

		<td style="background-color:<?php echo $saturdayColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>">
			<input
				id="CarClassStockStock"
				class="stock-check"
				type="checkbox"
				value="1"
				name="Check[<?php echo $val; ?>]">
		</td>

			<?php }

				if (++$tmpW > 6) {
					$tmpW = 0;
				}
			} ?>
		<td>&nbsp</td>
	</tr>
	<tr>
		<td rowspan=2>地域</td>
		<td rowspan=2>車両クラス</td>
		<td rowspan=2>設定数値</td>
		<td rowspan=2>&nbsp</td>
		<?php $tmpW = $w; ?>
		<?php foreach ($dayArray as $val) { ?>
			<?php if ($lastDay < $val) { ?>
			<?php break; ?>
			<?php } else if (strtotime($this->data['searchDate'].'-'.$val) < strtotime(date('Y-m-d'))) { ?>

		<td style="background-color:<?php echo $pastColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>"><?php echo number_format($val); ?></td>

			<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>

		<td class="date-cell" data-date="<?php echo $val; ?>"><?php echo number_format($val); ?></td>

			<?php } else if ($tmpW == 0) { ?>

		<td style="background-color:<?php echo $sundayColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>"><?php echo number_format($val); ?></td>

			<?php } else if ($tmpW == 6) { ?>

		<td style="background-color:<?php echo $saturdayColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>"><?php echo number_format($val); ?></td>

			<?php } ?>
			<?php if (++$tmpW > 6) {
				$tmpW = 0;
			} ?>
		<?php } ?>
		<td rowspan=2>合計</td>
	</tr>
	<tr>
		<?php $tmpW = $w; ?>
		<?php foreach ($dayArray as $val) { ?>
			<?php if ($lastDay < $val) { ?>
				<?php break; ?>
			<?php } else if (strtotime($this->data['searchDate'].'-'.$val) < strtotime(date('Y-m-d'))) { ?>

			<td style="background-color:<?php echo $pastColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>"><?php echo $wday[$tmpW]; ?>
			</td>

			<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>

			<td class="date-cell" data-date="<?php echo $val; ?>"><?php echo $wday[$tmpW]; ?></td>

			<?php } else if ($tmpW == 0) { ?>

			<td style="background-color:<?php echo $sundayColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>"><?php echo $wday[$tmpW]; ?>
			</td>

			<?php } else if ($tmpW == 6) { ?>

			<td style="background-color:<?php echo $saturdayColor; ?>;" class="date-cell" data-date="<?php echo $val; ?>"><?php echo $wday[$tmpW]; ?>
			</td>

			<?php } ?>

			<?php if (++$tmpW > 6) {
				$tmpW = 0;
			} ?>
		<?php } ?>
	</tr>
	<?php

	foreach ($result as $key => $val) {

		if (!empty($val['CarClassStockGroup'][0]) &&
			array_search($val['StockGroup']['id'],$val['CarClassStockGroup']) === false) {
				continue;
			}
	?>
	<tr>
		<td style="height: 1px; background-color: #111436;"
			colspan=<?php echo $lastDay+5; ?>></td>
	</tr>
	<tr>
		<td rowspan=3><?php echo ''.$val['StockGroup']['name']; ?>
		</td>
		<td rowspan=3 style="text-align:right;">
		<?php echo $val['CarClass']['name']; ?>
		<?php echo $this->Form->submit('満車',array('class'=>'btn btn-magenta full_car','name'=>$val['StockGroup']['id'].'-'.$val['CarClass']['id'],'onClick'=>'return false;','div'=>false));?>
		</td>
		<td rowspan=3>
		<?php
		echo $this->Form->input('stock_count',
				array('name' => 'StockCount['.$val['StockGroup']['id'].'-'.$val['CarClass']['id'].']',
						'style' => 'width:50px;', 'label' => false, 'min' => 0, 'required' => false));
		?>


		</td>
		<td>枠</td>
		<?php $tmpW = $w; ?>
		<?php for ($i = 0, $total = 0; $i < $lastDay; $i++) { ?>
			<?php $index = sprintf("%02d", $i+1); ?>
			<?php if (!empty($val['CarClassStock'][$index]['stock_count'])) { ?>
				<?php if (strtotime($this->data['searchDate'].'-'.$index) < strtotime(date('Y-m-d'))) { ?>

		<td style="background-color:<?php echo $pastColor; ?>;" class="<?php echo $val['CarClassStock'][$index]['id'];?> date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassStock'][$index]['stock_count']; ?></td>

				<?php } else if ($isRennaviApiTarget && !($isJnet) && isset($val['CarClassStock'][$index]['suspension']) && $val['CarClassStock'][$index]['suspension'] == 1) { ?>

		<td style="background-color:<?php echo $suspensionColor; ?>;" class="<?php echo $val['CarClassStock'][$index]['id'];?> date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassStock'][$index]['stock_count']; ?></td>

				<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>

		<td class="<?php echo $val['CarClassStock'][$index]['id'];?> date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassStock'][$index]['stock_count']; ?></td>

				<?php } else if ($tmpW == 0) { ?>

		<td style="background-color:<?php echo $sundayColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassStock'][$index]['stock_count']; ?></td>

				<?php } else if ($tmpW == 6) { ?>

		<td style="background-color:<?php echo $saturdayColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassStock'][$index]['stock_count']; ?></td>

				<?php } ?>
				<?php $total += $val['CarClassStock'][$index]['stock_count']; ?>
				<?php $diff[$i] = $val['CarClassStock'][$index]['stock_count']; ?>
			<?php } else { ?>

				<?php
				if(isset($val['CarClassStock'][$index]['stock_count'])){
					$z = '止';
				} else {
					$z = '0';
				}
				?>

				<?php if (strtotime($this->data['searchDate'].'-'.$index) < strtotime(date('Y-m-d'))) { ?>

				<?php
				$color = $pastColor;
				if($z != '0'){
					$color = '#cc0000;color:#fff';
				}
				?>
		<td style="background-color:<?php echo $color; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $z ?></td>

				<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>
					<?php
					$color = '#fff';
					if($z != '0'){
						$color = '#cc0000;color:#fff';
					}
					?>
		<td style="background-color:<?php echo $color; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $z ?></td>

				<?php } else if ($tmpW == 0) { ?>
					<?php
					$color = $sundayColor;
					if($z != '0'){
						$color = '#cc0000;color:#fff';
					}
					?>
		<td style="background-color:<?php echo $color; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $z ?></td>

				<?php } else if ($tmpW == 6) { ?>
					<?php
					$color = $saturdayColor;
					if($z != '0'){
						$color = '#cc0000;color:#fff';
					}
					?>
		<td style="background-color:<?php echo $color; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $z ?></td>

				<?php } ?>
				<?php $diff[$i] = 0; ?>
			<?php } ?>

			<?php if (++$tmpW > 6) {
				$tmpW = 0;
			} ?>
		<?php } ?>
		<td><?php echo $total; ?></td>
	</tr>
	<tr>
		<td>予約</td>
		<?php $tmpW = $w; ?>
		<?php for ($i = 0, $total = 0; $i < $lastDay; $i++) { ?>
		<?php $index = sprintf("%02d", $i+1); ?>
		<?php if (!empty($val['CarClassReservation'][$index])) { ?>

		<?php if (strtotime($this->data['searchDate'].'-'.$index) < strtotime(date('Y-m-d'))) { ?>

		<td style="background-color:<?php echo $pastColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassReservation'][$index]; ?></td>

		<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>

		<td class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassReservation'][$index]; ?></td>

		<?php } else if ($tmpW == 0) { ?>

		<td style="background-color:<?php echo $sundayColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassReservation'][$index]; ?></td>

		<?php } else if ($tmpW == 6) { ?>

		<td style="background-color:<?php echo $saturdayColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $val['CarClassReservation'][$index]; ?></td>

		<?php } ?>
			<?php $total += $val['CarClassReservation'][$index]; ?>
			<?php $diff[$i] -= $val['CarClassReservation'][$index]; ?>

		<?php } else { ?>

			<?php if (strtotime($this->data['searchDate'].'-'.$index) < strtotime(date('Y-m-d'))) { ?>

		<td style="background-color:<?php echo $pastColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d',$i +1); ?>">0</td>

			<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>

		<td class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>">0</td>

			<?php } else if ($tmpW == 0) { ?>

		<td style="background-color:<?php echo $sundayColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>">0</td>

			<?php } else if ($tmpW == 6) { ?>

		<td style="background-color:<?php echo $saturdayColor; ?>;" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>">0</td>

			<?php } ?>
			<?php $diff[$i] -= 0; ?>
		<?php } ?>

		<?php if (++$tmpW > 6) {
			$tmpW = 0;
} ?>
		<?php } ?>
		<td><?php echo $total; ?></td>
	</tr>
	<tr>
		<td>残</td>
		<?php $tmpW = $w; ?>
		<?php for ($i = 0, $total = 0; $i < $lastDay; $i++) { ?>
		<?php

		if ($diff[$i] <= 0) {
			$errorColor = ' color:#ff0000';
			$diff[$i] = 0;
		} else {
			$errorColor = '';
		} ?>
		<?php if (strtotime($this->data['searchDate'].'-'.($i+1)) < strtotime(date('Y-m-d'))) { ?>

		<td style="background-color:<?php echo $pastColor; ?>;<?php echo $errorColor ?>" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $diff[$i]; ?></td>

		<?php } else if ($tmpW != 0 && $tmpW != 6) { ?>

		<td style="<?php echo $errorColor ?>" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $diff[$i]; ?></td>

		<?php } else if ($tmpW == 0) { ?>

		<td style="background-color:<?php echo $sundayColor; ?>;<?php echo $errorColor ?>" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $diff[$i]; ?></td>

		<?php } else if ($tmpW == 6) { ?>

		<td style="background-color:<?php echo $saturdayColor; ?>;<?php echo $errorColor ?>" class="date-cell" data-date="<?php echo sprintf('%02d', $i +1); ?>"><?php echo $diff[$i]; ?></td>

		<?php } ?>
		<?php if (++$tmpW > 6) {
			$tmpW = 0;
		} ?>
		<?php $total += $diff[$i]; ?>
		<?php } ?>
		<td><?php echo $total; ?></td>
	</tr>
	<?php } ?>
</table>
<p>
<div style="float:left;">
	<?php echo $this->Form->submit('残数更新', array('name' => 'remaining_amount', 'class' => 'btn btn-success','div'=>false)); ?>
</div>

<?php echo $this->Form->input('year', array('value' => $this->data['CarClassStock']['year'], 'type' => 'hidden')); ?>
<?php echo $this->Form->input('month', array('value' => $this->data['CarClassStock']['month'], 'type' => 'hidden')); ?>

<?php echo $this->Form->hidden('oldData',array('value'=>json_encode($result))); ?>

<?php if (!empty($this->data['CarClassStock']['car_class_id'])) { ?>
<?php echo $this->Form->input('car_class_id', array('value' => $this->data['CarClassStock']['car_class_id'], 'type' => 'hidden')); ?>
<?php } ?>

<?php if (!empty($this->data['CarClassStock']['stock_group_id'])) { ?>
<?php echo $this->Form->input('stock_group_id', array('value' => $this->data['CarClassStock']['stock_group_id'], 'type' => 'hidden')); ?>
<?php } ?>

<?php if (!empty($this->data['CarClassStock']['commodity_group_id'])) { ?>
<?php echo $this->Form->input('commodity_group_id', array('value' => $this->data['CarClassStock']['commodity_group_id'], 'type' => 'hidden')); ?>
<?php } ?>

<?php echo $this->Form->input('result', array('value' => json_encode($result), 'type' => 'hidden')); ?>
<div style="float: right;">
	<?php echo $this->Form->submit('一括満車', array('name' => 'all_full_car', 'class' => 'btn btn-magenta click-alert', 'div' => false)); ?>
	<?php echo $this->Form->submit('一括売り止め', array('name' => 'delete', 'class' => 'btn btn-danger click-alert', 'div' => false)); ?>
</div>
</p>
<?php echo $this->Form->end(); ?>
<?php } else { ?>
<p>年月を指定して編集したい在庫を検索して下さい。</p>
<?php } ?>

<script>
$(function() {
	$(".full_car").click(function(){

		if(window.confirm('満車処理を行いますか？\n(予約分のキャンセル時、在庫が「残」に戻ります)') ){
			var stock_check = '';
			var name = $(this).attr('name');
			var year = $('#CarClassStockYear').val();
			var month = $('#CarClassStockMonth').val();
			var car_class_stock = $().val();

			var i = 0;
			var flg = false;
			$('.stock-check:checked').each(function() {
				flg = true;
				if(i > 0) {
					stock_check += ',';
				}
				stock_check += $(this).attr('name');
				i++;
			});

			if(stock_check) {
				$.ajax({
					type:"POST",
					url:"/rentacar/client/car_class_stocks/clientfullCar/",
					data:{
						name:name,
						year:year,
						month:month,
						stock_check:stock_check,
						full_car_flg:1
					},
					success:function(data){
						alert(data + "\nページを最新にします。しばらくお待ちください");
						location.reload();
					}
				});
			} else {
				alert('満車にしたい日付にチェックを入れてください');
			}
		}

	});

	$('.click-alert').click(function() {
		var str = $(this).val() + 'を実行します。よろしいですか？\n';
		var name = $(this).attr('name');
		if(name == 'all_full_car') {
			str += '(予約分のキャンセル時、在庫が「残」へ戻ります。)';
		} else if(name == 'delete') {
			str += '(予約のキャンセル時、在庫は戻りません。)';
		}

		if (!confirm( str + '')) {
			return false;
		}
	});

	$('.taking_over_data').click(function() {
		var str = '前月の在庫設定を引き継ぎます。よろしいですか？\n';

		if (!confirm( str + '')) {
			return false;
		}
	});

	//都道府県に応じて表示する在庫管理地域を変更する
	$('#CarClassStockPrefectureId').on('change',function() {
		changeStockGroup();
	});

	// 日付 単チェック
	$('.stock-check').on('click', function() {
		var clickedDate = $(this).parent('td').data('date');
		var targetTds = $(this).closest('table').find('[data-date="' + clickedDate + '"]');
		if ($(this).is(':checked')) {
			targetTds.addClass('is-dateSelected');
		} else {
			targetTds.removeClass('is-dateSelected');
		}
	});

	// 日付 全チェック
	$('#CarClassStockAll').on('change', function() {
		if ($(this).is(':checked')) {
			$('#stock').find('.date-cell').addClass('is-dateSelected');
		} else {
			$('#stock').find('.date-cell').removeClass('is-dateSelected');
		}
	});

	function changeStockGroup() {
		var prefectrue_id = $('#CarClassStockPrefectureId').val();
		$.ajax({
			type:"GET",
			url:"/rentacar/client/StockGroups/getStockGroupByPrefecture/" + prefectrue_id + '/',
			success:function(data){
				$('#CarClassStockStockGroupId').html(data);
			}
		});
	}
});

// リセット追加処理
$(document).on('click', '.btn-reset', function() {
	let $yearSelect = $('#CarClassStockYear');
	let selectIndex = $yearSelect.find('option').length - 2;
	// 後処理
	setTimeout(function() {
		$yearSelect.prop("selectedIndex", selectIndex);
	}, 1);
});

<!--
jQuery( function() {
    var dates = jQuery( '#jquery-ui-datepicker-from, #jquery-ui-datepicker-to' ) . datepicker( {
    	dateFormat: 'yy-mm-dd',
        showAnim: 'clip',
        monthNames: ['1月','2月','3月','4月','5月','6月',
                     '7月','8月','9月','10月','11月','12月'],
        changeMonth: false,
        numberOfMonths: 3,
        showCurrentAtPos: 1,
        onSelect: function( selectedDate ) {
            var option = this . id == 'jquery-ui-datepicker-from' ? 'minDate' : 'maxDate',
                instance = jQuery( this ) . data( 'datepicker' ),
                date = jQuery . datepicker . parseDate(
                    instance . settings . dateFormat ||
                    jQuery . datepicker . _defaults . dateFormat,
                    selectedDate, instance . settings );
            dates . not( this ) . datepicker( 'option', option, date );
        }
    } );
} );
// -->
</script>

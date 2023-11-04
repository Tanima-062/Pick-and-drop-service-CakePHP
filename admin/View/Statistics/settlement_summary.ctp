<!-- TASK-8134 -->
<?php echo $this->Form->create('Reservation'); ?>
<span id="finishedMonth" style="display: none"><?php echo $finishedMonth; ?></span>
計上月
<span>
	<?php echo $this->Form->year('date', 2016, date('Y'), array('empty' => false, 'value' => $year)); ?>年
</span>
<span>
	<?php echo $this->Form->month('date', array('empty' => false, 'monthNames' => false, 'value' => $month)); ?>月
</span>
<div>
クライアント名 
<span>	
<?php echo $this->Form->select('clientList',$clientList, array('default' => '', 'empty' => true)); ?>
</span>
</div>
<div>
精算管理会社名 
<span>	
<?php echo $this->Form->select('settlementCompanyList',$settlementCompanyList, array('default' => '', 'empty' => true)); ?>
</span>
</div>
<div class="control-group">
<?php
	echo $this->Form->button('絞り込む', array('class' => 'btn btn-primary'));
	echo $this->Form->button('リセット', array('type' => 'button', 'class' => 'btn btn-reset', 'style' => 'margin-right: 20px;'));
	echo $this->Form->submit('csv出力', array('class' => 'btn btn-warning', 'name' => 'getCsv', 'value' => '1', 'div' => false));
?>
</div>
<div id="settlementFinish" class="control-group" style="display: none">
<?php echo $this->Form->button('精算完了',array('class'=>'btn btn-danger','name'=>'settlementFinish','id'=>'finishButton','value'=>'1')); ?>
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

<h3>全体集計</h3>
<table class="table table-bordered">
	<tr>
		<th>合計 - 成約件数</th>
		<th>合計 - 成約金額</th>
		<th>現地精算 - 成約件数</th>
		<th>現地精算 - 成約金額</th>
		<th>事前決済 - 成約件数</th>
		<th>事前決済 - 成約金額</th>
		<th>決済キャンセル件数</th>
		<th>キャンセル料合計</th>
		<th>レコメンド手数料 - 税抜</th>
		<th>レコメンド手数料 - 税込</th>
		<th>販売手数料 - 税抜</th>
		<th>販売手数料 - 税込</th>
		<th>決済手数料 - 税抜</th>
		<th>決済手数料 - 税込</th>
		<th>販売手数料合計金額 - 税抜</th>
		<th>販売手数料合計金額 - 税込</th>
		<th>取消手続料金合計金額</th>
		<th>広告収入合計</th>
		<th>広告費合計</th>
	</tr>
	<tbody>
		<tr>
			<td><?php // 合計 - 成約件数
					if (isset($data['0'][$month]['agreement']['count'])) {
						echo number_format($data['0'][$month]['agreement']['count']);
					}
					?>
			</td>
			<td><?php // 合計 - 成約金額
					if (isset($data['0'][$month]['agreement']['price'])) {
						echo number_format($data['0'][$month]['agreement']['price']);
					}
					?>
			</td>
			<td><?php // 現地精算 - 成約件数
					if (isset($data['0'][$month]['agreement']['local_pay'])) {
						echo number_format($data['0'][$month]['agreement']['local_pay']);
					}
					?>
			</td>
			<td><?php // 現地精算 - 成約金額
					if (isset($data['0'][$month]['agreement']['local_amount'])) {
						echo number_format($data['0'][$month]['agreement']['local_amount']);
					}
					?>
			</td>
			<td><?php // 事前決済 - 成約件数
					if (isset($data['0'][$month]['agreement']['local_pay'])) {
						echo number_format($data['0'][$month]['agreement']['count'] - $data['0'][$month]['agreement']['local_pay']);
					}
					?>
			</td>
			<td><?php // 事前決済 - 成約金額
					if (isset($data['0'][$month]['agreement']['local_amount'])) {
						echo number_format($data['0'][$month]['agreement']['price'] - $data['0'][$month]['agreement']['local_amount']);
					}
					?>
			</td>
			<td><?php // 決済キャンセル件数
					if (isset($data['0'][$month]['cancel']['count']) && isset($data['0'][$month]['cancel']['local_pay'])) {
						echo number_format($data['0'][$month]['cancel']['count'] - $data['0'][$month]['cancel']['local_pay']);
					}
					?>
			</td>
			<td><?php // キャンセル料合計
					if (isset($data['0'][$month]['cancel']['cancel_detail_amount'])) {
						echo number_format($data['0'][$month]['cancel']['cancel_detail_amount']);
					}
					?>
			</td>
			<td><?php // レコメンド手数料 - 税抜
					if (!empty($data['0'][$month]['recommend']['recommend_without_tax'])) {
						echo number_format($data['0'][$month]['recommend']['recommend_without_tax']);
					}
					?>
			</td>
			<td><?php // レコメンド手数料 - 税込
					if (!empty($data['0'][$month]['recommend']['recommend_with_tax'])) {
						echo number_format($data['0'][$month]['recommend']['recommend_with_tax']);
					}
					?>
			</td>
			<td><?php // 販売手数料 - 税抜
					if (isset($data['0'][$month]['agreement']['commission'])) {
						echo number_format($data['0'][$month]['agreement']['commission']);
					}
					?>
			</td>
			<td><?php // 販売手数料 - 税込
					if (isset($data['0'][$month]['agreement']['commission'])) {
						echo number_format($taxRate * $data['0'][$month]['agreement']['commission']);
					}
					?>
			</td>
			<td><?php // 決済手数料 - 税抜
					if (isset($data['0'][$month]['agreement']['settlement_fee'])) {
						echo number_format($data['0'][$month]['agreement']['settlement_fee']);
					}
					?>
			</td>
			<td><?php // 決済手数料 - 税込
					if (isset($data['0'][$month]['agreement']['settlement_fee'])) {
						echo number_format($taxRate * $data['0'][$month]['agreement']['settlement_fee']);
					}
					?>
			</td>
			<td><?php // 販売手数料合計金額 - 税抜
					if (isset($data['0'][$month]['agreement']['commission']) || isset($data['0'][$month]['agreement']['settlement_fee']) || isset($data['0'][$month]['recommend']['recommend_without_tax'])) {
						echo number_format($data['0'][$month]['agreement']['commission'] + $data['0'][$month]['agreement']['settlement_fee'] + $data['0'][$month]['recommend']['recommend_without_tax']);
					}
					?>
			</td>
			<td><?php // 販売手数料合計金額 - 税込
					if (isset($data['0'][$month]['agreement']['commission']) || isset($data['0'][$month]['agreement']['settlement_fee']) || isset($data['0'][$month]['recommend']['recommend_with_tax'])) {
						echo number_format($taxRate * ($data['0'][$month]['agreement']['commission'] + $data['0'][$month]['agreement']['settlement_fee']) + $data['0'][$month]['recommend']['recommend_with_tax']);
					}
					?>
			</td>
			<td><?php // 取消手続料金合計金額
					if (isset($data['0'][$month]['cancel']['adventure_fee'])) {
						echo number_format($data['0'][$month]['cancel']['adventure_fee']);
					}
					?>
			</td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>

<h3>会社別集計</h3>
<?php //var_dump($data); ?>
<table class="table table-bordered">
	<tr>
		<th>クライアント名</th>
		<th>経理用管理コード</th>
		<th>精算管理会社名</th>
		<th>計上月</th>
		<th>合計 - 成約件数</th>
		<th>合計 - 成約金額</th>
		<th>現地精算 - 成約件数</th>
		<th>現地精算 - 成約金額</th>
		<th>事前決済 - 成約件数</th>
		<th>事前決済 - 成約金額</th>
		<th>決済キャンセル件数</th>
		<th>キャンセル料合計</th>
		<th>レコメンド - 対象件数</th>
		<th>レコメンド - 対象成約金額</th>
		<th>レコメンド手数料 - 税抜</th>
		<th>レコメンド手数料 - 消費税</th>
		<th>レコメンド手数料 - 税込</th>
		<th>販売手数料 - 料率</th>
		<th>販売手数料 - 税抜</th>
		<th>販売手数料 - 消費税</th>
		<th>販売手数料 - 税込</th>
		<th>決済手数料 - 料率</th>
		<th>決済手数料 - 税抜</th>
		<th>決済手数料 - 消費税</th>
		<th>決済手数料 - 税込</th>
		<th>精算金額</th>
		<th>精算種別</th>
	</tr>
	<tbody>
	<?php
	foreach ($dataSettles as $clientId => $dataSettle) {
		$includeTaxAdministrativeFee = 0;
		$settlementPrice = 0;
		if (
			$clientId == 0 ||
			($selectClientId != $clientId && !empty($selectClientId))
		) {
			continue;
		}
	?>
		<tr>
	<?php
		foreach ($dataSettle as $settlementCompanyId => $d) {
			if ($selectSettlementCompanyId != $d[$month]['settlement_company_id'] && !empty($selectSettlementCompanyId)) {
				continue;
			}
	?>
			<td><?php
					if ($d !== reset($dataSettle) && empty($selectSettlementCompanyId)) {
						echo '';
					} else {
						echo $clientList[$clientId];
					}
				?>
			</td>
			<td><?php
					if (isset($d[$month]['accounting_code'])) {
						echo $d[$month]['accounting_code'];
					}
				?>
			</td>
			<td><?php
					if (isset($d[$month]['settlement_company_name'])) {
						echo $d[$month]['settlement_company_name'];
					}
				?>
			</td>
			<td><?php echo $year . sprintf('%02d', $month); ?></td>
			<td><?php // 合計 - 成約件数
					if (isset($d[$month]['agreement']['count'])) {
						echo number_format($d[$month]['agreement']['count']);
					}
					?>
			</td>
			<td><?php // 合計 - 成約金額
					if (isset($d[$month]['agreement']['price'])) {
						echo number_format($d[$month]['agreement']['price']);
					}
					?>
			</td>
			<td><?php // 現地精算 - 成約件数
					if (isset($d[$month]['agreement']['local_pay'])) {
						echo number_format($d[$month]['agreement']['local_pay']);
					}
					?>
			</td>
			<td><?php // 現地精算 - 成約金額
					if (isset($d[$month]['agreement']['local_amount'])) {
						echo number_format($d[$month]['agreement']['local_amount']);
					}
					?>
			</td>
			<td><?php // 事前決済 - 成約件数
					if (isset($d[$month]['agreement']['local_pay'])) {
						echo number_format($d[$month]['agreement']['count'] - $d[$month]['agreement']['local_pay']);
					}
					?>
			</td>
			<td><?php // 事前決済 - 成約金額
					if (isset($d[$month]['agreement']['local_amount'])) {
						echo number_format($d[$month]['agreement']['price'] - $d[$month]['agreement']['local_amount']);
					}
					?>
			</td>
			<td><?php // 決済キャンセル件数
					if (isset($d[$month]['cancel']['count']) && isset($d[$month]['cancel']['local_pay'])) {
						echo number_format($d[$month]['cancel']['count'] - $d[$month]['cancel']['local_pay']);
					}
					?>
			</td>
			<td><?php // キャンセル料合計
					if (isset($d[$month]['cancel']['cancel_detail_amount'])) {
						echo number_format($d[$month]['cancel']['cancel_detail_amount']);
					}
					?>
			</td>
			<td><?php // レコメンド - 対象件数
					if (!empty($d[$month]['recommend']['recommend_count'])) {
						echo number_format($d[$month]['recommend']['recommend_count']);
					}
					?>
			</td>
			<td><?php // レコメンド - 対象成約金額
					if (!empty($d[$month]['recommend']['recommend_price'])) {
						echo number_format($d[$month]['recommend']['recommend_price']);
					}
					?>
			</td>
			<td><?php // レコメンド手数料 - 税抜
					if (!empty($d[$month]['recommend']['recommend_without_tax'])) {
						echo number_format($d[$month]['recommend']['recommend_without_tax']);
					}
					?>
			</td>
			<td><?php // レコメンド手数料 - 消費税
					if (!empty($d[$month]['recommend']['recommend_tax'])) {
						echo number_format($d[$month]['recommend']['recommend_tax']);
					}
					?>
			</td>
			<td><?php // レコメンド手数料 - 税込
					if (!empty($d[$month]['recommend']['recommend_with_tax'])) {
						echo number_format($d[$month]['recommend']['recommend_with_tax']);
					}
					?>
			</td>
			<td><?php // 販売手数料 - 料率
					if (isset($d[$month]['agreement']['commission_rate'])) {
						echo number_format($d[$month]['agreement']['commission_rate'] * 100, 1);
					}
					?>
			</td>
			<td><?php // 販売手数料 - 税抜
					if (isset($d[$month]['agreement']['commission'])) {
						echo number_format($d[$month]['agreement']['commission']);
					}
					?>
			</td>
			<td><?php // 販売手数料 - 消費税
					if (isset($d[$month]['agreement']['commission'])) {
						echo number_format(floor(($taxRate - 1.0) * $d[$month]['agreement']['commission']));
					}
					?>
			</td>
			<td><?php // 販売手数料 - 税込
					if (isset($d[$month]['agreement']['commission'])) {
						echo number_format(floor($taxRate * $d[$month]['agreement']['commission']));
					}
					?>
			</td>
			<td><?php // 決済手数料 - 料率
					if (isset($d[$month]['fee_rate'])) {
						echo number_format($d[$month]['fee_rate'], 1);
					}
					?>
			</td>
			<td><?php // 決済手数料 - 税抜
					if (!empty($d[$month]['agreement']['settlement_fee'])) {
						echo number_format($d[$month]['agreement']['settlement_fee']);
					}
					?>
			</td>
			<td><?php // 決済手数料 - 消費税
					if (!empty($d[$month]['agreement']['settlement_fee'])) {
						echo number_format(floor($d[$month]['agreement']['settlement_fee'] * ($taxRate - 1.0)));
					}
					?>
			</td>
			<td><?php // 決済手数料 - 税込
					if (!empty($d[$month]['agreement']['settlement_fee'])) {
						$includeTaxAdministrativeFee = $d[$month]['agreement']['settlement_fee'] + floor($d[$month]['agreement']['settlement_fee'] * ($taxRate - 1.0));
						echo number_format($includeTaxAdministrativeFee);
					}
					?>
			</td>
			<td><?php // 精算金額
					$commissionTax = ($d[$month]['is_internal_tax'] || !isset($d[$month]['agreement']['commission'])) ? 0 : floor(($taxRate - 1.0) * (int)$d[$month]['agreement']['commission']);
					$settlementTax = ($d[$month]['is_internal_tax']) ? 0 : floor($d[$month]['agreement']['settlement_fee'] * ($taxRate - 1.0));
					$settlementPrice = ((isset($d[$month]['agreement']['price']) ? $d[$month]['agreement']['price'] : 0) - (isset($d[$month]['agreement']['local_amount']) ? (int)$d[$month]['agreement']['local_amount'] : 0) + (isset($d[$month]['cancel']['cancel_detail_amount']) ? (int)$d[$month]['cancel']['cancel_detail_amount'] : 0)) -
									   ((isset($d[$month]['agreement']['commission']) ? (int)$d[$month]['agreement']['commission'] : 0) + $commissionTax + $d[$month]['agreement']['settlement_fee'] + $settlementTax + (isset($d[$month]['recommend']['recommend_with_tax']) ? (int)$d[$month]['recommend']['recommend_with_tax'] : 0));
					echo number_format($settlementPrice);
					?>
			</td>
			<td><?php
					echo ($settlementPrice > 0) ? '支払い' : '請求';
					?>
			</td>
		</tr>
	<?php
		}
	}
	?>
	</tbody>
</table>
<script>
	var finishedMonth = $("#finishedMonth").text();
	function setSettlementButton()
	{
		var now = new Date();
        now.setMonth(now.getMonth() - 1);
		var year = now.getFullYear().toString();
		var month = ("00" + (now.getMonth() + 1)).slice(-2);
		var selectedYear = $("#ReservationDateYear").val();
		var selectedMonth = $("#ReservationDateMonth").val();

		var button = $("#finishButton");
		var div = $("#settlementFinish");
		if (finishedMonth > selectedYear + selectedMonth) {
            button.prop("disabled", true);
            div.show();
		} else if (year + month < selectedYear + selectedMonth) {
		    div.hide();
        } else {
            button.prop("disabled", false);
		    div.show();
		}
	}
	$(function() {
		$('#ReservationDateYear').on("change", function() {
			setSettlementButton();
		});
		$('#ReservationDateMonth').on("change", function() {
			setSettlementButton();
		});
        $('#finishButton').on("click", function(e) {
            var year = $("#ReservationDateYear").val();
            var month = $("#ReservationDateMonth").val();
            if (!window.confirm(year + "年" + month + "月の精算を完了してもよろしいですか？")) {
                e.preventDefault();
            }
        });
	});
	setSettlementButton();
</script>
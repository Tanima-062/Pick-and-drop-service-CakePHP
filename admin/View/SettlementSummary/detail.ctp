<?php echo $this->Html->css("https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.2/themes/redmond/jquery-ui.min.css"); ?>
<div class="row-fluid">
	<div>
		<h3><?php echo $data['settlementCompanyName'];?>精算書詳細<br>(<?php echo $data['clientName'];?>)</h3>
		<?php echo $this->Form->create('SettlementSummary', array('type' => 'post')); ?>
		<div style="display:inline-block">
			<div class='span7' style="padding: 5px; background-color: #3A87AD;">
				<?php
				echo $this->Form->hidden('action', array('value' => 'NextAdjustmentAdd'));
				echo $this->Form->input('settlement_year', [
					'empty' => '差込年',
					'options' => $data['settlementYear'],
					'div' => false,
					'label' => false,
					'required' => true,
					'style' => 'width: auto; margin: 5px 5px;'
				]);
				echo $this->Form->input('settlement_month', [
					'empty' => '差込月',
					'options' => $data['settlementMonth'],
					'div' => false,
					'label' => false,
					'required' => true,
					'style' => 'width: auto; margin: 5px 5px;'
				]);
				echo $this->Form->input('item_name', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '品目',
					'style' => 'height: 28px; margin: 5px 5px',
					'maxlength' => 40,
				]);
				echo $this->Form->input('count', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '件数',
					'style' => 'height: 28px; width:auto; margin: 5px 5px',
					'maxlength' => 2,
				]);
				echo $this->Form->input('commission_rate', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '料率',
					'style' => 'height: 28px; width:auto; margin: 5px 5px',
					'maxlength' => 10,
				]);
				echo $this->Form->input('payment_amount', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '支払額',
					'style' => 'height: 28px; width:auto; margin: 5px 5px',
					'maxlength' => 10,
				]);
				echo $this->Form->input('billing_amount', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '請求額',
					'style' => 'height: 28px; width:auto; margin: 5px 5px',
					'maxlength' => 10,
				]);
				echo $this->Form->submit('追加', array('class' => 'btn btn-warning', 'div' => false, 'style' => 'margin: 5px 5px;'));
				echo $this->Form->end();
				?>
			</div>
		</div>
		<h4>登録済み次月調整</h4>
		<table class="table table-bordered">
			<tr class="success">
				<th>差込年月度</th>
				<th>品目</th>
				<th>件数</th>
				<th>料率</th>
				<th>支払額</th>
				<th>請求額</th>
				<th>最終更新日</th>
				<th>登録者</th>
				<th></th>
			</tr>
			<?php
				echo $this->Form->create('SettlementSummaryNextAdjustment', ['class' => 'edit-form',
				'url' => [
					'controller' => 'SettlementSummary',
					'action' => 'edit'
				]]);
				echo $this->Form->hidden('id');
				echo $this->Form->hidden('settlement_month');
				echo $this->Form->hidden('item_name');
				echo $this->Form->hidden('count');
				echo $this->Form->hidden('commission_rate');
				echo $this->Form->hidden('payment_amount');
				echo $this->Form->hidden('billing_amount');
				echo $this->Form->end();
				foreach ($settlementSummaryNextAdjustments as $key => $settlementSummaryNextAdjustment) {
					$nextAdjustment = $settlementSummaryNextAdjustment['SettlementSummaryNextAdjustment'];
					if ($nextAdjustment['status'] == 'NEW' || ($nextAdjustment['status'] == 'USED' && $data['isVisible'])) {
						$class = !empty($nextAdjustment['delete_flg']) ? 'gray' : '';
						if ((isset($nextAdjustment['payment_amount']) && $nextAdjustment['payment_amount'] > 0) || (isset($nextAdjustment['billing_amount']) && $nextAdjustment['billing_amount'] > 0)) {
							// 金額が入っていたら-か％付きの数字
							$commission_rate = !empty($nextAdjustment['commission_rate']) ? $nextAdjustment['commission_rate'].'%' : '-';
						} else {
							// 金額がない＝item_codeなし = 品目だけ記載したい項目なので空白
							$commission_rate = '';
						}
			?>
			<!-- 編集off -->
			<tr class="<?php echo $class; ?>">
				<td style="width: auto;"><?php echo h(implode('/', str_split($nextAdjustment['settlement_month'], 4))); ?>&nbsp;</td>
				<td><?php echo $nextAdjustment['item_name']; ?>&nbsp;</td>
				<td><?php echo $nextAdjustment['count']; ?>&nbsp;</td>
				<td><?php echo h($commission_rate); ?>&nbsp;</td>
				<td><?php echo ($nextAdjustment['payment_amount'] != '') ? h(number_format($nextAdjustment['payment_amount'])) : ''; ?>&nbsp;</td>
				<td><?php echo ($nextAdjustment['billing_amount'] != '') ? h(number_format($nextAdjustment['billing_amount'])) : ''; ?>&nbsp;</td>
				<td>
					<?php
						echo date("Y/m/d H:i:s",strtotime($nextAdjustment['update_datetime']));
					?>&nbsp;
				</td>
				<td><?php echo h($settlementSummaryNextAdjustment['Staff']['name']); ?>&nbsp;</td>
				<td class="actions">
					<?php if (!$nextAdjustment['delete_flg']) {
					echo $this->Html->link('編集', array(), array('class' => 'btn btn-warning edit-button'));
					?>
					<?php echo $this->Html->link('削除', array('action' => 'delete', $nextAdjustment['id']), array('class' => 'btn btn-danger')); }?>
				</td>
			</tr>
			<!-- 編集on -->
			<tr class="<?php echo $class; ?>">
				<td style="width: auto; display: none">
				<?php 
					echo $this->Form->input('settlement_year', [
						'empty' => '差込年',
						'options' => $data['settlementYear'],
						'div' => false,
						'label' => false,
						'required' => true,
						'style' => 'width: 70px; margin: 3px 3px;',
						'value' => str_split($nextAdjustment['settlement_month'], 4)[0]
					]);
					echo $this->Form->input('settlement_month', [
						'empty' => '差込月',
						'options' => $data['settlementMonth'],
						'div' => false,
						'label' => false,
						'required' => true,
						'style' => 'width: 50px; margin: 3px 3px;',
						'value' => str_split($nextAdjustment['settlement_month'], 4)[1]
					]);
				?>&nbsp;</td>
				<td style="display: none"><?php 
				echo $this->Form->input('item_name', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '品目',
					'style' => 'height: 28px; margin: 3px 3px',
					'maxlength' => 40,
					'value' => $nextAdjustment['item_name']
				]);
				?>&nbsp;</td>
				<td style="display: none">
				<?php 
				echo $this->Form->input('count', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '件数',
					'style' => 'height: 28px; width:30px; margin: 3px 3px',
					'maxlength' => 2,
					'value' => $nextAdjustment['count']
				]);
				?>&nbsp;</td>
				<td style="display: none"><?php 
				echo $this->Form->input('commission_rate', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '料率',
					'style' => 'height: 28px; width:30px; margin: 3px 3px',
					'maxlength' => 10,
					'value' => $nextAdjustment['commission_rate']
				]);
				?>&nbsp;</td>
				<td style="display: none"><?php 
				echo $this->Form->input('payment_amount', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '支払額',
					'style' => 'height: 28px; width:90px; margin: 3px 3px',
					'maxlength' => 10,
					'value' => $nextAdjustment['payment_amount']
				]);
				?>&nbsp;</td>
				<td style="display: none"><?php 
				echo $this->Form->input('billing_amount', [
					'type' => 'text',
					'div' => false,
					'label' => false,
					'placeholder' => '請求額',
					'style' => 'height: 28px; width:90px; margin: 3px 3px',
					'maxlength' => 10,
					'value' => $nextAdjustment['billing_amount']
				]);
				?>&nbsp;</td>
				<td style="display: none">
					<?php
						echo date("Y/m/d H:i:s",strtotime($nextAdjustment['update_datetime']));
					?>&nbsp;
				</td>
				<td style="display: none"><?php echo h($settlementSummaryNextAdjustment['Staff']['name']); ?>&nbsp;</td>
				<td class="actions" style="display: none">
					<?php
						echo $this->Form->hidden('targetId', array('value' => $nextAdjustment['id']));
						echo $this->Form->button('保存', array('class' => 'btn btn-warning edit-submit'));
					?>
				</td>
			</tr>
			<?php
					}
				}
			?>
		</table>
		<br>
		<h4>生成済み精算書類</h4>
		<?php
			echo $this->Form->create('SettlementSummary', array('type' => 'post', 'class'=> 'recreate'));
			echo $this->Form->hidden('action', array('value' => 'Recreate'));
			echo $this->Form->hidden('latestId', array('value' => $data['latestId']));
		?>
		<table class="table table-bordered">
			<tr class="success">
				<th>年月区分</th>
				<th>発行日</th>
				<th>同期日時</th>
				<th>精算書プレビュー</th>
				<th>精算書ダウンロード</th>
				<th>成約明細ダウンロード</th>
				<th>入金締切日</th>
				<th>再発行</th>
				<th>同期</th>
			</tr>
			<?php
				// 1ページ目限定で最新PDFがない場合だけ再発行ができるタイミングで「空白の再発行枠」を出す
				if ($this->params['paging']['SettlementSummary']['page'] == 1 && $data['latestId'] == '' && $data['isVisible']) {
			?>
			<tr>
				<td><?php echo h(implode('/', str_split($data['latestDate'], 4))); ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>
					<?php
						if ($data['paymentLimitDatetime']) {
							$paymentLimitDatetime = $data['paymentLimitDatetime'];
						} else {
							$paymentLimitDatetime = date("Y/m/d",strtotime('last day of next month'));
						}
						echo $this->Form->input('payment_limit_datetime', [
							'type' => 'text','div'=>false,'label'=>false,'id'=>'payment_limit_datetime','value'=>$paymentLimitDatetime
						]);
					?>&nbsp;
				</td>
				<td class="actions">
					<?php
						echo $this->Form->submit('再発行', array('class' => 'btn btn-warning', 'div' => false));
					?>
				</td>
				<td></td>
			</tr>
			<?php
				}
				foreach ($settlementSummaries as $key => $settlementSummary) {
			?>
			<tr>
				<td style="width: auto;"><?php echo h(implode('/', str_split($settlementSummary['SettlementSummary']['settlement_month'], 4))); ?>&nbsp;</td>
				<td>
					<?php
						echo date("Y/m/d",strtotime($settlementSummary['SettlementSummary']['update_datetime'])).'<br>'.date("H:i:s",strtotime($settlementSummary['SettlementSummary']['update_datetime']));
					?>&nbsp;
				</td>
				<td><?php
					if ($settlementSummary['SettlementSummary']['synchronization_status'] == 'SYNCHRONIZED') {
						echo date("Y/m/d",strtotime($settlementSummary['SettlementSummary']['synchronization_datetime'])).'<br>'.date("H:i:s",strtotime($settlementSummary['SettlementSummary']['synchronization_datetime']));
					} else {
						echo '---';
					}
				?>&nbsp;</td>
				<td class="actions">
					<?php echo $this->Html->link('プレビュー', array('action' => 'preview', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-warning', 'target' => '_blank')); ?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link('精算書DL', array('action' => 'download', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-warning')); ?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link('成約明細DL', array('action' => 'closingDownload', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-warning')); ?>
				</td>
				<td>
					<?php
					if ($data['latestId'] == $settlementSummary['SettlementSummary']['id'] && $data['isVisible']) {
						if ($data['paymentLimitDatetime']) {
							$paymentLimitDatetime = $data['paymentLimitDatetime'];
						} else {
							$paymentLimitDatetime = date("Y/m/d",strtotime($settlementSummary['SettlementSummary']['payment_limit_datetime']));
						}
						echo $this->Form->input('payment_limit_datetime', [
							'type' => 'text','div'=>false,'label'=>false,'id'=>'payment_limit_datetime','value'=>$paymentLimitDatetime
						]);
					} else {
						echo date("Y/m/d",strtotime($settlementSummary['SettlementSummary']['payment_limit_datetime']));
					}
					?>&nbsp;
				</td>
				<td class="actions">
					<?php
					if ($data['latestId'] == $settlementSummary['SettlementSummary']['id'] && $data['isVisible']) {
						echo $this->Form->submit('再発行', array('class' => 'btn btn-warning recreate', 'div' => false));
					}
					?>
				</td>
				<td class="actions">
					<?php
					if ($settlementSummary['SettlementSummary']['synchronization_status'] == 'CREATED') {
						echo $this->Html->link('同期', array('action' => 'synchronization', $settlementSummary['SettlementSummary']['id']), array('class' => 'btn btn-danger'));
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php 
			echo $this->Form->end();
			$pageParams = $this->Paginator->params();
			if (!empty($pageParams) && $pageParams['pageCount'] > 1) {
		?>
		<div class="pagination">
			<ul>
				<?php
					if ($this->Paginator->hasPrev()) {
						echo '<li>'.$this->Paginator->prev('< 戻る', array(), null, array('class' => 'prev disabled')). '</li>';
					}

					echo '<li>'.$this->Paginator->numbers(array('separator' => '')). '</li>';

					if ($this->Paginator->hasNext()) {
						echo '<li>'.$this->Paginator->next('次へ >', array(), null, array('class' => 'next disabled')). '</li>';
					}
				?>
			</ul>
		</div>
		<?php }?>
	</div>
</div>
<script>
	$(document).on('ready', function(){
		const pickeroption = {
			dateFormat: 'yy/mm/dd',
			monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
			dayNames: [ '日' , '月', '火', '水', '木', '金', '土'],
			dayNamesShort: [ '日' , '月', '火', '水', '木', '金', '土'],
			dayNamesMin: [ '日' , '月', '火', '水', '木', '金', '土'],
			prevText: '前へ',
			nextText: '次へ',
		};

		$('#payment_limit_datetime').datepicker(
			pickeroption
		);
	});

	//ボタンクリック時に実行
	$('.edit-button').click(function() {
		// 現在表示中のものを非表示に
		$(this).closest('tr').children("td").each(function(){
			changeElement($(this)[0]);
		});
		// 編集用の要素を表示
		$(this).closest('tr').next().children("td").each(function(){
			changeElement($(this)[0]);
		});

		// 遷移中止
		return false
	});

	//表示非表示切り替え
	let changeElement = (el)=> {
		if(el.style.display==''){
			el.style.display='none';
		}else{
			el.style.display='';
		}
	}

	//保存ボタンクリック時に実行
	$('.edit-submit').click(function() {
		let params = [];
		$(this).closest('tr').children('td').each(function(){
			$(this).children('select').each(function(){
				params.push($(this).val());
			});
			$(this).children('input').each(function(){
				params.push($(this).val());
			});
		});

		$('#SettlementSummaryNextAdjustmentId').val($(this).closest('tr').find('#targetId').val())
		$('#SettlementSummaryNextAdjustmentSettlementMonth').val(params[0] + params[1])
		$('#SettlementSummaryNextAdjustmentItemName').val(params[2])
		$('#SettlementSummaryNextAdjustmentCount').val(params[3])
		$('#SettlementSummaryNextAdjustmentCommissionRate').val(params[4])
		$('#SettlementSummaryNextAdjustmentPaymentAmount').val(params[5])
		$('#SettlementSummaryNextAdjustmentBillingAmount').val(params[6])
		$('.edit-form').submit();
	});
	// 再発行連打防止
	$('.recreate').submit(function() {
		if ($('.wait').size() > 0) {
			return false;
		}
		getClass = $(this).attr('class');
		$(this).attr('class',getClass+' wait');
	});
</script>

<style>
	.checkbox {
		float:left;
		width:100px;
		margin-left:2px;
	}
	th {
		width:100px;
	}
	.error-message{
		color:red;
	}
	.table-condensed{
		margin-bottom: 5px;
		border-radius: 4px;
	}
	.table-condensed thead:first-child tr:first-child>th:first-child{
		border-top-left-radius: 4px;
	}
	.table-condensed thead:first-child tr:first-child>th:last-child{
		border-top-right-radius: 4px;
	}
	.table-condensed tbody:last-child tr:last-child>td:first-child{
		border-bottom-left-radius: 4px;
	}
	.table-condensed tbody:last-child tr:last-child>td:last-child{
		border-bottom-right-radius: 4px;
	}
	#saveResponseHistory{
		text-shadow: 0 -1px 0 rgb(0 0 0 / 25%);
		background-color: #7bb33d;
		background-image: linear-gradient(to bottom,#80bb3f,#73a839);
		border: 1px solid #ccc;
		border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
		box-shadow: inset 0 1px 0 rgb(255 255 255 / 20%), 0 1px 2px rgb(0 0 0 / 5%);
		padding: 4px 12px;
		border-radius: 4px;
	}
</style>
<div class="recommends form span8">
	<h3>レコメンド編集</h3>
	<div style="overflow: hidden;margin-bottom:20px;">
		<h4 style="font-size: 17.5px;font-weight: bold;color: #317eac;">対応履歴</h4>
		<table class="table table-bordered table-condensed">
			<thead>
				<tr style="background-color: #dff0d8">
					<th style="width: 20%;">担当者</th>
					<th style="width: 20%">入力日時</th>
					<th>対応内容</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($MessageBoards as $messageBoard) { ?>
				<tr>
					<td><?php echo h($messageBoard['Staff']['name']); ?></td>
					<td><?php echo h($messageBoard['MessageBoard']['created']); ?></td>
					<td><?php echo nl2br(h($messageBoard['MessageBoard']['message'])); ?></td>
				</tr>
			<?php } ?>
				<tr>
					<td></td>
					<td></td>
					<td><textarea id="messageBoardMessage" name="messageBoardMessage" rows="2" style="width: 95%;border-radius: 4px;"></textarea></td>
				</tr>
			</tbody>
		</table>
		<div style="float: right">
			<button type="button" id="saveResponseHistory" class="btn btn-success">対応内容入力</button>
		</div>
	</div>

	<?php 
		echo $this->Form->create('Recommend', array('inputDefaults' => array('label' => false))); 
		echo $this->Form->hidden('id', array('label' => 'レコメンドID'));
	?>
	<?php $referer = ($this->request->data['Custom']['referer'] ? $this->request->data['Custom']['referer'] : $this->request->referer()); ?>
	<?php echo $this->Form->hidden('Custom.referer', array('value' => $referer)); ?>
	<table class="table table-bordered">
		<tr>
			<th>クライアント名</th>
			<td>
				<?php echo $this->Form->input('client_id',[
					'options' => $clientList,
					'empty' => true,
					'required' => true,
					'div' => false,
					'disabled' => 'disabled',
				]);
				echo $this->Form->hidden('client_id');
				?>
			</td>
		</tr>
		<tr>
			<th>PRタイトル</th>
			<td>
				<?php echo $this->Form->input('pr_title', array('required' => true, 'div' => false, 'style' => 'width:550px')); ?>
			</td>
		</tr>
		<tr class="form-inline">
			<th>掲載枠</th>
			<td>
				<?php
					echo $this->Form->input('space', array(
						'type' => 'radio', 'options' => $spaceOptions,
						'div' => false, 'legend' => false
					));
				?>
			</td>
		</tr>
		<tr>
			<th>開始日</th>
			<td>
				<?php echo $this->element('selectDatetime', $applyTermFromOptions);?>
				<?php if ($this->Form->isFieldError('apply_term_from')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('apply_term_from') ?></span>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<th>終了日</th>
			<td>
				<?php echo $this->element('selectDatetime', $applyTermToOptions);?>
				<?php if ($this->Form->isFieldError('apply_term_to')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('apply_term_to') ?></span>
				<?php } ?>
			</td>
		</tr>
		<?php
		if (!empty($fixedPrefectureList)) {
		?>
		<tr>
			<th>固定表示<br />対象地域</th>
			<td>
				<?php echo $this->Form->input('prefectures', [
					'type' => 'select', 
					'multiple'=> 'checkbox',
					'options' => $fixedPrefectureList,
					'class' => 'prefecture_check',
					'div' => false,
					'selected' => $fixedPrefectures,
				]); 
				?>
			</td>
		</tr>
		<?php
		}
		?>
		<?php
		if (!empty($randomPrefectureList)) {
		?>
		<tr>
			<th>ランダム表示<br />対象地域</th>
			<td>
				<?php echo $this->Form->input('randomPrefectures', [
					'type' => 'select', 
					'multiple'=> 'checkbox',
					'options' => $randomPrefectureList,
					'div' => false,
					'selected' => $randomPrefectures,
				]); 
				?>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<th>手数料</th>
			<td>
				<?php echo $this->Form->input('recommend_fee', array('required' => true, 'style' => 'float:left', 'div' => false, 'error' => false, 'disabled' => 'disabled')); ?>
				<?php echo $this->Form->input('recommend_fee_unit', array('default' => 0, 'div' => false, 'style' => 'float:left;width:50px;', 'options' => $recommendFeeUnit, 'disabled' => 'disabled')); ?>
				<?php echo $this->Form->input('is_internal_tax', array('default' => 0, 'div' => false, 'style' => 'width:80px;', 'options' => Constant::isInternalTax(), 'disabled' => 'disabled')); ?>

				<?php echo $this->Form->hidden('recommend_fee'); ?>
				<?php echo $this->Form->hidden('recommend_fee_unit'); ?>
				<?php echo $this->Form->hidden('is_internal_tax'); ?>
				<?php if ($this->Form->isFieldError('recommend_fee')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('recommend_fee') ?></span>
				<?php } ?>
				<?php if ($this->Form->isFieldError('recommend_fee_unit')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('recommend_fee_unit') ?></span>
				<?php } ?>
				<?php if ($this->Form->isFieldError('is_internal_tax')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('is_internal_tax') ?></span>
				<?php } ?>
			</td>
		</tr>
		<tr class="form-inline">
			<th>精算タイミング</th>
			<td>
				<?php
					echo $this->Form->input('settlement_timing', array(
						'type' => 'radio', 'options' => $settlementTiming,
						'div' => false, 'legend' => false, 'hiddenField' => false
					));
					echo $this->Form->hidden('settlement_timing');
				?>
			</td>
		</tr>
		<tr>
			<th>公開</th>
			<td>
				<?php echo $this->Form->input('is_published', array('type' => 'input', 'default' => 0, 'options' => $isPublishedOptions)); ?>
			</td>
		</tr>
	</table>
	<span class="left">
		<?php echo $this->Html->link('レコメンド一覧へ戻る', $referer, array('class' => 'btn btn-info'));?>
	</span>
	<div class="right">
		<?php echo $this->Form->submit('編集する', array('class'=>'btn btn-success'));?>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<script>
	$(function() {
		// 「全て」によるチェック処理
		$("[id*=Prefectures][value=0]").change(function() {
			if ($(this).is(':checked')) {
				$(this).parent().siblings().find('input[type="checkbox"]').prop("checked", true);
			} else {
				$(this).parent().siblings().find('input[type="checkbox"]').prop("checked", false);
			}
		});
		// 「全て」に対するチェック処理
		$("[id*=Prefectures][value!=0]").change(function() {
			// 固定かランダムか判定
			targetId = $(this).attr("id").replace(/\d/g,'');
			checkPrefectureCount = $('[id^='+targetId+'][value!=0][type!=hidden]:checked').length;
			if ($(this).is(':checked')) {
				// その枠の対象地域数
				maxPrefectureCount = $('[id^='+targetId+'][value!=0][type!=hidden]').length;
				if (maxPrefectureCount == checkPrefectureCount) {
					// 「全て」以外全てのチェックがついたら「全て」にチェックをつける
					$('[id^='+targetId+'][value=0]').prop("checked", true);
				}
			} else {
				if (checkPrefectureCount == 0) {
					// 「全て」以外全てのチェックが消えたら「全て」のチェックを消す
					$('[id^='+targetId+'][value=0]').prop("checked", false);
				}
			}
		});
		$('#RecommendSettlementTiming0').prop('disabled', true);
		$('#RecommendSettlementTiming1').prop('disabled', true);
		$('#RecommendRecommendFeeUnit').trigger('change');
	});

	$(document).on('click', '#saveResponseHistory', function () {
		const messageBoardMessage = $.trim($('#messageBoardMessage').val());
		if (messageBoardMessage.length === 0) {
			return;
		}

		$('#saveResponseHistory').prop('disabled', true);
		$.ajax({
			type: 'POST',
			dataType: 'json',
			timeout: 10000,
			url: '/rentacar/admin/Recommends/saveResponseHistory',
			data: {
				recommend_id: $('#RecommendId').val(),
				message: messageBoardMessage
			}
		})
		.done(function (res) {
			if (res && res.ret === 'ok') {
				$('#messageBoardMessage').val('');
				alert('登録しました');
                let url = location.href + '?Custom[referer]=' + $('#CustomReferer').val();
				location.href = url;
			} else {
				alert('Error:登録に失敗しました。\n' + res.message);
			}
		})
		.fail(function () {
			alert('Fail:登録に失敗しました');
		})
		.always(function(){
			$('#saveResponseHistory').prop('disabled', false);
		});
	});
</script>

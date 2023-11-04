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
</style>
<div class="recommends form span8">
	<h3>レコメンド追加</h3>
	<?php echo $this->Form->create('Recommend', array('inputDefaults' => array('label' => false))); ?>
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
				]);
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
						'type' => 'radio', 'options' => $spaceOptions, 'default' => 0,
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
					'div' => false
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
					'div' => false
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
				<?php echo $this->Form->input('recommend_fee', array('required' => true, 'style' => 'float:left', 'div' => false, 'error' => false)); ?>
				<?php echo $this->Form->input('recommend_fee_unit', array('default' => 0, 'div' => false, 'style' => 'float:left;width:50px;', 'options' => $recommendFeeUnit)); ?>
				<?php echo $this->Form->input('is_internal_tax', array('default' => 0, 'div' => false, 'style' => 'width:80px;', 'options' => Constant::isInternalTax())); ?>
				<?php if ($this->Form->isFieldError('recommend_fee')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('recommend_fee') ?></span>
				<?php } ?>
				<?php if ($this->Form->isFieldError('recommend_fee_unit')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('recommend_fee_unit') ?></span>
				<?php } ?>
				<?php if ($this->Form->isFieldError('is_internal_tax')) { ?>
						<span class="help-inline error-message"><?php echo $this->Form->error('is_internal_tax') ?></span>
				<?php } ?>
				<span style='margin-left:10px;color:red;'>※無償掲載の場合は％で登録してください</span>
			</td>
		</tr>
		<tr class="form-inline">
			<th>精算タイミング</th>
			<td>
				<?php
					echo $this->Form->input('settlement_timing', array(
							'type' => 'radio', 'options' => $settlementTiming, 'default' => 0,
							'div' => false, 'legend' => false
					));
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
		<?php echo $this->Form->submit('登録する', array('class'=>'btn btn-success'));?>
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
		$('#RecommendRecommendFeeUnit').change(function() {
			if ($('#RecommendRecommendFeeUnit').val() == '0') {
				$('#RecommendSettlementTiming0').prop('disabled', true);
				$('#RecommendSettlementTiming1').prop('disabled', true);
			} else {
				$('#RecommendSettlementTiming0').prop('disabled', false);
				$('#RecommendSettlementTiming1').prop('disabled', false);
			}
		});
		$('#RecommendRecommendFeeUnit').trigger('change');
	});
</script>

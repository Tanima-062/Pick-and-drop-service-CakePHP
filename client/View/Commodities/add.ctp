<style>
textarea,.uneditable-input {
	width: 100%;
}
#consider_opening input[type="radio"] {
	margin-top: 3px;
	margin-left: 1px;
}
#consider_opening label {
	font-size: 11px;
}
#consider_opening input[type="radio"] + label{
	margin-left: 18px;
}
</style>
<div class='commodities form'>
	<?php echo $this->Form->create('Commodity',array('enctype'=>'multipart/form-data','novalidate'=>true));?>

	<fieldset>
		<legend> 商品情報編集 </legend>
		<?php
		$errorMsg = $this->Session->flash('auth');
		if(!empty($errorMsg)) {
		?>
		<div class="alert alert-error">
			<?php echo $errorMsg;?>
		</div>
		<?php
		}
		?>
		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>商品管理番号</th>
				<td colspan="3"><div class="control-group">
						<div class="controls">
							<?php echo $this->Form->input('commodity_key',array('label'=>false));?>
						</div>
					</div>
				</td>
			</tr>
			<?php if ($clientData['Client']['is_managed_package'] === true): ?>
			<tr>
				<th>販売方法</th>
				<td colspan="3"><div class="control-group">
						<div class="controls">
							<?php echo $this->Form->input('sales_type', $salesType); ?>
						</div>
					</div>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th>商品名</th>
				<td colspan="3"><div class="control-group">
						<div class="controls">
							<?php echo $this->Form->input('name',array('label'=>false,'style'=>'width:95%'));?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th>グループ</th>
				<td colspan="3"><div class="control-group">
						<div class="controls">
							<?php echo $this->Form->input('commodity_group_id',$groupFormOptions);?>

						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th>受取可能時間</th>
				<td colspan="3">
				<?php echo $this->element('selectDatetime',$rentTimeFromOptions);?> ～ <?php echo $this->element('selectDatetime',$rentTimeToOptions);?>
				<br>
				<span  class="text-error">※24時間営業の営業所が紐づく商品プランの場合、0時00分～23時59分とご設定ください。</span>
				</td>
			</tr>
			<tr>
				<th>返却可能時間</th>
				<td colspan="3">
				<?php echo $this->element('selectDatetime',$returnTimeFromOptions);?> ～ <?php echo $this->element('selectDatetime',$returnTimeToOptions);?>
				<br>
				<span  class="text-error">※24時間営業の営業所が紐づく商品プランの場合、0時00分～23時59分とご設定ください。</span>
				</td>
			</tr>
			<tr>
				<th>商品説明</th>
				<td colspan="3"><?php  echo $this->Form->input('description',array('label'=>false, 'style' => 'box-sizing:border-box;'));?></td>
			</tr>
			<tr>
				<th>参考画像<br />※画像ｻｲｽﾞ：各2MB未満</th>
				<td colspan="3">
					<?php
					for($i = 0; $i <= 7; $i++) {
						$imageId = '';
						if(!empty($imageArray[$i]['id'])) {
							$imageId = $imageArray[$i]['id'];
						}
					?>
					<table class="table table-striped table-bordered table-condensed">
						<tr>
							<th>
								画像<?php echo ($i+1) ?>
								<?php if($i == 0): ?>
								（プラン紹介表示用・外装）
								<?php endif; ?>
							</th>
							<th>備考</th>
						</tr>
						<tr>
							<td style="width:30%;">
							<?php
							echo $this->Form->hidden("CommodityImage.{$i}.image_id",array("value"=>"{$imageId}"));
							if(!empty($imageArray[$i]['image_relative_url'])) {
								echo $this->Html->image('../../img/'.$imageArray[$i]['image_relative_url']);
							}
								echo $this->Form->input("CommodityImage.{$i}.image_relative_url", array('type'=>'file', 'label'=>false));
							?>
							</td>
							<td>
							<?php echo $this->Form->textarea("CommodityImage.{$i}.remark",array('type'=>'text','label'=>false, 'style' => 'box-sizing:border-box;'));?>
							</td>
						</tr>
					</table>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>備考</th>
				<td colspan="3"><?php echo $this->Form->input('remark',array('label'=>false, 'style' => 'box-sizing:border-box;'));?></td>
			</tr>
			<tr>
				<th>受取営業所</th>
				<td>
					<?php echo $this->Form->input('CommodityRentOffice.prefecture',$prefectureRentFormOptions);?>
					<div style="margin-bottom:20px;">
						<?php echo $this->Form->input('CommodityRentOffice.all_commodity_edit', array('type' => 'checkbox', 'label' => '下記すべてにチェックする'));?>
					</div>
					<?php echo $this->Form->input('CommodityRentOffice.commodity_id',$officeRentFormOptions);?>
				</td>
				<th>返却営業所</th>
				<td>
					<?php echo $this->Form->input('CommodityReturnOffice.prefecture',$prefectureReturnFormOptions);?>
					<div style="margin-bottom:20px;">
						<?php echo $this->Form->input('CommodityReturnOffice.all_commodity_edit', array('type' => 'checkbox', 'label' => '下記すべてにチェックする'));?>
					</div>
					<?php echo $this->Form->input('CommodityReturnOffice.commodity_id',$officeReturnFormOptions);?>
				</td>
			</tr>
			<tr>
				<th>提供日時</th>
				<td colspan="3">
					提供開始日時<?php echo $this->element('selectDatetime',$availableFromOptions);?>～<br>
					提供終了日時<?php echo $this->element('selectDatetime',$availableToOptions);?>
				</td>
			</tr>
			<tr id="deadline-hours">
				<th>手仕舞い設定</th>
				<td colspan="3">
					<input type="radio" id="HoursOrDays0" name="radioHoursOrDays" value="0" style="margin-top: -3px"<?php echo (!$hoursOrDays ? ' checked="checked"' : '');?>> 出発時間の<?php echo $this->Form->input('CommodityTerm.deadline_hours',array('label'=>false,'div'=>false));?> 時間前まで予約受付可
					<br>
					<div id="consider_opening">
						<?php echo $this->Form->input('CommodityTerm.consider_opening_hours', array(
							'type' => 'radio',
							'options' => array(0 => '営業時間を考慮しない', 1 => '営業時間を考慮する'),
							'legend' => false,
						)); ?>
						<p class="text-error" style="margin-left: 35px">
							※出発日前日の閉店時刻以降の予約の場合、予約可能な出発時間は開店時刻＋手仕舞い設定時間となります。<br>
						</p>
					</div>
					<input type="radio" id="HoursOrDays1" name="radioHoursOrDays" value="1" style="margin-top: -3px"<?php echo ($hoursOrDays ? ' checked="checked"' : '');?>> 出発日の <?php echo $this->Form->input('CommodityTerm.deadline_days',array('min' => 0, 'label'=>false,'div'=>false));?> 日前
					<?php echo $this->Form->hour('CommodityTerm.deadline_time', true, $deadlineTimeOptions); ?>時
					<?php echo $this->Form->minute('CommodityTerm.deadline_time', $deadlineTimeOptions); ?> 分まで予約受付可
				</td>
			</tr>
			<tr>
				<th>発売日設定</th>
				<td colspan="3">
				出発日の <?php echo $this->Form->input('CommodityTerm.bookable_days',array('min' => 0, 'label'=>false,'div'=>false));?> 日前に発売開始<br>
				</td>
			</tr>
			<tr>
				<th>禁煙・喫煙情報</th>
				<td>
				<?php echo $this->Form->input('smoking_flg', array(
						'type' => 'radio',
						'options' => array(0 => '禁煙', 1 => '喫煙'),
						'legend' => false,
						'default' => 0,
				)); ?>
				</td>
				<th>AT/MT情報</th>
				<td>
				<?php echo $this->Form->input('transmission_flg', array(
						'type' => 'radio',
						'options' => array(0 => 'AT', 1 => 'MT'),
						'legend' => false,
						'default' => 0,
				)); ?>
				</td>
			</tr>
			<tr>
				<th>商品装備情報</th>
				<td colspan="3"><?php echo $this->Form->input('CommodityEquipment.equipment_id',$equipmentFormOptions);?></td>
			</tr>
			<?php if (!empty($privilegeFormOptions)) { ?>
			<tr>
				<th>商品オプション情報</th>
				<td colspan="3">
				<?php
				echo $this->Form->input('CommodityPrivilege.privilege_id',array(
						'type' => 'select',
						'multiple'=>'checkbox',
						'options' => $privilegeFormOptions,
						'empty'=>'',
						'label'=> false,
						'div'=> false,
				));
				?>
				</td>
			</tr>
			<?php } ?>
			<?php if (!empty($sheetFormOptions)) { ?>
			<tr>
				<th>シート情報</th>
				<td colspan="3">
				<?php
				echo $this->Form->input('CommodityPrivilege.sheet_privilege_id',array(
						'type' => 'select',
						'multiple'=>'checkbox',
						'options' => $sheetFormOptions,
						'empty'=>'',
						'label'=> false,
						'div'=> false,
				));
				?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<th>料金形態</th>
				<td colspan="3">
				<?php
				$dayTimeList = array(0 => '暦日制', 1 => '時間制');
				?>
				<?php
				echo $this->Form->input('day_time_flg', array(
						'type' => 'radio',
						'options' => $dayTimeList,
						'label' => true,
						'legend' => false,
						'default' => 0,
				));
				?>
				</td>
			</tr>
			<?php if ($clientData['Client']['accept_prepay']) { ?>
			<tr>
				<th>お支払い方法</th>
				<td colspan="3">
					<?php echo $this->Form->input('payment_method', array('type' => 'select', 'label' => false, 'options' => $paymentMethodOptions)); ?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</fieldset>
	<center>
		<div class="control-group">
			<?php echo $this->Form->submit('保存して価格設定をする',array('class'=>'btn btn-success'));?>
		</div>

		<?php echo $this->Form->end();?>
		<a href="/rentacar/client/Commodities/" class="btn btn-warning">キャンセル</a>
	</center>

</div>

<script>
<!--
jQuery( function() {

	function hoursOrDays() {
		if ($('#HoursOrDays0').prop('checked')) {
			$('#consider_opening').show();
			$('#CommodityTermDeadlineDays').val('');
			$('#CommodityTermDeadlineTimeHour').val('');
			$('#CommodityTermDeadlineTimeMin').val('');
			$('#CommodityTermDeadlineHours').prop('disabled', false);
			$('#CommodityTermDeadlineDays').prop('disabled', true);
			$('#CommodityTermDeadlineTimeHour').prop('disabled', true);
			$('#CommodityTermDeadlineTimeMin').prop('disabled', true);
		} else {
			$('#consider_opening').hide();
			$('#CommodityTermConsiderOpeningHours0').prop('checked', true);
			$('#CommodityTermDeadlineHours').val('');
			$('#CommodityTermDeadlineDays').prop('disabled', false);
			$('#CommodityTermDeadlineTimeHour').prop('disabled', false);
			$('#CommodityTermDeadlineTimeMin').prop('disabled', false);
			$('#CommodityTermDeadlineHours').prop('disabled', true);
		}
	}

	$('input[name="radioHoursOrDays' ).change( function() {
		hoursOrDays();
	});

	// 募集型企画が選択された場合、選択可能な項目を制限する
	function changePropertyBySalesType() {
		if ($('#CommoditySalesType').val() === 'AGENT-ORGANIZED') {
			$('#CommodityDayTimeFlg0').prop('checked', true);
			$('#CommodityDayTimeFlg1').prop('disabled', true);
			$('#CommodityPaymentMethod option:nth-child(1)').hide();
			$('#CommodityPaymentMethod option:nth-child(2)').prop('selected', true);
			$('#CommodityPaymentMethod option:nth-child(3)').hide();
		} else {
			$('#CommodityDayTimeFlg1').prop('disabled', false);
			$('#CommodityPaymentMethod option:nth-child(1)').show().prop('selected', true);
			$('#CommodityPaymentMethod option:nth-child(3)').show();
		}
	}

	hoursOrDays();
	changePropertyBySalesType();

	$('#CommoditySalesType').change(function() {
		changePropertyBySalesType();
	});
} );
// -->
</script>

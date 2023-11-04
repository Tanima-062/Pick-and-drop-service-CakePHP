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

<script type="text/javascript"><!--
function DelCheck() {
   // 確認ダイアログを表示
    return res = confirm("この商品を削除します。");
}
//--></script>



<?php
$imageArray = $this->request->data['CommodityImage'];
?>
<div class='commodities form'>
	<?php echo $this->Form->create('Commodity',array('enctype'=>'multipart/form-data','novalidate'=>true));?>

	<?php echo $this->Form->input('id');?>
	<fieldset>
		<legend> 商品情報編集 </legend>
		<?php
			$errorMsg = $this->Session->flash('auth');
			if(!empty($errorMsg)) {
		?>
		<div class="alert alert-error">
			<?php echo $errorMsg;?>
		</div>
		<?php } ?>

		<table class="table table-striped table-bordered table-condensed">
			<tr>
				<th>商品管理番号</th>
				<td colspan="3"><?php echo $this->Form->input('commodity_key',array('label'=>false));?></td>
			</tr>
			<?php if ($clientData['Client']['is_managed_package']): ?>
			<tr>
				<th>販売方法</th>
				<td colspan="3" id="salesType" data-salestype="<?php echo $this->data['Commodity']['sales_type']; ?>">
				<?php echo Constant::salesType()[$this->data['Commodity']['sales_type']]; ?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th>商品名</th>
				<td colspan="3"><?php echo $this->Form->input('name',array('label'=>false,'style'=>'width:95%'));?></td>
			</tr>
			<tr>
				<th>グループ</th>
				<td colspan="3"><?php echo $this->Form->input('commodity_group_id',$groupFormOptions);?></td>
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
				<th>返車可能時間</th>
				<td colspan="3">
				<?php echo $this->element('selectDatetime',$returnTimeFromOptions);?> ～ <?php echo $this->element('selectDatetime',$returnTimeToOptions);?>
				<br>
				<span  class="text-error">※24時間営業の営業所が紐づく商品プランの場合、0時00分～23時59分とご設定ください。</span>
				</td>
			</tr>
			<tr>
				<th>商品説明</th>
				<td colspan="3"><?php  echo $this->Form->textarea('description',array('label'=>false,"rows"=>"10", 'style' => 'box-sizing:border-box;'));?></td>
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
							<th>画像<?php echo ($i+1) ?>
								<?php if($i == 0): ?>
								（プラン紹介表示用・外装）
								<?php endif; ?>
							</th>
							<th>備考</th>
							<th>操作</th>
						</tr>
						<tr>
							<td style="width:30%;">
							<?php
								echo $this->Form->hidden("CommodityImage.{$i}.image_id",array("value"=>"{$imageId}"));
								if(!empty($imageArray[$i]['image_relative_url'])) {
									echo $this->Html->image('../../img/commodity_reference/'.$clientId.'/'.$imageArray[$i]['image_relative_url'],array('style'=>'width:100%;'));
								}
								echo $this->Form->input("CommodityImage.{$i}.image_relative_url", array('type'=>'file', 'label'=>false));
							?>
							</td>
							<td style="width:55%;"><?php echo $this->Form->textarea("CommodityImage.{$i}.remark",array('type'=>'text','label'=>false,'novalidate'=>true, 'style' => 'box-sizing:border-box;width:100%;'));?></td>
							<td style="width:15%;text-align:center;">
								<?php if($i == 0): ?>
									<span class="text-error">※紹介表示用の画像は変更する場合、再アップロードをお願いします。</span>
								<?php else: ?>
								<?php echo $this->Html->link('画像削除','del_img/'.$imageId, array('class' => 'btn btn-danger btn-small')); ?>
								<?php endif; ?>
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
							※出発日前日の閉店時刻以降の予約の場合、予約可能な出発時間は開店時刻＋手仕舞い設定時間となります。
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
				)); ?>
				</td>
				<th>AT/MT情報</th>
				<td>
				<?php echo $this->Form->input('transmission_flg', array(
						'type' => 'radio',
						'options' => array(0 => 'AT', 1 => 'MT'),
						'legend' => false,
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
				<th>新車登録からの年数</th>
				<td colspan="3">
					<?php
					$carRegistrationArray = array(
							1=>'新車登録1年以内',
							2=>'新車登録2年以内',
							3=>'新車登録3年以内',
							4=>'新車登録4年以内',
							5=>'新車登録5年以上'
					);
					echo $this->Form->input('new_car_registration',array('type'=>'select','empty'=>'---', 'options'=>$carRegistrationArray, 'label'=>false,'div'=>false));
					?>
				</td>
			</tr>
			<tr>
				<th>公開</th>
				<td colspan="3">
				<?php echo $this->Form->input('is_published',array('type'=>'select', 'label'=>false,'options'=>array(0=>'非公開にする', 1=>'公開する'), 'selected' => $this->data['Commodity']['is_published']));?>
				<?php echo $this->Form->hidden('public_request',array('value'=>0));?>
				</td>
			</tr>
			<tr>
				<th>料金形態</th>
				<td colspan="3">
				<?php
				$dayTimeList = array(0 => '暦日制', 1 => '時間制');
				?>
				<?php echo $dayTimeList[$this->data['Commodity']['day_time_flg']]; ?>
				</td>
			</tr>
			<?php if ($clientData['Client']['accept_prepay']) { ?>
			<tr>
				<th>お支払い方法</th>
				<td colspan="3">
					<?php echo $this->Form->input('payment_method', array('type' => 'select', 'label' => false, 'options' => $paymentMethodOptions, 'selected' => $this->data['Commodity']['payment_method'])); ?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</fieldset>

	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>値段が登録されている車両クラス</th>
		</tr>
		<?php foreach($registCarClass as $key => $val) { ?>
			<?php if ($template !== ''): ?>
			<tr>
				<td><?php echo $this->Html->link("{$val['CarClasses']['name']}", "{$template}/{$val['Commodity']['id']}/{$val['CarClasses']['id']}");?></td>
			</tr>
			<?php endif; ?>
		<?php } ?>
	</table>

	<div class="control-group" style="padding-bottom:30px">
		<?php echo $this->Form->submit('編集',array('class'=>'btn btn-success', 'style' => 'float:left; margin-right:850px;'));?>
		<?php echo $this->Form->end();?>
	</div>


	<?php if (empty($registCarClass)) { ?>
	<div class="control-group" style="padding-bottom:10px">
		<?php echo $this->Form->create('Commodity',array('type' => 'post', 'action' => $template)); ?>
		<?php echo $this->Form->hidden('id');?>
		<?php echo $this->Form->submit('価格設定',array('class'=>'btn btn btn-warning'));?>
		<?php echo $this->Form->end();?>
	</div>
	<?php } ?>

	<div class="control-group">
		<?php echo $this->Form->create('Commodity',array('type' => 'post', 'action' => 'commodityDelete'));?>
		<?php echo $this->Form->hidden('id');?>
		<?php echo $this->Form->hidden('commodity_group_id');?>
		<?php echo $this->Form->submit('削除', array ('class' => 'btn btn-danger', 'div' => false,'onclick'=>'return DelCheck();')); ?>
		<?php echo $this->Form->end();?>
	</div>

</div>

<script>
<!--
jQuery( function() {

	var dates = jQuery( '.jquery-ui-datepicker-from, .jquery-ui-datepicker-to' ) . datepicker( {
		dateFormat: 'yy-mm-dd',
		showAnim: 'clip',
		monthNames: ['1月','2月','3月','4月','5月','6月',
		             '7月','8月','9月','10月','11月','12月'],
		changeMonth: false,
		numberOfMonths: 3,
		showCurrentAtPos: 0,
	} );

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

	hoursOrDays();
	if ($('#salesType').data('salestype') === 'AGENT-ORGANIZED') {
		$('#CommodityPaymentMethod option:nth-child(1)').hide();
		$('#CommodityPaymentMethod option:nth-child(2)').prop('selected', true);
		$('#CommodityPaymentMethod option:nth-child(3)').hide();
	}
} );
// -->
</script>

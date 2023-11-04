<style>
input[type="text"],
input[type="number"]{
	width: 100%;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}
.table {
	table-layout:fixed;
}
.table tr th,
.table tr td {
	text-align: center;
	border: solid #ddd 1px;
}
.deleted {
	background-color: #BDBDBD;
}
.first-th {
	width:280px;
}
</style>

<h3>時間制料金設定</h3>

<?php echo $this->Form->create('CommodityItem',array('novalidate'=>false, 'inputDefaults' => array('label' => false, 'div' => false)));?>
<div style="margin-bottom: 20px;">
	<div class="error">
		<?php echo $this->Session->flash();?>
	</div>
	<p style="color: #317eac; font-weight: bold; font-size: 150%;">
	車両クラス・車種
	</p>
	<p style="margin-left: 10px; font-size: 130%;">
	車種指定プランの場合は車種名を選択してください。
	</p>
	<div class="right">
		<?php echo $this->Html->link('商品編集画面へ戻る','/Commodities/edit/'.$commodityId . '/',array('class'=>'btn btn-warning')); ?>
	</div>
	<div style="margin-bottom: 10px;">
		<?php
		echo $this->Form->input('CommodityItem.car_class_id', array(
				'type' => 'select',
				'class' => '',
				'options' => $carClassList,
		));
		?>
		<?php
		echo $this->Form->input('CommodityItem.car_model_id', array(
				'type' => 'select',
				'class' => '',
				'options' => $carModelList,
				'empty' => '車種未指定',
		));
		?>
	</div>
	<?php if ($isSystemAdmin) { ?>
	<p style="color: #317eac; font-weight: bold; font-size: 150%;">
	SIPPコード&nbsp;(車両サイズ / ドア数 / トランスミッション / 燃料＆エアコン)
	</p>
	<div style="margin-bottom: 10px;">
		<?php
			for ($i = 0; $i < 4; $i++) {
				echo $this->Form->input('CommodityItem.sipp_code.' . $i, array(
						'type' => 'select',
						'class' => '',
						'options' => $sippCodeList[$i],
						'empty' => '---',
						'required' => true,
				));
			}
		?>
	</div>
	<?php } ?>
	<?php
		if (!empty($CommodityItemId)) {
			echo $this->Form->submit('車両情報のみ保存',array('name'=>'save2','class'=>'btn btn-success'));
		}
	?>
</div>
<div style="margin-bottom: 20px;">
	<p style="color: #317eac; font-weight: bold; font-size: 150%;">
	基本料金
	</p>
	<div style="margin: 0 0 10px 10px;">
		<p style="font-size: 130%;">
		免責補償料金抜きの価格を設定してください。
		</p>
		<?php
			echo $this->Form->input('disclaimer', array(
				'type' => 'checkbox',
				'label' => '選択中の車両クラスの免責補償料金設定を確認',
				'div' => true,
				'checked' => false,
			));
		?>
		<div id="disclaimerList" style="font-size: 130%;"></div>
	</div>
	<table class="table table-condensed">
		<tr class="success">
			<th>6時間</th>
			<th>12時間</th>
			<th>24時間</th>
			<th>以後1日</th>
			<th>超過1時間</th>
			<?php if (!empty($CommodityItemId)) { ?>
			<th></th>
			<?php } ?>
		</tr>
		<tr>
			<?php foreach ($commodityScheduleArray as $key => $value) { ?>
			<td>
			<?php echo $this->Form->input("CommodityPrice.{$value}.commodity_price", array(
					'type' => 'number',
					'label' => false,
					'min' => 0,
					'required' => true,
			));
			?>
			</td>
			<?php } ?>
			<?php if (!empty($CommodityItemId)) { ?>
			<td>
			<?php
				if(!empty($this->data['CommodityItem']['car_class_id'])) {
					echo $this->Html->link('詳細設定', '/commodities/detailTimeSystem/'.$detailLink, array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 5px'));
					echo '<br>';
				}
				echo $this->Form->submit('基本料金のみ保存',array('name'=>'save3','class'=>'btn btn-success'));
			?>
			</td>
			<?php } ?>
		</tr>
	</table>
</div>

<p style="color: #317eac; font-weight: bold; font-size: 150%;">
キャンペーン料金
</p>
<div style="margin-bottom: 20px; margin-left: 20px">
	<table style="margin-bottom: 20px; border: solid #ddd 1px; font-size: 130%">
		<tr>
			<td><p style="margin-left: 10px; margin-right: 10px; margin-top: 10px">期間</p></td>
			<td colspan="2"><?php echo $this->Form->select('Campaign.id', $campaignList, array('empty' => '---', 'class' => 'span7', 'style' => 'margin-left: 10px; margin-right: 10px; margin-top: 10px')); ?></td>
		</tr>
		<tr>
			<td valign="top"><p style="margin-left: 10px; margin-right: 10px; margin-top: 8px">料金</p></td>
			<td valign="top" style="width: 160px; line-height: 250%"><?php echo $this->Form->radio('Campaign.price_input_type', $priceInputType, array('legend' => false, 'div' => false, 'label' => false, 'separator' => '<br>', 'value' => 1, 'style' => 'position: relative; top: -3px; margin-left: 10px; margin-right: 10px')); ?></td>
			<td valign="top">
				<?php echo $this->Form->input('Campaign.price_number', array('label' => false, 'pattern' => '^[1-9]+[0-9]*$', 'class' => 'span4', 'style' => 'vertical-align: top; margin-left: 10px; margin-top: 3px')); ?>
				<?php echo $this->Form->select('Campaign.price_calc_unit', $priceCalcUnit, array('empty' => false, 'class' => 'span3', 'style' => 'margin-top: 3px')); ?>
				<?php echo $this->Form->select('Campaign.price_calc_type', $priceCalcType, array('empty' => false, 'class' => 'span3', 'style' => 'margin-top: 3px')); ?>
			</td>
		</tr>
	</table>
	<?php echo $this->Form->button('新規追加', array('type' => 'button', 'id' => 'AddCampaign', 'class'=>'btn btn-warning')); ?>
</div>

<table id="campaign_list" class="table table-bordered">
	<tr class="success">
		<th class="first-th"></th>
		<th>6時間</th>
		<th>12時間</th>
		<th>24時間</th>
		<th>以後1日</th>
		<th>超過1時間</th>
		<th>公開</th>
	</tr>
	<?php foreach($campaignIds as $id) { ?>
	<tr id="CommodityCampaignPrice<?php echo $id; ?>">
		<td>
		<?php
			echo $campaignList[$id];
			if (!empty($campaignTermList[$id])) {
				foreach ($campaignTermList[$id] as $term) {
					$weekStr = '';
					$weekJp = Constant::weekJp();
					if ($term['mon']) {
						$weekStr .= $weekJp['0'].', ';
					}
					if ($term['tue']) {
						$weekStr .= $weekJp['1'].', ';
					}
					if ($term['wed']) {
						$weekStr .= $weekJp['2'].', ';
					}
					if ($term['thu']) {
						$weekStr .= $weekJp['3'].', ';
					}
					if ($term['fri']) {
						$weekStr .= $weekJp['4'].', ';
					}
					if ($term['sat']) {
						$weekStr .= $weekJp['5'].', ';
					}
					if ($term['sun']) {
						$weekStr .= $weekJp['6'].', ';
					}
					if ($term['hol']) {
						$weekStr .= $weekJp['7'].', ';
					}
					echo '<br>'.$term['start_date'].'～'.$term['end_date'];
					if (!empty($weekStr)) {
						echo '　('.rtrim($weekStr, ', ').')';
					}
				}
			}
 		?>
		</td>
		<?php foreach($commodityScheduleArray as $commoditySchedule) { ?>
		<td>
		<?php echo $this->Form->input("CommodityCampaignPrice.{$id}.{$commoditySchedule}.commodity_price", array(
				'type' => 'number',
				'label' => false,
				'min' => 0,
				'required' => true,
		));
		?>
		</td>
		<?php } ?>
		<td>
		<?php
			echo $this->Html->link('詳細設定', '/commodities/detailTimeSystemCampaign/'.$detailLink.'/'.$id, array('class' => 'btn btn-primary', 'style' => 'margin-bottom: 10px'));
			echo '<br>';
			echo $this->Form->select("CommodityCampaignPrice.{$id}.delete_flg", $isPublishedOptions, array('empty' => false, 'class' => 'span5 campaign_delete', 'style' => 'width:85px'));
		?>
		</td>
	</tr>
	<?php } ?>
</table>

<div>
	<?php echo $this->Form->submit('全て保存',array('name'=>'save','class'=>'btn btn-success', 'div' => false));?>
</div>
<?php echo $this->Form->hidden('CommodityItem.id'); ?>
<?php echo $this->Form->hidden('system', array('value' => 'timeSystem', 'name' => 'system')); ?>
<?php echo $this->Form->hidden('display_time', array('value' => $DisplayTime, 'name' => 'display_time')); ?>
<?php echo $this->Form->end(); ?>

<?php if(!empty($CommodityItemId)) { ?>
	<?php echo $this->Form->postLink(__('削除'), array('action' => 'deleteAll/'.$CommodityItemId.'/'),
			array('class' => 'btn btn-danger', 'div' => false), __('削除してもよろしいでしょうか？')); ?>
<?php } ?>
<div id="commodityId" data-id="<?=$commodityId?>"></div>
<div id="campaignTerm" data-json='<?=$campaignTermJson?>'></div>
<div id="campaignOutOfScope" data-json='<?=$campaignOutOfScopeJson?>'></div>
<div id="weekJp" data-json='<?=json_encode(Constant::weekJp())?>'></div>
<script>
$(function(){
	var commodityId = document.getElementById('commodityId').getAttribute('data-id');
	var campaignTerm = JSON.parse($('#campaignTerm').attr('data-json'));
	var campaignOutOfScope = JSON.parse($('#campaignOutOfScope').attr('data-json'));
	var weekJp = JSON.parse($('#weekJp').attr('data-json'));
	var loaded = false;

	// キャンペーン公開・非公開
	$(document).on('change', '.campaign_delete', function() {
		var rowId = $(this).attr('id').replace('DeleteFlg', '');
		if ($(this).val() == '0') {
			var campaignId = rowId.replace('CommodityCampaignPrice', '');
			if (loaded) {
				if (dateDuplicateCheck(campaignId)) {
					alert('公開中のキャンペーンと期間が重複しています。');
					$(this).val('1');
					return;
				}
			}
			$('#' + rowId).removeClass('deleted');
		} else {
			$('#' + rowId).addClass('deleted');
		}
	});

	// キャンペーン期間重複チェック
	function dateDuplicateCheck(campaignId) {
		var ret = 0;
		$('#campaign_list tr:nth-child(n + 2)').each(function() {
			if (!($(this).hasClass('deleted'))) {
				var id = $(this).attr('id').replace('CommodityCampaignPrice', '');
				if (!dateCompare(campaignId, id)) {
					ret = 1;
					return;
				}
			}
		});
		if (!ret) {
			labelOutOfScope:
			for (var id in campaignOutOfScope) {
				if (!dateCompare(campaignId, id)) {
					ret = 2;
					break labelOutOfScope;
				}
			}
		}
		return ret;
	}
	function dateCompare(addCampaignId, campaignId) {
		var addCampaign = campaignTerm[addCampaignId];
		var campaign = campaignTerm[campaignId];
		for (var addTerms in addCampaign) {
			for (var terms in campaign) {
					if (campaign[terms]['start_date'] <= addCampaign[addTerms]['end_date'] && addCampaign[addTerms]['start_date'] <= campaign[terms]['end_date']) {
						var collision = false;
						for (var day of ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'hol']) {
							if (campaign[terms][day] == 1 && campaign[terms][day] == addCampaign[addTerms][day]) {
								collision = true;
								break;
							}
						}
						return !collision;
					}
				}
			}
		return true;
	}

	// キャンペーン料金追加
	$('#AddCampaign').click(function() {
		// 選択値チェック
		if ($('#CampaignId').val() == '') {
			alert('キャンペーン期間を選択してください。');
			return;
		}
		if ($('#CampaignPriceInputType0').is(':checked')) {
			if ($('#CampaignPriceNumber').val() == '') {
				var unit = $('#CampaignPriceCalcUnit option:selected').text();
				var type = $('#CampaignPriceCalcType option:selected').text();
				alert(type + '値を' + unit + '単位で入力してください。');
				return;
			}
		}

		var campaignId = $('#CampaignId').val();

		// 重複チェック
		var isDuplicated = false;
		var selectedIds = [];
		$('#campaign_list tr:nth-child(n + 2)').each(function() {
			var id = $(this).attr('id').replace('CommodityCampaignPrice', '');
			if (campaignId == id) {
				isDuplicated = true;
				return;
			}
			selectedIds.push(id);
		});
		if (isDuplicated) {
			alert('選択したキャンペーン期間はすでに登録されています。');
			return;
		}
		var ret = dateDuplicateCheck(campaignId);
		if (ret == 1) {
			alert('公開中のキャンペーンと期間が重複しています。');
			return;
		} else if (ret == 2) {
			alert('公開中のキャンペーン（閲覧権限なし）と期間が重複しています。');
			return;
		}

		var schedule = ['6', '12', '24', '0', '25'];
		var addPrice = Number($('#CampaignPriceNumber').val());
		var calcUnit = $('#CampaignPriceCalcUnit').val();
		var calcType = $('#CampaignPriceCalcType').val() == '0' ? 1 : -1;

		// 追加行編集
		var row = '<tr id="CommodityCampaignPrice' + campaignId + '">';

		// キャンペーン名、期間
		var term = '';
		var campaign = campaignTerm[campaignId];
		for (var terms in campaign) {
			var weekStr = '';
			if (campaign[terms]['mon'] == 1) {
				weekStr += weekJp['0']+', ';
			}
			if (campaign[terms]['tue'] == 1) {
				weekStr += weekJp['1']+', ';
			}
			if (campaign[terms]['wed'] == 1) {
				weekStr += weekJp['2']+', ';
			}
			if (campaign[terms]['thu'] == 1) {
				weekStr += weekJp['3']+', ';
			}
			if (campaign[terms]['fri'] == 1) {
				weekStr += weekJp['4']+', ';
			}
			if (campaign[terms]['sat'] == 1) {
				weekStr += weekJp['5']+', ';
			}
			if (campaign[terms]['sun'] == 1) {
				weekStr += weekJp['6']+', ';
			}
			if (campaign[terms]['hol'] == 1) {
				weekStr += weekJp['7']+', ';
			}
			term += '<br>' + campaign[terms]['start_date'] + '～' + campaign[terms]['end_date'];
			if (weekStr != '') {
				weekArray = weekStr.split(',');
				var week = '';
				for (var i=0; i < ((weekArray.length)-1); i++) {
					if (i == ((weekArray.length)-2)) {
						week += weekArray[i];
					} else {						
						week += weekArray[i]+', ';
					}
				}
				term += '　(' + week + ')';
			}
		}
		row += '<td>' + $('#CampaignId option:selected').text() + term + '</td>';
		// 料金
		for (var i = 0; i < schedule.length; i++) {
			var price = '';
			if ($('#CampaignPriceInputType0').is(':checked')) {
				var base = Number($('#CommodityPrice' + schedule[i] + 'CommodityPrice').val());
				if (calcUnit == '0') {
					price = base + calcType * addPrice;
				} else {
					price = base * (100 + calcType * addPrice) / 100;
				}
				if (price < 0) {
					price = 0;
				} else {
					price = Math.floor(price);
				}
			}
			row += '<td><input name="data[CommodityCampaignPrice][' + campaignId + '][' + schedule[i] + '][commodity_price]" min="0" required="required" value="' + price + '" type="number"></td>';
		}
		// 公開/非公開
		row += '<td><select id="CommodityCampaignPrice' + campaignId + 'DeleteFlg" class="span5 campaign_delete" style="width:85px" name="data[CommodityCampaignPrice][' + campaignId + '][delete_flg]"><option value="0">公開</option><option value="1">非公開</option></select></td>';

		row += '</tr>';

		// 行追加
		$('#campaign_list').append(row);
	});

	// 車両クラスセレクトボックス
	$('#CommodityItemCarClassId').change(function() {
		var carClassId = $(this).val();
		setCarModelList(carClassId);
	});
	// 車両クラスに応じて車種セレクトボックスを設定する
 	function setCarModelList(carClassId) {
		$.ajax({
			type: "GET",
			url: "/rentacar/client/commodities/get_car_model_list/" + carClassId + "/",
			success: function(carModel) {
				var carModelList = JSON.parse(carModel);
				var options = new Array(new Option('車種未指定', ''));
				for (key in carModelList) {
					options.push(new Option(carModelList[key], key));
				}
				$('#CommodityItemCarModelId').empty().append(options).change();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
		 		$('#CommodityItemCarModelId').empty();
				//alert(XMLHttpRequest.status);
				//alert(textStatus);
				//alert(errorThrown.message);
			}
		});
		$('#CommodityItemDisclaimer').prop('checked', false).trigger('change');
	}

	$('#CommodityItemCarModelId').change(function() {
		var carClassId = $('#CommodityItemCarClassId').val();
		var carModelId = $(this).val();
		var params = [commodityId, carClassId];
		if (carModelId) {
			params.push(carModelId);
		}

		$.ajax({
			type: "GET",
			url: "/rentacar/client/commodities/get_sipp_code_list/" +  params.join('/') + "/",
			success: function(response) {
				var sippCodeList = JSON.parse(response);
				var sippCodeLetters = [
					[new Option('---', '')], [new Option('---', '')], [new Option('---', '')], [new Option('---', '')]
				];
				for (var i = 0; i < sippCodeList.length; i++) {
					var list = sippCodeList[i];
					for (key in list) {
						sippCodeLetters[i].push(new Option(list[key], key));
					}

				}
				for (var i = 0; i < sippCodeLetters.length; i++) {
					$('#CommodityItemSippCode' + i).empty().append(sippCodeLetters[i]);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
			}
		});
	});

	$('#CommodityItemDisclaimer').change(function() {
		var list = $('#disclaimerList');
		list.empty();

		if (!$(this).is(':checked')) {
			return;
		}

		var carClassId = $('#CommodityItemCarClassId').val();
		
		if (!carClassId) {
			return;
		}

		var carClassName = $('#CommodityItemCarClassId option:selected').text();

		$.ajax({
			type: "GET",
			url: "/rentacar/client/commodities/get_disclaimer_list/" + carClassId + "/",
			success: function(response) {
				var disclaimerList = JSON.parse(response);

				if (!disclaimerList || disclaimerList.length == 0) {
					list.text(carClassName + ' 免責補償料金設定なし');
					return;
				}

				for (var i = 0; i < disclaimerList.length; i++) {
					var obj = disclaimerList[i];
					var price = obj.price.toString().replace(/(\d)(?=(\d{3})+$)/g , '$1,');
					
					var text = carClassName + '　' + price + '円 / ';
					text += (obj.period_flg) ? '1日' : '24時間';
					text += '　(' + obj.start_date + '～' + obj.end_date + ')';
					
					list.append('<p>' + text + '</p>');
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
			}
		});
	});

	$('.campaign_delete').trigger('change');

	loaded = true;
});
</script>

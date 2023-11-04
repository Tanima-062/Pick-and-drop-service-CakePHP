<style>
	input[type="text"],
	input[type="number"] {
		width: 80%;
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		margin: 0;
		padding: 0;
	}

	.table {
		table-layout: fixed;
	}

	.table tr th,
	.table tr td {
		text-align: center;
		border: solid #ddd 1px;
	}
</style>

<h3>暦日制料金設定</h3>

<?php
echo $this->Form->create('CommodityItem', [
	'novalidate' => false,
	'inputDefaults' => [
		'label' => false,
		'div' => false
	]
]);
?>
<div style="margin-bottom: 20px;">
	<div class="error">
		<?php echo $this->Session->flash(); ?>
	</div>
	<p style="color: #317eac; font-weight: bold; font-size: 150%;">
		車両クラス・車種
	</p>
	<p style="margin-left: 10px; font-size: 130%;">
		車種指定プランの場合は車種名を選択してください。
	</p>
	<div class="right">
		<?php echo $this->Html->link('商品編集画面へ戻る', "/Commodities/edit/$commodityId/", array('class' => 'btn btn-warning')); ?>
	</div>
	<div style="margin-bottom: 10px;">
		<?php
		echo $this->Form->input('car_class_id', array(
			'type' => 'select',
			'class' => '',
			'options' => $carClassList,
		));
		?>
		<?php
		echo $this->Form->input('car_model_id', array(
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
	if (!empty($commodityItemId)) {
		echo $this->Form->submit('車両情報のみ保存', ['name' => 'saveCarInfo', 'class' => 'btn btn-success']);
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
	<table id="priceList" class="table table-bordered">
		<colgroup>
			<col>
			<col style="width:45px;">
			<col>
			<col>
			<col>
			<col>
			<col>
			<col style="width:100px;">
		</colgroup>
		<tr class="success">
			<th colspan="3">シーズナリティ</th>
			<th>1泊2日</th>
			<th>2泊3日</th>
			<th>3泊4日</th>
			<th>以降1泊追加ごと</th>
			<th></th>
		</tr>
		<?php foreach ($agentOrganizedPriceList as $index => $agentOrganizedPrice) : ?>
			<tr><?php foreach ($agentOrganizedPrice as $key => $value) :
					$option = (substr($key, -4) === 'date') ? $inputOption['date'] : $inputOption['price'];
					$option['default']  = h($value);

					if ($key === 'id') :
						echo $this->Form->hidden("AgentOrganizedPrice.$index.$key");
					else :
						echo '<td>' . $this->Form->input("AgentOrganizedPrice.$index.$key", $option) . '</td>';
					endif;

					if ($key === 'start_date') echo '<td>〜</td>'; ?>
				<?php endforeach; ?>
				<td>
					<?php echo $this->Form->button(
						'削除',
						[
							'type' => 'button',
							'class' => 'btn btn-danger deletePrice',
							'value' => $agentOrganizedPrice['id']
						]
					); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->Form->button('行を追加', array('type' => 'button', 'id' => 'addPrice', 'class' => 'btn btn-warning')); ?>
	<?php
	if (!empty($commodityItemId)) {
		echo $this->Form->submit('基本料金のみ保存', array('name' => 'savePriceInfo', 'class' => 'btn btn-success'));
	}
	?>
</div>

<div>
	<?php echo $this->Form->submit('全て保存', array('name' => 'saveAll', 'class' => 'btn btn-success', 'div' => false)); ?>
</div>
<?php echo $this->Form->hidden('CommodityItem.id'); ?>
<?php echo $this->Form->hidden('display_time', array('value' => $DisplayTime, 'name' => 'display_time')); ?>
<?php echo $this->Form->end(); ?>

<?php if ($commodityItemId !== 0) {
	echo $this->Form->postLink(
		__('全て削除'),
		array('action' => 'deleteAll', $commodityItemId),
		array('class' => 'btn btn-danger', 'div' => false,),
		__('削除してもよろしいでしょうか？')
	);
} ?>

<div id="commodityId" data-id="<?= $commodityId ?>"></div>

<script>
	$(function() {
		var commodityId = document.getElementById('commodityId').getAttribute('data-id');
		$(document).on('ready', function() {
			// ヘッダ行のみ＝新規登録時：一行目のフォームを作る
			if (priceList.rows.length === 1) {
				apendPriceRow();
			}
			addDatePicker();
		});

		// シーズナリティ追加
		$(document).on('click', '#addPrice', function() {
			apendPriceRow();
			addDatePicker();
		});

		// datepicker適用
		function addDatePicker() {
			const pickeroption = {
				numberOfMonths: 1,
				changeMonth: true,
				changeYear: true,
				monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
				monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
				dayNamesShort: ['日', '月', '火', '水', '木', '金', '土'],
				dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
				showMonthAfterYear: true,
				dateFormat: 'yy-mm-dd',
			};

			$('.datepicker').datepicker(pickeroption);
		}

		// 料金設定行追加
		function apendPriceRow() {
			const index = priceList.rows.length - 1;
			var row = '<tr><td><input name="data[AgentOrganizedPrice][' + index + '][start_date]" class="datepicker">';
			row += '<td>〜</td>'
			row += '<td><input name="data[AgentOrganizedPrice][' + index + '][end_date]" class="datepicker"></td>';
			row += '<td><input name="data[AgentOrganizedPrice][' + index + '][price_stay_1]" class="price"></td>';
			row += '<td><input name="data[AgentOrganizedPrice][' + index + '][price_stay_2]" class="price"></td>';
			row += '<td><input name="data[AgentOrganizedPrice][' + index + '][price_stay_3]" class="price"></td>';
			row += '<td><input name="data[AgentOrganizedPrice][' + index + '][price_stay_over]" class="price">';
			row += '<td><button type="button" class="btn btn-danger deletePrice">削除</td>';
			$('#priceList').append(row);

			$('.datepicker').prop({
				required: true,
				type: 'text'
			});
			$('.price').prop({
				required: true,
				type: 'number',
				min: 0
			});
		}

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
				url: "/rentacar/client/commodities/get_sipp_code_list/" + params.join('/') + "/",
				success: function(response) {
					var sippCodeList = JSON.parse(response);
					var sippCodeLetters = [
						[new Option('---', '')],
						[new Option('---', '')],
						[new Option('---', '')],
						[new Option('---', '')]
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
				error: function(XMLHttpRequest, textStatus, errorThrown) {}
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
						var price = obj.price.toString().replace(/(\d)(?=(\d{3})+$)/g, '$1,');

						var text = carClassName + '　' + price + '円 / ';
						text += (obj.period_flg) ? '1日' : '24時間';
						text += '　(' + obj.start_date + '～' + obj.end_date + ')';

						list.append('<p>' + text + '</p>');
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {}
			});
		});

		// 料金情報 個別に削除
		$(document).on('click', '.deletePrice', function() {
			if (priceList.rows.length === 2) {
				alert('料金は最低1件の登録が必須です。');
				return;
			}
			if ($(this).val() === '') {
				$(this).closest('tr').remove();
				return;
			}
			$.ajax({
					type: 'POST',
					dataType: 'json',
					timeout: 10000,
					url: '/rentacar/client/commodities/deletePrice',
					data: {
						commodityId: commodityId,
						agentOrganizedPriceId: $(this).val()
					}
				})
				.done(function(response) {
					if (response && (response.result === 'success')) {
						alert('削除しました。');
						location.reload();
					} else {
						alert('Error: ' + response.message);
					}
				})
				.fail(function() {
					alert('Fail: システムエラー');
				})
		});
	});
</script>
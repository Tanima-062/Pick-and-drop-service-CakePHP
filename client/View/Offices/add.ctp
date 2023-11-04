<style>
	.table tr th {
		width: 20%;
	}
	.table tr td input,
	.table tr td select,
	.table tr td input[type="number"],
	.table tr td input[type="tel"],
	.table tr td input[type="email"] {
		margin: 0 auto;
	}
	.table tr td input[type="text"] {
		padding: 5px;
	}
	.table tr td textarea {
		width: 100%;
		margin: 0 auto;
		box-sizing: border-box;
	}
</style>
<div class="offices form">
	<?php echo $this->Form->create('Office', array('enctype' => 'multipart/form-data')); ?>

	<h3>営業所追加</h3>
	<table class="table table-bordered">
		<tr>
			<th class="alert-success">営業所コード</th>
			<td><?php echo $this->Form->input('office_code', array('label' => false, 'div' => false)); ?></td>
		</tr>

		<tr>
			<th class="alert-success">営業所名</th>
			<td><?php echo $this->Form->input('name', array('label' => false, 'div' => false, 'required', 'style' => 'width: 80%;')); ?></td>
		</tr>

		<tr>
			<th class="alert-success">画像</th>
			<td>
				<?php
				if (!empty($this->request->data['Office']['image_relative_url'])) {
					echo $this->Html->image('../../img/office/' . $this->data['Office']['id'] . '/' . $this->request->data['Office']['image_relative_url'], array('style' => 'width:80px;'));
				}
				echo $this->Form->input('file', array('type' => 'file', 'label' => false));
				?>
			</td>
		</tr>

		<tr>
			<th class="alert-success">電話番号</th>
			<td><?php echo $this->Form->input('tel', array('label' => false, 'div' => false)); ?></td>
		</tr>
		<?php if ($is_system_admin == 1) { ?>
			<tr>
				<th class="alert-success">郵便番号</th>
				<td><?php echo $this->Form->input('zipcode', array('label' => false, 'div' => false, 'required', 'maxlength' => '7', 'pattern' => '\d{7}')); ?><code>　※ハイフンなしでご入力ください。</code></td>
			</tr>
		<?php } ?>
		<tr>
			<th class="alert-success">住所</th>
			<td><?php echo $this->Form->input('address', array('label' => false, 'div' => false, 'class' => 'span6')); ?></td>
		</tr>
		<tr>
			<th class="alert-success">給油所併設</th>
			<td><?php echo $this->Form->input('adjacent_to_gas_station', array('label' => false, 'div' => false)); ?> <code>※店舗内、もしくは店舗に隣接して給油所がある場合、チェックしてください。</code></td>
		</tr>

		<tr>
			<th  class="alert-success">交通アクセス</th>
			<td>
				<div>
					プレビュー　　　
					<?php echo $this->Form->input('access_dynamic', array('label' => false, 'div' => false, 'class' => 'span6', 'readonly' => 'readonly')); ?>
				</div>

				<br>

				<div>
					最寄り交通機関　　　
					<?php echo $this->Form->input('OfficeSupplement.nearest_transport', array('type' => 'radio', 'options' => $nearestTransportOptions, 'value' => $nearestTransport, 'legend' => false, 'label' => false, 'div' => false, 'required', 'style' => 'display:inline;')); ?>
					<?php echo $this->Form->input('OfficeSupplement.other_transport', array('label' => false, 'div' => false, 'style' => 'display:inline;')); ?>
				</div>

				<br>

				<div>
					交通手段　　　
					<?php echo $this->Form->input('OfficeSupplement.method_of_transport', array('type' => 'radio', 'options' => $methodOfTransportOptions, 'value' => $methodOfTransport, 'legend' => false, 'label' => false, 'div' => false, 'required', 'style' => 'display:inline;')); ?>
				</div>

				<br>

				<div>
					上記交通手段での所要時間　　　
					<?php echo $this->Form->input('OfficeSupplement.required_transport_time', array('min' => 0, 'max' => 999, 'label' => false, 'div' => false, 'required', 'class' => 'span2')); ?>
					<?php echo "分"; ?>
				</div>

				<br>
				<code>※最寄り交通機関の候補が複数ある場合、最も利用される1カ所について選択してください。</code>
			</td>
		</tr>

		<tr id="meet-and-greet">
			<th class="alert-success">送迎</th>
			<td>
				<div>
					送迎対応時間
					<?php echo $this->Form->hour('pickup_from', true, array('empty' => false, 'class' => 'span2')); ?>時
					<?php echo $this->Form->minute('pickup_from', array('empty' => false, 'class' => 'span2')); ?>分
					－
					<?php echo $this->Form->hour('pickup_to', true, array('empty' => false, 'class' => 'span2')); ?>時
					<?php echo $this->Form->minute('pickup_to', array('empty' => false, 'class' => 'span2')); ?>分
				</div>

				<br>

				<div>
					送迎方法　　　
					<?php echo $this->Form->input('OfficeSupplement.pickup_method', array('type' => 'radio', 'options' => $pickupMethodOptions, 'value' => $pickupMethod, 'legend' => false, 'label' => false, 'div' => false, 'style' => 'display:inline;')); ?>
				</div>

				<br>

				<div id="pickup-call">
					電話連絡　　　
					<?php echo $this->Form->input('OfficeSupplement.need_pickup_call', array('type' => 'radio', 'options' => $pickupCallOptions, 'value' => $needPickupCall, 'legend' => false, 'label' => false, 'div' => false, 'style' => 'display:inline;')); ?>
					<br><br>
				</div>

				<div>
					送迎待ち時間　　　
					<?php echo "通常期　"; ?>
					<?php echo $this->Form->input('OfficeSupplement.pickup_wait_time', array('min' => 0, 'max' => 999, 'label' => false, 'div' => false, 'class' => 'span2')); ?>
					<?php echo "分　　繁忙期　"; ?>
					<?php echo $this->Form->input('OfficeSupplement.pickup_wait_time_busy', array('min' => 0, 'max' => 999, 'label' => false, 'div' => false, 'class' => 'span2')); ?>
					<?php echo "分"; ?><br>
				</div>
			</td>
		</tr>

		<tr>
			<th  class="alert-success">出発手続きの所要時間</th>
			<td>
				<?php echo "通常期　"; ?>
				<?php echo $this->Form->input('OfficeSupplement.rent_proc_time', array('min' => 1, 'max' => 999, 'label' => false, 'div' => false, 'required', 'class' => 'span2')); ?>
				<?php echo "分　　繁忙期　"; ?>
				<?php echo $this->Form->input('OfficeSupplement.rent_proc_time_busy', array('min' => 1, 'max' => 999, 'label' => false, 'div' => false, 'required', 'class' => 'span2')); ?>
				<?php echo "分"; ?>
			</td>
		</tr>
		<tr>
			<th  class="alert-success">返却手続きの所要時間</th>
			<td>
				<?php echo "通常期　"; ?>
				<?php echo $this->Form->input('OfficeSupplement.return_proc_time', array('min' => 1, 'max' => 999, 'label' => false, 'div' => false, 'required', 'class' => 'span2')); ?>
				<?php echo "分　　繁忙期　"; ?>
				<?php echo $this->Form->input('OfficeSupplement.return_proc_time_busy', array('min' => 1, 'max' => 999, 'label' => false, 'div' => false, 'required', 'class' => 'span2')); ?>
				<?php echo "分"; ?>
			</td>
		</tr>

		<tr>
			<th class="alert-success">送迎や待ち合わせに関する情報（受取）</th>
			<td><?php echo $this->Form->input('rent_meeting_info', array('label' => false, 'div' => false,)); ?></td>
		</tr>
		<tr>
			<th class="alert-success">送迎や待ち合わせに関する情報（返却）</th>
			<td><?php echo $this->Form->input('return_meeting_info', array('label' => false, 'div' => false,)); ?></td>
		</tr>
		<tr>
			<th class="alert-success">店舗からのご案内</th>
			<td><?php echo $this->Form->input('notification', array('label' => false, 'div' => false,)); ?></td>
		</tr>

		<tr>
			<th class="alert-success">営業時間</th>
			<td>
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="alert-success" colspan="2">詳細設定</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>月</th>
							<td>
								<?php echo $this->Form->hour('mon_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('mon_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('mon_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('mon_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
						<tr>
							<th>火</th>
							<td>
								<?php echo $this->Form->hour('tue_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('tue_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('tue_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('tue_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
						<tr>
							<th>水</th>
							<td>
								<?php echo $this->Form->hour('wed_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('wed_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('wed_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('wed_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
						<tr>
							<th>木</th>
							<td>
								<?php echo $this->Form->hour('thu_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('thu_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('thu_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('thu_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
						<tr>
							<th>金</th>
							<td>
								<?php echo $this->Form->hour('fri_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('fri_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('fri_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('fri_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
						<tr>
							<th>土</th>
							<td>
								<?php echo $this->Form->hour('sat_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('sat_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('sat_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('sat_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
						<tr>
							<th>日</th>
							<td>
								<?php echo $this->Form->hour('sun_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('sun_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('sun_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('sun_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
						<tr>
							<th>祝日</th>
							<td>
								<?php echo $this->Form->hour('hol_hours_from', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('hol_hours_from', array('empty' => '---', 'class' => 'span2')); ?>分
								～
								<?php echo $this->Form->hour('hol_hours_to', true, array('empty' => '---', 'class' => 'span2')); ?>時
								<?php echo $this->Form->minute('hol_hours_to', array('empty' => '---', 'class' => 'span2')); ?>分
							</td>
						</tr>
					</tbody>
				</table>

				<code>　※24時間営業の場合、「0時00分～23時59分」とご入力ください。</code>
			</td>
		</tr>
		<tr>
			<th class="alert-success">営業時間＿補足</th>
			<td><?php echo $this->Form->input('office_hours_remark', array('label' => false, 'div' => false, 'class' => 'span6')); ?><code>（例）送迎対応時間は、9:00～19:00となります。</code></td>
		</tr>
		<tr>
			<th class="alert-success">営業日＿補足</th>
			<td><?php echo $this->Form->input('office_holiday_remark', array('label' => false, 'div' => false, 'class' => 'span6')); ?><code>（例）年中無休 </code></td>
		</tr>

		<tr>
			<th class="alert-success">営業所在庫管理地域</th>
			<td><?php echo $this->Form->input('OfficeStockGroup.stock_group_id', array('label' => false, 'div' => false, 'empty' => '---', 'options' => $stocks, 'required' => true)); ?></td>
		</tr>

		<tr>
			<th  class="alert-success">対応エリア</th>
			<td><?php echo $this->Form->input('Office.area_id', array('label' => false, 'div' => false, 'empty' => '---', 'type' => 'select', 'options' => $area, 'required' => true)); ?></td>
		</tr>
		<?php if ($is_system_admin == 1) { ?>
			<tr>
				<th  class="alert-success">対応市区町村</th>
				<td><?php echo $this->Form->input('city_id', array('label' => false, 'div' => false, 'empty' => false, 'type' => 'select', 'required')); ?><code>　※郵便番号に対応する市区町村が自動的に設定されます。候補が複数ある場合、リストから選択してください。</code></td>
			</tr>
		<?php } ?>
		<tr>
			<th  class="alert-success">乗捨対象エリア</th>
			<td><?php echo $this->Form->input('area_drop_off_id', array('options' => $dropOffAreaList, 'label' => false, 'empty' => true)); ?></td>
		</tr>

		<tr>
			<th  class="alert-success">深夜手数料</th>
			<td><?php echo $this->Form->input('late_night_fee_flg', array('options' => $lateNightFeeList, 'label' => false, 'escape' => false, 'class' => 'span5', 'empty' => true)); ?></td>
		</tr>

		<tr>
			<th class="alert-success">最寄り駅</th>
			<td>
				<?php
				echo $this->Form->input('station_ids', array('label' => false,
					'div' => false,
					'class' => 'station_ids bootstrap-tagsinput',
					'data' => json_encode($officeStations, JSON_UNESCAPED_UNICODE)
				));
				?>
				<code>
					駅名を入力して、自動補完リストから選択してください。
				</code>
			</td>
		</tr>

		<tr>
			<th class="alert-success">最寄り空港・港</th>
			<td><?php echo $this->Form->input('airport_id', array('label' => false, 'options' => $landmarkList, 'div' => false, 'empty' => '---')); ?></td>
		</tr>

		<tr>
			<th  class="alert-success">出発／返却</th>
			<td>
				<?php echo $this->Form->input('accept_rent', array('label' => false, 'div' => false)); ?>
				<?php echo " 出発　　"; ?>
				<?php echo $this->Form->input('accept_return', array('label' => false, 'div' => false)); ?>
				<?php echo " 返却"; ?>
			</td>
		</tr>

		<tr>
			<th  class="alert-success">緯度</th>
			<td><?php echo $this->Form->input('latitude', array('label' => false, 'div' => false, 'pattern' => Constant::PATTERN_GEOCODE, 'required' => true)); ?></td>
		</tr>
		<tr>
			<th  class="alert-success">経度</th>
			<td><?php echo $this->Form->input('longitude', array('label' => false, 'div' => false, 'pattern' => Constant::PATTERN_GEOCODE, 'required' => true)); ?></td>
		</tr>
		<?php if ($is_system_admin == 1) { ?>
			<tr>
				<th  class="alert-success">リンク用URL</th>
				<td><?php echo $this->Form->input('url', array('label' => false, 'div' => false, 'pattern' => Constant::PATTERN_LINKCD, 'required')); ?></td>
			</tr>
		<?php } ?>
		<tr>
			<th class="alert-success">予約完了通知<br />メールアドレス（任意）</th>
			<td><?php echo $this->Form->input('reserve_mail', array('type' => 'email', 'label' => false, 'div' => false)); ?></td>
		</tr>
		<tr>
			<th class="alert-success">予約完了通知<br />メールアドレス　#2（任意）</th>
			<td><?php echo $this->Form->input('reserve_mail2', array('type' => 'email', 'label' => false, 'div' => false)); ?></td>
		</tr>
		<tr>
			<th class="alert-success">予約完了通知<br />メールアドレス #3（任意）</th>
			<td><?php echo $this->Form->input('reserve_mail3', array('type' => 'email', 'label' => false, 'div' => false)); ?></td>
		</tr>
		<tr>
			<th  class="alert-success">削除フラグ</th>
			<td><?php echo $this->Form->input('delete_flg', array('label' => false, 'div' => false)); ?></td>
		</tr>

	</table>
	<?php echo $this->Form->submit('登録する', array('class' => 'btn btn-success')) ?>
	<?php echo $this->Form->end(); ?>
	<?php echo $this->Html->link(__('戻る'), $redirectUrl, array('class' => 'btn btn-warning')); ?>
</div>
<script>
	$(function () {
		var loadFlg = false;

		// 郵便番号テキストボックス
		$('#OfficeZipcode').change(function () {
			var zipcode = $(this).val();
			setCityList(zipcode);
		});
		// 郵便番号に応じて市区町村セレクトボックスを設定する
		function setCityList(zipcode) {
			$('#OfficeCityId').empty();
			$.ajax({
				type: "GET",
				url: "/rentacar/client/Offices/get_city_list/" + zipcode + "/",
				success: function (city) {
					var cityList = JSON.parse(city);
					var options = new Array();
					var cityCount = 0;
					for (key in cityList) {
						options.push(new Option(cityList[key], key));
						cityCount++;
					}
					$('#OfficeCityId').append(options);
					if (cityCount > 1) {
						alert('郵便番号に対応する市区町村が複数存在します。正しい市区町村を選択してください。');
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					//alert(XMLHttpRequest.status);
					//alert(textStatus);
					//alert(errorThrown.message);
				}
			});
		}
		// 送迎方法ラジオボタン
		$('input[name="data[OfficeSupplement][pickup_method]"]').change(function () {
			var method = $(this).val();
			if (method == 0 || method == 3) {
				$('#pickup-call').show();
			} else {
				$('#pickup-call').hide();
				//$('#OfficeSupplement.NeedPickupCall0').prop('checked', true);
			}
		});
		$('#OfficeSupplementPickupMethod<?php echo $pickupMethod; ?>').trigger('change');
		// 動的交通アクセスを設定する
		function setAccessDynamic() {
			var nearestTransport = $('input[name="data[OfficeSupplement][nearest_transport]"]:checked').val();
			if (nearestTransport == 0) {
				var nearestLandmarkId = $('#OfficeAirportId').val();
			} else if (nearestTransport == 1) {
				var stations = $('#OfficeStationIds').val();
				var nearestLandmarkId = stations.split(',')[0];
			}
			var otherTransport = $('#OfficeSupplementOtherTransport').val();
			var methodOfTransport = $('input[name="data[OfficeSupplement][method_of_transport]"]:checked').val();
			var requiredTransportTime = $('#OfficeSupplementRequiredTransportTime').val();
			$.ajax({
				type: "GET",
				url: "/rentacar/client/Offices/get_access_dynamic/?nt=" + nearestTransport + "&nl=" + nearestLandmarkId + "&ot=" + otherTransport + "&mt=" + methodOfTransport + "&rt=" + requiredTransportTime,
				success: function (accessDynamic) {
					$('#OfficeAccessDynamic').val(accessDynamic);
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					//alert(XMLHttpRequest.status);
					//alert(textStatus);
					//alert(errorThrown.message);
				}
			});
		}
		// 最寄り交通機関ラジオボタン
		$('input[name="data[OfficeSupplement][nearest_transport]"]').change(function () {
			var method = $(this).val();
			if (method == 2) {
				$('#OfficeSupplementOtherTransport').show();
				$('#OfficeSupplementOtherTransport').attr('required', 'required');
			} else {
				$('#OfficeSupplementOtherTransport').removeAttr('required');
				$('#OfficeSupplementOtherTransport').hide();
				//$('#OfficeSupplementOtherTransport').val(null);
			}
			if (loadFlg) {
				setAccessDynamic();
			}
		});
		$('#OfficeSupplementNearestTransport<?php echo $nearestTransport; ?>').trigger('change');
		// その他交通機関
		$('#OfficeSupplementOtherTransport').keyup(function () {
			var nearestTransport = $('input[name="data[OfficeSupplement][nearest_transport]"]:checked').val();
			if (nearestTransport == 2) {
				setAccessDynamic();
			}
		});
		// 交通手段ラジオボタン
		$('input[name="data[OfficeSupplement][method_of_transport]"]').change(function () {
			var method = $(this).val();
			if (method == 0 || method == 3) {
				$('#OfficeSupplementPickupWaitTime').removeAttr('required');
				$('#OfficeSupplementPickupWaitTimeBusy').removeAttr('required');
				$('#meet-and-greet').hide();
			} else {
				$('#meet-and-greet').show();
				$('#OfficeSupplementPickupWaitTime').attr('required', 'required');
				$('#OfficeSupplementPickupWaitTimeBusy').attr('required', 'required');
			}
			setAccessDynamic();
		});
		// 所要時間
		$('#OfficeSupplementRequiredTransportTime').on('keyup mouseup', function () {
			setAccessDynamic();
		});
		// 最寄り駅
		$('#OfficeStationIds').change(function () {
			var nearestTransport = $('input[name="data[OfficeSupplement][nearest_transport]"]:checked').val();
			if (nearestTransport == 1) {
				setAccessDynamic();
			}
		});
		// 最寄り空港
		$('#OfficeAirportId').change(function () {
			var nearestTransport = $('input[name="data[OfficeSupplement][nearest_transport]"]:checked').val();
			if (nearestTransport == 0) {
				setAccessDynamic();
			}
		});

		$('#OfficeSupplementOtherTransport').hide();
		$('#meet-and-greet').hide();

		loadFlg = true;
	});
</script>

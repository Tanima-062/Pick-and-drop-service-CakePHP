<!-- 検索フォーム　メインパート TOP以外 -->
<div class="searchform_main_section">

	<div class="search_table">
		<!-- 検索フォーム左側 -->
		<div class="search_main_cell">
			<!-- 出発日時 -->
			<section>
				<p class="search_main_title">出発日時</p>
				<div class="select_datetime_wrap">
					<fieldset class="input-form -small">
						<div class="field-wrap icon-left select_date_box">
							<?php
								echo $this->Form->input('date',array(
									'id'=>'SearchDate',
									'class'=>'calendar datepicker field',
									'readonly'=>'readonly',
									'form'=>'nosend',
									'data-date_id'=>'over_departure_date',
									'default'=>$fromDate,
									'autocomplete'=>'off'
								));
							?>
							<i class="icm-calendar-title -icon"></i>
						</div>
					</fieldset>
					<fieldset class="select-form -small">
						<div class="field-wrap icon-left select_time_box">
							<?php
								echo $this->Form->input('time',array_merge(
									$timeOptions,array(
										'class'=>'field',
										'data-date_id'=>'over_departure_time',
										'default'=>$departureTime,
										'autocomplete'=>'off'
									))
								);
							?>
							<i class="icm-clock -icon"></i>
						</div>
					</fieldset>
				</div>
			</section>

			<!-- 出発場所 -->
			<section class="departure_place_wrap">
				<p class="search_main_title">出発場所</p>
				<!-- タブ -->
				<ul class="select_place_ul">
					<li class="select_place_li">
						<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 3){ echo 'is_selected'; } ?>" data-place="departure_airport" data-radio="3"><i class="select_place_icon icm-ticket-title"></i>空港・港</a>
					</li>
					<li class="select_place_li">
						<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 4){ echo 'is_selected'; } ?>" data-place="departure_station" data-radio="4"><i class="select_place_icon icm-train"></i>駅</a>
					</li>
					<li class="select_place_li">
						<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 1){ echo 'is_selected'; } ?>" data-place="departure_prefecture" data-radio="1"><i class="select_place_icon icm-location"></i>エリア</a>
					</li>
				</ul>
				<div id="departure_place_form" class="select_place_form">
					<ol id="departure_prefecture" class="select_place_ol">
						<li id="js-prefectureOptions">
							<label>
								<?php
									echo $this->Form->input('prefecture',array_merge(
										$prefectureOptions,array('id'=>'prefecture', 'autocomplete'=>'off')
									));
								?>
								<span class="dept-place-selected-pref">都道府県を選択してください</span>
							</label>
						</li>
						<li>
							<label>
								<p id="area_id">
									<?php 
										echo $this->Form->input('area_id', array_merge($areaOptions, array('autocomplete'=>'off'))); 
									?>
								</p>
								<span id="area_placeholder" class="disabled_placeholder">地区を選択してください</span>
								<i class="fa fa-unsorted"></i>
							</label>
						</li>
					</ol>
					<ol id="departure_airport" class="select_place_ol">
						<li>
							<label for="SearchAirportId">
								<p id="airport_id">
									<?php 
										echo $this->Form->input('airport_id', array_merge($airportInStockOptions, array('autocomplete'=>'off'))); 
									?>
								</p>
								<i class="fa fa-unsorted"></i>
							</label>
						</li>
						<li></li>
					</ol>
					<ol id="departure_station" class="select_place_ol">
						<li>
							<label>
								<p id="station_id"></p>
								<?php 
									echo $this->Form->input('station_id', array_merge($stationOptions, array('autocomplete'=>'off'))); 
								?>
								<span id="station_placeholder" class="disabled_placeholder">駅を選択してください</span>
								<i class="fa fa-unsorted"></i>
							</label>
						</li>
					</ol>
				</div>
			</section>
		</div>

		<div class="search_arrow_cell">
			<i class="icm-arrow"></i>
		</div>

		<!-- 検索フォーム検索フォーム中央-->
		<div class="search_main_cell">
			<!-- 返却日時 -->
			<section>
				<p class="search_main_title">返却日時</p>
				<div id="" class="select_datetime_wrap">
					<fieldset class="input-form -small">
						<div class="field-wrap icon-left select_date_box">
							<?php
								echo $this->Form->input('return_date',array(
									'id'=>'SearchReturnDate',
									'class'=>'calendar datepicker field',
									'readonly'=>'readonly',
									'form'=>'nosend',
									'data-date_id'=>'over_return_date',
									'default'=>$toDate,
									'autocomplete'=>'off'
								));
							?>
							<i class="icm-calendar-title -icon"></i>
						</div>
					</fieldset>
					<fieldset class="select-form -small">
						<div class="field-wrap icon-left select_time_box">
							<?php
								echo $this->Form->input('return_time',array_merge(
									$timeOptions,array(
										'class'=>'field',
										'data-date_id'=>'over_return_time',
										'default'=>$returnTime,
										'autocomplete'=>'off'
									)
								));
							?>
							<i class="icm-clock -icon"></i>
						</div>
					</fieldset>
				</div>
			</section>
			<section>
				<p class="search_main_title">返却場所</p>
				<div class="search_select_return">
					<!-- フォーム return_way -->
					<ul class="select_place_ul">
						<li class="select_place_li">
							<a href="javascript:void(0);" class="select_place_tab tab_return" data-place="return_way_airport" data-radio="3"><i class="select_place_icon icm-ticket-title"></i>空港・港</a>
						</li>
						<li class="select_place_li">
							<a href="javascript:void(0);" class="select_place_tab tab_return" data-place="return_way_station" data-radio="4"><i class="select_place_icon icm-train"></i>駅</a>
						</li>
						<li class="select_place_li">
							<a href="javascript:void(0);" class="select_place_tab tab_return" data-place="return_way_prefecture" data-radio="1"><i class="select_place_icon icm-location"></i>エリア</a>
						</li>
					</ul>
					<div id="return_way_form" class="select_place_form">
						<ol id="return_way_prefecture" class="select_return_ol">
							<li id="js-returnPrefectureOptions">
								<label>
									<?php
										echo $this->Form->input('return_prefecture',array_merge(
											$returnPrefectureOptions,array(
												'id'=>'return_prefecture',
												'autocomplete'=>'off'
											)
										));
									?>
									<span class="return-place-selected-pref">北海道</span>
								</label>
							</li>
							<li>
								<label>
									<p id="return_area_id">
										<?php
											echo $this->Form->input('return_area_id',array_merge(
												$returnAreaOptions,array(
													'disabled'=>false,
													'autocomplete'=>'off'
												)
											));
										?>
									</p>
									<span id="return_area_placeholder" class="disabled_placeholder">地区を選択してください</span>
									<i class="fa fa-unsorted"></i>
								</label>
							</li>
						</ol>
						<ol id="return_way_airport" class="select_return_ol">
							<li>
								<label>
									<p id="return_airport_id">
										<?php
											echo $this->Form->input('return_airport_id',array_merge($returnAirportInStockOptions, array('autocomplete'=>'off')));
										?>
									</p>
									<i class="fa fa-unsorted"></i>
								</label>
							</li>
							<li></li>
						</ol>
						<ol id="return_way_station" class="select_return_ol">
							<li>
								<label>
									<p id="return_station_id">
										<?php
											echo $this->Form->input('return_station_id',array_merge(
												$returnStationOptions,array(
													'disabled'=>false,
													'autocomplete'=>'off'
												)
											));
										?>
									</p>
									<span id="return_station_placeholder" class="disabled_placeholder">駅を選択してください</span>
									<i class="fa fa-unsorted"></i>
								</label>
							</li>
						</ol>
					</div>
				</div>
				<div>
					<input id="return_way_check" class="form-checkbox" type="checkbox" value="1" checked="checked" autocomplete="off"/>
					<label for="return_way_check">
							出発店舗へ返却
							<p class="return_way_check_text">※乗り捨て希望の方はチェックを外してください</p>
					</label>
				</div>
			</section>
		</div>
<?php
    // 検索結果はtable-cellをもうひとつ追加
	if (strstr($this->request->params['controller'], 'searches')){
?>
		<div id="search_select_smoking" class="search_main_cell">
			<p class="search_main_title">タバコ</p>
			<ul class="smoking_ul" role="radiogroup">
				<li class="smoking_li">
					<button type="button" class="btn_radio_smoking" data-smoking="0" role="radio" aria-checked="false"><i class="icm-no_smoking"></i>禁煙車</button>
				</li>
				<li class="smoking_li">
					<button type="button" class="btn_radio_smoking" data-smoking="1" role="radio" aria-checked="true"><i class="icm-smoking"></i>喫煙車</button>
				</li>
				<li class="smoking_li">
					<button type="button" class="btn_radio_smoking" data-smoking="2" role="radio" aria-checked="true"><i class="icm-thumbsup"></i>気にしない</button>
				</li>
			</ul>
		</div>
<?php
	}
?>
	</div>

	<div class="hidden_radio_box">
<?php
	echo $this->Form->input('place',array_merge($borrowPlaceOptions,array('class'=>'borrow_place','default'=>$borrowPlace, 'autocomplete'=>'off')));
	echo $this->Form->input('return_way',array_merge($returnWayOptions, array('autocomplete'=>'off')));
	echo $this->Form->input('return_place',array_merge($borrowPlaceOptions,array('class'=>'return_place','disabled'=>'disabled','default'=>$returnPlace, 'autocomplete'=>'off')));
	echo $this->Form->input('year',array_merge($yearOptions,array('name'=>'year', 'default'=>$departureYear, 'autocomplete'=>'off')));
	echo $this->Form->input('month',array_merge($monthOptions,array('name'=>'month', 'default'=>$departureMonth, 'autocomplete'=>'off')));
	echo $this->Form->input('day',array_merge($dayOptions,array('name'=>'day','default'=>$departureDay, 'autocomplete'=>'off')));
	echo $this->Form->input('return_year',array_merge($yearOptions,array('default'=>$returnYear, 'autocomplete'=>'off')));
	echo $this->Form->input('return_month',array_merge($monthOptions,array('default'=>$returnMonth, 'autocomplete'=>'off')));
	echo $this->Form->input('return_day',array_merge($dayOptions,array('default'=>$returnDay, 'autocomplete'=>'off')));
	if (strstr($this->request->params['controller'], 'searches')) {
		echo $this->Form->input('smoking_flg', array_merge($smokingOptions, array('autocomplete'=>'off')));
	}
	if ($fromRentacarClient) {
		echo $this->Form->hidden('from_rentacar_client', array('value' => 'true', 'autocomplete'=>'off'));
		echo $this->Form->hidden('client_id', array('value' => $client_id, 'autocomplete'=>'off'));
	} elseif ($this->params['controller'] == 'company' && $this->action == 'index') {
		echo $this->Form->hidden('client_id', array('value' => $clientInfo['Client']['id'], 'autocomplete'=>'off'));
	} elseif (isset($client_id_from_company_page)) {
		echo $this->Form->hidden('client_id', array('value' => $client_id_from_company_page, 'autocomplete'=>'off'));
	}
?>
	</div>
</div>

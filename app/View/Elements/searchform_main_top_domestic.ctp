<!-- 検索フォーム　メインパート TOP -->
<div class="searchform_main_top_domestic_section">

	<div class="search_table">
		<!-- 検索フォーム左側 -->
		<div class="search_main_cell">
			<!-- 出発日時 -->
			<section>
				<p class="search_main_title">出発日時</p>
				<div class="select_datetime_wrap">
					<fieldset class="input-form -small">
						<div class="field-wrap icon-left">
							<?php
								echo $this->Form->input('date',array(
									'id'=>'SearchDate',
									'class'=>'calendar datepicker field',
									'readonly'=>'readonly',
									'form'=>'nosend',
									'data-date_id'=>'over_departure_date',
									'default'=>$fromDate
								));
							?>
							<i class="icm-calendar-title -icon"></i>
						</div>
					</fieldset>
					<fieldset class="select-form -small">
						<div class="field-wrap icon-left">
							<?php
								echo $this->Form->input('time',array_merge(
									$timeOptions,array(
										'class'=>'field',
										'data-date_id'=>'over_departure_time',
										'default'=>$departureTime
									))
								);
							?>
							<i class="icm-right-arrow"></i>
							<i class="icm-clock -icon"></i>
						</div>
					</fieldset>
				</div>
			</section>
			<!-- 出発場所 -->
			<section>
				<p class="search_main_title">出発場所</p>
				<!-- タブ -->
				<ul class="select_place_ul">
					<li class="select_place_li">
						<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 3){ echo 'is_selected'; } ?>" data-place="departure_airport" data-radio="3"><span class="select_place_text"><i class="select_place_icon icm-ticket-title"></i>空港・港</span></a>
					</li>
					<li class="select_place_li">
						<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 4){ echo 'is_selected'; } ?>" data-place="departure_station" data-radio="4"><span class="select_place_text"><i class="select_place_icon icm-train"></i>駅</span></a>
					</li>
					<li class="select_place_li">
						<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 1){ echo 'is_selected'; } ?>" data-place="departure_prefecture" data-radio="1"><span class="select_place_text"><i class="select_place_icon icm-location"></i>エリア</span></a>
					</li>
				</ul>
				<div id="departure_place_form" class="select_place_form">
					<div id="departure_prefecture" class="select_place_ol select_area_item_2col">
						<fieldset id="js-prefectureOptions" class="select-form -small">
							<div class="field-wrap">
								<?php
									echo $this->Form->input('prefecture',array_merge(
										$prefectureOptions,array('id'=>'prefecture','class'=>'field')
									));
								?>
								<span class="dept-place-selected-pref">都道府県</span>
								<i class="icm-right-arrow"></i>
							</div>
						</fieldset>
						<fieldset class="select-form -small">
							<div class="field-wrap">
								<?php
									echo $this->Form->input('area_id',array_merge(
										$areaOptions,array('class'=>'field')
									));
								?>
								<span id="area_placeholder" class="disabled_placeholder">エリア</span>
								<i class="icm-right-arrow"></i>
							</div>
						</fieldset>
					</div>
					<div id="departure_airport" class="select_place_ol select_area_item_1col">
						<fieldset class="select-form -small">
							<div class="field-wrap">
								<?php
									echo $this->Form->input('airport_id',array_merge(
										$airportInStockOptions,array('class'=>'field')
									));
								?>
								<i class="icm-right-arrow"></i>
							</div>
						</fieldset>
					</div>
					<div id="departure_station" class="select_place_ol select_area_item_2col">
						<fieldset class="select-form -small">
							<div class="field-wrap">
								<?php
									echo $this->Form->input('station_id',array_merge(
										$stationOptions,array('class'=>'field')
									));
								?>
								<span id="station_placeholder" class="disabled_placeholder">駅</span>
								<i class="icm-right-arrow"></i>
							</div>
						</fieldset>
					</div>
				</div>
			</section>
		</div>

		<div class="search_arrow_cell">
			<i class="icm-arrow"></i>
		</div>

		<!-- 検索フォーム検索フォーム右側 -->
		<div class="search_main_cell">
			<!-- 返却日時 -->
			<section>
				<p class="search_main_title">返却日時</p>
				<div class="select_datetime_wrap">
					<fieldset class="input-form -small">
						<div class="field-wrap icon-left">
							<?php
								echo $this->Form->input('return_date',array(
									'id'=>'SearchReturnDate',
									'class'=>'calendar datepicker field',
									'readonly'=>'readonly',
									'form'=>'nosend',
									'data-date_id'=>'over_return_date',
									'default'=>$toDate
								));
							?>
							<i class="icm-calendar-title -icon"></i>
						</div>
					</fieldset>
					<fieldset class="select-form -small">
						<div class="field-wrap icon-left">
							<?php
								echo $this->Form->input('return_time',array_merge(
									$timeOptions,array(
										'class'=>'field',
										'data-date_id'=>'over_return_time',
										'default'=>$returnTime
									)
								));
							?>
							<i class="icm-right-arrow"></i>
							<i class="icm-clock -icon"></i>
						</div>
					</fieldset>
				</div>
			</section>
			<!-- 返却場所 -->
			<section>
				<!-- タブ -->
				<p class="search_main_title">返却場所</p>
				<div class="search_select_return">
					<ul class="select_place_ul">
						<li class="select_place_li">
							<a href="javascript:void(0);" class="select_place_tab select_place_tab_item_1 tab_return" data-place="return_way_airport" data-radio="3"><span class="select_place_text"><i class="select_place_icon icm-ticket-title"></i>空港・港</span></a>
						</li>
						<li class="select_place_li">
							<a href="javascript:void(0);" class="select_place_tab select_place_tab_item_2 tab_return" data-place="return_way_station" data-radio="4"><span class="select_place_text"><i class="select_place_icon icm-train"></i>駅</span></a>
						</li>
						<li class="select_place_li">
							<a href="javascript:void(0);" class="select_place_tab select_place_tab_item_3 tab_return" data-place="return_way_prefecture" data-radio="1"><span class="select_place_text"><i class="select_place_icon icm-location"></i>エリア</span></a>
						</li>
					</ul>
					<!-- 選択エリア -->
					<div id="return_way_form" class="select_place_form">
						<div id="return_way_prefecture" class="select_return_ol select_area_item_2col">
							<fieldset id="js-returnPrefectureOptions" class="select-form -small">
								<div class="field-wrap">
									<?php
										echo $this->Form->input('return_prefecture',array_merge(
											$returnPrefectureOptions,array(
												'id'=>'return_prefecture',
												'class'=>'field'
											)
										));
									?>
									<span class="return-place-selected-pref">北海道</span>
									<i class="icm-right-arrow"></i>
								</div>
							</fieldset>
							<fieldset class="select-form -small">
								<div class="field-wrap">
									<?php
										echo $this->Form->input('return_area_id',array_merge(
											$returnAreaOptions,array(
												'disabled'=>false,
												'class'=>'field'
											)
										));
									?>
									<span id="return_area_placeholder" class="disabled_placeholder">エリア</span>
									<i class="icm-right-arrow"></i>
								</div>
							</fieldset>
						</div>
						<div id="return_way_airport" class="select_return_ol select_area_item_1col">
							<fieldset class="select-form -small">
								<div class="field-wrap">
									<?php
										echo $this->Form->input('return_airport_id',array_merge(
											$returnAirportInStockOptions,array(
												'class'=>'field'
											)
										));
									?>
									<i class="icm-right-arrow"></i>
								</div>
							</fieldset>
						</div>
						<div id="return_way_station" class="select_return_ol select_area_item_2col">
							<fieldset class="select-form -small">
								<div class="field-wrap">
									<?php
										echo $this->Form->input('return_station_id',array_merge(
											$returnStationOptions,array(
												'disabled'=>false,
												'class'=>'field'
											)
										));
									?>
									<span id="return_station_placeholder" class="disabled_placeholder">駅</span>
									<i class="icm-right-arrow"></i>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
				<div>
					<fieldset class="checkbox-form -default -searchform">
						<input id="return_way_check" class="form-checkbox" type="checkbox" name="checkbox" value="1" checked="checked" />
						<label for="return_way_check" class="label">
							出発店舗へ返却
							<p class="return_way_check_text">※乗り捨て希望の方はチェックを外してください</p>
						</label>	
					</fieldset>
				</div>
			</section>
		</div>
	</div>
	<div class="hidden_radio_box">
<?php
	echo $this->Form->input('place',array_merge($borrowPlaceOptions,array('class'=>'borrow_place','default'=>$borrowPlace)));
	echo $this->Form->input('return_way',$returnWayOptions);
	echo $this->Form->input('return_place',array_merge($borrowPlaceOptions,array('class'=>'return_place','disabled'=>'disabled','default'=>$returnPlace)));
	echo $this->Form->input('year',array_merge($yearOptions,array('name'=>'year', 'default'=>$departureYear)));
	echo $this->Form->input('month',array_merge($monthOptions,array('name'=>'month', 'default'=>$departureMonth)));
	echo $this->Form->input('day',array_merge($dayOptions,array('name'=>'day','default'=>$departureDay)));
	echo $this->Form->input('return_year',array_merge($yearOptions,array('default'=>$returnYear)));
	echo $this->Form->input('return_month',array_merge($monthOptions,array('default'=>$returnMonth)));
	echo $this->Form->input('return_day',array_merge($dayOptions,array('default'=>$returnDay)));
	if (strstr($this->request->params['controller'], 'searches')) {
		echo $this->Form->input('smoking_flg', $smokingOptions);
	}
?>
	</div>

</div>
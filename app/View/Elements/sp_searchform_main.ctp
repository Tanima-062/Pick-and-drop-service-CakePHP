<!-- 検索フォーム　メインパート TOP以外 -->
<div class="searchform_main_section">

	<p class="searchform_main_title">出発日時と場所を選択</p>
	<section class="searchform_main_datetime">
		<!-- 出発日時 -->
		<div class="select_datetime_wrap">
			<fieldset class="input-form -small">
				<div class="field-wrap icon-left select_date_box">
					<?php
						echo $this->Form->input('date',array(
							'id'=>'SearchDate',
							'class'=>'calendar datepicker field',
							'readonly'=>'readonly',
							// 'form'=>'nosend',
							'data-date_id'=>'over_departure_date',
							'default'=>$fromDate
						));
					?>
				</div>
			</fieldset>
			<fieldset class="select-form -small">
				<div class="field-wrap icon-left select_time_box">
					<?php
						echo $this->Form->input('time',array_merge(
							$timeOptions,array(
								'class'=>'field',
								'data-date_id'=>'over_departure_time',
								'default'=>$departureTime
							))
						);
					?>
				</div>
			</fieldset>
		</div>

		<div class="arrow-wrap">
			<i class="icm-arrow"></i>
		</div>

		<!-- 返却日時 -->
		<div id="" class="select_datetime_wrap">
			<fieldset class="input-form -small">
				<div class="field-wrap icon-left select_date_box">
					<?php
						echo $this->Form->input('return_date',array(
							'id'=>'SearchReturnDate',
							'class'=>'calendar datepicker field',
							'readonly'=>'readonly',
							// 'form'=>'nosend',
							'data-date_id'=>'over_return_date',
							'default'=>$toDate
						));
					?>
				</div>
			</fieldset>
			<fieldset class="select-form -small">
				<div class="field-wrap icon-left select_time_box">
					<?php
						echo $this->Form->input('return_time',array_merge(
							$timeOptions,array(
								'class'=>'field',
								'data-date_id'=>'over_return_time',
								'default'=>$returnTime
							)
						));
					?>
				</div>
			</fieldset>
		</div>
	</section>

	<hr class="search_select_hr" />
	<div class="search_select_departure">
		<!-- フォーム place -->
		<ul class="select_place_ul">
			<li class="select_place_li">
				<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 4){ echo 'is_selected'; } ?>" data-place="departure_station" data-radio="4"><i class="select_place_icon icm-train"></i><span class="select_place_tab_text">駅</span></a>
			</li>
			<li class="select_place_li">
				<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 3){ echo 'is_selected'; } ?>" data-place="departure_airport" data-radio="3"><i class="select_place_icon icm-ticket-title"></i><span class="select_place_tab_text">空港・港</span></a>
			</li>
			<li class="select_place_li">
				<a href="javascript:void(0);" class="select_place_tab tab_departure <?php if($borrowPlace == 1){ echo 'is_selected'; } ?>" data-place="departure_prefecture" data-radio="1"><i class="select_place_icon icm-location"></i><span class="select_place_tab_text">エリア</span></a>
			</li>
		</ul>
		<div id="departure_place_form" class="select_place_form">
			<ol id="departure_airport" class="select_place_ol">
				<li>
					<label for="SearchAirportId">
						<p id="airport_id">
<?php
	echo $this->Form->input('airport_id',array_merge($airportInStockOptions));
?>
						</p>
						<i class="fa fa-unsorted"></i>
					</label>
				</li>
			</ol>
			<ol id="departure_prefecture" class="select_place_ol">
				<li id="js-prefectureOptions">
					<label>
<?php
	echo $this->Form->input('prefecture',array_merge($prefectureOptions,array('id'=>'prefecture')));
?>
						<span class="dept-place-selected-pref">都道府県を選択してください</span>
					</label>
				</li>
				<li>
					<label>
						<p id="area_id">
<?php
	echo $this->Form->input('area_id',$areaOptions);
?>
						</p>
						<span id="area_placeholder" class="disabled_placeholder">地区を選択してください</span>
						<i class="fa fa-unsorted"></i>
					</label>
				</li>
			</ol>
			<ol id="departure_station" class="select_place_ol">
				<li>
					<label>
						<p id="station_id"></p>
<?php
	echo $this->Form->input('station_id',$stationOptions);
?>
						<span id="station_placeholder" class="disabled_placeholder">駅を選択してください</span>
						<i class="fa fa-unsorted"></i>
					</label>
				</li>
			</ol>
		</div>
	</div>
	<div class="return_way_checkbox">
		<input id="return_way_check" class="form-return_way_input form-checkbox" type="checkbox" value="1" checked="checked" />
		<label for="return_way_check">
				出発店舗へ返却
				<p>※乗り捨て希望の方はチェックを外してください</p>
		</label>
	</div>

	<div class="search_select_return">
		<p class="searchform_main_title">返却場所を選択</p>
		<ul class="select_place_ul">
			<li class="select_place_li">
				<a href="javascript:void(0);" class="select_place_tab tab_return" data-place="return_way_station" data-radio="4"><i class="select_place_icon icm-train"></i><span class="select_place_tab_text">駅</span></a>
			</li>
			<li class="select_place_li">
				<a href="javascript:void(0);" class="select_place_tab tab_return" data-place="return_way_airport" data-radio="3"><i class="select_place_icon icm-ticket-title"></i><span class="select_place_tab_text">空港・港</span></a>
			</li>
			<li class="select_place_li">
				<a href="javascript:void(0);" class="select_place_tab tab_return" data-place="return_way_prefecture" data-radio="1"><i class="select_place_icon icm-location"></i><span class="select_place_tab_text">エリア</span></a>
			</li>
		</ul>

		<div id="return_way_form" class="select_place_form">
			<ol id="return_way_airport" class="select_return_ol">
				<li>
					<label>
						<p id="return_airport_id">
<?php
	echo $this->Form->input('return_airport_id',array_merge($returnAirportInStockOptions));
?>
						</p>
						<i class="fa fa-unsorted"></i>
					</label>
				</li>
			</ol>
			<ol id="return_way_prefecture" class="select_return_ol">
				<li id="js-returnPrefectureOptions">
					<label>
<?php
	echo $this->Form->input('return_prefecture',array_merge($returnPrefectureOptions,array('id'=>'return_prefecture')));
?>
						<span class="return-place-selected-pref">北海道</span>
					</label>
				</li>
				<li>
					<label>
						<p id="return_area_id">
<?php
	echo $this->Form->input('return_area_id',array_merge($returnAreaOptions,array('disabled'=>false)));
?>
						</p>
						<span id="return_area_placeholder" class="disabled_placeholder">地区を選択してください</span>
						<i class="fa fa-unsorted"></i>
					</label>
				</li>
			</ol>
			<ol id="return_way_station" class="select_return_ol">
				<li>
					<label>
						<p id="return_station_id">
<?php
	echo $this->Form->input('return_station_id',array_merge($returnStationOptions,array('disabled'=>false)));
?>
						</p>
						<span id="return_station_placeholder" class="disabled_placeholder">駅を選択してください</span>
						<i class="fa fa-unsorted"></i>
					</label>
				</li>
			</ol>
		</div>

		<div class="hidden_radio_box" style="display:none;">
<?php
	echo $this->Form->radio('place',array('3'=>'空港検索'),array('id'=>'checkbox-airport','label'=>false,'hiddenField'=>false,'default'=>$borrowPlace));
	echo $this->Form->radio('place',array('1'=>'都道府県検索'),array('id'=>'checkbox-area','label'=>false,'hiddenField'=>false,'default'=>$borrowPlace));
	echo $this->Form->radio('place',array('4'=>'駅検索'),array('id'=>'checkbox-area','label'=>false,'hiddenField'=>false,'default'=>$borrowPlace));
	/** 
	 * @param return_way に関して（忘れそうなのでコメントに保存）
	 * spの場合はpcと違って返却方法状態(return_way)が4つある。PC=0:出発店舗に返却 1:乗り捨て利用 / SP=0:出発店舗に返却 1:乗り捨て(都道府県) 2:乗り捨て(新幹線) 3:乗り捨て(空港)
	 * pcはreturn_way, return_placeを分けて使うけど、spはreturn_wayにreturn_placeの状態も含まれるからreturn_wayが4つになったらしい
	 * $returnWayはPCに合わせて0か1しか返ってこない。return_wayの初期値は$returnWayの代わりにSearchesControllerの193行目で$spReturnPlaceに$returnWayを代入しているのでそれを使う。 
	*/
	echo $this->Form->radio('return_way',array('0'=>'出発店舗へ返す'),array('label'=>false,'hiddenField'=>false,'default'=>$spReturnPlace));
	echo $this->Form->radio('return_way',array('3'=>'乗り捨て（空港検索）'),array('label'=>false,'hiddenField'=>false,'default'=>$spReturnPlace));
	echo $this->Form->radio('return_way',array('1'=>'乗り捨て（都道府県検索）'),array('label'=>false,'hiddenField'=>false,'default'=>$spReturnPlace));
	echo $this->Form->radio('return_way',array('4'=>'乗り捨て（駅検索）'),array('label'=>false,'hiddenField'=>false,'default'=>$spReturnPlace));

	if ($fromRentacarClient) {
		echo $this->Form->hidden('from_rentacar_client', array('value' => 'true'));
		echo $this->Form->hidden('client_id', array('value' => $client_id));
	} elseif ($this->params['controller'] == 'company' && $this->action == 'sp_index') {
		echo $this->Form->hidden('client_id', array('value' => $clientInfo['Client']['id']));
	} elseif (isset($client_id_from_company_page)) {
		echo $this->Form->hidden('client_id', array('value' => $client_id_from_company_page));
	}

?>
		</div>
	</div>
</div>
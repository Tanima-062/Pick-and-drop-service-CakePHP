<!-- 検索フォーム　メインパート TOP -->

<div class="searchform_main_top_oversea_section">

	<section>
		<div class="search-popularity">
			<p class="form-block-title">人気の都市から探す</p>
			<ul class="-grid">
				<li>
					<input type="radio" name="select-popularity" value="honolulu" id="select-popularity_1">
					<label for="select-popularity_1">ホノルル</label>
				</li>
				<li>
					<input type="radio" name="select-popularity" value="kona" id="select-popularity_2">
					<label for="select-popularity_2">コナ</label>
				</li>
				<li>
					<input type="radio" name="select-popularity" value="waikiki" id="select-popularity_3">
					<label for="select-popularity_3">ワイキキ</label>
				</li>
				<li>
					<input type="radio" name="select-popularity" value="bangkok" id="select-popularity_4">
					<label for="select-popularity_4">バンコク</label>
				</li>
				<li>
					<input type="radio" name="select-popularity" value="las-vegas" id="select-popularity_5">
					<label for="select-popularity_5">ラスベガス</label>
				</li>
			</ul>
		</div>
	</section>

	<section class="-grid grid-item-center mt-1">
		<div class="search-datetime">
			<p class="form-block-title">出発日時</p>
			<ul>
				<li class="select-date">
					<i class="icm-calendar-title -icon"></i>
<?php
	echo $this->Form->input('date',array('id'=>'pickUpDate','class'=>'calendar datepicker','readonly'=>'readonly','form'=>'nosend','data-date_id'=>'over_departure_date_oversea','default'=>$fromDate));
?>
				</li>
				<li class="select-time">
					<i class="fa fa-clock-o -icon"></i>
<?php
	echo $this->Form->input('time',array_merge($timeOptions,array('id'=>'pickUpTime','class'=>'','data-date_id'=>'over_departure_time_oversea','default'=>$departureTime)));
?>
				</li>
			</ul>
		</div>
		<div class="-arrow">
			<i class="icm-arrow -icon"></i>
		</div>
		<div class="search-datetime">
			<p class="form-block-title">返却日時</p>
			<ul>
				<li class="select-date">
					<i class="icm-calendar-title -icon"></i>
<?php
	echo $this->Form->input('return_date',array('id'=>'dropOffDate','class'=>'calendar datepicker','readonly'=>'readonly','form'=>'nosend','data-date_id'=>'over_return_date_oversea','default'=>$toDate));
?>
				</li>
				<li class="select-time">
					<i class="fa fa-clock-o -icon"></i>
<?php
	echo $this->Form->input('return_time',array_merge($timeOptions,array('id'=>'dropOffTime','class'=>'','data-date_id'=>'over_return_time_oversea','default'=>$returnTime)));
?>
				</li>
			</ul>
		</div>
	</section>
	<section class="mt-1">
		<div class="search-place">
			<p class="form-block-title">出発場所</p>
			<div>
				<i class="icm-location -icon"></i>
				<div class="-grid">
					<label class="select-country">
						<select id="pickUpCountry" name="select-pickup-country">
							<option value="0">国を選択してください</option>
						</select>
					</label>
					<label class="select-city disabled">
						<select id="pickUpCity" name="select-pickup-city">
							<option value="0">都市を選択してください</option>
						</select>
					</label>
					<label class="select-area disabled">
						<select id="pickUpLocation" name="select-pickup-location">
							<option value="0">エリアを選択してください</option>
						</select>
					</label>
				</div>
			</div>
		</div>
		<div class="search-place mt-1" id="return-place">
			<p class="form-block-title">返却場所</p>
			<div>
				<i class="icm-location -icon"></i>
				<div class="-grid">
					<label class="return-select-country disabled">
						<div id="dropOffLoading" class="-loading">
							<span class="icon-spinner"></span>
						</div>
						<select id="dropOffCountry" name="select-dropoff-country" disabled>
							<option value="0">国を選択してください</option>
						</select>
					</label>
					<label class="return-select-city disabled">
						<select id="dropOffCity" name="select-dropoff-city">
							<option value="0">都市を選択してください</option>
						</select>
					</label>
					<label class="return-select-area disabled">
						<select id="dropOffLocation" name="select-dropoff-location">
							<option value="0">エリアを選択してください</option>
						</select>
					</label>
				</div>
			</div>
		</div>
		<div class="mt-1">
			<input id="return_way_check_oversea" class="form-checkbox" type="checkbox" value="1" checked="checked" />
			<label for="return_way_check_oversea">
				出発店舗へ返却
			　<span class="font-size-tiny">※乗り捨て希望の方はチェックを外してください</span>
			</label>
		</div>
	</section>
	<section class="mt-1">
		<input id="driver-age" class="form-checkbox" type="checkbox" value="1" checked="checked" />
		<label for="driver-age">
			ドライバー年齢：30〜65歳
		</label>
	</section>
</div>
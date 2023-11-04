<!-- 検索フォーム　メインパート TOP -->

<div class="searchform_main_top_oversea_section">

	<section>
		<div class="search-popularity -cover">
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
		<!-- 出発日時 -->
		<div class="select_datetime_wrap">
			<fieldset class="input-form -small">
				<div class="field-wrap icon-left select_date_box">
				<?php
					echo $this->Form->input('date',array(
						'id'=>'pickUpDate',
						'class'=>'calendar datepicker field',
						'readonly'=>'readonly',
						'data-date_id'=>'over_departure_date_oversea',
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
							'id'=>'pickUpTime', 
							'class'=>'field',
							'data-date_id'=>'over_departure_time_oversea',
							'default'=>$departureTime
						)
					));
				?>
				</div>
			</fieldset>
		</div>

		<div class="arrow-wrap">
			<i class="icm-arrow"></i>
		</div>

		<!-- 返却日時 -->
		<div class="select_datetime_wrap">
			<fieldset class="input-form -small">
				<div class="field-wrap icon-left select_date_box">
					<?php
						echo $this->Form->input('return_date',array(
							'id'=>'dropOffDate',
							'class'=>'calendar datepicker field',
							'readonly'=>'readonly',
							'data-date_id'=>'over_return_date_oversea',
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
								'id'=>'dropOffTime',
								'class'=>'field',
								'data-date_id'=>'over_return_time_oversea',
								'default'=>$returnTime
							)
						));
					?>
				</div>
			</fieldset>
		</div>
	</section>

	<section class="mt-1">
		<div class="search-place">
			<p class="form-block-title">出発場所</p>
			<div>
				<ul>
					<li class="select_country">
						<label class="form-select">
							<select id="pickUpCountry" name="select-pickup-country">
								<option value="0">国を選択してください</option>
							</select>
						</label>
					</li>
					<li class="select_city mt-1">
						<label class="form-select disabled">
							<select id="pickUpCity" name="select-pickup-city">
								<option value="0">都市を選択してください</option>
							</select>
						</label>
					</li>
					<li class="select_area mt-1">
						<label class="form-select disabled">
							<select id="pickUpLocation" name="select-pickup-location">
								<option value="0">エリアを選択してください</option>
							</select>
						</label>
					</li>
				</ul>
			</div>
		</div>
		<!-- フォーム return_way -->
		<div class="search-place" id="return-place">
			<p class="form-block-title">返却場所</p>
			<div>
				<ul>
					<li class="select_country">
						<label class="return-select-country form-select">
							<div id="dropOffLoading" class="-loading">
								<span class="icon-spinner -icon"></span>
							</div>
							<select id="dropOffCountry" name="select-dropoff-country">
								<option value="0">国を選択してください</option>
							</select>
						</label>
					</li>
					<li class="select_city mt-1">
						<label class="return-select-city form-select disabled">
							<select id="dropOffCity" name="select-dropoff-city">
								<option value="0">都市を選択してください</option>
							</select>
						</label>
					</li>
					<li class="select_area mt-1">
						<label class="return-select-location form-select disabled">
							<select id="dropOffLocation" name="select-dropoff-location">
								<option value="0">エリアを選択してください</option>
							</select>
						</label>
					</li>
				</ul>
			</div>
		</div>
		<div class="checkbox-section">
			<div>
				<input id="return_way_check_oversea" class="form-checkbox" type="checkbox" value="1" checked="checked" />
				<label for="return_way_check_oversea">
						出発店舗へ返却
				</label>
				<p class="option-text">※乗り捨て希望の方はチェックを外してください</p>
			</div>
			<div class="mt-1">
				<input id="driver-age" class="form-checkbox" type="checkbox" value="1" checked="checked" />
				<label for="driver-age">
						ドライバー年齢：30〜65歳 <span class="question-title icon-btn" id="oven-driverage"></span>
				</label>
				<p class="option-text">
					ドライバー年齢が30〜65歳に該当しない場合は、チェックを外して検索してください。ドライバーの年齢によってレンタカー会社から追加料金を請求される場合があります。
				</p>
			</div>
		</div>
	</section>

	<div class="hidden_radio_box" style="display:none;">
<?php
	echo $this->Form->radio('place',array('3'=>'空港検索'),array('id'=>'checkbox-airport','label'=>false,'hiddenField'=>false,'default'=>$borrowPlace));
	echo $this->Form->radio('place',array('1'=>'都道府県検索'),array('id'=>'checkbox-area','label'=>false,'hiddenField'=>false,'default'=>$borrowPlace));
	echo $this->Form->radio('place',array('4'=>'駅検索'),array('id'=>'checkbox-area','label'=>false,'hiddenField'=>false,'default'=>$borrowPlace));
	echo $this->Form->radio('return_way',array('0'=>'出発店舗へ返す'),array('label'=>false,'hiddenField'=>false,'default'=>0));
	echo $this->Form->radio('return_way',array('3'=>'乗り捨て（空港検索）'),array('label'=>false,'hiddenField'=>false,'default'=>$returnWay));
	echo $this->Form->radio('return_way',array('1'=>'乗り捨て（都道府県検索）'),array('label'=>false,'hiddenField'=>false,'default'=>$returnWay));
	echo $this->Form->radio('return_way',array('4'=>'乗り捨て（駅検索）'),array('label'=>false,'hiddenField'=>false,'default'=>$returnWay));
?>
	</div>
	
</div>
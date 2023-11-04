<?php 
	echo $this->Html->css(array('swiper.min'), null, array('inline' => false));
	echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]); 
	echo $this->Html->script('swiper.min', array('inline' => false));
?>

<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
	$type = $activeWebp ? '.webp' : '.png';
?>
	<div class="cont_page_head">
		<h1 class="page_mainhead"><?=$prefectureData[0]['pre-head']?></h1>
		<span class="page_subhead"></span>
	</div>
	
	<ul class="link_in_page">
		<li class="link_in_page_li"><a href="#01" class="link_in_page_a">地図</a></li>
		<li class="link_in_page_li"><a href="#02" class="link_in_page_a">レンタカー会社</a></li>
<?php
	if( !empty($yotpoReviews) ){
?>
		<li class="link_in_page_li"><a href="#reviews" class="link_in_page_a">口コミ</a></li>
<?php
	}
?>
		<li class="link_in_page_li"><a href="#04" class="link_in_page_a">駅一覧</a></li>
<?php
	if( !empty($airportLinkCdList) ){
?>
		<li class="link_in_page_li"><a href="#05" class="link_in_page_a">空港一覧</a></li>
<?php
	}
?>
		<li class="link_in_page_li"><a href="#06" class="link_in_page_a">地域一覧</a></li>
		<li class="link_in_page_li"><a href="#07" class="link_in_page_a">レンタカー情報</a></li>
	</ul>

	<?php echo $this->element('pc_campaign_banner_hokkaido'); ?>

	<section class="content_section">
		<?php
			echo $this->Form->create('Search',array('action'=>'index', 'inputDefaults'=>array(
				'label'=>false,
				'div'=>false,
				'hiddenField'=>false,
				'legend'=>false,
				'fieldset'=>false,
			),'type'=>'get','class'=>'contents_search'));
		?>
			<?php
				//出発日時や返却日時などは共通項目のためエレメント化
				echo $this->element('searchform_main');
			?>
			<div class="searchform_submit_section_wrap">
					
				<?php echo $this->element('searchform_submit'); ?>

			</div>
		<?php
			echo $this->Form->end();
		?>
	</section>

	<h2 id="01" class="page_h3"><?=$prefectureName;?>のレンタカーをエリア、空港、主要駅から探す</h2>
	<section class="pref_cont_wrap">
		<?php echo $this->element('pc_areamap', ['alt_text' => $prefectureName]); ?>
	</section>

	<hr class="page_hr" />

	<h2 id="02" class="page_h3"><?=$prefectureName;?>で人気のレンタカー会社から探す</h2>
	<section class="pref_cont_wrap">
<?php
	if(!empty($pickupClientList)){
?>
		<dl>
<?php
		foreach($pickupClientList as $pickupClientId){
			$pickUpClientData = $clientList[$pickupClientId];
?>
			<dt class="pickup_company_dt">
				<a href="/rentacar/company/<?=$pickUpClientData['url']?>/" class="btn_pickup_company">
<?php
			if(!empty($pickUpClientData['sp_logo_image'])){
?>
					<img src="/rentacar/img/logo/square/<?=$pickupClientId;?>/<?=$pickUpClientData['sp_logo_image']; ?>" alt="<?=$pickUpClientData['name'];?>のロゴ" height="48" width="48" class="company_img" loading="lazy" importance="low" decoding="async">
<?php
			}
?>
					<h3 class="pickup_company_name"><?=$pickUpClientData['name'];?></h3>
<?php
			if( !empty($clientRatings[$pickUpClientData['id']]) ){
				$clientRatingsData = $clientRatings[$pickUpClientData['id']];
?>
					<span class="company_rate_star">
<?php
				for($i=1;$i<6;$i++){
					if($i <= $clientRatingsData['rating']){
						echo '<i class="fa fa-star"></i>';
					}else if($i-1 <= $clientRatingsData['rating']){
						echo '<i class="fa fa-star-half-o"></i>';
					}else{
						echo '<i class="fa fa-star-o"></i>';
					}
				}
?>
					</span>
					<span class="company_rate_count"><?=$clientRatingsData['rating'];?>（<?=$clientRatingsData['count'];?>件）</span>
<?php
			}
?>
					<i class="fa fa-angle-right"></i>
				</a>
			</dt>
			<dd class="pickup_company_dd">
				<div class="pickup_company_table">
					<div class="pickup_company_cell">
						<img src="<?=$clientPlanImages[$pickupClientId];?>?imwdith=280" alt="<?=$pickUpClientData['name'];?>でお取り扱いのある車両の参考写真" class="pref_pickup_car_img" width="280" height="auto" loading="lazy" importance="low" decoding="async"/>
					</div>
					<p class="pickup_company_cell"><?=$clientContents[$pickupClientId];?></p>
				</div>
				<div class="pickup_company_table">
					<div class="pickup_store_cell">
						<h4 class="pickup_store_title"><?=$prefectureName;?>で人気の店舗</h4>
					</div>
					<div class="pickup_company_cell">
						<ul class="link_cont_ul">
<?php
			foreach($officeListForPickupClients[$pickupClientId] as $pickupOfficeData){
?>
							<li class="link_cont_li">
								<i class="fa fa-caret-right"></i>&nbsp;<a href="/rentacar/company/<?=$pickUpClientData['url']?>/<?=$pickupOfficeData['url']?>/"><?=$pickupOfficeData['name'];?></a>
							</li>
<?php
			}
?>
						</ul>
					</div>
				</div>
				<div class="pickup_store_search">
					<a href="/rentacar/company/<?=$pickUpClientData['url']?>/" class="btn_search_store"><?=$pickUpClientData['name'];?>の店舗を探す</a>
				</div>
			</dd>
<?php
		}
?>
		</dl>
<?php
	}
?>
		<ul class="company_search_ul">
<?php
	foreach($clientList as $clientData){
		if( !empty($clientData['sp_logo_image']) ){
?>
			<li class="company_search_li">
				<a href="/rentacar/company/<?=$clientData['url']?>/" class="company_search_li_link"><img src="/rentacar/img/logo/square/<?=$clientData['id'];?>/<?=$clientData['sp_logo_image'];?>" alt="<?=$clientData['name'];?>のロゴ" height="45" width="45" class="company_img" loading="lazy" importance="low" decoding="async"/>&nbsp;<span class="company_search_name"><?=$clientData['name'];?></span></a>
			</li>
<?php
		}
	}
?>
		</ul>
	</section>

<!-- プラン掲載 -->
<?php
	if(!empty($landmarkRanking)) {
?>
	<div class="ranking">
		<h2><?=$prefectureName;?>の人気エリアランキング</h2>
<?php
		unset( $landmarkRanking[3] );
		foreach ($landmarkRanking as $rankingNum => $ranking) {
?>
		<div>
			<ul>
				<li class="popular_area_ranking_item">
					<h3>
						<span class="ranking-title"><i class="icm-royal-crown -icon"></i> <?=$rankingNum +1 ?>位　<?=$ranking['name']?></span>
					</h3>
					<div class="ranking-inner">
<?php
			foreach ($ranking['bestPriceCarTypes'] as $carTypeId => $bestPriceCarTypes) {
				if ($rankingNum == 0) {
					if($carTypeId == 1) {
						$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1 kei" alt="軽自動車" width="120" height="82.8"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2 compact" alt="コンパクト" width="150" height="82.5">';
					} else if($carTypeId == 3) {
						$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1 hybrid" alt="ハイブリッド" width="150" height="86"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2 sedan" alt="セダン" width="150" height="64.5">';
					} else if($carTypeId == 9) {
						$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1 miniban" alt="ミニバン" width="150" height="82"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2 wagon" alt="ワゴン" width="150" height="82.5">';
					}
				} else {
					$carImg = '';
				}

				if ($carTypeId == 9) {
					$carCapacity = $typeCapacityList[$carTypeId]."人乗り～";
				} else {
					$carCapacity = $typeCapacityList[$carTypeId]."人乗り";
				}
?>
						<a href="<?=$bestPriceCarTypes['url']?>">
							<p class="-carModels"><?=$bestPriceCarTypes['name']?></p>
							<p class="carImg"><?=$carImg?></p>
							<div class="bottom">
								<p><span class="carCapacity"><?=$carCapacity;?></span><span class="-marker">最安値</span><span class="-price"><?=$bestPriceCarTypes['price']?></p>
							</div>
						</a>
<?php
			}
?>
					</div>
				</li>
			</ul>
		</div>
<?php
		}
?>
	</div>
<?php
	}
?>
<!-- プラン掲載ここまで -->

	<div class="pricelist">
		<h2><?=$prefectureName;?>の人気エリアでレンタカー最安値を探す</h2>

		<table>
			<tr>
				<th>エリア</th>
				<th class="-cartype">車両タイプ</th>
				<th class="-client">レンタカー会社</th>
				<th class="-current_month"><?=$currentMonth;?>月最安値</th>
				<th class="-next_month"><?=$nextMonth;?>月最安値</th>
			</tr>

<?php
	foreach ($bestPriceAreas as $areaname => $areaTable) {
?>
			<tr>
				<td><a href="<?=$areaTable['url'];?>"><?=$areaname;?>（<?=$areaTable['plan_count'];?>件）</a></td>
				<td colspan="4" class="no-padding">
<?php
		if ($areaTable['plan_count'] !== 0) {
			foreach ($areaTable['price_info'] as $carType => $carTypeList){
?>
					<table>
						<tr>
							<td class="-cartype"><?=($carTypeList['car_type_name']);?></td>
							<td class="-client"><?=($carTypeList['current_month']['client_name']);?></td>
							<td class="-current_month"><?=($carTypeList['current_month']['best_price']);?></td>
							<td class="-next_month"><?=($carTypeList['next_month']['best_price']);?></td>
						</tr>
					</table>
<?php
			}
		}
?>
				</td>
			</tr>
<?php
	}
?>

		</table>

	</div>

<?php
	if( !empty($yotpoReviews) ){
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
	<hr class="page_hr" />
			
	<section id="reviews" class="yotpo_api_custom_wrap">
		<h2 class="review_section_title">口コミから<?=$prefectureName;?>のレンタカーを探す</h2>
		<?php echo $this->element('yotpo_review'); ?>
	</section>
			
<?php 
		}
	}
?>
	
<?php
	if(!empty($prefectureData)){
		foreach($prefectureData as $prefecture) {
			if( !empty($prefecture['pre-head']) ){
?>
	<hr class="page_hr" />

	<h2 id="07" class="page_h3"><?=$prefectureName;?>のレンタカー情報</h2>
<?php
			}
?>
	<section class="pref_cont_wrap">
		<h3 class="pref_article_title"><?=$prefecture['head-s']?></h3>
		<div class="pref_article_table">
			<div class="pref_article_table_cell">
				<img src="/rentacar/wp/img/<?=$prefecture['img']?>" alt="<?=$prefecture['head-s']?>" width="300" height="auto" />
			</div>
			<div class="pref_article_table_cell">
				<p><?=$prefecture['text']?></p>
			</div>
		</div>
	</section>
<?php
		}
	}
?>
	<hr class="page_hr" />

	<h2 id="04" class="page_h3"><?=$prefectureName;?>内のレンタカーをすべての駅名から探す</h2>
	<section id="js_station_list" class="variable_cont_area">
		<div class="variable_cont_wrap">

<?php
	$station_counter = 0;
	foreach($stationListGroupByArea['areas'] as $GroupByAreaData) {
		$station_counter++;
?>
			<div <?php if($station_counter > 2){ ?> class="variable_cont_hidden" <?php } ?>>
				<h3 class="pref_cont_title"><?= $GroupByAreaData['name'] ?></h3>
				<ul class="pref_link_cont_ul">
<?php
		foreach( $GroupByAreaData['stations'] as $stationByAreaKey => $stationByAreaData ){
?>
					<li class="link_cont_li">
						<i class="fa fa-caret-right"></i>&nbsp;<a href="/rentacar/<?= $baseUrl . $stationByAreaData['url']?>/"><?= $stationByAreaData['name'] . $stationByAreaData['type'] ?></a>
					</li>
<?php
		}
?>
				</ul>
			</div>
<?php
	}
?>
		</div>
<?php
	if($station_counter > 2){
?>
		<div class="variable_cont_more">
			<a href="javascript:void(0);" class="btn_more_cont" data-contents="js_station_list"><i class="fa fa-caret-down"></i>&nbsp;もっと見る</a>
		</div>
<?php
	}
?>
	</section>

<?php
	if( !empty($airportLinkCdList) ){
?>
	<hr class="page_hr" />

	<h2 id="05" class="page_h3"><?=$prefectureName;?>内のレンタカーを空港から探す</h2>
	<section class="variable_cont_area">
		<ul class="pref_link_cont_ul">
<?php
		foreach($airportLinkCdList as $k => $v) {
?>
			<li class="link_cont_li">
				<i class="fa fa-caret-right"></i>&nbsp;<a href="/rentacar/<?= $baseUrl . $k?>/"><?= $v['name'] ?></a>
			</li>
<?php
		}
?>
		</ul>
	</section>
<?php
	}
?>

	<hr class="page_hr" />

	<h2 id="06" class="page_h3"><?=$prefectureName;?>内のレンタカーを市区町村から探す</h2>
	<section id="js_city_list" class="variable_cont_area">
		<div class="variable_cont_wrap">
<?php
	$city_counter = 0;
	foreach($cityListGroupByArea['areas'] as $areaKey => $areaData) {
		$city_counter++
?>
			<div<?php if($city_counter > 2){ ?> class="variable_cont_hidden" <?php } ?>>
				<h3 class="pref_cont_title"><?= $areaData['name'] ?></h3>
				<ul class="pref_link_cont_ul">
<?php
		foreach($areaData['cities'] as $cityData){
?>
					<li class="link_cont_li">
						<i class="fa fa-caret-right"></i>&nbsp;<a href="/rentacar/<?= $baseUrl . $cityData['link_cd'];?>/"><?= $cityData['name']; ?></a>
					</li>
<?php
		}
?>
				</ul>
			</div>
<?php
	}
?>
		</div>
<?php
	if($city_counter > 2){
?>
		<div class="variable_cont_more">
			<a href="javascript:void(0);" class="btn_more_cont" data-contents="js_city_list"><i class="fa fa-caret-down"></i>&nbsp;もっと見る</a>
		</div>
<?php
	}
?>
	</section>

	<hr class="page_hr" />

	<h2 class="page_h3">レンタカーを全国の都道府県から探す</h2>
	<section>
		<div class="pref_all_list_col2">
<?php
	$region_counter = 0;
	foreach($prefectureListGroupByRegion as $allPrefKey => $allPrefData) {
		$region_counter++;
?>
			<h3 class="pref_cont_title"><?=$regions[$allPrefKey];?></h3>
			<ul class="pref_link_cont_ul">
<?php
		foreach($allPrefData as $prefKey => $prefData){
			$allPrefUrl = $allPrefKey."/".$prefKey;
			if($prefKey === "hokkaido" || $prefKey === "okinawa"){
				$allPrefUrl = $prefKey;
			}
?>
				<li class="link_cont_li">
					<i class="fa fa-caret-right"></i>&nbsp;<a href="/rentacar/<?=$allPrefUrl;?>/"><?=$prefData;?></a>
				</li>
<?php
		}
?>
			</ul>
<?php
		if( $region_counter == 5 ){
?>
		</div>
		<div class="pref_all_list_col2">
<?php
		}
	}
?>
		</div>
	</section>
	<hr class="page_hr" />
	
	<?php echo $this->element('popular_airport_linklist'); ?>
</div>

<script>
$(function(){
	// もっと見る
	var heightStationList = $("#js_station_list > .variable_cont_wrap").height();
	if( $("#js_station_list").has(".variable_cont_more").length > 0 ){
		heightStationList += 30;
	}
	$("#js_station_list").height( heightStationList );
	var heightCityList= $("#js_city_list > .variable_cont_wrap").height();
	if( $("#js_city_list").has(".variable_cont_more").length > 0 ){
		heightCityList += 30;
	}
	$("#js_city_list").height( heightCityList );
	var heightListAfter = 0;

	$(".btn_more_cont").on("click", function(){
		var contId = $(this).data("contents");

		$(this).toggleClass("show_more");
		$("#"+contId+" .variable_cont_hidden").toggle();
		heightListAfter = $("#"+contId+" > .variable_cont_wrap").height();
		$("#"+contId).height( heightListAfter + 30 );

		var isOpen = $(this).hasClass("show_more");
		switchMoreBtn(this, isOpen);
	});

	// もっと見るボタン 表示切り替え
	var switchMoreBtn = function(objBtn, isOpen){
		if( isOpen ){
			$(objBtn).html('<i class="fa fa-caret-up"></i>&nbsp;閉じる');
		}else{
			$(objBtn).html('<i class="fa fa-caret-down"></i>&nbsp;もっと見る');
		}
	};

	// レビュー続きを読む
	$(".btn_more_review").on("click", function(){
		var review_id = $(this).parents("li").attr("id");
		var objReviewAll = $("#"+review_id+" .review_cont_all");
		var objReviewOmmit = $("#"+review_id+" .review_cont_ommit");

		$(this).hide().attr("aria-expanded", false);
		objReviewOmmit.hide().attr("aria-hidden", true);
		objReviewAll.show().attr("aria-hidden", false);
	});
});
</script>

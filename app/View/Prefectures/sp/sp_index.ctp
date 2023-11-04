<link rel="stylesheet" type="text/css" href="/rentacar/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery-ui.css">
<script type="text/javascript" src="/rentacar/js/swiper.min.js"></script>

<div id="contents_top">
	<h1 class="contents_top_title"><?= $prefectureName; ?>の格安レンタカーを比較・予約する</h1>
</div>

<?php echo $this->element('sp_campaign_banner_hokkaido'); ?>

<section id="search" class="search_section">
	<?php
		echo $this->Form->create('Search',array(
			'action'=>'index',
			'inputDefaults'=>array(
				'label'=>false,
				'div'=>false,
				'hiddenField'=>false,
				'legend'=>false,
				'fieldset'=>false
			),
			'type'=>'get'
		));
	?>
		<?php
			//出発日時や返却日時などは共通項目のためエレメント化
			echo $this->element('sp_searchform_main');
		?>
		<div class="searchform_submit_section_wrap">
			<?php echo $this->element('sp_searchform_submit'); ?>
		</div>
	<?php
		echo $this->Form->end();
	?>
</section>

<hr class="page_hr" />

<section class="pricelist">
	<h2 class="page_h2"><?=$prefectureName;?>のレンタカーをエリア、空港、主要駅から探す</h2>
	<dl class="accordion_dl">
		<dt class="accordion_dt">
			<a href="javascript:void(0);" aria-expanded="false" role="tab">
				<h3><?=$prefectureName;?>のエリアからレンタカーを探す</h3>
			</a>
			<i class="fa fa-angle-down"></i>
		</dt>
		<dd class="accordion_dd" aria-hidden="true">
			<ul class="accordion_ul">
<?php
	foreach ($areaList AS $areaId => $areaData) {
		$padAreaId = sprintf('%03d', $areaId);
?>
				<li class="accordion_li">
					<a href="/rentacar/<?= $baseUrl . $areaData['area_link_cd']?>/"><?= $areaData['name']; ?></a>
					<i class="fa fa-angle-right"></i>
				</li>
<?php
	}
?>
			</ul>
		</dd>
<?php
	if (!empty($airportLinkCdList)) {
?>
		<dt class="accordion_dt">
			<a href="javascript:void(0);" aria-expanded="false" role="tab">
				<h3><?=$prefectureName;?>の空港からレンタカーを探す</h3>
			</a>
			<i class="fa fa-angle-down"></i>
		</dt>
		<dd class="accordion_dd" aria-hidden="true">
			<ul class="accordion_ul">
<?php
		foreach($airportLinkCdList AS $k => $v) {
?>
				<li class="accordion_li">
					<a href="/rentacar/<?= $baseUrl . $k?>/"><span><?= $v['name'] ?></span></a>
					<i class="fa fa-angle-right"></i>
				</li>
<?php
		}
?>
			</ul>
		</dd>
<?php
	}
	if (!empty($majorStationList)) {
?>
		<dt class="accordion_dt">
			<a href="javascript:void(0);" aria-expanded="false" role="tab">
				<h3><?=$prefectureName;?>の主要駅からレンタカーを探す</h3>
			</a>
			<i class="fa fa-angle-down"></i>
		</dt>
		<dd class="accordion_dd" aria-hidden="true">
			<ul class="accordion_ul">
<?php
		foreach ($majorStationList AS $majorStationData) {
?>
				<li class="accordion_li">
					<a href="/rentacar/<?= $baseUrl . $majorStationData['url']?>/"><span><?= $majorStationData['name'] . $majorStationData['type']; ?></span></a>
					<i class="fa fa-angle-right"></i>
				</li>
<?php
		}
?>
			</ul>
		</dd>
<?php
	}
?>
	</dl>
</section>

<section class="contents_text">
	<p><?=$prefectureData[0]['pre-head-text']?></p>
</section>

<hr class="page_hr" />

<section class="pricelist">
	<h2 class="page_h2"><?=$prefectureName;?>で人気のレンタカー会社から探す</h2>

	<?php echo $this->element('sp_companylist_accordion'); ?>

</section>

<!-- プラン掲載 -->
<?php
	if (!empty($landmarkRanking)) {
?>
<section class="ranking">
	<h2><?=$prefectureName;?>の人気エリアランキング</h2>
<?php
		unset( $landmarkRanking[3] );
		foreach ($landmarkRanking as $rankingNum => $ranking) {
?>
	<div class="ranking-list ranking<?=$rankingNum +1;?>">
		<h3><span class="ranking-title"><i class="icm-royal-crown -icon"></i> <?=$rankingNum +1 ?>位　<?=$ranking['name']?></span></h3>

<!-- ランキング1位 -->
<?php
			if ($rankingNum == 0) {
?>
		<div class="swiper-container">
			<div class="swiper-wrapper" id="sliderSet">
<?php
				$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
				$type = $activeWebp ? '.webp' : '.png';
				foreach ($ranking['bestPriceCarTypes'] as $carTypeId => $bestPriceCarTypes) {
					if ($rankingNum == 0) {
						if($carTypeId == 1) {
							$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1 kei" alt="軽自動車" width="120" height="82.8" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2 compact" alt="コンパクト" width="150" height="82.5" loading="lazy" importance="low" decoding="async">';
						} else if($carTypeId == 3) {
							$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1 hybrid" alt="ハイブリッド" width="150" height="86" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2 sedan" alt="セダン"width="150" height="64.5" loading="lazy" importance="low" decoding="async">';
						} else if($carTypeId == 9) {
							$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1 miniban" alt="ミニバン" width="150" height="82" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2 wagon" alt="ワゴン" width="150" height="82.5" loading="lazy" importance="low" decoding="async">';
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

				<a href="<?=$bestPriceCarTypes['url']?>" class="swiper-slide">
					<p class="-carModels"><?=$bestPriceCarTypes['name']?></p>
					<p class="carImg"><?=$carImg?></p>
					<div class="bottom">
						<p><span class="carCapacity"><?=$carCapacity;?></span><span class="-marker">最安値</span><span class="-price"><?=$bestPriceCarTypes['price']?></p>
					</div>
				</a>
<?php
				}
?>

			</div><!--swiper-wrapper-->
			<div class="swiper-pagination"></div>
		</div><!--swiper-container-->
<?php
			}
?>
<!-- ランキング1位ここまで -->
<!-- ランキング2.3位 -->
<?php
			foreach ($ranking['bestPriceCarTypes'] as $carTypeId => $bestPriceCarTypes) {
				if ($carTypeId == 9) {
					$carCapacity = $typeCapacityList[$carTypeId]."人乗り～";
				} else {
					$carCapacity = $typeCapacityList[$carTypeId]."人乗り";
				}
?>
<?php
				if ($rankingNum !== 0) {
?>
		<a href="<?=$bestPriceCarTypes['url']?>">
			<p class="-carModels"><?=$bestPriceCarTypes['name']?></p>
			<div class="bottom">
				<p><span class="carCapacity"><?=$carCapacity;?></span><span class="-marker">最安値</span><span class="-price"><?=$bestPriceCarTypes['price']?></p>
			</div>
		</a>
<?php
				}
			}
?>

<!-- ランキング2.3位ここまで -->
	</div>
<?php
		}
?>
</section>
<?php
	}
?>
<!-- プラン掲載ここまで -->

<hr class="page_hr" />

	<section class="pricelist">
		<h2><?=$prefectureName;?>の人気エリアでレンタカー最安値を探す</h2>
<?php
	foreach ($bestPriceAreas as $areaname => $areaTable) {
?>
		<div class="pricelist-item">
			<a href="<?=$areaTable['url'];?>">
				<p><span class="areaname"><?=$areaname;?></span>（<?=$areaTable['plan_count'];?>件）</p>
				<p><i class="icm-right-arrow"></i></p>
			</a>
<?php
		if ($areaTable['plan_count'] !== 0) {
?>
			<table>
				<tr>
					<th class="-cartype">車両タイプ</th>
					<!-- <th class="-client">レンタカー会社</th> -->
					<th class="-current_month"><?=$currentMonth;?>月最安値</th>
					<th class="-next_month"><?=$nextMonth;?>月最安値</th>
				</tr>
<?php
			foreach ($areaTable['price_info'] as $carType => $carTypeList) {
?>
				<tr>
					<td class="-cartype"><?=($carTypeList['car_type_name']);?></td>
					<!-- <td class="-client"><?=($carTypeList['current_month']['client_name']);?></td> -->
					<td class="-current_month"><?=($carTypeList['current_month']['best_price']);?></td>
					<td class="-next_month"><?=($carTypeList['next_month']['best_price']);?></td>
				</tr>
<?php
			}
?>
			</table>
<?php
		}
?>
		</div>
<?php
	}
?>
	</section>

<hr class="page_hr" />

<?php
	if(!empty($yotpoReviews) ){
		// YOTPO
		if ($yotpo_is_active && $use_yotpo) {
?>
	<section id="reviews" class="yotpo_api_custom_wrap">
		<h2 class="review_section_title">口コミから<?=$prefectureName;?>のレンタカーを探す</h2>
		<?php echo $this->element('sp_yotpo_review'); ?>
	</section>
<?php
		}
	}
?>

<hr class="page_hr" />

<?php
	if (!empty($prefectureData)) {
		foreach ($prefectureData AS $prefecture) {
			if (!empty($prefecture['pre-head'])) {
?>
<div class="article_section_wrap pricelist">
	<h2 class="page_h2"><?= $prefectureName; ?>のレンタカー情報</h2>
<?php
			}
?>
	<section class="article_section">
		<h3 class="article_title"><?=$prefecture['head-s']?></h3>
		<div class="article_wrap">
			<div class="article_body">
				<div class="article_img">
					<img src="/rentacar/wp/img/<?=$prefecture['img']?>" alt="<?=$prefecture['head-s']?>" width="100%" height="auto"  loading="lazy" importance="low" decoding="async"/>
				</div>
				<p class="article_cont"><?=$prefecture['text']?></p>
			</div>
		</div>
	</section>
<?php
		}
	}
?>
</div>

<hr class="page_hr" />

<section class="pricelist">
	<h2 class="page_h2"><?=$prefectureName;?>内のレンタカーをすべての駅名から探す</h2>
	<dl class="accordion_dl variable_cont_wrap">	
<?php
	$station_counter = 0;
	foreach ($stationListGroupByArea['areas'] as $GroupByAreaData) {
		$station_counter++;
?>	
		<dt class="accordion_dt <?php if($station_counter > 2){ ?>cvariable_cont_hidden<?php } ?>">
			<a href="javascript:void(0);" aria-expanded="false" role="tab">
				<h3 class="pref_cont_title"><?= $GroupByAreaData['name'] ?></h3>
			</a>
			<i class="fa fa-angle-down"></i>
		</dt>
		<dd class="accordion_dd" aria-hidden="true">
			<ul class="accordion_ul pref_link_cont_ul">
<?php 
		foreach ($GroupByAreaData['stations'] as $stationByAreaKey => $stationByAreaData) { 
?>
				<li class="accordion_li link_cont_li">
					<a href="/rentacar/<?= $baseUrl . $stationByAreaData['url']?>/"><span><?= $stationByAreaData['name'] . $stationByAreaData['type'] ?></span></a>
					<i class="fa fa-angle-right"></i>
				</li>
<?php 
		} 
?>
			</ul>
		</dd>
<?php 
	} 
?>
	</dl>
</section>

<hr class="page_hr" />

<?php
	if( !empty($airportLinkCdList) ){
?>
<section class="pricelist">
	<h2 class="page_h2"><?=$prefectureName;?>内のレンタカーを空港から探す</h2>
	<dl class="accordion_dl">
		<dd class="accordion_dd" style="height:100%">
			<ul class="accordion_ul pref_link_cont_ul">
<?php 
		foreach($airportLinkCdList as $k => $v) { 
?>
				<li class="accordion_li link_cont_li">
					<a href="/rentacar/<?= $baseUrl . $k?>/"><span><?= $v['name'] ?></span></a>
					<i class="fa fa-angle-right"></i>
				</li>
<?php 
		} 
?>
			</ul>
		</dd>
	</dl>
</section>
<?php
	}
?>

<hr class="page_hr" />

<?php
	if (!empty($cityListGroupByArea)) {
?>
<section class="pricelist">
	<h2 class="page_h2"><?= $prefectureName; ?>内のレンタカーを市区町村から探す</h2>
	<dl class="accordion_dl">
<?php
		foreach ($cityListGroupByArea['areas'] AS $areaKey => $areaData) {
?>

		<dt class="accordion_dt">
			<a href="javascript:void(0);" aria-expanded="false" role="tab"><h3><?=$areaData['name'];?></h3></a>
			<i class="fa fa-angle-down"></i>
		</dt>
		<dd class="accordion_dd" aria-hidden="true">
			<ul class="accordion_ul">
<?php
			foreach ($areaData['cities'] AS $cityData) {
?>
				<li class="accordion_li">
					<a href="/rentacar/<?= $baseUrl . $cityData['link_cd']?>/"><span><?= $cityData['name'] ?></span></a>
					<i class="fa fa-angle-right"></i>
				</li>
<?php
			}
?>
			</ul>
		</dd>
<?php
		}
?>
	</dl>
</section>

<hr class="page_hr" />
<?php
	}
?>

<section class="pricelist">
	<h2 class="page_h2">レンタカーを全国の都道府県から探す</h2>
	<dl class="accordion_dl">
<?php
	foreach ($prefectureListGroupByRegion AS $allPrefKey => $allPrefData) {
?>
		<dt class="accordion_dt">
			<a href="javascript:void(0);" aria-expanded="false" role="tab">
				<h3><?=$regions[$allPrefKey];?></h3>
			</a>
			<i class="fa fa-angle-down"></i>
		</dt>
		<dd class="accordion_dd" aria-hidden="true">
			<ul class="accordion_ul">
<?php
		foreach ($allPrefData AS $prefKey => $prefData) {
			$allPrefUrl = $allPrefKey."/".$prefKey;
			if ($prefKey === "hokkaido" || $prefKey === "okinawa") {
				$allPrefUrl = $prefKey;
			}
?>
				<li class="accordion_li">
					<a href="/rentacar/<?=$allPrefUrl;?>/"><?=$prefData;?></a>
					<i class="fa fa-angle-right"></i>
				</li>
<?php
		}
?>
			</ul>
		</dd>
<?php
	}
?>
	</dl>
</section>

<?php echo $this->element('sp_popular_airport_linklist'); ?>

<script>
$(function(){
	// accordion
	$(".accordion_dt").on("click", function(){
		$(this).children("i").toggleClass("rotate_arrow");
		$(this).toggleClass("show_more");
		var objListCont = $(this).next(".accordion_dd");
		var ulHeight = 0;

		var isOpen = $(this).hasClass("show_more");
		$(this).children("a").attr("aria-expanded", isOpen);
		objListCont.attr("aria-hidden", !isOpen);

		if( isOpen ){
			var ulHeight = objListCont.children(".accordion_ul").height();
		}

		objListCont.height( ulHeight );
	});
		
	// 使ってない？
	// more ReviewList
	// var heightReviewList = $("#js_review_list > ul").height();
	// $("#js_review_list").height( heightReviewList + 24 );
	// $("#js_btn_more_review").on("click", function(){
	// 	var review_cnt = $("#js_review_list .variable_cont_hidden").length;
	// 	$("#js_review_list .variable_cont_hidden").each(function(index, el){
	// 		if( index >= 10 ) return false;

	// 		$(el).removeClass("variable_cont_hidden");

	// 		if( index == review_cnt - 1 ){
	// 			$("#js_btn_more_review").parent(".variable_cont_more").hide();
	// 		}
	// 	});
	// 	var heightListAfter = $("#js_review_list > ul").height();
	// 	$("#js_review_list").height( heightListAfter + 24 );
	// });
  
});

/*
* プラン詳細表示Swiper
*/
var planSwiper = new Swiper ('.swiper-container', {
	slidesPerView: 'auto',
	pagination: '.swiper-pagination',
	paginationClickable:true,
})

</script>

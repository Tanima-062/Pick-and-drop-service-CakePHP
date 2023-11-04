<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="/rentacar/css/swiper.min.css">
<script type="text/javascript" src="/rentacar/js/swiper.min.js"></script>

<?php
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
	$type = $activeWebp ? '.webp' : '.png';
	if(!empty($cityContents['city-photo-guid'])){
		$cityHeader = $cityContents['city-photo-guid'];
	} else {
		$cityHeader = '/rentacar/img/station_default_header'.$type;
	}
?>
<div>
	<div class="fromairport_head">
		<div class="fromairport_head_text">
			<h1 class="fromairport_head_ttl"><?= $areaList['0']['Area']['name']; ?>の格安レンタカーを比較・予約する</h1>
<?php
	if(!empty($cityContents['city-single-city-en'])){
?>
			<p class="fromairport_head_en"><?= $cityContents['city-single-city-en']; ?></p>
<?php
	}
?>
		</div>
	</div>

	<section id="search">
<?php
	echo $this->Form->create('Search', array(
		'controller'=>'searches',
		'action'=>'index',
		'inputDefaults'=>array(
			'label'=>false,
			'div'=>false,
			'hiddenField'=>false,
			'legend'=>false,
			'fieldset'=>false
		),
	'type'=>'get'));
?>
		<section class="search_section">
			<?php
				//出発日時や返却日時などは共通項目のためエレメント化
				echo $this->element('sp_searchform_main');
			?>
			<div class="searchform_submit_section_wrap">
				<?php echo $this->element('sp_searchform_submit'); ?>
			</div>
		</section>
<?php
	echo $this->Form->end();
?>
	</section>




		<!-- プラン掲載 -->
		<?php
		if(!empty($bestPriceCarTypes)) {
		?>
			<div class="ranking">
				<h2><?= $areaList['0']['Area']['name']; ?>でレンタカー最安値を探す</h2>

			<div class="ranking-list ranking<?=$rankingNum +1;?>">
				<div class="swiper-container">
					<div class="swiper-wrapper" id="sliderSet">
		<?php
				$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
				$type = $activeWebp ? '.webp' : '.png';
				foreach ( $bestPriceCarTypes as $carTypeId => $ranking) {
						if($carTypeId == 1) {
							$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1 kei" width="120" height="82.8"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2 compact" width="150" height="82.5">';
						} else if($carTypeId == 3) {
							$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1 hybrid" width="150" height="86"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2 sedan" width="150" height="64.5">';
						} else if($carTypeId == 9) {
							$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1 miniban" width="150" height="82"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2 wagon" width="150" height="82.5">';
						}
					if ($carTypeId == 9) {
						$carCapacity = $typeCapacityList[$carTypeId]."人乗り～";
					} else {
						$carCapacity = $typeCapacityList[$carTypeId]."人乗り";
					}
		?>
								<a href="<?=$ranking['url']?>" class="swiper-slide">
									<p class="-carModels"><?=$ranking['name']?></p>
									<p class="carImg"><?=$carImg?></p>
									<div class="bottom">
										<p><span class="carCapacity"><?=$carCapacity;?></span><span class="-marker">最安値</span><span class="-price"><?=$ranking['price']?></p>
									</div>
								</a>
		<?php
			}
		?>
	</div><!--swiper-wrapper-->
		<div class="swiper-pagination"></div>
	</div><!--swiper-container-->
			</div>
					</div>
		<?php
			}
		?>
		<!-- プラン掲載ここまで -->



<?php
	if(!empty($cityContents['city-single-price']) OR !empty($cityContents['city-single-price-text']) OR !empty($officeInfoList)){
?>
	<div class="section_title_blue">
		<h2><?= $areaList['0']['Area']['name']; ?>の格安レンタカー会社と店舗一覧</h2>
	</div>
<?php
	}
?>

<?php
	if(!empty($officeInfoList)){
?>
	<?php echo $this->element('sp_shoplist_detail_accordion'); ?>

	<div class="inner_space_area">
		<div class="forairport_txt_area">
			<p><?= $cityContents['city-single-shop-text']; ?></p>
			<span>続きを読む<img src="/rentacar/img/sp/plus.png"></span>
		</div>
	</div>
<?php
	}
?>

<?php
	if(!empty($cityContents['city-single-price']) OR !empty($cityContents['city-single-price-text'])){
?>

	<div class="section_title_blue">
		<h2><?= $areaList['0']['Area']['name']; ?>からの乗り捨て料金一覧</h2>
	</div>

<?php
		if(!empty($cityContents['city-single-price'])){
?>
	<div class="dropoff-pricelist-section">
<?php
			if(!empty($cityContents['city-single-price'])){
?>
		<table class="fromairport_place_time_tbl forairport_price_list">
			<?= $cityContents['city-single-price']; ?>
		</table>
<?php
			}
			if(!empty($cityContents['city-single-price-text'])){
?>
		<div class="forairport_txt_area">
			<p><?= $cityContents['city-single-price-text']; ?></p>
			<span>続きを読む<img src="/rentacar/img/sp/plus.png"></span>
		</div>
<?php
			}
?>
		<?php echo $this->element('btn_select_dropoff'); ?>
	</div>
<?php
		}
	}
?>



<?php
	if( !empty($yotpoReviews) ){
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
		<div class="section_title_blue">
			<h2>口コミから<?= $areaList['0']['Area']['name']; ?>のレンタカーを探す</h2>
		</div>
		<section id="reviews" class="yotpo_api_custom_wrap" style="padding-top: 0;">
			<?php echo $this->element('sp_yotpo_review'); ?>
		</section>
<?php
		}
	}
?>

<?php echo $this->element('sp_popular_airport_linklist'); ?>	

<?php
	if (!empty($areaLinkCdList)){
?>
	<div class="section_title_blue">
		<h2><?= $areaList['0']['Area']['name']; ?>の近隣エリアの格安レンタカーを予約</h2>
	</div>
	<ul class="city_area_search clearfix">
<?php
		foreach($areaLinkCdList AS $key => $val){
?>
			<li><a href="/rentacar/<?= $base_url . $key ?>/"><?= $val ?></a></li>
<?php
		}
?>
	</ul>
<?php
	}
	if (!empty($airportLinkCdList)){
?>
	<div class="section_title_blue">
		<h2><?= $areaList['0']['Area']['name']; ?>の近隣空港の格安レンタカーを比較・予約</h2>
	</div>
	<ul class="city_area_search clearfix">
<?php
		foreach($airportLinkCdList AS $k => $v){
?>
		<li>
			<a href="/rentacar/<?=$base_url . $k . '/'?>"><?=$v['short_name']?></a>
		</li>
<?php
		}
?>
	</ul>
<?php
	}
	if (!empty($cityInfoList)){
?>
	<div class="section_title_blue">
		<h2><?= $areaList['0']['Area']['name']; ?>エリアの市区町村のレンタカーを比較・予約</h2>
	</div>
	<ul class="city_area_search clearfix">
<?php
		foreach($cityInfoList AS $v){
?>
		<li>
			<a href="/rentacar/<?=$base_url . $v['City']['link_cd'] . '/'?>"><?=$v['City']['name']?></a>
		</li>
<?php
		}
?>
	</ul>
<?php
	}
?>

<div class="section_title_blue">
	<h2><?= $areaList['0']['Area']['name']; ?>に関する格安レンタカー情報</h2>
</div>

<?php
	if(!empty($cityContents['0']['head'])){
?>
	<div class="img_caption_wrap -blueback">
		<div class="img_caption_contents">
<?php
		foreach($cityContents as $v){
			if (!empty($v['head'])){
				if (!empty($v['img'])){
?>
			<p><img src="<?= $v['img']; ?>"></p>
<?php
				}
?>
			<h3 class="img_caption_btn"><?= $v['head']; ?></h3>
			<div class="img_caption_detail">
				<p><?= $v['text']; ?></p>
			</div>
<?php
			}
		}
?>
		</div>
	</div>
<?php
	}
?>

</div>
<script type="text/javascript">
	$(function() {
		// 使ってない？
		// // 検索ボックスのカスタマイズ
		// $("#depature-place .airport-input").hide();
		// $("#depature-place .input-area").hide();
		// $("#depature-place .box-in-title").css({"minHeight":"auto", "height":"78px"});

		// 返却場所アコーディオン化
		// var acBtn = $("#js-btn_ac_return_place");
		// var acBody = $("#return-place").parent(".box-in");

		// acBtn.parent(".btn_ac_return_place").show();
		// acBody.hide();
		// acBtn.on("click", function(){
		// 	if( acBody.is(":visible") ){
		// 		acBody.slideUp();
		// 	}else{
		// 		acBody.slideDown();
		// 	}
		// });

		$(".forairport_txt_area span").click(function(){
			if($(this).prev(".forairport_txt_area p").hasClass("open")){
				$(this).prev(".forairport_txt_area p").removeClass("open");
				$(this).html("続きを読む<img src='/rentacar/img/sp/plus.png'>");
				$(this).next(".forairport_txt_area img").css("vertical-align","top");
			}else{
				$(this).prev(".forairport_txt_area p").addClass("open");
				$(this).html("閉じる<img src='/rentacar/img/sp/minus.png'>");
				$(this).next(".forairport_txt_area span img").css("vertical-align","middle");
			}
		});

		$(".img_caption_btn").click(function(){
			if($(this).hasClass("open")){
				$(this).removeClass("open");
			}else{
				$(this).addClass("open");
			}
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

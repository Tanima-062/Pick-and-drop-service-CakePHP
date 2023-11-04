<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="/rentacar/css/swiper.min.css">
<script type="text/javascript" src="/rentacar/js/swiper.min.js"></script>

<div>
<?php	
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
	$type = $activeWebp ? '.webp' : '.png';
?>
	<div class="fromairport_head">
		<p class="fromairport_head_ttl"><?= $city['name']; ?>のレンタカーを予約</p>
	</div>

	<section id="search">
		<?php
			echo $this->Form->create('Search', array(
				'controller'=>'searches','action'=>'index', 'inputDefaults'=>array(
					'label'=>false,
					'div'=>false,
					'hiddenField'=>false,
					'legend'=>false,
					'fieldset'=>false
				),
				'type'=>'get')
			);
		?>
			<section class="search_section">
				<?php
					// 出発日時や返却日時などは共通項目のためエレメント化
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

<?php
	if(!empty($activeContents)){
?>
	<div class="forairport_txt_area">
		<p><?= $activeContents; ?></p>
		<span>続きを読む<img src="/rentacar/img/sp/plus.png"></span>
	</div>
<?php
	}
?>

<!-- プラン掲載 -->
<?php
	if(!empty($bestPriceCarTypes)) {
?>
	<div class="ranking">
		<h2><?= $city['name']; ?>でレンタカー最安値を探す</h2>

		<div class="ranking-list ranking<?=$rankingNum +1;?>">
			<div class="swiper-container">
				<div class="swiper-wrapper" id="sliderSet">
<?php
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


	<div class="section_title_blue">
		<h2><?= $city['name']; ?> 車種別最安値一覧</h2>
	</div>

	<div class="cp_area">
		<div class="cp_outline">
			<div class="cp_outerbox">
				<a href="<?=$this->CreateUrl->view($search['url'],'car_type[]=1');?>" class="cp_outline_hover">
					<div class="cp_innerbox">
						<p class="cp_img"><img src="/rentacar/img/kei.png"></p>
						<p class="cp_car_type">軽自動車</p>
					</div>
					<p class="cp_orange">最安値</p>
<?php
	if (!empty($typeBestPrices[1])) {
?>
					<p class="cp_price"><?=number_format($typeBestPrices[1]['bestPrice'])?>円</p>
<?php
	} else {
?>
					<p class="cp_price">最安値を検索</p>
<?php
	}
?>
				</a>
			</div>
		</div>

		<div class="cp_outline">
			<div class="cp_outerbox">
				<a href="<?=$this->CreateUrl->view($search['url'],'car_type[]=2');?>" class="cp_outline_hover">
					<div class="cp_innerbox">
						<p class="cp_img"><img src="/rentacar/img/compact.png"></p>
						<p class="cp_car_type">コンパクト</p>
					</div>
					<p class="cp_orange">最安値</p>
<?php
	if (!empty($typeBestPrices[2])) {
?>
					<p class="cp_price"><?=number_format($typeBestPrices[2]['bestPrice'])?>円</p>
<?php
	} else {
?>
					<p class="cp_price">最安値を検索</p>
<?php
	}
?>
				</a>
			</div>
		</div>

		<div class="cp_outline">
			<div class="cp_outerbox">
				<a href="<?=$this->CreateUrl->view($search['url'],'car_type[]=3');?>" class="cp_outline_hover">
					<div class="cp_innerbox">
						<p class="cp_img"><img src="/rentacar/img/middle.png"></p>
						<p class="cp_car_type">ミドル・<br>セダン</p>
					</div>
					<p class="cp_orange">最安値</p>
<?php
	if (!empty($typeBestPrices[3])) {
?>
					<p class="cp_price"><?=number_format($typeBestPrices[3]['bestPrice'])?>円</p>
<?php
	} else {
?>
					<p class="cp_price">最安値を検索</p>
<?php
	}
?>
				</a>
			</div>
		</div>

		<div class="cp_outline">
			<div class="cp_outerbox">
				<a href="<?=$this->CreateUrl->view($search['url'],'car_type[]=5&car_type[]=9');?>" class="cp_outline_hover">
					<div class="cp_innerbox">
						<p class="cp_img"><img src="/rentacar/img/wagon.png"></p>
						<p class="cp_car_type">ミニバン・<br>ワゴン</p>
					</div>
					<p class="cp_orange">最安値</p>
<?php
	if (!empty($typeBestPrices[9])) {
?>
					<p class="cp_price"><?=number_format($typeBestPrices[9]['bestPrice'])?>円</p>
<?php
	} else {
?>
					<p class="cp_price">最安値を検索</p>
<?php
	}
?>
				</a>
			</div>
		</div>
	</div><!-- cp_area -->

<?php
	if (!empty($officeInfoList)) {
?>
	<div class="section_title_blue">
		<h2><?= $city['name']; ?>のレンタカー会社から探す</h2>
	</div>
	<?php echo $this->element('sp_shoplist_detail_accordion'); ?>
<?php
	}
?>

<?php
	if (!empty($yotpoReviews) ) {
		// YOTPO
		if ($yotpo_is_active && $use_yotpo) {
?>
	<div class="section_title_blue">
		<h2>口コミから<?=$city['name'];?>のレンタカーを探す</h2>
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
	if (!empty($neighborList)) {
?>
	<div class="section_title_blue">
		<h2><?= $city['name']; ?>の近隣市区町村から探す</h2>
	</div>
	<ul class="city_area_search clearfix">
<?php 
		foreach ($neighborList AS $key => $val) {
?>
		<li><a href="<?=$val['url']?>"><?=$val['name']?></a></li>
<?php
		}
?>
	</ul>
<?php
	}
?>

<?php
	if (!empty($airportLinkCdList) || !empty($majorStationList)) {
?>
	<div class="section_title_blue">
		<h2><?= $city['name']; ?>の近隣空港・主要駅から探す</h2>
	</div>
	<ul class="city_area_search clearfix">
<?php 
		foreach ($airportLinkCdList AS $key => $val) {
?>
		<li><a href="/rentacar/<?=$base_url . $key?>/"><?=$val['short_name']?></a></li>
<?php 
		}
?>
<?php 
		foreach ($majorStationList AS $key => $val) {
?>
		<li><a href="/rentacar/<?=$base_url . $val['url']?>/"><?=$val['name']?></a></li>
<?php 
		}
?>
	</ul>
<?php
	}
?>

</div>

<script type="text/javascript">
$(function() {
	// 使ってなさそう
	// 検索ボックスのカスタマイズ
	// $("#depature-place .airport-input").hide();
	// $("#depature-place .input-area").hide();
	// $("#depature-place .box-in-title").css({"minHeight":"auto", "height":"78px"});

	// 使ってなさそう
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

	// 使ってなさそう
	// $(".img_caption_btn").click(function(){
	// 	if($(this).hasClass("open")){
	// 		$(this).removeClass("open");
	// 	}else{
	// 		$(this).addClass("open");
	// 	}
	// });

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

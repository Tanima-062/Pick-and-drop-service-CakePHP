<?php
	echo $this->Html->css(['swiper.min', 'sp/jquery-ui'],null,['inline'=>false]);
	echo $this->Html->script(['/js/swiper.min'],['inline' => false]);
	echo $this->Html->script(['/js/sp/sp_faq_tablist','/js/smoothscroll'],['defer'=>true, 'inline' => false]);
?>

<div>
	<div id="contents_top">
		<h1 class="contents_top_title">
<?php
	$word_count = mb_strlen( $landmark['name'],'utf-8' );
	$title_tail = '';
	if($word_count > 5){
		$title_tail = '格安レンタカー'
?>
			<?= $landmark['name']; ?><br>格安レンタカー
<?php
	}else{
		$title_tail = 'のレンタカー'.$landmark['name'].'の店舗から最安値を比較'
?>
<?= $landmark['name']; ?>のレンタカー<br><span class="catch"><?= $landmark['name']; ?>の店舗から最安値を比較</span>
<?php
	}
?>
		</h1>
	</div>

	<?php echo $this->element('sp_campaign_banner_hokkaido'); ?>

	<section id="search">
<?php
	echo $this->Form->create('Search',array(
		'controller'=>'searches',
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

	<!-- プラン掲載 -->
	<?php
	if(!empty($bestPriceCarTypes)) {
	?>
		<div class="ranking">
			<h2><?= $landmark['name']; ?>でレンタカー最安値を探す</h2>

		<div class="ranking-list ranking<?=$rankingNum +1;?>">
			<div class="swiper-container">
				<div class="swiper-wrapper" id="sliderSet">
	<?php
			$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
			$type = $activeWebp ? '.webp' : '.png';
			foreach ( $bestPriceCarTypes as $carTypeId => $ranking) {
					if($carTypeId == 1) {
						$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1 kei" alt="'.$landmark['name'].' 軽自動車 レンタカー" width="120" height="82.8"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2 compact" alt="'.$landmark['name'].' コンパクト レンタカー" width="150" height="82.5">';
					} else if($carTypeId == 3) {
						$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1 hybrid" alt="'.$landmark['name'].' ハイブリッド レンタカー" width="150" height="86"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2 sedan" alt="'.$landmark['name'].' セダン レンタカー" width="150" height="64.5">';
					} else if($carTypeId == 9) {
						$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1 miniban" alt="'.$landmark['name'].' ミニバン レンタカー" width="150" height="82"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2 wagon" alt="'.$landmark['name'].' ワゴン レンタカー"width="150" height="82.5">';
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




	<table class="rentacar-grid">
<?php
	for ($i = 0; $i < $sp_menu_max; $i++){
		if ($i % 2 == 0){
?>
		<tr>
<?php
		}
?>
			<td class="rentacar-grid-half-list">
<?php
		if (isset($sp_menu[$i])){
?>
				<a href="<?= $sp_menu[$i][0]; ?>" class="rentacar-grid-list-link"><?= $sp_menu[$i][1]; ?></a>
<?php
		}
?>
			</td>
<?php
		if($i % 2 == 1){
?>
		</tr>
<?php
		}
	}
?>
	</table>

<?php
	if(!empty($officeInfoList)){
?>
	<div class="section_title_blue" id="01">
		<h2><?= $landmark['name']; ?>の格安レンタカー会社と店舗一覧</h2>
	</div>
	<?php echo $this->element('sp_shoplist_detail_accordion'); ?>
<?php
	}
?>

<?php

	if(!empty($airportContents['airport-single-price']) OR !empty($airportContents['airport-single-price-text'])){
?>
	<div class="section_title_blue" id="02">
		<h2><?= $landmark['name']; ?>からの乗り捨て料金一覧</h2>
	</div>
<?php
		if(!empty($airportContents['airport-single-price'])){
?>
	<div class="inner_space_area">
		<table class="fromairport_place_time_tbl forairport_price_list">
		<?= $airportContents['airport-single-price']; ?>
		</table>
	</div>
<?php
		}
		if(!empty($airportContents['airport-single-price-text'])){
?>
	<div class="forairport_txt_area1">
		<p><?= $airportContents['airport-single-price-text']; ?></p>
		<span>続きを読む<img src="/rentacar/img/sp/plus.png"></span>
	</div>
<?php
		}
?>
	<div class="for_airport_button_area">
		<?php echo $this->element('btn_select_dropoff'); ?>
	</div>
<?php
	}
?>
	<div class="section_title_blue" id="03">
		<h2><?= $landmark['name']; ?>の受付カウンター情報</h2>
	</div>
	<div class="inner_space_area">
		<?= $airportContents['airport-single-counter']; ?>
	</div>
	<div class="forairport_txt_area2">
		<p><?= $airportContents['airport-single-counter-text']; ?></p>
		<span>続きを読む<img src='/rentacar/img/sp/plus.png'></span>
	</div>


<?php
	if( !empty($yotpoReviews) ){
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
		<div class="section_title_blue" id="04">
			<h2>口コミから<?= $landmark['name']; ?>のレンタカーを探す</h2>
		</div>
		<section id="reviews" class="yotpo_api_custom_wrap" style="padding-top: 0">
			<?php echo $this->element('sp_yotpo_review'); ?>
		</section>
<?php
		}
	}
?>

<?php echo $this->element('sp_prefecture_linklist'); ?>

<?php echo $this->element('sp_popular_airport_linklist'); ?>

<?php
	if (!empty($airportLinkCdList)){
?>
	<div class="section_title_blue" id="05">
		<h2><?= $landmark['name']; ?>の近隣空港の格安レンタカーを比較・予約</h2>
	</div>
	<label class="formairport_select">
		<form action="" class="formairport_select_form">
			<select onchange="select_form_submit('/rentacar/',this.value);">
				<option value="0">こちらから空港をお選びください</option>
<?php
		foreach($airportLinkCdList as $k => $v){
?>
				<option value="<?=$base_url . $k . '/'?>"><?=$v['name']?></option>
<?php
		}
?>
			</select>
		</form>
	</label>
<?php
	}
	if(!empty($landmarkList['area'])){
?>
	<div class="section_title_blue" id="06">
		<h2><?= $landmark['name']; ?>の近隣エリアの格安レンタカーを予約</h2>
	</div>
	<label class="formairport_select">
		<form action="" class="formairport_select_form">
			<select onchange="select_form_submit('/rentacar/',this.value);">
				<option value="0">こちらから地域をお選びください</option>
<?php
		foreach($landmarkList['area'] as $k => $v){
?>
				<option value="<?= $base_url . $areaLinkCd[$k]['area_link_cd'] ?>/"><?= $v ?></option>
<?php
		}
?>
			</select>
		</form>
	</label>
<?php
	}
?>
	<div class="section_title_blue" id="07">
		<h2><?= $landmark['name']; ?>に関する格安レンタカー情報</h2>
	</div>
<?php
	if(!empty($airportContents['airport-single-contents-list'])){
?>
	<div class="img_caption_wrap -blueback">
<?php
		foreach($airportContents['airport-single-contents-list'] as $v){
			if (!empty($v['contents-head'])){
?>
		<div class="img_caption_contents">
<?php
				if (!empty($v['contents-img'])){
?>
			<p><img src="/rentacar/wp/img/<?= $v['contents-img']; ?>?imwidth=400" alt="<?= $v['contents-head']; ?>"></p>
<?php
				}
?>
			<h3 class="img_caption_btn"><?= $v['contents-head']; ?></h3>
			<div class="img_caption_detail">
				<p><?= $v['contents-text']; ?></p>
			</div>
		</div>
<?php
			}
		}
?>
	</div>
<?php
	}
?>


</div>
<div class="-section faq-tab-list">
<?php
	//順番に、最安値掲示があればそれを取得
	if ($bestPriceCarTypes[1][price] != '最安値を検索') {
		$lowestPriceThisAirport = $bestPriceCarTypes[1][price];
	}
	elseif ($bestPriceCarTypes[3][price] != '最安値を検索') {
		$lowestPriceThisAirport = $bestPriceCarTypes[3][price];
	}
	elseif ($bestPriceCarTypes[9][price] != '最安値を検索') {
		$lowestPriceThisAirport = $bestPriceCarTypes[9][price];
	}
	else {
		$lowestPriceThisAirport = "";
	};

	//　利用できる会社５社まで表示
	$companyList = [];
	foreach($officeInfoList as $office){
		if(count($companyList) < 5){
			$companyName = $office['Client']['name'];
			if(!in_array($companyName, $companyList, true)){
				array_push($companyList, $companyName);
			}
		} 
		else {
			break;
		}
	}
	if(count($companyList) > 0) {
		$availableCompanies = '';
		for($i = 0; $i < count($companyList); $i++){
			if($i < (count($companyList)-1)){
				$availableCompanies .= $companyList[$i] . '、';
			} 
			else {
				$availableCompanies .= $companyList[$i];
			}			
		}
	}
?>
	<article id="js-tabList" class="tab-list">
		<ul class="-tab">
<?php
	if (!empty($lowestPriceThisAirport)){
?>
			<li class="js-tabList-item">
				<a class="-title" href="javascript:void(0)"><?= $landmark['name']; ?>周辺のレンタカーの最安値はいくらですか？
					<i class="icm-right-arrow down"></i>
					<i class="icm-right-arrow up"></i>
				</a>
				<div class="-content js-tabList-content">
					<p>
					<?= $landmark['name']; ?>周辺のレンタカー料金は、<?= $lowestPriceThisAirport; ?>からとなっております。
					</p>
				</div>
			</li>
<?php
	};
?>
			<li class="js-tabList-item">
				<a class="-title" href="javascript:void(0)"><?= $landmark['name']; ?>に店舗があるレンタカー会社はどちらですか？
					<i class="icm-right-arrow down"></i>
					<i class="icm-right-arrow up"></i>
				</a>
				<div class="-content js-tabList-content">
					<p>
					<?= $landmark['name']; ?>周辺に店舗があるレンタカー会社は、<?= $availableCompanies; ?>などです。
					</p>
				</div>
			</li>
			<li class="js-tabList-item">
				<a class="-title" href="javascript:void(0)">レンタカーは長期で借りられますか？
					<i class="icm-right-arrow down"></i>
					<i class="icm-right-arrow up"></i>
				</a>
				<div class="-content js-tabList-content">
					<p>
					1時間や1日などの短時間から、1週間や1ヶ月などの長期までレンタカーを簡単に予約できます。
					</p>
				</div>
			</li>
			<li class="js-tabList-item">
				<a class="-title" href="javascript:void(0)">どのような車種・オプションが検索できますか？
					<i class="icm-right-arrow down"></i>
					<i class="icm-right-arrow up"></i>
				</a>
				<div class="-content js-tabList-content">
					<p>
					絞り込み検索の機能を使えば、ミニバンやワゴンなどの7人乗りから10人乗り、外車、高級車などの車種を指定して検索できます。オプションはスタッドレスタイヤ、4WDなどから選ぶことができ、いろいろな車をレンタルできます。
					</p>
				</div>
			</li>
		</ul>
	</article>
</div>
<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "FAQPage",
		"mainEntity": [
<?php
	if (!empty($lowestPriceThisAirport)){
?>			
			{
				"@type": "Question",
				"name": "<?= $landmark['name']; ?>周辺のレンタカーの最安値はいくらですか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"<?= $landmark['name']; ?>周辺のレンタカー料金は、<?= $lowestPriceThisAirport; ?>からとなっております。"
				}
			},
<?php
	}
?>
			{
				"@type": "Question",
				"name": "<?= $landmark['name']; ?>に店舗があるレンタカー会社はどちらですか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"<?= $landmark['name']; ?>周辺に店舗があるレンタカー会社は、<?= $availableCompanies; ?>などです。"
				}
			},
			{
				"@type": "Question",
				"name": "レンタカーは長期で借りられますか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"1時間や1日などの短時間から、1週間や1ヶ月などの長期までレンタカーを簡単に予約できます。"
				}
			},
			{
				"@type": "Question",
				"name": "どのような車種・オプションが検索できますか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"絞り込み検索の機能を使えば、ミニバンやワゴンなどの7人乗りから10人乗り、外車、高級車などの車種を指定して検索できます。オプションはスタッドレスタイヤ、4WDなどから選ぶことができ、いろいろな車をレンタルできます。"
				}
			}
		]
	}
</script>
<script type="text/javascript">
	$(function() {
		// 検索ボックスのカスタマイズ
		$("#depature-place .airport-input").hide();
		$("#depature-place .input-area").hide();
		$("#depature-place .box-in-title").css({"minHeight":"auto", "height":"38px"});

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

		$(".forairport_txt_area1 span").click(function(){
			if($(".forairport_txt_area1 p").hasClass("open")){
				$(".forairport_txt_area1 p").removeClass("open");
				$(".forairport_txt_area1 span").html("続きを読む<img src='/rentacar/img/sp/plus.png'>");
				$(".forairport_txt_area1 span img").css("vertical-align","top");
			}else{
				$(".forairport_txt_area1 p").addClass("open");
				$(".forairport_txt_area1 span").html("閉じる<img src='/rentacar/img/sp/minus.png'>");
				$(".forairport_txt_area1 span img").css("vertical-align","middle");
			}
		});

		$(".forairport_txt_area2 span").click(function(){
			if($(".forairport_txt_area2 p").hasClass("open")){
				$(".forairport_txt_area2 p").removeClass("open");
				$(".forairport_txt_area2 span").html("続きを読む<img src='/rentacar/img/sp/plus.png'>");
				$(".forairport_txt_area2 span img").css("vertical-align","top");
			}else{
				$(".forairport_txt_area2 p").addClass("open");
				$(".forairport_txt_area2 span").html("閉じる<img src='/rentacar/img/sp/minus.png'>");
				$(".forairport_txt_area2 span img").css("vertical-align","middle");
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

	function select_form_submit(url,airport_id){
		if(!!airport_id && airport_id != 0){
			location.href = url + airport_id;
		}
	}

	/*
	* プラン詳細表示Swiper
	*/
	var planSwiper = new Swiper ('.swiper-container', {
		slidesPerView: 'auto',
		pagination: '.swiper-pagination',
		paginationClickable:true,
	})
</script>

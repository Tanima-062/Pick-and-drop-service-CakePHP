<?php
	echo $this->Html->css(array('sp/jquery-ui', 'swiper.min'),null,array('inline'=>false));
	echo $this->Html->script(array('/js/swiper.min'));
?>
	<!-- ヘッダー-->
	<section class="company_head">
		<?php echo $this->element('sp_pagetitle_companypage'); ?>

<?php
	// YOTPO StarRating
	if($yotpo_is_active && $use_yotpo){
		$rating_avg = '';
		$rating_count = '';
		$client_id = $clientInfo['Client']['id'];
		if($use_yotpo_rating){
			if(array_key_exists($client_id, $ratings)){
				$rating_avg = $ratings[$client_id]['rating'];
				$rating_count = $ratings[$client_id]['count'];
			}
		}
?>
		<div class="yotpo_widget_wrap">
			<div class="yotpo bottomLine"
			  data-appkey="<?php echo $yotpo_app_key ?>"
			  data-domain="https://<?php echo $yotpo_domain; ?>/rentacar"
			  data-product-id="<?php echo $clientInfo['Client']['id'].'cl'; ?>"
			  data-product-models=""
			  data-name="<?php echo $clientInfo['Client']['name']; ?>"
			  data-url="https://<?php echo $yotpo_domain; ?><?= Router::url(); ?>"
			  data-image-url=""
			  data-description=""
			  data-bread-crumbs=""
			  data-rating-avg="<?= $rating_avg ?>"
			  data-rating-count="<?= $rating_count ?>"
			  >
			</div>
		</div>
<?php 
	}
?>
	</section>

	<section id="search">
<?php
	echo $this->Form->create('Search', array('controller' => 'searches', 'action' => 'index', 'inputDefaults' => array(
		'label' => false,
		'div' => false,
		'hiddenField' => false,
		'legend' => false,
		'fieldset' => false
	), 'type' => 'get'));
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
	// <!-- ランキング掲載 -->
	if(!empty($landmarkRanking)) {
?>
	<div class="ranking">
		<h2><?php echo $clientInfo['Client']['name']; ?>の人気エリアランキング</h2>
<?php
		unset( $landmarkRanking[3] );
		foreach ($landmarkRanking as $rankingNum => $ranking) {
?>
		<div class="ranking-list ranking<?=$rankingNum +1;?>">
			<h3><span class="ranking-title"><i class="icm-royal-crown -icon"></i> <?=$rankingNum +1 ?>位　<?=$ranking['name']?></span></h3>

	
<?php
			// <!-- ランキング1位 -->
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
							$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1 kei" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' 軽自動車" width="120" height="82.8" loading="lazy" importance="low" decoding="async""><img src="/rentacar/img/car_type_compact'.$type.'" class="img2 compact" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' コンパクト" width="150" height="82.5" loading="lazy" importance="low" decoding="async"">';
						} else if($carTypeId == 3) {
							$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1 hybrid" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' ハイブリッド" width="150" height="86" loading="lazy" importance="low" decoding="async""><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2 sedan" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' セダン" width="150" height="64.5" loading="lazy" importance="low" decoding="async"">';
						} else if($carTypeId == 9) {
							$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1 miniban" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' ミニバン" width="150" height="82" loading="lazy" importance="low" decoding="async""><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2 wagon" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' ワゴン" width="150" height="82.5" loading="lazy" importance="low" decoding="async"">';
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
			// <!-- ランキング1位ここまで -->
?>
	
<?php
			// <!-- ランキング2.3位 -->
			foreach ($ranking['bestPriceCarTypes'] as $carTypeId => $bestPriceCarTypes) {
				if ($carTypeId == 9) {
					$carCapacity = $typeCapacityList[$carTypeId]."人乗り～";
				} else {
					$carCapacity = $typeCapacityList[$carTypeId]."人乗り";
				}

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
			// <!-- ランキング2.3位ここまで -->
?>
		</div>
<?php
		}
?>
	</div>
<?php
	}
	// <!-- ランキング掲載ここまで -->
?>



<?php 
	// <!-- youtube -->
	if(isset($companyCharacterContents['company-youtube_0_youtube-video'])){
		$youtubeurl = $companyCharacterContents['company-youtube_0_youtube-video'];
		$youtubeurlembed = str_replace('watch?v=','embed/', $youtubeurl);
?>
	<section class="rentacar_txt_area company_content content_section_bordered company_youtube">
		<h2 class="company_contents_h2"><?= $clientInfo['Client']['name']; ?>の人気動画</h2>

		<div class="content_wrap">
			<h3 class="company_youtube_title"><i class="icm-skyticket-logo-round"></i><?= $companyCharacterContents['company-youtube_0_youtube-sub-title']; ?></h3>

			<iframe class="company_youtube_v" width="100%" height="" src="<?= $youtubeurlembed; ?>?enablejsapi=1" title="<?= $clientInfo['Client']['name']; ?>の人気動画" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

			<div class="youtube-home-link-wrap"><a class="youtube-home-link" href="https://www.youtube.com/c/AdventureInc_JP">skyticket 公式 YouTubeチャンネル<i class="icm-external-link"></i></a></div>
		</div>

		<button class="btn-type-sub" onclick="location.href='<?= $companyCharacterContents['company-youtube_0_youtube-link']; ?>'"><i class="icm-youtube"></i><?= $clientInfo['Client']['name']; ?>の動画をもっと見る</button>
		
	</section>
<?php
	}
	// <!-- youtube ここまで -->
?>

<?php
	if (!empty($companyCharacterContents['company-pre-contents'])  || !empty($clientInfo['Client']['clause_pdf']) || isset($companyCharacterContents['company-outline'])) {
?>
	<section class="content_section_bordered">
		<div class="company_content">
			<h2><?=$clientInfo['Client']['name']; ?>について</h2>
<?php
	if (!empty($companyCharacterContents['company-pre-contents']) || !empty($clientInfo['Client']['clause_pdf'])) {
?>
			<?php echo $this->element('sp_readmore_company_outline', ['outlineText' => $companyCharacterContents['company-pre-contents'], 'clausePdf' => $clientInfo['Client']['clause_pdf']]); ?>
<?php
	}
?>
		</div>
	
<?php
	if(isset($companyCharacterContents['company-outline'])){
?>
		<div class="img_caption_wrap">
			<div class="img_caption_contents">
<?php
		$title_cnt = 0;
		foreach($companyCharacterContents['company-outline'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if($title_cnt > 0){
?>
				</div>
<?php
				}
				$title_cnt++
?>
				<?php  if(!empty($val['value'])):
					$alt_text = $val['value']?>
					<h3 class="img_caption_btn open"><?=$val['value']?></h3>
				<?php endif;?>
				<div class="img_caption_detail">
<?php
			}
			if($val['field'] == 'rentacar-body-titlem'){
?>
				<?php  if(!empty($val['value'])):
					$alt_text = $val['value']?>
					<h4><?=$val['value']?></h4>
				<?php endif;?>
<?php
			}
			if($key > 0){
				if($val['field'] == 'rentacar-body-img'){
?>
					<?php  if(!empty($val['value'])):?>
					<img src="<?=$val['value']?>" alt="<?= $alt_text ?>" loading="lazy" importance="low" decoding="async"">
					<?php endif;?>
<?php
				}
			}
			if($val['field'] == 'rentacar-body-text'){
?>
					<?php  if(!empty($val['value'])):?>
					<p><?=$val['value']?></p>
					<?php endif;?>
<?php
			}
		}
		if($title_cnt > 0){
?>
				</div>
<?php
		}
?>
			</div>
		</div>
<?php
	}
?>
	</section>
<?php
	}
?>



<?php
	// YOTPO ReviewsWidget
	if($yotpo_is_active && $use_yotpo && !$fromRentacarClient){
?>

	<section class="content_section_bordered yotpo_container">
		<div class="yotpo_main_wrap" id="reviews">
			<h3 class="review_section_title"><?php echo $clientInfo['Client']['name']; ?>の感想・口コミ</h3>
			<div class="yotpo yotpo-main-widget"
				data-product-id="<?php echo $clientInfo['Client']['id'].'cl'; ?>"
				data-name="<?php echo $clientInfo['Client']['name']; ?>"
				data-url="https://skyticket.jp<?= Router::url(); ?>"
				data-image-url=""
                data-description="" >
                <?= !empty($main_widget) ? $main_widget : ''; ?>
			</div>
			<a class="review_btn" href="/rentacar/company/<?=$clientInfo['Client']['url'];?>/review/">
				<span>レビューをもっと読む</span>
			</a><br/>
		</div>
	</section>
<?php
	}
?>




<?php
	if(isset($companyCharacterContents['company-fee-contents'])){
?>
	<div class="content_section_bordered img_caption_wrap company_content">
		<h2><?php echo $clientInfo['Client']['name']; ?>の料金</h2>
		<div class="img_caption_contents">
<?php
		$title_cnt = 0;
		foreach($companyCharacterContents['company-fee-contents'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if($title_cnt > 0){
?>
			</div>
<?php
				}
				$title_cnt++
?>
			<?php  if(!empty($val['value'])):
				$alt_text = $val['value']?>
				<h3 class="img_caption_btn open"><?=$val['value']?></h3>
			<?php endif;?>
			<div class="img_caption_detail">
<?php
			}
			if($val['field'] == 'rentacar-body-titlem'){
?>
				<?php  if(!empty($val['value'])):
					$alt_text = $val['value']?>
					<h4><?=$val['value']?></h4>
				<?php endif;?>
<?php
			}
			if($key > 0){
				if($val['field'] == 'rentacar-body-img'){
?>
				<?php  if(!empty($val['value'])):?>
				<img src="<?=$val['value']?>" alt="<?= $alt_text ?>" loading="lazy" importance="low" decoding="async"">
				<?php endif;?>
<?php
				}
			}
			if($val['field'] == 'rentacar-body-text'){
?>
				<?php  if(!empty($val['value'])):?>
				<p><?=$val['value']?></p>
				<?php endif;?>
<?php
			}
		}
		if($title_cnt > 0){
?>
			</div>
<?php
		}
?>
		</div>
	</div>
<?php
	}
?>




<?php
	if(isset($companyCharacterContents['company-insurance-contents'])){
?>
	<div class="content_section_bordered img_caption_wrap company_content">
		<h2><?php echo $clientInfo['Client']['name']; ?>の免責補償制度</h2>
		<div class="img_caption_contents">
<?php
		$title_cnt = 0;
		foreach($companyCharacterContents['company-insurance-contents'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if($title_cnt > 0){
?>
			</div>
<?php
				}
				$title_cnt++
?>
			<?php  if(!empty($val['value'])):
				$alt_text = $val['value']?>
				<h3 class="img_caption_btn open"><?=$val['value']?></h3>
			<?php endif;?>
			<div class="img_caption_detail">
<?php
			}
			if($val['field'] == 'rentacar-body-titlem'){
?>
				<?php  if(!empty($val['value'])):
					$alt_text = $val['value']?>
					<h4><?=$val['value']?></h4>
				<?php endif;?>
<?php
			}
			if($key > 0){
				if($val['field'] == 'rentacar-body-img'){
?>
				<?php  if(!empty($val['value'])):?>
				<img src="<?=$val['value']?>" alt="<?= $alt_text ?>" loading="lazy" importance="low" decoding="async"">
				<?php endif;?>
<?php
				}
			}
			if($val['field'] == 'rentacar-body-text'){
?>
				<?php  if(!empty($val['value'])):?>
				<p><?=$val['value']?></p>
				<?php endif;?>
<?php
			}
		}
		if($title_cnt > 0){
?>
			</div>
<?php
		}
?>
		</div>
	</div>
<?php
	}
?>




<?php
	// 貸出当日の流れ
	if(isset($companyCharacterContents['company-today-contents'])){
?>
	<div class="content_section_bordered img_caption_wrap company_content">
		<h2><?php echo $clientInfo['Client']['name']; ?>での貸出当日の流れ</h2>
		<div class="img_caption_contents">
<?php
		$title_cnt = 0;
		foreach($companyCharacterContents['company-today-contents'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if($title_cnt > 0){
?>
			</div>
<?php
				}
				$title_cnt++
?>
			<?php  if(!empty($val['value'])):
				$alt_text = $val['value']?>
				<h3 class="img_caption_btn open"><?=$val['value']?></h3>
			<?php endif;?>
			<div class="img_caption_detail">
<?php
			}
			if($val['field'] == 'rentacar-body-titlem'){
?>
				<?php  if(!empty($val['value'])):
					$alt_text = $val['value']?>
					<h4><?=$val['value']?></h4>
				<?php endif;?>
<?php
			}
			if($key > 0){
				if($val['field'] == 'rentacar-body-img'){
?>
				<?php  if(!empty($val['value'])):?>
				<img src="<?=$val['value']?>" alt="<?= $alt_text ?>" loading="lazy" importance="low" decoding="async"">
				<?php endif;?>
<?php
				}
			}
			if($val['field'] == 'rentacar-body-text'){
?>
				<?php  if(!empty($val['value'])):?>
				<p><?=$val['value']?></p>
				<?php endif;?>
<?php
			}
		}
		if($title_cnt > 0){
?>
			</div>
<?php
		}
?>
		</div>
	</div>
<?php
	}
	// 貸出当日の流れ　ここまで
?>




<?php
	if(isset($companyCharacterContents['rentacar-body-list'])){
?>
	<div class="content_section_bordered img_caption_wrap">
		<div class="img_caption_contents">
<?php
		$title_cnt = 0;
		foreach($companyCharacterContents['rentacar-body-list'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if($title_cnt > 0){
?>
			</div>
<?php
				}
				$title_cnt++
?>
			<?php  if(!empty($val['value'])):
				$alt_text = $val['value']?>
				<h3 class="img_caption_btn open"><?=$val['title']?></h3>
			<?php endif;?>
			<div class="img_caption_detail">
<?php
			}
			if($val['field'] == 'rentacar-body-titlem'){
?>
				<?php  if(!empty($val['value'])):
					$alt_text = $val['value']?>
					<h4><?=$val['value']?></h4>
				<?php endif;?>
<?php
			}
			if(!$key == 0){
				if($val['field'] == 'rentacar-body-img'){
?>
				<?php  if(!empty($val['value'])):?>
					<img src="<?=$val['value']?>" alt="<?= $alt_text ?>" loading="lazy" importance="low" decoding="async"">
				<?php endif;?>
<?php
				}
			}
			if($val['field'] == 'rentacar-body-text'){
?>
				<?php  if(!empty($val['value'])):?>
				<p><?=$val['value']?></p>
				<?php endif;?>
<?php
			}
		}
		if($title_cnt > 0){
?>
			</div>
<?php
		}
?>
		</div>
	</div>
<?php
	}
?>



<?php
	if (!$fromRentacarClient) {
		echo $this->element('sp_prefecture_linklist');

		echo $this->element('sp_popular_airport_linklist');
	}
?>



<?php
	if (!empty($prefectureInfoList) && !$fromRentacarClient) {
?>
	<section class="content_section_bordered company_content">
		<h2><?php echo $clientInfo['Client']['name']; ?>の営業所を都道府県から探す</h2>
		<div class="search_list_div">
			<ul>
<?php
		foreach($prefectureInfoList as $k => $v){
			if(!empty($prefectureAreaInfo[$k]) && $k > 0){
?>
				<li id="display_prefecture_office_li<?=$k;?>" class="js-search_list_header search_list_header">
					<h3><?php echo $v ?></h3>
				</li>
<?php
				if($prefectureOfficeInfo[$k]){ 
?>
				<li class="search_list_contents">
					<ul class="search_list_ul">
<?php
					foreach($prefectureOfficeInfo[$k] as $k2 => $v2){
?>
						<li><a href="/rentacar/company/<?=$v2['client']['url'];?>/<?=$v2['Office']['url'];?>/"><span><?=$v2['Office']['name'];?></span></a></li>
<?php
					}
?>
					</ul>
				</li>
<?php
				}
			}
		}
?>
			</ul>
		</div>
	</section>
<?php
	}
?>



<?php
	echo $this->element("sp_sidebar");
?>



<script>
$(function() {
    // 使ってないぽい
	// $(".btn_search_tab").on("click", function(){
	// 	$(".btn_search_tab").removeClass("on").removeClass("off").addClass("off");
	// 	$(this).addClass("on");
	// 	var $menu_body = $(this).data("menu");

	// 	$(".company_search_body").hide();
	// 	$("#"+$menu_body).show();
	// });

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

	$(".img_caption_btn").click(function(){
		if($(this).hasClass("open")){
			$(this).removeClass("open");
		}else{
			$(this).addClass("open");
		}
	});

	$(".js-search_list_header").click(function(){
		if($(this).hasClass("open")){
			$(this).removeClass("open");
		}else{
			$(this).addClass("open");
		}
	});

	/*
	* プラン詳細表示Swiper
	*/
	var planSwiper = new Swiper ('.swiper-container', {
		slidesPerView: 'auto',
		pagination: '.swiper-pagination',
		paginationClickable:true,
	})

});
</script>
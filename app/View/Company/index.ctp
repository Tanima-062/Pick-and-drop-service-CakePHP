<?php 
	echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]); 
?>
<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
	<div class="contents_type clearfix">

		<div class="company_headline_box">

			<div class="cont_page_head">

				<div class="company_headline_logo">
<?php 
	if($clientInfo['Client']['sp_logo_image']){ 
?>
					<img src="/rentacar/img/logo/square/<?php echo $clientInfo['Client']['id']; ?>/<?php echo $clientInfo['Client']['sp_logo_image']; ?>" alt="<?= $clientInfo['Client']['name']; ?>" width="72" height="72" loading="lazy" importance="low" decoding="async"/>
<?php 
	} 
?>
				</div>
				
				<h1 class="company_headline_name"><?php echo $clientInfo['Client']['name']; ?><br />の予約・プラン比較</h1>
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
			</div>
			<ul class="link_in_page">
				<li class="link_in_page_li"><a href="#company_about" class="link_in_page_a"><?php echo $clientInfo['Client']['name']; ?>について</a></li>
				<li class="link_in_page_li"><a href="#fee" class="link_in_page_a">料金</a></li>
				<li class="link_in_page_li"><a href="#insurance" class="link_in_page_a">免責補償制度</a></li>
				<li class="link_in_page_li"><a href="#today" class="link_in_page_a">貸出当日の流れ</a></li>
			</ul>

			<div class="company_headline">
				<p class="company_headline_cont">
					<?php if(!empty($companyCharacterContents['company-pre-contents'])) {echo $companyCharacterContents['company-pre-contents'];} ?>
				</p>
				<p class="company_headline_cont">
					<?php if(!empty($clientInfo['Client']['clause_pdf'])){ ?>
					<a href="/rentacar/files/clause_pdf/<?php echo $clientInfo['Client']['clause_pdf']; ?>">約款・規約(PDF)</a>
					<?php } ?>
				</p>
			</div>
		</div>

		<section>
<?php
	echo $this->Form->create('Search', array('controller' => 'searches', 'action' => 'index', 'inputDefaults' => array(
		'label' => false,
		'div' => false,
		'hiddenField' => false,
		'legend' => false,
		'fieldset' => false,
	),'type' => 'get', 'class' => 'contents_search'));
	//検索　エリア・日付・人数などは共通処理のためエレメント化
	echo $this->element('searchform_main');
?>
			<div class="searchform_submit_section_wrap">
<?php
	echo $this->element('searchform_submit');
?>
			</div>
<?php
	echo $this->Form->end();
?>
		</section>

		<section class="contents_wrap">
<!-- ランキング掲載 -->
<?php
	if(!empty($landmarkRanking)) {
?>
		<div class="ranking content_section">
			<h2 class="company_contents_h2"><?php echo $clientInfo['Client']['name']; ?>の人気エリアランキング</h2>
<?php
		unset( $landmarkRanking[3] );
		foreach ($landmarkRanking as $rankingNum => $ranking) {
?>
			<div>
				<ul>
					<li class="popular_area_ranking_item">
						<h3><span class="ranking-title"><i class="icm-royal-crown -icon"></i> <?=$rankingNum +1 ?>位　<?=$ranking['name']?></span></h3>
						<div class="ranking-inner">
<?php
			$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
			$type = $activeWebp ? '.webp' : '.png';
			foreach ($ranking['bestPriceCarTypes'] as $carTypeId => $bestPriceCarTypes) {
				if ($rankingNum == 0) {
					if($carTypeId == 1) {
						$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' 軽自動車" width="120" height="82.8" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' コンパクト" width="150" height="82.5" loading="lazy" importance="low" decoding="async">';
					} else if($carTypeId == 3) {
						$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' ハイブリッド" width="150" height="86" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' セダン" width="150" height="64.5" loading="lazy" importance="low" decoding="async">';
					} else if($carTypeId == 9) {
						$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' ミニバン" width="150" height="82" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2" alt="'.$clientInfo['Client']['name']." ".$ranking['name'].' ワゴン" width="150" height="82.5" loading="lazy" importance="low" decoding="async">';
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
<!-- ランキング掲載ここまで -->


<?php 
    // <!-- youtube -->
	if(isset($companyCharacterContents['company-youtube_0_youtube-video'])){
		$youtubeurl = $companyCharacterContents['company-youtube_0_youtube-video'];
		$youtubeurlembed = str_replace('watch?v=','embed/', $youtubeurl);
?>
		<section class="rentacar_txt_area content_section company_youtube">
			<h2 class="company_contents_h2"><?= $clientInfo['Client']['name']; ?>の人気動画</h2>

			<div class="content_wrap">
				<h3 class="company_youtube_title"><i class="icm-skyticket-logo-round"></i><?= $companyCharacterContents['company-youtube_0_youtube-sub-title']; ?></h3>

				<iframe width="800" height="450" src="<?= $youtubeurlembed; ?>?enablejsapi=1" title="<?= $clientInfo['Client']['name']; ?>の人気動画" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

				<div class="youtube-home-link-wrap"><a class="youtube-home-link" href="https://www.youtube.com/c/AdventureInc_JP">skyticket 公式 YouTubeチャンネル<i class="icm-external-link"></i></a></div>
			</div>

			<button class="btn-type-sub" onclick="location.href='<?= $companyCharacterContents['company-youtube_0_youtube-link']; ?>'"><i class="icm-youtube"></i><?= $clientInfo['Client']['name']; ?>の動画をもっと見る</button>
			
		</section>
<?php
	}
    // <!-- youtube ここまで -->
?>


<?php 
	if(isset($companyCharacterContents['company-outline'])){
?>
		<section id="company_about" class="rentacar_txt_area content_section">
			<h2 class="company_contents_h2"><?php echo $clientInfo['Client']['name']; ?>について</h2>
<?php 
		foreach($companyCharacterContents['company-outline'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if(!empty($val['value'])){
					$alt_text = $val['value'] 
?>

			<h3 class="clearfix"><?=$val['value']?></h3>

<?php 
				}
			}
?>

<?php  
			if($val['field'] == 'rentacar-body-titlem'){
				if(!empty($val['value'])){
					$alt_text = $val['value'] 
?>
			<h4 class="clearfix"><?=$val['value']?></h4>
<?php 
				}
			}
?>

<?php  
			if($val['field'] == 'rentacar-body-text'){
				if(!empty($val['value'])){
?>
			<div class="renta_body_wrap"><?=$val['value']?></div>
<?php 
				}
			}
?>

<?php  
			if($val['field'] == 'rentacar-body-img'){
				if(!empty($val['value'])){
?>
			<p class="renta_img_wrap">
				<img src="<?=$val['value']?>" class="company_contents_img" alt="<?= $alt_text ?>" width="100%"  loading="lazy" importance="low" decoding="async"/>
			</p>
<?php 
				}
			}
?>
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
		<div class="yotpo_container content_section">
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
				</a>
			</div>
		</div>
<?php 
	}
?>




<?php 
	if(isset($companyCharacterContents['company-fee-contents'])){ 
?>
		<section id="fee" class="rentacar_txt_area content_section">
			<h2 class="company_contents_h2"><?php echo $clientInfo['Client']['name']; ?>の料金</h2>
<?php 
		foreach($companyCharacterContents['company-fee-contents'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>
			<h3 class="clearfix"><?=$val['value']?></h3>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-titlem'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>
			<h4 class="clearfix"><?=$val['value']?></h4>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-text'){
				if(!empty($val['value'])){
?>
			<div class="renta_body_wrap"><?=$val['value']?></div>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-img'){
				if(!empty($val['value'])){
?>
			<p class="renta_img_wrap">
				<img src="<?=$val['value']?>" class="company_contents_img" alt="<?= $alt_text ?>" width="100%" loading="lazy" importance="low" decoding="async"/>
			</p>
<?php 
				}
			}
		}
?>
		</section>
<?php 
	}
?>





<?php 
	if(isset($companyCharacterContents['company-insurance-contents'])){
?>
		<section id="insurance" class="rentacar_txt_area content_section">
			<h2 class="company_contents_h2"><?php echo $clientInfo['Client']['name']; ?>の免責補償制度</h2>
<?php 
		foreach($companyCharacterContents['company-insurance-contents'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>
			
			<h3 class="clearfix"><?=$val['value']?></h3>

<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-titlem'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>

			<h4 class="clearfix"><?=$val['value']?></h4>

<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-text'){
				if(!empty($val['value'])){
?>
			<div class="renta_body_wrap"><?=$val['value']?></div>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-img'){
				if(!empty($val['value'])){
?>
			<p class="renta_img_wrap">
				<img src="<?=$val['value']?>" class="company_contents_img" alt="<?= $alt_text ?>" width="100%" loading="lazy" importance="low" decoding="async"/>
			</p>
<?php 
				}
			}
		}
?>
		</section>
<?php 
	} 
?>





<?php 
	if(isset($companyCharacterContents['company-today-contents'])){
?>	
		<section id="today" class="rentacar_txt_area content_section">
			<h2 class="company_contents_h2"><?php echo $clientInfo['Client']['name']; ?>での貸出当日の流れ</h2>
<?php 
		foreach($companyCharacterContents['company-today-contents'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>
			<h3 class="clearfix"><?=$val['value']?></h3>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-titlem'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>
			<h4 class="clearfix"><?=$val['value']?></h4>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-text'){
				if(!empty($val['value'])){
?>
			<div class="renta_body_wrap"><?=$val['value']?></div>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-img'){
				if(!empty($val['value'])){
?>
			<p class="renta_img_wrap">
				<img src="<?=$val['value']?>" class="company_contents_img" alt="<?= $alt_text ?>" width="100%" loading="lazy" importance="low" decoding="async"/>
			</p>
<?php 
				}
			}
		}
?>
		</section>
<?php 
	}
?>



<?php 
	if(isset($companyCharacterContents['rentacar-body-list'])){
?>
		<section class="rentacar_txt_area">
<?php 
		foreach($companyCharacterContents['rentacar-body-list'] as $key => $val){
			if($val['field'] == 'rentacar-body-title'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>
			<h3 class="clearfix"><?=$val['value']?></h3>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-titlem'){
				if(!empty($val['value'])){
					$alt_text = $val['value']
?>
			<h4 class="clearfix"><?=$val['value']?></h4>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-text'){
				if(!empty($val['value'])){
?>
			<div class="renta_body_wrap"><?=$val['value']?></div>
<?php 
				}
			}
?>
<?php  
			if($val['field'] == 'rentacar-body-img'){
				if(!empty($val['value'])){
?>
			<p class="renta_img_wrap">
				<img src="<?=$val['value']?>" class="company_contents_img" alt="<?= $alt_text ?>" width="100%" loading="lazy" importance="low" decoding="async"/>
			</p>
<?php 
				}
			}
		}
?>
		</section>
<?php 
	}
?>



<?php
	if (!$fromRentacarClient) {
		echo $this->element('prefecture_linklist');

		echo $this->element('popular_airport_linklist');
	}
?>


<?php
	if (!$fromRentacarClient) {
?>
		<div class="rentacar_txt_area content_section">
			<h2 class="company_list_title company_contents_h2"><?php echo $clientInfo['Client']['name']; ?>の営業所を都道府県から探す</h2>
			<ul>

<?php
		foreach($prefectureInfoList as $k => $v){
			if(!empty($prefectureAreaInfo[$k]) && $k > 0){
?>
				<li id="prefectureArea<?=$k;?>" class="prefectureArea button">
					<h3 class="prefectureArea_text"><?php echo $v ?></h3>
					<span class="icon icm-right-arrow"></span>
				</li>
<?php
				if($prefectureOfficeInfo[$k]){ 
?>
				<li>
					<div class="prefectureOffice">
<?php
					foreach($prefectureOfficeInfo[$k] as $k2 => $v2){
?>
						<a href="/rentacar/company/<?=$v2['client']['url'];?>/<?=$v2['Office']['url'];?>/"> <?=$v2['Office']['name'];?></a>
<?php
					}
?>
					</div>
				</li>
<?php
				}
			}
		}
?>

			</ul>
		</div>
<?php
	}
?>
		</section><!-- //contents_wrap -->
	</div><!-- //contents_type -->

</div><!-- //wrap contents -->


<script>
$(function(){
	// 都道府県検索アコーディオン
	$('.button').click(function() {
		$(this).next().slideToggle();
		$(this).toggleClass('open');
		$('.button').not($(this)).next().slideUp();
		$('.button').not($(this)).removeClass('open');
	}).next().slideUp();



    function get_toparea(prefecture, clientid) {
		$.ajax({
			type: "GET",
			url: "/rentacar/areas/get_topareakey_byclientid/" + prefecture + "/" + clientid + "/",
			success: function(area) {
				ajaxGetOfficeData(clientid, '1', area);
				if(area && area != 0){
					$('#area_id option[value="'+area+'"]').prop('selected', true);
				}
			}
		});
    }



    function ajaxGetOfficeData(clientId, type, conditionId) {

		var url = '/rentacar/company/ajaxAction/';

        if(conditionId && conditionId != 0){
			$.ajax({
				url: url,
				method: 'POST',
				dataType: 'json',
				data: {
					clientId: clientId,
					type: type,
					conditionId: conditionId
				}
			}).done(function(data){
				$("#company_store_ul").empty();
				for (var i=0 ; i<data.length ; i++){
					if( data[i]["Client"]["url"] != '' && data[i]["Office"]["url"] != ''){
						$("#company_store_ul").append('<li><a href="/rentacar/company/'+data[i]["Client"]["url"]+'/'+data[i]["Office"]["url"]+'/"><span>'+data[i]["Office"]["name"]+'</span></a></li>');
					} else {
						$("#company_store_ul").append('<li><a href="/rentacar/localstoredetail?store_id='+data[i]["Office"]["id"]+'"><span>'+data[i]["Office"]["name"]+'</span></a></li>');
					}
				}
			}).fail(function(){
				alert('データの取得に失敗しました。');
				return false;
			});
        } else {
           $("#company_store_ul").empty();
        }
    }

});
</script>
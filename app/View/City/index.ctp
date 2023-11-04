<?php 
	echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]); 
?>
<link rel="stylesheet" type="text/css" href="/rentacar/css/swiper.min.css">
<script type="text/javascript" src="/rentacar/js/swiper.min.js"></script>

<div class="wrap contents clearfix city">
	<?php echo $this->element('progress_bar'); ?>
	<div class="cont_page_head">
		<h1 class="page_mainhead"><?= $areaList['0']['Area']['name']; ?>の格安レンタカーを比較・予約する</h1>
<?php
	if(!empty($cityContents['city-single-city-en'])){
?>
		<span class="page_subhead"><?= $cityContents['city-single-city-en']; ?></span>
<?php
	}
?>
	</div>
	<ul class="link_in_page is_from_airport">
		<li><a href="#03">レンタカー会社</a></li>
<?php
	if( !empty($yotpoReviews) ){
?>
		<li><a href="#reviews">口コミ</a></li>
<?php
	}
?>
		<li><a href="#04">乗り捨て料金</a></li>
<?php
	if (!empty($airportLinkCdList)){
?>
		<li><a href="#06">近隣空港</a></li>
<?php
	}
	if (!empty($cityInfoList)){
?>
		<li><a href="#05">市区町村</a></li>
<?php
	}
	if (!empty($landmarkList['area'])){
?>
		<li><a href="#07">近隣都市</a></li>
<?php
	}
?>
		<li><a href="#09">レンタカー情報</a></li>
	</ul>

	<!--検索エリア -->
	<section>
		<section class="content_section">
			<?php
				echo $this->Form->create('Search',array('controller'=>'searches','action'=>'index', 'inputDefaults'=>array(
					'label'=>false,
					'div'=>false,
					'hiddenField'=>false,
					'legend'=>false,
					'fieldset'=>false,
				),'type'=>'get','class'=>'contents_search'));
			?>
				<?php
					//検索　エリア・日付・人数などは共通処理のためエレメント化
					echo $this->element('searchform_main');
				?>
				<div class="searchform_submit_section_wrap">
					<?php echo $this->element('searchform_submit'); ?>
				</div>
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
			<div>
				<ul>
					<li>
						<div class="ranking-inner">
<?php
		$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
		$type = $activeWebp ? '.webp' : '.png';
		foreach ( $bestPriceCarTypes as $carTypeId => $ranking) {
			if($carTypeId == 1) {
				$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2">';
			} else if($carTypeId == 3) {
				$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2">';
			} else if($carTypeId == 9) {
				$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2">';
			}
			if ($carTypeId == 9) {
				$carCapacity = $typeCapacityList[$carTypeId]."人乗り～";
			} else {
				$carCapacity = $typeCapacityList[$carTypeId]."人乗り";
			}
?>
							<a href="<?=$ranking['url']?>">
								<p class="-carModels"><?=$ranking['name']?></p>
								<p class="carImg"><?=$carImg?></p>
								<div class="bottom">
									<p><span class="carCapacity"><?=$carCapacity;?></span><span class="-marker">最安値</span><span class="-price"><?=$ranking['price']?></p>
								</div>
							</a>
<?php
		}
?>
						</div>
					</li>
				</ul>
			</div>
		</div>
<?php
	}
?>
<!-- プラン掲載ここまで -->

		<p class="cont_text_wrap"><?= $cityContents['city-single-city-head']; ?></p>
	</section>
	<!--検索エリア End -->

	<!--cp_area -->
	<div class="page_title" id="03">
		<h2><?= $areaList['0']['Area']['name']; ?>の格安レンタカー会社と店舗一覧</h2>
	</div>
	<?php echo $this->element('pc_shoplist'); ?>

<?php 
	if(!empty($cityContents['city-single-price']) OR !empty($cityContents['city-single-price-text'])){
?>
	<p class="page_title_text"><?= $cityContents['city-single-shop-text']; ?></p>
	<div class="page_title" id="04">
		<h2><?= $areaList['0']['Area']['name']; ?>からの乗り捨て料金一覧</h2>
	</div>

<?php 
		if(!empty($cityContents['city-single-price'])){
?>
	<table class="fromairport_place_time_tbl forairport_price_list">
		<?= $cityContents['city-single-price']; ?>
	</table>
<?php 
		}
?>

<?php 
		if(!empty($cityContents['city-single-price-text'])){
?>
	<p class="page_title_text">
		<?= $cityContents['city-single-price-text']; ?>
	</p>
<?php 
		}
?>
	<p class="text_center margin-btm30">
		<?php echo $this->element('btn_select_dropoff'); ?>
	</p>
<?php 
	}
?>
	
<?php
	if( !empty($yotpoReviews) ){
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
	<section id="reviews" class="yotpo_api_custom_wrap">
		<div class="page_title">
			<h2>口コミから<?=$areaName;?>のレンタカーを探す</h2>
		</div>
		<?php echo $this->element('yotpo_review'); ?>
	</section>
<?php 
		}
	}
?>

	<?php echo $this->element('popular_airport_linklist'); ?>

<?php 
	if (!empty($areaLinkCdList)){
?>
	<div class="page_title" id="07">
		<h2><?= $areaList['0']['Area']['name']; ?>の近隣エリアの格安レンタカーを予約</h2>
	</div>
	<ul class="from_airport_link">
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
?>

<?php 
	if (!empty($airportLinkCdList)){
?>
	<div class="page_title" id="06">
		<h2><?= $areaList['0']['Area']['name']; ?>の近隣空港の格安レンタカーを比較・予約</h2>
	</div>
	<ul class="from_airport_link">
<?php
		foreach($airportLinkCdList AS $key => $val){
?>
		<li><a href="/rentacar/<?= $base_url . $key ?>/"><?= $val['short_name'] ?></a></li>
<?php 
		}
?>
	</ul>
<?php 
	}
?>

<?php 
	if (!empty($cityInfoList)){
?>
	<div class="page_title" id="05">
		<h2><?= $areaList['0']['Area']['name']; ?>エリアの市区町村のレンタカーを比較・予約</h2>
	</div>
	<ul class="from_airport_link">
<?php
		foreach($cityInfoList AS $val){
?>
		<li><a href="/rentacar/<?= $base_url . $val['City']['link_cd'] ?>/"><?= $val['City']['name'] ?></a></li>
<?php 
		}
?>
	</ul>
<?php 
	}
?>

	<div class="page_title" id="09">
		<h2><?= $areaList['0']['Area']['name']; ?>に関する格安レンタカー情報</h2>
	</div>

<?php 
	if(!empty($cityContents['0']['head'])){
?>
	<div class="fromairport_rentacar_info">
		<section class="contents_type_main_search">
<?php 
		foreach($cityContents as $v){
			if (!empty($v['head'])){
?>
			<h3 class="rentacar_text_ttl"><?= $v['head']; ?></h3>
<?php 
				if (!empty($v['img'])){ 
?>
			<img src="<?= $v['img']; ?>">
<?php 
				}
?>
			<p class="from_about_contents"><?= $v['text']; ?></p>
<?php 
			}
		}
?>
		</section>
	</div>
<?php 
	}
?>

</div>

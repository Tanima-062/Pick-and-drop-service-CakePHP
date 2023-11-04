<?php 
	echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]); 
?>
<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
	$type = $activeWebp ? '.webp' : '.png';
?>
	<div class="cont_page_head">
		<h2 class="page_mainhead"><?= $city['name']; ?>の格安レンタカーを比較・予約する</h2>
	</div>
	<ul class="link_in_page is_from_airport">
<?php
	if (!empty($officeInfoList)){
?>
		<li><a href="#03">レンタカー会社</a></li>
<?php
	}
?>
<?php
	if( !empty($yotpoReviews) ){
?>
		<li><a href="#reviews">口コミ</a></li>
<?php
	}
?>
<?php
	if (!empty($neighborList)){
?>
		<li><a href="#04">近隣市区町村</a></li>
<?php
	}
	if (!empty($airportLinkCdList) || !empty($majorStationList)){
?>
		<li><a href="#05">近隣空港・駅</a></li>
<?php
	}
?>
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


		<!-- プラン掲載 -->
		<?php
		if(!empty($bestPriceCarTypes)) {
		?>
			<div class="ranking">
				<h2><?= $city['name']; ?>でレンタカー最安値を探す</h2>

				<div>
					<ul>
						<li>
							<div class="ranking-inner">
		<?php
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

		<p class="cont_text_wrap"><?= $activeContents; ?></p>
	</section>
	<!--検索エリア End -->

<?php
	if (!empty($officeInfoList)){
?>
	<div class="page_title" id="03">
		<h2><?= $city['name']; ?>の格安レンタカー会社と店舗一覧</h2>
	</div>
	<?php echo $this->element('pc_shoplist'); ?>
<?php
	}
?>
	
	<?php echo $this->element('popular_airport_linklist'); ?>

<?php
	if( !empty($yotpoReviews) ){
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
		<section id="reviews" class="yotpo_api_custom_wrap">
			<div class="page_title">
				<h2>口コミから<?=$city['name'];?>のレンタカーを探す</h2>
			</div>
			<?php echo $this->element('yotpo_review'); ?>
		</section>
<?php 
		}
	}
?>

	<?php if (!empty($neighborList)): ?>
		<div class="page_title" id="04">
			<h2><?= $city['name']; ?>の近隣市区町村の格安レンタカーを比較・予約</h2>
		</div>
		<ul class="from_airport_link">
			<?php foreach($neighborList AS $key => $val):?>
				<li><a href="<?=$val['url']?>"><?=$val['name']?></a></li>
			<?php endforeach;?>
		</ul>
	<?php endif; ?>
	<?php if (!empty($airportLinkCdList) || !empty($majorStationList)): ?>
		<div class="page_title" id="05">
			<h2><?= $city['name']; ?>の近隣空港・主要駅の格安レンタカーを比較・予約</h2>
		</div>
		<ul class="from_airport_link">
			<?php foreach($airportLinkCdList AS $key => $val):?>
				<li><a href="/rentacar/<?=$base_url . $key?>/"><?=$val['short_name']?></a></li>
			<?php endforeach;?>
			<?php foreach($majorStationList AS $key => $val):?>
				<li><a href="/rentacar/<?=$base_url . $val['url']?>/"><?=$val['name']?></a></li>
			<?php endforeach;?>
		</ul>
	<?php endif; ?>
</div>
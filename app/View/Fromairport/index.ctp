<?php
	echo $this->Html->script(['/js/smoothscroll','/js/pc_faq_tablist'],['defer'=>true, 'inline' => false]);
?>

<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
	<div class="cont_page_head">
		<h1 class="page_mainhead"><?= $landmark['name']; ?>のレンタカー <?= $landmark['name']; ?>の店舗から最安値を比較</h1>
<?php
	if(!empty($airportContents['airport-single-airport-en'])){
?>
		<span class="page_subhead"><?= $airportContents['airport-single-airport-en']; ?></span>
<?php
	}
?>
	</div>
	<ul class="link_in_page is_from_airport">
		<li><a href="#03">レンタカー会社</a></li>
<?php
	if( !empty($yotpoReviews) ){
?>
		<li><a href="#10">口コミ</a></li>
<?php
	}
?>
		<li><a href="#04">乗り捨て料金</a></li>
		<li><a href="#05">受付情報</a></li>
<?php
	if (!empty($airportLinkCdList)){
?>
		<li><a href="#06">近隣空港</a></li>
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

	<?php echo $this->element('pc_campaign_banner_hokkaido'); ?>

	<!--検索エリア -->
		<section>
<?php
	echo $this->Form->create('Search',array('controller'=>'searches','action'=>'index', 'inputDefaults'=>array(
		'label'=>false,
		'div'=>false,
		'hiddenField'=>false,
		'legend'=>false,
		'fieldset'=>false,
	),'type'=>'get','class'=>'contents_search'));
	// 検索　エリア・日付・人数などは共通処理のためエレメント化
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

		<section class="pref_cont_wrap">
		<p class="cont_text_wrap"><?= $airportContents['airport-single-head']; ?></p>
	</section>
	<!--検索エリア End -->

	<!-- プラン掲載 -->
	<?php
	if(!empty($bestPriceCarTypes)) {
	?>
		<div class="ranking">
			<h2><?= $landmark['name']; ?>でレンタカー最安値を探す</h2>

			<div>
				<ul>
					<li>
						<div class="ranking-inner">
	<?php
			$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
			$type = $activeWebp ? '.webp' : '.png';
			foreach ( $bestPriceCarTypes as $carTypeId => $ranking) {
					if($carTypeId == 1) {
						$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1" alt="'.$landmark['name'].' 軽自動車 レンタカー"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2" alt="'.$landmark['name'].' コンパクト レンタカー">';
					} else if($carTypeId == 3) {
						$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1" alt="'.$landmark['name'].' ハイブリッド レンタカー"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2" alt="'.$landmark['name'].' セダン レンタカー">';
					} else if($carTypeId == 9) {
						$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1" alt="'.$landmark['name'].' ミニバン レンタカー"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2" alt="'.$landmark['name'].' ワゴン レンタカー">';
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


	<!--cp_area -->
	<div class="page_title" id="03">
		<h2><?= $landmark['name']; ?>の格安レンタカー会社と店舗一覧</h2>
	</div>
	<?php echo $this->element('pc_shoplist'); ?>

<?php
	if(!empty($airportContents['airport-single-price']) OR !empty($airportContents['airport-single-price-text'])){
?>
	<p class="page_title_text"><?= $airportContents['airport-single-shop-text']; ?></p>
	<div class="page_title" id="04">
		<h2><?= $landmark['name']; ?>からの乗り捨て料金一覧</h2>
	</div>
<?php
		if(!empty($airportContents['airport-single-price'])){
?>
	<table class="fromairport_place_time_tbl forairport_price_list">
	<?= $airportContents['airport-single-price']; ?>
	</table>
<?php
		}
		if(!empty($airportContents['airport-single-price-text'])){
?>
	<p class="page_title_text"><?= $airportContents['airport-single-price-text']; ?></p>
<?php
		}
?>
	<p class="text_center margin-btm30">
		<?php echo $this->element('btn_select_dropoff'); ?>
	</p>
	<div class="page_title" id="05">
		<h2><?= $landmark['name']; ?>の受付カウンター情報</h2>
	</div>
<?php
	}
?>
	<?= $airportContents['airport-single-counter']; ?>
	<p class="page_title_text"><?= $airportContents['airport-single-counter-text']; ?></p>


<?php
	if( !empty($yotpoReviews) ){
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
			<section id="reviews" class="yotpo_api_custom_wrap">
				<div class="page_title" id="10">
					<h2>口コミから<?= $landmark['name']; ?>のレンタカーを探す</h2>
				</div>
				<?php echo $this->element('yotpo_review'); ?>
			</section>
<?php 
		}
	}
?>

<?php echo $this->element('prefecture_linklist'); ?>

<?php echo $this->element('popular_airport_linklist'); ?>

<?php
	if(!empty($airportLinkCdList)){
?>
	<div class="page_title" id="06">
		<h2><?= $landmark['name']; ?>の近隣空港の格安レンタカーを比較・予約</h2>
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
	if(!empty($landmarkList['area'])){
?>
	<div class="page_title" id="07">
		<h2><?= $landmark['name']; ?>の近隣エリアの格安レンタカーを予約</h2>
	</div>
	<ul class="from_airport_link">
<?php
		foreach($landmarkList['area'] as $k => $v){
?>
		<li>
			<a href="/rentacar/<?= $base_url . $areaLinkCd[$k]['area_link_cd'] ?>/"><?= $v ?></a>
		</li>
<?php
		}
?>
	</ul>
<?php
	}
?>
	<div class="page_title" id="09">
		<h2><?= $landmark['name']; ?>に関する格安レンタカー情報</h2>
	</div>
<?php
	if(!empty($airportContents['airport-single-contents-list'])){
?>
	<div class="fromairport_rentacar_info">
		<section class="contents_type_main_search">
<?php
		foreach($airportContents['airport-single-contents-list'] as $v){
			if(!empty($v['contents-head'])){
?>
			<h3 class="rentacar_text_ttl"><?= $v['contents-head']; ?></h3>
<?php
				if(!empty($v['contents-img'])){
?>
			<img src="/rentacar/wp/img/<?= $v['contents-img']; ?>?imwidth=750" alt="<?= $v['contents-head']; ?>" loading="lazy" importance="low" decoding="async">
<?php
				}
?>
			<p class="from_about_contents"><?= $v['contents-text']; ?></p>
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

<div class="faq-tab-list">
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
		<div class="wrap">
			<ul class="-head">
<?php
	if (!empty($lowestPriceThisAirport)){
?>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)"><?= $landmark['name']; ?>周辺の<br>レンタカーの最安値はいくらですか？</a>
				</li>
<?php
	};
?>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)"><?= $landmark['name']; ?>に店舗がある<br>レンタカー会社はどちらですか？</a>
				</li>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)">レンタカーは長期で<br>借りられますか？</a>
				</li>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)">どのような車種・オプションが<br>検索できますか？</a>
				</li>
			</ul>
<?php
	if (!empty($lowestPriceThisAirport)){
?>
			<div class="js-tabList-content">
				<p>
					<?= $landmark['name']; ?>周辺のレンタカー料金は、<?= $lowestPriceThisAirport; ?>からとなっております。
				</p>
			</div>
<?php
	};
?>
			<div class="js-tabList-content">
				<p>
					<?= $landmark['name']; ?>周辺に店舗があるレンタカー会社は、<?= $availableCompanies; ?>などです。
				</p>
			</div>
			<div class="js-tabList-content">
				<p>
					1時間や1日などの短時間から、1週間や1ヶ月などの長期までレンタカーを簡単に予約できます。
				</p>
			</div>
			<div class="js-tabList-content">
				<p>
					絞り込み検索の機能を使えば、ミニバンやワゴンなどの7人乗りから10人乗り、外車、高級車などの車種を指定して検索できます。オプションはスタッドレスタイヤ、4WDなどから選ぶことができ、いろいろな車をレンタルできます。
				</p>
			</div>
		</div>
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
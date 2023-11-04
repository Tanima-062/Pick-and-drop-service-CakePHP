<?php
	// echo $this->Html->css(array('prefecture','company', 'common_2.0'), null, array('inline' => false));
?>
<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
	<div class="company_headline_box">
		<div class="company_headline_logo">
<?php
	if($clientInfo['Client']['sp_logo_image']){
?>
			<img src="/rentacar/img/logo/square/<?php echo $clientInfo['Client']['id']; ?>/<?php echo $clientInfo['Client']['sp_logo_image']; ?>" alt="<?php echo $clientInfo['Client']['name']; ?>" width="72" height="72" loading="lazy" importance="low" decoding="async"/>
<?php
	}
?>
		</div>
		<p class="company_headline_name">
			<?php echo $data['cp-head-title']; ?>
		</p>
	</div>

	<section class="campaign_head_wrap">
		<img src="<?php echo $data['cp-head-img']  ?>" alt="<?php echo $data['cp-head-title']; ?>" width="800" height="auto" />
		<p class="cont_text_wrap">
			<?php echo $data['cp-head-text']; ?>
		</p>
	</section>

	<h3 class="page_h3">キャンペーン中のおトクなレンタカーを予約する</h3>
	<section id="js-car_type_orign" class="campaign_car_type">
		<ul class="campaign_car_type_ul">
<?php
	foreach($data['cp-price'] as $price) {
		$searchParams = array();
		$searchParams['return_way'] = 0;

		$defaultYmd = date('Y-m-d');
		$defaultReturnYmd = date('Y-m-d');

		if(!empty($price['cp-price-dep-airport']['value'])){
			$searchParams['airport_id'] = $price['cp-price-dep-airport']['value'];
		}

		if(!empty($price['cp-price-dep-station']['value'])){
			$searchParams['station_id'] = $price['cp-price-dep-station']['value'];
		}

		$departName = '';
		if(!empty($price['cp-price-dep-place']['value'])){
			if($price['cp-price-dep-place']['value'] == 3){
				$searchParams['place'] = 3;
				$airportId = intval($searchParams['airport_id']);
				if(array_key_exists($airportId, $airportList)){
					$departName = $airportList[$airportId];
				}
			} elseif($price['cp-price-dep-place']['value'] == 4){
				$searchParams['place'] = 4;
				$stationId = intval($searchParams['station_id']);
				if(array_key_exists($stationId, $stationList)){
					$departName = $stationList[$stationId]['Station']['name'];
					$searchParams['prefecture'] = $stationList[$stationId]['Station']['prefecture_id'];
				}
			}
		}

		if(!empty($price['cp-price-dep-date']['value'])){
			$d = $price['cp-price-dep-date']['value'];
			$defaultYmd = date('Y-m-d',strtotime("+$d days"));
		}
		$defaultDate = explode('-',$defaultYmd);
		$searchParams['year'] = $defaultDate[0];
		$searchParams['month'] = $defaultDate[1];
		$searchParams['day'] = $defaultDate[2];
		$searchParams['time'] = '11-00';

		if(!empty($price['cp-price-return-date']['value'])){
			$d = $price['cp-price-return-date']['value'];
			$defaultReturnYmd = date('Y-m-d',strtotime("+$d days"));
		}
		$defaultReturnDate = explode('-',$defaultReturnYmd);
		$searchParams['return_year'] = $defaultReturnDate[0];
		$searchParams['return_month'] = $defaultReturnDate[1];
		$searchParams['return_day'] = $defaultReturnDate[2];
		$searchParams['return_time'] = '17-00';
		$searchParamsArray = '';
		if(!empty($price['cp-price-cartype']['value'])){
			$priceCarTypes = $price['cp-price-cartype']['value'];
			foreach($priceCarTypes as $priceCarType){
				//$searchParams['car_type'][] = $priceCarType;
				$searchParamsArray .= '&car_type%5B%5D='.$priceCarType;
			}
		}

		if(!empty($price['cp-price-client']['value'])){
			//$searchParams['client_id'][] = $price['cp-price-client']['value'];
			$searchParamsArray .= '&client_id%5B%5D='.$price['cp-price-client']['value'];
		}

		if(!empty($price['cp-price-option']['value'])){
			foreach($price['cp-price-option']['value'] as $option){
				if($option == 1 || $option == 2){
					$searchParamsArray .= '&smoking_flg='.($option-1);
				} else {
					$searchParamsArray .= '&option%5B%5D='.$option;
				}
			}
		}

		$searchUrl = '/rentacar/searches?'.urldecode(http_build_query($searchParams)).$searchParamsArray;

		if( !empty($priceCarTypes) ){
			$carTypeName = $carTypeList[$priceCarTypes[0]]['CarType']['name'];
			$carTypeCapacity = $carTypeList[$priceCarTypes[0]]['CarType']['capacity'];

			$countCarTypes = count( $priceCarTypes );
			if($countCarTypes > 1){
				$carTypeName .= " 他";
			}
?>
			<li class="campaign_car_type_li">
				<a href="<?= $searchUrl ?>" class="campaign_btn_car_type">
					<p class="campaign_area_name"><?=$departName;?></p>
					<p class="campaign_car_type_wrap">
						<img src="/rentacar/img/car_type_<?=sprintf('%02d', $priceCarTypes[0])?>.png" alt="<?= $clientInfo['Client']['name'].' '.$departName.'店 '.$carTypeName; ?>" />
						<span class="campaign_car_type_name"><?= $carTypeName; ?></span>
					</p>
					<p class="campaign_car_type_cap">（<?= $carTypeCapacity ?>人乗り）</p>
					<p class="campaign_best_price">¥<?= number_format( $price['cp-price-price']['value'] );?>〜</p>
				</a>
				<span class="car_type_best_badge">最安値</span>
			</li>
<?php
		}else{
?>
			<li class="campaign_car_type_li">
				<a href="<?= $searchUrl ?>" class="campaign_btn_car_type">
					<p class="campaign_area_name"><?=$departName;?></p>
					<p class="campaign_car_type_wrap">
						<img src="/rentacar/img/car_type_02.png" alt="<?= $clientInfo['Client']['name'].' '.$departName.'店 コンパクト 他'; ?>" />
						<span class="campaign_car_type_name">コンパクト 他</span>
					</p>
					<p class="campaign_best_price">¥<?= number_format( $price['cp-price-price']['value'] );?>〜</p>
				</a>
				<span class="car_type_best_badge">最安値</span>
			</li>
<?php
		}
	}
?>
		</ul>
	</section>

	<hr class="page_hr" />

	<section class="article_wrap">
<?php
	$is_open_table = false;
	$is_open_cell = false;
	foreach($data['cp-contents'] as $content){
		if($content['field'] == 'rentacar-body-title'){
			if($is_open_cell){
				echo "</div>";
				$is_open_cell = false;
			}
			if($is_open_table){
				echo "</div>";
				$is_open_table = false;
			}
?>
			<h3 class="article_h3"><?php echo $content['value']; ?></h3>
<?php
		}
		if($content['field'] == 'rentacar-body-titlem'){
			if($is_open_cell){
				echo "</div>";
				$is_open_cell = false;
			}
			if($is_open_table){
				echo "</div>";
				$is_open_table = false;
			}
?>
			<h4 class="pref_article_title"><?php echo $content['value']; ?></h4>
			<div class="pref_article_table">
<?php
			$content_subtitle = $content['value'];
			$is_open_table = true;
		}
		if($content['field'] == 'rentacar-body-titles'){
?>
				<h5 class="article_h5"><?php echo $content['value']; ?></h5>
<?php
		}
		if($content['field'] == 'rentacar-body-text'){
?>
				<div class="cont_text_wrap"><?php echo $content['value']; ?></div>
<?php
		}
		if($content['field'] == 'rentacar-body-textm'){
?>
				<div class="pref_article_table_cell">
					<p><?php echo $content['value']; ?></p>
<?php
			$is_open_cell = true;
		}
		if($content['field'] == 'rentacar-body-texts'){
?>
				<div class="article_cont_wrap"><?php echo $content['value']; ?></div>
<?php
		}
		if($content['field'] == 'rentacar-body-img'){
?>
				<div class="pref_article_table_cell">
					<img src="<?php echo $content['value']; ?>" alt="<?= $clientInfo['Client']['name'].' '.$content_subtitle ?>" width="300" height="auto">
				</div>
<?php
		}
		if($content['field'] == 'rentacar-body-imgm'){
?>
				<div class="pref_article_table_cell">
					<img src="<?php echo $content['value']; ?>" alt="<?= $clientInfo['Client']['name'].' '.$content_subtitle ?>" width="300" height="auto" />
				</div>
<?php
		}
		if($content['field'] == 'rentacar-body-imgs'){
?>
				<img src="<?php echo $content['value']; ?>" alt="<?= $clientInfo['Client']['name'].' '.$content_subtitle ?>" width="100%" height="auto">
<?php
		}
	}
?>
		<div id="js-car_type_clone"></div>
	</section>
	
	<?php echo $this->element('popular_airport_linklist'); ?>
	
<?php
	// YOTPO ReviewsWidget
	if($yotpo_is_active && $use_yotpo){ 
?>
	<section>
		<div class="yotpo_container">
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
				<a class="review_btn" href="/rentacar/company/toyota/review/">
					<span>レビューをもっと読む</span>
				</a>
			</div>
		</div>
	</section>
<?php 
	} 
?>

</div>

<script>
$(function(){
	$("#js-car_type_orign").clone(true).appendTo("#js-car_type_clone");
});
</script>

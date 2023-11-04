<section>
	<?php echo $this->element('sp_pagetitle_companypage'); ?>

	<img src="<?php echo $data['cp-head-img']  ?>" alt="<?php echo $data['cp-head-title']; ?>" width="100%" height="auto" />
</section>

<?php echo $this->element('sp_readmore_company_outline', ['outlineText' => $data['cp-head-text']]); ?>

<div class="page_bg">
	<section id="js-car_type_orign" class="page_section">
		<h3 class="page_h2">キャンペーン中の格安レンタカー</h3>
		<div class="car_type_wrap">
			<ul>
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
?>
				<li class="car_type_li">
					<a href="<?= $searchUrl ?>" class="car_type_cont">
						<div class="car_type_img_wrap">
							<span class="campaign_car_type_name">
<?php
		if( !empty($departName) ){
			echo $departName."<br />";
		}

		if( !empty($priceCarTypes) ){
			foreach($priceCarTypes as $priceCarType){
				echo $carTypeList[$priceCarType]['CarType']['name']." ";
			}
		}
?>
							</span>
						</div>
						<div class="car_type_best_price">
							<span class="icon_best_price">最安値</span>¥<?= number_format( $price['cp-price-price']['value'] );?>
						</div>
					</a>
					<i class="fa fa-chevron-circle-right"></i>
				</li>
<?php
		
	}
?>
			</ul>
		</div>
	</section>

	<section class="page_section">
		
<?php
	$openH3div = false;
	foreach($data['cp-contents'] as $content){
		if($content['field'] == 'rentacar-body-title'){
			if($openH3div){
				echo '</div></div></section>';
				$openH3div = false;
			}
?>
		<h3 class="page_h2"><?php echo $content['value']; ?></h3>
<?php
		}

		if($content['field'] == 'rentacar-body-text'){
?>
		<section class="article_section">
			<p class="article_cont"><?php echo $content['value']; ?></p>
		</section>
<?php
		}

		if($content['field'] == 'rentacar-body-titlem'){
			if($openH3div){
				echo '</div></div></section>';
			}
			$content_subtitle = $content['value'];
			$openH3div = true;
?>
		<section class="article_section">
			<h3 class="article_title"><?php echo $content['value']; ?></h3>
			<div class="article_wrap">
				<div class="article_body">
<?php
		}

		if($content['field'] == 'rentacar-body-titles'){
?>
					<h5 class="article_h5"><?php echo $content['value']; ?></h5>
<?php
		}

		if($content['field'] == 'rentacar-body-textm'){
?>
					<div class="article_cont"><?php echo $content['value']; ?></div>
<?php
		}

		if($content['field'] == 'rentacar-body-texts'){
?>
					<div class="article_cont"><?php echo $content['value']; ?></div>
<?php
		}

		if($content['field'] == 'rentacar-body-img'){
?>
					<div class="article_img">
						<img src="<?php echo $content['value']; ?>" alt="<?= $clientInfo['Client']['name'].' '.$content_subtitle ?>">
					</div>
<?php
		}

		if($content['field'] == 'rentacar-body-imgm'){
?>
					<div class="article_img">
						<img src="<?php echo $content['value']; ?>" alt="<?= $clientInfo['Client']['name'].' '.$content_subtitle ?>">
					</div>
<?php
		}

		if($content['field'] == 'rentacar-body-imgs'){
?>
					<div class="article_img">
						<img src="<?php echo $content['value']; ?>" alt="<?= $clientInfo['Client']['name'].' '.$content_subtitle ?>">
					</div>
<?php
		}
	}
?>
<?php
	if($openH3div){
		echo '</div></div></section>';
	}
?>
	</section>
	
	<section id="js-car_type_clone" class="page_section">
	</section>
	
	<?php echo $this->element('sp_popular_airport_linklist'); ?>

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
				<a class="review_btn" href="/rentacar/company/<?=$clientInfo['Client']['url'];?>/review/">
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
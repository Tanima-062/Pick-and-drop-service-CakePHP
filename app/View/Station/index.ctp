<?php 
	echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]); 
?>
<link rel="stylesheet" type="text/css" href="/rentacar/css/swiper.min.css">
<script type="text/javascript" src="/rentacar/js/swiper.min.js"></script>
<script type="text/javascript" src="/rentacar/js/station2.js" async></script>

<div class="wrap contents clearfix">
	<?php echo $this->element('progress_bar'); ?>
	<div class="cont_page_head">
		<h1 class="page_mainhead"><?=$stationName?>の格安レンタカーを比較・予約する</h1>
<?php
	if(!empty($stationContents['station-single-station-en'])){
?>
		<span class="page_subhead"><?= $stationContents['station-single-station-en']; ?></span>
<?php
	}
?>
	</div>
	<ul class="link_in_page is_from_airport">
		<li><a href="#01">最安値カレンダー</a></li>
		<li><a href="#02">車種から探す</a></li>
		<li><a href="#03">口コミから探す</a></li>
		<li><a href="#04">駅周辺から探す</a></li>
		<li><a href="#05">駅で借りる</a></li>
		<li><a href="#06">乗り捨て料金</a></li>
		<li><a href="#07">レンタカー情報</a></li>
	</ul>
	<!--検索エリア -->
	<section class="content_section">
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
					// submitボタン
					echo $this->element('searchform_submit');
				?>
			</div>
		<?php
			echo $this->Form->end();
		?>
	</section><!--検索エリア End -->

	<!-- プラン掲載 -->
<?php
	if(!empty($bestPriceCarTypes)) {
?>
	<div class="ranking">
		<h2><?=$stationName?>のレンタカー最安値を探す</h2>

		<div>
			<ul>
				<li>
					<div class="ranking-inner">
<?php
		$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
		$type = $activeWebp ? '.webp' : '.png';
		foreach ( $bestPriceCarTypes as $carTypeId => $ranking) {
			if($carTypeId == 1) {
				$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1" alt="'.$stationName.'軽自動車レンタカー" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2" alt="'.$stationName.'コンパクトレンタカー" loading="lazy" importance="low" decoding="async">';
			} else if($carTypeId == 3) {
				$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1" alt="'.$stationName.'ハイブリッドレンタカー" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2" alt="'.$stationName.'セダンレンタカー" loading="lazy" importance="low" decoding="async">';
			} else if($carTypeId == 9) {
				$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1" alt="'.$stationName.'ミニバンレンタカー" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2" alt="'.$stationName.'ワゴンレンタカー" loading="lazy" importance="low" decoding="async">';
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

	<!-- 車種別最安値カレンダー -->
	<section id="best_price_cal" class="pref_cont_wrap">
		<h2 id="01" class="station2_h3"><?=$stationName?>のレンタカーを最安値カレンダーから探す</h2>
		<div class="best_price_cal_container">
			<div id="best_price_cal_lead" class="best_price_cal_lead"></div>
			<div id="best_price_cal_tab" class="best_price_cal_tab"></div>
			<div id="best_price_cal_nav" class="best_price_cal_nav"></div>
			<div id="best_price_cal_calendar" class="best_price_cal_calendar"></div>
		</div>
	</section>

	<!-- カレンダーテンプレート -->
	<!-- リード文テンプレート -->
	<script id="best_price_cal_template_lead" type="text/template">
		<p><%= monthString %>の<%= stationName %>のレンタカー最安値は日帰り<strong><%= monthlyBestPrice %></strong>ご用意しています。</p>
		<p>レンタルする日数やプランにより料金は変動します。カレンダーでご利用日をお選びください。</p>
	</script>
	<!-- タブテンプレート -->
	<script id="best_price_cal_template_tab" type="text/template">
		<nav>
			<ul role="tablist" data-year="<%= year %>" data-month="<%= month %>" >
				<li data-tab="1" aria-selected="<%= selectedType1 %>" role="tab">
					<div>
						軽自動車
						<strong><%= bestPriceType1 %></strong>
					</div>
				</li>
				<li data-tab="2" aria-selected="<%= selectedType2 %>" role="tab">
					<div>
						コンパクトカー
						<strong><%= bestPriceType2 %></strong>
					</div>
				</li>
				<li data-tab="3" aria-selected="<%= selectedType3 %>" role="tab">
					<div>
						ミドル・セダン
						<strong><%= bestPriceType3 %></strong>
					</div>
				</li>
				<li data-tab="95" aria-selected="<%= selectedType95 %>" role="tab">
					<div>
						ミニバン・ワゴン
						<strong><%= bestPriceType95 %></strong>
					</div>
				</li>
			</ul>
		</nav>
	</script>

	<!-- アローテンプレート -->
	<script id="best_price_cal_template_arrow" type="text/template">
		<button
			type="button" role="button" aria-label="<%= label %>" data-nav="<%= nav %>"
			data-year="<%= year %>"
			data-month="<%= month %>"
			data-type="<%= currentType %>"
		><%= arrow %></button>
	</script>

	<!-- ナビゲーションテンプレート -->
	<script id="best_price_cal_template_nav" type="text/template">
		<p><%= currentYearMonth %></p>
		<nav>
			<%= prev %>
			<%= next %>
		</nav>
	</script>

	<!-- カレンダーヘッダーテンプレート -->
	<script id="best_price_cal_template_cal_header" type="text/template">
		<table>
			<thead>
				<tr>
					<th>日</th>
					<th>月</th>
					<th>火</th>
					<th>水</th>
					<th>木</th>
					<th>金</th>
					<th>土</th>
				</tr>
			</thead>
			<tbody>
	</script>

	<!-- カレンダー日付テンプレート -->
	<script id="best_price_cal_template_cal_day" type="text/template">
		<div data-publicholiday="<%= isPublicHoliday %>">
			<a <%= href %>>
				<p class="best_price_cal_calendar_date"><%= date %></p>
				<p class="best_price_cal_calendar_price" data-bestprice="<%= isBestPrice %>">
					<%= price %>
				</p>
			</a>
		</div>
	</script>

	<!-- カレンダーボディテンプレート -->
	<script id="best_price_cal_template_cal_body" type="text/template">
		<tr>
			<td>
				<%= sun %>
			</td>
			<td>
				<%= mon %>
			</td>
			<td>
				<%= tue %>
			</td>
			<td>
				<%= wed %>
			</td>
			<td>
				<%= thu %>
			</td>
			<td>
				<%= fri %>
			</td>
			<td>
				<%= sat %>
			</td>
		</tr>
	</script>

	<!-- カレンダーフッターテンプレート -->
	<script id="best_price_cal_template_cal_footer" type="text/template">
			</tbody>
		</table>
	</script>
	<!-- カレンダーテンプレート End -->
	<script>
		var bestPriceCal = {};
		bestPriceCal.calendar = <?php echo json_encode($calendar); ?>;
		bestPriceCal.stationName = <?php echo json_encode($stationName); ?>;
		bestPriceCal.stationId = <?php echo json_encode($stationId); ?>;
		bestPriceCal.prefectureId = <?php echo json_encode($prefectureId); ?>;
		window.bestPriceCal = bestPriceCal;
	</script>
	<!-- 車種別最安値カレンダー End -->

<?php
	if(!empty($recommendCarList)){
?>
	<h2 id="02" class="station2_h3"><?=$stationName?>のおすすめ車種から探す</h2>
	<section class="pref_cont_wrap">
		<ul>
<?php
		foreach($recommendCarList as $recommendCar){
			$catchCopy = $catchCopyList[$recommendCar['CarType']['id']];
?>
			<li class="station2_article_title">
				<h3 class="station2_btn_pickup_company"><?=$recommendCar['CarType']['name']?> (<?=$recommendCar['CarType']['description']?>)</h3>
			</li>
			<li class="station2_article_body">
				<div class="station2_article_body_image station2_article_body_image--borderd">
					<img src="/rentacar/img/commodity_reference/<?=$recommendCar['Client']['id']?>/<?=$recommendCar['Commodity']['image_relative_url'];?>" alt="<?=$recommendCar['CarType']['name']?>でお取り扱いのある車両の参考写真" class="station2_pickup_car_img" loading="lazy" importance="low" decoding="async"/>
				</div>
				<div>
					<p>
						<a class="station2_link" href="<?php echo $this->CreateUrl->view($search['baseUrl'], 'car_type[]=' . $recommendCar['CarType']['id']); ?>">
							<?=$recommendCar['Automaker']['name']?> ： <?=$recommendCar['CarModel']['name']?>
						</a>
					</p>
					<p>
						<?=$catchCopy?>の<?=$recommendCar['Automaker']['name']?>・<?=$recommendCar['CarModel']['name']?>。<?=$recommendCar['Client']['name']?>の
						車種確約プランで、<?=$recommendCar['Automaker']['name']?>・<?=$recommendCar['CarModel']['name']?>を指定して予約が可能です。
<?php
			if ($recommendCar['Commodity']['new_car_registration'] <= 2) {
?>
						登録から<?=$recommendCar['Commodity']['new_car_registration']?>年以内の新車で、
<?php
			}
			if (!empty($recommendCar['Equipment'])) {
				$equipmentText = implode('や', array_chunk($recommendCar['Equipment'], 2)[0]);
?>
						<?=$equipmentText?>などが標準装備！
<?php
			}
			if (!empty($recommendCar['Privilege'])) {
				$privilegeText = implode('・', array_chunk($recommendCar['Privilege'], 2)[0]);
?>
						オプションで<?=$privilegeText?>などを選択できます。
<?php
			}
?>
					</p>
					<div class="station2_pickup_store_search">
						<a href="<?php echo $this->CreateUrl->view($search['baseUrl'], 'car_type[]=' . $recommendCar['CarType']['id']); ?>" class="btn-type-primary">
							<?=$recommendCar['CarType']['name']?>のプラン一覧へ
						</a>
					</div>
				</div>
			</li>
<?php
		}
?>
		</ul>
	</section>
<?php
	}
?>

<?php
	if (!empty($yotpoReviews)){
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
	<section id="reviews" class="yotpo_api_custom_wrap">
		<h2 id="03" class="review_section_title">口コミの評判から<?=$stationName?>のレンタカーを探す</h2>
		<?php echo $this->element('yotpo_review'); ?>
	</section>
<?php 
		}
	}
?>

	<h2 id="<?=$areamapId?>" class="station2_h3"><?=$areamapName;?>のレンタカーをエリア、空港、主要駅から探す</h2>
	<section class="pref_cont_wrap">
		<?php echo $this->element('pc_areamap', ['alt_text' => $areamapName]); ?>
	</section>

	<?php echo $this->element('prefecture_linklist'); ?>

	<?php echo $this->element('popular_airport_linklist'); ?>

<?php 
	if (!empty($recommendOfficeIds)) {
?>
	<section class="pref_cont_wrap">
		<h2 id="05" class="station2_h3"><?=$stationName?>のレンタカーを店舗一覧から探す</h2>
		<?php echo $this->element('pc_shoplist'); ?>
	</section>
<?php 
	}
?>

<?php
	if (!empty($dropOffTable['table'])) {
?>
	<section class="pref_cont_wrap">

		<h2 id="06" class="station2_h3"><?=$stationName?>から乗捨てができるレンタカー会社と料金</h2>
		<table class="station2_fromairport_place_time_tbl">
			<tr>
<?php
		foreach ($dropOffTable['header'] as $k => $td) {
?>
				<th><?=$td?><?=($k > 0 && !empty($td)) ? 'までの乗り捨て料金' : ''?></th>
<?php
		}
?>
			</tr>
<?php
		$cnames = array();
		foreach ($dropOffTable['table'] as $tr) {
			$client = $clientList[$tr[0]];
			$cnames[] = $client['name'];
?>
			<tr>
				<td><a href="/rentacar/<?=$client['url']?>/">
				<img src="/rentacar/img/logo/square/<?=$client['id'];?>/<?=$client['sp_logo_image'];?>" alt="<?= $client['name'];?>" height="48" width="48" loading="lazy" importance="low" decoding="async"/>
				</a></td>
<?php
			for ($i = 1; $i <= 3; $i++) {
?>
				<td><?=$tr[$i]?></td>
<?php
			}
?>
			</tr>
<?php
		}
?>
		</table>
		<p class="station2_fromairport_place_time_text">
			<?=$stationName?>から乗捨て利用のできるレンタカー会社は<?=implode('・', $cnames)?>の<?=count($cnames)?>社です。
<?php
		if (!empty($dropOffTable['popularLocation']) && !empty($dropOffTable['lowestPriceClientList'])) {
			$lowestPriceClientNames = array();
			foreach ($dropOffTable['lowestPriceClientList'] as $clientId) {
				$lowestPriceClientNames[] = $clientList[$clientId]['name'];
			}
			$lowestPriceClientNames = implode('・', $lowestPriceClientNames);
?>
			最もよく利用される乗捨て返却場所は、<?=$dropOffTable['popularLocation']['name']?>で、
			<?=$dropOffTable['popularLocation']['name']?>への乗捨て料金が一番安いレンタカー会社は<?=$lowestPriceClientNames?>となります。
<?php
			if (!empty($dropOffTable['otherLocation'])) {
?>
			<?=implode('や', $dropOffTable['otherLocation'])?>への移動も、レンタカーなら格安で簡単！
<?php
			}
		}
?>
			<?=$stationName?>から乗捨てのレンタカー予約も最安値を検索・予約ができます。
		</p>
		<p class="station2_fromairport_place_time_link">
			<?php echo $this->element('btn_select_dropoff', ['additionaltext' => $stationName.'から']); ?>
		</p>
	</section>
<?php
	}
?>

<?php
	if (!empty($clientList) || !empty($stationContents['0']['head'])) {
?>
	<h2 id="07" class="station2_h3"><?=$stationName?>のレンタカー情報</h2>
<?php
	}
?>
<?php
	if (!empty($clientList)) {
?>
	<section class="station2_article">
		<h3 class="station2_article_title"><?=$stationName?>でおすすめのレンタカーについて</h3>
		<div class="station2_article_body">
			<div class="station2_article_body_image">
				<img src="/rentacar/img/pic_statioin2_info_1.jpg" alt="<?=$stationName?>でおすすめのレンタカーについて" loading="lazy" importance="low" decoding="async"/>
			</div>
			<div class="station2_article_body_text station2_article_body_text--rentacar-info">
				<p>
					<?=$stationName?>周辺で利用できるレンタカー会社は<?=count($recommendOfficeIds)?>店舗あります。
					<?=$stationName?>のレンタカーを借りるなら、口コミ高評価の<?=implode('や', $recommendClients)?>などのレンタカーがおすすめ。
<?php
		foreach ($recommendOfficeInfo as $k => $office) {
			if ($k > 0) {
				echo '、';
			}
?>
					<?=$office['name']?>は<?=$stationName?>から<?=$office['methodOfTransport']?>で約<?=$office['requiredTransportTime']?>分
<?php
		}
		if (!empty($recommendOfficeInfo)) {
			echo 'の位置にあり、アクセスが便利です。';
		}
?>
<?php
		if (!empty($lowestPriceClient)) {
?>
					<?=$stationName?>で最安値のレンタカーを提供するのは<?=$lowestPriceClient['name']?>です。
					<?=$stationName?>の<?=$lowestPriceClient['name']?>では日帰り利用で<?=$lowestPriceClient['carTypeName']?> &yen;<?=number_format($lowestPriceClient['bestPrice'])?>&#xFF5E;の格安で利用できます。
<?php
		}
?>
					<?=$stationName?>で大人気の格安レンタカーは売り切れる場合もありますので、ご予約はお早めに。
				</p>
			</div>
		</div>
	</section>
	<section class="station2_article">
		<h3 class="station2_article_title"><?=$stationName?>到着からレンタカー貸出までにかかる時間について</h3>
		<div class="station2_article_body">
			<div class="station2_article_body_image">
				<img src="/rentacar/img/pic_statioin2_info_2.jpg" alt="<?=$stationName?>到着からレンタカー貸出までにかかる時間について" loading="lazy" importance="low" decoding="async"/>
			</div>
			<div class="station2_article_body_text station2_article_body_text--rentacar-info">
				<p>
					<?=$stationName?>周辺のレンタカー会社では、レンタカー貸出の手続きを平均<?=$aboutTimeInfo['rentProcTime']?>分で完了できます。
					繁忙期は最大<?=$aboutTimeInfo['rentProcTimeBusy']?>分程度かかることもありますので、余裕を持った行程を心がけたいですね。
					また、<?=$stationName?>送迎に対応している店舗が<?=$aboutTimeInfo['pickupCount']?>店舗、徒歩圏内に位置する店舗が<?=$aboutTimeInfo['walkCount']?>店舗あります。
<?php
		if ($aboutTimeInfo['pickupCount'] > 0) {
?>
					送迎対応の場合、平均して送迎車到着の待ち時間に<?=$aboutTimeInfo['pickupWaitTime']?>分、送迎時間に<?=$aboutTimeInfo['requiredTransportTime']?>分かかることにも注意しましょう。
<?php
		}
?>
					最短で貸し出し可能なレンタカー会社は<?=$aboutTimeInfo['minRentClientName']?>でレンタカー貸し出しまで約<?=$aboutTimeInfo['minRentProcTime']?>分となっています。
					※<?=$stationName?>にあるレンタカー会社全<?=count($clientList)?>社を対象に算出しています。
				</p>
			</div>
		</div>
	</section>
<?php
		if (!empty($popularCarTypeInfo)) {
?>
	<section class="station2_article">
		<h3 class="station2_article_title"><?=$stationName?>で人気のレンタカー車両タイプ</h3>
		<div class="station2_article_body">
			<div class="station2_article_body_image">
				<img src="/rentacar/img/pic_statioin2_info_3.jpg" alt="<?=$stationName?>で人気のレンタカー車両タイプ" loading="lazy" importance="low" decoding="async"/>
			</div>
			<div class="station2_article_body_text station2_article_body_text--rentacar-info">
				<p>
					<?=$stationName?>では<?=count($typeBestPrices)?>つの車両タイプ、<?=$popularCarTypeInfo['carModelCount']?>車種からレンタカーを選ぶことができます。
					<?=$stationName?>で特に人気の車両タイプは<?=$popularCarTypeInfo['carTypeName']?>で、多くの場合、<?=$popularCarTypeInfo['avgCapacity']?>名で乗車されています。
					<?=$stationName?>で人気の車種は<?=implode('や', $popularCarTypeInfo['carModelNames'])?>などがあります。
					<?=$stationName?>の<?=$popularCarTypeInfo['carTypeName']?>平均基本料金は約<?=$popularCarTypeInfo['avgPrice']?>円で最大乗車人数は<?=$popularCarTypeInfo['maxCapacity']?>人、トランク最大積載量は<?=$popularCarTypeInfo['maxTrunk']?>個となっています。
				</p>
			</div>
		</div>
	</section>
<?php
		}
?>
<?php
	}
?>

<?php
	if(!empty($stationContents['0']['head'])){
		foreach($stationContents as $v) {
?>
<?php 
			if (!empty($v['head'])) {
?>
	<section class="station2_article">
		<h3 class="station2_article_title"><?=$v['head']?></h3>

		<div class="station2_article_body">
<?php
				if (!empty($v['img'])){
?>
			<div class="station2_article_body_image">
				<img src="<?= $v['img'] ?>" alt="<?=$v['head']?>" loading="lazy" importance="low" decoding="async"/>
			</div>
<?php
				}
?>
			<div class="station2_article_body_text station2_article_body_text--rentacar-info">
				<p><?= $v['text']; ?></p>
			</div>
		</div>
	</section>
<?php
			}
?>
<?php
		}
	}
?>
</div>

<script>
$(function(){
	// 使ってなさそう
	// もっと見る
	// var heightStationList = $("#js_station_list > .variable_cont_wrap").height();
	// if( $("#js_station_list").has(".variable_cont_more").length > 0 ){
	// 	heightStationList += 30;
	// }
	// $("#js_station_list").height( heightStationList );
	// var heightCityList= $("#js_city_list > .variable_cont_wrap").height();
	// if( $("#js_city_list").has(".variable_cont_more").length > 0 ){
	// 	heightCityList += 30;
	// }
	// $("#js_city_list").height( heightCityList );

	// var heightListAfter = 0;
	// $(".btn_more_cont").on("click", function(){
	// 	var contId = $(this).data("contents");

	// 	$(this).toggleClass("show_more");
	// 	$("#"+contId+" .variable_cont_hidden").toggle();
	// 	heightListAfter = $("#"+contId+" > .variable_cont_wrap").height();
	// 	$("#"+contId).height( heightListAfter + 30 );

	// 	var isOpen = $(this).hasClass("show_more");
	// 	switchMoreBtn(this, isOpen);
	// });

	// // もっと見るボタン 表示切り替え
	// var switchMoreBtn = function(objBtn, isOpen){
	// 	if( isOpen ){
	// 		$(objBtn).html('<i class="fa fa-caret-up"></i>&nbsp;閉じる');
	// 	}else{
	// 		$(objBtn).html('<i class="fa fa-caret-down"></i>&nbsp;もっと見る');
	// 	}
	// };

	// レビュー続きを読む
	// $(".btn_more_review").on("click", function(){
	// 	var review_id = $(this).parents("li").attr("id");
	// 	var objReviewAll = $("#"+review_id+" .review_cont_all");
	// 	var objReviewOmmit = $("#"+review_id+" .review_cont_ommit");

	// 	$(this).hide().attr("aria-expanded", false);
	// 	objReviewOmmit.hide().attr("aria-hidden", true);
	// 	objReviewAll.show().attr("aria-hidden", false);
	// });
});
</script>

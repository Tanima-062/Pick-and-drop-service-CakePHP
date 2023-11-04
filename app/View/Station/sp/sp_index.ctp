<link rel="stylesheet" type="text/css" href="/rentacar/css/swiper.min.css">
<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery-ui.css">
<script type="text/javascript" src="/rentacar/js/swiper.min.js"></script>
<script type="text/javascript" src="/rentacar/js/station2.js" async></script>

<?php
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
	$type = $activeWebp ? '.webp' : '.png';
	if(!empty($stationContents['station-photo-guid'])){
		$stationHeader = $stationContents['station-photo-guid'];
	} else {
		$stationHeader = '/rentacar/img/station_default_header'.$type;
	}
?>
<div>
	<div class="fromairport_head">
		<div class="fromairport_head_text">
			<h1 class="fromairport_head_ttl"><?=$stationName?>の格安レンタカーを比較・予約する</h1>
<?php
	if(!empty($stationContents['station-single-station-en'])){
?>
			<p class="fromairport_head_en"><?= $stationContents['station-single-station-en']; ?></p>
<?php
	}
?>
		</div>
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


<!-- プラン掲載 -->
<?php
	if(!empty($bestPriceCarTypes)) {
?>
	<div class="ranking">
		<h2><?=$stationName?>のレンタカー最安値を探す</h2>

		<div class="ranking-list ranking<?=$rankingNum +1;?>">
			<div class="swiper-container">
				<div class="swiper-wrapper" id="sliderSet">
<?php
		foreach ($bestPriceCarTypes as $carTypeId => $ranking) {
			if ($carTypeId == 1) {
				$carImg = '<img src="/rentacar/img/car_type_kei'.$type.'" class="img1 kei" width="120" height="82.8" alt="'.$stationName.'軽自動車レンタカー" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_compact'.$type.'" class="img2 compact" width="150" height="82.5" alt="'.$stationName.'コンパクトレンタカー" loading="lazy" importance="low" decoding="async">';
			} else if($carTypeId == 3) {
				$carImg = '<img src="/rentacar/img/car_type_hybrid'.$type.'" class="img1 hybrid" width="150" height="86" alt="'.$stationName.'ハイブリッドレンタカー"loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_sedan'.$type.'" class="img2 sedan" width="150" height="64.5" alt="'.$stationName.'セダンレンタカー" loading="lazy" importance="low" decoding="async">';
			} else if($carTypeId == 9) {
				$carImg = '<img src="/rentacar/img/car_type_miniban'.$type.'" class="img1 miniban" width="150" height="82" alt="'.$stationName.'ミニバンレンタカー" loading="lazy" importance="low" decoding="async"><img src="/rentacar/img/car_type_wagon'.$type.'" class="img2 wagon" width="150" height="82.5" alt="'.$stationName.'ワゴンレンタカー" loading="lazy" importance="low" decoding="async">';
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
							<p>
								<span class="carCapacity"><?=$carCapacity;?></span><span class="-marker">最安値</span><span class="-price"><?=$ranking['price']?>
							</p>
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


<!-- 車種別最安値カレンダー -->
	<section class="station2_section" id="best_price_cal">
		<h2 class="station2_h2 "><?=$stationName?>のレンタカーを最安値カレンダーから探す</h2>
		<div class="best_price_cal_container">
			<div id="best_price_cal_lead" class="best_price_cal_lead"></div>
			<div id="best_price_cal_tab" class="best_price_cal_tab"></div>
			<div id="best_price_cal_nav" class="best_price_cal_nav"></div>
			<div id="best_price_cal_calendar" class="best_price_cal_calendar"></div>
		</div>

		<!-- カレンダーテンプレート -->
		<!-- リード文テンプレート -->
		<script id="best_price_cal_template_lead" type="text/template">
			<p><%= monthString %>の<%= stationName %>（<?=$prefectureName?>）のレンタカー最安値は日帰り<strong><%= monthlyBestPrice %></strong>ご用意しています。</p>
			<p>レンタルする日数やプランにより料金は変動します。カレンダーでご利用日をお選びください。</p>
		</script>
		<!-- タブテンプレート -->
		<script id="best_price_cal_template_tab" type="text/template">
			<nav>
				<ul role="tablist" data-year="<%= year %>" data-month="<%= month %>" >
					<li data-tab="1" aria-selected="<%= selectedType1 %>" role="tab">
						<div>軽自動車</div>
					</li>
					<li data-tab="2" aria-selected="<%= selectedType2 %>" role="tab">
						<div>コンパクト</div>
					</li>
					<li data-tab="3" aria-selected="<%= selectedType3 %>" role="tab">
						<div>ミドル</div>
					</li>
					<li data-tab="95" aria-selected="<%= selectedType95 %>" role="tab">
						<div>ミニバン</div>
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
			><%= arrowSp %></button>
		</script>

		<!-- ナビゲーションテンプレート -->
		<script id="best_price_cal_template_nav" type="text/template">
			<p class="year_month"><%= currentYearMonth %></p>
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
						<a <%= hrefSp %>>
							<p class="best_price_cal_calendar_date"><%= date %></p>
							<p data-bestprice="<%= isBestPrice %>" class="best_price_cal_calendar_price">
								<%= priceSp %>
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
	</section>
	<!-- 車種別最安値カレンダー End -->

<?php
	if(!empty($recommendCarList)){
?>
	<section class="station2_section">
		<h2 class="station2_h2"><?=$stationName?>のおすすめ車種から探す</h2>
		<div>
			<ul class="station2_recommend-car">
<?php
		foreach($recommendCarList as $recommendCar){
			$catchCopy = $catchCopyList[$recommendCar['CarType']['id']];
?>
				<li class="station2_recommend-car_title">
					<h3><?=$recommendCar['CarType']['name']?> (<?=$recommendCar['CarType']['description']?>)</h3>
				</li>
				<li class="station2_recommend-car_contents">
					<div>
						<div class="station2_recommend-car_header">
							<div class="station2_recommend-car_header_image">
								<img
									src="/rentacar/img/commodity_reference/<?=$recommendCar['Client']['id']?>/<?=$recommendCar['Commodity']['image_relative_url'];?>"
									alt="<?=$recommendCar['CarType']['name']?>でお取り扱いのある車両の参考写真"
									loading="lazy" importance="low" decoding="async"
								/>
							</div>
							<ul>
								<li><?=$recommendCar['Automaker']['name']?></li>
								<li>車種：<?=$recommendCar['CarModel']['name']?></li>
								<li>推奨目安　
									<i class="fa fa-user"></i>：<?= $recommendCar['CarModel']['capacity'] ?>
									<i class="fa fa-suitcase"></i>：<?= $recommendCar['CarModel']['package_num'] ?>
								</li>
							</ul>
						</div>
						<p class="station2_recommend-car_text">
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
					</div>

					<div class="station2_recommend-car_link">
						<a href="<?php echo $this->CreateUrl->view($search['baseUrl'], 'car_type[]=' . $recommendCar['CarType']['id']); ?>" class="btn_search_store station2_link">
							<?=$recommendCar['CarType']['name']?>のプラン一覧へ
						</a>
					</div>
				</li>
<?php
		}
?>
			</ul>
		</div>
	</section>
<?php
	}
?>


<?php
	if (!empty($yotpoReviews)) {
		// YOTPO
		if ($yotpo_is_active && $use_yotpo) { 
?>
	<section id="reviews" class="yotpo_api_custom_wrap station2_section">
		<h2 class="review_section_title">口コミの評判から<?=$stationName?>のレンタカーを探す</h2>
		<?php echo $this->element('sp_yotpo_review'); ?>
	</section>
<?php 
		}
	}
?>

	<?php echo $this->element('sp_prefecture_linklist'); ?>

	<?php echo $this->element('sp_popular_airport_linklist'); ?>

<?php
	if (!empty($recommendOfficeIds)) {
?>
	<section class="">
		<div class="header-wrap">
			<h2><?=$stationName?>のレンタカーを店舗一覧から探す</h2>
		</div>
		<?php echo $this->element('sp_shoplist_detail_accordion'); ?>
	</section>
<?php
	}
?>

<?php
	if (!empty($dropOffTable['table'])) {
?>
	<section class="station2_section">
		<h2 class="station2_h2"><?=$stationName?>から乗捨てできるレンタカー</h2>

		<div>
<?php
		foreach ($dropOffTable['header'] as $k => $dropOffTitle) {
			if ($k === 0 || $dropOffTitle === '') { 
				continue; 
			}
?>
			<section class="staion2_dropoff">
				<h3 class="staion2_dropoff_title"><?=$dropOffTitle?>までの乗り捨て料金</h3>
				<ul class="staion2_dropoff_list">
<?php
			$cnames = array();

			foreach ($dropOffTable['table'] as $dropOffData) {
				$dropOffClient = $client = $clientList[$dropOffData[0]];
				$cnames[] = $client['name'];
?>
					<a href="/rentacar/<?=$client['url']?>/">
						<li class="staion2_dropoff_item">
							<dl class="staion2_dropoff_item_shoplist">
								<dt>
									<img
										src="/rentacar/img/logo/square/<?php echo $client['id']; ?>/<?php echo $client['sp_logo_image']; ?>"
										alt="<?=$client['name']?><?=$stationName?>乗り捨て料金"
										loading="lazy" importance="low" decoding="async"
									/>
									<?=$client['name']?>
								</dt>
								<dd><?=$dropOffData[$k]?></dd>
							</dl>
						</li>
					</a>
<?php 
			}
?>
				</ul>
			</section>
<?php 
		}
?>
		</div>

		<p class="staion2_dropoff_text">
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
		<div class="staion2_dropoff_link">
			<?php echo $this->element('btn_select_dropoff', ['additionaltext' => $stationName.'から']); ?>
		</div>
	</section>
<?php
	}
?>

<?php
	if (!empty($clientList) || !empty($stationContents['0']['head'])) {
?>
	<section class="station2_section">
		<h2 class="station2_h2"><?=$stationName?>のレンタカー情報</h2>
<?php 
		if (!empty($clientList)) {
?>
		<section class="station2_article">
			<h3 class="station2_article_title"><?=$stationName?>でおすすめのレンタカーについて</h3>
			<div class="station2_article_body">
				<div class="station2_article_body_image">
					<img src="/rentacar/img/sp/pic_statioin2_info_1.jpg" alt="<?=$stationName?>でおすすめのレンタカーについて" loading="lazy" importance="low" decoding="async"/>
				</div>
				<div class="station2_article_body_text">
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
					<img src="/rentacar/img/sp/pic_statioin2_info_2.jpg" alt="<?=$stationName?>到着からレンタカー貸出までにかかる時間について" loading="lazy" importance="low" decoding="async"/>
				</div>
				<div class="station2_article_body_text">
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
		}
?>
<?php
		if (!empty($popularCarTypeInfo)) {
?>
		<section class="station2_article">
			<h3 class="station2_article_title"><?=$stationName?>で人気のレンタカー車両タイプ</h3>
			<div class="station2_article_body">
				<div class="station2_article_body_image">
					<img src="/rentacar/img/sp/pic_statioin2_info_3.jpg" alt="<?=$stationName?>で人気のレンタカー車両タイプ" loading="lazy" importance="low" decoding="async"/>
				</div>
				<div class="station2_article_body_text">
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
		if(!empty($stationContents['0']['head'])){
			foreach($stationContents as $v){
				if (!empty($v['head'])){
?>
		<section class="station2_article">
			<h3 class="station2_article_title"><?= $v['head']; ?></h3>
			<div class="station2_article_body">
<?php 
					if (!empty($v['img'])){
?>
				<div class="station2_article_body_image">
					<img src="<?= $v['img']; ?>" alt="<?= $v['head']; ?>" loading="lazy" importance="low" decoding="async"/>
				</div>
<?php
					}
?>
				<div class="station2_article_body_text">
					<p><?= $v['text']; ?></p>
				</div>
			</div>
		</section>
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
	if (!empty($majorStationList) || !empty($areaList) || !empty($airportLinkCdList) || !empty($neighborhoodPrefectureList)) {
?>
	<section class="station2_section">
		<h2 class="station2_h2"><?=$stationName?>周辺からレンタカーを探す</h2>
<?php
		if (!empty($majorStationList)) {
?>
		<section class="station2_nearby">
			<h3 class="station2_nearby_title"><?=$stationName?>の近隣駅から探す</h3>
			<ul class="station2_nearby_list">
<?php 
			foreach($majorStationList as $majorStation) {
?>
				<li class="station2_nearby_item">

<?php 
				if ($prefectureLinkCd === 'hokkaido' || $prefectureLinkCd === 'okinawa') {
?>
					<a href="/rentacar/<?= $prefectureLinkCd ?>/<?= $majorStation['url'] ?>"><?= $majorStation['name'] ?></a>
<?php 
				} else {
?>
					<a href="/rentacar/<?= $regionLinkCd ?>/<?= $prefectureLinkCd ?>/<?= $majorStation['url'] ?>"><?= $majorStation['name'] ?></a>
<?php 
				}
?>
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
		if (!empty($areaList)){
?>
		<section class="station2_nearby">
			<h3 class="station2_nearby_title"><?=$stationName?>の近隣市区町村から探す</h3>
			<ul class="station2_nearby_list">
<?php 
			foreach($areaList as $area) {
?>
				<li class="station2_nearby_item">
<?php 
				if ($prefectureLinkCd === 'hokkaido' || $prefectureLinkCd === 'okinawa') {
?>
					<a href="/rentacar/<?= $prefectureLinkCd ?>/<?= $area['area_link_cd'] ?>"><?= $area['name'] ?></a>
<?php 
				} else {
?>
					<a href="/rentacar/<?= $regionLinkCd ?>/<?= $prefectureLinkCd ?>/<?= $area['area_link_cd'] ?>"><?= $area['name'] ?></a>
<?php 
				} 
?>
				<li>
<?php 
			}
?>
			</ul>
		</section>
<?php
		}
?>

<?php
		if (!empty($airportLinkCdList)){
?>
		<section class="station2_nearby">
			<h3 class="station2_nearby_title"><?=$stationName?>に近い空港から探す</h3>
			<ul class="station2_nearby_list">
<?php 
			foreach($airportLinkCdList as $airportLinkCd) {
?>
				<li class="station2_nearby_item">
					<a href="/rentacar/<?= $airportLinkCd['link_cd'] ?>"><?= $airportLinkCd['name'] ?></a>
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
		if (!empty($neighborhoodPrefectureList)){
?>
		<section class="station2_nearby">
			<h3 class="station2_nearby_title">近隣の都道府県から探す</h3>
			<ul class="station2_nearby_list">
<?php
			foreach($neighborhoodPrefectureList as $neighborhoodPrefecture) {
?>
				<li class="station2_nearby_item">
<?php 
				if ($neighborhoodPrefecture['link_cd'] === 'hokkaido' || $neighborhoodPrefecture['link_cd'] === 'okinawa') {
?>
					<a href="/rentacar/<?= $neighborhoodPrefecture['link_cd'] ?>"><?= $neighborhoodPrefecture['name'] ?></a>
<?php 
				} else {
?>
					<a href="/rentacar/<?= $neighborhoodPrefecture['region_link_cd'] ?>/<?= $neighborhoodPrefecture['link_cd'] ?>"><?= $neighborhoodPrefecture['name'] ?></a>
<?php 
				}
?>
				</li>
<?php
			}
?>
			</ul>
		</section>
<?php
		}
?>
	</section>
<?php
	}
?>

</div>

<style>
	/* .gm-style-iw {
		width: 200px !important;
	} */
</style>

<script type="text/javascript">
$(function() {
	// 検索ボックスのカスタマイズ
	$("#depature-place .airport-input").hide();
	$("#depature-place .input-area").hide();
	$("#depature-place .box-in-title").css({"minHeight":"auto", "height":"78px"});

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

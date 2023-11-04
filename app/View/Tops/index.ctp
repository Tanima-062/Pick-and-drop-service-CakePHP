<?php
	echo $this->Html->css(['swiper.min'],null,['inline'=>false, 'media'=>'print', 'onload'=>'this.media=\'all\'']);
	echo $this->Html->script(['/js/swiper.min', '/js/pc_faq_tablist', '/js/modal_float'],['defer'=>true, 'inline' => false]);
?>

<?php
	// webp対応ブラウザかの判定
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
?>

<div class="container">

	<!-- 検索エリア -->
	<div class="search-container <?php if(!$activeWebp){ echo 'no_webp'; }?>" id="search-container">

		<h1 class="search-container-header">大手レンタカー会社を最安値で予約</h1>
		<div class="wrap search">

<?php 
	if(!IS_PRODUCTION) { // 海外検索導入　
?>
			<ul id="rentacar-tab" class="rentacar-tab">
				<li>
					<input type="radio" name="rentacar-tab" value="rentacar-domestic" id="rentacar-domestic" checked>
					<label for="rentacar-domestic" class="rentacar-domestic-label">
						国内のレンタカーを探す
						<p class="-line"></p>
					</label>
				</li>
				<li>
					<input type="radio" name="rentacar-tab" value="rentacar-oversea" id="rentacar-oversea">
					<label for="rentacar-oversea" class="rentacar-oversea-label">
						海外のレンタカーを探す
						<p class="-line"></p>
					</label>
				</li>
			</ul>
<?php 
	} // !IS_PRODUCTION 
?>

<!-- 国内検索フォーム -->
<?php
	echo $this->Form->create('Search', array('action' => 'index', 'inputDefaults' => array(
			'label' => false,
			'div' => false,
			'hiddenField' => false,
			'legend' => false,
			'fieldset' => false,
		), 'type' => 'get', 'class' => 'contents_search', 'id' => 'SearchIndexForm'));
?>

<?php
	// main section
	echo $this->element('searchform_main_top_domestic');
?>

			<div id="js-searchform_options_section_toggler" class="searchform_options_section_toggler">
				<span>車両タイプ・オプションを選択する</span>
				<i class="icm-right-arrow icon-right-arrow_down"></i>
			</div>

			<div class="js-searchform_options_section_wrap searchform_options_section_wrap -hidden">
<?php
	// options section
	echo $this->element('searchform_options');
?>
			</div>

			<div class="searchform_submit_section_wrap">

<?php
	// submit section
	echo $this->element('searchform_submit');
?>

<?php
	// search condition modal
	echo $this->element('pc_modal_search_condition');
?>


			</div><!-- //searchform_submit_section_wrap -->

<?php
	echo $this->Form->end();
?>

<!-- 海外検索フォーム -->
<?php
	if(!IS_PRODUCTION) { // 海外検索導入　
?>
			<div id="rentacar-oversea-form" class="search-form">
<?php
		// 出発日時や返却日時などは共通項目のためエレメント化
		echo $this->element('searchform_main_top_oversea');
?>
				<div class="-grid form-oversea-bottom grid-item-end">
					<div class="notification">
						<div class="-icon-wrapper">
							<i class="icm-info-button-fill"></i>
						</div>
						<div class="-text-wrapper">
							ドライバー年齢が30〜65歳に該当しない場合は、チェックを外して検索してください。ドライバーの年齢によってレンタカー会社から追加料金を請求される場合があります。
						</div>
					</div>
					<div class="-submit">
<?php
	echo $this->Form->button('レンタカーの最安値を検索',array('id' => 'submit_oversea_search', 'class'=>'btn-type-primary', 'data-ga_category'=>'pc_top', 'data-ga_label'=>'トップ検索ボタン', 'disabled'=>true));
?>
					</div>
				</div>
			</div>
<?php 
	} // !IS_PRODUCTION 
?>
		</div>
	</div>

<?php
	if (!empty($messages)){
?>
	<dl class="news_dl">
		<dt class="news_dt"><i class="icm-info-button-fill"></i>skyticketレンタカーからのお知らせ</dt>
<?php
		foreach ($messages as $message){
?>
		<dd class="news_dd">
				<?=$this->Html->link($message['Message']['title'], '/news/show/' . $message['Message']['id']); ?>
		</dd>
<?php
		}
?>
	</dl>
<?php
	}
?>

<div class="wrap" id="top-contents-domestic">

<?php
	// バナー
	$ymd = date('ymd');
	if($ymd >= 210917 && $ymd <= 211231){
?>	
	<a href="/rentacar/hokkaido/"><img src="img/campaign/campaign_hokkaidrive2021_pc.png" alt="HOKKAIDrive Campaign 2021" class="top_banner"></a>
<?php
	}
?>

	<div class="campaign">
		<h2 class="block-title">お得なキャンペーンから探す</h2>
		<div class="swiper-container swiper-campaign">
			<div class="swiper-wrapper" id="sliderSet">
				<div class="swiper-slide slide1">
					<a href="campaign/toyota_rentacar_2007/?ad=topcp2007_rc">
<?php
	$month = date('n'); // nは現在の月を1〜12の数字で返す。
	if((3 <= $month) && ($month <= 5)) {
?>
					<img src="img/campaign_toyota_spring.jpg" width="284" height="140" loading="lazy" importance="low" decoding="async" alt="安心のトヨタレンタカー" />
<?php
	} elseif ((6 <= $month) && ($month <= 8)) {
?>
					<img src="img/campaign_toyota_summer.jpg" width="284" height="140" loading="lazy" importance="low" decoding="async" alt="安心のトヨタレンタカー" />
<?php
	} elseif ((9 <= $month) && ($month <= 11)) {
?>
					<img src="img/campaign_toyota_autumn.jpg" width="284" height="140" loading="lazy" importance="low" decoding="async" alt="安心のトヨタレンタカー" />
<?php
	} else {
?>
					<img src="img/campaign_toyota_winter.jpg" width="284" height="140" loading="lazy" importance="low" decoding="async" alt="安心のトヨタレンタカー" />
<?php
	} 
?>
					</a>
				</div>
				<div class="swiper-slide slide2">
					<a href="campaign/orix_rentacar_2004/?ad=topcp2004_rc">
						<picture>
							<source srcset="img/webp/rentacar_top_campaign2.webp" type="image/webp">
							<img src="img/rentacar_top_campaign2.png" width="284" height="140" loading="lazy" importance="low" decoding="async" alt="オリックスレンタカー 格安プランでドライブへ">
						</picture>
					</a>
				</div>
				<div class="swiper-slide slide3">
					<a href="campaign/budget_rentacar_1807/">
					<img src="img/banner_top_10.png" width="284" height="140" loading="lazy" importance="low" decoding="async" alt="セーフティレンタカーキャンペーン">
					</a>
				</div>
			</div><!--swiper-wrapper-->
		</div><!--swiper-container-->
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
		<div class="swiper-pagination"></div>
	</div>

	<div class="popularity">
		<h2 class="block-title">人気の空港から探す</h2>
		<div class="popularity-container">
<?php
	foreach ((array)$airportPrices as $k => $airportPrice) {
		$date_car_type = date_parse(date('Ymd',strtotime('+7 day')));
?>
				<a href="searches?time=11-00&prefecture=0&airport_id=<?=$airportPrice['airport_id']?>&return_time=17-00&return_prefecture=0&return_airport_id=0&place=3&return_way=0&year=<?= $date_car_type['year']; ?>&month=<?= $date_car_type['month']; ?>&day=<?= $date_car_type['day']; ?>&return_year=<?= $date_car_type['year']; ?>&return_month=<?= $date_car_type['month']; ?>&return_day=<?= $date_car_type['day']; ?>&sort=2">
					<div class="popularity-img">
						<picture>
							<source srcset="img/webp/airport_<?=$airportPrice['airport_id']?>.webp" type="image/webp">
							<img src="img/top/airport_<?=$airportPrice['airport_id']?>.jpg" width="100%" height="180" loading="lazy" importance="low" decoding="async" alt="<?=$airportPrice['airport_name']?>">
						</picture>
					</div>
					<div class="popularity-desc">
						<p><?=$airportPrice['airport_name']?></p>
						<div class="-cheapestprice">
							<p class="-tag">最安値</p>
							<p class="-price">¥<?=number_format($airportPrice['price'])?>〜</p>
						</div>
					</div>
				</a>
<?php
	}
?>
		</div>
	</div>

	<section class="search_by_airport_wrap">
		<h2 class="block-title">全国の主要な空港からレンタカーを探す</h2>
		<div class="search_by_airport_content">

			<div class="search_by_airport_content_left">
				<div class="content_row">
					<div class="content_row_title">北海道</div>
					<ul class="content_row_list">
						<li><a href="hokkaido/chitose_international_airport/">新千歳空港</a></li>
						<li><a href="hokkaido/wakkanai_airport/">稚内空港</a></li>
						<li><a href="hokkaido/memanbetsu_airport/">女満別空港</a></li>
						<li><a href="hokkaido/asahikawa_airport/">旭川空港</a></li>
						<li><a href="hokkaido/kushiro_airport/">釧路空港</a></li>
						<li><a href="hokkaido/hakodate_airport/">函館空港</a></li>
						<li><a href="hokkaido/tokachi_obihiro_airport/">帯広空港</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">東北</div>
					<ul class="content_row_list">
						<li><a href="tohoku/yamagata/yamagata_airport_junmachi_airport/">山形空港</a></li>
						<li><a href="tohoku/aomori/aomori_airport/">青森空港</a></li>
						<li><a href="tohoku/miyagi/sendai_airport/">仙台空港</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">関東</div>
					<ul class="content_row_list">
						<li><a href="kanto/tokyo/haneda_airport/">羽田空港</a></li>
						<li><a href="kanto/chiba/narita_international_airport/">成田空港</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">甲信越</div>
					<ul class="content_row_list">
						<li><a href="koushinetsu/niigata/niigata_airport/">新潟空港</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">北陸</div>
					<ul class="content_row_list">
						<li><a href="hokuriku/ishikawa/komatsu_airport_kanazawa_airport/">小松空港</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">東海</div>
					<ul class="content_row_list">
						<li><a href="tokai/aichi/chubu_centrair_international_airport/">中部国際空港(セントレア)</a></li>
					</ul>
				</div>
			</div><!-- //search_by_airport_content_left -->

			<div class="search_by_airport_content_right">
				<div class="content_row">
					<div class="content_row_title">関西</div>
					<ul class="content_row_list">
						<li><a href="kansai/osaka/kansai_international_airport/">関西国際空港</a></li>
						<li><a href="kansai/osaka/itami_airport/">伊丹空港(大阪国際空港)</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">中国</div>
					<ul class="content_row_list">
						<li><a href="chugoku/hiroshima/hiroshima_airport/">広島空港</a></li>
						<li><a href="chugoku/shimane/izumo_airport/">出雲空港(出雲縁結び空港)</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">九州</div>
					<ul class="content_row_list">
						<li><a href="kyushu/fukuoka/fukuoka_airport_itazuke_air_base/">福岡空港</a></li>
						<li><a href="kyushu/oita/oita_airport/">大分空港</a></li>
						<li><a href="kyushu/kagoshima/kagoshima_airport/">鹿児島空港</a></li>
						<li><a href="kyushu/nagasaki/nagasaki_airport/">長崎空港</a></li>
						<li><a href="kyushu/kumamoto/kumamoto_airport/">熊本空港</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">四国</div>
					<ul class="content_row_list">
						<li><a href="shikoku/ehime/matsuyama_airport/">松山空港</a></li>
						<li><a href="shikoku/kagawa/takamatsu_airport/">高松空港</a></li>
					</ul>
				</div>
				<div class="content_row">
					<div class="content_row_title">沖縄</div>
					<ul class="content_row_list">
						<li><a href="okinawa/naha_airport/">那覇空港</a></li>
						<li><a href="okinawa/ishigaki_airport/">新石垣空港</a></li>
						<li><a href="okinawa/miyako_airport/">宮古空港(宮古島空港)</a></li>
						<li><a href="okinawa/shimojishima_airport/">下地島空港</a></li>
					</ul>
				</div>
			</div><!-- //search_by_airport_content_right -->

			<div class="link_to_list"><a href="airportlist/">全国の空港一覧を見る<i class="icm-right-arrow"></i></a></div>
		</div>
	</section><!-- //search_by_airport_wrap -->

	<section class="search_by_2col_wrap">

		<div class="search_by_station_wrap">
			<h2 class="block-title">全国の主要な駅からレンタカーを探す</h2>
			<div class="search_by_station_content">
				<ul>
					<li><a href="hokkaido/sapporo_station/">札幌駅</a></li>
					<li><a href="tohoku/miyagi/miyagi_sendai_station/">仙台駅</a></li>
					<li><a href="tohoku/iwate/morioka_station/">盛岡駅</a></li>
					<li><a href="kanto/tokyo/tokyo_station/">東京駅</a></li>
					<li><a href="kanto/kanagawa/yokohama_station/">横浜駅</a></li>
					<li><a href="tokai/aichi/nagoya_station/">名古屋駅</a></li>
					<li><a href="hokuriku/ishikawa/kanazawa_station/">金沢駅</a></li>
					<li><a href="koushinetsu/niigata/niigata_station/">新潟駅</a></li>
					<li><a href="tokai/shizuoka/mishima_station/">三島駅</a></li>
					<li><a href="chugoku/okayama/okayama_station/">岡山駅</a></li>
					<li><a href="chugoku/hiroshima/hiroshima_station/">広島駅</a></li>
					<li><a href="tokai/shizuoka/shizuoka_station/">静岡駅</a></li>
					<li><a href="kansai/kyoto/kyoto_station/">京都駅</a></li>
					<li><a href="kyushu/fukuoka/hakata_station/">博多駅</a></li>
					<li><a href="kyushu/kumamoto/kumamoto_station/">熊本駅</a></li>
					<li><a href="kyushu/fukuoka/kokura_station/">小倉駅</a></li>
					<li><a href="kyushu/kagoshima/kagoshimachuo_station/">鹿児島中央駅</a></li>
					<li><a href="hokuriku/toyama/toyama_station/">富山駅</a></li>
					<li><a href="koushinetsu/nagano/nagano_station/">長野駅</a></li>
				</ul>
				<div class="link_to_list"><a href="stationlist/">全国の主要駅一覧を見る<i class="icm-right-arrow"></i></a></div>
			</div>
		</div><!-- //search_by_station_wrap -->

		<div class="search_by_place_wrap">
			<h2 class="block-title">人気の場所からレンタカーを探す</h2>
			<div class="search_by_place_content">
				<ul>
					<li><a href="hokkaido/">北海道</a></li>
					<li><a href="okinawa/">沖縄</a></li>
					<li><a href="kyushu/kagoshima/">鹿児島</a></li>
					<li><a href="tohoku/aomori/">青森</a></li>
					<li><a href="tohoku/fukushima/">福島</a></li>
					<li><a href="hokuriku/ishikawa/kanazawa_city/">金沢</a></li>
					<li><a href="hokkaido/sapporo/">札幌</a></li>
					<li><a href="kansai/kyoto/">京都</a></li>
					<li><a href="hokkaido/chitose_city/">千歳</a></li>
					<li><a href="hokkaido/obihiro_tokachi/">帯広</a></li>
					<li><a href="okinawa/naha/">那覇</a></li>
					<li><a href="kyushu/kagoshima/amami_oshima/">奄美大島</a></li>
					<li><a href="okinawa/miyakojima/">宮古島</a></li>
					<li><a href="okinawa/ishigakijima/">石垣島</a></li>
					<li><a href="koushinetsu/niigata/sado/">佐渡</a></li>
					<li><a href="okinawa/ishigaki_terminal/">石垣島離島ターミナル</a></li>
				</ul>				
			</div>
		</div><!-- //search_by_place_wrap -->
	</section>

<?php
	// クライアントリストの、キーをClient IDにしたリスト作成
	$clientListWithIdAsKey = array();
	foreach ($clientList as $client) {
		$key = $client['Client']['id'];
		$clientListWithIdAsKey[$key] = $client['Client'];
	}
?>
	<section class="search_by_supplier_wrap">
		<h2 class="block-title">おすすめのレンタカー会社から探す</h2>
		<div class="search_by_supplier_content">
			<ul>
				<li><!-- ニッポンレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[55]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[55]['id']; ?>/<?= $clientListWithIdAsKey[55]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[55]['name']; ?>">
						<?= $clientListWithIdAsKey[55]['name']; ?>
					</a>
				</li>
				<li><!-- タイムズカーレンタル -->
					<a href="company/<?= $clientListWithIdAsKey[33]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[33]['id']; ?>/<?= $clientListWithIdAsKey[33]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[33]['name']; ?>">
						<?= $clientListWithIdAsKey[33]['name']; ?>
					</a>
				</li>
				<li><!-- 日産レンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[46]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[46]['id']; ?>/<?= $clientListWithIdAsKey[46]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[46]['name']; ?>">
						<?= $clientListWithIdAsKey[46]['name']; ?>
					</a>
				</li>
				<li><!-- スカイレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[43]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[43]['id']; ?>/<?= $clientListWithIdAsKey[43]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[43]['name']; ?>">
						<?= $clientListWithIdAsKey[43]['name']; ?>
					</a>
				</li>
				<li><!-- ユウ・アイレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[102]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[102]['id']; ?>/<?= $clientListWithIdAsKey[102]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[102]['name']; ?>">
						<?= $clientListWithIdAsKey[102]['name']; ?>
					</a>
				</li>
				<li><!-- オリックスレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[4]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[4]['id']; ?>/<?= $clientListWithIdAsKey[4]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[4]['name']; ?>">
						<?= $clientListWithIdAsKey[4]['name']; ?>
					</a>
				</li>
				<li><!-- トラベルレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[6]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[6]['id']; ?>/<?= $clientListWithIdAsKey[6]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[6]['name']; ?>">
						<?= $clientListWithIdAsKey[6]['name']; ?>
					</a>
				</li>				
				<li><!-- Ｊネットレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[5]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[5]['id']; ?>/<?= $clientListWithIdAsKey[5]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[5]['name']; ?>">
						<?= $clientListWithIdAsKey[5]['name']; ?>
					</a>
				</li>
				<li><!-- トヨタレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[75]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[75]['id']; ?>/<?= $clientListWithIdAsKey[75]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[75]['name']; ?>">
						<?= $clientListWithIdAsKey[75]['name']; ?>
					</a>
				</li>
				<li><!-- バジェットレンタカー -->
					<a href="company/<?= $clientListWithIdAsKey[13]['url']; ?>/">
						<img src="img/logo/square/<?= $clientListWithIdAsKey[13]['id']; ?>/<?= $clientListWithIdAsKey[13]['sp_logo_image']; ?>" loading="lazy" importance="low" decoding="async" alt="<?= $clientListWithIdAsKey[13]['name']; ?>">
						<?= $clientListWithIdAsKey[13]['name']; ?>
					</a>
				</li>
			</ul>
			<div class="link_to_list"><a href="companylist/">全国のレンタカー会社一覧を見る<i class="icm-right-arrow"></i></a></div>
		</div>
	</section><!-- //search_by_supplier_wrap -->

	<section class="search_by_store_wrap">
		<h2 class="block-title">人気のレンタカー店舗から探す</h2>
		<div class="search_by_store_content">
			<ul>
				<li><a href="company/sky/hakata/">スカイレンタカー 博多駅店</a></li>
				<li><a href="company/orix/naha-airport/">オリックスレンタカー 那覇空港店</a></li>
				<li><a href="company/travel/naha-airport/">トラベルレンタカー 那覇空港店</a></li>
				<li><a href="company/sky/fukuoka-airport/">スカイレンタカー 福岡空港店</a></li>
				<li><a href="company/times/naha-airport/">タイムズカーレンタル 那覇空港店</a></li>
			</ul>				
		</div>
	</section><!-- //search_by_store_wrap -->

	<div class="info">
		<a href="https://support.skyticket.jp/hc/ja/articles/4405210627993" target="_blank" rel="noopener noreferrer" class="btn-type-link -reserve"><h2>予約から返却までの流れ</h2></a>
		<a href="https://support.skyticket.jp/hc/ja/categories/4403654208025" target="_blank" rel="noopener noreferrer" class="btn-type-link -faq"><h2>よくある質問</h2></a>
	</div>

	<div class="guide_ariticles">
		<h2 class="block-title">レンタカーの注目記事</h2>
		<div class="grid-container">
<?php
	foreach ((array)$contents as $k => $content) {
?>
			<a href="/<?=$content['Content']['url']?>">
				<div class="-image"><img src="img/contents/top/<?=$content['Content']['image']?>?imwidth=60" alt="<?=$content['Content']['title']?>"></div>
				<div>
					<h3 class="-title"><?=$content['Content']['title']?></h3>
					<p class="-description"><?=$content['Content']['description']?></p>
				</div>
			</a>
<?php
	}
?>
		</div>
	</div>

<?php 
	// YOTPO
	if($yotpo_is_active && $use_yotpo){ 
?>
	<div class="yotpo_container">
		<div class="yotpo_main_wrap">
			<h2 class="block-title">評判・口コミからレンタカー会社を比較する</h2>
			<script type="text/javascript">
            (function e(){var e=document.createElement("script");e.type="text/javascript",e.async=!0,
                e.src="//staticw2.yotpo.com/nmzINpXtOIn4Lz4pyOs3cubcjEvVBGATSO1qsINs/widget.js";var t=document.getElementsByTagName("script")[0];
                t.parentNode.insertBefore(e,t)})();
            </script>
            <div id='yotpo-testimonials-custom-tab'>
                <?= !empty($main_widget) ? $main_widget : ''; ?>
            </div>
		</div>
	</div>
<?php 
	} 
?>

</div>
<div class="faq-tab-list">
	<article id="js-tabList" class="tab-list">
		<div class="wrap">
			<ul class="-head">
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)">どの地域のレンタカーを<br>検索できますか？</a>
				</li>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)">どこのレンタカー会社の<br>予約ができますか？</a>
				</li>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)">どのような種類の車から<br>選べますか？</a>
				</li>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)">レンタルする期間は、<br>最短・最長どれくらいから<br>選べますか？</a>
				</li>
				<li class="js-tabList-item tab-li">
					<a class="-title" href="javascript:void(0)">保険などの追加料金が<br>発生しますか？</a>
				</li>
			</ul>
			<div class="js-tabList-content">
				<p>
					スカイチケットでは、日本全国のレンタカーを検索できます。 
					<a href='hokkaido/chitose_international_airport/'>新千歳空港</a>や
					<a href='okinawa/naha_airport/'>那覇空港</a>など、利用する空港からレンタカーの受取ができるのもスカイチケットレンタカーの特徴です。長距離でも乗り捨て可能、近くのレンタカーも検索できる。もちろん、東京都内の繁華街である
					<a href='kanto/tokyo/shinjuku_station/'>新宿駅</a>や
					<a href='kanto/tokyo/tokyo_station/'>東京駅</a>でもレンタカー予約が可能です。すでに車をレンタルしたい空港や駅が決まっている場合は、
					<a href='airportlist/'>空港</a>・
					<a href='stationlist/'>駅</a>・
					<a href='ferryterminallist/'>フェリーターミナル</a>それぞれのページから検索してみてください。
				</p>
			</div>
			<div class="js-tabList-content">
				<p>
					スカイチケットでは、安心の大手レンタカー会社から、離島の地場レンタカー会社まで、全国50社以上のレンタカー会社が提供する格安の料金プランを比較、検索・予約ができます。 
					<a href='company/times/'>タイムズカーレンタル</a>、
					<a href='company/toyota/'>トヨタレンタカー</a>、
					<a href='company/nippon/'>ニッポンレンタカー</a>、
					<a href='company/orix/'>オリックスレンタカー</a>、
					<a href='company/budget/'>バジェット・レンタカー</a>、
					<a href='company/nissan/'>日産レンタカー</a>など、それぞれのレンタカー会社で絞り込んだ検索も可能です。どのようなレンタカー会社が選べるか詳しく知りたい場合は「
					<a href='companylist/'>レンタカー会社一覧</a>」からご確認いただけます。
				</p>
			</div>
			<div class="js-tabList-content">
				<p>
					スカイチケットでは、大きく分けて6種類のレンタカー車種が予約可能です。モコやワゴンRなどの軽自動車や人気のヴィッツなどのコンパクトカー、大人数でワイワイ楽しむワゴン車、ロードスターをはじめ一流のスポーツカーなど。格安レンタカーにおける幅広い車種を最適なプランでご提供します。10人乗り、SUV、高級車、スタッドレスタイヤ装備などの様々なオプションがあります。 
				</p>
			</div>
			<div class="js-tabList-content">
				<p>
					スカイチケットレンタカーで比較ができるプランは最短1時間から。長期でレンタル希望の方には1週間から1ヶ月以上車借りられるプランもご用意しています。24時間営業・当日予約できる店舗もあります。日帰り帰省や短期の観光、長期のビジネス出張など、幅広いニーズにお応えする比較サイトです。
				</p>
			</div>
			<div class="js-tabList-content">
				<p>
					スカイチケットでは全てのプランを「免責補償料込」の価格でご提供しています。万一の際にも補償がついてるので、安心してお使いいただけます。<br>また、より手厚い補償をご希望の方にはNOC(ノンオペレーションチャージ)を免除する安心補償等の追加補償も有料で追加することが可能です。
				</p>
			</div>
		</div>
	</article>
</div>

<script defer>
	$(window).load(function() {
		/*
		* プラン詳細表示Swiper
		*/
		var campaignSwiper = new Swiper ('.swiper-container', {
			preloadImages: false,
			pagination: '.swiper-pagination',
			paginationClickable: true,
			nextButton: '.swiper-button-next',
 			prevButton: '.swiper-button-prev',
			slidesPerView: 3,
			// autoplay: 3000,
			autoplayStopOnLast: false,
			loop: true,
			autoplayDisableOnInteraction: false,
		})		
	})
</script>

<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "FAQPage",
		"mainEntity": [
			{
				"@type": "Question",
				"name": "どの地域のレンタカーを検索できますか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"スカイチケットでは、日本全国のレンタカーを検索できます。 
					<a href='hokkaido/chitose_international_airport/'>新千歳空港</a>や
					<a href='okinawa/naha_airport/'>那覇空港</a>など、利用する空港からレンタカーの受取ができるのもスカイチケットレンタカーの特徴です。長距離でも乗り捨て可能、近くのレンタカーも検索できる。もちろん、東京都内の繁華街である
					<a href='kanto/tokyo/shinjuku_station/'>新宿駅</a>や
					<a href='kanto/tokyo/tokyo_station/'>東京駅</a>でもレンタカー予約が可能です。すでに車をレンタルしたい空港や駅が決まっている場合は、
					<a href='airportlist/'>空港</a>・
					<a href='stationlist/'>駅</a>・
					<a href='ferryterminallist/'>フェリーターミナル</a>それぞれのページから検索してみてください。"
				}
			},
			{
				"@type": "Question",
				"name": "どこのレンタカー会社の予約ができますか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"スカイチケットでは、安心の大手レンタカー会社から、離島の地場レンタカー会社まで、全国50社以上のレンタカー会社が提供する格安の料金プランを比較、検索・予約ができます。 
					<a href='company/times/'>タイムズカーレンタル</a>、
					<a href='company/toyota/'>トヨタレンタカー</a>、
					<a href='company/nippon/'>ニッポンレンタカー</a>、
					<a href='company/orix/'>オリックスレンタカー</a>、
					<a href='company/budget/'>バジェット・レンタカー</a>、
					<a href='company/nissan/'>日産レンタカー</a>など、それぞれのレンタカー会社で絞り込んだ検索も可能です。どのようなレンタカー会社が選べるか詳しく知りたい場合は「
					<a href='companylist/'>レンタカー会社一覧</a>」からご確認いただけます。"
				}
			},
			{
				"@type": "Question",
				"name": "どのような種類の車から選べますか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"スカイチケットでは、大きく分けて6種類のレンタカー車種が予約可能です。モコやワゴンRなどの軽自動車や人気のヴィッツなどのコンパクトカー、大人数でワイワイ楽しむワゴン車、ロードスターをはじめ一流のスポーツカーなど。格安レンタカーにおける幅広い車種を最適なプランでご提供します。10人乗り、SUV、高級車、スタッドレスタイヤ装備などの様々なオプションがあります。"
				}
			},
			{
				"@type": "Question",
				"name": "レンタルする期間は、最短・最長どれくらいから選べますか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"スカイチケットレンタカーで比較ができるプランは最短1時間から。長期でレンタル希望の方には1週間から1ヶ月以上車借りられるプランもご用意しています。24時間営業・当日予約できる店舗もあります。日帰り帰省や短期の観光、長期のビジネス出張など、幅広いニーズにお応えする比較サイトです。"
				}
			},
			{
				"@type": "Question",
				"name": "保険などの追加料金が発生しますか？",
				"acceptedAnswer": {
					"@type": "Answer",
					"text": 
					"スカイチケットでは全てのプランを「免責補償料込」の価格でご提供しています。万一の際にも補償がついてるので、安心してお使いいただけます。<br>また、より手厚い補償をご希望の方にはNOC(ノンオペレーションチャージ)を免除する安心補償等の追加補償も有料で追加することが可能です。"
				}
			}
		]
	}
</script>
<script type="text/javascript">
    // YOTPOのSSR用内容を消す(widget.jsの読み込み時に再表示される)
    $("#yotpo-testimonials-custom-tab").html('');

	/* 検索オプション表示切り替え */
	const searchOptionBtn = document.querySelector('#js-searchform_options_section_toggler');
	searchOptionBtn.addEventListener('click', function() {
		$('#js-searchform_options_section_toggler .icm-right-arrow').toggleClass('icon-right-arrow_up');
		$('.js-searchform_options_section_wrap').toggleClass('-hidden');
	});
</script>

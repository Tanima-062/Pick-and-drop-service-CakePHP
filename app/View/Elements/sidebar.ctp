<div class="nav">
	<ul class="nav_list">
		<h2 class="nav_list_title">初めてご利用される方へのご案内</h2>
		<li class="nav_list_item is-return"><a href="https://support.skyticket.jp/hc/ja/articles/4405210627993" class="nav_list_item_link is-return"><i class="icm-rentacar"></i><h3>予約〜返却までの流れ</h3></a></li>
		<li class="nav_list_item is-faq"><a href="https://support.skyticket.jp/hc/ja/categories/4403654208025" class="nav_list_item_link is-faq"><i class="icm-question-fill"></i><h3>よくある質問Q&A</h3></a></li>
<?php /*
		<li class="nav_list_item is-schedule"><a href="" class="nav_list_item_link is-schedule">レンタカー貸出当日の流れ</a></li>
		<li class="nav_list_item is-cdw"><a href="" class="nav_list_item_link is-cdw">免責補償制度について</a></li>
*/ ?>
	</ul>
</div>

<!-- <div class="sidebar_links">
	<dl>
		<dt><a href="/rentacar/companylist/" class="btn_sidebar_dt">全国のレンタカー会社一覧<i class="icm-right-arrow"></i></a></dt> -->
<?php
 // foreach ((array)$clientList as $k => $client) {
	// if ($k >= 5) break;
?>
		<!-- <dd>
			<a href="/rentacar/company/<?=$client['Client']['url']?>/" class="icon_company">
				<img src="/rentacar/img/logo/square/<?=$client['Client']['id']?>/<?=$client['Client']['sp_logo_image']?>" alt="<?=$client['Client']['name']?>" width="43">
			</a>
			<div class="sidebar_dd_company">
				<span>人気No.<?=($k + 1)?></span><br /><a href="/rentacar/company/<?=$client['Client']['url']?>/"><?=$client['Client']['name']?></a> -->
<?php
	// if($yotpo_is_active && $use_yotpo){
	// 	$rating_avg = '';
	// 	$rating_count = '';
	// 	$client_id = $client['Client']['id'];
	// 	if($use_yotpo_rating){
	// 		if(isset($ratings[$client_id])){
	// 			$rating_avg = $ratings[$client_id]['rating'];
	// 			$rating_count = $ratings[$client_id]['count'];
	// 		}
	// 	}
?>
				<!-- <a href="/rentacar/company/<?=$client['Client']['url']?>/#reviews">
					<!-- YOTPO -->
					<!-- <div class="yotpo_widget_wrap">
						<div class="yotpo bottomLine"
						  data-appkey="<?=$yotpo_app_key?>"
						  data-domain="https://<?=$yotpo_domain?>/rentacar"
						  data-product-id="<?=$client_id.'cl'?>"
						  data-product-models=""
						  data-name="<?=$client['Client']['name']?>"
						  data-url="https://<?=$yotpo_domain?>/rentacar/company/<?=$client['Client']['url']?>/"
						  data-image-url=""
						  data-description=""
						  data-bread-crumbs=""
						  data-rating-avg="<?= $rating_avg ?>"
						  data-rating-count="<?= $rating_count ?>"
						> -->
						<!-- </div>
					</div> -->
					<!-- YOTPO -->
				<!-- </a> -->
<?php
	// }
?>
			<!-- </div>
		</dd> -->
<?php
 // }
?>
	<!-- </dl> -->
<?php
	// if ($this->viewPath != 'Companylist') {
?>
	<!-- <div class="all_list_link">
		<a href="/rentacar/companylist/" class="btn_all_list_link">全て見る</a>
	</div> -->
<?php
	// }
?>
<!-- </div> -->

<div class="sidebar_links">
	<dl>
		<dt><a href="/rentacar/airportlist/" class="btn_sidebar_dt">全国の空港一覧<i class="icm-right-arrow"></i></a></dt>
		<dd><a href="/rentacar/hokkaido/chitose_international_airport/" class="btn_sidebar_dd is_airport">新千歳空港</a></dd>
		<dd><a href="/rentacar/okinawa/naha_airport/" class="btn_sidebar_dd is_airport">那覇空港</a></dd>
		<dd><a href="/rentacar/kyushu/fukuoka/fukuoka_airport_itazuke_air_base/" class="btn_sidebar_dd is_airport">福岡空港</a></dd>
		<dd><a href="/rentacar/kyushu/kagoshima/kagoshima_airport/" class="btn_sidebar_dd is_airport">鹿児島空港</a></dd>
	</dl>
<?php
	if ($this->viewPath != 'Airportlist') {
?>
	<div class="all_list_link">
		<a href="/rentacar/airportlist/" class="btn_all_list_link">全て見る</a>
	</div>
<?php
	}
?>
</div>

<div class="sidebar_links">
	<dl>
		<dt><a href="/rentacar/stationlist/" class="btn_sidebar_dt">全国の主要駅一覧<i class="icm-right-arrow"></i></a></dt>
		<dd><a href="/rentacar/kanto/tokyo/tokyo_station/" class="btn_sidebar_dd is_station">東京駅</a></dd>
		<dd><a href="/rentacar/kansai/osaka/shinosaka_station/" class="btn_sidebar_dd is_station">新大阪駅</a></dd>
		<dd><a href="/rentacar/kyushu/fukuoka/hakata_station/" class="btn_sidebar_dd is_station">博多駅</a></dd>
		<dd><a href="/rentacar/tokai/aichi/nagoya_station/" class="btn_sidebar_dd is_station">名古屋駅</a></dd>
	</dl>
<?php
	if ($this->viewPath != 'Stationlist') {
?>
	<div class="all_list_link">
		<a href="/rentacar/stationlist/" class="btn_all_list_link">全て見る</a>
	</div>
<?php
	}
?>
</div>

<div class="sidebar_links">
	<dl>
		<dt><a href="/rentacar/ferryterminallist/" class="btn_sidebar_dt">全国のフェリーターミナル一覧<i class="icm-right-arrow"></i></a></dt>
		<dd><a href="/rentacar/okinawa/tomarin/" class="btn_sidebar_dd is_airport">那覇 とまりん</a></dd>
		<dd><a href="/rentacar/okinawa/ishigaki_terminal/" class="btn_sidebar_dd is_airport">石垣島 離島ターミナル</a></dd>
	</dl>
<?php
	if ($this->viewPath != 'Ferryterminallist') {
?>
	<div class="all_list_link">
		<a href="/rentacar/ferryterminallist/" class="btn_all_list_link">全て見る</a>
	</div>
<?php
	}
?>
</div>
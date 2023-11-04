<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php echo $this->Html->docType('html5'); ?>
<?php
	$idName = '';
	if ($this->params['controller'] == 'tops' && $this->action == 'sp_index') {
		$idName = 'top';
	}
?>
<html lang="ja-JP">
<head>
<?php
	// メイン画像のプリーロード
	$activeWebp = $is_google_user_agent === true || strpos((string)env('HTTP_ACCEPT'), 'image/webp') !== false;
	if ($this->params['controller'] == 'tops' && $this->action == 'sp_index') {
		if ($activeWebp) {
		  echo '<link rel="preload" href="/rentacar/img/webp/bg_domestic.webp" as="image" importance="high">';
		} else {
		  echo '<link rel="preload" href="/rentacar/img/bg_domestic.jpg" as="image" importance="high">';
		}
	}
	
	// if ($this->params['controller'] == 'localstoredetail' && $this->action == 'sp_reviews' && !empty($yotpoReviews)) {
	// 	$preloadLogoUrl = '/rentacar/img/logo/square/'.$yotpoReviews[0]['client_id'].'/'.$clientList[$yotpoReviews[0]['client_id']]['sp_logo_image'];
	// 	echo '<link rel="preload" href="'.$preloadLogoUrl.'" as="image" importance="high">';
	// }

	// if ($this->params['controller'] == 'station' && $this->action == 'sp_index') {
	// 	$type = $activeWebp ? '.webp' : '.png';
	// 	if(!empty($stationContents['station-photo-guid'])){
	// 		$stationHeader = $stationContents['station-photo-guid'];
	// 	} else {
	// 		$stationHeader = '/rentacar/img/station_default_header'.$type;
	// 	}
	// 	echo '<link rel="preload" href="'.$stationHeader.'" as="image" importance="high">';
	// }

	// if ($this->params['controller'] == 'municipality' && $this->action == 'sp_index') {
	// 	if ($activeWebp) {
	// 	  echo '<link rel="preload" href="/rentacar/img/webp/station_default_header.webp" as="image" importance="high">';
	// 	} else {
	// 	  echo '<link rel="preload" href="/rentacar/img/station_default_header.png" as="image" importance="high">';
	// 	}
	// }
?>

<?php
	// Googlebotから飛んだ場合はGTMを呼ばない
	if( !$is_google_user_agent ):
?>
	<!-- Google Optimize -->
	<!-- anti-flicker snippet (recommended) 参照：https://support.google.com/optimize/answer/7100284?hl=ja -->
	<style>.async-hide { opacity: 0 !important} </style>
	<script>(function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
	h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
	(a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
	})(window,document.documentElement,'async-hide','dataLayer',4000,
	{'GTM-572TR77':true, 'GTM-WCFD2T':true});</script>
	<!-- /anti-flicker snippet (recommended) -->
	
	<!-- Modified Analytics tracking code with Optimize plugin -->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-57619155-7', 'auto', 'optimizeTracker');	// Update tracker settings
		ga('optimizeTracker.require', 'GTM-572TR77');			// Add this line
												// Remove pageview call
<?php
	// Google Analytics eコマース プラグイン 予約完了時に送信される
	if( $this->viewPath == 'Reservations' && $this->action == 'sp_completion' ){
?>
		ga('create', 'UA-57619155-7', 'auto', 'tracker');	// Update tracker settings
		ga('tracker.require', 'ecommerce');
		ga('tracker.ecommerce:addTransaction', {
			"id": "<?= $reservation['Reservation']['reservation_key']; ?>",
			"affiliation": "<?= $reservation['Reservation']['advertising_cd']; ?>",
			"revenue": "<?= $reservation['Reservation']['amount']; ?>",
		});
		ga('tracker.ecommerce:addItem', {
			"id": "<?= $reservation['Reservation']['reservation_key']; ?>",
			"name": "<?= $reservation['RentOffice']['name']; ?>",
			"sku": "<?= $reservation['CarClass']['name'].' '.$reservation['CarType']['name']; ?>",
			"category": "<?= $reservation['Client']['name']; ?>",
			"quantity": "1"
		});
		ga('tracker.ecommerce:send');
<?php
	}
?>
	</script>
	<!-- /Google Optimize -->
<?php
	endif;
?>

	<?php echo $this->Html->charset('utf-8'); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<?php
	if (!empty($meta_robots) && $meta_robots == 'noindex') {
		echo $this->Html->meta(array('name'=>'robots','content'=>'noindex'));
		echo $this->Html->meta(array('name'=>'robots','content'=>'nofollow'));
		echo $this->Html->meta(array('name'=>'robots','content'=>'noarchive'));
	}
?>
	<title><?php echo $title_for_layout; ?></title>

	<!--icomoon-style.cssで参照するフォントファイルを優先的に読み込む-->
	<link rel="preload" href="/rentacar/css/icomoon/fonts/icomoon.ttf?w169j2"  as="font" crossorigin="anonymous">
<?php
	// echo $this->Html->css(array('/css/sp/basic', '/css/sp/common', '/css/fonts/font-awesome.min','/css/icomoon/fonts/icomoon-style'));

	if ($this->params['controller'] == 'tops' && $this->action == 'sp_index') {
		// トップ専用CSSを読み込む
		echo $this->Html->css(array('/css/sp/top'));
	} else {
		echo $this->Html->css(array('/css/sp/style_new'));
	}

	if(($idName !== 'top') || $is_afb) {
		echo $this->Html->css(array('/css/fonts/font-awesome.min'), ['media' => 'print', 'onload' => "this.media='all'"]);
	}

	echo $this->Html->css(array('/css/icomoon/fonts/icomoon-style'), ['media' => 'print', 'onload' => "this.media='all'"]);
	echo $this->Html->script(array('/js/sp/lib.min'));
	echo $this->Html->script(array('/js/sp/jquery.flexslider-min', '/js/sp/script', '/js/sp/accordion', '/js/sp/pagetop'), ["async"]);
	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
?>

<?php
	if ($this->request->params['is_move_search'] === true && !empty($this->request->query)) {
?>
	<link rel="canonical" href="https://skyticket.jp<?= str_replace('searches', '', Router::url()); ?>"/>
<?php
	} elseif($this->request->params['is_move_search'] === true && empty($this->request->query)) { 
?>
	<link rel="canonical" href="https://skyticket.jp/searches/"/>
<?php
	} else { 
?>
	<link rel="canonical" href="https://skyticket.jp<?= Router::url(); ?>"/>
<?php
	}
?>

<?php
	if (isset($description_for_layout)) {
?>
	<meta name="description" content="<?php echo $description_for_layout; ?>">
<?php
	}
?>

<?php
	if (isset($keywords)) {
?>
	<meta name="keywords" content="<?php echo $keywords;?>">
<?php
	}
?>
	<meta property="og:image" content="https://skyticket.jp/rentacar/img/ogp_image.png">

	<!-- フルスクリーンモードにすることによりブラウザ独自のツールバーの表示を抑制する :remove SmartAppBanner -->
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">

	<!-- 必須: polyfill: 共通ヘッダーフッター -->
	<!-- webcomponents-loaderにはdeferを付けてはいけない -->
	<script src="https://wc.skyticket.jp/js/webcomponents-loader.js?rentacar-version=<?=date('Y-m-d-H');?>"></script>
	<!-- SP用ヘッダー -->
	<script src="https://wc.skyticket.jp/js/skyticket-header-sp.js?rentacar-version=<?=date('Y-m-d-H');?>"></script>
	<!-- SP用フッター　-->
	<script src="https://wc.skyticket.jp/js/skyticket-footer-sp.js?rentacar-version=<?=date('Y-m-d-H');?>" defer></script>

<?php
	// YOTPO
	if($yotpo_is_active && $use_yotpo){
?>
	<script type="text/javascript">
		(function e(){var e=document.createElement("script");e.type="text/javascript",e.async=true,e.src="//staticw2.yotpo.com/<?php echo $yotpo_app_key; ?>/widget.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)})();
	</script>
<?php
		echo $this->Html->script(array('yotpo-custom.js'));
	} 
?>

<!-- MailChimp 計測タグ -->
	<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/894c09f10262eff5865e40ea9/964b17907bdd71573af50c19e.js");</script>
</head>
<?php
	// bodyタグにページごとのクラス名をつける
	$page_identifier = '';	
	$page_identifier_appx = '';
	
	if (($this->action == 'sp_reviews') ||
	($this->action == 'sp_article')){
		$page_identifier_appx = '-'.ltrim(mb_strtolower($this->action), 'sp_');
	}
	// ↑子ページのスタイルを全て分離できる状態じゃないと、index以外全てに一気にappxつける指定するの無理。たとえばmypagesやreservations配下。

	$page_identifier = mb_strtolower($this->name).$page_identifier_appx;
?>
<?php
	// Skyscannerの場合
	$is_skyscanner_ad = false;
	if(isset($_SESSION['advertising_cd'])){
		if($_SESSION['advertising_cd'] == "skyscanner_rc"){
			$is_skyscanner_ad = true;
		}
	}
	if( $this->viewPath == 'Reservations' && $this->action == 'sp_completion' && $is_skyscanner_ad){
?>
<body id="<?php echo $idName; ?>" class="sp_<?= $page_identifier ?>_page" onload="fireSkyscannerTag()">
<?php
	} else {
?>
<body id="<?php echo $idName; ?>" class="sp_<?= $page_identifier ?>_page">
<?php
	}
?>
<!-- https://developers.google.com/tag-manager/devguide#adding-data-layer-variables-to-a-page -->
<?php echo $this->element('common_set_data_layer'); ?>
<?php
	// Googlebotから飛んだ場合はGTMを呼ばない
	if( !$is_google_user_agent ):
?>
	<!-- Google Tag Manager -->
	<noscript><iframe rel="preconnect" src="//www.googletagmanager.com/ns.html?id=GTM-WCFD2T"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-WCFD2T');</script>
	<!-- End Google Tag Manager -->
<?php
	endif;
?>
	<div>
<?php
	if ( // 予約導線専用
		($this->params['controller'] === 'reservations')
		&& ($this->action == 'sp_step1' || $this->action == 'sp_step2')) {
		
		echo $this->element('sp_header_reservations');

	} else if ( // レンタカー会社サイトからの流入~決済直前画面まで
		(!empty($fromRentacarClient)) && (
		($this->params['controller'] === 'company') 
		|| ($this->params['controller'] === 'searches') 
		|| ($this->params['controller'] === 'plan') 
		|| (($this->params['controller'] === 'reservations') && ($this->action === 'sp_processing'))
		)) {
		
		echo $this->element('sp_header_nolinks');

	} else { // 共通ヘッダー（afbは共通ヘッダーをカスタム）
		
		echo $this->element('sp_header');

	}

	echo $this->fetch('content');
	echo $this->element('sp_progress_bar');
	echo $this->element('sp_pagetop');

	if ( // 予約導線専用
		($this->params['controller'] === 'reservations')
		&& ($this->action == 'sp_step1' || $this->action == 'sp_step2')) {
		
		echo $this->element('sp_footer_reservations');

	} else if ( // レンタカー会社サイトからの流入~決済直前画面まで
		(!empty($fromRentacarClient)) && (
		($this->params['controller'] === 'company') 
		|| ($this->params['controller'] === 'searches') 
		|| ($this->params['controller'] === 'plan') 
		|| (($this->params['controller'] === 'reservations') && ($this->action === 'sp_processing'))
		)) {
		
		echo $this->element('sp_footer_customlinks');

	} else { // 共通フッター

		echo $this->element('sp_footer');

	}

	echo $this->element('sql_dump');
	if (!empty($use_searchbox)) {
		if (!empty($area_arr)) {
			echo $this->Html->scriptBlock("var area_arr = {$area_arr};");
		}
		if (!empty($station_arr)) {
			echo $this->Html->scriptBlock("var station_arr = {$station_arr};");
		}
	//	echo $this->Html->scriptBlock("var area_arr = {$area_arr}; var station_arr = {$station_arr};");
		// region
		if ($this->params['controller'] == 'region' && $this->action == 'sp_index') {
			echo $this->Html->script(array('sp/jquery.bxslider', 'sp/region_slider'));
		}
		if ($this->action == 'sp_index' || $this->action == 'sp_reviews') {
			if ($this->params['controller'] !== 'searches') {
				echo $this->Html->script(array('sp/jquery-ui.min', 'sp/jquery.ui.datepicker-ja.min', 'sp/search', 'sp/sp_datepicker_custom'));
			} else {
				// ABテスト中にはSearches/sp//sp_index.ctpで検索周りのスクリプトを直接読み込んでる
				echo $this->Html->script(array('sp/jquery-ui.min', 'sp/jquery.ui.datepicker-ja.min', 'sp/sp_datepicker_custom'));
			}
		}

		// TOPのみ座標取得JSロード
		if ($this->params['controller'] == 'tops' && $this->action == 'sp_index') {
			// 既にcookie保存済みの場合はロードしない
			if (empty($existsHistoryCookie)) {
	//			echo $this->Html->script('current_location');
			}
		}
	}
?>
<?php
	if ($this->params['controller'] == 'company' && $this->action == 'sp_reviews') {
		echo $this->Html->script(array('meta-desc-optimize.js'));
	}
?>
	</div>
	
<?php 
	if (!IS_PRODUCTION): 
?>
	<style>
		.cake-sql-log {
			width:100%;
			font-size:9px;
			word-break: break-all;
		}
		.cake-sql-log th,
		.cake-sql-log td {
			border: solid #ccc 1px;
		}
	</style>
<?php
	endif;
?>

<?php
	if(!IS_PRODUCTION) { //開発環境で画像がない場合noimageを表示する試験運用
?>
	<script type="text/javascript">
		$('img').each(function() {
			$('img').error(function() {
				$(this).attr({
					src: '/rentacar/img/noimage.png',
					alt: 'No Image',
					style: 'object-fit: contain; border: 1px solid #eee;'
				});
			});
		});
	</script>
<?php
	}
?>

<?php 
	if($use_yotpo && $yotpo_is_active){ 
		if($use_yotpo_rating){ 
?>
		<!-- Change Yotpo review -->
	<script>
		var documentObserver = function() {
			var option = { childList: true, subtree: true };

			var observer = new MutationObserver(findClassListener);

			observer.observe(document.body, option);

			return observer;
		};

		var findClassListener = function(mutation) {
			mutation.forEach(function(m) {
				if (m.target.className.indexOf('yotpo') === -1) {
					return;
				}

				var avg = '';
				if(m.target.hasAttribute('data-rating-avg')){
					avg = m.target.getAttribute('data-rating-avg');
				} else {
					return;
				}

				//var count = '';
				//if(m.target.hasAttribute('data-rating-count')){
				//	count = m.target.getAttribute('data-rating-count');
				//} else {
				//	return;
				//}

				var nodes = m.addedNodes;
				for (i = 0; i < nodes.length; i++) {
					if (nodes[i].nodeType != 1) {
						continue;
					}

					var targets = nodes[i].getElementsByClassName('text-m');
					if (targets != null) {
						for(j=0; j<targets.length; j++) {

							var review_link_text = targets[j].innerHTML;
							if(review_link_text.indexOf(" レビュー") >= 0){
								var review_link_count = review_link_text.replace(" レビュー", "");
								var text = ' '+avg+' ('+review_link_count+'件)';
								targets[j].innerHTML = text;
							}

						}
					}
				}
				m.target.parentElement.style.display = "block";
			});
		};
		documentObserver();
	</script>
<?php 
		} else { 
?>
	<script>
		$('.yotpo_widget_wrap').show();
	</script>
<?php 
		} 
	} 
?>

<!-- Yahoo Tag Manager -->
	<script type="text/javascript">
	  (function () {
		var tagjs = document.createElement("script");
		var s = document.getElementsByTagName("script")[0];
		tagjs.async = true;
		tagjs.src = "//s.yjtag.jp/tag.js#site=JogvUwD";
		s.parentNode.insertBefore(tagjs, s);
	  }());
	</script>
<?php
	if (false/*IS_PRODUCTION*/) {
?>
	<script src="https://www.google.com/recaptcha/api.js?render=<?=$recaptcha_key;?>"></script>
	<script>
		grecaptcha.ready(function() {
			grecaptcha.execute('<?=$recaptcha_key;?>', {action: '<?=$this->viewPath.'/'.$this->action;?>'}).then(function(token) {
			});
		});
	</script>
<?php
	} 
?>
	<noscript>
	  <iframe src="//b.yjtag.jp/iframe?c=JogvUwD" width="1" height="1" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
	</noscript>
	
</body>
</html>

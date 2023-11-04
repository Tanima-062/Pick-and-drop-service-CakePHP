<!-- ヘッダー-->
<div class="section_title_blue">
	<h1>レンタカー会社一覧からレンタカーを探す</h1>
</div>
<p class="lead-paragraph">国内レンタカー各社の料金比較・オンライン予約情報。プラン・乗り捨てプランも取り揃えています。</p>
<h2 class="company_list_h2 headline-small">レンタカー会社一覧</h2>
<div class="left_contents clearfix">
<?php
	foreach($clientList as $k => $v){
?>
	<div class="company_list_div">
		<div id="<?php echo $v['Client']['id']; ?>" class="company_list_title">
<?php
		if ($v['Client']['sp_logo_image']) {
?>
			<img src="/rentacar/img/logo/square/<?php echo $v['Client']['id']; ?>/<?php echo $v['Client']['sp_logo_image']; ?>" alt="<?php echo $v['Client']['name']; ?>" loading="lazy" importance="low" decoding="async">
<?php
		}
?>
			<h3 class="company_list_title_text"><?php echo $v['Client']['name']; ?></h3>
<?php
		if ($yotpo_is_active && $use_yotpo) {
			$rating_avg = '';
			$rating_count = '';
			$client_id = $v['Client']['id'];
			if ($use_yotpo_rating) {
				if (array_key_exists($client_id, $ratings)) {
					$rating_avg = $ratings[$client_id]['rating'];
					$rating_count = $ratings[$client_id]['count'];
				}
			}
?>
<?php 
			if (!empty($v['Client']['url'])) { 
?>
			<a href="/rentacar/company/<?=$v['Client']['url'];?>/#reviews">
<?php 
			} else { 
?>
			<a href="/rentacar/company?company_id=<?php echo  $v['Client']['id']; ?>">
<?php 
			} 
?>
				<!-- YOTPO -->
				<div class="yotpo_widget_wrap yotpo_companyList">
					<div class="yotpo bottomLine"
					data-appkey="<?php echo $yotpo_app_key; ?>"
					data-domain="https://<?php echo $yotpo_domain; ?>/rentacar"
					data-product-id="<?php echo $v['Client']['id'].'cl'; ?>"
					data-product-models=""
					data-name="<?php echo $v['Client']['name']; ?>"
					data-url="https://<?php echo $yotpo_domain; ?>/rentacar/company/<?=$v['Client']['url'];?>/"
					data-image-url=""
					data-description=""
					data-bread-crumbs=""
					data-rating-avg="<?= $rating_avg ?>"
					data-rating-count="<?= $rating_count ?>"
					>
					</div>
				</div>
				<!-- YOTPO -->
			</a>
<?php
		}
?>
		</div>
		<div class="company_description_div clearfix">
			<p class="company_description"><?php echo $v['RcPostmeta']['meta_value']; ?></p>
<?php
		if (!empty($v['Client']['url'])) {
?>
			<a class="show_company_detail" href="/rentacar/company/<?php echo  $v['Client']['url']; ?>/">この会社の店舗探す</a>
<?php
		} else {
?>
			<a class="show_company_detail" href="/rentacar/company?company_id=<?php echo  $v['Client']['id']; ?>">この会社の店舗探す</a>
<?php
		}
?>
		</div>
	</div>
<?php
	}
?>
</div>

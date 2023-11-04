<div class="wrap contents clearfix">
<?php
	echo $this->element('progress_bar');
?>
	<div class="contents_type clearfix">
		<div class="contents_type_main">
			<div class="page_title">
				<h1 class="page_title_h2">レンタカー会社一覧からレンタカーを探す</h1>
			</div>
			<p class="page_title_text">国内レンタカー各社の料金比較・オンライン予約情報。プラン・乗り捨てプランも取り揃えています。</p>
			<h2 class="area_title company_list_h2 headline-small">レンタカー会社一覧</h2>
<?php foreach($clientList as $k => $v){ ?>
			<div class="company_list_div">
				<div id="<?php echo $v['Client']['id']; ?>" class="company_list_title" style="height:60px">
					<?php if($v['Client']['sp_logo_image']){ ?>
					<img src="/rentacar/img/logo/square/<?php echo $v['Client']['id']; ?>/<?php echo $v['Client']['sp_logo_image']; ?>" width="48" alt="<?php echo $v['Client']['name']; ?>" loading="lazy" importance="low" decoding="async">
					<?php } ?>
					<h3 class="company_list_title_text"><?php echo $v['Client']['name']; ?></h3>
					<?php if($yotpo_is_active && $use_yotpo){
						$rating_avg = '';
						$rating_count = '';
						$client_id = $v['Client']['id'];
						if($use_yotpo_rating){
							if(array_key_exists($client_id, $ratings)){
								$rating_avg = $ratings[$client_id]['rating'];
								$rating_count = $ratings[$client_id]['count'];
							}
						}

						if(!empty($v['Client']['url'])){
							$company_url = "/rentacar/company/".$v['Client']['url']."/#reviews";
						} else {
							$company_url = "/rentacar/company?company_id=".$v['Client']['id'];
						}
					?>
					<a href="<?=$company_url;?>">
					<!-- YOTPO -->
					<div class="yotpo_widget_wrap yotpo_inline">
						<div class="yotpo bottomLine"
						  data-appkey="<?php echo $yotpo_app_key ?>"
						  data-domain="https://<?php echo $yotpo_domain ?>/rentacar"
						  data-product-id="<?php echo $v['Client']['id'].'cl'; ?>"
						  data-product-models=""
						  data-name="<?php echo $v['Client']['name']; ?>"
						  data-url="https://<?php echo $yotpo_domain ?>/rentacar/company/<?=$v['Client']['url'];?>/"
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
					<?php } ?>
				</div>
				<p class="company_description"><?php echo $v['RcPostmeta']['meta_value']; ?></p>
				<?php if(!empty($v['Client']['url'])){ ?>
				<a class="show_company_detail clearfix" href="/rentacar/company/<?=$v['Client']['url'];?>/">この会社の店舗を探す</a>
				<?php } else { ?>
				<a class="show_company_detail clearfix" href="/rentacar/company?company_id=<?php echo  $v['Client']['id']; ?>">この会社の店舗を探す</a>
				<?php } ?>
			</div>
<?php } ?>
		</div>
		<div class="contents_type_side">
			<?php echo $this->element("sidebar"); ?>
		</div>
	</div>
</div>



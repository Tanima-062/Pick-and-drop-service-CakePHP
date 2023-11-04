<?php
	echo $this->Html->css('sp/jquery-ui',null,array('inline'=>false));
	echo $this->Html->script(array('/js/sp/search','/js/sp/jquery-ui.min'));
?>

<!-- ヘッダー-->
<div class="review-page-container">
	<h1 class="page-title"><?php echo $clientInfo['Client']['name']; ?>の感想・口コミ</h1>
		
<?php
	// YOTPO ReviewsWidget
	if($yotpo_is_active && $use_yotpo){
		$rating_avg = '';
		$rating_count = '';
		$client_id = $clientInfo['Client']['id'];
		if($use_yotpo_rating){
			if(array_key_exists($client_id, $ratings)){
				$rating_avg = $ratings[$client_id]['rating'];
				$rating_count = $ratings[$client_id]['count'];
			}
		}
		// 平均点数が空き＝レビューが存在しない
		if(!empty($rating_avg)) {
?>
	<div class="-section">
		<div class="review-yotpo">
			<div class="review-yotpo-title">店舗の総合評価</div>
			<div class="review-yotpo-score"><?php echo number_format($rating_avg,1,'.','') ?>点</div>
			<div class="pref_review_star" style="display:inline-block;">
<?php
			for($i=1;$i<6;$i++){
				if($i <= $rating_avg){
					echo '<i class="fa fa-star yellow"></i>';
				}else if($i-0.5 <= $rating_avg && $i > $rating_avg){
					echo '<i class="fa fa-star-half-empty yellow"></i>';
				}else{
					echo '<i class="fa fa-star-o yellow"></i>';
				}
			}
?>
			</div>
		</div>
	</div>
	
	<h2 class="page-sub-title">最新のレビュー</h2>
	<div class="yotpo">
		<div id="recent-review"></div>
	</div>
<?php
		}
	}
?>
		
	<div class="btn-type-link_wrap">
		<a class="btn-type-link" onclick="location.href='/rentacar/company/<?=$clientInfo['Client']['url'];?>'">会社の詳細を見る</a>
	</div>
	
	<hr class="page_hr" />
	
	<section id="search" class="-section">
		<?php
			echo $this->Form->create('Search',array('controller'=>'searches','action'=>'index', 'inputDefaults'=>array(
				'label'=>false,
				'div'=>false,
				'hiddenField'=>false,
				'legend'=>false,
				'fieldset'=>false
			),'type'=>'get'));
			echo $this->Form->hidden('client_id', array('value'=>$clientInfo['Client']['id']));
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
	
<?php
	// YOTPO
	if($yotpo_is_active && $use_yotpo){
		// 平均点数が空き＝レビューが存在しない
		if(!empty($rating_avg)) {
?>
	<hr class="page_hr" />
	
	<h2 class="page-sub-title">すべてのレビュー</h2>
	
	<div class="yotpo_container">
		<div class="yotpo_main_wrap">
			<div class="yotpo yotpo-main-widget"
				data-product-id="<?php echo $clientInfo['Client']['id'].'cl'; ?>"
				data-name="<?php echo $clientInfo['Client']['name']; ?>"
				data-url="https://<?php echo $yotpo_domain; ?><?= Router::url(); ?>"
				data-image-url=""
				data-description="">
				<?= !empty($main_widget) ? $main_widget : ''; ?>
			</div>
		</div>
	</div>
<?php
		}
	}
?>
</div>

<?php echo $this->element('sp_popular_airport_linklist'); ?>

<?php echo $this->element("sp_sidebar"); ?>


<script>
	$(window).on('load', function () {
		if($(".yotpo-reviews").length) {
			var html = $(".yotpo-reviews").children('.yotpo-regular-box-filters-padding ').first().clone();
			$("#recent-review").html(html)
		}
	})
</script>
<div class="wrap contents clearfix">
	<?php echo $this->element('progress_bar'); ?>
	<div class="contents_type clearfix">
		<div class="review-page-container">
			<h1 class="page-title"><?=$clientInfo['Client']['name']?>の感想・口コミ</h1>
			<div class="contents-top">
<?php
	// YOTPO
	if($yotpo_is_active && $use_yotpo ){
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
				<div class="review-yotpo">
					<div class="review-yotpo-title">会社の総合評価</div>
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
<?php
		}
	}
?>
				<a class="btn-type-link" onclick="location.href='/rentacar/company/<?=$clientInfo['Client']['url'];?>'">会社の詳細を見る</a>
			</div>
			
			<!--検索エリア -->
			<section>
				<section class="content_section contents_search-wrap">
					<?php
						echo $this->Form->create('Search',array('controller'=>'searches','action'=>'index', 'inputDefaults'=>array(
							'label'=>false,
							'div'=>false,
							'hiddenField'=>false,
							'legend'=>false,
							'fieldset'=>false,
						),'type'=>'get','class'=>'contents_search'));
						echo $this->Form->hidden('client_id' ,array('value'=>$clientInfo['Client']['id']));
						//検索　エリア・日付・人数などは共通処理のためエレメント化
						echo $this->element('searchform_main');
					?>
						<div class="searchform_submit_section_wrap">
							<?php echo $this->element('searchform_submit'); ?>
						</div>
					<?php
						echo $this->Form->end();
					?>	
				</section>
			</section>
			<!--検索エリア End -->
			
			<div class="contents_type_main">			
<?php 
	// YOTPO
	if($yotpo_is_active && $use_yotpo) {
		// 平均点数が空き＝レビューが存在しない
		if(!empty($rating_avg)) {
?>
				<div class="yotpo_container">
					<div class="yotpo_main_wrap">
						<div class="yotpo yotpo-main-widget"
							data-product-id="<?php echo $clientInfo['Client']['id'].'cl'; ?>"
							data-name="<?php echo $clientInfo['Client']['name']; ?>"
							data-url="https://<?php echo $yotpo_domain; ?><?= Router::url(); ?>"
							data-image-url=""
							data-description="" >
							<?= !empty($main_widget) ? $main_widget : ''; ?>
						</div>
					</div>
				</div>
<?php
		}
	} 
?>

			</div>

			<div class="contents_type_side">
				<?php echo $this->element("sidebar"); ?>
			</div>
		</div>
	</div>

	<?php echo $this->element('popular_airport_linklist'); ?>
</div>

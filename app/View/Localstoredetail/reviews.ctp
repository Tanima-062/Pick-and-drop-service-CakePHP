<div class="wrap contents clearfix local_store_wrap">
	<?php echo $this->element('progress_bar'); ?>
	<div class="review-page-container">
		<h1 class="page-title"><?= $clientInfo['name'] ?>の<?= $officeInfo['name'] ?>の感想・口コミ</h1>
<?php
	if($yotpo_is_active && $use_yotpo){
		if(!empty($reviewCount)){
?>
<!-- YOTPO -->
		<div class="contents-top">
			<div class="review-yotpo">
				<div class="review-yotpo-title">店舗の総合評価</div>
				<div class="review-yotpo-score"><?php echo number_format($reviewAvg,1,'.','') ?>点</div>
				<div class="pref_review_star" style="display:inline-block;">
<?php
			for($i=1;$i<6;$i++){
				if($i <= $reviewAvg){
					echo '<i class="fa fa-star yellow"></i>';
				}else if($i-0.5 <= $reviewAvg && $i > $reviewAvg){
					echo '<i class="fa fa-star-half-empty yellow"></i>';
				}else{
					echo '<i class="fa fa-star-o yellow"></i>';
				}
			}
?>
				</div>
			</div>
			<a class="btn-type-link" onclick="location.href='/rentacar/company/<?=$clientInfo['url'];?>/<?=$officeInfo['url'];?>'">店舗の詳細を見る</a>
		</div>
		<?php
			// schema.orgの構造化マークアップ
			echo $this->element('schema_org_autorental');
		?>
<?php
		}
	}
?>
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
					echo $this->Form->hidden('client_id' ,array('value' => $clientId));
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

		<h2 class="page-sub-title">すべてのレビュー</h2>
<?php 
	if(!empty($yotpoReviews)) {
		// YOTPO
		if($yotpo_is_active && $use_yotpo){ 
?>
		<section id="reviews" class="yotpo_api_custom_wrap">
			<?php echo $this->element('yotpo_review'); ?>
		</section>
<?php
		}
	}
?>
	
	</div>

	<?php echo $this->element('popular_airport_linklist'); ?>
</div>

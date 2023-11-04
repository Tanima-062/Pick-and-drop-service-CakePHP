<?php
	echo $this->Html->css(array('sp/jquery-ui'),null,array('inline'=>false));
	echo $this->Html->script(array('/js/sp/search', '/js/sp/jquery.ui.datepicker-ja.min', '/js/sp/jquery-ui.min'));
?>

<div class="review-page-container">
	<h1 class="page-title"><?= $clientInfo['name'] ?>の<?= $officeInfo['name'] ?>の感想・口コミ</h1>

<?php
	if($yotpo_is_active && $use_yotpo){
		if(!empty($reviewCount)){
?>
<!-- YOTPO -->
	<div class="-section">
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
		<?php
			// schema.orgの構造化マークアップ
			echo $this->element('schema_org_autorental');
		?>
		<div id="recent_review">
			<h2 class="page-sub-title">最新のレビュー</h2>
			<div class="yotpo_api_custom_wrap" style="padding: 0 16px;">
				<div class="review_content_wrap">
					<ul class="review_content_wrap">
						<li class="review_wrap">
							<div class="review_head">
								<div class="icon">
									<i class="icm-user-shape"></i>
								</div>
								<div class="stars">
<?php
			for($i=1;$i<6;$i++){
				if($i <= $yotpoReviews[0]['score']){
					echo '<i class="icm-star-full"></i>';
				}else{
					echo '<i class="icm-star-empty"></i>';
				}
			}
?>
								</div>
							</div>
							<div class="review_body">
								<div class="review_title"><?= $yotpoReviews[0]['title']; ?></div>
								<div class="review_content"><?= $yotpoReviews[0]['content']; ?></div>
								<div class="link_wrap">
										<?= $yotpoReviews[0]['client_name']; ?>
									&nbsp;&nbsp;|&nbsp;&nbsp;
										<?= $yotpoReviews[0]['office_name']; ?>
								</div>
								<div class="posted_date">
									投稿日：<?= $yotpoReviews[0]['created_at']; ?>
								</div> 
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
<!-- YOTPO -->
<?php
		}
	}
?>
		<div class="btn-type-link_wrap">
			<a class="btn-type-link" onclick="location.href='/rentacar/company/<?=$clientInfo['url'];?>/<?=$officeInfo['url'];?>'">店舗の詳細を見る</a>
		</div>
	</div>
	<div id="search" class="-section">
		<?php
			echo $this->Form->create('Search',array('controller'=>'searches','action'=>'index', 'inputDefaults'=>array(
				'label'=>false,
				'div'=>false,
				'hiddenField'=>false,
				'legend'=>false,
				'fieldset'=>false
			),'type'=>'get'));
			echo $this->Form->hidden('client_id' ,array('value' => $clientId));
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
	</div>

<?php
	if(!empty($yotpoReviews)){
		// YOTPO
		if ($yotpo_is_active && $use_yotpo) {
?>
	<div class="-section">
		<h2 class="page-sub-title">すべてのレビュー</h2>
		<section id="reviews" class="yotpo_api_custom_wrap" style="padding-top: 0;">
			<?php echo $this->element('sp_yotpo_review'); ?>
		</section>
	</div>
<?php
		}
	}
?>
</div>

<?php echo $this->element('sp_popular_airport_linklist'); ?>

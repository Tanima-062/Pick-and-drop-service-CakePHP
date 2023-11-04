	<div class="contents_top_title_div">
		<div class="contents_table_row">
<?php
	if(isset($clientInfo['sp_logo_image'])){
?>
			<img src="/rentacar/img/logo/square/<?php echo $clientInfo['id']; ?>/<?php echo $clientInfo['sp_logo_image']; ?>" alt="<?=$clientInfo['name']." ".$officeInfo['name']?>" loading="lazy" importance="low" decoding="async">
<?php
	}
?>
			<h1 class="contents_table_row_title"><?=$clientInfo['name']?> <?=$officeInfo['name']?>の予約・プラン比較</h1>
		</div>
	</div>

<?php
	if($yotpo_is_active && $use_yotpo){
		if(!empty($reviewCount)){
?>
<!-- YOTPO -->
	<div style="text-align:right;margin:16px 16px 0 16px">
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
		<a href="#reviews" style="display:inline-block;"><?php echo number_format($reviewAvg,1,'.','').' ('.$reviewCount.'件)'; ?></a>
	</div>
	<?php
		// schema.orgの構造化マークアップ
		echo $this->element('schema_org_autorental');
    ?>
<?php
		}
	}
?>

	<div class="lead-paragraph"><?=$clientInfo['public_relations']?></div>
	<div class="clearfix">
		<table class="store_detail_table">
			<tr>
				<th>営業時間</th>
				<td>
<?php 
	if(!empty($officeInfo['businessHours'])){ 
?>
					<p><?=$officeInfo['businessHours']?></p>
<?php 
	} 
?>
				</td>
			</tr>
<?php
	if(!empty($officeInfo['office_hours_remark'])){
?>
			<tr>
				<th>営業時間補足</th>
				<td><?= nl2br($officeInfo['office_hours_remark']); ?></td>
			</tr>
<?php
	}
	if(!empty($officeInfo['office_holiday_remark'])){
?>
			<tr>
				<th>営業日補足</th>
				<td><?= nl2br($officeInfo['office_holiday_remark']); ?></td>
			</tr>
<?php
	}
?>
			<tr>
				<th>住所</th>
				<td><?=$officeInfo['address_replaced']?></td>
			</tr>
<?php
	if(isset($officeInfo['access_dynamic']) && !empty($officeInfo['access_dynamic'])){
?>
			<tr>
				<th>アクセス</th>
				<td><?= nl2br($officeInfo['access_dynamic']); ?></td>
			</tr>
<?php
	}
	if(isset($officeInfo['rent_meeting_info']) && !empty($officeInfo['rent_meeting_info'])){
?>
			<tr>
				<th>送迎と待ち合わせについて</th>
				<td>
					<?= nl2br($officeInfo['rent_meeting_info']); ?>
				</td>
			</tr>
<?php
	}
?>
			<tr class="border-bottom_tr">
				<th>クレジットカード</th>
				<td>
<?php
	if (!empty($clientInfo['accept_card'])) {
		if (!empty($clientCardInfo)) {
			$clientCardList = $clientCardInfo[key($clientCardInfo)];
			for ($i = 0; $i < count($clientCardList['name']); $i++) {
				echo ' ' . $this->Html->image($clientCardList['url'][$i], array('alt' => $clientCardList['name'][$i]));
			}
			echo '<br>';
		}
		echo 'クレジットカード可';
	} else {
		echo '使用できません';
	}
?>
				</td>
			</tr>
		</table>
	</div>
	<div class="ggl_map_div">
		<iframe width="100%" height="300" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key=<?=getGoogleAPIKey( GOOGLE_APIKEY_Maps_Embed )?>&q=<?= $officeInfo['address'] ?>"></iframe>
	</div>
	<section id="search">
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
	</section>

<?php
	if(!empty($yotpoReviews)) {
	// YOTPO
		if($yotpo_is_active && $use_yotpo){
?>
	<section id="reviews" class="yotpo_api_custom_wrap">
		<h2 class="review_section_title"><?=$clientInfo['name']?> <?=$officeInfo['name']?>の感想・口コミ</h2>
		<script>
			const $_isExistedReviewPage = true
		</script>
		<?php echo $this->element('sp_yotpo_review'); ?>
	</section>
<?php
		}
	}
?>

<?php
	if(isset($officeNearList) && !empty($officeNearList)){
?>
	<div class="search_list_title">
		<h2><?=$clientInfo['name']?> <?=$officeInfo['name']?>のその他の近隣営業所から探す</h2>
	</div>
	<div class="search_list_div">
		<ul class="search_list_ul">
<?php
		foreach($officeNearList as $officeNear){
			if(!empty($officeNear['Office']['url']) && !empty($officeNear['Client']['url'])){
?>
			<li><a href="/rentacar/company/<?=$officeNear['Client']['url'];?>/<?=$officeNear['Office']['url'];?>/"><?=$officeNear['Client']['name'];?>・<?=$officeNear['Office']['name'];?></a></li>
<?php
			} else {
?>
			<li><a href="/rentacar/localstoredetail?store_id=<?=$officeNear['Office']['id'];?>"><?=$officeNear['Client']['name'];?>・<?=$officeNear['Office']['name'];?></a></li>
<?php
			}
		}
?>
		</ul>
	</div>
<?php
	}
?>

	<?php echo $this->element('sp_prefecture_linklist'); ?>

	<?php echo $this->element('sp_popular_airport_linklist'); ?>

<?php
	if(!empty($yotpoReviews)){
?>
<script type="text/javascript">
	$(document).ready(function(){

		// 使ってない？
		// more ReviewList
		// var heightReviewList = $("#js_review_list > ul").height();
		// $("#js_review_list").height( heightReviewList + 24 );
		// $("#js_btn_more_review").on("click", function(){
		// 	var review_cnt = $("#js_review_list .variable_cont_hidden").length;
		// 	$("#js_review_list .variable_cont_hidden").each(function(index, el){
		// 		if( index >= 10 ) return false;

		// 		$(el).removeClass("variable_cont_hidden");

		// 		if( index == review_cnt - 1 ){
		// 			$("#js_btn_more_review").parent(".variable_cont_more").hide();
		// 		}
		// 	});
		// 	var heightListAfter = $("#js_review_list > ul").height();
		// 	$("#js_review_list").height( heightListAfter + 24 );
		// });
	});
</script>
<?php
	}
?>
	<link rel="stylesheet" type="text/css" href="/rentacar/css/sp/jquery-ui.css" media="print" onload="this.media='all'">
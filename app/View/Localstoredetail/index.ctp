<?php
	echo $this->Html->css('swiper.min',null,array('inline'=>false, 'media'=>'print', 'onload'=>'this.media=\'all\''));
	echo $this->Html->script(array('/js/swiper.min'));
?>

<div class="wrap contents clearfix local_store_wrap">
	<?php echo $this->element('progress_bar'); ?>
	<div class="page_title">
	  	<h1 class="page_title_h2"><?=$clientInfo['name']?> <?=$officeInfo['name']?>の予約・プラン比較</h1>
	</div>
	<p class="page_title_text"><?=$clientInfo['public_relations']?></p>
	<div class="local_store_title" style="padding-top: 20px; padding-bottom: 20px;">
<?php 
	if(isset($clientInfo['sp_logo_image'])){ 
?>
	  	<img src="/rentacar/img/logo/square/<?php echo $clientInfo['id']; ?>/<?php echo $clientInfo['sp_logo_image']; ?>" alt="<?=$clientInfo['name']." ".$officeInfo['name']?>" height="48" width="48" loading="lazy" importance="low" decoding="async">
<?php 
	} 
?>
	  	<p style="margin-top: 4px; margin-bottom: 4px;"><?=$clientInfo['name']?> <?=$officeInfo['name']?></p>
<?php
  	if($yotpo_is_active && $use_yotpo){
		if(!empty($reviewCount)){
?>
		<!-- YOTPO -->
		<div style="text-align:right;margin:16px">
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
	</div>

	<div class="local_store_access clearfix">
		<table class="local_store_table">
			<tr>
				<th style="width: 135px">営業時間</th>
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
		  		<td>
					<?= nl2br($officeInfo['office_hours_remark']); ?>
				</td>
			</tr>
<?php 
	} 
?>
<?php 
	if(!empty($officeInfo['office_holiday_remark'])){
?>
			<tr>
				<th>営業日補足</th>
				<td>
					<?= nl2br($officeInfo['office_holiday_remark']); ?>
				</td>
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
?>

<?php 
	if(isset($officeInfo['rent_meeting_info']) && !empty($officeInfo['rent_meeting_info'])){ 
?>
			<tr>
				<th>送迎と待ち合わせについて</th>
				<td><?= nl2br($officeInfo['rent_meeting_info']); ?></td>
			</tr>
<?php 
	}
?>
			<tr>
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

		<div class="google_map<?php if(!$officeInfo['address']){ ?> noimage<?php } ?>">
			<iframe width="365" height="265" frameborder="0" style="border:0;" src="https://www.google.com/maps/embed/v1/place?key=<?=getGoogleAPIKey( GOOGLE_APIKEY_Maps_Embed )?>&q=<?= $officeInfo['address'] ?>"></iframe>
		</div>
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

				echo $this->Form->hidden('client_id' ,array('value' => $clientId));
			?>
				<?php
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

<?php 
	if(!empty($officeCharacterDataArray)){ 
?>
	<section class="rentacar_txt_area rent-margin-bottom-l">
		<h3 class="hd-left-bordered clearfix"><?=$clientInfo['name']?> <?=$officeInfo['name']?> の特徴</h3>
<?php 
		foreach($officeCharacterDataArray AS $officeCharacter) { 
?>
		<section class="contents_type_main_search from_about rent-margin-bottom">
			<h4 class="rentacar_text_ttl"><?=$officeCharacter['head']?></h4>
<?php 
			if($officeCharacter['img'] != ''){ 
?>
			<p class="from_about_contents"><img src="<?=$officeCharacter['img'];?>" loading="lazy" importance="low" decoding="async"></p>
<?php 
			} 
?>
			<p class="from_about_contents"><?=$officeCharacter['text']?></p>
	  	</section>
<?php 
		} 
?>
	</section>
<?php 
	} 
?>

<?php
// YOTPO
	if(!empty($yotpoReviews)) {
		if($yotpo_is_active && $use_yotpo){ 
?>
	<section id="reviews" class="yotpo_api_custom_wrap content_section">
		<h2 class="review_section_title"><?=$clientInfo['name']?> <?=$officeInfo['name']?>の感想・口コミ</h2>
		<script>
			const $_isExistedReviewPage = true
		</script>
		<?php echo $this->element('yotpo_review'); ?>
	</section>
<?php
		}
	}
?>

	<section class="content_section">
<?php 
	if(!empty($officeNearList)){ 
?>
		<h2 class="hd-left-bordered"><?=$clientInfo['name']?> <?=$officeInfo['name']?>のその他の近隣営業所から探す</h2>
		<div class="search_list">
			<ul>
<?php
		if(isset($officeNearList)){
  			foreach($officeNearList as $officeNear){
				if(!empty($officeNear['Office']['url']) && !empty($officeNear['Client']['url'])){
?>
		  		<li><i class="fa fa-caret-right"></i><a href="/rentacar/company/<?=$officeNear['Client']['url'];?>/<?=$officeNear['Office']['url'];?>/"><?=$officeNear['Client']['name'];?>・<?=$officeNear['Office']['name'];?></a></li>
<?php 
				} else {
?>
		  		<li><i class="fa fa-caret-right"></i><a href="/rentacar/localstoredetail?store_id=<?=$officeNear['Office']['id'];?>"><?=$officeNear['Client']['name'];?>・<?=$officeNear['Office']['name'];?></a></li>
<?php
				}
  			} 
		}
?>
			</ul>
	  	</div>
	</section>

<?php 
	} 
?>

	<?php echo $this->element('prefecture_linklist'); ?>

	<?php echo $this->element('popular_airport_linklist'); ?>

</div>

<?php 
	if($yotpo_is_active && $use_yotpo){
		echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]);
	} 
?>
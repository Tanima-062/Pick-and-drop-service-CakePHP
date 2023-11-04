<?php
	echo $this->Html->script(['/js/smoothscroll'],['defer'=>true, 'inline' => false]); 
?>

<div class="wrap contents clearfix">
	<?php echo $this->element('progress_bar'); ?>
	<div class="cont_page_head">
		<h1 class="page_mainhead"><?=$regionContents['region-name']?>地方の格安レンタカーを比較・予約する</h1>
		<span class="page_subhead"></span>
	</div>
	<ul class="link_in_page">
<?php 
	if(isset($regionContents['pref_details'])){
?>
<?php 
		foreach($regionContents['pref_details'] as $val){
?>
		<li class="link_in_page_li"><a href="#<?= $prefectureLinkCd[$val['id']]; ?>" class="link_in_page_a"><?= $prefectureList[$val['id']]; ?></a></li>
<?php 
		}
?>
		<li class="link_in_page_li"><a href="#reviews" class="link_in_page_a">ロコミ</a></li>
		<li class="link_in_page_li"><a href="#regions" class="link_in_page_a">他の地方から探す</a></li>
<?php
	}
?>
	</ul>
	<p class="page_text">
		<?= $regionContents['pre-contents']; ?>
	</p>
	<div>
		<section class="content_section">
			<?php
				echo $this->Form->create('Search',array('action'=>'index', 'inputDefaults'=>array(
					'label'=>false,
					'div'=>false,
					'hiddenField'=>false,
					'legend'=>false,
					'fieldset'=>false,
				),'type'=>'get','class'=>'contents_search'));
				//出発日時や返却日時などは共通項目のためエレメント化
				echo $this->element('searchform_main');
			?>
				<div class="searchform_submit_section_wrap">				
					<?php echo $this->element('searchform_submit'); ?>
				</div>
			<?php
				echo $this->Form->end();
			?>
		</section>
	</div>

<?php 
	if(isset($regionContents['pref_details'])){
		foreach($regionContents['pref_details'] as $val){ 
?>
	<div class="c-media area-contents_main clearfix" id="<?= $prefectureLinkCd[$val['id']]; ?>">
<?php 
			if (!empty($val['img'])){
?>
		<img src="/rentacar/wp/img/<?= $val['img']; ?>" alt="<?= $prefectureList[$val['id']]; ?>" width="490" height="369" class="area-contents_main_img">
<?php 
			}
?>
		<div class="c-media_body area-contents_main_body">

<?php 
			if (!empty($prefectureList[$val['id']])){
?>
			<h2 class="area-head"><?= $prefectureList[$val['id']]; ?></h2>
<?php 
			}
?>
<?php 
			if (!empty($val['text'])){
?>
			<p class="area-contents_main_body_text"><?= $val['text']; ?></p>
<?php 
			}
?>
<?php 
			if (!empty($prefectureList[$val['id']])){ 
?>
			<div class="btn-wrap">
				<a href="/rentacar/<?= $base_url . $prefectureLinkCd[$val['id']]; ?>/" class="btn-type-primary"><?= $prefectureList[$val['id']]; ?>のレンタカー予約へ</a>
			</div>
<?php 
			}
?>

		</div><!-- [/contents-main_body] -->
	</div>

<?php 
			if (!empty($prefectureList[$val['id']])){
?>
	<div class="page_title is-lightblue">
		<h3 class="page_title_header"><?= $prefectureList[$val['id']]; ?>内のドライブスポットから探す</h3>
	</div>
<?php 
			}
?>

	<div class="area-contents-sub clearfix">
<?php 
			$i=0;
			foreach($val['rows'] as $val2){
				$i++; 
?>
		<div class="area-contents-sub_column <?php if($i % 3 == 0):?>is-food<?php else:?>is-event<?php endif;?>">
<?php 
				if (!empty($val2['img'])){
?>
			<img src="/rentacar/wp/img/<?= $val2['img']; ?>" alt="<?= $val2['head-s']; ?>" width="320" height="203" class="area-contents-sub_img">
<?php 
				}
?>
<?php 
				if (!empty($val2['city']) AND !empty($areaLinkCdList[$val2['city']])){
?>
			<h4 class="area-contents-sub_headline"><span><?= $areaLinkCdList[$val2['city']]; ?></span></h4>

<?php 
				} elseif (!empty($val2['airport']) AND !empty($airportLinkCdList[$val2['airport']])){ 
?>
			<h4 class="area-contents-sub_headline"><span><?= $airportLinkCdList[$val2['airport']]; ?></span></h4>
<?php 
				}
?>
			<div class="area-contents-sub_body is-event">
<?php 
				if (!empty($val2['contents'])){
?>
				<p><?= $val2['contents']; ?></p>
<?php 
				}
?>
			</div><!-- [/contents_sub_body] -->

			<div class="btn-wrap">
<?php 
				if (!empty($val2['city']) AND !empty($areaLinkCdList[$val2['city']])){
?>
				<a href="/rentacar/<?=$base_url . $prefectureLinkCd[$val['id']] . DS . $val2['city'];?>/" class="btn-type-primary"><?= $areaLinkCdList[$val2['city']]; ?>のレンタカー予約へ</a>
<?php 
				} elseif (!empty($val2['airport']) AND !empty($airportLinkCdList[$val2['airport']])){ 
?>
				<a href="/rentacar/<?=$base_url . $prefectureLinkCd[$val['id']] . DS . $val2['airport'];?>/" class="btn-type-primary"><?= $airportLinkCdList[$val2['airport']]; ?>のレンタカー予約へ</a>
<?php 
				}
?>
			</div>
		</div>

<?php 
				if($i % 3 == 0){
?>
	</div>
	<div class="area-contents-sub clearfix">
<?php 
				}
?>
<?php 
			} // /foreach
?>
	</div>

<?php 
		} // /foreach
	}
?>


<?php 
	// YOTPO
	if($yotpo_is_active && $use_yotpo){ 
?>
	<section id="reviews" class="yotpo_api_custom_wrap">
		<h2 class="review_section_title"><?=$regionContents['region-name']?>地方でレンタカーを利用したお客様の口コミ・レビュー</h2>
		<?php echo $this->element('yotpo_review'); ?>
	</section>
<?php 
	}
?>
	
	<?php echo $this->element('popular_airport_linklist'); ?>

	<div class="page_title" id = "regions">
		<h2>他の地方からレンタカーを探す</h2>
	</div>

	<ul class="area_list">

<?php 
	foreach($regionList as $key => $val){
		if($key != $regionContents['region-id']){
?>
		<li>
			<a href="/rentacar/<?= str_replace('area_', '', $key); ?>/" class="area_list-label <?= $key?>"><span class="area_list-label_inner"><?= $val; ?></span><span class="area_list-label_arrow <?= $key?>">›</span></a>
		</li>
<?php 
		}
	}
?>
	</ul>

</div>

<script type="text/javascript">
var maxHeight = 0;
$(".area-contents-sub_body").each(function(){
	if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
});
$(".area-contents-sub_body").height(maxHeight);
</script>

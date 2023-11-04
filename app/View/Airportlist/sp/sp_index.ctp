<section class="listpage-heading-section">
	<h1 class="listpage-title">空港からレンタカーを探す</h1>
	
<?php   
	if(!empty($airportDataArrayTop)){
		foreach($airportDataArrayTop as $value){
?>
	<section class="listpage-heading-content">
		<h2 class="-header"><?= $value['head']; ?></h2>
		<p class="-text"><?= $value['text']; ?></p>
	</section>
<?php
		}
	}
?>
</section>

<div class="common-accordion-wrap">
	<div class="common_accordion">
<?php
	foreach($landmarkList['airportArray'] as $prefectures => $airport_prefectures){
?>
		<div class="loop_div">
			<h3 class="js-accordion-header prefectures_title"><?= $prefectures; ?>の空港から探す</h3>
			<ul class="js-accordion-inner sub_menu airport_list">
<?php
		foreach($airport_prefectures as $airport_id => $airport){
?>
				<li>
					<span>
<?php
			if(!empty($airportLinkCdList[$airport_id])){
?>
						<a href="/rentacar/<?= $base_url_arr[$prefectures] . $airportLinkCdList[$airport_id]?>/"><?= $airport ?></a>
<?php
			}else{
?>
						<a href="/rentacar/searches?place=3&airport_id=<?=$airport_id;?>&_submit=&year=<?=$link_date_arr['year'];?>&month=<?=$link_date_arr['month'];?>&day=<?=$link_date_arr['day'];?>&time=11-00&return_way=0&_submit=&return_year=<?=$link_date_arr['year'];?>&return_month=<?=$link_date_arr['month'];?>&return_day=<?=$link_date_arr['day'];?>&return_time=17-00&adults_count=2&children_count=&infants_count="><?= $airport ?></a>
<?php
			}
?>
					</span>
				</li>
<?php
		}
?>
			</ul>
		</div>
<?php
	}
?>
	</div>
</div>

<div class="img_caption_wrap">
	<div class="img_caption_contents">
<?php
	if(!empty($airportDataArrayBottom)){  
		foreach($airportDataArrayBottom as $value){
?>
		<h2 class="img_caption_btn"><?= $value['head']; ?></h2>
		<div class="img_caption_detail">
			<p><?= $value['text']; ?></p>
		</div>
<?php
		}
	}
?>
	</div>
</div>

<?php echo $this->element("sp_sidebar"); ?>

<script>
$(function() {
	$(".img_caption_btn").click(function(){
		if($(this).hasClass("open")){
			$(this).removeClass("open");
		}else{
			$(this).addClass("open");
		}
	});
});
</script>
